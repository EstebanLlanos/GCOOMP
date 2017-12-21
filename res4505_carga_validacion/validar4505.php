<?php
ignore_user_abort(true);
set_time_limit(2592000);
ini_set('max_execution_time', 2592000);
ini_set('memory_limit', '2000M');

/*
ini_set('display_errors',1); 
 error_reporting(E_ALL);
 */
include_once ('../utiles/ruta_temporales_files.php');

 
include_once ('../utiles/clase_coneccion_bd.php');
include ("../librerias_externas/PHPMailer/class.phpmailer.php");
include ("../librerias_externas/PHPMailer/class.smtp.php");
require_once ("../librerias_externas/PHPExcel/PHPExcel.php");
require_once ("../librerias_externas/PHPExcel/PHPExcel/Writer/Excel2007.php");
require_once '../utiles/queries_utiles_bd.php';

require_once '../utiles/configuracion_global_email.php';

require_once '../utiles/crear_zip.php';

require_once '../res4505/criterios_validacion_posafil_4505.php';

class LecturaArchivo extends criterios_validacion_posafil_4505
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
    
    var $old_fecha_de_corte_periodo_mensual="";
    
    var $secuencia_actual_para_email="";
    var $ruta_archivos_inconsistencias_para_email="";
    
    var $nombre_archivo_generado="";
    
    var $verificacion_inicial_global=true;
    
    var $cod_eapb_global="";
    
    var $tipo_entidad_que_efectua_el_cargue="individual_ips";
    var $cod_dpto_filtro="";
    var $cod_mpio_filtro="";
    
    var $nick_user="";
    
    var $ruta_archivo_filtrado_para_email;
    
    var $tipo_periodo_tiempo_global;
    
    var $USA_SMTP_CONFIGURACION_CORREO_fromclass;
    var $REQUIERE_AUTENTIFICACION_EMAIL_fromclass;
    var $PUERTO_CONF_EMAIL_fromclass;
    var $USUARIO_CONF_EMAIL_fromclass;
    var $PASS_CONF_EMAIL_fromclass;
    var $HOST_CONF_EMAIL_fromclass;
    var $SMTPSECURE_CONF_EMAIL_CE_fromclass;
    var $SMTPAUTH_CONF_EMAIL_CE_fromclass;
    
    var $fecha_actual_global;
    var $tiempo_actual_global;


    var $global_ruta_afiliados_no_registrados;
    var $global_ruta_registros_con_afiliados_modificados;
    var $global_ruta_registros_con_afiliados_modificados_sexo_diferentes;
    var $global_ruta_registros_con_afiliados_modificados_fecha_nacimiento_diferentes;
    var $global_ruta_archivo_plano_nuevo_original_con_sexo_fecha_nacimiento_corregidos;    
    var $global_ruta_registros_con_afiliados_modificados_y_campos_estadisticas_adicionales;
    var $global_ruta_estructura_mala;

    var $global_array_rutas_agrupados_ips_para_cada_eapb;
    var $global_array_eapb_para_carpetas;

    var $global_ruta_reporte_registros_por_ips;

    var $global_ruta_reporte_calificacion_campos;

    var $GlobalArraySoporteAdministrativoNoActividad;
    var $globalRutaTemporalesEscribirArchivos;
    var $globalNombreCampos;

    var $global_consecutivo_afiliados_no_registrados=1;
    var $global_consecutivo_solo_registros_con_sexo_fecha_nacimiento_corregidos=1;
    var $global_consecutivo_solo_registros_con_sexo_dif_corregidos=1;
    var $global_consecutivo_solo_registros_con_fecha_nacimiento_dif_corregidos=1;
    var $global_consecutivo_nuevo_original_con_sexo_fecha_nacimiento_corregidos=1;
    var $global_consecutivo_solo_registros_solo_afiliados_con_campos_adicionales=1;
    var $global_consecutivo_estructura_mala=1;

    var $global_array_consecutivo_agrupados_ips_para_cada_eapb;

    var $codigo_habilitacion_para_inconsistencias="";

    var $se_creo_tabla_indice=false;

    var $fecha_inicial_para_analisis;

    var $regional_global;

    var $array_campos_con_datos_afiliados=array();

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

    function lecturaPyP($file, $nit_entidad_prestadora="0000",$val1_modulo_informacion="",
			$val2_tema_informacion="",$val3_tipo_id_entidad_reportadora="",
			$val4_tipo_regimen="",$val5_consecutivo_nombre_archivo="",$nit_epba="",$crespss="",
			$string_fecha_corte="",$nombre_archivo_param="nombre_prueba",$codPeriodo="0",
			$cod_eapb_local="",
			$tipo_entidad_que_efectua_el_cargue_local="individual_ips",
			$cod_dpto_filtro_local="none",
			$cod_mpio_filtro_local="none",
			$nick_user_local="user_temp",
			$tipo_periodo_tiempo="trimestral"
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
		
		$this->tipo_entidad_que_efectua_el_cargue=$tipo_entidad_que_efectua_el_cargue_local;
		$this->cod_dpto_filtro=$cod_dpto_filtro_local;
		if($cod_mpio_filtro_local!="none" && $cod_dpto_filtro_local!="none")
		{
		    $this->cod_mpio_filtro=$cod_mpio_filtro_local;
		}
		else if($cod_dpto_filtro_local!="none")
		{
		    $this->cod_mpio_filtro="000";
		}
		
		$this->nick_user=$nick_user_local;
		
		$this->tipo_periodo_tiempo_global=$tipo_periodo_tiempo;
		
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
            $bandera_email = $this->ValidarArchivo4505($file[$j]['archivo'], $file[$j]['nombre_archivo'], $file[$j]['tipo_archivo'], sizeof($file));

            if ($bandera_email) 
			{
                $flag_enviar_mail = 1;
            }
        }
		
		//echo "<script>alert('Ruta archivo inconsistencias:".$this->ruta_archivos_inconsistencias_para_email.", numero secuencia: ".$this->secuencia_actual_para_email."');</script>";
		$hay_coneccion_a_internet=$this->is_connected_to_internet();
		
		/*
		if($hay_coneccion_a_internet==true)
		{
		    echo "<script>alert('Hay coneccion a internet');</script>";
		}
		*/
		
		if($this->verificacion_inicial_global==true && $hay_coneccion_a_internet==true)
		{
			if ($flag_enviar_mail == 1) 
			{
		
			    // inicio envio de mail
		
				//PARTE MENSAJE EMAIL
			    $mensaje_email_body="";
		    	$mensaje_email_body.="Cordial saludo,\n El sistema ha determinado que su archivo(s)  contiene diversos errores. <br> El numero de secuencia asignado fue ".$this->secuencia_actual_para_email." <strong>GIOSS</strong>.";

		    	$adjuntar=true;
		    	if($this->ruta_archivos_inconsistencias_para_email=="" 
		    		|| file_exists($this->ruta_archivos_inconsistencias_para_email)==false
		    	)
		    	{
		    		$adjuntar=false;
		    		$mensaje_email_body.="\nEl archivo comprimido no se pudo adjuntar debido a que su tamano sobrepasa los archivos sobrepasan los limites del formato zip y por ende no pudieron se comprimidos.";
		    	}//fin if
		    	else if(file_exists($this->ruta_archivos_inconsistencias_para_email)==true 
		    		&& filesize($this->ruta_archivos_inconsistencias_para_email)>26214400
		    	)
		    	{
		    		$adjuntar=false;
		    		$mensaje_email_body.="\nEl archivo comprimido no se pudo adjuntar debido a que su tamano sobrepasa los archivos sobrepasan los limites del servidor de correos para archivos adjuntos. Tamano Actual ".filesize($this->ruta_archivos_inconsistencias_para_email)."  BYTES ";
		    	}//fin else if
		    	else if (file_exists($this->ruta_archivos_inconsistencias_para_email)==true )
		    	{
		    		$adjuntar=true;//aunque no es necesario
		    		$mensaje_email_body.="\nEl archivo comprimido posee un Tamano Actual ".filesize($this->ruta_archivos_inconsistencias_para_email)."  BYTES ";
		    	}//fin else if
		    	//FIN PARTE MENSAJE EMAIL
		
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
			    $mail->Subject = "Inconsistencias PyP 4505";
			    $mail->AltBody = "Cordial saludo,\n El sistema ha determinado que su archivo(s)  contiene diversas inconsistencias,\n El numero de secuencia asignado fue ".$this->secuencia_actual_para_email;
			    $mail->MsgHTML($mensaje_email_body);
			    if($adjuntar==true)
			    {
					$mail->AddAttachment($this->ruta_archivos_inconsistencias_para_email);
			    }//fin if
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
			    return array("hubo_errores"=>true,"num_seq_def"=>$this->secuencia_actual_para_email);
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
				$mail->Subject = "Inconsistencias PyP 4505";
				$mail->AltBody = "Cordial saludo,\n El sistema ha determinado que no se encuentran inconsistencias 
										   en los archivos cargados correspondientes a la secuencia 
										   de validacion: " . $this->secuencia_actual_para_email . ".";
	
				$mail->MsgHTML("Cordial saludo,\n El sistema ha determinado que no se encuentran inconsistencias obligatorias 
										 en los archivos cargados correspondientes a la secuencia 
										 de validacion: " . $this->secuencia_actual_para_email . " (Aunque puden haber inconsistencias informativas para que mejore la calidad de sus datos).<strong>GIOSS</strong>.");
										 
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
	
				return array("hubo_errores"=>false,"num_seq_def"=>$this->secuencia_actual_para_email);
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
    
    function corrector_formato_fecha($campo_fecha,$es_fecha_nacimiento=false,$campo_especial=-1,$campo_debug=0)
    {
	date_default_timezone_set ("America/Bogota");
	
	$fecha_corte=explode("-",$this->fecha_de_corte_periodo);
	$date_de_corte=date($fecha_corte[0]."-".$fecha_corte[1]."-".$fecha_corte[2]);
     
	$fecha_corregida="";
	$fecha_corregida=trim($campo_fecha);
	$fecha_corregida=str_replace("/","-",$fecha_corregida);
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
						//$fecha_corregida="1800-01-01";
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
			     	//$fecha_corregida="1800-01-01";
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
			     	//$fecha_corregida="1800-01-01";
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
			 	//$fecha_corregida="1800-01-01";
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
		 //$fecha_corregida="1800-01-01";
		}
		else
		{
		 //$fecha_corregida=$date_de_corte;
		}
	    }//fin else
	}
	else
	{
	    if($es_fecha_nacimiento==false)
	    {
	     //$fecha_corregida="1800-01-01";
	    }
	    else
	    {
	     //$fecha_corregida=$date_de_corte;
	    }
	}//fin else
	
	/*
	if($es_fecha_nacimiento==false)
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
	      
	      
	      if($verificacion_fecha_corte<0 && $campo_especial==-1)//excede la fecha de corete, diferencia de dias es inferior
	      {
		$fecha_corregida=$date_de_corte;
	      }
	      if($verificacion_fecha_corte_pos_10_meses<0 && $campo_especial==33)//excede la fecha de corete, diferencia de dias es inferior
	      {
		$fecha_corregida=$date_de_corte;
	      }
	      else if($verificacion_fecha_corte_12_meses_menos>0)//es inferior, por eso la diferencia de dias es mayor de cero
	      {
	       $fecha_corregida="1800-01-01";
	      }
	      
	    }//fin si excede 1900 entonces no es codigo
	}//fin if si no es fecha de nacimiento
	*/
	
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

	public function formato_fecha_valida_quick($fecha_a_verificar,$separador="-")
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

    	
	var $diccionario_identificacion=array();
	var $diccionario_identificacion_lineas=array();
	
	var $diccionario_identificacion_para_bool=array();
	
	//FUNCION QUE VALIDA LOS 118 CAMPOS DE 4505
	public function validacionCamposPyPenArray($array_tipo_inconsistencia, $array_grupo_inconsistencia, $array_detalle_inconsistencia, $array_fields, $numLinea, $boolean_or_string, &$consecutivo_errores,&$conexion_bd_validar_campos)
	{
		$validador_boolean=true;
		$mensajes_error_campos ="";
		
		$nombre_archivo4505="";
		$nombre_archivo4505=$this->nombre_archivo_4505;
		
		//CONEXION BASE DE DATOS
		//$conexion_bd_validar_campos = new conexion();
		
		/*
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
		*/
		
		//echo "<script>alert('".$array_detalle_inconsistencia["0103034"]." nombre ".$nombre_archivo4505." ".$resultado_query3_detalle_validacion[0]["codigo_detalle_inconsistencia"]." ".$resultado_query3_detalle_validacion[0]["descripcion_inconsistencia"]."');</script>";
		
		//calculo de la edad con la fecha de nacimiento
		date_default_timezone_set ("America/Bogota");
		//$fecha_actual = date('Y-m-d');
		$fecha_actual = $this->fecha_actual_global;
		
		
		$fecha_ini=explode("-",$this->fecha_inicio_periodo);
		$date_ini_reporte=date($fecha_ini[0]."-".$fecha_ini[1]."-".$fecha_ini[2]);
		$fecha_fin=explode("-",$this->fecha_de_corte_periodo);
		
		$date_fin_reporte=date($fecha_fin[0]."-".$fecha_fin[1]."-".$fecha_fin[2]);		
		
		$year_corte=intval($fecha_fin[0]);
		
		//PARTE ANALISIS FECHA DE CORTE ES MENSUAL A TRIMESTRAL
		$old_date_fin_reporte=$this->old_fecha_de_corte_periodo_mensual;
		
		//echo $old_date_fin_reporte;
		
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

		//PARTE PRE CORRECCION SEXO Y FECHA NACIMIENTO DE ACUERDO A TABLAS DE REGIMEN
		//gioss_afiliados_eapb_rc, id_afiliado, tipo_id_afiliado,$this->tipo_de_regimen_de_la_informacion_reportada
		$query_bd_existe_afiliado_en_tabla_regimen="";
		$resultados_query_existe_afiliado_tablas_regimen=array();
		$nombre_tabla_afiliado_hallado="";
		$numero_id_c4=$array_fields[4];
		$tipo_id_c3=$array_fields[3];

		if($this->tipo_de_regimen_de_la_informacion_reportada=="C")
		{
			$nombre_tabla_afiliado_hallado="gioss_afiliados_eapb_rc";

			$query_bd_existe_afiliado_en_tabla_regimen="SELECT * FROM ".$nombre_tabla_afiliado_hallado." WHERE id_afiliado = '".$numero_id_c4."' AND tipo_id_afiliado = '".$tipo_id_c3."' AND codigo_eapb='".$this->cod_eapb_global."' ;";
			$resultados_query_existe_afiliado_tablas_regimen=$conexion_bd_validar_campos->consultar2_no_crea_cierra($query_bd_existe_afiliado_en_tabla_regimen);
		}
		if($this->tipo_de_regimen_de_la_informacion_reportada=="S")
		{
			$nombre_tabla_afiliado_hallado="gioss_afiliados_regimen_subsidiado";

			$query_bd_existe_afiliado_en_tabla_regimen="SELECT * FROM ".$nombre_tabla_afiliado_hallado." WHERE id_afiliado = '".$numero_id_c4."' AND tipo_id_afiliado = '".$tipo_id_c3."' AND codigo_eapb='".$this->cod_eapb_global."' ;";
			$resultados_query_existe_afiliado_tablas_regimen=$conexion_bd_validar_campos->consultar2_no_crea_cierra($query_bd_existe_afiliado_en_tabla_regimen);
		}
		if($this->tipo_de_regimen_de_la_informacion_reportada=="E" || $this->tipo_de_regimen_de_la_informacion_reportada=="O")
		{
			$nombre_tabla_afiliado_hallado="gioss_afiliados_eapb_mp";

			$query_bd_existe_afiliado_en_tabla_regimen="SELECT * FROM ".$nombre_tabla_afiliado_hallado." WHERE id_afiliado = '".$numero_id_c4."' AND tipo_id_afiliado = '".$tipo_id_c3."' AND codigo_eapb='".$this->cod_eapb_global."' ;";
			$resultados_query_existe_afiliado_tablas_regimen=$conexion_bd_validar_campos->consultar2_no_crea_cierra($query_bd_existe_afiliado_en_tabla_regimen);
		}
		if($this->tipo_de_regimen_de_la_informacion_reportada=="P")
		{
			$nombre_tabla_afiliado_hallado="gioss_afiliados_eapb_rp";

			$query_bd_existe_afiliado_en_tabla_regimen="SELECT * FROM ".$nombre_tabla_afiliado_hallado." WHERE id_afiliado = '".$numero_id_c4."' AND tipo_id_afiliado = '".$tipo_id_c3."' AND codigo_eapb='".$this->cod_eapb_global."' ;";
			$resultados_query_existe_afiliado_tablas_regimen=$conexion_bd_validar_campos->consultar2_no_crea_cierra($query_bd_existe_afiliado_en_tabla_regimen);
		}
		if($this->tipo_de_regimen_de_la_informacion_reportada=="N")
		{
			$nombre_tabla_afiliado_hallado="gioss_afiliados_eapb_nv";

			$query_bd_existe_afiliado_en_tabla_regimen="SELECT * FROM ".$nombre_tabla_afiliado_hallado." WHERE id_afiliado = '".$numero_id_c4."' AND tipo_id_afiliado = '".$tipo_id_c3."' AND codigo_eapb='".$this->cod_eapb_global."' ;";
			$resultados_query_existe_afiliado_tablas_regimen=$conexion_bd_validar_campos->consultar2_no_crea_cierra($query_bd_existe_afiliado_en_tabla_regimen);
		}//fin if


		$num_filas_resultado_existe_tablas_regimen=count($resultados_query_existe_afiliado_tablas_regimen);

		//variables booleanas para separar y visibilizar
		//mas facilmente si habia sexos y/o fechas de nacimiento diferentes
		//entre el archivo subido y la base de datos
		$se_modifico_sexo_pero_eran_diferentes_entre_si=false;
		$se_modifico_fecha_nacimiento_pero_eran_diferentes_entre_si=false;

		$se_modifico_sexo_o_fecha_de_acuerdo_a_tabla_regimen=false;
		$si_existe=false;

		$sexo_anterior="";
		$fecha_anterior="";

		$sexo_posterior="";
		$fecha_posterior="";

		$nombre_sucursal="";
		$nombre_regional="";

		if($num_filas_resultado_existe_tablas_regimen>0 
			&& is_array($resultados_query_existe_afiliado_tablas_regimen)
			)
		{
			$si_existe=true;
 			if(isset($resultados_query_existe_afiliado_tablas_regimen[0]['nombre_sucursal']) )
 			{
				$nombre_sucursal=trim($resultados_query_existe_afiliado_tablas_regimen[0]['nombre_sucursal']);
			}//fin if existe
			if(isset($resultados_query_existe_afiliado_tablas_regimen[0]['nombre_regional']) )
 			{
				$nombre_regional=trim($resultados_query_existe_afiliado_tablas_regimen[0]['nombre_regional']);
				$this->regional_global=$nombre_regional;
			}//fin if existe 

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
						$array_fields[$numero_campo_sexo]=$sexo_en_bd;
						$se_modifico_sexo_o_fecha_de_acuerdo_a_tabla_regimen=true;

						$se_modifico_sexo_pero_eran_diferentes_entre_si=true;
					}//fin if
				}//fin if

				$fecha_nacimiento_en_registro_archivo=$this->corrector_formato_fecha($fecha_nacimiento_en_registro_archivo,true);
				if($this->formato_fecha_valida_quick($fecha_nacimiento_en_bd) )
				{
					$array_fields[$numero_campo_fecha_nacimiento]=$fecha_nacimiento_en_bd;
					$se_modifico_sexo_o_fecha_de_acuerdo_a_tabla_regimen=true;

					
					
					if($fecha_nacimiento_en_bd!=$fecha_nacimiento_en_registro_archivo)
					{
						$se_modifico_fecha_nacimiento_pero_eran_diferentes_entre_si=true;
					}//fin if
					
				}//fin if fecha nacimeinto es valida
				else
				{
					$fecha_nacimiento_en_bd=$this->corrector_formato_fecha($fecha_nacimiento_en_bd,true);
					if($this->formato_fecha_valida_quick($fecha_nacimiento_en_bd) )
					{
						if($fecha_nacimiento_en_bd!=$fecha_nacimiento_en_registro_archivo)
						{
							$array_fields[$numero_campo_fecha_nacimiento]=$fecha_nacimiento_en_bd;
							$se_modifico_sexo_o_fecha_de_acuerdo_a_tabla_regimen=true;
							$se_modifico_fecha_nacimiento_pero_eran_diferentes_entre_si=true;
						}//fin if
					}//fin if fecha nacimeinto es valida

				}//fin else

				$sexo_posterior=$sexo_en_bd;
				$fecha_posterior=$fecha_nacimiento_en_bd;
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


		
		$primer_apellido="".$array_fields[5];
		$segundo_apellido="".$array_fields[6];
		$primer_nombre="".$array_fields[7];
		$segundo_nombre="".$array_fields[8];

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

		


		$datos_complementarios="Tipo ID: $tipo_id_c3  Numero ID: $numero_id_c4  Primer Nombre: $primer_nombre Segundo Nombre: $segundo_nombre Primer Apellido: $primer_apellido Segundo Apellido: $segundo_apellido ";

		if($se_modifico_sexo_o_fecha_de_acuerdo_a_tabla_regimen==true)
		{
			//PARTE LLENA ARCHIVO CON REGISTROS AFILIADOS MODIFICADOS
		    if($this->global_ruta_registros_con_afiliados_modificados!="")
		    {
			    //se remplaza el archivo si ya existe con modo w		
			    $archivo_excluido_registros_con_afiliados_modificados = fopen($this->global_ruta_registros_con_afiliados_modificados, "a") or die("fallo la creacion del archivo modo:a registros_con_afiliados_modificados");		    		    
			    fwrite($archivo_excluido_registros_con_afiliados_modificados, "\n"."Sexo anterior: $sexo_anterior Sexo posterior: $sexo_posterior Fecha Nacimiento Anterior: $fecha_anterior Fecha Nacimiento Posterior: $fecha_posterior ".$datos_complementarios );		    
			    fclose($archivo_excluido_registros_con_afiliados_modificados);
			    $this->global_consecutivo_solo_registros_con_sexo_fecha_nacimiento_corregidos++;	
		    }	    
		    //FIN PARTE LLENA ARCHIVO CON REGISTROS AFILIADOS MODIFICADOS

		    if($se_modifico_sexo_pero_eran_diferentes_entre_si==true)
		    {
		    	if($this->global_ruta_registros_con_afiliados_modificados_sexo_diferentes!="")
			    {
				    //se remplaza el archivo si ya existe con modo w		
				    $archivo_excluido_registros_con_afiliados_modificados_sex_dif = fopen($this->global_ruta_registros_con_afiliados_modificados_sexo_diferentes, "a") or die("fallo la creacion del archivo modo:a registros_con_afiliados_modificados_sex_dif");		    		    
				    fwrite($archivo_excluido_registros_con_afiliados_modificados_sex_dif, "\n"."Sexo anterior: $sexo_anterior Sexo posterior: $sexo_posterior Fecha Nacimiento Anterior: $fecha_anterior Fecha Nacimiento Posterior: $fecha_posterior ".$datos_complementarios );		    
				    fclose($archivo_excluido_registros_con_afiliados_modificados_sex_dif);
				    $this->global_consecutivo_solo_registros_con_sexo_dif_corregidos++;	
			    }

		    }//fin if

		    if($se_modifico_fecha_nacimiento_pero_eran_diferentes_entre_si==true)
		    {
		    	if($this->global_ruta_registros_con_afiliados_modificados_fecha_nacimiento_diferentes!="")
			    {
				    //se remplaza el archivo si ya existe con modo w		
				    $archivo_excluido_registros_con_afiliados_modificados_fecha_nac_dif = fopen($this->global_ruta_registros_con_afiliados_modificados_fecha_nacimiento_diferentes, "a") or die("fallo la creacion del archivo modo:a registros_con_afiliados_modificados_fecha_nac_dif");		    		    
				    fwrite($archivo_excluido_registros_con_afiliados_modificados_fecha_nac_dif, "\n"."Sexo anterior: $sexo_anterior Sexo posterior: $sexo_posterior Fecha Nacimiento Anterior: $fecha_anterior Fecha Nacimiento Posterior: $fecha_posterior ".$datos_complementarios );		    
				    fclose($archivo_excluido_registros_con_afiliados_modificados_fecha_nac_dif);
				    $this->global_consecutivo_solo_registros_con_fecha_nacimiento_dif_corregidos++;	
			    }
		    }//fin if

		}//fin fi hubo modificacion


		//PARTE LLENA ARCHIVO NUEVO ORIGINAL CON POSIBLES REGISTROS AFILIADOS MODIFICADOS SEXO FECHA NACIMIENTO
		//solo los que esten en tabla de afiliados
	    if($this->global_ruta_archivo_plano_nuevo_original_con_sexo_fecha_nacimiento_corregidos!=""
	    	&& $si_existe==true 
	    	)
	    {
		    //se remplaza el archivo si ya existe con modo w		
		    $archivo_nuevo_original_con_sexo_fecha_nacimiento_corregidos = fopen($this->global_ruta_archivo_plano_nuevo_original_con_sexo_fecha_nacimiento_corregidos, "a") or die("fallo la creacion del archivo modo:a registros_con_afiliados_modificados");		    		    
		    fwrite($archivo_nuevo_original_con_sexo_fecha_nacimiento_corregidos, "\n".$this->campos_a_registro($array_fields,$this->global_consecutivo_nuevo_original_con_sexo_fecha_nacimiento_corregidos) );		    
		    fclose($archivo_nuevo_original_con_sexo_fecha_nacimiento_corregidos);
		    $this->global_consecutivo_nuevo_original_con_sexo_fecha_nacimiento_corregidos++;	
	    }	    
	    //FIN PARTE LLENA ARCHIVO NUEVO ORIGINAL CON POSIBLES REGISTROS AFILIADOS MODIFICADOS SEXO FECHA NACIMIENTO

	    //PARTE LLENA ARCHIVO NUEVO ORIGINAL CON POSIBLES REGISTROS AFILIADOS MODIFICADOS SEXO FECHA NACIMIENTO Y CAMPOS ADICIONALES ESTADISTICAS
		//solo los que esten en tabla de afiliados
	    if($this->global_ruta_registros_con_afiliados_modificados_y_campos_estadisticas_adicionales!=""
	    	&& $si_existe==true 
	    	)
	    {
	    	$array_campos_estadisticas=array(119=>$nombre_sucursal,120=>$nombre_regional);
		    //se remplaza el archivo si ya existe con modo w		
		    $archivo_solo_afiliados_con_estadisticas = fopen($this->global_ruta_registros_con_afiliados_modificados_y_campos_estadisticas_adicionales, "a") or die("fallo la creacion del archivo modo:a registros_con_afiliados_modificados");		    		    
		    fwrite($archivo_solo_afiliados_con_estadisticas, "\n".$this->campos_a_registro(array_merge($array_fields,$array_campos_estadisticas),$this->global_consecutivo_solo_registros_solo_afiliados_con_campos_adicionales) );		    
		    fclose($archivo_solo_afiliados_con_estadisticas);
		    $this->global_consecutivo_solo_registros_solo_afiliados_con_campos_adicionales++;	
	    }	    
	    //FIN PARTE LLENA ARCHIVO NUEVO ORIGINAL CON POSIBLES REGISTROS AFILIADOS MODIFICADOS SEXO FECHA NACIMIENTO Y CAMPOS ADICIONALES ESTADISTICAS


		//FIN PARTE PRE CORRECCION SEXO Y FECHA NACIMIENTO DE ACUERDO A TABLAS DE REGIMEN

		//PARTE REPORTE JUSTIFICACION VALORES PERMITIDOS 
		$arrayEstructuraLinea=array();
		$lineaSoporteAdministrativoNoActividadRegistroActual="";
		if(count($array_fields)>=119)
		{
			if($this->tipo_entidad_que_efectua_el_cargue=="agrupado_eapb")
			{
				$arrayEstructuraLinea['c1_codigo_prestador']=trim($array_fields[2]);
			}//fin if
			else
			{
				$arrayEstructuraLinea['c1_codigo_prestador']=$this->cod_registro_especial_pss;
			}//else 

			if($this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120" && isset($array_fields[119])==true)
			{
				$arrayEstructuraLinea['c2_codigo_entidad_administradora']=trim($array_fields[119]);
			}//fin if
			else
			{
				$arrayEstructuraLinea['c2_codigo_entidad_administradora']=$this->cod_eapb_global;
			}//fin else

			$arrayEstructuraLinea['c3_nombre_archivo']=$this->nombre_archivo_4505;
			$arrayEstructuraLinea['c4_fecha_inicio']=$this->fecha_inicio_periodo;
			$arrayEstructuraLinea['c5_fecha_fin']=$this->fecha_de_corte_periodo;

			$arrayEstructuraLinea['c6_campo3_tipo_id']=trim($array_fields[3]);
			$arrayEstructuraLinea['c7_campo4_numero_id']=trim($array_fields[4]);
			$arrayEstructuraLinea['c8_campo5_primer_apellido']=trim($array_fields[5]);
			$arrayEstructuraLinea['c9_campo6_segundo_apellido']=trim($array_fields[6]);
			$arrayEstructuraLinea['c10_campo7_primer_nombre']=trim($array_fields[7]);
			$arrayEstructuraLinea['c11_campo8_segundo_nombre']=trim($array_fields[8]);
			$arrayEstructuraLinea['c12_campo9_fecha_nacimiento']=trim($array_fields[9]);
			$arrayEstructuraLinea['c13_campo10_sexo']=trim($array_fields[10]);
			$arrayEstructuraLinea['c14_numero_campo']="";
			$arrayEstructuraLinea['c15_descripcion_campo']="";
			$arrayEstructuraLinea['c16_numero_linea']=$numLinea;
			$arrayEstructuraLinea['c17_valor_registrado_campo']="";
			$arrayEstructuraLinea['c18_mensaje_campo']="No se realiza o No se Administra por Otras Razones";
			$arrayEstructuraLinea['c19_mensaje']="No se reporta el Usuario no demanda el servicio";

			$array_numero_campos_valor_20=array();
			$array_numero_campos_valor_20[]=35;
			$array_numero_campos_valor_20[]=36;
			$array_numero_campos_valor_20[]=37;
			$array_numero_campos_valor_20[]=38;
			$array_numero_campos_valor_20[]=39;
			$array_numero_campos_valor_20[]=40;
			$array_numero_campos_valor_20[]=41;
			$array_numero_campos_valor_20[]=42;
			$array_numero_campos_valor_20[]=43;
			$array_numero_campos_valor_20[]=44;
			$array_numero_campos_valor_20[]=45;
			$array_numero_campos_valor_20[]=46;
			$array_numero_campos_valor_20[]=47;
			$array_numero_campos_valor_20[]=48;
			$array_numero_campos_valor_20[]=54;
			$array_numero_campos_valor_20[]=59;
			$array_numero_campos_valor_20[]=60;
			$array_numero_campos_valor_20[]=61;
			$array_numero_campos_valor_20[]=70;
			$array_numero_campos_valor_20[]=71;
			$array_numero_campos_valor_20[]=77;
			$array_numero_campos_valor_20[]=86;
			$array_numero_campos_valor_20[]=114;
			$array_numero_campos_valor_20[]=115;
			$array_numero_campos_valor_20[]=116;
			$array_numero_campos_valor_20[]=117;

			foreach ($array_numero_campos_valor_20 as $key => $numero_campo) 
			{
				if(trim($array_fields[$numero_campo])=="20")
				{
					if($lineaSoporteAdministrativoNoActividadRegistroActual!=""){$lineaSoporteAdministrativoNoActividadRegistroActual.="\n";}
					$arrayEstructuraLinea['c14_numero_campo']=$numero_campo;
					$arrayEstructuraLinea['c15_descripcion_campo']=$this->globalNombreCampos[$numero_campo]['nombre_campo'];
					$arrayEstructuraLinea['c17_valor_registrado_campo']=trim($array_fields[$numero_campo]);
					$lineaSoporteAdministrativoNoActividadRegistroActual.=implode(",", $arrayEstructuraLinea);
				}//fin if
			}//fin foreach campos valor permitido 20

			$array_numero_campos_valor_1835=array();	

			$array_numero_campos_valor_1835[]=51;
			$array_numero_campos_valor_1835[]=52;
			$array_numero_campos_valor_1835[]=53;
			$array_numero_campos_valor_1835[]=55;
			$array_numero_campos_valor_1835[]=56;
			$array_numero_campos_valor_1835[]=62;
			$array_numero_campos_valor_1835[]=63;
			$array_numero_campos_valor_1835[]=65;
			$array_numero_campos_valor_1835[]=66;
			$array_numero_campos_valor_1835[]=67;
			$array_numero_campos_valor_1835[]=68;
			$array_numero_campos_valor_1835[]=69;
			$array_numero_campos_valor_1835[]=72;
			$array_numero_campos_valor_1835[]=73;
			$array_numero_campos_valor_1835[]=75;
			$array_numero_campos_valor_1835[]=76;
			$array_numero_campos_valor_1835[]=78;
			$array_numero_campos_valor_1835[]=80;
			$array_numero_campos_valor_1835[]=82;
			$array_numero_campos_valor_1835[]=84;
			$array_numero_campos_valor_1835[]=87;
			$array_numero_campos_valor_1835[]=91;
			$array_numero_campos_valor_1835[]=93;
			$array_numero_campos_valor_1835[]=96;
			$array_numero_campos_valor_1835[]=99;
			$array_numero_campos_valor_1835[]=103;
			$array_numero_campos_valor_1835[]=105;
			$array_numero_campos_valor_1835[]=106;
			$array_numero_campos_valor_1835[]=108;
			$array_numero_campos_valor_1835[]=110;
			$array_numero_campos_valor_1835[]=111;
			$array_numero_campos_valor_1835[]=112;
			$array_numero_campos_valor_1835[]=118;	

			foreach ($array_numero_campos_valor_1835 as $key => $numero_campo) 
			{
				if(trim($array_fields[$numero_campo])=="1835-01-01")
				{
					if($lineaSoporteAdministrativoNoActividadRegistroActual!=""){$lineaSoporteAdministrativoNoActividadRegistroActual.="\n";}
					$arrayEstructuraLinea['c14_numero_campo']=$numero_campo;
					$arrayEstructuraLinea['c15_descripcion_campo']=$this->globalNombreCampos[$numero_campo]['nombre_campo'];
					$arrayEstructuraLinea['c17_valor_registrado_campo']=trim($array_fields[$numero_campo]);
					$lineaSoporteAdministrativoNoActividadRegistroActual.=implode(",", $arrayEstructuraLinea);
				}//fin if
			}//fin foreach campos valor permitido 1835-01-01		
			
			$numero_campo=74;
			if(trim($array_fields[$numero_campo])=="993")
			{
				if($lineaSoporteAdministrativoNoActividadRegistroActual!=""){$lineaSoporteAdministrativoNoActividadRegistroActual.="\n";}
				$arrayEstructuraLinea['c14_numero_campo']=$numero_campo;
				$arrayEstructuraLinea['c15_descripcion_campo']=$this->globalNombreCampos[$numero_campo]['nombre_campo'];
				$arrayEstructuraLinea['c17_valor_registrado_campo']=trim($array_fields[$numero_campo]);
				$lineaSoporteAdministrativoNoActividadRegistroActual.=implode(",", $arrayEstructuraLinea);
			}//fin if caso campo 74 igual a 993

		}//fin if

		$titulosArchivoSoporteAdministrativoNoActividad="\n";
		$titulosArchivoSoporteAdministrativoNoActividad.="SISTEMA GIOSS\n";
		$titulosArchivoSoporteAdministrativoNoActividad.="ESTRUCTURA REPORTE DE SOPORTE ADMINISTRATIVO:  PARA NO SE REALIZA O NO ADMINISTRA POR OTRAS RAZONES\n";
		$titulosArchivoSoporteAdministrativoNoActividad.="APLICA VALORES : 20 - 993 - 1835-01-01\n";
		$arrayTitulosEstructuraLinea=array_keys($arrayEstructuraLinea);		
		$titulosArchivoSoporteAdministrativoNoActividad.="".ucwords( str_replace("_", " ", implode(",", $arrayTitulosEstructuraLinea) ) );
	    
    	if($this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120" && isset($array_fields[119])==true)
    	{
    		$arrayCaracteresEspecialesNoPermitidosDirectorios=array(" ","-","/","\\","|","*","<",">","?","¿","!","¡");
    		$codigoEapbC119=str_replace($arrayCaracteresEspecialesNoPermitidosDirectorios, "", trim($array_fields[119]) );
    		if($codigoEapbC119!="" && count($array_fields)>=119)
    		{
    			$rutaCarpetaTemp=$this->globalRutaTemporalesEscribirArchivos."SANA".$codigoEapbC119;
    			if(file_exists($rutaCarpetaTemp)==false)
    			{
    				mkdir($rutaCarpetaTemp, 0777, true);
    			}//fin if
    			$rutaTempSoporteAdmin=$rutaCarpetaTemp."/SoporteAdministrativoNoActividad".$this->cod_registro_especial_pss."_".$codigoEapbC119.".txt";
    			if(isset($this->GlobalArraySoporteAdministrativoNoActividad[$codigoEapbC119])==false)
    			{
    				$this->GlobalArraySoporteAdministrativoNoActividad[$codigoEapbC119]=$rutaTempSoporteAdmin;
    				$HandlerArchivoSoporteAdministrativoActual=fopen($rutaTempSoporteAdmin, 'w');
    				fwrite($HandlerArchivoSoporteAdministrativoActual, "Entidad Administradora: ".$codigoEapbC119.$titulosArchivoSoporteAdministrativoNoActividad);
    				fclose($HandlerArchivoSoporteAdministrativoActual);
    			}//fin a

    			$HandlerArchivoSoporteAdministrativoActual=fopen($rutaTempSoporteAdmin, 'a');
    			//linea reporte registro actual
    			fwrite($HandlerArchivoSoporteAdministrativoActual, "\n".$lineaSoporteAdministrativoNoActividadRegistroActual);
    			fclose($HandlerArchivoSoporteAdministrativoActual);
    		}//fin if
    		
    	}//fin if
    	if($this->tipo_entidad_que_efectua_el_cargue=="agrupado_eapb" && isset($array_fields[2])==true )
    	{
    		$arrayCaracteresEspecialesNoPermitidosDirectorios=array(" ","-","/","\\","|","*","<",">","?","¿","!","¡");
    		$codigoIpsC2=str_replace($arrayCaracteresEspecialesNoPermitidosDirectorios, "", trim($array_fields[2]) );
    		if($codigoIpsC2!="" && $codigoIpsC2!="999" && count($array_fields)>=119)
    		{
    			$rutaCarpetaTemp=$this->globalRutaTemporalesEscribirArchivos."SANA".$codigoIpsC2;
    			if(file_exists($rutaCarpetaTemp)==false)
    			{
    				mkdir($rutaCarpetaTemp, 0777, true);
    			}//fin if
    			$rutaTempSoporteAdmin=$rutaCarpetaTemp."/SoporteAdministrativoNoActividad".$codigoIpsC2."_".$this->cod_eapb_global.".txt";
    			if(isset($this->GlobalArraySoporteAdministrativoNoActividad[$codigoIpsC2])==false)
    			{
    				$this->GlobalArraySoporteAdministrativoNoActividad[$codigoIpsC2]=$rutaTempSoporteAdmin;
    				$HandlerArchivoSoporteAdministrativoActual=fopen($rutaTempSoporteAdmin, 'w');
    				fwrite($HandlerArchivoSoporteAdministrativoActual, "Entidad Prestadora: ".$codigoIpsC2.$titulosArchivoSoporteAdministrativoNoActividad);
    				fclose($HandlerArchivoSoporteAdministrativoActual);
    			}//fin if

    			$HandlerArchivoSoporteAdministrativoActual=fopen($rutaTempSoporteAdmin, 'a');
    			//linea reporte registro actual
    			fwrite($HandlerArchivoSoporteAdministrativoActual, "\n".$lineaSoporteAdministrativoNoActividadRegistroActual);
    			fclose($HandlerArchivoSoporteAdministrativoActual);
    		}//fin if
    	}//fin if
        if($this->tipo_entidad_que_efectua_el_cargue=="individual_ips"
	       || $this->tipo_entidad_que_efectua_el_cargue=="ent_territoriales"
	       || $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips"
	   	)//fin condicion
        {
        	if(count($array_fields)>=119)
    		{
    			$rutaCarpetaTemp=$this->globalRutaTemporalesEscribirArchivos."SANA";
    			if(file_exists($rutaCarpetaTemp)==false)
    			{
    				mkdir($rutaCarpetaTemp, 0777, true);
    			}//fin if
    			$rutaTempSoporteAdmin=$rutaCarpetaTemp."/SoporteAdministrativoNoActividad".$this->cod_registro_especial_pss."_".$this->cod_eapb_global.".txt";
    			if(isset($this->GlobalArraySoporteAdministrativoNoActividad[$this->cod_registro_especial_pss])==false)
    			{
    				$this->GlobalArraySoporteAdministrativoNoActividad[$this->cod_registro_especial_pss]=$rutaTempSoporteAdmin;
    				$HandlerArchivoSoporteAdministrativoActual=fopen($rutaTempSoporteAdmin, 'w');
    				fwrite($HandlerArchivoSoporteAdministrativoActual, "Entidad Prestadora: ".$this->cod_registro_especial_pss.$titulosArchivoSoporteAdministrativoNoActividad);
    				fclose($HandlerArchivoSoporteAdministrativoActual);
    			}//fin a

    			$HandlerArchivoSoporteAdministrativoActual=fopen($rutaTempSoporteAdmin, 'a');
    			//linea reporte registro actual
    			fwrite($HandlerArchivoSoporteAdministrativoActual, "\n".$lineaSoporteAdministrativoNoActividadRegistroActual);
    			fclose($HandlerArchivoSoporteAdministrativoActual);
    		}//fin if
        }//fin if	    
		//FIN PARTE REPORTE JUSTIFICACION VALORES PERMITIDOS

		$this->codigo_habilitacion_para_inconsistencias=trim($array_fields[2]);

		$PERMITIR_VALIDAR_ESTA_REGISTRADO_EN_AFILIADOS=true;
		//Aunque la query de verificacion se encuentra al principio 
		//el mensaje de error para la inexistencia de afiliado
		//se conserva en la misma posicion paramantener 
		//los mensjaes de error el campo 4 juntos
		if($num_filas_resultado_existe_tablas_regimen==0 
			|| !is_array($resultados_query_existe_afiliado_tablas_regimen)
			)
		{
			//campo 4 parte verificacion existencia afiliado error no existe en bd
			$numero_campo_actual=4;
			//ES OBLIGATORIA EN PREPAGADA
			$validador_boolean=false;
			if($mensajes_error_campos!=""){$mensajes_error_campos.="|";}
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105321";
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_inconsistencia[$var_numero_codigo])[1];
			$mensajes_error_campos.=$consecutivo_errores.",".$nombre_archivo4505.",01,".$array_tipo_inconsistencia["01"].",0105,".$array_grupo_inconsistencia["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$array_fields[$numero_campo_actual]." ,".$numLinea.",".$numero_campo_actual.",".$this->codigo_habilitacion_para_inconsistencias.",".$array_fields[3].",".$array_fields[4];
			$consecutivo_errores++;


			$PERMITIR_VALIDAR_ESTA_REGISTRADO_EN_AFILIADOS=false;

			//PARTE LLENA ARCHIVO AFILIADOS SIN REGISTROS EN BD
		    if($this->global_ruta_afiliados_no_registrados!="")
		    {
			    //se remplaza el archivo si ya existe con modo w		
			    $archivo_excluido_registros_afiliados_no_hallados = fopen($this->global_ruta_afiliados_no_registrados, "a") or die("fallo la creacion del archivo modo:a afiliados_no_hallados");		    		    
			    fwrite($archivo_excluido_registros_afiliados_no_hallados, "\n".$this->campos_a_registro($array_fields,$this->global_consecutivo_afiliados_no_registrados) );		    
			    fclose($archivo_excluido_registros_afiliados_no_hallados);	
			    $this->global_consecutivo_afiliados_no_registrados++;
		    }	    
		    //FIN PARTE LLENA ARCHIVO AFILIADOS SIN REGISTROS EN BD
		}//fin if no hay coincidencia en bd
		
		if($PERMITIR_VALIDAR_ESTA_REGISTRADO_EN_AFILIADOS==true)
		{
			$this->criterios_validacion($array_fields, $numLinea, $boolean_or_string, $date_fin_reporte, $array_tipo_inconsistencia, $array_grupo_inconsistencia, $array_detalle_inconsistencia, $nombre_archivo4505, $year_corte, $consecutivo_errores, $validador_boolean, $mensajes_error_campos, $conexion_bd_validar_campos);	
		
		
		}//fin if $PERMITIR_VALIDAR_ESTA_REGISTRADO_EN_AFILIADOS
		//guia $PERMITIR_VALIDAR_ESTA_REGISTRADO_EN_AFILIADOS
		if($boolean_or_string=="bool"){ return $validador_boolean;}
		else if ($boolean_or_string=="string"){ return $mensajes_error_campos;}
		else if ($boolean_or_string=="ambos"){ return array("es_valido"=>$validador_boolean,"inconsistencias_linea"=>$mensajes_error_campos,"array_campos_con_datos_afiliados"=>$array_fields);}
	}
	//FIN FUNCION QUE VALIDA LOS 118 CAMPOS DE 4505
	
	
	public function Inconsistencias_y_Validaciones_CamposPyP($array_tipo_inconsistencia, $array_grupo_inconsistencia, $array_detalle_inconsistencia, $linea_con_campos_de_archivo_pyp,$numLinea,&$consecutivo_errores,&$conexion_bd_validar_campos)
	{
		$this->array_campos_con_datos_afiliados=array();
		$error_linea = "";
		$array_campos = explode("|", $linea_con_campos_de_archivo_pyp);
		
		$nombre_archivo4505="";
		$nombre_archivo4505=$this->nombre_archivo_4505;
		
		//CONEXION BASE DE DATOS
		//$conexion_bd_validar_campos = new conexion();
		
		/*
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
		*/
		$es_valido = true;
		
		if($numLinea==0 && count($array_campos)==5)
		{
			//parte para verificar cada uno de los 5 campos de la primera linea
			$this->fecha_inicio_periodo=$array_campos[2];
			$this->fecha_de_corte_periodo=$array_campos[3];
			
			$this->old_fecha_de_corte_periodo_mensual=$array_campos[3];
			
			$fecha_ini_reporte_temp=explode("-",$array_campos[2]);
			$fecha_fin_reporte_temp=explode("-",$array_campos[3]);
			
			$cond_fecha_ini=checkdate($fecha_ini_reporte_temp[1],$fecha_ini_reporte_temp[2],$fecha_ini_reporte_temp[0]);
			$cond_fecha_fin=checkdate($fecha_fin_reporte_temp[1],$fecha_fin_reporte_temp[2],$fecha_fin_reporte_temp[0]);
			
			$numero_campo_actual=2;
			if(!$cond_fecha_ini)
			{
				$es_valido=false;
				if($error_linea!=""){$error_linea.="|";}
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
				$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_inconsistencia[$var_numero_codigo])[1];
				$error_linea.=$consecutivo_errores.",".$nombre_archivo4505.",01,".$array_tipo_inconsistencia["01"].",0102,".$array_grupo_inconsistencia["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".count($array_campos)." ,".$numLinea.",".$numero_campo_actual.",".$this->codigo_habilitacion_para_inconsistencias;
				$consecutivo_errores++;
			}
			$numero_campo_actual=3;
			if(!$cond_fecha_fin)
			{
				$es_valido=false;
				if($error_linea!=""){$error_linea.="|";}
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
				$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_inconsistencia[$var_numero_codigo])[1];
				$error_linea.=$consecutivo_errores.",".$nombre_archivo4505.",01,".$array_tipo_inconsistencia["01"].",0102,".$array_grupo_inconsistencia["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".count($array_campos)." ,".$numLinea.",".$numero_campo_actual.",".$this->codigo_habilitacion_para_inconsistencias;
				$consecutivo_errores++;
			}
			
			$tipo_de_registro = $array_campos[0];
			$codigoEPS = $array_campos[1];
			$numero_registros=$array_campos[4];
		}
		else if($numLinea>0 && count($array_campos)==119)
		{
			//PARTE PARA VERIFICAR CADA UNO DE LOS 119 CAMPOS
			$array_inconsistencias_linea=$this->validacionCamposPyPenArray($array_tipo_inconsistencia, $array_grupo_inconsistencia, $array_detalle_inconsistencia, $array_campos,$numLinea,"ambos",$consecutivo_errores,$conexion_bd_validar_campos);
			$error_linea = $array_inconsistencias_linea["inconsistencias_linea"];
			$es_valido = $array_inconsistencias_linea["es_valido"];
			//FIN PARTE PARA VERIFICAR CADA UNO DE LOS 119 CAMPOS
			$this->array_campos_con_datos_afiliados=$array_inconsistencias_linea["array_campos_con_datos_afiliados"];
		}
		else if($numLinea>0 && $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120" && count($array_campos)==120)
		{
			//PARTE PARA VERIFICAR CADA UNO DE LOS 119 CAMPOS
			$array_inconsistencias_linea=$this->validacionCamposPyPenArray($array_tipo_inconsistencia, $array_grupo_inconsistencia, $array_detalle_inconsistencia, $array_campos,$numLinea,"ambos",$consecutivo_errores,$conexion_bd_validar_campos);
			$error_linea = $array_inconsistencias_linea["inconsistencias_linea"];
			$es_valido = $array_inconsistencias_linea["es_valido"];
			//FIN PARTE PARA VERIFICAR CADA UNO DE LOS 119 CAMPOS
			$this->array_campos_con_datos_afiliados=$array_inconsistencias_linea["array_campos_con_datos_afiliados"];
		}
		else
		{
		 $numero_campo_actual=999;
		 if($numLinea==0)
		 {
		    $es_valido=false;
		    if($error_linea!=""){$error_linea.="|";}
		    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0301001";
		    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_inconsistencia[$var_numero_codigo])[1];
		    $error_linea.=$consecutivo_errores.",".$nombre_archivo4505.",03,".$array_tipo_inconsistencia["03"].",0301,".$array_grupo_inconsistencia["0301"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".count($array_campos)." ,".$numLinea.",".$numero_campo_actual.",".$this->codigo_habilitacion_para_inconsistencias;
		    $consecutivo_errores++;
		 }
		 else
		 {
		    $es_valido=false;
		    if($error_linea!=""){$error_linea.="|";}
		    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0301002";
		    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_inconsistencia[$var_numero_codigo])[1];
		    $error_linea.=$consecutivo_errores.",".$nombre_archivo4505.",03,".$array_tipo_inconsistencia["03"].",0301,".$array_grupo_inconsistencia["0301"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".count($array_campos)." ,".$numLinea.",".$numero_campo_actual.",".$this->codigo_habilitacion_para_inconsistencias;
		    $consecutivo_errores++;

		    if($this->global_ruta_estructura_mala!="")
		    {
			    //se remplaza el archivo si ya existe con modo w		
			    $archivo_excluido_estructura_mala = fopen($this->global_ruta_estructura_mala, "a") or die("fallo la creacion del archivo modo:a estructura mala");		    		    
			    fwrite($archivo_excluido_estructura_mala, "\n".trim($linea_con_campos_de_archivo_pyp) );		 // $this->global_consecutivo_estructura_mala no es necesario aca   
			    fclose($archivo_excluido_estructura_mala);	
			    $this->global_consecutivo_estructura_mala++;
		    }//fin if
		 }//fin else no coincide con los 119 campos
		}//fin else numero de campos incorrecto
		
		return array("es_valido"=>$es_valido,"inconsistencias_linea"=>$error_linea);
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
	    $cadena = str_replace("ñ","n",$string);//aca debe ser $string o la cadena estara vacia (nota obvia por si acaso :P)
	    $cadena = str_replace("Ñ","N",$cadena);
	    $cadena = str_replace("á","a",$cadena);
	    $cadena = str_replace("é","e",$cadena);
	    $cadena = str_replace("í","i",$cadena);
	    $cadena = str_replace("ó","o",$cadena);
	    $cadena = str_replace("ú","u",$cadena);
	    $cadena = str_replace("Á","A",$cadena);
	    $cadena = str_replace("É","E",$cadena);
	    $cadena = str_replace("Í","I",$cadena);
	    $cadena = str_replace("Ó","O",$cadena);
	    $cadena = str_replace("Ú","U",$cadena);
	    $cadena = str_replace(array("\n\r", "\r\n", "\r", "\n"), '', $cadena);
	    $cadena = str_replace(PHP_EOL, null, $cadena);
	    $cadena = preg_replace('/[^a-zA-Z0-9\s,\-\.]/', '', trim($cadena));
	    //$cadena = substr($cadena,0,10);
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

    public function campos_a_registro($campos,$consecutivo_linea_param=0,$separador="|")
    {
    	//crea cadena con el registro actual
	    $registro_actual="";
	    if(is_array($campos))
	    {
		    $ccamp=0;
		    while($ccamp<count($campos))
		    {
				if($ccamp!=0){$registro_actual.=$separador;}
				if($ccamp==1)
				{
				    $registro_actual.=$consecutivo_linea_param;
				}//fin if
				else
				{
				    $registro_actual.=$this->alphanumericAndSpace3($campos[$ccamp]);
				}//fin else
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

    public function reescribe_primera_linea_function($consecutivo_param, $ruta_file, $array_linea_procesada)
    {
    	$consecutivo_param=$consecutivo_param-1;//se le resta 1 posicion, para que tenga como valor el ultimo consecutivo escrito
	    $archivo_a_procesar=fopen($ruta_file, "c") or die("fallo la creacion del archivo modo:c buenos");
	    //parte primera linea
	    $consecutivo_anterior=intval($array_linea_procesada[4]);
	    $numero_caracteres_consecutivo_anterior=strlen("".$consecutivo_anterior);
	    $numero_caracteres_consecutivo_nuevo=strlen("".$consecutivo_param);
	    //echo "<script>alert('$numero_caracteres_consecutivo_anterior $consecutivo_anterior $numero_caracteres_consecutivo_nuevo ".($this->consecutivo_fixer-1)."');</script>";
	    if($numero_caracteres_consecutivo_anterior==$numero_caracteres_consecutivo_nuevo)
	    {
	    	//echo "entro c1 ".$ruta_file." numero_caracteres_consecutivo_anterior $numero_caracteres_consecutivo_anterior numero_caracteres_consecutivo_nuevo $numero_caracteres_consecutivo_nuevo <br>";
		fwrite($archivo_a_procesar, $array_linea_procesada[0]."|".$array_linea_procesada[1]."|".$array_linea_procesada[2]."|".$array_linea_procesada[3]."|".$consecutivo_param." ");
	    }
	    else if($numero_caracteres_consecutivo_anterior<$numero_caracteres_consecutivo_nuevo)
	    {
	    	//echo "entro c2 ".$ruta_file." numero_caracteres_consecutivo_anterior $numero_caracteres_consecutivo_anterior numero_caracteres_consecutivo_nuevo $numero_caracteres_consecutivo_nuevo<br>";
		fwrite($archivo_a_procesar, $array_linea_procesada[0]."|".$array_linea_procesada[1]."|".$array_linea_procesada[2]."|".$array_linea_procesada[3]."|".$consecutivo_param);
	    }
	    else if($numero_caracteres_consecutivo_anterior>$numero_caracteres_consecutivo_nuevo)
	    {
	    	
			$numero_espacios_adicionar=0;
			$numero_espacios_adicionar=$numero_caracteres_consecutivo_anterior-$numero_caracteres_consecutivo_nuevo;
			$cont_espacios_add=0;
			$string_espacios_add="";
			while($cont_espacios_add<$numero_espacios_adicionar)
			{
			    $string_espacios_add.=" ";
			    $cont_espacios_add++;
			}
			$string_espacios_add.=" ";

			//echo "entro c3 ".$ruta_file." numero_caracteres_consecutivo_anterior $numero_caracteres_consecutivo_anterior numero_caracteres_consecutivo_nuevo $numero_caracteres_consecutivo_nuevo consecutivo_param '$consecutivo_param' consecutivo_anterior '$consecutivo_anterior' string_espacios_add '$string_espacios_add' len ".strlen($string_espacios_add)." cont_espacios_add $cont_espacios_add numero_espacios_adicionar $numero_espacios_adicionar '".$consecutivo_param.$string_espacios_add."' '".strlen($consecutivo_param.$string_espacios_add)."'<br>";

			fwrite($archivo_a_procesar, $array_linea_procesada[0]."|".$array_linea_procesada[1]."|".$array_linea_procesada[2]."|".$array_linea_procesada[3]."|".$consecutivo_param.$string_espacios_add);
	    }
	    //fin part primear linea
	    fclose($archivo_a_procesar);
    }//fin function
	
    //funcion que lee el archivo y los sube a la base de datos en la tabla
    public function ValidarArchivo4505($file, $nombreArchivo, $tipoArchivo, $cantArchivos) 
    {
		flush();
		
		$utilidades = new Utilidades();
		$obj = new conexion();
		$obj->crearConexion();
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
			
			$this->fecha_inicio_periodo=$fecha_inicial;
			$this->fecha_de_corte_periodo=$fecha_final;
			
			$this->old_fecha_de_corte_periodo_mensual=$fecha_final;
			
			if(intval($consecutivo_de_la_linea)!=0 && intval($consecutivo_de_la_linea)!=1)
			{
				$mensaje_error_vi.="El consecutivo indicado en la primera linea es incorrecto. <br>";
			}
			
			if($eapb_registrada!=$this->cod_eapb_global)
			{
			    
			    $sql_consulta_descripcion_por_codigo_1="";
			    $sql_consulta_descripcion_por_codigo_1.="select * from gioss_entidades_sector_salud WHERE codigo_entidad='".$eapb_registrada."';";
			    $resultado_query_descripcion_por_codigo_1=$obj->consultar2_no_crea_cierra($sql_consulta_descripcion_por_codigo_1);
			    $nombre_entidad_1="";
			    if(is_array($resultado_query_descripcion_por_codigo_1))
			    {
				$nombre_entidad_1=$resultado_query_descripcion_por_codigo_1[0]["nombre_de_la_entidad"];
			    }
			    
			    $sql_consulta_descripcion_por_codigo_2="";
			    $sql_consulta_descripcion_por_codigo_2.="select * from gioss_entidades_sector_salud WHERE codigo_entidad='".$this->cod_eapb_global."';";
			    $resultado_query_descripcion_por_codigo_2=$obj->consultar2_no_crea_cierra($sql_consulta_descripcion_por_codigo_2);
			    $nombre_entidad_2="";
			    if(is_array($resultado_query_descripcion_por_codigo_2))
			    {
				$nombre_entidad_2=$resultado_query_descripcion_por_codigo_2[0]["nombre_de_la_entidad"];
			    }
			    
			    $mensaje_error_vi.="La EAPB $eapb_registrada  $nombre_entidad_1 registrada en el archivo, no corresponde con la EAPB a reportar: ".$this->cod_eapb_global." $nombre_entidad_2 .  <br>";
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
				    $resultados_consulta_periodo_informacion_4505=$obj->consultar2_no_crea_cierra($consultar_periodo_informacion_4505);
				}
				else if($this->tipo_periodo_tiempo_global=="mensual")
				{
				    $consultar_periodo_informacion_4505="";
				    $consultar_periodo_informacion_4505.=" SELECT * FROM gioss_periodo_informacion_4505_mensual WHERE cod_periodo_informacion='".$this->codigo_periodo."'; ";
				    $resultados_consulta_periodo_informacion_4505=$obj->consultar2_no_crea_cierra($consultar_periodo_informacion_4505);
				}
				
				
				if(count($resultados_consulta_periodo_informacion_4505)>0
				   && $verificador_vi_inicial_final>0
				   && is_array($resultados_consulta_periodo_informacion_4505))
				{				    
				    $fecha_inicio_periodo_bd=$resultados_consulta_periodo_informacion_4505[0]["fec_inicio_periodo"];
				    $array_fecha_inicio_periodo_bd=explode("-",$fecha_inicio_periodo_bd);
				    $year=explode("-",$fecha_inicial)[0];
				    $fecha_inicio_periodo_bd=$year."-".$array_fecha_inicio_periodo_bd[1]."-".$array_fecha_inicio_periodo_bd[2];

				    $this->fecha_inicial_para_analisis=$fecha_inicio_periodo_bd;
				    
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
			$mensaje .= "El archivo no contiene los campos adecuados para la linea de verificacion inicial (".count($array_linea1)."). <br>";
			
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
		    $seq_cargue_4505 = $utilidades->obtenerSecuencia("gioss_numero_secuencia_pyp4505"); //consultar a base de datos la secuencia (nextval)
		    
		    $mensaje_advertencia_tiempo="";
		    $mensaje_advertencia_tiempo .="Estimado usuario, se ha iniciado el proceso de validación del archivo $nombre_archivo4505 ,<br> lo que puede tomar varios minutos, dependiendo del volumen de registros.<br>";
		    $mensaje_advertencia_tiempo .="Una vez validado, se genera el Logs de errores, el cual se enviará a su Correo electrónico o puede descargarlo directamente del aplicativo.<br>";
		    $mensaje_advertencia_tiempo .="Si la validación es exitosa, los datos se cargarán en la base de datos y se dará por aceptada la información reportada<br>";
				    
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
			echo "document.title='V I PyP $nombre_archivo4505.';";
			echo "</script>";		    
			
			ob_flush();
			flush();
		    }
		    
		    $error_de_base_de_datos="";
		    
		    date_default_timezone_set ("America/Bogota");
		    $fecha_actual = date('Y-m-d');
		    $tiempo_actual = date('H:i:s');
		    
		    $this->fecha_actual_global=$fecha_actual;
		    $this->tiempo_actual_global=$tiempo_actual;
		    
		    $fecha_para_archivo= date('Y-m-d-H-i-s');
		    
		    
		    $bandera = false;
		    $flag = 0;
		    $x = 1;
		    $i = 0;
		    
		    
		    
		    
		    $archivo = fopen($file, 'r') or exit("No se pudo abrir el archivo");
		    
		    $hubo_errores_en_los_campos_del_archivo = false;
		    $campos_linea_validos = false;
		    
		    $numLinea=0;
		    
		    $sql_consultar_existe_en_rechazados ="";
		    $sql_consultar_existe_en_rechazados .=" select count(*) from gios_datos_rechazados_r4505 where ";
		    $sql_consultar_existe_en_rechazados .=" codigo_eapb ='".$this->cod_eapb_global."' and ";
		    $sql_consultar_existe_en_rechazados .=" cod_prestador_servicios_salud='".$this->cod_registro_especial_pss."' and ";
		    $sql_consultar_existe_en_rechazados .=" fecha_de_corte = '".$this->cadena_fecha_corte."' and ";
		    $sql_consultar_existe_en_rechazados .=" nombre_archivo = '".$nombre_archivo4505."' and ";
		    $sql_consultar_existe_en_rechazados .=" consecutivo_de_archivo = '".$this->consecutivo_de_archivo."' ";
		    $sql_consultar_existe_en_rechazados .=";";
		    
		    $resultados_query_existe=$obj->consultar_no_crea_cierra($sql_consultar_existe_en_rechazados);	
		    $existe_n_veces=intval($resultados_query_existe[0]);
		    
		    
		    $secuencia_dependiendo_existencia="";
		    //la secuencia se incrementara sin importar e estado de validacion
		    $secuencia_dependiendo_existencia=$seq_cargue_4505;
		    
		    //se asigna a la variable global secuencia_actual_para_email la cual permitira escribir el numero de secuencia al devolver
		    $this->secuencia_actual_para_email=$secuencia_dependiendo_existencia;
		    
		    $contador_registros_buenos=0;
		    $contador_registros_malos=0;
		    
		    //INSERTA AEN ESTA VALIDANDO ACTUALMENTE
		    if($this->tipo_entidad_que_efectua_el_cargue=="individual_ips"
		       || $this->tipo_entidad_que_efectua_el_cargue=="ent_territoriales"
		       || $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips"
		       || $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120"
		       )
		    {
			$query_insert_esta_siendo_procesado="";
			$query_insert_esta_siendo_procesado.=" INSERT INTO gioss_4505_esta_validando_actualmente ";
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
			$query_insert_esta_siendo_procesado.=" '".$this->cod_registro_especial_pss."',  ";
			$query_insert_esta_siendo_procesado.=" '".$nombre_archivo4505."',  ";
			$query_insert_esta_siendo_procesado.=" '".$this->cadena_fecha_corte."',  ";
			$query_insert_esta_siendo_procesado.=" '".$fecha_actual."',  ";
			$query_insert_esta_siendo_procesado.=" '".$tiempo_actual."',  ";
			$query_insert_esta_siendo_procesado.=" '".$this->nick_user."',  ";
			$query_insert_esta_siendo_procesado.=" 'SI',  ";
			$query_insert_esta_siendo_procesado.=" 'NO',  ";
			$query_insert_esta_siendo_procesado.=" 'inicio el proceso'  ";
			$query_insert_esta_siendo_procesado.=" ) ";
			$query_insert_esta_siendo_procesado.=" ; ";
			$error_bd="";
			$bool_hubo_error_query=$obj->insertar_no_warning_get_error_no_crea_cierra($query_insert_esta_siendo_procesado, $error_bd);
			if($error_bd!="")
			{
				if(connection_aborted()==false)
				{
					echo "<script>alert('error al iniciar el estado actual de validacion en tiempo real  4505 ');</script>";
				}
			}//fin if
		    }//fin if
		    else if($this->tipo_entidad_que_efectua_el_cargue=="agrupado_eapb")
		    {
			$query_insert_esta_siendo_procesado="";
			$query_insert_esta_siendo_procesado.=" INSERT INTO gioss_4505_esta_validando_actualmente ";
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
			$query_insert_esta_siendo_procesado.=" '".$this->cod_eapb_global."',  ";
			$query_insert_esta_siendo_procesado.=" '".$nombre_archivo4505."',  ";
			$query_insert_esta_siendo_procesado.=" '".$this->cadena_fecha_corte."',  ";
			$query_insert_esta_siendo_procesado.=" '".$fecha_actual."',  ";
			$query_insert_esta_siendo_procesado.=" '".$tiempo_actual."',  ";
			$query_insert_esta_siendo_procesado.=" '".$this->nick_user."',  ";
			$query_insert_esta_siendo_procesado.=" 'SI',  ";
			$query_insert_esta_siendo_procesado.=" 'NO',  ";
			$query_insert_esta_siendo_procesado.=" 'inicio el proceso'  ";
			$query_insert_esta_siendo_procesado.=" ) ";
			$query_insert_esta_siendo_procesado.=" ; ";
			$error_bd="";
			$bool_hubo_error_query=$obj->insertar_no_warning_get_error_no_crea_cierra($query_insert_esta_siendo_procesado, $error_bd);
			if($error_bd!="")
			{
				if(connection_aborted()==false)
				{
					echo "<script>alert('error al iniciar el estado actual de validacion en tiempo real  4505 ');</script>";
				}
			}//fin if
		    }//fin else if
		    
		    //FIN INSERTA AEN ESTA VALIDANDO ACTUALMENTE
		    
		    //PARTE NUMERO SECUENCIA	    
		    //nota: la fecha de validacion de la tabla numero de secuencia corresponde a la fecha de corte no el dia que se realizo la validacion
		    if($this->tipo_entidad_que_efectua_el_cargue=="individual_ips"
		    	|| $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips"
		       || $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120"
		       )
		    {
			$sql_query_inserta_seq="";
			$sql_query_inserta_seq.=" INSERT INTO gioss_numero_secuencia_archivos_4505 ";
			$sql_query_inserta_seq.=" ( ";
			$sql_query_inserta_seq.=" fecha_de_corte, ";
			$sql_query_inserta_seq.=" codigo_eapb, ";
			$sql_query_inserta_seq.=" codigo_prestador_servicios_salud, ";
			$sql_query_inserta_seq.=" nombre_archivo_4505, ";
			$sql_query_inserta_seq.=" numero_secuencia ";
			$sql_query_inserta_seq.=" ) ";
			$sql_query_inserta_seq.=" VALUES ";
			$sql_query_inserta_seq.=" ( ";
			$sql_query_inserta_seq.=" '".$this->old_fecha_de_corte_periodo_mensual."', ";
			$sql_query_inserta_seq.=" '".$this->cod_eapb_global."', ";
			$sql_query_inserta_seq.=" '".$this->cod_registro_especial_pss."', ";
			$sql_query_inserta_seq.=" '".$nombre_archivo4505."', ";
			$sql_query_inserta_seq.=" '".$secuencia_dependiendo_existencia."' ";
			$sql_query_inserta_seq.=" ) ";
			$sql_query_inserta_seq.=" ; ";
			$error_bd_seq="";
			$bool_hubo_error_query=$obj->insertar_no_warning_get_error_no_crea_cierra($sql_query_inserta_seq, $error_bd_seq);
			if($error_bd_seq!="")
			{
				$errores_bd_estado_numero_sec="";
				$errores_bd_estado_numero_sec.=$error_bd_seq."<br>";
				if(connection_aborted()==false)
				{
				    echo "<script>alert(' ERROR NUMERO SECUENCIA: ".$this->procesar_mensaje($errores_bd_estado_numero_sec)."');</script>";
				}
			}
		    }//fin if
		    else if($this->tipo_entidad_que_efectua_el_cargue=="agrupado_eapb")
		    {
			$sql_query_inserta_seq="";
			$sql_query_inserta_seq.=" INSERT INTO gioss_numero_secuencia_archivos_4505 ";
			$sql_query_inserta_seq.=" ( ";
			$sql_query_inserta_seq.=" fecha_de_corte, ";
			$sql_query_inserta_seq.=" codigo_eapb, ";
			$sql_query_inserta_seq.=" codigo_prestador_servicios_salud, ";
			$sql_query_inserta_seq.=" nombre_archivo_4505, ";
			$sql_query_inserta_seq.=" numero_secuencia ";
			$sql_query_inserta_seq.=" ) ";
			$sql_query_inserta_seq.=" VALUES ";
			$sql_query_inserta_seq.=" ( ";
			$sql_query_inserta_seq.=" '".$this->old_fecha_de_corte_periodo_mensual."', ";
			$sql_query_inserta_seq.=" '".$this->cod_eapb_global."', ";
			$sql_query_inserta_seq.=" 'AGRUP_EAPB', ";
			$sql_query_inserta_seq.=" '".$nombre_archivo4505."', ";
			$sql_query_inserta_seq.=" '".$secuencia_dependiendo_existencia."' ";
			$sql_query_inserta_seq.=" ) ";
			$sql_query_inserta_seq.=" ; ";
			$error_bd_seq="";
			$bool_hubo_error_query=$obj->insertar_no_warning_get_error_no_crea_cierra($sql_query_inserta_seq, $error_bd_seq);
			if($error_bd_seq!="")
			{
				$errores_bd_estado_numero_sec="";
				$errores_bd_estado_numero_sec.=$error_bd_seq."<br>";
				if(connection_aborted()==false)
				{
				    echo "<script>alert(' ERROR NUMERO SECUENCIA: ".$this->procesar_mensaje($errores_bd_estado_numero_sec)."');</script>";
				}
			}
		    }//fin if
		    //FIN PARTE NUMERO SECUENCIA
		    
		    
		    
		    $archivo_a_verificar = fopen($file, 'r') or exit("No se pudo abrir el archivo");
		    
		    $cont_linea=0;
		    $consecutivo_errores=1;
		    
		    
		    
		    //DIRECTORIO DE LOS ARCHIVOS
		    $ruta_temporales="../TEMPORALES/";
		    $nombre_archivo_sin_extension=explode(".",$nombre_archivo4505)[0];
		    $nueva_carpeta=$ruta_temporales.$nombre_archivo_sin_extension.$fecha_para_archivo."V";
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
		    $ruta_temporales=$nueva_carpeta."/";
		    //FIN DIRECTORIO DE LOS ARCHIVOS
		    
		    
		    //PARTE CREA ARCHIVO INCONSISTENCIAS
		    
		    $ruta_inconsistencias_campos=$ruta_temporales."log_inconsRT_".$nombre_archivo_sin_extension."_".$secuencia_dependiendo_existencia."_".$fecha_para_archivo.".csv";
		    
		    //se remplaza el archivo si ya existe con modo w		
		    $log_errores = fopen($ruta_inconsistencias_campos, "w") or die("fallo la creacion del archivo modo:w log_inconsistencias");
		    
		    $titulos="";
		    $titulos.="consecutivo,nombre archivo 4505,codigo tipo inconsistencia,tipo inconsistencia,codigo grupo inconsistencia,grupo inconsistencia,";
		    $titulos.="codigo detalle inconsistencia,detalle inconsistencia, numero de linea, numero de campo, codigo habilitacion prestador, tipo identificacion afiliado, numero identificacion afiliado";
		    
		    //PARTE REESCRIBE TITULOS INCONSISTENCIAS PARA CSV SIMPLE DE ABRIR
		    $columnas_titulos_inconsistencias_para_bd=explode(",", $titulos);
		    $error_titulos_procesado_csv_excel="";
		    $error_titulos_procesado_csv_excel.="=\"".implode("\";=\"", $columnas_titulos_inconsistencias_para_bd)."\"";
		    //FIN PARTE REESCRIBE TITULOS INCONSISTENCIAS PARA CSV SIMPLE DE ABRIR
		    fwrite($log_errores, utf8_decode($error_titulos_procesado_csv_excel) . "\n");		
		    
		    fclose($log_errores);
		    
		    //FIN PARTE CREA ARCHIVO INCONSISTENCIAS
		    
		    //extraccion consecutivo
		    $consecutivo_nombre_archivo_string=substr($nombre_archivo_sin_extension,-2);
		    $consecutivo_nombre_archivo_int_plus=intval($consecutivo_nombre_archivo_string)+1;
		    
		    $consecutivo_nombre_archivo_string_plus="";
		    if($consecutivo_nombre_archivo_int_plus<9)
		    {
			$consecutivo_nombre_archivo_string_plus="0".$consecutivo_nombre_archivo_int_plus;
		    }//fin if
			
		    $nombre_archivo_sin_consecutivo=substr($nombre_archivo_sin_extension,0,-2);
		    //fin extraccion consecutivo
		    
		    //PARTE PRIMERA LINEA
		    $linea_1_procesada= str_replace("\n","",preg_replace('/[^a-zA-Z0-9\s,\-\|]/', '', $linea1));
		    $array_linea_procesada=explode("|",$linea_1_procesada);
		    //FIN PART PRIMEAR LINEA
		    
		    //PARTE CREA ARCHIVO REGISTROS EXCLUIDOS MALOS
		    $consecutivo_archivo_excluido_registros_malos=1;
		    $ruta_archivo_registros_malos=$ruta_temporales.$nombre_archivo_sin_extension."RM".".txt";		    
		    //se remplaza el archivo si ya existe con modo w		
		    $archivo_excluido_registros_malos = fopen($ruta_archivo_registros_malos, "w") or die("fallo la creacion del archivo modo:w malos");		    		    
		    fwrite($archivo_excluido_registros_malos, $linea_1_procesada." ");		    
		    fclose($archivo_excluido_registros_malos);		    
		    //FIN PARTE CREA ARCHIVO REGISTROS EXCLUIDOS MALOS
		    
		    //PARTE CREA INCONSISTENCIAS SOLO PARA ARCHIVO REGISTROS EXCLUIDOS MALOS
		    $consecutivo_incons_archivo_excluido_registros_malos=1;
		    $ruta_inconsistencias_archivo_registros_malos=$ruta_temporales."log_inconsRM"."_".$nombre_archivo_sin_extension."_".$secuencia_dependiendo_existencia."_".$fecha_para_archivo.".csv";
		    //se remplaza el archivo si ya existe con modo w		
		    $log_errores_archivo_registros_malos = fopen($ruta_inconsistencias_archivo_registros_malos, "w") or die("fallo la creacion del archivo modo:w log_inconsistencias_MALOS");
		    $titulos="";
		    $titulos.="consecutivo,nombre archivo 4505,codigo tipo inconsistencia,tipo inconsistencia,codigo grupo inconsistencia,grupo inconsistencia,";
		    $titulos.="codigo detalle inconsistencia,detalle inconsistencia, numero de linea, numero de campo, codigo habilitacion prestador, tipo identificacion afiliado, numero identificacion afiliado";
		    //PARTE REESCRIBE TITULOS INCONSISTENCIAS PARA CSV SIMPLE DE ABRIR
		    $columnas_titulos_inconsistencias_para_bd=explode(",", $titulos);
		    $error_titulos_procesado_csv_excel="";
		    $error_titulos_procesado_csv_excel.="=\"".implode("\";=\"", $columnas_titulos_inconsistencias_para_bd)."\"";
		    //FIN PARTE REESCRIBE TITULOS INCONSISTENCIAS PARA CSV SIMPLE DE ABRIR
		    fwrite($log_errores_archivo_registros_malos, utf8_decode($error_titulos_procesado_csv_excel) . "\n");		
		    fclose($log_errores_archivo_registros_malos);		    
		    //FIN PARTE CREA INCONSISTENCIAS SOLO PARA ARCHIVO REGISTROS EXCLUIDOS MALOS
		    
		    //PARTE CREA ARCHIVO REGISTROS EXCLUIDOS BUENOS
		    $consecutivo_archivo_excluido_registros_buenos=1;
		    $ruta_archivo_registros_buenos=$ruta_temporales.$nombre_archivo_sin_extension."RB".".txt";		    
		    //se remplaza el archivo si ya existe con modo w		
		    $archivo_excluido_registros_buenos = fopen($ruta_archivo_registros_buenos, "w") or die("fallo la creacion del archivo modo:w buenos");		    		    
		    fwrite($archivo_excluido_registros_buenos, $linea_1_procesada." ");		    
		    fclose($archivo_excluido_registros_buenos);
		    //FIN PARTE CREA ARCHIVO REGISTROS EXCLUIDOS BUENOS

		    
		    //PARTE CREA ARCHIVO REGISTROS ESTRUCTURA MALA
		    $this->global_ruta_estructura_mala=$ruta_temporales.$nombre_archivo_sin_extension."ESTRUCTMAL".".txt";		    
		    //se remplaza el archivo si ya existe con modo w		
		    $archivo_excluido_estructura_mala = fopen($this->global_ruta_estructura_mala, "w") or die("fallo la creacion del archivo modo:w estructura mala ");		    		    
		    fwrite($archivo_excluido_estructura_mala, $linea_1_procesada." ");		    
		    fclose($archivo_excluido_estructura_mala);		    
		    //FIN PARTE CREA ARCHIVO REGISTROS ESTRUCTURA MALA


		    //PARTE CREA ARCHIVO AFILIADOS SIN REGISTROS EN BD
		    $this->global_ruta_afiliados_no_registrados=$ruta_temporales.$nombre_archivo_sin_extension."NA".".txt";		    
		    //se remplaza el archivo si ya existe con modo w		
		    $archivo_excluido_registros_afiliados_no_hallados = fopen($this->global_ruta_afiliados_no_registrados, "w") or die("fallo la creacion del archivo modo:w afiliados_no_hallados");		    		    
		    fwrite($archivo_excluido_registros_afiliados_no_hallados, $linea_1_procesada." ");		    
		    fclose($archivo_excluido_registros_afiliados_no_hallados);		    
		    //FIN PARTE CREA ARCHIVO AFILIADOS SIN REGISTROS EN BD

		    //PARTE CREA ARCHIVO AFILIADOS CON SEXO Y FECHA NACIMIENTO CORREGIDOS DE ACUERDO A BD
		    $this->global_consecutivo_solo_registros_con_sexo_fecha_nacimiento_corregidos=1;
		    $this->global_ruta_registros_con_afiliados_modificados=$ruta_temporales.$nombre_archivo_sin_extension."CES".".txt";		    
		    //se remplaza el archivo si ya existe con modo w		
		    $archivo_excluido_registros_con_afiliados_modificados = fopen($this->global_ruta_registros_con_afiliados_modificados, "w") or die("fallo la creacion del archivo modo:w registros_con_afiliados_modificados ");		    		    
		    fwrite($archivo_excluido_registros_con_afiliados_modificados, $linea_1_procesada." ");		    
		    fclose($archivo_excluido_registros_con_afiliados_modificados);		    
		    //FIN PARTE CREA ARCHIVO AFILIADOS CON SEXO Y FECHA NACIMEINTO CORREGIDOS DE ACUERDO A BD

		    //PARTE CREA ARCHIVO AFILIADOS CON CAMPOS ADICIONALES PARA ESTADISTICAS
		    $this->global_consecutivo_solo_registros_solo_afiliados_con_campos_adicionales=1;
		    $this->global_ruta_registros_con_afiliados_modificados_y_campos_estadisticas_adicionales=$ruta_temporales.$nombre_archivo_sin_extension."SAESTD".".txt";		    
		    //se remplaza el archivo si ya existe con modo w		
		    $archivo_solo_afiliados_con_estadisticas = fopen($this->global_ruta_registros_con_afiliados_modificados_y_campos_estadisticas_adicionales, "w") or die("fallo la creacion del archivo modo:w registros_con_afiliados_modificados ");		    		    
		    fwrite($archivo_solo_afiliados_con_estadisticas, $linea_1_procesada." ");		    
		    fclose($archivo_solo_afiliados_con_estadisticas);		    
		    //FIN PARTE CREA ARCHIVO AFILIADOS CON CAMPOS ADICIONALES PARA ESTADISTICAS

		    //PARTE CREA ARCHIVO AFILIADOS CON SEXO DIFERENTES Y CORREGIDOS DE ACUERDO A BD
		    $this->global_consecutivo_solo_registros_con_sexo_dif_corregidos=1;
		    $this->global_ruta_registros_con_afiliados_modificados_sexo_diferentes=$ruta_temporales.$nombre_archivo_sin_extension."SEXDIF".".txt";		    
		    //se remplaza el archivo si ya existe con modo w		
		    $archivo_excluido_registros_con_afiliados_modificados_sex_dif = fopen($this->global_ruta_registros_con_afiliados_modificados_sexo_diferentes, "w") or die("fallo la creacion del archivo modo:w registros_con_afiliados_modificados ");		    		    
		    fwrite($archivo_excluido_registros_con_afiliados_modificados_sex_dif, $linea_1_procesada." ");		    
		    fclose($archivo_excluido_registros_con_afiliados_modificados_sex_dif);		    
		    //FIN PARTE CREA ARCHIVO AFILIADOS CON SEXO DIFERENTES Y CORREGIDOS DE ACUERDO A BD

		    //PARTE CREA ARCHIVO AFILIADOS CON FECHA NACIMIENTO DIFERENTES Y CORREGIDOS DE ACUERDO A BD
		    $this->global_consecutivo_solo_registros_con_fecha_nacimiento_dif_corregidos=1;
		    $this->global_ruta_registros_con_afiliados_modificados_fecha_nacimiento_diferentes=$ruta_temporales.$nombre_archivo_sin_extension."FCHNACDIF".".txt";		    
		    //se remplaza el archivo si ya existe con modo w		
		    $archivo_excluido_registros_con_afiliados_modificados_fecha_nac_dif = fopen($this->global_ruta_registros_con_afiliados_modificados_fecha_nacimiento_diferentes, "w") or die("fallo la creacion del archivo modo:w registros_con_afiliados_modificados ");		    		    
		    fwrite($archivo_excluido_registros_con_afiliados_modificados_fecha_nac_dif, $linea_1_procesada." ");		    
		    fclose($archivo_excluido_registros_con_afiliados_modificados_fecha_nac_dif);		    
		    //FIN PARTE CREA ARCHIVO AFILIADOS CON FECHA NACIMIENTO DIFERENTES Y CORREGIDOS DE ACUERDO A BD

		    //PARTE CREA NUEVO ARCHIVO ORIGINAL CON SEXO Y FECHA NACIMIENTO CORREGIDOS DE ACUERDO A BD
		    $this->global_consecutivo_nuevo_original_con_sexo_fecha_nacimiento_corregidos=1;
		    $this->global_ruta_archivo_plano_nuevo_original_con_sexo_fecha_nacimiento_corregidos=$ruta_temporales.$nombre_archivo_sin_extension."SA".".txt";		    
		    //se remplaza el archivo si ya existe con modo w		
		    $archivo_nuevo_original_con_sexo_fecha_nacimiento_corregidos = fopen($this->global_ruta_archivo_plano_nuevo_original_con_sexo_fecha_nacimiento_corregidos, "w") or die("fallo la creacion del archivo modo:w plano_nuevo_original_con_sexo_fecha_nacimiento_corregidos ");		    		    
		    fwrite($archivo_nuevo_original_con_sexo_fecha_nacimiento_corregidos, $linea_1_procesada." ");		    
		    fclose($archivo_nuevo_original_con_sexo_fecha_nacimiento_corregidos);		    
		    //FIN PARTE CREA NUEVO ARCHIVO ORIGINAL CON SEXO Y FECHA NACIMIENTO CORREGIDOS DE ACUERDO A BD

		    //PARTE CREA ARCHIVO JUSTIFICACION VALORES PERMITIDOS
		    $this->globalRutaTemporalesEscribirArchivos=$ruta_temporales;
		    //FIN PARTE CREA ARCHIVO JUSTIFICACION VALORES PERMITIDOS

		    
		    $this->global_array_rutas_agrupados_ips_para_cada_eapb=array();
		    $this->global_array_consecutivo_agrupados_ips_para_cada_eapb=array();
		    $this->global_array_eapb_para_carpetas=array();

		    
		    //PARTE CREA ARCHIVO FILTRADO
		    $ruta_temporales_para_filtrado="../TEMPORALES/";
		    $ruta_carpeta_filtrado=$ruta_temporales_para_filtrado."/".$this->nick_user."_filtrado_pyp_".$fecha_para_archivo;
		    if(!file_exists($ruta_carpeta_filtrado))
		    {
			    mkdir($ruta_carpeta_filtrado, 0700);
		    }//fin if
		    $ruta_archivo_filtrado=$ruta_carpeta_filtrado."/".$nombre_archivo_sin_extension.".txt";
		    $archivo_filtrado=fopen($ruta_archivo_filtrado, "w") or die("fallo la creacion del archivo modo:w filtrado");
		    fclose($archivo_filtrado);
		    //FIN PARTE CREA ARCHIVO FILTRADO

		    
		    if($this->tipo_entidad_que_efectua_el_cargue=="individual_ips"
		    	|| $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips"
		       || $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120"
		    	)
		    {
			//BORRA RECHAZADOS PREVIOS DEL MISMO ARCHIVO		
			$sql_delete_rechazados="";
			$sql_delete_rechazados.=" DELETE FROM gios_datos_rechazados_r4505 WHERE ";
			$sql_delete_rechazados.=" cod_prestador_servicios_salud='".$this->cod_registro_especial_pss."' AND ";
			$sql_delete_rechazados.=" codigo_eapb='".$this->cod_eapb_global."' AND ";
			$sql_delete_rechazados.=" fecha_de_corte = '".$this->old_fecha_de_corte_periodo_mensual."' AND ";
			$sql_delete_rechazados.=" nombre_archivo ='".$nombre_archivo4505."' ; ";
			$error_bd_seq="";
			$bool_hubo_error_query=$obj->insertar_no_warning_get_error_no_crea_cierra($sql_delete_rechazados, $error_bd_seq);		
			//FIN BORRA RECHAZADOS PREVIOS DEL MISMO ARCHIVO
		    }
		    else if($this->tipo_entidad_que_efectua_el_cargue=="ent_territoriales")
		    {
			//BORRA RECHAZADOS PREVIOS DEL MISMO ARCHIVO		
			$sql_delete_rechazados="";
			$sql_delete_rechazados.=" DELETE FROM gioss_archivo_4505_rechazado_para_eapb WHERE ";
			$sql_delete_rechazados.=" codigo_eapb='".$this->cod_registro_especial_pss."' AND ";
			$sql_delete_rechazados.=" fecha_de_corte = '".$this->old_fecha_de_corte_periodo_mensual."' AND ";
			$sql_delete_rechazados.=" nombre_archivo ='".$nombre_archivo4505."' AND  ";
			$sql_delete_rechazados.=" codigo_departamento ='".$this->cod_dpto_filtro."' AND  ";
			$sql_delete_rechazados.=" codigo_municipio ='".$this->cod_mpio_filtro."'   ";
			$sql_delete_rechazados.=" ;  ";
			$error_bd_seq="";
			$bool_hubo_error_query=$obj->insertar_no_warning_get_error_no_crea_cierra($sql_delete_rechazados, $error_bd_seq);		
			//FIN BORRA RECHAZADOS PREVIOS DEL MISMO ARCHIVO
		    }
		    else if($this->tipo_entidad_que_efectua_el_cargue=="agrupado_eapb")
		    {
			//BORRA RECHAZADOS PREVIOS DEL MISMO ARCHIVO		
			$sql_delete_rechazados="";
			$sql_delete_rechazados.=" DELETE FROM gios_datos_rechazados_r4505 WHERE ";
			$sql_delete_rechazados.=" cod_prestador_servicios_salud='AGRUP_EAPB' AND ";
			$sql_delete_rechazados.=" codigo_eapb='".$this->cod_eapb_global."' AND ";
			$sql_delete_rechazados.=" fecha_de_corte = '".$this->old_fecha_de_corte_periodo_mensual."' AND ";
			$sql_delete_rechazados.=" nombre_archivo ='".$nombre_archivo4505."' ; ";
			$error_bd_seq="";
			$bool_hubo_error_query=$obj->insertar_no_warning_get_error_no_crea_cierra($sql_delete_rechazados, $error_bd_seq);		
			//FIN BORRA RECHAZADOS PREVIOS DEL MISMO ARCHIVO
		    }
    
		    //INICIALIZACION ARRAY ERRORES CAMPO INDIVIDUAL POR CADA LINEA 
		    $array_contador_total_errores_obligatorios_campo=array();
		    $cont_llenado=0;
		    while($cont_llenado<119)
		    {
		    	$array_contador_total_errores_obligatorios_campo[$cont_llenado]=0;
		    	$cont_llenado++;
		    }//fin while
		    $array_contador_total_errores_obligatorios_campo[999]=0;

		    $array_contador_total_inconsistencias_campo_0105=array();
		    $cont_llenado=0;
		    while($cont_llenado<119)
		    {
		    	$array_contador_total_inconsistencias_campo_0105[$cont_llenado]=0;
		    	$cont_llenado++;
		    }//fin while
		    $array_contador_total_inconsistencias_campo_0105[999]=0;

		    $array_contador_total_inconsistencias_campo_0104=array();
		    $cont_llenado=0;
		    while($cont_llenado<119)
		    {
		    	$array_contador_total_inconsistencias_campo_0104[$cont_llenado]=0;
		    	$cont_llenado++;
		    }//fin while
		    $array_contador_total_inconsistencias_campo_0104[999]=0;

		    $array_contador_total_inconsistencias_campo_0103=array();
		    $cont_llenado=0;
		    while($cont_llenado<119)
		    {
		    	$array_contador_total_inconsistencias_campo_0103[$cont_llenado]=0;
		    	$cont_llenado++;
		    }//fin while
		    $array_contador_total_inconsistencias_campo_0103[999]=0;

		    $array_contador_total_inconsistencias_campo_0102=array();
		    $cont_llenado=0;
		    while($cont_llenado<119)
		    {
		    	$array_contador_total_inconsistencias_campo_0102[$cont_llenado]=0;
		    	$cont_llenado++;
		    }//fin while
		    $array_contador_total_inconsistencias_campo_0102[999]=0;

		    $array_contador_total_inconsistencias_campo_0101=array();
		    $cont_llenado=0;
		    while($cont_llenado<119)
		    {
		    	$array_contador_total_inconsistencias_campo_0101[$cont_llenado]=0;
		    	$cont_llenado++;
		    }//fin while
		    $array_contador_total_inconsistencias_campo_0101[999]=0;
		    //INICIALIZACION ARRAY ERRORES CAMPO INDIVIDUAL POR CADA LINEA 
    
		    //parte que trae las descripciones de las inconsistencias
			$array_tipo_inconsistencia=array();
			$array_grupo_inconsistencia=array();
			$array_detalle_inconsistencia=array();

			$query1_tipo_validacion="SELECT * FROM gioss_tipo_inconsistencias;";
			$resultado_query1_tipo_validacion=$obj->consultar2_no_crea_cierra($query1_tipo_validacion);
			foreach($resultado_query1_tipo_validacion as $tipo_validacion)
			{
				$array_tipo_inconsistencia[$tipo_validacion["tipo_validacion"]]=$tipo_validacion["descripcion_tipo_validacion"];
			}
			$query2_grupo_validacion="SELECT * FROM gioss_grupo_inconsistencias;";
			$resultado_query2_grupo_validacion=$obj->consultar2_no_crea_cierra($query2_grupo_validacion);
			foreach($resultado_query2_grupo_validacion as $grupo_validacion)
			{
				$array_grupo_inconsistencia[$grupo_validacion["grupo_validacion"]]=$grupo_validacion["descripcion_grupo_validacion"];
			}
			$query3_detalle_validacion="SELECT * FROM gioss_detalle_inconsistecias_4505;";
			$resultado_query3_detalle_validacion=$obj->consultar2_no_crea_cierra($query3_detalle_validacion);
			foreach($resultado_query3_detalle_validacion as $detalle_validacion)
			{
				$array_detalle_inconsistencia[$detalle_validacion["codigo_detalle_inconsistencia"]]=$detalle_validacion["descripcion_inconsistencia"];
			}	
			//fin parte que trae las descripciones de las inconsistencias

			$query_nombre_campo="SELECT * FROM valores_permitidos_4505 ORDER BY numero_campo_norma::numeric asc;";
			$resultados_nombre_campo=$obj->consultar2_no_crea_cierra($query_nombre_campo);
			$this->globalNombreCampos=$resultados_nombre_campo;
    
		    //PARTE VALIDA ARCHIVO Y ESCRIBE INCONSISTENCIAS
		    $cont_porcentaje=0;
		    $contador_filtrados=0;
		    $fue_cerrada_la_gui=false;
		    while (!feof($archivo)) 
		    {
			    if($fue_cerrada_la_gui==false)
			    {
				if(connection_aborted()==true)
				{
				    $fue_cerrada_la_gui=true;
				}
			    }//fin if verifica si el usuario cerro la pantalla
			    
			    //PARTE VERIFICA SI FUE CANCELADA LA EJECUCION DEL SCRIPT
			    if($this->tipo_entidad_que_efectua_el_cargue=="individual_ips"
				    || $this->tipo_entidad_que_efectua_el_cargue=="ent_territoriales"
				    || $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips"
		       		|| $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120"
				)
			    {
				 
				 if(connection_aborted()==false)
				{
					//echo "GUIA 5 gioss_4505_esta_validando_actualmente";
					ob_flush();
				   flush();
				}
				$verificar_si_ejecucion_fue_cancelada="";
				$verificar_si_ejecucion_fue_cancelada.=" SELECT esta_ejecutando FROM gioss_4505_esta_validando_actualmente ";
				$verificar_si_ejecucion_fue_cancelada.=" WHERE fecha_remision='".$this->cadena_fecha_corte."' ";
				$verificar_si_ejecucion_fue_cancelada.=" AND codigo_entidad_reportadora='".$this->cod_registro_especial_pss."' ";
				$verificar_si_ejecucion_fue_cancelada.=" AND nombre_archivo='".$nombre_archivo4505."'  ";
				$verificar_si_ejecucion_fue_cancelada.=" AND nick_usuario='".$this->nick_user."'  ";
				$verificar_si_ejecucion_fue_cancelada.=" AND fecha_validacion='".$fecha_actual."'  ";
				$verificar_si_ejecucion_fue_cancelada.=" AND hora_validacion='".$tiempo_actual."'  ";
				$verificar_si_ejecucion_fue_cancelada.=" ; ";
				$error_bd_seq="";
				$resultados_si_ejecucion_fue_cancelada=$obj->consultar_no_warning_get_error_no_crea_cierra($verificar_si_ejecucion_fue_cancelada, $error_bd_seq);		
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
			    }//fin prestador archivo individual o entidad territorial
			    else if($this->tipo_entidad_que_efectua_el_cargue=="agrupado_eapb")
			    {
				 
				 if(connection_aborted()==false)
				{
					//echo "GUIA 4 gioss_4505_esta_validando_actualmente";
					ob_flush();
				   flush();
				}
				
				$verificar_si_ejecucion_fue_cancelada="";
				$verificar_si_ejecucion_fue_cancelada.=" SELECT esta_ejecutando FROM gioss_4505_esta_validando_actualmente ";
				$verificar_si_ejecucion_fue_cancelada.=" WHERE fecha_remision='".$this->cadena_fecha_corte."' ";
				$verificar_si_ejecucion_fue_cancelada.=" AND codigo_entidad_reportadora='".$this->cod_eapb_global."' ";
				$verificar_si_ejecucion_fue_cancelada.=" AND nombre_archivo='".$nombre_archivo4505."'  ";
				$verificar_si_ejecucion_fue_cancelada.=" AND nick_usuario='".$this->nick_user."'  ";
				$verificar_si_ejecucion_fue_cancelada.=" AND fecha_validacion='".$fecha_actual."'  ";
				$verificar_si_ejecucion_fue_cancelada.=" AND hora_validacion='".$tiempo_actual."'  ";
				$verificar_si_ejecucion_fue_cancelada.=" ; ";
				$error_bd_seq="";
				$resultados_si_ejecucion_fue_cancelada=$obj->consultar_no_warning_get_error_no_crea_cierra($verificar_si_ejecucion_fue_cancelada, $error_bd_seq);		
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
			    }//fin agrupado 
			    //FIN PARTE VERIFICA SI FUE CANCELADA LA EJECUCION DEL SCRIPT
			    
			    //obtiene la linea actual
			    $linea = fgets($archivo);		    
			    $linea = str_replace(",",".",$linea);
			    $campos = explode("|", $linea);
			    
			    //PARTE FILTRADO (solo entidades territoriales)
			    $bool_se_tiene_en_cuenta=false;
			    if($this->tipo_entidad_que_efectua_el_cargue=="ent_territoriales")
			    {
				if($numLinea==0)
				{
				    $archivo_filtrado=fopen($ruta_archivo_filtrado, "a") or die("fallo la creacion del archivo modo:a filtrado");
				    fwrite($archivo_filtrado, $linea);
				    fclose($archivo_filtrado);
				    
				    $contador_filtrados++;
				}
				else
				{
				    $cod_dpto_mpio=$this->cod_dpto_filtro.$this->cod_mpio_filtro;
				    if($this->cod_mpio_filtro!="000")
				    {
					$extraccion_localizacion_del_codigo_prestador=substr($campos[2], 0, 5);
					if($cod_dpto_mpio==$extraccion_localizacion_del_codigo_prestador)
					{
					    $bool_se_tiene_en_cuenta=true;
					}
				    }//fin if
				    else
				    {
					$extraccion_localizacion_del_codigo_prestador=substr($campos[2], 0, 2);
					if($this->cod_dpto_filtro==$extraccion_localizacion_del_codigo_prestador)
					{
					    $bool_se_tiene_en_cuenta=true;
					}
				    }//fin else
				    
				    if($bool_se_tiene_en_cuenta==true)
				    {
					$archivo_filtrado=fopen($ruta_archivo_filtrado, "a") or die("fallo la creacion del archivo modo:a filtrado");
					fwrite($archivo_filtrado, $linea);
					fclose($archivo_filtrado);
					
					$contador_filtrados++;
				    }
				}//fin else no es la primera linea
			    }//fin if archivo viene de eapb
			    //FIN PARTE FILTRADO
			    
			    $array_resultado_validacion_linea=array();
			    $campos_linea_validos=true;
			    $errores_campos_linea="";
			    if($this->tipo_entidad_que_efectua_el_cargue=="individual_ips"
			    	|| $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips"
		       || $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120"
			    	)
			    {
				$array_resultado_validacion_linea=$this->Inconsistencias_y_Validaciones_CamposPyP($array_tipo_inconsistencia, $array_grupo_inconsistencia, $array_detalle_inconsistencia, $linea,$numLinea,$consecutivo_errores,$obj);
				$campos_linea_validos=$array_resultado_validacion_linea["es_valido"];			
				$errores_campos_linea = $array_resultado_validacion_linea["inconsistencias_linea"];
			    }
			    else if($this->tipo_entidad_que_efectua_el_cargue=="ent_territoriales" && $bool_se_tiene_en_cuenta==true)
			    {
				$array_resultado_validacion_linea=$this->Inconsistencias_y_Validaciones_CamposPyP($array_tipo_inconsistencia, $array_grupo_inconsistencia, $array_detalle_inconsistencia, $linea,$numLinea,$consecutivo_errores,$obj);
				$campos_linea_validos=$array_resultado_validacion_linea["es_valido"];			
				$errores_campos_linea = $array_resultado_validacion_linea["inconsistencias_linea"];
			    }
			    else if($this->tipo_entidad_que_efectua_el_cargue=="agrupado_eapb")
			    {
				$array_resultado_validacion_linea=$this->Inconsistencias_y_Validaciones_CamposPyP($array_tipo_inconsistencia, $array_grupo_inconsistencia, $array_detalle_inconsistencia, $linea,$numLinea,$consecutivo_errores,$obj);
				$campos_linea_validos=$array_resultado_validacion_linea["es_valido"];			
				$errores_campos_linea = $array_resultado_validacion_linea["inconsistencias_linea"];
			    }

			    //PARTE ACTUALIZA EN EL ARRAY QUE USA PARA CARGAR AS TABLAS CON LOS DATOS DE AFILIADOS
			    if(is_array($this->array_campos_con_datos_afiliados)==true
			    	&& count($this->array_campos_con_datos_afiliados)==count($campos)
			    )//fin condicion
			    {
			    	$contador_actualizar_detalles=0;
			    	while($contador_actualizar_detalles<=13)
			    	{
			    		$valor_actual_campo_actualizar=trim($this->array_campos_con_datos_afiliados[$contador_actualizar_detalles]);
			    		if($valor_actual_campo_actualizar!="")
			    		{
			    			$campos[$contador_actualizar_detalles]=$valor_actual_campo_actualizar;
			    		}//fin if
			    		$contador_actualizar_detalles++;
			    	}//fin while
			    }//fin if
			    //FIN PARTE ACTUALIZA EN EL ARRAY QUE USA PARA CARGAR AS TABLAS CON LOS DATOS DE AFILIADOS

			    $cantidad_errores_por_linea=0;
			    
			    if($errores_campos_linea!="")
			    {
				    //ABRE EL ARCHIVO Y HACE EXPLODE
				    $array_errores_linea=explode("|", $errores_campos_linea);
				    //se abre con modo a para que adicione
				    $log_errores = fopen($ruta_inconsistencias_campos, "a") or die("fallo la creacion del archivo modo:a log_inconsistencias");
				    
				    //PARTE LLENA ARRAY BOOLEANOS PARA QUE SOLO TENGA EN CUENTA LA PRIMERA INCONSISTENCIA DEL CAMPO
				    $array_booleano_primer_error_por_linea=array();
				    $cont_llenado=0;
				    while($cont_llenado<119)
				    {
				    	$array_booleano_primer_error_por_linea[$cont_llenado]=true;
				    	$cont_llenado++;
				    }//fin while
				    $array_booleano_primer_error_por_linea[999]=true;
				    //FIN PARTE LLENA ARRAY BOOLEANOS PARA QUE SOLO TENGA EN CUENTA LA PRIMERA INCONSISTENCIA DEL CAMPO
				    
				    $cont_error=0;
				    $mostrar_error_bd_inconsistencias="";
				    foreach ($array_errores_linea as $error) 
				    {
					    //PARTE SUBIR INCONSISTENCIAS ENCONTRADAS A BD
					    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					    $columnas_inconsistencias_para_bd=array();
					    $columnas_inconsistencias_para_bd=explode(",",$error);
					    
					    //PARTE INCREMENTA CONTADOR ERRORES POR CADA CAMPO PERO SOLO 1 POR LINEA
					    $cod_tipo_inconsistencia_temp=trim($columnas_inconsistencias_para_bd[2]);
					    $numero_campo_temp=trim($columnas_inconsistencias_para_bd[9]);

					    $cod_grupo_inconsistencia_temp=trim($columnas_inconsistencias_para_bd[4]);
					    //echo $cod_grupo_inconsistencia_temp." nc: ".$numero_campo_temp."<br>";

					    if($array_booleano_primer_error_por_linea[intval($numero_campo_temp)]==true
						&& $cod_tipo_inconsistencia_temp=="01"
						)
				    	{
				    		//echo "ENTRO ".$cod_tipo_inconsistencia_temp." nc: ".$numero_campo_temp."<br>";
				    		$array_contador_total_errores_obligatorios_campo[intval($numero_campo_temp)]++;
				    		$array_booleano_primer_error_por_linea[intval($numero_campo_temp)]=false;
				    	}//fin if

				    	if($cod_grupo_inconsistencia_temp=="0105")
				    	{
				    		//echo "ENTRO ".$cod_grupo_inconsistencia_temp." nc: ".$numero_campo_temp."<br>";
				    		$array_contador_total_inconsistencias_campo_0105[intval($numero_campo_temp)]++;
				    	}//fin if

				    	if($cod_grupo_inconsistencia_temp=="0104")
				    	{
				    		//echo "ENTRO ".$cod_grupo_inconsistencia_temp." nc: ".$numero_campo_temp."<br>";
				    		$array_contador_total_inconsistencias_campo_0104[intval($numero_campo_temp)]++;
				    	}//fin if

				    	if($cod_grupo_inconsistencia_temp=="0103")
				    	{
				    		//echo "ENTRO ".$cod_grupo_inconsistencia_temp." nc: ".$numero_campo_temp."<br>";
				    		$array_contador_total_inconsistencias_campo_0103[intval($numero_campo_temp)]++;
				    	}//fin if

				    	if($cod_grupo_inconsistencia_temp=="0102")
				    	{
				    		//echo "ENTRO ".$cod_grupo_inconsistencia_temp." nc: ".$numero_campo_temp."<br>";
				    		$array_contador_total_inconsistencias_campo_0102[intval($numero_campo_temp)]++;
				    	}//fin if

				    	if($cod_grupo_inconsistencia_temp=="0101")
				    	{
				    		//echo "ENTRO ".$cod_grupo_inconsistencia_temp." nc: ".$numero_campo_temp."<br>";
				    		$array_contador_total_inconsistencias_campo_0101[intval($numero_campo_temp)]++;
				    	}//fin if
				    	//FIN PARTE INCREMENTA CONTADOR ERRORES POR CADA CAMPO PERO SOLO 1 POR LINEA
					    
					    if(count($columnas_inconsistencias_para_bd)>=10)
					    {
						    //echo "Entro insertar errores linea ".$this->procesar_mensaje(trim($columnas_inconsistencias_para_bd[8]))." , del campo ".$this->procesar_mensaje(trim($columnas_inconsistencias_para_bd[9]))."<br>";
						    
						    //se insertan los datos de detalles de inconsistencia para 4505
						    
						    $sql_insertar_inconsistencia_rips="";
						    $sql_insertar_inconsistencia_rips.=" INSERT INTO gioss_reporte_inconsistencia_archivos_4505 ";
						    $sql_insertar_inconsistencia_rips.=" ( ";
						    $sql_insertar_inconsistencia_rips.=" numero_orden, ";
						    $sql_insertar_inconsistencia_rips.=" nombre_archivo_datos_originales, ";
						    $sql_insertar_inconsistencia_rips.=" cod_tipo_inconsitencia, ";
						    $sql_insertar_inconsistencia_rips.=" nombre_tipo_inconsistencia, ";
						    $sql_insertar_inconsistencia_rips.=" cod_grupo_inconsistencia, ";
						    $sql_insertar_inconsistencia_rips.=" nombre_grupo_inconsistencia, ";
						    $sql_insertar_inconsistencia_rips.=" cod_detalle_inconsistencia, ";
						    $sql_insertar_inconsistencia_rips.=" detalle_inconsistencia, ";
						    $sql_insertar_inconsistencia_rips.=" numero_linea, ";
						    $sql_insertar_inconsistencia_rips.=" numero_campo ";
						    $sql_insertar_inconsistencia_rips.=" ) ";
						    $sql_insertar_inconsistencia_rips.=" VALUES ";
						    $sql_insertar_inconsistencia_rips.=" ( ";
						    $sql_insertar_inconsistencia_rips.=" '".$secuencia_dependiendo_existencia."', ";
						    $sql_insertar_inconsistencia_rips.=" '".$nombre_archivo4505."', ";
						    $sql_insertar_inconsistencia_rips.=" '".$this->procesar_mensaje(trim($columnas_inconsistencias_para_bd[2]))."', ";
						    $sql_insertar_inconsistencia_rips.=" '".$this->procesar_mensaje(trim($columnas_inconsistencias_para_bd[3]))."', ";
						    $sql_insertar_inconsistencia_rips.=" '".$this->procesar_mensaje(trim($columnas_inconsistencias_para_bd[4]))."', ";
						    $sql_insertar_inconsistencia_rips.=" '".$this->procesar_mensaje(trim($columnas_inconsistencias_para_bd[5]))."', ";
						    $sql_insertar_inconsistencia_rips.=" '".$this->procesar_mensaje(trim($columnas_inconsistencias_para_bd[6]))."', ";
						    $sql_insertar_inconsistencia_rips.=" '".$this->procesar_mensaje(trim($columnas_inconsistencias_para_bd[7]))."', ";
						    $sql_insertar_inconsistencia_rips.=" '".$this->procesar_mensaje(trim($columnas_inconsistencias_para_bd[8]))."', ";
						    $sql_insertar_inconsistencia_rips.=" '".$this->procesar_mensaje(trim($columnas_inconsistencias_para_bd[9]))."' ";
						    $sql_insertar_inconsistencia_rips.=" ); ";
						    $error_bd_ins="";
						    $bool_hubo_error_query=$obj->insertar_no_warning_get_error_no_crea_cierra($sql_insertar_inconsistencia_rips, $error_bd_ins);
						    if($error_bd_ins!="")
						    {
							    $mostrar_error_bd_inconsistencias.=$error_bd_ins."<br>";
							    //echo "<script>alert('detallado error insert gioss_reporte_inconsistencia_archivos_4505 ".$this->procesar_mensaje($error_bd_ins)." ');</script>";
							    echo "No pudo subir detalle de inconsistencia: $error <br>";
						    }
						    //fin se insertan los datos de detalles de inconsistencia para 4505
					    }
					    //FIN PARTE SE SUBEN LOS DETALLES DE LAS INCONSISTENCIAS ENCONTRADAS A BD
					    
					    //PARTE REESCRIBE PARA CSV SIMPLE DE ABRIR
					    $error_procesado_csv_excel="";
					    $error_procesado_csv_excel.="=\"".implode("\";=\"", $columnas_inconsistencias_para_bd)."\"";
					    //FIN PARTE REESCRIBE PARA CSV SIMPLE DE ABRIR
					    //cambio $error por $error_procesado_csv_excel
					    fwrite($log_errores, utf8_decode($error_procesado_csv_excel) . "\n");
					    $cont_error++;

					    if($cod_tipo_inconsistencia_temp=="01")
						{
							$cantidad_errores_por_linea++;
						}//fin if solo si la inconsistencia es obligatoria
				    }//fin foreach
				    //$cantidad_errores_por_linea=$cont_error; //con este tendria en cuenta tambien las informativas
				    
				    //echo "Fin inserto errores linea<br>";
				    
				    //CIERRA EL ARCHIVO
				    fclose($log_errores);
			    }//fin if
			    
			    //ESCRITURA ARCHIVOS DE REGISTROS BUENOS Y MALOS
			    if( (count($campos)==119 && $this->tipo_entidad_que_efectua_el_cargue!="agrupado_ips120" )
			    	|| (count($campos)==120 && $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120" )
			    	)
			    {
					if($campos_linea_validos==true)
					{
					    //SE ADICIONA EL REGISTRO AL ARCHIVO CON LOS REGISTROS BUENOS
					    $archivo_excluido_registros_buenos = fopen($ruta_archivo_registros_buenos, "a") or die("fallo la creacion del archivo modo:a buenos");
					    //crea cadena con el registro actual
					    $registro_actual="";
					    $ccamp=0;
					    while($ccamp<count($campos))
					    {
						if($registro_actual!=""){$registro_actual.="|";}
						if($ccamp==1)
						{
						    $registro_actual.=$consecutivo_archivo_excluido_registros_buenos;
						}
						else
						{
						    $registro_actual.=$this->alphanumericAndSpace3($campos[$ccamp]);
						}
						$ccamp++;
					    }
					    //fin crea cadena con el registro actual
					    fwrite($archivo_excluido_registros_buenos,  "\n".$registro_actual);
					    fclose($archivo_excluido_registros_buenos);
					    $consecutivo_archivo_excluido_registros_buenos++;
					    //FIN SE ADICIONA EL REGISTRO AL ARCHIVO CON LOS REGISTROS BUENOS
					}//fin if
					else
					{
					    //SE ADICIONA EL REGISTRO AL ARCHIVO CON LOS REGISTROS MALOS
					    $archivo_excluido_registros_malos = fopen($ruta_archivo_registros_malos, "a") or die("fallo la creacion del archivo modo:a malos");
					    //crea cadena con el registro actual
					    $registro_actual="";
					    $ccamp=0;
					    while($ccamp<count($campos))
					    {
						if($registro_actual!=""){$registro_actual.="|";}
						if($ccamp==1)
						{
						    $registro_actual.=$consecutivo_archivo_excluido_registros_malos;
						}
						else
						{
						    $registro_actual.=$this->alphanumericAndSpace3($campos[$ccamp]);
						}
						$ccamp++;
					    }
					    //fin crea cadena con el registro actual
					    fwrite($archivo_excluido_registros_malos,  "\n".$registro_actual);
					    fclose($archivo_excluido_registros_malos);
					    $consecutivo_archivo_excluido_registros_malos++;
					    //FIN SE ADICIONA EL REGISTRO AL ARCHIVO CON LOS REGISTROS MALOS
					    
					    
					}//fin else

					//FILTRADOS DEL AGRUPADO IPS 120 CAMPOS
					if(count($campos)==120 && $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120" )
					{
					    //echo "entro filtrar en archivos por eapb ".$campos[119]."<br>";
						$arrayCaracteresEspecialesNoPermitidosDirectorios=array(" ","-","/","\\","|","*","<",">","?","¿","!","¡");
						$campos[119]=str_replace($arrayCaracteresEspecialesNoPermitidosDirectorios,"",$this->procesar_mensaje(trim($campos[119]) ) );
						//echo "Valor campo extra despues de procesar: ".$campos[119]."<br>";
						$eapb_para_carpeta=trim($campos[119]);
						$ruta_carpeta_eapb=$ruta_temporales.$eapb_para_carpeta."/";
						$ruta_archivo_en_carpeta_eapb=$ruta_carpeta_eapb."EAPB_".$eapb_para_carpeta.".txt";
						if(!file_exists($ruta_carpeta_eapb))
					    {
						    mkdir($ruta_carpeta_eapb, 0777);

						    if(in_array($ruta_archivo_en_carpeta_eapb, $this->global_array_rutas_agrupados_ips_para_cada_eapb) )
						    {
						    	//si ya existe en el array ya debio haber creado la carpeta para este, y creado el archivo
						    }
						    else
						    {
						    	//crea el archivo para la epab actual
						    	$array_linea1proc_pre_eapb=explode("|", $linea_1_procesada);
						    	if(isset($array_linea1proc_pre_eapb[1])==true)
						    	{
						    		$array_linea1proc_pre_eapb[1]=$eapb_para_carpeta;
						    	}
						    	$linea_1_con_la_eapb_actual= implode("|", $array_linea1proc_pre_eapb);
						    	$archivo_actual_eapb = fopen($ruta_archivo_en_carpeta_eapb, "w") or die("fallo la creacion del archivo modo:w  para la eapb actual ");
							    fwrite($archivo_actual_eapb, $linea_1_con_la_eapb_actual." ");		    
							    fclose($archivo_actual_eapb);

							    $consecutivo_actual_eapb=1;

						    	

						    	//tiene que poner aca la primera linea que encontro
						    	$ruta_actual_eapb=$ruta_archivo_en_carpeta_eapb;
						    	$archivo_actual_eapb = fopen($ruta_actual_eapb, "a") or die("fallo la creacion del archivo modo:a para la eapb actual primer registro encontrado");
							    //crea cadena con el registro actual
							    $registro_actual="";
							    $ccamp=0;
							    while($ccamp<count($campos) && $ccamp<119)
							    {
								if($registro_actual!=""){$registro_actual.="|";}
								if($ccamp==1)
								{
								    $registro_actual.=$consecutivo_actual_eapb;
								}
								else
								{
								    $registro_actual.=$this->alphanumericAndSpace3($campos[$ccamp]);
								}
								$ccamp++;
							    }
							    //fin crea cadena con el registro actual
							    fwrite($archivo_actual_eapb,  "\n".$registro_actual);
							    fclose($archivo_actual_eapb);
							    //FIN tiene que poner aca la primera linea que encontro

							    $this->global_array_rutas_agrupados_ips_para_cada_eapb[]=$ruta_archivo_en_carpeta_eapb;
						    	$this->global_array_consecutivo_agrupados_ips_para_cada_eapb[]=$consecutivo_actual_eapb+1;
							    $this->global_array_eapb_para_carpetas[]=$eapb_para_carpeta;


							}//fin else
					    }//fin if
					    else
					    {
					    	$llave_eapb=array_search($ruta_archivo_en_carpeta_eapb, $this->global_array_rutas_agrupados_ips_para_cada_eapb);

					    	$ruta_actual_eapb=$this->global_array_rutas_agrupados_ips_para_cada_eapb[$llave_eapb];
					    	$consecutivo_actual_eapb=$this->global_array_consecutivo_agrupados_ips_para_cada_eapb[$llave_eapb];

					    	$archivo_actual_eapb = fopen($ruta_actual_eapb, "a") or die("fallo la creacion del archivo modo:a para la eapb actual desde el 2do registro encontrado en adelante ");
						    //crea cadena con el registro actual
						    $registro_actual="";
						    $ccamp=0;
						    while($ccamp<count($campos) && $ccamp<119)
						    {
							if($registro_actual!=""){$registro_actual.="|";}
							if($ccamp==1)
							{
							    $registro_actual.=$consecutivo_actual_eapb;
							}
							else
							{
							    $registro_actual.=$this->alphanumericAndSpace3($campos[$ccamp]);
							}
							$ccamp++;
						    }
						    //fin crea cadena con el registro actual
						    fwrite($archivo_actual_eapb,  "\n".$registro_actual);
						    fclose($archivo_actual_eapb);

					    	$this->global_array_consecutivo_agrupados_ips_para_cada_eapb[$llave_eapb]++;
					    }//fin else
					}//fin if
					//FIN FILTRADOS DEL AGRUPADO IPS 120 CAMPOS

					//parte adicional para buenos y malos de cada filtrado
					if($campos_linea_validos==true)
					{
						//FILTRADOS DEL AGRUPADO IPS 120 CAMPOS SOLO BUENOS
						if(count($campos)==120 && $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120" )
						{
							$eapb_para_carpeta=trim($campos[119])."_RB";
							$ruta_carpeta_eapb=$ruta_temporales.$eapb_para_carpeta."/";
							$ruta_archivo_en_carpeta_eapb=$ruta_carpeta_eapb."EAPB_".$eapb_para_carpeta.".txt";
							if(!file_exists($ruta_carpeta_eapb))
						    {
							    mkdir($ruta_carpeta_eapb, 0777);

							    if(in_array($ruta_archivo_en_carpeta_eapb, $this->global_array_rutas_agrupados_ips_para_cada_eapb) )
							    {
							    	//si ya existe en el array ya debio haber creado la carpeta para este, y creado el archivo
							    }
							    else
							    {
							    	//crea el archivo para la epab actual
							    	$array_linea1proc_pre_eapb=explode("|", $linea_1_procesada);
							    	if(isset($array_linea1proc_pre_eapb[1])==true)
							    	{
							    		$array_linea1proc_pre_eapb[1]=str_replace(array("_RB","_RM"), "", $eapb_para_carpeta);
							    	}
							    	$linea_1_con_la_eapb_actual= implode("|", $array_linea1proc_pre_eapb);
							    	$archivo_actual_eapb = fopen($ruta_archivo_en_carpeta_eapb, "w") or die("fallo la creacion del archivo modo:w  para la eapb actual ");
								    fwrite($archivo_actual_eapb, $linea_1_con_la_eapb_actual." ");		    
								    fclose($archivo_actual_eapb);

								    $consecutivo_actual_eapb=1;

							    	

							    	//tiene que poner aca la primera linea que encontro
							    	$ruta_actual_eapb=$ruta_archivo_en_carpeta_eapb;
							    	$archivo_actual_eapb = fopen($ruta_actual_eapb, "a") or die("fallo la creacion del archivo modo:a para la eapb actual primer registro encontrado");
								    //crea cadena con el registro actual
								    $registro_actual="";
								    $ccamp=0;
								    while($ccamp<count($campos) && $ccamp<119)
								    {
									if($registro_actual!=""){$registro_actual.="|";}
									if($ccamp==1)
									{
									    $registro_actual.=$consecutivo_actual_eapb;
									}
									else
									{
									    $registro_actual.=$this->alphanumericAndSpace3($campos[$ccamp]);
									}
									$ccamp++;
								    }
								    //fin crea cadena con el registro actual
								    fwrite($archivo_actual_eapb,  "\n".$registro_actual);
								    fclose($archivo_actual_eapb);
								    //FIN tiene que poner aca la primera linea que encontro

								    $this->global_array_rutas_agrupados_ips_para_cada_eapb[]=$ruta_archivo_en_carpeta_eapb;
							    	$this->global_array_consecutivo_agrupados_ips_para_cada_eapb[]=$consecutivo_actual_eapb+1;
								    $this->global_array_eapb_para_carpetas[]=$eapb_para_carpeta;


								}//fin else
						    }//fin if
						    else
						    {
						    	$llave_eapb=array_search($ruta_archivo_en_carpeta_eapb, $this->global_array_rutas_agrupados_ips_para_cada_eapb);

						    	$ruta_actual_eapb=$this->global_array_rutas_agrupados_ips_para_cada_eapb[$llave_eapb];
						    	$consecutivo_actual_eapb=$this->global_array_consecutivo_agrupados_ips_para_cada_eapb[$llave_eapb];

						    	$archivo_actual_eapb = fopen($ruta_actual_eapb, "a") or die("fallo la creacion del archivo modo:a para la eapb actual desde el 2do registro encontrado en adelante ");
							    //crea cadena con el registro actual
							    $registro_actual="";
							    $ccamp=0;
							    while($ccamp<count($campos) && $ccamp<119)
							    {
								if($registro_actual!=""){$registro_actual.="|";}
								if($ccamp==1)
								{
								    $registro_actual.=$consecutivo_actual_eapb;
								}
								else
								{
								    $registro_actual.=$this->alphanumericAndSpace3($campos[$ccamp]);
								}
								$ccamp++;
							    }
							    //fin crea cadena con el registro actual
							    fwrite($archivo_actual_eapb,  "\n".$registro_actual);
							    fclose($archivo_actual_eapb);

						    	$this->global_array_consecutivo_agrupados_ips_para_cada_eapb[$llave_eapb]++;
						    }//fin else
						}//fin if
						//FIN FILTRADOS DEL AGRUPADO IPS 120 CAMPOS SOLO BUENOS

					}//fin if 120 campos si esta bueno el registro
					else
					{
						//FILTRADOS DEL AGRUPADO IPS 120 CAMPOS SOLO MALOS
						if(count($campos)==120 && $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120" )
						{
							$eapb_para_carpeta=trim($campos[119])."_RM";
							$ruta_carpeta_eapb=$ruta_temporales.$eapb_para_carpeta."/";
							$ruta_archivo_en_carpeta_eapb=$ruta_carpeta_eapb."EAPB_".$eapb_para_carpeta.".txt";
							if(!file_exists($ruta_carpeta_eapb))
						    {
							    mkdir($ruta_carpeta_eapb, 0777);

							    if(in_array($ruta_archivo_en_carpeta_eapb, $this->global_array_rutas_agrupados_ips_para_cada_eapb) )
							    {
							    	//si ya existe en el array ya debio haber creado la carpeta para este, y creado el archivo
							    }
							    else
							    {
							    	//crea el archivo para la epab actual
							    	$array_linea1proc_pre_eapb=explode("|", $linea_1_procesada);
							    	if(isset($array_linea1proc_pre_eapb[1])==true)
							    	{
							    		$array_linea1proc_pre_eapb[1]=str_replace(array("_RB","_RM"), "", $eapb_para_carpeta);
							    	}
							    	$linea_1_con_la_eapb_actual= implode("|", $array_linea1proc_pre_eapb);
							    	$archivo_actual_eapb = fopen($ruta_archivo_en_carpeta_eapb, "w") or die("fallo la creacion del archivo modo:w  para la eapb actual ");
								    fwrite($archivo_actual_eapb, $linea_1_con_la_eapb_actual." ");		    
								    fclose($archivo_actual_eapb);

								    $consecutivo_actual_eapb=1;

							    	

							    	//tiene que poner aca la primera linea que encontro
							    	$ruta_actual_eapb=$ruta_archivo_en_carpeta_eapb;
							    	$archivo_actual_eapb = fopen($ruta_actual_eapb, "a") or die("fallo la creacion del archivo modo:a para la eapb actual primer registro encontrado");
								    //crea cadena con el registro actual
								    $registro_actual="";
								    $ccamp=0;
								    while($ccamp<count($campos) && $ccamp<119)
								    {
									if($registro_actual!=""){$registro_actual.="|";}
									if($ccamp==1)
									{
									    $registro_actual.=$consecutivo_actual_eapb;
									}
									else
									{
									    $registro_actual.=$this->alphanumericAndSpace3($campos[$ccamp]);
									}
									$ccamp++;
								    }
								    //fin crea cadena con el registro actual
								    fwrite($archivo_actual_eapb,  "\n".$registro_actual);
								    fclose($archivo_actual_eapb);
								    //FIN tiene que poner aca la primera linea que encontro

								    $this->global_array_rutas_agrupados_ips_para_cada_eapb[]=$ruta_archivo_en_carpeta_eapb;
							    	$this->global_array_consecutivo_agrupados_ips_para_cada_eapb[]=$consecutivo_actual_eapb+1;
								    $this->global_array_eapb_para_carpetas[]=$eapb_para_carpeta;


								}//fin else
						    }//fin if
						    else
						    {
						    	$llave_eapb=array_search($ruta_archivo_en_carpeta_eapb, $this->global_array_rutas_agrupados_ips_para_cada_eapb);

						    	$ruta_actual_eapb=$this->global_array_rutas_agrupados_ips_para_cada_eapb[$llave_eapb];
						    	$consecutivo_actual_eapb=$this->global_array_consecutivo_agrupados_ips_para_cada_eapb[$llave_eapb];

						    	$archivo_actual_eapb = fopen($ruta_actual_eapb, "a") or die("fallo la creacion del archivo modo:a para la eapb actual desde el 2do registro encontrado en adelante ");
							    //crea cadena con el registro actual
							    $registro_actual="";
							    $ccamp=0;
							    while($ccamp<count($campos) && $ccamp<119)
							    {
								if($registro_actual!=""){$registro_actual.="|";}
								if($ccamp==1)
								{
								    $registro_actual.=$consecutivo_actual_eapb;
								}
								else
								{
								    $registro_actual.=$this->alphanumericAndSpace3($campos[$ccamp]);
								}
								$ccamp++;
							    }
							    //fin crea cadena con el registro actual
							    fwrite($archivo_actual_eapb,  "\n".$registro_actual);
							    fclose($archivo_actual_eapb);

						    	$this->global_array_consecutivo_agrupados_ips_para_cada_eapb[$llave_eapb]++;
						    }//fin else
						}//fin if
						//FIN FILTRADOS DEL AGRUPADO IPS 120 CAMPOS SOLO MALOS

					}//fin else hubo inconsistencia
					//fin parte adicional para buenos y malos de cada filtrado

			    }//fin if cantidad de campos es igual a 119 (NOTA funciona para los tres tipos ips, filtrado territorial, agrupado eapb)
			    //FIN ESCRITURA ARCHIVOS DE REGISTROS BUENOS Y MALOS

			    //INCONSISTENCIA SOLO MALOS
			    if($errores_campos_linea!="" && $campos_linea_validos==false)
			    {
			    	//echo "Error, registros malos al archivo $ruta_inconsistencias_archivo_registros_malos<br>";
				    
				    $array_errores_linea=explode("|", $errores_campos_linea);
				    //se abre con modo a para que adicione
				    $log_errores_archivo_registros_malos = fopen($ruta_inconsistencias_archivo_registros_malos, "a") or die("fallo la creacion del archivo modo:a log_incons_MALOS");
				    
				    $cont_error=0;
				    foreach ($array_errores_linea as $error) 
				    {
						$columnas_inconsistencias_malos=array();
						$columnas_inconsistencias_malos=explode(",",$error);
						//echo "count(columnas_inconsistencias_malos): ".count($columnas_inconsistencias_malos)."<br>";
						if(count($columnas_inconsistencias_malos)>=10)//tiene que contar mayor o igual a diez debido a que se han agregado mas columnas
						{
						    $linea_inconsistencia="";
						    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
						    $columnas_inconsistencias_malos[0]=$consecutivo_incons_archivo_excluido_registros_malos;
						    $columnas_inconsistencias_malos[8]=($consecutivo_archivo_excluido_registros_malos-1);
						    $cerrmalos=0;
						    while($cerrmalos<count($columnas_inconsistencias_malos))
						    {
								if($linea_inconsistencia!=""){$linea_inconsistencia.=",";}
								$linea_inconsistencia.=$columnas_inconsistencias_malos[$cerrmalos];
								$cerrmalos++;
						    }
						    //echo "Inconsistencia: $linea_inconsistencia<br>";
						    //PARTE REESCRIBE PARA CSV SIMPLE DE ABRIR
						    $array_detalle_inconsistencia_malos_solos=explode(",", utf8_decode($linea_inconsistencia) );
						    $error_procesado_csv_excel="";
						    $error_procesado_csv_excel.="=\"".implode("\";=\"", $array_detalle_inconsistencia_malos_solos)."\"";
						    //FIN PARTE REESCRIBE PARA CSV SIMPLE DE ABRIR
						    fwrite($log_errores_archivo_registros_malos, trim($error_procesado_csv_excel). "\n");
						    $consecutivo_incons_archivo_excluido_registros_malos++;
						}//fin if
				    }//fin foreach
				    
				    //CIERRA EL ARCHIVO
				    fclose($log_errores_archivo_registros_malos);
			    }//fin if
			    //FIN INCONSISTENCIAS SOLO MALOS
			    
			    
			    
			    if(count($campos)==119 
			    	&& ($this->tipo_entidad_que_efectua_el_cargue=="individual_ips" 
			    	 || $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips")
       			)
			    {
				    if($campos_linea_validos==true)
				    {
					//INICIA QUERY INSERT gios_datos_validados_exito_r4505
					$sql_insertar_en_validos_r4505="";				    
					$sql_insertar_en_validos_r4505.="insert into gios_datos_validados_exito_r4505";
					$sql_insertar_en_validos_r4505.="(";
					$sql_insertar_en_validos_r4505.="cod_prestador_servicios_salud,";
					$sql_insertar_en_validos_r4505.="codigo_eapb,";
					$sql_insertar_en_validos_r4505.="numero_de_identificacion_de_la_entidad_reportadora,";
					$sql_insertar_en_validos_r4505.="numero_de_secuencia,";
					$sql_insertar_en_validos_r4505.="numero_fila,";
					$sql_insertar_en_validos_r4505.="fecha_de_corte,";
					$sql_insertar_en_validos_r4505.="tipo_de_identificacion_entidad_reportadora,";
					$sql_insertar_en_validos_r4505.="tipo_de_regimen_de_la_informacion_reportada,";
					$sql_insertar_en_validos_r4505.="consecutivo_de_archivo,";
					$sql_insertar_en_validos_r4505.="fecha_validacion,";
					$sql_insertar_en_validos_r4505.="nombre_archivo,";
					$ccamp=0;
					while($ccamp<119)
					{
						
						$sql_insertar_en_validos_r4505.="campo".$ccamp.",";
						
						$ccamp++;
					}
					$sql_insertar_en_validos_r4505.="estado_registro";
					$sql_insertar_en_validos_r4505.=")";
					$sql_insertar_en_validos_r4505.="values";
					$sql_insertar_en_validos_r4505.="(";
					$sql_insertar_en_validos_r4505.="'".$this->cod_registro_especial_pss."',";
					$sql_insertar_en_validos_r4505.="'".$this->cod_eapb_global."',";
					$sql_insertar_en_validos_r4505.="'".$this->nit_prestador."',";
					$sql_insertar_en_validos_r4505.="'".$secuencia_dependiendo_existencia."',";
					$sql_insertar_en_validos_r4505.="'".$numLinea."',";
					$sql_insertar_en_validos_r4505.="'".$this->old_fecha_de_corte_periodo_mensual."',";
					$sql_insertar_en_validos_r4505.="'".$this->tipo_de_identificacion_entidad_reportadora."',";
					$sql_insertar_en_validos_r4505.="'".$this->tipo_de_regimen_de_la_informacion_reportada."',";
					$sql_insertar_en_validos_r4505.="'".$this->consecutivo_de_archivo."',";
					$sql_insertar_en_validos_r4505.="'".$fecha_actual."',";
					$sql_insertar_en_validos_r4505.="'".$nombre_archivo4505."',";
					$ccamp=0;
					while($ccamp<count($campos))
					{
						$sql_insertar_en_validos_r4505.="'".$this->alphanumericAndSpace3($campos[$ccamp])."',";												
						$ccamp++;
					}
					$sql_insertar_en_validos_r4505.="'1'";
					$sql_insertar_en_validos_r4505.=");";
					$error_bd_seq="";
					$bandera = $obj->insertar_no_warning_get_error_no_crea_cierra($sql_insertar_en_validos_r4505, $error_bd_seq);
					if($error_bd_seq!="")
					{
					    $error_de_base_de_datos.=" ERROR CARGANDO VALIDACIONES EXITOSAS: ".$error_bd_seq."<br>";
					}
					 //FIN QUERY INSERT gios_datos_validados_exito_r4505
				    }//fin if si es linea valida
				    
				    //INICIA QUERY INSERT gios_datos_rechazados_r4505
				    $sql_insertar_en_rechazados_r4505="";				    
				    $sql_insertar_en_rechazados_r4505.="insert into gios_datos_rechazados_r4505";
				    $sql_insertar_en_rechazados_r4505.="(";
				    $sql_insertar_en_rechazados_r4505.="cod_prestador_servicios_salud,";
				    $sql_insertar_en_rechazados_r4505.="codigo_eapb,";
				    $sql_insertar_en_rechazados_r4505.="numero_de_identificacion_de_la_entidad_reportadora,";
				    $sql_insertar_en_rechazados_r4505.="numero_de_secuencia,";
				    $sql_insertar_en_rechazados_r4505.="numero_fila,";
				    $sql_insertar_en_rechazados_r4505.="fecha_de_corte,";
				    $sql_insertar_en_rechazados_r4505.="tipo_de_identificacion_entidad_reportadora,";
				    $sql_insertar_en_rechazados_r4505.="tipo_de_regimen_de_la_informacion_reportada,";
				    $sql_insertar_en_rechazados_r4505.="consecutivo_de_archivo,";
				    $sql_insertar_en_rechazados_r4505.="fecha_validacion,";
				    $sql_insertar_en_rechazados_r4505.="nombre_archivo,";
				    $ccamp=0;
				    while($ccamp<119)
				    {
					    $sql_insertar_en_rechazados_r4505.="campo".$ccamp.",";
					    
					    $ccamp++;
				    }
				    $sql_insertar_en_rechazados_r4505.="estado_registro";
				    $sql_insertar_en_rechazados_r4505.=")";
				    $sql_insertar_en_rechazados_r4505.="values";
				    $sql_insertar_en_rechazados_r4505.="(";
				    $sql_insertar_en_rechazados_r4505.="'".$this->cod_registro_especial_pss."',";
				    $sql_insertar_en_rechazados_r4505.="'".$this->cod_eapb_global."',";
				    $sql_insertar_en_rechazados_r4505.="'".$this->nit_prestador."',";
				    $sql_insertar_en_rechazados_r4505.="'".$secuencia_dependiendo_existencia."',";
				    $sql_insertar_en_rechazados_r4505.="'".$numLinea."',";
				    $sql_insertar_en_rechazados_r4505.="'".$this->old_fecha_de_corte_periodo_mensual."',";
				    $sql_insertar_en_rechazados_r4505.="'".$this->tipo_de_identificacion_entidad_reportadora."',";
				    $sql_insertar_en_rechazados_r4505.="'".$this->tipo_de_regimen_de_la_informacion_reportada."',";
				    $sql_insertar_en_rechazados_r4505.="'".$this->consecutivo_de_archivo."',";
				    $sql_insertar_en_rechazados_r4505.="'".$fecha_actual."',";
				    $sql_insertar_en_rechazados_r4505.="'".$nombre_archivo4505."',";
				    $ccamp=0;
				    while($ccamp<count($campos))
				    {
					    $sql_insertar_en_rechazados_r4505.="'".$this->alphanumericAndSpace3($campos[$ccamp])."',";
					    
					    $ccamp++;
				    }
				    if($campos_linea_validos==true)
				    {
					$sql_insertar_en_rechazados_r4505.="'1'";
				    }
				    else
				    {
					$sql_insertar_en_rechazados_r4505.="'2'";
				    }
				    $sql_insertar_en_rechazados_r4505.=");";
				    $error_bd_seq="";
				    $bandera = $obj->insertar_no_warning_get_error_no_crea_cierra($sql_insertar_en_rechazados_r4505, $error_bd_seq);
				    if($error_bd_seq!="")
				    {
					$error_de_base_de_datos.=" ERROR CARGANDO VALIDACIONES RECHAZADAS: ".$error_bd_seq."<br>";
				    }
				    //FIN QUERY INSERT gios_datos_rechazados_r4505
				    
			    }//fin if numero campos es 119
			    
			    if(count($campos)==120 && $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120" )
			    {
				    if($campos_linea_validos==true)
				    {
					//INICIA QUERY INSERT gios_datos_validados_exito_r4505
					$sql_insertar_en_validos_r4505="";				    
					$sql_insertar_en_validos_r4505.="insert into gios_datos_validados_exito_r4505";
					$sql_insertar_en_validos_r4505.="(";
					$sql_insertar_en_validos_r4505.="cod_prestador_servicios_salud,";
					$sql_insertar_en_validos_r4505.="codigo_eapb,";
					$sql_insertar_en_validos_r4505.="numero_de_identificacion_de_la_entidad_reportadora,";
					$sql_insertar_en_validos_r4505.="numero_de_secuencia,";
					$sql_insertar_en_validos_r4505.="numero_fila,";
					$sql_insertar_en_validos_r4505.="fecha_de_corte,";
					$sql_insertar_en_validos_r4505.="tipo_de_identificacion_entidad_reportadora,";
					$sql_insertar_en_validos_r4505.="tipo_de_regimen_de_la_informacion_reportada,";
					$sql_insertar_en_validos_r4505.="consecutivo_de_archivo,";
					$sql_insertar_en_validos_r4505.="fecha_validacion,";
					$sql_insertar_en_validos_r4505.="nombre_archivo,";
					$ccamp=0;
					while($ccamp<119)
					{
						
						$sql_insertar_en_validos_r4505.="campo".$ccamp.",";
						
						$ccamp++;
					}
					$sql_insertar_en_validos_r4505.="campo_extra_120_eapb_regis,";
					$sql_insertar_en_validos_r4505.="estado_registro";
					$sql_insertar_en_validos_r4505.=")";
					$sql_insertar_en_validos_r4505.="values";
					$sql_insertar_en_validos_r4505.="(";
					$sql_insertar_en_validos_r4505.="'".$this->cod_registro_especial_pss."',";
					$sql_insertar_en_validos_r4505.="'".$this->cod_eapb_global."',";
					$sql_insertar_en_validos_r4505.="'".$this->nit_prestador."',";
					$sql_insertar_en_validos_r4505.="'".$secuencia_dependiendo_existencia."',";
					$sql_insertar_en_validos_r4505.="'".$numLinea."',";
					$sql_insertar_en_validos_r4505.="'".$this->old_fecha_de_corte_periodo_mensual."',";
					$sql_insertar_en_validos_r4505.="'".$this->tipo_de_identificacion_entidad_reportadora."',";
					$sql_insertar_en_validos_r4505.="'".$this->tipo_de_regimen_de_la_informacion_reportada."',";
					$sql_insertar_en_validos_r4505.="'".$this->consecutivo_de_archivo."',";
					$sql_insertar_en_validos_r4505.="'".$fecha_actual."',";
					$sql_insertar_en_validos_r4505.="'".$nombre_archivo4505."',";
					$ccamp=0;
					while($ccamp<count($campos))
					{
						$sql_insertar_en_validos_r4505.="'".$this->alphanumericAndSpace3($campos[$ccamp])."',";												
						$ccamp++;
					}
					$sql_insertar_en_validos_r4505.="'1'";
					$sql_insertar_en_validos_r4505.=");";
					$error_bd_seq="";
					$bandera = $obj->insertar_no_warning_get_error_no_crea_cierra($sql_insertar_en_validos_r4505, $error_bd_seq);
					if($error_bd_seq!="")
					{
					    $error_de_base_de_datos.=" ERROR CARGANDO VALIDACIONES EXITOSAS: ".$error_bd_seq."<br>";
					}
					 //FIN QUERY INSERT gios_datos_validados_exito_r4505
				    }//fin if si es linea valida
				    
				    //INICIA QUERY INSERT gios_datos_rechazados_r4505
				    $sql_insertar_en_rechazados_r4505="";				    
				    $sql_insertar_en_rechazados_r4505.="insert into gios_datos_rechazados_r4505";
				    $sql_insertar_en_rechazados_r4505.="(";
				    $sql_insertar_en_rechazados_r4505.="cod_prestador_servicios_salud,";
				    $sql_insertar_en_rechazados_r4505.="codigo_eapb,";
				    $sql_insertar_en_rechazados_r4505.="numero_de_identificacion_de_la_entidad_reportadora,";
				    $sql_insertar_en_rechazados_r4505.="numero_de_secuencia,";
				    $sql_insertar_en_rechazados_r4505.="numero_fila,";
				    $sql_insertar_en_rechazados_r4505.="fecha_de_corte,";
				    $sql_insertar_en_rechazados_r4505.="tipo_de_identificacion_entidad_reportadora,";
				    $sql_insertar_en_rechazados_r4505.="tipo_de_regimen_de_la_informacion_reportada,";
				    $sql_insertar_en_rechazados_r4505.="consecutivo_de_archivo,";
				    $sql_insertar_en_rechazados_r4505.="fecha_validacion,";
				    $sql_insertar_en_rechazados_r4505.="nombre_archivo,";
				    $ccamp=0;
				    while($ccamp<119)
				    {
					    $sql_insertar_en_rechazados_r4505.="campo".$ccamp.",";
					    
					    $ccamp++;
				    }
				    $sql_insertar_en_rechazados_r4505.="campo_extra_120_eapb_regis,";
				    $sql_insertar_en_rechazados_r4505.="estado_registro";
				    $sql_insertar_en_rechazados_r4505.=")";
				    $sql_insertar_en_rechazados_r4505.="values";
				    $sql_insertar_en_rechazados_r4505.="(";
				    $sql_insertar_en_rechazados_r4505.="'".$this->cod_registro_especial_pss."',";
				    $sql_insertar_en_rechazados_r4505.="'".$this->cod_eapb_global."',";
				    $sql_insertar_en_rechazados_r4505.="'".$this->nit_prestador."',";
				    $sql_insertar_en_rechazados_r4505.="'".$secuencia_dependiendo_existencia."',";
				    $sql_insertar_en_rechazados_r4505.="'".$numLinea."',";
				    $sql_insertar_en_rechazados_r4505.="'".$this->old_fecha_de_corte_periodo_mensual."',";
				    $sql_insertar_en_rechazados_r4505.="'".$this->tipo_de_identificacion_entidad_reportadora."',";
				    $sql_insertar_en_rechazados_r4505.="'".$this->tipo_de_regimen_de_la_informacion_reportada."',";
				    $sql_insertar_en_rechazados_r4505.="'".$this->consecutivo_de_archivo."',";
				    $sql_insertar_en_rechazados_r4505.="'".$fecha_actual."',";
				    $sql_insertar_en_rechazados_r4505.="'".$nombre_archivo4505."',";
				    $ccamp=0;
				    while($ccamp<count($campos))
				    {
					    $sql_insertar_en_rechazados_r4505.="'".$this->alphanumericAndSpace3($campos[$ccamp])."',";
					    
					    $ccamp++;
				    }
				    if($campos_linea_validos==true)
				    {
					$sql_insertar_en_rechazados_r4505.="'1'";
				    }
				    else
				    {
					$sql_insertar_en_rechazados_r4505.="'2'";
				    }
				    $sql_insertar_en_rechazados_r4505.=");";
				    $error_bd_seq="";
				    $bandera = $obj->insertar_no_warning_get_error_no_crea_cierra($sql_insertar_en_rechazados_r4505, $error_bd_seq);
				    if($error_bd_seq!="")
				    {
					$error_de_base_de_datos.=" ERROR CARGANDO VALIDACIONES RECHAZADAS: ".$error_bd_seq."<br>";
				    }
				    //FIN QUERY INSERT gios_datos_rechazados_r4505
				    
			    }//fin if numero campos es 120 por campo extra
			    
			    if(count($campos)==119 && $this->tipo_entidad_que_efectua_el_cargue=="ent_territoriales" && $bool_se_tiene_en_cuenta)
			    {
				if($campos_linea_validos==true)
				{
				    $sql_insertar_en_validos_r4505="";
				    //INICIA QUERY INSERT gios_datos_validados_exito_r4505
				    $sql_insertar_en_validos_r4505.="insert into gioss_archivo_4505_exitoso_para_eapb ";
				    $sql_insertar_en_validos_r4505.="(";
				    $sql_insertar_en_validos_r4505.="codigo_eapb,";
				    $sql_insertar_en_validos_r4505.="numero_de_identificacion_de_la_entidad_reportadora,";
				    $sql_insertar_en_validos_r4505.="numero_de_secuencia,";
				    $sql_insertar_en_validos_r4505.="numero_fila,";
				    $sql_insertar_en_validos_r4505.="fecha_de_corte,";
				    $sql_insertar_en_validos_r4505.="tipo_de_identificacion_entidad_reportadora,";
				    $sql_insertar_en_validos_r4505.="tipo_de_regimen_de_la_informacion_reportada,";
				    $sql_insertar_en_validos_r4505.="consecutivo_de_archivo,";
				    $sql_insertar_en_validos_r4505.="fecha_validacion,";
				    $sql_insertar_en_validos_r4505.="nombre_archivo,";
				    $ccamp=0;
				    while($ccamp<119)
				    {
					    
					    $sql_insertar_en_validos_r4505.="campo".$ccamp.",";
					    
					    $ccamp++;
				    }
				    $sql_insertar_en_validos_r4505.="estado_registro,";
				    $sql_insertar_en_validos_r4505.="codigo_departamento,";
				    $sql_insertar_en_validos_r4505.="codigo_municipio";
				    $sql_insertar_en_validos_r4505.=")";
				    $sql_insertar_en_validos_r4505.="values";
				    $sql_insertar_en_validos_r4505.="(";
				    //aqui el codigo prestador es el codigo de la eapb
				    $sql_insertar_en_validos_r4505.="'".$this->cod_registro_especial_pss."',";
				    $sql_insertar_en_validos_r4505.="'".$this->nit_prestador."',";
				    $sql_insertar_en_validos_r4505.="'".$secuencia_dependiendo_existencia."',";
				    $sql_insertar_en_validos_r4505.="'".$contador_filtrados."',";
				    $sql_insertar_en_validos_r4505.="'".$this->old_fecha_de_corte_periodo_mensual."',";
				    $sql_insertar_en_validos_r4505.="'".$this->tipo_de_identificacion_entidad_reportadora."',";
				    $sql_insertar_en_validos_r4505.="'".$this->tipo_de_regimen_de_la_informacion_reportada."',";
				    $sql_insertar_en_validos_r4505.="'".$this->consecutivo_de_archivo."',";
				    $sql_insertar_en_validos_r4505.="'".$fecha_actual."',";
				    $sql_insertar_en_validos_r4505.="'".$nombre_archivo4505."',";
				    $ccamp=0;
				    while($ccamp<count($campos))
				    {
					    $sql_insertar_en_validos_r4505.="'".$this->alphanumericAndSpace3($campos[$ccamp])."',";												
					    $ccamp++;
				    }
				    $sql_insertar_en_validos_r4505.="'1',";
				    $sql_insertar_en_validos_r4505.="'".$this->cod_dpto_filtro."',";
				    $sql_insertar_en_validos_r4505.="'".$this->cod_mpio_filtro."'";
				    $sql_insertar_en_validos_r4505.=");";
				    //FIN QUERY INSERT gios_datos_validados_exito_r4505
			    
				    $error_bd_seq="";
				    $bandera = $obj->insertar_no_warning_get_error_no_crea_cierra($sql_insertar_en_validos_r4505, $error_bd_seq);
				    if($error_bd_seq!="")
				    {
					$error_de_base_de_datos.=" ERROR CARGANDO VALIDACIONES EXITOSAS: ".$error_bd_seq."<br>";
				    }
				}//fin if si el registro fue validado con exito
				    
				//INICIA QUERY INSERT gios_datos_rechazados_r4505
				$sql_insertar_en_rechazados_r4505="";				
				$sql_insertar_en_rechazados_r4505.="insert into gioss_archivo_4505_rechazado_para_eapb ";
				$sql_insertar_en_rechazados_r4505.="(";
				$sql_insertar_en_rechazados_r4505.="codigo_eapb,";
				$sql_insertar_en_rechazados_r4505.="numero_de_identificacion_de_la_entidad_reportadora,";
				$sql_insertar_en_rechazados_r4505.="numero_de_secuencia,";
				$sql_insertar_en_rechazados_r4505.="numero_fila,";
				$sql_insertar_en_rechazados_r4505.="fecha_de_corte,";
				$sql_insertar_en_rechazados_r4505.="tipo_de_identificacion_entidad_reportadora,";
				$sql_insertar_en_rechazados_r4505.="tipo_de_regimen_de_la_informacion_reportada,";
				$sql_insertar_en_rechazados_r4505.="consecutivo_de_archivo,";
				$sql_insertar_en_rechazados_r4505.="fecha_validacion,";
				$sql_insertar_en_rechazados_r4505.="nombre_archivo,";
				$ccamp=0;
				while($ccamp<119)
				{
					$sql_insertar_en_rechazados_r4505.="campo".$ccamp.",";
					
					$ccamp++;
				}
				$sql_insertar_en_rechazados_r4505.="estado_registro,";
				$sql_insertar_en_rechazados_r4505.="codigo_departamento,";
				$sql_insertar_en_rechazados_r4505.="codigo_municipio";
				$sql_insertar_en_rechazados_r4505.=")";
				$sql_insertar_en_rechazados_r4505.="values";
				$sql_insertar_en_rechazados_r4505.="(";
				//aqui el codigo prestador es el codigo de la eapb
				$sql_insertar_en_rechazados_r4505.="'".$this->cod_registro_especial_pss."',";
				$sql_insertar_en_rechazados_r4505.="'".$this->nit_prestador."',";
				$sql_insertar_en_rechazados_r4505.="'".$secuencia_dependiendo_existencia."',";
				$sql_insertar_en_rechazados_r4505.="'".$contador_filtrados."',";
				$sql_insertar_en_rechazados_r4505.="'".$this->old_fecha_de_corte_periodo_mensual."',";
				$sql_insertar_en_rechazados_r4505.="'".$this->tipo_de_identificacion_entidad_reportadora."',";
				$sql_insertar_en_rechazados_r4505.="'".$this->tipo_de_regimen_de_la_informacion_reportada."',";
				$sql_insertar_en_rechazados_r4505.="'".$this->consecutivo_de_archivo."',";
				$sql_insertar_en_rechazados_r4505.="'".$fecha_actual."',";
				$sql_insertar_en_rechazados_r4505.="'".$nombre_archivo4505."',";
				$ccamp=0;
				while($ccamp<count($campos))
				{
					$sql_insertar_en_rechazados_r4505.="'".$this->alphanumericAndSpace3($campos[$ccamp])."',";					    
					$ccamp++;
				}
				if($campos_linea_validos==true)
				{
				    $sql_insertar_en_rechazados_r4505.="'1',";
				}
				else
				{
				    $sql_insertar_en_rechazados_r4505.="'2',";
				}
				$sql_insertar_en_rechazados_r4505.="'".$this->cod_dpto_filtro."',";
				$sql_insertar_en_rechazados_r4505.="'".$this->cod_mpio_filtro."'";
				$sql_insertar_en_rechazados_r4505.=");";				
				$error_bd_seq="";
				$bandera = $obj->insertar_no_warning_get_error_no_crea_cierra($sql_insertar_en_rechazados_r4505, $error_bd_seq);
				if($error_bd_seq!="")
				{
				    $error_de_base_de_datos.=" ERROR CARGANDO VALIDACIONES RECHAZADAS: ".$error_bd_seq."<br>";
				}
				//FIN QUERY INSERT gios_datos_rechazados_r4505
			    
				    
			    }//fin if numero campos es 119
			    
			    if(count($campos)==119 && $this->tipo_entidad_que_efectua_el_cargue=="agrupado_eapb")
			    {
				    if($campos_linea_validos==true && $hubo_errores_en_los_campos_del_archivo==false )
				    {
					//INICIA QUERY INSERT gios_datos_validados_exito_r4505
					$sql_insertar_en_validos_r4505="";				    
					$sql_insertar_en_validos_r4505.="insert into gios_datos_validados_exito_r4505";
					$sql_insertar_en_validos_r4505.="(";
					$sql_insertar_en_validos_r4505.="cod_prestador_servicios_salud,";
					$sql_insertar_en_validos_r4505.="codigo_eapb,";
					$sql_insertar_en_validos_r4505.="numero_de_identificacion_de_la_entidad_reportadora,";
					$sql_insertar_en_validos_r4505.="numero_de_secuencia,";
					$sql_insertar_en_validos_r4505.="numero_fila,";
					$sql_insertar_en_validos_r4505.="fecha_de_corte,";
					$sql_insertar_en_validos_r4505.="tipo_de_identificacion_entidad_reportadora,";
					$sql_insertar_en_validos_r4505.="tipo_de_regimen_de_la_informacion_reportada,";
					$sql_insertar_en_validos_r4505.="consecutivo_de_archivo,";
					$sql_insertar_en_validos_r4505.="fecha_validacion,";
					$sql_insertar_en_validos_r4505.="nombre_archivo,";
					$ccamp=0;
					while($ccamp<119)
					{
						
						$sql_insertar_en_validos_r4505.="campo".$ccamp.",";
						
						$ccamp++;
					}
					$sql_insertar_en_validos_r4505.="estado_registro";
					$sql_insertar_en_validos_r4505.=")";
					$sql_insertar_en_validos_r4505.="values";
					$sql_insertar_en_validos_r4505.="(";
					$sql_insertar_en_validos_r4505.="'AGRUP_EAPB',";
					$sql_insertar_en_validos_r4505.="'".$this->cod_eapb_global."',";
					$sql_insertar_en_validos_r4505.="'00',";
					$sql_insertar_en_validos_r4505.="'".$secuencia_dependiendo_existencia."',";
					$sql_insertar_en_validos_r4505.="'".$numLinea."',";
					$sql_insertar_en_validos_r4505.="'".$this->old_fecha_de_corte_periodo_mensual."',";
					$sql_insertar_en_validos_r4505.="'".$this->tipo_de_identificacion_entidad_reportadora."',";
					$sql_insertar_en_validos_r4505.="'".$this->tipo_de_regimen_de_la_informacion_reportada."',";
					$sql_insertar_en_validos_r4505.="'".$this->consecutivo_de_archivo."',";
					$sql_insertar_en_validos_r4505.="'".$fecha_actual."',";
					$sql_insertar_en_validos_r4505.="'".$nombre_archivo4505."',";
					$ccamp=0;
					while($ccamp<count($campos))
					{
						$sql_insertar_en_validos_r4505.="'".$this->alphanumericAndSpace3($campos[$ccamp])."',";												
						$ccamp++;
					}
					$sql_insertar_en_validos_r4505.="'1'";
					$sql_insertar_en_validos_r4505.=");";
					$error_bd_seq="";
					$bandera = $obj->insertar_no_warning_get_error_no_crea_cierra($sql_insertar_en_validos_r4505, $error_bd_seq);
					if($error_bd_seq!="")
					{
					    $error_de_base_de_datos.=" ERROR CARGANDO VALIDACIONES EXITOSAS: ".$error_bd_seq."<br>";
					}
					 //FIN QUERY INSERT gios_datos_validados_exito_r4505
				    }//fin if si es linea valida
				    
				    //INICIA QUERY INSERT gios_datos_rechazados_r4505
				    $sql_insertar_en_rechazados_r4505="";				    
				    $sql_insertar_en_rechazados_r4505.="insert into gios_datos_rechazados_r4505";
				    $sql_insertar_en_rechazados_r4505.="(";
				    $sql_insertar_en_rechazados_r4505.="cod_prestador_servicios_salud,";
				    $sql_insertar_en_rechazados_r4505.="codigo_eapb,";
				    $sql_insertar_en_rechazados_r4505.="numero_de_identificacion_de_la_entidad_reportadora,";
				    $sql_insertar_en_rechazados_r4505.="numero_de_secuencia,";
				    $sql_insertar_en_rechazados_r4505.="numero_fila,";
				    $sql_insertar_en_rechazados_r4505.="fecha_de_corte,";
				    $sql_insertar_en_rechazados_r4505.="tipo_de_identificacion_entidad_reportadora,";
				    $sql_insertar_en_rechazados_r4505.="tipo_de_regimen_de_la_informacion_reportada,";
				    $sql_insertar_en_rechazados_r4505.="consecutivo_de_archivo,";
				    $sql_insertar_en_rechazados_r4505.="fecha_validacion,";
				    $sql_insertar_en_rechazados_r4505.="nombre_archivo,";
				    $ccamp=0;
				    while($ccamp<119)
				    {
					    $sql_insertar_en_rechazados_r4505.="campo".$ccamp.",";
					    
					    $ccamp++;
				    }
				    $sql_insertar_en_rechazados_r4505.="estado_registro";
				    $sql_insertar_en_rechazados_r4505.=")";
				    $sql_insertar_en_rechazados_r4505.="values";
				    $sql_insertar_en_rechazados_r4505.="(";
				    $sql_insertar_en_rechazados_r4505.="'AGRUP_EAPB',";
				    $sql_insertar_en_rechazados_r4505.="'".$this->cod_eapb_global."',";
				    $sql_insertar_en_rechazados_r4505.="'00',";
				    $sql_insertar_en_rechazados_r4505.="'".$secuencia_dependiendo_existencia."',";
				    $sql_insertar_en_rechazados_r4505.="'".$numLinea."',";
				    $sql_insertar_en_rechazados_r4505.="'".$this->old_fecha_de_corte_periodo_mensual."',";
				    $sql_insertar_en_rechazados_r4505.="'".$this->tipo_de_identificacion_entidad_reportadora."',";
				    $sql_insertar_en_rechazados_r4505.="'".$this->tipo_de_regimen_de_la_informacion_reportada."',";
				    $sql_insertar_en_rechazados_r4505.="'".$this->consecutivo_de_archivo."',";
				    $sql_insertar_en_rechazados_r4505.="'".$fecha_actual."',";
				    $sql_insertar_en_rechazados_r4505.="'".$nombre_archivo4505."',";
				    $ccamp=0;
				    while($ccamp<count($campos))
				    {
					    $sql_insertar_en_rechazados_r4505.="'".$this->alphanumericAndSpace3($campos[$ccamp])."',";
					    
					    $ccamp++;
				    }
				    if($campos_linea_validos==true)
				    {
					$sql_insertar_en_rechazados_r4505.="'1'";
				    }
				    else
				    {
					$sql_insertar_en_rechazados_r4505.="'2'";
				    }
				    $sql_insertar_en_rechazados_r4505.=");";
				    $error_bd_seq="";
				    $bandera = $obj->insertar_no_warning_get_error_no_crea_cierra($sql_insertar_en_rechazados_r4505, $error_bd_seq);
				    if($error_bd_seq!="")
				    {
					$error_de_base_de_datos.=" ERROR CARGANDO VALIDACIONES RECHAZADAS: ".$error_bd_seq."<br>";
				    }
				    //FIN QUERY INSERT gios_datos_rechazados_r4505
				    
			    }//fin if numero campos es 119

			    //PARTE CARGA EN TABLA INDEXADORA IPS POR ARCHIVO
			    if(count($campos)>=119)
			    {
				    try
				    {
				    	$codigo_prestador_para_insercion="";
				    	$codigo_eapb_para_insercion="";

				    	if($this->tipo_entidad_que_efectua_el_cargue=="agrupado_eapb")
				    	{
				    		$codigo_prestador_para_insercion="AGRUP_EAPB";
				    		$codigo_eapb_para_insercion=$this->cod_eapb_global;
				    	}//fin if
				    	if($this->tipo_entidad_que_efectua_el_cargue=="ent_territoriales")
				    	{
				    		$codigo_prestador_para_insercion="AGRUP_ENT_TERR";
				    		$codigo_eapb_para_insercion=$this->cod_registro_especial_pss;
				    	}//fin if
				    	if($this->tipo_entidad_que_efectua_el_cargue=="individual_ips" 
				    		|| $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips"
				    		|| $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120"
				    		)
				    	{
				    		$codigo_prestador_para_insercion=$this->cod_registro_especial_pss;
				    		$codigo_eapb_para_insercion=$this->cod_eapb_global;
				    	}//fin if

				    	

				    	$prestador_archivo=$this->alphanumericAndSpace3($campos[2]);
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

				    	$sql_query_nit_prestador="SELECT num_tipo_identificacion, cod_municipio, cod_depto, nom_entidad_prestadora FROM gios_prestador_servicios_salud WHERE (cod_registro_especial_pss='$prestador_archivo' OR cod_registro_especial_pss='$codigo_habilitacion_con_ceros_consulta' ); ";
						$resultado_query_nit_prestador=$obj->consultar2_no_crea_cierra($sql_query_nit_prestador);
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

						
						$fecha_de_corte_temp_analisis=$this->fecha_de_corte_periodo;
						if($fecha_de_corte_temp_analisis!=$this->old_fecha_de_corte_periodo_mensual)
						{
						    $fecha_de_corte_temp_analisis=$this->old_fecha_de_corte_periodo_mensual;
						}//fin if

						//se necesita hacer el select de todos modos
						$cantidad_lineas_en_archivo_para_prestador=0;
						$cantidad_lineas_correctas_en_archivo_para_prestador=0;

						$cantidad_inconsistencias_para_ips=0;
						$cantidad_inconsistencias_para_ips=intval($cantidad_errores_por_linea);

						$sql_select_datos_prestador_en_reporte="";
						$sql_select_datos_prestador_en_reporte.="SELECT cantidad_lineas_en_archivo, cantidad_lineas_correctas_en_archivo, cantidad_inconsistencias_para_ips
						FROM gioss_indexador_para_reporte_ips 
						WHERE 
						entidad_reportante ='".$codigo_prestador_para_insercion."'
						AND
						entidad_a_reportar ='".$codigo_eapb_para_insercion."'
						AND
						fecha_inicio_periodo ='".$this->fecha_inicial_para_analisis."'
						AND
						fecha_de_corte ='".$fecha_de_corte_temp_analisis."'
						AND
						fecha_y_hora_validacion ='".$fecha_actual." ".$tiempo_actual."'
						AND 
						nombre_archivo ='".$nombre_archivo4505."'
						AND
						numero_de_secuencia ='".$secuencia_dependiendo_existencia."'
						AND
						prestador_en_archivo ='".$prestador_archivo."'
						";
						$resultado_query_prestador_en_reporte=$obj->consultar2_no_crea_cierra($sql_select_datos_prestador_en_reporte);
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
						if($campos_linea_validos==true)
						{
							$cantidad_lineas_correctas_en_archivo_para_prestador++;
						}//fin if


						//UPSERT

						$filas_afectadas=0;

						$sql_update_en_reporte_ips="";
						$sql_update_en_reporte_ips.="UPDATE gioss_indexador_para_reporte_ips SET ";
						$sql_update_en_reporte_ips.=" cantidad_lineas_en_archivo='$cantidad_lineas_en_archivo_para_prestador' , ";
						$sql_update_en_reporte_ips.=" cantidad_lineas_correctas_en_archivo='$cantidad_lineas_correctas_en_archivo_para_prestador',  ";						
						$sql_update_en_reporte_ips.=" cantidad_inconsistencias_para_ips='$cantidad_inconsistencias_para_ips'  ";
						$sql_update_en_reporte_ips.="
							WHERE 
							entidad_reportante ='".$codigo_prestador_para_insercion."'
							AND
							entidad_a_reportar ='".$codigo_eapb_para_insercion."'
							AND
							fecha_inicio_periodo ='".$this->fecha_inicial_para_analisis."'
							AND
							fecha_de_corte ='".$fecha_de_corte_temp_analisis."'
							AND
							fecha_y_hora_validacion ='".$fecha_actual." ".$tiempo_actual."'
							AND 
							nombre_archivo ='".$nombre_archivo4505."'
							AND
							numero_de_secuencia ='".$secuencia_dependiendo_existencia."'
							AND
							prestador_en_archivo ='".$prestador_archivo."'
						";
						$error_bd_seq="";
						$bandera = $obj->insertar_no_warning_get_error_no_crea_cierra($sql_update_en_reporte_ips, $error_bd_seq);
						if($error_bd_seq!="")
						{
						    $error_de_base_de_datos.=" ERROR AL ACTUALIZAR PARA REPORTE PRESTADOR: ".$error_bd_seq."<br>";
						    echo $error_de_base_de_datos;
						}//fin if


						$filas_afectadas=intval($obj->get_filas_afectadas_update() );
						if($filas_afectadas==0)
						{
					    	$sql_insertar_en_reporte_ips="";				    
							$sql_insertar_en_reporte_ips.="insert into gioss_indexador_para_reporte_ips";
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
							$sql_insertar_en_reporte_ips.="'".$this->fecha_inicial_para_analisis."',";					
							$sql_insertar_en_reporte_ips.="'".$fecha_de_corte_temp_analisis."',";					
							$sql_insertar_en_reporte_ips.="'".$fecha_actual." ".$tiempo_actual."',";							
							$sql_insertar_en_reporte_ips.="'".$nombre_archivo4505."',";							
							$sql_insertar_en_reporte_ips.="'".$secuencia_dependiendo_existencia."',";
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
							$bandera = $obj->insertar_no_warning_get_error_no_crea_cierra($sql_insertar_en_reporte_ips, $error_bd_seq);
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

			    //PARTE INSERT EN TABLA PARA ANALISIS RESULTADOS SOLO SI EL REGISTRO DE DICHO ARCHIVO SUS CAMPOS SON CALIFICADOS COMO BUENOS
			    if($campos_linea_validos==true && count($campos)>=119 )//se coloca mayor o igual paraque acepte si la linea tiene 119 campos o mas si hay campos extras (aka agrupado ips, aunque en prepagado no se usa)
			    {
			    	try
			    	{
				    	$codigo_prestador_para_insercion="";
				    	$codigo_eapb_para_insercion="";

					$fecha_de_corte_temp_analisis=$this->fecha_de_corte_periodo;
					if($fecha_de_corte_temp_analisis!=$this->old_fecha_de_corte_periodo_mensual)
					{
					    $fecha_de_corte_temp_analisis=$this->old_fecha_de_corte_periodo_mensual;
					}//fin if

				    	if($this->tipo_entidad_que_efectua_el_cargue=="agrupado_eapb")
				    	{
				    		$codigo_prestador_para_insercion="AGRUP_EAPB";
				    		$codigo_eapb_para_insercion=$this->cod_eapb_global;
				    	}//fin if
				    	if($this->tipo_entidad_que_efectua_el_cargue=="ent_territoriales")
				    	{
				    		$codigo_prestador_para_insercion="AGRUP_ENT_TERR";
				    		$codigo_eapb_para_insercion=$this->cod_registro_especial_pss;
				    	}//fin if
				    	if($this->tipo_entidad_que_efectua_el_cargue=="individual_ips" 
				    		|| $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips"
				    		|| $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120"
				    		)
				    	{
				    		$codigo_prestador_para_insercion=$this->cod_registro_especial_pss;
				    		$codigo_eapb_para_insercion=$this->cod_eapb_global;
				    	}//fin if

				    	//tabla indice insert
				    	if($this->se_creo_tabla_indice==false)
				    	{
					    //echo $this->fecha_inicial_para_analisis." ".$fecha_de_corte_temp_analisis."<br>";
				    		$insercion_tabla_indice_exitosa=true;

				    		$sql_insertar_en_tabla_indice_analisis_coherencia="";				    
							$sql_insertar_en_tabla_indice_analisis_coherencia.="insert into gioss_indice_archivo_para_analisis_4505";
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
							$sql_insertar_en_tabla_indice_analisis_coherencia.="'".$codigo_prestador_para_insercion."',";
							$sql_insertar_en_tabla_indice_analisis_coherencia.="'".$codigo_eapb_para_insercion."',";
							$sql_insertar_en_tabla_indice_analisis_coherencia.="'".$this->fecha_inicial_para_analisis."',";					
							$sql_insertar_en_tabla_indice_analisis_coherencia.="'".$fecha_de_corte_temp_analisis."',";					
							$sql_insertar_en_tabla_indice_analisis_coherencia.="'".$fecha_actual." ".$tiempo_actual."',";							
							$sql_insertar_en_tabla_indice_analisis_coherencia.="'".$nombre_archivo4505."',";							
							$sql_insertar_en_tabla_indice_analisis_coherencia.="'".$secuencia_dependiendo_existencia."'";								
							$sql_insertar_en_tabla_indice_analisis_coherencia.=");";
							$error_bd_seq="";
							$bandera = $obj->insertar_no_warning_get_error_no_crea_cierra($sql_insertar_en_tabla_indice_analisis_coherencia, $error_bd_seq);
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
				    			$this->se_creo_tabla_indice=true;
				    		}
				    	}//fin if

				    	if($this->se_creo_tabla_indice==true)
				    	{
				    		//PREPARA EDADES Y CAMPOS ADICIONALES

				    		//CALCULO EDAD
							$fecha_nacimiento= explode("-",$campos[9]);
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
							    $fecha_corte_format=new DateTime($this->fecha_de_corte_periodo);
							
							    $interval = date_diff($fecha_nacimiento_format,$fecha_corte_format);
							    $edad_dias =(float)($interval->days);
							    
							    //$edad= (float)($interval->days / 365);		    
							    //$edad_meses = (float)($interval->days / 30.4368499);
							    //$edad_meses_2 = (float)($interval->format('%m')+ 12 * $interval->format('%y'));
							    
							    $array_fecha_nacimiento=explode("-",$string_fecha_nacimiento);
							    $array_fecha_corte=explode("-",$this->fecha_de_corte_periodo);
							    $array_edad=$this->edad_years_months_days($array_fecha_nacimiento[2]."-".$array_fecha_nacimiento[1]."-".$array_fecha_nacimiento[0],$array_fecha_corte[2]."-".$array_fecha_corte[1]."-".$array_fecha_corte[0]);
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
							$sql_insertar_en_tabla_analisis_coherencia.="insert into gioss_archivo_para_analisis_4505";
							$sql_insertar_en_tabla_analisis_coherencia.="(";
							$sql_insertar_en_tabla_analisis_coherencia.="cod_prestador_servicios_salud,";
							$sql_insertar_en_tabla_analisis_coherencia.="codigo_eapb,";
							$sql_insertar_en_tabla_analisis_coherencia.="fecha_inicio_periodo,";					
							$sql_insertar_en_tabla_analisis_coherencia.="fecha_de_corte,";					
							$sql_insertar_en_tabla_analisis_coherencia.="fecha_y_hora_validacion,";					
							$sql_insertar_en_tabla_analisis_coherencia.="nombre_archivo,";
							$ccamp=0;
							while($ccamp<119)
							{
								
								$sql_insertar_en_tabla_analisis_coherencia.="campo".$ccamp.",";
								
								$ccamp++;
							}
							if($bool_fecha_nacimiento_valida==true)
							{
								$sql_insertar_en_tabla_analisis_coherencia.="edad_years,";
								$sql_insertar_en_tabla_analisis_coherencia.="edad_meses,";
								$sql_insertar_en_tabla_analisis_coherencia.="edad_dias,";

								$sql_insertar_en_tabla_analisis_coherencia.="grupo_edad_quinquenal,";								
								$sql_insertar_en_tabla_analisis_coherencia.="grupo_etareo,";
							}//fin if fecha nacimeinto valida
							if($this->regional_global!="")
							{
								$sql_insertar_en_tabla_analisis_coherencia.="regional,";
							}//fin if hay regional
							$sql_insertar_en_tabla_analisis_coherencia.="numero_fila, ";							
							$sql_insertar_en_tabla_analisis_coherencia.="numero_de_secuencia";
							$sql_insertar_en_tabla_analisis_coherencia.=")";
							$sql_insertar_en_tabla_analisis_coherencia.="values";
							$sql_insertar_en_tabla_analisis_coherencia.="(";
							$sql_insertar_en_tabla_analisis_coherencia.="'".$codigo_prestador_para_insercion."',";
							$sql_insertar_en_tabla_analisis_coherencia.="'".$codigo_eapb_para_insercion."',";
							$sql_insertar_en_tabla_analisis_coherencia.="'".$this->fecha_inicial_para_analisis."',";					
							$sql_insertar_en_tabla_analisis_coherencia.="'".$fecha_de_corte_temp_analisis."',";					
							$sql_insertar_en_tabla_analisis_coherencia.="'".$fecha_actual." ".$tiempo_actual."',";							
							$sql_insertar_en_tabla_analisis_coherencia.="'".$nombre_archivo4505."',";
							$ccamp=0;
							while($ccamp<119)
							{
								$sql_insertar_en_tabla_analisis_coherencia.="'".$this->alphanumericAndSpace3($campos[$ccamp])."',";												
								$ccamp++;
							}
							if($bool_fecha_nacimiento_valida==true)
							{
								$sql_insertar_en_tabla_analisis_coherencia.="'".$edad."', ";
								$sql_insertar_en_tabla_analisis_coherencia.="'".$edad_meses."', ";
								$sql_insertar_en_tabla_analisis_coherencia.="'".$edad_dias."', ";

								$sql_insertar_en_tabla_analisis_coherencia.="'".$edad_quinquenio."', ";
								$sql_insertar_en_tabla_analisis_coherencia.="'".$edad_etarea."', ";
							}//fin if fecha nacimeinto valida
							if($this->regional_global!="")
							{
								$sql_insertar_en_tabla_analisis_coherencia.="'".$this->regional_global."', ";
							}//fin if hay regional			
							$sql_insertar_en_tabla_analisis_coherencia.="'".$numLinea."', ";											
							$sql_insertar_en_tabla_analisis_coherencia.="'".$secuencia_dependiendo_existencia."'";	
							$sql_insertar_en_tabla_analisis_coherencia.=");";
							$error_bd_seq="";
							$bandera = $obj->insertar_no_warning_get_error_no_crea_cierra($sql_insertar_en_tabla_analisis_coherencia, $error_bd_seq);
							if($error_bd_seq!="")
							{
							    $error_de_base_de_datos.=" ERROR CARGANDO ARCHIVO PARA ANALISIS: ".$error_bd_seq."<br>";
							    echo $sql_insertar_en_tabla_analisis_coherencia."<br>";
							    echo $error_de_base_de_datos;
							}//fin if
						}//fin if se inserto el indice
					 	//FIN QUERY INSERT gioss_archivo_para_analisis_4505
				    
					}//fin try
					catch(Exception $exc_analisis_insert)
					{
						//no hay nada por ahora si ocurre una excepcion
					}//fin catch
			    }//fin if
			    //PARTE INSERT EN TABLA PARA ANALISIS RESULTADOS SOLO SI EL REGISTRO DE DICHO ARCHIVO SUS CAMPOS SON CALIFICADOS COMO BUENOS
			    
			    
			    if($this->tipo_entidad_que_efectua_el_cargue=="individual_ips"
			    	|| $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips"
		       		|| $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120"
		    	)
			    {
				if($campos_linea_validos==true)
				{
					$contador_registros_buenos++;
					
				}//fin if los campos fueron validados con exito
				else
				{
					$hubo_errores_en_los_campos_del_archivo=true;
					$contador_registros_malos++;
				}
			    }//fin else if 
			    else if($this->tipo_entidad_que_efectua_el_cargue=="ent_territoriales" && $bool_se_tiene_en_cuenta)
			    {
				if($campos_linea_validos==true)
				{
					$contador_registros_buenos++;
					
				}//fin if los campos fueron validados con exito
				else
				{
					$hubo_errores_en_los_campos_del_archivo=true;
					$contador_registros_malos++;
				}
			    }//fin else if 
			    else if($this->tipo_entidad_que_efectua_el_cargue=="agrupado_eapb")
			    {
				if($campos_linea_validos==true)
				{
					$contador_registros_buenos++;
					
				}//fin if los campos fueron validados con exito
				else
				{
					$hubo_errores_en_los_campos_del_archivo=true;
					$contador_registros_malos++;
				}
			    }//fin else if 
			    
			    //ACTUALIZA ESTADO EJECUCION
			    
			    $muestra_mensaje_nuevo=false;
			    $porcentaje=intval((($numLinea+1)*100)/$lineas_del_archivo);
			    if($porcentaje!=$cont_porcentaje || ($porcentaje==0 && ($numLinea+1)==1) || $porcentaje==100)
			    {
			     $cont_porcentaje=$porcentaje;
			     $muestra_mensaje_nuevo=true;
			    }//fin if

			    $tiempo_temp_porcentaje_actual="";
			    if($muestra_mensaje_nuevo==true)
			    {
			    	$tiempo_temp_porcentaje_actual = "".date('H:i:s');
			    	//echo $tiempo_temp_porcentaje_actual."<br>";
			    }//fin if tiempo_temp_porcentaje_actual
			    
			    
			    //MENSAJES HTML
			    if(
			    	($this->tipo_entidad_que_efectua_el_cargue=="individual_ips" 
			    		|| $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips"
		       		|| $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120" )
		       		 && $muestra_mensaje_nuevo
		       	)
			    {
				if($fue_cerrada_la_gui==false)
				{
					    echo "<script>document.getElementById('estado_validacion').innerHTML=\"<p id='parrafo_estado' align='left'>La linea ".($numLinea+1)." validada, de ".$lineas_del_archivo." lineas.<br> Se han encontrado  $contador_registros_buenos registros buenos y $contador_registros_malos registros malos.<br> $porcentaje % <br>$tiempo_temp_porcentaje_actual</p>\";</script>";				
				    ob_flush();
				    flush();
				}
			    }
			    else if($this->tipo_entidad_que_efectua_el_cargue=="ent_territoriales" && $bool_se_tiene_en_cuenta && $muestra_mensaje_nuevo)
			    {
				if($fue_cerrada_la_gui==false)
				{
					    echo "<script>document.getElementById('estado_validacion').innerHTML=\"<p id='parrafo_estado' align='left'>Se han filtrado y validado ".($contador_filtrados+1).", de ".$lineas_del_archivo." lineas originales.<br> Se han encontrado  $contador_registros_buenos registros buenos y $contador_registros_malos registros malos. <br> $porcentaje % <br>$tiempo_temp_porcentaje_actual</p>\";</script>";				
				    ob_flush();
				    flush();
				}
			    }
			    else if($this->tipo_entidad_que_efectua_el_cargue=="agrupado_eapb" && $muestra_mensaje_nuevo)
			    {
				if($fue_cerrada_la_gui==false)
				{
					    echo "<script>document.getElementById('estado_validacion').innerHTML=\"<p id='parrafo_estado' align='left'>La linea ".($numLinea+1)." validada, de ".$lineas_del_archivo." lineas.<br> Se han encontrado  $contador_registros_buenos registros buenos y $contador_registros_malos registros malos. <br> $porcentaje % <br>$tiempo_temp_porcentaje_actual</p>\";</script>";				
				    ob_flush();
				    flush();
				}
			    }//fin else
			    
			    if($fue_cerrada_la_gui==false && $muestra_mensaje_nuevo)
			    {
				echo "<script>document.title='V $porcentaje % PyP $nombre_archivo4505.';</script>";				
				ob_flush();
				flush();
			    }
			    //FIN MENSAJES HTML
			    
			    //MENSAJES BD
			    $mensaje_contador_errores="";
			    if($muestra_mensaje_nuevo==true)
			    {
			    if($this->tipo_entidad_que_efectua_el_cargue=="individual_ips"
			    	|| $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips"
		       		|| $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120"
			    	)
			    {
				$mensaje_contador_errores.="La linea ".($numLinea+1)." validada, de ".$lineas_del_archivo." lineas.<br>";
					$mensaje_contador_errores.=" Se han encontrado  $contador_registros_buenos registros buenos y $contador_registros_malos registros malos.<br> $porcentaje % <br>$tiempo_temp_porcentaje_actual";
			    }
			    else if($this->tipo_entidad_que_efectua_el_cargue=="ent_territoriales" && $bool_se_tiene_en_cuenta)
			    {
				$mensaje_contador_errores.="Se han filtrado y validado ".($contador_filtrados+1).", de ".$lineas_del_archivo." lineas originales.<br>";
					$mensaje_contador_errores.=" Se han encontrado  $contador_registros_buenos registros buenos y $contador_registros_malos registros malos.<br> $porcentaje % <br>$tiempo_temp_porcentaje_actual";
			    }
			    if($this->tipo_entidad_que_efectua_el_cargue=="agrupado_eapb")
			    {
				$mensaje_contador_errores.="La linea ".($numLinea+1)." validada, de ".$lineas_del_archivo." lineas.<br>";
					$mensaje_contador_errores.=" Se han encontrado  $contador_registros_buenos registros buenos y $contador_registros_malos registros malos.<br> $porcentaje % <br>$tiempo_temp_porcentaje_actual";
			    }
				}//fin if muestra mensaje nuevo
			    
			    
			    
			    
			    if(($this->tipo_entidad_que_efectua_el_cargue=="individual_ips"
			       || $this->tipo_entidad_que_efectua_el_cargue=="ent_territoriales"
			       || $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips"
		       		|| $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120"
			       )
			       && $muestra_mensaje_nuevo
			       )
			    {
				$query_update_esta_siendo_procesado="";
				$query_update_esta_siendo_procesado.=" UPDATE gioss_4505_esta_validando_actualmente ";
				$query_update_esta_siendo_procesado.=" SET mensaje_estado_registros='".$mensaje_contador_errores."' ";
				$query_update_esta_siendo_procesado.=" WHERE fecha_remision='".$this->cadena_fecha_corte."' ";
				$query_update_esta_siendo_procesado.=" AND codigo_entidad_reportadora='".$this->cod_registro_especial_pss."' ";
				$query_update_esta_siendo_procesado.=" AND nombre_archivo='".$nombre_archivo4505."'  ";
				$query_update_esta_siendo_procesado.=" AND nick_usuario='".$this->nick_user."'  ";
				$query_update_esta_siendo_procesado.=" AND fecha_validacion='".$fecha_actual."'  ";
				$query_update_esta_siendo_procesado.=" AND hora_validacion='".$tiempo_actual."'  ";
				$query_update_esta_siendo_procesado.=" ; ";
				$error_bd="";
				$bool_hubo_error_query=$obj->insertar_no_warning_get_error_no_crea_cierra($query_update_esta_siendo_procesado, $error_bd);
				if($error_bd!="")
				{
					if($fue_cerrada_la_gui==false)
					{
						echo "<script>alert('error al actualizar el estado actual de validacion en tiempo real  4505 ');</script>";
					}
				}
			    }//fin if
			    else if($this->tipo_entidad_que_efectua_el_cargue=="agrupado_eapb"
				    && $muestra_mensaje_nuevo
				    )
			    {
				$query_update_esta_siendo_procesado="";
				$query_update_esta_siendo_procesado.=" UPDATE gioss_4505_esta_validando_actualmente ";
				$query_update_esta_siendo_procesado.=" SET mensaje_estado_registros='".$mensaje_contador_errores."' ";
				$query_update_esta_siendo_procesado.=" WHERE fecha_remision='".$this->cadena_fecha_corte."' ";
				$query_update_esta_siendo_procesado.=" AND codigo_entidad_reportadora='".$this->cod_eapb_global."' ";
				$query_update_esta_siendo_procesado.=" AND nombre_archivo='".$nombre_archivo4505."'  ";
				$query_update_esta_siendo_procesado.=" AND nick_usuario='".$this->nick_user."'  ";
				$query_update_esta_siendo_procesado.=" AND fecha_validacion='".$fecha_actual."'  ";
				$query_update_esta_siendo_procesado.=" AND hora_validacion='".$tiempo_actual."'  ";
				$query_update_esta_siendo_procesado.=" ; ";
				$error_bd="";
				$bool_hubo_error_query=$obj->insertar_no_warning_get_error_no_crea_cierra($query_update_esta_siendo_procesado, $error_bd);
				if($error_bd!="")
				{
					if($fue_cerrada_la_gui==false)
					{
						echo "<script>alert('error al actualizar el estado actual de validacion en tiempo real  4505 ');</script>";
					}
				}
			    }//fin if
			    //FIN MENSAJES BD
			    
			    //FIN ACTUALIZA ESTADO EJECUCION
			    
			    $numLinea++;
		    }//fin while funcion end of file
		    fclose($archivo);
		    //FIN PARTE VALIDA ARCHIVO Y ESCRIBE INCONSISTENCIAS
		    
		    //REESCRIBE PRIMERA LINEA PARA ESCRIBIR EL NUMERO DE REGISTROS DEL ARCHIVO EXCLUIDO CON REGISTROS BUENOS
		    $consecutivo_archivo_excluido_registros_buenos=$consecutivo_archivo_excluido_registros_buenos-1;//se le resta 1 posicion, para que tenga como valor el ultimo consecutivo escrito
		    $archivo_excluido_registros_buenos=fopen($ruta_archivo_registros_buenos, "c") or die("fallo la creacion del archivo modo:c buenos");
		    //parte primera linea
		    $consecutivo_anterior=intval($array_linea_procesada[4]);
		    $numero_caracteres_consecutivo_anterior=strlen("".$consecutivo_anterior);
		    $numero_caracteres_consecutivo_nuevo=strlen("".$consecutivo_archivo_excluido_registros_buenos);
		    //echo "<script>alert('$numero_caracteres_consecutivo_anterior $consecutivo_anterior $numero_caracteres_consecutivo_nuevo ".($this->consecutivo_fixer-1)."');</script>";
		    if($numero_caracteres_consecutivo_anterior==$numero_caracteres_consecutivo_nuevo)
		    {
			fwrite($archivo_excluido_registros_buenos, $array_linea_procesada[0]."|".$array_linea_procesada[1]."|".$array_linea_procesada[2]."|".$array_linea_procesada[3]."|".$consecutivo_archivo_excluido_registros_buenos." ");
		    }
		    else if($numero_caracteres_consecutivo_anterior<$numero_caracteres_consecutivo_nuevo)
		    {
			fwrite($archivo_excluido_registros_buenos, $array_linea_procesada[0]."|".$array_linea_procesada[1]."|".$array_linea_procesada[2]."|".$array_linea_procesada[3]."|".$consecutivo_archivo_excluido_registros_buenos);
		    }
		    else if($numero_caracteres_consecutivo_anterior>$numero_caracteres_consecutivo_nuevo)
		    {
				$numero_espacios_adicionar=0;
				$numero_espacios_adicionar=$numero_caracteres_consecutivo_anterior-$numero_caracteres_consecutivo_nuevo;
				$cont_espacios_add=0;
				$string_espacios_add="";
				while($cont_espacios_add<$numero_espacios_adicionar)
				{
				    $string_espacios_add.=" ";
				    $cont_espacios_add++;
				}
				$string_espacios_add.=" ";
				fwrite($archivo_excluido_registros_buenos, $array_linea_procesada[0]."|".$array_linea_procesada[1]."|".$array_linea_procesada[2]."|".$array_linea_procesada[3]."|".$consecutivo_archivo_excluido_registros_buenos.$string_espacios_add);
		    }
		    //fin part primear linea
		    fclose($archivo_excluido_registros_buenos);
		    //REESCRIBE PRIMERA LINEA PARA ESCRIBIR EL NUMERO DE REGISTROS DEL ARCHIVO EXCLUIDO CON REGISTROS BUENOS
		    
		    //REESCRIBE PRIMERA LINEA PARA ESCRIBIR EL NUMERO DE REGISTROS DEL ARCHIVO EXCLUIDO CON REGISTROS MALOS
		    $consecutivo_archivo_excluido_registros_malos=$consecutivo_archivo_excluido_registros_malos-1;//se le resta 1 posicion, para que tenga como valor el ultimo consecutivo escrito
		    $archivo_excluido_registros_malos=fopen($ruta_archivo_registros_malos, "c") or die("fallo la creacion del archivo modo:c malos");
		    //parte primera linea
		    $consecutivo_anterior=intval($array_linea_procesada[4]);
		    $numero_caracteres_consecutivo_anterior=strlen("".$consecutivo_anterior);
		    $numero_caracteres_consecutivo_nuevo=strlen("".$consecutivo_archivo_excluido_registros_malos);
		    //echo "<script>alert('$numero_caracteres_consecutivo_anterior $consecutivo_anterior $numero_caracteres_consecutivo_nuevo ".($this->consecutivo_fixer-1)."');</script>";
		    if($numero_caracteres_consecutivo_anterior==$numero_caracteres_consecutivo_nuevo)
		    {
			fwrite($archivo_excluido_registros_malos, $array_linea_procesada[0]."|".$array_linea_procesada[1]."|".$array_linea_procesada[2]."|".$array_linea_procesada[3]."|".$consecutivo_archivo_excluido_registros_malos." ");
		    }
		    else if($numero_caracteres_consecutivo_anterior<$numero_caracteres_consecutivo_nuevo)
		    {
			fwrite($archivo_excluido_registros_malos, $array_linea_procesada[0]."|".$array_linea_procesada[1]."|".$array_linea_procesada[2]."|".$array_linea_procesada[3]."|".$consecutivo_archivo_excluido_registros_malos);
		    }
		    else if($numero_caracteres_consecutivo_anterior>$numero_caracteres_consecutivo_nuevo)
		    {
				$numero_espacios_adicionar=0;
				$numero_espacios_adicionar=$numero_caracteres_consecutivo_anterior-$numero_caracteres_consecutivo_nuevo;
				$cont_espacios_add=0;
				$string_espacios_add="";
				while($cont_espacios_add<$numero_espacios_adicionar)
				{
				    $string_espacios_add.=" ";
				    $cont_espacios_add++;
				}
				$string_espacios_add.=" ";
				fwrite($archivo_excluido_registros_malos, $array_linea_procesada[0]."|".$array_linea_procesada[1]."|".$array_linea_procesada[2]."|".$array_linea_procesada[3]."|".$consecutivo_archivo_excluido_registros_malos.$string_espacios_add);
		    }
		    //fin part primear linea
		    fclose($archivo_excluido_registros_malos);
		    //REESCRIBE PRIMERA LINEA PARA ESCRIBIR EL NUMERO DE REGISTROS DEL ARCHIVO EXCLUIDO CON REGISTROS MALOS

		    $this->reescribe_primera_linea_function($this->global_consecutivo_afiliados_no_registrados,
		    	$this->global_ruta_afiliados_no_registrados,
		    	$array_linea_procesada);
		    $this->reescribe_primera_linea_function($this->global_consecutivo_solo_registros_con_sexo_fecha_nacimiento_corregidos,
		    	$this->global_ruta_registros_con_afiliados_modificados,
		    	$array_linea_procesada);
		    $this->reescribe_primera_linea_function($this->global_consecutivo_solo_registros_con_sexo_dif_corregidos,
		    	$this->global_ruta_registros_con_afiliados_modificados_sexo_diferentes,
		    	$array_linea_procesada);
		    $this->reescribe_primera_linea_function($this->global_consecutivo_solo_registros_con_fecha_nacimiento_dif_corregidos,
		    	$this->global_ruta_registros_con_afiliados_modificados_fecha_nacimiento_diferentes,
		    	$array_linea_procesada);
		    $this->reescribe_primera_linea_function($this->global_consecutivo_nuevo_original_con_sexo_fecha_nacimiento_corregidos,
		    	$this->global_ruta_archivo_plano_nuevo_original_con_sexo_fecha_nacimiento_corregidos,
		    	$array_linea_procesada);
		    $this->reescribe_primera_linea_function($this->global_consecutivo_solo_registros_solo_afiliados_con_campos_adicionales,
		    	$this->global_ruta_registros_con_afiliados_modificados_y_campos_estadisticas_adicionales,
		    	$array_linea_procesada);
		    $this->reescribe_primera_linea_function($this->global_consecutivo_estructura_mala,
		    	$this->global_ruta_estructura_mala,
		    	$array_linea_procesada);

		    if($this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120")
		    {
		    	foreach ($this->global_array_rutas_agrupados_ips_para_cada_eapb as $key_reapb2 => $ruta_actual_eapb_fe2) 
		    	{
		    		$consecutivo_actual_eapb=$this->global_array_consecutivo_agrupados_ips_para_cada_eapb[$key_reapb2];
		    		$eapb_para_carpeta=$this->global_array_eapb_para_carpetas[$key_reapb2];

		    		$array_linea1proc_pre_eapb=explode("|", $linea_1_procesada);
			    	if(isset($array_linea1proc_pre_eapb[1])==true)
			    	{
			    		$array_linea1proc_pre_eapb[1]=str_replace(array("_RB","_RM"), "", $eapb_para_carpeta);//se hace arriba ya que si no queda inconsistente la cantidad de caracteres para remplazar, igual se deja aca porque  ya no hay ningun _RM o RB
			    	}//fin if
			    	//echo "'".implode("|", $array_linea1proc_pre_eapb)."'<br>";

			    	$this->reescribe_primera_linea_function($consecutivo_actual_eapb,
			    	$ruta_actual_eapb_fe2,
			    	$array_linea1proc_pre_eapb);
		    	}//fin foreach

		    }//fin if
		    
		    $estado_validacion_4505=0;
		    
		    //SI HUBO ERRORES OBLIGATORIOS
		    if ($hubo_errores_en_los_campos_del_archivo==true) 
		    {
			    $estado_validacion_4505=2;
			    
			    $flag=1;
			    
			    if($this->tipo_entidad_que_efectua_el_cargue=="individual_ips"
			    	|| $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips"
		       		|| $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120"
			    	)
			    {
				//DELETE DATOS VALIDADOS SI HUBO INCONSISTENCIAS
				$sql_delete_validados_rollback="";
				$sql_delete_validados_rollback.=" DELETE FROM gios_datos_validados_exito_r4505 WHERE ";
				$sql_delete_validados_rollback.=" cod_prestador_servicios_salud='".$this->cod_registro_especial_pss."' AND ";
				$sql_delete_validados_rollback.=" codigo_eapb='".$this->cod_eapb_global."' AND ";
				$sql_delete_validados_rollback.=" fecha_de_corte = '".$this->old_fecha_de_corte_periodo_mensual."' AND ";
				$sql_delete_validados_rollback.=" nombre_archivo ='".$nombre_archivo4505."' ; ";
				$error_bd_seq="";
				$bool_hubo_error_query=$obj->insertar_no_warning_get_error_no_crea_cierra($sql_delete_validados_rollback, $error_bd_seq);
				if($error_bd_seq!="")
				{
				    $error_de_base_de_datos.=" ERROR BORRAR VALIDADOS SI HUBO ERROR AL VALIDAR: ".$error_bd_seq."<br>";
				}
				//FIN DELETE
			    }
			    else if($this->tipo_entidad_que_efectua_el_cargue=="ent_territoriales")
			    {
				//BORRA DATOS VALIDADOS SI HUBO INCONSISTENCIAS		
				$sql_delete_validados_rollback="";
				$sql_delete_validados_rollback.=" DELETE FROM gioss_archivo_4505_exitoso_para_eapb WHERE ";
				$sql_delete_validados_rollback.=" codigo_eapb='".$this->cod_registro_especial_pss."' AND ";
				$sql_delete_validados_rollback.=" fecha_de_corte = '".$this->old_fecha_de_corte_periodo_mensual."' AND ";
				$sql_delete_validados_rollback.=" nombre_archivo ='".$nombre_archivo4505."' AND  ";
				$sql_delete_validados_rollback.=" codigo_departamento ='".$this->cod_dpto_filtro."' AND  ";
				$sql_delete_validados_rollback.=" codigo_municipio ='".$this->cod_mpio_filtro."'   ";
				$sql_delete_validados_rollback.=" ;  ";
				$error_bd_seq="";
				$bool_hubo_error_query=$obj->insertar_no_warning_get_error_no_crea_cierra($sql_delete_validados_rollback, $error_bd_seq);		
				//FIN BORRA DATOS VALIDADOS SI HUBO INCONSISTENCIAS
			    }
			    if($this->tipo_entidad_que_efectua_el_cargue=="agrupado_eapb")
			    {
				//DELETE DATOS VALIDADOS SI HUBO INCONSISTENCIAS
				$sql_delete_validados_rollback="";
				$sql_delete_validados_rollback.=" DELETE FROM gios_datos_validados_exito_r4505 WHERE ";
				$sql_delete_validados_rollback.=" cod_prestador_servicios_salud='AGRUP_EAPB' AND ";
				$sql_delete_validados_rollback.=" codigo_eapb='".$this->cod_eapb_global."' AND ";
				$sql_delete_validados_rollback.=" fecha_de_corte = '".$this->old_fecha_de_corte_periodo_mensual."' AND ";
				$sql_delete_validados_rollback.=" nombre_archivo ='".$nombre_archivo4505."' ; ";
				$error_bd_seq="";
				$bool_hubo_error_query=$obj->insertar_no_warning_get_error_no_crea_cierra($sql_delete_validados_rollback, $error_bd_seq);
				if($error_bd_seq!="")
				{
				    $error_de_base_de_datos.=" ERROR BORRAR VALIDADOS SI HUBO ERROR AL VALIDAR: ".$error_bd_seq."<br>";
				}
				//FIN DELETE
			    }
		    }//fin if flag igual a 1 hace rollback en la base de datos
		    
		    //SI EL ARCHIVO FUE VALIDADO EXITOSAMENTE 
		    if ($hubo_errores_en_los_campos_del_archivo==false) 
		    {
			    $estado_validacion_4505=1;
			    
			    if($this->tipo_entidad_que_efectua_el_cargue=="individual_ips"
			    	|| $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips"
		       		|| $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120"
			    	)
			    {
				//DELETE DATOS RECHAZADOS DESPUES DE SUBIR A EXITOSOS Y DESPUES DEL RESUMEN EXITOSO
				$sql_delete_rechazados_bf="";
				$sql_delete_rechazados_bf.=" DELETE FROM gios_datos_rechazados_r4505 WHERE ";
				$sql_delete_rechazados_bf.=" cod_prestador_servicios_salud='".$this->cod_registro_especial_pss."' AND ";
				$sql_delete_rechazados_bf.=" codigo_eapb='".$this->cod_eapb_global."' AND ";
				$sql_delete_rechazados_bf.=" fecha_de_corte = '".$this->old_fecha_de_corte_periodo_mensual."' AND ";
				$sql_delete_rechazados_bf.=" nombre_archivo ='".$nombre_archivo4505."' ; ";
				$error_bd_seq="";
				$bool_hubo_error_query=$obj->insertar_no_warning_get_error_no_crea_cierra($sql_delete_rechazados_bf, $error_bd_seq);
				if($error_bd_seq!="")
				{
				    $error_de_base_de_datos.=" ERROR BORRAR RECHAZADOS DESPUES DE VALIDAR CON EXITO: ".$error_bd_seq."<br>";
				}
				//FIN DELETE RECHAZADOS DESPUES DE SUBIR A EXITOSOS Y DESPUES DEL RESUMEN EXITOSO
				
				
				//PARTE SUBIDA A  TABLA gioss_registros_cargados_exito_4505
				
				
				$query_nombre_prestador="";
				$query_nombre_prestador.="SELECT nombre_de_la_entidad FROM gioss_entidades_sector_salud WHERE codigo_entidad='".$this->cod_registro_especial_pss."' ; ";
				$resultado_query_nombre_prestador=$obj->consultar2_no_crea_cierra($query_nombre_prestador);
				
				$nombre_prestador="";
				if(count($resultado_query_nombre_prestador)>0)
				{
					$nombre_prestador=$resultado_query_nombre_prestador[0]["nombre_de_la_entidad"];
				}
				
				$query_nombre_eapb="";
				$query_nombre_eapb.="SELECT nombre_de_la_entidad FROM gioss_entidades_sector_salud WHERE codigo_entidad='".$this->cod_eapb_global."' ; ";
				$resultado_query_nombre_eapb=$obj->consultar2_no_crea_cierra($query_nombre_eapb);
				
				$nombre_eapb="";
				if(count($resultado_query_nombre_eapb)>0 )
				{
					$nombre_eapb=$resultado_query_nombre_eapb[0]["nombre_de_la_entidad"];
				}
				
				//if si hayo los nombres de las entidades inserta en la tabla exitosos
				if(count($resultado_query_nombre_eapb)>0 && count($resultado_query_nombre_prestador)>0)
				{
					$query_registrar_cargado_con_exito="";
					$query_registrar_cargado_con_exito.="INSERT INTO gioss_registros_cargados_exito_4505 ";
					$query_registrar_cargado_con_exito.="(";
					$query_registrar_cargado_con_exito.="codigo_habilitacion_reps,";
					$query_registrar_cargado_con_exito.="nombre_entidad_prestadora,";
					$query_registrar_cargado_con_exito.="codigo_eapb,";
					$query_registrar_cargado_con_exito.="nombre_eapb,";
					$query_registrar_cargado_con_exito.="numero_secuencia_validacion,";
					$query_registrar_cargado_con_exito.="nombre_archivo_4505,";
					$query_registrar_cargado_con_exito.="fecha_validacion,";
					$query_registrar_cargado_con_exito.="periodo_reportado,";
					$query_registrar_cargado_con_exito.="fecha_corte,";
					$query_registrar_cargado_con_exito.="numeros_registros_archivo_4505,";
					$query_registrar_cargado_con_exito.="mensaje_aceptacion";			
					$query_registrar_cargado_con_exito.=")";
					$query_registrar_cargado_con_exito.="VALUES";
					$query_registrar_cargado_con_exito.="(";
					$query_registrar_cargado_con_exito.="'".$this->cod_registro_especial_pss."',";
					$query_registrar_cargado_con_exito.="'".$nombre_prestador."',";
					$query_registrar_cargado_con_exito.="'".$this->cod_eapb_global."',";
					$query_registrar_cargado_con_exito.="'".$nombre_eapb."',";
					$query_registrar_cargado_con_exito.="'".$secuencia_dependiendo_existencia."',";
					$query_registrar_cargado_con_exito.="'".$nombre_archivo4505."',";
					$query_registrar_cargado_con_exito.="'".$fecha_actual."',";
					$query_registrar_cargado_con_exito.="'".$this->codigo_periodo."',";
					$query_registrar_cargado_con_exito.="'".$this->old_fecha_de_corte_periodo_mensual."',";
					$query_registrar_cargado_con_exito.="'".$lineas_del_archivo."',";
					$query_registrar_cargado_con_exito.="'Archivos validados con exito y cargados en el sistema'";
					$query_registrar_cargado_con_exito.=");";
					$error_bd_seq="";
					$bool_hubo_error_query=$obj->insertar_no_warning_get_error_no_crea_cierra($query_registrar_cargado_con_exito, $error_bd_seq);
					if($error_bd_seq!="")
					{
					    $error_de_base_de_datos.=" ERROR RESUMEN EXITOSO: ".$error_bd_seq."<br>";
					}
				}//fin if si hayo los nombres de las entidades inserta en la tabla exitosos
				
				//FIN PARTE SUBIDA A  TABLA gioss_registros_cargados_exito_4505
			    }//fin si proviene de ips
			    else if($this->tipo_entidad_que_efectua_el_cargue=="ent_territoriales")
			    {
				//BORRA RECHAZADOS PREVIOS DEL MISMO ARCHIVO		
				$sql_delete_rechazados="";
				$sql_delete_rechazados.=" DELETE FROM gioss_archivo_4505_rechazado_para_eapb WHERE ";
				$sql_delete_rechazados.=" codigo_eapb='".$this->cod_registro_especial_pss."' AND ";
				$sql_delete_rechazados.=" fecha_de_corte = '".$this->old_fecha_de_corte_periodo_mensual."' AND ";
				$sql_delete_rechazados.=" nombre_archivo ='".$nombre_archivo4505."' AND  ";
				$sql_delete_rechazados.=" codigo_departamento ='".$this->cod_dpto_filtro."' AND  ";
				$sql_delete_rechazados.=" codigo_municipio ='".$this->cod_mpio_filtro."'   ";
				$sql_delete_rechazados.=" ;  ";
				$error_bd_seq="";
				$bool_hubo_error_query=$obj->insertar_no_warning_get_error_no_crea_cierra($sql_delete_rechazados, $error_bd_seq);		
				//FIN BORRA RECHAZADOS PREVIOS DEL MISMO ARCHIVO
			    }
			    if($this->tipo_entidad_que_efectua_el_cargue=="agrupado_eapb")
			    {
				//DELETE DATOS RECHAZADOS DESPUES DE SUBIR A EXITOSOS Y DESPUES DEL RESUMEN EXITOSO
				$sql_delete_rechazados_bf="";
				$sql_delete_rechazados_bf.=" DELETE FROM gios_datos_rechazados_r4505 WHERE ";
				$sql_delete_rechazados_bf.=" cod_prestador_servicios_salud='AGRUP_EAPB' AND ";
				$sql_delete_rechazados_bf.=" codigo_eapb='".$this->cod_eapb_global."' AND ";
				$sql_delete_rechazados_bf.=" fecha_de_corte = '".$this->old_fecha_de_corte_periodo_mensual."' AND ";
				$sql_delete_rechazados_bf.=" nombre_archivo ='".$nombre_archivo4505."' ; ";
				$error_bd_seq="";
				$bool_hubo_error_query=$obj->insertar_no_warning_get_error_no_crea_cierra($sql_delete_rechazados_bf, $error_bd_seq);
				if($error_bd_seq!="")
				{
				    $error_de_base_de_datos.=" ERROR BORRAR RECHAZADOS DESPUES DE VALIDAR CON EXITO: ".$error_bd_seq."<br>";
				}
				//FIN DELETE RECHAZADOS DESPUES DE SUBIR A EXITOSOS Y DESPUES DEL RESUMEN EXITOSO
				
				
				//PARTE SUBIDA A  TABLA gioss_registros_cargados_exito_4505
				
				
				
				$query_nombre_eapb="";
				$query_nombre_eapb.="SELECT nombre_de_la_entidad FROM gioss_entidades_sector_salud WHERE codigo_entidad='".$this->cod_eapb_global."' ; ";
				$resultado_query_nombre_eapb=$obj->consultar2_no_crea_cierra($query_nombre_eapb);
				
				$nombre_eapb="";
				if(count($resultado_query_nombre_eapb)>0 )
				{
					$nombre_eapb=$resultado_query_nombre_eapb[0]["nombre_de_la_entidad"];
				}
				
				//if si hayo los nombres de las entidades inserta en la tabla exitosos
				if(count($resultado_query_nombre_eapb)>0)
				{
					$query_registrar_cargado_con_exito="";
					$query_registrar_cargado_con_exito.="INSERT INTO gioss_registros_cargados_exito_4505 ";
					$query_registrar_cargado_con_exito.="(";
					$query_registrar_cargado_con_exito.="codigo_habilitacion_reps,";
					$query_registrar_cargado_con_exito.="nombre_entidad_prestadora,";
					$query_registrar_cargado_con_exito.="codigo_eapb,";
					$query_registrar_cargado_con_exito.="nombre_eapb,";
					$query_registrar_cargado_con_exito.="numero_secuencia_validacion,";
					$query_registrar_cargado_con_exito.="nombre_archivo_4505,";
					$query_registrar_cargado_con_exito.="fecha_validacion,";
					$query_registrar_cargado_con_exito.="periodo_reportado,";
					$query_registrar_cargado_con_exito.="fecha_corte,";
					$query_registrar_cargado_con_exito.="numeros_registros_archivo_4505,";
					$query_registrar_cargado_con_exito.="mensaje_aceptacion";			
					$query_registrar_cargado_con_exito.=")";
					$query_registrar_cargado_con_exito.="VALUES";
					$query_registrar_cargado_con_exito.="(";
					$query_registrar_cargado_con_exito.="'AGRUP_EAPB',";
					$query_registrar_cargado_con_exito.="'AGRUP_EAPB',";
					$query_registrar_cargado_con_exito.="'".$this->cod_eapb_global."',";
					$query_registrar_cargado_con_exito.="'".$nombre_eapb."',";
					$query_registrar_cargado_con_exito.="'".$secuencia_dependiendo_existencia."',";
					$query_registrar_cargado_con_exito.="'".$nombre_archivo4505."',";
					$query_registrar_cargado_con_exito.="'".$fecha_actual."',";
					$query_registrar_cargado_con_exito.="'".$this->codigo_periodo."',";
					$query_registrar_cargado_con_exito.="'".$this->old_fecha_de_corte_periodo_mensual."',";
					$query_registrar_cargado_con_exito.="'".$lineas_del_archivo."',";
					$query_registrar_cargado_con_exito.="'Archivos validados con exito y cargados en el sistema'";
					$query_registrar_cargado_con_exito.=");";
					$error_bd_seq="";
					$bool_hubo_error_query=$obj->insertar_no_warning_get_error_no_crea_cierra($query_registrar_cargado_con_exito, $error_bd_seq);
					if($error_bd_seq!="")
					{
					    $error_de_base_de_datos.=" ERROR RESUMEN EXITOSO: ".$error_bd_seq."<br>";
					}
				}//fin if si hayo los nombres de las entidades inserta en la tabla exitosos
				
				//FIN PARTE SUBIDA A  TABLA gioss_registros_cargados_exito_4505
			    }
			    
		    }//fin if no hubo errores de escritura en la base de datos ni en los campos
		    
		    
		    
		    
		    //PARTE SUBIDA A TABLA QUE INDICA EL ESTADO DE VALIDACION CONSOLIDACION
		    if($this->tipo_entidad_que_efectua_el_cargue=="individual_ips"
		    	|| $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips"
		       		|| $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120"
		    	)
		    {
			$query_id_info_prestador="";
			$query_id_info_prestador.="SELECT * FROM gios_prestador_servicios_salud WHERE cod_registro_especial_pss='".$this->cod_registro_especial_pss."' ; ";
			$resultado_query_id_info_prestador=$obj->consultar2_no_crea_cierra($query_id_info_prestador);
			     
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
			
			
			
			if(count($resultado_query_id_info_prestador)>0)
			{	   
				$query_registrar_estado_validacion="";
				$query_registrar_estado_validacion.="INSERT INTO gioss_tabla_consolidacion_registros_validados_4505 ";
				$query_registrar_estado_validacion.="(";
				$query_registrar_estado_validacion.="estado_validacion,";
				$query_registrar_estado_validacion.="fecha_validacion,";
				$query_registrar_estado_validacion.="numero_secuencia,";
				$query_registrar_estado_validacion.="nombre_archivo,";
				$query_registrar_estado_validacion.="fecha_corte,";
				$query_registrar_estado_validacion.="tipo_identificacion_entidad_reportadora,";
				$query_registrar_estado_validacion.="numero_identificacion_entidad_reportadora,";
				$query_registrar_estado_validacion.="tipo_regimen,";
				$query_registrar_estado_validacion.="codigo_eapb,";
				$query_registrar_estado_validacion.="fecha_inicio_periodo,";
				$query_registrar_estado_validacion.="fecha_final_periodo,";	
				$query_registrar_estado_validacion.="codigo_entidad_reportadora,";
				$query_registrar_estado_validacion.="codigo_depto_prestador,";
				$query_registrar_estado_validacion.="codigo_municipio_prestador";
				$query_registrar_estado_validacion.=")";
				$query_registrar_estado_validacion.="VALUES";
				$query_registrar_estado_validacion.="(";
				$query_registrar_estado_validacion.="'".$estado_validacion_4505."',";
				$query_registrar_estado_validacion.="'".$fecha_actual."',";
				$query_registrar_estado_validacion.="'".$secuencia_dependiendo_existencia."',";
				$query_registrar_estado_validacion.="'".$nombre_archivo4505."',";
				$query_registrar_estado_validacion.="'".$this->old_fecha_de_corte_periodo_mensual."',";
				$query_registrar_estado_validacion.="'".$tipo_id_prestador."',";
				$query_registrar_estado_validacion.="'".$nit_prestador."',";
				$query_registrar_estado_validacion.="'".$this->tipo_de_regimen_de_la_informacion_reportada."',";
				$query_registrar_estado_validacion.="'".$this->cod_eapb_global."',";
				$query_registrar_estado_validacion.="'".$this->fecha_inicio_periodo."',";
				$query_registrar_estado_validacion.="'".$this->old_fecha_de_corte_periodo_mensual."',";
				$query_registrar_estado_validacion.="'".$this->cod_registro_especial_pss."',";
				$query_registrar_estado_validacion.="'".$codigo_depto_prestador."',";
				$query_registrar_estado_validacion.="'".$codigo_municipio_prestador."'";
				$query_registrar_estado_validacion.=");";
				$error_bd_seq="";
				$bool_hubo_error_query=$obj->insertar_no_warning_get_error_no_crea_cierra($query_registrar_estado_validacion, $error_bd_seq);
				if($error_bd_seq!="")
				{
				    $error_de_base_de_datos.="ERROR CONSOLIDACION ".$error_bd_seq;
				}
			}//prestadores archivo individual
			$mensaje_procesado="";
			$mensaje_procesado=$this->procesar_mensaje($error_de_base_de_datos);
			
			if(connection_aborted()==false)
			{
			    echo "<script>document.getElementById('errores_bd_div').innerHTML=\"<p id='parrafo_errores_bd' align='left'>$mensaje_procesado</p>\";</script>";
			}
			
		    }//fin prestador archivo individual
		    else if($this->tipo_entidad_que_efectua_el_cargue=="agrupado_eapb")
		    {
			
			
			$query_info_eapb="SELECT * FROM gios_entidad_administradora WHERE cod_entidad_administradora='".$this->cod_eapb_global."' ;";
			$resultado_query_info_eapb=$obj->consultar2_no_crea_cierra($query_info_eapb);
			$nombre_eapb="";
			if(count($resultado_query_info_eapb)>0)
			{
				$nombre_eapb=$resultado_query_info_eapb[0]["nom_entidad_administradora"];
			}
			
			$codigo_depto_eapb="";
			$codigo_municipio_eapb="";
			if(count($resultado_query_info_eapb)>0)
			{
				$codigo_depto_eapb=$resultado_query_info_eapb[0]["dpto"];
				$codigo_municipio_eapb=$resultado_query_info_eapb[0]["mpio"];
			}
			
			
			
			if(count($resultado_query_info_eapb)>0)
			{	   
				$query_registrar_estado_validacion="";
				$query_registrar_estado_validacion.="INSERT INTO gioss_tabla_consolidacion_registros_validados_4505 ";
				$query_registrar_estado_validacion.="(";
				$query_registrar_estado_validacion.="estado_validacion,";
				$query_registrar_estado_validacion.="fecha_validacion,";
				$query_registrar_estado_validacion.="numero_secuencia,";
				$query_registrar_estado_validacion.="nombre_archivo,";
				$query_registrar_estado_validacion.="fecha_corte,";
				$query_registrar_estado_validacion.="tipo_identificacion_entidad_reportadora,";
				$query_registrar_estado_validacion.="numero_identificacion_entidad_reportadora,";
				$query_registrar_estado_validacion.="tipo_regimen,";
				$query_registrar_estado_validacion.="codigo_eapb,";
				$query_registrar_estado_validacion.="fecha_inicio_periodo,";
				$query_registrar_estado_validacion.="fecha_final_periodo,";	
				$query_registrar_estado_validacion.="codigo_entidad_reportadora,";
				$query_registrar_estado_validacion.="codigo_depto_prestador,";
				$query_registrar_estado_validacion.="codigo_municipio_prestador";
				$query_registrar_estado_validacion.=")";
				$query_registrar_estado_validacion.="VALUES";
				$query_registrar_estado_validacion.="(";
				$query_registrar_estado_validacion.="'".$estado_validacion_4505."',";
				$query_registrar_estado_validacion.="'".$fecha_actual."',";
				$query_registrar_estado_validacion.="'".$secuencia_dependiendo_existencia."',";
				$query_registrar_estado_validacion.="'".$nombre_archivo4505."',";
				$query_registrar_estado_validacion.="'".$this->old_fecha_de_corte_periodo_mensual."',";
				$query_registrar_estado_validacion.="'00',";
				$query_registrar_estado_validacion.="'0000',";
				$query_registrar_estado_validacion.="'".$this->tipo_de_regimen_de_la_informacion_reportada."',";
				$query_registrar_estado_validacion.="'".$this->cod_eapb_global."',";
				$query_registrar_estado_validacion.="'".$this->fecha_inicio_periodo."',";
				$query_registrar_estado_validacion.="'".$this->old_fecha_de_corte_periodo_mensual."',";
				$query_registrar_estado_validacion.="'AGRUP_EAPB',";
				$query_registrar_estado_validacion.="'".$codigo_depto_eapb."',";
				$query_registrar_estado_validacion.="'".$codigo_municipio_eapb."'";
				$query_registrar_estado_validacion.=");";
				$error_bd_seq="";
				$bool_hubo_error_query=$obj->insertar_no_warning_get_error_no_crea_cierra($query_registrar_estado_validacion, $error_bd_seq);
				if($error_bd_seq!="")
				{
				    $error_de_base_de_datos.="ERROR CONSOLIDACION ".$error_bd_seq;
				}
			}//prestadores archivo individual
			$mensaje_procesado="";
			$mensaje_procesado=$this->procesar_mensaje($error_de_base_de_datos);
			
			if(connection_aborted()==false)
			{
			    echo "<script>document.getElementById('errores_bd_div').innerHTML=\"<p id='parrafo_errores_bd' align='left'>$mensaje_procesado</p>\";</script>";
				ob_flush();
			   flush();
			}
			
		    }//fin agrupado eapb
		    //FIN PARTE SUBIDA A TABLA QUE INDICA EL ESTADO DE VALIDACION CONSOLIDACION
	       
	      
		    //PARTE ENTIDADES OBLIGADAS A REPORTAR
		    if($this->tipo_entidad_que_efectua_el_cargue=="individual_ips"
		    	|| $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips"
		       		|| $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120"
		    	)
		    {
		    
			if(connection_aborted()==false)
				{
					//echo "GUIA 3 gioss_entidades_obligadas_a_reportar";
					ob_flush();
				   flush();
				}
			
			$query_buscar_si_entidad_esta_obligada_a_reportar="";
			$query_buscar_si_entidad_esta_obligada_a_reportar.=" SELECT max(year_actual::numeric) as last_year FROM gioss_entidades_obligadas_a_reportar ";
			$query_buscar_si_entidad_esta_obligada_a_reportar.=" WHERE ";
			$query_buscar_si_entidad_esta_obligada_a_reportar.=" codigo_prestador='".$this->cod_registro_especial_pss."' ";
			$query_buscar_si_entidad_esta_obligada_a_reportar.=" AND ";
			$query_buscar_si_entidad_esta_obligada_a_reportar.=" codigo_eapb='".$this->cod_eapb_global."' ";
			$query_buscar_si_entidad_esta_obligada_a_reportar.=";";
			$error_bd_seq="";
			$resultados_si_entidad_esta_obligada_a_reportar=$obj->consultar_no_warning_get_error_no_crea_cierra($query_buscar_si_entidad_esta_obligada_a_reportar, $error_bd_seq);		
			if($error_bd_seq!="")
			{
				$errores_bd_estado_informacion.=$error_bd_seq."<br>";
				if(connection_aborted()==false)
				{
				    echo "<script>alert(' ERROR ENTIDADES OBLIGADAS A REPORTAR: ".$this->procesar_mensaje($errores_bd_estado_informacion)."');</script>";
				}
				
			}
			
			if(connection_aborted()==false)
				{
					//echo "GUIA 6 gioss_entidades_obligadas_a_reportar: ".$query_buscar_si_entidad_esta_obligada_a_reportar;
					ob_flush();
				   flush();
				}
			
			
			if(is_array($resultados_si_entidad_esta_obligada_a_reportar) && count($resultados_si_entidad_esta_obligada_a_reportar)>0)
			{
				if(connection_aborted()==false)
				{
					//echo "GUIA 2 gioss_entidades_obligadas_a_reportar";
					ob_flush();
				   flush();
				}
				
			    $year_corte_que_se_esta_validando=intval(explode("-",$this->old_fecha_de_corte_periodo_mensual)[0]);
			    $year_consultado_bd=intval($resultados_si_entidad_esta_obligada_a_reportar[0]["last_year"]);
			    if($year_corte_que_se_esta_validando>$year_consultado_bd)
			    {
				$query_consulta_datos_anterior="";
				$query_consulta_datos_anterior.=" SELECT * FROM gioss_entidades_obligadas_a_reportar ";
				$query_consulta_datos_anterior.=" WHERE ";
				$query_consulta_datos_anterior.=" codigo_prestador='".$this->cod_registro_especial_pss."' ";
				$query_consulta_datos_anterior.=" AND ";
				$query_consulta_datos_anterior.=" codigo_eapb='".$this->cod_eapb_global."' ";
				$query_consulta_datos_anterior.=" AND ";
				$query_consulta_datos_anterior.=" year_actual::numeric='$year_consultado_bd' ";
				$query_consulta_datos_anterior.=";";
				$error_bd_seq="";
				$resultados_consulta_datos_anterior=$obj->consultar_no_warning_get_error_no_crea_cierra($query_consulta_datos_anterior, $error_bd_seq);		
				if($error_bd_seq!="")
				{
				    $errores_bd_estado_informacion.=$error_bd_seq."<br>";
				    if(connection_aborted()==false)
				    {
					echo "<script>alert(' ERROR DATOS ANTERIOR ENTIDADES OBLIGADAS A REPORTAR: ".$this->procesar_mensaje($errores_bd_estado_informacion)."');</script>";
				    }
				}
				
				if(is_array($resultados_consulta_datos_anterior))
				{
				    $query_upsert_entidades_obligadas_a_reportar="";
				    $query_upsert_entidades_obligadas_a_reportar.="INSERT into gioss_entidades_obligadas_a_reportar ";
				    $query_upsert_entidades_obligadas_a_reportar.="(";
				    $query_upsert_entidades_obligadas_a_reportar.="codigo_eapb,";
				    $query_upsert_entidades_obligadas_a_reportar.="tipo_regimen,";
				    $query_upsert_entidades_obligadas_a_reportar.="codigo_prestador,";
				    $query_upsert_entidades_obligadas_a_reportar.="tipo_identificacion,";
				    $query_upsert_entidades_obligadas_a_reportar.="numero_identificacion,";
				    $query_upsert_entidades_obligadas_a_reportar.="nombre_razon_social_prestador,";
				    $query_upsert_entidades_obligadas_a_reportar.="estado_prestador,";
				    $query_upsert_entidades_obligadas_a_reportar.="tipo_informacion_a_reportar,";
				    $query_upsert_entidades_obligadas_a_reportar.="tipo_archivo_norma,";
				    $query_upsert_entidades_obligadas_a_reportar.="codigo_municipio,";
				    $query_upsert_entidades_obligadas_a_reportar.="codigo_departamento,";			
				    $query_upsert_entidades_obligadas_a_reportar.="year_actual";
				    $query_upsert_entidades_obligadas_a_reportar.=")";
				    $query_upsert_entidades_obligadas_a_reportar.=" VALUES ";
				    $query_upsert_entidades_obligadas_a_reportar.="(";
				    $query_upsert_entidades_obligadas_a_reportar.="'".$resultados_consulta_datos_anterior[0]["codigo_eapb"]."',";
				    $query_upsert_entidades_obligadas_a_reportar.="'".$resultados_consulta_datos_anterior[0]["tipo_regimen"]."',";
				    $query_upsert_entidades_obligadas_a_reportar.="'".$resultados_consulta_datos_anterior[0]["codigo_prestador"]."',";
				    $query_upsert_entidades_obligadas_a_reportar.="'".$resultados_consulta_datos_anterior[0]["tipo_identificacion"]."',";
				    $query_upsert_entidades_obligadas_a_reportar.="'".$resultados_consulta_datos_anterior[0]["numero_identificacion"]."',";
				    $query_upsert_entidades_obligadas_a_reportar.="'".$resultados_consulta_datos_anterior[0]["nombre_razon_social_prestador"]."',";
				    $query_upsert_entidades_obligadas_a_reportar.="'".$resultados_consulta_datos_anterior[0]["estado_prestador"]."',";
				    $query_upsert_entidades_obligadas_a_reportar.="'".$resultados_consulta_datos_anterior[0]["tipo_informacion_a_reportar"]."',";
				    $query_upsert_entidades_obligadas_a_reportar.="'".$resultados_consulta_datos_anterior[0]["tipo_archivo_norma"]."',";
				    $query_upsert_entidades_obligadas_a_reportar.="'".$resultados_consulta_datos_anterior[0]["codigo_municipio"]."',";
				    $query_upsert_entidades_obligadas_a_reportar.="'".$resultados_consulta_datos_anterior[0]["codigo_departamento"]."',";
				    $query_upsert_entidades_obligadas_a_reportar.="'".$year_corte_que_se_esta_validando."'";
				    $query_upsert_entidades_obligadas_a_reportar.=")";
				    $query_upsert_entidades_obligadas_a_reportar.=";";
				    $error_bd_seq="";
				    $bool_hubo_error_query=$obj->insertar_no_warning_get_error_no_crea_cierra($query_upsert_entidades_obligadas_a_reportar, $error_bd_seq);		
				    if($error_bd_seq!="")
				    {
					    $errores_bd_estado_informacion.=$error_bd_seq."<br>";
					    if(connection_aborted()==false)
					    {
						echo "<script>alert(' ERROR insertando nuevo year para entidad obligada a reportar: ".$this->procesar_mensaje($errores_bd_estado_informacion)."');</script>";
					    }
				    }
				    
				    $query_update_datos_adicionales="";
				    $query_update_datos_adicionales.="UPDATE gioss_entidades_obligadas_a_reportar SET ";
				    if(intval($estado_validacion_4505)==1)
				    {
					if(intval($this->codigo_periodo)==1)
					{
					    $query_update_datos_adicionales.="periodo_1='1',";
					}
					else if(intval($this->codigo_periodo)>1)
					{
					    $query_update_datos_adicionales.="periodo_1='3',";
					}
					else if(intval($this->codigo_periodo)<1)
					{
					    $query_update_datos_adicionales.="periodo_1='0',";
					}
					if(intval($this->codigo_periodo)==2)
					{
					    $query_update_datos_adicionales.="periodo_2='1',";
					}
					else if(intval($this->codigo_periodo)>2)
					{
					    $query_update_datos_adicionales.="periodo_2='3',";
					}
					else if(intval($this->codigo_periodo)<2)
					{
					    $query_update_datos_adicionales.="periodo_2='0',";
					}
					if(intval($this->codigo_periodo)==3)
					{
					    $query_update_datos_adicionales.="periodo_3='1',";
					}
					else if(intval($this->codigo_periodo)>3)
					{
					    $query_update_datos_adicionales.="periodo_3='0',";
					}
					else if(intval($this->codigo_periodo)<3)
					{
					    $query_update_datos_adicionales.="periodo_3='3',";
					}
					if(intval($this->codigo_periodo)==4)
					{
					    $query_update_datos_adicionales.="periodo_4='1',";
					}
					else if(intval($this->codigo_periodo)>4)
					{
					    $query_update_datos_adicionales.="periodo_4='3',";
					}
					else if(intval($this->codigo_periodo)<4)
					{
					    $query_update_datos_adicionales.="periodo_4='0',";
					}
					
				    }
				    if(intval($estado_validacion_4505)==2)
				    {
					if(intval($this->codigo_periodo)==1)
					{
					    $query_update_datos_adicionales.="periodo_1='2',";
					}
					else if(intval($this->codigo_periodo)>1)
					{
					    $query_update_datos_adicionales.="periodo_1='3',";
					}
					else if(intval($this->codigo_periodo)<1)
					{
					    $query_update_datos_adicionales.="periodo_1='0',";
					}
					if(intval($this->codigo_periodo)==2)
					{
					    $query_update_datos_adicionales.="periodo_2='2',";
					}
					else if(intval($this->codigo_periodo)>2)
					{
					    $query_update_datos_adicionales.="periodo_2='3',";
					}
					else if(intval($this->codigo_periodo)<2)
					{
					    $query_update_datos_adicionales.="periodo_2='0',";
					}
					if(intval($this->codigo_periodo)==3)
					{
					    $query_update_datos_adicionales.="periodo_3='2',";
					}
					else if(intval($this->codigo_periodo)>3)
					{
					    $query_update_datos_adicionales.="periodo_3='0',";
					}
					else if(intval($this->codigo_periodo)<3)
					{
					    $query_update_datos_adicionales.="periodo_3='3',";
					}
					if(intval($this->codigo_periodo)==4)
					{
					    $query_update_datos_adicionales.="periodo_4='2',";
					}
					else if(intval($this->codigo_periodo)>4)
					{
					    $query_update_datos_adicionales.="periodo_4='3',";
					}
					else if(intval($this->codigo_periodo)<4)
					{
					    $query_update_datos_adicionales.="periodo_4='0',";
					}
				    }
				    
				    if(intval($this->codigo_periodo)==1)
				    {
					$query_update_datos_adicionales.="numero_registros_periodo_1='$lineas_del_archivo',";
				    }
				    else
				    {
					$query_update_datos_adicionales.="numero_registros_periodo_1='0',";
				    }
				    if(intval($this->codigo_periodo)==2)
				    {
					$query_update_datos_adicionales.="numero_registros_periodo_2='$lineas_del_archivo',";
				    }
				    else
				    {
					$query_update_datos_adicionales.="numero_registros_periodo_2='0',";
				    }
				    if(intval($this->codigo_periodo)==3)
				    {
					$query_update_datos_adicionales.="numero_registros_periodo_3='$lineas_del_archivo',";
				    }
				    else
				    {
					$query_update_datos_adicionales.="numero_registros_periodo_3='0',";
				    }
				    if(intval($this->codigo_periodo)==4)
				    {
					$query_update_datos_adicionales.="numero_registros_periodo_4='$lineas_del_archivo',";
				    }
				    else
				    {
					$query_update_datos_adicionales.="numero_registros_periodo_4='0',";
				    }
				    $query_update_datos_adicionales.=" WHERE ";
				    $query_update_datos_adicionales.=" codigo_eapb='".$this->cod_eapb_global."' ";
				    $query_update_datos_adicionales.=" AND codigo_prestador='".$this->cod_registro_especial_pss."' ";
				    $query_update_datos_adicionales.=" AND tipo_informacion_a_reportar='02' ";
				    $query_update_datos_adicionales.=" AND tipo_archivo_norma='0201' ";
				    $query_update_datos_adicionales.=" AND year_actual='$year_corte_que_se_esta_validando' ";
				    $query_update_datos_adicionales.=";";
				    $error_bd="";			
				    $bool_funciono=$obj->insertar_no_warning_get_error_no_crea_cierra($query_update_datos_adicionales, $error_bd);
				    if($error_bd!="")
				    {
					    $mensajes_error_bd.="ERROR AL ACTUALIZAR DATOS ADICIONALES LA ENTIDAD EN LA LINEA ".($numero_linea+1)." OBLIGADAS A REPORTAR: ".procesar_mensaje($error_bd)." <br> ";
				    }
				    
				}//fin if hay resultados datos anterior
				
			    }//fin if nuevo year es mayor que ultimo year
			    
				if(connection_aborted()==false)
				{
					//echo " GUIA 1 gioss_entidades_obligadas_a_reportar";
					ob_flush();
				   flush();
				}
				
			    if($year_corte_que_se_esta_validando==$year_consultado_bd)
			    {
				$query_consulta_datos_anterior="";
				$query_consulta_datos_anterior.=" SELECT * FROM gioss_entidades_obligadas_a_reportar ";
				$query_consulta_datos_anterior.=" WHERE ";
				$query_consulta_datos_anterior.=" codigo_prestador='".$this->cod_registro_especial_pss."' ";
				$query_consulta_datos_anterior.=" AND ";
				$query_consulta_datos_anterior.=" codigo_eapb='".$this->cod_eapb_global."' ";
				$query_consulta_datos_anterior.=" AND ";
				$query_consulta_datos_anterior.=" year_actual::numeric='$year_corte_que_se_esta_validando' ";
				$query_consulta_datos_anterior.=";";
				$error_bd_seq="";
				$resultados_consulta_datos_anterior=$obj->consultar_no_warning_get_error_no_crea_cierra($query_consulta_datos_anterior, $error_bd_seq);		
				if($error_bd_seq!="")
				{
				    $errores_bd_estado_informacion.=$error_bd_seq."<br>";
				    if(connection_aborted()==false)
				    {
					echo "<script>alert(' ERROR DATOS ANTERIOR ENTIDADES OBLIGADAS A REPORTAR: ".$this->procesar_mensaje($errores_bd_estado_informacion)."');</script>";
				    }
				}
				
				if(is_array($resultados_consulta_datos_anterior))
				{
				    $estado_periodo_a_actualizar=intval($resultados_consulta_datos_anterior[0]["periodo_".intval($this->codigo_periodo)]);
				    
				    if($estado_periodo_a_actualizar!=1)
				    {
					$query_update_datos_adicionales="";
					$query_update_datos_adicionales.="UPDATE gioss_entidades_obligadas_a_reportar SET ";
					if(intval($estado_validacion_4505)==1)
					{
					    if(intval($this->codigo_periodo)==1)
					    {
						$query_update_datos_adicionales.="periodo_1='1',";
					    }
					    if(intval($this->codigo_periodo)==2)
					    {
						$query_update_datos_adicionales.="periodo_2='1',";
					    }
					    if(intval($this->codigo_periodo)==3)
					    {
						$query_update_datos_adicionales.="periodo_3='1',";
					    }
					    if(intval($this->codigo_periodo)==4)
					    {
						$query_update_datos_adicionales.="periodo_4='1',";
					    }
					    
					}
					if(intval($estado_validacion_4505)==2)
					{
					    if(intval($this->codigo_periodo)==1)
					    {
						$query_update_datos_adicionales.="periodo_1='2',";
					    }
					    if(intval($this->codigo_periodo)==2)
					    {
						$query_update_datos_adicionales.="periodo_2='2',";
					    }
					    if(intval($this->codigo_periodo)==3)
					    {
						$query_update_datos_adicionales.="periodo_3='2',";
					    }
					    if(intval($this->codigo_periodo)==4)
					    {
						$query_update_datos_adicionales.="periodo_4='2',";
					    }
					}			
					if(intval($this->codigo_periodo)==1)
					{
					    $query_update_datos_adicionales.="numero_registros_periodo_1='$lineas_del_archivo',";
					}
					if(intval($this->codigo_periodo)==2)
					{
					    $query_update_datos_adicionales.="numero_registros_periodo_2='$lineas_del_archivo',";
					}
					if(intval($this->codigo_periodo)==3)
					{
					    $query_update_datos_adicionales.="numero_registros_periodo_3='$lineas_del_archivo',";
					}
					if(intval($this->codigo_periodo)==4)
					{
					    $query_update_datos_adicionales.="numero_registros_periodo_4='$lineas_del_archivo',";
					}
					$query_update_datos_adicionales.=" WHERE ";
					$query_update_datos_adicionales.=" codigo_eapb='".$this->cod_eapb_global."' ";
					$query_update_datos_adicionales.=" AND codigo_prestador='".$this->cod_registro_especial_pss."' ";
					$query_update_datos_adicionales.=" AND tipo_informacion_a_reportar='02' ";
					$query_update_datos_adicionales.=" AND tipo_archivo_norma='0201' ";
					$query_update_datos_adicionales.=" AND year_actual='$year_corte_que_se_esta_validando' ";
					$query_update_datos_adicionales.=";";
					$error_bd="";			
					$bool_funciono=$obj->insertar_no_warning_get_error_no_crea_cierra($query_update_datos_adicionales, $error_bd);
					if($error_bd!="")
					{
						$mensajes_error_bd.="ERROR AL ACTUALIZAR DATOS ADICIONALES LA ENTIDAD EN LA LINEA ".($numero_linea+1)." OBLIGADAS A REPORTAR: ".procesar_mensaje($error_bd)." <br> ";
					}
				    }//solo se actualiza si el estado del periodo a actualizar es diferente de 1
				}//fin si existe
			    }//fin if nuevo year igual ultimo year
			}//fin if
		    }//fin if solo archivos provenientes de prestadores
		    
		    //FIN ENTIDADES OBLIGADAS A REPORTAR
	       
		    //PARTE ESTADO INFORMACION
		    if($this->tipo_entidad_que_efectua_el_cargue=="individual_ips"
		    	|| $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips"
		       		|| $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120"
		    	)
		    {
			$query_info_eapb="SELECT * FROM gios_entidad_administradora WHERE cod_entidad_administradora='".$this->cod_eapb_global."' ;";
			$resultado_query_info_eapb=$obj->consultar2_no_crea_cierra($query_info_eapb);
			$nombre_eapb="";
			if(count($resultado_query_info_eapb)>0)
			{
				$nombre_eapb=$resultado_query_info_eapb[0]["nom_entidad_administradora"];
			}
			
			$query_id_info_prestador="";
			$query_id_info_prestador.="SELECT * FROM gios_prestador_servicios_salud WHERE cod_registro_especial_pss='".$this->cod_registro_especial_pss."' ; ";
			$resultado_query_id_info_prestador=$obj->consultar2_no_crea_cierra($query_id_info_prestador);
			
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
		   
			$query_descripcion_estado_validacion="";
			$query_descripcion_estado_validacion.=" SELECT * FROM gioss_estado_validacion_archivos WHERE codigo_estado_validacion='$estado_validacion_4505' ; ";
			$resultado_query_descripcion_estado_validacion=$obj->consultar2_no_crea_cierra($query_descripcion_estado_validacion);
			$descripcion_estado_validacion=$resultado_query_descripcion_estado_validacion[0]["descripcion_estado_validacion"];
			
			$query_nombre_dpt="SELECT * FROM gios_dpto WHERE cod_departamento='$codigo_depto_prestador' ; ";
			$resultado_query_dpto=$obj->consultar2_no_crea_cierra($query_nombre_dpt);
			$nombre_dpto="";
			if(count($resultado_query_dpto)>0)
			{
				$nombre_dpto=$resultado_query_dpto[0]["nom_departamento"];
			}
			
			$query_nombre_mpio="SELECT * FROM gios_mpio WHERE cod_municipio='$codigo_municipio_prestador' ; ";
			$resultado_query_mpio=$obj->consultar2_no_crea_cierra($query_nombre_mpio);
			$nombre_mpio="";
			if(count($resultado_query_mpio)>0)
			{
				$nombre_mpio=$resultado_query_mpio[0]["nom_municipio"];
			}
					    
			if(count($resultado_query_id_info_prestador)>0
			   && count($resultado_query_info_eapb)>0
			   && count($resultado_query_dpto)>0
			   && count($resultado_query_mpio)>0
			   )
			{	   
				    $query_registrar_estado_informacion="";
				    $query_registrar_estado_informacion.="INSERT INTO gioss_tabla_estado_informacion_4505 ";
				    $query_registrar_estado_informacion.="(";
				    $query_registrar_estado_informacion.="codigo_estado_informacion,";
				    $query_registrar_estado_informacion.="nombre_estado_informacion,";
				    $query_registrar_estado_informacion.="fecha_validacion,";
				    $query_registrar_estado_informacion.="periodo_reporte,";
				    $query_registrar_estado_informacion.="descripcion_periodo_reporte,";
				    $query_registrar_estado_informacion.="fecha_corte_periodo,";
				    $query_registrar_estado_informacion.="numero_secuencia,";
				    $query_registrar_estado_informacion.="codigo_eapb,";
				    $query_registrar_estado_informacion.="nombre_eapb,";
				    $query_registrar_estado_informacion.="codigo_prestador_servicios,";	
				    $query_registrar_estado_informacion.="tipo_identificacion_prestador,";
				    $query_registrar_estado_informacion.="numero_identificacion_prestado,";
				    $query_registrar_estado_informacion.="nombre_del_archivo,";
				    $query_registrar_estado_informacion.="total_registros,";
				    $query_registrar_estado_informacion.="codigo_departamento,";
				    $query_registrar_estado_informacion.="nombre_del_departamento,";
				    $query_registrar_estado_informacion.="codigo_municipio,";
				    $query_registrar_estado_informacion.="nombre_de_municipio";
				    $query_registrar_estado_informacion.=")";
				    $query_registrar_estado_informacion.="VALUES";
				    $query_registrar_estado_informacion.="(";
				    $query_registrar_estado_informacion.="'".$estado_validacion_4505."',";
				    $query_registrar_estado_informacion.="'".$descripcion_estado_validacion."',";
				    $query_registrar_estado_informacion.="'".$fecha_actual."',";
				    $query_registrar_estado_informacion.="'".$this->codigo_periodo."',";
				    $query_registrar_estado_informacion.="'".$this->codigo_periodo."',";
				    $query_registrar_estado_informacion.="'".$this->old_fecha_de_corte_periodo_mensual."',";			
				    $query_registrar_estado_informacion.="'".$secuencia_dependiendo_existencia."',";		
				    $query_registrar_estado_informacion.="'".$this->cod_eapb_global."',";
				    $query_registrar_estado_informacion.="'".$nombre_eapb."',";
				    $query_registrar_estado_informacion.="'".$this->cod_registro_especial_pss."',";
				    $query_registrar_estado_informacion.="'".$tipo_id_prestador."',";
				    $query_registrar_estado_informacion.="'".$nit_prestador."',";
				    $query_registrar_estado_informacion.="'".$nombre_archivo4505."',";
				    $query_registrar_estado_informacion.="'".$lineas_del_archivo."',";	
				    $query_registrar_estado_informacion.="'".$codigo_depto_prestador."',";
				    $query_registrar_estado_informacion.="'".$nombre_dpto."',";
				    $query_registrar_estado_informacion.="'".$codigo_municipio_prestador."',";
				    $query_registrar_estado_informacion.="'".$nombre_mpio."' ";
				    $query_registrar_estado_informacion.=");";
				    $error_bd_seq="";
				    $bool_hubo_error_query=$obj->insertar_no_warning_get_error_no_crea_cierra($query_registrar_estado_informacion, $error_bd_seq);		
				    if($error_bd_seq!="")
				    {
					    $errores_bd_estado_informacion.=$error_bd_seq."<br>";
					    if(connection_aborted()==false)
					    {
						echo "<script>alert(' ERROR ESTADO INFORMACION: ".$this->procesar_mensaje($errores_bd_estado_informacion)."');</script>";
					    }
				    }
			}//fin if
		    }//fin if prestador archivo individual
		    
		    if($this->tipo_entidad_que_efectua_el_cargue=="ent_territoriales")
		    {
			$query_info_eapb="SELECT * FROM gios_entidad_administradora WHERE cod_entidad_administradora='".$this->cod_registro_especial_pss."' ;";
			$resultado_query_info_eapb=$obj->consultar2_no_crea_cierra($query_info_eapb);
			$nombre_eapb="";
			if(count($resultado_query_info_eapb)>0)
			{
				$nombre_eapb=$resultado_query_info_eapb[0]["nom_entidad_administradora"];
			}
			
			$query_nombre_dpt="SELECT * FROM gios_dpto WHERE cod_departamento='".$this->cod_dpto_filtro."' ; ";
			$resultado_query_dpto=$obj->consultar2_no_crea_cierra($query_nombre_dpt);
			$nombre_dpto="";
			if(count($resultado_query_dpto)>0)
			{
				$nombre_dpto=$resultado_query_dpto[0]["nom_departamento"];
			}
			
			$query_nombre_mpio="SELECT * FROM gios_mpio WHERE cod_municipio='".$this->cod_mpio_filtro."' ; ";
			$resultado_query_mpio=$obj->consultar2_no_crea_cierra($query_nombre_mpio);
			$nombre_mpio="";
			if(count($resultado_query_mpio)>0)
			{
				$nombre_mpio=$resultado_query_mpio[0]["nom_municipio"];
			}
		    
		    //echo "<script>alert('entro gioss_tabla_estado_informacion_4505_eapb')</script>";

			$query_registrar_estado_informacion="";
			$query_registrar_estado_informacion.="INSERT INTO gioss_tabla_estado_informacion_4505_eapb ";
			$query_registrar_estado_informacion.="(";
			$query_registrar_estado_informacion.="codigo_estado_informacion,";
			$query_registrar_estado_informacion.="nombre_estado_informacion,";
			$query_registrar_estado_informacion.="fecha_validacion,";
			$query_registrar_estado_informacion.="periodo_reporte,";
			$query_registrar_estado_informacion.="descripcion_periodo_reporte,";
			$query_registrar_estado_informacion.="fecha_corte_periodo,";
			$query_registrar_estado_informacion.="numero_secuencia,";
			$query_registrar_estado_informacion.="codigo_eapb,";
			$query_registrar_estado_informacion.="nombre_eapb,";
			$query_registrar_estado_informacion.="nombre_del_archivo,";
			$query_registrar_estado_informacion.="total_registros,";
			$query_registrar_estado_informacion.="codigo_departamento,";
			$query_registrar_estado_informacion.="nombre_del_departamento,";
			$query_registrar_estado_informacion.="codigo_municipio,";
			$query_registrar_estado_informacion.="nombre_de_municipio";
			$query_registrar_estado_informacion.=")";
			$query_registrar_estado_informacion.="VALUES";
			$query_registrar_estado_informacion.="(";
			$query_registrar_estado_informacion.="'".$estado_validacion_4505."',";
			$query_registrar_estado_informacion.="'".$descripcion_estado_validacion."',";
			$query_registrar_estado_informacion.="'".$fecha_actual."',";
			$query_registrar_estado_informacion.="'".$this->codigo_periodo."',";
			$query_registrar_estado_informacion.="'".$this->codigo_periodo."',";
			$query_registrar_estado_informacion.="'".$this->old_fecha_de_corte_periodo_mensual."',";			
			$query_registrar_estado_informacion.="'".$secuencia_dependiendo_existencia."',";
			//aqui el codigo de la eap es el codigo de la entidad reportante
			$query_registrar_estado_informacion.="'".$this->cod_registro_especial_pss."',";
			$query_registrar_estado_informacion.="'".$nombre_eapb."',";
			$query_registrar_estado_informacion.="'".$nombre_archivo4505."',";
			$query_registrar_estado_informacion.="'".$lineas_del_archivo."',";	
			$query_registrar_estado_informacion.="'".$this->cod_dpto_filtro."',";
			$query_registrar_estado_informacion.="'".$nombre_dpto."',";
			$query_registrar_estado_informacion.="'".$this->cod_mpio_filtro."',";
			$query_registrar_estado_informacion.="'".$nombre_mpio."' ";
			$query_registrar_estado_informacion.=");";
			$error_bd_seq="";
			$bool_hubo_error_query=$obj->insertar_no_warning_get_error_no_crea_cierra($query_registrar_estado_informacion, $error_bd_seq);		
			if($error_bd_seq!="")
			{
				$errores_bd_estado_informacion.=$error_bd_seq."<br>";
				if(connection_aborted()==false)
				{
				    echo "<script>alert(' ERROR ESTADO INFORMACION EAPB: ".$this->procesar_mensaje($errores_bd_estado_informacion)."');</script>";
				}
			}//fin if
		    }//fin entidades territoriales
		    
		    if($this->tipo_entidad_que_efectua_el_cargue=="agrupado_eapb")
		    {
			$query_info_eapb="SELECT * FROM gios_entidad_administradora WHERE cod_entidad_administradora='".$this->cod_eapb_global."' ;";
			$resultado_query_info_eapb=$obj->consultar2_no_crea_cierra($query_info_eapb);
			$nombre_eapb="";
			if(count($resultado_query_info_eapb)>0)
			{
				$nombre_eapb=$resultado_query_info_eapb[0]["nom_entidad_administradora"];
			}

			 //echo "<script>alert('  gios_entidad_administradora ".$this->procesar_mensaje($query_info_eapb)." ".count($resultado_query_info_eapb)."');</script>";

			 if(count($resultado_query_info_eapb)==0)
			 {
			 	echo "<script>alert(' La EAPB usada ".$this->cod_eapb_global." usada para la validacion no esta en gios_entidad_administradora , debe de registrarse para poder actualizar el estado de la validacion use la interfas de ACT. EAPB');</script>";
			 }//mensaje
			
			$codigo_depto_eapb="";
			$codigo_municipio_eapb="";
			if(count($resultado_query_info_eapb)>0)
			{
				$codigo_depto_eapb=$resultado_query_info_eapb[0]["dpto"];
				$codigo_municipio_eapb=$resultado_query_info_eapb[0]["mpio"];
			}
			
				
		   
			$query_descripcion_estado_validacion="";
			$query_descripcion_estado_validacion.=" SELECT * FROM gioss_estado_validacion_archivos WHERE codigo_estado_validacion='$estado_validacion_4505' ; ";
			$resultado_query_descripcion_estado_validacion=$obj->consultar2_no_crea_cierra($query_descripcion_estado_validacion);
			$descripcion_estado_validacion=$resultado_query_descripcion_estado_validacion[0]["descripcion_estado_validacion"];
			
			$query_nombre_dpt="SELECT * FROM gios_dpto WHERE cod_departamento='$codigo_depto_eapb' ; ";
			$resultado_query_dpto=$obj->consultar2_no_crea_cierra($query_nombre_dpt);
			$nombre_dpto="";
			if(count($resultado_query_dpto)>0)
			{
				$nombre_dpto=$resultado_query_dpto[0]["nom_departamento"];
			}
			
			$query_nombre_mpio="SELECT * FROM gios_mpio WHERE cod_municipio='$codigo_municipio_eapb' ; ";
			$resultado_query_mpio=$obj->consultar2_no_crea_cierra($query_nombre_mpio);
			$nombre_mpio="";
			if(count($resultado_query_mpio)>0)
			{
				$nombre_mpio=$resultado_query_mpio[0]["nom_municipio"];
			}

			//echo "<script>alert('entro gioss_tabla_estado_informacion_4505')</script>";
					    
			if(count($resultado_query_info_eapb)>0)
			{	   
				    $query_registrar_estado_informacion="";
				    $query_registrar_estado_informacion.="INSERT INTO gioss_tabla_estado_informacion_4505 ";
				    $query_registrar_estado_informacion.="(";
				    $query_registrar_estado_informacion.="codigo_estado_informacion,";
				    $query_registrar_estado_informacion.="nombre_estado_informacion,";
				    $query_registrar_estado_informacion.="fecha_validacion,";
				    $query_registrar_estado_informacion.="periodo_reporte,";
				    $query_registrar_estado_informacion.="descripcion_periodo_reporte,";
				    $query_registrar_estado_informacion.="fecha_corte_periodo,";
				    $query_registrar_estado_informacion.="numero_secuencia,";
				    $query_registrar_estado_informacion.="codigo_eapb,";
				    $query_registrar_estado_informacion.="nombre_eapb,";
				    $query_registrar_estado_informacion.="codigo_prestador_servicios,";	
				    $query_registrar_estado_informacion.="tipo_identificacion_prestador,";
				    $query_registrar_estado_informacion.="numero_identificacion_prestado,";
				    $query_registrar_estado_informacion.="nombre_del_archivo,";
				    $query_registrar_estado_informacion.="total_registros,";
				    $query_registrar_estado_informacion.="codigo_departamento,";
				    $query_registrar_estado_informacion.="nombre_del_departamento,";
				    $query_registrar_estado_informacion.="codigo_municipio,";
				    $query_registrar_estado_informacion.="nombre_de_municipio";
				    $query_registrar_estado_informacion.=")";
				    $query_registrar_estado_informacion.="VALUES";
				    $query_registrar_estado_informacion.="(";
				    $query_registrar_estado_informacion.="'".$estado_validacion_4505."',";
				    $query_registrar_estado_informacion.="'".$descripcion_estado_validacion."',";
				    $query_registrar_estado_informacion.="'".$fecha_actual."',";
				    $query_registrar_estado_informacion.="'".$this->codigo_periodo."',";
				    $query_registrar_estado_informacion.="'".$this->codigo_periodo."',";
				    $query_registrar_estado_informacion.="'".$this->old_fecha_de_corte_periodo_mensual."',";			
				    $query_registrar_estado_informacion.="'".$secuencia_dependiendo_existencia."',";		
				    $query_registrar_estado_informacion.="'".$this->cod_eapb_global."',";
				    $query_registrar_estado_informacion.="'".$nombre_eapb."',";
				    $query_registrar_estado_informacion.="'AGRUP_EAPB',";
				    $query_registrar_estado_informacion.="'00',";
				    $query_registrar_estado_informacion.="'0000',";
				    $query_registrar_estado_informacion.="'".$nombre_archivo4505."',";
				    $query_registrar_estado_informacion.="'".$lineas_del_archivo."',";	
				    $query_registrar_estado_informacion.="'".$codigo_depto_eapb."',";
				    $query_registrar_estado_informacion.="'".$nombre_dpto."',";
				    $query_registrar_estado_informacion.="'".$codigo_municipio_eapb."',";
				    $query_registrar_estado_informacion.="'".$nombre_mpio."' ";
				    $query_registrar_estado_informacion.=");";
				    $error_bd_seq="";
				    $bool_hubo_error_query=$obj->insertar_no_warning_get_error_no_crea_cierra($query_registrar_estado_informacion, $error_bd_seq);		
				    if($error_bd_seq!="")
				    {
					    $errores_bd_estado_informacion.=$error_bd_seq."<br>";
					    if(connection_aborted()==false)
					    {
						echo "<script>alert(' ERROR ESTADO INFORMACION: ".$this->procesar_mensaje($errores_bd_estado_informacion)."');</script>";
					    }
				    }

				    //echo "<script>alert('  ESTADO INFORMACION: ".$this->procesar_mensaje($errores_bd_estado_informacion)."');</script>";
			}//fin if
		    }//fin eapb agrupado
		    
		    //FIN PARTE ESTADO INFORMACION
		    
		    
		    
		    
		    unset($this->diccionario_identificacion);
		    unset($this->diccionario_identificacion_lineas);
			    
		    
		    if(connection_aborted()==false)
		    {
		    	$tiempo_temp_porcentaje_actual = "".date('H:i:s');
			echo "<script>document.getElementById('advertencia').innerHTML=\"<p  align='center'>Se ha terminado de revisar y validar las ".$lineas_del_archivo." lineas del archivo R4505.<br> Se han encontrado  $contador_registros_buenos registros buenos y $contador_registros_malos registros malos.<br>$tiempo_temp_porcentaje_actual</p>\";</script>";
			echo "<script>document.getElementById('loading').style.display='none';</script>";
			echo "<script>document.getElementById('estado_validacion').style.display='none';</script>";
			echo "<script>document.title='V F PyP $nombre_archivo4505.';</script>";
			ob_flush();
			flush();
		    }


		    //PARTE CREACION ARCHIVO REPORTE REGISTROS POR IPS
		    $archivo_reporte_por_ips_exitoso=true;
		    try
		    {
			    $codigo_prestador_para_insercion="";
		    	$codigo_eapb_para_insercion="";

		    	if($this->tipo_entidad_que_efectua_el_cargue=="agrupado_eapb")
		    	{
		    		$codigo_prestador_para_insercion="AGRUP_EAPB";
		    		$codigo_eapb_para_insercion=$this->cod_eapb_global;
		    	}//fin if
		    	if($this->tipo_entidad_que_efectua_el_cargue=="ent_territoriales")
		    	{
		    		$codigo_prestador_para_insercion="AGRUP_ENT_TERR";
		    		$codigo_eapb_para_insercion=$this->cod_registro_especial_pss;
		    	}//fin if
		    	if($this->tipo_entidad_que_efectua_el_cargue=="individual_ips" 
		    		|| $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips"
		    		|| $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120"
		    		)
		    	{
		    		$codigo_prestador_para_insercion=$this->cod_registro_especial_pss;
		    		$codigo_eapb_para_insercion=$this->cod_eapb_global;
		    	}//fin if

			$fecha_de_corte_temp_analisis=$this->fecha_de_corte_periodo;
					if($fecha_de_corte_temp_analisis!=$this->old_fecha_de_corte_periodo_mensual)
					{
					    $fecha_de_corte_temp_analisis=$this->old_fecha_de_corte_periodo_mensual;
					}//fin if

			    $sql_count_reporte_por_ips="";
				$sql_count_reporte_por_ips.="SELECT count(*) as numero_registros
				FROM gioss_indexador_para_reporte_ips 
				WHERE 
				entidad_reportante ='".$codigo_prestador_para_insercion."'
				AND
				entidad_a_reportar ='".$codigo_eapb_para_insercion."'
				AND
				fecha_inicio_periodo ='".$this->fecha_inicial_para_analisis."'
				AND
				fecha_de_corte ='".$fecha_de_corte_temp_analisis."'
				AND
				fecha_y_hora_validacion ='".$fecha_actual." ".$tiempo_actual."'
				AND 
				nombre_archivo ='".$nombre_archivo4505."'
				AND
				numero_de_secuencia ='".$secuencia_dependiendo_existencia."'
				";

				$numero_registros_para_reporte_por_ips=0;

				$resultado_query_count_reporte_por_ips=$obj->consultar2_no_crea_cierra($sql_count_reporte_por_ips);
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
					$array_fecha_corte_rep_ips=explode("-", $this->fecha_de_corte_periodo);
					$mes_periodo="";
					if(isset($array_fecha_corte_rep_ips['1']) )
					{
						$mes_periodo=$array_fecha_corte_rep_ips['1'];
					}//fin if
					

				    $this->global_ruta_reporte_registros_por_ips=$ruta_temporales."RESULTADOVALIDACIONPORIPS".$codigo_eapb_para_insercion.$mes_periodo.str_replace('-', '', $fecha_actual).".csv";		    
				    //se remplaza el archivo si ya existe con modo w		
				    $archivo_reporte_registros_por_ips = fopen($this->global_ruta_reporte_registros_por_ips, "w") or die("fallo la creacion del archivo modo:w reporte para ips ");	

				    $parte_inicial_reporte_registros_por_ips="";
					$parte_inicial_reporte_registros_por_ips.="SISTEMA DE INFORMACION GIOSS\n";
					$parte_inicial_reporte_registros_por_ips.="REPORTE DE ESTADO DE REGISTROS REPORTADOS POR INSTITUCION PRESTADORA DE SERVICIOS IPS\n";
					$parte_inicial_reporte_registros_por_ips.="RESOLUCION 4505 del 2012\n";
					$parte_inicial_reporte_registros_por_ips.="RESULTADO DEL PROCESO DE VALIDACION\n";

					fwrite($archivo_reporte_registros_por_ips, $parte_inicial_reporte_registros_por_ips); 

				    $titulos_reporte="";
					$titulos_reporte.="\"Numero Identificacion Prestador\";\"Codigo Habilitacion Prestador\";\"Nombre Del Prestador\";\"Codigo Del Departamento\";\"Codigo Municipio\";\"Numero de Registros Leidos\";\"Numero de Registros Errados\";\"Numero de Registros Correctos\";\"Relacion Registros Correctos\";\"Numero de Inconsistencias por IPS\"";
					$titulos_reporte=str_replace("_", " ", $titulos_reporte);
					//$titulos_reporte=strtoupper($titulos_reporte);
					fwrite($archivo_reporte_registros_por_ips, $titulos_reporte);    		        
				    
					$sql_reporte_por_ips="";
					$sql_reporte_por_ips.="SELECT *
					FROM gioss_indexador_para_reporte_ips 
					WHERE 
					entidad_reportante ='".$codigo_prestador_para_insercion."'
					AND
					entidad_a_reportar ='".$codigo_eapb_para_insercion."'
					AND
					fecha_inicio_periodo ='".$this->fecha_inicial_para_analisis."'
					AND
					fecha_de_corte ='".$fecha_de_corte_temp_analisis."'
					AND
					fecha_y_hora_validacion ='".$fecha_actual." ".$tiempo_actual."'
					AND 
					nombre_archivo ='".$nombre_archivo4505."'
					AND
					numero_de_secuencia ='".$secuencia_dependiendo_existencia."'
					LIMIT $block_limit OFFSET $offset

					";
					$resultado_query_reporte_por_ips=$obj->consultar2_no_crea_cierra($sql_reporte_por_ips);
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
						fwrite($archivo_reporte_registros_por_ips, "\n".$linea_reporte);

					}//fin foreach

					fclose($archivo_reporte_registros_por_ips);

				}//fin if hay registros
			}//fin try
			catch(Exception $e)
			{
				$archivo_reporte_por_ips_exitoso=false;
				echo "ERROR EN LA CONSTRUCCION DEL REPORTE DE RESULTADOS POR IPS<br>".$e->getMessage();
			}//fin catch
			//FIN PARTE CREACION ARCHIVO REPORTE REGISTROS POR IPS
		    
		    //PARTE ESCRIBE ARCHIVO ERRORES CAMPO INDIVIDUAL
		    $resultados_nombre_campo=array();
			if(count($this->globalNombreCampos)>0 && is_array($this->globalNombreCampos)==true)
			{
				$resultados_nombre_campo=$this->globalNombreCampos;
			}
			else
			{
		    	$query_nombre_campo="SELECT * FROM valores_permitidos_4505 ORDER BY numero_campo_norma::numeric asc;";
				$resultados_nombre_campo=$obj->consultar2_no_crea_cierra($query_nombre_campo);
			}//fin else
			//echo print_r($resultados_nombre_campo,true);			
			$this->global_ruta_reporte_calificacion_campos=$ruta_temporales."EvaluacionResultadoValidacion".$codigo_eapb_para_insercion.$this->codigo_periodo.str_replace('-', '', $fecha_actual).".csv";		
			$archivo_reporte_errores_por_campo = fopen($this->global_ruta_reporte_calificacion_campos, "w") or die("fallo la creacion del archivo modo:w reporte errores por campo ");

			$lineas_totales_archivo_temp=(intval($lineas_del_archivo)-1);
			//lineas_del_archivo

			$fecha_terminacion="";
			$hora_terminacion="";

			$fecha_terminacion = "".date('Y-m-d');
		    $hora_terminacion = "".date('H:i:s');

			$parte_inicial_reporte_evaluacion_resultado_validacion="";
			$parte_inicial_reporte_evaluacion_resultado_validacion.="SISTEMA DE INFORMACION GIOSS\n";
			$parte_inicial_reporte_evaluacion_resultado_validacion.="Evaluacion Resultados Por Campo Proceso validacion\n";
			$parte_inicial_reporte_evaluacion_resultado_validacion.="Resolucion 4505 del 2012\n";
			$parte_inicial_reporte_evaluacion_resultado_validacion.="Numero de Registros Validados ( ".$lineas_totales_archivo_temp." ) \n";
			$parte_inicial_reporte_evaluacion_resultado_validacion.="\"$codigo_eapb_para_insercion\";\"Periodo ".$this->codigo_periodo."\"\n\"Fecha Y Hora Inicio Validacion $fecha_actual $tiempo_actual\"\n\"Fecha Y Hora Fin Validacion $fecha_terminacion $hora_terminacion\"";
			fwrite($archivo_reporte_errores_por_campo, $parte_inicial_reporte_evaluacion_resultado_validacion);



			$titulos_conteo="\"NUMERO CAMPO\";\"DESCRIPCION CAMPO\";\"NUMERO DE REGISTROS CORRECTOS\";\"NUMERO DE REGISTROS INCONSISTENTES\";\"PORCENTAJE DE REGISTROS INCONSISTENTES\";\"0105 Inconsistencias Calidad\";\"0104 Inconsistencias Campo En Blanco\";\"0103 Inconsistencias Valor Permitido\";\"0102 Inconsistencias Formato\";\"0101 Inconsistencias Longitud\";\"TOTAL INCONSISTENCIAS\"";
			
			fwrite($archivo_reporte_errores_por_campo, "\n".$titulos_conteo);
			foreach($array_contador_total_errores_obligatorios_campo as $key=>$error_campo_actual)
			{
				$nombre_campo="";
				if(isset($resultados_nombre_campo[intval($key)])==true)
				{
					$nombre_campo=$resultados_nombre_campo[intval($key)]['nombre_campo'];
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

			//crea zip unico de los archivos de soporte administrativo no actividad
			$arrayZipsArchivosSoporteAdmnistrativoNoActividad=array();
			$ruta_zip_archivos_soporte_administrativo="";
			$ruta_zip_archivos_soporte_administrativo=$this->globalRutaTemporalesEscribirArchivos."archivosSoporteAdministrativo.zip";
			foreach ($this->GlobalArraySoporteAdministrativoNoActividad as $key => $rutaSANA) 
			{
				//echo $rutaSANA."<br>";
				$arrayZipsArchivosSoporteAdmnistrativoNoActividad[]=$rutaSANA;
				
			}//fin foreach
			$resZipActualSANA=create_zip($arrayZipsArchivosSoporteAdmnistrativoNoActividad,$ruta_zip_archivos_soporte_administrativo);			
			//fin crea zip unico de los archivos de soporte administrativo no actividad
		    
		    $rutaZipFiltradosEapbCampo120="";
			if($this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120")
		    {		  
		    	
    			$arrayArchivosEapbTempActual=array();  	
		    	foreach ($this->global_array_rutas_agrupados_ips_para_cada_eapb as $key_reapb => $ruta_actual_eapb_fe) 
		    	{		    		
		    		if(file_exists($ruta_actual_eapb_fe)==true)
		    		{
						$arrayArchivosEapbTempActual[]=$ruta_actual_eapb_fe;
						$rutaZipFiltradosEapbCampo120=$this->globalRutaTemporalesEscribirArchivos."archivosFiltradosPorEAPB.zip";
		    		}//fin if verifica si existe antes de agregarlo al zip de las eapb
		    	}//fin foreach
		    	
		    	$resultado_zip_FEAPB=create_zip($arrayArchivosEapbTempActual,$rutaZipFiltradosEapbCampo120);				
		    }//fin if en caso de ser agrupado ips agrega la siguiente lista de archivos al arreglo de archivos a comprimir
		    
		    
		    //GENERANDO ARCHIVO ZIP
		    $archivos_a_comprimir=array();
		    $archivos_a_comprimir[]=$ruta_inconsistencias_campos;
		    $archivos_a_comprimir[]=$ruta_archivo_registros_buenos;
		    $archivos_a_comprimir[]=$ruta_archivo_registros_malos;
		    $archivos_a_comprimir[]=$ruta_inconsistencias_archivo_registros_malos;
		    $archivos_a_comprimir[]=$this->global_ruta_afiliados_no_registrados;
		    //$archivos_a_comprimir[]=$this->global_ruta_registros_con_afiliados_modificados;
		    $archivos_a_comprimir[]=$this->global_ruta_registros_con_afiliados_modificados_sexo_diferentes;
		    $archivos_a_comprimir[]=$this->global_ruta_registros_con_afiliados_modificados_fecha_nacimiento_diferentes;
		    $archivos_a_comprimir[]=$this->global_ruta_archivo_plano_nuevo_original_con_sexo_fecha_nacimiento_corregidos;
		    $archivos_a_comprimir[]=$this->global_ruta_registros_con_afiliados_modificados_y_campos_estadisticas_adicionales;
		    $archivos_a_comprimir[]=$this->global_ruta_estructura_mala;

		    if($ruta_zip_archivos_soporte_administrativo!="")
		    {
		    	$archivos_a_comprimir[]=$ruta_zip_archivos_soporte_administrativo;
		    }//fin if

		    if($rutaZipFiltradosEapbCampo120!="")
		    {
		    	$archivos_a_comprimir[]=$rutaZipFiltradosEapbCampo120;
		    }//fin if

		    if($archivo_reporte_por_ips_exitoso==true)
		    {
		    	$archivos_a_comprimir[]=$this->global_ruta_reporte_registros_por_ips;
		    }//fin if

		    $archivos_a_comprimir[]=$this->global_ruta_reporte_calificacion_campos;

		    

		    //PARTE VERIFICA TAMANO ARCHIVOS DENTRO DEL ZIP (EVITAR QUE PASEN DE 4 GB)
		    $tamano_acumulado_files_bytes=0;
		    foreach ($archivos_a_comprimir as $key => $ruta_archivo_actual_para_zip)
		    {
		    	$tamano_acumulado_files_bytes+=$this->realFileSize($ruta_archivo_actual_para_zip);
		    }//fin foreach
		    $sobrepasa_limite_del_zip=false;
		    if($tamano_acumulado_files_bytes>4294967296)
		    {
		    	$sobrepasa_limite_del_zip=true;
		    	echo "<span style='position:relative;left:40%;text-align:center;'>NO SE PUEDE COMPRIMIR, EL TAMANO DE LOS ARCHIVOS INTERNOS ( $tamano_acumulado_files_bytes ) SOBREPASA EL TAMANO LIMITE DEL FORMATO ZIP DE 4294967296 BYTES </span><br>";
		    }//fin if 
		    echo "<span style='position:relative;left:40%;color:white;text-align:center;'>tamano_acumulado_files_bytes $tamano_acumulado_files_bytes</span><br>";
		    //FIN PARTE VERIFICA TAMANO ARCHIVOS DENTRO DEL ZIP (EVITAR QUE PASEN DE 4 GB)

		    $ruta_zip=$ruta_temporales."inconsistencias_".$nombre_archivo_sin_extension."_".$secuencia_dependiendo_existencia."_".$fecha_para_archivo.'.zip';
		    if($sobrepasa_limite_del_zip==false)
		    {
		    $result_zip = create_zip($archivos_a_comprimir,$ruta_zip);
			}//fin if
		    if(connection_aborted()==false)
		    {
				echo "<script>var ruta_zip= '$ruta_zip'; </script>";
				ob_flush();
				flush();
		    }//fin if
		    
		    //FIN GENERANDO ARCHIVO ZIP
		    
		    
		    $ruta_zip_filtrado="";
		    //GENERANDO ARCHIVO ZIP FILTRADO
		    if($this->tipo_entidad_que_efectua_el_cargue=="ent_territoriales")
		    {
			$archivos_a_comprimir_filtrado=array();
			$archivos_a_comprimir_filtrado[0]=$ruta_archivo_filtrado;
			$ruta_zip_filtrado=$ruta_carpeta_filtrado."/filtrado_".$nombre_archivo_sin_extension."_".$secuencia_dependiendo_existencia."_".$fecha_para_archivo.'.zip';
			$result_zip_filtrado = create_zip($archivos_a_comprimir_filtrado,$ruta_zip_filtrado);
			if(connection_aborted()==false)
			{
			    echo "<script>var ruta_zip_filtrado= '$ruta_zip_filtrado'; </script>";
			    ob_flush();
			    flush();
			}
		    }
		    //FIN GENERANDO ARCHIVO ZIP FILTRADO
		    
		    //PARTE BORRA LA TABLA DE INDEXADOR QUE ES SOLO PARA LA VALIDACION
		    $tipo_id_provisional="no_idea_ti";
		    $identificacion_provisional="no_idea_id";
		    $cod_prestador_de_acuerdo_tipo_entidad="";
		    $cod_eapb_de_acuerdo_tipo_entidad="";
		    if($this->tipo_entidad_que_efectua_el_cargue=="individual_ips"
		    	|| $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips"
		       		|| $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120"
		       || $this->tipo_entidad_que_efectua_el_cargue=="ent_territoriales")
		    {
			$cod_prestador_de_acuerdo_tipo_entidad=$this->cod_registro_especial_pss;
			
			if($this->tipo_entidad_que_efectua_el_cargue=="individual_ips"
				|| $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips"
		       		|| $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120"
				)
			{
			    $cod_eapb_de_acuerdo_tipo_entidad=$this->cod_eapb_global;
			}
			else if($this->tipo_entidad_que_efectua_el_cargue=="ent_territoriales")
			{
			    $cod_eapb_de_acuerdo_tipo_entidad=$this->cod_registro_especial_pss;
			}
		    }
		    else if($this->tipo_entidad_que_efectua_el_cargue=="agrupado_eapb")
		    {
			$cod_prestador_de_acuerdo_tipo_entidad="AGRUP_EAPB";
			$cod_eapb_de_acuerdo_tipo_entidad=$this->cod_eapb_global;
		    }

		    $nombre_tabla_indexador_duplicados="gioss_indexador_duplicados_del_validador_4505";
			if($this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120")
			{
				//susa una tabla de indexador distinta pero para duplicados teniendo en cuenta el campo extra 120 con codigo eapb
				$nombre_tabla_indexador_duplicados="gioss_indexador_dupl_del_validador_4505_agrup_ips";
			}//fin if

		    $query_delete_en_indexador="";
		    $query_delete_en_indexador.=" DELETE FROM ";
		    $query_delete_en_indexador.=" $nombre_tabla_indexador_duplicados ";
		    $query_delete_en_indexador.=" WHERE  ";
		    $query_delete_en_indexador.="tipo_id_usuario='".$tipo_id_provisional."'";				
		    $query_delete_en_indexador.=" AND ";
		    $query_delete_en_indexador.="id_usuario='".$identificacion_provisional."'";
		    $query_delete_en_indexador.=" AND ";
		    $query_delete_en_indexador.="nick_usuario='".$this->nick_user."'";
		    $query_delete_en_indexador.=" AND ";
		    $query_delete_en_indexador.="fecha_corte_reporte='".$this->cadena_fecha_corte."'";
		    $query_delete_en_indexador.=" AND ";
		    $query_delete_en_indexador.="fecha_de_generacion='".$this->fecha_actual_global."'";
		    $query_delete_en_indexador.=" AND ";
		    $query_delete_en_indexador.="hora_generacion='".$this->tiempo_actual_global."'";
		    $query_delete_en_indexador.=" AND ";
		    $query_delete_en_indexador.="codigo_entidad_eapb_generadora='".$cod_eapb_de_acuerdo_tipo_entidad."'";
		    $query_delete_en_indexador.=" AND ";
		    $query_delete_en_indexador.="codigo_entidad_prestadora='".$cod_prestador_de_acuerdo_tipo_entidad."'";
		    $query_delete_en_indexador.=" AND ";
		    $query_delete_en_indexador.="nombre_archivo_pyp='".$this->nombre_archivo_4505."'";
		    $query_delete_en_indexador.=" ; ";
		    $error_bd_seq="";		
		    $bool_hubo_error_query=$obj->insertar_no_warning_get_error_no_crea_cierra($query_delete_en_indexador, $error_bd_seq);
		    //FIN PARTE BORRA LA TABLA DE INDEXADOR QUE ES SOLO PARA LA VALIDACION
		    
		    //YA NO ESTA EN USO EL ARCHIVO
		    
		    if($this->tipo_entidad_que_efectua_el_cargue=="individual_ips"
		    	|| $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips"
		       		|| $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120"
		       || $this->tipo_entidad_que_efectua_el_cargue=="ent_territoriales"
		       )
		    {
		    
			$query_update_esta_siendo_procesado="";
			$query_update_esta_siendo_procesado.=" UPDATE gioss_4505_esta_validando_actualmente ";
			$query_update_esta_siendo_procesado.=" SET esta_ejecutando='NO',";
			$query_update_esta_siendo_procesado.=" ruta_archivo_descarga='$ruta_zip' ";
			if($ruta_zip_filtrado!="")
			{
			    //lleva la coma aca por si no esta vacio
			    $query_update_esta_siendo_procesado.=" , ruta_archivo_descarga_filtrado='$ruta_zip_filtrado' ";
			}
			$query_update_esta_siendo_procesado.=" WHERE fecha_remision='".$this->cadena_fecha_corte."' ";
			$query_update_esta_siendo_procesado.=" AND codigo_entidad_reportadora='".$this->cod_registro_especial_pss."' ";
			$query_update_esta_siendo_procesado.=" AND nombre_archivo='".$nombre_archivo4505."'  ";
			$query_update_esta_siendo_procesado.=" AND nick_usuario='".$this->nick_user."'  ";
			$query_update_esta_siendo_procesado.=" AND fecha_validacion='".$fecha_actual."'  ";
			$query_update_esta_siendo_procesado.=" AND hora_validacion='".$tiempo_actual."'  ";
			$query_update_esta_siendo_procesado.=" ; ";
			$error_bd="";
			$bool_hubo_error_query=$obj->insertar_no_warning_get_error_no_crea_cierra($query_update_esta_siendo_procesado, $error_bd);
			if($error_bd!="")
			{
				if(connection_aborted()==false)
				{
					echo "<script>alert('error al actualizar el estado actual de validacion en tiempo real  4505 ');</script>";
				}
			}
		    }//fin if
		    else if($this->tipo_entidad_que_efectua_el_cargue=="agrupado_eapb")
		    {
		    
			$query_update_esta_siendo_procesado="";
			$query_update_esta_siendo_procesado.=" UPDATE gioss_4505_esta_validando_actualmente ";
			$query_update_esta_siendo_procesado.=" SET esta_ejecutando='NO',";
			$query_update_esta_siendo_procesado.=" ruta_archivo_descarga='$ruta_zip' ";
			if($ruta_zip_filtrado!="")
			{
			    //lleva la coma aca por si no esta vacio
			    $query_update_esta_siendo_procesado.=" , ruta_archivo_descarga_filtrado='$ruta_zip_filtrado' ";
			}
			$query_update_esta_siendo_procesado.=" WHERE fecha_remision='".$this->cadena_fecha_corte."' ";
			$query_update_esta_siendo_procesado.=" AND codigo_entidad_reportadora='".$this->cod_eapb_global."' ";
			$query_update_esta_siendo_procesado.=" AND nombre_archivo='".$nombre_archivo4505."'  ";
			$query_update_esta_siendo_procesado.=" AND nick_usuario='".$this->nick_user."'  ";
			$query_update_esta_siendo_procesado.=" AND fecha_validacion='".$fecha_actual."'  ";
			$query_update_esta_siendo_procesado.=" AND hora_validacion='".$tiempo_actual."'  ";
			$query_update_esta_siendo_procesado.=" ; ";
			$error_bd="";
			$bool_hubo_error_query=$obj->insertar_no_warning_get_error_no_crea_cierra($query_update_esta_siendo_procesado, $error_bd);
			if($error_bd!="")
			{
				if(connection_aborted()==false)
				{
					echo "<script>alert('error al actualizar el estado actual de validacion en tiempo real  4505 ');</script>";
				}
			}
		    }//fin if
		    //FIN YA NO ESTA EN USO EL ARCHIVO
		    
		    $hubo_errores_al_finalizar_la_validacion=false;
		    //parte bandera terminado
		    if ($hubo_errores_en_los_campos_del_archivo==false) 
		    {
			    $hubo_errores_al_finalizar_la_validacion=false;
		    }//fin if no hubo errores de escritura en la base de datos ni en los campos
			    
		    if($hubo_errores_en_los_campos_del_archivo==true)
		    {
			    $hubo_errores_al_finalizar_la_validacion=true;
		    }//fin si hubo errores
		    
		    if ($hubo_errores_al_finalizar_la_validacion) 
		    {
			    $mensaje ="";
			    $mensaje .= "El archivo se ha validado. Recuerde su secuencia de validacion es: " . $secuencia_dependiendo_existencia . "<br>";
			    $mensaje .= "El reporte de las inconsistencias encontradas ha sido enviado al correo electronico " . $_SESSION['correo'] . "<br>";			

			    if($sobrepasa_limite_del_zip==false)
			    {	
			    $mensaje .= "<input type=\'button\' value=\'Haga clic aqui para descargar las inconsistencias encontradas\' class=\'btn btn-success color_boton\' onclick=\'download_inconsistencias_campos(ruta_zip);\'/>";
				}//fin if
				else
				{
					//PARTE ESCRIBE BOTONES DESCARAG ARCHIVOS INDIVIDUALES SI FALLA AL CREAR EL ZIP
					$html_script_archivos_para_zip_individual="";
					foreach ($archivos_a_comprimir as $key => $ruta_archivo_actual_para_zip)
		    		{
		    			$array_ruta_archivo_actual=explode("/", $ruta_archivo_actual_para_zip);
		    			$string_nombre_archivo_actual_para_zip=$array_ruta_archivo_actual[count($array_ruta_archivo_actual)-1];
		    			if(connection_aborted()==false)
						{
						    $html_script_archivos_para_zip_individual.="<script>var archivo".$key."= '$ruta_archivo_actual_para_zip'; </script>";
						    
						}//fin if
		    			
		    		}//fin foreach
		    		echo $html_script_archivos_para_zip_individual;
		    		ob_flush();
				    flush();

				    foreach ($archivos_a_comprimir as $key => $ruta_archivo_actual_para_zip)
		    		{
		    			$array_ruta_archivo_actual=explode("/", $ruta_archivo_actual_para_zip);
		    			$string_nombre_archivo_actual_para_zip=$array_ruta_archivo_actual[count($array_ruta_archivo_actual)-1];
		    					    			
		    			$mensaje .= "<input type=\'button\' value=\'Haga clic aqui para descargar $string_nombre_archivo_actual_para_zip\' class=\'btn btn-success color_boton\' onclick=\'download_inconsistencias_campos(archivo".$key.");\'/><br>";
		    		}//fin foreach
		    		//FIN PARTE ESCRIBE BOTONES DESCARAG ARCHIVOS INDIVIDUALES SI FALLA AL CREAR EL ZIP
				}//fin else
			    
			    if($this->tipo_entidad_que_efectua_el_cargue=="ent_territoriales")
			    {
				$mensaje .= "<input type=\'button\' value=\'Haga clic aqui para descargar el archivo 4505 filtrado por localizacion\' class=\'btn btn-success color_boton\' onclick=\'download_inconsistencias_campos(ruta_zip_filtrado);\'/>";
			    }
			    
			    if(connection_aborted()==false)
			    {
				echo "<script>document.getElementById('div_mensaje_error').style.display='inline';</script>";
				echo "<script>document.getElementById('parrafo_error').innerHTML='$mensaje';</script>";
				
				ob_flush();
				flush();
			    }
			    
		    }//fin if hubo errores obligatorios
		    else
		    {
			    $mensajeExito ="";
			    $mensajeExito .= "El archivo se ha validado. Recuerde su secuencia de validacion es: " . $secuencia_dependiendo_existencia . "<br>";
			    $mensajeExito .= "El reporte de las inconsistencias encontradas ha sido enviado al correo electronico " . $_SESSION['correo'] . "<br>";

			    if($sobrepasa_limite_del_zip==false)
			    {	
			    $mensajeExito .= "<input type=\'button\' value=\'Haga clic aqui para descargar las inconsistencias encontradas\' class=\'btn btn-success color_boton\' onclick=\'download_inconsistencias_campos(ruta_zip);\'/>";
			    }//fin if
				else
				{
					//PARTE ESCRIBE BOTONES DESCARAG ARCHIVOS INDIVIDUALES SI FALLA AL CREAR EL ZIP
					$html_script_archivos_para_zip_individual="";
					foreach ($archivos_a_comprimir as $key => $ruta_archivo_actual_para_zip)
		    		{
		    			$array_ruta_archivo_actual=explode("/", $ruta_archivo_actual_para_zip);
		    			$string_nombre_archivo_actual_para_zip=$array_ruta_archivo_actual[count($array_ruta_archivo_actual)-1];
		    			if(connection_aborted()==false)
						{
						    $html_script_archivos_para_zip_individual.="<script>var archivo".$key."= '$ruta_archivo_actual_para_zip'; </script>";
						    
						}//fin if
		    			
		    		}//fin foreach
		    		echo $html_script_archivos_para_zip_individual;
		    		ob_flush();
				    flush();

				    foreach ($archivos_a_comprimir as $key => $ruta_archivo_actual_para_zip)
		    		{
		    			$array_ruta_archivo_actual=explode("/", $ruta_archivo_actual_para_zip);
		    			$string_nombre_archivo_actual_para_zip=$array_ruta_archivo_actual[count($array_ruta_archivo_actual)-1];
		    					    			
		    			$mensaje .= "<input type=\'button\' value=\'Haga clic aqui para descargar $string_nombre_archivo_actual_para_zip\' class=\'btn btn-success color_boton\' onclick=\'download_inconsistencias_campos(archivo".$key.");\'/><br>";
		    		}//fin foreach
		    		//FIN PARTE ESCRIBE BOTONES DESCARAG ARCHIVOS INDIVIDUALES SI FALLA AL CREAR EL ZIP
				}//fin else
			    
			    if($this->tipo_entidad_que_efectua_el_cargue=="ent_territoriales")
			    {
				$mensaje .= "<input type=\'button\' value=\'Haga clic aqui para descargar el archivo 4505 filtrado por localizacion\' class=\'btn btn-success color_boton\' onclick=\'download_inconsistencias_campos(ruta_zip_filtrado);\'/>";
			    }
			    
			    if(connection_aborted()==false)
			    {
				echo "<script>document.getElementById('div_mensaje_exito').style.display='inline';</script>";
				echo "<script>document.getElementById('parrafo_exito').innerHTML='$mensajeExito';</script>";
				
				ob_flush();
				flush();
			    }
			    
		    }//fin else  exito, no hubo errores obligatorios
		    
		    
		    $this->ruta_archivos_inconsistencias_para_email=$ruta_zip;
		    $this->ruta_archivo_filtrado_para_email=$ruta_zip_filtrado;
		    
		    $obj->cerrar_conexion();
		    
		    //PARTE ABRE VENTANA RIESGO POBLACION
		    $cod_prestador_de_acuerdo_tipo_entidad="";
		    $cod_eapb_de_acuerdo_tipo_entidad="";
		    
		    if($this->tipo_entidad_que_efectua_el_cargue=="individual_ips"
		    	|| $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips"
		       		|| $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120"
		       || $this->tipo_entidad_que_efectua_el_cargue=="ent_territoriales")
		    {
			$cod_prestador_de_acuerdo_tipo_entidad=$this->cod_registro_especial_pss;
			
				if($this->tipo_entidad_que_efectua_el_cargue=="individual_ips"
					|| $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips"
		       		|| $this->tipo_entidad_que_efectua_el_cargue=="agrupado_ips120"
					)
			{
			    $cod_eapb_de_acuerdo_tipo_entidad=$this->cod_eapb_global;
			}
			else if($this->tipo_entidad_que_efectua_el_cargue=="ent_territoriales")
			{
			    $cod_eapb_de_acuerdo_tipo_entidad=$this->cod_registro_especial_pss;
			}
		    }
		    else if($this->tipo_entidad_que_efectua_el_cargue=="agrupado_eapb")
		    {
			$cod_prestador_de_acuerdo_tipo_entidad="AGRUP_EAPB";
			$cod_eapb_de_acuerdo_tipo_entidad=$this->cod_eapb_global;
		    }
		    
		    $llamada_a_ventana_subir_riesgo_poblacion="";
		    $llamada_a_ventana_subir_riesgo_poblacion.="<script>mostrar_ventana_tablas_riesgo_poblacion(";
		    $llamada_a_ventana_subir_riesgo_poblacion.="'".$cod_prestador_de_acuerdo_tipo_entidad."',";
		    $llamada_a_ventana_subir_riesgo_poblacion.="'".$cod_eapb_de_acuerdo_tipo_entidad."',";
		    $llamada_a_ventana_subir_riesgo_poblacion.="'".$this->nit_prestador."',";
		    $llamada_a_ventana_subir_riesgo_poblacion.="'".$secuencia_dependiendo_existencia."',";
		    $llamada_a_ventana_subir_riesgo_poblacion.="'".$this->cadena_fecha_corte."',";
		    $llamada_a_ventana_subir_riesgo_poblacion.="'".$this->tipo_de_identificacion_entidad_reportadora."',";
		    $llamada_a_ventana_subir_riesgo_poblacion.="'".$this->tipo_de_regimen_de_la_informacion_reportada."',";
		    $llamada_a_ventana_subir_riesgo_poblacion.="'".$fecha_actual."',";
		    $llamada_a_ventana_subir_riesgo_poblacion.="'".$tiempo_actual."',";
		    $llamada_a_ventana_subir_riesgo_poblacion.="'".$nombre_archivo4505."'";
		    $llamada_a_ventana_subir_riesgo_poblacion.="";
		    $llamada_a_ventana_subir_riesgo_poblacion.=");</script>";
		    //echo $llamada_a_ventana_subir_riesgo_poblacion;
		    //FIN PARTE ABRE VENTANA RIESGO POBLACION
		    
		    //parte retorno de la funcion 
		    if ($flag != 1 && $hubo_errores_en_los_campos_del_archivo==false) 
		    {
			    
			    return false;
		    }//fin if no hubo errores de escritura en la base de datos ni en los campos
			    
		    if($hubo_errores_en_los_campos_del_archivo==true)
		    {
			    
			    
			    return true;
		    }//fin si hubo errores
		    
		}//fin if verificacion
		//FIN SI LA VERIFICACION DEL LA LINEA INICIAL FUE CORRECTA
		
    }//fin funcion ValidarArchivo4505

    /**
	* Return file size (even for file > 2 Gb)
	* For file size over PHP_INT_MAX (2 147 483 647), PHP filesize function loops from -PHP_INT_MAX to PHP_INT_MAX.
	*
	* @param string $path Path of the file
	* @return mixed File size or false if error
	*/
	function realFileSize($path)
	{
	    if (!file_exists($path))
	        return false;

	    $size = filesize($path);
	    
	    if (!($file = fopen($path, 'rb')))
	        return false;
	    
	    if ($size >= 0)
	    {//Check if it really is a small file (< 2 GB)
	        if (fseek($file, 0, SEEK_END) === 0)
	        {//It really is a small file
	            fclose($file);
	            return $size;
	        }//fin if
	    }//fin if
	    
	    //Quickly jump the first 2 GB with fseek. After that fseek is not working on 32 bit php (it uses int internally)
	    $size = PHP_INT_MAX - 1;
	    if (fseek($file, PHP_INT_MAX - 1) !== 0)
	    {
	        fclose($file);
	        return false;
	    }//fin if
	    
	    $length = 1024 * 1024;
	    while (!feof($file))
	    {//Read the file until end
	        $read = fread($file, $length);
	        $size = bcadd($size, $length);
	    }//fin while
	    $size = bcsub($size, $length);
	    $size = bcadd($size, strlen($read));
	    
	    fclose($file);
	    return $size;
	}//fin function

}//fin clase

?>
