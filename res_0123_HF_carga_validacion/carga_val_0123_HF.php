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

require_once '../utiles/configuracion_global_email.php';

//require_once 'validador_0123_HF.php';

require_once '../utiles/queries_utiles_bd.php';

require_once '../utiles/crear_zip.php';

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
			$prestador.="<option value='".$prestador_asociado_eapb['codigo_entidad']."' selected>".$prestador_asociado_eapb['codigo_entidad']." ".$prestador_asociado_eapb['nombre_de_la_entidad']."</option>";
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
$query_periodos_rips="SELECT * FROM gioss_periodo_reporte_0123_hf ORDER BY codigo_periodo;";
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

$smarty->assign("proveniente_de_prestador_o_eapb", $proveniente_de_prestador_o_eapb, true);

$smarty->assign("campo_periodo", $selector_periodo, true);
$smarty->assign("campo_eapb", $eapb, true);
$smarty->assign("campo_prestador", $prestador, true);
$smarty->assign("mensaje_proceso", $mensaje, true);
$smarty->assign("mostrarResultado", $mostrarResultado, true);
$smarty->assign("nombre", $nombre, true);
$smarty->assign("menu", $menu, true);
$smarty->display('carga_val_0123_HF.html.tpl');

//echo "<script>alert('$HOST_CONF_EMAIL');</script>";

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
$query3_detalle_validacion="SELECT * FROM gioss_detalle_inconsistencia_0123_hf;";
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

$fecha_para_archivo=str_replace("-", "", $fecha_actual ).str_replace(":", "", $tiempo_actual );

$rutaTemporal = '../TEMPORALES/';
$error_mensaje="";

$ruta_archivo_inconsistencias_HF="";
$se_genero_archivo_de_inconsistencias=false;
$verificacion_es_diferente_prestador_en_ct=false;
$verificacion_fecha_diferente_en_ct=false;
$verificacion_numero_remision=false;
$verificacion_ya_se_valido_con_exito=false;

$mensaje_advertencia_tiempo="";
$mensaje_advertencia_tiempo .="Estimado usuario, se ha iniciado el proceso de validaci&oacuten del archivo,<br> lo que puede tomar varios minutos, dependiendo del volumen de registros.<br>";
$mensaje_advertencia_tiempo .="Una vez validado, se genera el Logs de errores, el cual se enviar&aacute a su Correo electr&oacutenico o puede descargarlo directamente del aplicat&iacutevo.<br>";
$mensaje_advertencia_tiempo .="Si la validaci&oacuten es exitosa, los datos se cargar&aacuten en la base de datos y se dar&aacute por aceptada la informaci&oacuten reportada<br>";

if(isset($_POST["accion"]) && $_POST["accion"]=="validar" && isset($_FILES["0123_HEMOFILIA_file"]) && $_FILES["0123_HEMOFILIA_file"]["error"]==0)
{	
	

	

	$nombre_archivo_file=explode(".",$_FILES["0123_HEMOFILIA_file"]["name"])[0];
	$nombre_archivo_registrado=explode(".",$_FILES["0123_HEMOFILIA_file"]["name"])[0];	
	
	$numero_de_remision=$_POST["numero_de_remision"];

	if (!ctype_digit($numero_de_remision)) {
		$numero_de_remision = '00';
	}

	$archivo_norma=$_FILES["0123_HEMOFILIA_file"];
	$archivo_fuente_after_post=$_FILES["0123_HEMOFILIA_file"];
	$cod_prestador=$_POST["prestador"];
	$cod_eapb=$_POST["eapb"];	
	$codigo_periodo=explode("::",$_POST["periodo"])[0];
	$fecha_de_corte=$_POST["year_de_corte"]."-".explode("::",$_POST["periodo"])[1];

	$tipo_entidad_que_efectua_el_cargue=$_POST["tipo_archivo_norma"];
	
	if($tipo_entidad_que_efectua_el_cargue=="agrupado_eapb")
	{
		$cod_prestador="AGRUP_EAPB";
	}
	else if($tipo_entidad_que_efectua_el_cargue=="individual_ips")
	{		
		$cod_prestador=$_POST["prestador"];
	}

	//echo $cod_prestador."<br>";

	//DIRECTORIO DE LOS ARCHIVOS
    $rutaTemporal="../TEMPORALES/";
    $nombre_archivo_sin_extension=explode(".",$nombre_archivo_registrado)[0];
    $nueva_carpeta=$rutaTemporal.$nombre_archivo_sin_extension.$fecha_para_archivo."V";
    if(!file_exists($nueva_carpeta))
    {
	    mkdir($nueva_carpeta, 0777);
    }
    else
    {
	    $files_to_erase = glob($nueva_carpeta."/*"); // get all file names
	    foreach($files_to_erase as $file_to_be_erased)
	    { // iterate files
	      if(is_file($file_to_be_erased))
	      {
			unlink($file_to_be_erased); // delete file
	      }
	    }
    }
    $rutaTemporal=$nueva_carpeta."/";
    //FIN DIRECTORIO DE LOS ARCHIVOS

    //parte verifica si es mayor de 03-31 del mismo year
	$year_corte_inferior=trim($_POST["year_de_corte"]);
	$year_corte_para_buscar=trim($_POST["year_de_corte"]);
	$mitad_year_ver=trim($_POST["year_de_corte"])."-03-31";
	$diferencia_dias_con_mitad_year=diferencia_dias_entre_fechas($fecha_de_corte,$mitad_year_ver);
	if($diferencia_dias_con_mitad_year<0)
	{
		$year_corte_para_buscar="".(intval(trim($_POST["year_de_corte"]))+1);
	}//fin if
	else
	{
		$year_corte_inferior="".(intval(trim($_POST["year_de_corte"]))-1);
	}
	//echo "year_corte_inferior $year_corte_inferior year_corte_para_buscar $year_corte_para_buscar<br>";
	//fin parte verifica si es mayor de 03-31 del mismo year

	//PARTE FECHA INFERIOR Y NUEVA FECHA DE CORTE
	$fecha_corte_anterior_registrada_nombre=$fecha_de_corte;
	$fecha_inferior_pv="";
	$fecha_inferior_pv=$year_corte_inferior."-04-01";
	$fecha_de_corte=$year_corte_para_buscar."-03-31";
	//no tabla variados que contiene algunos rangos de years
	//revisar esto en ERC	
	//FIN PARTE FECHA INFERIOR Y NUEVA FECHA DE CORTE

	//SELECTOR VERSION
	$nombre_base_version="validador_0123_HF_v";
	$array_fecha_corte=explode("-", $fecha_de_corte);
	$year_corte_para_version_validacion=trim($array_fecha_corte[0]);
	$directorio_validacion_per_year='../res_0123_HF/';
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

	$mensaje_advertencia_tiempo="";
	$mensaje_advertencia_tiempo .="Estimado usuario, se ha iniciado el proceso de validaci&oacuten del archivo <b> $nombre_archivo_registrado </b>,<br> lo que puede tomar varios minutos, dependiendo del volumen de registros.<br>";
	$mensaje_advertencia_tiempo .="Una vez validado, se genera el Logs de errores, el cual se enviar&aacute a su Correo electr&oacutenico o puede descargarlo directamente del aplicat&iacutevo.<br>";
	$mensaje_advertencia_tiempo .="Si la validaci&oacuten es exitosa, los datos se cargar&aacuten en la base de datos y se dar&aacute por aceptada la informaci&oacuten reportada<br>";

	
	//CREACION ARCHIVO REGISTROS BUENOS, MALOS, E INCONSISTENCIAS REGISTROS MALOS
	
	//creacion archivo separado registros buenos
	$ruta_registros_buenos=$rutaTemporal.$nombre_archivo_registrado."_RB".$fecha_actual."_".$tiempo_actual_string.".txt";
	if(file_exists($ruta_registros_buenos))
	{
		unlink($ruta_registros_buenos);
	}
	$file_archivo_registros_buenos = fopen($ruta_registros_buenos, "w") or die("fallo la creacion del archivo");
	$titulo_rb="REGISTROS BUENOS";
	fwrite($file_archivo_registros_buenos,$titulo_rb);
	
	//creacion archivo separado registros malos
	$ruta_registros_malos=$rutaTemporal.$nombre_archivo_registrado."_RM".$fecha_actual."_".$tiempo_actual_string.".txt";
	if(file_exists($ruta_registros_malos))
	{
		unlink($ruta_registros_malos);
	}
	$file_archivo_registros_malos = fopen($ruta_registros_malos, "w") or die("fallo la creacion del archivo");
	$titulo_rb="REGISTROS MALOS";
	fwrite($file_archivo_registros_malos,$titulo_rb);
	
	//creacion archivo separado inconsistencias registros malos
	$ruta_inconsistencias_registros_malos=$rutaTemporal."inconHF_RM_".$fecha_actual."_".$tiempo_actual_string.".csv";
	if(file_exists($ruta_inconsistencias_registros_malos))
	{
		unlink($ruta_inconsistencias_registros_malos);
	}
	$file_archivo_incons_registros_malos = fopen($ruta_inconsistencias_registros_malos, "w") or die("fallo la creacion del archivo");
	$titulos="";
	$titulos.="consecutivo,nombre archivo,codigo tipo inconsistencia,tipo inconsistencia,codigo grupo inconsistencia,grupo inconsistencia,";
	$titulos.="codigo detalle inconsistencia,detalle inconsistencia, numero de linea, numero de campo, tipo identificacion, numero identificacion";
	//PARTE REESCRIBE TITULOS INCONSISTENCIAS PARA CSV SIMPLE DE ABRIR
    $columnas_titulos_inconsistencias_para_bd=explode(",", $titulos);
    $error_titulos_procesado_csv_excel="";
    $error_titulos_procesado_csv_excel.="=\"".implode("\";=\"", $columnas_titulos_inconsistencias_para_bd)."\"";
    //FIN PARTE REESCRIBE TITULOS INCONSISTENCIAS PARA CSV SIMPLE DE ABRIR
	fwrite($file_archivo_incons_registros_malos,$error_titulos_procesado_csv_excel."\n");
	
	//FIN CREACION REGISTROS BUENOS, MALOS, E INCONSISTENCIAS REGISTROS MALOS

	//abre o crea el archivo donde se escribiran las inconsistencias
	//CREA ARCHIVO INCONSISTENCIAS TOTALES
	$ruta_archivo_inconsistencias_HF=$rutaTemporal."inconsistencias0123HF_".$cod_prestador."_".$fecha_actual."_".$tiempo_actual_string.".csv";
	if(file_exists($ruta_archivo_inconsistencias_HF))
	{
		unlink($ruta_archivo_inconsistencias_HF);
	}
	$file_inconsistencias_r4725_HF = fopen($ruta_archivo_inconsistencias_HF, "w") or die("fallo la creacion del archivo");
	$titulos="";
	$titulos.="consecutivo,nombre archivo,codigo tipo inconsistencia,tipo inconsistencia,codigo grupo inconsistencia,grupo inconsistencia,";
	$titulos.="codigo detalle inconsistencia,detalle inconsistencia, numero de linea, numero de campo, tipo de identificacion, numero de identificacion";
	//PARTE REESCRIBE TITULOS INCONSISTENCIAS PARA CSV SIMPLE DE ABRIR
    $columnas_titulos_inconsistencias_para_bd=explode(",", $titulos);
    $error_titulos_procesado_csv_excel="";
    $error_titulos_procesado_csv_excel.="=\"".implode("\";=\"", $columnas_titulos_inconsistencias_para_bd)."\"";
    //FIN PARTE REESCRIBE TITULOS INCONSISTENCIAS PARA CSV SIMPLE DE ABRIR
	fwrite($file_inconsistencias_r4725_HF,$error_titulos_procesado_csv_excel."\n");
	//FIN CREA ARCHIVO INCONSISTENCIAS TOTALES

	//CREACION ARCHIVO PARA REGISTROS QUE TIENEN AFILIADO EN BASE DE DATOS
	$ruta_registros_que_tienen_afiliados=$rutaTemporal.$nombre_archivo_registrado."_SA".$fecha_actual."_".$tiempo_actual_string.".txt";
	if(file_exists($ruta_registros_que_tienen_afiliados))
	{
		unlink($ruta_registros_que_tienen_afiliados);
	}
	$file_archivo_registros_que_tienen_afiliados = fopen($ruta_registros_que_tienen_afiliados, "w") or die("fallo la creacion del archivo");
	$titulo_rsa="REGISTROS QUE TIENEN AFILIADOS EN BD";
	fwrite($file_archivo_registros_que_tienen_afiliados,$titulo_rsa);	
	//FIN CREACION ARCHIVO PARA REGISTROS QUE TIENEN AFILIADO EN BASE DE DATOS

	//CREACION ARCHIVO PARA REGISTROS QUE TIENEN AFILIADO EN BASE DE DATOS
	$ruta_registros_que_no_tienen_afiliados=$rutaTemporal.$nombre_archivo_registrado."_NA".$fecha_actual."_".$tiempo_actual_string.".txt";
	if(file_exists($ruta_registros_que_no_tienen_afiliados) )
	{
		unlink($ruta_registros_que_no_tienen_afiliados);
	}
	$file_archivo_registros_que_no_tienen_afiliados = fopen($ruta_registros_que_no_tienen_afiliados, "w") or die("fallo la creacion del archivo");
	$titulo_rna="REGISTROS QUE NO TIENEN AFILIADOS";
	fwrite($file_archivo_registros_que_no_tienen_afiliados,$titulo_rna);	
	//FIN CREACION ARCHIVO PARA REGISTROS QUE TIENEN AFILIADO EN BASE DE DATOS

	//CREACION ARCHIVO PARA REGISTROS QUE TIENEN AFILIADO EN BASE DE DATOS
	$ruta_log_registros_modificados=$rutaTemporal.$nombre_archivo_registrado."_LOGMODSA".$fecha_actual."_".$tiempo_actual_string.".txt";
	if(file_exists($ruta_log_registros_modificados) )
	{
		unlink($ruta_log_registros_modificados);
	}
	$file_archivo_log_registros_modificados = fopen($ruta_log_registros_modificados, "w") or die("fallo la creacion del archivo");
	$titulo_rlmsa="LOG MODIFICADOS QUE SI TIENEN AFILIADOS EN BD";
	fwrite($file_archivo_log_registros_modificados,$titulo_rlmsa);	
	//FIN CREACION ARCHIVO PARA REGISTROS QUE TIENEN AFILIADO EN BASE DE DATOS

	//CREACION ARCHIVOS AFILIADO EXISTE CAMBIOS SEXO FECHA NACIMIENTO
	//archivo afiliado existe CAMBIO SEXO
	$ruta_archivo_afiliado_existe_cambio_sexo=$rutaTemporal."afiliados_existe_cambio_sexo.txt";
	if(file_exists($ruta_archivo_afiliado_existe_cambio_sexo) )
	{
		unlink($ruta_archivo_afiliado_existe_cambio_sexo);
	}//fin if
	$file_archivo_afiliado_existe_cambio_sexo= fopen($ruta_archivo_afiliado_existe_cambio_sexo, "w") or die("fallo la creacion del archivo");
	fwrite($file_archivo_afiliado_existe_cambio_sexo, "ARCHIVO AFILIADOS EXISTE, SE REALIZO CAMBIO EN EL ARCHIVO POR EL SEXO DEL AFILIADO ENCONTRADO EN EL SISTEMA"); 

	//archivo afiliado existe CAMBIO FECHA NACIMIENTO
	$ruta_archivo_afiliado_existe_cambio_fecha_nacimiento=$rutaTemporal."afiliados_existe_cambio_fecha_nacimiento.txt";
	if(file_exists($ruta_archivo_afiliado_existe_cambio_fecha_nacimiento) )
	{
		unlink($ruta_archivo_afiliado_existe_cambio_fecha_nacimiento);
	}//fin if
	$file_archivo_afiliado_existe_cambio_fecha_nacimiento= fopen($ruta_archivo_afiliado_existe_cambio_fecha_nacimiento, "w") or die("fallo la creacion del archivo");
	fwrite($file_archivo_afiliado_existe_cambio_fecha_nacimiento, "ARCHIVO AFILIADOS EXISTE, SE REALIZO CAMBIO EN EL ARCHIVO POR LA FECHA DE NACIMIENTO DEL AFILIADO ENCONTRADO EN EL SISTEMA"); 						  
	
	//FIN CREACION ARCHIVOS AFILIADO EXISTE CAMBIOS SEXO FECHA NACIMIENTO

	//CREACION REPORTE CALIFICACION CAMPOS
	$codigo_perido_para_archivo=$codigo_periodo;
	if(strlen($codigo_perido_para_archivo)==1)
	{
		$codigo_perido_para_archivo="0".$codigo_periodo;
	}//fin if
	$ruta_archivo_reporte_calificacion_campos=$rutaTemporal."EvaluacionResultadoValidacion".$cod_eapb.$codigo_perido_para_archivo.str_replace('-', '', $fecha_actual).".csv";
	if(file_exists($ruta_archivo_reporte_calificacion_campos) )
	{
		unlink($ruta_archivo_reporte_calificacion_campos);
	}//fin if
	$file_archivo_reporte_calificacion_campos= fopen($ruta_archivo_reporte_calificacion_campos, "w") or die("fallo la creacion del archivo");
	fwrite($file_archivo_reporte_calificacion_campos, ""); 	

	fclose($file_archivo_reporte_calificacion_campos);
	//FIN CREACION REPORTE CALIFICACION CAMPOS
	
	//CREACION REPORTE RESULTADO VALIDACION por ips(si es posible)
	$ruta_archivo_reporte_registros_por_ips=$rutaTemporal."RESULTADOVALIDACIONPORIPS".$cod_eapb.$codigo_perido_para_archivo.str_replace('-', '', $fecha_actual).".csv";
	if(file_exists($ruta_archivo_reporte_registros_por_ips) )
	{
		unlink($ruta_archivo_reporte_registros_por_ips);
	}//fin if
	$file_archivo_reporte_registros_por_ips= fopen($ruta_archivo_reporte_registros_por_ips, "w") or die("fallo la creacion del archivo");
	fwrite($file_archivo_reporte_registros_por_ips, ""); 	
	
	fclose($file_archivo_reporte_registros_por_ips);
	//FIN CREACION REPORTE RESULTADO VALIDACION por ips(si es posible)

	//PARTE LLENADO ARRAY PARA EL NUMERO DE CAMPO REAL INDEXADO POR EL NUMERO DE ORDEN(NUMERO CAMPO SISTEMATICO)
	$query_consulta_estructura_numero_campos="";
	$query_consulta_estructura_numero_campos.=" SELECT numero_de_orden,numero_de_campo,descripcion_nombre_campo FROM gioss_estructura_campos_por_norma_a_reportar ";
	$query_consulta_estructura_numero_campos.=" WHERE ";
	$query_consulta_estructura_numero_campos.=" codigo_tipo_norma_obligatoria='07' ";
	$query_consulta_estructura_numero_campos.=" AND ";
	$query_consulta_estructura_numero_campos.=" codigo_tipo_archivo='0701' ORDER BY numero_de_orden ";
	$query_consulta_estructura_numero_campos.=" ; ";
	$resultado_query_estructura_campos=$coneccionBD->consultar2_no_crea_cierra($query_consulta_estructura_numero_campos);
	
	$array_numero_campo_bd=array();
	$array_descripcion_nombre_campo_bd=array();
	$array_numero_orden=array();
	if(count($resultado_query_estructura_campos)>0)
	{
		foreach($resultado_query_estructura_campos as $estructura_campo)
		{
			$array_numero_campo_bd[intval($estructura_campo["numero_de_orden"])]=$estructura_campo["numero_de_campo"];
			$array_descripcion_nombre_campo_bd[intval($estructura_campo["numero_de_orden"])]=$estructura_campo["descripcion_nombre_campo"];
			$array_numero_orden[$estructura_campo["numero_de_campo"]]=intval($estructura_campo["numero_de_orden"]);
		}
	}
	//FIN PARTE LLENADO ARRAY PARA EL NUMERO DE CAMPO REAL INDEXADO POR EL NUMERO DE ORDEN(NUMERO CAMPO SISTEMATICO)

	//INICIALIZACION ARRAYS PARA CONTADOR ERRORES POR CAMPO INDIVIDUAL
	$array_contador_total_errores_obligatorios_campo=array();
	$cont_llenado=0;
    while($cont_llenado<95)
    {
    	$numero_campo_actual=trim($array_numero_campo_bd[$cont_llenado]);
    	$array_contador_total_errores_obligatorios_campo[$numero_campo_actual]=0;
    	$cont_llenado++;
    }//fin while
    $array_contador_total_errores_obligatorios_campo[999]=0;

    $array_contador_total_inconsistencias_campo_0105=array();
    $cont_llenado=0;
    while($cont_llenado<95)
    {
    	$numero_campo_actual=trim($array_numero_campo_bd[$cont_llenado]);
    	$array_contador_total_inconsistencias_campo_0105[$numero_campo_actual]=0;
    	$cont_llenado++;
    }//fin while
    $array_contador_total_inconsistencias_campo_0105[999]=0;

    $array_contador_total_inconsistencias_campo_0104=array();
    $cont_llenado=0;
    while($cont_llenado<95)
    {
    	$numero_campo_actual=trim($array_numero_campo_bd[$cont_llenado]);
    	$array_contador_total_inconsistencias_campo_0104[$numero_campo_actual]=0;
    	$cont_llenado++;
    }//fin while
    $array_contador_total_inconsistencias_campo_0104[999]=0;

    $array_contador_total_inconsistencias_campo_0103=array();
    $cont_llenado=0;
    while($cont_llenado<95)
    {
    	$numero_campo_actual=trim($array_numero_campo_bd[$cont_llenado]);
    	$array_contador_total_inconsistencias_campo_0103[$numero_campo_actual]=0;
    	$cont_llenado++;
    }//fin while
    $array_contador_total_inconsistencias_campo_0103[999]=0;

    $array_contador_total_inconsistencias_campo_0102=array();
    $cont_llenado=0;
    while($cont_llenado<95)
    {
    	$numero_campo_actual=trim($array_numero_campo_bd[$cont_llenado]);
    	$array_contador_total_inconsistencias_campo_0102[$numero_campo_actual]=0;
    	$cont_llenado++;
    }//fin while
    $array_contador_total_inconsistencias_campo_0102[999]=0;

    $array_contador_total_inconsistencias_campo_0101=array();
    $cont_llenado=0;
    while($cont_llenado<95)
    {
    	$numero_campo_actual=trim($array_numero_campo_bd[$cont_llenado]);
    	$array_contador_total_inconsistencias_campo_0101[$numero_campo_actual]=0;
    	$cont_llenado++;
    }//fin while
    $array_contador_total_inconsistencias_campo_0101[999]=0;
    //FIN INICIALIZACION ARRAYS PARA CONTADOR ERRORES POR CAMPO INDIVIDUAL
	
	
	$errores="";
	$exitos="";
	$tipo_regimen_archivo="";
	
	//PARTE VALIDACION ESTRUCTURA NOMBRE DEL ARCHIVO HF
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
			$ruta_archivo_hf = $rutaTemporal.$archivo_norma['name'];
			move_uploaded_file($archivo_norma['tmp_name'], $ruta_archivo_hf);
			
			$array_nombre_sin_sigla=explode("HEMOFILIA",$archivo_norma['name']);
			if(count($array_nombre_sin_sigla)!=2)
			{
				$es_valido_nombre_archivo=false;
				$errores.="El encabezado del archivo $nombre_archivo_file no corresponde a un archivo HEMOFILIA. <br>";
			}
			else
			{
				$nombre_archivo_fecha_prestador=$array_nombre_sin_sigla[0];
				$prestador_del_nombre_archivo="";
				$year="";
				$mes="";
				$dia="";
				$prestador_del_nombre_archivo=substr($nombre_archivo_fecha_prestador,9,12);
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
					$cod_eapb_temporal_new_name=$cod_eapb;
                    while(strlen($cod_eapb_temporal_new_name)<12)
                    {
                        $cod_eapb_temporal_new_name="0".$cod_eapb_temporal_new_name;
                    }//fin while

					if($prestador_del_nombre_archivo!=$cod_eapb_temporal_new_name && (strlen($nombre_sin_txt_para_verificacion)==31
                       || strlen($nombre_sin_txt_para_verificacion)==33) )
                    {
                        $es_valido_nombre_archivo=false;
                        $errores.="La eapb $prestador_del_nombre_archivo, no corresponde a la eapb en seleccionada $cod_eapb_temporal_new_name para el tipo de validacion agrupado. <br>";
                    }
				}//fin else if

				/*if($tipo_entidad_que_efectua_el_cargue=="individual_ips")
				{
					$regimen_nombre=substr($nombre_archivo_fecha_prestador,20,1);
					if($regimen_nombre!="C" && $regimen_nombre!="S" && $regimen_nombre!="P" && $regimen_nombre!="N" && $regimen_nombre!="E" && $regimen_nombre!="O")
					{
						$es_valido_nombre_archivo=false;
						$errores.="El regimen ($regimen_nombre) no corresponde a C-S-P-N-E. <br>";
					}
				}*///fin if
				//echo "<script>alert('$regimen_nombre');</script>";

				/*para agrupado remover de las tablas gioss_tabla_estado_informacion_r0123_hf y gioss_numero_de_secuencia_archivos_hf las condiciones de llave foranea para prestador
				ALTER TABLE gioss_numero_de_secuencia_archivos_hf DROP CONSTRAINT gioss_numero_de_secuencia_ar_codigo_prestador_servicios_s_fkey1;
				ALTER TABLE gioss_tabla_estado_informacion_r0123_hf DROP CONSTRAINT gioss_tabla_estado_informacion_codigo_prestador_servicios_fkey3;
				ALTER TABLE gioss_tabla_registros_cargados_exito_r0123_hf DROP CONSTRAINT gioss_tabla_registros_cargado_codigo_prestador_reportante_fkey3;
				ALTER TABLE gioss_tabla_registros_no_cargados_rechazados_r0123_hf DROP CONSTRAINT gioss_tabla_registros_no_carg_codigo_prestador_reportante_fkey3;
				ALTER TABLE gioss_tabla_consolidacion_registros_validados_r0123_hf DROP CONSTRAINT gioss_tabla_consolidacion_regi_codigo_entidad_reportadora_fkey3;
				*/

				//LONGITUD INCORRECTA para erc y vih es 32|22|19para hf debe ser 31|21|18 por el menos un caracter
				$nombre_sin_txt_para_verificacion=str_replace(".txt","",$archivo_fuente_after_post['name']);
				if(strlen($nombre_sin_txt_para_verificacion)!=31 && strlen($nombre_sin_txt_para_verificacion)!=33 && $tipo_entidad_que_efectua_el_cargue=="individual_ips")
				{
					$es_valido_nombre_archivo=false;
					$errores.="La longitud del archivo sin incluir el .txt debe ser de 31, 33 caracteres no ".strlen($nombre_sin_txt_para_verificacion)."  <br>";
				}
				else if($tipo_entidad_que_efectua_el_cargue=="agrupado_eapb")
				{
					if(strlen($nombre_sin_txt_para_verificacion)!=31
					   && strlen($nombre_sin_txt_para_verificacion)!=18
					   && strlen($nombre_sin_txt_para_verificacion)!=21
					   )
					{
						if(strlen($nombre_sin_txt_para_verificacion)<18
						   || (strlen($nombre_sin_txt_para_verificacion)>18 && strlen($nombre_sin_txt_para_verificacion)<21)
						   )
						{
							$es_valido_nombre_archivo=false;
							$errores.="La longitud del archivo sin incluir el .txt debe ser de 19 caracteres no ".strlen($nombre_sin_txt_para_verificacion)."  <br>";
						}
						else if(
						(strlen($nombre_sin_txt_para_verificacion)>21 && strlen($nombre_sin_txt_para_verificacion)<31)
						|| strlen($nombre_sin_txt_para_verificacion)>31
						)
						{
							$es_valido_nombre_archivo=false;
							$errores.="La longitud del archivo sin incluir el .txt debe ser de 32 caracteres no ".strlen($nombre_sin_txt_para_verificacion)."  <br>";
						}
					}//fin if
				}//fin else if				
				//FIN LONGITUD INCORRECTA

				//REGIMEN
				/*if($tipo_entidad_que_efectua_el_cargue=="individual_ips")
				{
					//esta comentado debido a que hay que verificar tablas asi como en ERC para que acepte regimen alfanumerico
					$tipo_regimen_archivo=substr($nombre_archivo_fecha_prestador,20,1); 
					//echo "<script>alert('$tipo_regimen_archivo');</script>";
				}
				else
				{
					$tipo_regimen_archivo="C";
					if(isset($_POST["selector_regimen_para_agrupados"])
					   && $_POST["selector_regimen_para_agrupados"]!="none"
					   )
					{
						$tipo_regimen_archivo=$_POST["selector_regimen_para_agrupados"];
					}
				}*/
				//FIN REGIMEN
				
				//$eapb_del_nombre_del_archivo=substr($nombre_archivo_fecha_prestador,21,6);
				/*$eapb_del_nombre_del_archivo="";
				if($tipo_entidad_que_efectua_el_cargue=="individual_ips")
				{
					$eapb_del_nombre_del_archivo=substr($nombre_archivo_fecha_prestador,21,6);
				}
				else if($tipo_entidad_que_efectua_el_cargue=="agrupado_eapb")
				{
					$nombre_sin_txt_para_verificacion=str_replace(".txt","",$archivo_fuente_after_post['name']);
					if(strlen($nombre_sin_txt_para_verificacion)==31)
					{
						$eapb_del_nombre_del_archivo=substr($nombre_archivo_fecha_prestador,21,6);
					}
					else if(strlen($nombre_sin_txt_para_verificacion)==18 || strlen($nombre_sin_txt_para_verificacion)==21)
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
				
				$tipo_entidad_reportadora_del_nombre_archivo=substr($nombre_archivo_fecha_prestador,8,2);
				$tipo_regimen_archivo=$tipo_entidad_reportadora_del_nombre_archivo;				
				if ($tipo_entidad_reportadora_del_nombre_archivo!="NI" && $tipo_entidad_reportadora_del_nombre_archivo!="DI" && $tipo_entidad_reportadora_del_nombre_archivo!="MU" && $tipo_entidad_reportadora_del_nombre_archivo!="DE")
				{
					$es_valido_nombre_archivo=false;
					$errores.="El tipo de prestador indicado en el nombre del archivo ( $tipo_entidad_reportadora_del_nombre_archivo ), no corresponde a NI, MU, DI, DE . <br>";
				}
				
				
				if($eapb_del_nombre_del_archivo!=$cod_eapb_temporal)
				{
					$es_valido_nombre_archivo=false;
					$errores.="El codigo de la EAPB indicada en el nombre del archivo ( $eapb_del_nombre_del_archivo ), no corresponde al codigo de la EAPB a reportar ( $cod_eapb ). <br>";
				}*/
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
			$errores.="El nombre del archivo para HEMOFILIA es invalido. <br>";
		}
	}//fin else
	//FIN PARTE VALIDACION ESTRUCTURA NOMBRE DEL ARCHIVO HF
	
	//VERIFICA SI EL ARCHIVO YA ESTA SIENDO VALIDADO ACTUALMENTE
        $bool_esta_siendo_validado=false;
        
        $query_verificacion_esta_siendo_procesado="";
        $query_verificacion_esta_siendo_procesado.=" SELECT * FROM gioss_0123_esta_validando_actualmente ";
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
	$sql_query_verificar.=" SELECT * FROM gioss_tabla_estado_informacion_r0123_hf ";
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
		$sql_query_delete.=" DELETE FROM gioss_tabla_registros_no_cargados_rechazados_r0123_hf ";
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
		$numero_secuencia_actual=$utilidades->obtenerSecuencia("gioss_numero_secuencia_r0123_hf");
					
		$sql_query_inserta_seq="";
		$sql_query_inserta_seq.=" INSERT INTO gioss_numero_de_secuencia_archivos_hf ";
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
		$query_insert_esta_siendo_procesado.=" INSERT INTO gioss_0123_esta_validando_actualmente ";
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
				echo "<script>alert('error al iniciar el estado actual de validacion en tiempo real  0123 ');</script>";
			}
		}
		//FIN INICIO LA EJECUCION
		
		$lineas_del_archivo=0;
		
		$registros_buenos=0;
		$registros_malos=0;

		$cont_porcentaje=0;
		
		// parte donde valida los campos del archivo HF 
		$hubo_inconsistencias_en_HF=false;	
		$diccionario_identificacion=array();
		$diccionario_identificacion_lineas=array();

		$numero_registros_afiliado_no_existe=0;
		$numero_registros_a_mostrar_max_afiliado_no_existe=0;
		$cadena_primeros_afiliados_inexistentes="";

		$numero_registros_afiliado_si_existen=0;
		$numero_registros_a_mostrar_max_afiliado_si_existen=0;
		$cadena_primeros_afiliados_existentes="";
		if($es_valido_nombre_archivo)
		{
			$mensaje_errores_HF="";
			$lineas_del_archivo = count(file($ruta_archivo_hf)); 
			$file_HF = fopen($ruta_archivo_hf, 'r') or exit("No se pudo abrir el archivo");
			
			//la variable $consecutivo_errores pasara como referencia y aumentara cada que se haye un error y su incremento es independiente de la variable $nlinea
			$consecutivo_errores=0;
			
			$nlinea=0;
			$fue_cerrada_la_gui=false;
			$se_creo_tabla_indice=false;
			while (!feof($file_HF)) 
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
				$verificar_si_ejecucion_fue_cancelada.=" SELECT esta_ejecutando FROM gioss_0123_esta_validando_actualmente ";
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
				
				$linea_tmp = fgets($file_HF);
				$linea= explode("\n", $linea_tmp)[0];
				//$linea=str_replace(",",".",$linea);
				
				$campos = explode("\t", $linea);
				
				//parte para evitar caracteres extraños en el ultimo campo antes del salto de linea
				$campos[count($campos)-1]=procesar_mensaje($campos[count($campos)-1]);
				
				//pasa a validar los campos
				if(count($campos)==95)
				{
					$cont_linea=$nlinea;//solo validacion esta parte
					//porcentaje
					$muestra_mensaje_nuevo=false;
					$porcentaje=intval((($cont_linea)*100)/($lineas_del_archivo-1));
					if($porcentaje!=$cont_porcentaje || ($porcentaje==0 && ($cont_linea)==1) || $porcentaje==100)
					{
					 $cont_porcentaje=$porcentaje;
					 $muestra_mensaje_nuevo=true;
					}
					//fin porcentaje

					if($muestra_mensaje_nuevo)
					{
				
						$mensaje_contador_errores="revisando linea ".($nlinea+1)." de $lineas_del_archivo del archivo HEMOFILIA. <br> registros buenos $registros_buenos , registros malos $registros_malos . <br>Porcentaje actual $porcentaje %  ";
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
						$query_update_esta_siendo_procesado.=" UPDATE gioss_0123_esta_validando_actualmente ";
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
								echo "<script>alert('error al actualizar el estado actual de validacion en tiempo real  0123 ');</script>";
							}
						}
						
						//FIN ACTUALIZA ESTADO EJECUCION
					}//fin muestra mensaje nuevo

					//PARTE CONSULTA VERIFICA EXISTENCIA AFILIADOS
					//gioss_afiliados_eapb_rc, id_afiliado, tipo_id_afiliado,$tipo_de_regimen_de_la_informacion_reportada
					$query_bd_existe_afiliado_en_tabla_regimen="";
					$resultados_query_existe_afiliado_tablas_regimen=array();
					$nombre_tabla_afiliado_hallado="";
					$campo_n5_ti=trim($campos[4]);//TIPO IDENTIFICACION si es el numero de orden 4 y campo 5 en hemofilia
					$campo_n6_ni=trim($campos[5]);//NUMERO IDENTIFICACION	si es el numero de orden 5 y campo 6 en hemofilia
					$tipo_de_regimen_de_la_informacion_reportada=trim($campos[9]);//REGIMEN	
					$cod_eapb_global=$cod_eapb;			
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

					if(
						$num_filas_resultado_existe_tablas_regimen==0 
						|| !is_array($resultados_query_existe_afiliado_tablas_regimen)
					)//fin parentesis condicion
					{
						$numero_registros_afiliado_no_existe++;
						if($numero_registros_a_mostrar_max_afiliado_no_existe<10)
						{
							//$cadena_primeros_afiliados_inexistentes.=$campo_n5_ti."-".$campo_n6_ni." REGIMEN $tipo_de_regimen_de_la_informacion_reportada ".procesar_mensaje2($query_bd_existe_afiliado_en_tabla_regimen)."<br>";
							$cadena_primeros_afiliados_inexistentes.=$campo_n5_ti."-".$campo_n6_ni." REGIMEN $tipo_de_regimen_de_la_informacion_reportada NO<br>";
							$numero_registros_a_mostrar_max_afiliado_no_existe++;
						}//fin if muestra 10 primeros
					}//contador numero afiliados inexistentes en bd
					else if($num_filas_resultado_existe_tablas_regimen>0 
						&& is_array($resultados_query_existe_afiliado_tablas_regimen)
						)
					{
						$numero_registros_afiliado_si_existen++;
						if($numero_registros_a_mostrar_max_afiliado_si_existen<10)
						{
							$cadena_primeros_afiliados_existentes.=$campo_n5_ti."-".$campo_n6_ni." REGIMEN $tipo_de_regimen_de_la_informacion_reportada SE<br>";
							$numero_registros_a_mostrar_max_afiliado_si_existen++;							
						}//fin if
					}//fin else if
					//FIN PARTE CONSULTA VERIFICA EXISTENCIA AFILIADOS

					//PARTE VALIDACION ERC AFILIADO ESTA EN BD para hf es igual el orden que erc en estos campos de ident.
					$campo_n1_primer_nombre=trim($campos[0]);
					$campo_n2_segundo_nombre=trim($campos[1]);
					$campo_n3_primer_apellido=trim($campos[2]);
					$campo_n4_segundo_apellido=trim($campos[3]);
					$campo_n5_ti=trim($campos[4]);
					$campo_n6_ni=trim($campos[5]);
					$campo_n7_fecha_nacimiento=trim($campos[6]);
					$campo_n8_sexo=trim($campos[7]);


					$tipo_de_regimen_de_la_informacion_reportada=trim($campos[9]);//campo 10, nota agregar regimen O

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
						$numero_campo_sexo=7;//campo 8 norma ERC
						$sexo_en_registro_archivo=strtoupper(trim($campos[$numero_campo_sexo]) );
						$sexo_anterior=$sexo_en_registro_archivo;
						$numero_campo_fecha_nacimiento=6;//campo  7 norma ERC
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

						//coloca none ya que puede venir desde la tabla de afiliados en blanco y ser calificado como incorrecto
						if($campos[0]==""){$campos[0]="NONE";}
						if($campos[1]==""){$campos[1]="NONE";}
						if($campos[2]==""){$campos[2]="NONE";}
						if($campos[3]==""){$campos[3]="NONE";}

						//PARTE VERIFICA SI EL AFILIADO CORRESPONDE A UNO DE LOS AFILIADOS DUPLICADOS ENTRE SI POR SOLO NUMERO ID
						//en caso de que si corresponda, reemplaza el tipo de identificacion por el almacenado en
						// la tabla unicos_de_tabla_duplicados_afiliados_mp
						$campo_n7_fecha_nacimiento=trim($campos[6]);
						$campo_n8_sexo=trim($campos[7]);
						$campo_n6_numero_identificacion=trim($campos[5]);

						$numero_campo_tipo_id=4;//campo 5 numero orden 4

						$numero_campo_primer_nombre=0;
						$numero_campo_segundo_nombre=1;
						$numero_campo_primer_apellido=2;
						$numero_campo_segundo_apellido=3;
						$error_bd_pertenece="";
						$query_pertenece_a_un_afiliado_duplicado="SELECT * FROM unicos_de_tabla_duplicados_afiliados_mp where numero_id='".$campo_n6_numero_identificacion."' and sexo='".$campo_n8_sexo."' and fecha_de_nacimiento='".$campo_n7_fecha_nacimiento."' ;";
						$resultados_pertenece_a_un_afiliado_duplicado=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($query_pertenece_a_un_afiliado_duplicado,$error_bd_pertenece);
						if(is_array($resultados_pertenece_a_un_afiliado_duplicado) && count($resultados_pertenece_a_un_afiliado_duplicado)>0 )
						{
							foreach ($resultados_pertenece_a_un_afiliado_duplicado as $key => $datosAfiliadoDuplicadoEnTablaPrincipal) 
							{
								if(
									$campo_n7_fecha_nacimiento==$datosAfiliadoDuplicadoEnTablaPrincipal['fecha_de_nacimiento']
									&& $campo_n8_sexo==$datosAfiliadoDuplicadoEnTablaPrincipal['sexo']
									&& ($campos[$numero_campo_primer_apellido]==$datosAfiliadoDuplicadoEnTablaPrincipal['primer_apellido']
										|| $campos[$numero_campo_primer_apellido]==$datosAfiliadoDuplicadoEnTablaPrincipal['primer_nombre']
										|| $campos[$numero_campo_primer_apellido]=="NONE"
										|| $campos[$numero_campo_primer_apellido]=="NOAP"
										)//fin primer apellido
									&& ($campos[$numero_campo_primer_nombre]==$datosAfiliadoDuplicadoEnTablaPrincipal['primer_apellido']
										|| $campos[$numero_campo_primer_nombre]==$datosAfiliadoDuplicadoEnTablaPrincipal['primer_nombre']
										|| $campos[$numero_campo_primer_nombre]=="NONE"
										|| $campos[$numero_campo_primer_nombre]=="NOAP"
										)//fin primer nombre
									)//fin condicion
								{
									$campos[$numero_campo_tipo_id]=$datosAfiliadoDuplicadoEnTablaPrincipal['tipo_id'];

									
									if(
										$campos[$numero_campo_segundo_apellido]=="NONE" // solo se mira igual a none ya que el vacio lo mira antes
										&& $datosAfiliadoDuplicadoEnTablaPrincipal['segundo_apellido']!=""
										&& $datosAfiliadoDuplicadoEnTablaPrincipal['segundo_apellido']!="NONE"
									)//fin condicion
									{
										$campos[$numero_campo_segundo_apellido]=$datosAfiliadoDuplicadoEnTablaPrincipal['segundo_apellido'];
									}//fin if

									
									if(
										$campos[$numero_campo_segundo_nombre]=="NONE" // solo se mira igual a none ya que el vacio lo mira antes
										&& $datosAfiliadoDuplicadoEnTablaPrincipal['segundo_nombre']!=""
										&& $datosAfiliadoDuplicadoEnTablaPrincipal['segundo_nombre']!="NONE"
									)//fin condicion
									{
										$campos[$numero_campo_segundo_nombre]=$datosAfiliadoDuplicadoEnTablaPrincipal['segundo_nombre'];
									}//fin if

									
									if(
										$campos[$numero_campo_primer_apellido]=="NONE" // solo se mira igual a none ya que el vacio lo mira antes
										&& $datosAfiliadoDuplicadoEnTablaPrincipal['primer_apellido']!=""
										&& $datosAfiliadoDuplicadoEnTablaPrincipal['primer_apellido']!="NONE"
									)//fin condicion
									{
										$campos[$numero_campo_primer_apellido]=$datosAfiliadoDuplicadoEnTablaPrincipal['primer_apellido'];
									}//fin if

									
									if(
										$campos[$numero_campo_primer_nombre]=="NONE" // solo se mira igual a none ya que el vacio lo mira antes
										&& $datosAfiliadoDuplicadoEnTablaPrincipal['primer_nombre']!=""
										&& $datosAfiliadoDuplicadoEnTablaPrincipal['primer_nombre']!="NONE"
									)//fin condicion
									{
										$campos[$numero_campo_primer_nombre]=$datosAfiliadoDuplicadoEnTablaPrincipal['primer_nombre'];
									}//fin if
								}//fin if
							}//fin foreach
						}//fin if encontro que pertenece a un afiliado duplicado en la tabl principal
						//FIN PARTE VERIFICA SI EL AFILIADO CORRESPONDE A UNO DE LOS AFILIADOS DUPLICADOS ENTRE SI POR SOLO NUMERO ID 



					}//fin if hay concidencia en bd

					$primer_nombre="".$campos[0];
					$segundo_nombre="".$campos[1];
					$primer_apellido="".$campos[2];
					$segundo_apellido="".$campos[3];

					$datos_complementarios="Tipo ID: $campo_n5_ti  Numero ID: $campo_n6_ni  Primer Nombre: $primer_nombre Segundo Nombre: $segundo_nombre Primer Apellido: $primer_apellido Segundo Apellido: $segundo_apellido ";

					if($se_modifico_sexo_o_fecha_de_acuerdo_a_tabla_regimen==true)
					{
						fwrite($file_archivo_log_registros_modificados, "\n"."Sexo anterior: $sexo_anterior Sexo posterior: $sexo_posterior Fecha Nacimiento Anterior: $fecha_anterior Fecha Nacimiento Posterior: $fecha_posterior ".$datos_complementarios );	

					}//fin fi hubo modificacion

					if($se_modifico_sexo==true)
					{
						$linea_se_modifico_sexo_afiliado="=\"$campo_n5_ti\";=\"$campo_n6_ni\";=\"Al afiliado $campo_n5_ti $campo_n6_ni se le cambio el sexo ( $sexo_anterior ) por ( $sexo_posterior )\"";
						fwrite($file_archivo_afiliado_existe_cambio_sexo, "\n".$linea_se_modifico_sexo_afiliado);
					}//fin if

					if($se_modifico_fecha_nacimiento==true)
					{
						$linea_se_modifico_fecha_nacimiento_afiliado="=\"$campo_n5_ti\";=\"$campo_n6_ni\";=\"Al afiliado $campo_n5_ti $campo_n6_ni se le cambio la fecha de nacimiento ( $fecha_anterior) por ( $fecha_posterior )\"";
						fwrite($file_archivo_afiliado_existe_cambio_fecha_nacimiento, "\n".$linea_se_modifico_fecha_nacimiento_afiliado);
					}//fin if

					//PARTE LLENA ARCHIVO NUEVO ORIGINAL CON POSIBLES REGISTROS AFILIADOS MODIFICADOS SEXO FECHA NACIMIENTO
					//solo los que esten en tabla de afiliados
					$array_resultados_validacion=array("error"=>false,"mensaje"=>"");//inicializa aqui
				    if($si_existe==true )
				    {
					    		    
					    fwrite($file_archivo_registros_que_tienen_afiliados, "\n".campos_a_registro_sin_consecutivo($campos) );	
				    }//fin if
			    	else if	($si_existe==false )
			    	{
			    		fwrite($file_archivo_registros_que_no_tienen_afiliados, "\n".campos_a_registro_sin_consecutivo($campos) );

			    		//se indica la inconsistencia						
			    		$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0105600"])[1];
			    		$errores_campo_identificacion="";
						$errores_campo_identificacion.="1,".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",0105600,$cadena_descripcion_inconsistencia ...VR:".$campos[5]." ,".($nlinea+1).",6";//$campos[5] es el campo 6
			    		//fin se indica la inconsistencia	

						//se pone false debido a que en las versiones fvll y general es informativo
			    		$array_resultados_validacion=array("error"=>true,"mensaje"=>$errores_campo_identificacion);


			    	}//fin else if
				    //FIN PARTE LLENA ARCHIVO NUEVO ORIGINAL CON POSIBLES REGISTROS AFILIADOS MODIFICADOS SEXO FECHA NACIMIENTO
					//FIN PARTE VALIDACION ERC AFILIADO ESTA EN BD

					$linea_mod_depende_afiliados=implode("\t", $campos);
					
					if($si_existe==true)
					{
						//validar_HF($campos,$nlinea,&$consecutivo_errores,$array_tipo_validacion,$array_grupo_validacion,$array_detalle_validacion,$nombre_archivo,$fecha_remision,$fecha_de_corte,$cod_prestador,$cod_eapb)
						$array_resultados_validacion=validar_HF($campos,
											 $nlinea,
											 $consecutivo_errores,
											 $array_tipo_validacion,
											 $array_grupo_validacion,
											 $array_detalle_validacion,
											 $nombre_archivo_registrado,
											 $fecha_de_corte,
											 $fecha_inferior_pv,
											 $cod_prestador,
											 $cod_eapb,
											 $diccionario_identificacion,
											 $diccionario_identificacion_lineas,
											 $coneccionBD,
											 $array_numero_campo_bd
											);
					}//fin if si existe afiliado
										
					if($hubo_inconsistencias_en_HF==false)
					{
						$hubo_inconsistencias_en_HF=$array_resultados_validacion["error"];
					}
					
					$estado_validacion_registro=0;
					if($array_resultados_validacion["error"]==false)
					{
						$estado_validacion_registro=1;
						$registros_buenos++;

						fwrite($file_archivo_registros_buenos,"\n".trim($linea_mod_depende_afiliados));

						//PARTE DATOS PARA TABLA ANALISIS USADA PARA CONSULTAS SOBRE LOS DATOS VALIDADOS CORRECTOS
						$fecha_inicial_para_analisis=$fecha_de_corte;
						$regional_para_analisis="";
						if($se_creo_tabla_indice==false)
						{
							$insercion_tabla_indice_exitosa=true;

				    		$sql_insertar_en_tabla_indice_analisis_coherencia="";				    
							$sql_insertar_en_tabla_indice_analisis_coherencia.="insert into gioss_indice_archivo_para_analisis_0123";
							$sql_insertar_en_tabla_indice_analisis_coherencia.="(";
							$sql_insertar_en_tabla_indice_analisis_coherencia.="cod_prestador_servicios_salud,";
							$sql_insertar_en_tabla_indice_analisis_coherencia.="codigo_eapb,";
							$sql_insertar_en_tabla_indice_analisis_coherencia.="fecha_inicio_periodo,";					
							$sql_insertar_en_tabla_indice_analisis_coherencia.="fecha_de_corte,";					
							$sql_insertar_en_tabla_indice_analisis_coherencia.="fecha_y_hora_validacion,";					
							$sql_insertar_en_tabla_indice_analisis_coherencia.="nombre_archivo,";
							$sql_insertar_en_tabla_indice_analisis_coherencia.="numero_de_secuencia";								
							$sql_insertar_en_tabla_indice_analisis_coherencia.=")";
							$sql_insertar_en_tabla_indice_analisis_coherencia.="values";
							$sql_insertar_en_tabla_indice_analisis_coherencia.="(";
							$sql_insertar_en_tabla_indice_analisis_coherencia.="'".$cod_prestador."',";
							$sql_insertar_en_tabla_indice_analisis_coherencia.="'".$cod_eapb."',";
							$sql_insertar_en_tabla_indice_analisis_coherencia.="'".$fecha_inicial_para_analisis."',";					
							$sql_insertar_en_tabla_indice_analisis_coherencia.="'".$fecha_de_corte."',";					
							$sql_insertar_en_tabla_indice_analisis_coherencia.="'".$fecha_actual." ".$tiempo_actual."',";							
							$sql_insertar_en_tabla_indice_analisis_coherencia.="'".$nombre_archivo_registrado."',";							
							$sql_insertar_en_tabla_indice_analisis_coherencia.="'".$numero_secuencia_actual."'";								
							$sql_insertar_en_tabla_indice_analisis_coherencia.=");";
							$error_bd_seq="";
							$bandera = $coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_insertar_en_tabla_indice_analisis_coherencia, $error_bd_seq);
							if($error_bd_seq!="")
							{
							    $error_de_base_de_datos.=" ERROR AL INSERTAR INDICE PARA EL ARCHIVO DE ANALISIS: ".$error_bd_seq."<br>";
							    echo $error_de_base_de_datos;
							    $insercion_tabla_indice_exitosa=false;
							}
							 //FIN QUERY INSERT gioss_indice_archivo_para_analisis_4505

				    		if($insercion_tabla_indice_exitosa==true)
				    		{
				    			echo "<span style='color:white;'>creo indice</span><br>";
				    			$se_creo_tabla_indice=true;
				    		}
						}//fin if no se ha creado registro en la tabla indice
						
						if($se_creo_tabla_indice==true)
						{
							//PREPARA EDADES Y CAMPOS ADICIONALES

							$numero_campo_fecha_nacimiento=6;//campo  7 norma ERC
							$fecha_nacimiento_en_registro_archivo=trim($campos[$numero_campo_fecha_nacimiento]);

				    		//CALCULO EDAD
							$fecha_nacimiento= explode("-",$fecha_nacimiento_en_registro_archivo);
							$bool_fecha_nacimiento_valida=true;
							if(count($fecha_nacimiento)!=3
							   || !(ctype_digit($fecha_nacimiento[0]) && ctype_digit($fecha_nacimiento[1]) && ctype_digit($fecha_nacimiento[2]) )
							   || !checkdate($fecha_nacimiento[1],$fecha_nacimiento[2],$fecha_nacimiento[0]))
							{			
								$bool_fecha_nacimiento_valida=false;
							}
							
							
							$edad= -1;
							$edad_dias =-1;
							$edad_meses =-1;
							$edad_semanas = -1;
							$verificador_edad= -1;

							$edad_quinquenio=0;
							$edad_etarea=0;
							
							if($bool_fecha_nacimiento_valida==true)
							{
							    
							    $string_fecha_nacimiento=date($fecha_nacimiento[0]."-".$fecha_nacimiento[1]."-".$fecha_nacimiento[2]);
							    
							    $fecha_nacimiento_format=new DateTime($string_fecha_nacimiento);
							    $fecha_corte_format=new DateTime($fecha_de_corte);
							
							    $interval = date_diff($fecha_nacimiento_format,$fecha_corte_format);
							    $edad_dias =(float)($interval->days);
							    
							    //$edad= (float)($interval->days / 365);		    
							    //$edad_meses = (float)($interval->days / 30.4368499);
							    //$edad_meses_2 = (float)($interval->format('%m')+ 12 * $interval->format('%y'));
							    
							    $array_fecha_nacimiento=explode("-",$string_fecha_nacimiento);
							    $array_fecha_corte=explode("-",$fecha_de_corte);
							    $array_edad=edad_years_months_days($array_fecha_nacimiento[2]."-".$array_fecha_nacimiento[1]."-".$array_fecha_nacimiento[0],$array_fecha_corte[2]."-".$array_fecha_corte[1]."-".$array_fecha_corte[0]);
							    $edad_meses=(intval($array_edad['y'])*12)+$array_edad['m'];
							    $edad=intval($array_edad['y']);
							    
							    $edad_semanas = (float)($interval->days / 7);
							    $verificador_edad= (float)$interval->format("%r%a");

							    //edad quinquenio
							    if($edad==0)
							    {
							    	$edad_quinquenio=1;							    	
							    }//fin if
							    else if($edad>=1 && $edad<=4)
							    {
							    	$edad_quinquenio=2;
							    }
							    else if($edad>=5 && $edad<=9)
							    {
							    	$edad_quinquenio=3;
							    }
							    else if($edad>=10 && $edad<=14)
							    {
							    	$edad_quinquenio=4;
							    }
							    else if($edad>=15 && $edad<=19)
							    {
							    	$edad_quinquenio=5;
							    }
							    else if($edad>=20 && $edad<=24)
							    {
							    	$edad_quinquenio=6;
							    }
							    else if($edad>=25 && $edad<=29)
							    {
							    	$edad_quinquenio=7;
							    }
							    else if($edad>=30 && $edad<=34)
							    {
							    	$edad_quinquenio=8;
							    }
							    else if($edad>=35 && $edad<=39)
							    {
							    	$edad_quinquenio=9;
							    }
							    else if($edad>=40 && $edad<=44)
							    {
							    	$edad_quinquenio=10;
							    }
							    else if($edad>=45 && $edad<=49)
							    {
							    	$edad_quinquenio=11;
							    }
							    else if($edad>=50 && $edad<=54)
							    {
							    	$edad_quinquenio=12;
							    }
							    else if($edad>=55 && $edad<=59)
							    {
							    	$edad_quinquenio=13;
							    }
							    else if($edad>=60 && $edad<=64)
							    {
							    	$edad_quinquenio=14;
							    }
							    else if($edad>=65 && $edad<=69)
							    {
							    	$edad_quinquenio=15;
							    }
							    else if($edad>=70 && $edad<=74)
							    {
							    	$edad_quinquenio=16;
							    }
							    else if($edad>=75 && $edad<=79)
							    {
							    	$edad_quinquenio=17;
							    }
							    else if($edad>=80 && $edad<=84)
							    {
							    	$edad_quinquenio=18;
							    }
							    else if($edad>=85 && $edad<=89)
							    {
							    	$edad_quinquenio=19;
							    }
							    else if($edad>=90 )
							    {
							    	$edad_quinquenio=20;
							    }
							    //fin edad quinquenio

							    //edad etarea
							    if($edad==0)
							    {
							    	$edad_etarea=1;							    	
							    }//fin if
							    else if($edad>=1 && $edad<=4)
							    {
							    	$edad_etarea=2;
							    }
							    else if($edad>=5 && $edad<=14)
							    {
							    	$edad_etarea=3;
							    }
							    else if($edad>=15 && $edad<=44)
							    {
							    	$edad_etarea=4;
							    }
							    else if($edad>=45 && $edad<=59)
							    {
							    	$edad_etarea=5;
							    }
							    else if($edad>=60 )
							    {
							    	$edad_etarea=6;
							    }
							    //fin edad etarea
							    
							    
							}
							//FIN CALCULO EDAD

				    		//FIN PREPARA EDADES Y CAMPOS ADICIONALES

				    		//tabla analisis insert
					    	$sql_insertar_en_tabla_analisis_coherencia="";				    
							$sql_insertar_en_tabla_analisis_coherencia.="insert into gioss_archivo_para_analisis_0123";
							$sql_insertar_en_tabla_analisis_coherencia.="(";
							$sql_insertar_en_tabla_analisis_coherencia.="cod_prestador_servicios_salud,";
							$sql_insertar_en_tabla_analisis_coherencia.="codigo_eapb,";
							$sql_insertar_en_tabla_analisis_coherencia.="fecha_inicio_periodo,";					
							$sql_insertar_en_tabla_analisis_coherencia.="fecha_de_corte,";					
							$sql_insertar_en_tabla_analisis_coherencia.="fecha_y_hora_validacion,";					
							$sql_insertar_en_tabla_analisis_coherencia.="nombre_archivo,";
							$cont_campo_ins=1;
							while($cont_campo_ins<=95)
							{
								
								$sql_insertar_en_tabla_analisis_coherencia.="campo_hf_de_numero_orden_".$cont_campo_ins.",";
								
								$cont_campo_ins++;
							}
							if($bool_fecha_nacimiento_valida==true)
							{
								$sql_insertar_en_tabla_analisis_coherencia.="edad_years,";
								$sql_insertar_en_tabla_analisis_coherencia.="edad_meses,";
								$sql_insertar_en_tabla_analisis_coherencia.="edad_dias,";

								$sql_insertar_en_tabla_analisis_coherencia.="grupo_edad_quinquenal,";								
								$sql_insertar_en_tabla_analisis_coherencia.="grupo_etareo,";
							}//fin if fecha nacimeinto valida
							if($regional_para_analisis!="")
							{
								$sql_insertar_en_tabla_analisis_coherencia.="regional,";
							}//fin if hay regional
							$sql_insertar_en_tabla_analisis_coherencia.="numero_fila, ";							
							$sql_insertar_en_tabla_analisis_coherencia.="numero_de_secuencia";
							$sql_insertar_en_tabla_analisis_coherencia.=")";
							$sql_insertar_en_tabla_analisis_coherencia.="values";
							$sql_insertar_en_tabla_analisis_coherencia.="(";
							$sql_insertar_en_tabla_analisis_coherencia.="'".$cod_prestador."',";
							$sql_insertar_en_tabla_analisis_coherencia.="'".$cod_eapb."',";
							$sql_insertar_en_tabla_analisis_coherencia.="'".$fecha_inicial_para_analisis."',";					
							$sql_insertar_en_tabla_analisis_coherencia.="'".$fecha_de_corte."',";					
							$sql_insertar_en_tabla_analisis_coherencia.="'".$fecha_actual." ".$tiempo_actual."',";							
							$sql_insertar_en_tabla_analisis_coherencia.="'".$nombre_archivo_registrado."',";							
							$cont_campo_ins=1;
							while($cont_campo_ins<=95)
							{
								$sql_insertar_en_tabla_analisis_coherencia.="'".procesar_mensaje($campos[$cont_campo_ins-1])."',";
								$cont_campo_ins++;
							}
							if($bool_fecha_nacimiento_valida==true)
							{
								$sql_insertar_en_tabla_analisis_coherencia.="'".$edad."', ";
								$sql_insertar_en_tabla_analisis_coherencia.="'".$edad_meses."', ";
								$sql_insertar_en_tabla_analisis_coherencia.="'".$edad_dias."', ";

								$sql_insertar_en_tabla_analisis_coherencia.="'".$edad_quinquenio."', ";
								$sql_insertar_en_tabla_analisis_coherencia.="'".$edad_etarea."', ";
							}//fin if fecha nacimeinto valida
							if($regional_para_analisis!="")
							{
								$sql_insertar_en_tabla_analisis_coherencia.="'".$regional_para_analisis."', ";
							}//fin if hay regional			
							$sql_insertar_en_tabla_analisis_coherencia.="'".$nlinea."', ";											
							$sql_insertar_en_tabla_analisis_coherencia.="'".$numero_secuencia_actual."'";	
							$sql_insertar_en_tabla_analisis_coherencia.=");";
							$error_bd_seq="";
							$bandera = $coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_insertar_en_tabla_analisis_coherencia, $error_bd_seq);
							if($error_bd_seq!="")
							{
							    $error_de_base_de_datos.=" ERROR CARGANDO ARCHIVO PARA ANALISIS: ".$error_bd_seq."<br>";
							    echo $sql_insertar_en_tabla_analisis_coherencia."<br>";
							    echo $error_de_base_de_datos;
							}//fin if
						}//fin if se inserto el indice
						//FIN PARTE DATOS PARA TABLA ANALISIS USADA PARA CONSULTAS SOBRE LOS DATOS VALIDADOS CORRECTOS
					}//fin else escribe registros buenos
					else
					{
						$estado_validacion_registro=2;
						$registros_malos++;

						fwrite($file_archivo_registros_malos,"\n".trim($linea_mod_depende_afiliados));
					}//fin else escribe registros malos
					
					$query_insertar_campos_a_bd="";
					$query_insertar_campos_a_bd.="INSERT INTO gioss_tabla_registros_no_cargados_rechazados_r0123_hf ";
					$query_insertar_campos_a_bd.="(";				
					$cont_campo_ins=0;
					while($cont_campo_ins<95)
					{
						$query_insertar_campos_a_bd.="campo_hf_de_numero_orden_".$cont_campo_ins.",";
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
					while($cont_campo_ins<95)
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

					//PARTE LLENA ARRAY BOOLEANOS PARA QUE SOLO TENGA EN CUENTA LA PRIMERA INCONSISTENCIA DEL CAMPO
				    $array_booleano_primer_error_por_linea=array();
				    $cont_llenado=0;
				    while($cont_llenado<95)
				    {
				    	$numero_campo_actual=trim($array_numero_campo_bd[$cont_llenado]);
				    	$array_booleano_primer_error_por_linea[$numero_campo_actual]=true;
				    	$cont_llenado++;
				    }//fin while
				    $array_booleano_primer_error_por_linea[999]=true;
				    //FIN PARTE LLENA ARRAY BOOLEANOS PARA QUE SOLO TENGA EN CUENTA LA PRIMERA INCONSISTENCIA DEL CAMPO

				    $cantidad_errores_por_linea=0;
					
					//escribe los errores
					$mensaje_errores_HF=$array_resultados_validacion["mensaje"];
					$array_mensajes_errores_campos=explode("|",$mensaje_errores_HF);
					
					foreach($array_mensajes_errores_campos as $msg_error)
					{
						$columnas_inconsistencias_para_bd=array();
						$columnas_inconsistencias_para_bd=explode(",",$msg_error);

						//PARTE INCREMENTA CONTADOR ERRORES POR CADA CAMPO PERO SOLO 1 POR LINEA
					    $cod_tipo_inconsistencia_temp=trim($columnas_inconsistencias_para_bd[2]);
					    $numero_campo_temp=trim($columnas_inconsistencias_para_bd[9]);

					    $cod_grupo_inconsistencia_temp=trim($columnas_inconsistencias_para_bd[4]);
					    //echo $cod_grupo_inconsistencia_temp." nc: ".$numero_campo_temp."<br>";

					    if($array_booleano_primer_error_por_linea[trim($numero_campo_temp)]==true
						&& $cod_tipo_inconsistencia_temp=="01"
						)
				    	{
				    		//echo "ENTRO ".$cod_tipo_inconsistencia_temp." nc: ".$numero_campo_temp."<br>";
				    		$array_contador_total_errores_obligatorios_campo[trim($numero_campo_temp)]++;
				    		$array_booleano_primer_error_por_linea[trim($numero_campo_temp)]=false;
				    	}//fin if

				    	if($cod_grupo_inconsistencia_temp=="0105")
				    	{
				    		//echo "ENTRO ".$cod_grupo_inconsistencia_temp." nc: ".$numero_campo_temp."<br>";
				    		$array_contador_total_inconsistencias_campo_0105[trim($numero_campo_temp)]++;
				    	}//fin if

				    	if($cod_grupo_inconsistencia_temp=="0104")
				    	{
				    		//echo "ENTRO ".$cod_grupo_inconsistencia_temp." nc: ".$numero_campo_temp."<br>";
				    		$array_contador_total_inconsistencias_campo_0104[trim($numero_campo_temp)]++;
				    	}//fin if

				    	if($cod_grupo_inconsistencia_temp=="0103")
				    	{
				    		//echo "ENTRO ".$cod_grupo_inconsistencia_temp." nc: ".$numero_campo_temp."<br>";
				    		$array_contador_total_inconsistencias_campo_0103[trim($numero_campo_temp)]++;
				    	}//fin if

				    	if($cod_grupo_inconsistencia_temp=="0102")
				    	{
				    		//echo "ENTRO ".$cod_grupo_inconsistencia_temp." nc: ".$numero_campo_temp."<br>";
				    		$array_contador_total_inconsistencias_campo_0102[trim($numero_campo_temp)]++;
				    	}//fin if

				    	if($cod_grupo_inconsistencia_temp=="0101")
				    	{
				    		//echo "ENTRO ".$cod_grupo_inconsistencia_temp." nc: ".$numero_campo_temp."<br>";
				    		$array_contador_total_inconsistencias_campo_0101[trim($numero_campo_temp)]++;
				    	}//fin if
				    	//FIN PARTE INCREMENTA CONTADOR ERRORES POR CADA CAMPO PERO SOLO 1 POR LINEA

						if($msg_error!="")
						{
							$columnas_inconsistencias_para_bd[]=$campo_n5_ti;
							$columnas_inconsistencias_para_bd[]=$campo_n6_ni;

							//PARTE REESCRIBE PARA CSV SIMPLE DE ABRIR
						    $error_procesado_csv_excel="";
						    $error_procesado_csv_excel.="=\"".implode("\";=\"", $columnas_inconsistencias_para_bd)."\"";
						    //FIN PARTE REESCRIBE PARA CSV SIMPLE DE ABRIR						    
							fwrite($file_inconsistencias_r4725_HF, $error_procesado_csv_excel."\n");

							//$file_archivo_incons_registros_malos
							if($array_resultados_validacion["error"]==true)
							{								
								fwrite($file_archivo_incons_registros_malos, $error_procesado_csv_excel."\n");
							}//fin if
						}//fin if
						
						//SUBIDA DE INCONSISTENCIAS A LA BASE DE DATOS
						if(count($columnas_inconsistencias_para_bd)==10)
						{
							
							
							//se insertan los datos de detalles de inconsistencia
							
							$sql_insertar_inconsistencia_hf="";
							$sql_insertar_inconsistencia_hf.=" INSERT INTO gioss_reporte_inconsistencias_r0123_hf ";
							$sql_insertar_inconsistencia_hf.=" ( ";
							$sql_insertar_inconsistencia_hf.=" numero_orden, ";
							$sql_insertar_inconsistencia_hf.=" nombre_archivo, ";
							$sql_insertar_inconsistencia_hf.=" cod_tipo_inconsitencia, ";
							$sql_insertar_inconsistencia_hf.=" nombre_tipo_inconsistencia, ";
							$sql_insertar_inconsistencia_hf.=" cod_grupo_inconsistencia, ";
							$sql_insertar_inconsistencia_hf.=" nombre_grupo_inconsistencia, ";
							$sql_insertar_inconsistencia_hf.=" cod_detalle_inconsistencia, ";
							$sql_insertar_inconsistencia_hf.=" detalle_inconsistencia, ";
							$sql_insertar_inconsistencia_hf.=" numero_linea, ";
							$sql_insertar_inconsistencia_hf.=" numero_campo, ";
							$sql_insertar_inconsistencia_hf.=" fecha_validacion, ";
							$sql_insertar_inconsistencia_hf.=" hora_validacion ";
							$sql_insertar_inconsistencia_hf.=" ) ";
							$sql_insertar_inconsistencia_hf.=" VALUES ";
							$sql_insertar_inconsistencia_hf.=" ( ";
							$sql_insertar_inconsistencia_hf.=" '".$numero_secuencia_actual."', ";
							$sql_insertar_inconsistencia_hf.=" '".$nombre_archivo_registrado."', ";
							$sql_insertar_inconsistencia_hf.=" '".preg_replace("/[^A-Za-z0-9:.\-\s+]/", "", trim($columnas_inconsistencias_para_bd[2]) )."', ";
							$sql_insertar_inconsistencia_hf.=" '".preg_replace("/[^A-Za-z0-9:.\-\s+]/", "", trim($columnas_inconsistencias_para_bd[3]) )."', ";
							$sql_insertar_inconsistencia_hf.=" '".preg_replace("/[^A-Za-z0-9:.\-\s+]/", "", trim($columnas_inconsistencias_para_bd[4]) )."', ";
							$sql_insertar_inconsistencia_hf.=" '".preg_replace("/[^A-Za-z0-9:.\-\s+]/", "", trim($columnas_inconsistencias_para_bd[5]) )."', ";
							$sql_insertar_inconsistencia_hf.=" '".preg_replace("/[^A-Za-z0-9:.\-\s+]/", "", trim($columnas_inconsistencias_para_bd[6]) )."', ";
							$sql_insertar_inconsistencia_hf.=" '".preg_replace("/[^A-Za-z0-9:.\-\s+]/", "", trim($columnas_inconsistencias_para_bd[7]) )."', ";
							$sql_insertar_inconsistencia_hf.=" '".preg_replace("/[^A-Za-z0-9:.\-\s+]/", "", trim($columnas_inconsistencias_para_bd[8]) )."', ";
							$sql_insertar_inconsistencia_hf.=" '".preg_replace("/[^A-Za-z0-9:.\-\s+]/", "", trim($columnas_inconsistencias_para_bd[9]) )."', ";
							$sql_insertar_inconsistencia_hf.=" '".$fecha_actual."', ";
							$sql_insertar_inconsistencia_hf.=" '".$tiempo_actual."' ";
							$sql_insertar_inconsistencia_hf.=" ); ";
							$error_bd_ins="";
							$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_insertar_inconsistencia_hf, $error_bd_ins);
							if($error_bd_ins!="")
							{
								$error_mostrar_bd.="ERROR AL SUBIR LAS INCONSISTENCIAS ".$error_bd_ins."<br>";
							}
							//fin se insertan los datos de detalles de inconsistencia
							
							
						}					
						//FIN SUBIDA INCONSISTENCIAS A LA BASE DE DATOS
						if($cod_tipo_inconsistencia_temp=="01")
						{
							$cantidad_errores_por_linea++;
						}//fin if solo si la inconsistencia es obligatoria
					}//fin foreach
					//fin escribe los errores

					//PARTE CARGA EN TABLA INDEXADORA IPS POR ARCHIVO
					if(count($campos)>=95)
				    {
					    try
					    {
					    	$codigo_prestador_para_insercion="";
					    	$codigo_eapb_para_insercion="";

					    	$codigo_prestador_para_insercion=$cod_prestador;
					    	$codigo_eapb_para_insercion=$cod_eapb;
					    	$fecha_inicial_para_analisis=$fecha_de_corte;
						

					    	//$prestador_archivo=procesar_mensaje($campos[2]);
					    	$prestador_archivo=$codigo_prestador_para_insercion;//debido a que en cancer no se sabe aun que campo es 
					    	$nit_prestador_archivo="";
					    	$cod_municipio="";
					    	$cod_depto="";
					    	$nombre_prestador="";

					    	$codigo_habilitacion_con_ceros_consulta=$prestador_archivo;

					    	if(strlen($codigo_habilitacion_con_ceros_consulta)==11 &&
						       (substr($codigo_habilitacion_con_ceros_consulta,0,1)=="5" || substr($codigo_habilitacion_con_ceros_consulta,0,1)=="8")
						       )
							{
							    $codigo_habilitacion_con_ceros_consulta="0".$codigo_habilitacion_con_ceros_consulta;
					}
							else if(strlen($codigo_habilitacion_con_ceros_consulta)==10)
							{
							    $codigo_habilitacion_con_ceros_consulta=$codigo_habilitacion_con_ceros_consulta."01";
					}
							else if(strlen($codigo_habilitacion_con_ceros_consulta)==9 
								&& (substr($codigo_habilitacion_con_ceros_consulta,0,1)=="5" || substr($codigo_habilitacion_con_ceros_consulta,0,1)=="8")
							)
							{
							    $codigo_habilitacion_con_ceros_consulta="0".$codigo_habilitacion_con_ceros_consulta."01";
							}//fin else 

					    	while(strlen($codigo_habilitacion_con_ceros_consulta)<12 
								&& $codigo_habilitacion_con_ceros_consulta!="999"
							)
							{
								//incrementa cuando es menor de 12
								$codigo_habilitacion_con_ceros_consulta="0".$codigo_habilitacion_con_ceros_consulta;
							}//fin while

					    	$sql_query_nit_prestador="SELECT num_tipo_identificacion, cod_municipio, cod_depto, nom_entidad_prestadora FROM gios_prestador_servicios_salud WHERE (cod_registro_especial_pss='$prestador_archivo' OR cod_registro_especial_pss='$codigo_habilitacion_con_ceros_consulta' ) ; ";
							$resultado_query_nit_prestador=$coneccionBD->consultar2_no_crea_cierra($sql_query_nit_prestador);
							if(count($resultado_query_nit_prestador)>0 
								&& is_array($resultado_query_nit_prestador) 
								)//fin condicion if
							{
								$nit_prestador_archivo=trim($resultado_query_nit_prestador[0]["num_tipo_identificacion"]);
								$cod_municipio=trim($resultado_query_nit_prestador[0]["cod_municipio"]);
								$cod_depto=trim($resultado_query_nit_prestador[0]["cod_depto"]);
								$nombre_prestador=trim($resultado_query_nit_prestador[0]["nom_entidad_prestadora"]);
							}//fin if

							if($codigo_habilitacion_con_ceros_consulta!="" && $codigo_habilitacion_con_ceros_consulta!=$prestador_archivo)
							{
								$nombre_prestador="(Codigo Habilitacion Probable: $codigo_habilitacion_con_ceros_consulta | Codigo Habilitacion Escrito Archivo: $prestador_archivo ) ".$nombre_prestador;
							}//fin if

							

							//se necesita hacer el select de todos modos
							$cantidad_lineas_en_archivo_para_prestador=0;
							$cantidad_lineas_correctas_en_archivo_para_prestador=0;

							$cantidad_inconsistencias_para_ips=0;
							$cantidad_inconsistencias_para_ips=intval($cantidad_errores_por_linea);

							$sql_select_datos_prestador_en_reporte="";
							$sql_select_datos_prestador_en_reporte.="SELECT cantidad_lineas_en_archivo, cantidad_lineas_correctas_en_archivo, cantidad_inconsistencias_para_ips
							FROM gioss_index_reporte_ips_0123 
							WHERE 
							entidad_reportante ='".$codigo_prestador_para_insercion."'
							AND
							entidad_a_reportar ='".$codigo_eapb_para_insercion."'
							AND
							fecha_inicio_periodo ='".$fecha_inicial_para_analisis."'
							AND
							fecha_de_corte ='".$fecha_de_corte."'
							AND
							fecha_y_hora_validacion ='".$fecha_actual." ".$tiempo_actual."'
							AND 
							nombre_archivo ='".$nombre_archivo_registrado."'
							AND
							numero_de_secuencia ='".$numero_secuencia_actual."'
							AND
							prestador_en_archivo ='".$prestador_archivo."'
							";
							$resultado_query_prestador_en_reporte=$coneccionBD->consultar2_no_crea_cierra($sql_select_datos_prestador_en_reporte);
							if(count($resultado_query_prestador_en_reporte)>0 
								&& is_array($resultado_query_prestador_en_reporte) 
								)//fin condicion if
							{
								$cantidad_lineas_en_archivo_para_prestador=intval($resultado_query_prestador_en_reporte[0]['cantidad_lineas_en_archivo']);
								$cantidad_lineas_correctas_en_archivo_para_prestador=intval($resultado_query_prestador_en_reporte[0]['cantidad_lineas_correctas_en_archivo']);
								$cantidad_inconsistencias_para_ips=$cantidad_inconsistencias_para_ips+intval($resultado_query_prestador_en_reporte[0]['cantidad_inconsistencias_para_ips']);
							}//fin if

							//incrementa los valores consultados
							$cantidad_lineas_en_archivo_para_prestador++;
							if($estado_validacion_registro==1)
							{
								$cantidad_lineas_correctas_en_archivo_para_prestador++;
							}//fin if



							//UPSERT

							$filas_afectadas=0;

							$sql_update_en_reporte_ips="";
							$sql_update_en_reporte_ips.="UPDATE gioss_index_reporte_ips_0123 SET ";
							$sql_update_en_reporte_ips.=" cantidad_lineas_en_archivo='$cantidad_lineas_en_archivo_para_prestador' , ";
							$sql_update_en_reporte_ips.=" cantidad_lineas_correctas_en_archivo='$cantidad_lineas_correctas_en_archivo_para_prestador',  ";
							$sql_update_en_reporte_ips.=" cantidad_inconsistencias_para_ips='$cantidad_inconsistencias_para_ips'  ";
							$sql_update_en_reporte_ips.="
								WHERE 
								entidad_reportante ='".$codigo_prestador_para_insercion."'
								AND
								entidad_a_reportar ='".$codigo_eapb_para_insercion."'
								AND
								fecha_inicio_periodo ='".$fecha_inicial_para_analisis."'
								AND
								fecha_de_corte ='".$fecha_de_corte."'
								AND
								fecha_y_hora_validacion ='".$fecha_actual." ".$tiempo_actual."'
								AND 
								nombre_archivo ='".$nombre_archivo_registrado."'
								AND
								numero_de_secuencia ='".$numero_secuencia_actual."'
								AND
								prestador_en_archivo ='".$prestador_archivo."'
							";
							$error_bd_seq="";
							$bandera = $coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_update_en_reporte_ips, $error_bd_seq);
							if($error_bd_seq!="")
							{
							    $error_de_base_de_datos.=" ERROR AL ACTUALIZAR PARA REPORTE PRESTADOR: ".$error_bd_seq."<br>";
							    echo $error_de_base_de_datos;
							}//fin if


							$filas_afectadas=intval($coneccionBD->get_filas_afectadas_update() );
							if($filas_afectadas==0)
							{
						    	$sql_insertar_en_reporte_ips="";				    
								$sql_insertar_en_reporte_ips.="insert into gioss_index_reporte_ips_0123";
								$sql_insertar_en_reporte_ips.="(";
								$sql_insertar_en_reporte_ips.="entidad_reportante,";
								$sql_insertar_en_reporte_ips.="entidad_a_reportar,";
								$sql_insertar_en_reporte_ips.="fecha_inicio_periodo,";					
								$sql_insertar_en_reporte_ips.="fecha_de_corte,";					
								$sql_insertar_en_reporte_ips.="fecha_y_hora_validacion,";					
								$sql_insertar_en_reporte_ips.="nombre_archivo,";
								$sql_insertar_en_reporte_ips.="numero_de_secuencia,";
								$sql_insertar_en_reporte_ips.="prestador_en_archivo,";
								$sql_insertar_en_reporte_ips.="nit_prestador_en_archivo,";
								$sql_insertar_en_reporte_ips.="nombre_prestador,";	
								$sql_insertar_en_reporte_ips.="codigo_departamento,";
								$sql_insertar_en_reporte_ips.="codigo_municipio,";
								$sql_insertar_en_reporte_ips.="cantidad_lineas_en_archivo,";
								$sql_insertar_en_reporte_ips.="cantidad_lineas_correctas_en_archivo,";
								$sql_insertar_en_reporte_ips.="cantidad_inconsistencias_para_ips";						
								$sql_insertar_en_reporte_ips.=")";
								$sql_insertar_en_reporte_ips.="values";
								$sql_insertar_en_reporte_ips.="(";
								$sql_insertar_en_reporte_ips.="'".$codigo_prestador_para_insercion."',";
								$sql_insertar_en_reporte_ips.="'".$codigo_eapb_para_insercion."',";
								$sql_insertar_en_reporte_ips.="'".$fecha_inicial_para_analisis."',";					
								$sql_insertar_en_reporte_ips.="'".$fecha_de_corte."',";					
								$sql_insertar_en_reporte_ips.="'".$fecha_actual." ".$tiempo_actual."',";							
								$sql_insertar_en_reporte_ips.="'".$nombre_archivo_registrado."',";							
								$sql_insertar_en_reporte_ips.="'".$numero_secuencia_actual."',";
								$sql_insertar_en_reporte_ips.="'".$prestador_archivo."',";
								$sql_insertar_en_reporte_ips.="'".$nit_prestador_archivo."',";	
								$sql_insertar_en_reporte_ips.="'".$nombre_prestador."',";
								$sql_insertar_en_reporte_ips.="'".$cod_depto."',";	
								$sql_insertar_en_reporte_ips.="'".$cod_municipio."',";	
								$sql_insertar_en_reporte_ips.="'".$cantidad_lineas_en_archivo_para_prestador."',";						
								$sql_insertar_en_reporte_ips.="'".$cantidad_lineas_correctas_en_archivo_para_prestador."',";
								$sql_insertar_en_reporte_ips.="'".$cantidad_inconsistencias_para_ips."'";			
								$sql_insertar_en_reporte_ips.=");";
								$error_bd_seq="";
								$bandera = $coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_insertar_en_reporte_ips, $error_bd_seq);
								if($error_bd_seq!="")
								{
								    $error_de_base_de_datos.=" ERROR AL INSERTAR PARA REPORTE PRESTADOR: ".$error_bd_seq."<br>";
								    echo $error_de_base_de_datos;
								}//fin if
							}//filas afectadas
							//FIN UPSERT


					    }//fin try
					    catch(Exception $e)
					    {
					    	echo "Error al llenar la informacion de los prestadores por archivo: ".$e->getMessage();
					    }//fin catch
					}//fin if si es mayor o igual a 119
					//FIN PARTE CARGA EN TABLA INDEXADORA IPS POR ARCHIVO
					
				}//fin if verifica longitud
				else
				{
					$registros_malos++;//debido a que no cumple estructura

					$array_contador_total_errores_obligatorios_campo[999]++;

					$mensaje_contador_errores="revisando linea ".($nlinea+1)." de $lineas_del_archivo del archivo HEMOFILIA ";
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
					
					if($hubo_inconsistencias_en_HF==false)
					{
						$hubo_inconsistencias_en_HF=true;
					}
					
					
					$columnas_inconsistencias_para_bd=array();
					$columnas_inconsistencias_para_bd=explode(",",$error_longitud);

					//PARTE REESCRIBE PARA CSV SIMPLE DE ABRIR
				    $error_procesado_csv_excel="";
				    $error_procesado_csv_excel.="=\"".implode("\";=\"", $columnas_inconsistencias_para_bd)."\"";
				    //FIN PARTE REESCRIBE PARA CSV SIMPLE DE ABRIR			

				    fwrite($file_inconsistencias_r4725_HF, $error_procesado_csv_excel."\n");
					
					//SUBIDA DE INCONSISTENCIAS A LA BASE DE DATOS
					if(count($columnas_inconsistencias_para_bd)==10)
					{
						
						
						//se insertan los datos de detalles de inconsistencia
						
						$sql_insertar_inconsistencia_hf="";
						$sql_insertar_inconsistencia_hf.=" INSERT INTO gioss_reporte_inconsistencias_r0123_hf ";
						$sql_insertar_inconsistencia_hf.=" ( ";
						$sql_insertar_inconsistencia_hf.=" numero_orden, ";
						$sql_insertar_inconsistencia_hf.=" nombre_archivo, ";
						$sql_insertar_inconsistencia_hf.=" cod_tipo_inconsitencia, ";
						$sql_insertar_inconsistencia_hf.=" nombre_tipo_inconsistencia, ";
						$sql_insertar_inconsistencia_hf.=" cod_grupo_inconsistencia, ";
						$sql_insertar_inconsistencia_hf.=" nombre_grupo_inconsistencia, ";
						$sql_insertar_inconsistencia_hf.=" cod_detalle_inconsistencia, ";
						$sql_insertar_inconsistencia_hf.=" detalle_inconsistencia, ";
						$sql_insertar_inconsistencia_hf.=" numero_linea, ";
						$sql_insertar_inconsistencia_hf.=" numero_campo, ";
						$sql_insertar_inconsistencia_hf.=" fecha_validacion, ";
						$sql_insertar_inconsistencia_hf.=" hora_validacion ";
						$sql_insertar_inconsistencia_hf.=" ) ";
						$sql_insertar_inconsistencia_hf.=" VALUES ";
						$sql_insertar_inconsistencia_hf.=" ( ";
						$sql_insertar_inconsistencia_hf.=" '".$numero_secuencia_actual."', ";
						$sql_insertar_inconsistencia_hf.=" '".$nombre_archivo_registrado."', ";
						$sql_insertar_inconsistencia_hf.=" '".preg_replace("/[^A-Za-z0-9:.\-\s+]/", "", trim($columnas_inconsistencias_para_bd[2]) )."', ";
						$sql_insertar_inconsistencia_hf.=" '".preg_replace("/[^A-Za-z0-9:.\-\s+]/", "", trim($columnas_inconsistencias_para_bd[3]) )."', ";
						$sql_insertar_inconsistencia_hf.=" '".preg_replace("/[^A-Za-z0-9:.\-\s+]/", "", trim($columnas_inconsistencias_para_bd[4]) )."', ";
						$sql_insertar_inconsistencia_hf.=" '".preg_replace("/[^A-Za-z0-9:.\-\s+]/", "", trim($columnas_inconsistencias_para_bd[5]) )."', ";
						$sql_insertar_inconsistencia_hf.=" '".preg_replace("/[^A-Za-z0-9:.\-\s+]/", "", trim($columnas_inconsistencias_para_bd[6]) )."', ";
						$sql_insertar_inconsistencia_hf.=" '".preg_replace("/[^A-Za-z0-9:.\-\s+]/", "", trim($columnas_inconsistencias_para_bd[7]) )."', ";
						$sql_insertar_inconsistencia_hf.=" '".preg_replace("/[^A-Za-z0-9:.\-\s+]/", "", trim($columnas_inconsistencias_para_bd[8]) )."', ";
						$sql_insertar_inconsistencia_hf.=" '".preg_replace("/[^A-Za-z0-9:.\-\s+]/", "", trim($columnas_inconsistencias_para_bd[9]) )."', ";
						$sql_insertar_inconsistencia_hf.=" '".$fecha_actual."', ";
						$sql_insertar_inconsistencia_hf.=" '".$tiempo_actual."' ";
						$sql_insertar_inconsistencia_hf.=" ); ";
						$error_bd_ins="";
						$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_insertar_inconsistencia_hf, $error_bd_ins);
						if($error_bd_ins!="")
						{
							$error_mostrar_bd.="ERROR AL SUBIR LAS INCONSISTENCIAS ".$error_bd_ins."<br>";
						}
						//fin se insertan los datos de detalles de inconsistencia
						
						
					}					
					//FIN SUBIDA INCONSISTENCIAS A LA BASE DE DATOS
				}//fin else longitud no apropiada
				$nlinea++;

				$ram_usada_MB=(memory_get_usage(true)/1048576.2);
				if($fue_cerrada_la_gui==false)
				{
					echo "<script>document.getElementById('medidor_ram').innerHTML='".$ram_usada_MB."';</script>";
				}
			}
			fclose($file_HF);
		}
		//fin parte valida archivo
		
		//cierra el archivo donde se escriben las inconsistencias
		fclose($file_inconsistencias_r4725_HF);
		fclose($file_archivo_registros_buenos);
		fclose($file_archivo_registros_malos);
		fclose($file_archivo_incons_registros_malos);
		
		fclose($file_archivo_registros_que_tienen_afiliados);
		fclose($file_archivo_registros_que_no_tienen_afiliados);

		fclose($file_archivo_log_registros_modificados);						  
		fclose($file_archivo_afiliado_existe_cambio_sexo);
		fclose($file_archivo_afiliado_existe_cambio_fecha_nacimiento);
		
		
		
		if($es_valido_nombre_archivo)
		{
			
			
			//SUBIR TABLA CARGADOS CON EXITO
			if($hubo_inconsistencias_en_HF==false)
			{
				//se eliminan los rechazados porque ya fue exitoso
				$sql_query_delete="";
				$sql_query_delete.=" DELETE FROM gioss_tabla_registros_no_cargados_rechazados_r0123_hf ";
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
				$sql_query_delete.=" DELETE FROM gioss_tabla_registros_cargados_exito_r0123_hf ";
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
				
				$file_HF = fopen($ruta_archivo_hf, 'r') or exit("No se pudo abrir el archivo");
				$nlinea=0;
				while (!feof($file_HF)) 
				{
					$linea_tmp = fgets($file_HF);
					$linea= explode("\n", $linea_tmp)[0];
					$linea=str_replace(",",".",$linea);
					$campos = explode("\t", $linea);
					
					//parte para evitar caracteres extraños en el ultimo campo antes del salto de linea
					$campos[count($campos)-1]=procesar_mensaje($campos[count($campos)-1]);
					
					//pasa a validar los campos
					if(count($campos)==95)
					{
						$query_insertar_campos_a_bd="";
						$query_insertar_campos_a_bd.="INSERT INTO gioss_tabla_registros_cargados_exito_r0123_hf ";
						$query_insertar_campos_a_bd.="(";
						
						$cont_campo_ins=0;
						while($cont_campo_ins<95)
						{
							$query_insertar_campos_a_bd.="campo_hf_de_numero_orden_".$cont_campo_ins.",";
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
						while($cont_campo_ins<95)
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
						
						$mensaje_contador_errores="Subiendo a cargados con exito el registro ".($nlinea+1)." de $lineas_del_archivo del archivo HEMOFILIA ";
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
				fclose($file_HF);
				
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
				$query_registrar_resumen_cargado_con_exito.="INSERT INTO gioss_resumen_cargados_exito_0123 ";
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
				
			
			
			//SUBIR A TABLA CONSOLIDADOS PARA HF
			$estado_validacion_hf=2;
			if($hubo_inconsistencias_en_HF==false)
			{
				$estado_validacion_hf=1;
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
			$query_delete_estado_validacion.="DELETE FROM gioss_tabla_consolidacion_registros_validados_r0123_hf ";
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
			$query_registrar_estado_validacion.="INSERT INTO gioss_tabla_consolidacion_registros_validados_r0123_hf ";
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
			$query_registrar_estado_validacion.="'".$estado_validacion_hf."',";
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
			//FIN SUBIR A TABLA CONSOLIDADOS PARA HF
			
			
			//SUBIR A TABLA DE ESTADO INFORMACION PARA HF
			$estado_validacion_hf=2;
			if($hubo_inconsistencias_en_HF==false)
			{
				$estado_validacion_hf=1;
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
			$query_descripcion_estado_validacion.=" SELECT * FROM gioss_estado_validacion_archivos WHERE codigo_estado_validacion='$estado_validacion_hf' ; ";
			$resultado_query_descripcion_estado_validacion=$coneccionBD->consultar2_no_crea_cierra($query_descripcion_estado_validacion);
			$descripcion_estado_validacion=$resultado_query_descripcion_estado_validacion[0]["descripcion_estado_validacion"];
			
			//query delete si ya habia sido subido para actualizar
			$query_delete_estado_informacion="";
			$query_delete_estado_informacion.=" DELETE FROM gioss_tabla_estado_informacion_r0123_hf ";
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
			$query_registrar_estado_informacion.="INSERT INTO gioss_tabla_estado_informacion_r0123_hf ";
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
			$query_registrar_estado_informacion.="'".$estado_validacion_hf."',";//1
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
			//FIN SUBIR A TABLA DE ESTADO INFORMACION PARA HF
			
			
			
			if(connection_aborted()==false)
			{
				echo "<script>document.getElementById('mensaje').innerHTML='Se ha terminado de revisar y validar el archivo HEMOFILIA';</script>";
				ob_flush();
				flush();
			}
			
			$errores.=procesar_mensaje2($error_mostrar_bd);
		
		}//fin if nombre archivo valido
		
		if($hubo_inconsistencias_en_HF)
		{
			$se_genero_archivo_de_inconsistencias=true;		
			$errores.="Hubo inconsistencias en el archivo HEMOFILIA.<br>";
			
		}

		//PARTE ARCHIVO REPORTE POR IPS(aunque para esta norma de alto costo seria solo el resultado de la validacion del archivo)
		$fecha_inicial_para_analisis=$fecha_de_corte;
		$sql_count_reporte_por_ips="";
		$sql_count_reporte_por_ips.="SELECT count(*) as numero_registros
		FROM gioss_index_reporte_ips_0123 
		WHERE 
		entidad_reportante ='".$cod_prestador."'
		AND
		entidad_a_reportar ='".$cod_eapb."'
		AND
		fecha_inicio_periodo ='".$fecha_inicial_para_analisis."'
		AND
		fecha_de_corte ='".$fecha_de_corte."'
		AND
		fecha_y_hora_validacion ='".$fecha_actual." ".$tiempo_actual."'
		AND 
		nombre_archivo ='".$nombre_archivo_registrado."'
		AND
		numero_de_secuencia ='".$numero_secuencia_actual."'
		";

		$numero_registros_para_reporte_por_ips=0;

		$resultado_query_count_reporte_por_ips=$coneccionBD->consultar2_no_crea_cierra($sql_count_reporte_por_ips);
		if(count($resultado_query_count_reporte_por_ips)>0 
			&& is_array($resultado_query_count_reporte_por_ips) 
			)//fin condicion if
		{
			$numero_registros_para_reporte_por_ips=intval($resultado_query_count_reporte_por_ips[0]['numero_registros']);
			//echo "numero_registros_para_reporte_por_ips $numero_registros_para_reporte_por_ips query sql_count_reporte_por_ips $sql_count_reporte_por_ips<br>";
		}//fin if	

		$offset=0;
		$block_limit=1000;
		if($numero_registros_para_reporte_por_ips>0)
		{

			$file_archivo_reporte_registros_por_ips = fopen($ruta_archivo_reporte_registros_por_ips, "a") or die("fallo la creacion del archivo modo:w reporte para ips ");	

		    $parte_inicial_reporte_registros_por_ips="";
			$parte_inicial_reporte_registros_por_ips.="SISTEMA DE INFORMACION GIOSS\n";
			$parte_inicial_reporte_registros_por_ips.="REPORTE DE ESTADO DE REGISTROS REPORTADOS POR INSTITUCION PRESTADORA DE SERVICIOS IPS\n";
			$parte_inicial_reporte_registros_por_ips.="RESOLUCION 0123\n";
			$parte_inicial_reporte_registros_por_ips.="RESULTADO DEL PROCESO DE VALIDACION\n";

			fwrite($file_archivo_reporte_registros_por_ips, $parte_inicial_reporte_registros_por_ips); 

		    $titulos_reporte="";
			$titulos_reporte.="\"Numero Identificacion Prestador\";\"Codigo Habilitacion Prestador\";\"Nombre Del Prestador\";\"Codigo Del Departamento\";\"Codigo Municipio\";\"Numero de Registros Leidos\";\"Numero de Registros Errados\";\"Numero de Registros Correctos\";\"Relacion Registros Correctos\";\"Numero de Inconsistencias por IPS\"";
			$titulos_reporte=str_replace("_", " ", $titulos_reporte);
			//$titulos_reporte=strtoupper($titulos_reporte);
			fwrite($file_archivo_reporte_registros_por_ips, $titulos_reporte);    		        
		    
		    
			$sql_reporte_por_ips="";
			$sql_reporte_por_ips.="SELECT *
			FROM gioss_index_reporte_ips_0123 
			WHERE 
			entidad_reportante ='".$cod_prestador."'
			AND
			entidad_a_reportar ='".$cod_eapb."'
			AND
			fecha_inicio_periodo ='".$fecha_inicial_para_analisis."'
			AND
			fecha_de_corte ='".$fecha_de_corte."'
			AND
			fecha_y_hora_validacion ='".$fecha_actual." ".$tiempo_actual."'
			AND 
			nombre_archivo ='".$nombre_archivo_registrado."'
			AND
			numero_de_secuencia ='".$numero_secuencia_actual."'
			LIMIT $block_limit OFFSET $offset

			";
			$resultado_query_reporte_por_ips=$coneccionBD->consultar2_no_crea_cierra($sql_reporte_por_ips);
			foreach ($resultado_query_reporte_por_ips as $key => $reporte_pretador_actual) 
			{
				$nit_prestador_a_escribir=trim($reporte_pretador_actual['nit_prestador_en_archivo']);
				$codigo_habilitacion_a_escribir=trim($reporte_pretador_actual['prestador_en_archivo']);
				$nombre_prestador_a_escribir=trim($reporte_pretador_actual['nombre_prestador']);
				$codigo_departamento_a_escribir=trim($reporte_pretador_actual['codigo_departamento']);
				$codigo_municipio_a_escribir=trim($reporte_pretador_actual['codigo_municipio']);
				$cantidad_lineas_en_archivo_a_escribir=intval(trim($reporte_pretador_actual['cantidad_lineas_en_archivo']) );
				$cantidad_lineas_correctas_en_archivo_a_escribir=intval(trim($reporte_pretador_actual['cantidad_lineas_correctas_en_archivo']) );
				$lineas_incorrectas_a_escribir=	$cantidad_lineas_en_archivo_a_escribir-$cantidad_lineas_correctas_en_archivo_a_escribir;
				$indicador_de_registros_correctos=0;
				if($cantidad_lineas_en_archivo_a_escribir>0)
				{
					$indicador_de_registros_correctos= $cantidad_lineas_correctas_en_archivo_a_escribir/$cantidad_lineas_en_archivo_a_escribir;
					$indicador_de_registros_correctos=round(floatval($indicador_de_registros_correctos),2,PHP_ROUND_HALF_UP);
				}//fin if

				$cantidad_inconsistencias_para_ips_a_escribir="";
				if(isset($reporte_pretador_actual['cantidad_inconsistencias_para_ips']) )
				{
					$cantidad_inconsistencias_para_ips_a_escribir=intval(trim($reporte_pretador_actual['cantidad_inconsistencias_para_ips']) );
				}//fin if

				if(strlen($codigo_habilitacion_a_escribir)==11 &&
					       (substr($codigo_habilitacion_a_escribir,0,1)=="5" || substr($codigo_habilitacion_a_escribir,0,1)=="8")
			       )
				{
				    $codigo_habilitacion_a_escribir="0".$codigo_habilitacion_a_escribir;
				}
				else if(strlen($codigo_habilitacion_a_escribir)==10)
				{
				    $codigo_habilitacion_a_escribir=$codigo_habilitacion_a_escribir."01";
				}
				else if(strlen($codigo_habilitacion_a_escribir)==9 
					&& (substr($codigo_habilitacion_a_escribir,0,1)=="5" || substr($codigo_habilitacion_a_escribir,0,1)=="8")
				)
				{
				    $codigo_habilitacion_a_escribir="0".$codigo_habilitacion_a_escribir."01";
				}//fin else 

		    	while(strlen($codigo_habilitacion_a_escribir)<12 
					&& $codigo_habilitacion_a_escribir!="999"
				)
				{
					//incrementa cuando es menor de 12
					$codigo_habilitacion_a_escribir="0".$codigo_habilitacion_a_escribir;
				}//fin while

				$linea_reporte="";
				$linea_reporte.="=\"$nit_prestador_a_escribir\";=\"$codigo_habilitacion_a_escribir\";=\"$nombre_prestador_a_escribir\";=\"$codigo_departamento_a_escribir\";=\"$codigo_municipio_a_escribir\";$cantidad_lineas_en_archivo_a_escribir;$lineas_incorrectas_a_escribir;$cantidad_lineas_correctas_en_archivo_a_escribir;=\"$indicador_de_registros_correctos %\";$cantidad_inconsistencias_para_ips_a_escribir";
				fwrite($file_archivo_reporte_registros_por_ips, "\n".$linea_reporte);

			}//fin foreach
			fclose($file_archivo_reporte_registros_por_ips);
		}//fin if numero registros mayor de cero

		//FIN PARTE ARCHIVO REPORTE POR IPS(aunque para esta norma de alto costo seria solo el resultado de la validacion del archivo)

		//PARTE ESCRIBE ARCHIVO ERRORES CAMPO INDIVIDUAL		
		$archivo_reporte_errores_por_campo = fopen($ruta_archivo_reporte_calificacion_campos, "a") or die("fallo la creacion del archivo modo:w reporte errores por campo ");

		$lineas_totales_archivo_temp=intval($lineas_del_archivo);
		//lineas_del_archivo

		$fecha_terminacion="";
		$hora_terminacion="";

		$fecha_terminacion = "".date('Y-m-d');
	    $hora_terminacion = "".date('H:i:s');

		$parte_inicial_reporte_evaluacion_resultado_validacion="";
		$parte_inicial_reporte_evaluacion_resultado_validacion.="SISTEMA DE INFORMACION GIOSS\n";
		$parte_inicial_reporte_evaluacion_resultado_validacion.="Evaluacion Resultados Por Campo Proceso validacion\n";
		$parte_inicial_reporte_evaluacion_resultado_validacion.="Resolucion 0123\n";
		$parte_inicial_reporte_evaluacion_resultado_validacion.="Numero de Registros Validados ( ".$lineas_totales_archivo_temp." ) \n";
		$parte_inicial_reporte_evaluacion_resultado_validacion.="\"$cod_eapb\";\"Periodo ".$codigo_perido_para_archivo."\"\n\"Fecha Y Hora Inicio Validacion $fecha_actual $tiempo_actual\"\n\"Fecha Y Hora Fin Validacion $fecha_terminacion $hora_terminacion\"";
		fwrite($archivo_reporte_errores_por_campo, $parte_inicial_reporte_evaluacion_resultado_validacion);



		$titulos_conteo="\"NUMERO CAMPO\";\"DESCRIPCION CAMPO\";\"NUMERO DE REGISTROS CORRECTOS\";\"NUMERO DE REGISTROS INCONSISTENTES\";\"PORCENTAJE DE REGISTROS INCONSISTENTES\";\"0105 Inconsistencias Calidad\";\"0104 Inconsistencias Campo En Blanco\";\"0103 Inconsistencias Valor Permitido\";\"0102 Inconsistencias Formato\";\"0101 Inconsistencias Longitud\";\"TOTAL INCONSISTENCIAS\"";
		
		fwrite($archivo_reporte_errores_por_campo, "\n".$titulos_conteo);
		foreach($array_contador_total_errores_obligatorios_campo as $key=>$error_campo_actual)
		{
			$nombre_campo="";
			$numero_orden_actual=999;
			if(isset($array_numero_orden[$key])==true)
			{
				$numero_orden_actual=$array_numero_orden[$key];
			}//fin if
			
			if(isset($array_descripcion_nombre_campo_bd[$numero_orden_actual])==true)
			{
				$nombre_campo=$array_descripcion_nombre_campo_bd[$numero_orden_actual];
			}
			else
			{
				$nombre_campo="Error Relacionado Con Estructura Del Registro";
			}//fin else if

			$cantidad_inconsistencias_0105=intval($array_contador_total_inconsistencias_campo_0105[$key]);
			$cantidad_inconsistencias_0104=intval($array_contador_total_inconsistencias_campo_0104[$key]);
			$cantidad_inconsistencias_0103=intval($array_contador_total_inconsistencias_campo_0103[$key]);
			$cantidad_inconsistencias_0102=intval($array_contador_total_inconsistencias_campo_0102[$key]);
			$cantidad_inconsistencias_0101=intval($array_contador_total_inconsistencias_campo_0101[$key]);

			$TOTAL_INCONSISTENCIAS=$cantidad_inconsistencias_0105+$cantidad_inconsistencias_0104+$cantidad_inconsistencias_0103+$cantidad_inconsistencias_0102+$cantidad_inconsistencias_0101;

			$ratio=0;
			if($lineas_totales_archivo_temp>0)
			{
				$ratio=round( ((intval($error_campo_actual)*100)/$lineas_totales_archivo_temp),2,PHP_ROUND_HALF_UP );
			}//fin if atrapa division por cero

			$string_resultado_campo_actual="";
			$string_resultado_campo_actual.="=\"".$key."\"";
			$string_resultado_campo_actual.=";=\"".$nombre_campo."\"";
			$string_resultado_campo_actual.=";".($lineas_totales_archivo_temp-$error_campo_actual)."";
			$string_resultado_campo_actual.=";".$error_campo_actual."";
			$string_resultado_campo_actual.=";=\"".$ratio." % \"";
			$string_resultado_campo_actual.=";".$cantidad_inconsistencias_0105."";
			$string_resultado_campo_actual.=";".$cantidad_inconsistencias_0104."";
			$string_resultado_campo_actual.=";".$cantidad_inconsistencias_0103."";
			$string_resultado_campo_actual.=";".$cantidad_inconsistencias_0102."";
			$string_resultado_campo_actual.=";".$cantidad_inconsistencias_0101."";
			$string_resultado_campo_actual.=";".$TOTAL_INCONSISTENCIAS."";
			fwrite($archivo_reporte_errores_por_campo, "\n".$string_resultado_campo_actual);
		}//fin foreach
		fclose($archivo_reporte_errores_por_campo);
		//echo print_r($array_contador_total_errores_obligatorios_campo,true);
		//FIN PARTE ESCRIBE ARCHIVO ERRORES CAMPO INDIVIDUAL
		
		//CREAR ZIP
		$archivos_a_comprimir=array();
		$archivos_a_comprimir[0]=$ruta_archivo_inconsistencias_HF;
		$archivos_a_comprimir[]=$ruta_registros_malos;
		$archivos_a_comprimir[]=$ruta_registros_buenos;
		$archivos_a_comprimir[]=$ruta_inconsistencias_registros_malos;
		$archivos_a_comprimir[]=$ruta_registros_que_tienen_afiliados;
		$archivos_a_comprimir[]=$ruta_registros_que_no_tienen_afiliados;
		$archivos_a_comprimir[]=$ruta_archivo_afiliado_existe_cambio_sexo;
		$archivos_a_comprimir[]=$ruta_archivo_afiliado_existe_cambio_fecha_nacimiento;
		$archivos_a_comprimir[]=$ruta_archivo_reporte_calificacion_campos;
		$archivos_a_comprimir[]=$ruta_archivo_reporte_registros_por_ips;
		$ruta_zip=$rutaTemporal."inconsistencias0123HF_".$cod_prestador."_".$fecha_actual."_".$tiempo_actual_string.'.zip';
		if(file_exists($ruta_zip))
		{
			unlink($ruta_zip);
		}
		$result_zip = create_zip($archivos_a_comprimir,$ruta_zip);	
		//FIN CREAR ZIP
		
		//YA NO ESTA EN USO EL ARCHIVO
		$query_update_esta_siendo_procesado="";
		$query_update_esta_siendo_procesado.=" UPDATE gioss_0123_esta_validando_actualmente ";
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
				echo "<script>alert('error al actualizar el estado actual de validacion en tiempo real  0123 ');</script>";
			}
		}
		//FIN YA NO ESTA EN USO EL ARCHIVO
		
		//BOTONES DESCARGA
		$botones="";
		$botones.=" <input type=\'button\' value=\'Descargar archivo de inconsistencias para HEMOFILIA\'  class=\'btn btn-success color_boton\' onclick=\"download_inconsistencias_campos(\'$ruta_zip\');\"/> ";
		
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
				$mail->Subject = "Inconsistencias HEMOFILIA 0123 ";
				$mail->AltBody = "Cordial saludo,\n El sistema ha determinado que su archivo(s)  contiene diversas inconsistencias,\n las cuales pueden ser: campos con información inconsistente, usuarios duplicados ó el uso de caracteres especiales(acentos,'Ñ' o É,Ý, ¥, ¤, ´)";
		    
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
	if(!( isset($_FILES["0123_HEMOFILIA_file"])))
	{
		$ultimo_error="El archivo no se cargo ";
	}
	else if(!($_FILES["0123_HEMOFILIA_file"]["error"]==0))
	{
		$ultimo_error="Error con el archivo de tipo ".$_FILES["0123_HEMOFILIA_file"]["error"];
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