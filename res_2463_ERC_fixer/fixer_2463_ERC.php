<?php
ignore_user_abort(true);
set_time_limit(936000);
ini_set('max_execution_time', 936000);
ini_set('memory_limit', '900M');

require '../librerias_externas/Smarty-3.1.17/libs/Smarty.class.php';
include_once ('../utiles/clase_coneccion_bd.php');
include_once ('../utiles/funciones_crear_menu.php');

include ("../librerias_externas/PHPMailer/class.phpmailer.php");
include ("../librerias_externas/PHPMailer/class.smtp.php");
require_once ("../librerias_externas/PHPExcel/PHPExcel.php");
require_once ("../librerias_externas/PHPExcel/PHPExcel/Writer/Excel2007.php");

//require_once 'reparador_2463_ERC.php';
//require_once '../res_2463_ERC/reparador_2463_ERC_v2017.php';
require_once '../res_2463_ERC/reparador_2463_ERC_v2018.php';

require_once 'reparacion_campos_duplicados.php';

require_once '../utiles/queries_utiles_bd.php';

require_once '../utiles/crear_zip.php';

require_once '../utiles/configuracion_global_email.php';

require_once '../utiles/configuracion_tipo_entidad.php';


$smarty = new Smarty;
$coneccionBD = new conexion();
$coneccionBD->crearConexion();
$utilidades = new Utilidades();

session_start();

if (!isset($_SESSION['usuario']) && !isset($_SESSION['tipo_perfil']))
{
	
	header ("Location: ../index.php");
}

$menu= $_SESSION['menu_logueo_html'];
$nombre= $_SESSION['nombre_completo'];

$perfil_usuario_actual= $_SESSION['tipo_perfil'];
$entidad_salud_usuario_actual= $_SESSION['entidad_asociada_al_nick'];

$tipo_id=$_SESSION['tipo_id'];
$identificacion=$_SESSION['identificacion'];

$nick_user=$_SESSION['usuario'];

$correo_electronico=$_SESSION['correo'];

session_write_close();

$mensaje="";
$mostrarResultado="none";

$mensajes_error_bd="";


function contar_lineas_archivo($ruta_file)
{
    $linecount = 0;
    $handle = fopen($ruta_file, "r");
    while(!feof($handle)){
      $line = fgets($handle);
      $linecount++;
    }
    
    fclose($handle);
    
    return $linecount;
}

function alphanumericAndSpace( $string )
{
	$string = str_replace("á","a",$string);
	$string = str_replace("é","e",$string);
	$string = str_replace("í","i",$string);
	$string = str_replace("ó","o",$string);
	$string = str_replace("ú","u",$string);
	$string = str_replace("ñ","n",$string);
	$string = str_replace("Á","A",$string);
	$string = str_replace("É","E",$string);
	$string = str_replace("Í","I",$string);
	$string = str_replace("Ó","O",$string);
	$string = str_replace("Ú","U",$string);
	$string = str_replace("Ñ","N",$string);
    return preg_replace('/[^a-zA-Z0-9:.\s\-,@\_\+]/', '', $string);
}

function alphanumericAndSpace2( $string )
{
	$string = str_replace("á","a",$string);
	$string = str_replace("é","e",$string);
	$string = str_replace("í","i",$string);
	$string = str_replace("ó","o",$string);
	$string = str_replace("ú","u",$string);
	$string = str_replace("ñ","n",$string);
	$string = str_replace("Á","A",$string);
	$string = str_replace("É","E",$string);
	$string = str_replace("Í","I",$string);
	$string = str_replace("Ó","O",$string);
	$string = str_replace("Ú","U",$string);
	$string = str_replace("Ñ","N",$string);
    return preg_replace('/[^a-zA-Z0-9\s\_,@<>\+\.]/', '', $string);
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
	$mensaje_procesado = str_replace("'"," ",$mensaje_procesado);
	$mensaje_procesado = str_replace("\n"," ",$mensaje_procesado);
	$mensaje_procesado = alphanumericAndSpace($mensaje_procesado);
	
	return $mensaje_procesado;
}


function procesar_mensaje2($mensaje)
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
	$mensaje_procesado = str_replace("'"," ",$mensaje_procesado);
	$mensaje_procesado = str_replace("\n"," ",$mensaje_procesado);
	$mensaje_procesado = alphanumericAndSpace2($mensaje_procesado);
	
	return $mensaje_procesado;
}

//contiene backslash
function alphanumericAndSpace3( $string )
{
	$string = str_replace("á","a",$string);
	$string = str_replace("é","e",$string);
	$string = str_replace("í","i",$string);
	$string = str_replace("ó","o",$string);
	$string = str_replace("ú","u",$string);
	$string = str_replace("ñ","n",$string);
	$string = str_replace("Á","A",$string);
	$string = str_replace("É","E",$string);
	$string = str_replace("Í","I",$string);
	$string = str_replace("Ó","O",$string);
	$string = str_replace("Ú","U",$string);
	$string = str_replace("Ñ","N",$string);
    return preg_replace('/[^a-zA-Z0-9:.\s\-\/,@\_\|\s+\+]/', '', $string);
}

function procesar_mensaje3($mensaje)
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
	$mensaje_procesado = str_replace("'"," ",$mensaje_procesado);
	$mensaje_procesado = str_replace("\n"," ",$mensaje_procesado);
	$mensaje_procesado = alphanumericAndSpace3($mensaje_procesado);
	
	return $mensaje_procesado;
}

function formato_fecha_valida_quick($fecha_a_verificar,$separador="-")
{
	$es_fecha_valida=true;

	$fecha_a_verificar_array= explode($separador,$fecha_a_verificar);

	if(count($fecha_a_verificar_array)!=3)
	{			
		$es_fecha_valida=false;
	}//fin if
	else if( !ctype_digit($fecha_a_verificar_array[0]) 
		|| !ctype_digit($fecha_a_verificar_array[1]) 
		|| !ctype_digit($fecha_a_verificar_array[2])  
		)
	{			
		$es_fecha_valida=false;
	}//fin if
	else if( 
		!checkdate($fecha_a_verificar_array[1],$fecha_a_verificar_array[2],$fecha_a_verificar_array[0])
		)
	{			
		$es_fecha_valida=false;
	}//fin if

	return $es_fecha_valida;
}//fin function

//funciones que estaban en el reparador y se migraron

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

function convert_to_standard_notation($floatAsString)
{
    $norm = strval(floatval($floatAsString));

    if (($e = strrchr($norm, 'E')) === false) {
        return $norm;
    }

    return number_format($norm, -intval(substr($e, 1)));
}

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
    $date_de_corte="";
    $date_de_corte=date($fecha_corte[0]."-".$fecha_corte[1]."-".$fecha_corte[2]);
    
    //echo "<script>alert($date_de_corte $campo_fecha);</script>";
    
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
				else if($campo_especial==-3)
				{
					$fecha_corregida="1788-01-01";
				}
			   }
			   else
			   {
			    $fecha_corregida=$date_de_corte;
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
				else if($campo_especial==-3)
				{
					$fecha_corregida="1788-01-01";
				}
			}
			else
			{
				$fecha_corregida=$date_de_corte;
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
				else if($campo_especial==-3)
				{
					$fecha_corregida="1788-01-01";
				}
			}
			else
			{
				$fecha_corregida=$date_de_corte;
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
			else if($campo_especial==-3)
			{
				$fecha_corregida="1788-01-01";
			}
		    }
		    else
		    {
			$fecha_corregida=$date_de_corte;
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
		else if($campo_especial==-3)
		{
			$fecha_corregida="1788-01-01";
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
	    else if($campo_especial==-3)
	    {
		    $fecha_corregida="1788-01-01";
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
    
    //echo 'fecha corregida ',$fecha_corregida,'<br>';
    
    if($es_fecha_nacimiento==false && $campo_especial!=0)
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
    
    if( $campo_debug==801 && trim($campo_fecha)!="99")
    {
	//echo "<script>alert('entro 801 funcion  $campo_fecha pos $fecha_corregida');</script>";
    }
    
    
    return $fecha_corregida;
}//fin funcion

//consultar el tipo de entidad
$sql_query_tipo_entidad_asociada="SELECT * FROM gioss_entidades_sector_salud WHERE codigo_entidad='$entidad_salud_usuario_actual'; ";
$resultado_query_tipo_entidad=$coneccionBD->consultar2_no_crea_cierra($sql_query_tipo_entidad_asociada);
$tipo_entidad=$resultado_query_tipo_entidad[0]["cod_tipo_entidad"];
//fin consultar el tipo de entidad

//SELECTOR PRESTADORES ASOCIADOS USUARIO
$prestador="";
$prestador.="<div id='div_prestador'>";
$prestador.="<select id='prestador' name='prestador' class='campo_azul' onchange='validar_antes_seleccionar_archivos();' >";
$prestador.="<option value='none'>Seleccione un prestador de salud</option>";
if((intval($perfil_usuario_actual)==5 || intval($perfil_usuario_actual)==4 || intval($perfil_usuario_actual)==3)
   && (intval($tipo_entidad)!=6 && intval($tipo_entidad)!=7 && intval($tipo_entidad)!=8 && intval($tipo_entidad)!=10) )
{
	$sql_consulta_prestadores_asociados_eapb="SELECT ea.codigo_entidad,ea.nombre_de_la_entidad FROM ";
	$sql_consulta_prestadores_asociados_eapb.=" gioss_relacion_entre_entidades_salud re INNER JOIN gioss_entidades_sector_salud ea ON (re.entidad1 = ea.codigo_entidad) ";
	$sql_consulta_prestadores_asociados_eapb.=" WHERE re.entidad2='".$entidad_salud_usuario_actual."' ";
	$resultado_query_prestadores_asociados_eapb=$coneccionBD->consultar2_no_crea_cierra($sql_consulta_prestadores_asociados_eapb);

	if(count($resultado_query_prestadores_asociados_eapb)>0)
	{
		foreach($resultado_query_prestadores_asociados_eapb as $prestador_asociado_eapb)
		{
			$prestador.="<option value='".$prestador_asociado_eapb['codigo_entidad']."' selected>".$prestador_asociado_eapb['codigo_entidad']." ".$prestador_asociado_eapb['nombre_de_la_entidad']."</option>";
		}
	}
}//si el tipo entidad es diferente de 6,7,8,10
else if((intval($perfil_usuario_actual)==1 || intval($perfil_usuario_actual)==2 || intval($perfil_usuario_actual)==5) && (intval($tipo_entidad)==6 || intval($tipo_entidad)==7 || intval($tipo_entidad)==8 || intval($tipo_entidad)==10) )
{
	$sql_query_tipo_entidad_asociada="SELECT * FROM gioss_entidades_sector_salud WHERE codigo_entidad='$entidad_salud_usuario_actual'; ";
	$resultado_query_tipo_entidad=$coneccionBD->consultar2_no_crea_cierra($sql_query_tipo_entidad_asociada);

	if(count($resultado_query_tipo_entidad)>0)
	{
		foreach($resultado_query_tipo_entidad as $eapb_entidad)
		{
			$prestador.="<option value='".$eapb_entidad['codigo_entidad']."' selected>".$eapb_entidad['codigo_entidad']." ".$eapb_entidad['nombre_de_la_entidad']."</option>";
		}
	}
}//fin else if en caso de que el perfil sea 1 o 2 y el tipo de la entidad sea igual a 6,7,8,10
$prestador.="</select>";
$prestador.="</div>";
//FIN PRESTADOR

//SELECTOR EAPB-ASOCIADA_PRESTADOR_ASOCIADO_USUARIO
$eapb="";
$eapb.="<div id='div_eapb'>";
$eapb.="<select id='eapb' name='eapb' class='campo_azul' onchange='validar_antes_seleccionar_archivos();'>";
$eapb.="<option value='none'>Seleccione un EAPB</option>";


if((intval($perfil_usuario_actual)==5 || intval($perfil_usuario_actual)==1 || intval($perfil_usuario_actual)==2) && (intval($tipo_entidad)==6 || intval($tipo_entidad)==7 || intval($tipo_entidad)==8 || intval($tipo_entidad)==10) )
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
else if((intval($perfil_usuario_actual)==3 || intval($perfil_usuario_actual)==4 || intval($perfil_usuario_actual)==5) && (intval($tipo_entidad)!=6 && intval($tipo_entidad)!=7 && intval($tipo_entidad)!=8 && intval($tipo_entidad)!=10) )
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
//FIN EAPB

//TIPO ENTIDAD

$proveniente_de_prestador_o_eapb="";
if($TIPO_ENTIDAD_DE_LA_VERSION=="GENERAL"
   || $TIPO_ENTIDAD_DE_LA_VERSION=="EPS"
   || $TIPO_ENTIDAD_DE_LA_VERSION=="SECRETARIA"
   )
{
$proveniente_de_prestador_o_eapb.="<tr><td style='text-align:left;'><h5 id='sub_titulo_tipo_entidad' style=\"color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;\">Tipo Entidad que realiza el cargue</h5></td></tr>
					<tr>
					    <td style='text-align:left;'>
						<select id='tipo_archivo_norma' name='tipo_archivo_norma' class='campo_azul' onchange='mostrar_selectores_geograficos();validar_antes_seleccionar_archivos();' style='width:230px;'>							    
						    <option value='individual_ips'>Prestador Individual</option>
						    <!--<option value='ent_territoriales'>Agrupado Entidad Territorial</option>-->
						    <option value='agrupado_eapb'>Agrupado EAPB</option>
						</select>
					    </td>
					</tr>
				";
}
else if($TIPO_ENTIDAD_DE_LA_VERSION=="IPS")
{
	$proveniente_de_prestador_o_eapb.="
	<!--
	<tr>
	<td style='text-align:left;'><b><br>El tipo de archivo a validar <br> proveendra de una IPS o prestador.<br>&nbsp;<b/>
	</td>
	</tr>
	-->
	<tr>
	<td>
	<input type='hidden' id='tipo_archivo_norma' name='tipo_archivo_norma' value='individual_ips'/>			
	</td>
	</tr>
	";
}

//FIN TIPO ENTIDAD

//SELECTOR PERIODO
$query_periodos_rips="SELECT * FROM gioss_periodo_reporte_2463_erc ORDER BY codigo_periodo;";
$resultado_query_periodos=$coneccionBD->consultar2_no_crea_cierra($query_periodos_rips);

$selector_periodo="";

$selector_periodo.="<select id='periodo' name='periodo' class='campo_azul' onchange='validar_antes_seleccionar_archivos();' style='width:228px;' >";
$selector_periodo.="<option value='none'>Seleccione un periodo</option>";
foreach($resultado_query_periodos as $key=>$periodo)
{
	$cod_periodo=$periodo["codigo_periodo"];
	$nombre_periodo=$periodo["descripcion_periodo"];
	$fecha_periodo=$periodo["valor_fecha_permitida"];
	$fecha_periodo_mes_dia=explode("-",$periodo["valor_fecha_permitida"])[1]."-".explode("-",$periodo["valor_fecha_permitida"])[2];
	$selector_periodo.="<option value='".$cod_periodo."::".$fecha_periodo_mes_dia."'>Periodo $cod_periodo ($nombre_periodo , $fecha_periodo)</option>";
}
$selector_periodo.="</select>";
//FIN PERIODO


$smarty->assign("proveniente_de_prestador_o_eapb", $proveniente_de_prestador_o_eapb, true);

$smarty->assign("campo_periodo", $selector_periodo, true);
$smarty->assign("campo_eapb", $eapb, true);
$smarty->assign("campo_prestador", $prestador, true);
$smarty->assign("mensaje_proceso", $mensaje, true);
$smarty->assign("mostrarResultado", $mostrarResultado, true);
$smarty->assign("nombre", $nombre, true);
$smarty->assign("menu", $menu, true);
$smarty->display('fixer_2463_ERC.html.tpl');

//PARTE PARA LA VALIDACION DEL ARCHIVO

//cargar errores desde bd y meterlos en arrays con keys que corresponden a la llave primaria de estos
$array_tipo_validacion=array();
$array_grupo_validacion=array();
$array_detalle_validacion=array();

$query1_tipo_validacion="SELECT * FROM gioss_tipo_inconsistencias;";
$resultado_query1_tipo_validacion=$coneccionBD->consultar2_no_crea_cierra($query1_tipo_validacion);
foreach($resultado_query1_tipo_validacion as $tipo_validacion)
{
	$array_tipo_validacion[$tipo_validacion["tipo_validacion"]]=$tipo_validacion["descripcion_tipo_validacion"];
}
$query2_grupo_validacion="SELECT * FROM gioss_grupo_inconsistencias;";
$resultado_query2_grupo_validacion=$coneccionBD->consultar2_no_crea_cierra($query2_grupo_validacion);
foreach($resultado_query2_grupo_validacion as $grupo_validacion)
{
	$array_grupo_validacion[$grupo_validacion["grupo_validacion"]]=$grupo_validacion["descripcion_grupo_validacion"];
}
$query3_detalle_validacion="SELECT * FROM gioss_detalle_inconsistencia_2463_erc;";
$resultado_query3_detalle_validacion=$coneccionBD->consultar2_no_crea_cierra($query3_detalle_validacion);
if(count($resultado_query3_detalle_validacion)>0)
{
	foreach($resultado_query3_detalle_validacion as $detalle_validacion)
	{
		$array_detalle_validacion[$detalle_validacion["codigo_detalle_inconsistencia"]]=$detalle_validacion["descripcion_detalle_inconsistencia"];
	}	
}//fin if hay detalles inconsistencias
//fin carga detalles inconsistencias desde bd

date_default_timezone_set ("America/Bogota");
$fecha_actual = date('Y-m-d');
$tiempo_actual = date('H:i:s');
$tiempo_actual_string=str_replace(":","-",$tiempo_actual);

$fecha_y_hora_para_view=str_replace(":","",$tiempo_actual).str_replace("-","",$fecha_actual);
$fecha_y_hora_para_view=substr($fecha_y_hora_para_view,0,4);

$rutaTemporal = '../TEMPORALES/';
$error_mensaje="";

$ruta_archivo_inconsistencias_ERC="";
$se_genero_archivo_de_inconsistencias=false;
$verificacion_es_diferente_prestador_en_ct=false;
$verificacion_fecha_diferente_en_ct=false;
$verificacion_numero_remision=false;

$verificacion_ya_se_valido_con_exito=false;

$verificacion_no_se_ha_validado_con_anterioridad=false;

$mensaje_advertencia_tiempo="";
$mensaje_advertencia_tiempo .="Estimado usuario, se ha iniciado el proceso de validaci&oacuten del archivo,<br> lo que puede tomar varios minutos, dependiendo del volumen de registros.<br>";
$mensaje_advertencia_tiempo .="Una vez validado, se genera el Logs de errores, el cual se enviar&aacute a su Correo electr&oacutenico o puede descargarlo directamente del aplicat&iacutevo.<br>";
$mensaje_advertencia_tiempo .="Si la validaci&oacuten es exitosa, los datos se cargar&aacuten en la base de datos y se dar&aacute por aceptada la informaci&oacuten reportada<br>";

if(isset($_POST["accion"]) && $_POST["accion"]=="validar" && isset($_FILES["2463_ERC_file"]) && $_FILES["2463_ERC_file"]["error"]==0)
{
	if(connection_aborted()==false)
	{
		$html_advertencia="";
		$html_advertencia.= "<script>";
		$html_advertencia.= "document.getElementById('advertencia').innerHTML='$mensaje_advertencia_tiempo';";
		$html_advertencia.= "</script>";
		echo $html_advertencia;
		ob_flush();
		flush();
	}
	
	$tipo_entidad_que_efectua_el_cargue=$_POST["tipo_archivo_norma"];
	$cod_prestador="";
	if($tipo_entidad_que_efectua_el_cargue=="agrupado_eapb")
	{
		$cod_prestador="AGRUP_EAPB";
	}
	else if($tipo_entidad_que_efectua_el_cargue=="individual_ips")
	{		
		$cod_prestador=$_POST["prestador"];
	}
	
	$nombre_archivo_registrado=explode(".",$_FILES["2463_ERC_file"]["name"])[0];	
	$numero_de_remision=$_POST["numero_de_remision"];
	$archivo_erc=$_FILES["2463_ERC_file"];
	//$cod_prestador=$_POST["prestador"];
	$cod_eapb=$_POST["eapb"];	
	$codigo_periodo=explode("::",$_POST["periodo"])[0];
	$fecha_de_corte=$_POST["year_de_corte"]."-".explode("::",$_POST["periodo"])[1];
	
	//parte verifica si es mayor de 06-30 del mismo year
	$year_corte_inferior=trim($_POST["year_de_corte"]);
	$year_corte_para_buscar=trim($_POST["year_de_corte"]);
	$mitad_year_ver=trim($_POST["year_de_corte"])."-06-30";
	$diferencia_dias_con_mitad_year=diferencia_dias_entre_fechas($fecha_de_corte,$mitad_year_ver);
	if($diferencia_dias_con_mitad_year<0)
	{
		$year_corte_para_buscar="".(intval(trim($_POST["year_de_corte"]))+1);
	}//fin if
	else
	{
		$year_corte_inferior="".(intval(trim($_POST["year_de_corte"]))-1);
	}
	//fin parte verifica si es mayor de 06-30 del mismo year

	
	//PARTE FECHA INFERIOR Y NUEVA FECHA DE CORTE
	$fecha_corte_anterior_registrada_nombre=$fecha_de_corte;
	$fecha_inferior_pv="";
	$query_periodo_variado="SELECT * FROM gioss_periodo_variados_2463_erc WHERE year_corte_periodo='".$year_corte_para_buscar."' ;";
	$resultado_query_periodo_variado=$coneccionBD->consultar2_no_crea_cierra($query_periodo_variado);
	if(count($resultado_query_periodo_variado)>0 && is_array($resultado_query_periodo_variado))
	{		
		$fecha_inferior_pv=$resultado_query_periodo_variado[0]["fecha_limite_inferior"];
		$fecha_de_corte=$resultado_query_periodo_variado[0]["fecha_limite_superior"];
	}//fin if
	else
	{
		$fecha_inferior_pv=$year_corte_inferior."-07-01";
		$fecha_de_corte=$year_corte_para_buscar."-06-30";
	}//fin else
	//FIN PARTE FECHA INFERIOR Y NUEVA FECHA DE CORTE

	//SELECTOR VERSION	
	$nombre_base_version="reparador_2463_ERC_v";
	$array_fecha_corte=explode("-", $fecha_de_corte);
	$year_corte_para_version_validacion=trim($array_fecha_corte[0]);
	$directorio_validacion_per_year='../res_2463_ERC/';
	$ruta_validacion_version=$directorio_validacion_per_year.$nombre_base_version.$year_corte_para_version_validacion.'.php';
	if(file_exists($ruta_validacion_version)==true)
	{
		require_once $ruta_validacion_version;
	}//fin if
	else
	{
		$version_minima=0;
		$version_maxima=0;
		$array_versiones_scripts=array();
		if ($filesVersiones = opendir($directorio_validacion_per_year)) 
		{
			while (false !== ($script_actual = readdir($filesVersiones))) 
			{
				$script_actual_temp=str_replace(".php", "", $script_actual);
				$script_actual_temp=str_replace($nombre_base_version, "", $script_actual_temp);
				$array_versiones_scripts[]=intval($script_actual_temp);



			}//fin while
			$selecciono_version=false;
			$version_minima=min($array_versiones_scripts);
			$version_maxima=max($array_versiones_scripts);
			if($version_minima>$year_corte_para_version_validacion)
			{
				$ruta_validacion_version=$directorio_validacion_per_year.$nombre_base_version.$version_minima.'.php';
				if(file_exists($ruta_validacion_version)==true)
				{
					require_once $ruta_validacion_version;
					$selecciono_version=true;
				}//fin if
			}//fin if

			if($version_maxima<$year_corte_para_version_validacion)
			{
				$ruta_validacion_version=$directorio_validacion_per_year.$nombre_base_version.$version_maxima.'.php';
				if(file_exists($ruta_validacion_version)==true)
				{
					require_once $ruta_validacion_version;
					$selecciono_version=true;
				}//fin if
			}//fin if

			$year_retroceso_version=intval($year_corte_para_version_validacion);
			while($selecciono_version==false)
			{				
				$year_retroceso_version--;
				$ruta_validacion_version=$directorio_validacion_per_year.$nombre_base_version.$year_retroceso_version.'.php';
				if(file_exists($ruta_validacion_version)==true)
				{
					require_once $ruta_validacion_version;
					$selecciono_version=true;
				}//fin if
			}//fin while




		}//fin if
	}//fin else
	//FIN SELECTOR VERSION
	
	$error_mostrar_bd="";
	
	$bool_esta_siendo_reparado=false;
	
	$mensaje_perm_estado="";
	$mensaje_perm_estado_reg_dupl="";
	$mensaje_perm_estado_reg_recuperados="";
	
	
	
	//DIRECTORIO DE LOS ARCHIVOS
	$ruta_carpeta_del_reparado=$rutaTemporal."rERC_".$nombre_archivo_registrado."_".$fecha_actual."_".$tiempo_actual_string;
	if(!file_exists($ruta_carpeta_del_reparado))
	{
		mkdir($ruta_carpeta_del_reparado, 0777);
	}
	else
	{
		$files_to_erase = glob($ruta_carpeta_del_reparado."/*"); // get all file names
		foreach($files_to_erase as $file_to_be_erased)
		{ // iterate files
		  if(is_file($file_to_be_erased))
		  {
		    unlink($file_to_be_erased); // delete file
		  }
		}
	}
	//FIN DIRECTORIO DE LOS ARCHIVOS
	
	//abre o crea el archivo donde se escribiran las inconsistencias NO ESTA EN USO
	$ruta_archivo_inconsistencias_ERC=$ruta_carpeta_del_reparado."/"."iERC_".$fecha_actual."_".$tiempo_actual_string.".csv";
	if(file_exists($ruta_archivo_inconsistencias_ERC))
	{
		unlink($ruta_archivo_inconsistencias_ERC);
	}
	$file_inconsistencias_r2463_ERC = fopen($ruta_archivo_inconsistencias_ERC, "w") or die("fallo la creacion del archivo");
	$titulos="";
	$titulos.="consecutivo,nombre archivo,codigo tipo inconsistencia,tipo inconsistencia,codigo grupo inconsistencia,grupo inconsistencia,";
	$titulos.="codigo detalle inconsistencia,detalle inconsistencia, numero de linea, numero de campo";
	fwrite($file_inconsistencias_r2463_ERC,$titulos."\n");
	
	//CREACION DEL ARCHIVO DE CAMBIOS PARA DUPLICADOS
	$fecha_para_archivo= date('Y-m-d-H-i-s');
	$ruta_cambios_duplicados_campos=$ruta_carpeta_del_reparado."/".$nombre_archivo_registrado."_crpdupl_".$fecha_para_archivo.".txt";		    
	//se remplaza el archivo si ya existe con modo w		
	$file_cambios_duplicados_registro = fopen($ruta_cambios_duplicados_campos, "w") or die("fallo la creacion del archivo");		    
	fclose($file_cambios_duplicados_registro);		    
	//FIN CREACION DEL ARCHIVO DE CAMBIOS PARA DUPLICADOS
	
	//CREACION ARCHIVO LOG CAMBIOS CAMPOS CORRECCION NORMA
	//archivo cambios correccion pre duplicados
	$ruta_cambios_campos_correccion_norma_pre=$ruta_carpeta_del_reparado."/"."cambios_campos_correccion_norma_pre.txt";
	$file_cambios_campos_correccion_norma_pre= fopen($ruta_cambios_campos_correccion_norma_pre, "w") or die("fallo la creacion del archivo");
	fwrite($file_cambios_campos_correccion_norma_pre, "LOG CAMBIOS CORRECCION CAMPOS PRE sobre todos los registros antes de correccion de duplicados o coincidentes"); 						  
	fclose($file_cambios_campos_correccion_norma_pre);
	//archivo cambios correcion pos correccion duplicados
	$ruta_cambios_campos_correccion_norma_pos=$ruta_carpeta_del_reparado."/"."cambios_campos_correccion_norma_pos.txt";
	$file_cambios_campos_correccion_norma_pos= fopen($ruta_cambios_campos_correccion_norma_pos, "w") or die("fallo la creacion del archivo");
	fwrite($file_cambios_campos_correccion_norma_pos, "LOG CAMBIOS CORRECCION CAMPOS POS Sobre unificados"); 						  
	fclose($file_cambios_campos_correccion_norma_pos);
	//FIN CREACION ARCHIVO LOG CAMBIOS CAMPOS CORRECCION NORMA 

	//CREACION ARCHIVOS AFILIADO NO EXISTE
	//archivo afiliado no existe
	$ruta_archivo_afiliado_no_existe=$ruta_carpeta_del_reparado."/"."afiliados_no_existentes.txt";
	$file_archivo_afiliado_no_existe= fopen($ruta_archivo_afiliado_no_existe, "w") or die("fallo la creacion del archivo");
	fwrite($file_archivo_afiliado_no_existe, "ARCHIVO AFILIADOS INEXISTENTES EN EL SISTEMA"); 						  
	fclose($file_archivo_afiliado_no_existe);
	//FIN CREACION ARCHIVOS AFILIADO NO EXISTE

	//CREACION ARCHIVOS AFILIADO EXISTE CAMBIOS SEXO FECHA NACIMIENTO
	//archivo afiliado existe CAMBIO SEXO
	$ruta_archivo_afiliado_existe_cambio_sexo=$ruta_carpeta_del_reparado."/"."afiliados_existe_cambio_sexo.txt";
	$file_archivo_afiliado_existe_cambio_sexo= fopen($ruta_archivo_afiliado_existe_cambio_sexo, "w") or die("fallo la creacion del archivo");
	fwrite($file_archivo_afiliado_existe_cambio_sexo, "ARCHIVO AFILIADOS EXISTE, SE REALIZO CAMBIO EN EL ARCHIVO POR EL SEXO DEL AFILIADO ENCONTRADO EN EL SISTEMA"); 						  
	fclose($file_archivo_afiliado_existe_cambio_sexo);

	//archivo afiliado existe CAMBIO FECHA NACIMIENTO
	$ruta_archivo_afiliado_existe_cambio_fecha_nacimiento=$ruta_carpeta_del_reparado."/"."afiliados_existe_cambio_fecha_nacimiento.txt";
	$file_archivo_afiliado_existe_cambio_fecha_nacimiento= fopen($ruta_archivo_afiliado_existe_cambio_fecha_nacimiento, "w") or die("fallo la creacion del archivo");
	fwrite($file_archivo_afiliado_existe_cambio_fecha_nacimiento, "ARCHIVO AFILIADOS EXISTE, SE REALIZO CAMBIO EN EL ARCHIVO POR LA FECHA DE NACIMIENTO DEL AFILIADO ENCONTRADO EN EL SISTEMA"); 						  
	fclose($file_archivo_afiliado_existe_cambio_fecha_nacimiento);
	//FIN CREACION ARCHIVOS AFILIADO EXISTE CAMBIOS SEXO FECHA NACIMIENTO

	//ARCHIVO REGISTROS EXCLUIDOS FECHA NACIMIENTO INVALIDA
	$ruta_archivo_registros_excluidos_fecha_nacimiento_invalida=$ruta_carpeta_del_reparado."/"."registros_excluidos_fecha_nacimiento_invalida.txt";
	$file_archivo_registros_excluidos_fecha_nacimiento_invalida= fopen($ruta_archivo_registros_excluidos_fecha_nacimiento_invalida, "w") or die("fallo la creacion del archivo");
	fwrite($file_archivo_registros_excluidos_fecha_nacimiento_invalida, "ARCHIVO REGISTROS EXCLUIDOS POR FECHA DE NACIMIENTO INVALIDA"); 						  
	fclose($file_archivo_registros_excluidos_fecha_nacimiento_invalida);
	//FIN ARCHIVO REGISTROS EXCLUIDOS FECHA NACIMIENTO INVALIDA

	//ARCHIVO REGISTROS EXCLUIDOS NO AFILIADOS
	$ruta_archivo_registros_excluidos_no_afiliados=$ruta_carpeta_del_reparado."/"."registros_excluidos_no_afiliados.txt";
	$file_archivo_registros_excluidos_no_afiliados= fopen($ruta_archivo_registros_excluidos_no_afiliados, "w") or die("fallo la creacion del archivo");
	fwrite($file_archivo_registros_excluidos_no_afiliados, "ARCHIVO REGISTROS EXCLUIDOS POR NO AFILIADOS -- NO APLICA FUNDACION"); 						  
	fclose($file_archivo_registros_excluidos_no_afiliados);
	//FIN ARCHIVO REGISTROS EXCLUIDOS NO AFILIADOS
	
	//ABRE O CREA EL ARCHIVO DONDE SE REPARARA EL ARCHIVO
	$ruta_archivo_reparado_ERC=$ruta_carpeta_del_reparado."/".$nombre_archivo_registrado."_dupl.txt";
	if(file_exists($ruta_archivo_reparado_ERC))
	{
		unlink($ruta_archivo_reparado_ERC);
	}
	
	$ruta_archivo_excluido_ERC=$ruta_carpeta_del_reparado."/".$nombre_archivo_registrado."_excluido.txt";
	if(file_exists($ruta_archivo_excluido_ERC))
	{
		unlink($ruta_archivo_excluido_ERC);
	}
	
	$ruta_archivo_reparado_sin_duplicados_ERC=$ruta_carpeta_del_reparado."/".$nombre_archivo_registrado.".txt";
	if(file_exists($ruta_archivo_reparado_sin_duplicados_ERC))
	{
		unlink($ruta_archivo_reparado_sin_duplicados_ERC);
	}
	
	$file_reparado_r2463_ERC = fopen($ruta_archivo_reparado_ERC, "w") or die("fallo la creacion del archivo");
	fclose($file_reparado_r2463_ERC);
	
	$file_excluido_r2463_ERC = fopen($ruta_archivo_excluido_ERC, "w") or die("fallo la creacion del archivo");
	fclose($file_excluido_r2463_ERC);
	
	$file_reparado_r2463_sin_dupl_ERC = fopen($ruta_archivo_reparado_sin_duplicados_ERC, "w") or die("fallo la creacion del archivo");
	fclose($file_reparado_r2463_sin_dupl_ERC);
	//FIN ABRE O CREA EL ARCHIVO DONDE SE REPARARA EL ARCHIVO
	

	//PARTE LLENADO ARRAY PARA EL NUMERO DE CAMPO REAL INDEXADO POR EL NUMERO DE ORDEN(NUMERO CAMPO SISTEMATICO)
	$query_consulta_estructura_numero_campos="";
	$query_consulta_estructura_numero_campos.=" SELECT numero_de_orden,numero_de_campo FROM gioss_estructura_campos_por_norma_a_reportar ";
	$query_consulta_estructura_numero_campos.=" WHERE ";
	$query_consulta_estructura_numero_campos.=" codigo_tipo_norma_obligatoria='06' ";
	$query_consulta_estructura_numero_campos.=" AND ";
	$query_consulta_estructura_numero_campos.=" codigo_tipo_archivo='0601' ORDER BY numero_de_orden ";
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


	
	$errores="";
	$exitos="";
	$tipo_regimen_archivo="";
	
	//PARTE VALIDACION ESTRUCTURA NOMBRE DEL ARCHIVO ERC
	$es_valido_nombre_archivo=true;
	
	$exitos="Archivo $nombre_archivo_registrado. <br>";
	
	if ($archivo_erc['size'] > 250000000)
	{
		$es_valido_nombre_archivo=false;
		$errores.="EL tama&ntildeo no es valido. <br>";
	}
	else
	{
		if($nombre_archivo_registrado!="")
		{
			$ruta_archivo_erc = $rutaTemporal.$archivo_erc['name'];
			move_uploaded_file($archivo_erc['tmp_name'], $ruta_archivo_erc);
			
			$array_nombre_sin_sigla=explode("ERC",$archivo_erc['name']);
			if(count($array_nombre_sin_sigla)!=2)
			{
				$es_valido_nombre_archivo=false;
				$errores.="El encabezado del archivo $nombre_archivo_registrado no corresponde a un archivo ERC. <br>";
			}
			else
			{
				$nombre_archivo_fecha_prestador=$array_nombre_sin_sigla[0];
				$prestador_del_nombre_archivo="";
				$year="";
				$mes="";
				$dia="";
				$prestador_del_nombre_archivo=substr($nombre_archivo_fecha_prestador,8,12);
				$cod_prestador_temporal=$cod_prestador;
				while(strlen($cod_prestador_temporal)<12)
				{
					$cod_prestador_temporal="0".$cod_prestador_temporal;
				}
				//echo "<script>alert('$prestador_del_nombre_archivo y $cod_prestador_temporal');</script>";
				$year=substr($nombre_archivo_fecha_prestador,0,4);
				$mes=substr($nombre_archivo_fecha_prestador,4,2);
				$dia=substr($nombre_archivo_fecha_prestador,6,2);
				//echo "<script>alert('$year $mes $dia');</script>";
				if($prestador_del_nombre_archivo!=$cod_prestador_temporal && $tipo_entidad_que_efectua_el_cargue=="individual_ips")
				{
					$es_valido_nombre_archivo=false;
					$errores.="El codigo de prestador indicado en el nombre del archivo ( $prestador_del_nombre_archivo ), no corresponde al codigo prestador asociado ( $cod_prestador ). <br>";
				}
				else if($tipo_entidad_que_efectua_el_cargue=="agrupado_eapb")
				{
					$nombre_sin_txt_para_verificacion=str_replace(".txt","",$archivo_erc['name']);
					if("0000AGRUPADO"!=$prestador_del_nombre_archivo && strlen($nombre_sin_txt_para_verificacion)==32 )//para erc y vih es 32
					{
						$es_valido_nombre_archivo=false;
						$errores.="La parte del nombre para el  archivo que indica si esta agrupado( $prestador_del_nombre_archivo ), no corresponde a la especificacion 0000AGRUPADO. <br>";
					}
				}
				
				if($tipo_entidad_que_efectua_el_cargue=="individual_ips")
				{
					$regimen_nombre=substr($nombre_archivo_fecha_prestador,20,1);
					if($regimen_nombre!="C" && $regimen_nombre!="S" && $regimen_nombre!="P" && $regimen_nombre!="N" && $regimen_nombre!="E")
					{
						$es_valido_nombre_archivo=false;
						$errores.="El regimen ($regimen_nombre) no corresponde a C-S-P-N-E. <br>";
					}
				}//fin if
				//echo "<script>alert('$regimen_nombre');</script>";
				
				//LONGITUD INCORRECTA para erc y vih
				$nombre_sin_txt_para_verificacion=str_replace(".txt","",$archivo_erc['name']);
				if(strlen($nombre_sin_txt_para_verificacion)!=32 && $tipo_entidad_que_efectua_el_cargue=="individual_ips")
				{
					$es_valido_nombre_archivo=false;
					$errores.="La longitud del archivo sin incluir el .txt debe ser de 32 caracteres no ".strlen($nombre_sin_txt_para_verificacion)."  <br>";
				}
				else if($tipo_entidad_que_efectua_el_cargue=="agrupado_eapb")
				{
					if(strlen($nombre_sin_txt_para_verificacion)!=32
					   && strlen($nombre_sin_txt_para_verificacion)!=19
					   && strlen($nombre_sin_txt_para_verificacion)!=22
					   )
					{
						if(strlen($nombre_sin_txt_para_verificacion)<19
						   || (strlen($nombre_sin_txt_para_verificacion)>19 && strlen($nombre_sin_txt_para_verificacion)<22)
						   )
						{
							$es_valido_nombre_archivo=false;
							$errores.="La longitud del archivo sin incluir el .txt debe ser de 19 caracteres no ".strlen($nombre_sin_txt_para_verificacion)."  <br>";
						}
						else if(
						(strlen($nombre_sin_txt_para_verificacion)>22 && strlen($nombre_sin_txt_para_verificacion)<32)
						|| strlen($nombre_sin_txt_para_verificacion)>32
						)
						{
							$es_valido_nombre_archivo=false;
							$errores.="La longitud del archivo sin incluir el .txt debe ser de 32 caracteres no ".strlen($nombre_sin_txt_para_verificacion)."  <br>";
						}
					}//fin if
				}//fin else if				
				//FIN LONGITUD INCORRECTA
				
				//REGIMEN
				if($tipo_entidad_que_efectua_el_cargue=="individual_ips")
				{
					//esta comentado debido a que hay que verificar tablas asi como en cancer para que acepte regimen alfanumerico
					//$tipo_regimen_archivo=substr($nombre_archivo_fecha_prestador,20,1); 
					//echo "<script>alert('$tipo_regimen_archivo');</script>";
				}
				else
				{
					$tipo_regimen_archivo="C";
				}
				//FIN REGIMEN
				
				$eapb_del_nombre_del_archivo="";
				if($tipo_entidad_que_efectua_el_cargue=="individual_ips")
				{
					$eapb_del_nombre_del_archivo=substr($nombre_archivo_fecha_prestador,21,6);
				}
				else if($tipo_entidad_que_efectua_el_cargue=="agrupado_eapb")
				{
					$nombre_sin_txt_para_verificacion=str_replace(".txt","",$archivo_erc['name']);
					if(strlen($nombre_sin_txt_para_verificacion)==32)
					{
						$eapb_del_nombre_del_archivo=substr($nombre_archivo_fecha_prestador,21,6);
					}
					else if(strlen($nombre_sin_txt_para_verificacion)==19 || strlen($nombre_sin_txt_para_verificacion)==22)
					{
						$barra_al_piso=substr($nombre_archivo_fecha_prestador,8,1);
						if($barra_al_piso=="_")
						{
							$eapb_del_nombre_del_archivo=substr($nombre_archivo_fecha_prestador,9,6);
						}
						else
						{
							$eapb_del_nombre_del_archivo=substr($nombre_archivo_fecha_prestador,8,6);
						}
					}
				}//fin else if agrupado para erc y vih
				$cod_eapb_temporal=$cod_eapb;
				while(strlen($cod_eapb_temporal)<6)
				{
					$cod_eapb_temporal="0".$cod_eapb_temporal;
				}
				//echo "<script>alert('$eapb_del_nombre_del_archivo y $cod_eapb_temporal');</script>";
				
				/*
				$tipo_entidad_reportadora_del_nombre_archivo=substr($nombre_archivo_fecha_prestador,8,2);
				$tipo_regimen_archivo=$tipo_entidad_reportadora_del_nombre_archivo;				
				if ($tipo_entidad_reportadora_del_nombre_archivo!="NI" && $tipo_entidad_reportadora_del_nombre_archivo!="DI" && $tipo_entidad_reportadora_del_nombre_archivo!="MU" && $tipo_entidad_reportadora_del_nombre_archivo!="DE")
				{
					$es_valido_nombre_archivo=false;
					$errores.="El tipo de prestador indicado en el nombre del archivo ( $tipo_entidad_reportadora_del_nombre_archivo ), no corresponde a NI, MU, DI, DE . <br>";
				}
				*/
				
				if($eapb_del_nombre_del_archivo!=$cod_eapb_temporal)
				{
					$es_valido_nombre_archivo=false;
					$errores.="El codigo de la EAPB indicada en el nombre del archivo ( $eapb_del_nombre_del_archivo ), no corresponde al codigo de la EAPB a reportar ( $cod_eapb ). <br>";
				}
				//echo $fecha_de_corte."<br>";
				$array_fecha_de_corte=explode("-",$fecha_de_corte);
				//echo $fecha_corte_anterior_registrada_nombre."<br>";
				$array_fecha_de_corte_anterior_registrada_nombre=explode("-",$fecha_corte_anterior_registrada_nombre);
				if($year!=$array_fecha_de_corte_anterior_registrada_nombre[0])
				{
					$es_valido_nombre_archivo=false;
					$errores.="El a&ntildeo indicado en el nombre del archivo ( $year ), no corresponde al a&ntildeo registrado ( ".$array_fecha_de_corte_anterior_registrada_nombre[0]." ). <br>";
				}
				if($mes!=$array_fecha_de_corte_anterior_registrada_nombre[1])
				{
					$es_valido_nombre_archivo=false;
					$errores.="El mes indicado en el nombre del archivo ( $mes ), no corresponde al mes registrado ( ".$array_fecha_de_corte_anterior_registrada_nombre[1]." ). <br>";
				}
				if($dia!=$array_fecha_de_corte_anterior_registrada_nombre[2])
				{
					$es_valido_nombre_archivo=false;
					$errores.="El dia indicado en el nombre del archivo ( $dia ), no corresponde al dia registrado ( ".$array_fecha_de_corte_anterior_registrada_nombre[2]." ). <br>";
				}
			}//fin if contiene la sigla
		}//fin if nombre del archivo no es vacio
		else
		{
			$es_valido_nombre_archivo=false;
			$errores.="El nombre del archivo para ERC es invalido. <br>";
		}
	}//fin else
	//FIN PARTE VALIDACION ESTRUCTURA NOMBRE DEL ARCHIVO ERC
	
	//VERIFICA SI EL ARCHIVO YA ESTA SIENDO VALIDADO ACTUALMENTE
        $bool_esta_siendo_reparado=false;
        
        $query_verificacion_esta_siendo_procesado="";
        $query_verificacion_esta_siendo_procesado.=" SELECT * FROM gioss_2463_esta_reparando_ar_actualmente ";
        $query_verificacion_esta_siendo_procesado.=" WHERE fecha_corte_archivo_en_reparacion='".$fecha_de_corte."' ";
        $query_verificacion_esta_siendo_procesado.=" AND codigo_entidad_reportadora='".$cod_prestador."' ";
        $query_verificacion_esta_siendo_procesado.=" AND nombre_archivo='".$nombre_archivo_registrado."'  ";
        $query_verificacion_esta_siendo_procesado.=" ; ";
        $resultados_query_verificar_esta_siendo_procesado=$coneccionBD->consultar2_no_crea_cierra($query_verificacion_esta_siendo_procesado);
        if(count($resultados_query_verificar_esta_siendo_procesado)>0)
        {
                foreach($resultados_query_verificar_esta_siendo_procesado as $estado_tiempo_real_archivo)
                {
                        if($estado_tiempo_real_archivo["esta_ejecutando"]=="SI")
                        {
                                $bool_esta_siendo_reparado=true;
				$es_valido_nombre_archivo=false;
				$errores.="Se esta validando el archivo actualmente, por favor espere a que termine para volver a validarlo nuevamente.<br>";
                                break;
                        }
                }
                
        }        
        //FIN VERIFICA SI EL ARCHIVO YA ESTA SIENDO VALIDADO ACTUALMENTE
	
	//VERIFICA SI FUE VALIDADO PREVIAMENTE
	$sql_query_verificar="";
	$sql_query_verificar.=" SELECT * FROM gioss_tabla_estado_informacion_r2463_erc ";
	$sql_query_verificar.=" WHERE fecha_corte='".$fecha_de_corte."' ";
	$sql_query_verificar.=" AND codigo_eapb='".$cod_eapb."' ";
	$sql_query_verificar.=" AND codigo_prestador_servicios='".$cod_prestador."' ";
	$sql_query_verificar.=" AND nombre_del_archivo='".$nombre_archivo_registrado."'  ";
	$sql_query_verificar.=" ; ";
	$resultados_query_verificar=$coneccionBD->consultar2_no_crea_cierra($sql_query_verificar);
	if(count($resultados_query_verificar)>0)
	{
		
		
		
	}
	else
	{
		$es_valido_nombre_archivo=false;
		$verificacion_no_se_ha_validado_con_anterioridad=true;
		$errores.="No se ha validado previamente el archivo,  es necesario validar el archivo para poder realizar el proceso de correcci&oacuten.<br>";
	}
	//FIN VERIFICA SI FUE VALIDADO PREVIAMENTE
	
	if($verificacion_no_se_ha_validado_con_anterioridad==false && $bool_esta_siendo_reparado==false)
	{
		//SE BORRA SI HABIA ALGO ANTES EN LA TABLA TEMPORAL DE CORREGIDOS PARA EL USUARIO
		/*
		$sql_delete_corregidos_temp="";
		$sql_delete_corregidos_temp.=" DELETE FROM corregidos_con_duplicados_erc2463 ";
		$sql_delete_corregidos_temp.=" WHERE ";
		$sql_delete_corregidos_temp.=" tipo_id_usuario='$tipo_id' ";
		$sql_delete_corregidos_temp.=" AND ";
		$sql_delete_corregidos_temp.=" id_usuario='$identificacion' ";
		$sql_delete_corregidos_temp.=" AND ";
		$sql_delete_corregidos_temp.=" nick_usuario='$nick_user' ";
		$sql_delete_corregidos_temp.=" ; ";
		$error_bd_seq="";		
		$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_delete_corregidos_temp, $error_bd_seq);
		if($error_bd_seq!="")
		{
			$mensajes_error_bd.=" ERROR Al eliminar en la tabla temporal de registros corregidos pre correccion duplicados  para corrector .<br>";
			
		}
		
		$sql_delete_corregidos_temp="";
		$sql_delete_corregidos_temp.=" DELETE FROM corregidos_solo_duplicados_erc2463 ";
		$sql_delete_corregidos_temp.=" WHERE ";
		$sql_delete_corregidos_temp.=" tipo_id_usuario='$tipo_id' ";
		$sql_delete_corregidos_temp.=" AND ";
		$sql_delete_corregidos_temp.=" id_usuario='$identificacion' ";
		$sql_delete_corregidos_temp.=" AND ";
		$sql_delete_corregidos_temp.=" nick_usuario='$nick_user' ";
		$sql_delete_corregidos_temp.=" ; ";
		$error_bd_seq="";		
		$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_delete_corregidos_temp, $error_bd_seq);
		if($error_bd_seq!="")
		{
			$mensajes_error_bd.=" ERROR Al eliminar en la tabla temporal de registros corregidos con solo los duplicados encontrados para corrector .<br>";
			
		}
		
		$sql_delete_corregidos_temp="";
		$sql_delete_corregidos_temp.=" DELETE FROM corregidos_sin_duplicados_erc2463 ";
		$sql_delete_corregidos_temp.=" WHERE ";
		$sql_delete_corregidos_temp.=" tipo_id_usuario='$tipo_id' ";
		$sql_delete_corregidos_temp.=" AND ";
		$sql_delete_corregidos_temp.=" id_usuario='$identificacion' ";
		$sql_delete_corregidos_temp.=" AND ";
		$sql_delete_corregidos_temp.=" nick_usuario='$nick_user' ";
		$sql_delete_corregidos_temp.=" ; ";
		$error_bd_seq="";		
		$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_delete_corregidos_temp, $error_bd_seq);
		if($error_bd_seq!="")
		{
			$mensajes_error_bd.=" ERROR Al eliminar en la tabla temporal de registros corregidos pos correccion sin duplicados  para corrector .<br>";
			
		}
		*/
		//FIN SE BORRA SI HABIA ALGO ANTES EN LA TABLA TEMPORAL DE CORREGIDOS PARA EL USUARIO
		
		//INICIO LA EJECUCION		
		$query_insert_esta_siendo_procesado="";
		$query_insert_esta_siendo_procesado.=" INSERT INTO gioss_2463_esta_reparando_ar_actualmente ";
		$query_insert_esta_siendo_procesado.=" ( ";
		$query_insert_esta_siendo_procesado.=" codigo_entidad_reportadora,";
		$query_insert_esta_siendo_procesado.=" nombre_archivo,";
		$query_insert_esta_siendo_procesado.=" fecha_corte_archivo_en_reparacion,";
		$query_insert_esta_siendo_procesado.=" fecha_validacion,";
		$query_insert_esta_siendo_procesado.=" hora_validacion,";
		$query_insert_esta_siendo_procesado.=" nick_usuario,";
		$query_insert_esta_siendo_procesado.=" esta_ejecutando,";
		$query_insert_esta_siendo_procesado.=" se_pudo_descargar,";
		$query_insert_esta_siendo_procesado.=" mensaje_estado_registros";
		$query_insert_esta_siendo_procesado.=" ) ";
		$query_insert_esta_siendo_procesado.=" VALUES ";
		$query_insert_esta_siendo_procesado.=" ( ";
		$query_insert_esta_siendo_procesado.=" '".$cod_prestador."',  ";
		$query_insert_esta_siendo_procesado.=" '".$nombre_archivo_registrado."',  ";
		$query_insert_esta_siendo_procesado.=" '".$fecha_de_corte."',  ";
		$query_insert_esta_siendo_procesado.=" '".$fecha_actual."',  ";
		$query_insert_esta_siendo_procesado.=" '".$tiempo_actual."',  ";
		$query_insert_esta_siendo_procesado.=" '".$nick_user."',  ";
		$query_insert_esta_siendo_procesado.=" 'SI',  ";
		$query_insert_esta_siendo_procesado.=" 'NO',  ";
		$query_insert_esta_siendo_procesado.=" 'inicio el proceso'  ";
		$query_insert_esta_siendo_procesado.=" ) ";
		$query_insert_esta_siendo_procesado.=" ; ";
		$error_bd="";
		$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($query_insert_esta_siendo_procesado, $error_bd);
		if($error_bd!="")
		{
			if(connection_aborted()==false)
			{
				echo "<script>alert('error al iniciar el estado actual de validacion en tiempo real  2463 ');</script>";
			}
		}
		//FIN INICIO LA EJECUCION
		
		$es_primera_linea=false;
		
		$es_primera_linea_exc=false;
		
		$lineas_del_archivo=0;
		
		$registros_buenos=0;
		$registros_malos=0;
		
		$numero_lineas_campos_incorrectos=0;
		
		//PARTE CORRIGE ARCHIVO, IDENTIFICA E INDEXA DUPLICADOS 
		$hubo_inconsistencias_en_ERC=false;
		
		//estas listas no se usan debido al cambio del metodo
		$diccionario_identificacion=array();
		$diccionario_identificacion_lineas=array();
		
		//variables para nuevo metodo duplicados
		$acumulador_para_contar_duplicados=0;
		$personas_con_duplicados_hasta_el_momento=0;
		$personas_insertadas_hasta_el_momento=0;
		$cont_porcentaje=0;
		$cont_porcentaje_dupl=0;
		$cont_porcentaje_csv=0;
		
		//la variable $consecutivo_errores pasara como referencia
		//y aumentara cada que se haye un error y su incremento
		//es independiente de la variable $cont_linea
		$consecutivo_errores=0;
		
		if($es_valido_nombre_archivo)
		{
			$mensaje_errores_ERC="";
			$lineas_del_archivo = contar_lineas_archivo($ruta_archivo_erc); 
			$file_ERC = fopen($ruta_archivo_erc, 'r') or exit("No se pudo abrir el archivo");
			
			$es_primera_linea=false;			
			$cont_linea=0;
			
			$cont_linea_para_indexador=0;//solo pone las lineas que fueron aprobadas en estructura, fehca de nacimiento o si es prepagada en afiliados existentes
			
			$fue_cerrada_la_gui=false;
			while (!feof($file_ERC)) 
			{
				if($fue_cerrada_la_gui==false)
				{
				    if(connection_aborted()==true)
				    {
					$fue_cerrada_la_gui=true;
				    }
				}//fin if verifica si el usuario cerro la pantalla
				
				//porcentaje
				$muestra_mensaje_nuevo=false;
				$porcentaje=intval((($cont_linea)*100)/($lineas_del_archivo-1));
				if($porcentaje!=$cont_porcentaje || ($porcentaje==0 && ($cont_linea)==1) || $porcentaje==100)
				{
				 $cont_porcentaje=$porcentaje;
				 $muestra_mensaje_nuevo=true;
				}
				//fin porcentaje
				
				$mensaje_estado_registros="";
				$mensaje_estado_registros.="<table style=text-align:center;width:60%;left:25%;border-style:solid;border-width:5px; id=tabla_estado_1>";
				$mensaje_estado_registros.="<tr style=background-color:#80bfff><th colspan=2 style=text-align:center;width:60%><span style=\"color:white;text-shadow:2px 2px 8px #0000ff;\">Inicio a las $tiempo_actual del $fecha_actual para $nombre_archivo_registrado</span></th></tr>";
				$mensaje_estado_registros.="<tr><td style=text-align:left;width:60%>Numero de registros:</td><td style=text-align:left>".($lineas_del_archivo-1)."</td></tr>";
				$mensaje_estado_registros.="<tr style=background-color:#80bfff><td style=text-align:left;width:60%>Numero de registros corregidos:</td><td style=text-align:left>".$cont_linea."</td></tr>";
				$mensaje_estado_registros.="<tr><td style=text-align:left;width:60%>Porcentaje registros corregidos:</td><td style=text-align:left>$porcentaje %</td></tr>";
				$mensaje_estado_registros.="<tr style=background-color:#80bfff><td style=text-align:left;width:60%>Numero de registros duplicados:</td><td style=text-align:left>".$acumulador_para_contar_duplicados.".</tr>";
				$mensaje_estado_registros.="<tr><td style=text-align:left;width:60%>Numero registros unicos:</td><td style=text-align:left>".$personas_insertadas_hasta_el_momento."</td></tr>";
				$mensaje_estado_registros.="<tr style=background-color:#80bfff><td style=text-align:left;width:60%>Numero de personas con registros duplicados:</td><td style=text-align:left>".$personas_con_duplicados_hasta_el_momento."</td></tr>";
				$mensaje_estado_registros.="<tr><td style=text-align:left;width:60%>Numero de registros con numero de campos invalidos (menor o mayor de 119):</td><td style=text-align:left>".$numero_lineas_campos_incorrectos."</td></tr>";
				$mensaje_estado_registros.="</table><br>";
				
				$mensaje_perm_estado=$mensaje_estado_registros;
				
				if($muestra_mensaje_nuevo)
				{
					$html_del_mensaje="";
					$html_del_mensaje.="<table>";
					/*
					$html_del_mensaje.="<tr>";
					$html_del_mensaje.="<td colspan=\'2\'>";
					$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="</tr>";
					*/
					$html_del_mensaje.="<tr>";
					/*
					$html_del_mensaje.="<td style=\'width:35%;text-align:right;\'>";
					$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
					$html_del_mensaje.="</td>";
					*/
					$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
					$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_estado_registros."</div><div id=\'errores_bd_div\'></div>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="</tr>";
					$html_del_mensaje.="</table>";
					if(connection_aborted()==false)
					{
						echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
						ob_flush();
						flush();
					}
					
					//UPDATE MENSAJE
					
					$query_update_esta_siendo_procesado="";
					$query_update_esta_siendo_procesado.=" UPDATE gioss_2463_esta_reparando_ar_actualmente ";
					$query_update_esta_siendo_procesado.=" SET mensaje_estado_registros='$mensaje_estado_registros' ";
					$query_update_esta_siendo_procesado.=" WHERE fecha_corte_archivo_en_reparacion='".$fecha_de_corte."' ";
					$query_update_esta_siendo_procesado.=" AND codigo_entidad_reportadora='".$cod_prestador."' ";
					$query_update_esta_siendo_procesado.=" AND nombre_archivo='".$nombre_archivo_registrado."'  ";
					$query_update_esta_siendo_procesado.=" AND nick_usuario='".$nick_user."'  ";
					$query_update_esta_siendo_procesado.=" AND fecha_validacion='".$fecha_actual."'  ";
					$query_update_esta_siendo_procesado.=" AND hora_validacion='".$tiempo_actual."'  ";
					$query_update_esta_siendo_procesado.=" ; ";
					$error_bd="";
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($query_update_esta_siendo_procesado, $error_bd);
					if($error_bd!="")
					{
						if($fue_cerrada_la_gui==false)
						{
							echo "<script>alert('error al actualizar el estado actual de reparacion en tiempo real  2463 ');</script>";
						}
					}
					//FIN UPDATE MENSAJE
					
					//CANCELA EJECUCION DEL ARCHIVO			    
					$verificar_si_ejecucion_fue_cancelada="";
					$verificar_si_ejecucion_fue_cancelada.=" SELECT esta_ejecutando FROM gioss_2463_esta_reparando_ar_actualmente ";
					$verificar_si_ejecucion_fue_cancelada.=" WHERE fecha_corte_archivo_en_reparacion='".$fecha_de_corte."' ";
					$verificar_si_ejecucion_fue_cancelada.=" AND codigo_entidad_reportadora='".$cod_prestador."' ";	    
					$verificar_si_ejecucion_fue_cancelada.=" AND nombre_archivo='".$nombre_archivo_registrado."'  ";
					$verificar_si_ejecucion_fue_cancelada.=" AND nick_usuario='".$nick_user."'  ";
					$verificar_si_ejecucion_fue_cancelada.=" AND fecha_validacion='".$fecha_actual."'  ";
					$verificar_si_ejecucion_fue_cancelada.=" AND hora_validacion='".$tiempo_actual."'  ";
					$verificar_si_ejecucion_fue_cancelada.=" ; ";
					$error_bd="";
					$resultados_si_ejecucion_fue_cancelada=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($verificar_si_ejecucion_fue_cancelada, $error_bd);
					if($error_bd!="")
					{
						if(connection_aborted()==false)
						{
							echo "<script>alert('error al consultar si se cancelo la ejecucion ');</script>";
						}
					}
					
					if(count($resultados_si_ejecucion_fue_cancelada)>0 && is_array($resultados_si_ejecucion_fue_cancelada))
					{
					    $esta_ejecutando=$resultados_si_ejecucion_fue_cancelada[0]["esta_ejecutando"];
					    if($esta_ejecutando=="NO")
					    {
						exit(0);
					    }
					}
					//FIN CANCELA EJECUCION DEL ARCHIVO
				}//fin if muestra mensaje o cancela ejecucion
				
				$linea_tmp = fgets($file_ERC);
				$linea= explode("\n", $linea_tmp)[0];
				$linea=str_replace(",",".",$linea);
				$campos = explode("\t", $linea);
				
								
				//pasa a validar los campos
				if(count($campos)==119)
				{
					$cont_pre_fix_campos=0;
					while($cont_pre_fix_campos<119)
					{
						$campos[$cont_pre_fix_campos] = str_replace("á","a",$campos[$cont_pre_fix_campos]);
						$campos[$cont_pre_fix_campos] = str_replace("é","e",$campos[$cont_pre_fix_campos]);
						$campos[$cont_pre_fix_campos] = str_replace("í","i",$campos[$cont_pre_fix_campos]);
						$campos[$cont_pre_fix_campos] = str_replace("ó","o",$campos[$cont_pre_fix_campos]);
						$campos[$cont_pre_fix_campos] = str_replace("ú","u",$campos[$cont_pre_fix_campos]);
						$campos[$cont_pre_fix_campos] = str_replace("ñ","n",$campos[$cont_pre_fix_campos]);
						$campos[$cont_pre_fix_campos] = str_replace("Á","A",$campos[$cont_pre_fix_campos]);
						$campos[$cont_pre_fix_campos] = str_replace("É","E",$campos[$cont_pre_fix_campos]);
						$campos[$cont_pre_fix_campos] = str_replace("Í","I",$campos[$cont_pre_fix_campos]);
						$campos[$cont_pre_fix_campos] = str_replace("Ó","O",$campos[$cont_pre_fix_campos]);
						$campos[$cont_pre_fix_campos] = str_replace("Ú","U",$campos[$cont_pre_fix_campos]);
						$campos[$cont_pre_fix_campos] = str_replace("Ñ","N",$campos[$cont_pre_fix_campos]);
						$campos[$cont_pre_fix_campos] = str_replace("'"," ",$campos[$cont_pre_fix_campos]);
						$campos[$cont_pre_fix_campos] = str_replace("\n"," ",$campos[$cont_pre_fix_campos]);
						$campos[$cont_pre_fix_campos]=preg_replace("/[^A-Za-z0-9:,.\/\_\|\-\s+\+]/", "", trim($campos[$cont_pre_fix_campos]) );
						$cont_pre_fix_campos++;
					}
					
					//parte para evitar caracteres extraños en el ultimo campo antes del salto de linea
					$campos[count($campos)-1]=procesar_mensaje3($campos[count($campos)-1]);
						
					$cont_campos_ver_vacios=0;
					while($cont_campos_ver_vacios<119)
					{
						
						if(trim($campos[$cont_campos_ver_vacios])!="")
						{
							//si no estan vacios quita los espacios que rodean la cadena de texto
							$campos[$cont_campos_ver_vacios]=trim($campos[$cont_campos_ver_vacios]);
						}
						$cont_campos_ver_vacios++;
					}
					

					$array_pre_campos=array();
					$array_pos_campos=array();

					$array_pre_campos=$campos;

					//PARTE CONSULTA VERIFICA EXISTENCIA AFILIADOS
					//gioss_afiliados_eapb_rc, id_afiliado, tipo_id_afiliado,$tipo_de_regimen_de_la_informacion_reportada
					$query_bd_existe_afiliado_en_tabla_regimen="";
					$resultados_query_existe_afiliado_tablas_regimen=array();
					$nombre_tabla_afiliado_hallado="";
					$campo_n5_ti=preg_replace("/[^a-zA-Z0-9]+/", "", trim($campos[4]) );//TIPO IDENTIFICACION 
					$campo_n5_ti=strtoupper($campo_n5_ti);
					$campos[4]=$campo_n5_ti;//se reasigna para que quede corregido en caso de que no exista el afiliado
					$campo_n6_ni=preg_replace("/[^a-zA-Z0-9]+/", "",trim($campos[5]) );//NUMERO IDENTIFICACION
					$campos[5]=$campo_n6_ni;//se reasigna para que quede corregido en caso de que no exista el afiliado
					$tipo_de_regimen_de_la_informacion_reportada=trim($campos[9]);//REGIMEN	
					$tipo_de_regimen_de_la_informacion_reportada=strtoupper($tipo_de_regimen_de_la_informacion_reportada);
					$campos[9]=$tipo_de_regimen_de_la_informacion_reportada;
					$cod_eapb_global=$cod_eapb;//se reasigna para que quede semi corregido 
					if($tipo_de_regimen_de_la_informacion_reportada=="C")
					{
						$nombre_tabla_afiliado_hallado="gioss_afiliados_eapb_rc";

						$query_bd_existe_afiliado_en_tabla_regimen="SELECT * FROM ".$nombre_tabla_afiliado_hallado." WHERE id_afiliado = '".$campo_n6_ni."' AND tipo_id_afiliado = '".$campo_n5_ti."' AND codigo_eapb='".$cod_eapb_global."' ;";
						$resultados_query_existe_afiliado_tablas_regimen=$coneccionBD->consultar2_no_crea_cierra($query_bd_existe_afiliado_en_tabla_regimen);
					}
					if($tipo_de_regimen_de_la_informacion_reportada=="S")
					{
						$nombre_tabla_afiliado_hallado="gioss_afiliados_regimen_subsidiado";

						$query_bd_existe_afiliado_en_tabla_regimen="SELECT * FROM ".$nombre_tabla_afiliado_hallado." WHERE id_afiliado = '".$campo_n6_ni."' AND tipo_id_afiliado = '".$campo_n5_ti."' AND codigo_eapb='".$cod_eapb_global."' ;";
						$resultados_query_existe_afiliado_tablas_regimen=$coneccionBD->consultar2_no_crea_cierra($query_bd_existe_afiliado_en_tabla_regimen);
					}
					if($tipo_de_regimen_de_la_informacion_reportada=="E" || $tipo_de_regimen_de_la_informacion_reportada=="O")
					{
						$nombre_tabla_afiliado_hallado="gioss_afiliados_eapb_mp";

						$query_bd_existe_afiliado_en_tabla_regimen="SELECT * FROM ".$nombre_tabla_afiliado_hallado." WHERE id_afiliado = '".$campo_n6_ni."' AND tipo_id_afiliado = '".$campo_n5_ti."' AND codigo_eapb='".$cod_eapb_global."' ;";
						$resultados_query_existe_afiliado_tablas_regimen=$coneccionBD->consultar2_no_crea_cierra($query_bd_existe_afiliado_en_tabla_regimen);
					}
					if($tipo_de_regimen_de_la_informacion_reportada=="P")
					{
						$nombre_tabla_afiliado_hallado="gioss_afiliados_eapb_rp";

						$query_bd_existe_afiliado_en_tabla_regimen="SELECT * FROM ".$nombre_tabla_afiliado_hallado." WHERE id_afiliado = '".$campo_n6_ni."' AND tipo_id_afiliado = '".$campo_n5_ti."' AND codigo_eapb='".$cod_eapb_global."' ;";
						$resultados_query_existe_afiliado_tablas_regimen=$coneccionBD->consultar2_no_crea_cierra($query_bd_existe_afiliado_en_tabla_regimen);
					}
					if($tipo_de_regimen_de_la_informacion_reportada=="N")
					{
						$nombre_tabla_afiliado_hallado="gioss_afiliados_eapb_nv";

						$query_bd_existe_afiliado_en_tabla_regimen="SELECT * FROM ".$nombre_tabla_afiliado_hallado." WHERE id_afiliado = '".$campo_n6_ni."' AND tipo_id_afiliado = '".$campo_n5_ti."' AND codigo_eapb='".$cod_eapb_global."' ;";
						$resultados_query_existe_afiliado_tablas_regimen=$coneccionBD->consultar2_no_crea_cierra($query_bd_existe_afiliado_en_tabla_regimen);
					}//fin if
					//contador filas
					$num_filas_resultado_existe_tablas_regimen=count($resultados_query_existe_afiliado_tablas_regimen);
					//FIN PARTE CONSULTA VERIFICA EXISTENCIA AFILIADOS

					//PARTE PRE CORRECCION SEXO Y FECHA NACIMIENTO DE ACUERDO A TABLAS DE REGIMEN
					$se_modifico_sexo_o_fecha_de_acuerdo_a_tabla_regimen=false;
					$se_modifico_sexo=false;
					$se_modifico_fecha_nacimiento=false;
					$se_modifico_nombres_o_apellidos=false;
					$si_existe=false;

					$sexo_anterior="";
					$fecha_anterior="";

					$sexo_posterior="";
					$fecha_posterior="";

					if($num_filas_resultado_existe_tablas_regimen>0 
						&& is_array($resultados_query_existe_afiliado_tablas_regimen)
						)
					{
						$si_existe=true;
						//verifica el sexo y fecha registrado en bd
						$numero_campo_sexo=7;//campo 8 norma cancer
						$sexo_en_registro_archivo=strtoupper(trim($campos[$numero_campo_sexo]) );
						$sexo_anterior=$sexo_en_registro_archivo;
						$numero_campo_fecha_nacimiento=6;//campo  7 norma cancer
						$fecha_nacimiento_en_registro_archivo=trim($campos[$numero_campo_fecha_nacimiento]);
						$fecha_anterior=$fecha_nacimiento_en_registro_archivo;
						$sexo_en_bd=strtoupper(trim($resultados_query_existe_afiliado_tablas_regimen[0]['sexo']) );
						$fecha_nacimiento_en_bd=trim($resultados_query_existe_afiliado_tablas_regimen[0]['fecha_nacimiento']);

						if($sexo_en_bd!="" && $fecha_nacimiento_en_bd!="")
						{
							//si el sexo registrado en la tabla regimen
							//esta bien escrito y es diferente de lo registrado en 
							//el archivo(independiente si este esta bien escrito o no)
							//lo remplaza por el nuevo sexo
							if($sexo_en_bd=="F" 
								|| $sexo_en_bd=="M"
								)
							{
								if($sexo_en_bd!=$sexo_en_registro_archivo)
								{
									$campos[$numero_campo_sexo]=$sexo_en_bd;
									$se_modifico_sexo_o_fecha_de_acuerdo_a_tabla_regimen=true;
									$se_modifico_sexo=true;
								}//fin if
							}//fin if

							//echo "<script>alert('$fecha_nacimiento_en_registro_archivo $campo_n5_ti $campo_n6_ni');</script>";

							$fecha_nacimiento_en_registro_archivo=corrector_formato_fecha($fecha_nacimiento_en_registro_archivo,$fecha_de_corte,true);
							$campos[$numero_campo_fecha_nacimiento]=$fecha_nacimiento_en_registro_archivo;
							if(formato_fecha_valida_quick($fecha_nacimiento_en_bd) )
							{
								$campos[$numero_campo_fecha_nacimiento]=$fecha_nacimiento_en_bd;
								$se_modifico_sexo_o_fecha_de_acuerdo_a_tabla_regimen=true;
								$se_modifico_fecha_nacimiento=true;
								
							}//fin if fecha nacimeinto es valida
							else
							{
								$fecha_nacimiento_en_bd=corrector_formato_fecha($fecha_nacimiento_en_bd,$fecha_de_corte,true);
								if(formato_fecha_valida_quick($fecha_nacimiento_en_bd) )
								{
									if($fecha_nacimiento_en_bd!=$fecha_nacimiento_en_registro_archivo)
									{
										$campos[$numero_campo_fecha_nacimiento]=$fecha_nacimiento_en_bd;
										$se_modifico_sexo_o_fecha_de_acuerdo_a_tabla_regimen=true;
										$se_modifico_fecha_nacimiento=true;
									}//fin if
								}//fin if fecha nacimeinto es valida

							}//fin else

							$sexo_posterior=$sexo_en_bd;
							$fecha_posterior=$fecha_nacimiento_en_bd;
						}//fin if datos de bd no estan vacios

						//se modifica nombres						
						$campos[0]=procesar_mensaje(trim($resultados_query_existe_afiliado_tablas_regimen[0]['primer_nombre']) );
						$campos[1]=procesar_mensaje(trim($resultados_query_existe_afiliado_tablas_regimen[0]['segundo_nombre']) );
						$campos[2]=procesar_mensaje(trim($resultados_query_existe_afiliado_tablas_regimen[0]['primer_apellido']) );
						$campos[3]=procesar_mensaje(trim($resultados_query_existe_afiliado_tablas_regimen[0]['segundo_apellido']) );
						$se_modifico_nombres_o_apellidos=true;
					}//fin if hay concidencia en bd					

					//FIN PARTE PRE CORRECCION SEXO Y FECHA NACIMIENTO DE ACUERDO A TABLAS DE REGIMEN

					$primer_nombre="".$campos[0];
					$segundo_nombre="".$campos[1];
					$primer_apellido="".$campos[2];
					$segundo_apellido="".$campos[3];

					//PARTE AFILIADO NO EXISTE (escribe en archivo)

					$linea_mod_depende_afiliados=implode("\t", $campos);
					
					
					if($si_existe==false)
					{
						$linea_datos_afiliado_no_existe="El afiliado $campo_n5_ti $campo_n6_ni NO EXISTE";
						//si el afiliado no existe
						$file_archivo_afiliado_no_existe= fopen($ruta_archivo_afiliado_no_existe, "a") or die("fallo la creacion del archivo");
						fwrite($file_archivo_afiliado_no_existe, "\n".$linea_datos_afiliado_no_existe); 						  
						fclose($file_archivo_afiliado_no_existe);

						//excluidos
						$file_archivo_registros_excluidos_no_afiliados= fopen($ruta_archivo_registros_excluidos_no_afiliados, "a") or die("fallo la creacion del archivo");
						fwrite($file_archivo_registros_excluidos_no_afiliados, "\n".$linea_mod_depende_afiliados);
						fclose($file_archivo_registros_excluidos_no_afiliados);
					}//fin else
					//PARTE AFILIADO NO EXISTE (escribe en archivo)

					if($se_modifico_sexo==true)
					{
						$linea_se_modifico_sexo_afiliado="=\"$campo_n5_ti\";=\"$campo_n6_ni\";=\"Al afiliado $campo_n5_ti $campo_n6_ni se le cambio el sexo ( $sexo_anterior ) por ( $sexo_posterior )\"";
						$file_archivo_afiliado_existe_cambio_sexo= fopen($ruta_archivo_afiliado_existe_cambio_sexo, "a") or die("fallo la creacion del archivo");
						fwrite($file_archivo_afiliado_existe_cambio_sexo, "\n".$linea_se_modifico_sexo_afiliado);
						fclose($file_archivo_afiliado_existe_cambio_sexo);
					}//fin if

					if($se_modifico_fecha_nacimiento==true)
					{
						$linea_se_modifico_fecha_nacimiento_afiliado="=\"$campo_n5_ti\";=\"$campo_n6_ni\";=\"Al afiliado $campo_n5_ti $campo_n6_ni se le cambio la fecha de nacimiento ( $fecha_anterior) por ( $fecha_posterior )\"";
						$file_archivo_afiliado_existe_cambio_fecha_nacimiento= fopen($ruta_archivo_afiliado_existe_cambio_fecha_nacimiento, "a") or die("fallo la creacion del archivo");
						fwrite($file_archivo_afiliado_existe_cambio_fecha_nacimiento, "\n".$linea_se_modifico_fecha_nacimiento_afiliado);
						fclose($file_archivo_afiliado_existe_cambio_fecha_nacimiento);
					}//fin if

					//VERIFICACION FECHA NACIMIENTO ES MENOR 1900-12-31 O EXCEDE LA FECHA DE CORTE Y DE FORMATO VALIDO
					$numero_campo_fecha_nacimiento=6;//campo  7 norma cancer
					$fecha_nacimiento_a_verificar=trim($campos[$numero_campo_fecha_nacimiento]);

					$fecha_nacimiento= explode("-",$fecha_nacimiento_a_verificar);
					$bool_fecha_nacimiento_valida=true;
					if(count($fecha_nacimiento)!=3
					   || !(ctype_digit($fecha_nacimiento[0]) && ctype_digit($fecha_nacimiento[1]) && ctype_digit($fecha_nacimiento[2]) )
					   || !checkdate($fecha_nacimiento[1],$fecha_nacimiento[2],$fecha_nacimiento[0]))
					{			
						$bool_fecha_nacimiento_valida=false;
					}//verificacion formato fecha

					
					if($bool_fecha_nacimiento_valida==true)
					{
						//diferencia_dias_entre_fechas esta en reparadorCANCER.php ya esta importado al principio
						$es_menor_a_1900_12_31=diferencia_dias_entre_fechas(trim($fecha_nacimiento_a_verificar),"1900-12-31");
						if($es_menor_a_1900_12_31>0)
						{
							$bool_fecha_nacimiento_valida=false;
							
						}//fin if

						$fecha_nacimiento_excede_fecha_corte=diferencia_dias_entre_fechas(trim($fecha_nacimiento_a_verificar),$fecha_de_corte);
						if($fecha_nacimiento_excede_fecha_corte<0)
						{
							$bool_fecha_nacimiento_valida=false;
						}//fin if
					}//fin if verifica si es menor a 1900-12-31 y marca como invalida

					if($bool_fecha_nacimiento_valida==false)
					{
						$file_archivo_registros_excluidos_fecha_nacimiento_invalida= fopen($ruta_archivo_registros_excluidos_fecha_nacimiento_invalida, "a") or die("fallo la creacion del archivo");
						fwrite($file_archivo_registros_excluidos_fecha_nacimiento_invalida, "\n".$linea_mod_depende_afiliados);
						fclose($file_archivo_registros_excluidos_fecha_nacimiento_invalida);
					}//fin if
					//FIN VERIFICACION FECHA NACIMIENTO ES MENOR 1900-12-31 O EXCEDE LA FECHA DE CORTE Y DE FORMATO VALIDO

					$es_linea_valida_para_reparar=true;
					if($bool_fecha_nacimiento_valida==false){$es_linea_valida_para_reparar=false;}
					if($si_existe==false){$es_linea_valida_para_reparar=false;}//solo aplica prepagada
					
					if($es_linea_valida_para_reparar==true)//si la linea cumple con las condiciones prevas es valida para reparar
					{
					//validar_ERC($campos,$cont_linea,&$consecutivo_errores,$array_tipo_validacion,$array_grupo_validacion,$array_detalle_validacion,$nombre_archivo,$fecha_remision,$fecha_de_corte,$cod_prestador,$cod_eapb)
					$array_resultados_validacion=reparacion_campo_en_blanco_ERC($campos,
										$cod_eapb,
											 $cont_linea_para_indexador,
										 $consecutivo_errores,
										 $array_tipo_validacion,
										 $array_grupo_validacion,
										 $array_detalle_validacion,
										 $nombre_archivo_registrado,
										 $fecha_de_corte,
										 $cod_prestador,
										 $cod_eapb,
										 $diccionario_identificacion,
										 $diccionario_identificacion_lineas,
										 $coneccionBD, $array_numero_campo_bd);
					
					$array_resultados_validacion=reparacion_formato_ERC($campos,
											 $cont_linea_para_indexador,
										 $consecutivo_errores,
										 $array_tipo_validacion,
										 $array_grupo_validacion,
										 $array_detalle_validacion,
										 $nombre_archivo_registrado,
										 $fecha_de_corte,
										 $cod_prestador,
										 $cod_eapb,
										 $diccionario_identificacion,
										 $diccionario_identificacion_lineas,
										 $coneccionBD, $array_numero_campo_bd);
					
					$array_resultados_validacion=reparacion_valor_permitido_ERC($campos,
											 $cont_linea_para_indexador,
										 $consecutivo_errores,
										 $array_tipo_validacion,
										 $array_grupo_validacion,
										 $array_detalle_validacion,
										 $nombre_archivo_registrado,
										 $fecha_de_corte,
										 $cod_prestador,
										 $cod_eapb,
										 $diccionario_identificacion,
										 $diccionario_identificacion_lineas,
										 $coneccionBD, $array_numero_campo_bd);
					
					$array_resultados_validacion_2=reparacion_criterios_de_calidad_ERC($campos,
											 $cont_linea_para_indexador,
										 $consecutivo_errores,
										 $array_tipo_validacion,
										 $array_grupo_validacion,
										 $array_detalle_validacion,
										 $nombre_archivo_registrado,
										 $fecha_de_corte,
										 $cod_prestador,
										 $cod_eapb,
										 $diccionario_identificacion,
										 $diccionario_identificacion_lineas,
										 $coneccionBD, $array_numero_campo_bd);
							
						$array_pos_campos=$campos;

						//parte LOG cmabios correccion pre
						if(count($array_pre_campos)==count($array_pos_campos) )
						{
							$cont_comparacion_antes_despues=0;
							while ($cont_comparacion_antes_despues<count($array_pre_campos) )
							{
								$antes=$array_pre_campos[$cont_comparacion_antes_despues];
								$despues=$array_pos_campos[$cont_comparacion_antes_despues];
								if($antes!=$despues)
								{

									$numero_campo_actual_de_acuerdo_norma=$array_numero_campo_bd[$cont_comparacion_antes_despues];
									$linea_a_escribir_log="El campo numero ( $numero_campo_actual_de_acuerdo_norma ) tenia el valor ( $antes )  y fue cambiado por el valor ( $despues ) En la linea Numero $cont_linea.";
									
									$file_cambios_campos_correccion_norma_pre= fopen($ruta_cambios_campos_correccion_norma_pre, "a") or die("fallo la creacion del archivo");
									fwrite($file_cambios_campos_correccion_norma_pre, "\n".$linea_a_escribir_log); 						  
									fclose($file_cambios_campos_correccion_norma_pre);
								}//fin if
								$cont_comparacion_antes_despues++;
							}//fin while
						}//fin if
						//FIN parte LOG cmabios correccion pre
							
					
					
					//proceso de indexacion de duplicados y subida a las tablas de sin duplicados (para registros unicos detectados previamente)
					//y a la tabla de corregidos con duplicados con motivos de log
					
					if(count($campos)==119)
					{
						//INDEXADOR DE DUPLICADOS
						//FASE 1 consulta por el campo 4 y 5 (tipo id,  numero id afiliado) si existe duplicado
						$existe_afiliado=false;
							$lista_lineas_duplicados="".$cont_linea_para_indexador;
						$query_consultar_en_indexador="";
						$query_consultar_en_indexador.=" SELECT lista_lineas_donde_hay_duplicados FROM  ";
						$query_consultar_en_indexador.=" gioss_indexador_duplicados_del_reparador_2463 ";
						$query_consultar_en_indexador.=" WHERE  ";
						$query_consultar_en_indexador.="tipo_id_usuario='".$tipo_id."'";				
						$query_consultar_en_indexador.=" AND ";
						$query_consultar_en_indexador.="id_usuario='".$identificacion."'";
						$query_consultar_en_indexador.=" AND ";
						$query_consultar_en_indexador.="nick_usuario='".$nick_user."'";
						$query_consultar_en_indexador.=" AND ";
						$query_consultar_en_indexador.="fecha_corte_reporte='".$fecha_de_corte."'";
						$query_consultar_en_indexador.=" AND ";
						$query_consultar_en_indexador.="fecha_de_generacion='".$fecha_actual."'";
						$query_consultar_en_indexador.=" AND ";
						$query_consultar_en_indexador.="hora_generacion='".$tiempo_actual."'";
						$query_consultar_en_indexador.=" AND ";
						$query_consultar_en_indexador.="codigo_entidad_eapb_generadora='".$cod_eapb."'";
						$query_consultar_en_indexador.=" AND ";
						$query_consultar_en_indexador.="codigo_entidad_prestadora='".$cod_prestador."'";
						$query_consultar_en_indexador.=" AND ";
						$query_consultar_en_indexador.="nombre_archivo='".$nombre_archivo_registrado."'";
						$query_consultar_en_indexador.=" AND ";
						$query_consultar_en_indexador.="campo_erc_de_numero_orden_4_tipo_id='".$campos[4]."'";
						$query_consultar_en_indexador.=" AND ";
						$query_consultar_en_indexador.="campo_erc_de_numero_orden_5_numero_id='".$campos[5]."'";
						$query_consultar_en_indexador.=" ; ";
						$error_bd_seq="";		
						$resultado_esta_afiliado_en_indexador=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($query_consultar_en_indexador, $error_bd_seq);
						if($error_bd_seq!="")
						{
						    $mensajes_error_bd.=" ERROR Al consultar en la tabla gioss_indexador_duplicados_del_reparador_2463 ".procesar_mensaje($error_bd_seq).".<br>";
						    
						    if($fue_cerrada_la_gui==false)
						    {
							    echo "<script>alert('ERROR Al consultar en la tabla gioss_indexador_duplicados_del_reparador_2463 ".procesar_mensaje($error_bd_seq)."');</script>";
						    }
							
						}//fin if
						if(is_array($resultado_esta_afiliado_en_indexador) && count($resultado_esta_afiliado_en_indexador)>0)
						{
						    $existe_afiliado=true;
						    $array_check_is_list=array();
						    $string_res_en_indexador=$resultado_esta_afiliado_en_indexador[0]["lista_lineas_donde_hay_duplicados"];
						    $array_check_is_list=explode(";;",$string_res_en_indexador);
						    if(count($array_check_is_list)<2)
						    {
							//echo "<script>alert('adiciona el primero $string_res_en_indexador acc $acumulador_para_contar_duplicados');</script>";
							$acumulador_para_contar_duplicados+=1;
						    }
							    $lista_lineas_duplicados=$resultado_esta_afiliado_en_indexador[0]["lista_lineas_donde_hay_duplicados"].";;".$cont_linea_para_indexador;
						    //si haya duplicado, suma 1
						    $acumulador_para_contar_duplicados+=1;
						    //echo "<script>alert('antes $string_res_en_indexador despues $lista_lineas_duplicados acc $acumulador_para_contar_duplicados');</script>";
						}
						else
						{
						    //si no haya duplicado, suma cero
						    $acumulador_para_contar_duplicados+=0;
						}
						//FIN FASE 1
						
						
						//FASE 2 inserta en indexador de duplicado si no habia
						if($existe_afiliado==false)
						{
						    $query_insert_updt_en_indexador="";
						    $query_insert_updt_en_indexador.=" INSERT INTO ";
						    $query_insert_updt_en_indexador.=" gioss_indexador_duplicados_del_reparador_2463 ";				
						    $query_insert_updt_en_indexador.=" ( ";	
						    $query_insert_updt_en_indexador.=" tipo_id_usuario, ";
						    $query_insert_updt_en_indexador.=" id_usuario, ";
						    $query_insert_updt_en_indexador.=" nick_usuario, ";
						    $query_insert_updt_en_indexador.=" fecha_corte_reporte, ";
						    $query_insert_updt_en_indexador.=" fecha_de_generacion, ";
						    $query_insert_updt_en_indexador.=" hora_generacion, ";
						    $query_insert_updt_en_indexador.=" codigo_entidad_eapb_generadora, ";
						    $query_insert_updt_en_indexador.=" codigo_entidad_prestadora, ";
						    $query_insert_updt_en_indexador.=" nombre_archivo, ";
						    $query_insert_updt_en_indexador.=" campo_erc_de_numero_orden_4_tipo_id, ";
						    $query_insert_updt_en_indexador.=" campo_erc_de_numero_orden_5_numero_id, ";
						    $query_insert_updt_en_indexador.=" contiene_filas_coincidentes, ";
						    $query_insert_updt_en_indexador.=" lista_lineas_donde_hay_duplicados ";
						    $query_insert_updt_en_indexador.=" ) ";
						    $query_insert_updt_en_indexador.=" VALUES ";
						    $query_insert_updt_en_indexador.=" ( ";
						    $query_insert_updt_en_indexador.="'".$tipo_id."',";
						    $query_insert_updt_en_indexador.="'".$identificacion."',";
						    $query_insert_updt_en_indexador.="'".$nick_user."',";							
						    $query_insert_updt_en_indexador.="'".$fecha_de_corte."',";
						    $query_insert_updt_en_indexador.="'".$fecha_actual."',";
						    $query_insert_updt_en_indexador.="'".$tiempo_actual."',";
						    $query_insert_updt_en_indexador.="'".$cod_eapb."',";
						    $query_insert_updt_en_indexador.="'".$cod_prestador."',";
						    $query_insert_updt_en_indexador.="'".$nombre_archivo_registrado."',";
						    $query_insert_updt_en_indexador.="'".$campos[4]."',";
						    $query_insert_updt_en_indexador.="'".$campos[5]."',";
						    $query_insert_updt_en_indexador.="'NO',";
							    $query_insert_updt_en_indexador.="'".$cont_linea_para_indexador."'";
						    $query_insert_updt_en_indexador.=" ) ";
						    $query_insert_updt_en_indexador.=" ; ";
						    $error_bd_seq="";		
						    $bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($query_insert_updt_en_indexador, $error_bd_seq);
						    if($error_bd_seq!="")
						    {
							$mensajes_error_bd.=" ERROR Al subir en la tabla gioss_indexador_duplicados_del_reparador_2463 ".procesar_mensaje($error_bd_seq).".<br>";
							
							if($fue_cerrada_la_gui==false)
							{
								echo "<script>alert('ERROR Al subir en la tabla gioss_indexador_duplicados_del_reparador_2463  ".procesar_mensaje($error_bd_seq)."');</script>";
							}
						    }
						    else
						    {
							$personas_insertadas_hasta_el_momento+=1;
									//echo "se inserto el numero linea $cont_linea_para_indexador lista_lineas_duplicados $lista_lineas_duplicados<br>";
						    }
						    
						    //sube a corregidos_sin_duplicados_erc2463
						    $query_subir_registro_corregido="";
						    $query_subir_registro_corregido.=" INSERT INTO ";
						    $query_subir_registro_corregido.=" corregidos_sin_duplicados_erc2463 ";				
						    $query_subir_registro_corregido.=" ( ";				
						    $numero_actual_campo_registro_corregido=0;
						    while($numero_actual_campo_registro_corregido<119)
						    {
							    $query_subir_registro_corregido.=" campo_erc_de_numero_orden_".$numero_actual_campo_registro_corregido." , ";
							    $numero_actual_campo_registro_corregido++;
						    }//fin while para nombres columnas de bd correspondientes a los campos de La_Norma_Actual a insertar
						    $query_subir_registro_corregido.=" tipo_id_usuario, ";
						    $query_subir_registro_corregido.=" id_usuario, ";
						    $query_subir_registro_corregido.=" nick_usuario, ";
						    $query_subir_registro_corregido.=" numero_registro, ";
						    $query_subir_registro_corregido.=" fecha_corte_reporte, ";
						    $query_subir_registro_corregido.=" fecha_de_generacion, ";
						    $query_subir_registro_corregido.=" hora_generacion, ";
						    $query_subir_registro_corregido.=" codigo_entidad_eapb_generadora, ";
						    $query_subir_registro_corregido.=" codigo_entidad_prestadora, ";
						    $query_subir_registro_corregido.=" nombre_archivo_erc ";
						    $query_subir_registro_corregido.=" ) ";
						    $query_subir_registro_corregido.=" VALUES ";
						    $query_subir_registro_corregido.=" ( ";				
						    $numero_actual_campo_registro_corregido=0;
						    while($numero_actual_campo_registro_corregido<119)
						    {
							    $query_subir_registro_corregido.="'".$campos[$numero_actual_campo_registro_corregido]."',";
							    $numero_actual_campo_registro_corregido++;
						    }//fin while con los valores de los campos La_Norma_Actual a insertar en la tabla
						    $query_subir_registro_corregido.="'".$tipo_id."',";
						    $query_subir_registro_corregido.="'".$identificacion."',";
						    $query_subir_registro_corregido.="'".$nick_user."',";	
							    $query_subir_registro_corregido.="'".($cont_linea_para_indexador+1)."',";							
						    $query_subir_registro_corregido.="'".$fecha_de_corte."',";
						    $query_subir_registro_corregido.="'".$fecha_actual."',";
						    $query_subir_registro_corregido.="'".$tiempo_actual."',";
						    $query_subir_registro_corregido.="'".$cod_eapb."',";
						    $query_subir_registro_corregido.="'".$cod_prestador."',";
						    $query_subir_registro_corregido.="'".$nombre_archivo_registrado."'";
						    $query_subir_registro_corregido.=" ) ";
						    $query_subir_registro_corregido.=" ; ";
						    $error_bd_seq="";		
						    $bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($query_subir_registro_corregido, $error_bd_seq);
						    if($error_bd_seq!="")
						    {
							    $mensajes_error_bd.=" ERROR Al subir en la tabla temporal de registros corregidos pre correccion duplicados  para corrector ".procesar_mensaje($error_bd_seq).".<br>";
							    
						    }
						    //fin sube a corregidos_sin_duplicados_erc2463
						    
						}//fin if
						//o actualiza si ya habia concatenando a la lista de numero de filas
						else if($existe_afiliado==true)
						{
						    $array_check_tiene_2_filas_coincidentes=explode(";;",$lista_lineas_duplicados);
						    
						    //borra el que estaba en corregidos_sin_duplicados_erc2463
						    //entrea si el nuemro de filas es igual a dos
						    if(count($array_check_tiene_2_filas_coincidentes)==2)
						    {
							//BORRANDO el afiliado duplicado de corregidos_sin_duplicados_erc2463
							$sql_delete_corregidos_temp="";
							$sql_delete_corregidos_temp.=" DELETE FROM corregidos_sin_duplicados_erc2463  ";
							$sql_delete_corregidos_temp.=" WHERE fecha_de_generacion='$fecha_actual'  AND hora_generacion='$tiempo_actual' ";
							$sql_delete_corregidos_temp.=" AND ";
							$sql_delete_corregidos_temp.=" fecha_corte_reporte='".$fecha_de_corte."'  ";
							$sql_delete_corregidos_temp.=" AND ";
							$sql_delete_corregidos_temp.=" codigo_entidad_prestadora='".$cod_prestador."'  ";
							$sql_delete_corregidos_temp.=" AND ";
							$sql_delete_corregidos_temp.=" codigo_entidad_eapb_generadora='".$cod_eapb."'  ";
							$sql_delete_corregidos_temp.=" AND ";
							$sql_delete_corregidos_temp.=" nombre_archivo_erc='".$nombre_archivo_registrado."'  ";
							$sql_delete_corregidos_temp.=" AND ";
							$sql_delete_corregidos_temp.=" tipo_id_usuario='$tipo_id' ";
							$sql_delete_corregidos_temp.=" AND ";
							$sql_delete_corregidos_temp.=" id_usuario='$identificacion' ";
							$sql_delete_corregidos_temp.=" AND ";
							$sql_delete_corregidos_temp.=" nick_usuario='$nick_user' ";
							$sql_delete_corregidos_temp.=" AND ";
							$sql_delete_corregidos_temp.=" campo_erc_de_numero_orden_4='".$campos[4]."' ";
							$sql_delete_corregidos_temp.=" AND ";
							$sql_delete_corregidos_temp.=" campo_erc_de_numero_orden_5='".$campos[5]."' ";
							$sql_delete_corregidos_temp.=" ; ";
							$error_bd_seq="";		
							$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_delete_corregidos_temp, $error_bd_seq);
							if($error_bd_seq!="")
							{
								$mensajes_error_bd.=" ERROR Al eliminar en la tabla corregidos_sin_duplicados_erc2463 ".procesar_mensaje($error_bd_seq).".<br>";
								
							}
							//FIN BORRANDO el afiliado duplicado de corregidos_sin_duplicados_erc2463
						    }
						    //fin borra el que estaba en corregidos_sin_duplicados_erc2463
						    
						    $query_insert_updt_en_indexador="";
						    $query_insert_updt_en_indexador.=" UPDATE  ";
						    $query_insert_updt_en_indexador.=" gioss_indexador_duplicados_del_reparador_2463 ";				
						    $query_insert_updt_en_indexador.=" SET ";
						    $query_insert_updt_en_indexador.=" contiene_filas_coincidentes='SI', ";
						    $query_insert_updt_en_indexador.=" lista_lineas_donde_hay_duplicados='".$lista_lineas_duplicados."' ";
						    $query_insert_updt_en_indexador.=" WHERE  ";
						    $query_insert_updt_en_indexador.="tipo_id_usuario='".$tipo_id."'";				
						    $query_insert_updt_en_indexador.=" AND ";
						    $query_insert_updt_en_indexador.="id_usuario='".$identificacion."'";
						    $query_insert_updt_en_indexador.=" AND ";
						    $query_insert_updt_en_indexador.="nick_usuario='".$nick_user."'";
						    $query_insert_updt_en_indexador.=" AND ";
						    $query_insert_updt_en_indexador.="fecha_corte_reporte='".$fecha_de_corte."'";
						    $query_insert_updt_en_indexador.=" AND ";
						    $query_insert_updt_en_indexador.="fecha_de_generacion='".$fecha_actual."'";
						    $query_insert_updt_en_indexador.=" AND ";
						    $query_insert_updt_en_indexador.="hora_generacion='".$tiempo_actual."'";
						    $query_insert_updt_en_indexador.=" AND ";
						    $query_insert_updt_en_indexador.="codigo_entidad_eapb_generadora='".$cod_eapb."'";
						    $query_insert_updt_en_indexador.=" AND ";
						    $query_insert_updt_en_indexador.="codigo_entidad_prestadora='".$cod_prestador."'";
						    $query_insert_updt_en_indexador.=" AND ";
						    $query_insert_updt_en_indexador.="nombre_archivo='".$nombre_archivo_registrado."'";
						    $query_insert_updt_en_indexador.=" AND ";
						    $query_insert_updt_en_indexador.="campo_erc_de_numero_orden_4_tipo_id='".$campos[4]."'";
						    $query_insert_updt_en_indexador.=" AND ";
						    $query_insert_updt_en_indexador.="campo_erc_de_numero_orden_5_numero_id='".$campos[5]."'";						    
						    $query_insert_updt_en_indexador.=" ; ";
						    $error_bd_seq="";		
						    $bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($query_insert_updt_en_indexador, $error_bd_seq);
						    if($error_bd_seq!="")
						    {
							$mensajes_error_bd.=" ERROR Al actualizar en la tabla gioss_indexador_duplicados_del_reparador_2463 ".procesar_mensaje($error_bd_seq).".<br>";
							
							if($fue_cerrada_la_gui==false)
							{
								echo "<script>alert('ERROR Al actualizar en la tabla gioss_indexador_duplicados_del_reparador_2463  ".procesar_mensaje($error_bd_seq)."');</script>";
							}
						    }
						    
						    if(count($array_check_tiene_2_filas_coincidentes)==2)
						    {
							$personas_con_duplicados_hasta_el_momento+=1;
						    }
						}//fin if actualizar
						//FIN FASE 2
						//FIN INDEXADOR DE DUPLICADOS
						
						//SUBE A BD EL REGISTRO PARA EL PROCESO DE CORRECCION DE DUPLICADOS
						$query_subir_registro_corregido="";
						$query_subir_registro_corregido.=" INSERT INTO ";
						$query_subir_registro_corregido.=" corregidos_con_duplicados_erc2463 ";				
						$query_subir_registro_corregido.=" ( ";				
						$numero_actual_campo_registro_corregido=0;
						while($numero_actual_campo_registro_corregido<119)
						{
							$query_subir_registro_corregido.=" campo_erc_de_numero_orden_".$numero_actual_campo_registro_corregido." , ";
							$numero_actual_campo_registro_corregido++;
						}//fin while para nombres columnas de bd correspondientes a los campos de 2463 a insertar
						$query_subir_registro_corregido.=" tipo_id_usuario, ";
						$query_subir_registro_corregido.=" id_usuario, ";
						$query_subir_registro_corregido.=" nick_usuario, ";
						$query_subir_registro_corregido.=" numero_registro, ";
						$query_subir_registro_corregido.=" fecha_corte_reporte, ";
						$query_subir_registro_corregido.=" fecha_de_generacion, ";
						$query_subir_registro_corregido.=" hora_generacion, ";
						$query_subir_registro_corregido.=" codigo_entidad_eapb_generadora, ";
						$query_subir_registro_corregido.=" codigo_entidad_prestadora, ";
						$query_subir_registro_corregido.=" nombre_archivo_erc ";
						$query_subir_registro_corregido.=" ) ";
						$query_subir_registro_corregido.=" VALUES ";
						$query_subir_registro_corregido.=" ( ";				
						$numero_actual_campo_registro_corregido=0;
						while($numero_actual_campo_registro_corregido<119)
						{						
						    $query_subir_registro_corregido.="'".$campos[$numero_actual_campo_registro_corregido]."',";
						    $numero_actual_campo_registro_corregido++;
						}//fin while con los valores de los campos 2463 a insertar en la tabla
						$query_subir_registro_corregido.="'".$tipo_id."',";
						$query_subir_registro_corregido.="'".$identificacion."',";
						$query_subir_registro_corregido.="'".$nick_user."',";	
							$query_subir_registro_corregido.="'".($cont_linea_para_indexador+1)."',";							
						$query_subir_registro_corregido.="'".$fecha_de_corte."',";
						$query_subir_registro_corregido.="'".$fecha_actual."',";
						$query_subir_registro_corregido.="'".$tiempo_actual."',";
						$query_subir_registro_corregido.="'".$cod_eapb."',";
						$query_subir_registro_corregido.="'".$cod_prestador."',";
						$query_subir_registro_corregido.="'".$nombre_archivo_registrado."'";
						$query_subir_registro_corregido.=" ) ";
						$query_subir_registro_corregido.=" ; ";
						$error_bd_seq="";		
						$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($query_subir_registro_corregido, $error_bd_seq);
						if($error_bd_seq!="")
						{
							$mensajes_error_bd.=" ERROR Al subir en la tabla temporal de registros <br> corregidos pre correccion duplicados  para corrector .<br> $error_bd_seq ";
							
						}
						//FIN SUBE A BD EL REGISTRO PARA EL PROCESO DE CORRECCION DE DUPLICADOS
					}//fin if numero de campos correcto
					
					
					//ESCRIBE LINEA REPARADA	
					$linea_reparada="";
					$cont_campo_reparado=0;
					while($cont_campo_reparado<119)
					{
						if($linea_reparada!=""){$linea_reparada.="\t";}
						$linea_reparada.=$campos[$cont_campo_reparado];
						$cont_campo_reparado++;
					}
					
					$file_reparado_r2463_ERC = fopen($ruta_archivo_reparado_ERC, "a") or die("fallo la creacion del archivo");
					if($es_primera_linea==false)
					{
						fwrite($file_reparado_r2463_ERC,$linea_reparada);
						$es_primera_linea=true;
					}
					else
					{
						fwrite($file_reparado_r2463_ERC,"\n".$linea_reparada);
					}
					fclose($file_reparado_r2463_ERC);
					//FIN ESCRIBE LINEA REPARADA
					
					if($hubo_inconsistencias_en_ERC==false)
					{
						$hubo_inconsistencias_en_ERC=$array_resultados_validacion["error"];
					}
					if($hubo_inconsistencias_en_ERC==false)
					{
						$hubo_inconsistencias_en_ERC=$array_resultados_validacion_2["error"];
					}
					
						//escribe los errores NO USA
						/*
					$mensaje_errores_ERC=$array_resultados_validacion["mensaje"];
					$array_mensajes_errores_campos=explode("|",$mensaje_errores_ERC);
					
					foreach($array_mensajes_errores_campos as $msg_error)
					{	
						fwrite($file_inconsistencias_r2463_ERC, $msg_error."\n");
						
						$columnas_inconsistencias_para_bd=array();
						$columnas_inconsistencias_para_bd=explode(",",$msg_error);
						
						
						
					}
						*/
						//fin escribe los errores NO USA

						//echo $cont_linea_para_indexador." y mas uno ".($cont_linea_para_indexador+1)." afiliado $campo_n5_ti $campo_n6_ni ".$campos[16]."  <br>";

						$cont_linea_para_indexador++;//solo se incrementa cuando la linea ha cumplido con las indicaciones de que es valida para reparar

					}//fin if es linea valida para reparar
					
				}//fin if verifica longitud
				else
				{
					$numero_lineas_campos_incorrectos++;
					
					//NO USA
					/*
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0301001"])[1];
					$error_longitud=$consecutivo_errores.",".$nombre_archivo_registrado.",03,".$array_tipo_validacion["03"].",0301,".$array_grupo_validacion["0301"].",0301001,$cadena_descripcion_inconsistencia ".count($campos).",".($cont_linea+1).","."-1";
					$consecutivo_errores++;
					
					if($hubo_inconsistencias_en_ERC==false)
					{
						$hubo_inconsistencias_en_ERC=true;
					}
					fwrite($file_inconsistencias_r2463_ERC, $error_longitud."\n");
					
					$columnas_inconsistencias_para_bd=array();
					$columnas_inconsistencias_para_bd=explode(",",$error_longitud);
					*/
					//FIN NO USA
					
					//escribe en el archivo excluido los registros con campos incorrectos
					$linea_original=explode("\n", $linea_tmp)[0];
					
					$file_excluido_r2463_ERC = fopen($ruta_archivo_excluido_ERC, "a") or die("fallo la creacion del archivo");
					if($es_primera_linea_exc==false)
					{
						fwrite($file_excluido_r2463_ERC,$linea_original);
						$es_primera_linea_exc=true;
					}
					else
					{
						fwrite($file_excluido_r2463_ERC,"\n".$linea_original);
					}
					fclose($file_excluido_r2463_ERC);
					//fin escribe en el archivo excluido los registros con campos incorrectos
					
					/*
					//ESCRIBE NO SUBIO EN EL CORREGIDO CON DUPLICADOS
					//no es necesario debido al contador para cuando la linea es valida
					//para reparar separado del contador de lineas
					//normales
					$file_reparado_r2463_ERC = fopen($ruta_archivo_reparado_ERC, "a") or die("fallo la creacion del archivo");
					if($es_primera_linea==false)
					{
						fwrite($file_reparado_r2463_ERC,"NO_SUBIO");
						$es_primera_linea=true;
					}
					else
					{
						fwrite($file_reparado_r2463_ERC,"\n"."NO_SUBIO");
					}
					fclose($file_reparado_r2463_ERC);
					//ESCRIBE NO SUBIO EN EL CORREGIDO CON DUPLICADOS
					*/
					
				}//fin else longitud no apropiada
				$cont_linea++;
			}
			fclose($file_ERC);
			
			//despues del while
			
			$mensaje_estado_registros="";
			$mensaje_estado_registros.="<table style=text-align:center;width:60%;left:25%;border-style:solid;border-width:5px; id=tabla_estado_1>";
			$mensaje_estado_registros.="<tr style=background-color:#80bfff><th colspan=2 style=text-align:center;width:60%><span style=\"color:white;text-shadow:2px 2px 8px #0000ff;\">Inicio a las $tiempo_actual del $fecha_actual para $nombre_archivo_registrado</span></th></tr>";
			$mensaje_estado_registros.="<tr><td style=text-align:left;width:60%>Numero de registros:</td><td style=text-align:left>".($lineas_del_archivo-1)."</td></tr>";
			$mensaje_estado_registros.="<tr style=background-color:#80bfff><td style=text-align:left;width:60%>Numero de registros corregidos:</td><td style=text-align:left>".($cont_linea-1)."</td></tr>";
			$mensaje_estado_registros.="<tr><td style=text-align:left;width:60%>Porcentaje registros corregidos:</td><td style=text-align:left>$porcentaje %</td></tr>";
			$mensaje_estado_registros.="<tr style=background-color:#80bfff><td style=text-align:left;width:60%>Numero de registros duplicados:</td><td style=text-align:left>".$acumulador_para_contar_duplicados.".</tr>";
			$mensaje_estado_registros.="<tr><td style=text-align:left;width:60%>Numero registros unicos:</td><td style=text-align:left>".$personas_insertadas_hasta_el_momento."</td></tr>";
			$mensaje_estado_registros.="<tr style=background-color:#80bfff><td style=text-align:left;width:60%>Numero de personas con registros duplicados:</td><td style=text-align:left>".$personas_con_duplicados_hasta_el_momento."</td></tr>";
			$mensaje_estado_registros.="<tr><td style=text-align:left;width:60%>Numero de registros con numero de campos invalidos (menor o mayor de 119):</td><td style=text-align:left>".$numero_lineas_campos_incorrectos."</td></tr>";
			$mensaje_estado_registros.="</table><br>";
			
			$mensaje_perm_estado=$mensaje_estado_registros;
			//fin despues del while
		}
		//fin if el nombre del archivo es valido
		
		//cierra el archivo donde se escriben las inconsistencias
		fclose($file_inconsistencias_r2463_ERC);
		
		//FIN PARTE CORRIGE ARCHIVO, IDENTIFICA E INDEXA DUPLICADOS
		
		//ARREGLO DE DUPLICADOS EN UNO SOLO
		$nombre_vista_index_duplicados="indxd2463".$nombre_archivo_registrado.$nick_user.$fecha_y_hora_para_view;
		
		$sql_vista_duplicados_reporte_obligatorio ="";
		$sql_vista_duplicados_reporte_obligatorio.="CREATE OR REPLACE VIEW $nombre_vista_index_duplicados ";
		$sql_vista_duplicados_reporte_obligatorio.=" AS  ";					
		$sql_vista_duplicados_reporte_obligatorio .="SELECT * from gioss_indexador_duplicados_del_reparador_2463  ";	
		$sql_vista_duplicados_reporte_obligatorio.=" WHERE fecha_de_generacion='$fecha_actual'  AND hora_generacion='$tiempo_actual' ";
		$sql_vista_duplicados_reporte_obligatorio.=" AND ";
		$sql_vista_duplicados_reporte_obligatorio.=" codigo_entidad_prestadora='".$cod_prestador."'  ";
		$sql_vista_duplicados_reporte_obligatorio.=" AND ";
		$sql_vista_duplicados_reporte_obligatorio.=" codigo_entidad_eapb_generadora='".$cod_eapb."'  ";
		$sql_vista_duplicados_reporte_obligatorio.=" AND ";
		$sql_vista_duplicados_reporte_obligatorio.=" nombre_archivo='".$nombre_archivo_registrado."'  ";
		$sql_vista_duplicados_reporte_obligatorio.=" AND ";
		$sql_vista_duplicados_reporte_obligatorio.=" tipo_id_usuario='$tipo_id' ";
		$sql_vista_duplicados_reporte_obligatorio.=" AND ";
		$sql_vista_duplicados_reporte_obligatorio.=" id_usuario='$identificacion' ";
		$sql_vista_duplicados_reporte_obligatorio.=" AND ";
		$sql_vista_duplicados_reporte_obligatorio.=" nick_usuario='$nick_user' ";
		$sql_vista_duplicados_reporte_obligatorio.=" AND ";
		$sql_vista_duplicados_reporte_obligatorio.=" contiene_filas_coincidentes='SI' ";
		$sql_vista_duplicados_reporte_obligatorio.=" ORDER BY campo_erc_de_numero_orden_4_tipo_id asc,campo_erc_de_numero_orden_5_numero_id asc  ";
		$sql_vista_duplicados_reporte_obligatorio.=";";
		$error_bd_seq="";		
		$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_vista_duplicados_reporte_obligatorio, $error_bd_seq);
		if($error_bd_seq!="")
		{
			$mensajes_error_bd.=" ERROR al crear vista de duplicados: ".$error_bd_seq."<br>";
		}
		
		//numero de duplicados
		$sql_numero_de_personas="";
		$sql_numero_de_personas.=" SELECT count(*) as numero_registros FROM $nombre_vista_index_duplicados  ; ";
		$error_bd_seq="";
		$array_numero_de_personas=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($sql_numero_de_personas, $error_bd_seq);
		if($error_bd_seq!="")
		{
			$mensajes_error_bd.=" ERROR al consultar numero de registros del total de los duplicados  dejado: ".$error_bd_seq."<br>";
		}
		
		$numero_personas=0;
		
		if(count($array_numero_de_personas)>0 && is_array($array_numero_de_personas))
		{
			$numero_personas=$array_numero_de_personas[0]["numero_registros"];
		}
		//fin numero de duplicados
		
		
		$limite_personas=0;
		$contador_offset_personas=0;
		//a diferencia de los otros bloques donde eran bloques
		//de registros delarchivo aqui es un bloque de mil personas
		$numero_registros_bloque_personas=150;
		$fue_cerrada_la_gui2=false;
		$numero_filas_donde_esta_afiliado_actual=0;
		$numero_duplicados_procesados_hasta_el_momento=0;
		if($numero_personas>0)
		{								
		    while($contador_offset_personas<$numero_personas)
		    {
			if($fue_cerrada_la_gui2==false)
			{
			    if(connection_aborted()==true)
			    {
				$fue_cerrada_la_gui2=true;
			    }
			}//fin if verifica si el usuario cerro la pantalla
			
			//CANCELA EJECUCION DEL ARCHIVO			    
			$verificar_si_ejecucion_fue_cancelada="";
			$verificar_si_ejecucion_fue_cancelada.=" SELECT esta_ejecutando FROM gioss_2463_esta_reparando_ar_actualmente ";
			$verificar_si_ejecucion_fue_cancelada.=" WHERE fecha_corte_archivo_en_reparacion='".$fecha_de_corte."' ";
			$verificar_si_ejecucion_fue_cancelada.=" AND codigo_entidad_reportadora='".$cod_eapb."' ";					    
			$verificar_si_ejecucion_fue_cancelada.=" AND nombre_archivo='".$nombre_archivo_registrado."'  ";
			$verificar_si_ejecucion_fue_cancelada.=" AND nick_usuario='".$nick_user."'  ";
			$verificar_si_ejecucion_fue_cancelada.=" AND fecha_validacion='".$fecha_actual."'  ";
			$verificar_si_ejecucion_fue_cancelada.=" AND hora_validacion='".$tiempo_actual."'  ";
			$verificar_si_ejecucion_fue_cancelada.=" ; ";
			$error_bd="";
			$resultados_si_ejecucion_fue_cancelada=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($verificar_si_ejecucion_fue_cancelada, $error_bd);
			if($error_bd!="")
			{
				if($fue_cerrada_la_gui2==false)
				{
					echo "<script>alert('error al consultar si se cancelo la ejecucion ');</script>";
				}
			}
			
			if(count($resultados_si_ejecucion_fue_cancelada)>0 && is_array($resultados_si_ejecucion_fue_cancelada))
			{
			    $esta_ejecutando=$resultados_si_ejecucion_fue_cancelada[0]["esta_ejecutando"];
			    if($esta_ejecutando=="NO")
			    {
				exit(0);
			    }
			}
			//FIN CANCELA EJECUCION DEL ARCHIVO
				    
			$limite_personas=$numero_registros_bloque_personas;
				
			if( ($contador_offset_personas+$numero_registros_bloque_personas)>=$numero_personas)
			{
				$limite_personas=$numero_registros_bloque_personas+($numero_personas-$contador_offset_personas);
			}
			
			$sql_query_busqueda_personas_bloques="";
			$sql_query_busqueda_personas_bloques.="SELECT * FROM $nombre_vista_index_duplicados LIMIT $limite_personas OFFSET $contador_offset_personas;  ";
			$error_bd_seq="";
			$resultados_query_erc2463_duplicados=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($sql_query_busqueda_personas_bloques,$error_bd_seq);
			if($error_bd_seq!="")
			{
				$mensajes_error_bd.="ERROR AL CONSULTAR de vista de las personas: ".$error_bd_seq."<br>";
			}
			
			if(count($resultados_query_erc2463_duplicados)>0 && is_array($resultados_query_erc2463_duplicados))
			{
				foreach($resultados_query_erc2463_duplicados as $duplicado_actual)
				{
					//echo "<script>alert('entro ".count($resultados_query_erc2463_duplicados)."');</script>";
			
				    //TOMA LOS DATOS DEL DUPLICADO ACTUAL DE LA VISTA DE LA TABLA DEL INDEXADOR
				    $tipo_id_duplicado_actual=trim($duplicado_actual["campo_erc_de_numero_orden_4_tipo_id"]);
				    $numero_id_duplicado_actual=trim($duplicado_actual["campo_erc_de_numero_orden_5_numero_id"]);
				    
				    $lista_string_filas_donde_esta_duplicado=trim($duplicado_actual["lista_lineas_donde_hay_duplicados"]);
				    $array_filas_correspondientes_al_duplicado_actual=explode(";;",$lista_string_filas_donde_esta_duplicado);
				    $numero_filas_donde_esta_afiliado_actual=count($array_filas_correspondientes_al_duplicado_actual);
				    if($numero_filas_donde_esta_afiliado_actual>1)
				    {						    
					$numero_duplicados_procesados_hasta_el_momento+=$numero_filas_donde_esta_afiliado_actual;
				    }//fin if
				    //FIN TOMA LOS DATOS DEL DUPLICADO ACTUAL DE LA VISTA DE LA TABLA DEL INDEXADOR
				    
				    
				    
				    $bool_ya_se_proceso=false;
				    
				    //LEE EL ARCHIVO CORREGIDO PARA CADA LINEA Y LO SUBE A BD
				    foreach($array_filas_correspondientes_al_duplicado_actual as $numero_linea_dupl)
				    {
					//lee el archivo de texto en la linea especifica
					$linea_act = intval($numero_linea_dupl) ; 
					$fileHandler = new SplFileObject($ruta_archivo_reparado_ERC);		
					$fileHandler->seek($linea_act);
					$linea_duplicada_del_afiliado=$fileHandler->current();
					$array_campos_del_duplicado_del_afiliado=explode("\t",$linea_duplicada_del_afiliado);
					//fin lee el archivo de texto en la linea especifica
					
					//echo "<script>alert('entro ".count($resultados_query_erc2463_duplicados)."');</script>";
					
					//PARTE ESCRIBE LOG REPARACION DE DUPLICADOS PARTE 1
					//se abre con modo a para que adicione que no subio
					$file_cambios_duplicados_registro = fopen($ruta_cambios_duplicados_campos, "a") or die("fallo la creacion del archivo");
					
					$identificadores_de_cambios_duplicados_registro="";
					$identificadores_de_cambios_duplicados_registro.=$nombre_archivo_registrado."||";//nombre del archivo
					$identificadores_de_cambios_duplicados_registro.=$fecha_actual."||";//fecha correccion
					$identificadores_de_cambios_duplicados_registro.=$tiempo_actual."||";//hora correcion
					$identificadores_de_cambios_duplicados_registro.="DUPLICADO"."||";//identificador si es duplicado, unico, final
					$identificadores_de_cambios_duplicados_registro.=$fecha_de_corte."||";//fecha de corte
					$identificadores_de_cambios_duplicados_registro.="ERC"."||";//tipo reporte
					$identificadores_de_cambios_duplicados_registro.=$cod_eapb."||";
					$identificadores_de_cambios_duplicados_registro.=$cod_prestador."||";//codigo prestador del registro en el archivo
					$identificadores_de_cambios_duplicados_registro.="REPARACION"."||";//reparacion o consolidado
					if($cod_prestador=="AGRUP_EAPB")
					{
					    $identificadores_de_cambios_duplicados_registro.="AGRUPADO"."||";
					}
					else
					{
					    $identificadores_de_cambios_duplicados_registro.="DE PRESTADOR"."||";
					}
					$identificadores_de_cambios_duplicados_registro.=($linea_act+1)."||";//numero registro
					fwrite($file_cambios_duplicados_registro, $identificadores_de_cambios_duplicados_registro.$linea_duplicada_del_afiliado);
					
					//cierra el archivo del log reparacion de duplicados
					fclose($file_cambios_duplicados_registro);
					//FIN PARTE ESCRIBE LOG REPARACION DE DUPLICADOS PARTE 1
					
					//if mira que la linea contega los 119 campos
					if(count($array_campos_del_duplicado_del_afiliado)==119)
					{
						//echo "<script>alert('entro ".count($array_filas_correspondientes_al_duplicado_actual)."');</script>";
					    //if en caso de que solo haya un elemento en la lista de filas de duplicados
					    //se sube a corregidos sin duplicados debido a que si hay solo una fila
					    //es porque no posee duplicados
					    if($numero_filas_donde_esta_afiliado_actual==1)
					    {							    
						//se coloca que ya se proceso en verdadero debido a que no es un duplicado
						$bool_ya_se_proceso=true;
					    }//fin if solo habia una fila en la lista por ende no tenia duplicados
					    else if($numero_filas_donde_esta_afiliado_actual>1)
					    {
						//sube a gioss_temp_dupl_afiliado_actual_reparador_erc2463
						//para agrupar solo los registros duplicados para dicho afiliado
						$query_subir_registro_corregido="";
						$query_subir_registro_corregido.=" INSERT INTO ";
						$query_subir_registro_corregido.=" gioss_temp_dupl_afiliado_actual_reparador_erc2463 ";				
						$query_subir_registro_corregido.=" ( ";				
						$numero_actual_campo_registro_corregido=0;
						while($numero_actual_campo_registro_corregido<119)
						{
							$query_subir_registro_corregido.=" campo_erc_de_numero_orden_".$numero_actual_campo_registro_corregido." , ";
							$numero_actual_campo_registro_corregido++;
						}//fin while para nombres columnas de bd correspondientes a los campos de 2463 a insertar
						$query_subir_registro_corregido.=" tipo_id_usuario, ";
						$query_subir_registro_corregido.=" id_usuario, ";
						$query_subir_registro_corregido.=" nick_usuario, ";
						$query_subir_registro_corregido.=" numero_registro, ";
						$query_subir_registro_corregido.=" fecha_corte_reporte, ";
						$query_subir_registro_corregido.=" fecha_de_generacion, ";
						$query_subir_registro_corregido.=" hora_generacion, ";
						$query_subir_registro_corregido.=" codigo_entidad_eapb_generadora, ";
						$query_subir_registro_corregido.=" codigo_entidad_prestadora, ";
						$query_subir_registro_corregido.=" nombre_archivo_erc ";
						$query_subir_registro_corregido.=" ) ";
						$query_subir_registro_corregido.=" VALUES ";
						$query_subir_registro_corregido.=" ( ";				
						$numero_actual_campo_registro_corregido=0;
						while($numero_actual_campo_registro_corregido<119)
						{
						    if($numero_actual_campo_registro_corregido!=4 &&  $numero_actual_campo_registro_corregido!=5 )
						    {
							$query_subir_registro_corregido.="'".trim(procesar_mensaje3($array_campos_del_duplicado_del_afiliado[$numero_actual_campo_registro_corregido]))."',";								
						    }
						    else if($numero_actual_campo_registro_corregido==4)
						    {
							$query_subir_registro_corregido.="'".$tipo_id_duplicado_actual."',";
							
							$tipo_id_actual_del_array=trim(procesar_mensaje3($array_campos_del_duplicado_del_afiliado[$numero_actual_campo_registro_corregido]));
							if($tipo_id_actual_del_array!=$tipo_id_duplicado_actual)
							{
							    echo "<script>alert('numero registro: ".$linea_act." los tipo id son diferentes del array: $num_id_temp_del_array de la tabla indexador:$numero_id_duplicado_actual lista: $lista_string_filas_donde_esta_duplicado');</script>";
							}
						    
						    }
						    else if($numero_actual_campo_registro_corregido==5)
						    {
							$query_subir_registro_corregido.="'".$numero_id_duplicado_actual."',";
							
							$num_id_temp_del_array=trim(procesar_mensaje3($array_campos_del_duplicado_del_afiliado[$numero_actual_campo_registro_corregido]));
							if($num_id_temp_del_array!=$numero_id_duplicado_actual)
							{
							    echo "<script>alert('numero registro: ".$linea_act." los numero id son diferentes del array: $num_id_temp_del_array de la tabla indexador:$numero_id_duplicado_actual lista: $lista_string_filas_donde_esta_duplicado');</script>";
							}
						    }
						    
						    $numero_actual_campo_registro_corregido++;
						}//fin while con los valores de los campos a insertar en la tabla
						$query_subir_registro_corregido.="'".$tipo_id."',";
						$query_subir_registro_corregido.="'".$identificacion."',";
						$query_subir_registro_corregido.="'".$nick_user."',";
						//aqui  debe sumar + 1 debido a  que cont_linea inicializado en 0 se sube sumandol una posicion	
						$query_subir_registro_corregido.="'".($linea_act+1)."',";						
						$query_subir_registro_corregido.="'".$fecha_de_corte."',";
						$query_subir_registro_corregido.="'".$fecha_actual."',";
						$query_subir_registro_corregido.="'".$tiempo_actual."',";
						$query_subir_registro_corregido.="'".$cod_eapb."',";
						$query_subir_registro_corregido.="'".$cod_prestador."',";
						$query_subir_registro_corregido.="'".$nombre_archivo_registrado."'";
						$query_subir_registro_corregido.=" ) ";
						$query_subir_registro_corregido.=" ; ";
						$error_bd_seq="";		
						$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($query_subir_registro_corregido, $error_bd_seq);
						if($error_bd_seq!="")
						{
						    echo "<script>alert('ERROR al subir $tipo_id_duplicado_actual $numero_id_duplicado_actual, numero registro ".$linea_act."');</script>";
						    $mensajes_error_bd.=" ERROR Al subir en la tabla gioss_temp_dupl_afiliado_actual_reparador_erc2463 ".procesar_mensaje($error_bd_seq).".<br>";
							
						}
						//fin sube a gioss_temp_dupl_afiliado_actual_reparador_erc2463
						
						
						//sube a corregidos_solo_duplicados_erc2463 para reportes futuros
						$query_subir_registro_corregido="";
						$query_subir_registro_corregido.=" INSERT INTO ";
						$query_subir_registro_corregido.=" corregidos_solo_duplicados_erc2463 ";				
						$query_subir_registro_corregido.=" ( ";				
						$numero_actual_campo_registro_corregido=0;
						while($numero_actual_campo_registro_corregido<119)
						{
							$query_subir_registro_corregido.=" campo_erc_de_numero_orden_".$numero_actual_campo_registro_corregido." , ";
							$numero_actual_campo_registro_corregido++;
						}//fin while para nombres columnas de bd correspondientes a los campos  a insertar
						$query_subir_registro_corregido.=" tipo_id_usuario, ";
						$query_subir_registro_corregido.=" id_usuario, ";
						$query_subir_registro_corregido.=" nick_usuario, ";
						$query_subir_registro_corregido.=" numero_registro, ";
						$query_subir_registro_corregido.=" fecha_corte_reporte, ";
						$query_subir_registro_corregido.=" fecha_de_generacion, ";
						$query_subir_registro_corregido.=" hora_generacion, ";
						$query_subir_registro_corregido.=" codigo_entidad_eapb_generadora, ";
						$query_subir_registro_corregido.=" codigo_entidad_prestadora, ";
						$query_subir_registro_corregido.=" nombre_archivo_erc ";
						$query_subir_registro_corregido.=" ) ";
						$query_subir_registro_corregido.=" VALUES ";
						$query_subir_registro_corregido.=" ( ";				
						$numero_actual_campo_registro_corregido=0;
						while($numero_actual_campo_registro_corregido<119)
						{
							$query_subir_registro_corregido.="'".trim(procesar_mensaje3($array_campos_del_duplicado_del_afiliado[$numero_actual_campo_registro_corregido]))."',";
							$numero_actual_campo_registro_corregido++;
						}//fin while con los valores de los campos  a insertar en la tabla
						$query_subir_registro_corregido.="'".$tipo_id."',";
						$query_subir_registro_corregido.="'".$identificacion."',";
						$query_subir_registro_corregido.="'".$nick_user."',";	
						$query_subir_registro_corregido.="'".($linea_act+1)."',";							
						$query_subir_registro_corregido.="'".$fecha_de_corte."',";
						$query_subir_registro_corregido.="'".$fecha_actual."',";
						$query_subir_registro_corregido.="'".$tiempo_actual."',";
						$query_subir_registro_corregido.="'".$cod_eapb."',";
						$query_subir_registro_corregido.="'".$cod_prestador."',";
						$query_subir_registro_corregido.="'".$nombre_archivo_registrado."'";
						$query_subir_registro_corregido.=" ) ";
						$query_subir_registro_corregido.=" ; ";
						$error_bd_seq="";		
						$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($query_subir_registro_corregido, $error_bd_seq);
						if($error_bd_seq!="")
						{
						    echo "<script>alert('ERROR Al subir en la tabla corregidos_solo_duplicados_erc2463 ".procesar_mensaje($error_bd_seq)." ');</script>";
						    $mensajes_error_bd.=" ERROR Al subir en la tabla corregidos_solo_duplicados_erc2463 ".procesar_mensaje($error_bd_seq).".<br>";
							
						}
						//fin sube a corregidos_solo_duplicados_erc2463 para reportes futuros
						
						
					    }//fin else if si habian varias filas en la lista por ende tiene duplicados el afiliado 
					}//fin if si la linea posee 119 campos
					
				    }//fin foreach							
				    //FIN LEE EL ARCHIVO CORREGIDO PARA CADA LINEA Y LO SUBE A BD
				    
				    if($bool_ya_se_proceso==false)
				    {
					$nombre_vista_con_los_duplicados_del_afiliado_actual="duppa2463".$nombre_archivo_registrado.$nick_user.$fecha_y_hora_para_view;
					
					$sql_vista_duplicados_de_la_persona_actual ="";
					$sql_vista_duplicados_de_la_persona_actual.="CREATE OR REPLACE VIEW $nombre_vista_con_los_duplicados_del_afiliado_actual ";
					$sql_vista_duplicados_de_la_persona_actual.=" AS  ";					
					$sql_vista_duplicados_de_la_persona_actual .="SELECT * from gioss_temp_dupl_afiliado_actual_reparador_erc2463  ";	
					$sql_vista_duplicados_de_la_persona_actual.=" WHERE fecha_de_generacion='$fecha_actual'  AND hora_generacion='$tiempo_actual' ";
					$sql_vista_duplicados_de_la_persona_actual.=" AND ";
					$sql_vista_duplicados_de_la_persona_actual.=" codigo_entidad_prestadora='".$cod_prestador."'  ";
					$sql_vista_duplicados_de_la_persona_actual.=" AND ";
					$sql_vista_duplicados_de_la_persona_actual.=" codigo_entidad_eapb_generadora='".$cod_eapb."'  ";
					$sql_vista_duplicados_de_la_persona_actual.=" AND ";
					$sql_vista_duplicados_de_la_persona_actual.=" nombre_archivo_erc='".$nombre_archivo_registrado."'  ";
					$sql_vista_duplicados_de_la_persona_actual.=" AND ";
					$sql_vista_duplicados_de_la_persona_actual.=" tipo_id_usuario='$tipo_id' ";
					$sql_vista_duplicados_de_la_persona_actual.=" AND ";
					$sql_vista_duplicados_de_la_persona_actual.=" id_usuario='$identificacion' ";
					$sql_vista_duplicados_de_la_persona_actual.=" AND ";
					$sql_vista_duplicados_de_la_persona_actual.=" nick_usuario='$nick_user' ";
					$sql_vista_duplicados_de_la_persona_actual.=" AND ";
					$sql_vista_duplicados_de_la_persona_actual.=" campo_erc_de_numero_orden_4='$tipo_id_duplicado_actual' ";
					$sql_vista_duplicados_de_la_persona_actual.=" AND ";
					$sql_vista_duplicados_de_la_persona_actual.=" campo_erc_de_numero_orden_5='$numero_id_duplicado_actual' ";
					$sql_vista_duplicados_de_la_persona_actual.=" ORDER BY numero_registro asc ";
					$sql_vista_duplicados_de_la_persona_actual.=";";
					$error_bd_seq="";		
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_vista_duplicados_de_la_persona_actual, $error_bd_seq);
					if($error_bd_seq!="")
					{
						echo "<script>alert('ERROR al crear vista de duplicados de la persona actual para corregir en uno solo por persona ".procesar_mensaje($error_bd_seq)." ');</script>";
					    $mensajes_error_bd.=" ERROR al crear vista de duplicados de la persona actual para corregir en uno solo por persona: ".$error_bd_seq."<br>";
					}
					
					//numero de duplicados del duplicado
					$sql_numero_de_personas_de_duplicado="";
					$sql_numero_de_personas_de_duplicado.=" SELECT count(*) as numero_registros FROM $nombre_vista_con_los_duplicados_del_afiliado_actual  ; ";
					$error_bd_seq="";
					$array_numero_de_personas_de_duplicado=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($sql_numero_de_personas_de_duplicado, $error_bd_seq);
					if($error_bd_seq!="")
					{
						$mensajes_error_bd.=" ERROR al consultar numero de registros del total de los duplicados del duplicado: ".$error_bd_seq."<br>";
					}
					
					$numero_personas_de_duplicado=0;
					if(count($array_numero_de_personas_de_duplicado)>0 && is_array($array_numero_de_personas_de_duplicado))
					{
						$numero_personas_de_duplicado=$array_numero_de_personas_de_duplicado[0]["numero_registros"];
					}
					//fin numeros de duplicados del duplicado
					
					//PARTE DONDE LLAMA A LA FUNCION QUE CONTIENE LOS CRITERIOS PARA PROCESAR LOS DUPLICADOS
					//enves del numero de secuencia se usara el ultimo numero de registro(fila)
					$numero_registro_para_procesado="";
					$cod_prestador_para_procesado="";
					
					if($cod_prestador=="AGRUP_EAPB")
					{
						$cod_prestador_para_procesado="AGRUP_EAPB";
					}
					//en la funcion se hara falso si no se proceso los duplicados al haber campos vacios
					$bool_fueron_procesados_duplicados_en_un_registro=true;
					
					$array_campos_procesados_de_los_duplicados_del_duplicado=array();					
					$array_campos_procesados_de_los_duplicados_del_duplicado=reparacion_campos_duplicados($tipo_id_duplicado_actual,
															$numero_id_duplicado_actual,
															$fecha_actual,
															$tiempo_actual,
															$nick_user,
															$identificacion,
															$tipo_id,
															$numero_personas_de_duplicado,
															$nombre_vista_con_los_duplicados_del_afiliado_actual,
															$numero_registro_para_procesado,
															$cod_prestador_para_procesado,
															$bool_fueron_procesados_duplicados_en_un_registro,
															$contador_offset_personas,
															$mensajes_error_bd,
															$coneccionBD);
		    
					//insertando registro procesado
					if($bool_fueron_procesados_duplicados_en_un_registro==true)
					{						
						if(count($array_campos_procesados_de_los_duplicados_del_duplicado)!=119)
						{
							echo "<script>alert(' el numero de campos es incorrecto en el arreglo');</script>";
						}
						
						//CONVERSION CAMPOS A LINEA A RESULTANTE UNICO DE DUPLICADOS POS CORRECCION CRITERIOS
						$string_campos_procesados_de_los_duplicados_del_duplicado_pre_correccion="";
							    
						$cont_orden_campo_a_string=0;									
						while($cont_orden_campo_a_string<119)
						{
						    if($string_campos_procesados_de_los_duplicados_del_duplicado_pre_correccion!=""){$string_campos_procesados_de_los_duplicados_del_duplicado_pre_correccion.="\t";}
						    $string_campos_procesados_de_los_duplicados_del_duplicado_pre_correccion.=$array_campos_procesados_de_los_duplicados_del_duplicado[$cont_orden_campo_a_string];
						    $cont_orden_campo_a_string++;
						}//fin while
						//FIN CONVERSION CAMPOS A LINEA A RESULTANTE UNICO DE DUPLICADOS POS CORRECCION CRITERIOS
						
						$array_pre_campos=array();
						$array_pos_campos=array();

						$array_pre_campos=$array_campos_procesados_de_los_duplicados_del_duplicado;
						
						//pasar corrector aca
						$array_errores=array();
						//PD: el array con los campos pasa por referencia por lo tanto si se necesitan lso campos no necesita el return, el return devuelve
						// los mensajes de error no los campos
						$array_errores=reparacion_campo_en_blanco_ERC($array_campos_procesados_de_los_duplicados_del_duplicado,
											$cod_eapb,
											 $numero_registro_para_procesado,
											 $consecutivo_errores,
											 $array_tipo_validacion,
											 $array_grupo_validacion,
											 $array_detalle_validacion,
											 $nombre_archivo_registrado,
											 $fecha_de_corte,
											 $cod_prestador,
											 $cod_eapb,
											 $diccionario_identificacion,
											 $diccionario_identificacion_lineas,
											 $coneccionBD);
						
						$array_errores=reparacion_formato_ERC($array_campos_procesados_de_los_duplicados_del_duplicado,
											 $numero_registro_para_procesado,
											 $consecutivo_errores,
											 $array_tipo_validacion,
											 $array_grupo_validacion,
											 $array_detalle_validacion,
											 $nombre_archivo_registrado,
											 $fecha_de_corte,
											 $cod_prestador,
											 $cod_eapb,
											 $diccionario_identificacion,
											 $diccionario_identificacion_lineas,
											 $coneccionBD);
						
						$array_errores=reparacion_valor_permitido_ERC($array_campos_procesados_de_los_duplicados_del_duplicado,
											 $numero_registro_para_procesado,
											 $consecutivo_errores,
											 $array_tipo_validacion,
											 $array_grupo_validacion,
											 $array_detalle_validacion,
											 $nombre_archivo_registrado,
											 $fecha_de_corte,
											 $cod_prestador,
											 $cod_eapb,
											 $diccionario_identificacion,
											 $diccionario_identificacion_lineas,
											 $coneccionBD);
						
						$array_errores=reparacion_criterios_de_calidad_ERC($array_campos_procesados_de_los_duplicados_del_duplicado,
											 $numero_registro_para_procesado,
											 $consecutivo_errores,
											 $array_tipo_validacion,
											 $array_grupo_validacion,
											 $array_detalle_validacion,
											 $nombre_archivo_registrado,
											 $fecha_de_corte,
											 $cod_prestador,
											 $cod_eapb,
											 $diccionario_identificacion,
											 $diccionario_identificacion_lineas,
											 $coneccionBD);
								
						//fin pasar corrector aca
						
						$array_pos_campos=$array_campos_procesados_de_los_duplicados_del_duplicado;

						//PARTE escribe segundo log
						if(count($array_pre_campos)==count($array_pos_campos) )
						{
							$cont_comparacion_antes_despues=0;
							while ($cont_comparacion_antes_despues<count($array_pre_campos) )
							{
								$antes=$array_pre_campos[$cont_comparacion_antes_despues];
								$despues=$array_pos_campos[$cont_comparacion_antes_despues];
								if($antes!=$despues)
								{

									$numero_campo_actual_de_acuerdo_norma=$array_numero_campo_bd[$cont_comparacion_antes_despues];
									$linea_a_escribir_log="El campo numero ( $numero_campo_actual_de_acuerdo_norma ) tenia el valor ( $antes )  y fue cambiado por el valor ( $despues ) en el registro U-$numero_registro_para_procesado ";
									
									$file_cambios_campos_correccion_norma_pos= fopen($ruta_cambios_campos_correccion_norma_pos, "a") or die("fallo la creacion del archivo");
									fwrite($file_cambios_campos_correccion_norma_pos, "\n".$linea_a_escribir_log); 						  
									fclose($file_cambios_campos_correccion_norma_pos);
								}//fin if
								$cont_comparacion_antes_despues++;
							}//fin while
						}//fin if
						//FIN PARTE escribe segundo log
						
						//PARTE ESCRIBE LOG REPARACION DE DUPLICADOS PARTE 2
						
						//conversion campos a linea a resultante unico de duplicados pos correccion criterios
						$linea_duplicado_resultante_pos_reparacion="";
							    
						$cont_orden_campo_a_string=0;									
						while($cont_orden_campo_a_string<119)
						{
						    if($linea_duplicado_resultante_pos_reparacion!=""){$linea_duplicado_resultante_pos_reparacion.="\t";}
						    $linea_duplicado_resultante_pos_reparacion.=$array_campos_procesados_de_los_duplicados_del_duplicado[$cont_orden_campo_a_string];
						    $cont_orden_campo_a_string++;
						}//fin while
						//fin conversion campos a linea a resultante unico de duplicados pos correccion criterios
						
						//se abre con modo a para que adicione que no subio
						$file_cambios_duplicados_registro = fopen($ruta_cambios_duplicados_campos, "a") or die("fallo la creacion del archivo");
						
						$identificadores_de_cambios_duplicados_registro="";
						$identificadores_de_cambios_duplicados_registro.=$nombre_archivo_registrado."||";//nombre del archivo
						$identificadores_de_cambios_duplicados_registro.=$fecha_actual."||";//fecha correccion
						$identificadores_de_cambios_duplicados_registro.=$tiempo_actual."||";//hora correcion
						$identificadores_de_cambios_duplicados_registro.="--UNICO--"."||";//identificador si es duplicado, unico, final
						$identificadores_de_cambios_duplicados_registro.=$fecha_de_corte."||";//fecha de corte
						$identificadores_de_cambios_duplicados_registro.="ERC"."||";//tipo reporte
						$identificadores_de_cambios_duplicados_registro.=$cod_eapb."||";							    
						$identificadores_de_cambios_duplicados_registro.=$cod_prestador."||";//codigo prestador del registro en el archivo
						$identificadores_de_cambios_duplicados_registro.="REPARACION"."||";//reparacion o consolidado
						if($cod_prestador=="AGRUP_EAPB")
						{
						    $identificadores_de_cambios_duplicados_registro.="AGRUPADO"."||";
						}
						else
						{
						    $identificadores_de_cambios_duplicados_registro.="DE PRESTADOR"."||";
						}
						$identificadores_de_cambios_duplicados_registro.="U".$numero_registro_para_procesado."||";
						fwrite($file_cambios_duplicados_registro, $identificadores_de_cambios_duplicados_registro.$string_campos_procesados_de_los_duplicados_del_duplicado_pre_correccion);
						
						
						//se abre con modo a para que adicione que no subio
						$file_cambios_duplicados_registro = fopen($ruta_cambios_duplicados_campos, "a") or die("fallo la creacion del archivo");
						
						$identificadores_de_cambios_duplicados_registro="";
						$identificadores_de_cambios_duplicados_registro.=$nombre_archivo_registrado."||";//nombre del archivo
						$identificadores_de_cambios_duplicados_registro.=$fecha_actual."||";//fecha correccion
						$identificadores_de_cambios_duplicados_registro.=$tiempo_actual."||";//hora correcion
						$identificadores_de_cambios_duplicados_registro.="--FINAL--"."||";//identificador si es duplicado, unico, final
						$identificadores_de_cambios_duplicados_registro.=$fecha_de_corte."||";//fecha de corte
						$identificadores_de_cambios_duplicados_registro.="ERC"."||";//tipo reporte
						$identificadores_de_cambios_duplicados_registro.=$cod_eapb."||";							    
						$identificadores_de_cambios_duplicados_registro.=$cod_prestador."||";//codigo prestador del registro en el archivo
						$identificadores_de_cambios_duplicados_registro.="REPARACION"."||";//reparacion o consolidado
						if($cod_prestador=="AGRUP_EAPB")
						{
						    $identificadores_de_cambios_duplicados_registro.="AGRUPADO"."||";
						}
						else
						{
						    $identificadores_de_cambios_duplicados_registro.="DE PRESTADOR"."||";
						}
						$identificadores_de_cambios_duplicados_registro.="F".$numero_registro_para_procesado."||";
						fwrite($file_cambios_duplicados_registro, "\n".$identificadores_de_cambios_duplicados_registro.$linea_duplicado_resultante_pos_reparacion."\n");
						
						/*
						if($string_campos_procesados_de_los_duplicados_del_duplicado_pre_correccion=="")
						{
						    echo "<script>alert('esta vacio');</script>";
						}
						else
						{
						    echo "<script>alert('$string_campos_procesados_de_los_duplicados_del_duplicado_pre_correccion');</script>";
						}							    
						*/
						
						//cierra el archivo del log reparacion de duplicados
						fclose($file_cambios_duplicados_registro);
						//FIN PARTE ESCRIBE LOG REPARACION DE DUPLICADOS PARTE 2
						
						$sql_insert_procesado_en_reporte_obligatorio="";
						$sql_insert_procesado_en_reporte_obligatorio.=" INSERT INTO ";
						$sql_insert_procesado_en_reporte_obligatorio.=" corregidos_sin_duplicados_erc2463 ";									    
						$sql_insert_procesado_en_reporte_obligatorio.=" ( ";				
						$cont_orden_campo_erc=0;
						while($cont_orden_campo_erc<119)
						{
							$sql_insert_procesado_en_reporte_obligatorio.=" campo_erc_de_numero_orden_".$cont_orden_campo_erc." , ";
							$cont_orden_campo_erc++;
						}//fin while para nombres columnas de bd correspondientes a los campos de 2463 a insertar
						$sql_insert_procesado_en_reporte_obligatorio.=" tipo_id_usuario, ";
						$sql_insert_procesado_en_reporte_obligatorio.=" id_usuario, ";
						$sql_insert_procesado_en_reporte_obligatorio.=" nick_usuario, ";
						$sql_insert_procesado_en_reporte_obligatorio.=" numero_registro, ";
						$sql_insert_procesado_en_reporte_obligatorio.=" fecha_corte_reporte, ";
						$sql_insert_procesado_en_reporte_obligatorio.=" fecha_de_generacion, ";
						$sql_insert_procesado_en_reporte_obligatorio.=" hora_generacion, ";
						$sql_insert_procesado_en_reporte_obligatorio.=" codigo_entidad_eapb_generadora, ";
						$sql_insert_procesado_en_reporte_obligatorio.=" codigo_entidad_prestadora, ";
						$sql_insert_procesado_en_reporte_obligatorio.=" nombre_archivo_erc ";
						$sql_insert_procesado_en_reporte_obligatorio.=" ) ";
						$sql_insert_procesado_en_reporte_obligatorio.=" VALUES ";
						$sql_insert_procesado_en_reporte_obligatorio.=" ( ";
						//aqui si viene desde cero porque es el campo procesado
						$cont_orden_campo_erc=0;
						while($cont_orden_campo_erc<119)
						{
							$sql_insert_procesado_en_reporte_obligatorio.="'".$array_campos_procesados_de_los_duplicados_del_duplicado[$cont_orden_campo_erc]."',";
							$cont_orden_campo_erc++;
						}//fin while con los valores de los campos 2463 a insertar en la tabla de reporte obligatorio
						$sql_insert_procesado_en_reporte_obligatorio.="'".$tipo_id."',";
						$sql_insert_procesado_en_reporte_obligatorio.="'".$identificacion."',";
						$sql_insert_procesado_en_reporte_obligatorio.="'".$nick_user."',";
						$sql_insert_procesado_en_reporte_obligatorio.="'".$numero_registro_para_procesado."',";								
						$sql_insert_procesado_en_reporte_obligatorio.="'".$fecha_de_corte."',";
						$sql_insert_procesado_en_reporte_obligatorio.="'".$fecha_actual."',";
						$sql_insert_procesado_en_reporte_obligatorio.="'".$tiempo_actual."',";
						$sql_insert_procesado_en_reporte_obligatorio.="'".$cod_eapb."',";
						$sql_insert_procesado_en_reporte_obligatorio.="'".$cod_prestador."',";
						$sql_insert_procesado_en_reporte_obligatorio.="'".$nombre_archivo_registrado."'";
						$sql_insert_procesado_en_reporte_obligatorio.=" ) ";
						$sql_insert_procesado_en_reporte_obligatorio.=" ; ";
						$error_bd_seq="";		
						$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_insert_procesado_en_reporte_obligatorio, $error_bd_seq);
						if($error_bd_seq!="")
						{
							$mensajes_error_bd.=" ERROR Al subir en la tabla reporte obligatorio: ".$error_bd_seq."<br>";
						}
					}//fin if si fueron procesados duplicados inserta el porcesado en la tabla de archivos reportados obligatorios exitosos de 2463
					//fin insertando registro procesado
								
								
					    
					//BORRANDO VISTA DE LA TABLA TEMPORAL CON LOS DUPLICADOS DEL AFILIADO
					$sql_borrar_vista_duplicados_en_uno_solo="";
					$sql_borrar_vista_duplicados_en_uno_solo.=" DROP VIEW $nombre_vista_con_los_duplicados_del_afiliado_actual ; ";							
					$error_bd="";		
					$bool_funciono=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_borrar_vista_duplicados_en_uno_solo, $error_bd);		
					if($error_bd!="")
					{
					    if(connection_aborted()==false)
					    {
						echo "<script>alert('error al borrar la vista duplicados del afiliado actual');</script>";
					    }
						$mensajes_error_bd.=" ERROR Al al borrar la vista duplicados en uno solo: ".$error_bd."<br>";
					}
					//FIN BORRANDO VISTA DE LA TABLA TEMPORAL CON LOS DUPLICADOS DEL AFILIADO
					    
					//BORRANDO INFORMACION DE LA TABLA TEMPORAL CON LOS DUPLICADOS DEL AFILIADO
					$sql_delete_corregidos_temp="";
					$sql_delete_corregidos_temp.=" DELETE FROM gioss_temp_dupl_afiliado_actual_reparador_erc2463  ";
					$sql_delete_corregidos_temp.=" WHERE fecha_de_generacion='$fecha_actual'  AND hora_generacion='$tiempo_actual' ";
					$sql_delete_corregidos_temp.=" AND ";
					$sql_delete_corregidos_temp.=" codigo_entidad_prestadora='".$cod_prestador."'  ";
					$sql_delete_corregidos_temp.=" AND ";
					$sql_delete_corregidos_temp.=" codigo_entidad_eapb_generadora='".$cod_eapb."'  ";
					$sql_delete_corregidos_temp.=" AND ";
					$sql_delete_corregidos_temp.=" nombre_archivo_erc='".$nombre_archivo_registrado."'  ";
					$sql_delete_corregidos_temp.=" AND ";
					$sql_delete_corregidos_temp.=" tipo_id_usuario='$tipo_id' ";
					$sql_delete_corregidos_temp.=" AND ";
					$sql_delete_corregidos_temp.=" id_usuario='$identificacion' ";
					$sql_delete_corregidos_temp.=" AND ";
					$sql_delete_corregidos_temp.=" nick_usuario='$nick_user' ";
					$sql_delete_corregidos_temp.=" ; ";
					$error_bd_seq="";		
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_delete_corregidos_temp, $error_bd_seq);
					if($error_bd_seq!="")
					{
						$mensajes_error_bd.=" ERROR Al eliminar en la tabla temporal de registros corregidos pre correccion duplicados  para corrector ".procesar_mensaje($error_bd_seq).".<br>";
						
					}
					//FIN BORRANDO INFORMACION DE LA TABLA TEMPORAL CON LOS DUPLICADOS DEL AFILIADO
				    }//fin if si el duplicado no se ha procesado
					
					//porcentaje
					$muestra_mensaje_nuevo_dupl=false;
					$porcentaje_dupl=intval((($numero_duplicados_procesados_hasta_el_momento)*100)/($acumulador_para_contar_duplicados));
					if($porcentaje_dupl!=$cont_porcentaje_dupl || ($porcentaje_dupl==0 && ($numero_duplicados_procesados_hasta_el_momento)==1) || $porcentaje_dupl==100)
					{
					 $cont_porcentaje_dupl=$porcentaje_dupl;
					 $muestra_mensaje_nuevo_dupl=true;
					 //echo "<script>alert('entro a mostrar porcentaje  $porcentaje_dupl ');</script>";
					}
					//fin porcentaje
					
					
					//ACTUALIZA ESTADO DEL ARCHIVO
					$mensaje_estado_registros_temp_dupl="<span style=color:red>Por favor espere, se han arreglado $numero_duplicados_procesados_hasta_el_momento duplicados para un total de $acumulador_para_contar_duplicados duplicados;  $porcentaje_dupl % .</span><br>";
										
					$mensaje_perm_estado_reg_dupl=$mensaje_estado_registros_temp_dupl;
					
					$msg_a_bd="";
					$msg_a_bd=$mensaje_perm_estado." ".$mensaje_perm_estado_reg_dupl;
					
					if($muestra_mensaje_nuevo_dupl==true)
					{
						//echo "<script>alert('$msg_a_bd');</script>";
						$query_update_esta_siendo_procesado="";
						$query_update_esta_siendo_procesado.=" UPDATE gioss_2463_esta_reparando_ar_actualmente ";
						$query_update_esta_siendo_procesado.=" SET mensaje_estado_registros='$msg_a_bd' ";
						$query_update_esta_siendo_procesado.=" WHERE fecha_corte_archivo_en_reparacion='".$fecha_de_corte."' ";
						$query_update_esta_siendo_procesado.=" AND codigo_entidad_reportadora='".$cod_prestador."' ";
						$query_update_esta_siendo_procesado.=" AND nombre_archivo='".$nombre_archivo_registrado."'  ";
						$query_update_esta_siendo_procesado.=" AND nick_usuario='".$nick_user."'  ";
						$query_update_esta_siendo_procesado.=" AND fecha_validacion='".$fecha_actual."'  ";
						$query_update_esta_siendo_procesado.=" AND hora_validacion='".$tiempo_actual."'  ";
						$query_update_esta_siendo_procesado.=" ; ";
						$error_bd="";
						$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($query_update_esta_siendo_procesado, $error_bd);
						if($error_bd!="")
						{
							if($fue_cerrada_la_gui2==false)
							{
							    echo "<script>alert('error al actualizar el estado actual de reparacion en tiempo real  2463 ');</script>";
							}
						}
						else
						{
						    //echo "<script>alert('$mensaje_perm_estado_reg_dupl');</script>";
						}
						
						//CANCELA EJECUCION DEL ARCHIVO			    
						$verificar_si_ejecucion_fue_cancelada="";
						$verificar_si_ejecucion_fue_cancelada.=" SELECT esta_ejecutando FROM gioss_2463_esta_reparando_ar_actualmente ";
						$verificar_si_ejecucion_fue_cancelada.=" WHERE fecha_corte_archivo_en_reparacion='".$fecha_de_corte."' ";
						$verificar_si_ejecucion_fue_cancelada.=" AND codigo_entidad_reportadora='".$cod_prestador."' ";	    
						$verificar_si_ejecucion_fue_cancelada.=" AND nombre_archivo='".$nombre_archivo_registrado."'  ";
						$verificar_si_ejecucion_fue_cancelada.=" AND nick_usuario='".$nick_user."'  ";
						$verificar_si_ejecucion_fue_cancelada.=" AND fecha_validacion='".$fecha_actual."'  ";
						$verificar_si_ejecucion_fue_cancelada.=" AND hora_validacion='".$tiempo_actual."'  ";
						$verificar_si_ejecucion_fue_cancelada.=" ; ";
						$error_bd="";
						$resultados_si_ejecucion_fue_cancelada=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($verificar_si_ejecucion_fue_cancelada, $error_bd);
						if($error_bd!="")
						{
							if(connection_aborted()==false)
							{
								echo "<script>alert('error al consultar si se cancelo la ejecucion ');</script>";
							}
						}
						
						if(count($resultados_si_ejecucion_fue_cancelada)>0 && is_array($resultados_si_ejecucion_fue_cancelada))
						{
						    $esta_ejecutando=$resultados_si_ejecucion_fue_cancelada[0]["esta_ejecutando"];
						    if($esta_ejecutando=="NO")
						    {
							exit(0);
						    }
						}
						//FIN CANCELA EJECUCION DEL ARCHIVO
					}//fin if
					//FIN ACTUALIZA ESTADO DEL ARCHIVO
					
					
					if($fue_cerrada_la_gui2==false && $muestra_mensaje_nuevo_dupl)
					{
						echo "<script>document.getElementById('estado_validacion').innerHTML='$mensaje_perm_estado $mensaje_perm_estado_reg_dupl';</script>";
						ob_flush();
						flush();
					}
				}//fin foreach trae bloques de personas
			}//fin if hay resultados
			
			
			
			//incremento contador
			$contador_offset_personas+=$numero_registros_bloque_personas;
		    }//fin while
		}//fin if si hay archivos duplicados
		
		//BORRANDO VISTAS
		$sql_borrar_vista_duplicados_en_uno_solo="";
		$sql_borrar_vista_duplicados_en_uno_solo.=" DROP VIEW $nombre_vista_index_duplicados ; ";							
		$error_bd="";		
		$bool_funciono=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_borrar_vista_duplicados_en_uno_solo, $error_bd);		
		if($error_bd!="")
		{
			if(connection_aborted()==false)
			{
			    echo "<script>alert('error al borrar la vista duplicados en uno solo');</script>";
			}
			$mensajes_error_bd.=" ERROR Al al borrar la vista duplicados en uno solo: ".$error_bd."<br>";
		}
		//FIN BORRANDO VISTAS
		
		//FIN ARREGLO DE DUPLICADOS EN UNO SOLO
		
		
		//PARTE ESCRIBE CSV
		$nombre_vista_consulta_definitiva_corregidos="vcroerc2463_".$nick_user."_".$tipo_id."_".$identificacion;
		
		$numero_registros_bloque=1000;
		$sql_vista_consulta_reporte_obligatorio="";
		$sql_vista_consulta_reporte_obligatorio.="CREATE OR REPLACE VIEW $nombre_vista_consulta_definitiva_corregidos ";
		$sql_vista_consulta_reporte_obligatorio.=" AS SELECT * FROM corregidos_sin_duplicados_erc2463 ";
		$sql_vista_consulta_reporte_obligatorio.=" WHERE ";
		$sql_vista_consulta_reporte_obligatorio.=" fecha_de_generacion='$fecha_actual'  AND hora_generacion='$tiempo_actual' ";
		$sql_vista_consulta_reporte_obligatorio.=" AND ";
		$sql_vista_consulta_reporte_obligatorio.=" codigo_entidad_eapb_generadora='".$cod_eapb."'  ";
		$sql_vista_consulta_reporte_obligatorio.=" AND ";
		$sql_vista_consulta_reporte_obligatorio.=" nombre_archivo_erc='".$nombre_archivo_registrado."'  ";
		$sql_vista_consulta_reporte_obligatorio.=" AND ";
		$sql_vista_consulta_reporte_obligatorio.=" tipo_id_usuario='$tipo_id' ";
		$sql_vista_consulta_reporte_obligatorio.=" AND ";
		$sql_vista_consulta_reporte_obligatorio.=" id_usuario='$identificacion' ";
		$sql_vista_consulta_reporte_obligatorio.=" AND ";
		$sql_vista_consulta_reporte_obligatorio.=" nick_usuario='$nick_user' ";
		$sql_vista_consulta_reporte_obligatorio.=" ORDER BY numero_registro asc ; ";
		$error_bd_seq="";
		$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_vista_consulta_reporte_obligatorio, $error_bd_seq);
		if($error_bd_seq!="")
		{
			$mensajes_error_bd.=$error_bd_seq."<br>";
		}
		
		$sql_numero_registros="";
		$sql_numero_registros.="SELECT count(*) as contador FROM $nombre_vista_consulta_definitiva_corregidos;  ";		
		$error_bd_seq="";
		$resultado_query_numero_registros=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($sql_numero_registros,$error_bd_seq);
		if($error_bd_seq!="")
		{
			$mensajes_error_bd.="ERROR AL CONSULTAR numero registros de vista_consulta: ".$error_bd_seq."<br>";
		}
		$numero_registros=intval($resultado_query_numero_registros[0]["contador"]);
		if($numero_registros==0)
		{
			$mensajes_error_bd.="No hay registros a consultar. <br> ";
		}
		
		
		$cont_linea=1;
		$contador_offset=0;
		$limite=0;
		$flag_para_salto_linea_inicial=false;
		$cont_resultados=1;	
		while($contador_offset<$numero_registros)
		{
			$limite=$numero_registros_bloque;
			
			if( ($contador_offset+$numero_registros_bloque)>=$numero_registros)
			{
				$limite=$numero_registros_bloque+($numero_registros-$contador_offset);
			}
		
			$sql_query_busqueda="";
			$sql_query_busqueda.="SELECT * FROM $nombre_vista_consulta_definitiva_corregidos LIMIT $limite OFFSET $contador_offset;  ";
			$error_bd_seq="";
			$resultado_query_reporte_obligatoria=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($sql_query_busqueda,$error_bd_seq);			
			if($error_bd_seq!="")
			{
				$mensajes_error_bd.="ERROR AL CONSULTAR de vista_consulta: ".$error_bd_seq."<br>";
			}
			
			if(count($resultado_query_reporte_obligatoria)>0)
			{
				
				
				$file_reparado_r2463_sin_dupl_ERC= fopen($ruta_archivo_reparado_sin_duplicados_ERC, "a") or die("fallo la creacion del archivo");
						    
				foreach($resultado_query_reporte_obligatoria as $resultado)
				{
					$cadena_escribir_linea="";
					$cont_orden_campo_erc=0;
					while($cont_orden_campo_erc<119)
					{
						if($cadena_escribir_linea!="")
						{
							$cadena_escribir_linea.="\t";
						}
						
						/*
						//el -777 indica un campo en blanco, si no hay escribe el valor del campo
						if($resultado["campo_erc_de_numero_orden_".$cont_orden_campo_erc]!="-777")
						{
							$cadena_escribir_linea.=$resultado["campo_erc_de_numero_orden_".$cont_orden_campo_erc];
						}
						else
						{
							$cadena_escribir_linea.="";
						}
						//fin el -777 indica un campo en blanco, si no hay escribe el valor del campo
						*/
						
						$cadena_escribir_linea.=$resultado["campo_erc_de_numero_orden_".$cont_orden_campo_erc];
						
						$cont_orden_campo_erc++;
					}
					if($flag_para_salto_linea_inicial==false)
					{
						fwrite($file_reparado_r2463_sin_dupl_ERC, $cadena_escribir_linea);
						$flag_para_salto_linea_inicial=true;
					}
					else
					{
						fwrite($file_reparado_r2463_sin_dupl_ERC, "\n".$cadena_escribir_linea);
					}
					
					
					//porcentaje
					$muestra_mensaje_nuevo_csv=false;
					$porcentaje_csv=intval((($cont_linea)*100)/($numero_registros));
					if($porcentaje_csv!=$cont_porcentaje_csv || ($porcentaje_csv==0 && ($cont_linea)==1) || $porcentaje_csv==100)
					{
					 $cont_porcentaje_csv=$porcentaje_csv;
					 $muestra_mensaje_nuevo_csv=true;
					}
					//fin porcentaje
					
					if(connection_aborted()==false && $muestra_mensaje_nuevo_csv)
					{
						$mensaje_perm_estado_reg_recuperados="";
						$mensaje_perm_estado_reg_recuperados.="Por favor espere, $cont_linea registros recuperados de $numero_registros.<br>";
						echo "<script>document.getElementById('estado_validacion').innerHTML='$mensaje_perm_estado $mensaje_perm_estado_reg_recuperados ';</script>";
						ob_flush();
						flush();
					}
					$cont_resultados++;
					$cont_linea++;
				}//fin foreach
				fclose($file_reparado_r2463_sin_dupl_ERC);
				
				
				
			}//fin if hayo resultados
			
			$contador_offset+=$numero_registros_bloque;
		
		}//fin while
		//FIN PARTE ESCRIBE CSV
	    
		//FIN FASE DE VERIFICACION Y CORRECCION DE DUPLICADOS
		
		//BORRANDO VISTAS
		$sql_borrar_vistas="";
		//$sql_borrar_vistas.=" DROP VIEW $nombre_vista_corregidos_con_duplicados ; ";
		$sql_borrar_vistas.=" DROP VIEW $nombre_vista_consulta_definitiva_corregidos ; ";		    
		$error_bd="";		
		$bool_funciono=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_borrar_vistas, $error_bd);		
		if($error_bd!="")
		{
			if(connection_aborted()==false)
			{
				echo "<script>alert('error al borrar vistas');</script>";
			}
		}
		//FIN BORRANDO VISTAS
		
		//TERMINA
		if($es_valido_nombre_archivo)
		{
			
			if(connection_aborted()==false)
			{
				
				if($mensaje_perm_estado_reg_dupl=="")
				{
					echo "<script>alert('No hubo duplicados');</script>";
				}
				echo "<script>document.getElementById('mensaje').style.textAlign='center';</script>";
				echo "<script>document.getElementById('mensaje').innerHTML='$mensaje_perm_estado $mensaje_perm_estado_reg_dupl $mensaje_perm_estado_reg_recuperados Se ha terminado de corregir ERC<br> lineas con numero de campos incorrectos, que no permitieron corregir: $numero_lineas_campos_incorrectos';</script>";
				
				echo "<script>document.getElementById('tabla_estado_1').style.position='relative';</script>";
				ob_flush();
				flush();
			}
			$errores.=procesar_mensaje2($mensajes_error_bd);
			$errores.=procesar_mensaje2($error_mostrar_bd);
		
		}//fin if nombre archivo valido
		
		if($hubo_inconsistencias_en_ERC)
		{
			$se_genero_archivo_de_inconsistencias=true;		
			$errores.="Se corrigieron los errores a corregir en el archivo ERC.<br>";
			
		}
		
		//CREAR ZIP
		$archivos_a_comprimir=array();		
		//$archivos_a_comprimir[]=$ruta_archivo_inconsistencias_ERC;
		$archivos_a_comprimir[]=$ruta_archivo_reparado_ERC;
		$archivos_a_comprimir[]=$ruta_archivo_reparado_sin_duplicados_ERC;
		$archivos_a_comprimir[]=$ruta_archivo_excluido_ERC;
		$archivos_a_comprimir[]=$ruta_cambios_duplicados_campos;
		$archivos_a_comprimir[]=$ruta_cambios_campos_correccion_norma_pre;//agregado 03 10 2017
		$archivos_a_comprimir[]=$ruta_cambios_campos_correccion_norma_pos;//agregado 03 10 2017
		$archivos_a_comprimir[]=$ruta_archivo_afiliado_no_existe;//agregado 03 10 2017
		$archivos_a_comprimir[]=$ruta_archivo_afiliado_existe_cambio_sexo;//agregado 03 10 2017
		$archivos_a_comprimir[]=$ruta_archivo_afiliado_existe_cambio_fecha_nacimiento;//agregado 03 10 2017
		$archivos_a_comprimir[]=$ruta_archivo_registros_excluidos_no_afiliados;//agregado 03 10 2017
		$archivos_a_comprimir[]=$ruta_archivo_registros_excluidos_fecha_nacimiento_invalida;//agregado 03 10 2017
		$ruta_zip=$rutaTemporal."reparado2463ERC_".$cod_prestador."_".$fecha_actual."_".$tiempo_actual_string.'.zip';
		if(file_exists($ruta_zip))
		{
			unlink($ruta_zip);
		}
		$result_zip = create_zip($archivos_a_comprimir,$ruta_zip);	
		//FIN CREAR ZIP
		
		
		
		//BOTONES DESCARGA
		$botones="";
		$botones.=" <input type=\'button\' value=\'Descargar archivo reparado para ERC\'  class=\'btn btn-success color_boton\' onclick=\"download_inconsistencias_campos(\'$ruta_zip\');\"/> ";
		
		//FIN BOTONES DESCARGA
		
		//ESCRIBE LOS MENSAJES AL FINALIZAR LA VALIDACION
		if($errores!="")
		{
			if(connection_aborted()==false)
			{
				echo "<script>document.getElementById('div_mensaje_error').style.display='inline';</script>";
				echo "<script>document.getElementById('parrafo_mensaje_error').innerHTML='$errores <br>  $botones';</script>";
				ob_flush();
				flush();
			}
		}
		else
		{
			if(connection_aborted()==false)
			{
				echo "<script>document.getElementById('div_mensaje_exito').style.display='inline';</script>";
				echo "<script>document.getElementById('parrafo_mensaje_exito').innerHTML='$exitos <br>  $botones';</script>";
				ob_flush();
				flush();
			}
		}
		//FIN ESCRIBE MENSAJES FINALES
		
		//SUBE A GIOSS_LOG_DUPL PARA REPORTES FUTUROS
		    
		//borra el anterior
		$query_delete_log_dupl_anterior="";
		$query_delete_log_dupl_anterior.=" DELETE FROM ";
		$query_delete_log_dupl_anterior.=" gioss_log_dupl ";				
		$query_delete_log_dupl_anterior.=" WHERE ";
		$query_delete_log_dupl_anterior.=" tipo_id_usuario='".$tipo_id."'  ";
		$query_delete_log_dupl_anterior.=" AND ";
		$query_delete_log_dupl_anterior.=" id_usuario='".$identificacion."' ";
		$query_delete_log_dupl_anterior.=" AND ";
		$query_delete_log_dupl_anterior.=" nick_usuario='".$nick_user."' ";    
		$query_delete_log_dupl_anterior.=" AND ";
		$query_delete_log_dupl_anterior.=" fecha_corte_reporte='".$fecha_de_corte."' ";    
		$query_delete_log_dupl_anterior.=" AND ";
		$query_delete_log_dupl_anterior.=" codigo_entidad_eapb_generadora='".$cod_eapb."' ";
		$query_delete_log_dupl_anterior.=" AND ";
		$query_delete_log_dupl_anterior.=" tipo_reporte='ERC' ";
		$query_delete_log_dupl_anterior.=" AND ";
		$query_delete_log_dupl_anterior.=" reparacion_o_consolidado='REPARACION' ";
		if($cod_prestador=="AGRUP_EAPB")
		{
		    $query_delete_log_dupl_anterior.=" AND ";
		    $query_delete_log_dupl_anterior.=" agrupado_o_prestador='AGRUPADO' ";
		}
		else
		{
		    $query_delete_log_dupl_anterior.=" AND ";
		    $query_delete_log_dupl_anterior.=" agrupado_o_prestador='DE PRESTADOR' ";
		}
		$query_delete_log_dupl_anterior.=" AND ";
		$query_delete_log_dupl_anterior.=" nombre_archivo='".$nombre_archivo_registrado."' ";
		$query_delete_log_dupl_anterior.=" ; ";
		$error_bd_seq="";		
		$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($query_delete_log_dupl_anterior, $error_bd_seq);
		if($error_bd_seq!="")
		{
			echo "<script>alert('ERROR Al borrar de tabla gioss_log_dupl ".procesar_mensaje($error_bd_seq)." ');</script>";
			
		}
		//fin borra el anterior
		
		$cont_lineas_log_dupl = 0;
		$lectura_archivo_log_dupl = fopen($ruta_cambios_duplicados_campos, "r");
		while(!feof($lectura_archivo_log_dupl))
		{
		    $linea_log_dupl = fgets($lectura_archivo_log_dupl);
		    
		    //se separa en un array por medio del caracter || como separador
		    //no se usa | ya que separaria campos de mas y los campos del registro
		    $separadores_linea_log_dupl=explode("||",$linea_log_dupl);
		    
		    if(count($separadores_linea_log_dupl)>=12)
		    {
			$nombre_archivo_log_dupl=$separadores_linea_log_dupl[0];
			$fecha_actual_log_dupl=$separadores_linea_log_dupl[1];
			$hora_actual_log_dupl=$separadores_linea_log_dupl[2];
			$ident_dupl_unico_final=$separadores_linea_log_dupl[3];
			$fecha_corte_log_dupl=$separadores_linea_log_dupl[4];
			$tipo_reporte_log_dupl=$separadores_linea_log_dupl[5];
			$eapb_log_dupl=$separadores_linea_log_dupl[6];			
			$prestador_log_dupl=$separadores_linea_log_dupl[7];
			$reparacion_o_consolidado_log_dupl=$separadores_linea_log_dupl[8];
			
			$agrupado_o_prestador=$separadores_linea_log_dupl[count($separadores_linea_log_dupl)-3];			
			$nlinea_correspondiente_en_log=$separadores_linea_log_dupl[count($separadores_linea_log_dupl)-2];			
			$registro_con_campos = $separadores_linea_log_dupl[count($separadores_linea_log_dupl)-1];
		    
		    
			$query_subir_registro_corregido="";
			$query_subir_registro_corregido.=" INSERT INTO ";
			$query_subir_registro_corregido.=" gioss_log_dupl ";				
			$query_subir_registro_corregido.=" ( ";
			$query_subir_registro_corregido.=" tipo_id_usuario, ";
			$query_subir_registro_corregido.=" id_usuario, ";
			$query_subir_registro_corregido.=" nick_usuario, ";
			$query_subir_registro_corregido.=" numero_registro, ";
			$query_subir_registro_corregido.=" fecha_corte_reporte, ";
			$query_subir_registro_corregido.=" fecha_de_generacion, ";
			$query_subir_registro_corregido.=" hora_generacion, ";
			$query_subir_registro_corregido.=" codigo_entidad_eapb_generadora, ";			
			$query_subir_registro_corregido.=" codigo_entidad_prestadora, ";
			$query_subir_registro_corregido.=" identificador_dupl, ";
			$query_subir_registro_corregido.=" tipo_reporte, ";
			$query_subir_registro_corregido.=" reparacion_o_consolidado, ";			
			$query_subir_registro_corregido.=" agrupado_o_prestador, ";
			$query_subir_registro_corregido.=" registro_con_campos, ";
			$query_subir_registro_corregido.=" nombre_archivo ";
			$query_subir_registro_corregido.=" ) ";
			$query_subir_registro_corregido.=" VALUES ";
			$query_subir_registro_corregido.=" ( ";
			$query_subir_registro_corregido.="'".$tipo_id."',";
			$query_subir_registro_corregido.="'".$identificacion."',";
			$query_subir_registro_corregido.="'".$nick_user."',";	
			$query_subir_registro_corregido.="'".$nlinea_correspondiente_en_log."',";							
			$query_subir_registro_corregido.="'".$fecha_corte_log_dupl."',";
			$query_subir_registro_corregido.="'".$fecha_actual_log_dupl."',";
			$query_subir_registro_corregido.="'".$hora_actual_log_dupl."',";
			$query_subir_registro_corregido.="'".$eapb_log_dupl."',";			
			$query_subir_registro_corregido.="'".$prestador_log_dupl."',";
			$query_subir_registro_corregido.="'".$ident_dupl_unico_final."',";
			$query_subir_registro_corregido.="'".$tipo_reporte_log_dupl."',";
			$query_subir_registro_corregido.="'".$reparacion_o_consolidado_log_dupl."',";
			$query_subir_registro_corregido.="'".$agrupado_o_prestador."',";
			$query_subir_registro_corregido.="'".$registro_con_campos."',";
			$query_subir_registro_corregido.="'".$nombre_archivo_log_dupl."'";
			$query_subir_registro_corregido.=" ) ";
			$query_subir_registro_corregido.=" ; ";
			$error_bd_seq="";		
			$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($query_subir_registro_corregido, $error_bd_seq);
			if($error_bd_seq!="")
			{
				echo "<script>alert('ERROR Al subir en la tabla gioss_log_dupl ".procesar_mensaje($error_bd_seq)." ');</script>";
				
			}
		    }//fin if longitud es correcta
		    
		    $cont_lineas_log_dupl++;
		}		    
		fclose($lectura_archivo_log_dupl);		    
		
		//FIN SUBE A GIOSS_LOG_DUPL PARA REPORTES FUTUROS
		
		//YA NO ESTA EN USO EL ARCHIVO
		$query_update_esta_siendo_procesado="";
		$query_update_esta_siendo_procesado.=" UPDATE gioss_2463_esta_reparando_ar_actualmente ";
		$query_update_esta_siendo_procesado.=" SET esta_ejecutando='NO',";
		$query_update_esta_siendo_procesado.=" ruta_archivo_descarga='$ruta_zip' ";
		$query_update_esta_siendo_procesado.=" WHERE fecha_corte_archivo_en_reparacion='".$fecha_de_corte."' ";
		$query_update_esta_siendo_procesado.=" AND codigo_entidad_reportadora='".$cod_prestador."' ";
		$query_update_esta_siendo_procesado.=" AND nombre_archivo='".$nombre_archivo_registrado."'  ";
		$query_update_esta_siendo_procesado.=" AND nick_usuario='".$nick_user."'  ";
		$query_update_esta_siendo_procesado.=" AND fecha_validacion='".$fecha_actual."'  ";
		$query_update_esta_siendo_procesado.=" AND hora_validacion='".$tiempo_actual."'  ";
		$query_update_esta_siendo_procesado.=" ; ";
		$error_bd="";
		$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($query_update_esta_siendo_procesado, $error_bd);
		if($error_bd!="")
		{
			if(connection_aborted()==false)
			{
				echo "<script>alert('error al actualizar el estado actual de validacion en tiempo real  2463 ');</script>";
			}
		}
		//FIN YA NO ESTA EN USO EL ARCHIVO
		
		//PARTE ENVIAR E-MAIL
		try
		{
			if($errores!="")
			{	
				//si hubo errores obligatorios
				
				// inicio envio de mail
	
				$mail = new PHPMailer();
				//inicio configuracion mail de acuerdoa archivo gloabl de configuracion
				if($USA_SMTP_CONFIGURACION_CORREO==true)
				{
				    
				 $mail->IsSMTP();
				 $mail->SMTPAuth = $SMTPAUTH_CONF_EMAIL_CE;
				 $mail->SMTPSecure = $SMTPSECURE_CONF_EMAIL_CE;
				 $mail->Host = $HOST_CONF_EMAIL;
				 $mail->Port = $PUERTO_CONF_EMAIL;
				 if($REQUIERE_AUTENTIFICACION_EMAIL==true)
				 {
				  $mail->Username = $USUARIO_CONF_EMAIL;
				  $mail->Password = $PASS_CONF_EMAIL;
				 }//fin if da el usuario y password
				}//fin if usa ../utiles/configuracion_global_email.php
				$mail->From = "sistemagioss@gmail.com";
				$mail->FromName = "GIOSS";
				$mail->Subject = "Reparacion ERC 2463 ";
				$mail->AltBody = "Cordial saludo,\n El sistema ha reparado su archivo )";
		    
				$mail->MsgHTML("Cordial saludo,\n El sistema ha realizado las correciones necesarias para la calidad de su archivo para la EAPB $cod_eapb .<strong>GIOSS</strong>.");
					    $mail->AddAttachment($ruta_zip);
				$mail->AddAddress($correo_electronico, "Destinatario");
		    
				$mail->IsHTML(true);
	
				if (!$mail->Send()) 
				{
				    //echo "Error: " . $mail->ErrorInfo;
				}
				else 
				{
				    // echo "Mensaje enviado.";
				    if(connection_aborted()==false)
				    {
					echo "<script>alert('Se ha enviado una copia del log con las inconsistencias encontradas a su correo $correo_electronico')</script>";
				    }
				}
		    
				//fin envio de mail
			}
			else
			{
				//si no hubo errores obligatorios
			}
		}
		catch(Exception $e)
		{
		}
		//FIN PARTE ENVIAR E-MAIL
		
	}//fin if si no fue validado exitosamente previamente
	else
	{
		if($errores!="")
		{
			if(connection_aborted()==false)
			{
				echo "<script>document.getElementById('div_mensaje_error').style.display='inline';</script>";
				echo "<script>document.getElementById('parrafo_mensaje_error').innerHTML='$errores';</script>";
				ob_flush();
				flush();
			}
		}
		
		if($bool_esta_siendo_reparado)
		{
			if(connection_aborted()==false)
			{
				echo "<script>document.getElementById('div_mensaje_error').style.display='inline';</script>";
				echo "<script>document.getElementById('parrafo_mensaje_error').innerHTML='<br>El archivo 2463 esta siendo reparado actualmente.';</script>";
				ob_flush();
				flush();
			}
		}
	}
}//fin if envia datos y archivo para validar
else if(isset($_POST["accion"]) && $_POST["accion"]=="validar" )
{
	$ultimo_error="";
	if(!( isset($_FILES["2463_ERC_file"])))
	{
		$ultimo_error="El archivo no se cargo ";
	}
	else if(!($_FILES["2463_ERC_file"]["error"]==0))
	{
		$ultimo_error="Error con el archivo de tipo ".$_FILES["2463_ERC_file"]["error"];
	}
	
	if(connection_aborted()==false)
	{
		echo "<script>document.getElementById('div_mensaje_error').style.display='inline';</script>";
		echo "<script>document.getElementById('parrafo_mensaje_error').innerHTML='Hubo error al cargar el archivo <br> $ultimo_error';</script>";
		ob_flush();
		flush();
	}
}
$coneccionBD->cerrar_conexion();
?>