<?php
ignore_user_abort(true);
set_time_limit(2592000);
ini_set('max_execution_time', 2592000);
ini_set('memory_limit', '4000M');

error_reporting(E_ALL);
ini_set('display_errors', '1');

/*
ini_set('display_errors',1); 
 error_reporting(E_ALL);
 */

 
include_once ('../utiles/clase_coneccion_bd.php');
include ("../librerias_externas/PHPMailer/class.phpmailer.php");
include ("../librerias_externas/PHPMailer/class.smtp.php");
require_once ("../librerias_externas/PHPExcel/PHPExcel.php");
require_once ("../librerias_externas/PHPExcel/PHPExcel/Writer/Excel2007.php");
require_once '../utiles/queries_utiles_bd.php';
require_once '../utiles/configuracion_global_email.php';

require_once 'reparacion_campos_duplicados.php';
require_once '../res4505/reparacion_duplicados_por_txt.php';

require_once '../utiles/crear_zip.php';

require_once '../res4505/criterios_reparacion_4505.php';


class LecturaArchivo extends criterios_reparacion_4505
{

    var $delimitador = ",";
    var $i = 0;
    var $seq;
    var $codEntidad;
    var $fechaRemision;
	
    var $nit_prestador="0000";
    var $modulo_de_informacion="";
    var $tema_de_informacion="";
    var $tipo_de_identificacion_entidad_reportadora="";
    var $tipo_de_regimen_de_la_informacion_reportada="";
    var $consecutivo_de_archivo="";
    var $identificacion_entidad_administradora="";
    var $cod_registro_especial_pss="";
    var $cadena_fecha_corte="";
    
    var $nombre_archivo_4505="";
    
    var $codigo_periodo="";
    
    var $fecha_inicio_periodo="";
    var $fecha_de_corte_periodo="";
    
    var $secuencia_actual_para_email="";
    var $ruta_archivos_inconsistencias_para_email="";
    
    var $nombre_archivo_generado="";
    
    var $verificacion_inicial_global=true;
    
    var $cod_eapb_global="";
    
    var $consecutivo_fixer=1;
    
    var $tipo_periodo_tiempo_global;

    var $tipo_entidad_que_efectua_el_cargue="individual_ips";

    var $USA_SMTP_CONFIGURACION_CORREO_fromclass;
    var $REQUIERE_AUTENTIFICACION_EMAIL_fromclass;
    var $PUERTO_CONF_EMAIL_fromclass;
    var $USUARIO_CONF_EMAIL_fromclass;
    var $PASS_CONF_EMAIL_fromclass;
    var $HOST_CONF_EMAIL_fromclass;
    var $SMTPSECURE_CONF_EMAIL_CE_fromclass;
    var $SMTPAUTH_CONF_EMAIL_CE_fromclass;

    var $ruta_archivo_fecha_nacimiento_invalida;

    var $ruta_archivo_afiliado_no_existe;

    var $ruta_archivo_registros_excluidos_por_no_registro_de_actividad=array();

    var $global_ruta_temporales;

    function __construct() 
    {
	global $USA_SMTP_CONFIGURACION_CORREO;
	global $REQUIERE_AUTENTIFICACION_EMAIL;
	global $PUERTO_CONF_EMAIL;
	global $USUARIO_CONF_EMAIL;
	global $PASS_CONF_EMAIL;
	global $HOST_CONF_EMAIL;
	global $SMTPSECURE_CONF_EMAIL_CE;
	global $SMTPAUTH_CONF_EMAIL_CE;
	
	$this->USA_SMTP_CONFIGURACION_CORREO_fromclass=$USA_SMTP_CONFIGURACION_CORREO;
	$this->REQUIERE_AUTENTIFICACION_EMAIL_fromclass=$REQUIERE_AUTENTIFICACION_EMAIL;
	$this->PUERTO_CONF_EMAIL_fromclass=$PUERTO_CONF_EMAIL;
	$this->USUARIO_CONF_EMAIL_fromclass=$USUARIO_CONF_EMAIL;
	$this->PASS_CONF_EMAIL_fromclass=$PASS_CONF_EMAIL;
	$this->HOST_CONF_EMAIL_fromclass=$HOST_CONF_EMAIL;
	$this->SMTPSECURE_CONF_EMAIL_CE_fromclass=$SMTPSECURE_CONF_EMAIL_CE;
	$this->SMTPAUTH_CONF_EMAIL_CE_fromclass=$SMTPAUTH_CONF_EMAIL_CE;
	
    }

    function lecturaPyP($file,
			$nit_entidad_prestadora="0000",
			$val1_modulo_informacion="",
			$val2_tema_informacion="",
			$val3_tipo_id_entidad_reportadora="",
			$val4_tipo_regimen="",
			$val5_consecutivo_nombre_archivo="",
			$nit_epba="",
			$crespss="",
			$string_fecha_corte="",
			$nombre_archivo_param="nombre_prueba",
			$codPeriodo="0",
			$cod_eapb_local="",
			$tipo_periodo_tiempo="trimestral",
			$tipo_entidad_que_efectua_el_cargue_local="individual_ips"
			) 
	{
        $flag_enviar_mail = 0;
		$this->nit_prestador=$nit_entidad_prestadora;
		$this->modulo_de_informacion=$val1_modulo_informacion;
		$this->tema_de_informacion=$val2_tema_informacion;
		$this->tipo_de_identificacion_entidad_reportadora=$val3_tipo_id_entidad_reportadora;
		$this->tipo_de_regimen_de_la_informacion_reportada=$val4_tipo_regimen;
		$this->consecutivo_de_archivo=$val5_consecutivo_nombre_archivo;
		
		$this->identificacion_entidad_administradora=$nit_epba;
		$this->cod_registro_especial_pss=$crespss;
		
		$this->cadena_fecha_corte=$string_fecha_corte;
		$this->nombre_archivo_4505=$nombre_archivo_param;
		$this->codigo_periodo=$codPeriodo;
		$this->cod_eapb_global=$cod_eapb_local;
		
		$this->tipo_periodo_tiempo_global=$tipo_periodo_tiempo;
		
		$this->tipo_entidad_que_efectua_el_cargue=$tipo_entidad_que_efectua_el_cargue_local;
		
		
        function my_warning_handler2($errno, $errstr, $errfile, $errline) {
            switch ($errno) {
                case E_WARNING:
                    
                default:
            }
        }

        set_error_handler('my_warning_handler2', E_ALL);
        
        

		
	//parte que lee archivo 
	$bandera_email=false;

	for ($j = 0; $j < sizeof($file); $j++) 
	{
	    $bandera_email = $this->CorreccionArchivo4505_verificacion_escritura($file[$j]['archivo'], $file[$j]['nombre_archivo'], $file[$j]['tipo_archivo'], sizeof($file));

	    if ($bandera_email) 
	    {
		$flag_enviar_mail = 1;
	    }
	}
	
	$hay_coneccion_a_internet=$this->is_connected_to_internet();
	
	
	
	if($this->verificacion_inicial_global==true && $hay_coneccion_a_internet==true)
	{
		if ($flag_enviar_mail == 1) 
		{
	
		    // inicio envio de mail
	
		    $mail = new PHPMailer();
		    //inicio configuracion mail de acuerdoa archivo gloabl de configuracion
		    if($this->USA_SMTP_CONFIGURACION_CORREO_fromclass==true)
		    {
			
		     $mail->IsSMTP();
		     $mail->SMTPAuth = $this->SMTPAUTH_CONF_EMAIL_CE_fromclass;
		     $mail->SMTPSecure = $this->SMTPSECURE_CONF_EMAIL_CE_fromclass;
		     $mail->Host = $this->HOST_CONF_EMAIL_fromclass;
		     $mail->Port = $this->PUERTO_CONF_EMAIL_fromclass;
		     if($this->REQUIERE_AUTENTIFICACION_EMAIL_fromclass==true)
		     {
		      $mail->Username = $this->USUARIO_CONF_EMAIL_fromclass;
		      $mail->Password = $this->PASS_CONF_EMAIL_fromclass;
		     }//fin if da el usuario y password
		    }//fin if usa configuracion_global_email.php
		    $mail->From = "sistemagioss@gmail.com";
		    $mail->FromName = "GIOSS";
		    $mail->Subject = "Reparacion PyP 4505";
		    $mail->AltBody = "Cordial saludo,\n El sistema ha reparado las  diversas inconsistencias del archivo ";
		    $mail->MsgHTML("Cordial saludo,\n El sistema ha determinado que su archivo(s)  contiene diversos errores.<strong>GIOSS</strong>.");
				$mail->AddAttachment($this->ruta_archivos_inconsistencias_para_email);
		    $mail->AddAddress($_SESSION['correo'], "Destinatario");
	
		    $mail->IsHTML(true);
	
		    if (!$mail->Send()) 
		    {
			if(connection_aborted()==false)
			{
			    echo "<script>alert('No se pudo enviar el mensaje al correo ".$_SESSION['correo']."-".$mail->ErrorInfo."' );</script>";
			}
		    } 
		    else 
		    {
			if(connection_aborted()==false)
			{
			    echo "<script>alert('Mensaje enviado al correo ".$_SESSION['correo']." ');</script>";
			}
		    }
	
		    //fin envio de mail
		    return array("hubo_errores"=>true,"num_seq_def"=>'0');
		} 
		elseif ($flag_enviar_mail ==0) 
		{

			// inicio envio de mail

			$mail = new PHPMailer();
			//inicio configuracion mail de acuerdoa archivo gloabl de configuracion
			if($this->USA_SMTP_CONFIGURACION_CORREO_fromclass==true)
			{
			    
			 $mail->IsSMTP();
			 $mail->SMTPAuth = $this->SMTPAUTH_CONF_EMAIL_CE_fromclass;
			 $mail->SMTPSecure = $this->SMTPSECURE_CONF_EMAIL_CE_fromclass;
			 $mail->Host = $this->HOST_CONF_EMAIL_fromclass;
			 $mail->Port = $this->PUERTO_CONF_EMAIL_fromclass;
			 if($this->REQUIERE_AUTENTIFICACION_EMAIL_fromclass==true)
			 {
			  $mail->Username = $this->USUARIO_CONF_EMAIL_fromclass;
			  $mail->Password = $this->PASS_CONF_EMAIL_fromclass;
			 }//fin if da el usuario y password
			}//fin if usa configuracion_global_email.php
			$mail->From = "sistemagioss@gmail.com";
			$mail->FromName = "GIOSS";
			$mail->Subject = "Reparacion PyP 4505";
			$mail->AltBody = "Cordial saludo,\n El sistema ha determinado que no fue necesario reparar el archivo, los campos del archivo por criterios obligatorias de la norma ";

			$mail->MsgHTML("Cordial saludo,\n El sistema ha determinado que no se encuentran inconsistencias obligatorias 
									 en el archivo a reparar  (Aunque pueden haberse reparado inconsistencias de tipo informativo).<strong>GIOSS</strong>.");
									 
			$mail->AddAttachment($this->ruta_archivos_inconsistencias_para_email);
			
			$mail->AddAddress($_SESSION['correo'], "Destinatario");

			$mail->IsHTML(true);

			if (!$mail->Send()) 
			{
			    if(connection_aborted()==false)
			    {
				 echo "<script>alert('No se pudo enviar el mensaje al correo ".$_SESSION['correo']."-".$mail->ErrorInfo."' );</script>";
			    }
			} 
			else 
			{
			    if(connection_aborted()==false)
			    {
				echo "<script>alert('Mensaje enviado al correo ".$_SESSION['correo']." ');</script>";
			    }
			}

			return array("hubo_errores"=>false,"num_seq_def"=>'0');
		}//fin else
	}//fin if verificacion inicial global, solo envia el correo si se ha verificado el archivo completo y hay internet
	else if($this->verificacion_inicial_global==true)
	{
	    if(connection_aborted()==false)
	    {
		echo "<script>alert('No se pudo enviar los archivos al correo ".$_SESSION['correo']." debido a que no hay internet, pero puede revisar su reporte de inconsistencias desde la interfaz de consulta validacion ');</script>";
	    }
	}
    }//fin funcion lectura PyP
    
    //verifica si hay coneccion a internet
    function is_connected_to_internet()
    {
	$connected = @fsockopen("www.google.com", 80); 
					    //website, port  (try 80 or 443)
	if ($connected)
	{
	    $is_conn = true; //action when connected
	    fclose($connected);
	}
	else
	{
	    $is_conn = false; //action in connection failure
	}
	return $is_conn;
    
    }//fin funcion verifica coneccion a internet
    
    
    function diferencia_dias_entre_fechas($fecha_1,$fecha_2)
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

    	
	var $diccionario_identificacion=array();
	var $diccionario_identificacion_lineas=array();
	
	var $diccionario_identificacion_para_bool=array();
	
	
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


	function alterar_mes($mes, $cantidad, $signo)
	{
	    $mes_alterado_int=intval($mes);
	    if($signo="+"){$mes_alterado_int=intval($mes)+intval($cantidad);}
	    if($signo="-"){$mes_alterado_int=intval($mes)-intval($cantidad);}
	    
	    if($mes_alterado_int<=0 || $mes_alterado_int>12)
	    {
	        return false;
	    }
	    else
	    {
	        $string_mes_alt="".$mes_alterado_int;
	        if(strlen($mes_alterado_int)==1)
	        {
	            $string_mes_alt="0".$mes_alterado_int;
	        }
	        
	        return $string_mes_alt;
	        
	    }
	}//fin funcion alterar mes

	function numeroAfechaExcel($numero)
	{
		if(ctype_digit($numero)==true 
			&& strlen($numero)<=6 
			&& $numero>=2
		)//fin condicion
		{
		    $fecha = date_create(trim("1900-01-01"));
			//date sub resta, por lo cual al poner un dia negativo suma
		    date_sub($fecha, date_interval_create_from_date_string('-'.(intval($numero)-2).' days'));
		    $resultado="".date_format($fecha, 'Y-m-d');

	   		return $resultado;
		}
		else
		{
			return false;
		}
	}//fin function
	
	function corrector_formato_fecha($campo_fecha,$es_fecha_nacimiento=false,$campo_especial=-1,$campo_debug=0)
	{
	    date_default_timezone_set ("America/Bogota");
	    $dejarlo_como_esta=false;
	    
	    $fecha_corte=explode("-",$this->fecha_de_corte_periodo);
	    $date_de_corte=date($fecha_corte[0]."-".$fecha_corte[1]."-".$fecha_corte[2]);
	 
	    $fecha_corregida="";
	    $fecha_corregida=trim($campo_fecha);

	    if(ctype_digit($fecha_corregida)==true)
	    {
	    	$resultadoNumeroAfechaExcel=$this->numeroAfechaExcel($fecha_corregida);
	    	if($resultadoNumeroAfechaExcel!==false)
	    	{
	    		$fecha_corregida=$resultadoNumeroAfechaExcel;
	    	}//fin if
	    }//fin if

	    $array_fecha_corregida=array();
	    if(strlen($fecha_corregida)==10)
	    {	
	    	$fecha_corregida=str_replace("/","-",$fecha_corregida);
	    	$array_fecha_corregida=explode("-",$fecha_corregida);
		}
		else if(strlen($fecha_corregida)==8)
		{
			//parte year CORREGIR PARA QUE COMPARE CON FECHAS CALENDARIO HASTA LA FECHA DE CORTE
			$posicion_year=strpos($fecha_corregida, $fecha_corte[0]);
			$esta_al_final_el_year=false;
			if(($posicion_year+4)===8)
			{
			    $esta_al_final_el_year=true;
			}//fin if
			$yearfc=substr($fecha_corregida,$posicion_year,4);
			//fin parte year
			

			//parte mes
			$cont_mes=1;
			$posicion_mes=0;
			while($cont_mes<=12)
			{
			    
			    $mes_fix="".$cont_mes;
			    if(strlen($mes_fix)==1)
			    {
			        $mes_fix="0".$mes_fix;
			    }
			    
			    $posicion_mes=strpos($fecha_corregida, $mes_fix );
			    //se coloca !== para que identifique tipo
			    if($posicion_mes!==false 
			    && ($posicion_mes<$posicion_year || $posicion_mes>($posicion_year+3) )
			    && ( ($esta_al_final_el_year==true && $posicion_mes==($posicion_year-2) )
			     || ($esta_al_final_el_year==false && $posicion_mes==($posicion_year+4) )
			    )
			    )
			    {
			        break;
			    }
			    $cont_mes++;
			}//fin while mes
			if($posicion_mes===false)
			{
			    //echo "no esta en la mitad \n";
			    if($esta_al_final_el_year==true)
			    {
			        $posicion_mes=0;
			    }
			    else
			    {
			        $posicion_mes=$posicion_year+4;
			        $mesTemp=substr($fecha_corregida,$posicion_mes,2);
			        if(intval($mesTemp)>12)
			        {
			            $posicion_mes=$posicion_year+6;
			        }//fin if
			    }//fin else
			}
			$mesfc=substr($fecha_corregida,$posicion_mes,2);
			//fin parte mes
			
			//echo "pm ".$posicion_mes."\n";


			//parte dia
			$cont_dia=1;
			$posicion_dia=0;
			while($cont_dia<=31)
			{
			    
			    $dia_fix="".$cont_dia;
			    if(strlen($dia_fix)==1)
			    {
			        $dia_fix="0".$dia_fix;
			    }
			    
			    $posicion_dia=strpos($fecha_corregida, $dia_fix );
			    if($posicion_dia!==false
			    && ($posicion_dia<$posicion_year || $posicion_dia>($posicion_year+3) )
			    )
			    {
			    //echo "dia ".$dia_fix." pos ".$posicion_dia." pm ".$posicion_mes." py ".$posicion_year."\n";
			    }
			    //se coloca !== para que identifique tipo
			    if($posicion_dia!==false 
			    && ($posicion_dia<$posicion_year || $posicion_dia>($posicion_year+3) )
			    && ($posicion_dia<$posicion_mes || $posicion_dia==($posicion_mes+2) )
			    )
			    {
			        //echo "entro dia ".$dia_fix." pos ".$posicion_dia."\n";
			        break;
			    }
			    $cont_dia++;
			}//fin while dia			
			$diafc=substr($fecha_corregida,$posicion_dia,2);
			//fin parte dia

			if(intval($mesfc)<=12 && intval($diafc)<=12)
			{
			    $diferencia_meses_con_mes=intval($fecha_corte[1])-intval($mesfc);
			    $diferencia_meses_con_dia=intval($fecha_corte[1])-intval($diafc);
			    if($diferencia_meses_con_mes<0 && $yearfc==$fecha_corte[0] )
			    {
			        $temp=$mesfc;
			        $mesfc=$diafc;
			        $diafc=$temp;
			    }//mes es mayor que la fecha de corte lo cual no es posible si es el mismo year
			    else if($diferencia_meses_con_dia>=0 && $yearfc==$fecha_corte[0] )
			    {
			        if($diferencia_meses_con_mes>$diferencia_meses_con_dia)
			        {
    			        $temp=$mesfc;
    			        $mesfc=$diafc;
    			        $diafc=$temp;
    			    }
			    }//fin else if cuando mes y dia son menores  al mes de la fehca de corte
			}//fin if


			$fecha_corregida=$yearfc."-".$mesfc."-".$diafc;			
	    	$array_fecha_corregida=explode("-",$fecha_corregida);

		}//fin else if corrigiendo fechas de menos de 8 
		else if(strlen($fecha_corregida)>10)
		{
			$fecha_corregida=str_replace("/","-",$fecha_corregida);		
	    	$array_fecha_corregida=explode("-",$fecha_corregida);
	    	if(count($array_fecha_corregida)==3)
	    	{
	    		$array_fecha_corregida[0]=intval($array_fecha_corregida[0])."";
	    		$array_fecha_corregida[1]=intval($array_fecha_corregida[1])."";
	    		$array_fecha_corregida[2]=intval($array_fecha_corregida[2])."";
	    		$fecha_corregida=$array_fecha_corregida[0]."-".$array_fecha_corregida[1]."-".$array_fecha_corregida[2];	
	    	}//fin if
	    	else
	    	{
	    		$dejarlo_como_esta=true;
	    	}//fin else
			
			$array_fecha_corregida=explode("-",$fecha_corregida);
		}//fin if es mayor a 10
	    
	    if(is_array($array_fecha_corregida)
		&& count($array_fecha_corregida)==3
		)
	     {
		 	$fecha_corregida=$this->corrige_longitud_fecha($array_fecha_corregida,$fecha_corte);
	     }//fin if
	     
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
								if($campo_especial==-1 || $campo_especial==33)
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
						    if($campo_especial==-1 || $campo_especial==33)
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
						    if($campo_especial==-1 || $campo_especial==33)
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
						if($campo_especial==-1 || $campo_especial==33)
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
					if($campo_especial==-1 || $campo_especial==33)
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
	    }//fin if array count es 3
	    else
	    {
			if($es_fecha_nacimiento==false)
			{
			    if($campo_especial==-1 || $campo_especial==33)
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
	    
	    $nuevo_array_fecha_corregida=explode("-",$fecha_corregida);
	    if(is_array($nuevo_array_fecha_corregida)
	       && count($nuevo_array_fecha_corregida)==3
	       && $es_fecha_nacimiento==false
	       )
	    {
			$fecha_corregida=$this->corrige_longitud_fecha($nuevo_array_fecha_corregida,$fecha_corte,1);
	    }
	    
	    if($es_fecha_nacimiento==false)
	    {	    
			$date_de_corte_12_meses_menos=date('Y-m-d',strtotime($fecha_corte[0]."-".$fecha_corte[1]."-".$fecha_corte[2] . ' -24 months'));
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
			    //$fecha_corregida=$date_de_corte;
			  }
			  if($verificacion_fecha_corte_pos_10_meses<0 && $campo_especial==33)//excede la fecha de corete, diferencia de dias es inferior
			  {
			    $fecha_corregida=$date_de_corte_posterior_10_meses;
			  }
			  else if($verificacion_fecha_corte_12_meses_menos>0)//es inferior, por eso la diferencia de dias es mayor de cero
			  {
			   $fecha_corregida="1800-01-01";
			  }
			  
			}//fin si excede 1900 entonces no es codigo
	    }//fin if si no es fecha de nacimiento
	    
	    if($es_fecha_nacimiento==true)
	    {	    
	      //echo "<script>alert('pre $campo_fecha pos $fecha_corregida $caso_al_que_entro');</script>";
	    }
	    
	    /*
	    if($campo_debug==50)
	    {
	      echo "<script>alert('pre $campo_fecha pos $fecha_corregida $caso_al_que_entro');</script>";
	    }
	    */

	    
	    return $fecha_corregida;
	}//fin funcion
	
	function corrector_valor_permitido_numerico_criterio_025($valor_campo)
	{
	    $valor_permitido_corregido=$valor_campo;
	    
	    if($valor_campo!="1"
	       && $valor_campo!="2"
	       && $valor_campo!="3"
	       && $valor_campo!="4"
	       && $valor_campo!="5"
	       && $valor_campo!="6")
	    {
		$valor_permitido_corregido="6";
	    }
	    
	    return $valor_permitido_corregido;
	}//fin funcion criterio 025
	
	function corrector_valor_permitido_numerico_criterio_006($valor_campo)
	{
	    $valor_permitido_corregido=$valor_campo;
	    
	    if($valor_campo!="1"
	       && $valor_campo!="2"
	       && $valor_campo!="3"
	       && $valor_campo!="4"
	       && $valor_campo!="5"
	       && $valor_campo!="6"
	       && $valor_campo!="7"
	       && $valor_campo!="8"
	       && $valor_campo!="9"
	       && $valor_campo!="10"
	       && $valor_campo!="11"
	       && $valor_campo!="12"
	       && $valor_campo!="13")
	    {
		$valor_permitido_corregido="13";
	    }
	    
	    return $valor_permitido_corregido;
	}//fin funcion criterio 006
	
	
	
	
	function convert_to_standard_notation($floatAsString)
	{
	    $norm = strval(floatval($floatAsString));
	
	    if (($e = strrchr($norm, 'E')) === false) {
		return $norm;
	    }
	
	    return number_format($norm, -intval(substr($e, 1)));
	}
	
	//FUNCION QUE VALIDA Y CORRIGE LOS 118 CAMPOS DE 4505
	public function correccion_campos_registro_de_archivo_4505($array_fields, $numLinea,  &$consecutivo_errores,&$conexion_bd_validar_campos)
	{
		$fallo_sexo=false;
		$fallo_fecha_nacimiento=false;
		
		$validador_boolean=true;
		$mensajes_error_campos ="";
		$registro_corregido="";
		
		$nombre_archivo4505="";
		$nombre_archivo4505=$this->nombre_archivo_4505;
		
		//CONEXION BASE DE DATOS
		//$conexion_bd_validar_campos = new conexion();
		
		//parte que trae las descripciones de las inconsistencias
		$array_tipo_inconsistencia=array();
		$array_grupo_inconsistencia=array();
		$array_detalle_inconsistencia=array();

		$query1_tipo_validacion="SELECT * FROM gioss_tipo_inconsistencias;";
		$resultado_query1_tipo_validacion=$conexion_bd_validar_campos->consultar2_no_crea_cierra($query1_tipo_validacion);
		foreach($resultado_query1_tipo_validacion as $tipo_validacion)
		{
			$array_tipo_inconsistencia[$tipo_validacion["tipo_validacion"]]=$tipo_validacion["descripcion_tipo_validacion"];
		}
		$query2_grupo_validacion="SELECT * FROM gioss_grupo_inconsistencias;";
		$resultado_query2_grupo_validacion=$conexion_bd_validar_campos->consultar2_no_crea_cierra($query2_grupo_validacion);
		foreach($resultado_query2_grupo_validacion as $grupo_validacion)
		{
			$array_grupo_inconsistencia[$grupo_validacion["grupo_validacion"]]=$grupo_validacion["descripcion_grupo_validacion"];
		}
		$query3_detalle_validacion="SELECT * FROM gioss_detalle_inconsistecias_4505;";
		$resultado_query3_detalle_validacion=$conexion_bd_validar_campos->consultar2_no_crea_cierra($query3_detalle_validacion);
		foreach($resultado_query3_detalle_validacion as $detalle_validacion)
		{
			$array_detalle_inconsistencia[$detalle_validacion["codigo_detalle_inconsistencia"]]=$detalle_validacion["descripcion_inconsistencia"];
		}	
		//fin parte que trae las descripciones de las inconsistencias
		
		//echo "<script>alert('".$array_detalle_inconsistencia["0103034"]." nombre ".$nombre_archivo4505." ".$resultado_query3_detalle_validacion[0]["codigo_detalle_inconsistencia"]." ".$resultado_query3_detalle_validacion[0]["descripcion_inconsistencia"]."');</script>";
		
		//calculo de la edad con la fecha de nacimiento
		date_default_timezone_set ("America/Bogota");
		$fecha_actual = date('Y-m-d');
		
		$fecha_ini=explode("-",$this->fecha_inicio_periodo);
		$date_ini_reporte=date($fecha_ini[0]."-".$fecha_ini[1]."-".$fecha_ini[2]);
		$fecha_fin=explode("-",$this->fecha_de_corte_periodo);
		$date_fin_reporte=date($fecha_fin[0]."-".$fecha_fin[1]."-".$fecha_fin[2]);
		
		$year_corte=intval($fecha_fin[0]);
		
		//PARTE ANALISIS FECHA DE CORTE ES MENSUAL A TRIMESTRAL
		$old_date_fin_reporte=$date_fin_reporte;
		
		
		//si corresponde al periodo 1 aaaa-01-01 hasta aaaa-03-31
		$corresponde_primer_periodo=true;
		$primer_periodo_li=$year_corte."-01-01";
		$primer_periodo_ls=$year_corte."-03-31";
		$fecha_de_corte_del_archivo_es_menor_primer_periodo_li=$this->diferencia_dias_entre_fechas($date_fin_reporte,$primer_periodo_li);
		$fecha_de_corte_del_archivo_es_mayor_primer_periodo_ls=$this->diferencia_dias_entre_fechas($date_fin_reporte,$primer_periodo_ls);
		if($fecha_de_corte_del_archivo_es_menor_primer_periodo_li>0
		   || $fecha_de_corte_del_archivo_es_mayor_primer_periodo_ls<0)
		{
		    $corresponde_primer_periodo=false;
		}
		
		if($corresponde_primer_periodo==true)
		{
		    $date_fin_reporte=date($primer_periodo_ls);
		}
		//fin si corresponde al periodo 1
		
		//si corresponde al periodo 2 aaaa-04-01 hasta aaaa-06-30
		$corresponde_segundo_periodo=true;
		$segundo_periodo_li=$year_corte."-04-01";
		$segundo_periodo_ls=$year_corte."-06-30";
		$fecha_de_corte_del_archivo_es_menor_segundo_periodo_li=$this->diferencia_dias_entre_fechas($date_fin_reporte,$segundo_periodo_li);
		$fecha_de_corte_del_archivo_es_mayor_segundo_periodo_ls=$this->diferencia_dias_entre_fechas($date_fin_reporte,$segundo_periodo_ls);
		if($fecha_de_corte_del_archivo_es_menor_segundo_periodo_li>0
		   || $fecha_de_corte_del_archivo_es_mayor_segundo_periodo_ls<0)
		{
		    $corresponde_segundo_periodo=false;
		}
		
		if($corresponde_segundo_periodo==true)
		{
		    $date_fin_reporte=date($segundo_periodo_ls);
		}
		//fin si corresponde al periodo 2
		
		//si corresponde al periodo 3 aaaa-07-01 hasta aaaa-09-30
		$corresponde_tercer_periodo=true;
		$tercer_periodo_li=$year_corte."-07-01";
		$tercer_periodo_ls=$year_corte."-09-30";
		$fecha_de_corte_del_archivo_es_menor_tercer_periodo_li=$this->diferencia_dias_entre_fechas($date_fin_reporte,$tercer_periodo_li);
		$fecha_de_corte_del_archivo_es_mayor_tercer_periodo_ls=$this->diferencia_dias_entre_fechas($date_fin_reporte,$tercer_periodo_ls);
		if($fecha_de_corte_del_archivo_es_menor_tercer_periodo_li>0
		   || $fecha_de_corte_del_archivo_es_mayor_tercer_periodo_ls<0)
		{
		    $corresponde_tercer_periodo=false;
		}
		
		if($corresponde_tercer_periodo==true)
		{
		    $date_fin_reporte=date($tercer_periodo_ls);
		}
		//fin si corresponde al periodo 3
		
		//si corresponde al periodo 4 aaaa-10-01 hasta aaaa-12-31
		$corresponde_cuarto_periodo=true;
		$cuarto_periodo_li=$year_corte."-10-01";
		$cuarto_periodo_ls=$year_corte."-12-31";
		$fecha_de_corte_del_archivo_es_menor_cuarto_periodo_li=$this->diferencia_dias_entre_fechas($date_fin_reporte,$cuarto_periodo_li);
		$fecha_de_corte_del_archivo_es_mayor_cuarto_periodo_ls=$this->diferencia_dias_entre_fechas($date_fin_reporte,$cuarto_periodo_ls);
		if($fecha_de_corte_del_archivo_es_menor_cuarto_periodo_li>0
		   || $fecha_de_corte_del_archivo_es_mayor_cuarto_periodo_ls<0)
		{
		    //echo "<script>alert(' $fecha_de_corte_del_archivo_es_menor_cuarto_periodo_li $fecha_de_corte_del_archivo_es_menor_cuarto_periodo_li');</script>";
		    $corresponde_cuarto_periodo=false;
		}
		
		if($corresponde_cuarto_periodo==true)
		{
		    $date_fin_reporte=date($cuarto_periodo_ls);
		}
		//fin si corresponde al periodo 4
		$this->fecha_de_corte_periodo=$date_fin_reporte;
		//echo "<script>alert(' ANTES: $old_date_fin_reporte, DESPUES: $date_fin_reporte');</script>";
		//FIN PARTE DECHA DE CORTE ES MENSUAL A TRIMESTRAL

		require_once '../utiles/conf_personalizada.php';
		$NOMBRE_ENTIDAD_PERSONALIZADA=get_entidad_personalizada();

		$bool_existe_afiliado=true;

		if($NOMBRE_ENTIDAD_PERSONALIZADA=="PrepagadaCoomeva" || true)//para que cambie si encuentra algo y mantener consistencia
		{
			//echo "entro 1 ".$NOMBRE_ENTIDAD_PERSONALIZADA." test campo 9 antes $campo_corregido <br>";
			//PARTE PRE CORRECCION SEXO Y FECHA NACIMIENTO DE ACUERDO A TABLAS DE REGIMEN
			//gioss_afiliados_eapb_rc, id_afiliado, tipo_id_afiliado,$this->tipo_de_regimen_de_la_informacion_reportada
			$query_bd_existe_afiliado_en_tabla_regimen="";
			$resultados_query_existe_afiliado_tablas_regimen=array();
			$nombre_tabla_afiliado_hallado="";
			$numero_id_c4=preg_replace("/[^a-zA-Z0-9]+/", "", trim($array_fields[4]) );
			if(strtoupper( trim($array_fields[3]) )=="NU" )
			{
				$array_fields[3]="NV";
			}//fin if
			$tipo_id_c3=preg_replace("/[^a-zA-Z0-9]+/", "", trim($array_fields[3]) );
			$tipo_id_c3=strtoupper($tipo_id_c3);

			if($this->tipo_de_regimen_de_la_informacion_reportada=="C")
			{
				$nombre_tabla_afiliado_hallado="gioss_afiliados_eapb_rc";

				$query_bd_existe_afiliado_en_tabla_regimen="SELECT * FROM ".$nombre_tabla_afiliado_hallado." WHERE id_afiliado = '".$numero_id_c4."' AND tipo_id_afiliado = '".$tipo_id_c3."' AND codigo_eapb='".$this->cod_eapb_global."' ;";
				
			}
			if($this->tipo_de_regimen_de_la_informacion_reportada=="S")
			{
				$nombre_tabla_afiliado_hallado="gioss_afiliados_regimen_subsidiado";

				$query_bd_existe_afiliado_en_tabla_regimen="SELECT * FROM ".$nombre_tabla_afiliado_hallado." WHERE id_afiliado = '".$numero_id_c4."' AND tipo_id_afiliado = '".$tipo_id_c3."' AND codigo_eapb='".$this->cod_eapb_global."' ;";

			}
			if($this->tipo_de_regimen_de_la_informacion_reportada=="E" || $this->tipo_de_regimen_de_la_informacion_reportada=="O")
			{
				$nombre_tabla_afiliado_hallado="gioss_afiliados_eapb_mp";

				$query_bd_existe_afiliado_en_tabla_regimen="SELECT * FROM ".$nombre_tabla_afiliado_hallado." WHERE id_afiliado = '".$numero_id_c4."' AND tipo_id_afiliado = '".$tipo_id_c3."' AND codigo_eapb='".$this->cod_eapb_global."' ;";

			}
			if($this->tipo_de_regimen_de_la_informacion_reportada=="P")
			{
				$nombre_tabla_afiliado_hallado="gioss_afiliados_eapb_rp";

				$query_bd_existe_afiliado_en_tabla_regimen="SELECT * FROM ".$nombre_tabla_afiliado_hallado." WHERE id_afiliado = '".$numero_id_c4."' AND tipo_id_afiliado = '".$tipo_id_c3."' AND codigo_eapb='".$this->cod_eapb_global."' ;";

			}
			if($this->tipo_de_regimen_de_la_informacion_reportada=="N")
			{
				$nombre_tabla_afiliado_hallado="gioss_afiliados_eapb_nv";

				$query_bd_existe_afiliado_en_tabla_regimen="SELECT * FROM ".$nombre_tabla_afiliado_hallado." WHERE id_afiliado = '".$numero_id_c4."' AND tipo_id_afiliado = '".$tipo_id_c3."' AND codigo_eapb='".$this->cod_eapb_global."' ;";

			}//fin if
			$error_bd_afiliados="";
			$resultados_query_existe_afiliado_tablas_regimen=$conexion_bd_validar_campos->consultar_no_warning_get_error_no_crea_cierra($query_bd_existe_afiliado_en_tabla_regimen,$error_bd_afiliados);


			$num_filas_resultado_existe_tablas_regimen=count($resultados_query_existe_afiliado_tablas_regimen);

			$se_modifico_sexo_o_fecha_de_acuerdo_a_tabla_regimen=false;
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
				$numero_campo_sexo=10;
				$sexo_en_registro_archivo=strtoupper(trim($array_fields[$numero_campo_sexo]) );
				$sexo_anterior=$sexo_en_registro_archivo;
				$numero_campo_fecha_nacimiento=9;
				$fecha_nacimiento_en_registro_archivo=trim($array_fields[$numero_campo_fecha_nacimiento]);
				$fecha_anterior=$fecha_nacimiento_en_registro_archivo;
				$sexo_en_bd=strtoupper(trim($resultados_query_existe_afiliado_tablas_regimen[0]['sexo']) );
				$fecha_nacimiento_en_bd=trim($resultados_query_existe_afiliado_tablas_regimen[0]['fecha_nacimiento']);

				if($sexo_en_bd!="" && $fecha_nacimiento_en_bd!="")
				{
					$eran_diferentes=false;
					if($array_fields[10]!=$sexo_en_bd || $array_fields[9]!=$fecha_nacimiento_en_bd)
					{
						//echo $array_fields[10]." ".$sexo_en_bd." ".$array_fields[9]." ".$fecha_nacimiento_en_bd."<br>";
						$eran_diferentes=true;
					}//fin if

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
								$array_fields[10]=$sexo_en_bd;
								$se_modifico_sexo_o_fecha_de_acuerdo_a_tabla_regimen=true;
							}//fin if
						}//fin if
						
					}//fin if datos de bd no estan vacios

					$fecha_nacimiento_en_registro_archivo=$this->corrector_formato_fecha($fecha_nacimiento_en_registro_archivo,true);
					if($this->formato_fecha_valida_quick($fecha_nacimiento_en_bd) )
					{
						$array_fields[9]=$fecha_nacimiento_en_bd;
						$se_modifico_sexo_o_fecha_de_acuerdo_a_tabla_regimen=true;
						
					}//fin if fecha nacimiento es valida
					else
					{
						$fecha_nacimiento_en_bd=$this->corrector_formato_fecha($fecha_nacimiento_en_bd,true);
						if($this->formato_fecha_valida_quick($fecha_nacimiento_en_bd) )
						{
							if($fecha_nacimiento_en_bd!=$fecha_nacimiento_en_registro_archivo)
							{
								$array_fields[9]=$fecha_nacimiento_en_bd;
								$se_modifico_sexo_o_fecha_de_acuerdo_a_tabla_regimen=true;
							}//fin if
						}//fin if fecha nacimeinto es valida

					}//fin else

					if($eran_diferentes==true)
					{
						//echo "D ".$array_fields[10]." ".$sexo_en_bd." ".$array_fields[9]." ".$fecha_nacimiento_en_bd."<br>";
						ob_flush();
						flush();
					}
				}//fin if datos de bd no estan vacios

				//se habilito de nuevo
				if($se_modifico_sexo_o_fecha_de_acuerdo_a_tabla_regimen==true)
				{
					
					$array_fields[5]=$this->alphanumericAndSpace3(trim($resultados_query_existe_afiliado_tablas_regimen[0]['primer_apellido']) );
					$array_fields[6]=$this->alphanumericAndSpace3(trim($resultados_query_existe_afiliado_tablas_regimen[0]['segundo_apellido']) );
					$array_fields[7]=$this->alphanumericAndSpace3(trim($resultados_query_existe_afiliado_tablas_regimen[0]['primer_nombre']) );
					$array_fields[8]=$this->alphanumericAndSpace3(trim($resultados_query_existe_afiliado_tablas_regimen[0]['segundo_nombre']) );

					
				}//fin if

				$array_fields[5]=str_replace(array(".",",",";",":","-"), "", $array_fields[5]);
				$array_fields[6]=str_replace(array(".",",",";",":","-"), "", $array_fields[6]);
				$array_fields[7]=str_replace(array(".",",",";",":","-"), "", $array_fields[7]);
				$array_fields[8]=str_replace(array(".",",",";",":","-"), "", $array_fields[8]);

				//coloca none ya que puede venir desde la tabla de afiliados en blanco y ser calificado como incorrecto
				if($array_fields[5]==""){$array_fields[5]="NONE";}
				if($array_fields[6]==""){$array_fields[6]="NONE";}
				if($array_fields[7]==""){$array_fields[7]="NONE";}
				if($array_fields[8]==""){$array_fields[8]="NONE";}

				//PARTE VERIFICA SI EL AFILIADO CORRESPONDE A UNO DE LOS AFILIADOS DUPLICADOS ENTRE SI POR SOLO NUMERO ID
				//en caso de que si corresponda, reemplaza el tipo de identificacion por el almacenado en
				// la tabla unicos_de_tabla_duplicados_afiliados_mp
				$numero_campo_primer_nombre=7;
				$numero_campo_segundo_nombre=8;
				$numero_campo_primer_apellido=5;
				$numero_campo_segundo_apellido=6;
				$error_bd_pertenece="";
				$query_pertenece_a_un_afiliado_duplicado="SELECT * FROM unicos_de_tabla_duplicados_afiliados_mp where numero_id='".$numero_id_c4."' and sexo='".$array_fields[10]."' and fecha_de_nacimiento='".$array_fields[9]."' ;";
				$resultados_pertenece_a_un_afiliado_duplicado=$conexion_bd_validar_campos->consultar_no_warning_get_error_no_crea_cierra($query_pertenece_a_un_afiliado_duplicado,$error_bd_pertenece);
				if(is_array($resultados_pertenece_a_un_afiliado_duplicado) && count($resultados_pertenece_a_un_afiliado_duplicado)>0 )
				{
					foreach ($resultados_pertenece_a_un_afiliado_duplicado as $key => $datosAfiliadoDuplicadoEnTablaPrincipal) 
					{
						if(
							$array_fields[9]==$datosAfiliadoDuplicadoEnTablaPrincipal['fecha_de_nacimiento']
							&& $array_fields[10]==$datosAfiliadoDuplicadoEnTablaPrincipal['sexo']
							&& ($array_fields[5]==$datosAfiliadoDuplicadoEnTablaPrincipal['primer_apellido']
								|| $array_fields[5]==$datosAfiliadoDuplicadoEnTablaPrincipal['primer_nombre']
								|| $array_fields[5]=="NONE"
								)//fin primer apellido
							&& ($array_fields[7]==$datosAfiliadoDuplicadoEnTablaPrincipal['primer_apellido']
								|| $array_fields[7]==$datosAfiliadoDuplicadoEnTablaPrincipal['primer_nombre']
								|| $array_fields[7]=="NONE"
								)//fin primer nombre
							)//fin condicion
						{
							$array_fields[3]=$datosAfiliadoDuplicadoEnTablaPrincipal['tipo_id'];

							
							if(
								$array_fields[$numero_campo_segundo_apellido]=="NONE" // solo se mira igual a none ya que el vacio lo mira antes
								&& $datosAfiliadoDuplicadoEnTablaPrincipal['segundo_apellido']!=""
								&& $datosAfiliadoDuplicadoEnTablaPrincipal['segundo_apellido']!="NONE"
							)//fin condicion
							{
								$array_fields[$numero_campo_segundo_apellido]=$datosAfiliadoDuplicadoEnTablaPrincipal['segundo_apellido'];
							}//fin if

							
							if(
								$array_fields[$numero_campo_segundo_nombre]=="NONE" // solo se mira igual a none ya que el vacio lo mira antes
								&& $datosAfiliadoDuplicadoEnTablaPrincipal['segundo_nombre']!=""
								&& $datosAfiliadoDuplicadoEnTablaPrincipal['segundo_nombre']!="NONE"
							)//fin condicion
							{
								$array_fields[$numero_campo_segundo_nombre]=$datosAfiliadoDuplicadoEnTablaPrincipal['segundo_nombre'];
							}//fin if

							
							if(
								$array_fields[$numero_campo_primer_apellido]=="NONE" // solo se mira igual a none ya que el vacio lo mira antes
								&& $datosAfiliadoDuplicadoEnTablaPrincipal['primer_apellido']!=""
								&& $datosAfiliadoDuplicadoEnTablaPrincipal['primer_apellido']!="NONE"
							)//fin condicion
							{
								$array_fields[$numero_campo_primer_apellido]=$datosAfiliadoDuplicadoEnTablaPrincipal['primer_apellido'];
							}//fin if

							
							if(
								$array_fields[$numero_campo_primer_nombre]=="NONE" // solo se mira igual a none ya que el vacio lo mira antes
								&& $datosAfiliadoDuplicadoEnTablaPrincipal['primer_nombre']!=""
								&& $datosAfiliadoDuplicadoEnTablaPrincipal['primer_nombre']!="NONE"
							)//fin condicion
							{
								$array_fields[$numero_campo_primer_nombre]=$datosAfiliadoDuplicadoEnTablaPrincipal['primer_nombre'];
							}//fin if
						}//fin if
					}//fin foreach
				}//fin if encontro que pertenece a un afiliado duplicado en la tabl principal
				//FIN PARTE VERIFICA SI EL AFILIADO CORRESPONDE A UNO DE LOS AFILIADOS DUPLICADOS ENTRE SI POR SOLO NUMERO ID 
				

			}//fin if hay concidencia en bd
			else
			{
				$bool_existe_afiliado=false;
			}//fin else
		    //FIN PARTE LLENA ARCHIVO NUEVO ORIGINAL CON POSIBLES REGISTROS AFILIADOS MODIFICADOS SEXO FECHA NACIMIENTO
		}//fin if es coomeva prepagada

		//PARTE CORRECCION CARACTERES ESPECIALES CAMPOS ENTIDADES
		$arrayCaracteresEspecialesNoPermitidosParaCodigosEntidades=array(" ","/","\\","|","*","<",">","?","¿","!","¡");
		if(isset($array_fields[119])==true)
		{
			$array_fields[119]=utf8_encode( str_replace($arrayCaracteresEspecialesNoPermitidosParaCodigosEntidades, '', trim($array_fields[119]) ) );
		}//fin if

		$arrayCaracteresEspecialesNoPermitidosParaCodigosEntidades=array(" ","/","\\","|","*","<",">","?","¿","!","¡");
		if(isset($array_fields[2])==true)
		{
			$array_fields[2]=str_replace($arrayCaracteresEspecialesNoPermitidosParaCodigosEntidades, '', trim($array_fields[2]) );
		}//fin if
		//FIN PARTE CORRECCION CARACTERES ESPECIALES CAMPOS ENTIDADES

		unset($resultados_query_existe_afiliado_tablas_regimen);
		
		$array_fields[9]=$this->corrector_formato_fecha($array_fields[9],true);
		
		$fecha_nacimiento= explode("-",$array_fields[9]);
		$bool_fecha_nacimiento_valida=true;
		if(count($fecha_nacimiento)!=3
		   || !(ctype_digit($fecha_nacimiento[0]) && ctype_digit($fecha_nacimiento[1]) && ctype_digit($fecha_nacimiento[2]) )
		   || !checkdate($fecha_nacimiento[1],$fecha_nacimiento[2],$fecha_nacimiento[0]))
		{			
			$bool_fecha_nacimiento_valida=false;
			$validador_boolean=false;
		}//verificacion formato fecha

		//VERIFICACION FECHA NACIMIENTO ES MENOR 1900-12-31 O EXCEDE LA FECHA DE CORTE
		if($bool_fecha_nacimiento_valida==true)
		{
			$es_menor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),"1900-12-31");
			if($es_menor_a_1900_12_31>0)
			{
				$bool_fecha_nacimiento_valida=false;
				$validador_boolean=false;
				
			}//fin if

			$fecha_nacimiento_excede_fecha_corte=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),$date_fin_reporte);
			if($fecha_nacimiento_excede_fecha_corte<0)
			{
				$bool_fecha_nacimiento_valida=false;
				$validador_boolean=false;
			}//fin if
		}//fin if verifica si es menor a 1900-12-31 y marca como invalida
		//FIN VERIFICACION FECHA NACIMIENTO ES MENOR 1900-12-31 O EXCEDE LA FECHA DE CORTE


		if($bool_fecha_nacimiento_valida==false)
		{
			$string_campos_temp=implode("|", $array_fields);
		    $file_registros_con_fecha_invalida=fopen($this->ruta_archivo_fecha_nacimiento_invalida, "a") or die("fallo la creacion del archivo");
		    fwrite($file_registros_con_fecha_invalida, "\n".trim($string_campos_temp) );
		    fclose($file_registros_con_fecha_invalida);
		}//fin if si es invalida la escribe en el archivo

		if($NOMBRE_ENTIDAD_PERSONALIZADA=="PrepagadaCoomeva" && $bool_existe_afiliado==false)
		{

			$bool_fecha_nacimiento_valida=false;
			$validador_boolean=false;
		}//fin if afiliado no existe y es coomeva prepagada

		if($bool_existe_afiliado==false)
		{
			$string_campos_temp=implode("|", $array_fields);
		    $file_registros_afiliado_no_existe=fopen($this->ruta_archivo_afiliado_no_existe, "a") or die("fallo la creacion del archivo");
		    fwrite($file_registros_afiliado_no_existe, "\n".trim($string_campos_temp) );
		    fclose($file_registros_afiliado_no_existe);
		}//fin if afiliado no existe 
		
		
		$edad= -1;
		$edad_dias =-1;
		$edad_meses =-1;
		$edad_semanas = -1;
		$verificador_edad= -1;
		
		if($bool_fecha_nacimiento_valida==true)
		{
		    
		    $string_fecha_nacimiento=date($fecha_nacimiento[0]."-".$fecha_nacimiento[1]."-".$fecha_nacimiento[2]);
		    
		    $fecha_nacimiento_format=new DateTime($string_fecha_nacimiento);
		    $fecha_corte_format=new DateTime($date_fin_reporte);
		
		    $interval = date_diff($fecha_nacimiento_format,$fecha_corte_format);
		    $edad_dias =(float)($interval->days);
		    
		    //$edad= (float)($interval->days / 365);		    
		    //$edad_meses = (float)($interval->days / 30.4368499);
		    //$edad_meses_2 = (float)($interval->format('%m')+ 12 * $interval->format('%y'));
		    
		    $array_fecha_nacimiento=explode("-",$string_fecha_nacimiento);
		    $array_fecha_corte=explode("-",$date_fin_reporte);
		    $array_edad=$this->edad_years_months_days($array_fecha_nacimiento[2]."-".$array_fecha_nacimiento[1]."-".$array_fecha_nacimiento[0],$array_fecha_corte[2]."-".$array_fecha_corte[1]."-".$array_fecha_corte[0]);
		    $edad_meses=(intval($array_edad['y'])*12)+$array_edad['m'];
		    $edad=intval($array_edad['y']);
		    
		    //echo "<script>alert('total age years $edad , total age months $edad_meses, total days $edad_dias fecha nacimiento $string_fecha_nacimiento fecha de corte $date_fin_reporte');</script>";
		    
		    $edad_semanas = (float)($interval->days / 7);
		    $verificador_edad= (float)$interval->format("%r%a");
		    
		    //echo "<script>alert('$edad_semanas $verificador_edad');</script>";
		}
		//fin calculo de la edad
		
		//corrige sexos indicados en minuscula
		$bool_sexo_es_valido=true;
		$array_fields[10]=trim($this->alphanumericAndSpace4($array_fields[10]));
		if($array_fields[10]=="m")
		{
		    $array_fields[10]="M";
		}
		else if($array_fields[10]=="f")
		{
		    $array_fields[10]="F";
		}
		
		//valida si el sexo indicado es valido
		if($array_fields[10]!="F" && $array_fields[10]!="M")
		{
			$bool_sexo_es_valido=false;
		    
			$validador_boolean=false;
			
		}
		//fin campo 10
		
		
		//echo "<script>alert('entro $numLinea ".$array_fields[10]."');</script>";
		
		//NOTA: el parametro $array_fields pasa por referencia debido a que se altera dentro de la funcion correccion_campo
		    
		$cont_corrector_notacion_cientifica=0;
		while($cont_corrector_notacion_cientifica<count($array_fields))
		{
		    if(!ctype_digit(trim($array_fields[$cont_corrector_notacion_cientifica]))
		       && is_numeric(trim($array_fields[$cont_corrector_notacion_cientifica])))
		    {
			    $antes=$array_fields[$cont_corrector_notacion_cientifica];
			    
			    $array_fields[$cont_corrector_notacion_cientifica]="".$this->convert_to_standard_notation($array_fields[$cont_corrector_notacion_cientifica]);
			    
			    $despues=$array_fields[$cont_corrector_notacion_cientifica];
			    //echo "<script>alert('$antes $despues');</script>";
		    }
		    $cont_corrector_notacion_cientifica++;
		}
		
		
		
		
		
		//CORRIGE LOS CAMPOS SOLO SI EL SEXO Y LA FECHA DE NACIMIENTO SON VALIDAS
		if($bool_fecha_nacimiento_valida==true && $bool_sexo_es_valido==true)
		{
			//echo "Uso de Memoria Antes en linea $numLinea: ".memory_get_usage (true)." F=".memory_get_usage()."<br>";
		    $this->orden_para_correccion_campos_y_correccion($array_fields,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,0,$conexion_bd_validar_campos);
		    $this->orden_para_correccion_campos_y_correccion($array_fields,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,1,$conexion_bd_validar_campos);
		    		    
		    //FASE ESCRITURA
		    $cont_para_escribir_campos=0;
		    while($cont_para_escribir_campos<count($array_fields))
		    {
		     if($registro_corregido!=""){$registro_corregido.="|";}
		     $registro_corregido.=$array_fields[$cont_para_escribir_campos];
		     $cont_para_escribir_campos++;
		    }//fin while

		    /*
		    echo "Uso de Memoria despues en linea $numLinea: ".memory_get_usage (true)." F=".memory_get_usage()."<br>";
		    ob_flush();
		    flush();
		    */
		      
		    
		}//fin if solo valida si fecha de nacimiento y sexo es valid apartir del campo 11 contando desde cero
		else
		{
		    if($bool_sexo_es_valido==false)
		    {
			$fallo_sexo=true;
			//echo "<script>alert('fallo registro sexo $numLinea');</script>";
		    }//fin if
		    if($bool_fecha_nacimiento_valida==false)
		    {
			$fallo_fecha_nacimiento=true;
			//echo "<script>alert('fallo registro fecha de nacimiento $numLinea');</script>";
		    }//fin if
		}//fin else para cuando no es valida la fecha de nacimiento o el sexo
		
		$array_retorno=array();
		$array_retorno["booleano"]=$validador_boolean;
		$array_retorno["detalle_correccion_del_error"]=$mensajes_error_campos;
		$array_retorno["registro_corregido"]=$registro_corregido;
		$array_retorno["fallo_sexo"]=$fallo_sexo;
		$array_retorno["fallo_fecha_nacimiento"]=$fallo_fecha_nacimiento;
		
		return $array_retorno;
	}
	//FIN FUNCION QUE VALIDA LOS 118 CAMPOS DE 4505
	
	
	
	public function correccion_errores_campos_PyP_4505($linea_con_campos_de_archivo_pyp,$numLinea,&$consecutivo_errores,&$conexion_bd_validar_campos)
	{
	    $registro_corregido="";
	    $error_linea = "";
	    $validador_boolean=true;
	    
	    $fallo_sexo=false;
	    $fallo_fecha_nacimiento=false;
	    $fallo_numero_campos=false;
	    
	    $array_campos = explode("|", $linea_con_campos_de_archivo_pyp);
	    
	    //echo "<script>alert('$numLinea ".count($array_campos)."');</script>";
	    
	    $nombre_archivo4505="";
	    $nombre_archivo4505=$this->nombre_archivo_4505;
	    
	    //CONEXION BASE DE DATOS
	    //$conexion_bd_validar_campos = new conexion();
	    
	    //parte que trae las descripciones de las inconsistencias
	    $array_tipo_inconsistencia=array();
	    $array_grupo_inconsistencia=array();
	    $array_detalle_inconsistencia=array();

	    $query1_tipo_validacion="SELECT * FROM gioss_tipo_inconsistencias;";
	    $resultado_query1_tipo_validacion=$conexion_bd_validar_campos->consultar2_no_crea_cierra($query1_tipo_validacion);
	    foreach($resultado_query1_tipo_validacion as $tipo_validacion)
	    {
		    $array_tipo_inconsistencia[$tipo_validacion["tipo_validacion"]]=$tipo_validacion["descripcion_tipo_validacion"];
	    }
	    $query2_grupo_validacion="SELECT * FROM gioss_grupo_inconsistencias;";
	    $resultado_query2_grupo_validacion=$conexion_bd_validar_campos->consultar2_no_crea_cierra($query2_grupo_validacion);
	    foreach($resultado_query2_grupo_validacion as $grupo_validacion)
	    {
		    $array_grupo_inconsistencia[$grupo_validacion["grupo_validacion"]]=$grupo_validacion["descripcion_grupo_validacion"];
	    }
	    $query3_detalle_validacion="SELECT * FROM gioss_detalle_inconsistecias_4505;";
	    $resultado_query3_detalle_validacion=$conexion_bd_validar_campos->consultar2_no_crea_cierra($query3_detalle_validacion);
	    foreach($resultado_query3_detalle_validacion as $detalle_validacion)
	    {
		    $array_detalle_inconsistencia[$detalle_validacion["codigo_detalle_inconsistencia"]]=$detalle_validacion["descripcion_inconsistencia"];
	    }	
	    //fin parte que trae las descripciones de las inconsistencias
	    
	    if($numLinea==0 && count($array_campos)==5)
	    {
		    //parte para verificar cada uno de los 5 campos de la primera linea
		    $this->fecha_inicio_periodo=$array_campos[2];
		    $this->fecha_de_corte_periodo=$array_campos[3];
		    
		    $fecha_ini_reporte_temp=explode("-",$array_campos[2]);
		    $fecha_fin_reporte_temp=explode("-",$array_campos[3]);
		    
		    $cond_fecha_ini=checkdate($fecha_ini_reporte_temp[1],$fecha_ini_reporte_temp[2],$fecha_ini_reporte_temp[0]);
		    $cond_fecha_fin=checkdate($fecha_fin_reporte_temp[1],$fecha_fin_reporte_temp[2],$fecha_fin_reporte_temp[0]);
		    
		    if(!$cond_fecha_ini || !$cond_fecha_fin)
		    {
		    	$numero_campo_a_corregir=-1;
				$validador_boolean=false;
				if($error_linea!=""){$error_linea.="|";}
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_inconsistencia["0102001"])[1];
				$error_linea.=$consecutivo_errores.",".$nombre_archivo4505.",01,".$array_tipo_inconsistencia["01"].",0102,".$array_grupo_inconsistencia["0102"].",0102001,$cadena_descripcion_inconsistencia ...VR:".count($array_campos)." ,".$numLinea.",".$numero_campo_a_corregir;
				$consecutivo_errores++;
		    }//fin if
		    
		    $tipo_de_registro = $array_campos[0];
		    $codigoEPS = $array_campos[1];
		    $numero_registros=$array_campos[4];
	    }
	    else if($numLinea>0 
	    	&& (count($array_campos)==119 || (count($array_campos)==120 && $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120") )
	    	)
	    {
		
		//parte para reparar cada uno de los 119 campos
		$array_resultados=array();
		$array_resultados= $this->correccion_campos_registro_de_archivo_4505($array_campos,$numLinea,$consecutivo_errores,$conexion_bd_validar_campos);
		
		$error_linea = $array_resultados["detalle_correccion_del_error"];
		$validador_boolean= $array_resultados["booleano"];
		$registro_corregido= $array_resultados["registro_corregido"];
		$fallo_sexo=$array_resultados["fallo_sexo"];
		$fallo_fecha_nacimiento=$array_resultados["fallo_fecha_nacimiento"];
	    }
	    else
	    {
		     if($numLinea==0)
		     {
		     	$numero_campo_a_corregir=-1;
				$validador_boolean=false;
				if($error_linea!=""){$error_linea.="|";}
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_inconsistencia["0301001"])[1];
				$error_linea.=$consecutivo_errores.",".$nombre_archivo4505.",03,".$array_tipo_inconsistencia["03"].",0301,".$array_grupo_inconsistencia["0301"].",0301001,$cadena_descripcion_inconsistencia ...VR:".count($array_campos)." ,".$numLinea.",".$numero_campo_a_corregir;
				$consecutivo_errores++;
		     }
		     else
		     {
		     	$numero_campo_a_corregir=-1;
				$validador_boolean=false;
				if($error_linea!=""){$error_linea.="|";}
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_inconsistencia["0301002"])[1];
				$error_linea.=$consecutivo_errores.",".$nombre_archivo4505.",03,".$array_tipo_inconsistencia["03"].",0301,".$array_grupo_inconsistencia["0301"].",0301002,$cadena_descripcion_inconsistencia ...VR:".count($array_campos)." ,".$numLinea.",".$numero_campo_a_corregir;
				$consecutivo_errores++;
				
				$fallo_numero_campos=true;
		     }//fin else
		     
		     
	    }//fin else numero de campos incorrecto
	    	    
	    $array_return=array();
	    $array_return["booleano"]=$validador_boolean;
	    $array_return["detalle_correccion_del_error"]=$error_linea;
	    $array_return["registro_corregido"]=$registro_corregido;
	    $array_return["fallo_sexo"]=$fallo_sexo;
	    $array_return["fallo_fecha_nacimiento"]=$fallo_fecha_nacimiento;
	    $array_return["fallo_numero_campos"]=$fallo_numero_campos;
	    return $array_return;
	}//fin funcion
	
	public function alphanumericAndSpace( $string )
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
	    return preg_replace('/[^a-zA-Z0-9\s, ;\-@.\/]/', '', $string);
	}
	
	public function alphanumericAndSpace2( $string )
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
	    return preg_replace('/[^a-zA-Z0-9\s, ;\-@.\/]/', '', $string);
	}
	
	public function alphanumericAndSpace3( $string )
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
	    $cadena = preg_replace('/[^a-zA-Z0-9\s,\-]/', '', $string);
	    $cadena = substr($cadena,0,10);
	    return $cadena;
	}
	
	public function alphanumericAndSpace4( $string )
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
	
	public function correccion_query( $string )
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
	    $cadena = preg_replace('/[^a-zA-Z0-9\s,\-\/\=\*\_\<\>\(\)\.]/', '', $string);
	    return $cadena;
	}
	
	public function correccion_caracteres_linea( $string )
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
	    $cadena = preg_replace('/[^a-zA-Z0-9\s,\-\|\.]/', '', $string);
	    return $cadena;
	}
	
	public function procesar_mensaje($mensaje)
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
		$mensaje_procesado = $this->alphanumericAndSpace($mensaje_procesado);
		
		return $mensaje_procesado;
	}
	
	public function procesar_mensaje_query($mensaje)
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
		$mensaje_procesado = $this->correccion_query($mensaje_procesado);
		
		return $mensaje_procesado;
	}
	
	function getLines($file)
	{
	    $f = fopen($file, 'rb');
	    $lines = 0;
	
	    while (!feof($f)) {
		$lines += substr_count(fread($f, 8192), "\n");
	    }
	
	    fclose($f);
	
	    return $lines;
	}
	
    public function contar_lineas_archivo($ruta_file)
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
	
    //funcion que lee el archivo y los sube a la base de datos en la tabla	
    public function CorreccionArchivo4505_verificacion_escritura($file, $nombreArchivo, $tipoArchivo, $cantArchivos) 
    {
		flush();
		
		$utilidades = new Utilidades();
		$conexionbd = new conexion();
		$conexionbd->crearConexion();
		
		$lineas_del_archivo = $this->contar_lineas_archivo($file);		
		$nombre_archivo4505=$this->nombre_archivo_4505;		
		$verificacion_inicial=true;
		
		//this is zero based so need to subtract 1 to the line you are going to search
		$myLine = 0 ; 
		$fileHandler = new SplFileObject($file);		
		$fileHandler->seek($myLine);
		//now print the line
		$linea1=$fileHandler->current();
		$array_linea1=explode("|",$linea1);
		
		
		
		//VERIFICACION INICIAL
		if(count($array_linea1)==5)
		{
			$consecutivo_de_la_linea=$this->alphanumericAndSpace2(trim($array_linea1[0]));
			$eapb_registrada=$this->alphanumericAndSpace2(trim($array_linea1[1]));
			$fecha_inicial=$this->alphanumericAndSpace2(trim($array_linea1[2]));
			$fecha_final=$this->alphanumericAndSpace2(trim($array_linea1[3]));
			$numero_lineas_indicadas_archivo=$this->alphanumericAndSpace2(trim($array_linea1[4]));
			$array_fecha_ini=explode("-",$fecha_inicial);
			$array_fecha_fin=explode("-",$fecha_final);
			$mensaje_error_vi="";
			
			if(intval($consecutivo_de_la_linea)!=0 && intval($consecutivo_de_la_linea)!=1)
			{
				$mensaje_error_vi.="El consecutivo indicado en la primera linea es incorrecto. <br>";
			}
			
			if($eapb_registrada!=$this->cod_eapb_global)
			{
				$mensaje_error_vi.="La EAPB $eapb_registrada registrada en el archivo, no corresponde con la EAPB a reportar: ".$this->cod_eapb_global.". <br>";
			}
			
			$fechas_correctas=true;
			
			if(count($array_fecha_ini)!=3
			   || !(ctype_digit($array_fecha_ini[0]) && ctype_digit($array_fecha_ini[1]) && ctype_digit($array_fecha_ini[2]) )			  
			   )
			{
				$mensaje_error_vi.="La fecha de inicio, no cumple con el formato exigido. <br>";
				$fechas_correctas=false;
			}
			else if(!checkdate($array_fecha_ini[1],$array_fecha_ini[2],$array_fecha_ini[0]) )//checkdate(month,day,year)
			{
				$mensaje_error_vi.="La fecha de inicio, no existe en el calendario. <br>";
				$fechas_correctas=false;
			}
			
			if(count($array_fecha_fin)!=3 || !(ctype_digit($array_fecha_fin[0]) && ctype_digit($array_fecha_fin[1]) && ctype_digit($array_fecha_fin[2]) )  )
			{
				$mensaje_error_vi.="La fecha de fin del reporte(corte) indicada en el archivo subido, no cumple con el formato exigido. <br>";
				$fechas_correctas=false;
			}
			else if(!checkdate($array_fecha_fin[1],$array_fecha_fin[2],$array_fecha_fin[0]) )//checkdate(month,day,year)
			{
				$mensaje_error_vi.="La fecha de fin del reporte(corte) indicada en el archivo subido, no existe en el calendario. <br>";
				$fechas_correctas=false;
			}
			
			$array_fecha_corte_indicada=explode("-",$this->cadena_fecha_corte);
			
			if(count($array_fecha_corte_indicada)!=3 || !(ctype_digit($array_fecha_corte_indicada[0]) && ctype_digit($array_fecha_corte_indicada[1]) && ctype_digit($array_fecha_corte_indicada[2]) )  )
			{
				$mensaje_error_vi.="La fecha de fin del reporte(corte) indicada en la interfaz, no cumple con el formato exigido. <br>";
				$fechas_correctas=false;
			}
			else if(!checkdate($array_fecha_corte_indicada[1],$array_fecha_corte_indicada[2],$array_fecha_corte_indicada[0]) )//checkdate(month,day,year)
			{
				$mensaje_error_vi.="La fecha de fin del reporte(corte) indicada en la interfaz, no existe en el calendario. <br>";
				$fechas_correctas=false;
			}
			
			if($fechas_correctas)
			{
				//verificacion fecha de corte diferentes
				$fecha_final_format=new DateTime($fecha_final);
				$fecha_corte_vi_format=new DateTime($this->cadena_fecha_corte);
				
				$interval = date_diff($fecha_final_format,$fecha_corte_vi_format);
				$verificador_vi_final_corte= (float)$interval->format("%r%a");
				
				
				if($verificador_vi_final_corte>=1 || $verificador_vi_final_corte<=-1)
				{
					$mensaje_error_vi.="La fecha final del periodo indicada ".$this->cadena_fecha_corte." no corresponde a la fecha de final del periodo del archivo $fecha_final  reportado. <br>";
				}
				//fin verificacion fecha de corte diferentes
				
				
				
				//verificacion fecha de corte debe ser mayor a fecha inicio
				$fecha_inicial_format=new DateTime($fecha_inicial);
				$fecha_final_format=new DateTime($fecha_final);				
				
				$interval = date_diff($fecha_inicial_format,$fecha_final_format);
				$verificador_vi_inicial_final= (float)$interval->format("%r%a");
				
				
				if($verificador_vi_inicial_final<=0)
				{
					$mensaje_error_vi.="La fecha de final(corte) $fecha_final es menor o igual que la fecha inicial $fecha_inicial. <br>";
				}
				//fin verificacion fecha de corte debe ser mayor a fecha inicio
				
				$resultados_consulta_periodo_informacion_4505=array();
				if($this->tipo_periodo_tiempo_global=="trimestral")
				{
				    $consultar_periodo_informacion_4505="";
				    $consultar_periodo_informacion_4505.=" SELECT * FROM gioss_periodo_informacion WHERE cod_periodo_informacion='".$this->codigo_periodo."'; ";
				    $resultados_consulta_periodo_informacion_4505=$conexionbd->consultar2_no_crea_cierra($consultar_periodo_informacion_4505);
				}
				else if($this->tipo_periodo_tiempo_global=="mensual")
				{
				    $consultar_periodo_informacion_4505="";
				    $consultar_periodo_informacion_4505.=" SELECT * FROM gioss_periodo_informacion_4505_mensual WHERE cod_periodo_informacion='".$this->codigo_periodo."'; ";
				    $resultados_consulta_periodo_informacion_4505=$conexionbd->consultar2_no_crea_cierra($consultar_periodo_informacion_4505);
				}
				
				
				if(count($resultados_consulta_periodo_informacion_4505)>0
				   && $verificador_vi_inicial_final>0
				   && is_array($resultados_consulta_periodo_informacion_4505))
				{				    
				    $fecha_inicio_periodo_bd=$resultados_consulta_periodo_informacion_4505[0]["fec_inicio_periodo"];
				    $array_fecha_inicio_periodo_bd=explode("-",$fecha_inicio_periodo_bd);
				    $year=explode("-",$fecha_inicial)[0];
				    $fecha_inicio_periodo_bd=$year."-".$array_fecha_inicio_periodo_bd[1]."-".$array_fecha_inicio_periodo_bd[2];
				    
				    $fecha_inicial_format=new DateTime($fecha_inicial);
				    $fecha_inicial_periodo_bd_format=new DateTime($fecha_inicio_periodo_bd);				
				    
				    $interval = date_diff($fecha_inicial_format,$fecha_inicial_periodo_bd_format);
				    $verificador_vi_inicial_inicial_bd= (float)$interval->format("%r%a");
				    
				    if($verificador_vi_inicial_inicial_bd>=1 || $verificador_vi_inicial_inicial_bd<=-1)
				    {
					    $mensaje_error_vi.="La fecha inicial del periodo indicada $fecha_inicial no corresponde a la fecha de inicio del periodo del archivo $fecha_inicio_periodo_bd  reportado. <br>";
				    }
				}//fin if verificacion fecha inicial con fecha inicial del periodo
				
			}//fin if si las fechas estaban con el formato correcto y existian en el calendario
			
			$lineas_Archivo_menos_uno=$lineas_del_archivo-1;
			//se sustrae la linea inicial
			if(intval($numero_lineas_indicadas_archivo)!=$lineas_Archivo_menos_uno)
			{
				$mensaje_error_vi.="El numero de lineas indicadas por el registro: $numero_lineas_indicadas_archivo, no corresponde con el numero de lineas que posee actualmente el archivo: $lineas_Archivo_menos_uno. <br>";
			}
			
			if($mensaje_error_vi!="")
			{
				$verificacion_inicial=false;
				$mensaje ="";
				$mensaje .= $mensaje_error_vi;
				
				if(connection_aborted()==false)
				{
				echo "<script>document.getElementById('div_mensaje_error').style.display='inline';</script>";
				echo "<script>document.getElementById('parrafo_error').innerHTML='$mensaje';</script>";
				
				ob_flush();
				flush();
				}
			}
			
		}
		else
		{
			$verificacion_inicial=false;
			$mensaje ="";
			$mensaje .= "El archivo no contiene los campos adecuados para la linea de verificacion inicial. <br>";
			
			if(connection_aborted()==false)
			{
			    echo "<script>document.getElementById('div_mensaje_error').style.display='inline';</script>";
			    echo "<script>document.getElementById('parrafo_error').innerHTML='$mensaje';</script>";
			    
			    ob_flush();
			    flush();
			}
		}
		//cierra el manejador SplFileObject con null
		$fileHandler = null;
		
		$this->verificacion_inicial_global=$verificacion_inicial;
		//FIN VERIFICACION INICIAL
		
		
		//IF SI LA VERIFICACION DEL LA LINEA INICIAL FUE CORRECTA
		if($verificacion_inicial)
		{   
		    $mensaje_advertencia_tiempo="";
		    $mensaje_advertencia_tiempo .="Estimado usuario, se ha iniciado el proceso de reparación del archivo,<br> lo que puede tomar varios minutos, dependiendo del volumen de registros.<br>";
		    $mensaje_advertencia_tiempo .="Una vez reparado, se generara el archivo  de reparacion y un archivo con los detalles de la reparacion,";
		    $mensaje_advertencia_tiempo .="El cual se enviará a su Correo electrónico o puede descargarlo directamente del aplicativo.<br>";
		    
		    if(connection_aborted()==false)
		    {
			$html_advertencia="";
			$html_advertencia.= "<script>";
			$html_advertencia.= "document.getElementById('advertencia').innerHTML='$mensaje_advertencia_tiempo';";
			$html_advertencia.= "document.title='R PyP $nombre_archivo4505.';";
			$html_advertencia.= "</script>";
			echo $html_advertencia;
			ob_flush();
			flush();
		    }
		    
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
		    $html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
		    $html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
		    $html_del_mensaje.="</td>";
		    */
		    $html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
		    $html_del_mensaje.="<div id=\'estado_validacion\'></div><div id=\'errores_bd_div\'></div>";
		    $html_del_mensaje.="</td>";
		    $html_del_mensaje.="</tr>";
		    $html_del_mensaje.="</table>";
		    
		    if(connection_aborted()==false)
		    {
			echo "<script>";
			//echo "document.getElementById('tituloVentana').innerHTML='Validando...';";
			echo "document.getElementById('mensaje').innerHTML='$html_del_mensaje';";
			//echo "$('#myModal').modal('toggle');";
			echo "</script>";			
			
			ob_flush();
			flush();
		    }
		    
		    $error_de_base_de_datos="";
		    
		    date_default_timezone_set ("America/Bogota");
		    $fecha_actual = date('Y-m-d');
		    $tiempo_actual = date('H:i:s');
		    $fecha_para_archivo= date('Y-m-d-H-i-s');
		    
		    $fecha_y_hora_para_view=str_replace(":","",$tiempo_actual).str_replace("-","",$fecha_actual);
		    $fecha_y_hora_para_view=substr($fecha_y_hora_para_view,0,4);
		    
		    $tipo_id=$_SESSION['tipo_id'];
		    $identificacion=$_SESSION['identificacion'];		    
		    $nick_user=$_SESSION['usuario'];
		    
		    $mensaje_perm_estado="";
		    $mensaje_perm_estado_reg_dupl="";
		    $mensaje_perm_estado_reg_recuperados="";
		    
		    //PARTE ESCRIBE ARCHIVOS
		
		    $archivo_a_verificar = fopen($file, 'r') or exit("No se pudo abrir el archivo");
		    
		    
		    
		    
		    $ruta_temporales="../TEMPORALES/";
		    $nombre_archivo_sin_extension=explode(".",$nombre_archivo4505)[0];
		    
		    //DIRECTORIO DE LOS ARCHIVOS
		    if(!file_exists($ruta_temporales.$nombre_archivo_sin_extension.$fecha_para_archivo))
		    {
			    mkdir($ruta_temporales.$nombre_archivo_sin_extension.$fecha_para_archivo, 0777);
		    }
		    else
		    {
			    $files_to_erase = glob($ruta_temporales.$nombre_archivo_sin_extension.$fecha_para_archivo."/*"); // get all file names
			    foreach($files_to_erase as $file_to_be_erased)
			    { // iterate files
			      if(is_file($file_to_be_erased))
			      {
				unlink($file_to_be_erased); // delete file
			      }
			    }
		    }
		    $ruta_temporales=$ruta_temporales.$nombre_archivo_sin_extension.$fecha_para_archivo."/";
		    //FIN DIRECTORIO DE LOS ARCHIVOS
		    
		    $query_insert_esta_siendo_procesado="";
		    $query_insert_esta_siendo_procesado.=" INSERT INTO gioss_4505_esta_reparando_ar_actualmente ";
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
		    if($this->cod_registro_especial_pss=="AGRUP_EAPB")
		    {
			$query_insert_esta_siendo_procesado.=" '".$this->cod_eapb_global."',  ";
		    }
		    else
		    {
			$query_insert_esta_siendo_procesado.=" '".$this->cod_registro_especial_pss."',  ";
		    }
		    $query_insert_esta_siendo_procesado.=" '".$nombre_archivo4505."',  ";
		    $query_insert_esta_siendo_procesado.=" '".$this->cadena_fecha_corte."',  ";
		    $query_insert_esta_siendo_procesado.=" '".$fecha_actual."',  ";
		    $query_insert_esta_siendo_procesado.=" '".$tiempo_actual."',  ";
		    $query_insert_esta_siendo_procesado.=" '".$nick_user."',  ";
		    $query_insert_esta_siendo_procesado.=" 'SI',  ";
		    $query_insert_esta_siendo_procesado.=" 'NO',  ";
		    $query_insert_esta_siendo_procesado.=" 'inicio el proceso'  ";
		    $query_insert_esta_siendo_procesado.=" ) ";
		    $query_insert_esta_siendo_procesado.=" ; ";
		    $error_bd="";
		    $bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($query_insert_esta_siendo_procesado, $error_bd);
		    if($error_bd!="")
		    {
			    if(connection_aborted()==false)
			    {
				    echo "<script>alert('error al iniciar el estado actual de validacion en tiempo real  4505 ');</script>";
			    }
		    }
		    
		    
		    //CREACION DEL ARCHIVO DE CAMBIOS REALIZADOS
		    $ruta_cambios_realizados_campos=$ruta_temporales.$nombre_archivo_sin_extension."_cambios_realizados_".$fecha_para_archivo.".txt";		    
		    //se remplaza el archivo si ya existe con modo w		
		    $file_cambios_realizados_registro = fopen($ruta_cambios_realizados_campos, "w") or die("fallo la creacion del archivo");
		    fwrite($file_cambios_realizados_registro, "LOG CAMBIOS CORRECCION CAMPOS TODOS LOS REGISTROS");
		    fclose($file_cambios_realizados_registro);		    
		    //FIN CREACION DEL ARCHIVO DE CAMBIOS REALIZADOS
		    
		    //CREACION DEL ARCHIVO DE CAMBIOS REALIZADOS 2
		    $ruta_cambios_real_dupl_campos_2=$ruta_temporales.$nombre_archivo_sin_extension."_cambios_real2_dupl_".$fecha_para_archivo.".txt";		    
		    //se remplaza el archivo si ya existe con modo w		
		    $file_cambios_real_dupl_registro_2 = fopen($ruta_cambios_real_dupl_campos_2, "w") or die("fallo la creacion del archivo");
		    fwrite($file_cambios_real_dupl_registro_2, "LOG CAMBIOS CORRECCION CAMPOS UNIFICADOS DE DUPLICADOS");
		    fclose($file_cambios_real_dupl_registro_2);		    
		    //FIN CREACION DEL ARCHIVO DE CAMBIOS REALIZADOS 2
		    
		    
		    //CREACION DEL ARCHIVO DE CAMBIOS PARA DUPLICADOS
		    $ruta_cambios_duplicados_campos=$ruta_temporales.$nombre_archivo_sin_extension."_crpdupl_".$fecha_para_archivo.".txt";		    
		    //se remplaza el archivo si ya existe con modo w		
		    $file_cambios_duplicados_registro = fopen($ruta_cambios_duplicados_campos, "w") or die("fallo la creacion del archivo");		    
		    fclose($file_cambios_duplicados_registro);		    
		    //FIN CREACION DEL ARCHIVO DE CAMBIOS PARA DUPLICADOS
		    
		    
		    //CREACION DEL ARCHIVO DE DETALLES CORRECCION
		    $ruta_detalles_correccion_campos=$ruta_temporales.$nombre_archivo_sin_extension."_detalles_correccion_".$fecha_para_archivo.".csv";		    
		    //se remplaza el archivo si ya existe con modo w		
		    $file_detalles_correccion_registro = fopen($ruta_detalles_correccion_campos, "w") or die("fallo la creacion del archivo");		    
		    $bool_hubo_errores_en_la_correccion=false;			    
		    fclose($file_detalles_correccion_registro);		    
		    //FIN CREACION DEL ARCHIVO DE DETALLES CORRECCION
		    
		    //CREACION DEL ARCHIVO QUE CONTENDRA LOS REGISTROS CORREGIDOS
		    $ruta_archivo4505_corregido=$ruta_temporales.$nombre_archivo_sin_extension."_con_dupl.txt";		    
		    $file_archivo4505_corregido=fopen($ruta_archivo4505_corregido, "w") or die("fallo la creacion del archivo");
		    //parte primera linea
		    $linea_1_procesada= str_replace("\n","",preg_replace('/[^a-zA-Z0-9\s,\-\|]/', '', $linea1));
		    $array_linea_procesada=explode("|",$linea_1_procesada);
		    fwrite($file_archivo4505_corregido, $linea_1_procesada." ");
		    //fin part primear linea
		    fclose($file_archivo4505_corregido);
		    //FIN CREACION DEL ARCHIVO QUE CONTENDRA LOS REGISTROS CORREGIDOS
		    
		    //CREACION DEL ARCHIVO QUE CONTENDRA LOS REGISTROS CORREGIDOS SIN DUPLICADOS		    
		    mkdir($ruta_temporales."sin_duplicados", 0700);		    
		    $ruta_archivo4505_corregido_sin_duplicados=$ruta_temporales."sin_duplicados/".$nombre_archivo_sin_extension.".txt";		    
		    $file_archivo4505_corregido_sin_duplicados=fopen($ruta_archivo4505_corregido_sin_duplicados, "w") or die("fallo la creacion del archivo");
		    
		    fclose($file_archivo4505_corregido_sin_duplicados);
		    //FIN CREACION DEL ARCHIVO QUE CONTENDRA LOS REGISTROS CORREGIDOS SIN DUPLICADOS
		    
		    //CREACION ARCHIVO PARA REGISTROS CON FECHA NACIMIENTO INVALIDA FORMATO O INFERIOR A 1900-12-31
		    $this->ruta_archivo_fecha_nacimiento_invalida=$ruta_temporales."registros_con_fecha_invalida.txt";
		    $file_registros_con_fecha_invalida=fopen($this->ruta_archivo_fecha_nacimiento_invalida, "w") or die("fallo la creacion del archivo");
		    fwrite($file_registros_con_fecha_invalida, "Archivo Registros Con Fecha De Nacimiento Invalida Por Formato");
		    fclose($file_registros_con_fecha_invalida);
		    //FIN CREACION ARCHIVO PARA REGISTROS CON FECHA NACIMIENTO INVALIDA FORMATO O INFERIOR A 1900-12-31
					    
		    //CREACION ARCHIVO PARA AFILIADOS INEXISTENTES
		    $this->ruta_archivo_afiliado_no_existe=$ruta_temporales."registros_no_son_afiliados.txt";
		    $file_registros_afiliado_no_existe=fopen($this->ruta_archivo_afiliado_no_existe, "w") or die("fallo la creacion del archivo");
		    fwrite($file_registros_afiliado_no_existe, "Archivo Registros Con Afiliados Inexistentes En La Base De Datos");
		    fclose($file_registros_afiliado_no_existe);
		    //FIN CREACION ARCHIVO PARA AFILIADOS INEXISTENTES
		    
		    $this->global_ruta_temporales=$ruta_temporales;
		    
		    $mensajes_error_bd="";
		    
		    
		    
		    $se_realizo_correccion_a_campos_con_errores_del_archivo=false;
		    $cont_linea=0;
		    $consecutivo_errores=1;
		    $fue_cerrada_la_gui=false;
		    $acumulador_para_contar_duplicados=0;
		    $personas_con_duplicados_hasta_el_momento=0;
		    $personas_insertadas_hasta_el_momento=0;
		    $cont_porcentaje=0;
		    $cont_porcentaje_dupl=0;
		    $cont_porcentaje_csv=0;

		    $cont_linea_index_dupl=1;//debido a que no cuenta la linea de identificacion de 4505
		    
		    $numero_lineas_campos_incorrectos=0;
		    while (!feof($archivo_a_verificar)) 
		    {
			    if($fue_cerrada_la_gui==false)
			    {
				if(connection_aborted()==true)
				{
				    $fue_cerrada_la_gui=true;
				}
			    }//fin if verifica si el usuario cerro la pantalla
			    
			    //CANCELA EJECUCION DEL ARCHIVO			    
			    $verificar_si_ejecucion_fue_cancelada="";
			    $verificar_si_ejecucion_fue_cancelada.=" SELECT esta_ejecutando FROM gioss_4505_esta_reparando_ar_actualmente ";
			    $verificar_si_ejecucion_fue_cancelada.=" WHERE fecha_corte_archivo_en_reparacion='".$this->cadena_fecha_corte."' ";
			    if($this->cod_registro_especial_pss=="AGRUP_EAPB")
			    {
				$verificar_si_ejecucion_fue_cancelada.=" AND codigo_entidad_reportadora='".$this->cod_eapb_global."' ";
			    }
			    else
			    {
				$verificar_si_ejecucion_fue_cancelada.=" AND codigo_entidad_reportadora='".$this->cod_registro_especial_pss."' ";
			    }		    
			    $verificar_si_ejecucion_fue_cancelada.=" AND nombre_archivo='".$nombre_archivo4505."'  ";
			    $verificar_si_ejecucion_fue_cancelada.=" AND nick_usuario='".$nick_user."'  ";
			    $verificar_si_ejecucion_fue_cancelada.=" AND fecha_validacion='".$fecha_actual."'  ";
			    $verificar_si_ejecucion_fue_cancelada.=" AND hora_validacion='".$tiempo_actual."'  ";
			    $verificar_si_ejecucion_fue_cancelada.=" ; ";
			    $error_bd="";
			    $resultados_si_ejecucion_fue_cancelada=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($verificar_si_ejecucion_fue_cancelada, $error_bd);
			    if($error_bd!="")
			    {
				    if($fue_cerrada_la_gui==false)
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
			    
			    //obtiene la linea actual
			    $linea = fgets($archivo_a_verificar);
			    
			    //parte array campos pre correccion para log cambios
			    $array_campos_pre_correccion=array();
			    $array_campos_pre_correccion=explode("|",$linea);
			    //fin parte array campos pre correccion para log cambios
			   
			    $array_resultados_correcciones=array();
			    $array_resultados_correcciones= $this->correccion_errores_campos_PyP_4505($linea,$cont_linea,$consecutivo_errores,$conexionbd);
			    
			    $detalles_correciones_campos="";
			    $se_hizo_correccion=false;
			    $registro_archivo4505_corregido="";
			    
			    $fallo_sexo=false;
			    $fallo_fecha_nacimiento=false;
			    $fallo_numero_campos=false;
			    
			    $detalles_correciones_campos = $array_resultados_correcciones["detalle_correccion_del_error"];
			    $se_hizo_correccion= $array_resultados_correcciones["booleano"];
			    $registro_archivo4505_corregido= $array_resultados_correcciones["registro_corregido"];
			    
			    $fallo_sexo=$array_resultados_correcciones["fallo_sexo"];
			    $fallo_fecha_nacimiento=$array_resultados_correcciones["fallo_fecha_nacimiento"];
			    $fallo_numero_campos=$array_resultados_correcciones["fallo_numero_campos"];
			    
			    //parte array campos pos correccion para log cambios
			    $array_campos_pos_correccion=array();
			    $array_campos_pos_correccion=explode("|",$registro_archivo4505_corregido);
			    //fin parte array campos pre correccion para log cambios
			    			   			    
			    //PARTE ESCRIBE LOG CAMBIOS CORRECCION
			    if($fallo_sexo==false
			       && $fallo_fecha_nacimiento==false
			       && $fallo_numero_campos==false
			       && (
				       (count($array_campos_pre_correccion)==119
			       && count($array_campos_pos_correccion)==119
				       && $this->tipo_entidad_que_efectua_el_cargue!="agrupado_ips120" // especifica que es diferente
				       )//fin p1
				       || (count($array_campos_pre_correccion)==120 
				       	&& count($array_campos_pos_correccion)==120 
				       	&& $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120"
				       	)//fin p2
				       )//fin comparacion cantidad campos
			       )//fin condicion
			    {
				//se abre con modo a para que adicione que no subio
				
				$file_cambios_realizados_registro = fopen($ruta_cambios_realizados_campos, "a") or die("fallo la creacion del archivo");
				
				$cont_log_cambios=0;
				while($cont_log_cambios<119 )// ya que pre_correccion y poscorreccion deben ser iguales no hay problema
				{
				    if(trim($array_campos_pre_correccion[$cont_log_cambios])!=trim($array_campos_pos_correccion[$cont_log_cambios]))
				    {
					$linea_log_cambos_realizados_correccion="";
					$linea_log_cambos_realizados_correccion.="El registro original ".trim($array_campos_pre_correccion[1])." ";
					$linea_log_cambos_realizados_correccion.=" convertido en el registro ".trim($array_campos_pos_correccion[1])." ";
					$linea_log_cambos_realizados_correccion.=" reparo el campo numero $cont_log_cambios ";
					$linea_log_cambos_realizados_correccion.=" con un valor inicial de ".trim($array_campos_pre_correccion[$cont_log_cambios]);
					$linea_log_cambos_realizados_correccion.=" transformado en ";
					$linea_log_cambos_realizados_correccion.=" el valor final de ".trim($array_campos_pos_correccion[$cont_log_cambios]);
					$linea_log_cambos_realizados_correccion.=" de acuerdo a los criterios de correccion ";
					$linea_log_cambos_realizados_correccion.="";
					
					fwrite($file_cambios_realizados_registro, "\n".$linea_log_cambos_realizados_correccion);
				    }//fin if solo escribe si hubo cambios en el campo al corregir
				    
				    $cont_log_cambios++;
				}//fin while
				
				//cierra el archivo del log reparacion de duplicados
				fclose($file_cambios_realizados_registro);
			    }//fin if
			    //FIN PARTE ESCRIBE LOG CAMBIOS CORRECCION
			    
			    //ESCRIBIENDO ARCHIVO DE DETALLES DE CORRECCION			    
			    if($detalles_correciones_campos!="")
			    {
				    if($fue_cerrada_la_gui==false)
				    {
				    	//se comento para que no contraste con el cuadro que muestra la primera fase del proceso
						//echo "<script>document.getElementById('estado_validacion').innerHTML=\"<p id='parrafo_estado' align='left'>Escribiendo el detalle de la correccion para la linea ".$cont_linea.", de ".($lineas_del_archivo-1)." lineas.</p>\";</script>";
						ob_flush();
						flush();
				    }//fin if
				    
				    if($bool_hubo_errores_en_la_correccion==false)
				    {
						$titulos="";
						$titulos.="consecutivo,nombre archivo 4505,codigo tipo inconsistencia,tipo inconsistencia,codigo grupo inconsistencia,grupo inconsistencia,";
						$titulos.="codigo detalle inconsistencia,detalle inconsistencia, numero de linea, numero de campo";
						$file_detalles_correccion_registro = fopen($ruta_detalles_correccion_campos, "a") or die("fallo la creacion del archivo");
						fwrite($file_detalles_correccion_registro, utf8_decode($titulos) . "\n");
						fclose($file_detalles_correccion_registro);
						$bool_hubo_errores_en_la_correccion=true;
				    }//fin if
				    
				    
				    //ABRE EL ARCHIVO Y HACE EXPLODE
				    $array_errores_linea=explode("|", $detalles_correciones_campos);
				    //se abre con modo a para que adicione
				    $file_detalles_correccion_registro = fopen($ruta_detalles_correccion_campos, "a") or die("fallo la creacion del archivo");
				    
				    //se separo porque puede haber varios dettalles de error por linea
				    $cont_error=0;
				    foreach ($array_errores_linea as $error) 
				    {					   
					    fwrite($file_detalles_correccion_registro, utf8_decode($error) . "\n");
					    $cont_error++;
				    }//fin foreach
				    
				    //CIERRA EL ARCHIVO
				    fclose($file_detalles_correccion_registro);
			    }//fin if
			    //FIN ESCRIBIENDO ARCHIVO DE DETALLES DE CORRECCION
			    
			    //ESCRIBE EL REGISTRO CORREGIDO
			    if($registro_archivo4505_corregido!="" && $fallo_sexo==false && $fallo_fecha_nacimiento==false && $fallo_numero_campos==false)
			    {
				//porcentaje
				$muestra_mensaje_nuevo=false;
				$porcentaje=intval((($cont_linea)*100)/($lineas_del_archivo-1));
				if($porcentaje!=$cont_porcentaje || ($porcentaje==0 && ($cont_linea)==1) || $porcentaje==100)
				{
				 $cont_porcentaje=$porcentaje;
				 $muestra_mensaje_nuevo=true;
				}
				//fin porcentaje
				
				//ACTUALIZA ESTADO DEL ARCHIVO
				
				$mensaje_estado_registros="";
				$mensaje_estado_registros.="<table style=text-align:center;width:60%;left:25%;border-style:solid;border-width:5px; id=tabla_estado_1>";
				$mensaje_estado_registros.="<tr style=background-color:#80bfff><th colspan=2 style=text-align:center;width:60%><span style=\"color:white;text-shadow:2px 2px 8px #0000ff;\">Inicio a las $tiempo_actual del $fecha_actual para $nombre_archivo4505</span></th></tr>";
				$mensaje_estado_registros.="<tr><td style=text-align:left;width:60%>Numero de registros:</td><td style=text-align:left>".($lineas_del_archivo-1)."</td></tr>";
				$mensaje_estado_registros.="<tr style=background-color:#80bfff><td style=text-align:left;width:60%>Numero de registros corregidos:</td><td style=text-align:left>".$cont_linea."</td></tr>";
				$mensaje_estado_registros.="<tr><td style=text-align:left;width:60%>Porcentaje registros corregidos:</td><td style=text-align:left>$cont_porcentaje %</td></tr>";
				$mensaje_estado_registros.="<tr style=background-color:#80bfff><td style=text-align:left;width:60%>Numero de registros duplicados:</td><td style=text-align:left>".$acumulador_para_contar_duplicados.".</tr>";
				$mensaje_estado_registros.="<tr><td style=text-align:left;width:60%>Numero de personas unicas:</td><td style=text-align:left>".$personas_insertadas_hasta_el_momento."</td></tr>";
				$mensaje_estado_registros.="<tr style=background-color:#80bfff><td style=text-align:left;width:60%>Numero de personas con registros duplicados:</td><td style=text-align:left>".$personas_con_duplicados_hasta_el_momento."</td></tr>";
				$mensaje_estado_registros.="<tr><td style=text-align:left;width:60%>Numero de registros Excluidos</td><td style=text-align:left>".$numero_lineas_campos_incorrectos."</td></tr>";
				$mensaje_estado_registros.="<tr style=background-color:#80bfff><td style=text-align:left;width:60%>Uso de RAM:</td><td style=text-align:left>T=".memory_get_usage (true)." F=".memory_get_usage()."</td></tr>";
				$mensaje_estado_registros.="</table><br>";
				
				$mensaje_perm_estado=$mensaje_estado_registros;
				
				if($muestra_mensaje_nuevo)
				{
				    $query_update_esta_siendo_procesado="";
				    $query_update_esta_siendo_procesado.=" UPDATE gioss_4505_esta_reparando_ar_actualmente ";
				    $query_update_esta_siendo_procesado.=" SET mensaje_estado_registros='$mensaje_estado_registros' ";
				    $query_update_esta_siendo_procesado.=" WHERE fecha_corte_archivo_en_reparacion='".$this->cadena_fecha_corte."' ";
				    if($this->cod_registro_especial_pss=="AGRUP_EAPB")
				    {
					$query_update_esta_siendo_procesado.=" AND codigo_entidad_reportadora='".$this->cod_eapb_global."' ";
				    }
				    else
				    {
					$query_update_esta_siendo_procesado.=" AND codigo_entidad_reportadora='".$this->cod_registro_especial_pss."' ";
				    }		    
				    $query_update_esta_siendo_procesado.=" AND nombre_archivo='".$nombre_archivo4505."'  ";
				    $query_update_esta_siendo_procesado.=" AND nick_usuario='".$nick_user."'  ";
				    $query_update_esta_siendo_procesado.=" AND fecha_validacion='".$fecha_actual."'  ";
				    $query_update_esta_siendo_procesado.=" AND hora_validacion='".$tiempo_actual."'  ";
				    $query_update_esta_siendo_procesado.=" ; ";
				    $error_bd="";
				    $bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($query_update_esta_siendo_procesado, $error_bd);
				    if($error_bd!="")
				    {
					    if($fue_cerrada_la_gui==false)
					    {
						    echo "<script>alert('error al actualizar el estado actual de reparacion en tiempo real  4505 ');</script>";
					    }
				    }
				}//fin if $muestra_mensaje_nuevo
				//FIN ACTUALIZA ESTADO DEL ARCHIVO
				
				if($fue_cerrada_la_gui==false && $muestra_mensaje_nuevo)
				{
				    echo "<script>document.getElementById('estado_validacion').innerHTML='<p id=\"parrafo_estado\" align=\"left\">$mensaje_estado_registros</p>';</script>";
				    ob_flush();
				    flush();
				}
				
				//se abre con modo a para que adicione
				$file_archivo4505_corregido = fopen($ruta_archivo4505_corregido, "a") or die("fallo la creacion del archivo");
				
				fwrite($file_archivo4505_corregido, "\n".utf8_decode($registro_archivo4505_corregido));
				
				//CIERRA EL ARCHIVO
				fclose($file_archivo4505_corregido);
				
				//SUBIDA A BASE DE DATOS 
				$campos_del_registro_corregido=explode("|",$registro_archivo4505_corregido);
				
				//realiza trim para los datos en los campos
				$contador_trim=0;
				while($contador_trim<count($campos_del_registro_corregido))
				{
				    $campos_del_registro_corregido[$contador_trim]=trim($campos_del_registro_corregido[$contador_trim]);
				    $contador_trim++;
				}
				//fin realiza trim para los datos en los campos
				
				if( (count($campos_del_registro_corregido)==119 && $this->tipo_entidad_que_efectua_el_cargue!="agrupado_ips120" )
					|| (count($campos_del_registro_corregido)==120 && $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120")
				)//fin condicion
				{
				    //INDEXADOR DE DUPLICADOS
				    $nombre_tabla_indexador_duplicados="gioss_indexador_duplicados_del_reparador_4505";
				    if($this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120")
					{
						//susa una tabla de indexador distinta pero para duplicados teniendo en cuenta el campo extra 120 con codigo eapb
						$nombre_tabla_indexador_duplicados="gioss_indexador_dupl_del_reparador_4505_agrup_ips";
					}//fin if

					$compiladoPrimerApellidoNombreFechaNac="";
					$compiladoPrimerApellidoNombreFechaNac.=trim($campos_del_registro_corregido[5])."|".trim($campos_del_registro_corregido[6])."|".trim($campos_del_registro_corregido[9]);

				    //FASE 1 consulta por el campo 3 y 4 (tipo id y numero id afiliado) si existe duplicado
				    $existe_afiliado=false;
				    $lista_lineas_duplicados="".$cont_linea_index_dupl;
				    $query_consultar_en_indexador="";
				    $query_consultar_en_indexador.=" SELECT lista_lineas_donde_hay_duplicados FROM  ";
				    $query_consultar_en_indexador.=" $nombre_tabla_indexador_duplicados ";
				    $query_consultar_en_indexador.=" WHERE  ";
				    $query_consultar_en_indexador.="tipo_id_usuario='".$tipo_id."'";				
				    $query_consultar_en_indexador.=" AND ";
				    $query_consultar_en_indexador.="id_usuario='".$identificacion."'";
				    $query_consultar_en_indexador.=" AND ";
				    $query_consultar_en_indexador.="nick_usuario='".$nick_user."'";
				    $query_consultar_en_indexador.=" AND ";
				    $query_consultar_en_indexador.="fecha_corte_reporte='".$this->cadena_fecha_corte."'";
				    $query_consultar_en_indexador.=" AND ";
				    $query_consultar_en_indexador.="fecha_de_generacion='".$fecha_actual."'";
				    $query_consultar_en_indexador.=" AND ";
				    $query_consultar_en_indexador.="hora_generacion='".$tiempo_actual."'";
				    $query_consultar_en_indexador.=" AND ";
				    $query_consultar_en_indexador.="codigo_entidad_eapb_generadora='".$this->cod_eapb_global."'";
				    $query_consultar_en_indexador.=" AND ";
				    $query_consultar_en_indexador.="codigo_entidad_prestadora='".$this->cod_registro_especial_pss."'";
				    $query_consultar_en_indexador.=" AND ";
				    $query_consultar_en_indexador.="nombre_archivo_pyp='".$nombre_archivo_sin_extension."'";
				    $query_consultar_en_indexador.=" AND ";
				    $query_consultar_en_indexador.=" (campo_3_tipo_id='".$campos_del_registro_corregido[3]."' )";
				    $query_consultar_en_indexador.=" AND ";
				    $query_consultar_en_indexador.="campo_4_numero_id='".$campos_del_registro_corregido[4]."'";
				    if($this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120")
					{
						$query_consultar_en_indexador.=" AND ";
						$query_consultar_en_indexador.="campo_extra_120_eapb_regis='".trim($campos_del_registro_corregido[119])."'";
					}
				    $query_consultar_en_indexador.=" ; ";
				    $error_bd_seq="";		
				    $resultado_esta_afiliado_en_indexador=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_consultar_en_indexador, $error_bd_seq);
				    if($error_bd_seq!="")
				    {
					$mensajes_error_bd.=" ERROR Al consultar en la tabla $nombre_tabla_indexador_duplicados ".$this->procesar_mensaje($error_bd_seq).".<br>";
					
					if($fue_cerrada_la_gui==false)
					{
						echo "<script>alert('ERROR Al consultar en la tabla $nombre_tabla_indexador_duplicados ".$this->procesar_mensaje($error_bd_seq)."');</script>";
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
					    //echo "<script>alert('adiciona el primero $string_res_en_indexador');</script>";
					    $acumulador_para_contar_duplicados+=1;
					}
					$lista_lineas_duplicados=$resultado_esta_afiliado_en_indexador[0]["lista_lineas_donde_hay_duplicados"].";;".$cont_linea_index_dupl;
					//si haya duplicaco, suma 1
					$acumulador_para_contar_duplicados+=1;
					//echo "<script>alert('antes $string_res_en_indexador despues $lista_lineas_duplicados');</script>";
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
					$query_insert_updt_en_indexador.=" $nombre_tabla_indexador_duplicados ";				
					$query_insert_updt_en_indexador.=" ( ";	
					$query_insert_updt_en_indexador.=" tipo_id_usuario, ";
					$query_insert_updt_en_indexador.=" id_usuario, ";
					$query_insert_updt_en_indexador.=" nick_usuario, ";
					$query_insert_updt_en_indexador.=" fecha_corte_reporte, ";
					$query_insert_updt_en_indexador.=" fecha_de_generacion, ";
					$query_insert_updt_en_indexador.=" hora_generacion, ";
					$query_insert_updt_en_indexador.=" codigo_entidad_eapb_generadora, ";
					$query_insert_updt_en_indexador.=" codigo_entidad_prestadora, ";
					$query_insert_updt_en_indexador.=" nombre_archivo_pyp, ";
					$query_insert_updt_en_indexador.=" campo_3_tipo_id, ";
					$query_insert_updt_en_indexador.=" campo_4_numero_id, ";
					if($this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120")
					{
						$query_insert_updt_en_indexador.="campo_extra_120_eapb_regis, ";
					}
					$query_insert_updt_en_indexador.=" contiene_filas_coincidentes, ";
					$query_insert_updt_en_indexador.=" lista_lineas_donde_hay_duplicados ";
					$query_insert_updt_en_indexador.=" ) ";
					$query_insert_updt_en_indexador.=" VALUES ";
					$query_insert_updt_en_indexador.=" ( ";
					$query_insert_updt_en_indexador.="'".$tipo_id."',";
					$query_insert_updt_en_indexador.="'".$identificacion."',";
					$query_insert_updt_en_indexador.="'".$nick_user."',";							
					$query_insert_updt_en_indexador.="'".$this->cadena_fecha_corte."',";
					$query_insert_updt_en_indexador.="'".$fecha_actual."',";
					$query_insert_updt_en_indexador.="'".$tiempo_actual."',";
					$query_insert_updt_en_indexador.="'".$this->cod_eapb_global."',";
					$query_insert_updt_en_indexador.="'".$this->cod_registro_especial_pss."',";
					$query_insert_updt_en_indexador.="'".$nombre_archivo_sin_extension."',";
					$query_insert_updt_en_indexador.="'".$campos_del_registro_corregido[3]."',";
					$query_insert_updt_en_indexador.="'".$campos_del_registro_corregido[4]."',";
					if($this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120")
					{
						$query_insert_updt_en_indexador.="'".trim($campos_del_registro_corregido[119])."',";
					}
					$query_insert_updt_en_indexador.="'NO',";
					$query_insert_updt_en_indexador.="'".$cont_linea_index_dupl."'";
					$query_insert_updt_en_indexador.=" ) ";
					$query_insert_updt_en_indexador.=" ; ";
					$error_bd_seq="";		
					$bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($query_insert_updt_en_indexador, $error_bd_seq);
					if($error_bd_seq!="")
					{
					    $mensajes_error_bd.=" ERROR Al subir en la tabla $nombre_tabla_indexador_duplicados ".$this->procesar_mensaje($error_bd_seq).".<br>";
					    
					    if($fue_cerrada_la_gui==false)
					    {
						    echo "<script>alert('ERROR Al subir en la tabla $nombre_tabla_indexador_duplicados  ".$this->procesar_mensaje($error_bd_seq)."');</script>";
					    }
					}
					else
					{
					    $personas_insertadas_hasta_el_momento+=1;
					}


					
					//sube a corregidos_sin_duplicados_pyp4505
					$query_subir_registro_corregido="";
					$query_subir_registro_corregido.=" INSERT INTO ";
					$query_subir_registro_corregido.=" corregidos_sin_duplicados_pyp4505 ";				
					$query_subir_registro_corregido.=" ( ";				
					$numero_actual_campo_registro_corregido=0;
					while($numero_actual_campo_registro_corregido<=118)
					{
						$query_subir_registro_corregido.=" campo_".$numero_actual_campo_registro_corregido." , ";
						$numero_actual_campo_registro_corregido++;
					}//fin while para nombres columnas de bd correspondientes a los campos de 4505 a insertar
					if($this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120")
				    {
				    	$query_subir_registro_corregido.="campo_extra_120_eapb_regis,";
					}//fin if
					$query_subir_registro_corregido.=" tipo_id_usuario, ";
					$query_subir_registro_corregido.=" id_usuario, ";
					$query_subir_registro_corregido.=" nick_usuario, ";
					$query_subir_registro_corregido.=" numero_registro, ";
					$query_subir_registro_corregido.=" fecha_corte_reporte, ";
					$query_subir_registro_corregido.=" fecha_de_generacion, ";
					$query_subir_registro_corregido.=" hora_generacion, ";
					$query_subir_registro_corregido.=" codigo_entidad_eapb_generadora, ";
					$query_subir_registro_corregido.=" codigo_entidad_prestadora, ";
					$query_subir_registro_corregido.=" nombre_archivo_pyp ";
					$query_subir_registro_corregido.=" ) ";
					$query_subir_registro_corregido.=" VALUES ";
					$query_subir_registro_corregido.=" ( ";				
					$numero_actual_campo_registro_corregido=0;
					while($numero_actual_campo_registro_corregido<=118)
					{
						$query_subir_registro_corregido.="'".$campos_del_registro_corregido[$numero_actual_campo_registro_corregido]."',";
						$numero_actual_campo_registro_corregido++;
					}//fin while con los valores de los campos 4505 a insertar en la tabla
					if($this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120")
				    {
				    	$query_subir_registro_corregido.="'".$campos_del_registro_corregido[119]."',";
					}//fin if
					$query_subir_registro_corregido.="'".$tipo_id."',";
					$query_subir_registro_corregido.="'".$identificacion."',";
					$query_subir_registro_corregido.="'".$nick_user."',";	
					$query_subir_registro_corregido.="'".$this->consecutivo_fixer."',";							
					$query_subir_registro_corregido.="'".$this->cadena_fecha_corte."',";
					$query_subir_registro_corregido.="'".$fecha_actual."',";
					$query_subir_registro_corregido.="'".$tiempo_actual."',";
					$query_subir_registro_corregido.="'".$this->cod_eapb_global."',";
					$query_subir_registro_corregido.="'".$this->cod_registro_especial_pss."',";
					$query_subir_registro_corregido.="'".$nombre_archivo_sin_extension."'";
					$query_subir_registro_corregido.=" ) ";
					$query_subir_registro_corregido.=" ; ";
					$error_bd_seq="";		
					$bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($query_subir_registro_corregido, $error_bd_seq);
					if($error_bd_seq!="")
					{
						$mensajes_error_bd.=" ERROR Al subir en la tabla temporal de registros corregidos pre correccion duplicados  para corrector ".$this->procesar_mensaje($error_bd_seq).".<br>";
						
					}
					//fin sube a corregidos_sin_duplicados_pyp4505
					
				    }//fin if
				    //o actualiza si ya habia concatenando a la lista de numero de filas
				    else if($existe_afiliado==true)
				    {
					$array_check_tiene_2_filas_coincidentes=explode(";;",$lista_lineas_duplicados);
					
					//borra el que estaba en corregidos_sin_duplicados_pyp4505
					//entrea si el nuemro de filas es igual a dos
					if(count($array_check_tiene_2_filas_coincidentes)==2)
					{
					    //BORRANDO el afiliado duplicado de corregidos_sin_duplicados_pyp4505
					    $sql_delete_corregidos_temp="";
					    $sql_delete_corregidos_temp.=" DELETE FROM corregidos_sin_duplicados_pyp4505  ";
					    $sql_delete_corregidos_temp.=" WHERE fecha_de_generacion='$fecha_actual'  AND hora_generacion='$tiempo_actual' ";
					    $sql_delete_corregidos_temp.=" AND ";
					    $sql_delete_corregidos_temp.=" fecha_corte_reporte='".$this->cadena_fecha_corte."'  ";
					    $sql_delete_corregidos_temp.=" AND ";
					    $sql_delete_corregidos_temp.=" codigo_entidad_prestadora='".$this->cod_registro_especial_pss."'  ";
					    $sql_delete_corregidos_temp.=" AND ";
					    $sql_delete_corregidos_temp.=" codigo_entidad_eapb_generadora='".$this->cod_eapb_global."'  ";
					    $sql_delete_corregidos_temp.=" AND ";
					    $sql_delete_corregidos_temp.=" nombre_archivo_pyp='".$nombre_archivo_sin_extension."'  ";
					    $sql_delete_corregidos_temp.=" AND ";
					    $sql_delete_corregidos_temp.=" tipo_id_usuario='$tipo_id' ";
					    $sql_delete_corregidos_temp.=" AND ";
					    $sql_delete_corregidos_temp.=" id_usuario='$identificacion' ";
					    $sql_delete_corregidos_temp.=" AND ";
					    $sql_delete_corregidos_temp.=" nick_usuario='$nick_user' ";
					    $sql_delete_corregidos_temp.=" AND ";
					    $sql_delete_corregidos_temp.=" campo_3='".$campos_del_registro_corregido[3]."' ";
					    $sql_delete_corregidos_temp.=" AND ";
					    $sql_delete_corregidos_temp.=" campo_4='".$campos_del_registro_corregido[4]."' ";
					    if($this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120")
					    {					    	
						    $sql_delete_corregidos_temp.=" AND ";
						    $sql_delete_corregidos_temp.=" campo_extra_120_eapb_regis='".$campos_del_registro_corregido[119]."' ";
						}//fin if
					    $sql_delete_corregidos_temp.=" ; ";
					    $error_bd_seq="";		
					    $bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($sql_delete_corregidos_temp, $error_bd_seq);
					    if($error_bd_seq!="")
					    {
						    $mensajes_error_bd.=" ERROR Al eliminar en la tabla corregidos_sin_duplicados_pyp4505 ".$this->procesar_mensaje($error_bd_seq).".<br>";
						    
					    }
					    //FIN BORRANDO el afiliado duplicado de corregidos_sin_duplicados_pyp4505
					}
					//fin borra el que estaba en corregidos_sin_duplicados_pyp4505
					
					$query_insert_updt_en_indexador="";
					$query_insert_updt_en_indexador.=" UPDATE  ";
					$query_insert_updt_en_indexador.=" $nombre_tabla_indexador_duplicados ";				
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
					$query_insert_updt_en_indexador.="fecha_corte_reporte='".$this->cadena_fecha_corte."'";
					$query_insert_updt_en_indexador.=" AND ";
					$query_insert_updt_en_indexador.="fecha_de_generacion='".$fecha_actual."'";
					$query_insert_updt_en_indexador.=" AND ";
					$query_insert_updt_en_indexador.="hora_generacion='".$tiempo_actual."'";
					$query_insert_updt_en_indexador.=" AND ";
					$query_insert_updt_en_indexador.="codigo_entidad_eapb_generadora='".$this->cod_eapb_global."'";
					$query_insert_updt_en_indexador.=" AND ";
					$query_insert_updt_en_indexador.="codigo_entidad_prestadora='".$this->cod_registro_especial_pss."'";
					$query_insert_updt_en_indexador.=" AND ";
					$query_insert_updt_en_indexador.="nombre_archivo_pyp='".$nombre_archivo_sin_extension."'";
					$query_insert_updt_en_indexador.=" AND ";
					$query_insert_updt_en_indexador.=" (campo_3_tipo_id='".$campos_del_registro_corregido[3]."' )";
					$query_insert_updt_en_indexador.=" AND ";
					$query_insert_updt_en_indexador.="campo_4_numero_id='".$campos_del_registro_corregido[4]."'";
					if($this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120")
					{
						$query_insert_updt_en_indexador.=" AND ";
						$query_insert_updt_en_indexador.="campo_extra_120_eapb_regis='".trim($campos_del_registro_corregido[119])."'";
					}
					$query_insert_updt_en_indexador.=" ; ";
					$error_bd_seq="";		
					$bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($query_insert_updt_en_indexador, $error_bd_seq);
					if($error_bd_seq!="")
					{
					    $mensajes_error_bd.=" ERROR Al actualizar en la tabla $nombre_tabla_indexador_duplicados ".$this->procesar_mensaje($error_bd_seq).".<br>";
					    
					    if($fue_cerrada_la_gui==false)
					    {
						    echo "<script>alert('ERROR Al actualizar en la tabla $nombre_tabla_indexador_duplicados  ".$this->procesar_mensaje($error_bd_seq)."');</script>";
					    }
					}
					
					if(count($array_check_tiene_2_filas_coincidentes)==2)
					{
					    $personas_con_duplicados_hasta_el_momento+=1;
					}
				    }//fin if actualizar
				    //FIN FASE 2
				    //FIN INDEXADOR DE DUPLICADOS
				    
				    //sube a corregidos_con_duplicados_pyp4505
				    /*
				    $query_subir_registro_corregido="";
				    $query_subir_registro_corregido.=" INSERT INTO ";
				    $query_subir_registro_corregido.=" corregidos_con_duplicados_pyp4505 ";				
				    $query_subir_registro_corregido.=" ( ";				
				    $numero_actual_campo_registro_corregido=0;
				    while($numero_actual_campo_registro_corregido<=118)
				    {
					    $query_subir_registro_corregido.=" campo_".$numero_actual_campo_registro_corregido." , ";
					    $numero_actual_campo_registro_corregido++;
				    }//fin while para nombres columnas de bd correspondientes a los campos de 4505 a insertar
				    if($this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120")
				    {
				    	$query_subir_registro_corregido.="campo_extra_120_eapb_regis,";
					}//fin if
				    $query_subir_registro_corregido.=" tipo_id_usuario, ";
				    $query_subir_registro_corregido.=" id_usuario, ";
				    $query_subir_registro_corregido.=" nick_usuario, ";
				    $query_subir_registro_corregido.=" numero_registro, ";
				    $query_subir_registro_corregido.=" fecha_corte_reporte, ";
				    $query_subir_registro_corregido.=" fecha_de_generacion, ";
				    $query_subir_registro_corregido.=" hora_generacion, ";
				    $query_subir_registro_corregido.=" codigo_entidad_eapb_generadora, ";
				    $query_subir_registro_corregido.=" codigo_entidad_prestadora, ";
				    $query_subir_registro_corregido.=" nombre_archivo_pyp ";
				    $query_subir_registro_corregido.=" ) ";
				    $query_subir_registro_corregido.=" VALUES ";
				    $query_subir_registro_corregido.=" ( ";				
				    $numero_actual_campo_registro_corregido=0;
				    while($numero_actual_campo_registro_corregido<=118)
				    {
					    $query_subir_registro_corregido.="'".$campos_del_registro_corregido[$numero_actual_campo_registro_corregido]."',";
					    $numero_actual_campo_registro_corregido++;
				    }//fin while con los valores de los campos 4505 a insertar en la tabla
				    if($this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120")
				    {
				    	$query_subir_registro_corregido.="'".$campos_del_registro_corregido[119]."',";
					}//fin if
				    $query_subir_registro_corregido.="'".$tipo_id."',";
				    $query_subir_registro_corregido.="'".$identificacion."',";
				    $query_subir_registro_corregido.="'".$nick_user."',";	
				    $query_subir_registro_corregido.="'".$this->consecutivo_fixer."',";							
				    $query_subir_registro_corregido.="'".$this->cadena_fecha_corte."',";
				    $query_subir_registro_corregido.="'".$fecha_actual."',";
				    $query_subir_registro_corregido.="'".$tiempo_actual."',";
				    $query_subir_registro_corregido.="'".$this->cod_eapb_global."',";
				    $query_subir_registro_corregido.="'".$this->cod_registro_especial_pss."',";
				    $query_subir_registro_corregido.="'".$nombre_archivo_sin_extension."'";
				    $query_subir_registro_corregido.=" ) ";
				    $query_subir_registro_corregido.=" ; ";
				    $error_bd_seq="";		
				    $bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($query_subir_registro_corregido, $error_bd_seq);
				    if($error_bd_seq!="")
				    {
					    $mensajes_error_bd.=" ERROR Al subir en la tabla temporal de registros corregidos pre correccion duplicados  para corrector ".$this->procesar_mensaje($error_bd_seq).".<br>";
					    
				    }
				    */
				    //fin sube a corregidos_con_duplicados_pyp4505

				    $cont_linea_index_dupl++;

				}//fin if el numero de campos es igual a 119
				//FIN SUBIDA A BASE DE DATOS
				
				$this->consecutivo_fixer++;
			    }
			    else if($cont_linea!=0)
			    {

			    	/*
					//se abre con modo a para que adicione que no subio
					$file_archivo4505_corregido = fopen($ruta_archivo4505_corregido, "a") or die("fallo la creacion del archivo");
					
					fwrite($file_archivo4505_corregido, "\n"."NO SUBIO");
					
					//CIERRA EL ARCHIVO
					fclose($file_archivo4505_corregido);
					*/
					
					$numero_lineas_campos_incorrectos++;
			    }
			    //FIN ESCRIBE EL REGISTRO CORREGIDO
			    
			    if($se_realizo_correccion_a_campos_con_errores_del_archivo==false)
			    {
				$se_realizo_correccion_a_campos_con_errores_del_archivo=$se_hizo_correccion;
			    }
			    
			    //incrementa la linea si no ha ocurrido alguno de estos casos
			    
			    $cont_linea++;
			    
		    }//fin while
		    fclose($archivo_a_verificar);
		    
		    //despues del while
		    
		    /*
		    $mensaje_estado_registros="";
		    $mensaje_estado_registros.="Numero de registros: ".($lineas_del_archivo-1).",<br>";
		    //solo aqui se resta al cont_linea
		    $mensaje_estado_registros.="Numero de registros corregidos: ".($cont_linea-1).",<br>";
		    $mensaje_estado_registros.="Numero de personas: ".$personas_insertadas_hasta_el_momento.",<br>";
		    $mensaje_estado_registros.="Numero de personas con registros duplicadas: ".$personas_con_duplicados_hasta_el_momento.",<br>";
		    $mensaje_estado_registros.="Numero de registros duplicadas: ".$acumulador_para_contar_duplicados.".<br>";
		    $mensaje_estado_registros.="Porcentaje actual: $porcentaje %.<br>";
		    */
		    
		    $mensaje_estado_registros="";
		    $mensaje_estado_registros.="<table style=text-align:center;width:60%;left:25%;border-style:solid;border-width:5px; id=tabla_estado_1>";
		    $mensaje_estado_registros.="<tr style=background-color:#80bfff><th colspan=2 style=text-align:center;width:60%><span style=\"color:white;text-shadow:2px 2px 8px #0000ff;\">Inicio a las $tiempo_actual del $fecha_actual para $nombre_archivo4505</span></th></tr>";
		    $mensaje_estado_registros.="<tr><td style=text-align:left;width:60%>Numero de registros:</td><td style=text-align:left>".($lineas_del_archivo-1)."</td></tr>";
		    //solo aqui se resta al cont_linea
		    $mensaje_estado_registros.="<tr style=background-color:#80bfff><td style=text-align:left;width:60%>Numero de registros corregidos:</td><td style=text-align:left>".($cont_linea-1)."</td></tr>";
		    $mensaje_estado_registros.="<tr><td style=text-align:left;width:60%>Porcentaje registros corregidos:</td><td style=text-align:left>$cont_porcentaje %</td></tr>";
		    $mensaje_estado_registros.="<tr style=background-color:#80bfff><td style=text-align:left;width:60%>Numero de registros duplicados:</td><td style=text-align:left>".$acumulador_para_contar_duplicados.".</tr>";
		    $mensaje_estado_registros.="<tr><td style=text-align:left;width:60%>Numero de personas unicas:</td><td style=text-align:left>".$personas_insertadas_hasta_el_momento."</td></tr>";
		    $mensaje_estado_registros.="<tr style=background-color:#80bfff><td style=text-align:left;width:60%>Numero de personas con registros duplicados:</td><td style=text-align:left>".$personas_con_duplicados_hasta_el_momento."</td></tr>";
		    $mensaje_estado_registros.="<tr><td style=text-align:left;width:60%>Numero de registros que no cumple por numero de campos</td><td style=text-align:left>".$numero_lineas_campos_incorrectos."</td></tr>";
		    $mensaje_estado_registros.="</table><br>";
				
		    $mensaje_perm_estado=$mensaje_estado_registros;
		    //fin despues del while
		    
		    //reescribe primera linea para escribir el numero de registros
		    $file_archivo4505_corregido=fopen($ruta_archivo4505_corregido, "c") or die("fallo la creacion del archivo");
		    //parte primera linea
		    $consecutivo_anterior=intval($array_linea_procesada[4]);
		    $numero_caracteres_consecutivo_anterior=strlen("".$consecutivo_anterior);
		    $numero_caracteres_consecutivo_nuevo=strlen("".($this->consecutivo_fixer-1));
		    //echo "<script>alert('$numero_caracteres_consecutivo_anterior $consecutivo_anterior $numero_caracteres_consecutivo_nuevo ".($this->consecutivo_fixer-1)."');</script>";
		    if($numero_caracteres_consecutivo_anterior==$numero_caracteres_consecutivo_nuevo)
		    {
			fwrite($file_archivo4505_corregido, $array_linea_procesada[0]."|".$array_linea_procesada[1]."|".$array_linea_procesada[2]."|".$array_linea_procesada[3]."|".($this->consecutivo_fixer-1)." ");
		    }
		    else
		    {
			fwrite($file_archivo4505_corregido, $array_linea_procesada[0]."|".$array_linea_procesada[1]."|".$array_linea_procesada[2]."|".$array_linea_procesada[3]."|".($this->consecutivo_fixer-1));
		    }
		    //fin part primear linea
		    fclose($file_archivo4505_corregido);
		    //reescribe primera linea para escribir el numero de registros
	       
		    
		    
		    unset($this->diccionario_identificacion);
		    unset($this->diccionario_identificacion_lineas);
			    
		    $nombre_tabla_indexador_duplicados="gioss_indexador_duplicados_del_reparador_4505";
		    if($this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120")
			{
				//susa una tabla de indexador distinta pero para duplicados teniendo en cuenta el campo extra 120 con codigo eapb
				$nombre_tabla_indexador_duplicados="gioss_indexador_dupl_del_reparador_4505_agrup_ips";
			}//fin if
		    
		    
		    //ARREGLO DE DUPLICADOS EN UNO SOLO
		    $nombre_vista_index_duplicados="indxd4505".$nombre_archivo_sin_extension.$nick_user.$fecha_y_hora_para_view;
		    $hubo_al_menos_un_duplicado=true;//se pone por defecto true para mantener el bloque organizado ya que esta variable no es relevante
		    $contador_duplicado_para_excluidos=0;
		    if($hubo_al_menos_un_duplicado==true)
		    {
			    $sql_vista_duplicados_reporte_obligatorio ="";
			    $sql_vista_duplicados_reporte_obligatorio.="CREATE OR REPLACE VIEW $nombre_vista_index_duplicados ";
			    $sql_vista_duplicados_reporte_obligatorio.=" AS  ";					
			    $sql_vista_duplicados_reporte_obligatorio .="SELECT * from $nombre_tabla_indexador_duplicados  ";	
			    $sql_vista_duplicados_reporte_obligatorio.=" WHERE fecha_de_generacion='$fecha_actual'  AND hora_generacion='$tiempo_actual' ";
			    $sql_vista_duplicados_reporte_obligatorio.=" AND ";
			    $sql_vista_duplicados_reporte_obligatorio.=" codigo_entidad_prestadora='".$this->cod_registro_especial_pss."'  ";
			    $sql_vista_duplicados_reporte_obligatorio.=" AND ";
			    $sql_vista_duplicados_reporte_obligatorio.=" codigo_entidad_eapb_generadora='".$this->cod_eapb_global."'  ";
			    $sql_vista_duplicados_reporte_obligatorio.=" AND ";
			    $sql_vista_duplicados_reporte_obligatorio.=" nombre_archivo_pyp='".$nombre_archivo_sin_extension."'  ";
			    $sql_vista_duplicados_reporte_obligatorio.=" AND ";
			    $sql_vista_duplicados_reporte_obligatorio.=" tipo_id_usuario='$tipo_id' ";
			    $sql_vista_duplicados_reporte_obligatorio.=" AND ";
			    $sql_vista_duplicados_reporte_obligatorio.=" id_usuario='$identificacion' ";
			    $sql_vista_duplicados_reporte_obligatorio.=" AND ";
			    $sql_vista_duplicados_reporte_obligatorio.=" nick_usuario='$nick_user' ";
			    $sql_vista_duplicados_reporte_obligatorio.=" AND ";
			    $sql_vista_duplicados_reporte_obligatorio.=" contiene_filas_coincidentes='SI' ";
			    
			    if($this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120")
				{
					$sql_vista_duplicados_reporte_obligatorio.=" ORDER BY campo_3_tipo_id asc,campo_4_numero_id, campo_extra_120_eapb_regis asc ";
				}//fin if
				else
				{
					$sql_vista_duplicados_reporte_obligatorio.=" ORDER BY campo_3_tipo_id asc,campo_4_numero_id asc ";//campo_extra_120_eapb_regis
				}//fin else
			    $sql_vista_duplicados_reporte_obligatorio.=";";
			    $error_bd_seq="";		
			    $bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($sql_vista_duplicados_reporte_obligatorio, $error_bd_seq);
			    if($error_bd_seq!="")
			    {
				    $mensajes_error_bd.=" ERROR al crear vista de duplicados ($nombre_vista_index_duplicados): ".$error_bd_seq."<br>";
				    echo " ERROR al crear vista de duplicados ($nombre_vista_index_duplicados): ".$error_bd_seq."<br>";
			    }
			    else
			    {
			    	echo "<span style='color:white;'>Se creo vista con el nombre ($nombre_vista_index_duplicados)  sin truncar idealmente </span>";
			    }
			    
			    //numero de duplicados
			    $sql_numero_de_personas="";
			    $sql_numero_de_personas.=" SELECT count(*) as numero_registros FROM $nombre_vista_index_duplicados  ; ";
			    $error_bd_seq="";
			    $array_numero_de_personas=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($sql_numero_de_personas, $error_bd_seq);
			    if($error_bd_seq!="")
			    {
				    $mensajes_error_bd.=" ERROR al consultar numero de registros del total de los duplicados  dejado en la vista ($nombre_vista_index_duplicados): ".$error_bd_seq."<br>";
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
				    $verificar_si_ejecucion_fue_cancelada.=" SELECT esta_ejecutando FROM gioss_4505_esta_reparando_ar_actualmente ";
				    $verificar_si_ejecucion_fue_cancelada.=" WHERE fecha_corte_archivo_en_reparacion='".$this->cadena_fecha_corte."' ";
				    if($this->cod_registro_especial_pss=="AGRUP_EAPB")
				    {
					$verificar_si_ejecucion_fue_cancelada.=" AND codigo_entidad_reportadora='".$this->cod_eapb_global."' ";
				    }
				    else
				    {
					$verificar_si_ejecucion_fue_cancelada.=" AND codigo_entidad_reportadora='".$this->cod_registro_especial_pss."' ";
				    }		    
				    $verificar_si_ejecucion_fue_cancelada.=" AND nombre_archivo='".$nombre_archivo4505."'  ";
				    $verificar_si_ejecucion_fue_cancelada.=" AND nick_usuario='".$nick_user."'  ";
				    $verificar_si_ejecucion_fue_cancelada.=" AND fecha_validacion='".$fecha_actual."'  ";
				    $verificar_si_ejecucion_fue_cancelada.=" AND hora_validacion='".$tiempo_actual."'  ";
				    $verificar_si_ejecucion_fue_cancelada.=" ; ";
				    $error_bd="";
				    $resultados_si_ejecucion_fue_cancelada=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($verificar_si_ejecucion_fue_cancelada, $error_bd);
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
				    $resultados_query_pyp4505_duplicados=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($sql_query_busqueda_personas_bloques,$error_bd_seq);
				    if($error_bd_seq!="")
				    {
					    $mensajes_error_bd.="ERROR AL CONSULTAR de vista de las personas: ".$error_bd_seq."<br>";
				    }
				    
				    if(count($resultados_query_pyp4505_duplicados)>0 && is_array($resultados_query_pyp4505_duplicados))
				    {
					    foreach($resultados_query_pyp4505_duplicados as $duplicado_actual)
					    {						
				    
						//TOMA LOS DATOS DEL DUPLICADO ACTUAL DE LA VISTA DE LA TABLA DEL INDEXADOR
						$tipo_id_duplicado_actual=trim($duplicado_actual["campo_3_tipo_id"]);
						$numero_id_duplicado_actual=trim($duplicado_actual["campo_4_numero_id"]);
						$lista_string_filas_donde_esta_duplicado=trim($duplicado_actual["lista_lineas_donde_hay_duplicados"]);
						$array_filas_correspondientes_al_duplicado_actual=explode(";;",$lista_string_filas_donde_esta_duplicado);
						$numero_filas_donde_esta_afiliado_actual=count($array_filas_correspondientes_al_duplicado_actual);

						$eapb_asoc_regis_actual="";//se llena solo si es agrupado ips
						if($this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120")
						{
							$eapb_asoc_regis_actual=trim($duplicado_actual["campo_extra_120_eapb_regis"]);
						}//fin if

						if($numero_filas_donde_esta_afiliado_actual>1)
						{						    
						    $numero_duplicados_procesados_hasta_el_momento+=$numero_filas_donde_esta_afiliado_actual;
						}//fin if
						//FIN TOMA LOS DATOS DEL DUPLICADO ACTUAL DE LA VISTA DE LA TABLA DEL INDEXADOR
						
						
						$bool_ya_se_proceso=false;
						
						//LEE EL ARCHIVO CORREGIDO PARA CADA LINEA Y LO SUBE A BD

						//CREACION DEL ARCHIVO DE REGISTROS DUPLICADOS DEL AFILIADO ACTUAL REPARACIONDUPLPORTXT
						$primera_linea_duplicados_afiliado_actual=true;
						$ruta_temporal_duplicados_afiliado_actual=$ruta_temporales.$nombre_archivo_sin_extension."tmpdupl".$fecha_para_archivo.".txt";
						//se remplaza el archivo si ya existe con modo w		
						$file_temporal_duplicados_afiliado_actual = fopen($ruta_temporal_duplicados_afiliado_actual, "w") or die("fallo la creacion del archivo");		    
						fclose($file_temporal_duplicados_afiliado_actual);
						
						$ruta_temporal_nsecuencia_duplicados_afiliado_actual=$ruta_temporales.$nombre_archivo_sin_extension."secdupl".$fecha_para_archivo.".txt";
						//se remplaza el archivo si ya existe con modo w		
						$file_temporal_nsecuencia_duplicados_afiliado_actual = fopen($ruta_temporal_nsecuencia_duplicados_afiliado_actual, "w") or die("fallo la creacion del archivo");		    
						fclose($file_temporal_nsecuencia_duplicados_afiliado_actual);	
						//FIN CREACION DEL ARCHIVO DE REGISTROS DUPLICADOS DEL AFILIADO ACTUAL REPARACIONDUPLPORTXT

						foreach($array_filas_correspondientes_al_duplicado_actual as $numero_linea_dupl)
						{
						    //lee el archivo de texto en la linea especifica
						    $linea_act = intval($numero_linea_dupl) ; 
						    $fileHandler = new SplFileObject($ruta_archivo4505_corregido);		
						    $fileHandler->seek($linea_act);
						    $linea_duplicada_del_afiliado=$fileHandler->current();
						    $array_campos_del_duplicado_del_afiliado=explode("|",$linea_duplicada_del_afiliado);
						    //fin lee el archivo de texto en la linea especifica
						    
						    //lee el archivo con el numero de secuencia del registro REPARACIONDUPLPORTXT							
							/*
							//no aplica porque no se tiene numero secuencia al reparar
							$fileHandler_2 = new SplFileObject($ruta_temp_numero_secuencia);		
							$fileHandler_2->seek($linea_act);
							$linea_posee_secuencia_prestador_desde_txt=$fileHandler_2->current();
							$array_posee_secuencia_prestador_desde_txt=explode("|",$linea_posee_secuencia_prestador_desde_txt);
							$numero_secuencia_desde_txt=trim($array_posee_secuencia_prestador_desde_txt[0]);
							$prestador_desde_txt=trim($array_posee_secuencia_prestador_desde_txt[1]);
							$regimen_desde_txt=trim($array_posee_secuencia_prestador_desde_txt[2]);
							*/
							//fin lee el archivo con el numero de secuencia del registro REPARACIONDUPLPORTXT
						    
						    //PARTE ESCRIBE LOG REPARACION DE DUPLICADOS PARTE 1
						    //se abre con modo a para que adicione que no subio
						    $file_cambios_duplicados_registro = fopen($ruta_cambios_duplicados_campos, "a") or die("fallo la creacion del archivo");
						    
						    $identificadores_de_cambios_duplicados_registro="";
						    $identificadores_de_cambios_duplicados_registro.=$nombre_archivo_sin_extension."||";//nombre del archivo
						    $identificadores_de_cambios_duplicados_registro.=$fecha_actual."||";//fecha correccion
						    $identificadores_de_cambios_duplicados_registro.=$tiempo_actual."||";//hora correcion
						    $identificadores_de_cambios_duplicados_registro.="DUPLICADO"."||";//identificador si es duplicado, unico, final
						    $identificadores_de_cambios_duplicados_registro.=$this->cadena_fecha_corte."||";//fecha de corte
						    $identificadores_de_cambios_duplicados_registro.="PYP"."||";//tipo reporte
						    $identificadores_de_cambios_duplicados_registro.=$this->cod_eapb_global."||";
						    $identificadores_de_cambios_duplicados_registro.=$array_campos_del_duplicado_del_afiliado[2]."||";//codigo prestador del registro en el archivo
						    $identificadores_de_cambios_duplicados_registro.="REPARACION"."||";//reparacion o consolidado
						    if($this->cod_registro_especial_pss=="AGRUP_EAPB")
						    {
							$identificadores_de_cambios_duplicados_registro.="AGRUPADO"."||";
						    }
						    else
						    {
							$identificadores_de_cambios_duplicados_registro.="DE PRESTADOR"."||";
						    }
						    $identificadores_de_cambios_duplicados_registro.=$array_campos_del_duplicado_del_afiliado[1]."||";//numero registro
						    fwrite($file_cambios_duplicados_registro, $identificadores_de_cambios_duplicados_registro.$linea_duplicada_del_afiliado);
						    
						    //cierra el archivo del log reparacion de duplicados
						    fclose($file_cambios_duplicados_registro);
						    //FIN PARTE ESCRIBE LOG REPARACION DE DUPLICADOS PARTE 1
						    
						    
						    
						    //if mira que la linea contega los 119 campos
						    if( (count($array_campos_del_duplicado_del_afiliado)==119 && $this->tipo_entidad_que_efectua_el_cargue!="agrupado_ips120")
						    	|| (count($array_campos_del_duplicado_del_afiliado)==120 && $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120")
						    	)//fin condicion
						    {
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
									/*
								    //sube a gioss_temp_dupl_afiliado_actual_reparador_pyp4505
								    //para agrupar solo los registros duplicados para dicho afiliado
								    $query_subir_registro_corregido="";
								    $query_subir_registro_corregido.=" INSERT INTO ";
								    $query_subir_registro_corregido.=" gioss_temp_dupl_afiliado_actual_reparador_pyp4505 ";				
								    $query_subir_registro_corregido.=" ( ";				
								    $numero_actual_campo_registro_corregido=0;
								    while($numero_actual_campo_registro_corregido<=118)
								    {
									    $query_subir_registro_corregido.=" campo_".$numero_actual_campo_registro_corregido." , ";
									    $numero_actual_campo_registro_corregido++;
								    }//fin while para nombres columnas de bd correspondientes a los campos de 4505 a insertar
								    $query_subir_registro_corregido.=" tipo_id_usuario, ";
								    $query_subir_registro_corregido.=" id_usuario, ";
								    $query_subir_registro_corregido.=" nick_usuario, ";
								    $query_subir_registro_corregido.=" numero_registro, ";
								    $query_subir_registro_corregido.=" fecha_corte_reporte, ";
								    $query_subir_registro_corregido.=" fecha_de_generacion, ";
								    $query_subir_registro_corregido.=" hora_generacion, ";
								    $query_subir_registro_corregido.=" codigo_entidad_eapb_generadora, ";
								    $query_subir_registro_corregido.=" codigo_entidad_prestadora, ";
								    $query_subir_registro_corregido.=" nombre_archivo_pyp ";
								    $query_subir_registro_corregido.=" ) ";
								    $query_subir_registro_corregido.=" VALUES ";
								    $query_subir_registro_corregido.=" ( ";				
								    $numero_actual_campo_registro_corregido=0;
								    while($numero_actual_campo_registro_corregido<=118)
								    {
									if($numero_actual_campo_registro_corregido!=3 &&  $numero_actual_campo_registro_corregido!=4)
									{
									    $query_subir_registro_corregido.="'".trim($this->alphanumericAndSpace4($array_campos_del_duplicado_del_afiliado[$numero_actual_campo_registro_corregido]))."',";								
									}
									else if($numero_actual_campo_registro_corregido==3)
									{
									    $query_subir_registro_corregido.="'".$tipo_id_duplicado_actual."',";
									
									}
									else if($numero_actual_campo_registro_corregido==4)
									{
									    $query_subir_registro_corregido.="'".$numero_id_duplicado_actual."',";
									    
									    $num_id_temp_del_array=trim($this->alphanumericAndSpace4($array_campos_del_duplicado_del_afiliado[$numero_actual_campo_registro_corregido]));
									    if($num_id_temp_del_array!=$numero_id_duplicado_actual)
									    {
										echo "<script>alert('numero registro: ".$array_campos_del_duplicado_del_afiliado[1]." los numero id son diferentes del array: $num_id_temp_del_array de la tabla indexador:$numero_id_duplicado_actual lista: $lista_string_filas_donde_esta_duplicado');</script>";
									    }
									}
									
									$numero_actual_campo_registro_corregido++;
								    }//fin while con los valores de los campos 4505 a insertar en la tabla
								    $query_subir_registro_corregido.="'".$tipo_id."',";
								    $query_subir_registro_corregido.="'".$identificacion."',";
								    $query_subir_registro_corregido.="'".$nick_user."',";	
								    $query_subir_registro_corregido.="'".$array_campos_del_duplicado_del_afiliado[1]."',";							
								    $query_subir_registro_corregido.="'".$this->cadena_fecha_corte."',";
								    $query_subir_registro_corregido.="'".$fecha_actual."',";
								    $query_subir_registro_corregido.="'".$tiempo_actual."',";
								    $query_subir_registro_corregido.="'".$this->cod_eapb_global."',";
								    $query_subir_registro_corregido.="'".$this->cod_registro_especial_pss."',";
								    $query_subir_registro_corregido.="'".$nombre_archivo_sin_extension."'";
								    $query_subir_registro_corregido.=" ) ";
								    $query_subir_registro_corregido.=" ; ";
								    $error_bd_seq="";		
								    $bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($query_subir_registro_corregido, $error_bd_seq);
								    if($error_bd_seq!="")
								    {
									    $mensajes_error_bd.=" ERROR Al subir en la tabla gioss_temp_dupl_afiliado_actual_reparador_pyp4505 ".$this->procesar_mensaje($error_bd_seq).".<br>";
									    
								    }
								    //fin sube a gioss_temp_dupl_afiliado_actual_reparador_pyp4505
								    */

								    //PARTE CREA TXT TEMPORAL PARA DUPLICADOS DEL COINCIDENTE ACTUAL	REPARACIONDUPLPORTXT						
									$file_temporal_duplicados_afiliado_actual = fopen($ruta_temporal_duplicados_afiliado_actual, "a") or die("fallo la creacion del archivo");							
									$registro_para_txt="";								
									//corresponden los id de el registro con los id del coincidente actual
									$tipo_id_temp_del_array=trim($this->alphanumericAndSpace4($array_campos_del_duplicado_del_afiliado[3]));
									$num_id_temp_del_array=trim($this->alphanumericAndSpace4($array_campos_del_duplicado_del_afiliado[4]));						

									$eapb_asoc_regis_temp_del_array="";//se llena si existe el campo 120
									if($this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120" && isset($array_campos_del_duplicado_del_afiliado[119]) )
									{
										$eapb_asoc_regis_temp_del_array=trim($this->alphanumericAndSpace4($array_campos_del_duplicado_del_afiliado[119]));
									}//fin if					
									if($num_id_temp_del_array==$numero_id_duplicado_actual
									&& $tipo_id_temp_del_array==$tipo_id_duplicado_actual
									&& ($eapb_asoc_regis_temp_del_array==$eapb_asoc_regis_actual)
									)//fin parentesis condicion
									{
										$registro_para_txt.=trim($linea_duplicada_del_afiliado);
									}//fin if
									else 
									{
										$mensaje_verificador_id_dupl="";
										$mensaje_verificador_id_dupl.="(para txt) numero registro: ".$array_campos_del_duplicado_del_afiliado[1];
										$mensaje_verificador_id_dupl.=" los id son diferentes del array: $tipo_id_temp_del_array $num_id_temp_del_array ";
										$mensaje_verificador_id_dupl.="de la tabla indexador: $tipo_id_duplicado_actual $numero_id_duplicado_actual $eapb_asoc_regis_actual ";
										$mensaje_verificador_id_dupl.=" lista: $lista_string_filas_donde_esta_duplicado .";
										echo "<script>alert('$mensaje_verificador_id_dupl');</script>";
									}
									//fin corresponden los id de el registro con los id del coincidente actual							
									if($primera_linea_duplicados_afiliado_actual==true)
									{
										$primera_linea_duplicados_afiliado_actual=false;
										fwrite($file_temporal_duplicados_afiliado_actual, $registro_para_txt);								
										
									}//fin if
									else
									{
										fwrite($file_temporal_duplicados_afiliado_actual, "\n".$registro_para_txt);
									}//fin else
									fclose($file_temporal_duplicados_afiliado_actual);
									
									/*
									$file_temporal_nsecuencia_duplicados_afiliado_actual = fopen($ruta_temporal_nsecuencia_duplicados_afiliado_actual, "a") or die("fallo la creacion del archivo");							
									$secuencia_prestador_para_txt="";								
									//corresponden los id de el registro con los id del coincidente actual
									$tipo_id_temp_del_array=trim($this->alphanumericAndSpace4($array_campos_del_duplicado_del_afiliado[3]));
									$num_id_temp_del_array=trim($this->alphanumericAndSpace4($array_campos_del_duplicado_del_afiliado[4]));						
									if($num_id_temp_del_array==$numero_id_duplicado_actual
									&& $tipo_id_temp_del_array==$tipo_id_duplicado_actual
									)//fin parentesis condicion
									{
										$secuencia_prestador_para_txt.=trim($linea_posee_secuencia_prestador_desde_txt);
									}//fin if
									else 
									{
										$mensaje_verificador_id_dupl="";
										$mensaje_verificador_id_dupl.="(para txt) numero registro: ".$array_campos_del_duplicado_del_afiliado[1];
										$mensaje_verificador_id_dupl.=" los id son diferentes del array: $tipo_id_temp_del_array $num_id_temp_del_array ";
										$mensaje_verificador_id_dupl.="de la tabla indexador: $tipo_id_duplicado_actual $numero_id_duplicado_actual ";
										$mensaje_verificador_id_dupl.=" lista: $lista_string_filas_donde_esta_duplicado .";
										echo "<script>alert('$mensaje_verificador_id_dupl');</script>";
									}
									//fin corresponden los id de el registro con los id del coincidente actual							
									if($primera_linea_duplicados_afiliado_actual==true)
									{
										$primera_linea_duplicados_afiliado_actual=false;
										fwrite($file_temporal_nsecuencia_duplicados_afiliado_actual, $secuencia_prestador_para_txt);								
										
									}//fin if
									else
									{
										fwrite($file_temporal_nsecuencia_duplicados_afiliado_actual, "\n".$secuencia_prestador_para_txt);
									}//fin else
									fclose($file_temporal_nsecuencia_duplicados_afiliado_actual);	
									*/
									//FIN PARTE CREA TXT TEMPORAL PARA DUPLICADOS DEL COINCIDENTE ACTUAL REPARACIONDUPLPORTXT
								    
								    //para agrupar solo los registros duplicados para dicho afiliado
								    
								    //sube a corregidos_solo_duplicados_pyp4505 para reportes futuros
								    /*
								    $query_subir_registro_corregido="";
								    $query_subir_registro_corregido.=" INSERT INTO ";
								    $query_subir_registro_corregido.=" corregidos_solo_duplicados_pyp4505 ";				
								    $query_subir_registro_corregido.=" ( ";				
								    $numero_actual_campo_registro_corregido=0;
								    while($numero_actual_campo_registro_corregido<=118)
								    {
									    $query_subir_registro_corregido.=" campo_".$numero_actual_campo_registro_corregido." , ";
									    $numero_actual_campo_registro_corregido++;
								    }//fin while para nombres columnas de bd correspondientes a los campos de 4505 a insertar
								    if($this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120")
								    {
								    	$query_subir_registro_corregido.="campo_extra_120_eapb_regis,";
									}//fin if
								    $query_subir_registro_corregido.=" tipo_id_usuario, ";
								    $query_subir_registro_corregido.=" id_usuario, ";
								    $query_subir_registro_corregido.=" nick_usuario, ";
								    $query_subir_registro_corregido.=" numero_registro, ";
								    $query_subir_registro_corregido.=" fecha_corte_reporte, ";
								    $query_subir_registro_corregido.=" fecha_de_generacion, ";
								    $query_subir_registro_corregido.=" hora_generacion, ";
								    $query_subir_registro_corregido.=" codigo_entidad_eapb_generadora, ";
								    $query_subir_registro_corregido.=" codigo_entidad_prestadora, ";
								    $query_subir_registro_corregido.=" nombre_archivo_pyp ";
								    $query_subir_registro_corregido.=" ) ";
								    $query_subir_registro_corregido.=" VALUES ";
								    $query_subir_registro_corregido.=" ( ";				
								    $numero_actual_campo_registro_corregido=0;
								    while($numero_actual_campo_registro_corregido<=118)
								    {
									    $query_subir_registro_corregido.="'".trim($this->alphanumericAndSpace4($array_campos_del_duplicado_del_afiliado[$numero_actual_campo_registro_corregido]))."',";
									    $numero_actual_campo_registro_corregido++;
								    }//fin while con los valores de los campos 4505 a insertar en la tabla
								    if($this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120")
								    {
								    	$query_subir_registro_corregido.="'".trim($this->alphanumericAndSpace4($array_campos_del_duplicado_del_afiliado[119]))."',";
									}//fin if
								    $query_subir_registro_corregido.="'".$tipo_id."',";
								    $query_subir_registro_corregido.="'".$identificacion."',";
								    $query_subir_registro_corregido.="'".$nick_user."',";	
								    $query_subir_registro_corregido.="'".$array_campos_del_duplicado_del_afiliado[1]."',";							
								    $query_subir_registro_corregido.="'".$this->cadena_fecha_corte."',";
								    $query_subir_registro_corregido.="'".$fecha_actual."',";
								    $query_subir_registro_corregido.="'".$tiempo_actual."',";
								    $query_subir_registro_corregido.="'".$this->cod_eapb_global."',";
								    $query_subir_registro_corregido.="'".$this->cod_registro_especial_pss."',";
								    $query_subir_registro_corregido.="'".$nombre_archivo_sin_extension."'";
								    $query_subir_registro_corregido.=" ) ";
								    $query_subir_registro_corregido.=" ; ";
								    $error_bd_seq="";		
								    $bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($query_subir_registro_corregido, $error_bd_seq);
								    if($error_bd_seq!="")
								    {
									    $mensajes_error_bd.=" ERROR Al subir en la tabla corregidos_solo_duplicados_pyp4505 ".$this->procesar_mensaje($error_bd_seq).".<br>";
									    
								    }
								    */
								    //fin sube a corregidos_solo_duplicados_pyp4505 para reportes futuros
								    
								    
								}//fin else if si habian varias filas en la lista por ende tiene duplicados el afiliado 
						    }//fin if si la linea posee 119 campos
						    
						}//fin foreach							
						//FIN LEE EL ARCHIVO CORREGIDO PARA CADA LINEA Y LO SUBE A BD
						
						if($bool_ya_se_proceso==false)
						{
							/*
							$nombre_vista_con_los_duplicados_del_afiliado_actual="duppa4505".$nombre_archivo_sin_extension.$nick_user.$fecha_y_hora_para_view;
							
							$sql_vista_duplicados_de_la_persona_actual ="";
							$sql_vista_duplicados_de_la_persona_actual.="CREATE OR REPLACE VIEW $nombre_vista_con_los_duplicados_del_afiliado_actual ";
							$sql_vista_duplicados_de_la_persona_actual.=" AS  ";					
							$sql_vista_duplicados_de_la_persona_actual .="SELECT * from gioss_temp_dupl_afiliado_actual_reparador_pyp4505  ";	
							$sql_vista_duplicados_de_la_persona_actual.=" WHERE fecha_de_generacion='$fecha_actual'  AND hora_generacion='$tiempo_actual' ";
							$sql_vista_duplicados_de_la_persona_actual.=" AND ";
							$sql_vista_duplicados_de_la_persona_actual.=" codigo_entidad_prestadora='".$this->cod_registro_especial_pss."'  ";
							$sql_vista_duplicados_de_la_persona_actual.=" AND ";
							$sql_vista_duplicados_de_la_persona_actual.=" codigo_entidad_eapb_generadora='".$this->cod_eapb_global."'  ";
							$sql_vista_duplicados_de_la_persona_actual.=" AND ";
							$sql_vista_duplicados_de_la_persona_actual.=" nombre_archivo_pyp='".$nombre_archivo_sin_extension."'  ";
							$sql_vista_duplicados_de_la_persona_actual.=" AND ";
							$sql_vista_duplicados_de_la_persona_actual.=" tipo_id_usuario='$tipo_id' ";
							$sql_vista_duplicados_de_la_persona_actual.=" AND ";
							$sql_vista_duplicados_de_la_persona_actual.=" id_usuario='$identificacion' ";
							$sql_vista_duplicados_de_la_persona_actual.=" AND ";
							$sql_vista_duplicados_de_la_persona_actual.=" nick_usuario='$nick_user' ";
							$sql_vista_duplicados_de_la_persona_actual.=" AND ";
							$sql_vista_duplicados_de_la_persona_actual.=" campo_3='$tipo_id_duplicado_actual' ";
							$sql_vista_duplicados_de_la_persona_actual.=" AND ";
							$sql_vista_duplicados_de_la_persona_actual.=" campo_4='$numero_id_duplicado_actual' ";			    
							$sql_vista_duplicados_de_la_persona_actual.=" ORDER BY numero_registro asc ";
							$sql_vista_duplicados_de_la_persona_actual.=";";
							$error_bd_seq="";		
							$bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($sql_vista_duplicados_de_la_persona_actual, $error_bd_seq);
							if($error_bd_seq!="")
							{
							    $mensajes_error_bd.=" ERROR al crear vista de duplicados de la persona actual para corregir en uno solo por persona: ".$error_bd_seq."<br>";
							}
							
							//numero de duplicados del duplicado
							$sql_numero_de_personas_de_duplicado="";
							$sql_numero_de_personas_de_duplicado.=" SELECT count(*) as numero_registros FROM $nombre_vista_con_los_duplicados_del_afiliado_actual  ; ";
							$error_bd_seq="";
							$array_numero_de_personas_de_duplicado=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($sql_numero_de_personas_de_duplicado, $error_bd_seq);
							if($error_bd_seq!="")
							{
								$mensajes_error_bd.=" ERROR al consultar numero de registros del total de los duplicados del duplicado: ".$error_bd_seq."<br>";
							}
							
							$numero_personas_de_duplicado=0;
							if(count($array_numero_de_personas_de_duplicado)>0 && is_array($array_numero_de_personas_de_duplicado))
							{
								$numero_personas_de_duplicado=$array_numero_de_personas_de_duplicado[0]["numero_registros"];
							}
							//numeros de duplicados del duplicado
							*/
							
							//PARTE DONDE LLAMA A LA FUNCION QUE CONTIENE LOS CRITERIOS PARA PROCESAR LOS DUPLICADOS
							//enves del numero de secuencia se usara el ultimo numero de registro(fila)
							$numero_registro_para_procesado="";
							$cod_prestador_para_procesado="";
							//en la funcion se hara falso si no se proceso los duplicados al haber campos vacios
							$bool_fueron_procesados_duplicados_en_un_registro=true;
							
							$array_campos_procesados_de_los_duplicados_del_duplicado=array();

							/*
							$array_campos_procesados_de_los_duplicados_del_duplicado=reparacion_campos_duplicados($tipo_id_duplicado_actual,
																	      $numero_id_duplicado_actual,
																	      $numero_personas_de_duplicado,
																	      $nombre_vista_con_los_duplicados_del_afiliado_actual,
																	      $numero_registro_para_procesado,
																	      $cod_prestador_para_procesado,
																	      $bool_fueron_procesados_duplicados_en_un_registro,
																	      $contador_offset_personas,
																	      $mensajes_error_bd,
																	      $conexionbd);
							*/
							

							//FUNCION QUE REPARA DUPLICADOS POR MEDIO TXT
							$fecha_corte_bd=$this->fecha_de_corte_periodo;
							$numero_secuencia_para_procesado="";
						    $regimen_para_procesado="";
						    $array_campos_procesados_de_los_duplicados_del_duplicado=reparacion_duplicados_por_txt($tipo_id_duplicado_actual,
																	$numero_id_duplicado_actual,
																	$fecha_actual,
																	$tiempo_actual,
																	$fecha_corte_bd,
																	$nick_user,
																	$identificacion,
																	$tipo_id,
																	$numero_filas_donde_esta_afiliado_actual, //nuevo
																	$ruta_temporal_duplicados_afiliado_actual, //nuevo 
																	$ruta_temporal_nsecuencia_duplicados_afiliado_actual,//nuevo
																	$numero_secuencia_para_procesado,//nuevo pasa por referencia
																	$numero_registro_para_procesado,//pasa por referencia
																	$cod_prestador_para_procesado,//pasa por referencia
																	$regimen_para_procesado,//no esta pasa por referencia
																	$bool_fueron_procesados_duplicados_en_un_registro,//pasa por referencia
																	$contador_offset_personas,//pasa por referencia
																	$contador_duplicado_para_excluidos,//no esta pasa por referencia
																	$mensajes_error_bd,//pasa por referencia
																	$conexionbd);//estaba coneccionBD se cambia a conexionbd
						    //FIN FUNCION QUE REPARA DUPLICADOS POR MEDIO TXT
						    
							//fin parte donde se procesaran los duplicados
							
							//insertando registro procesado
							if($bool_fueron_procesados_duplicados_en_un_registro==true)
							{
							    $nlinea_que_tomo_duplicado="";
							    if(isset($array_campos_procesados_de_los_duplicados_del_duplicado[1]))
							    {
								$nlinea_que_tomo_duplicado=$array_campos_procesados_de_los_duplicados_del_duplicado[1];
							    }
							    
							    //eapb_asoc_regis_actual
							    if($this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120")
							    {
							    	$array_campos_procesados_de_los_duplicados_del_duplicado[119]=$eapb_asoc_regis_actual;
								}
							    
							    //campos unificado del duplicado pre correccion
							    $array_pre_correccion_unificado_dupl=array();
							    $cont_asign=0;
							    while($cont_asign<count($array_campos_procesados_de_los_duplicados_del_duplicado))
							    {
								$array_pre_correccion_unificado_dupl[]=$array_campos_procesados_de_los_duplicados_del_duplicado[$cont_asign];
								$cont_asign++;
							    }
							    //fin campos unificado del duplicado pre correccion
							    
							    //parte correccion de campos para el duplicado corregido
							    $cont_total_registros_del_duplicado_corregido_antes=count($array_campos_procesados_de_los_duplicados_del_duplicado);
							    
							    $linea_duplicado_corregido_a_reparar="";
							    
							    $cont_orden_campo_pyp=0;									
							    while($cont_orden_campo_pyp<=118)
							    {
								if($linea_duplicado_corregido_a_reparar!=""){$linea_duplicado_corregido_a_reparar.="|";}
								$linea_duplicado_corregido_a_reparar.=$array_campos_procesados_de_los_duplicados_del_duplicado[$cont_orden_campo_pyp];
								$cont_orden_campo_pyp++;
							    }//fin while
							    $string_campos_procesados_de_los_duplicados_del_duplicado=$this->correccion_errores_campos_PyP_4505($linea_duplicado_corregido_a_reparar,
																			       $array_campos_procesados_de_los_duplicados_del_duplicado[1],
																			       $consecutivo_errores,
																			       $conexionbd
																			       )["registro_corregido"];
							    
							    $array_campos_procesados_de_los_duplicados_del_duplicado=explode("|",$string_campos_procesados_de_los_duplicados_del_duplicado);
							    
							    //eapb_asoc_regis_actual
							    if($this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120")
							    {
							    	$array_campos_procesados_de_los_duplicados_del_duplicado[119]=$eapb_asoc_regis_actual;
								}//fin if
							    
							    $cont_total_registros_del_duplicado_corregido_despues=count($array_campos_procesados_de_los_duplicados_del_duplicado);
							    //echo "<script>alert('antes: $cont_total_registros_del_duplicado_corregido_antes, despues: $cont_total_registros_del_duplicado_corregido_despues');</script>";
							    //fin parte correccion de campos para el duplicado corregido
							    
							    //PARTE ESCRIBE LOG CAMBIOS CORRECCION UNIFICADO DEL DUPLICADO
							    if( 
							    	(count($array_pre_correccion_unificado_dupl)==119
							       && count($array_campos_procesados_de_los_duplicados_del_duplicado)==119
							       && $this->tipo_entidad_que_efectua_el_cargue!="agrupado_ips120"
							       )
							    	|| (count($array_pre_correccion_unificado_dupl)==120
							       && count($array_campos_procesados_de_los_duplicados_del_duplicado)==120
							       && $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120")
							       )//fin condicion if
							    {
								//se abre con modo a para que adicione que no subio
								
								$file_cambios_real_dupl_registro_2 = fopen($ruta_cambios_real_dupl_campos_2, "a") or die("fallo la creacion del archivo");
								
								$cont_log_cambios=0;
								while($cont_log_cambios<119)
								{
								    if(trim($array_pre_correccion_unificado_dupl[$cont_log_cambios])!=trim($array_campos_procesados_de_los_duplicados_del_duplicado[$cont_log_cambios])
								       && $cont_log_cambios!=1//no interesa el consecutivo aca
								       )
								    {
									$linea_log_cambos_realizados_correccion="";
									$linea_log_cambos_realizados_correccion.="La persona  TI: ".trim($array_pre_correccion_unificado_dupl[3])." ".trim($array_pre_correccion_unificado_dupl[4])." $eapb_asoc_regis_actual ";
									$linea_log_cambos_realizados_correccion.=" reparo el campo numero $cont_log_cambios ";
									$linea_log_cambos_realizados_correccion.=" con un valor inicial de ".trim($array_pre_correccion_unificado_dupl[$cont_log_cambios]);
									$linea_log_cambos_realizados_correccion.=" transformado en ";
									$linea_log_cambos_realizados_correccion.=" el valor final de ".trim($array_campos_procesados_de_los_duplicados_del_duplicado[$cont_log_cambios]);
									$linea_log_cambos_realizados_correccion.=" de acuerdo a los criterios de correccion ";
									$linea_log_cambos_realizados_correccion.="";
									
									fwrite($file_cambios_real_dupl_registro_2, "\n".$linea_log_cambos_realizados_correccion);
								    }//fin if solo escribe si hubo cambios en el campo al corregir
								    
								    $cont_log_cambios++;
								}//fin while
								
								//cierra el archivo del log reparacion de duplicados
								fclose($file_cambios_real_dupl_registro_2);
							    }//fin if
							    //FIN PARTE ESCRIBE LOG CAMBIOS CORRECCION UNIFICADO DEL DUPLICADO
							    
							    //PARTE ESCRIBE LOG REPARACION DE DUPLICADOS PARTE 2
							    //se abre con modo a para que adicione que no subio
							    $file_cambios_duplicados_registro = fopen($ruta_cambios_duplicados_campos, "a") or die("fallo la creacion del archivo");
							    
							    $identificadores_de_cambios_duplicados_registro="";
							    $identificadores_de_cambios_duplicados_registro.=$nombre_archivo_sin_extension."||";//nombre del archivo
							    $identificadores_de_cambios_duplicados_registro.=$fecha_actual."||";//fecha correccion
							    $identificadores_de_cambios_duplicados_registro.=$tiempo_actual."||";//hora correcion
							    $identificadores_de_cambios_duplicados_registro.="--UNICO--"."||";//identificador si es duplicado, unico, final
							    $identificadores_de_cambios_duplicados_registro.=$this->cadena_fecha_corte."||";//fecha de corte
							    $identificadores_de_cambios_duplicados_registro.="PYP"."||";//tipo reporte
							    $identificadores_de_cambios_duplicados_registro.=$this->cod_eapb_global."||";							    
							    $identificadores_de_cambios_duplicados_registro.=$array_campos_procesados_de_los_duplicados_del_duplicado[2]."||";//codigo prestador del registro en el archivo
							    $identificadores_de_cambios_duplicados_registro.="REPARACION"."||";//reparacion o consolidado
							    if($this->cod_registro_especial_pss=="AGRUP_EAPB")
							    {
								$identificadores_de_cambios_duplicados_registro.="AGRUPADO"."||";
							    }
							    else
							    {
								$identificadores_de_cambios_duplicados_registro.="DE PRESTADOR"."||";
							    }
							    $identificadores_de_cambios_duplicados_registro.="U".$nlinea_que_tomo_duplicado."||";
							    fwrite($file_cambios_duplicados_registro, $identificadores_de_cambios_duplicados_registro.$linea_duplicado_corregido_a_reparar);
							    
							    
							    //se abre con modo a para que adicione que no subio
							    $file_cambios_duplicados_registro = fopen($ruta_cambios_duplicados_campos, "a") or die("fallo la creacion del archivo");
							    
							    $identificadores_de_cambios_duplicados_registro="";
							    $identificadores_de_cambios_duplicados_registro.=$nombre_archivo_sin_extension."||";//nombre del archivo
							    $identificadores_de_cambios_duplicados_registro.=$fecha_actual."||";//fecha correccion
							    $identificadores_de_cambios_duplicados_registro.=$tiempo_actual."||";//hora correcion
							    $identificadores_de_cambios_duplicados_registro.="--FINAL--"."||";//identificador si es duplicado, unico, final
							    $identificadores_de_cambios_duplicados_registro.=$this->cadena_fecha_corte."||";//fecha de corte
							    $identificadores_de_cambios_duplicados_registro.="PYP"."||";//tipo reporte
							    $identificadores_de_cambios_duplicados_registro.=$this->cod_eapb_global."||";							    
							    $identificadores_de_cambios_duplicados_registro.=$array_campos_procesados_de_los_duplicados_del_duplicado[2]."||";//codigo prestador del registro en el archivo
							    $identificadores_de_cambios_duplicados_registro.="REPARACION"."||";//reparacion o consolidado
							    if($this->cod_registro_especial_pss=="AGRUP_EAPB")
							    {
								$identificadores_de_cambios_duplicados_registro.="AGRUPADO"."||";
							    }
							    else
							    {
								$identificadores_de_cambios_duplicados_registro.="DE PRESTADOR"."||";
							    }
							    $identificadores_de_cambios_duplicados_registro.="F".$nlinea_que_tomo_duplicado."||";
							    fwrite($file_cambios_duplicados_registro, "\n".$identificadores_de_cambios_duplicados_registro.$string_campos_procesados_de_los_duplicados_del_duplicado."\n");
							    
							    /*
							    if($string_campos_procesados_de_los_duplicados_del_duplicado=="")
							    {
								echo "<script>alert('esta vacio');</script>";
							    }
							    else
							    {
								echo "<script>alert('$string_campos_procesados_de_los_duplicados_del_duplicado');</script>";
							    }							    
							    */
							    
							    //cierra el archivo del log reparacion de duplicados
							    fclose($file_cambios_duplicados_registro);
							    //FIN PARTE ESCRIBE LOG REPARACION DE DUPLICADOS PARTE 2
							    
							    $sql_insert_procesado_en_reporte_obligatorio="";
							    $sql_insert_procesado_en_reporte_obligatorio.=" INSERT INTO ";
							    $sql_insert_procesado_en_reporte_obligatorio.=" corregidos_sin_duplicados_pyp4505 ";									    
							    $sql_insert_procesado_en_reporte_obligatorio.=" ( ";				
							    $cont_orden_campo_pyp=0;
							    while($cont_orden_campo_pyp<=118)
							    {
								    $sql_insert_procesado_en_reporte_obligatorio.=" campo_".$cont_orden_campo_pyp." , ";
								    $cont_orden_campo_pyp++;
							    }//fin while para nombres columnas de bd correspondientes a los campos de 4505 a insertar
							    if($this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120")
							    {
							    	$sql_insert_procesado_en_reporte_obligatorio.="campo_extra_120_eapb_regis,";
								}//fin if
							    $sql_insert_procesado_en_reporte_obligatorio.=" tipo_id_usuario, ";
							    $sql_insert_procesado_en_reporte_obligatorio.=" id_usuario, ";
							    $sql_insert_procesado_en_reporte_obligatorio.=" nick_usuario, ";
							    $sql_insert_procesado_en_reporte_obligatorio.=" numero_registro, ";
							    $sql_insert_procesado_en_reporte_obligatorio.=" fecha_corte_reporte, ";
							    $sql_insert_procesado_en_reporte_obligatorio.=" fecha_de_generacion, ";
							    $sql_insert_procesado_en_reporte_obligatorio.=" hora_generacion, ";
							    $sql_insert_procesado_en_reporte_obligatorio.=" codigo_entidad_eapb_generadora, ";
							    $sql_insert_procesado_en_reporte_obligatorio.=" codigo_entidad_prestadora, ";
							    $sql_insert_procesado_en_reporte_obligatorio.=" nombre_archivo_pyp ";
							    $sql_insert_procesado_en_reporte_obligatorio.=" ) ";
							    $sql_insert_procesado_en_reporte_obligatorio.=" VALUES ";
							    $sql_insert_procesado_en_reporte_obligatorio.=" ( ";				
							    $cont_orden_campo_pyp=0;
							    while($cont_orden_campo_pyp<=118)
							    {
								    $sql_insert_procesado_en_reporte_obligatorio.="'".$array_campos_procesados_de_los_duplicados_del_duplicado[$cont_orden_campo_pyp]."',";
								    $cont_orden_campo_pyp++;
							    }//fin while con los valores de los campos 4505 a insertar en la tabla de reporte obligatorio
							    if($this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120")
							    {
							    	$sql_insert_procesado_en_reporte_obligatorio.="'".$array_campos_procesados_de_los_duplicados_del_duplicado[119]."',";
								}//fin if
							    $sql_insert_procesado_en_reporte_obligatorio.="'".$tipo_id."',";
							    $sql_insert_procesado_en_reporte_obligatorio.="'".$identificacion."',";
							    $sql_insert_procesado_en_reporte_obligatorio.="'".$nick_user."',";
							    $sql_insert_procesado_en_reporte_obligatorio.="'".$numero_registro_para_procesado."',";								
							    $sql_insert_procesado_en_reporte_obligatorio.="'".$this->cadena_fecha_corte."',";
							    $sql_insert_procesado_en_reporte_obligatorio.="'".$fecha_actual."',";
							    $sql_insert_procesado_en_reporte_obligatorio.="'".$tiempo_actual."',";
							    $sql_insert_procesado_en_reporte_obligatorio.="'".$this->cod_eapb_global."',";
							    $sql_insert_procesado_en_reporte_obligatorio.="'".$this->cod_registro_especial_pss."',";
							    $sql_insert_procesado_en_reporte_obligatorio.="'".$nombre_archivo_sin_extension."'";
							    $sql_insert_procesado_en_reporte_obligatorio.=" ) ";
							    $sql_insert_procesado_en_reporte_obligatorio.=" ; ";
							    $error_bd_seq="";		
							    $bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($sql_insert_procesado_en_reporte_obligatorio, $error_bd_seq);
							    if($error_bd_seq!="")
							    {
								    $mensajes_error_bd.=" ERROR Al subir en la tabla corregidos_sin_duplicados_pyp4505 despues de reparar duplicados en un unico registro: ".$error_bd_seq."<br>";
							    }
							}//fin if si fueron procesados duplicados inserta el porcesado en la tabla de archivos reportados obligatorios exitosos de 4505
							//fin insertando registro procesado
							
							//BORRANDO VISTA DE LA TABLA TEMPORAL CON LOS DUPLICADOS DEL AFILIADO
							/*
							$sql_borrar_vista_duplicados_en_uno_solo="";
							$sql_borrar_vista_duplicados_en_uno_solo.=" DROP VIEW $nombre_vista_con_los_duplicados_del_afiliado_actual ; ";							
							$error_bd="";		
							$bool_funciono=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($sql_borrar_vista_duplicados_en_uno_solo, $error_bd);		
							if($error_bd!="")
							{
							    if(connection_aborted()==false)
							    {
								echo "<script>alert('error al borrar la vista duplicados del afiliado actual');</script>";
							    }
								$mensajes_error_bd.=" ERROR Al al borrar la vista duplicados en uno solo: ".$error_bd."<br>";
							}
							*/
							//FIN BORRANDO VISTA DE LA TABLA TEMPORAL CON LOS DUPLICADOS DEL AFILIADO
							
							//BORRANDO INFORMACION DE LA TABLA TEMPORAL CON LOS DUPLICADOS DEL AFILIADO
							/*
							//no esta siendo usada ya que no se inserta nada a esta
							$sql_delete_corregidos_temp="";
							$sql_delete_corregidos_temp.=" DELETE FROM gioss_temp_dupl_afiliado_actual_reparador_pyp4505  ";
							$sql_delete_corregidos_temp.=" WHERE fecha_de_generacion='$fecha_actual'  AND hora_generacion='$tiempo_actual' ";
							$sql_delete_corregidos_temp.=" AND ";
							$sql_delete_corregidos_temp.=" codigo_entidad_prestadora='".$this->cod_registro_especial_pss."'  ";
							$sql_delete_corregidos_temp.=" AND ";
							$sql_delete_corregidos_temp.=" codigo_entidad_eapb_generadora='".$this->cod_eapb_global."'  ";
							$sql_delete_corregidos_temp.=" AND ";
							$sql_delete_corregidos_temp.=" nombre_archivo_pyp='".$nombre_archivo_sin_extension."'  ";
							$sql_delete_corregidos_temp.=" AND ";
							$sql_delete_corregidos_temp.=" tipo_id_usuario='$tipo_id' ";
							$sql_delete_corregidos_temp.=" AND ";
							$sql_delete_corregidos_temp.=" id_usuario='$identificacion' ";
							$sql_delete_corregidos_temp.=" AND ";
							$sql_delete_corregidos_temp.=" nick_usuario='$nick_user' ";
							$sql_delete_corregidos_temp.=" ; ";
							$error_bd_seq="";		
							$bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($sql_delete_corregidos_temp, $error_bd_seq);
							if($error_bd_seq!="")
							{
								$mensajes_error_bd.=" ERROR Al eliminar en la tabla temporal de registros corregidos pre correccion duplicados  para corrector ".$this->procesar_mensaje($error_bd_seq).".<br>";
								
							}
							*/
							//FIN BORRANDO INFORMACION DE LA TABLA TEMPORAL CON LOS DUPLICADOS DEL AFILIADO
						}//fin if si el duplicado no se ha procesado
						
						//porcentaje
						$muestra_mensaje_nuevo_dupl=false;
						$porcentaje_dupl=intval((($numero_duplicados_procesados_hasta_el_momento)*100)/($acumulador_para_contar_duplicados));
						if($porcentaje_dupl!=$cont_porcentaje_dupl || ($porcentaje_dupl==0 && ($numero_duplicados_procesados_hasta_el_momento)==1) || $porcentaje_dupl==100)
						{
						 $cont_porcentaje_dupl=$porcentaje_dupl;
						 $muestra_mensaje_nuevo_dupl=true;
						}
						//fin porcentaje
						
						
						//ACTUALIZA ESTADO DEL ARCHIVO
						$mensaje_estado_registros_temp_dupl="<span style=color:red>Por favor espere, se han arreglado $numero_duplicados_procesados_hasta_el_momento duplicados para un total de $acumulador_para_contar_duplicados duplicados. $porcentaje_dupl %.</span><br>";
						
						$mensaje_perm_estado_reg_dupl=$mensaje_estado_registros_temp_dupl;
						    
						$msg_a_bd="";
						$msg_a_bd=$mensaje_perm_estado." ".$mensaje_perm_estado_reg_dupl;
						
						if($muestra_mensaje_nuevo_dupl)
						{
						    $query_update_esta_siendo_procesado="";
						    $query_update_esta_siendo_procesado.=" UPDATE gioss_4505_esta_reparando_ar_actualmente ";
						    $query_update_esta_siendo_procesado.=" SET mensaje_estado_registros='$msg_a_bd' ";
						    $query_update_esta_siendo_procesado.=" WHERE fecha_corte_archivo_en_reparacion='".$this->cadena_fecha_corte."' ";
						    if($this->cod_registro_especial_pss=="AGRUP_EAPB")
						    {
							$query_update_esta_siendo_procesado.=" AND codigo_entidad_reportadora='".$this->cod_eapb_global."' ";
						    }
						    else
						    {
							$query_update_esta_siendo_procesado.=" AND codigo_entidad_reportadora='".$this->cod_registro_especial_pss."' ";
						    }		    
						    $query_update_esta_siendo_procesado.=" AND nombre_archivo='".$nombre_archivo4505."'  ";
						    $query_update_esta_siendo_procesado.=" AND nick_usuario='".$nick_user."'  ";
						    $query_update_esta_siendo_procesado.=" AND fecha_validacion='".$fecha_actual."'  ";
						    $query_update_esta_siendo_procesado.=" AND hora_validacion='".$tiempo_actual."'  ";
						    $query_update_esta_siendo_procesado.=" ; ";
						    $error_bd="";
						    $bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($query_update_esta_siendo_procesado, $error_bd);
						    if($error_bd!="")
						    {
							    if($fue_cerrada_la_gui2==false)
							    {
								    echo "<script>alert('error al actualizar el estado actual de reparacion en tiempo real  4505 ');</script>";
							    }
						    }
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
			    $bool_funciono=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($sql_borrar_vista_duplicados_en_uno_solo, $error_bd);		
			    if($error_bd!="")
			    {
				if(connection_aborted()==false)
				{
				    echo "<script>alert('error al borrar la vista duplicados en uno solo');</script>";
				}
				    $mensajes_error_bd.=" ERROR Al al borrar la vista nombre completo vista ($nombre_vista_index_duplicados) duplicados en uno solo: ".$error_bd."<br>";
			    }
			    //FIN BORRANDO VISTAS
		    }//fin if si se genera reporte para un archivo validado
		    
		    //FIN ARREGLO DE DUPLICADOS EN UNO SOLO
		    
		    //PARTE ESCRIBE CSV
		    $nombre_vista_consulta_definitiva_corregidos="cro4505".$nombre_archivo_sin_extension.$nick_user.$fecha_y_hora_para_view;
		    
		    $numero_registros_bloque=1000;
		    $sql_vista_consulta_reporte_obligatorio="";
		    $sql_vista_consulta_reporte_obligatorio.="CREATE OR REPLACE VIEW $nombre_vista_consulta_definitiva_corregidos ";
		    $sql_vista_consulta_reporte_obligatorio.=" AS SELECT * FROM corregidos_sin_duplicados_pyp4505 ";
		    $sql_vista_consulta_reporte_obligatorio.=" WHERE ";
		    $sql_vista_consulta_reporte_obligatorio.=" fecha_de_generacion='$fecha_actual'  AND hora_generacion='$tiempo_actual' ";
		    $sql_vista_consulta_reporte_obligatorio.=" AND ";
		    $sql_vista_consulta_reporte_obligatorio.=" codigo_entidad_prestadora='".$this->cod_registro_especial_pss."'  ";
		    $sql_vista_consulta_reporte_obligatorio.=" AND ";
		    $sql_vista_consulta_reporte_obligatorio.=" codigo_entidad_eapb_generadora='".$this->cod_eapb_global."'  ";
		    $sql_vista_consulta_reporte_obligatorio.=" AND ";
		    $sql_vista_consulta_reporte_obligatorio.=" nombre_archivo_pyp='".$nombre_archivo_sin_extension."'  ";
		    $sql_vista_consulta_reporte_obligatorio.=" AND ";
		    $sql_vista_consulta_reporte_obligatorio.=" tipo_id_usuario='$tipo_id' ";
		    $sql_vista_consulta_reporte_obligatorio.=" AND ";
		    $sql_vista_consulta_reporte_obligatorio.=" id_usuario='$identificacion' ";
		    $sql_vista_consulta_reporte_obligatorio.=" AND ";
		    $sql_vista_consulta_reporte_obligatorio.=" nick_usuario='$nick_user' ";
		    $sql_vista_consulta_reporte_obligatorio.=" ORDER BY numero_registro asc ; ";
		    $error_bd_seq="";
		    $bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($sql_vista_consulta_reporte_obligatorio, $error_bd_seq);
		    if($error_bd_seq!="")
		    {
			    $mensajes_error_bd.="ERROR al crear vista de consulta sin duplicados definitvo para escribir al txt ".$error_bd_seq."<br>";
		    }
		    
		    $sql_numero_registros="";
		    $sql_numero_registros.="SELECT count(*) as contador FROM $nombre_vista_consulta_definitiva_corregidos;  ";		
		    $error_bd_seq="";
		    $resultado_query_numero_registros=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($sql_numero_registros,$error_bd_seq);
		    if($error_bd_seq!="")
		    {
			    $mensajes_error_bd.="ERROR AL CONSULTAR numero registros de vista_consulta: ".$error_bd_seq."<br>";
		    }
		    $numero_registros=intval($resultado_query_numero_registros[0]["contador"]);
		    if($numero_registros==0)
		    {
			    $mensajes_error_bd.="No hay registros a consultar. <br> ";
		    }
		    
		    //echo "<script>alert('".$numero_registros."');</script>";
		    
		    //RE-ESCRIBE PRIMERA LINEA DE 4505 PARA ADICIONAR EL NUMERO DE REGISTROS primera vez
		    $file_archivo4505_corregido_sin_duplicados=fopen($ruta_archivo4505_corregido_sin_duplicados, "c") or die("fallo la creacion del archivo");
		    //parte primera linea
		    $consecutivo_anterior=intval($array_linea_procesada[4]);
		    $numero_caracteres_consecutivo_anterior=strlen("".$consecutivo_anterior);
		    $numero_caracteres_consecutivo_nuevo=strlen("".($cont_linea-1));
		    //echo "<script>alert('$numero_caracteres_consecutivo_anterior $consecutivo_anterior $numero_caracteres_consecutivo_nuevo ".($this->consecutivo_fixer-1)."');</script>";
		    
		    fwrite($file_archivo4505_corregido_sin_duplicados, $array_linea_procesada[0]."|".$array_linea_procesada[1]."|".$array_linea_procesada[2]."|".$array_linea_procesada[3]."|".$numero_registros);
		    
		    //fin part primear linea
		    fclose($file_archivo4505_corregido_sin_duplicados);
		    //FIN RE-ESCRIBE PRIMERA LINEA DE 4505 PARA ADICIONAR EL NUMERO DE REGISTROS primera vez
		    
		    
		    $numeroRegistrosExcluidosPorNoPresentarActividad=0;
		    $cont_linea=1;
		    $contador_offset=0;
		    $limite=0;
		    $string_vacia="                ";
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
			    $resultado_query_reporte_obligatoria=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($sql_query_busqueda,$error_bd_seq);			
			    if($error_bd_seq!="")
			    {
				    $mensajes_error_bd.="ERROR AL CONSULTAR de vista_consulta ($nombre_vista_consulta_definitiva_corregidos): ".$error_bd_seq."<br>";
			    }
			    
			    if(count($resultado_query_reporte_obligatoria)>0)
			    {
				    
				    
				    $file_archivo4505_corregido_sin_duplicados= fopen($ruta_archivo4505_corregido_sin_duplicados, "a") or die("fallo la creacion del archivo");
				    			
				    
				    			
				    foreach($resultado_query_reporte_obligatoria as $resultado)
				    {
				    	$boolExcluidoPorNoPresentarActividad=false;

				    	//PARTE WHILE ESCRIBE LA LINEA 
					    $cadena_escribir_linea="";
					    $cont_orden_campo_pyp=0;
					    while(
					    	($cont_orden_campo_pyp<=118 && $this->tipo_entidad_que_efectua_el_cargue!="agrupado_ips120")
					    	|| ($cont_orden_campo_pyp<=119 && $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120")
					    	)//fin condicion
					    {
						    if($cadena_escribir_linea!="")
						    {
							    $cadena_escribir_linea.="|";
						    }//fin if
						    if($cont_orden_campo_pyp!=1)
						    {
						    	if($cont_orden_campo_pyp==119)
						    	{
						    		$cadena_escribir_linea.=$resultado["campo_extra_120_eapb_regis"];
						    	}//fin if
						    	else
						    	{
							    $cadena_escribir_linea.=$resultado["campo_".$cont_orden_campo_pyp];
								}//fin else
						    }//fin ele
						    else
						    {
							    $cadena_escribir_linea.=$cont_resultados;
						    }//fin else
						    $cont_orden_campo_pyp++;
					    }//fin while
					    //PARTE FIN WHILE ESCRIBE LA LINEA
					    	

					    //CRITERIOS EXCLUSION REGISTRO NO PRESENTA ACTIVIDAD
					    $array_campos_verificacion_no_actividad=array();
					    $array_campos_verificacion_no_actividad=explode("|", $cadena_escribir_linea);

					    $mensaje_debug_no_actividad="";
					    $mensaje_debug_no_actividad.= "Entro a los campos al detectar no actividad en: ";

					    $contadorNoActividad=0;
					    
					    $numeroCampoActualNoActividad=14;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="21" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="0"
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if
					    
					    
					    $numeroCampoActualNoActividad=15;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="21" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="0"
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if
					    

					    $numeroCampoActualNoActividad=16;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="21" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="0"
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if
					    

					    $numeroCampoActualNoActividad=17;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="21" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="0"
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if
					    

					    $numeroCampoActualNoActividad=18;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="21" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if
					    

					    $numeroCampoActualNoActividad=19;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="21" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="0"
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if
					    

					    $numeroCampoActualNoActividad=20;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="21" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if
					    

					    $numeroCampoActualNoActividad=21;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="21" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if
					    

					    $numeroCampoActualNoActividad=22;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="21" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="0"
						|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="3"
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if
					    

					    $numeroCampoActualNoActividad=23;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="21" 
					       || trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="2"
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if
					    

					    $numeroCampoActualNoActividad=24;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="21" 
					       || trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="2"
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=25;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="21" 
					       || trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="7"
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=26;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="21" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="0"
						|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="2"
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=27;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="21" 
					       || trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="2"
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=28;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="21" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=29;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1800-01-01" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=30;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="999" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=31;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1800-01-01" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=32;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="999" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=33;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1845-01-01" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=34;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="999" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="0" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=35;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="22" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="0"
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="20"
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=36;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="22" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="0"
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="20"
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=37;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="22" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="0"
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="20"
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=38;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="22" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="0"
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="20"
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=39;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="22" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="0"
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="20"
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=40;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="22" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="0"
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="20"
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=41;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="22" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="0"
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="20"
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=42;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="22" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="0"
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="20"
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=43;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="22" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="0"
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="20"
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=44;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="22" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="0"
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="20"
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=45;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="22" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="0"
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="20"
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=46;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="22" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="0"
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="20"
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=47;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="22" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="0"
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="20"
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=48;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="22" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="0"
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=49;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1845-01-01" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=50;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1845-01-01" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=51;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1845-01-01" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1835-01-01" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=52;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1845-01-01" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1835-01-01" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=53;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1845-01-01" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1835-01-01" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=54;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="21" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="0"
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="20"
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=55;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1845-01-01" 
					       || trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1835-01-01" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=56;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1845-01-01" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1835-01-01" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=57;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="0"
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="999"
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=58;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1845-01-01" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=59;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="21" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="0"
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="20"
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=60;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="21" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="0"
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="20"
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=61;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="21" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="0"
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="20"
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=62;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1845-01-01" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1835-01-01" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=63;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1845-01-01" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1835-01-01" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=64;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1845-01-01" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=65;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1845-01-01" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1835-01-01" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=66;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1845-01-01" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1835-01-01" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=67;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1845-01-01" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1835-01-01" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if
					    
					    $numeroCampoActualNoActividad=68;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1845-01-01" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1835-01-01" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=69;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1845-01-01" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1835-01-01" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=70;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="21" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="0"
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="20"
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=71;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="21" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="0"
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="20"
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=72;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1845-01-01" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1835-01-01" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=73;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1845-01-01" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1835-01-01" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=74;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="999" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="0"
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="993"
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=75;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1845-01-01" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1835-01-01" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=76;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1845-01-01" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1835-01-01" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=77;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="22" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="0"
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="20"
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=78;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1845-01-01" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1835-01-01" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=79;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="22" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="0" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=80;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1845-01-01" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1835-01-01" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=81;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="22" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="0" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=82;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1845-01-01" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1835-01-01" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=83;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="22" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="0" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=84;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1845-01-01" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1835-01-01" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=85;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="22" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="0" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=86;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="22" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="0" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="20" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=87;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1845-01-01" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1835-01-01" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=88;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="999" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="0" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=89;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="999" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="0" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=90;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="999" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="0" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=91;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1845-01-01" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1835-01-01" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=92;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="999" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="0" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=93;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1845-01-01" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1835-01-01" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=94;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="999" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="0" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=95;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="999" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="0" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=96;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1845-01-01" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1835-01-01" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=97;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="999" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="0" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=98;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="999" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="0" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=99;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1845-01-01" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1835-01-01" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=100;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1845-01-01" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1835-01-01" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=101;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="999" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="0" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=102;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="999" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="0" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=103;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1845-01-01" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1835-01-01" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=104;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="0" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=105;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1845-01-01" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1835-01-01" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=106;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1845-01-01" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1835-01-01" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=107;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="999" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="0" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=108;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1845-01-01" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1835-01-01" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=109;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="999" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="0" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=110;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1845-01-01" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1835-01-01" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=111;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1845-01-01" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1835-01-01" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=112;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1845-01-01" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1835-01-01" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=113;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="22" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="4" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=114;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="22" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="0" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="20" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=115;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="22" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="0" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="20" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=116;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="22" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="0" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="20" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=117;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="22" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="0" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="20" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad.", ";
					    }//fin if

					    $numeroCampoActualNoActividad=118;
					    if(trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1845-01-01" 
					    	|| trim($array_campos_verificacion_no_actividad[$numeroCampoActualNoActividad])=="1835-01-01" 
					    )//fin condicion
					    {
					    	$contadorNoActividad++;$mensaje_debug_no_actividad.=$numeroCampoActualNoActividad."<br>";
					    }//fin if

					    if($contadorNoActividad==105 )//tiene en cuenta desde el 14 en adelante
					    {
					    	$boolExcluidoPorNoPresentarActividad=true;
					    	$numeroRegistrosExcluidosPorNoPresentarActividad++;
					    }//fin if
					    //FIN CRITERIOS EXCLUSION REGISTRO NO PRESENTA ACTIVIDAD

					    //echo $mensaje_debug_no_actividad;

					    //echo "contadorNoActividad: ".$contadorNoActividad."<br>";
					    
					    //PARTE MENSAJE FASE 3
					    $fecha_y_hora_finalizacion= date('Y-m-d H:i:s');
					    //porcentaje
					    $muestra_mensaje_nuevo_csv=false;
					    $porcentaje_csv=intval((($cont_linea)*100)/($numero_registros));
					    if($porcentaje_csv!=$cont_porcentaje_csv || ($porcentaje_csv==0 && ($cont_linea)==1) || $porcentaje_csv==100)
					    {
					     $cont_porcentaje_csv=$porcentaje_csv;
					     $muestra_mensaje_nuevo_csv=true;
					    }
					    //fin porcentaje
					    
						$mensaje_perm_estado_reg_recuperados="";
					    					    
					    if($muestra_mensaje_nuevo_csv)
					    {
					    	$mensaje_perm_estado_reg_recuperados.="Por favor espere, $cont_linea registros recuperados de $numero_registros.<br>Se han excluido ( $numeroRegistrosExcluidosPorNoPresentarActividad ) Registros hasta el momento, por no presentar realizacion de actividad.<br>$fecha_y_hora_finalizacion";
							
							//ACTUALIZA ESTADO DEL ARCHIVO
							$msg_a_bd="";
							$msg_a_bd=$mensaje_perm_estado." ".$mensaje_perm_estado_reg_dupl." ".$mensaje_perm_estado_reg_recuperados;
							
							
						    $query_update_esta_siendo_procesado="";
						    $query_update_esta_siendo_procesado.=" UPDATE gioss_4505_esta_reparando_ar_actualmente ";
						    $query_update_esta_siendo_procesado.=" SET mensaje_estado_registros='$msg_a_bd' ";
						    $query_update_esta_siendo_procesado.=" WHERE fecha_corte_archivo_en_reparacion='".$this->cadena_fecha_corte."' ";
						    if($this->cod_registro_especial_pss=="AGRUP_EAPB")
						    {
							$query_update_esta_siendo_procesado.=" AND codigo_entidad_reportadora='".$this->cod_eapb_global."' ";
						    }
						    else
						    {
							$query_update_esta_siendo_procesado.=" AND codigo_entidad_reportadora='".$this->cod_registro_especial_pss."' ";
						    }		    
						    $query_update_esta_siendo_procesado.=" AND nombre_archivo='".$nombre_archivo4505."'  ";
						    $query_update_esta_siendo_procesado.=" AND nick_usuario='".$nick_user."'  ";
						    $query_update_esta_siendo_procesado.=" AND fecha_validacion='".$fecha_actual."'  ";
						    $query_update_esta_siendo_procesado.=" AND hora_validacion='".$tiempo_actual."'  ";
						    $query_update_esta_siendo_procesado.=" ; ";
						    $error_bd="";
						    $bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($query_update_esta_siendo_procesado, $error_bd);
						    if($error_bd!="")
						    {
							    if($fue_cerrada_la_gui2==false)
							    {
								    echo "<script>alert('error al actualizar el estado actual de reparacion en tiempo real  4505 ');</script>";
							    }//fin if
						    }//fin if
							
							//FIN ACTUALIZA ESTADO DEL ARCHIVO
					    }//fin if

					    if(connection_aborted()==false && $muestra_mensaje_nuevo_csv)
					    {
						echo "<script>document.getElementById('estado_validacion').innerHTML='$mensaje_perm_estado $mensaje_perm_estado_reg_recuperados ';</script>";
						ob_flush();
						flush();
					    }//fin if

					    
					    //FIN PARTE MENSAJE FASE 3

					    
					    if($boolExcluidoPorNoPresentarActividad===false)
					    {
					    	fwrite($file_archivo4505_corregido_sin_duplicados, "\n".$cadena_escribir_linea);
					    $cont_resultados++;
					    $cont_linea++;
						}
						else
						{	
							//CREACION ARCHIVO REGISTROS EXCLUIDOS POR NO PRESENTAR REGISTRO DE ACTIVIDAD
							$ruta_archivo_registros_excluidos_por_no_presentar_actividad_actual=$this->global_ruta_temporales."registros_no_presentan_registro_actividad.txt";
							$eapb_para_nombre_archivo="";
							if(isset($array_campos_verificacion_no_actividad[119])==true)
							{
								$arrayCaracteresEspecialesNoPermitidosDirectorios=array(" ","-","/","\\","|","*","<",">","?","¿","!","¡");
								$eapb_para_nombre_archivo=str_replace($arrayCaracteresEspecialesNoPermitidosDirectorios, "", trim($array_campos_verificacion_no_actividad[119]) );
								$ruta_archivo_registros_excluidos_por_no_presentar_actividad_actual=$this->global_ruta_temporales."registros_no_presentan_registro_actividad".$eapb_para_nombre_archivo.".txt";
							}//fin if
							if(in_array($ruta_archivo_registros_excluidos_por_no_presentar_actividad_actual, $this->ruta_archivo_registros_excluidos_por_no_registro_de_actividad)==false)
							{
						    	$this->ruta_archivo_registros_excluidos_por_no_registro_de_actividad[]=$ruta_archivo_registros_excluidos_por_no_presentar_actividad_actual;
						    	$file_registros_excluidos_por_no_registro_de_actividad=fopen($ruta_archivo_registros_excluidos_por_no_presentar_actividad_actual, "w") or die("fallo la creacion del archivo");
						    	fwrite($file_registros_excluidos_por_no_registro_de_actividad, "Archivo Registros Excluidos Por No Presentar Realizacion De Actividad por Riesgo o Intervencion Para La Aseguradora $eapb_para_nombre_archivo ");
						    	fclose($file_registros_excluidos_por_no_registro_de_actividad);
							}//fin if inserta en array de rutas si no existe						    
						    //FIN CREACION ARCHIVO REGISTROS EXCLUIDOS POR NO PRESENTAR REGISTRO DE ACTIVIDAD

						    $array_campos_verificacion_no_actividad[1]=$numeroRegistrosExcluidosPorNoPresentarActividad;
						    if(isset($array_campos_verificacion_no_actividad[119])==true)
						    {
						    	unset($array_campos_verificacion_no_actividad[119]);
						    }//fin if
						    $cadena_escribir_linea_excluido_no_actividad=implode("|", $array_campos_verificacion_no_actividad);

							$file_registros_excluidos_por_no_registro_de_actividad= fopen($ruta_archivo_registros_excluidos_por_no_presentar_actividad_actual, "a") or die("fallo la creacion del archivo");
							fwrite($file_registros_excluidos_por_no_registro_de_actividad, "\n".$cadena_escribir_linea_excluido_no_actividad);
							fclose($file_registros_excluidos_por_no_registro_de_actividad);
						}//fin else	
				    }//fin foreach
				    
				    fclose($file_archivo4505_corregido_sin_duplicados);
				    
				    
				    
			    }//fin if hayo resultados
			    
			    $contador_offset+=$numero_registros_bloque;
		    
		    }//fin while
		    
		    //RE-ESCRIBE PRIMERA LINEA DE 4505 PARA ADICIONAR EL NUMERO DE REGISTROS segunda vez despues de excluir
		    $numero_registros_definitivo_sin_excluidos="".($cont_linea-1);
		    $file_archivo4505_corregido_sin_duplicados=fopen($ruta_archivo4505_corregido_sin_duplicados, "c") or die("fallo la creacion del archivo");
		    //parte primera linea
		    $consecutivo_anterior=intval($array_linea_procesada[4]);
		    $numero_caracteres_consecutivo_anterior=strlen("".$numero_registros);
		    $numero_caracteres_consecutivo_nuevo=strlen($numero_registros_definitivo_sin_excluidos);
		    if($numero_caracteres_consecutivo_anterior>$numero_caracteres_consecutivo_nuevo)
		    {
		    	while($numero_caracteres_consecutivo_anterior>$numero_caracteres_consecutivo_nuevo)
		    	{
		    		$numero_registros_definitivo_sin_excluidos.=" ";
		    		$numero_caracteres_consecutivo_nuevo=strlen($numero_registros_definitivo_sin_excluidos);
		    	}//fin while
		    }//fin if
		    //echo "<script>alert('$numero_caracteres_consecutivo_anterior $consecutivo_anterior $numero_caracteres_consecutivo_nuevo ".($this->consecutivo_fixer-1)."');</script>";
		    
		    fwrite($file_archivo4505_corregido_sin_duplicados, $array_linea_procesada[0]."|".$array_linea_procesada[1]."|".$array_linea_procesada[2]."|".$array_linea_procesada[3]."|".$numero_registros_definitivo_sin_excluidos );
		    
		    //fin part primear linea
		    fclose($file_archivo4505_corregido_sin_duplicados);
		    //FIN RE-ESCRIBE PRIMERA LINEA DE 4505 PARA ADICIONAR EL NUMERO DE REGISTROS segunda vez despues de excluir
		    
		    
		    //reescribe primera linea para escribir el numero de registros
		    
		    //reescribe primera linea para escribir el numero de registros
		    
		    //FIN PARTE ESCRIBE CSV
		
		    //FIN FASE DE VERIFICACION Y CORRECCION DE DUPLICADOS
		    $fecha_y_hora_finalizacion= date('Y-m-d H:i:s');
		    if(connection_aborted()==false)
		    {
			if($mensaje_perm_estado_reg_dupl=="")
			{
				echo "<script>alert('No tiene registros duplicados');</script>";
			}
			echo "<script>document.getElementById('mensaje').style.textAlign='center';</script>";
			echo "<script>document.getElementById('mensaje').innerHTML='<p  align=\"center\">$mensaje_perm_estado $mensaje_perm_estado_reg_dupl $mensaje_perm_estado_reg_recuperados Se ha terminado de corregir el archivo y este ha terminado con un total de ".($cont_linea-1)." registros  de ".($lineas_del_archivo-1)." lineas originales del archivo R4505.</p><br>Se han excluido ( $numeroRegistrosExcluidosPorNoPresentarActividad ) Registros hasta el momento, por no presentar realizacion de actividad.<br>$fecha_y_hora_finalizacion';</script>";
			
			echo "<script>document.getElementById('tabla_estado_1').style.position='relative';</script>";
			
			//echo "<script>document.getElementById('loading').style.display='none';</script>";
			//echo "<script>document.getElementById('estado_validacion').style.display='none';</script>";
			
			ob_flush();
			flush();
		    }
		    
		    //borrando vistas
		    $sql_borrar_vistas="";
		    //$sql_borrar_vistas.=" DROP VIEW $nombre_vista_corregidos_con_duplicados ; ";
		    $sql_borrar_vistas.=" DROP VIEW $nombre_vista_consulta_definitiva_corregidos ; ";		    
		    $error_bd="";		
		    $bool_funciono=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($sql_borrar_vistas, $error_bd);		
		    if($error_bd!="")
		    {
			if(connection_aborted()==false)
			{
			    echo "<script>alert('error al borrar vistas');</script>";
			}
		    }
		    //fin borrando vistas
		    
		    $ruta_zip_archivos_de_registros_excluidos_por_no_presentar_actividad=$this->global_ruta_temporales."zipExcluidosNoActividadReportada.zip";
		    $resultado_zip_excluidos_no_actividad_presentada=create_zip($this->ruta_archivo_registros_excluidos_por_no_registro_de_actividad,$ruta_zip_archivos_de_registros_excluidos_por_no_presentar_actividad);
		    
		    //GENERANDO ARCHIVO ZIP
		    $archivos_a_comprimir=array();
		    $archivos_a_comprimir[]=$ruta_archivo4505_corregido;
		    $archivos_a_comprimir[]=$ruta_archivo4505_corregido_sin_duplicados;
		    if($bool_hubo_errores_en_la_correccion==true)
		    {
			 //$archivos_a_comprimir[]=$ruta_detalles_correccion_campos;
		    }
		    $archivos_a_comprimir[]=$ruta_cambios_duplicados_campos;//listo
		    $archivos_a_comprimir[]=$ruta_cambios_realizados_campos;//listo?
		    $archivos_a_comprimir[]=$ruta_cambios_real_dupl_campos_2;//casi
		    $archivos_a_comprimir[]=$this->ruta_archivo_fecha_nacimiento_invalida;
		    $archivos_a_comprimir[]=$this->ruta_archivo_afiliado_no_existe;

		    if(file_exists($ruta_zip_archivos_de_registros_excluidos_por_no_presentar_actividad)==true)
		    {
		    	$archivos_a_comprimir[]=$ruta_zip_archivos_de_registros_excluidos_por_no_presentar_actividad;
		    }//fin if

		    $ruta_zip=$ruta_temporales.$nombre_archivo_sin_extension."_correccion_".$fecha_para_archivo.'.zip';
		    $result_zip = create_zip($archivos_a_comprimir,$ruta_zip);
		    
		    if(connection_aborted()==false)
		    {
			echo "<script>var ruta_zip= '$ruta_zip'; </script>";
			ob_flush();
			flush();
		    }
		    
		    //FIN GENERANDO ARCHIVO ZIP
		    
		    
		    
		    $hubo_errores_al_finalizar_la_validacion=false;
		    //parte bandera terminado
		    if ($se_realizo_correccion_a_campos_con_errores_del_archivo==false) 
		    {
			    $hubo_errores_al_finalizar_la_validacion=false;
		    }//fin if no hubo errores de escritura en la base de datos ni en los campos
			    
		    if($se_realizo_correccion_a_campos_con_errores_del_archivo==true)
		    {
			    $hubo_errores_al_finalizar_la_validacion=true;
		    }//fin si hubo errores
		    
		    if ($hubo_errores_al_finalizar_la_validacion) 
		    {
			    $mensaje ="";
			    $mensaje .= "El archivo se ha reparado.  <br>";
			    $mensaje .= "El comprimido con los archivos de correccion se han enviado al correo " . $_SESSION['correo'] . "<br>";			
			    $mensaje .= "<input type=\'button\' value=\'Haga clic aqui para descargar el comprimido con el archivo reparado y detalles de la reparacion\' class=\'btn btn-success color_boton\' onclick=\'download_inconsistencias_campos(ruta_zip);\'/>";
			    
			    if(connection_aborted()==false)
			    {
				echo "<script>document.getElementById('div_mensaje_error').style.display='inline';</script>";
				echo "<script>document.getElementById('parrafo_error').innerHTML=\"".$this->procesar_mensaje_query($mensajes_error_bd)." $mensaje \";</script>";
				
				ob_flush();
				flush();
			    }
			    
		    }//fin if hubo errores obligatorios
		    else
		    {
			    $mensajeExito ="";
			    $mensajeExito .= "El archivo se ha reparado. <br>";
			    $mensajeExito .= "El comprimido con los archivos de correccion se han enviado al correo " . $_SESSION['correo'] . "<br>";
			    $mensajeExito .= "<input type=\'button\' value=\'Haga clic aqui para descargar el comprimido con el archivo reparado y detalles de la reparacion\' class=\'btn btn-success color_boton\' onclick=\'download_inconsistencias_campos(ruta_zip);\'/>";
			    
			    if(connection_aborted()==false)
			    {
				echo "<script>document.getElementById('div_mensaje_exito').style.display='inline';</script>";
				echo "<script>document.getElementById('parrafo_exito').innerHTML='$mensajeExito';</script>";
				
				ob_flush();
				flush();
			    }
			    
		    }//fin else  exito, no hubo errores obligatorios


		    //limpia tabla indexador
		    $nombre_tabla_indexador_duplicados="gioss_indexador_duplicados_del_reparador_4505";
		    if($this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120")
			{
				//susa una tabla de indexador distinta pero para duplicados teniendo en cuenta el campo extra 120 con codigo eapb
				$nombre_tabla_indexador_duplicados="gioss_indexador_dupl_del_reparador_4505_agrup_ips";
			}//fin if

		    $query_insert_updt_en_indexador="";
			$query_insert_updt_en_indexador.=" DELETE FROM ";
			$query_insert_updt_en_indexador.=" $nombre_tabla_indexador_duplicados ";
			$query_insert_updt_en_indexador.=" WHERE  ";
			$query_insert_updt_en_indexador.="tipo_id_usuario='".$tipo_id."'";				
			$query_insert_updt_en_indexador.=" AND ";
			$query_insert_updt_en_indexador.="id_usuario='".$identificacion."'";
			$query_insert_updt_en_indexador.=" AND ";
			$query_insert_updt_en_indexador.="nick_usuario='".$nick_user."'";
			$query_insert_updt_en_indexador.=" AND ";
			$query_insert_updt_en_indexador.="fecha_corte_reporte='".$this->cadena_fecha_corte."'";
			$query_insert_updt_en_indexador.=" AND ";
			$query_insert_updt_en_indexador.="fecha_de_generacion='".$fecha_actual."'";
			$query_insert_updt_en_indexador.=" AND ";
			$query_insert_updt_en_indexador.="hora_generacion='".$tiempo_actual."'";
			$query_insert_updt_en_indexador.=" AND ";
			$query_insert_updt_en_indexador.="codigo_entidad_eapb_generadora='".$this->cod_eapb_global."'";
			$query_insert_updt_en_indexador.=" AND ";
			$query_insert_updt_en_indexador.="codigo_entidad_prestadora='".$this->cod_registro_especial_pss."'";
			$query_insert_updt_en_indexador.=" AND ";
			$query_insert_updt_en_indexador.="nombre_archivo_pyp='".$nombre_archivo_sin_extension."'";
			$query_insert_updt_en_indexador.=" ; ";
			$error_bd_seq="";		
			$bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($query_insert_updt_en_indexador, $error_bd_seq);
			if($error_bd_seq!="")
			{
			    $mensajes_error_bd.=" ERROR Al limpiar la tabla $nombre_tabla_indexador_duplicados ".$this->procesar_mensaje($error_bd_seq).".<br>";
			    
			    if($fue_cerrada_la_gui==false)
			    {
				    echo "<script>alert('ERROR Al limpiar en la tabla $nombre_tabla_indexador_duplicados  ".$this->procesar_mensaje($error_bd_seq)."');</script>";
			    }
			}
			//limpia tabla indexador
		    
		    
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
		    $query_delete_log_dupl_anterior.=" fecha_corte_reporte='".$this->cadena_fecha_corte."' ";    
		    $query_delete_log_dupl_anterior.=" AND ";
		    $query_delete_log_dupl_anterior.=" codigo_entidad_eapb_generadora='".$this->cod_eapb_global."' ";
		    $query_delete_log_dupl_anterior.=" AND ";
		    $query_delete_log_dupl_anterior.=" tipo_reporte='PYP' ";
		    $query_delete_log_dupl_anterior.=" AND ";
		    $query_delete_log_dupl_anterior.=" reparacion_o_consolidado='REPARACION' ";
		    if($this->cod_registro_especial_pss=="AGRUP_EAPB")
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
		    $query_delete_log_dupl_anterior.=" nombre_archivo='".$nombre_archivo_sin_extension."' ";
		    $query_delete_log_dupl_anterior.=" ; ";
		    $error_bd_seq="";		
		    $bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($query_delete_log_dupl_anterior, $error_bd_seq);
		    if($error_bd_seq!="")
		    {
			    echo "<script>alert('ERROR Al borrar de tabla gioss_log_dupl ".$this->procesar_mensaje($error_bd_seq)." ');</script>";
			    
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
			    $bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($query_subir_registro_corregido, $error_bd_seq);
			    if($error_bd_seq!="")
			    {
				    echo "<script>alert('ERROR Al subir en la tabla gioss_log_dupl ".$this->procesar_mensaje($error_bd_seq)." ');</script>";
				    
			    }
			}//fin if longitud es correcta
			
			$cont_lineas_log_dupl++;
		    }		    
		    fclose($lectura_archivo_log_dupl);		    
		    
		    //FIN SUBE A GIOSS_LOG_DUPL PARA REPORTES FUTUROS
		    
		    
		    //YA NO ESTA EN USO EL ARCHIVO
		    
		    $query_update_esta_siendo_procesado="";
		    $query_update_esta_siendo_procesado.=" UPDATE gioss_4505_esta_reparando_ar_actualmente ";
		    $query_update_esta_siendo_procesado.=" SET esta_ejecutando='NO',";
		    $query_update_esta_siendo_procesado.=" ruta_archivo_descarga='$ruta_zip' ";
		    /*
		    if($ruta_zip_filtrado!="")
		    {
			//lleva la coma aca por si no esta vacio
			$query_update_esta_siendo_procesado.=" , ruta_archivo_descarga_filtrado='$ruta_zip_filtrado' ";
		    }
		    */
		    $query_update_esta_siendo_procesado.=" WHERE fecha_corte_archivo_en_reparacion='".$this->cadena_fecha_corte."' ";
		    if($this->cod_registro_especial_pss=="AGRUP_EAPB")
		    {
			$query_update_esta_siendo_procesado.=" AND codigo_entidad_reportadora='".$this->cod_eapb_global."' ";
		    }
		    else
		    {
			$query_update_esta_siendo_procesado.=" AND codigo_entidad_reportadora='".$this->cod_registro_especial_pss."' ";
		    }		    
		    $query_update_esta_siendo_procesado.=" AND nombre_archivo='".$nombre_archivo4505."'  ";
		    $query_update_esta_siendo_procesado.=" AND nick_usuario='".$nick_user."'  ";
		    $query_update_esta_siendo_procesado.=" AND fecha_validacion='".$fecha_actual."'  ";
		    $query_update_esta_siendo_procesado.=" AND hora_validacion='".$tiempo_actual."'  ";
		    $query_update_esta_siendo_procesado.=" ; ";
		    $error_bd="";
		    $bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($query_update_esta_siendo_procesado, $error_bd);
		    if($error_bd!="")
		    {
			    if(connection_aborted()==false)
			    {
				    echo "<script>alert('error al actualizar el estado actual de reparacion en tiempo real  4505 ');</script>";
			    }
		    }
		    //FIN YA NO ESTA EN USO EL ARCHIVO
		    
		    
		    $this->ruta_archivos_inconsistencias_para_email=$ruta_zip;
		    
		    $conexionbd->cerrar_conexion();
		    
		    //parte retorno de la funcion 
		    if ($se_realizo_correccion_a_campos_con_errores_del_archivo==false) 
		    {
			    return false;
		    }//fin if no hubo errores de escritura en la base de datos ni en los campos
			    
		    if($se_realizo_correccion_a_campos_con_errores_del_archivo==true)
		    {
			    return true;
		    }//fin si hubo errores
		    
		}//fin if verificacion
		//FIN SI LA VERIFICACION DEL LA LINEA INICIAL FUE CORRECTA
		
    }//fin funcion CorreccionArchivo4505_verificacion_escritura

}

?>
