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

require_once 'valildador_1393_Artritis_Reumatoide.php';

require_once '../utiles/queries_utiles_bd.php';

require_once '../utiles/crear_zip.php';

require_once '../utiles/configuracion_global_email.php';

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

function alphanumericAndSpace( $string )
{
    return preg_replace('/[^a-zA-Z0-9:.\s\-,@]/', '', $string);
}

function alphanumericAndSpace2( $string )
{
    return preg_replace('/[^a-zA-Z0-9\s\_,@<>]/', '', $string);
}

function procesar_mensaje($mensaje)
{
	$mensaje_procesado = str_replace("�","a",$mensaje);
	$mensaje_procesado = str_replace("�","e",$mensaje_procesado);
	$mensaje_procesado = str_replace("�","i",$mensaje_procesado);
	$mensaje_procesado = str_replace("�","o",$mensaje_procesado);
	$mensaje_procesado = str_replace("�","u",$mensaje_procesado);
	$mensaje_procesado = str_replace("�","n",$mensaje_procesado);
	$mensaje_procesado = str_replace("�","A",$mensaje_procesado);
	$mensaje_procesado = str_replace("�","E",$mensaje_procesado);
	$mensaje_procesado = str_replace("�","I",$mensaje_procesado);
	$mensaje_procesado = str_replace("�","O",$mensaje_procesado);
	$mensaje_procesado = str_replace("�","U",$mensaje_procesado);
	$mensaje_procesado = str_replace("�","N",$mensaje_procesado);
	$mensaje_procesado = str_replace("'"," ",$mensaje_procesado);
	$mensaje_procesado = str_replace("\n"," ",$mensaje_procesado);
	$mensaje_procesado = alphanumericAndSpace($mensaje_procesado);
	
	return $mensaje_procesado;
}

function procesar_mensaje2($mensaje)
{
	$mensaje_procesado = str_replace("�","a",$mensaje);
	$mensaje_procesado = str_replace("�","e",$mensaje_procesado);
	$mensaje_procesado = str_replace("�","i",$mensaje_procesado);
	$mensaje_procesado = str_replace("�","o",$mensaje_procesado);
	$mensaje_procesado = str_replace("�","u",$mensaje_procesado);
	$mensaje_procesado = str_replace("�","n",$mensaje_procesado);
	$mensaje_procesado = str_replace("�","A",$mensaje_procesado);
	$mensaje_procesado = str_replace("�","E",$mensaje_procesado);
	$mensaje_procesado = str_replace("�","I",$mensaje_procesado);
	$mensaje_procesado = str_replace("�","O",$mensaje_procesado);
	$mensaje_procesado = str_replace("�","U",$mensaje_procesado);
	$mensaje_procesado = str_replace("�","N",$mensaje_procesado);
	$mensaje_procesado = str_replace("'"," ",$mensaje_procesado);
	$mensaje_procesado = str_replace("\n"," ",$mensaje_procesado);
	$mensaje_procesado = alphanumericAndSpace2($mensaje_procesado);
	
	return $mensaje_procesado;
}


function diferencia_dias_entre_fechas_parte_estructura($fecha_1,$fecha_2)
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

function campos_a_registro_sin_consecutivo($campos, $separador="\t")
{
	//crea cadena con el registro actual
    $registro_actual="";
    if(is_array($campos))
    {
	    $ccamp=0;
	    while($ccamp<count($campos))
	    {
			if($registro_actual!=""){$registro_actual.=$separador;}
			
			$registro_actual.=procesar_mensaje($campos[$ccamp]);

			$ccamp++;
	    }//fin while
	}//if campos is array
	else
	{
		return false;
	}//fin else
    //fin crea cadena con el registro actual
    return $registro_actual;
}//fin function campos_a_registro


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

//este tiene parametors distintos que el de 4505 tener cuidado al reusar
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

function custom_replace_str($needle,$replace,$haystack,$replace_all=true,$qty_to_replace_from_start=1,$qty_to_replace_from_end=0,$replace2="",$needle2="")
{
    $result_string="";
    $counter_replaces_from_start=0;
    $counter_replaces_from_end=0;
    
    if(strlen($needle)<strlen($haystack) )
    {
        //remplazo desde el inicio
        $cont_str_actual=0;
        while($cont_str_actual<strlen($haystack))
        {
            $cont_str_needle_actual=0;
            $coincidencia=false;
            $cont_haystack_comp=$cont_str_actual;
            while($cont_str_needle_actual<strlen($needle))
            {
                
                //echo "needle char: ".$needle[$cont_str_needle_actual]." vs ".$haystack[$cont_haystack_comp]."<br>\n";
                if($needle[$cont_str_needle_actual]==$haystack[$cont_haystack_comp])
                {
                    if($cont_str_needle_actual==(strlen($needle)-1) )
                    {
                        if($counter_replaces_from_start<$qty_to_replace_from_start
                        || $replace_all==true
                        )
                        {
                            $coincidencia=true;
                            $result_string.=$replace;
                            $counter_replaces_from_start++;
                        }//remplaza si no ha llegado al maximo de remplazos o si esta habilitado remplazar todo
                    }//fin if

                    if($cont_haystack_comp<strlen($haystack) )
                    {
                        $cont_haystack_comp++;
                    }//fin if
                }//fin if
                else
                {
                    break;
                }//fin else sale del ciclo interno
                $cont_str_needle_actual++;
            }//fin while interno needle
            if($coincidencia==false)
            {
                $result_string.=$haystack[$cont_str_actual];
            }//fin if
            else if($coincidencia==true)
            {
                $cont_str_actual=($cont_haystack_comp-1);
            }//fin if
            //echo $cont_str_actual." result_string: ".$result_string."<br>\n";
            
            $cont_str_actual++;
        }//fin while
        
        //fin reemplazo desde el inicio



        //parte remplazo desde el final
        if($qty_to_replace_from_end>0 && $replace_all==false)
        {
            //echo "<br>\nReverso:<br>\n";
            if($replace2!="")
            {
                $replace=$replace2;
            }//fin if se digito replace 2
            if($needle2!="")
            {
                $needle=$needle2;
            }//fin if needle2

            //se reinician las cadenas para proceder a realizar 
            //el reemplazo desde atras con lo ultimo 
            //que se dejo
            $haystack=$result_string;
            $result_string="";
            $cont_str_actual=strlen($haystack)-1;
            while($cont_str_actual>=0)
            {
                $cont_str_needle_actual=strlen($needle)-1;
                $coincidencia=false;
                $cont_haystack_comp=$cont_str_actual;

                while($cont_str_needle_actual>=0)
                {
                    
                    //echo "needle char: ".$needle[$cont_str_needle_actual]." vs ".$haystack[$cont_haystack_comp]."<br>\n";
                    if($needle[$cont_str_needle_actual]==$haystack[$cont_haystack_comp])
                    {
                        if($cont_str_needle_actual==0 )
                        {
                            if($counter_replaces_from_end<$qty_to_replace_from_end )
                            {
                                $coincidencia=true;
                                $result_string=$replace.$result_string;
                                $counter_replaces_from_end++;

                                //echo "Se cambio en: ".$cont_haystack_comp." y contador frase pos actual es: ".$cont_str_actual."<br>\n";
                            }//remplaza si no ha llegado al maximo de remplazos o si esta habilitado remplazar todo
                        }//fin if

                        if($cont_haystack_comp>0 )
                        {
                            $cont_haystack_comp--;
                        }//fin if
                    }//fin if
                    else
                    {
                        break;
                    }//fin else sale del ciclo interno
                    $cont_str_needle_actual--;
                }//fin while interno needle
                if($coincidencia==false)
                {
                    $result_string=$haystack[$cont_str_actual].$result_string;
                }//fin if
                else if($coincidencia==true)
                {
                    if($cont_haystack_comp>0)
                    {
                        $cont_str_actual=($cont_haystack_comp+1);
                    }
                    else if($cont_haystack_comp==0)
                    {
                        $cont_str_actual=0;
                    }//fin else

                }//fin if
                //echo $cont_str_actual." result_string: ".$result_string."<br>\n";

                $cont_str_actual--;
            }//fin while
        }//fin if se decicio remplazar desde atras
        //fin parte reemplazo desde el final
    }//fin if
    else
    {
        return false;
    }

    return $result_string;
}//fin function remplazar cadena cuantificado

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

function formato_fecha_valida_quick_val($fecha_a_verificar,$separador="-")
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
if((intval($perfil_usuario_actual)==5 || intval($perfil_usuario_actual)==13 || intval($perfil_usuario_actual)==4 || intval($perfil_usuario_actual)==3)
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
			$prestador.="<option value='".$prestador_asociado_eapb['codigo_entidad']."' selected>".$prestador_asociado_eapb['nombre_de_la_entidad']."</option>";
		}
	}
}//si el tipo entidad es diferente de 6,7,8,10
else if((intval($perfil_usuario_actual)==1 || intval($perfil_usuario_actual)==2 || intval($perfil_usuario_actual)==5 || intval($perfil_usuario_actual)==13) && (intval($tipo_entidad)==6 || intval($tipo_entidad)==7 || intval($tipo_entidad)==8 || intval($tipo_entidad)==10) )
{
	$sql_query_tipo_entidad_asociada="SELECT * FROM gioss_entidades_sector_salud WHERE codigo_entidad='$entidad_salud_usuario_actual'; ";
	$resultado_query_tipo_entidad=$coneccionBD->consultar2_no_crea_cierra($sql_query_tipo_entidad_asociada);

	if(count($resultado_query_tipo_entidad)>0)
	{
		foreach($resultado_query_tipo_entidad as $eapb_entidad)
		{
			$prestador.="<option value='".$eapb_entidad['codigo_entidad']."' selected>".$eapb_entidad['nombre_de_la_entidad']."</option>";
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


if((intval($perfil_usuario_actual)==5 || intval($perfil_usuario_actual)==13 || intval($perfil_usuario_actual)==1 || intval($perfil_usuario_actual)==2) && (intval($tipo_entidad)==6 || intval($tipo_entidad)==7 || intval($tipo_entidad)==8 || intval($tipo_entidad)==10) )
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
			$eapb.="<option value='".$eapb_prestador_usuario_res['codigo_entidad']."' selected>".$eapb_prestador_usuario_res['nombre_de_la_entidad']."</option>";
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
			$eapb.="<option value='".$eapb_entidad['codigo_entidad']."' selected>".$eapb_entidad['nombre_de_la_entidad']."</option>";
		}
	}
}//fin else si el usuario es de perfil 3 y la entidad es de tipo eapb


$eapb.="</select>";
$eapb.="</div>";
//FIN EAPB

//SELECTOR PERIODO
$query_periodos_rips="SELECT * FROM gioss_periodo_reporte_1393_arte ORDER BY codigo_periodo;";
$resultado_query_periodos=$coneccionBD->consultar2_no_crea_cierra($query_periodos_rips);

$selector_periodo="";

$selector_periodo.="<select id='periodo' name='periodo' class='campo_azul' onchange='validar_antes_seleccionar_archivos();' >";
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

$smarty->assign("campo_periodo", $selector_periodo, true);
$smarty->assign("campo_eapb", $eapb, true);
$smarty->assign("campo_prestador", $prestador, true);
$smarty->assign("mensaje_proceso", $mensaje, true);
$smarty->assign("mostrarResultado", $mostrarResultado, true);
$smarty->assign("nombre", $nombre, true);
$smarty->assign("menu", $menu, true);
$smarty->display('carga_val_1393_Artritis_Reumatoide.html.tpl');

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
$query3_detalle_validacion="SELECT * FROM gioss_detalle_inconsistencia_1393_arte;";
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

$rutaTemporal = '../TEMPORALES/';
$error_mensaje="";

$ruta_archivo_inconsistencias_ARTE="";
$se_genero_archivo_de_inconsistencias=false;
$verificacion_es_diferente_prestador_en_ct=false;
$verificacion_fecha_diferente_en_ct=false;
$verificacion_numero_remision=false;
$verificacion_ya_se_valido_con_exito=false;

$mensaje_advertencia_tiempo="";
$mensaje_advertencia_tiempo .="Estimado usuario, se ha iniciado el proceso de validaci&oacuten del archivo,<br> lo que puede tomar varios minutos, dependiendo del volumen de registros.<br>";
$mensaje_advertencia_tiempo .="Una vez validado, se genera el Logs de errores, el cual se enviar&aacute a su Correo electr&oacutenico o puede descargarlo directamente del aplicat&iacutevo.<br>";
$mensaje_advertencia_tiempo .="Si la validaci&oacuten es exitosa, los datos se cargar&aacuten en la base de datos y se dar&aacute por aceptada la informaci&oacuten reportada<br>";

if(isset($_POST["accion"]) && $_POST["accion"]=="validar" && isset($_FILES["1393_ARTE_file"]) && $_FILES["1393_ARTE_file"]["error"]==0)
{	
	$nombre_archivo_file=explode(".",$_FILES["1393_ARTE_file"]["name"])[0];
	$nombre_archivo_registrado=explode(".",$_FILES["1393_ARTE_file"]["name"])[0];	
	$numero_de_remision=$_POST["numero_de_remision"];
	$archivo_norma=$_FILES["1393_ARTE_file"];
	$cod_prestador=$_POST["prestador"];
	$cod_eapb=$_POST["eapb"];	
	$codigo_periodo=explode("::",$_POST["periodo"])[0];
	$fecha_de_corte=$_POST["year_de_corte"]."-".explode("::",$_POST["periodo"])[1];
	
	$error_mostrar_bd="";
	
	//abre o crea el archivo dodne se escribiran las inconsistencias
	$ruta_archivo_inconsistencias_ARTE=$rutaTemporal."inconsistencias1393ARTE_".$cod_prestador."_".$fecha_actual."_".$tiempo_actual_string.".csv";
	if(file_exists($ruta_archivo_inconsistencias_ARTE))
	{
		unlink($ruta_archivo_inconsistencias_ARTE);
	}
	$file_inconsistencias_r4725_ARTE = fopen($ruta_archivo_inconsistencias_ARTE, "w") or die("fallo la creacion del archivo");
	$titulos="";
	$titulos.="consecutivo,nombre archivo,codigo tipo inconsistencia,tipo inconsistencia,codigo grupo inconsistencia,grupo inconsistencia,";
	$titulos.="codigo detalle inconsistencia,detalle inconsistencia, numero de linea, numero de campo";
	fwrite($file_inconsistencias_r4725_ARTE,$titulos."\n");
	
	
	$errores="";
	$exitos="";
	$tipo_regimen_archivo="";
	
	//PARTE VALIDACION ESTRUCTURA NOMBRE DEL ARCHIVO ARTE
	$es_valido_nombre_archivo=true;
	if($nombre_archivo_file!=$nombre_archivo_registrado)
	{
		$errores.="El nombre registrado y el nombre del archivo son diferentes. <br>";
	}
	else
	{
		
		$exitos="Archivo $nombre_archivo_file. <br>";
		
	}//fin if/else verificacion de igualdad entre nombre archivo y nombre archivo registrado 
	if ($archivo_norma['size'] > 250000000)
	{
		$es_valido_nombre_archivo=false;
		$errores.="EL tama&ntildeo no es valido. <br>";
	}
	else
	{
		if($nombre_archivo_file!="")
		{
			$ruta_archivo_arte = $rutaTemporal.$archivo_norma['name'];
			move_uploaded_file($archivo_norma['tmp_name'], $ruta_archivo_arte);
			
			$array_nombre_sin_sigla=explode("AR",$archivo_norma['name']);
			if(count($array_nombre_sin_sigla)!=2)
			{
				$es_valido_nombre_archivo=false;
				$errores.="El encabezado del archivo $nombre_archivo_file no corresponde a un archivo ARTE. <br>";
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
				if($prestador_del_nombre_archivo!=$cod_prestador_temporal)
				{
					$es_valido_nombre_archivo=false;
					$errores.="El codigo de prestador indicado en el nombre del archivo ( $prestador_del_nombre_archivo ), no corresponde al codigo prestador asociado ( $cod_prestador ). <br>";
				}
				$regimen_nombre=substr($nombre_archivo_fecha_prestador,20,1);
				if($regimen_nombre!="C" && $regimen_nombre!="S" && $regimen_nombre!="P" && $regimen_nombre!="N" && $regimen_nombre!="E")
				{
					$es_valido_nombre_archivo=false;
					$errores.="El regimen ($regimen_nombre) no corresponde a C-S-P-N-E. <br>";
				}
				//echo "<script>alert('$regimen_nombre');</script>";
				
				$eapb_del_nombre_del_archivo=substr($nombre_archivo_fecha_prestador,21,6);
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
				$array_fecha_de_corte=explode("-",$fecha_de_corte);
				if($year!=$array_fecha_de_corte[0])
				{
					$es_valido_nombre_archivo=false;
					$errores.="El a&ntildeo indicado en el nombre del archivo ( $year ), no corresponde al a&ntildeo registrado ( ".$array_fecha_de_corte[0]." ). <br>";
				}
				if($mes!=$array_fecha_de_corte[1])
				{
					$es_valido_nombre_archivo=false;
					$errores.="El mes indicado en el nombre del archivo ( $mes ), no corresponde al mes registrado ( ".$array_fecha_de_corte[1]." ). <br>";
				}
				if($dia!=$array_fecha_de_corte[2])
				{
					$es_valido_nombre_archivo=false;
					$errores.="El dia indicado en el nombre del archivo ( $dia ), no corresponde al dia registrado ( ".$array_fecha_de_corte[2]." ). <br>";
				}
			}//fin if contiene la sigla
		}//fin if nombre del archivo no es vacio
		else
		{
			$es_valido_nombre_archivo=false;
			$errores.="El nombre del archivo para ARTE es invalido. <br>";
		}
	}//fin else
	//FIN PARTE VALIDACION ESTRUCTURA NOMBRE DEL ARCHIVO ARTE
	
	//VERIFICA SI EL ARCHIVO YA ESTA SIENDO VALIDADO ACTUALMENTE
        $bool_esta_siendo_validado=false;
        
        $query_verificacion_esta_siendo_procesado="";
        $query_verificacion_esta_siendo_procesado.=" SELECT * FROM gioss_1393_esta_validando_actualmente ";
        $query_verificacion_esta_siendo_procesado.=" WHERE fecha_remision='".$fecha_de_corte."' ";
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
                                $bool_esta_siendo_validado=true;
				$es_valido_nombre_archivo=false;
				$errores.="Se esta validando el archivo actualmente, por favor espere a que termine para volver a validarlo nuevamente.<br>";
                                break;
                        }
                }
                
        }        
        //FIN VERIFICA SI EL ARCHIVO YA ESTA SIENDO VALIDADO ACTUALMENTE
	
	//VERIFICA SI FUE CARGADO COMO EXITOSO PREVIAMENTE
	$sql_query_verificar="";
	$sql_query_verificar.=" SELECT * FROM gioss_tabla_estado_informacion_r1393_arte ";
	$sql_query_verificar.=" WHERE fecha_corte='".$fecha_de_corte."' ";
	$sql_query_verificar.=" AND codigo_eapb='".$cod_eapb."' ";
	$sql_query_verificar.=" AND codigo_prestador_servicios='".$cod_prestador."' ";
	$sql_query_verificar.=" AND nombre_del_archivo='".$nombre_archivo_registrado."'  ";
	$sql_query_verificar.=" AND codigo_estado_informacion='1'  ";
	$sql_query_verificar.=" ;  ";
	$resultados_query_verificar=$coneccionBD->consultar2_no_crea_cierra($sql_query_verificar);
	if(count($resultados_query_verificar)>0)
	{
		$verificacion_ya_se_valido_con_exito=true;
		$es_valido_nombre_archivo=false;
		$errores.="Ya se ha validado previamente de forma exitosa los archivos, no es necesario volverlos a validar.<br>";
	}
	//FIN VERIFICA SI FUE CARGADO COMO EXITOSO PREVIAMENTE
	
	if($verificacion_ya_se_valido_con_exito==false)
	{
		$sql_query_delete="";
		$sql_query_delete.=" DELETE FROM gioss_tabla_registros_no_cargados_rechazados_r1393_arte ";
		$sql_query_delete.=" WHERE fecha_corte='".$fecha_de_corte."' ";
		$sql_query_delete.=" AND codigo_eapb_a_reportar='".$cod_eapb."' ";
		$sql_query_delete.=" AND codigo_prestador_reportante='".$cod_prestador."' ";
		$sql_query_delete.=" AND nombre_archivo='".$nombre_archivo_registrado."' ; ";
		$error_bd_seq="";
		$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_query_delete, $error_bd_seq);
		if($error_bd_seq!="")
		{
			$error_mostrar_bd.=" AL ELIMINAR CAMPOS RECHAZADOS PREVIOS ".$error_bd_seq."<br>";
		}
	
		//ASIGNACION NUMERO SECUENCIA
		//consulta ultimo numero de secuencia
		$numero_secuencia_actual=$utilidades->obtenerSecuencia("gioss_numero_secuencia_r1393_arte");
					
		$sql_query_inserta_seq="";
		$sql_query_inserta_seq.=" INSERT INTO gioss_numero_de_secuencia_archivos_arte ";
		$sql_query_inserta_seq.=" ( ";
		$sql_query_inserta_seq.=" fecha_de_corte, ";
		$sql_query_inserta_seq.=" codigo_eapb, ";
		$sql_query_inserta_seq.=" codigo_prestador_servicios_salud, ";
		$sql_query_inserta_seq.=" nombre_archivo, ";
		$sql_query_inserta_seq.=" numero_secuencia ";
		$sql_query_inserta_seq.=" ) ";
		$sql_query_inserta_seq.=" VALUES ";
		$sql_query_inserta_seq.=" ( ";
		$sql_query_inserta_seq.=" '".$fecha_de_corte."', ";
		$sql_query_inserta_seq.=" '".$cod_eapb."', ";
		$sql_query_inserta_seq.=" '".$cod_prestador."', ";
		$sql_query_inserta_seq.=" '".$nombre_archivo_registrado."', ";
		$sql_query_inserta_seq.=" '".$numero_secuencia_actual."' ";
		$sql_query_inserta_seq.=" ) ";
		$sql_query_inserta_seq.=" ; ";
		$error_bd_seq="";
		$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_query_inserta_seq, $error_bd_seq);
		if($error_bd_seq!="")
		{
			$error_mostrar_bd.=$error_bd_seq."<br>";
		}
		//FIN ASIGNACION NUMERO SECUENCIA
		
		//INICIO LA EJECUCION		
		$query_insert_esta_siendo_procesado="";
		$query_insert_esta_siendo_procesado.=" INSERT INTO gioss_1393_esta_validando_actualmente ";
		$query_insert_esta_siendo_procesado.=" ( ";
		$query_insert_esta_siendo_procesado.=" codigo_entidad_reportadora,";
		$query_insert_esta_siendo_procesado.=" nombre_archivo,";
		$query_insert_esta_siendo_procesado.=" fecha_remision,";
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
				echo "<script>alert('error al iniciar el estado actual de validacion en tiempo real  1393 ');</script>";
			}
		}
		//FIN INICIO LA EJECUCION
		
		$lineas_del_archivo=0;
		
		$registros_buenos=0;
		$registros_malos=0;
		
		// parte donde valida los campos del archivo ARTE 
		$hubo_inconsistencias_en_ARTE=false;	
		$diccionario_identificacion=array();
		$diccionario_identificacion_lineas=array();
		if($es_valido_nombre_archivo)
		{
			$mensaje_errores_ARTE="";
			$lineas_del_archivo = count(file($ruta_archivo_arte)); 
			$file_ARTE = fopen($ruta_archivo_arte, 'r') or exit("No se pudo abrir el archivo");
			
			//la variable $consecutivo_errores pasara como referencia y aumentara cada que se haye un error y su incremento es independiente de la variable $nlinea
			$consecutivo_errores=0;
			
			$nlinea=0;
			$fue_cerrada_la_gui=false;
			while (!feof($file_ARTE)) 
			{
				if($fue_cerrada_la_gui==false)
				{
				    if(connection_aborted()==true)
				    {
					$fue_cerrada_la_gui=true;
				    }
				}//fin if verifica si el usuario cerro la pantalla
				
				//PARTE VERIFICA SI FUE CANCELADA LA EJECUCION DEL SCRIPT
				$verificar_si_ejecucion_fue_cancelada="";
				$verificar_si_ejecucion_fue_cancelada.=" SELECT esta_ejecutando FROM gioss_1393_esta_validando_actualmente ";
				$verificar_si_ejecucion_fue_cancelada.=" WHERE fecha_remision='".$fecha_de_corte."' ";
				$verificar_si_ejecucion_fue_cancelada.=" AND codigo_entidad_reportadora='".$cod_prestador."' ";
				$verificar_si_ejecucion_fue_cancelada.=" AND nombre_archivo='".$nombre_archivo_registrado."'  ";
				$verificar_si_ejecucion_fue_cancelada.=" AND nick_usuario='".$nick_user."'  ";
				$verificar_si_ejecucion_fue_cancelada.=" AND fecha_validacion='".$fecha_actual."'  ";
				$verificar_si_ejecucion_fue_cancelada.=" AND hora_validacion='".$tiempo_actual."'  ";
				$verificar_si_ejecucion_fue_cancelada.=" ; ";
				$error_bd_seq="";
				$resultados_si_ejecucion_fue_cancelada=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($verificar_si_ejecucion_fue_cancelada, $error_bd_seq);		
				if($error_bd_seq!="")
				{
				    if($fue_cerrada_la_gui==false)
				    {
					echo "<script>alert(' error al consultar si se cancelo la ejecucion ');</script>";
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
				//FIN PARTE VERIFICA SI FUE CANCELADA LA EJECUCION DEL SCRIPT
				
				$linea_tmp = fgets($file_ARTE);
				$linea= explode("\n", $linea_tmp)[0];
				//$linea=str_replace(",",".",$linea);
				
				$campos = explode("\t", $linea);
				
				//parte para evitar caracteres extra�os en el ultimo campo antes del salto de linea
				$campos[count($campos)-1]=procesar_mensaje($campos[count($campos)-1]);
				
				//pasa a validar los campos
				if(count($campos)==152)
				{
				
					$mensaje_contador_errores="revisando linea ".($nlinea+1)." de $lineas_del_archivo del archivo ARTE. <br> registros buenos $registros_buenos , registros malos $registros_malos . ";
					$html_del_mensaje="";
					$html_del_mensaje.="<table>";
					$html_del_mensaje.="<tr>";
					$html_del_mensaje.="<td colspan=\'2\'>";
					$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="</tr>";
					$html_del_mensaje.="<tr>";
					$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
					$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
					$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_contador_errores."</div><div id=\'errores_bd_div\'></div>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="</tr>";
					$html_del_mensaje.="</table>";
					if($fue_cerrada_la_gui==false)
					{
						echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
						ob_flush();
						flush();
					}
					//ACTUALIZA ESTADO EJECUCION
					/*
					$porcentaje=(($nlinea+1)*100)/$lineas_del_archivo;
					$dies_porciento=intval((10*$lineas_del_archivo)/100);
					if(($porcentaje%$dies_porciento)==0 )
					*/
					
					$query_update_esta_siendo_procesado="";
					$query_update_esta_siendo_procesado.=" UPDATE gioss_1393_esta_validando_actualmente ";
					$query_update_esta_siendo_procesado.=" SET mensaje_estado_registros='".$mensaje_contador_errores."' ";
					$query_update_esta_siendo_procesado.=" WHERE fecha_remision='".$fecha_de_corte."' ";
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
							echo "<script>alert('error al actualizar el estado actual de validacion en tiempo real  1393 ');</script>";
						}
					}
					
					//FIN ACTUALIZA ESTADO EJECUCION
					
					
					//validar_ARTE($campos,$nlinea,&$consecutivo_errores,$array_tipo_validacion,$array_grupo_validacion,$array_detalle_validacion,$nombre_archivo,$fecha_remision,$fecha_de_corte,$cod_prestador,$cod_eapb)
					$array_resultados_validacion=validar_ARTE($campos,
										 $nlinea,
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
										
					if($hubo_inconsistencias_en_ARTE==false)
					{
						$hubo_inconsistencias_en_ARTE=$array_resultados_validacion["error"];
					}
					
					$estado_validacion_registro=0;
					if($array_resultados_validacion["error"]==false)
					{
						$estado_validacion_registro=1;
						$registros_buenos++;
					}
					else
					{
						$estado_validacion_registro=2;
						$registros_malos++;
					}
					
					$query_insertar_campos_a_bd="";
					$query_insertar_campos_a_bd.="INSERT INTO gioss_tabla_registros_no_cargados_rechazados_r1393_arte ";
					$query_insertar_campos_a_bd.="(";				
					$cont_campo_ins=0;
					while($cont_campo_ins<152)
					{
						$query_insertar_campos_a_bd.="campo_arte_de_numero_orden_".$cont_campo_ins.",";
						$cont_campo_ins++;
					}
					$query_insertar_campos_a_bd.="nombre_archivo,";
					$query_insertar_campos_a_bd.="numero_secuencia,";
					$query_insertar_campos_a_bd.="codigo_prestador_reportante,";
					$query_insertar_campos_a_bd.="codigo_eapb_a_reportar,";
					$query_insertar_campos_a_bd.="fecha_corte,";
					$query_insertar_campos_a_bd.="fecha_validacion,";
					$query_insertar_campos_a_bd.="hora_validacion,";
					$query_insertar_campos_a_bd.="fila,";
					$query_insertar_campos_a_bd.="estado_registro";
					$query_insertar_campos_a_bd.=")";
					$query_insertar_campos_a_bd.=" VALUES ";
					$query_insertar_campos_a_bd.="(";
					
					$cont_campo_ins=0;
					while($cont_campo_ins<152)
					{
						$query_insertar_campos_a_bd.="'".procesar_mensaje($campos[$cont_campo_ins])."',";
						$cont_campo_ins++;
					}
					$query_insertar_campos_a_bd.="'".$nombre_archivo_registrado."',";
					$query_insertar_campos_a_bd.="'".$numero_secuencia_actual."',";
					$query_insertar_campos_a_bd.="'".$cod_prestador."',";
					$query_insertar_campos_a_bd.="'".$cod_eapb."',";
					$query_insertar_campos_a_bd.="'".$fecha_de_corte."',";
					$query_insertar_campos_a_bd.="'".$fecha_actual."',";
					$query_insertar_campos_a_bd.="'".$tiempo_actual."',";
					$query_insertar_campos_a_bd.="'".$nlinea."',";
					$query_insertar_campos_a_bd.="'".$estado_validacion_registro."'";
					$query_insertar_campos_a_bd.=");";
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($query_insertar_campos_a_bd, $error_bd_seq);		
					if($error_bd_seq!="")
					{
						$error_mostrar_bd.=" AL SUBIR CAMPOS RECHAZADOS ".$error_bd_seq."<br>";
					}
					
					//escribe los errores
					$mensaje_errores_ARTE=$array_resultados_validacion["mensaje"];
					$array_mensajes_errores_campos=explode("|",$mensaje_errores_ARTE);
					
					foreach($array_mensajes_errores_campos as $msg_error)
					{
						if($msg_error!="")
						{
							fwrite($file_inconsistencias_r4725_ARTE, $msg_error."\n");
						}
						
						$columnas_inconsistencias_para_bd=array();
						$columnas_inconsistencias_para_bd=explode(",",$msg_error);
						
						//SUBIDA DE INCONSISTENCIAS A LA BASE DE DATOS
						if(count($columnas_inconsistencias_para_bd)==10)
						{
							
							
							//se insertan los datos de detalles de inconsistencia
							
							$sql_insertar_inconsistencia_arte="";
							$sql_insertar_inconsistencia_arte.=" INSERT INTO gioss_reporte_inconsistencias_r1393_arte ";
							$sql_insertar_inconsistencia_arte.=" ( ";
							$sql_insertar_inconsistencia_arte.=" numero_orden, ";
							$sql_insertar_inconsistencia_arte.=" nombre_archivo, ";
							$sql_insertar_inconsistencia_arte.=" cod_tipo_inconsitencia, ";
							$sql_insertar_inconsistencia_arte.=" nombre_tipo_inconsistencia, ";
							$sql_insertar_inconsistencia_arte.=" cod_grupo_inconsistencia, ";
							$sql_insertar_inconsistencia_arte.=" nombre_grupo_inconsistencia, ";
							$sql_insertar_inconsistencia_arte.=" cod_detalle_inconsistencia, ";
							$sql_insertar_inconsistencia_arte.=" detalle_inconsistencia, ";
							$sql_insertar_inconsistencia_arte.=" numero_linea, ";
							$sql_insertar_inconsistencia_arte.=" numero_campo, ";
							$sql_insertar_inconsistencia_arte.=" fecha_validacion, ";
							$sql_insertar_inconsistencia_arte.=" hora_validacion ";
							$sql_insertar_inconsistencia_arte.=" ) ";
							$sql_insertar_inconsistencia_arte.=" VALUES ";
							$sql_insertar_inconsistencia_arte.=" ( ";
							$sql_insertar_inconsistencia_arte.=" '".$numero_secuencia_actual."', ";
							$sql_insertar_inconsistencia_arte.=" '".$nombre_archivo_registrado."', ";
							$sql_insertar_inconsistencia_arte.=" '".preg_replace("/[^A-Za-z0-9:.\-\s+]/", "", trim($columnas_inconsistencias_para_bd[2]) )."', ";
							$sql_insertar_inconsistencia_arte.=" '".preg_replace("/[^A-Za-z0-9:.\-\s+]/", "", trim($columnas_inconsistencias_para_bd[3]) )."', ";
							$sql_insertar_inconsistencia_arte.=" '".preg_replace("/[^A-Za-z0-9:.\-\s+]/", "", trim($columnas_inconsistencias_para_bd[4]) )."', ";
							$sql_insertar_inconsistencia_arte.=" '".preg_replace("/[^A-Za-z0-9:.\-\s+]/", "", trim($columnas_inconsistencias_para_bd[5]) )."', ";
							$sql_insertar_inconsistencia_arte.=" '".preg_replace("/[^A-Za-z0-9:.\-\s+]/", "", trim($columnas_inconsistencias_para_bd[6]) )."', ";
							$sql_insertar_inconsistencia_arte.=" '".preg_replace("/[^A-Za-z0-9:.\-\s+]/", "", trim($columnas_inconsistencias_para_bd[7]) )."', ";
							$sql_insertar_inconsistencia_arte.=" '".preg_replace("/[^A-Za-z0-9:.\-\s+]/", "", trim($columnas_inconsistencias_para_bd[8]) )."', ";
							$sql_insertar_inconsistencia_arte.=" '".preg_replace("/[^A-Za-z0-9:.\-\s+]/", "", trim($columnas_inconsistencias_para_bd[9]) )."', ";
							$sql_insertar_inconsistencia_arte.=" '".$fecha_actual."', ";
							$sql_insertar_inconsistencia_arte.=" '".$tiempo_actual."' ";
							$sql_insertar_inconsistencia_arte.=" ); ";
							$error_bd_ins="";
							$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_insertar_inconsistencia_arte, $error_bd_ins);
							if($error_bd_ins!="")
							{
								$error_mostrar_bd.="ERROR AL SUBIR LAS INCONSISTENCIAS ".$error_bd_ins."<br>";
							}
							//fin se insertan los datos de detalles de inconsistencia
							
							
						}					
						//FIN SUBIDA INCONSISTENCIAS A LA BASE DE DATOS
						
					}
					//fin escribe los errores
					
				}//fin if verifica longitud
				else
				{
					$mensaje_contador_errores="revisando linea ".($nlinea+1)." de $lineas_del_archivo del archivo ARTE ";
					$html_del_mensaje="";
					$html_del_mensaje.="<table>";
					$html_del_mensaje.="<tr>";
					$html_del_mensaje.="<td colspan=\'2\'>";
					$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="</tr>";
					$html_del_mensaje.="<tr>";
					$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
					$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
					$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_contador_errores."</div><div id=\'errores_bd_div\'></div>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="</tr>";
					$html_del_mensaje.="</table>";
					if($fue_cerrada_la_gui==false)
					{
						echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
						ob_flush();
						flush();
					}
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0301001"])[1];
					$error_longitud=$consecutivo_errores.",".$nombre_archivo_registrado.",03,".$array_tipo_validacion["03"].",0301,".$array_grupo_validacion["0301"].",0301001,$cadena_descripcion_inconsistencia ".count($campos).",".($nlinea+1).","."-1";
					$consecutivo_errores++;
					
					if($hubo_inconsistencias_en_ARTE==false)
					{
						$hubo_inconsistencias_en_ARTE=true;
					}
					fwrite($file_inconsistencias_r4725_ARTE, $error_longitud."\n");
					
					$columnas_inconsistencias_para_bd=array();
					$columnas_inconsistencias_para_bd=explode(",",$error_longitud);
					
					//SUBIDA DE INCONSISTENCIAS A LA BASE DE DATOS
					if(count($columnas_inconsistencias_para_bd)==10)
					{
						
						
						//se insertan los datos de detalles de inconsistencia
						
						$sql_insertar_inconsistencia_arte="";
						$sql_insertar_inconsistencia_arte.=" INSERT INTO gioss_reporte_inconsistencias_r1393_arte ";
						$sql_insertar_inconsistencia_arte.=" ( ";
						$sql_insertar_inconsistencia_arte.=" numero_orden, ";
						$sql_insertar_inconsistencia_arte.=" nombre_archivo, ";
						$sql_insertar_inconsistencia_arte.=" cod_tipo_inconsitencia, ";
						$sql_insertar_inconsistencia_arte.=" nombre_tipo_inconsistencia, ";
						$sql_insertar_inconsistencia_arte.=" cod_grupo_inconsistencia, ";
						$sql_insertar_inconsistencia_arte.=" nombre_grupo_inconsistencia, ";
						$sql_insertar_inconsistencia_arte.=" cod_detalle_inconsistencia, ";
						$sql_insertar_inconsistencia_arte.=" detalle_inconsistencia, ";
						$sql_insertar_inconsistencia_arte.=" numero_linea, ";
						$sql_insertar_inconsistencia_arte.=" numero_campo, ";
						$sql_insertar_inconsistencia_arte.=" fecha_validacion, ";
						$sql_insertar_inconsistencia_arte.=" hora_validacion ";
						$sql_insertar_inconsistencia_arte.=" ) ";
						$sql_insertar_inconsistencia_arte.=" VALUES ";
						$sql_insertar_inconsistencia_arte.=" ( ";
						$sql_insertar_inconsistencia_arte.=" '".$numero_secuencia_actual."', ";
						$sql_insertar_inconsistencia_arte.=" '".$nombre_archivo_registrado."', ";
						$sql_insertar_inconsistencia_arte.=" '".preg_replace("/[^A-Za-z0-9:.\-\s+]/", "", trim($columnas_inconsistencias_para_bd[2]) )."', ";
						$sql_insertar_inconsistencia_arte.=" '".preg_replace("/[^A-Za-z0-9:.\-\s+]/", "", trim($columnas_inconsistencias_para_bd[3]) )."', ";
						$sql_insertar_inconsistencia_arte.=" '".preg_replace("/[^A-Za-z0-9:.\-\s+]/", "", trim($columnas_inconsistencias_para_bd[4]) )."', ";
						$sql_insertar_inconsistencia_arte.=" '".preg_replace("/[^A-Za-z0-9:.\-\s+]/", "", trim($columnas_inconsistencias_para_bd[5]) )."', ";
						$sql_insertar_inconsistencia_arte.=" '".preg_replace("/[^A-Za-z0-9:.\-\s+]/", "", trim($columnas_inconsistencias_para_bd[6]) )."', ";
						$sql_insertar_inconsistencia_arte.=" '".preg_replace("/[^A-Za-z0-9:.\-\s+]/", "", trim($columnas_inconsistencias_para_bd[7]) )."', ";
						$sql_insertar_inconsistencia_arte.=" '".preg_replace("/[^A-Za-z0-9:.\-\s+]/", "", trim($columnas_inconsistencias_para_bd[8]) )."', ";
						$sql_insertar_inconsistencia_arte.=" '".preg_replace("/[^A-Za-z0-9:.\-\s+]/", "", trim($columnas_inconsistencias_para_bd[9]) )."', ";
						$sql_insertar_inconsistencia_arte.=" '".$fecha_actual."', ";
						$sql_insertar_inconsistencia_arte.=" '".$tiempo_actual."' ";
						$sql_insertar_inconsistencia_arte.=" ); ";
						$error_bd_ins="";
						$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_insertar_inconsistencia_arte, $error_bd_ins);
						if($error_bd_ins!="")
						{
							$error_mostrar_bd.="ERROR AL SUBIR LAS INCONSISTENCIAS ".$error_bd_ins."<br>";
						}
						//fin se insertan los datos de detalles de inconsistencia
						
						
					}					
					//FIN SUBIDA INCONSISTENCIAS A LA BASE DE DATOS
				}//fin else longitud no apropiada
				$nlinea++;
			}
			fclose($file_ARTE);
		}
		//fin parte valida archivo
		
		//cierra el archivo donde se escriben las inconsistencias
		fclose($file_inconsistencias_r4725_ARTE);
		
		
		
		if($es_valido_nombre_archivo)
		{
			
			
			//SUBIR TABLA CARGADOS CON EXITO
			if($hubo_inconsistencias_en_ARTE==false)
			{
				//se eliminan los rechazados porque ya fue exitoso
				$sql_query_delete="";
				$sql_query_delete.=" DELETE FROM gioss_tabla_registros_no_cargados_rechazados_r1393_arte ";
				$sql_query_delete.=" WHERE fecha_corte='".$fecha_de_corte."' ";
				$sql_query_delete.=" AND codigo_eapb_a_reportar='".$cod_eapb."' ";
				$sql_query_delete.=" AND codigo_prestador_reportante='".$cod_prestador."' ";
				$sql_query_delete.=" AND nombre_archivo='".$nombre_archivo_registrado."' ; ";
				$error_bd_seq="";
				$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_query_delete, $error_bd_seq);
				if($error_bd_seq!="")
				{
					$error_mostrar_bd.=" AL ELIMINAR CAMPOS RECHAZADOS PREVIOS ".$error_bd_seq."<br>";
				}
				//fin se eliminan los rechazados porque ya fue exitoso
				
				//se eliminan los exitosos incompletos previos 
				$sql_query_delete="";
				$sql_query_delete.=" DELETE FROM gioss_tabla_registros_cargados_exito_r1393_arte ";
				$sql_query_delete.=" WHERE fecha_corte='".$fecha_de_corte."' ";
				$sql_query_delete.=" AND codigo_eapb_a_reportar='".$cod_eapb."' ";
				$sql_query_delete.=" AND codigo_prestador_reportante='".$cod_prestador."' ";
				$sql_query_delete.=" AND nombre_archivo='".$nombre_archivo_registrado."' ; ";
				$error_bd_seq="";
				$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_query_delete, $error_bd_seq);
				if($error_bd_seq!="")
				{
					$error_mostrar_bd.=" AL ELIMINAR CAMPOS EXITOSOS INCOMPLETOS PREVIOS ".$error_bd_seq."<br>";
				}
				//se eliminan los exitosos incompletos previos
				
				$file_ARTE = fopen($ruta_archivo_arte, 'r') or exit("No se pudo abrir el archivo");
				$nlinea=0;
				while (!feof($file_ARTE)) 
				{
					$linea_tmp = fgets($file_ARTE);
					$linea= explode("\n", $linea_tmp)[0];
					$linea=str_replace(",",".",$linea);
					$campos = explode("\t", $linea);
					
					//parte para evitar caracteres extra�os en el ultimo campo antes del salto de linea
					$campos[count($campos)-1]=procesar_mensaje($campos[count($campos)-1]);
					
					//pasa a validar los campos
					if(count($campos)==152)
					{
						$query_insertar_campos_a_bd="";
						$query_insertar_campos_a_bd.="INSERT INTO gioss_tabla_registros_cargados_exito_r1393_arte ";
						$query_insertar_campos_a_bd.="(";
						
						$cont_campo_ins=0;
						while($cont_campo_ins<152)
						{
							$query_insertar_campos_a_bd.="campo_arte_de_numero_orden_".$cont_campo_ins.",";
							$cont_campo_ins++;
						}
						$query_insertar_campos_a_bd.="nombre_archivo,";
						$query_insertar_campos_a_bd.="numero_secuencia,";
						$query_insertar_campos_a_bd.="codigo_prestador_reportante,";
						$query_insertar_campos_a_bd.="codigo_eapb_a_reportar,";
						$query_insertar_campos_a_bd.="fecha_corte,";
						$query_insertar_campos_a_bd.="fecha_validacion,";
						$query_insertar_campos_a_bd.="hora_validacion,";
						$query_insertar_campos_a_bd.="fila,";
						$query_insertar_campos_a_bd.="estado_registro";
						$query_insertar_campos_a_bd.=")";
						$query_insertar_campos_a_bd.=" VALUES ";
						$query_insertar_campos_a_bd.="(";
						
						$cont_campo_ins=0;
						while($cont_campo_ins<152)
						{
							$query_insertar_campos_a_bd.="'".procesar_mensaje($campos[$cont_campo_ins])."',";
							$cont_campo_ins++;
						}
						$query_insertar_campos_a_bd.="'".$nombre_archivo_registrado."',";
						$query_insertar_campos_a_bd.="'".$numero_secuencia_actual."',";
						$query_insertar_campos_a_bd.="'".$cod_prestador."',";
						$query_insertar_campos_a_bd.="'".$cod_eapb."',";
						$query_insertar_campos_a_bd.="'".$fecha_de_corte."',";
						$query_insertar_campos_a_bd.="'".$fecha_actual."',";
						$query_insertar_campos_a_bd.="'".$tiempo_actual."',";
						$query_insertar_campos_a_bd.="'".$nlinea."',";
						$query_insertar_campos_a_bd.="'1'";
						$query_insertar_campos_a_bd.=");";
						$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($query_insertar_campos_a_bd, $error_bd_seq);		
						if($error_bd_seq!="")
						{
							$error_mostrar_bd.=" AL SUBIR CAMPOS EXITOSOS ".$error_bd_seq."<br>";
						}
						
						$mensaje_contador_errores="Subiendo a cargados con exito el registro ".($nlinea+1)." de $lineas_del_archivo del archivo ARTE ";
						$html_del_mensaje="";
						$html_del_mensaje.="<table>";
						$html_del_mensaje.="<tr>";
						$html_del_mensaje.="<td colspan=\'2\'>";
						$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
						$html_del_mensaje.="</td>";
						$html_del_mensaje.="</tr>";
						$html_del_mensaje.="<tr>";
						$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
						$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
						$html_del_mensaje.="</td>";
						$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
						$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_contador_errores."</div><div id=\'errores_bd_div\'></div>";
						$html_del_mensaje.="</td>";
						$html_del_mensaje.="</tr>";
						$html_del_mensaje.="</table>";
						if(connection_aborted()==false)
						{
							echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
							ob_flush();
							flush();
						}
					}//fin verifica el numero de campos
					$nlinea++;
				}
				fclose($file_ARTE);
				
				//CARGA A RESUMEN EXITOSO
				$query_nombre_prestador="";
				$query_nombre_prestador.="SELECT nombre_de_la_entidad FROM gioss_entidades_sector_salud WHERE codigo_entidad='".$cod_prestador."' ; ";
				$resultado_query_nombre_prestador=$coneccionBD->consultar2_no_crea_cierra($query_nombre_prestador);
				
				$nombre_prestador="";
				if(count($resultado_query_nombre_prestador)>0)
				{
					$nombre_prestador=$resultado_query_nombre_prestador[0]["nombre_de_la_entidad"];
				}
				
				$query_nombre_eapb="";
				$query_nombre_eapb.="SELECT nombre_de_la_entidad FROM gioss_entidades_sector_salud WHERE codigo_entidad='".$cod_eapb."' ; ";
				$resultado_query_nombre_eapb=$coneccionBD->consultar2_no_crea_cierra($query_nombre_eapb);
				
				$nombre_eapb="";
				if(count($resultado_query_nombre_eapb)>0 )
				{
					$nombre_eapb=$resultado_query_nombre_eapb[0]["nombre_de_la_entidad"];
				}
				
				$query_registrar_resumen_cargado_con_exito="";
				$query_registrar_resumen_cargado_con_exito.="INSERT INTO gioss_resumen_cargados_exito_1393 ";
				$query_registrar_resumen_cargado_con_exito.="(";
				$query_registrar_resumen_cargado_con_exito.="codigo_habilitacion_reps,";
				$query_registrar_resumen_cargado_con_exito.="nombre_entidad_prestadora,";
				$query_registrar_resumen_cargado_con_exito.="codigo_eapb,";
				$query_registrar_resumen_cargado_con_exito.="nombre_eapb,";
				$query_registrar_resumen_cargado_con_exito.="numero_secuencia_validacion,";
				$query_registrar_resumen_cargado_con_exito.="nombre_archivo,";
				$query_registrar_resumen_cargado_con_exito.="fecha_validacion,";
				$query_registrar_resumen_cargado_con_exito.="fecha_corte,";
				$query_registrar_resumen_cargado_con_exito.="numeros_registros_archivo,";
				$query_registrar_resumen_cargado_con_exito.="mensaje_aceptacion";			
				$query_registrar_resumen_cargado_con_exito.=")";
				$query_registrar_resumen_cargado_con_exito.="VALUES";
				$query_registrar_resumen_cargado_con_exito.="(";
				$query_registrar_resumen_cargado_con_exito.="'".$cod_prestador."',";
				$query_registrar_resumen_cargado_con_exito.="'".$nombre_prestador."',";
				$query_registrar_resumen_cargado_con_exito.="'".$cod_eapb."',";
				$query_registrar_resumen_cargado_con_exito.="'".$nombre_eapb."',";
				$query_registrar_resumen_cargado_con_exito.="'".$numero_secuencia_actual."',";
				$query_registrar_resumen_cargado_con_exito.="'".$nombre_archivo_registrado."',";
				$query_registrar_resumen_cargado_con_exito.="'".$fecha_actual."',";
				$query_registrar_resumen_cargado_con_exito.="'$lineas_del_archivo',";
				$query_registrar_resumen_cargado_con_exito.="'Archivos validados con exito y cargados en el sistema'";
				$query_registrar_resumen_cargado_con_exito.=");";
				$error_bd_seq="";
				$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($query_registrar_resumen_cargado_con_exito, $error_bd_seq);
				if($error_bd_seq!="")
				{
					$error_mostrar_bd.=$error_bd_seq."<br>";
				}
				//FIN CARGA A RESUMEN EXITOSO
			}
			//FIN SUBIR A TABLA CARGADOS CON EXITO
				
			
			
			//SUBIR A TABLA CONSOLIDADOS PARA ARTE
			$estado_validacion_arte=2;
			if($hubo_inconsistencias_en_ARTE==false)
			{
				$estado_validacion_arte=1;
			}
			
			$query_id_info_prestador="";
			$query_id_info_prestador.="SELECT * FROM gios_prestador_servicios_salud WHERE cod_registro_especial_pss='".$cod_prestador."' ; ";
			$resultado_query_id_info_prestador=$coneccionBD->consultar2_no_crea_cierra($query_id_info_prestador);
			
			$tipo_id_prestador="";
			$nit_prestador="";
			$codigo_depto_prestador="";
			$codigo_municipio_prestador="";
			if(count($resultado_query_id_info_prestador)>0)
			{
				$tipo_id_prestador=$resultado_query_id_info_prestador[0]["cod_tipo_identificacion"];
				$nit_prestador=$resultado_query_id_info_prestador[0]["num_tipo_identificacion"];
				$codigo_depto_prestador=$resultado_query_id_info_prestador[0]["cod_depto"];
				$codigo_municipio_prestador=$resultado_query_id_info_prestador[0]["cod_municipio"];
			}	
			
			$query_delete_estado_validacion="";
			$query_delete_estado_validacion.="DELETE FROM gioss_tabla_consolidacion_registros_validados_r1393_arte ";
			$query_delete_estado_validacion.=" WHERE ";
			$query_delete_estado_validacion.=" codigo_eapb ='".$cod_eapb."' ";
			$query_delete_estado_validacion.=" AND ";
			$query_delete_estado_validacion.=" codigo_entidad_reportadora ='".$cod_prestador."' ";
			$query_delete_estado_validacion.=" AND ";
			$query_delete_estado_validacion.=" nombre_archivo ='".$nombre_archivo_registrado."' ";
			$query_delete_estado_validacion.=" AND ";
			$query_delete_estado_validacion.=" numero_secuencia ='".$numero_secuencia_actual."' ";
			$query_delete_estado_validacion.=" AND ";
			$query_delete_estado_validacion.=" fecha_corte ='".$fecha_de_corte."' ";
			$query_delete_estado_validacion.=" ; ";
			$error_bd_seq="";
			$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($query_delete_estado_validacion, $error_bd_seq);
			if($error_bd_seq!="")
			{
				$error_mostrar_bd.=$error_bd_seq."<br>";
			}
			
			$query_registrar_estado_validacion="";
			$query_registrar_estado_validacion.="INSERT INTO gioss_tabla_consolidacion_registros_validados_r1393_arte ";
			$query_registrar_estado_validacion.="(";
			$query_registrar_estado_validacion.="estado_validacion,";
			$query_registrar_estado_validacion.="fecha_validacion,";
			$query_registrar_estado_validacion.="numero_secuencia,";
			$query_registrar_estado_validacion.="nombre_archivo,";
			$query_registrar_estado_validacion.="fecha_corte,";
			$query_registrar_estado_validacion.="tipo_identificacion_entidad_reportadora,";
			$query_registrar_estado_validacion.="numero_identificacion_entidad_reportadora,";
			$query_registrar_estado_validacion.="codigo_eapb,";
			$query_registrar_estado_validacion.="tipo_regimen,";
			$query_registrar_estado_validacion.="codigo_entidad_reportadora,";
			$query_registrar_estado_validacion.="codigo_depto_prestador,";
			$query_registrar_estado_validacion.="codigo_municipio_prestador";
			$query_registrar_estado_validacion.=")";
			$query_registrar_estado_validacion.="VALUES";
			$query_registrar_estado_validacion.="(";
			$query_registrar_estado_validacion.="'".$estado_validacion_arte."',";
			$query_registrar_estado_validacion.="'".$fecha_actual."',";
			$query_registrar_estado_validacion.="'".$numero_secuencia_actual."',";
			$query_registrar_estado_validacion.="'".$nombre_archivo_registrado."',";
			$query_registrar_estado_validacion.="'".$fecha_de_corte."',";
			$query_registrar_estado_validacion.="'".$tipo_id_prestador."',";
			$query_registrar_estado_validacion.="'".$nit_prestador."',";
			$query_registrar_estado_validacion.="'".$cod_eapb."',";
			$query_registrar_estado_validacion.="'".$tipo_regimen_archivo."',";
			$query_registrar_estado_validacion.="'".$cod_prestador."',";
			$query_registrar_estado_validacion.="'".$codigo_depto_prestador."',";
			$query_registrar_estado_validacion.="'".$codigo_municipio_prestador."'";
			$query_registrar_estado_validacion.=");";
			$error_bd_seq="";
			$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($query_registrar_estado_validacion, $error_bd_seq);		
			if($error_bd_seq!="")
			{
				$error_mostrar_bd.=" EN CONSOLIDADOS ".$error_bd_seq."<br>";		
			}
			//FIN SUBIR A TABLA CONSOLIDADOS PARA ARTE
			
			
			//SUBIR A TABLA DE ESTADO INFORMACION PARA ARTE
			$estado_validacion_arte=2;
			if($hubo_inconsistencias_en_ARTE==false)
			{
				$estado_validacion_arte=1;
			}
			
			$query_id_info_prestador="";
			$query_id_info_prestador.="SELECT * FROM gios_prestador_servicios_salud WHERE cod_registro_especial_pss='".$cod_prestador."' ; ";
			$resultado_query_id_info_prestador=$coneccionBD->consultar2_no_crea_cierra($query_id_info_prestador);
			    
			$nombre_prestador="";
			$tipo_id_prestador="";
			$nit_prestador="";
			$codigo_depto_prestador="";
			$codigo_municipio_prestador="";
			if(count($resultado_query_id_info_prestador)>0)
			{
				$tipo_id_prestador=$resultado_query_id_info_prestador[0]["cod_tipo_identificacion"];
				$nit_prestador=$resultado_query_id_info_prestador[0]["num_tipo_identificacion"];
				$codigo_depto_prestador=$resultado_query_id_info_prestador[0]["cod_depto"];
				$codigo_municipio_prestador=$resultado_query_id_info_prestador[0]["cod_municipio"];
				$nombre_prestador=$resultado_query_id_info_prestador[0]["nom_entidad_prestadora"];
			}
			
			$query_info_eapb="SELECT * FROM gios_entidad_administradora WHERE cod_entidad_administradora='$cod_eapb' ;";
			$resultado_query_info_eapb=$coneccionBD->consultar2_no_crea_cierra($query_info_eapb);
			$nombre_eapb="";
			if(count($resultado_query_info_eapb)>0)
			{
				$nombre_eapb=$resultado_query_info_eapb[0]["nom_entidad_administradora"];
			}
			
			$query_descripcion_estado_validacion="";
			$query_descripcion_estado_validacion.=" SELECT * FROM gioss_estado_validacion_archivos WHERE codigo_estado_validacion='$estado_validacion_arte' ; ";
			$resultado_query_descripcion_estado_validacion=$coneccionBD->consultar2_no_crea_cierra($query_descripcion_estado_validacion);
			$descripcion_estado_validacion=$resultado_query_descripcion_estado_validacion[0]["descripcion_estado_validacion"];
			
			//query delete si ya habia sido subido para actualizar
			$query_delete_estado_informacion="";
			$query_delete_estado_informacion.=" DELETE FROM gioss_tabla_estado_informacion_r1393_arte ";
			$query_delete_estado_informacion.=" WHERE ";
			$query_delete_estado_informacion.=" codigo_eapb ='".$cod_eapb."' ";
			$query_delete_estado_informacion.=" AND ";
			$query_delete_estado_informacion.=" codigo_prestador_servicios ='".$cod_prestador."' ";
			$query_delete_estado_informacion.=" AND ";
			$query_delete_estado_informacion.=" nombre_del_archivo ='".$nombre_archivo_registrado."' ";
			$query_delete_estado_informacion.=" AND ";
			$query_delete_estado_informacion.=" numero_secuencia ='".$numero_secuencia_actual."' ";
			$query_delete_estado_informacion.=" AND ";
			$query_delete_estado_informacion.=" fecha_corte ='".$fecha_de_corte."' ";
			$query_delete_estado_informacion.=" ; ";
			$error_bd_seq="";
			$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($query_delete_estado_informacion, $error_bd_seq);
			if($error_bd_seq!="")
			{
				$error_mostrar_bd.=$error_bd_seq."<br>";
			}
			//fin query delete si ya habia sido subido para actualizar
			
			$query_registrar_estado_informacion="";
			$query_registrar_estado_informacion.="INSERT INTO gioss_tabla_estado_informacion_r1393_arte ";
			$query_registrar_estado_informacion.="(";
			$query_registrar_estado_informacion.="codigo_estado_informacion,";//1
			$query_registrar_estado_informacion.="nombre_estado_informacion,";//2
			$query_registrar_estado_informacion.="fecha_validacion,";//3
			$query_registrar_estado_informacion.="fecha_corte,";//4
			$query_registrar_estado_informacion.="numero_secuencia,";//5
			$query_registrar_estado_informacion.="nombre_del_archivo,";//6
			$query_registrar_estado_informacion.="codigo_eapb,";//7
			$query_registrar_estado_informacion.="nombre_eapb,";//8
			$query_registrar_estado_informacion.="codigo_prestador_servicios,";//9
			$query_registrar_estado_informacion.="tipo_identificacion_prestador,";//10
			$query_registrar_estado_informacion.="numero_identificacion_prestador,";//11	
			$query_registrar_estado_informacion.="total_registros,";//12
			$query_registrar_estado_informacion.="codigo_departamento,";//13
			$query_registrar_estado_informacion.="codigo_municipio";//14
			$query_registrar_estado_informacion.=")";
			$query_registrar_estado_informacion.="VALUES";
			$query_registrar_estado_informacion.="(";
			$query_registrar_estado_informacion.="'".$estado_validacion_arte."',";//1
			$query_registrar_estado_informacion.="'".$descripcion_estado_validacion."',";//2
			$query_registrar_estado_informacion.="'".$fecha_actual."',";//3
			$query_registrar_estado_informacion.="'".$fecha_de_corte."',";//4
			$query_registrar_estado_informacion.="'".$numero_secuencia_actual."',";//5
			$query_registrar_estado_informacion.="'".$nombre_archivo_registrado."',";//6
			$query_registrar_estado_informacion.="'".$cod_eapb."',";//7
			$query_registrar_estado_informacion.="'".$nombre_eapb."',";//8
			$query_registrar_estado_informacion.="'".$cod_prestador."',";//9
			$query_registrar_estado_informacion.="'".$tipo_id_prestador."',";//10
			$query_registrar_estado_informacion.="'".$nit_prestador."',";//11
			$query_registrar_estado_informacion.="'".$lineas_del_archivo."',";//12
			$query_registrar_estado_informacion.="'".$codigo_depto_prestador."',";//13
			$query_registrar_estado_informacion.="'".$codigo_municipio_prestador."' ";//14
			$query_registrar_estado_informacion.=");";
			$error_bd_seq="";
			$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($query_registrar_estado_informacion, $error_bd_seq);		
			if($error_bd_seq!="")
			{
				$error_mostrar_bd.=$error_bd_seq."<br>";
			}
			//FIN SUBIR A TABLA DE ESTADO INFORMACION PARA ARTE
			
			
			
			if(connection_aborted()==false)
			{
				echo "<script>document.getElementById('mensaje').innerHTML='Se ha terminado de revisar y validar el archivo ARTE';</script>";
				ob_flush();
				flush();
			}
			
			$errores.=procesar_mensaje2($error_mostrar_bd);
		
		}//fin if nombre archivo valido
		
		if($hubo_inconsistencias_en_ARTE)
		{
			$se_genero_archivo_de_inconsistencias=true;		
			$errores.="Hubo inconsistencias en el archivo ARTE.<br>";
			
		}
		
		//CREAR ZIP
		$archivos_a_comprimir=array();
		$archivos_a_comprimir[0]=$ruta_archivo_inconsistencias_ARTE;
		$ruta_zip=$rutaTemporal."inconsistencias1393ARTE_".$cod_prestador."_".$fecha_actual."_".$tiempo_actual_string.'.zip';
		if(file_exists($ruta_zip))
		{
			unlink($ruta_zip);
		}
		$result_zip = create_zip($archivos_a_comprimir,$ruta_zip);	
		//FIN CREAR ZIP
		
		//YA NO ESTA EN USO EL ARCHIVO
		$query_update_esta_siendo_procesado="";
		$query_update_esta_siendo_procesado.=" UPDATE gioss_1393_esta_validando_actualmente ";
		$query_update_esta_siendo_procesado.=" SET esta_ejecutando='NO',";
		$query_update_esta_siendo_procesado.=" ruta_archivo_descarga='$ruta_zip' ";
		$query_update_esta_siendo_procesado.=" WHERE fecha_remision='".$fecha_de_corte."' ";
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
				echo "<script>alert('error al actualizar el estado actual de validacion en tiempo real  1393 ');</script>";
			}
		}
		//FIN YA NO ESTA EN USO EL ARCHIVO
		
		//BOTONES DESCARGA
		$botones="";
		$botones.=" <input type=\'button\' value=\'Descargar archivo de inconsistencias para ARTE\'  class=\'btn btn-success color_boton\' onclick=\"download_inconsistencias_campos(\'$ruta_zip\');\"/> ";
		
		//FIN BOTONES DESCARGA
		
		//ESCRIBE LOS MENSAJES AL FINALIZAR LA VALIDACION
		if($errores!="")
		{
			if(connection_aborted()==false)
			{
				echo "<script>document.getElementById('div_mensaje_error').style.display='inline';</script>";
				echo "<script>document.getElementById('parrafo_mensaje_error').innerHTML='$errores <br> Con el numero de secuencia: $numero_secuencia_actual <br> $botones';</script>";
				ob_flush();
				flush();
			}
		}
		else
		{
			if(connection_aborted()==false)
			{
				echo "<script>document.getElementById('div_mensaje_exito').style.display='inline';</script>";
				echo "<script>document.getElementById('parrafo_mensaje_exito').innerHTML='$exitos <br> Con el numero de secuencia: $numero_secuencia_actual <br> $botones';</script>";
				ob_flush();
				flush();
			}
		}
		//FIN ESCRIBE MENSAJES FINALES
		
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
				$mail->Subject = "Inconsistencias ARTE 1393 ";
				$mail->AltBody = "Cordial saludo,\n El sistema ha determinado que su archivo(s)  contiene diversas inconsistencias,\n las cuales pueden ser: campos con informaci�n inconsistente, usuarios duplicados � el uso de caracteres especiales(acentos,'�' o �,�, �, �, �)";
		    
				$mail->MsgHTML("Cordial saludo,\n El sistema ha determinado que su archivo(s)  contiene diversos errores, con el numero de secuencia: $numero_secuencia_actual.<strong>GIOSS</strong>.");
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
	}
}//fin if envia datos y archivo para validar
else if(isset($_POST["accion"]) && $_POST["accion"]=="validar" )
{
	$ultimo_error="";
	if(!( isset($_FILES["1393_ARTE_file"])))
	{
		$ultimo_error="El archivo no se cargo ";
	}
	else if(!($_FILES["1393_ARTE_file"]["error"]==0))
	{
		$ultimo_error="Error con el archivo de tipo ".$_FILES["1393_ARTE_file"]["error"];
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