<?php
ignore_user_abort(true);
set_time_limit(936000);
ini_set('max_execution_time', 936000);
ini_set('memory_limit', '900M');

require '../librerias_externas/Smarty-3.1.17/libs/Smarty.class.php';
include_once ('../utiles/clase_coneccion_bd.php');
include_once ('../utiles/funciones_crear_menu.php');
require_once '../utiles/queries_utiles_bd.php';

require_once '../utiles/crear_zip.php';


include ("../librerias_externas/PHPMailer/class.phpmailer.php");
include ("../librerias_externas/PHPMailer/class.smtp.php");
require_once ("../librerias_externas/PHPExcel/PHPExcel.php");
require_once ("../librerias_externas/PHPExcel/PHPExcel/Writer/Excel2007.php");

require_once '../utiles/configuracion_global_email.php';

$smarty = new Smarty;
$coneccionBD = new conexion();
$coneccionBD->crearConexion();
session_start();

if (!isset($_SESSION['usuario']) && !isset($_SESSION['tipo_perfil']))
{
	
	header ("Location: ../index.php");
}

function alphanumericAndSpace( $string )
{
	$string = str_replace("á","a",$string);
	$string = str_replace("é","e",$string);
	$string = str_replace("í","i",$string);
	$string = str_replace("ó","o",$string);
	$string = str_replace("ú","u",$string);
	$string = str_replace("Á","A",$string);
	$string = str_replace("É","E",$string);
	$string = str_replace("Í","I",$string);
	$string = str_replace("Ó","O",$string);
	$string = str_replace("Ú","U",$string);
	
	$string = str_replace("ñ","n",$string);
	$string = str_replace("Ñ","N",$string);
    return trim(preg_replace('/[^a-zA-Z0-9\s, ;\-@.\/]/', '', $string));
}

function alphanumericAndSpace_include_br( $string )
{
	$string = str_replace("á","a",$string);
	$string = str_replace("é","e",$string);
	$string = str_replace("í","i",$string);
	$string = str_replace("ó","o",$string);
	$string = str_replace("ú","u",$string);
	$string = str_replace("Á","A",$string);
	$string = str_replace("É","E",$string);
	$string = str_replace("Í","I",$string);
	$string = str_replace("Ó","O",$string);
	$string = str_replace("Ú","U",$string);
	
	$string = str_replace("ñ","n",$string);
	$string = str_replace("Ñ","N",$string);
    return trim(preg_replace('/[^a-zA-Z0-9\s, ;\-@.\/\<\>\_\:]/', '', $string));
}


function alphanumericAndSpace4( $string )
{
    $string = str_replace("á","a",$string);
    $string = str_replace("é","e",$string);
    $string = str_replace("í","i",$string);
    $string = str_replace("ó","o",$string);
    $string = str_replace("ú","u",$string);
    $string = str_replace("Á","A",$string);
    $string = str_replace("É","E",$string);
    $string = str_replace("Í","I",$string);
    $string = str_replace("Ó","O",$string);
    $string = str_replace("Ú","U",$string);
    
    $string = str_replace("ñ","n",$string);
    $string = str_replace("Ñ","N",$string);
    $cadena = preg_replace('/[^a-zA-Z0-9\s,\-\/\.]/', '', $string);
    return $cadena;
}

function procesar_mensaje($mensaje)
{
	$mensaje_procesado = str_replace("á","a",$mensaje);
	$mensaje_procesado = str_replace("é","e",$mensaje_procesado);
	$mensaje_procesado = str_replace("í","i",$mensaje_procesado);
	$mensaje_procesado = str_replace("ó","o",$mensaje_procesado);
	$mensaje_procesado = str_replace("ú","u",$mensaje_procesado);
	$mensaje_procesado = str_replace("ñ","n",$mensaje_procesado);
	$mensaje_procesado = str_replace("Á","A",$mensaje_procesado);
	$mensaje_procesado = str_replace("É","E",$mensaje_procesado);
	$mensaje_procesado = str_replace("Í","I",$mensaje_procesado);
	$mensaje_procesado = str_replace("Ó","O",$mensaje_procesado);
	$mensaje_procesado = str_replace("Ú","U",$mensaje_procesado);
	$mensaje_procesado = str_replace("Ñ","N",$mensaje_procesado);
	$mensaje_procesado = str_replace(" "," ",$mensaje_procesado);
	$mensaje_procesado = str_replace("'"," ",$mensaje_procesado);
	$mensaje_procesado = str_replace("\n"," ",$mensaje_procesado);
	$mensaje_procesado = alphanumericAndSpace_include_br($mensaje_procesado);
	
	return $mensaje_procesado;
}

//funciones migradas sdesde el script reparador


function convert_to_standard_notation($floatAsString)
{
    $norm = strval(floatval($floatAsString));

    if (($e = strrchr($norm, 'E')) === false) {
        return $norm;
    }

    return number_format($norm, -intval(substr($e, 1)));
}



//recibe en dia mes year
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



function edad_calculada_a_partir_de_dos_fechas($fecha_nacimiento_param,$fecha_secundaria_param,$tiempo)
{
	$edad_en_years= -1;
	$edad_meses =-1;	
	$edad_dias =-1;

	$array_fecha_nacimiento=explode("-",trim($fecha_nacimiento_param) );
	$array_fecha_secundaria_para_nacimiento=explode("-",$fecha_secundaria_param);
	$array_edad_fc=array();
	
	if(count($array_fecha_nacimiento)==3 
		&& count($array_fecha_secundaria_para_nacimiento)==3
		)
	{
		$array_edad_fc=edad_years_months_days($array_fecha_nacimiento[2]."-".$array_fecha_nacimiento[1]."-".$array_fecha_nacimiento[0],$array_fecha_secundaria_para_nacimiento[2]."-".$array_fecha_secundaria_para_nacimiento[1]."-".$array_fecha_secundaria_para_nacimiento[0]);
		$edad_en_years=intval($array_edad_fc['y']);
		$edad_meses=(intval($array_edad_fc['y'])*12)+$array_edad_fc['m'];
		$edad_dias=diferencia_dias_entre_fechas($fecha_nacimiento_param,$fecha_secundaria_param);
	}//fin if

	if(strtoupper( trim($tiempo) )=="Y")
	{
		return $edad_en_years;
	}
	else if(strtoupper( trim($tiempo) )=="M")
	{
		return $edad_meses;
	}
	else if(strtoupper( trim($tiempo) )=="D")
	{
		return $edad_dias;
	}
	
}//fin function

function diferencia_dias_entre_fechas($fecha_1,$fecha_2)
{
    //las fechas deben ser cadenas de 10 caracteres en el siguiente formato AAAA-MM-DD, ejemplo: 1989-03-03
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
    try
    {
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
	    }//fin else
    }//fin try
	catch(Exception $e)
	{
		//echo "<script>alert('error excepcion: ".$e->getMessage()."')</script>";
		return false;
	}//fin catch
    
    return $diferencia_dias_entre_fechas;
    
}//fin calculo diferencia entre fechas


function corrige_longitud_fecha($array_fecha_corregida,$fecha_corte,$fase=0)
{
    if(is_array($array_fecha_corregida) && is_array($fecha_corte))
    {    
        if(count($array_fecha_corregida)==3)
        {
            //PARTE YEAR
	    //accede a corregir year si la longitud de dia y mes son iguales o menores a dos	    
	    if(ctype_digit($array_fecha_corregida[0])
		&& strlen($array_fecha_corregida[0])<4
		&& strlen($array_fecha_corregida[1])<=2
		&& strlen($array_fecha_corregida[2])<=2
	       )
	    {
		//accede a corregir year si la longitud de dia y mes son iguales o menores a dos para cuando el year es menor de 4 caracteres
		if(intval($array_fecha_corregida[0])>200)
		{
		    $longitud_year_corte=strlen($fecha_corte[0]);
		    $ultimo_digito_year_corte=substr($fecha_corte[0],($longitud_year_corte-1),$longitud_year_corte);
		    $array_fecha_corregida[0]=$array_fecha_corregida[0].$ultimo_digito_year_corte;
		}//fin if
		else if(intval($array_fecha_corregida[0])>190)
		{
		    $array_fecha_corregida[0]=$array_fecha_corregida[0]."0";
		}//fin else
		
	    }//fin if
	    else if(ctype_digit($array_fecha_corregida[0])
	       && strlen($array_fecha_corregida[0])>4
	       && strlen($array_fecha_corregida[0])>strlen($array_fecha_corregida[1])
		&& strlen($array_fecha_corregida[0])>strlen($array_fecha_corregida[2])
	       )
	    {
		$array_fecha_corregida[0]=intval($array_fecha_corregida[0]);
		if(strlen($array_fecha_corregida[0])>4)
		{
		    $array_fecha_corregida[0]=substr($array_fecha_corregida[0],0,4);
		}
		else if(strlen($array_fecha_corregida[0])<4)
		{
		    if(intval($array_fecha_corregida[0])>200)
		    {
			$longitud_year_corte=strlen($fecha_corte[0]);
			$ultimo_digito_year_corte=substr($fecha_corte[0],($longitud_year_corte-1),$longitud_year_corte);
			$array_fecha_corregida[0]=$array_fecha_corregida[0].$ultimo_digito_year_corte;
		    }//fin if
		    else if(strlen($array_fecha_corregida[0])==3
			    && intval($array_fecha_corregida[0])>190
			    )
		    {
			$array_fecha_corregida[0]=$array_fecha_corregida[0]."0";
		    }//fin else
		}//fin else
	    }//fin else if
	    else if(ctype_digit($array_fecha_corregida[0])
		    && ctype_digit($array_fecha_corregida[1])
		    && ctype_digit($array_fecha_corregida[2])
		    && strlen($array_fecha_corregida[0])==strlen($array_fecha_corregida[1])
		    && strlen($array_fecha_corregida[0])==strlen($array_fecha_corregida[2])
		    )
	    {
		$array_fecha_corregida[0]="".intval($array_fecha_corregida[0]);
		$array_fecha_corregida[1]="".intval($array_fecha_corregida[1]);
		$array_fecha_corregida[2]="".intval($array_fecha_corregida[2]);
		if(ctype_digit($array_fecha_corregida[0])
		    && ctype_digit($array_fecha_corregida[1])
		    && ctype_digit($array_fecha_corregida[2])
		    && strlen($array_fecha_corregida[0])==strlen($array_fecha_corregida[1])
		    && strlen($array_fecha_corregida[0])==strlen($array_fecha_corregida[2])
		    )
		{
		    $diferencia_0=0;
		    $diferencia_0=intval($fecha_corte[0])-intval($array_fecha_corregida[0]);
		    $diferencia_1=0;
		    $diferencia_1=intval($fecha_corte[0])-intval($array_fecha_corregida[1]);
		    $diferencia_2=0;
		    $diferencia_2=intval($fecha_corte[0])-intval($array_fecha_corregida[2]);
		    
		    
		    if(intval($array_fecha_corregida[0])<(intval($fecha_corte[0])-5) || intval($array_fecha_corregida[0])>intval($fecha_corte[0]))
		    {
			//caso en el que el valor del campo 0 no esta remotamente cercano a el year de la fecha de corte en 5 years o es superior
			if($diferencia_1>=0 && $diferencia_2>=0)
			{
			    $temp=$array_fecha_corregida[0];
			    if($diferencia_1<$diferencia_2)
			    {				
				$array_fecha_corregida[0]=$array_fecha_corregida[1];
				$array_fecha_corregida[1]=$temp;
				
			    }//fin if
			    else if($diferencia_2<$diferencia_1)
			    {
				$array_fecha_corregida[0]=$array_fecha_corregida[2];
				$array_fecha_corregida[2]=$temp;
			    }//fin else if
			}//fin if
			
		    }//fin if
		    else
		    {
			$diferencia_seleccionada="none";
			if($diferencia_1>=0 && $diferencia_2>=0)
			{
			    if($diferencia_1<$diferencia_2)
			    {				
				$diferencia_seleccionada="dif_1";
				
			    }//fin if
			    else if($diferencia_2<$diferencia_1)
			    {
				$diferencia_seleccionada="dif_2";
			    }//fin else if
			}//fin if
			
			if($diferencia_seleccionada=="dif_1")
			{
			    $temp=$array_fecha_corregida[0];
			    if($diferencia_1<$diferencia_0)
			    {				
				$array_fecha_corregida[0]=$array_fecha_corregida[1];
				$array_fecha_corregida[1]=$temp;
				
			    }//fin if
			}//fin if
			else if($diferencia_seleccionada=="dif_2")
			{
			    $temp=$array_fecha_corregida[0];
			    if($diferencia_2<$diferencia_0)
			    {				
				$array_fecha_corregida[0]=$array_fecha_corregida[2];
				$array_fecha_corregida[2]=$temp;
				
			    }//fin if
			}//fin else
		    }//fin else
		}//fin if
		
	    }//fin else if
	    //FIN PARTE YEAR
            
            //PARTE MES
            if(ctype_digit($array_fecha_corregida[1])
               && strlen($array_fecha_corregida[1])==1
               )
            {
                $array_fecha_corregida[1]="0".$array_fecha_corregida[1];
            }//fin if
            else if(ctype_digit($array_fecha_corregida[1])
               && strlen($array_fecha_corregida[1])>2
               && strlen($array_fecha_corregida[1])<4
               )
            {
                $array_fecha_corregida[1]=substr($array_fecha_corregida[1],0,2);
            }//fin else if
	    //FIN PARTE MES
            
            //PARTE DIA
            if(ctype_digit($array_fecha_corregida[2])
               && strlen($array_fecha_corregida[2])==1
               )
            {
                $array_fecha_corregida[2]="0".$array_fecha_corregida[2];
            }//fin if
            else if(ctype_digit($array_fecha_corregida[2])
               && strlen($array_fecha_corregida[2])>2
               && strlen($array_fecha_corregida[2])<4
               )
            {
                $array_fecha_corregida[2]=substr($array_fecha_corregida[2],0,2);
            }//fin else if
	    //FIN PARTE DIA
            
            $fecha_corregida="";
            $fecha_corregida=$array_fecha_corregida[0]."-".$array_fecha_corregida[1]."-".$array_fecha_corregida[2];
            return $fecha_corregida;
        }//fin if
        else 
        {
            echo 'Alguno de los parametros no es un arreglo. ';
            return false;
        }
    }//fin if son array los parametros
}//fin funcion corrige_longitud_fecha

function corrector_formato_fecha($campo_fecha,$fecha_corte_param,$es_fecha_nacimiento=false,$campo_especial=-1,$campo_debug=0)
{
    date_default_timezone_set ("America/Bogota");
    
    $fecha_corte=explode("-",$fecha_corte_param);
    $date_de_corte=date($fecha_corte[0]."-".$fecha_corte[1]."-".$fecha_corte[2]);
 
    $fecha_corregida="";
    $fecha_corregida=trim($campo_fecha);
    //$fecha_corregida=substr($fecha_corregida,0,10);
    $fecha_corregida=str_replace("/","-",$fecha_corregida);
    $array_fecha_corregida=explode("-",$fecha_corregida);
    
    //echo 'fecha antes de corregir ',$campo_fecha," ";
    
    if(is_array($array_fecha_corregida)
       && count($array_fecha_corregida)==3
       )
    {
	$fecha_corregida=corrige_longitud_fecha($array_fecha_corregida,$fecha_corte);
    }    
    
    $fecha_corregida=substr($fecha_corregida,0,10);
    $array_fecha_corregida=explode("-",$fecha_corregida);
    
    $caso_al_que_entro="";
    
    if(count($array_fecha_corregida)==3)
    {
	if(ctype_digit($array_fecha_corregida[0]) && ctype_digit($array_fecha_corregida[1]) && ctype_digit($array_fecha_corregida[2]))
	{
	    //checkdate mm-dd-aaaa -> aaaa-mm-dd ?
	    if(checkdate($array_fecha_corregida[1],$array_fecha_corregida[2],$array_fecha_corregida[0])
	       && intval($array_fecha_corregida[0])>=32)
	    {
		//no se cambia
		$caso_al_que_entro="no cambia, caso 0 aaaa-mm-dd";
	    }
	    else
	    {
		
		if(intval($array_fecha_corregida[1])>12 && intval($array_fecha_corregida[1])<=31)
		{
		    //checkdate mm-dd-aaaa -> aaaa-dd-mm ?
		    if(checkdate($array_fecha_corregida[2],$array_fecha_corregida[1],$array_fecha_corregida[0]))
		    {
			$fecha_corregida=$array_fecha_corregida[0]."-".$array_fecha_corregida[2]."-".$array_fecha_corregida[1];
			$caso_al_que_entro="cambia, caso 1 aaaa-dd-mm";
		    }
		    else if(intval($array_fecha_corregida[2])>=32)
		    {
			//checkdate mm-dd-aaaa -> mm-dd-aaaa ?
			if(checkdate($array_fecha_corregida[0],$array_fecha_corregida[1],$array_fecha_corregida[2]))
			{
			    $fecha_corregida=$array_fecha_corregida[2]."-".$array_fecha_corregida[0]."-".$array_fecha_corregida[1];
			    $caso_al_que_entro="cambia, caso 1 mm-dd-aaaa";
			}//fin if
			else
			{
			   if($es_fecha_nacimiento==false)
			   {
				if($campo_especial==-1)
				{
					$fecha_corregida="1800-01-01";
				}
				else if($campo_especial==-2)
				{
					$fecha_corregida="1845-01-01";
				}
			   }
			   else
			   {
			     //$fecha_corregida=$date_de_corte;
			   }
			}//fin else
		    }//fin else if
		    else
		    {
			if($es_fecha_nacimiento==false)
			{
				if($campo_especial==-1)
				{
					$fecha_corregida="1800-01-01";
				}
				else if($campo_especial==-2)
				{
					$fecha_corregida="1845-01-01";
				}
			}
			else
			{
				//$fecha_corregida=$date_de_corte;
			}
		    }
		}//fin if			
		else if(intval($array_fecha_corregida[2])>=32)
		{
		    //checkdate mm-dd-aaaa -> dd-mm-aaaa ?
		    if(checkdate($array_fecha_corregida[1],$array_fecha_corregida[0],$array_fecha_corregida[2]))
		    {
			$fecha_corregida=$array_fecha_corregida[2]."-".$array_fecha_corregida[1]."-".$array_fecha_corregida[0];
			$caso_al_que_entro="cambia, caso 1 dd-mm-aaaa";
		    }//fin if
		    else
		    {
			if($es_fecha_nacimiento==false)
			{
				if($campo_especial==-1)
				{
					$fecha_corregida="1800-01-01";
				}
				else if($campo_especial==-2)
				{
					$fecha_corregida="1845-01-01";
				}
			}
			else
			{
				//$fecha_corregida=$date_de_corte;
			}
		    }//fin else
		}//fin else if
		else
		{
		    if($es_fecha_nacimiento==false)
		    {
			if($campo_especial==-1)
			{
				$fecha_corregida="1800-01-01";
			}
			else if($campo_especial==-2)
			{
				$fecha_corregida="1845-01-01";
			}
		    }
		    else
		    {
			  //$fecha_corregida=$date_de_corte;
		    }
		}//fin else
		
	    }//fin else
	    
	}//fin if
	else
	{
	    if($es_fecha_nacimiento==false)
	    {
		if($campo_especial==-1)
		{
			$fecha_corregida="1800-01-01";
		}
		else if($campo_especial==-2)
		{
			$fecha_corregida="1845-01-01";
		}
	    }
	    else
	    {
		$fecha_corregida=$date_de_corte;
	    }
	}//fin else
    }
    else
    {
	if($es_fecha_nacimiento==false)
	{
	    if($campo_especial==-1)
	    {
		    $fecha_corregida="1800-01-01";
	    }
	    else if($campo_especial==-2)
	    {
		    $fecha_corregida="1845-01-01";
	    }
	}
	else
	{
	    $fecha_corregida=$date_de_corte;
	}
    }//fin else
    
    $nuevo_array_fecha_corregida=explode("-",$fecha_corregida);
    if(is_array($nuevo_array_fecha_corregida)
       && count($nuevo_array_fecha_corregida)==3
       )
    {
	$fecha_corregida=corrige_longitud_fecha($nuevo_array_fecha_corregida,$fecha_corte,1);
    }
    
    $check_formato_fecha=explode("-",$fecha_corregida);
    
    if($es_fecha_nacimiento==false && count($check_formato_fecha)==3)
    {	    
	$date_de_corte_12_meses_menos=date('Y-m-d',strtotime($fecha_corte[0]."-".$fecha_corte[1]."-".$fecha_corte[2] . ' -12 months'));
	$date_de_corte_posterior_10_meses=date('Y-m-d',strtotime($fecha_corte[0]."-".$fecha_corte[1]."-".$fecha_corte[2] . ' +10 months'));
	$diferencia_de_1900=-1;
	$interval = date_diff(new DateTime($fecha_corregida),new DateTime("1900-01-01"));
	$diferencia_de_1900 =(float)($interval->format("%r%a"));
	if($diferencia_de_1900<0)//si excede 1900 entonces no es codigo
	{
	  $interval = date_diff(new DateTime($fecha_corregida),new DateTime($date_de_corte));
	  $verificacion_fecha_corte =(float)($interval->format("%r%a"));
	  $interval = date_diff(new DateTime($fecha_corregida),new DateTime($date_de_corte_12_meses_menos));
	  $verificacion_fecha_corte_12_meses_menos=(float)($interval->format("%r%a"));
	  $interval = date_diff(new DateTime($fecha_corregida),new DateTime($date_de_corte_posterior_10_meses));
	  $verificacion_fecha_corte_pos_10_meses =(float)($interval->format("%r%a"));
	  
	  
	  if($verificacion_fecha_corte<0 && ($campo_especial==-1 || $campo_especial==-2))//excede la fecha de corete, diferencia de dias es inferior
	  {
	    $fecha_corregida=$date_de_corte;
	  }
	  /*
	  else if($verificacion_fecha_corte_12_meses_menos>0)//es inferior, por eso la diferencia de dias es mayor de cero
	  {
	   $fecha_corregida="1800-01-01";
	  }
	  */
	  
	}//fin si excede 1900 entonces no es codigo
    }//fin if si no es fecha de nacimiento
    
    if($es_fecha_nacimiento==true)
    {	    
      //echo "<script>alert('pre $campo_fecha pos $fecha_corregida $caso_al_que_entro');</script>";
    }
    
    
    return $fecha_corregida;
}//fin funcion

$menu= $_SESSION['menu_logueo_html'];
$nombre= $_SESSION['nombre_completo'];

$tipo_id=$_SESSION['tipo_id'];
$identificacion=$_SESSION['identificacion'];

$nick_user=$_SESSION['usuario'];

$correo_electronico=$_SESSION['correo'];


$perfil_usuario_actual= $_SESSION['tipo_perfil'];
$entidad_salud_usuario_actual= $_SESSION['entidad_asociada_al_nick'];

session_write_close();

$mostrarResultado = "none";
$mensaje="";
$resultadoDefinitivo="";
$utilidades = new Utilidades();
$rutaTemporal = '../TEMPORALES/';



$hubo_al_menos_un_duplicado=false;

$selector_fechas_corte="";
$selector_fechas_corte.="<input type='hidden' id='fechas_corte' name='fechas_corte' >";


$query_periodos_rips="SELECT * FROM gioss_periodo_reporte_0247_cancer;";
$resultado_query_periodos=$coneccionBD->consultar2_no_crea_cierra($query_periodos_rips);

$selector_periodo="";
$selector_periodo.="<select id='periodo' name='periodo' class='campo_azul' onchange='seleccionar_fecha_de_corte();'>";
$selector_periodo.="<option value='none'>Seleccione un periodo</option>";
foreach($resultado_query_periodos as $key=>$periodo_bd)
{
	$cod_periodo=$periodo_bd["codigo_periodo"];
	$nombre_periodo=$periodo_bd["descripcion_periodo"];
	$fecha_permitida=$periodo_bd["valor_fecha_permitida"];
	$selector_periodo.="<option value='$cod_periodo'>Periodo $cod_periodo ($nombre_periodo $fecha_permitida)</option>";
}

/*
$selector_periodo.="<option value='13'>Periodo 1er semestre (Enero 01-01 Junio 06-30)</option>";
$selector_periodo.="<option value='14'>Periodo 2do semestre (Julio 07-01 Diciembre 12-31)</option>";
*/

$selector_periodo.="</select>";



//consultar el tipo de entidad
$sql_query_tipo_entidad_asociada="SELECT * FROM gioss_entidades_sector_salud WHERE codigo_entidad='$entidad_salud_usuario_actual'; ";
$resultado_query_tipo_entidad=$coneccionBD->consultar2_no_crea_cierra($sql_query_tipo_entidad_asociada);
$tipo_entidad=$resultado_query_tipo_entidad[0]["cod_tipo_entidad"];
//fin consultar el tipo de entidad


//SELECTOR EAPB-ASOCIADA_ASOCIADA_USUARIO
$eapb="";
$eapb.="<div id='div_eapb'>";
$eapb.="<select id='eapb' name='eapb' class='campo_azul'>";
$eapb.="<option value='none'>Seleccione un EAPB</option>";

if( (intval($perfil_usuario_actual)==5 || intval($perfil_usuario_actual)==13) && (intval($tipo_entidad)==6 || intval($tipo_entidad)==7 || intval($tipo_entidad)==8 || intval($tipo_entidad)==10) )
{
	$sql_consulta_eapb_usuario_prestador="SELECT ea.codigo_entidad,ea.nombre_de_la_entidad FROM ";
	$sql_consulta_eapb_usuario_prestador.=" gioss_relacion_entre_entidades_salud re INNER JOIN gioss_entidades_sector_salud ea ON (re.entidad2 = ea.codigo_entidad) ";
	$sql_consulta_eapb_usuario_prestador.=" WHERE re.entidad1='".$entidad_salud_usuario_actual."' ";
	$sql_consulta_eapb_usuario_prestador.=";";

	$resultado_query_eapb_usuario=$coneccionBD->consultar2_no_crea_cierra($sql_consulta_eapb_usuario_prestador);

	if(count($resultado_query_eapb_usuario)>0)
	{
		foreach($resultado_query_eapb_usuario as $eapb_prestador_usuario_res)
		{
			$eapb.="<option value='".$eapb_prestador_usuario_res['codigo_entidad']."' selected>".$eapb_prestador_usuario_res['codigo_entidad']." ".$eapb_prestador_usuario_res['nombre_de_la_entidad']."</option>";
		}
	}
}//fin if si el usuario es administrador y la entidad no es eapb, por lo tanto busca la eapb asociada a la entidad
else if((intval($perfil_usuario_actual)==3 || intval($perfil_usuario_actual)==4 || intval($perfil_usuario_actual)==5 || intval($perfil_usuario_actual)==13) && (intval($tipo_entidad)!=6 && intval($tipo_entidad)!=7 && intval($tipo_entidad)!=8 && intval($tipo_entidad)!=10) )
{
	$sql_query_tipo_entidad_asociada="SELECT * FROM gioss_entidades_sector_salud WHERE codigo_entidad='$entidad_salud_usuario_actual'; ";
	$resultado_query_tipo_entidad=$coneccionBD->consultar2_no_crea_cierra($sql_query_tipo_entidad_asociada);

	if(count($resultado_query_tipo_entidad)>0)
	{
		foreach($resultado_query_tipo_entidad as $eapb_entidad)
		{
			$eapb.="<option value='".$eapb_entidad['codigo_entidad']."' selected>".$eapb_entidad['codigo_entidad']." ".$eapb_entidad['nombre_de_la_entidad']."</option>";
		}
	}
}//fin else si el usuario es de perfil 3 y la entidad es de tipo eapb

$eapb.="</select>";
$eapb.="</div>";
//FIN
$mensaje_div="<div id='mensaje_div' style='text-align:left;'></div>";
$res_def_div="<div id='resultado_definitivo' style='text-align:left;'></div>";
$smarty->assign("mensaje_proceso", $mensaje_div, true);
$smarty->assign("mostrarResultado",$mostrarResultado,true);
$smarty->assign("resultado_definitivo", $res_def_div, true);
$smarty->assign("campo_eapb", $eapb, true);
//$smarty->assign("campo_prestador", $prestador, true);
$smarty->assign("campo_periodo", $selector_periodo, true);
$smarty->assign("campo_fechas_corte", $selector_fechas_corte, true);
$smarty->assign("nombre", $nombre, true);
$smarty->assign("menu", $menu, true);
$smarty->display('alimaudCANCER0247.html.tpl');


/*
INSERT INTO gios_menus_opciones_sistema(id_principal,id_padre,nombre_opcion,descripcion_ayuda,tiene_submenus,ruta_interfaz,prioridad_jerarquica) VALUES ('236','13','Alimentar Auditoria','',FALSE,'..|alimentar_auditoria_res_0247_CANCER|alimaudCANCER0247.php','50.02');

INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('236','5'); --admin sistema
*/


//INICIA RECEPCION DEL SUBMIT

$mensajes_error_bd="";

if(isset($_REQUEST['eapb'])==true 
	&& trim($_REQUEST['eapb'])!="" 
	&& trim($_REQUEST['eapb'])!="none" 
	&& isset($_REQUEST['periodo_indicado'])==true
	&& trim($_REQUEST['periodo_indicado'])!=""
)//fin if
{
	$codigo_entidad_reporta=trim($_REQUEST['eapb']);
	$periodo_indicado=trim($_REQUEST['periodo_indicado']);
	

	
	
	/*

	--ALTERS TABLAS REPORTE OBLIGATORIO
	alter table gioss_consulta_reporte_obligatorio_cancer0247_exitoso add column campo_cancer_de_numero_orden_211 character varying(320);
	alter table gioss_consulta_reporte_obligatorio_cancer0247_exitoso_duplicado add column campo_cancer_de_numero_orden_211 character varying(320);
	alter table gioss_consulta_reporte_obligatorio_cancer0247_rechazado add column campo_cancer_de_numero_orden_211 character varying(320);
	alter table gioss_consulta_reporte_obligatorio_cancer0247_solo_duplicados add column campo_cancer_de_numero_orden_211 character varying(320);
	
	--ALTERS TABLAS REGISTROS CARGADOS EXITO
	alter table gioss_tabla_registros_cargados_exito_r0247_cancer add column campo_cancer_de_numero_orden_211 character varying(320);
	alter table gioss_tabla_registros_no_cargados_rechazados_r0247_cancer add column campo_cancer_de_numero_orden_211 character varying(320);
	
	--ALTERS TABLAS CORRECCION
	alter table corregidos_con_duplicados_cancer0247 add column campo_cancer_de_numero_orden_211 character varying(320);
	alter table corregidos_sin_duplicados_cancer0247 add column campo_cancer_de_numero_orden_211 character varying(320);
	alter table corregidos_solo_duplicados_cancer0247 add column campo_cancer_de_numero_orden_211 character varying(320);

	
	--TABLA INDICE PERIODOS USUARIOS REPORTADOS CANCER (AUN NO HAY TABLA DEUSUARIOR REPORTADOS PARA ESTA NORMA)
	CREATE TABLE tabla_aud_us_rep_cancer_indice
	(
		periodo_reportado character varying(320),
		fecha_de_corte date,
		codigo_entidad_reporta character varying(320),
		numero_total_registros INT,
		PRIMARY KEY(periodo_reportado,fecha_de_corte,codigo_entidad_reporta)
	);

	 
	*/

	//CONSULTA MAXIMA FECHA DE CORTE EN REPORTE OBLIGATORIO
	$query_busca_ultima_fecha_reporte_obligatorio="SELECT max(fecha_corte_reporte) as maxima_fecha_corte from gioss_consulta_reporte_obligatorio_cancer0247_exitoso WHERE codigo_entidad_eapb_generadora='$codigo_entidad_reporta';";
	$resultados_busca_ultima_fecha_reporte_obligatorio=$coneccionBD->consultar2_no_crea_cierra($query_busca_ultima_fecha_reporte_obligatorio);
	$fecha_corte_ultimo_periodo="";
	if(count($resultados_busca_ultima_fecha_reporte_obligatorio)>0 && is_array($resultados_busca_ultima_fecha_reporte_obligatorio)==true)
	{
		$fecha_corte_ultimo_periodo=trim($resultados_busca_ultima_fecha_reporte_obligatorio[0]['maxima_fecha_corte']);
	}//fin if
	//FIN CONSULTA MAXIMA FECHA DE CORTE EN REPORTE OBLIGATORIO

	//CONSULTA PERIODO MAS RECIENTE DE USUARIOS REPORTADOS COHORTE
	$query_buscar_ultimo_periodo_en_tabla_usuarios_reportados_cohorte="SELECT max(periodo_reportado::numeric) as maximo_periodo from tabla_auditoria_usuarios_reportados_cancer WHERE codigo_entidad_reporta='$codigo_entidad_reporta' ;";
	$resultados_buscar_ultimo_periodo_en_tabla_usuarios_reportados_hf=$coneccionBD->consultar2_no_crea_cierra($query_buscar_ultimo_periodo_en_tabla_usuarios_reportados_cohorte);
	$periodo_anterior="";
	if(count($resultados_buscar_ultimo_periodo_en_tabla_usuarios_reportados_hf)>0 && is_array($resultados_buscar_ultimo_periodo_en_tabla_usuarios_reportados_hf)==true)
	{
		$periodo_anterior=trim($resultados_buscar_ultimo_periodo_en_tabla_usuarios_reportados_hf[0]['maximo_periodo']);
	}//fin if
	//FIN CONSULTA PERIODO MAS RECIENTE DE USUARIOS REPORTADOS COHORTE

	//VERIFICA SI YA FEU PROCESADO PARA EL PERIODO Y ENTIDAD DILIGENCIADOS
	$bool_fue_diligenciado=false;
	$query_verifica_si_se_proceso_para_el_periodo_indicado="SELECT * FROM tabla_aud_us_rep_hf_indice WHERE periodo_reportado='$periodo_indicado' AND codigo_entidad_reporta='$codigo_entidad_reporta' ; ";
	$resultados_verifica_si_se_proceso_para_el_periodo_indicado=$coneccionBD->consultar2_no_crea_cierra($query_verifica_si_se_proceso_para_el_periodo_indicado);

	if(count($resultados_verifica_si_se_proceso_para_el_periodo_indicado)>0 && is_array($resultados_verifica_si_se_proceso_para_el_periodo_indicado)==true)
	{
		$bool_fue_diligenciado=true;
	}//fin if ya fue diligenciado
	else
	{
		$query_delete_registros_proceso_incompleto="DELETE * FROM tabla_auditoria_usuarios_reportados_cancer WHERE periodo_reportado='$periodo_indicado' AND codigo_entidad_reporta='$codigo_entidad_reporta' ;";
		$error_bd_seq="";
		$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($query_delete_registros_proceso_incompleto, $error_bd_seq);
		if($error_bd_seq!="")
		{
			$mensajes_error_bd.="ERROR al eliminar registros de un proceso anterior incompleto en la tabla requisito para la auditoria: ".$error_bd_seq."<br>";
		}//fin if
	}//fin else
	//FIN VERIFICA SI YA FEU PROCESADO PARA EL PERIODO Y ENTIDAD DILIGENCIADOS

	if($fecha_corte_ultimo_periodo!="" && $periodo_anterior!="" && $bool_fue_diligenciado==false)
	{

		//CREACION DE LA VISTA DEL ARCCHIVO DEFINITIVO EN EL REPORTE OBLIGATORIO Y CONTEO REGISTROS DE LA MISMA
		$nombre_view_ultimo_reporte_obligatorio_generado="valim0247aud_".$nick_user.str_replace("-", "", $fecha_corte_ultimo_periodo);
		$query_view_ultimo_reporte_obligatorio="CREATE OR REPLACE VIEW $nombre_view_ultimo_reporte_obligatorio_generado AS 
		( 
			SELECT * FROM gioss_consulta_reporte_obligatorio_cancer0247_exitoso WHERE fecha_corte_reporte='$fecha_corte_ultimo_periodo' ORDER BY numero_registro ASC
		);
		";
		$error_bd_seq="";
		$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($query_view_ultimo_reporte_obligatorio, $error_bd_seq);
		echo "Query crea vista: ".$query_view_ultimo_reporte_obligatorio."<br>";
		if($error_bd_seq!="")
		{
			$mensajes_error_bd.="ERROR al crear la vista ".$error_bd_seq."<br>";
		}//fin if
		else
		{
			echo "Se creo vista <br>";
		}

		$numero_registros=0;
		$sql_query_numero_registros="";
		$sql_query_numero_registros.="SELECT count(*) as contador FROM $nombre_view_ultimo_reporte_obligatorio_generado ; ";
		$resultado_query_numero_registros=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($sql_query_numero_registros,$error_bd_seq);
		if($error_bd_seq!="")
		{
		    $mensajes_error_bd.="ERROR AL CONSULTAR CONTEO de vista_consulta: ".$error_bd_seq."<br>";
		}//fin if

		if(count($resultado_query_numero_registros)>0 && is_array($resultado_query_numero_registros)==true )
		{
			$numero_registros=intval($resultado_query_numero_registros[0]["contador"]);
		}//fin if
		//FIN CREACION DE LA VISTA DEL ARCCHIVO DEFINITIVO EN EL REPORTE OBLIGATORIO Y CONTEO REGISTROS DE LA MISMA

		$numero_registros_bloque=10000;
		$contador_offset=0;

		$inicializa_contador_campos_en=1;//esta variable almacen el valor en que se deben inicializar lso contadores de campos para bd
		$numero_total_campos_norma=212;
		$contador_registros_insertados=0;
		if(
			$numero_registros>0 
			&& intval($periodo_indicado)>intval($periodo_anterior)
			&& (intval($periodo_indicado)-intval($periodo_anterior))==1
		 )//fin if
		{
			//Variables para insert
			$periodo_reportado_para_insert="";
			$fecha_corte_para_insert="";
			$tipo_cohorte_auditoria_para_insert="";
			$codigo_entidad_reporte_para_insert="";
			//Fin variables para insert

			while($contador_offset<$numero_registros)
			{
				$query_consultar_registros_ultimo_reporte_obligatorio="SELECT * FROM $nombre_view_ultimo_reporte_obligatorio_generado ORDER BY numero_registro ASC LIMIT $numero_registros_bloque  OFFSET $contador_offset ";
				$resultado_query_consultar_registros_ultimo_reporte_obligatorio=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($query_consultar_registros_ultimo_reporte_obligatorio,$error_bd_seq);
				if($error_bd_seq!="")
				{
				    $mensajes_error_bd.="ERROR AL CONSULTAR de vista_consulta: ".$error_bd_seq."<br>";
				}//fin if

				//foreach por cada bloque de registros
				foreach ($resultado_query_consultar_registros_ultimo_reporte_obligatorio as $key => $registro) 
				{
					$valor_campo_23_cancer=trim($registro['campo_cancer_de_numero_orden_23']);//cambia 
					$valor_campo_24_cancer=trim($registro['campo_cancer_de_numero_orden_24']);//cambia
					$tipo_identificacion_campo_5_cancer=trim($registro['campo_cancer_de_numero_orden_5']);//ya que cancer en bd cuanta desde uno
					$numero_identificacion_campo_6_cancer=trim($registro['campo_cancer_de_numero_orden_6']);//ya que cancer en bd cuanta desde uno

					$array_campos_registro=array();
					$contador_campos=0;
					while(isset($registro['campo_cancer_de_numero_orden_'.$contador_campos])==true)
					{
						$array_campos_registro[$contador_campos]=trim($registro['campo_cancer_de_numero_orden_'.$contador_campos]);
						$contador_campos++;
					}//fin while

					//aqui va query busqueda si fue reportado previamente el usuario en tabla_auditoria_usuarios_reportados_cancer
					$query_buscar_paciente="SELECT * FROM tabla_auditoria_usuarios_reportados_cancer WHERE campo_cancer_5='$tipo_identificacion_campo_5_cancer' AND campo_cancer_6='$numero_identificacion_campo_6_cancer' AND periodo_reportado='$periodo_anterior' ;";
					$resultados_buscar_paciente=$coneccionBD->consultar2_no_crea_cierra($query_buscar_paciente);


					

					//EVALUA CRITERIOS PARA CUANDO EXISTE Y CUANDO NO EXISTE EN EL REPORTE DE USUARIOS COHORTE
					/*
					if(count($resultados_buscar_paciente)>0 && is_array($resultados_buscar_paciente)==true)
					{
						//ANTERIORES
						if($valor_campo_23_cancer=="0" || $valor_campo_23_cancer=="1")
						{
							//COHORETE HEMOFILIA
							$tipo_cohorte_auditoria_para_insert="2";
						}//fin if
						else if(
							$valor_campo_23_cancer=="2"
							|| $valor_campo_23_cancer=="3"
							|| $valor_campo_23_cancer=="4"
							|| $valor_campo_23_cancer=="5"
							|| $valor_campo_23_cancer=="6"
							|| $valor_campo_23_cancer=="7"
							|| $valor_campo_23_cancer=="8"
							|| $valor_campo_23_cancer=="9"
							|| $valor_campo_23_cancer=="10"
							|| $valor_campo_23_cancer=="11"
						)//fin if
						{
							//OTRAS COAGULOPATIAS
							$tipo_cohorte_auditoria_para_insert="4";
						}//fin else if

						//CRITERIOS CAMBIO DE DIAGNOSTICO
						$valor_campo_23_usuarios_reportados_cancer=trim($resultados_buscar_paciente['campo_cancer_22']);
						$valor_campo_24_usuarios_reportados_cancer=trim($resultados_buscar_paciente['campo_cancer_23']);
						if($valor_campo_23_usuarios_reportados_cancer!=$valor_campo_23_cancer)
						{
							//I. definicion tecnica cambio de diagnostico
							$tipo_cohorte_auditoria_para_insert="5";
						}//fin if
						else if(
							$valor_campo_23_usuarios_reportados_cancer==$valor_campo_23_cancer 
							&& $valor_campo_24_cancer!=$valor_campo_24_usuarios_reportados_cancer
						)//fin condicion
						{
							if($valor_campo_23_cancer=="0" || $valor_campo_23_cancer=="1")
							{
								//II. definicion tecnica cambio de severidad
								$tipo_cohorte_auditoria_para_insert="6";
							}//fin if

						}//ambos son iguales en el c 23
					}//fin if existe
					else
					{
						//NUEVOS
						//No existen en la tabla de usuarios repportados hemofilia en el periodo anterior
						
						if($valor_campo_23_cancer=="0" || $valor_campo_23_cancer=="1")
						{
							//COHORETE HEMOFILIA
							$tipo_cohorte_auditoria_para_insert="1";
						}//fin if
						else if(
							$valor_campo_23_cancer=="2"
							|| $valor_campo_23_cancer=="3"
							|| $valor_campo_23_cancer=="4"
							|| $valor_campo_23_cancer=="5"
							|| $valor_campo_23_cancer=="6"
							|| $valor_campo_23_cancer=="7"
							|| $valor_campo_23_cancer=="8"
							|| $valor_campo_23_cancer=="9"
							|| $valor_campo_23_cancer=="10"
							|| $valor_campo_23_cancer=="11"
						)//fin if
						{
							//OTRAS COAGULOPATIAS
							$tipo_cohorte_auditoria_para_insert="3";
						}//fin else if
					}//fin else no existe en la tabla usuario hemofilia reportados
					*/
					//FIN EVALUA CRITERIOS PARA CUANDO EXISTE Y CUANDO NO EXISTE EN EL REPORTE DE USUARIOS COHORTE

					$periodo_reportado_para_insert=$periodo_indicado;
					$fecha_corte_para_insert=$fecha_corte_ultimo_periodo;
					$codigo_entidad_reporte_para_insert=$codigo_entidad_reporta;

					//PARTE INSERT DESPUES DE CRITERIOS
					if(
						$periodo_reportado_para_insert!=""
						&& $fecha_corte_para_insert!=""
						&& $tipo_cohorte_auditoria_para_insert!="" 
						&& $codigo_entidad_reporte_para_insert!=""
					)
					{
						$query_preparacion_insert="";
						$query_preparacion_insert.="INSERT INTO tabla_auditoria_usuarios_reportados_cancer
							(
							";
						$cont_campos_ins_hf=$inicializa_contador_campos_en;
						while ($cont_campos_ins_hf<$numero_total_campos_norma) 
						{
							$query_preparacion_insert.="campo_cancer_".$cont_campos_ins_hf.",";
							$cont_campos_ins_hf++;
						}//fin while
						$query_preparacion_insert.="
						periodo_reportado,
						fecha_de_corte,
						tipo_cohorte_afiliado,
						codigo_entidad_reporta
							)
							VALUES
							(
							";
						$cont_campos_ins_hf=$inicializa_contador_campos_en;
						while ($cont_campos_ins_hf<$numero_total_campos_norma) 
						{
							$query_preparacion_insert.="'".trim($registro['campo_cancer_de_numero_orden_'.$cont_campos_ins_hf])."',";
							$cont_campos_ins_hf++;
						}//fin while
						$query_preparacion_insert.="
						'".$periodo_reportado_para_insert."',
						'".$fecha_corte_para_insert."',
						'".$tipo_cohorte_auditoria_para_insert."',
						'".$codigo_entidad_reporte_para_insert."'
							)
							;
						";

						$error_bd_seq="";
						$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($query_preparacion_insert, $error_bd_seq);
						if($error_bd_seq!="")
						{
							$mensajes_error_bd.="ERROR al insertar en la tabla requisito para la auditoria: ".$error_bd_seq."<br>";
						}//fin if
						else
						{
							$contador_registros_insertados++;
						}//fin else no hubo error al insertar
						
					}//fin if
					//FIN PARTE INSERT DESPUES DE CRITERIOS

				}//fin foreach
				//fin foreach por cada bloque de registros

				$contador_offset+=$numero_registros_bloque;
			}//fin while

			//INSERT EN TABLA INDICE AL TERMINAR
			if($contador_registros_insertados>0 
				&& $contador_registros_insertados==$numero_registros
				&& $periodo_reportado_para_insert!=""
				&& $fecha_corte_para_insert!=""
				&& $codigo_entidad_reporte_para_insert!=""
			)//fin if
			{
				$query_insert_indice="";
				$query_insert_indice.="INSERT INTO tabla_aud_us_rep_hf_indice
				(
				periodo_reportado,
				fecha_de_corte,
				codigo_entidad_reporta,
				numero_total_registros
				)
				VALUES
				(
				'".$periodo_reportado_para_insert."',
				'".$fecha_corte_para_insert."',
				'".$codigo_entidad_reporte_para_insert."',
				'".$contador_registros_insertados."'
				)
				;";
				$error_bd_seq="";
				$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($query_insert_indice, $error_bd_seq);
				if($error_bd_seq!="")
				{
					$mensajes_error_bd.="ERROR al insertar en la tabla indice al finalizar el proceso de alimentar auditoria: ".$error_bd_seq."<br>";
				}//fin if
			}//solo si la cantidad de registros insertados es mayor de cero y despues del proceso de insercion 
			else if($contador_registros_insertados!=$numero_registros)
			{
				$mensajes_error_bd.="ERROR LA CANTIDAD DE REGISTROS INSERTADOS NO ES IGUAL A LA CANTIDAD DE REGISTROS CONSULTADOS DEL REPORTE OBLIGATORIO.<br>";
			}

			//FIN INSERT EN TABLA INDICE AL TERMINAR
		}//fin if cantidad numero registros es mayor de cero
		else
		{
			if($numero_registros==0)
			{
				$mensajes_error_bd.="No se encontraron registros<br>";
			}//fin if
			else if(intval($periodo_indicado)<=intval($periodo_anterior))
			{
				$mensajes_error_bd.="El periodo indicado es menor que el ultimo perido encontrado<br>";
			}//fin else
			else if((intval($periodo_indicado)-intval($periodo_anterior))!=1)
			{
				$mensajes_error_bd.="El periodo indicado comparado con el ultimo periodo almacenado, sobrepasa un anio de diferencia.";
			}//fin if
		}//fin else
	}//fin if mientras el ultimo periodo de fecha de corte y periodo de reporte no sean cadenas vacias
	else
	{
		if($fecha_corte_ultimo_periodo=="")
		{
			$mensajes_error_bd.="No se encontraro ni un registro generado en el reporte obligatorio.<br>";
		}//fin if

		if($periodo_anterior!="")
		{
			$mensajes_error_bd.="No se encontraro ni un registro en usuarios reportados cohorte.<br>";
		}//fin if

		if($bool_fue_diligenciado==true)
		{
			$mensajes_error_bd.="El periodo indicado ( $periodo_indicado ) para la entidad ( $codigo_entidad_reporta ) ya fue alimentado en la tabla de auditoria correspondiente.<br>";
		}//fin if
	}//fin else

	

}//fin if
else
{
	if(isset($_REQUEST['eapb'])==true && (trim($_REQUEST['eapb'])=="" || trim($_REQUEST['eapb'])=="none")  )
	{
		$mensajes_error_bd.="No se selecciono una entidad valida<br>";
	}//fin if

	if(isset($_REQUEST['periodo_indicado'])==true && trim($_REQUEST['periodo_indicado'])==""  )
	{
		$mensajes_error_bd.="No se indico un periodo valido<br>";
	}//fin if
}//fin else

if($mensajes_error_bd!="")
{
	if(connection_aborted()==false)
	{
		echo "<script>document.getElementById('div_mensaje_error').style.display='inline';</script>";
		echo "<script>document.getElementById('parrafo_mensaje_error').innerHTML='$mensajes_error_bd';</script>";
		ob_flush();
		flush();
	}//fin if
}//fin if

$coneccionBD->cerrar_conexion();

?>