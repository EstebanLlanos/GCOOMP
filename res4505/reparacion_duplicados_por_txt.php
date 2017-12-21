<?php
set_time_limit(2592000);
ini_set('max_execution_time', 2592000);
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

//PYP
function reparacion_duplicados_por_txt($tipo_id_duplicado_actual,
                                      $numero_id_duplicado_actual,
                                      $fecha_actual,
                                      $tiempo_actual,
                                      $fecha_de_corte,
                                      $nick_user,
                                      $identificacion,
                                      $tipo_id,
                                      $numero_duplicados_de_duplicado,
                                      $ruta_temporal_duplicados_afiliado_actual,
                                      $ruta_temporal_nsecuencia_duplicados_afiliado_actual,
                                      &$numero_secuencia_para_procesado,
                                      &$numero_registro_para_procesado,
                                      &$cod_prestador_para_procesado,
                                      &$regimen_para_procesado,
                                      &$bool_fueron_procesados_duplicados_en_un_registro,
                                      &$contador_offset_duplicados,
                                      &$contador_duplicado_para_excluidos,
                                      &$mensajes_error_bd,
                                      $conexionbd)
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
    while($cont_campos_reparados_pre_fix<119)
    {
        $arreglo_campos_reparados[$cont_campos_reparados_pre_fix]="NO_VALOR_1024488856";
        $cont_campos_reparados_pre_fix++;
    }//fin while dejando listas posiciones delos 119 campos desde 0 a 118
    
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
    
        
    $posicion_para_maxima_secuencia=posicion_del_maximo_numero_en_array($matrix_secuencia_prestador,0);
    
    //se asigna el regimen y la secuencia  para la secuencia mas grande
    if(isset($matrix_secuencia_prestador[$posicion_para_maxima_secuencia][0]) )
    {
      $numero_secuencia_para_procesado=$matrix_secuencia_prestador[$posicion_para_maxima_secuencia][0]; 
    }//fin if  
    if(isset($matrix_secuencia_prestador[$posicion_para_maxima_secuencia][2]) )
    {
      $regimen_para_procesado=$matrix_secuencia_prestador[$posicion_para_maxima_secuencia][2];
    }//fin if 

    //echo "posicion_para_maxima_secuencia ".$posicion_para_maxima_secuencia." array ".print_r($matrix_secuencia_prestador,true);
    //obtiene el registro de acuerdo a la matrix de registros pero
    //con la posicion donde se encontro la secuencia mas alta del archivo de secuencia
    $numero_registro_para_procesado=$matrix_registros_duplicados_afiliado_actual[$posicion_para_maxima_secuencia][1];
    
    
    $cod_prestador_para_procesado=$arreglo_campos_reparados[2];
    
    /*
     //array test
     $fecha_de_corte="2016-06-30";//esta comentada porque es solo para prueba
     $matrix_registros_duplicados_afiliado_actual=array();
     $matrix_registros_duplicados_afiliado_actual[]=array("0","10","id0","16","1835-01-01");//por ahora solo con dos posiciones enves de 119
     $matrix_registros_duplicados_afiliado_actual[]=array("2","1","0","19","1815-01-01");
     $matrix_registros_duplicados_afiliado_actual[]=array("2","15","999","19","1825-01-01");
     $matrix_registros_duplicados_afiliado_actual[]=array("6","20","9990","19","1825-01-01");
     $matrix_registros_duplicados_afiliado_actual[]=array("2","2","id10","16","1825-01-01");
     $matrix_registros_duplicados_afiliado_actual[]=array("2","30","id80","16","1835-01-01");
     $arreglo_campos_reparados=array();//inicializa este array para test 
     $arreglo_campos_reparados=array();//inicializa este array para test 
    */
    
    //CAMPO 0
    $numero_orden_campo_actual=0;
    $posiciones_iguales_array=array();
    $posiciones_iguales_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"2");
    //print_r($posiciones_iguales_array);
    if(count($posiciones_iguales_array)>0 && is_array($posiciones_iguales_array))
    {
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_array);
            $numero_registro_para_procesado=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][1];//ultimo numero registro
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }
    else
    {
        $arreglo_campos_reparados[$numero_orden_campo_actual]="2";
    }
    //FIN CAMPO 0
    
    //echo "array en el campo 0: ".$arreglo_campos_reparados[0];
    
    //CAMPO 1
    $arreglo_campos_reparados[1]=$numero_registro_para_procesado;
    //FIN CAMPO 1
    
    //CAMPO 2
    $numero_orden_campo_actual=2;
    
    $matriz_comparaciones=array();
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"999");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
                                      
    $array_diferentes_a_0_999_vacio=array();
    $array_diferentes_a_0_999_vacio=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($array_diferentes_a_0_999_vacio)>0 && is_array($array_diferentes_a_0_999_vacio))
    {
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$array_diferentes_a_0_999_vacio);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }
    else
    {
            $arreglo_campos_reparados[$numero_orden_campo_actual]="999"; 
    }
    //FIN CAMPO 2
    
    //CAMPO 3
    $arreglo_campos_reparados[3]=$tipo_id_duplicado_actual;
    //FIN CAMPO 3
    
    //CAMPO 4    
    $arreglo_campos_reparados[4]=$numero_id_duplicado_actual;
    //FIN CAMPO 4
    
    //CAMPO 5
    $numero_orden_campo_actual=5;
    
    $matriz_comparaciones=array();
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"NONE");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
                                      
    $array_diferentes_a_NONE_vacio=array();
    $array_diferentes_a_NONE_vacio=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($array_diferentes_a_NONE_vacio)>0 && is_array($array_diferentes_a_NONE_vacio))
    {
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$array_diferentes_a_NONE_vacio);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }
    else
    {
            $arreglo_campos_reparados[$numero_orden_campo_actual]="NONE"; 
    }
    //FIN CAMPO 5
    
    //CAMPO 6
    $numero_orden_campo_actual=6;
    
    $matriz_comparaciones=array();
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"NONE");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
                                      
    $array_diferentes_a_NONE_vacio=array();
    $array_diferentes_a_NONE_vacio=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($array_diferentes_a_NONE_vacio)>0 && is_array($array_diferentes_a_NONE_vacio))
    {
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$array_diferentes_a_NONE_vacio);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }
    else
    {
            $arreglo_campos_reparados[$numero_orden_campo_actual]="NONE"; 
    }
    //FIN CAMPO 6
    
    //CAMPO 7
    $numero_orden_campo_actual=7;
    
    $matriz_comparaciones=array();
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"NONE");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
                                      
    $array_diferentes_a_NONE_vacio=array();
    $array_diferentes_a_NONE_vacio=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($array_diferentes_a_NONE_vacio)>0 && is_array($array_diferentes_a_NONE_vacio))
    {
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$array_diferentes_a_NONE_vacio);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }
    else
    {
            $arreglo_campos_reparados[$numero_orden_campo_actual]="NONE"; 
    }
    //FIN CAMPO 7
    
    //CAMPO 8
    $numero_orden_campo_actual=8;
    
    $matriz_comparaciones=array();
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"NONE");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
                                      
    $array_diferentes_a_NONE_vacio=array();
    $array_diferentes_a_NONE_vacio=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($array_diferentes_a_NONE_vacio)>0 && is_array($array_diferentes_a_NONE_vacio))
    {
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$array_diferentes_a_NONE_vacio);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }
    else
    {
            $arreglo_campos_reparados[$numero_orden_campo_actual]="NONE"; 
    }
    //FIN CAMPO 8
    
    //CAMPO 9
    $numero_orden_campo_actual=9;
    
    $matriz_comparaciones=array();
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"NONE");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
                                      
    $array_diferentes_a_NONE_vacio=array();
    $array_diferentes_a_NONE_vacio=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($array_diferentes_a_NONE_vacio)>0 && is_array($array_diferentes_a_NONE_vacio))
    {
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$array_diferentes_a_NONE_vacio);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }
    else
    {
            $arreglo_campos_reparados[$numero_orden_campo_actual]="NONE"; 
    }
    //FIN CAMPO 9
    
    //CAMPO 10
    $numero_orden_campo_actual=10;
    
    $matriz_comparaciones=array();
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"NONE");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
                                      
    $array_diferentes_a_NONE_vacio=array();
    $array_diferentes_a_NONE_vacio=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($array_diferentes_a_NONE_vacio)>0 && is_array($array_diferentes_a_NONE_vacio))
    {
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$array_diferentes_a_NONE_vacio);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }
    else
    {
            $arreglo_campos_reparados[$numero_orden_campo_actual]="NONE"; 
    }
    //FIN CAMPO 10
    
    //CAMPO 11
    $numero_orden_campo_actual=11;
    
    $matriz_comparaciones=array();
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"NONE");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
                                      
    $array_diferentes_a_NONE_vacio=array();
    $array_diferentes_a_NONE_vacio=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($array_diferentes_a_NONE_vacio)>0 && is_array($array_diferentes_a_NONE_vacio))
    {
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$array_diferentes_a_NONE_vacio);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }
    else
    {
            $arreglo_campos_reparados[$numero_orden_campo_actual]="NONE"; 
    }
    //FIN CAMPO 11
    
    //CAMPO 12
    $numero_orden_campo_actual=12;
    
    $matriz_comparaciones=array();
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"NONE");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
                                      
    $array_diferentes_a_NONE_vacio=array();
    $array_diferentes_a_NONE_vacio=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($array_diferentes_a_NONE_vacio)>0 && is_array($array_diferentes_a_NONE_vacio))
    {
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$array_diferentes_a_NONE_vacio);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }
    else
    {
            $arreglo_campos_reparados[$numero_orden_campo_actual]="NONE"; 
    }
    //FIN CAMPO 12
    
    //CAMPO 13
    $numero_orden_campo_actual=13;
    
    $matriz_comparaciones=array();
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"NONE");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
                                      
    $array_diferentes_a_NONE_vacio=array();
    $array_diferentes_a_NONE_vacio=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($array_diferentes_a_NONE_vacio)>0 && is_array($array_diferentes_a_NONE_vacio))
    {
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$array_diferentes_a_NONE_vacio);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }
    else
    {
            $arreglo_campos_reparados[$numero_orden_campo_actual]="NONE"; 
    }
    //FIN CAMPO 13
    
    //VACUNACION
    
    //CAMPO 35
    $numero_orden_campo_actual=35;
    $posiciones_iguales_a_1_array=array();
    $posiciones_iguales_a_1_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
    if(count($posiciones_iguales_a_1_array)>0 && is_array($posiciones_iguales_a_1_array))
    {
            //criterio 1
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1_array);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
             
                $array_varios_valores=array(
                                        "16",
                                        "17",
                                        "18",
                                        "19",
                                        "20",
                                        "22"
                                        );
            
            $array_iguales_a_16_17_18_19_20_22=array();
            $array_iguales_a_16_17_18_19_20_22=iguales_a_varios_valores($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_varios_valores);
            
            if(count($array_iguales_a_16_17_18_19_20_22)>0 && is_array($array_iguales_a_16_17_18_19_20_22))
            {
                //criterio 2
                $mayor_valor_mayor_frecuencia="";
                $mayor_valor_mayor_frecuencia=calcula_mayor_valor_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_16_17_18_19_20_22);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                
            }//fin if
            else
            {
                $posiciones_iguales_a_0_array=array();
                $posiciones_iguales_a_0_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
                if(count($posiciones_iguales_a_0_array)>0 && is_array($posiciones_iguales_a_0_array))
                {
                        //criterio 3
                        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_0_array);
                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                }//fin if
                else
                {
                        //criterio para no dejar faltante
                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                }
            }//fin if
            
    }//fin else
    //FIN CAMPO 35
    
    //CAMPO 36
    $numero_orden_campo_actual=36;
    $posiciones_iguales_a_1_array=array();
    $posiciones_iguales_a_1_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
    if(count($posiciones_iguales_a_1_array)>0 && is_array($posiciones_iguales_a_1_array))
    {
            //criterio 1
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1_array);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
            $array_varios_valores=array(
                                        "16",
                                        "17",
                                        "18",
                                        "19",
                                        "20",
                                        "22"
                                        );
            
            $array_iguales_a_16_17_18_19_20_22=array();
            $array_iguales_a_16_17_18_19_20_22=iguales_a_varios_valores($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_varios_valores);
            
            if(count($array_iguales_a_16_17_18_19_20_22)>0 && is_array($array_iguales_a_16_17_18_19_20_22))
            {
                //criterio 2
                $mayor_valor_mayor_frecuencia="";
                $mayor_valor_mayor_frecuencia=calcula_mayor_valor_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_16_17_18_19_20_22);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                
            }//fin if
            else
            {
                $posiciones_iguales_a_0_array=array();
                $posiciones_iguales_a_0_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
                if(count($posiciones_iguales_a_0_array)>0 && is_array($posiciones_iguales_a_0_array))
                {
                        //criterio 3
                        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_0_array);
                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                }//fin if
                else
                {
                        //criterio para no dejar faltante
                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                }
            }//fin if
            
    }//fin else
    //FIN CAMPO 36
    
    //CAMPO 43
    $numero_orden_campo_actual=43;
    $posiciones_iguales_a_1_array=array();
    $posiciones_iguales_a_1_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
    if(count($posiciones_iguales_a_1_array)>0 && is_array($posiciones_iguales_a_1_array))
    {
            //criterio 1
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1_array);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
            $array_varios_valores=array(
                                        "16",
                                        "17",
                                        "18",
                                        "19",
                                        "20",
                                        "22"
                                        );
            
            $array_iguales_a_16_17_18_19_20_22=array();
            $array_iguales_a_16_17_18_19_20_22=iguales_a_varios_valores($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_varios_valores);
            
            if(count($array_iguales_a_16_17_18_19_20_22)>0 && is_array($array_iguales_a_16_17_18_19_20_22))
            {
                //criterio 2
                $mayor_valor_mayor_frecuencia="";
                $mayor_valor_mayor_frecuencia=calcula_mayor_valor_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_16_17_18_19_20_22);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                
            }//fin if
            else
            {
                $posiciones_iguales_a_0_array=array();
                $posiciones_iguales_a_0_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
                if(count($posiciones_iguales_a_0_array)>0 && is_array($posiciones_iguales_a_0_array))
                {
                        //criterio 3
                        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_0_array);
                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                }//fin if
                else
                {
                        //criterio para no dejar faltante
                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                }
            }//fin if
            
    }//fin else
    //FIN CAMPO 43
    
    //CAMPO 44
    $numero_orden_campo_actual=44;
    $posiciones_iguales_a_1_array=array();
    $posiciones_iguales_a_1_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
    if(count($posiciones_iguales_a_1_array)>0 && is_array($posiciones_iguales_a_1_array))
    {
            //criterio 1
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1_array);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
            $array_varios_valores=array(
                                        "16",
                                        "17",
                                        "18",
                                        "19",
                                        "20",
                                        "22"
                                        );
            
            $array_iguales_a_16_17_18_19_20_22=array();
            $array_iguales_a_16_17_18_19_20_22=iguales_a_varios_valores($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_varios_valores);
            
            if(count($array_iguales_a_16_17_18_19_20_22)>0 && is_array($array_iguales_a_16_17_18_19_20_22))
            {
                //criterio 2
                $mayor_valor_mayor_frecuencia="";
                $mayor_valor_mayor_frecuencia=calcula_mayor_valor_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_16_17_18_19_20_22);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                
            }//fin if
            else
            {
                $posiciones_iguales_a_0_array=array();
                $posiciones_iguales_a_0_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
                if(count($posiciones_iguales_a_0_array)>0 && is_array($posiciones_iguales_a_0_array))
                {
                        //criterio 3
                        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_0_array);
                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                }//fin if
                else
                {
                        //criterio para no dejar faltante
                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                }
            }//fin if
            
    }//fin else
    //FIN CAMPO 44
    
    //CAMPO 40
    $numero_orden_campo_actual=40;
    
        $posiciones_iguales_a_2_array=array();
        $posiciones_iguales_a_2_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"2");
        if(count($posiciones_iguales_a_2_array)>0 && is_array($posiciones_iguales_a_2_array))
        {
            //criterio 2
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_2_array);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            $posiciones_iguales_a_1_array=array();
            $posiciones_iguales_a_1_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
            if(count($posiciones_iguales_a_1_array)>0 && is_array($posiciones_iguales_a_1_array))
            {
                    //criterio 3
                    $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1_array);
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
            }//fin if
            else
            {
                    $array_varios_valores=array(
                                        "16",
                                        "17",
                                        "18",
                                        "19",
                                        "20",
                                        "22"
                                        );
            
                        $array_iguales_a_16_17_18_19_20_22=array();
                        $array_iguales_a_16_17_18_19_20_22=iguales_a_varios_valores($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_varios_valores);
                    
                    if(count($array_iguales_a_16_17_18_19_20_22)>0 && is_array($array_iguales_a_16_17_18_19_20_22))
                    {
                        //criterio 4
                        $mayor_valor_mayor_frecuencia="";
                        $mayor_valor_mayor_frecuencia=calcula_mayor_valor_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_16_17_18_19_20_22);
                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                        
                    }//fin if
                    else
                    {
                        $posiciones_iguales_a_0_array=array();
                        $posiciones_iguales_a_0_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
                        if(count($posiciones_iguales_a_0_array)>0 && is_array($posiciones_iguales_a_0_array))
                        {
                                //criterio 5
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_0_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                        }//fin if
                        else
                        {
                                //criterio para no dejar faltante
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                        }
                    }//fin if
            }//fin else
        }//fin if

    //FIN CAMPO 40
    
    //CAMPO 45
    $numero_orden_campo_actual=45;
    
        $posiciones_iguales_a_2_array=array();
        $posiciones_iguales_a_2_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"2");
        if(count($posiciones_iguales_a_2_array)>0 && is_array($posiciones_iguales_a_2_array))
        {
            //criterio 2
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_2_array);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            $posiciones_iguales_a_1_array=array();
            $posiciones_iguales_a_1_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
            if(count($posiciones_iguales_a_1_array)>0 && is_array($posiciones_iguales_a_1_array))
            {
                    //criterio 3
                    $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1_array);
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
            }//fin if
            else
            {
                    $array_varios_valores=array(
                                        "16",
                                        "17",
                                        "18",
                                        "19",
                                        "20",
                                        "22"
                                        );
            
                        $array_iguales_a_16_17_18_19_20_22=array();
                        $array_iguales_a_16_17_18_19_20_22=iguales_a_varios_valores($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_varios_valores);
                    
                    if(count($array_iguales_a_16_17_18_19_20_22)>0 && is_array($array_iguales_a_16_17_18_19_20_22))
                    {
                        //criterio 4
                        $mayor_valor_mayor_frecuencia="";
                        $mayor_valor_mayor_frecuencia=calcula_mayor_valor_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_16_17_18_19_20_22);
                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                        
                    }//fin if
                    else
                    {
                        $posiciones_iguales_a_0_array=array();
                        $posiciones_iguales_a_0_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
                        if(count($posiciones_iguales_a_0_array)>0 && is_array($posiciones_iguales_a_0_array))
                        {
                                //criterio 5
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_0_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                        }//fin if
                        else
                        {
                                //criterio para no dejar faltante
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                        }
                    }//fin if
            }//fin else
        }//fin if

    //FIN CAMPO 45
    
    //CAMPO 48
    //duda
    $numero_orden_campo_actual=48;
    
        $posiciones_iguales_a_2_array=array();
        $posiciones_iguales_a_2_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"2");
        if(count($posiciones_iguales_a_2_array)>0 && is_array($posiciones_iguales_a_2_array))
        {
            //criterio 2
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_2_array);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            $posiciones_iguales_a_1_array=array();
            $posiciones_iguales_a_1_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
            if(count($posiciones_iguales_a_1_array)>0 && is_array($posiciones_iguales_a_1_array))
            {
                    //criterio 3
                    $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1_array);
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
            }//fin if
            else
            {
                    $array_varios_valores=array(
                                        "16",
                                        "17",
                                        "18",
                                        "19",
                                        "20",
                                        "22"
                                        );
            
                        $array_iguales_a_16_17_18_19_20_22=array();
                        $array_iguales_a_16_17_18_19_20_22=iguales_a_varios_valores($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_varios_valores);
                    
                    if(count($array_iguales_a_16_17_18_19_20_22)>0 && is_array($array_iguales_a_16_17_18_19_20_22))
                    {
                        //criterio 4
                        $mayor_valor_mayor_frecuencia="";
                        $mayor_valor_mayor_frecuencia=calcula_mayor_valor_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_16_17_18_19_20_22);
                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                        
                    }//fin if
                    else
                    {
                        $posiciones_iguales_a_0_array=array();
                        $posiciones_iguales_a_0_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
                        if(count($posiciones_iguales_a_0_array)>0 && is_array($posiciones_iguales_a_0_array))
                        {
                                //criterio 5
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_0_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                        }//fin if
                        else
                        {
                                //criterio para no dejar faltante
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                        }
                    }//fin if
            }//fin else
        }//fin if
        

    //FIN CAMPO 48
    
    //CAMPO 37
    $numero_orden_campo_actual=37;
    $posiciones_iguales_a_3_array=array();
    $posiciones_iguales_a_3_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"3");
    if(count($posiciones_iguales_a_3_array)>0 && is_array($posiciones_iguales_a_3_array))
    {
            //criterio 1
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_3_array);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
            $posiciones_iguales_a_2_array=array();
            $posiciones_iguales_a_2_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"2");
            if(count($posiciones_iguales_a_2_array)>0 && is_array($posiciones_iguales_a_2_array))
            {
                //criterio 2
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_2_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
            }//fin if
            else
            {
                $posiciones_iguales_a_1_array=array();
                $posiciones_iguales_a_1_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
                if(count($posiciones_iguales_a_1_array)>0 && is_array($posiciones_iguales_a_1_array))
                {
                        //criterio 3
                        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1_array);
                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                }//fin if
                else
                {
                        $array_varios_valores=array(
                                        "16",
                                        "17",
                                        "18",
                                        "19",
                                        "20",
                                        "22"
                                        );
            
                        $array_iguales_a_16_17_18_19_20_22=array();
                        $array_iguales_a_16_17_18_19_20_22=iguales_a_varios_valores($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_varios_valores);
                        
                        if(count($array_iguales_a_16_17_18_19_20_22)>0 && is_array($array_iguales_a_16_17_18_19_20_22))
                        {
                            //criterio 4
                            $mayor_valor_mayor_frecuencia="";
                            $mayor_valor_mayor_frecuencia=calcula_mayor_valor_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_16_17_18_19_20_22);
                            $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                            
                        }//fin if
                        else
                        {
                            $posiciones_iguales_a_0_array=array();
                            $posiciones_iguales_a_0_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
                            if(count($posiciones_iguales_a_0_array)>0 && is_array($posiciones_iguales_a_0_array))
                            {
                                    //criterio 5
                                    $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_0_array);
                                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                            }//fin if
                            else
                            {
                                    //criterio para no dejar faltante
                                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                            }
                        }//fin if
                }//fin else
            }//fin if
            
    }//fin else
    //FIN CAMPO 37
    
    //CAMPO 41
    $numero_orden_campo_actual=41;
    $posiciones_iguales_a_3_array=array();
    $posiciones_iguales_a_3_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"3");
    if(count($posiciones_iguales_a_3_array)>0 && is_array($posiciones_iguales_a_3_array))
    {
            //criterio 1
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_3_array);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
            $posiciones_iguales_a_2_array=array();
            $posiciones_iguales_a_2_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"2");
            if(count($posiciones_iguales_a_2_array)>0 && is_array($posiciones_iguales_a_2_array))
            {
                //criterio 2
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_2_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
            }//fin if
            else
            {
                $posiciones_iguales_a_1_array=array();
                $posiciones_iguales_a_1_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
                if(count($posiciones_iguales_a_1_array)>0 && is_array($posiciones_iguales_a_1_array))
                {
                        //criterio 3
                        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1_array);
                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                }//fin if
                else
                {
                        $array_varios_valores=array(
                                        "16",
                                        "17",
                                        "18",
                                        "19",
                                        "20",
                                        "22"
                                        );
            
                        $array_iguales_a_16_17_18_19_20_22=array();
                        $array_iguales_a_16_17_18_19_20_22=iguales_a_varios_valores($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_varios_valores);
                        
                        if(count($array_iguales_a_16_17_18_19_20_22)>0 && is_array($array_iguales_a_16_17_18_19_20_22))
                        {
                            //criterio 4
                            $mayor_valor_mayor_frecuencia="";
                            $mayor_valor_mayor_frecuencia=calcula_mayor_valor_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_16_17_18_19_20_22);
                            $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                            
                        }//fin if
                        else
                        {
                            $posiciones_iguales_a_0_array=array();
                            $posiciones_iguales_a_0_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
                            if(count($posiciones_iguales_a_0_array)>0 && is_array($posiciones_iguales_a_0_array))
                            {
                                    //criterio 5
                                    $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_0_array);
                                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                            }//fin if
                            else
                            {
                                    //criterio para no dejar faltante
                                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                            }
                        }//fin if
                }//fin else
            }//fin if
            
    }//fin else
    //FIN CAMPO 41
    
    //CAMPO 42
    $numero_orden_campo_actual=42;
    $posiciones_iguales_a_3_array=array();
    $posiciones_iguales_a_3_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"3");
    if(count($posiciones_iguales_a_3_array)>0 && is_array($posiciones_iguales_a_3_array))
    {
            //criterio 1
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_3_array);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
            $posiciones_iguales_a_2_array=array();
            $posiciones_iguales_a_2_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"2");
            if(count($posiciones_iguales_a_2_array)>0 && is_array($posiciones_iguales_a_2_array))
            {
                //criterio 2
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_2_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
            }//fin if
            else
            {
                $posiciones_iguales_a_1_array=array();
                $posiciones_iguales_a_1_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
                if(count($posiciones_iguales_a_1_array)>0 && is_array($posiciones_iguales_a_1_array))
                {
                        //criterio 3
                        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1_array);
                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                }//fin if
                else
                {
                        $array_varios_valores=array(
                                        "16",
                                        "17",
                                        "18",
                                        "19",
                                        "20",
                                        "22"
                                        );
            
                        $array_iguales_a_16_17_18_19_20_22=array();
                        $array_iguales_a_16_17_18_19_20_22=iguales_a_varios_valores($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_varios_valores);
                        
                        if(count($array_iguales_a_16_17_18_19_20_22)>0 && is_array($array_iguales_a_16_17_18_19_20_22))
                        {
                            //criterio 4
                            $mayor_valor_mayor_frecuencia="";
                            $mayor_valor_mayor_frecuencia=calcula_mayor_valor_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_16_17_18_19_20_22);
                            $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                            
                        }//fin if
                        else
                        {
                            $posiciones_iguales_a_0_array=array();
                            $posiciones_iguales_a_0_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
                            if(count($posiciones_iguales_a_0_array)>0 && is_array($posiciones_iguales_a_0_array))
                            {
                                    //criterio 5
                                    $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_0_array);
                                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                            }//fin if
                            else
                            {
                                    //criterio para no dejar faltante
                                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                            }
                        }//fin if
                }//fin else
            }//fin if
            
    }//fin else
    //FIN CAMPO 42
    
    //CAMPO 46
    $numero_orden_campo_actual=46;
    $posiciones_iguales_a_3_array=array();
    $posiciones_iguales_a_3_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"3");
    if(count($posiciones_iguales_a_3_array)>0 && is_array($posiciones_iguales_a_3_array))
    {
            //criterio 1
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_3_array);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
            $posiciones_iguales_a_2_array=array();
            $posiciones_iguales_a_2_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"2");
            if(count($posiciones_iguales_a_2_array)>0 && is_array($posiciones_iguales_a_2_array))
            {
                //criterio 2
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_2_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
            }//fin if
            else
            {
                $posiciones_iguales_a_1_array=array();
                $posiciones_iguales_a_1_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
                if(count($posiciones_iguales_a_1_array)>0 && is_array($posiciones_iguales_a_1_array))
                {
                        //criterio 3
                        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1_array);
                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                }//fin if
                else
                {
                        $array_varios_valores=array(
                                        "16",
                                        "17",
                                        "18",
                                        "19",
                                        "20",
                                        "22"
                                        );
            
                        $array_iguales_a_16_17_18_19_20_22=array();
                        $array_iguales_a_16_17_18_19_20_22=iguales_a_varios_valores($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_varios_valores);
                        
                        if(count($array_iguales_a_16_17_18_19_20_22)>0 && is_array($array_iguales_a_16_17_18_19_20_22))
                        {
                            //criterio 4
                            $mayor_valor_mayor_frecuencia="";
                            $mayor_valor_mayor_frecuencia=calcula_mayor_valor_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_16_17_18_19_20_22);
                            $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                            
                        }//fin if
                        else
                        {
                            $posiciones_iguales_a_0_array=array();
                            $posiciones_iguales_a_0_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
                            if(count($posiciones_iguales_a_0_array)>0 && is_array($posiciones_iguales_a_0_array))
                            {
                                    //criterio 5
                                    $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_0_array);
                                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                            }//fin if
                            else
                            {
                                    //criterio para no dejar faltante
                                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                            }
                        }//fin if
                }//fin else
            }//fin if
            
    }//fin else
    //FIN CAMPO 46
    
    //CAMPO 38
    $numero_orden_campo_actual=38;
    $posiciones_iguales_a_5_array=array();
    $posiciones_iguales_a_5_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"5");
    if(count($posiciones_iguales_a_5_array)>0 && is_array($posiciones_iguales_a_5_array))
    {
            //criterio 1
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_5_array);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        $posiciones_iguales_a_4_array=array();
        $posiciones_iguales_a_4_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"4");
        if(count($posiciones_iguales_a_4_array)>0 && is_array($posiciones_iguales_a_4_array))
        {
                //criterio 2
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_4_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            $posiciones_iguales_a_3_array=array();
            $posiciones_iguales_a_3_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"3");
            if(count($posiciones_iguales_a_3_array)>0 && is_array($posiciones_iguales_a_3_array))
            {
                    //criterio 3
                    $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_3_array);
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
            }//fin if
            else
            {
                    $posiciones_iguales_a_2_array=array();
                    $posiciones_iguales_a_2_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"2");
                    if(count($posiciones_iguales_a_2_array)>0 && is_array($posiciones_iguales_a_2_array))
                    {
                        //criterio 4
                        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_2_array);
                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                    }//fin if
                    else
                    {
                        $posiciones_iguales_a_1_array=array();
                        $posiciones_iguales_a_1_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
                        if(count($posiciones_iguales_a_1_array)>0 && is_array($posiciones_iguales_a_1_array))
                        {
                                //criterio 5
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                        }//fin if
                        else
                        {
                                $array_varios_valores=array(
                                        "16",
                                        "17",
                                        "18",
                                        "19",
                                        "20",
                                        "22"
                                        );
            
                                $array_iguales_a_16_17_18_19_20_22=array();
                                $array_iguales_a_16_17_18_19_20_22=iguales_a_varios_valores($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_varios_valores);
                                
                                if(count($array_iguales_a_16_17_18_19_20_22)>0 && is_array($array_iguales_a_16_17_18_19_20_22))
                                {
                                    //criterio 6
                                    $mayor_valor_mayor_frecuencia="";
                                    $mayor_valor_mayor_frecuencia=calcula_mayor_valor_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_16_17_18_19_20_22);
                                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                                    
                                }//fin if
                                else
                                {
                                    $posiciones_iguales_a_0_array=array();
                                    $posiciones_iguales_a_0_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
                                    if(count($posiciones_iguales_a_0_array)>0 && is_array($posiciones_iguales_a_0_array))
                                    {
                                            //criterio 7
                                            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_0_array);
                                            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                                    }//fin if
                                    else
                                    {
                                            //criterio para no dejar faltante
                                            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                                    }
                                }//fin if
                        }//fin else
                    }//fin if
                    
            }//fin else
        }//fin else
    }//fin else
    //FIN CAMPO 38
    
    //CAMPO 47
    $numero_orden_campo_actual=47;
    $posiciones_iguales_a_5_array=array();
    $posiciones_iguales_a_5_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"5");
    if(count($posiciones_iguales_a_5_array)>0 && is_array($posiciones_iguales_a_5_array))
    {
            //criterio 1
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_5_array);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        $posiciones_iguales_a_4_array=array();
        $posiciones_iguales_a_4_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"4");
        if(count($posiciones_iguales_a_4_array)>0 && is_array($posiciones_iguales_a_4_array))
        {
                //criterio 2
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_4_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            $posiciones_iguales_a_3_array=array();
            $posiciones_iguales_a_3_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"3");
            if(count($posiciones_iguales_a_3_array)>0 && is_array($posiciones_iguales_a_3_array))
            {
                    //criterio 3
                    $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_3_array);
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
            }//fin if
            else
            {
                    $posiciones_iguales_a_2_array=array();
                    $posiciones_iguales_a_2_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"2");
                    if(count($posiciones_iguales_a_2_array)>0 && is_array($posiciones_iguales_a_2_array))
                    {
                        //criterio 4
                        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_2_array);
                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                    }//fin if
                    else
                    {
                        $posiciones_iguales_a_1_array=array();
                        $posiciones_iguales_a_1_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
                        if(count($posiciones_iguales_a_1_array)>0 && is_array($posiciones_iguales_a_1_array))
                        {
                                //criterio 5
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                        }//fin if
                        else
                        {
                                $array_varios_valores=array(
                                        "16",
                                        "17",
                                        "18",
                                        "19",
                                        "20",
                                        "22"
                                        );
            
                                $array_iguales_a_16_17_18_19_20_22=array();
                                $array_iguales_a_16_17_18_19_20_22=iguales_a_varios_valores($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_varios_valores);
                                
                                if(count($array_iguales_a_16_17_18_19_20_22)>0 && is_array($array_iguales_a_16_17_18_19_20_22))
                                {
                                    //criterio 6
                                    $mayor_valor_mayor_frecuencia="";
                                    $mayor_valor_mayor_frecuencia=calcula_mayor_valor_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_16_17_18_19_20_22);
                                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                                    
                                }//fin if
                                else
                                {
                                    $posiciones_iguales_a_0_array=array();
                                    $posiciones_iguales_a_0_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
                                    if(count($posiciones_iguales_a_0_array)>0 && is_array($posiciones_iguales_a_0_array))
                                    {
                                            //criterio 7
                                            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_0_array);
                                            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                                    }//fin if
                                    else
                                    {
                                            //criterio para no dejar faltante
                                            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                                    }
                                }//fin if
                        }//fin else
                    }//fin if
                    
            }//fin else
        }//fin else
    }//fin else
    //FIN CAMPO 47
    
    //CAMPO 39
    $numero_orden_campo_actual=39;
    $posiciones_iguales_a_5_array=array();
    $posiciones_iguales_a_5_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"5");
    if(count($posiciones_iguales_a_5_array)>0 && is_array($posiciones_iguales_a_5_array))
    {
            //criterio 1
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_5_array);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        $posiciones_iguales_a_4_array=array();
        $posiciones_iguales_a_4_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"4");
        if(count($posiciones_iguales_a_4_array)>0 && is_array($posiciones_iguales_a_4_array))
        {
                //criterio 2
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_4_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
                $array_varios_valores=array(
                                        "16",
                                        "17",
                                        "18",
                                        "19",
                                        "20",
                                        "22"
                                        );
            
            $array_iguales_a_16_17_18_19_20_22=array();
            $array_iguales_a_16_17_18_19_20_22=iguales_a_varios_valores($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_varios_valores);
                
                if(count($array_iguales_a_16_17_18_19_20_22)>0 && is_array($array_iguales_a_16_17_18_19_20_22))
                {
                    //criterio 6
                    $mayor_valor_mayor_frecuencia="";
                    $mayor_valor_mayor_frecuencia=calcula_mayor_valor_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_16_17_18_19_20_22);
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                    
                }//fin if
                else
                {
                    $posiciones_iguales_a_0_array=array();
                    $posiciones_iguales_a_0_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
                    if(count($posiciones_iguales_a_0_array)>0 && is_array($posiciones_iguales_a_0_array))
                    {
                            //criterio 7
                            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_0_array);
                            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                    }//fin if
                    else
                    {
                            //criterio para no dejar faltante
                            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                    }
                }//fin if
        }//fin else
    }//fin else
    //FIN CAMPO 39
    
    //FIN VACUNACION
    
    //CONSULTAS y otros campos de fechas
    
    //CAMPO 52
    $numero_orden_campo_actual=52;
    
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
        $posiciones_iguales_a_1800_01_01_array=array();
        $posiciones_iguales_a_1800_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1800-01-01");
        if(count($posiciones_iguales_a_1800_01_01_array)>0 && is_array($posiciones_iguales_a_1800_01_01_array))
        {
                //criterio 2
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1800_01_01_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
                $matriz_comparaciones=array();
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1805-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1810-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1825-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1830-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1835-01-01");
                
                $array_iguales_a_codigos_desde_1805_hasta_1835=array();
                $array_iguales_a_codigos_desde_1805_hasta_1835=union_arrays_de_matriz_posiciones($matriz_comparaciones);
                
                if(count($array_iguales_a_codigos_desde_1805_hasta_1835)>0 && is_array($array_iguales_a_codigos_desde_1805_hasta_1835))
                {
                    //criterio 3
                    $mayor_valor_mayor_frecuencia="";
                    $mayor_valor_mayor_frecuencia=calcula_mayor_fecha_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_codigos_desde_1805_hasta_1835);
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                    
                }//fin if
                else
                {
                        $posiciones_iguales_a_1845_01_01_array=array();
                        $posiciones_iguales_a_1845_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1845-01-01");
                        if(count($posiciones_iguales_a_1845_01_01_array)>0 && is_array($posiciones_iguales_a_1845_01_01_array))
                        {
                                //criterio 2
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1845_01_01_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                        }//fin if
                        else
                        {
                                //criterio para no dejar faltante
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                        }
                }//fin if
        }//fin else
    }//fin else
    //FIN CAMPO 52
    
    //CAMPO 51
    $numero_orden_campo_actual=51;
    
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
        $posiciones_iguales_a_1800_01_01_array=array();
        $posiciones_iguales_a_1800_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1800-01-01");
        if(count($posiciones_iguales_a_1800_01_01_array)>0 && is_array($posiciones_iguales_a_1800_01_01_array))
        {
                //criterio 2
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1800_01_01_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
                $matriz_comparaciones=array();
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1805-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1810-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1825-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1830-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1835-01-01");
                
                $array_iguales_a_codigos_desde_1805_hasta_1835=array();
                $array_iguales_a_codigos_desde_1805_hasta_1835=union_arrays_de_matriz_posiciones($matriz_comparaciones);
                
                if(count($array_iguales_a_codigos_desde_1805_hasta_1835)>0 && is_array($array_iguales_a_codigos_desde_1805_hasta_1835))
                {
                    //criterio 3
                    $mayor_valor_mayor_frecuencia="";
                    $mayor_valor_mayor_frecuencia=calcula_mayor_fecha_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_codigos_desde_1805_hasta_1835);
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                    
                }//fin if
                else
                {
                        $posiciones_iguales_a_1845_01_01_array=array();
                        $posiciones_iguales_a_1845_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1845-01-01");
                        if(count($posiciones_iguales_a_1845_01_01_array)>0 && is_array($posiciones_iguales_a_1845_01_01_array))
                        {
                                //criterio 2
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1845_01_01_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                        }//fin if
                        else
                        {
                                //criterio para no dejar faltante
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                        }
                }//fin if
        }//fin else
    }//fin else
    //FIN CAMPO 51
    
    
    
    
    //CAMPO 62
    $numero_orden_campo_actual=62;
    
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
        $posiciones_iguales_a_1800_01_01_array=array();
        $posiciones_iguales_a_1800_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1800-01-01");
        if(count($posiciones_iguales_a_1800_01_01_array)>0 && is_array($posiciones_iguales_a_1800_01_01_array))
        {
                //criterio 2
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1800_01_01_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
                $matriz_comparaciones=array();
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1805-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1810-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1825-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1830-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1835-01-01");
                
                $array_iguales_a_codigos_desde_1805_hasta_1835=array();
                $array_iguales_a_codigos_desde_1805_hasta_1835=union_arrays_de_matriz_posiciones($matriz_comparaciones);
                
                if(count($array_iguales_a_codigos_desde_1805_hasta_1835)>0 && is_array($array_iguales_a_codigos_desde_1805_hasta_1835))
                {
                    //criterio 3
                    $mayor_valor_mayor_frecuencia="";
                    $mayor_valor_mayor_frecuencia=calcula_mayor_fecha_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_codigos_desde_1805_hasta_1835);
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                    
                }//fin if
                else
                {
                        $posiciones_iguales_a_1845_01_01_array=array();
                        $posiciones_iguales_a_1845_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1845-01-01");
                        if(count($posiciones_iguales_a_1845_01_01_array)>0 && is_array($posiciones_iguales_a_1845_01_01_array))
                        {
                                //criterio 2
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1845_01_01_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                        }//fin if
                        else
                        {
                                //criterio para no dejar faltante
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                        }
                }//fin if
        }//fin else
    }//fin else
    //FIN CAMPO 62
    
    //CAMPO 63
    $numero_orden_campo_actual=63;
    
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
        $posiciones_iguales_a_1800_01_01_array=array();
        $posiciones_iguales_a_1800_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1800-01-01");
        if(count($posiciones_iguales_a_1800_01_01_array)>0 && is_array($posiciones_iguales_a_1800_01_01_array))
        {
                //criterio 2
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1800_01_01_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
                $matriz_comparaciones=array();
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1805-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1810-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1825-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1830-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1835-01-01");
                
                $array_iguales_a_codigos_desde_1805_hasta_1835=array();
                $array_iguales_a_codigos_desde_1805_hasta_1835=union_arrays_de_matriz_posiciones($matriz_comparaciones);
                
                if(count($array_iguales_a_codigos_desde_1805_hasta_1835)>0 && is_array($array_iguales_a_codigos_desde_1805_hasta_1835))
                {
                    //criterio 3
                    $mayor_valor_mayor_frecuencia="";
                    $mayor_valor_mayor_frecuencia=calcula_mayor_fecha_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_codigos_desde_1805_hasta_1835);
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                    
                }//fin if
                else
                {
                        $posiciones_iguales_a_1845_01_01_array=array();
                        $posiciones_iguales_a_1845_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1845-01-01");
                        if(count($posiciones_iguales_a_1845_01_01_array)>0 && is_array($posiciones_iguales_a_1845_01_01_array))
                        {
                                //criterio 2
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1845_01_01_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                        }//fin if
                        else
                        {
                                //criterio para no dejar faltante
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                        }
                }//fin if
        }//fin else
    }//fin else
    //FIN CAMPO 63
    
    //CAMPO 65
    $numero_orden_campo_actual=65;
    
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
        $posiciones_iguales_a_1800_01_01_array=array();
        $posiciones_iguales_a_1800_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1800-01-01");
        if(count($posiciones_iguales_a_1800_01_01_array)>0 && is_array($posiciones_iguales_a_1800_01_01_array))
        {
                //criterio 2
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1800_01_01_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
                $matriz_comparaciones=array();
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1805-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1810-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1825-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1830-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1835-01-01");
                
                $array_iguales_a_codigos_desde_1805_hasta_1835=array();
                $array_iguales_a_codigos_desde_1805_hasta_1835=union_arrays_de_matriz_posiciones($matriz_comparaciones);
                
                if(count($array_iguales_a_codigos_desde_1805_hasta_1835)>0 && is_array($array_iguales_a_codigos_desde_1805_hasta_1835))
                {
                    //criterio 3
                    $mayor_valor_mayor_frecuencia="";
                    $mayor_valor_mayor_frecuencia=calcula_mayor_fecha_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_codigos_desde_1805_hasta_1835);
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                    
                }//fin if
                else
                {
                        $posiciones_iguales_a_1845_01_01_array=array();
                        $posiciones_iguales_a_1845_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1845-01-01");
                        if(count($posiciones_iguales_a_1845_01_01_array)>0 && is_array($posiciones_iguales_a_1845_01_01_array))
                        {
                                //criterio 2
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1845_01_01_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                        }//fin if
                        else
                        {
                                //criterio para no dejar faltante
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                        }
                }//fin if
        }//fin else
    }//fin else
    //FIN CAMPO 65
    
    //CAMPO 66
    $numero_orden_campo_actual=66;
    
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
        $posiciones_iguales_a_1800_01_01_array=array();
        $posiciones_iguales_a_1800_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1800-01-01");
        if(count($posiciones_iguales_a_1800_01_01_array)>0 && is_array($posiciones_iguales_a_1800_01_01_array))
        {
                //criterio 2
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1800_01_01_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
                $matriz_comparaciones=array();
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1805-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1810-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1825-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1830-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1835-01-01");
                
                $array_iguales_a_codigos_desde_1805_hasta_1835=array();
                $array_iguales_a_codigos_desde_1805_hasta_1835=union_arrays_de_matriz_posiciones($matriz_comparaciones);
                
                if(count($array_iguales_a_codigos_desde_1805_hasta_1835)>0 && is_array($array_iguales_a_codigos_desde_1805_hasta_1835))
                {
                    //criterio 3
                    $mayor_valor_mayor_frecuencia="";
                    $mayor_valor_mayor_frecuencia=calcula_mayor_fecha_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_codigos_desde_1805_hasta_1835);
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                    
                }//fin if
                else
                {
                        $posiciones_iguales_a_1845_01_01_array=array();
                        $posiciones_iguales_a_1845_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1845-01-01");
                        if(count($posiciones_iguales_a_1845_01_01_array)>0 && is_array($posiciones_iguales_a_1845_01_01_array))
                        {
                                //criterio 2
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1845_01_01_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                        }//fin if
                        else
                        {
                                //criterio para no dejar faltante
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                        }
                }//fin if
        }//fin else
    }//fin else
    //FIN CAMPO 66
    
    
    
    //CAMPO 68
    $numero_orden_campo_actual=68;
    
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
        $posiciones_iguales_a_1800_01_01_array=array();
        $posiciones_iguales_a_1800_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1800-01-01");
        if(count($posiciones_iguales_a_1800_01_01_array)>0 && is_array($posiciones_iguales_a_1800_01_01_array))
        {
                //criterio 2
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1800_01_01_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
                $matriz_comparaciones=array();
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1805-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1810-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1825-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1830-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1835-01-01");
                
                $array_iguales_a_codigos_desde_1805_hasta_1835=array();
                $array_iguales_a_codigos_desde_1805_hasta_1835=union_arrays_de_matriz_posiciones($matriz_comparaciones);
                
                if(count($array_iguales_a_codigos_desde_1805_hasta_1835)>0 && is_array($array_iguales_a_codigos_desde_1805_hasta_1835))
                {
                    //criterio 3
                    $mayor_valor_mayor_frecuencia="";
                    $mayor_valor_mayor_frecuencia=calcula_mayor_fecha_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_codigos_desde_1805_hasta_1835);
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                    
                }//fin if
                else
                {
                        $posiciones_iguales_a_1845_01_01_array=array();
                        $posiciones_iguales_a_1845_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1845-01-01");
                        if(count($posiciones_iguales_a_1845_01_01_array)>0 && is_array($posiciones_iguales_a_1845_01_01_array))
                        {
                                //criterio 2
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1845_01_01_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                        }//fin if
                        else
                        {
                                //criterio para no dejar faltante
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                        }
                }//fin if
        }//fin else
    }//fin else
    //FIN CAMPO 68
    
    
    
    
    
    //CAMPO 80
    //aspciado campo 81
    $numero_orden_campo_actual=80;
    
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

            $orden_ultimo_registro_campo_actual=$orden_mayor_fecha_campo_actual;
                        
            $numero_campo_dependiente=81;
            $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
    }//fin if
    else
    {
        $posiciones_iguales_a_1800_01_01_array=array();
        $posiciones_iguales_a_1800_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1800-01-01");
        if(count($posiciones_iguales_a_1800_01_01_array)>0 && is_array($posiciones_iguales_a_1800_01_01_array))
        {
                //criterio 2
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1800_01_01_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                        
                $numero_campo_dependiente=81;
                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
        }//fin if
        else
        {
                $matriz_comparaciones=array();
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1805-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1810-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1825-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1830-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1835-01-01");
                
                $array_iguales_a_codigos_desde_1805_hasta_1835=array();
                $array_iguales_a_codigos_desde_1805_hasta_1835=union_arrays_de_matriz_posiciones($matriz_comparaciones);
                
                if(count($array_iguales_a_codigos_desde_1805_hasta_1835)>0 && is_array($array_iguales_a_codigos_desde_1805_hasta_1835))
                {
                    //criterio 3
                    $mayor_valor_mayor_frecuencia="";
                    $mayor_valor_mayor_frecuencia=calcula_mayor_fecha_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_codigos_desde_1805_hasta_1835);
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;

                    $posiciones_iguales_a_mayor_frecuencia_array=array();
                    $posiciones_iguales_a_mayor_frecuencia_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$mayor_valor_mayor_frecuencia);

                    $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_mayor_frecuencia_array);

                    $numero_campo_dependiente=81;
                    $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                    
                }//fin if
                else
                {
                        $posiciones_iguales_a_1845_01_01_array=array();
                        $posiciones_iguales_a_1845_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1845-01-01");
                        if(count($posiciones_iguales_a_1845_01_01_array)>0 && is_array($posiciones_iguales_a_1845_01_01_array))
                        {
                                //criterio 2
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1845_01_01_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                        
                                $numero_campo_dependiente=81;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                        }//fin if
                        else
                        {
                                //criterio para no dejar faltante
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];

                                $numero_campo_dependiente=81;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[0][$numero_campo_dependiente];
                        }
                }//fin if
        }//fin else
    }//fin else
    //FIN CAMPO 80
    
    //CAMPO 82
    $numero_orden_campo_actual=82;
    
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
        $posiciones_iguales_a_1800_01_01_array=array();
        $posiciones_iguales_a_1800_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1800-01-01");
        if(count($posiciones_iguales_a_1800_01_01_array)>0 && is_array($posiciones_iguales_a_1800_01_01_array))
        {
                //criterio 2
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1800_01_01_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
                $matriz_comparaciones=array();
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1805-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1810-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1825-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1830-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1835-01-01");
                
                $array_iguales_a_codigos_desde_1805_hasta_1835=array();
                $array_iguales_a_codigos_desde_1805_hasta_1835=union_arrays_de_matriz_posiciones($matriz_comparaciones);
                
                if(count($array_iguales_a_codigos_desde_1805_hasta_1835)>0 && is_array($array_iguales_a_codigos_desde_1805_hasta_1835))
                {
                    //criterio 3
                    $mayor_valor_mayor_frecuencia="";
                    $mayor_valor_mayor_frecuencia=calcula_mayor_fecha_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_codigos_desde_1805_hasta_1835);
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                    
                }//fin if
                else
                {
                        $posiciones_iguales_a_1845_01_01_array=array();
                        $posiciones_iguales_a_1845_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1845-01-01");
                        if(count($posiciones_iguales_a_1845_01_01_array)>0 && is_array($posiciones_iguales_a_1845_01_01_array))
                        {
                                //criterio 2
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1845_01_01_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                        }//fin if
                        else
                        {
                                //criterio para no dejar faltante
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                        }
                }//fin if
        }//fin else
    }//fin else
    //FIN CAMPO 82
    
    //CAMPO 84
    //asociado campo 85
    $numero_orden_campo_actual=84;
    
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

            $orden_ultimo_registro_campo_actual=$orden_mayor_fecha_campo_actual;

            $numero_campo_dependiente=85;
                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
    }//fin if
    else
    {
        $posiciones_iguales_a_1800_01_01_array=array();
        $posiciones_iguales_a_1800_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1800-01-01");
        if(count($posiciones_iguales_a_1800_01_01_array)>0 && is_array($posiciones_iguales_a_1800_01_01_array))
        {
                //criterio 2
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1800_01_01_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];

                $numero_campo_dependiente=85;
                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
        }//fin if
        else
        {
                $matriz_comparaciones=array();
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1805-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1810-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1825-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1830-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1835-01-01");
                
                $array_iguales_a_codigos_desde_1805_hasta_1835=array();
                $array_iguales_a_codigos_desde_1805_hasta_1835=union_arrays_de_matriz_posiciones($matriz_comparaciones);
                
                if(count($array_iguales_a_codigos_desde_1805_hasta_1835)>0 && is_array($array_iguales_a_codigos_desde_1805_hasta_1835))
                {
                    //criterio 3
                    $mayor_valor_mayor_frecuencia="";
                    $mayor_valor_mayor_frecuencia=calcula_mayor_fecha_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_codigos_desde_1805_hasta_1835);
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;

                    $posiciones_iguales_a_mayor_frecuencia_array=array();
                    $posiciones_iguales_a_mayor_frecuencia_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$mayor_valor_mayor_frecuencia);

                    $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_mayor_frecuencia_array);

                    $numero_campo_dependiente=85;
                    $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                    
                }//fin if
                else
                {
                        $posiciones_iguales_a_1845_01_01_array=array();
                        $posiciones_iguales_a_1845_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1845-01-01");
                        if(count($posiciones_iguales_a_1845_01_01_array)>0 && is_array($posiciones_iguales_a_1845_01_01_array))
                        {
                                //criterio 2
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1845_01_01_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];

                                $numero_campo_dependiente=85;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                        }//fin if
                        else
                        {
                                //criterio para no dejar faltante
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];

                                $numero_campo_dependiente=85;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[0][$numero_campo_dependiente];
                        }
                }//fin if
        }//fin else
    }//fin else
    
    //FIN CAMPO 84
    
    
    
    
    
    
    
    //CAMPO 103
    //asociado a CAMPO 104
    $numero_orden_campo_actual=103;
    
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

            $orden_ultimo_registro_campo_actual=$orden_mayor_fecha_campo_actual;
            
            $numero_campo_dependiente=104;
            $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
    }//fin if
    else
    {
        $posiciones_iguales_a_1800_01_01_array=array();
        $posiciones_iguales_a_1800_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1800-01-01");
        if(count($posiciones_iguales_a_1800_01_01_array)>0 && is_array($posiciones_iguales_a_1800_01_01_array))
        {
                //criterio 2
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1800_01_01_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                
                $numero_campo_dependiente=104;
                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
        }//fin if
        else
        {
                $matriz_comparaciones=array();
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1805-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1810-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1825-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1830-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1835-01-01");
                
                $array_iguales_a_codigos_desde_1805_hasta_1835=array();
                $array_iguales_a_codigos_desde_1805_hasta_1835=union_arrays_de_matriz_posiciones($matriz_comparaciones);
                
                if(count($array_iguales_a_codigos_desde_1805_hasta_1835)>0 && is_array($array_iguales_a_codigos_desde_1805_hasta_1835))
                {
                    //criterio 3
                    $mayor_valor_mayor_frecuencia="";
                    $mayor_valor_mayor_frecuencia=calcula_mayor_fecha_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_codigos_desde_1805_hasta_1835);
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                    
                    $posiciones_iguales_a_valor_mayor_frecuencia=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$mayor_valor_mayor_frecuencia);
                    $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_valor_mayor_frecuencia);
                    
                    $numero_campo_dependiente=104;
                    $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                    
                }//fin if
                else
                {
                        $posiciones_iguales_a_1845_01_01_array=array();
                        $posiciones_iguales_a_1845_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1845-01-01");
                        if(count($posiciones_iguales_a_1845_01_01_array)>0 && is_array($posiciones_iguales_a_1845_01_01_array))
                        {
                                //criterio 2
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1845_01_01_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                                
                                $numero_campo_dependiente=104;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                        }//fin if
                        else
                        {
                                //criterio para no dejar faltante
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                                
                                $numero_campo_dependiente=104;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[0][$numero_campo_dependiente];
                        }
                }//fin if
        }//fin else
    }//fin else
    //FIN CAMPO 103
    
    //CAMPO 105
    $numero_orden_campo_actual=105;
    
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
        $posiciones_iguales_a_1800_01_01_array=array();
        $posiciones_iguales_a_1800_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1800-01-01");
        if(count($posiciones_iguales_a_1800_01_01_array)>0 && is_array($posiciones_iguales_a_1800_01_01_array))
        {
                //criterio 2
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1800_01_01_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
                $matriz_comparaciones=array();
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1805-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1810-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1825-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1830-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1835-01-01");
                
                $array_iguales_a_codigos_desde_1805_hasta_1835=array();
                $array_iguales_a_codigos_desde_1805_hasta_1835=union_arrays_de_matriz_posiciones($matriz_comparaciones);
                
                if(count($array_iguales_a_codigos_desde_1805_hasta_1835)>0 && is_array($array_iguales_a_codigos_desde_1805_hasta_1835))
                {
                    //criterio 3
                    $mayor_valor_mayor_frecuencia="";
                    $mayor_valor_mayor_frecuencia=calcula_mayor_fecha_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_codigos_desde_1805_hasta_1835);
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                    
                }//fin if
                else
                {
                        $posiciones_iguales_a_1845_01_01_array=array();
                        $posiciones_iguales_a_1845_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1845-01-01");
                        if(count($posiciones_iguales_a_1845_01_01_array)>0 && is_array($posiciones_iguales_a_1845_01_01_array))
                        {
                                //criterio 2
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1845_01_01_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                        }//fin if
                        else
                        {
                                //criterio para no dejar faltante
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                        }
                }//fin if
        }//fin else
    }//fin else
    //FIN CAMPO 105
    
    //CAMPO 106
    //asociado CAMPO 107
    $numero_orden_campo_actual=106;
    
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
            
            $orden_ultimo_registro_campo_actual=$orden_mayor_fecha_campo_actual;

            $numero_campo_dependiente=107;
            $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
    }//fin if
    else
    {
        $posiciones_iguales_a_1800_01_01_array=array();
        $posiciones_iguales_a_1800_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1800-01-01");
        if(count($posiciones_iguales_a_1800_01_01_array)>0 && is_array($posiciones_iguales_a_1800_01_01_array))
        {
                //criterio 2
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1800_01_01_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                
                $numero_campo_dependiente=107;
                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
        }//fin if
        else
        {
                $matriz_comparaciones=array();
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1805-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1810-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1825-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1830-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1835-01-01");
                
                $array_iguales_a_codigos_desde_1805_hasta_1835=array();
                $array_iguales_a_codigos_desde_1805_hasta_1835=union_arrays_de_matriz_posiciones($matriz_comparaciones);
                
                if(count($array_iguales_a_codigos_desde_1805_hasta_1835)>0 && is_array($array_iguales_a_codigos_desde_1805_hasta_1835))
                {
                    //criterio 3
                    $mayor_valor_mayor_frecuencia="";
                    $mayor_valor_mayor_frecuencia=calcula_mayor_fecha_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_codigos_desde_1805_hasta_1835);
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                    
                    $posiciones_iguales_a_valor_mayor_frecuencia=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$mayor_valor_mayor_frecuencia);
                    $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_valor_mayor_frecuencia);
                    
                    $numero_campo_dependiente=107;
                    $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                    
                }//fin if
                else
                {
                        $posiciones_iguales_a_1845_01_01_array=array();
                        $posiciones_iguales_a_1845_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1845-01-01");
                        if(count($posiciones_iguales_a_1845_01_01_array)>0 && is_array($posiciones_iguales_a_1845_01_01_array))
                        {
                                //criterio 4
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1845_01_01_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                                
                                $numero_campo_dependiente=107;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                        }//fin if
                        else
                        {
                                //criterio para no dejar faltante
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                                
                                $numero_campo_dependiente=107;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[0][$numero_campo_dependiente];
                        }
                }//fin if
        }//fin else
    }//fin else
    //FIN CAMPO 106
    
    //CAMPO 108
    //asociado CAMPO 109
    $numero_orden_campo_actual=108;
    
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

            $orden_ultimo_registro_campo_actual=$orden_mayor_fecha_campo_actual;
            
            $numero_campo_dependiente=109;
            $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
    }//fin if
    else
    {
        $posiciones_iguales_a_1800_01_01_array=array();
        $posiciones_iguales_a_1800_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1800-01-01");
        if(count($posiciones_iguales_a_1800_01_01_array)>0 && is_array($posiciones_iguales_a_1800_01_01_array))
        {
                //criterio 2
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1800_01_01_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                
                $numero_campo_dependiente=109;
                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
        }//fin if
        else
        {
                $matriz_comparaciones=array();
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1805-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1810-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1825-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1830-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1835-01-01");
                
                $array_iguales_a_codigos_desde_1805_hasta_1835=array();
                $array_iguales_a_codigos_desde_1805_hasta_1835=union_arrays_de_matriz_posiciones($matriz_comparaciones);
                
                if(count($array_iguales_a_codigos_desde_1805_hasta_1835)>0 && is_array($array_iguales_a_codigos_desde_1805_hasta_1835))
                {
                    //criterio 3
                    $mayor_valor_mayor_frecuencia="";
                    $mayor_valor_mayor_frecuencia=calcula_mayor_fecha_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_codigos_desde_1805_hasta_1835);
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                    
                    $posiciones_iguales_a_valor_mayor_frecuencia=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$mayor_valor_mayor_frecuencia);
                    $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_valor_mayor_frecuencia);
                    
                    $numero_campo_dependiente=109;
                    $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                    
                }//fin if
                else
                {
                        $posiciones_iguales_a_1845_01_01_array=array();
                        $posiciones_iguales_a_1845_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1845-01-01");
                        if(count($posiciones_iguales_a_1845_01_01_array)>0 && is_array($posiciones_iguales_a_1845_01_01_array))
                        {
                                //criterio 4
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1845_01_01_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                                
                                $numero_campo_dependiente=109;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                        }//fin if
                        else
                        {
                                //criterio para no dejar faltante
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                                
                                $numero_campo_dependiente=109;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[0][$numero_campo_dependiente];
                        }
                }//fin if
        }//fin else
    }//fin else
    //FIN CAMPO 108
    
    //CAMPO 110
    $numero_orden_campo_actual=110;
    
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
        $posiciones_iguales_a_1800_01_01_array=array();
        $posiciones_iguales_a_1800_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1800-01-01");
        if(count($posiciones_iguales_a_1800_01_01_array)>0 && is_array($posiciones_iguales_a_1800_01_01_array))
        {
                //criterio 2
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1800_01_01_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
                $matriz_comparaciones=array();
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1805-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1810-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1825-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1830-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1835-01-01");
                
                $array_iguales_a_codigos_desde_1805_hasta_1835=array();
                $array_iguales_a_codigos_desde_1805_hasta_1835=union_arrays_de_matriz_posiciones($matriz_comparaciones);
                
                if(count($array_iguales_a_codigos_desde_1805_hasta_1835)>0 && is_array($array_iguales_a_codigos_desde_1805_hasta_1835))
                {
                    //criterio 3
                    $mayor_valor_mayor_frecuencia="";
                    $mayor_valor_mayor_frecuencia=calcula_mayor_fecha_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_codigos_desde_1805_hasta_1835);
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                    
                }//fin if
                else
                {
                        $posiciones_iguales_a_1845_01_01_array=array();
                        $posiciones_iguales_a_1845_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1845-01-01");
                        if(count($posiciones_iguales_a_1845_01_01_array)>0 && is_array($posiciones_iguales_a_1845_01_01_array))
                        {
                                //criterio 2
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1845_01_01_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                        }//fin if
                        else
                        {
                                //criterio para no dejar faltante
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                        }
                }//fin if
        }//fin else
    }//fin else
    //FIN CAMPO 110
    
    //CAMPO 111
    $numero_orden_campo_actual=111;
    
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
        $posiciones_iguales_a_1800_01_01_array=array();
        $posiciones_iguales_a_1800_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1800-01-01");
        if(count($posiciones_iguales_a_1800_01_01_array)>0 && is_array($posiciones_iguales_a_1800_01_01_array))
        {
                //criterio 2
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1800_01_01_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
                $matriz_comparaciones=array();
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1805-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1810-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1825-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1830-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1835-01-01");
                
                $array_iguales_a_codigos_desde_1805_hasta_1835=array();
                $array_iguales_a_codigos_desde_1805_hasta_1835=union_arrays_de_matriz_posiciones($matriz_comparaciones);
                
                if(count($array_iguales_a_codigos_desde_1805_hasta_1835)>0 && is_array($array_iguales_a_codigos_desde_1805_hasta_1835))
                {
                    //criterio 3
                    $mayor_valor_mayor_frecuencia="";
                    $mayor_valor_mayor_frecuencia=calcula_mayor_fecha_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_codigos_desde_1805_hasta_1835);
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                    
                }//fin if
                else
                {
                        $posiciones_iguales_a_1845_01_01_array=array();
                        $posiciones_iguales_a_1845_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1845-01-01");
                        if(count($posiciones_iguales_a_1845_01_01_array)>0 && is_array($posiciones_iguales_a_1845_01_01_array))
                        {
                                //criterio 2
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1845_01_01_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                        }//fin if
                        else
                        {
                                //criterio para no dejar faltante
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                        }
                }//fin if
        }//fin else
    }//fin else
    //FIN CAMPO 111
    
    //CAMPO 112
    //asociado CAMPO 113
    $numero_orden_campo_actual=112;
    
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

            $orden_ultimo_registro_campo_actual=$orden_mayor_fecha_campo_actual;
            
            $numero_campo_dependiente=113;
            $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
    }//fin if
    else
    {
        $posiciones_iguales_a_1800_01_01_array=array();
        $posiciones_iguales_a_1800_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1800-01-01");
        if(count($posiciones_iguales_a_1800_01_01_array)>0 && is_array($posiciones_iguales_a_1800_01_01_array))
        {
                //criterio 2
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1800_01_01_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                
                $numero_campo_dependiente=113;
                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
        }//fin if
        else
        {
                $matriz_comparaciones=array();
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1805-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1810-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1825-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1830-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1835-01-01");
                
                $array_iguales_a_codigos_desde_1805_hasta_1835=array();
                $array_iguales_a_codigos_desde_1805_hasta_1835=union_arrays_de_matriz_posiciones($matriz_comparaciones);
                
                if(count($array_iguales_a_codigos_desde_1805_hasta_1835)>0 && is_array($array_iguales_a_codigos_desde_1805_hasta_1835))
                {
                    //criterio 3
                    $mayor_valor_mayor_frecuencia="";
                    $mayor_valor_mayor_frecuencia=calcula_mayor_fecha_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_codigos_desde_1805_hasta_1835);
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                    
                    $posiciones_iguales_a_valor_mayor_frecuencia=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$mayor_valor_mayor_frecuencia);
                    $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_valor_mayor_frecuencia);
                    
                    $numero_campo_dependiente=113;
                    $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                    
                }//fin if
                else
                {
                        $posiciones_iguales_a_1845_01_01_array=array();
                        $posiciones_iguales_a_1845_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1845-01-01");
                        if(count($posiciones_iguales_a_1845_01_01_array)>0 && is_array($posiciones_iguales_a_1845_01_01_array))
                        {
                                //criterio 4
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1845_01_01_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                                
                                $numero_campo_dependiente=113;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                        }//fin if
                        else
                        {
                                //criterio para no dejar faltante
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                                
                                $numero_campo_dependiente=113;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[0][$numero_campo_dependiente];
                        }
                }//fin if
        }//fin else
    }//fin else
    //FIN CAMPO 112
    
    //CAMPO 118
    $numero_orden_campo_actual=118;
    
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
        $posiciones_iguales_a_1800_01_01_array=array();
        $posiciones_iguales_a_1800_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1800-01-01");
        if(count($posiciones_iguales_a_1800_01_01_array)>0 && is_array($posiciones_iguales_a_1800_01_01_array))
        {
                //criterio 2
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1800_01_01_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
                $matriz_comparaciones=array();
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1805-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1810-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1825-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1830-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1835-01-01");
                
                $array_iguales_a_codigos_desde_1805_hasta_1835=array();
                $array_iguales_a_codigos_desde_1805_hasta_1835=union_arrays_de_matriz_posiciones($matriz_comparaciones);
                
                if(count($array_iguales_a_codigos_desde_1805_hasta_1835)>0 && is_array($array_iguales_a_codigos_desde_1805_hasta_1835))
                {
                    //criterio 3
                    $mayor_valor_mayor_frecuencia="";
                    $mayor_valor_mayor_frecuencia=calcula_mayor_fecha_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_codigos_desde_1805_hasta_1835);
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                    
                }//fin if
                else
                {
                        $posiciones_iguales_a_1845_01_01_array=array();
                        $posiciones_iguales_a_1845_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1845-01-01");
                        if(count($posiciones_iguales_a_1845_01_01_array)>0 && is_array($posiciones_iguales_a_1845_01_01_array))
                        {
                                //criterio 2
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1845_01_01_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                        }//fin if
                        else
                        {
                                //criterio para no dejar faltante
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                        }
                }//fin if
        }//fin else
    }//fin else
    //FIN CAMPO 118
    
    //FIN CONSULTAS y otros campos de fechas
    
    
    //ENFERMEDAD MENTAL
    
    //CAMPO 25
    //asociado al CAMPO 77
    $numero_orden_campo_actual=25;
    $matriz_comparaciones=array();
        $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
        $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"2");
        $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"3");
        $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"4");
        $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"5");
        $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"6");
        $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"7");
        
        $array_iguales_a_1_2_3_4_5_6_7=array();
        $array_iguales_a_1_2_3_4_5_6_7=union_arrays_de_matriz_posiciones($matriz_comparaciones);
        
        if(count($array_iguales_a_1_2_3_4_5_6_7)>0 && is_array($array_iguales_a_1_2_3_4_5_6_7))
        {
            //criterio 1
            $mayor_valor_mayor_frecuencia="";
            $mayor_valor_mayor_frecuencia=calcula_mayor_valor_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_1_2_3_4_5_6_7);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
            
            $posiciones_iguales_al_mayor_valor=array();
            $posiciones_iguales_al_mayor_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$mayor_valor_mayor_frecuencia);
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_al_mayor_valor);
            $arreglo_campos_reparados[77]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][77];
            
        }//fin if
        else
        {
            $posiciones_iguales_a_21_array=array();
            $posiciones_iguales_a_21_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"21");
            if(count($posiciones_iguales_a_21_array)>0 && is_array($posiciones_iguales_a_21_array))
            {
                    //criterio 3
                    $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_21_array);
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                    
                    $arreglo_campos_reparados[77]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][77];
            }//fin if
            else
            {
                    //criterio para no dejar faltante
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                    
                    $numero_campo_dependiente=77;
                    $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[0][$numero_campo_dependiente];
            }
        }//fin if
    //FIN CAMPO 25
    
    //FIN ENFERMEDAD MENTAL
    
    //GESTACION
    
    //CAMPO 49
    //asociado a los campos: CAMPO 14, CAMPO 15, CAMPO 16
    //asociado a: CAMPO 33 , l CAMPO 50, CAMPO 51,l CAMPO 56 , CAMPO 57, CAMPO 58
    //asociado a: CAMPO 59, CAMPO 60, CAMPO 61, l CAMPO 78, CAMPO 79 
    $numero_orden_campo_actual=49;
    
    $es_campo_49_fecha_calendario_o_1800=false;
    
    $matriz_comparaciones=array();
    $matriz_comparaciones[]=mayor_a_fecha_pos_array($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1900-12-31");
    $matriz_comparaciones[]=menor_igual_a_fecha_pos_array($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,trim($fecha_de_corte));
    
    $posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte=array();
    $posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte)>0 && is_array($posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte))
    {
        $es_campo_49_fecha_calendario_o_1800=true;
            //criterio 1
            $orden_mayor_fecha_campo_actual=maximo_fecha_orden($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_mayor_fecha_campo_actual][$numero_orden_campo_actual];
            
            $orden_ultimo_registro_campo_actual=$orden_mayor_fecha_campo_actual;
            
            $numero_campo_dependiente=14;
            $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
            
            $numero_campo_dependiente=15;
            $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
            $numero_campo_dependiente=115;
            $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
            $numero_campo_dependiente=116;
            $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
            
            $numero_campo_dependiente=16;
            $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
            $numero_campo_dependiente=33;
            $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
            $numero_campo_dependiente=50;
            $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
            $numero_campo_dependiente=51;
            $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
            $numero_campo_dependiente=56;
            $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
            $numero_campo_dependiente=57;
            $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
            $numero_campo_dependiente=58;
            $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
            $numero_campo_dependiente=59;
            $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
            $numero_campo_dependiente=60;
            $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
            $numero_campo_dependiente=61;
            $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
            $numero_campo_dependiente=78;
            $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
            $numero_campo_dependiente=79;
            $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
    }//fin if
    else
    {
        $posiciones_iguales_a_1800_01_01_array=array();
        $posiciones_iguales_a_1800_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1800-01-01");
        if(count($posiciones_iguales_a_1800_01_01_array)>0 && is_array($posiciones_iguales_a_1800_01_01_array))
        {
                $es_campo_49_fecha_calendario_o_1800=true;
                //criterio 2
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1800_01_01_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                
                $numero_campo_dependiente=14;
                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                
                $numero_campo_dependiente=15;
                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                $numero_campo_dependiente=115;
                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                $numero_campo_dependiente=116;
                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                
                $numero_campo_dependiente=16;
                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                $numero_campo_dependiente=33;
                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                $numero_campo_dependiente=50;
                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                $numero_campo_dependiente=51;
                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                $numero_campo_dependiente=56;
                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                $numero_campo_dependiente=57;
                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                $numero_campo_dependiente=58;
                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                $numero_campo_dependiente=59;
                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                $numero_campo_dependiente=60;
                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                $numero_campo_dependiente=61;
                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                $numero_campo_dependiente=78;
                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                $numero_campo_dependiente=79;
                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
        }//fin if
        else
        {
                $posiciones_iguales_a_1845_01_01_array=array();
                $posiciones_iguales_a_1845_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1845-01-01");
                if(count($posiciones_iguales_a_1845_01_01_array)>0 && is_array($posiciones_iguales_a_1845_01_01_array))
                {
                        //criterio 3
                        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1845_01_01_array);
                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                        
                        $numero_campo_dependiente=14;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                        
                        $numero_campo_dependiente=15;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                        $numero_campo_dependiente=115;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                        $numero_campo_dependiente=116;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                        
                        $numero_campo_dependiente=16;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                        $numero_campo_dependiente=33;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                        $numero_campo_dependiente=50;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                        $numero_campo_dependiente=51;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                        $numero_campo_dependiente=56;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                        $numero_campo_dependiente=57;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                        $numero_campo_dependiente=58;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                        $numero_campo_dependiente=59;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                        $numero_campo_dependiente=60;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                        $numero_campo_dependiente=61;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                        $numero_campo_dependiente=78;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                        $numero_campo_dependiente=79;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                }//fin if
                else
                {
                        //criterio para no dejar faltante
                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                        
                        $numero_campo_dependiente=14;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[0][$numero_campo_dependiente];
                        
                        $numero_campo_dependiente=15;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[0][$numero_campo_dependiente];
                        $numero_campo_dependiente=115;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[0][$numero_campo_dependiente];
                        $numero_campo_dependiente=116;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[0][$numero_campo_dependiente];
                        
                        $numero_campo_dependiente=16;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[0][$numero_campo_dependiente];
                        $numero_campo_dependiente=33;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[0][$numero_campo_dependiente];
                        $numero_campo_dependiente=50;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[0][$numero_campo_dependiente];
                        $numero_campo_dependiente=51;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[0][$numero_campo_dependiente];
                        $numero_campo_dependiente=56;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[0][$numero_campo_dependiente];
                        $numero_campo_dependiente=57;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[0][$numero_campo_dependiente];
                        $numero_campo_dependiente=58;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[0][$numero_campo_dependiente];
                        $numero_campo_dependiente=59;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[0][$numero_campo_dependiente];
                        $numero_campo_dependiente=60;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[0][$numero_campo_dependiente];
                        $numero_campo_dependiente=61;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[0][$numero_campo_dependiente];
                        $numero_campo_dependiente=78;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[0][$numero_campo_dependiente];
                        $numero_campo_dependiente=79;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[0][$numero_campo_dependiente];
                } 
        }//fin else
    }//fin else
    //FIN CAMPO 49
    
    //CAMPO 50
    $numero_orden_campo_actual=50;
    if($es_campo_49_fecha_calendario_o_1800==false)
    {
        $posiciones_iguales_a_1845_01_01_array=array();
        $posiciones_iguales_a_1845_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1845-01-01");
        if(count($posiciones_iguales_a_1845_01_01_array)>0 && is_array($posiciones_iguales_a_1845_01_01_array))
        {
                //criterio 3
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1845_01_01_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
                //criterio para no dejar faltante
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
        } 
    }//fin if si el campo 49 no es fecha calendario o 1800-01-01
    //FIN CAMPO 50
    
    //CAMPO 14
    $numero_orden_campo_actual=14;
    if($es_campo_49_fecha_calendario_o_1800==false)
    {
        $posiciones_iguales_a_1_array=array();
        $posiciones_iguales_a_1_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
        if(count($posiciones_iguales_a_1_array)>0 && is_array($posiciones_iguales_a_1_array))
        {
                //criterio 1
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
                $posiciones_iguales_a_2_array=array();
                $posiciones_iguales_a_2_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"2");
                if(count($posiciones_iguales_a_2_array)>0 && is_array($posiciones_iguales_a_2_array))
                {
                        //criterio 3
                        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_2_array);
                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                }//fin if
                else
                {
                        $posiciones_iguales_a_21_array=array();
                        $posiciones_iguales_a_21_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"21");
                        if(count($posiciones_iguales_a_21_array)>0 && is_array($posiciones_iguales_a_21_array))
                        {
                                //criterio 3
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_21_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                        }//fin if
                        else
                        {
                                $posiciones_iguales_a_0_array=array();
                                $posiciones_iguales_a_0_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
                                if(count($posiciones_iguales_a_0_array)>0 && is_array($posiciones_iguales_a_0_array))
                                {
                                        //criterio 3
                                        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_0_array);
                                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                                }//fin if    
                                else
                                {
                                        //criterio para no dejar faltante
                                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                                } 
                        }//fin else
                    
                }//fin else
                
        }//fin else
    }//fin if si el campo 49 no es fecha calendario o 1800-01-01
    //FIN CAMPO 14
    
    
    //CAMPO 16
    $numero_orden_campo_actual=16;
    if($es_campo_49_fecha_calendario_o_1800==false)
    {
        $posiciones_iguales_a_1_array=array();
        $posiciones_iguales_a_1_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
        if(count($posiciones_iguales_a_1_array)>0 && is_array($posiciones_iguales_a_1_array))
        {
                //criterio 1
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
                $posiciones_iguales_a_2_array=array();
                $posiciones_iguales_a_2_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"2");
                if(count($posiciones_iguales_a_2_array)>0 && is_array($posiciones_iguales_a_2_array))
                {
                        //criterio 3
                        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_2_array);
                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                }//fin if
                else
                {
                        $posiciones_iguales_a_21_array=array();
                        $posiciones_iguales_a_21_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"21");
                        if(count($posiciones_iguales_a_21_array)>0 && is_array($posiciones_iguales_a_21_array))
                        {
                                //criterio 3
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_21_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                        }//fin if
                        else
                        {
                                $posiciones_iguales_a_0_array=array();
                                $posiciones_iguales_a_0_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
                                if(count($posiciones_iguales_a_0_array)>0 && is_array($posiciones_iguales_a_0_array))
                                {
                                        //criterio 3
                                        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_0_array);
                                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                                }//fin if  
                                else
                                {
                                        //criterio para no dejar faltante
                                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                                }   
                        }//fin else
                    
                }//fin else
                
        }//fin else
    }//fin if si el campo 49 no es fecha calendario o 1800-01-01
    //FIN CAMPO 16
    
    //CAMPO 33
    $numero_orden_campo_actual=33;
    if($es_campo_49_fecha_calendario_o_1800==false)
    {
        $corte_280="";
        $date_de_corte=$fecha_de_corte;
        $array_fcorte=explode("-",trim($date_de_corte));
        if(checkdate($array_fcorte[1],$array_fcorte[2],$array_fcorte[0]))
        {
            $fecha = date_create(trim($date_de_corte));
            //date sub resta, por lo cual al poner un dia negativo suma
            date_sub($fecha, date_interval_create_from_date_string('-280 days'));
            $corte_280="".date_format($fecha, 'Y-m-d');
        }//fin if
        
        $matriz_comparaciones=array();
        $matriz_comparaciones[]=mayor_a_fecha_pos_array($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1900-12-31");
        $matriz_comparaciones[]=menor_igual_a_fecha_pos_array($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,trim($corte_280));
        
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
            $posiciones_iguales_a_1800_01_01_array=array();
            $posiciones_iguales_a_1800_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1800-01-01");
            if(count($posiciones_iguales_a_1800_01_01_array)>0 && is_array($posiciones_iguales_a_1800_01_01_array))
            {
                    //criterio 2
                    $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1800_01_01_array);
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
            }//fin if
            else
            {
                    $posiciones_iguales_a_1845_01_01_array=array();
                    $posiciones_iguales_a_1845_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1845-01-01");
                    if(count($posiciones_iguales_a_1845_01_01_array)>0 && is_array($posiciones_iguales_a_1845_01_01_array))
                    {
                            //criterio 3
                            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1845_01_01_array);
                            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                    }//fin if
                    else
                    {
                            //criterio para no dejar faltante
                            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                    } 
            }//fin else
        }//fin else
    }//fin if si el campo 49 no es fecha calendario o 1800-01-01
    //FIN CAMPO 33
    
    //CAMPO 56
    $numero_orden_campo_actual=56;
    if($es_campo_49_fecha_calendario_o_1800==false)
    {
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
            $posiciones_iguales_a_1800_01_01_array=array();
            $posiciones_iguales_a_1800_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1800-01-01");
            if(count($posiciones_iguales_a_1800_01_01_array)>0 && is_array($posiciones_iguales_a_1800_01_01_array))
            {
                    //criterio 2
                    $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1800_01_01_array);
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
            }//fin if
            else
            {
                $matriz_comparaciones=array();
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1805-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1810-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1825-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1830-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1835-01-01");
                
                $array_iguales_a_codigos_desde_1805_hasta_1835=array();
                $array_iguales_a_codigos_desde_1805_hasta_1835=union_arrays_de_matriz_posiciones($matriz_comparaciones);
                
                if(count($array_iguales_a_codigos_desde_1805_hasta_1835)>0 && is_array($array_iguales_a_codigos_desde_1805_hasta_1835))
                {
                    //criterio 3
                    $mayor_valor_mayor_frecuencia="";
                    $mayor_valor_mayor_frecuencia=calcula_mayor_fecha_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_codigos_desde_1805_hasta_1835);
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                    
                    
                }//fin if
                else
                {
                        $posiciones_iguales_a_1845_01_01_array=array();
                        $posiciones_iguales_a_1845_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1845-01-01");
                        if(count($posiciones_iguales_a_1845_01_01_array)>0 && is_array($posiciones_iguales_a_1845_01_01_array))
                        {
                                //criterio 4
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1845_01_01_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                                
                        }//fin if
                        else
                        {
                                //criterio para no dejar faltante
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                        } 
                }//fin if
            }//fin else
        }//fin else
    }//fin if si el campo 49 no es fecha calendario o 1800-01-01
    //FIN CAMPO 56
    
    //CAMPO 57
    $numero_orden_campo_actual=57;
    if($es_campo_49_fecha_calendario_o_1800==false)
    {
        $array_varios_valores=array("1",
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
                                    "20",
                                    "21",
                                    "22",
                                    "23",
                                    "24",
                                    "25"
                                    );
        
        $array_iguales_de_1_a_25=array();
        $array_iguales_de_1_a_25=iguales_a_varios_valores($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_varios_valores);
        
        if(count($array_iguales_de_1_a_25)>0 && is_array($array_iguales_de_1_a_25))
        {
            //criterio 2
            $mayor_valor_mayor_frecuencia="";
            $mayor_valor_mayor_frecuencia=calcula_mayor_valor_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_de_1_a_25);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
            
        }//fin if
        else
        {
                $posiciones_iguales_a_999_array=array();
                $posiciones_iguales_a_999_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"999");
                if(count($posiciones_iguales_a_999_array)>0 && is_array($posiciones_iguales_a_999_array))
                {
                        //criterio 3
                        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_999_array);
                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                }//fin if
                else
                {
                        $posiciones_iguales_a_0_array=array();
                        $posiciones_iguales_a_0_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
                        if(count($posiciones_iguales_a_0_array)>0 && is_array($posiciones_iguales_a_0_array))
                        {
                                //criterio 3
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_0_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                        }//fin else
                        else
                        {
                                //criterio para no dejar faltante
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                        } 
                }//fin else
        }//fin else
                
    }//fin if si el campo 49 no es fecha calendario o 1800-01-01
    //FIN CAMPO 57
    
    
    
    //CAMPO 58
    $numero_orden_campo_actual=58;
    if($es_campo_49_fecha_calendario_o_1800==false)
    {
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
            $posiciones_iguales_a_1800_01_01_array=array();
            $posiciones_iguales_a_1800_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1800-01-01");
            if(count($posiciones_iguales_a_1800_01_01_array)>0 && is_array($posiciones_iguales_a_1800_01_01_array))
            {
                    //criterio 2
                    $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1800_01_01_array);
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
            }//fin if
            else
            {
                    $posiciones_iguales_a_1845_01_01_array=array();
                    $posiciones_iguales_a_1845_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1845-01-01");
                    if(count($posiciones_iguales_a_1845_01_01_array)>0 && is_array($posiciones_iguales_a_1845_01_01_array))
                    {
                            //criterio 3
                            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1845_01_01_array);
                            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                    }//fin if
                    else
                    {
                            //criterio para no dejar faltante
                            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                    } 
            }//fin else
        }//fin else
    }//fin if si el campo 49 no es fecha calendario o 1800-01-01
    //FIN CAMPO 58
    
    
    //CAMPO 59
    $numero_orden_campo_actual=59;
    if($es_campo_49_fecha_calendario_o_1800==false)
    {
        $posiciones_iguales_a_1_array=array();
        $posiciones_iguales_a_1_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
        if(count($posiciones_iguales_a_1_array)>0 && is_array($posiciones_iguales_a_1_array))
        {
                //criterio 3
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
        
                $array_varios_valores=array(
                                            "16",
                                            "17",
                                            "18",
                                            "20",
                                            "21"
                                            );
                
                $array_iguales_de_16_a_21=array();
                $array_iguales_de_16_a_21=iguales_a_varios_valores($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_varios_valores);
                
                if(count($array_iguales_de_16_a_21)>0 && is_array($array_iguales_de_16_a_21))
                {
                    //criterio 2
                    $mayor_valor_mayor_frecuencia="";
                    $mayor_valor_mayor_frecuencia=calcula_mayor_valor_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_de_16_a_21);
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                    
                }//fin if
                else
                {
                        $posiciones_iguales_a_0_array=array();
                        $posiciones_iguales_a_0_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
                        if(count($posiciones_iguales_a_0_array)>0 && is_array($posiciones_iguales_a_0_array))
                        {
                                //criterio 3
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_0_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                        }//fin else
                        else
                        {
                                //criterio para no dejar faltante
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                        } 
                }//fin else
        }//fin else
                
    }//fin if si el campo 49 no es fecha calendario o 1800-01-01
    //FIN CAMPO 59
    
    //CAMPO 60
    $numero_orden_campo_actual=60;
    if($es_campo_49_fecha_calendario_o_1800==false)
    {
        $posiciones_iguales_a_1_array=array();
        $posiciones_iguales_a_1_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
        if(count($posiciones_iguales_a_1_array)>0 && is_array($posiciones_iguales_a_1_array))
        {
                //criterio 3
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
        
                $array_varios_valores=array(
                                            "16",
                                            "17",
                                            "18",
                                            "20",
                                            "21"
                                            );
                
                $array_iguales_de_16_a_21=array();
                $array_iguales_de_16_a_21=iguales_a_varios_valores($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_varios_valores);
                
                if(count($array_iguales_de_16_a_21)>0 && is_array($array_iguales_de_16_a_21))
                {
                    //criterio 2
                    $mayor_valor_mayor_frecuencia="";
                    $mayor_valor_mayor_frecuencia=calcula_mayor_valor_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_de_16_a_21);
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                    
                }//fin if
                else
                {
                        $posiciones_iguales_a_0_array=array();
                        $posiciones_iguales_a_0_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
                        if(count($posiciones_iguales_a_0_array)>0 && is_array($posiciones_iguales_a_0_array))
                        {
                                //criterio 3
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_0_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                        }//fin else
                        else
                        {
                                //criterio para no dejar faltante
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                        } 
                }//fin else
        }//fin else
                
    }//fin if si el campo 49 no es fecha calendario o 1800-01-01
    //FIN CAMPO 60
    
    //CAMPO 61
    $numero_orden_campo_actual=61;
    if($es_campo_49_fecha_calendario_o_1800==false)
    {
        $posiciones_iguales_a_1_array=array();
        $posiciones_iguales_a_1_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
        if(count($posiciones_iguales_a_1_array)>0 && is_array($posiciones_iguales_a_1_array))
        {
                //criterio 3
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
        
                $array_varios_valores=array(
                                            "16",
                                            "17",
                                            "18",
                                            "20",
                                            "21"
                                            );
                
                $array_iguales_de_16_a_21=array();
                $array_iguales_de_16_a_21=iguales_a_varios_valores($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_varios_valores);
                
                if(count($array_iguales_de_16_a_21)>0 && is_array($array_iguales_de_16_a_21))
                {
                    //criterio 2
                    $mayor_valor_mayor_frecuencia="";
                    $mayor_valor_mayor_frecuencia=calcula_mayor_valor_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_de_16_a_21);
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                    
                }//fin if
                else
                {
                        $posiciones_iguales_a_0_array=array();
                        $posiciones_iguales_a_0_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
                        if(count($posiciones_iguales_a_0_array)>0 && is_array($posiciones_iguales_a_0_array))
                        {
                                //criterio 3
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_0_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                        }//fin else
                        else
                        {
                                //criterio para no dejar faltante
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                        } 
                }//fin else
        }//fin else
                
    }//fin if si el campo 49 no es fecha calendario o 1800-01-01
    //FIN CAMPO 61
    
    //CAMPO 78
    //asociado CAMPO 79
    $numero_orden_campo_actual=78;
    if($es_campo_49_fecha_calendario_o_1800==false)
    {
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

                $orden_ultimo_registro_campo_actual=$orden_mayor_fecha_campo_actual;
                
                $numero_campo_dependiente=79;
                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
        }//fin if
        else
        {
            $posiciones_iguales_a_1800_01_01_array=array();
            $posiciones_iguales_a_1800_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1800-01-01");
            if(count($posiciones_iguales_a_1800_01_01_array)>0 && is_array($posiciones_iguales_a_1800_01_01_array))
            {
                    //criterio 2
                    $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1800_01_01_array);
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                    
                    $numero_campo_dependiente=79;
                    $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
            }//fin if
            else
            {
                    $matriz_comparaciones=array();
                    $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1805-01-01");
                    $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1810-01-01");
                    $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1825-01-01");
                    $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1830-01-01");
                    $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1835-01-01");
                    
                    $array_iguales_a_codigos_desde_1805_hasta_1835=array();
                    $array_iguales_a_codigos_desde_1805_hasta_1835=union_arrays_de_matriz_posiciones($matriz_comparaciones);
                    
                    if(count($array_iguales_a_codigos_desde_1805_hasta_1835)>0 && is_array($array_iguales_a_codigos_desde_1805_hasta_1835))
                    {
                        //criterio 3
                        $mayor_valor_mayor_frecuencia="";
                        $mayor_valor_mayor_frecuencia=calcula_mayor_fecha_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_codigos_desde_1805_hasta_1835);
                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                        
                        $posiciones_iguales_a_valor_mayor_frecuencia=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$mayor_valor_mayor_frecuencia);
                        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_valor_mayor_frecuencia);
                        
                        $numero_campo_dependiente=79;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                        
                    }//fin if
                    else
                    {
                            $posiciones_iguales_a_1845_01_01_array=array();
                            $posiciones_iguales_a_1845_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1845-01-01");
                            if(count($posiciones_iguales_a_1845_01_01_array)>0 && is_array($posiciones_iguales_a_1845_01_01_array))
                            {
                                    //criterio 2
                                    $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1845_01_01_array);
                                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                                    
                                    $numero_campo_dependiente=79;
                                    $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                            }//fin if
                            else
                            {
                                    //criterio para no dejar faltante
                                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                                    
                                    $numero_campo_dependiente=79;
                                    $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[0][$numero_campo_dependiente];
                            } 
                    }//fin if
            }//fin else
        }//fin else
    }//fin if si el campo 49 no es fecha calendario o 1800-01-01
    //FIN CAMPO 78
    
    
    //FIN GESTACION
    
    
    //HIPOTIROIDISMO CONGENITO
    //CAMPO 17
    //asociado a CAMPO 114
        $numero_orden_campo_actual=17;
    
        $posiciones_iguales_a_1_array=array();
        $posiciones_iguales_a_1_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
        if(count($posiciones_iguales_a_1_array)>0 && is_array($posiciones_iguales_a_1_array))
        {
                //criterio 1
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                
                $numero_campo_dependiente=114;
                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
        }//fin if
        else
        {
                $posiciones_iguales_a_2_array=array();
                $posiciones_iguales_a_2_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"2");
                if(count($posiciones_iguales_a_2_array)>0 && is_array($posiciones_iguales_a_2_array))
                {
                        //criterio 2
                        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_2_array);
                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                        
                        $numero_campo_dependiente=114;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                }//fin if
                else
                {
                        $posiciones_iguales_a_21_array=array();
                        $posiciones_iguales_a_21_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"21");
                        if(count($posiciones_iguales_a_21_array)>0 && is_array($posiciones_iguales_a_21_array))
                        {
                                //criterio 3
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_21_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                                
                                $numero_campo_dependiente=114;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                        }//fin if
                        else
                        {
                                $posiciones_iguales_a_0_array=array();
                                $posiciones_iguales_a_0_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
                                if(count($posiciones_iguales_a_0_array)>0 && is_array($posiciones_iguales_a_0_array))
                                {
                                        //criterio 4
                                        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_0_array);
                                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                                        
                                        $numero_campo_dependiente=114;
                                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                                }//fin if
                                else
                                {
                                        //criterio para no dejar faltante
                                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                                        
                                        $numero_campo_dependiente=114;
                                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[0][$numero_campo_dependiente];
                                }     
                        }//fin else
                    
                }//fin else
                
        }//fin else
    //FIN CAMPO 17
    
    //CAMPO 85
    //asociado a CAMPO 84
    $numero_orden_campo_actual=85;
    
        
        $posiciones_iguales_a_2_array=array();
        $posiciones_iguales_a_2_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"2");
        /*
        if(count($posiciones_iguales_a_2_array)>0 && is_array($posiciones_iguales_a_2_array))
        {
                //criterio 1
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_2_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                
                $numero_campo_dependiente=84;
                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
        }//fin if
        else
        {
                $posiciones_iguales_a_1_array=array();
                $posiciones_iguales_a_1_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
                if(count($posiciones_iguales_a_1_array)>0 && is_array($posiciones_iguales_a_1_array))
                {
                        //criterio 2
                        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1_array);
                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                        
                        $numero_campo_dependiente=84;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                }//fin if
                else
                {
                  //mira el 22 el nomrbe de la variable esta  diferente
                        $posiciones_iguales_a_21_array=array();
                        $posiciones_iguales_a_21_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"22");
                        if(count($posiciones_iguales_a_21_array)>0 && is_array($posiciones_iguales_a_21_array))
                        {
                                //criterio 3
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_21_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                                
                                $numero_campo_dependiente=84;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                        }//fin if
                        else
                        {
                                $posiciones_iguales_a_0_array=array();
                                $posiciones_iguales_a_0_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
                                if(count($posiciones_iguales_a_0_array)>0 && is_array($posiciones_iguales_a_0_array))
                                {
                                        //criterio 4
                                        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_0_array);
                                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                                        
                                        $numero_campo_dependiente=84;
                                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                                }//fin if 
                                else
                                {
                                        //criterio para no dejar faltante
                                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                                        
                                        $numero_campo_dependiente=84;
                                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[0][$numero_campo_dependiente];
                                }   
                        }//fin else
                    
                }//fin else
                
        }//fin else
        */
    //FIN CAMPO 85
    
    //FIN HIPOTIROIDISMO CONGENITO
    
    //LEPRA
    //CAMPO 20
    //asociado a CAMPO 117
    $numero_orden_campo_actual=20;
    
        
        $posiciones_iguales_a_2_array=array();
        $posiciones_iguales_a_2_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"2");
        if(count($posiciones_iguales_a_2_array)>0 && is_array($posiciones_iguales_a_2_array))
        {
                //criterio 1
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_2_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                
                $numero_campo_dependiente=117;
                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
        }//fin if
        else
        {
                $posiciones_iguales_a_1_array=array();
                $posiciones_iguales_a_1_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
                if(count($posiciones_iguales_a_1_array)>0 && is_array($posiciones_iguales_a_1_array))
                {
                        //criterio 2
                        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1_array);
                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                        
                        $numero_campo_dependiente=117;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                }//fin if
                else
                {
                        $posiciones_iguales_a_3_array=array();
                        $posiciones_iguales_a_3_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"3");
                        if(count($posiciones_iguales_a_3_array)>0 && is_array($posiciones_iguales_a_3_array))
                        {
                                //criterio 3
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_3_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                                
                                $numero_campo_dependiente=117;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                        }//fin if
                        else
                        {
                                $posiciones_iguales_a_21_array=array();
                                $posiciones_iguales_a_21_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"21");
                                if(count($posiciones_iguales_a_21_array)>0 && is_array($posiciones_iguales_a_21_array))
                                {
                                        //criterio 4
                                        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_21_array);
                                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                                        
                                        $numero_campo_dependiente=117;
                                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                                }//fin if  
                                else
                                {
                                        //criterio para no dejar faltante
                                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                                        
                                        $numero_campo_dependiente=117;
                                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[0][$numero_campo_dependiente];
                                }  
                        }//fin else
                    
                }//fin else
                
        }//fin else
    //FIN CAMPO 20
    //FIN LEPRA
    
    //OBESIDAD
    
    //CAMPO 21
    //asociado a CAMPO 29, CAMPO 30, CAMPO 31, CAMPO 32, CAMPO 64
    $numero_orden_campo_actual=21;
    
        $posiciones_iguales_a_1_array=array();
        $posiciones_iguales_a_1_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
        if(count($posiciones_iguales_a_1_array)>0 && is_array($posiciones_iguales_a_1_array))
        {
                //criterio 1
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                
                $numero_campo_secundario=64;
                $matriz_comparaciones=array();
                $matriz_comparaciones[]=mayor_a_fecha_pos_array($matrix_registros_duplicados_afiliado_actual,$numero_campo_secundario,"1900-12-31");
                $matriz_comparaciones[]=menor_igual_a_fecha_pos_array($matrix_registros_duplicados_afiliado_actual,$numero_campo_secundario,trim($fecha_de_corte));
                
                $posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte=array();
                $posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
                
                $matriz_secundaria_comparacion=array();
                $matriz_secundaria_comparacion[]=$posiciones_iguales_a_2_array;
                $matriz_secundaria_comparacion[]=$posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte;
                $posiciones_array_campo_primario_contra_primer_campo_secundario=interseccion_arrays_de_matriz_posiciones($matriz_secundaria_comparacion);
                
                if(count($posiciones_array_campo_primario_contra_primer_campo_secundario)>0 && is_array($posiciones_array_campo_primario_contra_primer_campo_secundario))
                {
                        $orden_mayor_fecha_campo_actual=maximo_fecha_orden($matrix_registros_duplicados_afiliado_actual,$numero_campo_secundario,$posiciones_array_campo_primario_contra_primer_campo_secundario);
                        
                        $orden_ultimo_registro_campo_actual=$orden_mayor_fecha_campo_actual;
                        
                        $numero_campo_dependiente=29;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                        $numero_campo_dependiente=30;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                        $numero_campo_dependiente=31;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                        $numero_campo_dependiente=32;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                        $numero_campo_dependiente=64;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                }
                else
                {
                        $numero_campo_dependiente=29;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                        $numero_campo_dependiente=30;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                        $numero_campo_dependiente=31;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                        $numero_campo_dependiente=32;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                        $numero_campo_dependiente=64;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                }//fin else
        }//fin if
        else
        {
                $posiciones_iguales_a_2_array=array();
                $posiciones_iguales_a_2_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"2");
                if(count($posiciones_iguales_a_2_array)>0 && is_array($posiciones_iguales_a_2_array))
                {
                        //criterio 2
                        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_2_array);
                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                        
                        $numero_campo_secundario=64;
                        $matriz_comparaciones=array();
                        $matriz_comparaciones[]=mayor_a_fecha_pos_array($matrix_registros_duplicados_afiliado_actual,$numero_campo_secundario,"1900-12-31");
                        $matriz_comparaciones[]=menor_igual_a_fecha_pos_array($matrix_registros_duplicados_afiliado_actual,$numero_campo_secundario,trim($fecha_de_corte));
                        
                        $posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte=array();
                        $posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
                        
                        $matriz_secundaria_comparacion=array();
                        $matriz_secundaria_comparacion[]=$posiciones_iguales_a_2_array;
                        $matriz_secundaria_comparacion[]=$posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte;
                        $posiciones_array_campo_primario_contra_primer_campo_secundario=interseccion_arrays_de_matriz_posiciones($matriz_secundaria_comparacion);
                        
                        if(count($posiciones_array_campo_primario_contra_primer_campo_secundario)>0 && is_array($posiciones_array_campo_primario_contra_primer_campo_secundario))
                        {
                                $orden_mayor_fecha_campo_actual=maximo_fecha_orden($matrix_registros_duplicados_afiliado_actual,$numero_campo_secundario,$posiciones_array_campo_primario_contra_primer_campo_secundario);
                                
                                $orden_ultimo_registro_campo_actual=$orden_mayor_fecha_campo_actual;
                                
                                $numero_campo_dependiente=29;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                                $numero_campo_dependiente=30;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                                $numero_campo_dependiente=31;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                                $numero_campo_dependiente=32;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                                $numero_campo_dependiente=64;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                        }
                        else
                        {
                                $numero_campo_dependiente=29;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                                $numero_campo_dependiente=30;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                                $numero_campo_dependiente=31;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                                $numero_campo_dependiente=32;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                                $numero_campo_dependiente=64;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                        }//fin else
                        
                        
                        
                }//fin if
                else
                {
                        $posiciones_iguales_a_3_array=array();
                        $posiciones_iguales_a_3_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"3");
                        if(count($posiciones_iguales_a_3_array)>0 && is_array($posiciones_iguales_a_3_array))
                        {
                                //criterio 3
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_3_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                                
                                $numero_campo_dependiente=29;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                                $numero_campo_dependiente=30;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                                $numero_campo_dependiente=31;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                                $numero_campo_dependiente=32;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                                $numero_campo_dependiente=64;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                        }//fin if
                        else
                        {
                                $posiciones_iguales_a_21_array=array();
                                $posiciones_iguales_a_21_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"21");
                                if(count($posiciones_iguales_a_21_array)>0 && is_array($posiciones_iguales_a_21_array))
                                {
                                        //criterio 4
                                        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_21_array);
                                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                                        
                                        $numero_campo_dependiente=29;
                                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                                        $numero_campo_dependiente=30;
                                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                                        $numero_campo_dependiente=31;
                                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                                        $numero_campo_dependiente=32;
                                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                                        $numero_campo_dependiente=64;
                                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                                }//fin if
                                else
                                {
                                        //criterio para no dejar faltante
                                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                                        
                                        $numero_campo_dependiente=29;
                                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[0][$numero_campo_dependiente];
                                        $numero_campo_dependiente=30;
                                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[0][$numero_campo_dependiente];
                                        $numero_campo_dependiente=31;
                                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[0][$numero_campo_dependiente];
                                        $numero_campo_dependiente=32;
                                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[0][$numero_campo_dependiente];
                                        $numero_campo_dependiente=64;
                                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[0][$numero_campo_dependiente];
                                } 
                                
                        }//fin else
                    
                }//fin else
                
        }//fin else
    
    //FIN CAMPO 21
    
    //CAMPO 67
    $numero_orden_campo_actual=67;
    
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
        $posiciones_iguales_a_1800_01_01_array=array();
        $posiciones_iguales_a_1800_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1800-01-01");
        if(count($posiciones_iguales_a_1800_01_01_array)>0 && is_array($posiciones_iguales_a_1800_01_01_array))
        {
                //criterio 2
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1800_01_01_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
                $matriz_comparaciones=array();
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1805-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1810-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1825-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1830-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1835-01-01");
                
                $array_iguales_a_codigos_desde_1805_hasta_1835=array();
                $array_iguales_a_codigos_desde_1805_hasta_1835=union_arrays_de_matriz_posiciones($matriz_comparaciones);
                
                if(count($array_iguales_a_codigos_desde_1805_hasta_1835)>0 && is_array($array_iguales_a_codigos_desde_1805_hasta_1835))
                {
                    //criterio 3
                    $mayor_valor_mayor_frecuencia="";
                    $mayor_valor_mayor_frecuencia=calcula_mayor_fecha_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_codigos_desde_1805_hasta_1835);
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                    
                }//fin if
                else
                {
                        $posiciones_iguales_a_1845_01_01_array=array();
                        $posiciones_iguales_a_1845_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1845-01-01");
                        if(count($posiciones_iguales_a_1845_01_01_array)>0 && is_array($posiciones_iguales_a_1845_01_01_array))
                        {
                                //criterio 2
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1845_01_01_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                        }//fin if
                        else
                        {
                                //criterio para no dejar faltante
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                        } 
                }//fin if
        }//fin else
    }//fin else
    //FIN CAMPO 67
    
    //FIN OBESIDAD
    
    //ODONTOLOGIA
    
    
    //CAMPO 28
    $numero_orden_campo_actual=28;
    
        
        $posiciones_iguales_a_1_array=array();
        $posiciones_iguales_a_1_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
        if(count($posiciones_iguales_a_1_array)>0 && is_array($posiciones_iguales_a_1_array))
        {
                //criterio 1
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            $posiciones_iguales_a_2_array=array();
                $posiciones_iguales_a_2_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"2");
                if(count($posiciones_iguales_a_2_array)>0 && is_array($posiciones_iguales_a_2_array))
                {
                    //criterio 2
                    $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_2_array);
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                }//fin if
            else
            {
                    $posiciones_iguales_a_21_array=array();
                    $posiciones_iguales_a_21_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"21");
                    if(count($posiciones_iguales_a_21_array)>0 && is_array($posiciones_iguales_a_21_array))
                    {
                            //criterio 3
                            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_21_array);
                            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                    }//fin if
                    else
                    {
                            //criterio para no dejar faltante
                            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                    } 
            }//fin else
        }//fin if

    //FIN CAMPO 28
    
    
    //CAMPO 48
    $numero_orden_campo_actual=48;
    
        $posiciones_iguales_a_2_array=array();
        $posiciones_iguales_a_2_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"2");
        if(count($posiciones_iguales_a_2_array)>0 && is_array($posiciones_iguales_a_2_array))
        {
            //criterio 2
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_2_array);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            $posiciones_iguales_a_1_array=array();
            $posiciones_iguales_a_1_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
            if(count($posiciones_iguales_a_1_array)>0 && is_array($posiciones_iguales_a_1_array))
            {
                    //criterio 3
                    $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1_array);
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
            }//fin if
            else
            {
                    $array_varios_valores=array(
                                        "16",
                                        "17",
                                        "18",
                                        "19",
                                        "20",
                                        "22"
                                        );
            
                        $array_iguales_a_16_17_18_19_20_22=array();
                        $array_iguales_a_16_17_18_19_20_22=iguales_a_varios_valores($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_varios_valores);
                    
                    if(count($array_iguales_a_16_17_18_19_20_22)>0 && is_array($array_iguales_a_16_17_18_19_20_22))
                    {
                        //criterio 4
                        $mayor_valor_mayor_frecuencia="";
                        $mayor_valor_mayor_frecuencia=calcula_mayor_valor_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_16_17_18_19_20_22);
                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                        
                    }//fin if
                    else
                    {
                        $posiciones_iguales_a_0_array=array();
                        $posiciones_iguales_a_0_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
                        if(count($posiciones_iguales_a_0_array)>0 && is_array($posiciones_iguales_a_0_array))
                        {
                                //criterio 5
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_0_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                        }//fin if
                        else
                        {
                                //criterio para no dejar faltante
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                        } 
                    }//fin if
            }//fin else
        }//fin if

    //FIN CAMPO 48
    
    //FIN ODONTOLOGIA
    
    //PLANIFICACION FAMILIAR
    
    //CAMPO 53
    $numero_orden_campo_actual=53;
    
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
        $posiciones_iguales_a_1800_01_01_array=array();
        $posiciones_iguales_a_1800_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1800-01-01");
        if(count($posiciones_iguales_a_1800_01_01_array)>0 && is_array($posiciones_iguales_a_1800_01_01_array))
        {
                //criterio 2
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1800_01_01_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
                $matriz_comparaciones=array();
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1805-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1810-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1825-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1830-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1835-01-01");
                
                $array_iguales_a_codigos_desde_1805_hasta_1835=array();
                $array_iguales_a_codigos_desde_1805_hasta_1835=union_arrays_de_matriz_posiciones($matriz_comparaciones);
                
                if(count($array_iguales_a_codigos_desde_1805_hasta_1835)>0 && is_array($array_iguales_a_codigos_desde_1805_hasta_1835))
                {
                    //criterio 3
                    $mayor_valor_mayor_frecuencia="";
                    $mayor_valor_mayor_frecuencia=calcula_mayor_fecha_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_codigos_desde_1805_hasta_1835);
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                    
                }//fin if
                else
                {
                        $posiciones_iguales_a_1845_01_01_array=array();
                        $posiciones_iguales_a_1845_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1845-01-01");
                        if(count($posiciones_iguales_a_1845_01_01_array)>0 && is_array($posiciones_iguales_a_1845_01_01_array))
                        {
                                //criterio 2
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1845_01_01_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                        }//fin if
                        else
                        {
                                //criterio para no dejar faltante
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                        } 
                }//fin if
        }//fin else
    }//fin else
    //FIN CAMPO 53
    
    //CAMPO 54
    //asociado al CAMPO 55
    $numero_orden_campo_actual=54;
    
        
        $array_varios_valores=array("1",
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
                                    "15"
                                    );
        
        $array_iguales_de_1_a_15=array();
        $array_iguales_de_1_a_15=iguales_a_varios_valores($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_varios_valores);
        
        if(count($array_iguales_de_1_a_15)>0 && is_array($array_iguales_de_1_a_15))
        {
                //criterio 2
                $mayor_valor_mayor_frecuencia="";
                $mayor_valor_mayor_frecuencia=calcula_mayor_valor_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_de_1_a_15);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                
                //echo "entro 1 $mayor_valor_mayor_frecuencia ";
                
                $posiciones_iguales_a_valor_mayor_frecuencia=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$mayor_valor_mayor_frecuencia);
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_valor_mayor_frecuencia);
            
                $numero_campo_dependiente=55;
                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
            
        }//fin if
        else
        {
                //echo "entro else 2 ".$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                
                $array_varios_valores=array(
                                            "16",
                                            "17",
                                            "18",
                                            "20",
                                            "21"
                                            );
                
                $array_iguales_de_16_a_21=array();
                $array_iguales_de_16_a_21=iguales_a_varios_valores($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_varios_valores);
                
                if(count($array_iguales_de_16_a_21)>0 && is_array($array_iguales_de_16_a_21))
                {
                        //echo "entro 3 ";
                        //criterio 2
                        $mayor_valor_mayor_frecuencia="";
                        $mayor_valor_mayor_frecuencia=calcula_mayor_valor_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_de_16_a_21);
                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                        
                        $numero_campo_dependiente=55;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                    
                }//fin if
                else
                {
                        //echo "entro else 4 ";
                        
                        $posiciones_iguales_a_21_array=array();
                        $posiciones_iguales_a_21_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"21");
                        if(count($posiciones_iguales_a_21_array)>0 && is_array($posiciones_iguales_a_21_array))
                        {
                                //echo "entro 5 ";
                                //criterio 3
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_21_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                                
                                $numero_campo_dependiente=55;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                        }//fin if
                        else
                        {
                                $posiciones_iguales_a_0_array=array();
                                $posiciones_iguales_a_0_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
                                if(count($posiciones_iguales_a_0_array)>0 && is_array($posiciones_iguales_a_0_array))
                                {
                                        //echo "entro 6 ";
                                        //criterio 4
                                        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_0_array);
                                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                                        
                                        $numero_campo_dependiente=55;
                                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                                }//fin if
                                else
                                {
                                        //criterio para no dejar faltante
                                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                                } 
                        }//fin else
                }//fin else
        }//fin if

    //FIN CAMPO 54
    
    //FIN PLANIFICACION FAMILIAR
    
    //RIESGO EDADES
    
    
    //CAMPO 69
    $numero_orden_campo_actual=69;
    
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
        $posiciones_iguales_a_1800_01_01_array=array();
        $posiciones_iguales_a_1800_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1800-01-01");
        if(count($posiciones_iguales_a_1800_01_01_array)>0 && is_array($posiciones_iguales_a_1800_01_01_array))
        {
                //criterio 2
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1800_01_01_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
                $matriz_comparaciones=array();
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1805-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1810-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1825-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1830-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1835-01-01");
                
                $array_iguales_a_codigos_desde_1805_hasta_1835=array();
                $array_iguales_a_codigos_desde_1805_hasta_1835=union_arrays_de_matriz_posiciones($matriz_comparaciones);
                
                if(count($array_iguales_a_codigos_desde_1805_hasta_1835)>0 && is_array($array_iguales_a_codigos_desde_1805_hasta_1835))
                {
                    //criterio 3
                    $mayor_valor_mayor_frecuencia="";
                    $mayor_valor_mayor_frecuencia=calcula_mayor_fecha_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_codigos_desde_1805_hasta_1835);
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                    
                }//fin if
                else
                {
                        $posiciones_iguales_a_1845_01_01_array=array();
                        $posiciones_iguales_a_1845_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1845-01-01");
                        if(count($posiciones_iguales_a_1845_01_01_array)>0 && is_array($posiciones_iguales_a_1845_01_01_array))
                        {
                                //criterio 2
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1845_01_01_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                        }//fin if
                        else
                        {
                                //criterio para no dejar faltante
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                        } 
                }//fin if
        }//fin else
    }//fin else
    //FIN CAMPO 69
    
    
    //CAMPO 70
    $numero_orden_campo_actual=70;
    
        
        $posiciones_iguales_a_1_array=array();
        $posiciones_iguales_a_1_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
        if(count($posiciones_iguales_a_1_array)>0 && is_array($posiciones_iguales_a_1_array))
        {
                //criterio 1
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
                $array_varios_valores=array(
                                            "16",
                                            "17",
                                            "18",
                                            "20",
                                            "21"
                                            );
                
                $array_iguales_de_16_a_21=array();
                $array_iguales_de_16_a_21=iguales_a_varios_valores($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_varios_valores);
                
                if(count($array_iguales_de_16_a_21)>0 && is_array($array_iguales_de_16_a_21))
                {
                        //criterio 2
                        $mayor_valor_mayor_frecuencia="";
                        $mayor_valor_mayor_frecuencia=calcula_mayor_valor_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_de_16_a_21);
                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                        
                    
                }//fin if
                else
                {
                        $posiciones_iguales_a_0_array=array();
                        $posiciones_iguales_a_0_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
                        if(count($posiciones_iguales_a_0_array)>0 && is_array($posiciones_iguales_a_0_array))
                        {
                                //criterio 3
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_0_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                        }//fin if
                        else
                        {
                                //criterio para no dejar faltante
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                        } 
                }//fin else
        }//fin if

    //FIN CAMPO 70
    
    //CAMPO 71
    $numero_orden_campo_actual=71;
    
        
        $posiciones_iguales_a_1_array=array();
        $posiciones_iguales_a_1_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
        if(count($posiciones_iguales_a_1_array)>0 && is_array($posiciones_iguales_a_1_array))
        {
                //criterio 1
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
                $array_varios_valores=array(
                                            "16",
                                            "17",
                                            "18",
                                            "20",
                                            "21"
                                            );
                
                $array_iguales_de_16_a_21=array();
                $array_iguales_de_16_a_21=iguales_a_varios_valores($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_varios_valores);
                
                if(count($array_iguales_de_16_a_21)>0 && is_array($array_iguales_de_16_a_21))
                {
                        //criterio 2
                        $mayor_valor_mayor_frecuencia="";
                        $mayor_valor_mayor_frecuencia=calcula_mayor_valor_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_de_16_a_21);
                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                        
                    
                }//fin if
                else
                {
                        $posiciones_iguales_a_0_array=array();
                        $posiciones_iguales_a_0_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
                        if(count($posiciones_iguales_a_0_array)>0 && is_array($posiciones_iguales_a_0_array))
                        {
                                //criterio 3
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_0_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                        }//fin if
                        else
                        {
                                //criterio para no dejar faltante
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                        } 
                }//fin else
        }//fin if

    //FIN CAMPO 71
    
    //CAMPO 72
    $numero_orden_campo_actual=72;
    
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
        $posiciones_iguales_a_1800_01_01_array=array();
        $posiciones_iguales_a_1800_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1800-01-01");
        if(count($posiciones_iguales_a_1800_01_01_array)>0 && is_array($posiciones_iguales_a_1800_01_01_array))
        {
                //criterio 2
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1800_01_01_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
                $matriz_comparaciones=array();
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1805-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1810-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1825-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1830-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1835-01-01");
                
                $array_iguales_a_codigos_desde_1805_hasta_1835=array();
                $array_iguales_a_codigos_desde_1805_hasta_1835=union_arrays_de_matriz_posiciones($matriz_comparaciones);
                
                if(count($array_iguales_a_codigos_desde_1805_hasta_1835)>0 && is_array($array_iguales_a_codigos_desde_1805_hasta_1835))
                {
                    //criterio 3
                    $mayor_valor_mayor_frecuencia="";
                    $mayor_valor_mayor_frecuencia=calcula_mayor_fecha_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_codigos_desde_1805_hasta_1835);
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                    
                }//fin if
                else
                {
                        $posiciones_iguales_a_1845_01_01_array=array();
                        $posiciones_iguales_a_1845_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1845-01-01");
                        if(count($posiciones_iguales_a_1845_01_01_array)>0 && is_array($posiciones_iguales_a_1845_01_01_array))
                        {
                                //criterio 2
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1845_01_01_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                        }//fin if
                        else
                        {
                                //criterio para no dejar faltante
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                        } 
                }//fin if
        }//fin else
    }//fin else
    //FIN CAMPO 72
    
    //CAMPO 73
    $numero_orden_campo_actual=73;
    
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
        $posiciones_iguales_a_1800_01_01_array=array();
        $posiciones_iguales_a_1800_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1800-01-01");
        if(count($posiciones_iguales_a_1800_01_01_array)>0 && is_array($posiciones_iguales_a_1800_01_01_array))
        {
                //criterio 2
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1800_01_01_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
                $matriz_comparaciones=array();
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1805-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1810-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1825-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1830-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1835-01-01");
                
                $array_iguales_a_codigos_desde_1805_hasta_1835=array();
                $array_iguales_a_codigos_desde_1805_hasta_1835=union_arrays_de_matriz_posiciones($matriz_comparaciones);
                
                if(count($array_iguales_a_codigos_desde_1805_hasta_1835)>0 && is_array($array_iguales_a_codigos_desde_1805_hasta_1835))
                {
                    //criterio 3
                    $mayor_valor_mayor_frecuencia="";
                    $mayor_valor_mayor_frecuencia=calcula_mayor_fecha_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_codigos_desde_1805_hasta_1835);
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                    
                }//fin if
                else
                {
                        $posiciones_iguales_a_1845_01_01_array=array();
                        $posiciones_iguales_a_1845_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1845-01-01");
                        if(count($posiciones_iguales_a_1845_01_01_array)>0 && is_array($posiciones_iguales_a_1845_01_01_array))
                        {
                                //criterio 2
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1845_01_01_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                        }//fin if
                        else
                        {
                                //criterio para no dejar faltante
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                        } 
                }//fin if
        }//fin else
    }//fin else
    //FIN CAMPO 73
    
    //FIN RIESGO EDADES
    
    
    //VICTIMA MALTRATO
    
    //CAMPO 22
    //asociado a CAMPO 65
    $numero_orden_campo_actual=22;
    
        $posiciones_iguales_a_1_array=array();
        $posiciones_iguales_a_1_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
        if(count($posiciones_iguales_a_1_array)>0 && is_array($posiciones_iguales_a_1_array))
        {
                //criterio 1
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                
                $numero_campo_secundario=65;
                $matriz_comparaciones=array();
                $matriz_comparaciones[]=mayor_a_fecha_pos_array($matrix_registros_duplicados_afiliado_actual,$numero_campo_secundario,"1900-12-31");
                $matriz_comparaciones[]=menor_igual_a_fecha_pos_array($matrix_registros_duplicados_afiliado_actual,$numero_campo_secundario,trim($fecha_de_corte));
                
                $posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte=array();
                $posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
                
                $matriz_secundaria_comparacion=array();
                $matriz_secundaria_comparacion[]=$posiciones_iguales_a_2_array;
                $matriz_secundaria_comparacion[]=$posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte;
                $posiciones_array_campo_primario_contra_primer_campo_secundario=interseccion_arrays_de_matriz_posiciones($matriz_secundaria_comparacion);
                
                if(count($posiciones_array_campo_primario_contra_primer_campo_secundario)>0 && is_array($posiciones_array_campo_primario_contra_primer_campo_secundario))
                {
                        $orden_mayor_fecha_campo_actual=maximo_fecha_orden($matrix_registros_duplicados_afiliado_actual,$numero_campo_secundario,$posiciones_array_campo_primario_contra_primer_campo_secundario);
                        
                        $orden_ultimo_registro_campo_actual=$orden_mayor_fecha_campo_actual;
                        
                        $numero_campo_dependiente=65;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                }
                else
                {
                        $numero_campo_dependiente=65;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                }//fin else
        }//fin if
        else
        {
                $posiciones_iguales_a_2_array=array();
                $posiciones_iguales_a_2_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"2");
                if(count($posiciones_iguales_a_2_array)>0 && is_array($posiciones_iguales_a_2_array))
                {
                        //criterio 2
                        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_2_array);
                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                        
                        $numero_campo_secundario=65;
                        $matriz_comparaciones=array();
                        $matriz_comparaciones[]=mayor_a_fecha_pos_array($matrix_registros_duplicados_afiliado_actual,$numero_campo_secundario,"1900-12-31");
                        $matriz_comparaciones[]=menor_igual_a_fecha_pos_array($matrix_registros_duplicados_afiliado_actual,$numero_campo_secundario,trim($fecha_de_corte));
                        
                        $posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte=array();
                        $posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
                        
                        $matriz_secundaria_comparacion=array();
                        $matriz_secundaria_comparacion[]=$posiciones_iguales_a_2_array;
                        $matriz_secundaria_comparacion[]=$posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte;
                        $posiciones_array_campo_primario_contra_primer_campo_secundario=interseccion_arrays_de_matriz_posiciones($matriz_secundaria_comparacion);
                        
                        if(count($posiciones_array_campo_primario_contra_primer_campo_secundario)>0 && is_array($posiciones_array_campo_primario_contra_primer_campo_secundario))
                        {
                                $orden_mayor_fecha_campo_actual=maximo_fecha_orden($matrix_registros_duplicados_afiliado_actual,$numero_campo_secundario,$posiciones_array_campo_primario_contra_primer_campo_secundario);
                                
                                $orden_ultimo_registro_campo_actual=$orden_mayor_fecha_campo_actual;
                                
                                $numero_campo_dependiente=65;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                        }
                        else
                        {
                                $numero_campo_dependiente=65;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                        }//fin else
                }//fin if
                else
                {
                        $posiciones_iguales_a_3_array=array();
                        $posiciones_iguales_a_3_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"3");
                        if(count($posiciones_iguales_a_3_array)>0 && is_array($posiciones_iguales_a_3_array))
                        {
                                //criterio 3
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_3_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                                
                                $numero_campo_dependiente=65;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                        }//fin if
                        else
                        {
                                $posiciones_iguales_a_21_array=array();
                                $posiciones_iguales_a_21_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"21");
                                if(count($posiciones_iguales_a_21_array)>0 && is_array($posiciones_iguales_a_21_array))
                                {
                                        //criterio 4
                                        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_21_array);
                                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                                        
                                        $numero_campo_dependiente=65;
                                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                                }//fin if
                                else
                                {
                                        $posiciones_iguales_a_0_array=array();
                                        $posiciones_iguales_a_0_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
                                        if(count($posiciones_iguales_a_0_array)>0 && is_array($posiciones_iguales_a_0_array))
                                        {
                                                //criterio 5
                                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_0_array);
                                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                                                
                                                $numero_campo_dependiente=65;
                                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                                        }//fin if
                                        else
                                        {
                                                //criterio para no dejar faltante
                                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                                                
                                                $numero_campo_dependiente=65;
                                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[0][$numero_campo_dependiente];
                                        }     
                                }//fin else
                        }//fin else
                    
                }//fin else
                
        }//fin else
    //FIN CAMPO 22
    
    //FIN VICTIMA MALTRATO
    
    //VICTIMA VIOLENCIA SEXUAL
    
    //CAMPO 23
    //asociado a CAMPO 66
    $numero_orden_campo_actual=23;
    
        $posiciones_iguales_a_1_array=array();
        $posiciones_iguales_a_1_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
        if(count($posiciones_iguales_a_1_array)>0 && is_array($posiciones_iguales_a_1_array))
        {
                //criterio 1
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                
                $numero_campo_secundario=66;
                $matriz_comparaciones=array();
                $matriz_comparaciones[]=mayor_a_fecha_pos_array($matrix_registros_duplicados_afiliado_actual,$numero_campo_secundario,"1900-12-31");
                $matriz_comparaciones[]=menor_igual_a_fecha_pos_array($matrix_registros_duplicados_afiliado_actual,$numero_campo_secundario,trim($fecha_de_corte));
                
                $posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte=array();
                $posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
                
                $matriz_secundaria_comparacion=array();
                $matriz_secundaria_comparacion[]=$posiciones_iguales_a_2_array;
                $matriz_secundaria_comparacion[]=$posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte;
                $posiciones_array_campo_primario_contra_primer_campo_secundario=interseccion_arrays_de_matriz_posiciones($matriz_secundaria_comparacion);
                
                if(count($posiciones_array_campo_primario_contra_primer_campo_secundario)>0 && is_array($posiciones_array_campo_primario_contra_primer_campo_secundario))
                {
                        $orden_mayor_fecha_campo_actual=maximo_fecha_orden($matrix_registros_duplicados_afiliado_actual,$numero_campo_secundario,$posiciones_array_campo_primario_contra_primer_campo_secundario);
                        
                        $orden_ultimo_registro_campo_actual=$orden_mayor_fecha_campo_actual;
                        
                        $numero_campo_dependiente=66;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                }
                else
                {
                        $numero_campo_dependiente=66;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                }//fin else
        }//fin if
        else
        {
                $posiciones_iguales_a_2_array=array();
                $posiciones_iguales_a_2_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"2");
                if(count($posiciones_iguales_a_2_array)>0 && is_array($posiciones_iguales_a_2_array))
                {
                        //criterio 2
                        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_2_array);
                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                        
                        $numero_campo_dependiente=66;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                }//fin if
                else
                {
                        $posiciones_iguales_a_21_array=array();
                        $posiciones_iguales_a_21_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"21");
                        if(count($posiciones_iguales_a_21_array)>0 && is_array($posiciones_iguales_a_21_array))
                        {
                                //criterio 4
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_21_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                                
                                $numero_campo_dependiente=66;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                        }//fin if
                        else
                        {
                                //criterio para no dejar faltante
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                                
                                $numero_campo_dependiente=66;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[0][$numero_campo_dependiente];
                        } 
                    
                }//fin else
                
        }//fin else
    //FIN CAMPO 23
    
    //FIN VICTIMA VIOLENCIA SEXUAL
    
    
    //ITS INFECCION TRANSMISION SEXUAL
    
    //CAMPO 15 
    //asociado a CAMPO 115, CAMPO 116
    $numero_orden_campo_actual=15;
    if($es_campo_49_fecha_calendario_o_1800==false)
    {
        $posiciones_iguales_a_1_array=array();
        $posiciones_iguales_a_1_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
        if(count($posiciones_iguales_a_1_array)>0 && is_array($posiciones_iguales_a_1_array))
        {
                //criterio 1
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                
                $numero_campo_dependiente=115;
                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                $numero_campo_dependiente=116;
                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
        }//fin if
        else
        {
                $posiciones_iguales_a_2_array=array();
                $posiciones_iguales_a_2_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"2");
                if(count($posiciones_iguales_a_2_array)>0 && is_array($posiciones_iguales_a_2_array))
                {
                        //criterio 2
                        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_2_array);
                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                        
                        $numero_campo_dependiente=115;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                        $numero_campo_dependiente=116;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                }//fin if
                else
                {
                        $posiciones_iguales_a_3_array=array();
                        $posiciones_iguales_a_3_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"3");
                        if(count($posiciones_iguales_a_3_array)>0 && is_array($posiciones_iguales_a_3_array))
                        {
                                //criterio 3
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_3_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                                
                                $numero_campo_dependiente=115;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                                $numero_campo_dependiente=116;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                        }//fin if
                        else
                        {
                                $posiciones_iguales_a_21_array=array();
                                $posiciones_iguales_a_21_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"21");
                                if(count($posiciones_iguales_a_21_array)>0 && is_array($posiciones_iguales_a_21_array))
                                {
                                        //criterio 4
                                        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_21_array);
                                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                                        
                                        $numero_campo_dependiente=115;
                                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                                        $numero_campo_dependiente=116;
                                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                                }//fin if
                                else
                                {
                                        $posiciones_iguales_a_0_array=array();
                                        $posiciones_iguales_a_0_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
                                        if(count($posiciones_iguales_a_0_array)>0 && is_array($posiciones_iguales_a_0_array))
                                        {
                                                //criterio 5
                                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_0_array);
                                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                                                
                                                $numero_campo_dependiente=115;
                                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                                                $numero_campo_dependiente=116;
                                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                                        }//fin if  
                                        else
                                        {
                                                //criterio para no dejar faltante
                                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                                                
                                                $numero_campo_dependiente=115;
                                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[0][$numero_campo_dependiente];
                                                $numero_campo_dependiente=116;
                                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[0][$numero_campo_dependiente];
                                        }   
                                }//fin else
                        }//fin else
                    
                }//fin else
                
        }//fin else
    }//fin if si el campo 49 no es fecha calendario o 1800-01-01
    //FIN CAMPO 15 
    
    
    //CAMPO 24
    $numero_orden_campo_actual=24;
    
        $posiciones_iguales_a_1_array=array();
        $posiciones_iguales_a_1_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
        if(count($posiciones_iguales_a_1_array)>0 && is_array($posiciones_iguales_a_1_array))
        {
                //criterio 1
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                
        }//fin if
        else
        {
                $posiciones_iguales_a_2_array=array();
                $posiciones_iguales_a_2_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"2");
                if(count($posiciones_iguales_a_2_array)>0 && is_array($posiciones_iguales_a_2_array))
                {
                        //criterio 2
                        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_2_array);
                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                        
                }//fin if
                else
                {
                        $posiciones_iguales_a_21_array=array();
                        $posiciones_iguales_a_21_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"21");
                        if(count($posiciones_iguales_a_21_array)>0 && is_array($posiciones_iguales_a_21_array))
                        {
                                //criterio 4
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_21_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                                
                        }//fin if
                        else
                        {
                                //criterio para no dejar faltante
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                        } 
                    
                }//fin else
                
        }//fin else
    //FIN CAMPO 24
    
    //CAMPO 74
    $numero_orden_campo_actual=74;
    
        $posiciones_entre_1_hasta_149_array=array();
        $posiciones_entre_1_hasta_149_array=entre_el_rango_de_valores_enteros($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1","149");
        if(count($posiciones_entre_1_hasta_149_array)>0 && is_array($posiciones_entre_1_hasta_149_array))
        {
                //criterio 1
                $mayor_valor_mayor_frecuencia="";
                $mayor_valor_mayor_frecuencia=calcula_mayor_valor_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$posiciones_entre_1_hasta_149_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                
        }//fin if
        else
        {
                $array_varios_valores=array(
                                        "993",
                                        "994",
                                        "995",
                                        "996",
                                        "997",
                                        "999"
                                        );
            
                $array_iguales_a_993_999_sin_998=array();
                $array_iguales_a_993_999_sin_998=iguales_a_varios_valores($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_varios_valores);
            
                if(count($array_iguales_a_993_999_sin_998)>0 && is_array($array_iguales_a_993_999_sin_998))
                {
                        //criterio 2
                        $mayor_valor_mayor_frecuencia="";
                        $mayor_valor_mayor_frecuencia=calcula_mayor_valor_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_993_999_sin_998);
                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                        
                }//fin if
                else
                {
                        $posiciones_iguales_a_0_array=array();
                        $posiciones_iguales_a_0_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
                        if(count($posiciones_iguales_a_0_array)>0 && is_array($posiciones_iguales_a_0_array))
                        {
                                //criterio 3
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_0_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                                
                        }//fin if
                        else
                        {
                                //criterio para no dejar faltante
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                        } 
                    
                }//fin else
                
        }//fin else
    //FIN CAMPO 74 
    
    //CAMPO 75
    $numero_orden_campo_actual=75;
    
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
        $posiciones_iguales_a_1800_01_01_array=array();
        $posiciones_iguales_a_1800_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1800-01-01");
        if(count($posiciones_iguales_a_1800_01_01_array)>0 && is_array($posiciones_iguales_a_1800_01_01_array))
        {
                //criterio 2
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1800_01_01_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
                $matriz_comparaciones=array();
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1805-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1810-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1825-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1830-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1835-01-01");
                
                $array_iguales_a_codigos_desde_1805_hasta_1835=array();
                $array_iguales_a_codigos_desde_1805_hasta_1835=union_arrays_de_matriz_posiciones($matriz_comparaciones);
                
                if(count($array_iguales_a_codigos_desde_1805_hasta_1835)>0 && is_array($array_iguales_a_codigos_desde_1805_hasta_1835))
                {
                    //criterio 3
                    $mayor_valor_mayor_frecuencia="";
                    $mayor_valor_mayor_frecuencia=calcula_mayor_fecha_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_codigos_desde_1805_hasta_1835);
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                    
                }//fin if
                else
                {
                        $posiciones_iguales_a_1845_01_01_array=array();
                        $posiciones_iguales_a_1845_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1845-01-01");
                        if(count($posiciones_iguales_a_1845_01_01_array)>0 && is_array($posiciones_iguales_a_1845_01_01_array))
                        {
                                //criterio 2
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1845_01_01_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                        }//fin if
                        else
                        {
                                //criterio para no dejar faltante
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                        } 
                }//fin if
        }//fin else
    }//fin else
    //FIN CAMPO 75
    
    
    //CAMPO 76
    $numero_orden_campo_actual=76;
    
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
        $posiciones_iguales_a_1800_01_01_array=array();
        $posiciones_iguales_a_1800_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1800-01-01");
        if(count($posiciones_iguales_a_1800_01_01_array)>0 && is_array($posiciones_iguales_a_1800_01_01_array))
        {
                //criterio 2
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1800_01_01_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
                $matriz_comparaciones=array();
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1805-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1810-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1825-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1830-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1835-01-01");
                
                $array_iguales_a_codigos_desde_1805_hasta_1835=array();
                $array_iguales_a_codigos_desde_1805_hasta_1835=union_arrays_de_matriz_posiciones($matriz_comparaciones);
                
                if(count($array_iguales_a_codigos_desde_1805_hasta_1835)>0 && is_array($array_iguales_a_codigos_desde_1805_hasta_1835))
                {
                    //criterio 3
                    $mayor_valor_mayor_frecuencia="";
                    $mayor_valor_mayor_frecuencia=calcula_mayor_fecha_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_codigos_desde_1805_hasta_1835);
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                    
                }//fin if
                else
                {
                        $posiciones_iguales_a_1845_01_01_array=array();
                        $posiciones_iguales_a_1845_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1845-01-01");
                        if(count($posiciones_iguales_a_1845_01_01_array)>0 && is_array($posiciones_iguales_a_1845_01_01_array))
                        {
                                //criterio 2
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1845_01_01_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                        }//fin if
                        else
                        {
                                //criterio para no dejar faltante
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                        } 
                }//fin if
        }//fin else
    }//fin else
    //FIN CAMPO 76
    
    //CAMPO 81
    //asociado a CAMPO 80
    $numero_orden_campo_actual=81;
    
        
        $posiciones_iguales_a_2_array=array();
        $posiciones_iguales_a_2_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"2");
        /*
        if(count($posiciones_iguales_a_2_array)>0 && is_array($posiciones_iguales_a_2_array))
        {
                //criterio 2
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_2_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                
                $numero_campo_secundario=80;
                $matriz_comparaciones=array();
                $matriz_comparaciones[]=mayor_a_fecha_pos_array($matrix_registros_duplicados_afiliado_actual,$numero_campo_secundario,"1900-12-31");
                $matriz_comparaciones[]=menor_igual_a_fecha_pos_array($matrix_registros_duplicados_afiliado_actual,$numero_campo_secundario,trim($fecha_de_corte));
                
                $posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte=array();
                $posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
                
                $matriz_secundaria_comparacion=array();
                $matriz_secundaria_comparacion[]=$posiciones_iguales_a_2_array;
                $matriz_secundaria_comparacion[]=$posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte;
                $posiciones_array_campo_primario_contra_primer_campo_secundario=interseccion_arrays_de_matriz_posiciones($matriz_secundaria_comparacion);
                
                if(count($posiciones_array_campo_primario_contra_primer_campo_secundario)>0 && is_array($posiciones_array_campo_primario_contra_primer_campo_secundario))
                {
                        $orden_mayor_fecha_campo_actual=maximo_fecha_orden($matrix_registros_duplicados_afiliado_actual,$numero_campo_secundario,$posiciones_array_campo_primario_contra_primer_campo_secundario);
                        
                        $orden_ultimo_registro_campo_actual=$orden_mayor_fecha_campo_actual;
                        
                        $numero_campo_dependiente=80;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                }
                else
                {
                        $numero_campo_dependiente=80;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                }//fin else
        }//fin if
        else
        {
                $posiciones_iguales_a_1_array=array();
                $posiciones_iguales_a_1_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
                if(count($posiciones_iguales_a_1_array)>0 && is_array($posiciones_iguales_a_1_array))
                {
                        //criterio 1
                        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1_array);
                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                        
                        $numero_campo_secundario=80;
                        $matriz_comparaciones=array();
                        $matriz_comparaciones[]=mayor_a_fecha_pos_array($matrix_registros_duplicados_afiliado_actual,$numero_campo_secundario,"1900-12-31");
                        $matriz_comparaciones[]=menor_igual_a_fecha_pos_array($matrix_registros_duplicados_afiliado_actual,$numero_campo_secundario,trim($fecha_de_corte));
                        
                        $posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte=array();
                        $posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
                        
                        $matriz_secundaria_comparacion=array();
                        $matriz_secundaria_comparacion[]=$posiciones_iguales_a_2_array;
                        $matriz_secundaria_comparacion[]=$posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte;
                        $posiciones_array_campo_primario_contra_primer_campo_secundario=interseccion_arrays_de_matriz_posiciones($matriz_secundaria_comparacion);
                        
                        if(count($posiciones_array_campo_primario_contra_primer_campo_secundario)>0 && is_array($posiciones_array_campo_primario_contra_primer_campo_secundario))
                        {
                                $orden_mayor_fecha_campo_actual=maximo_fecha_orden($matrix_registros_duplicados_afiliado_actual,$numero_campo_secundario,$posiciones_array_campo_primario_contra_primer_campo_secundario);
                                
                                $orden_ultimo_registro_campo_actual=$orden_mayor_fecha_campo_actual;
                                
                                $numero_campo_dependiente=80;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                        }
                        else
                        {
                                $numero_campo_dependiente=80;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                        }//fin else
                }//fin if
                else
                {
                        $posiciones_iguales_a_22_array=array();
                        $posiciones_iguales_a_22_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"22");
                        if(count($posiciones_iguales_a_22_array)>0 && is_array($posiciones_iguales_a_22_array))
                        {
                                //criterio 3
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_22_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                                
                                $numero_campo_dependiente=80;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                        }//fin if
                        else
                        {
                                $posiciones_iguales_a_0_array=array();
                                $posiciones_iguales_a_0_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
                                if(count($posiciones_iguales_a_0_array)>0 && is_array($posiciones_iguales_a_0_array))
                                {
                                        //criterio 4
                                        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_0_array);
                                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                                        
                                        $numero_campo_dependiente=80;
                                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                                }//fin if
                                else
                                {
                                        //criterio para no dejar faltante
                                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                                        
                                        $numero_campo_dependiente=80;
                                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[0][$numero_campo_dependiente];
                                }     
                        }//fin else
                    
                }//fin else
                
        }//fin else
        */
    //FIN CAMPO 81
    
    //CAMPO 83
    //asociado a CAMPO 82
    $numero_orden_campo_actual=83;
    
        
        $posiciones_iguales_a_2_array=array();
        $posiciones_iguales_a_2_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"2");
        if(count($posiciones_iguales_a_2_array)>0 && is_array($posiciones_iguales_a_2_array))
        {
                //criterio 2
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_2_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                
                $numero_campo_secundario=82;
                $matriz_comparaciones=array();
                $matriz_comparaciones[]=mayor_a_fecha_pos_array($matrix_registros_duplicados_afiliado_actual,$numero_campo_secundario,"1900-12-31");
                $matriz_comparaciones[]=menor_igual_a_fecha_pos_array($matrix_registros_duplicados_afiliado_actual,$numero_campo_secundario,trim($fecha_de_corte));
                
                $posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte=array();
                $posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
                
                $matriz_secundaria_comparacion=array();
                $matriz_secundaria_comparacion[]=$posiciones_iguales_a_2_array;
                $matriz_secundaria_comparacion[]=$posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte;
                $posiciones_array_campo_primario_contra_primer_campo_secundario=interseccion_arrays_de_matriz_posiciones($matriz_secundaria_comparacion);
                
                if(count($posiciones_array_campo_primario_contra_primer_campo_secundario)>0 && is_array($posiciones_array_campo_primario_contra_primer_campo_secundario))
                {
                        $orden_mayor_fecha_campo_actual=maximo_fecha_orden($matrix_registros_duplicados_afiliado_actual,$numero_campo_secundario,$posiciones_array_campo_primario_contra_primer_campo_secundario);
                        
                        $orden_ultimo_registro_campo_actual=$orden_mayor_fecha_campo_actual;
                        
                        $numero_campo_dependiente=82;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                }
                else
                {
                        $numero_campo_dependiente=82;
                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                }//fin else
        }//fin if
        else
        {
                $posiciones_iguales_a_1_array=array();
                $posiciones_iguales_a_1_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
                if(count($posiciones_iguales_a_1_array)>0 && is_array($posiciones_iguales_a_1_array))
                {
                        //criterio 1
                        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1_array);
                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                        
                        $numero_campo_secundario=82;
                        $matriz_comparaciones=array();
                        $matriz_comparaciones[]=mayor_a_fecha_pos_array($matrix_registros_duplicados_afiliado_actual,$numero_campo_secundario,"1900-12-31");
                        $matriz_comparaciones[]=menor_igual_a_fecha_pos_array($matrix_registros_duplicados_afiliado_actual,$numero_campo_secundario,trim($fecha_de_corte));
                        
                        $posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte=array();
                        $posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
                        
                        $matriz_secundaria_comparacion=array();
                        $matriz_secundaria_comparacion[]=$posiciones_iguales_a_2_array;
                        $matriz_secundaria_comparacion[]=$posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte;
                        $posiciones_array_campo_primario_contra_primer_campo_secundario=interseccion_arrays_de_matriz_posiciones($matriz_secundaria_comparacion);
                        
                        if(count($posiciones_array_campo_primario_contra_primer_campo_secundario)>0 && is_array($posiciones_array_campo_primario_contra_primer_campo_secundario))
                        {
                                $orden_mayor_fecha_campo_actual=maximo_fecha_orden($matrix_registros_duplicados_afiliado_actual,$numero_campo_secundario,$posiciones_array_campo_primario_contra_primer_campo_secundario);
                                
                                $orden_ultimo_registro_campo_actual=$orden_mayor_fecha_campo_actual;
                                
                                $numero_campo_dependiente=82;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                        }
                        else
                        {
                                $numero_campo_dependiente=82;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                        }//fin else
                }//fin if
                else
                {
                        $posiciones_iguales_a_22_array=array();
                        $posiciones_iguales_a_22_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"22");
                        if(count($posiciones_iguales_a_22_array)>0 && is_array($posiciones_iguales_a_22_array))
                        {
                                //criterio 3
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_22_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                                
                                $numero_campo_dependiente=82;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                        }//fin if
                        else
                        {
                                $posiciones_iguales_a_0_array=array();
                                $posiciones_iguales_a_0_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
                                if(count($posiciones_iguales_a_0_array)>0 && is_array($posiciones_iguales_a_0_array))
                                {
                                        //criterio 4
                                        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_0_array);
                                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                                        
                                        $numero_campo_dependiente=82;
                                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                                }//fin if 
                                else
                                {
                                        //criterio para no dejar faltante
                                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                                        
                                        $numero_campo_dependiente=82;
                                        $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[0][$numero_campo_dependiente];
                                }    
                        }//fin else
                    
                }//fin else
                
        }//fin else
    //FIN CAMPO 83
    
    
    //FIN ITS INFECCION TRANSMISION SEXUAL
    
    //CANCER DE CERVIX
    //CAMPO 26
    $numero_orden_campo_actual=26;
    
        $posiciones_iguales_a_1_array=array();
        $posiciones_iguales_a_1_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
        if(count($posiciones_iguales_a_1_array)>0 && is_array($posiciones_iguales_a_1_array))
        {
                //criterio 1
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                
        }//fin if
        else
        {
                $posiciones_iguales_a_2_array=array();
                $posiciones_iguales_a_2_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"2");
                if(count($posiciones_iguales_a_2_array)>0 && is_array($posiciones_iguales_a_2_array))
                {
                        //criterio 2
                        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_2_array);
                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                        
                }//fin if
                else
                {
                        $posiciones_iguales_a_21_array=array();
                        $posiciones_iguales_a_21_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"21");
                        if(count($posiciones_iguales_a_21_array)>0 && is_array($posiciones_iguales_a_21_array))
                        {
                                //criterio 3
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_21_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                                
                        }//fin if
                        else
                        {
                            $posiciones_iguales_a_0_array=array();
                            $posiciones_iguales_a_0_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
                            if(count($posiciones_iguales_a_0_array)>0 && is_array($posiciones_iguales_a_0_array))
                            {
                                    //criterio 4
                                    $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_0_array);
                                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                            }//fin if
                            else
                            {
                                    //criterio para no dejar faltante
                                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                            } 
                        }//fin if
                    
                }//fin else
                
        }//fin else
    //FIN CAMPO 26
    
    
    //CAMPO 86
    $numero_orden_campo_actual=86;
    
    $posiciones_iguales_a_1_array=array();
        $posiciones_iguales_a_1_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
        if(count($posiciones_iguales_a_1_array)>0 && is_array($posiciones_iguales_a_1_array))
        {
                //criterio 3
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
    else
    {
            $posiciones_iguales_a_2_array=array();
            $posiciones_iguales_a_2_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"2");
            if(count($posiciones_iguales_a_2_array)>0 && is_array($posiciones_iguales_a_2_array))
            {
                //criterio 2
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_2_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
            }//fin if
            else
            {
                $posiciones_iguales_a_3_array=array();
                $posiciones_iguales_a_3_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"3");
                if(count($posiciones_iguales_a_3_array)>0 && is_array($posiciones_iguales_a_3_array))
                {
                        //criterio 1
                        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_3_array);
                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                }//fin if                
                else
                {
                        $array_varios_valores=array(
                                        "16",
                                        "17",
                                        "18",
                                        "19",
                                        "20",
                                        "22"
                                        );
            
                        $array_iguales_a_16_17_18_19_20_22=array();
                        $array_iguales_a_16_17_18_19_20_22=iguales_a_varios_valores($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_varios_valores);
                        
                        if(count($array_iguales_a_16_17_18_19_20_22)>0 && is_array($array_iguales_a_16_17_18_19_20_22))
                        {
                            //criterio 4
                            $mayor_valor_mayor_frecuencia="";
                            $mayor_valor_mayor_frecuencia=calcula_mayor_valor_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_16_17_18_19_20_22);
                            $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                            
                        }//fin if
                        else
                        {
                            $posiciones_iguales_a_0_array=array();
                            $posiciones_iguales_a_0_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
                            if(count($posiciones_iguales_a_0_array)>0 && is_array($posiciones_iguales_a_0_array))
                            {
                                    //criterio 5
                                    $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_0_array);
                                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                            }//fin if
                            else
                            {
                                    //criterio para no dejar faltante
                                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                            } 
                        }//fin if
                }//fin else
            }//fin if
            
    }//fin else
    //FIN CAMPO 86
    
    //CAMPO 87
    //asociado CAMPO 88,CAMPO 89,CAMPO 90
    $numero_orden_campo_actual=87;
    
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

            $orden_ultimo_registro_campo_actual=$orden_mayor_fecha_campo_actual;
            
            $numero_campo_dependiente=88;
            $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
            $numero_campo_dependiente=89;
            $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
            $numero_campo_dependiente=90;
            $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
    }//fin if
    else
    {
        $posiciones_iguales_a_1800_01_01_array=array();
        $posiciones_iguales_a_1800_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1800-01-01");
        if(count($posiciones_iguales_a_1800_01_01_array)>0 && is_array($posiciones_iguales_a_1800_01_01_array))
        {
                //criterio 2
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1800_01_01_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                
                $numero_campo_dependiente=88;
                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                $numero_campo_dependiente=89;
                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                $numero_campo_dependiente=90;
                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
        }//fin if
        else
        {
                $matriz_comparaciones=array();
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1805-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1810-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1825-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1830-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1835-01-01");
                
                $array_iguales_a_codigos_desde_1805_hasta_1835=array();
                $array_iguales_a_codigos_desde_1805_hasta_1835=union_arrays_de_matriz_posiciones($matriz_comparaciones);
                
                if(count($array_iguales_a_codigos_desde_1805_hasta_1835)>0 && is_array($array_iguales_a_codigos_desde_1805_hasta_1835))
                {
                    //criterio 3
                    $mayor_valor_mayor_frecuencia="";
                    $mayor_valor_mayor_frecuencia=calcula_mayor_fecha_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_codigos_desde_1805_hasta_1835);
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                    
                    $posiciones_iguales_a_valor_mayor_frecuencia=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$mayor_valor_mayor_frecuencia);
                    $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_valor_mayor_frecuencia);
                    
                    $numero_campo_dependiente=88;
                    $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                    $numero_campo_dependiente=89;
                    $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                    $numero_campo_dependiente=90;
                    $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                    
                }//fin if
                else
                {
                        $posiciones_iguales_a_1845_01_01_array=array();
                        $posiciones_iguales_a_1845_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1845-01-01");
                        if(count($posiciones_iguales_a_1845_01_01_array)>0 && is_array($posiciones_iguales_a_1845_01_01_array))
                        {
                                //criterio 2
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1845_01_01_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                                
                                $numero_campo_dependiente=88;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                                $numero_campo_dependiente=89;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                                $numero_campo_dependiente=90;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                        }//fin if
                        else
                        {
                                //criterio para no dejar faltante
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                                
                                $numero_campo_dependiente=88;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[0][$numero_campo_dependiente];
                                $numero_campo_dependiente=89;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[0][$numero_campo_dependiente];
                                $numero_campo_dependiente=90;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[0][$numero_campo_dependiente];
                        } 
                }//fin if
        }//fin else
    }//fin else
    //FIN CAMPO 87
    
    
    //CAMPO 91
    //asociado CAMPO 92
    $numero_orden_campo_actual=91;
    
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

            $orden_ultimo_registro_campo_actual=$orden_mayor_fecha_campo_actual;
            
            $numero_campo_dependiente=92;
            $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
    }//fin if
    else
    {
        $posiciones_iguales_a_1800_01_01_array=array();
        $posiciones_iguales_a_1800_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1800-01-01");
        if(count($posiciones_iguales_a_1800_01_01_array)>0 && is_array($posiciones_iguales_a_1800_01_01_array))
        {
                //criterio 2
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1800_01_01_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                
                $numero_campo_dependiente=92;
                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
        }//fin if
        else
        {
                $matriz_comparaciones=array();
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1805-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1810-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1825-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1830-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1835-01-01");
                
                $array_iguales_a_codigos_desde_1805_hasta_1835=array();
                $array_iguales_a_codigos_desde_1805_hasta_1835=union_arrays_de_matriz_posiciones($matriz_comparaciones);
                
                if(count($array_iguales_a_codigos_desde_1805_hasta_1835)>0 && is_array($array_iguales_a_codigos_desde_1805_hasta_1835))
                {
                    //criterio 3
                    $mayor_valor_mayor_frecuencia="";
                    $mayor_valor_mayor_frecuencia=calcula_mayor_fecha_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_codigos_desde_1805_hasta_1835);
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                    
                    $posiciones_iguales_a_valor_mayor_frecuencia=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$mayor_valor_mayor_frecuencia);
                    $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_valor_mayor_frecuencia);
                    
                    $numero_campo_dependiente=92;
                    $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                    
                }//fin if
                else
                {
                        $posiciones_iguales_a_1845_01_01_array=array();
                        $posiciones_iguales_a_1845_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1845-01-01");
                        if(count($posiciones_iguales_a_1845_01_01_array)>0 && is_array($posiciones_iguales_a_1845_01_01_array))
                        {
                                //criterio 2
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1845_01_01_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                                
                                $numero_campo_dependiente=92;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                        }//fin if
                        else
                        {
                                //criterio para no dejar faltante
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                                
                                $numero_campo_dependiente=92;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[0][$numero_campo_dependiente];
                        } 
                }//fin if
        }//fin else
    }//fin else
    //FIN CAMPO 91
    
    //CAMPO 93
    //asociado CAMPO 94, CAMPO 95
    $numero_orden_campo_actual=93;
    
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

            $orden_ultimo_registro_campo_actual=$orden_mayor_fecha_campo_actual;
            
            $numero_campo_dependiente=94;
            $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
            $numero_campo_dependiente=95;
            $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
    }//fin if
    else
    {
        $posiciones_iguales_a_1800_01_01_array=array();
        $posiciones_iguales_a_1800_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1800-01-01");
        if(count($posiciones_iguales_a_1800_01_01_array)>0 && is_array($posiciones_iguales_a_1800_01_01_array))
        {
                //criterio 2
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1800_01_01_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                
                $numero_campo_dependiente=94;
                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                $numero_campo_dependiente=95;
                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
        }//fin if
        else
        {
                $matriz_comparaciones=array();
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1805-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1810-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1825-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1830-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1835-01-01");
                
                $array_iguales_a_codigos_desde_1805_hasta_1835=array();
                $array_iguales_a_codigos_desde_1805_hasta_1835=union_arrays_de_matriz_posiciones($matriz_comparaciones);
                
                if(count($array_iguales_a_codigos_desde_1805_hasta_1835)>0 && is_array($array_iguales_a_codigos_desde_1805_hasta_1835))
                {
                    //criterio 3
                    $mayor_valor_mayor_frecuencia="";
                    $mayor_valor_mayor_frecuencia=calcula_mayor_fecha_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_codigos_desde_1805_hasta_1835);
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                    
                    $posiciones_iguales_a_valor_mayor_frecuencia=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$mayor_valor_mayor_frecuencia);
                    $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_valor_mayor_frecuencia);
                    
                    $numero_campo_dependiente=94;
                    $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                    $numero_campo_dependiente=95;
                    $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                    
                }//fin if
                else
                {
                        $posiciones_iguales_a_1845_01_01_array=array();
                        $posiciones_iguales_a_1845_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1845-01-01");
                        if(count($posiciones_iguales_a_1845_01_01_array)>0 && is_array($posiciones_iguales_a_1845_01_01_array))
                        {
                                //criterio 2
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1845_01_01_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                                
                                $numero_campo_dependiente=94;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                                $numero_campo_dependiente=95;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                        }//fin if
                        else
                        {
                                //criterio para no dejar faltante
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                                
                                $numero_campo_dependiente=94;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[0][$numero_campo_dependiente];
                                $numero_campo_dependiente=95;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[0][$numero_campo_dependiente];
                        } 
                }//fin if
        }//fin else
    }//fin else
    //FIN CAMPO 93
    
    //FIN CANCER DE CERVIX
    
    //CANCER DE SENO
    
    //CAMPO 27
    $numero_orden_campo_actual=27;
    
        $posiciones_iguales_a_1_array=array();
        $posiciones_iguales_a_1_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
        if(count($posiciones_iguales_a_1_array)>0 && is_array($posiciones_iguales_a_1_array))
        {
                //criterio 1
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                
        }//fin if
        else
        {
                $posiciones_iguales_a_2_array=array();
                $posiciones_iguales_a_2_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"2");
                if(count($posiciones_iguales_a_2_array)>0 && is_array($posiciones_iguales_a_2_array))
                {
                        //criterio 2
                        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_2_array);
                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                        
                }//fin if
                else
                {
                        $posiciones_iguales_a_21_array=array();
                        $posiciones_iguales_a_21_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"21");
                        if(count($posiciones_iguales_a_21_array)>0 && is_array($posiciones_iguales_a_21_array))
                        {
                                //criterio 4
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_21_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                                
                        }//fin if
                        else
                        {
                                //criterio para no dejar faltante
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                        } 
                    
                }//fin else
                
        }//fin else
    //FIN CAMPO 27
    
    //CAMPO 96
    //asociado CAMPO 97,CAMPO 98
    $numero_orden_campo_actual=96;
    
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

            $orden_ultimo_registro_campo_actual=$orden_mayor_fecha_campo_actual;
            
            $numero_campo_dependiente=97;
            $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
            $numero_campo_dependiente=98;
            $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
    }//fin if
    else
    {
        $posiciones_iguales_a_1800_01_01_array=array();
        $posiciones_iguales_a_1800_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1800-01-01");
        if(count($posiciones_iguales_a_1800_01_01_array)>0 && is_array($posiciones_iguales_a_1800_01_01_array))
        {
                //criterio 2
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1800_01_01_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                
                $numero_campo_dependiente=97;
                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                $numero_campo_dependiente=98;
                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
        }//fin if
        else
        {
                $matriz_comparaciones=array();
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1805-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1810-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1825-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1830-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1835-01-01");
                
                $array_iguales_a_codigos_desde_1805_hasta_1835=array();
                $array_iguales_a_codigos_desde_1805_hasta_1835=union_arrays_de_matriz_posiciones($matriz_comparaciones);
                
                if(count($array_iguales_a_codigos_desde_1805_hasta_1835)>0 && is_array($array_iguales_a_codigos_desde_1805_hasta_1835))
                {
                    //criterio 3
                    $mayor_valor_mayor_frecuencia="";
                    $mayor_valor_mayor_frecuencia=calcula_mayor_fecha_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_codigos_desde_1805_hasta_1835);
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                    
                    $posiciones_iguales_a_valor_mayor_frecuencia=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$mayor_valor_mayor_frecuencia);
                    $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_valor_mayor_frecuencia);
                    
                    $numero_campo_dependiente=97;
                    $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                    $numero_campo_dependiente=98;
                    $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                    
                }//fin if
                else
                {
                        $posiciones_iguales_a_1845_01_01_array=array();
                        $posiciones_iguales_a_1845_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1845-01-01");
                        if(count($posiciones_iguales_a_1845_01_01_array)>0 && is_array($posiciones_iguales_a_1845_01_01_array))
                        {
                                //criterio 2
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1845_01_01_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                                
                                $numero_campo_dependiente=97;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                                $numero_campo_dependiente=98;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                        }//fin if
                        else
                        {
                                //criterio para no dejar faltante
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                                
                                $numero_campo_dependiente=97;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[0][$numero_campo_dependiente];
                                $numero_campo_dependiente=98;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[0][$numero_campo_dependiente];
                        } 
                }//fin if
        }//fin else
    }//fin else
    //FIN CAMPO 96
    
    //CAMPO 99
    $numero_orden_campo_actual=99;
    
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
        $posiciones_iguales_a_1800_01_01_array=array();
        $posiciones_iguales_a_1800_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1800-01-01");
        if(count($posiciones_iguales_a_1800_01_01_array)>0 && is_array($posiciones_iguales_a_1800_01_01_array))
        {
                //criterio 2
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1800_01_01_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
                $matriz_comparaciones=array();
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1805-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1810-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1825-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1830-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1835-01-01");
                
                $array_iguales_a_codigos_desde_1805_hasta_1835=array();
                $array_iguales_a_codigos_desde_1805_hasta_1835=union_arrays_de_matriz_posiciones($matriz_comparaciones);
                
                if(count($array_iguales_a_codigos_desde_1805_hasta_1835)>0 && is_array($array_iguales_a_codigos_desde_1805_hasta_1835))
                {
                    //criterio 3
                    $mayor_valor_mayor_frecuencia="";
                    $mayor_valor_mayor_frecuencia=calcula_mayor_fecha_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_codigos_desde_1805_hasta_1835);
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                    
                }//fin if
                else
                {
                        $posiciones_iguales_a_1845_01_01_array=array();
                        $posiciones_iguales_a_1845_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1845-01-01");
                        if(count($posiciones_iguales_a_1845_01_01_array)>0 && is_array($posiciones_iguales_a_1845_01_01_array))
                        {
                                //criterio 2
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1845_01_01_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                        }//fin if
                        else
                        {
                                //criterio para no dejar faltante
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                        } 
                }//fin if
        }//fin else
    }//fin else
    //FIN CAMPO 99
    
    
    //CAMPO 100
    //asociado CAMPO 101,CAMPO 102
    $numero_orden_campo_actual=100;
    
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

            $orden_ultimo_registro_campo_actual=$orden_mayor_fecha_campo_actual;
            
            $numero_campo_dependiente=101;
            $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
            $numero_campo_dependiente=102;
            $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
    }//fin if
    else
    {
        $posiciones_iguales_a_1800_01_01_array=array();
        $posiciones_iguales_a_1800_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1800-01-01");
        if(count($posiciones_iguales_a_1800_01_01_array)>0 && is_array($posiciones_iguales_a_1800_01_01_array))
        {
                //criterio 2
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1800_01_01_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                
                $numero_campo_dependiente=101;
                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                $numero_campo_dependiente=102;
                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
        }//fin if
        else
        {
                $matriz_comparaciones=array();
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1805-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1810-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1825-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1830-01-01");
                $matriz_comparaciones[]=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1835-01-01");
                
                $array_iguales_a_codigos_desde_1805_hasta_1835=array();
                $array_iguales_a_codigos_desde_1805_hasta_1835=union_arrays_de_matriz_posiciones($matriz_comparaciones);
                
                if(count($array_iguales_a_codigos_desde_1805_hasta_1835)>0 && is_array($array_iguales_a_codigos_desde_1805_hasta_1835))
                {
                    //criterio 3
                    $mayor_valor_mayor_frecuencia="";
                    $mayor_valor_mayor_frecuencia=calcula_mayor_fecha_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_codigos_desde_1805_hasta_1835);
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
                    
                    $posiciones_iguales_a_valor_mayor_frecuencia=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$mayor_valor_mayor_frecuencia);
                    $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_valor_mayor_frecuencia);
                    
                    $numero_campo_dependiente=101;
                    $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                    $numero_campo_dependiente=102;
                    $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                    
                }//fin if
                else
                {
                        $posiciones_iguales_a_1845_01_01_array=array();
                        $posiciones_iguales_a_1845_01_01_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1845-01-01");
                        if(count($posiciones_iguales_a_1845_01_01_array)>0 && is_array($posiciones_iguales_a_1845_01_01_array))
                        {
                                //criterio 2
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1845_01_01_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                                
                                $numero_campo_dependiente=101;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                                $numero_campo_dependiente=102;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_campo_dependiente];
                        }//fin if
                        else
                        {
                                //criterio para no dejar faltante
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                                
                                $numero_campo_dependiente=101;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[0][$numero_campo_dependiente];
                                $numero_campo_dependiente=102;
                                $arreglo_campos_reparados[$numero_campo_dependiente]=$matrix_registros_duplicados_afiliado_actual[0][$numero_campo_dependiente];
                        } 
                }//fin if
        }//fin else
    }//fin else
    //FIN CAMPO 100
    
    
    //FIN CANCER DE SENO
    
    //CAMPO 19
    $numero_orden_campo_actual=19;
    
        $posiciones_iguales_a_1_array=array();
        $posiciones_iguales_a_1_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
        if(count($posiciones_iguales_a_1_array)>0 && is_array($posiciones_iguales_a_1_array))
        {
                //criterio 1
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
                $posiciones_iguales_a_2_array=array();
                $posiciones_iguales_a_2_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"2");
                if(count($posiciones_iguales_a_2_array)>0 && is_array($posiciones_iguales_a_2_array))
                {
                        //criterio 3
                        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_2_array);
                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                }//fin if
                else
                {
                        $posiciones_iguales_a_21_array=array();
                        $posiciones_iguales_a_21_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"21");
                        if(count($posiciones_iguales_a_21_array)>0 && is_array($posiciones_iguales_a_21_array))
                        {
                                //criterio 3
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_21_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                        }//fin if
                        else
                        {
                                $posiciones_iguales_a_0_array=array();
                                $posiciones_iguales_a_0_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
                                if(count($posiciones_iguales_a_0_array)>0 && is_array($posiciones_iguales_a_0_array))
                                {
                                        //criterio 3
                                        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_0_array);
                                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                                }//fin if
                                else
                                {
                                        //criterio para no dejar faltante
                                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                                }     
                        }//fin else
                    
                }//fin else
                
        }//fin else
    
    //FIN CAMPO 19
    
    //CAMPO 18
    $numero_orden_campo_actual=18;
    
        $posiciones_iguales_a_1_array=array();
        $posiciones_iguales_a_1_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
        if(count($posiciones_iguales_a_1_array)>0 && is_array($posiciones_iguales_a_1_array))
        {
                //criterio 1
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_1_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
                $posiciones_iguales_a_2_array=array();
                $posiciones_iguales_a_2_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"2");
                if(count($posiciones_iguales_a_2_array)>0 && is_array($posiciones_iguales_a_2_array))
                {
                        //criterio 3
                        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_2_array);
                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                }//fin if
                else
                {
                        $posiciones_iguales_a_21_array=array();
                        $posiciones_iguales_a_21_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"21");
                        if(count($posiciones_iguales_a_21_array)>0 && is_array($posiciones_iguales_a_21_array))
                        {
                                //criterio 3
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_21_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                        }//fin if
                        else
                        {
                                //criterio para no dejar faltante
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                        } 
                        
                    
                }//fin else
                
        }//fin else
    
    //FIN CAMPO 18
    
    //CAMPO 34
        $numero_orden_campo_actual=34;
        
        $matriz_comparaciones=array();
        $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
        $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"999");
        $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
                                          
        $array_diferentes_a_0_999_vacio=array();
        $array_diferentes_a_0_999_vacio=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
        
        if(count($array_diferentes_a_0_999_vacio)>0 && is_array($array_diferentes_a_0_999_vacio))
        {
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$array_diferentes_a_0_999_vacio);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }
        else
        {
                $posiciones_iguales_a_999_array=array();
                $posiciones_iguales_a_999_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"999");
                if(count($posiciones_iguales_a_999_array)>0 && is_array($posiciones_iguales_a_999_array))
                {
                        //criterio 3
                        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_999_array);
                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                }//fin if
                else
                {
                        $posiciones_iguales_a_0_array=array();
                        $posiciones_iguales_a_0_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
                        if(count($posiciones_iguales_a_0_array)>0 && is_array($posiciones_iguales_a_0_array))
                        {
                                //criterio 3
                                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,1,$posiciones_iguales_a_0_array);
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                        }//fin else
                        else
                        {
                                //criterio para no dejar faltante
                                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                        } 
                }//fin else
        }//fin else
    //FIN CAMPO 34
    
    //ciclo para verificar que los datos fueron insertados de forma correcta en el arreglo para insertar el nuevo registro resultante de los duplicados
    $campo_pyp4505_con_numero_orden_faltante=-1;
    $cont_campos_verificacion=0;
    $string_campos_faltantes="";
    foreach($arreglo_campos_reparados as $campo_pyp4505_con_numero_orden_arreglado)
    {
        if((string)$campo_pyp4505_con_numero_orden_arreglado=="NO_VALOR_1024488856")
        {
            $bool_fueron_procesados_duplicados_en_un_registro=false;
            $campo_pyp4505_con_numero_orden_faltante=$cont_campos_verificacion;
            $string_campos_faltantes.=" ".$campo_pyp4505_con_numero_orden_faltante;
        }
        $cont_campos_verificacion++;
    }
    if($campo_pyp4505_con_numero_orden_faltante!=-1 && $numero_duplicados_de_duplicado>0)
    {
        //echo "<script>alert('el campo faltante es $string_campos_faltantes ');</script>";
        echo "<b>el campo faltante es $string_campos_faltantes $tipo_id_duplicado_actual $numero_id_duplicado_actual</b><br>";
    }
    //fin ciclo para verificar que los datos fueron insertados de forma correcta en el arreglo para insertar el nuevo registro resultante de los duplicados
    
    return $arreglo_campos_reparados;    
}
?>