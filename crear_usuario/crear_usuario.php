<?php
set_time_limit(936000);
ini_set('max_execution_time', 936000);
ini_set('memory_limit', '900M');

require '../librerias_externas/Smarty-3.1.17/libs/Smarty.class.php';
include_once ('../utiles/clase_coneccion_bd.php');
include_once ('../utiles/funciones_crear_menu.php');

include ("../librerias_externas/PHPMailer/class.phpmailer.php");
include ("../librerias_externas/PHPMailer/class.smtp.php");

require_once '../utiles/configuracion_global_email.php';

$smarty = new Smarty;
$coneccionBD = new conexion();

session_start();

if (!isset($_SESSION['usuario']) && !isset($_SESSION['tipo_perfil']))
{
	
	header ("Location: ../index.php");
}

$menu= $_SESSION['menu_logueo_html'];
$nombre= $_SESSION['nombre_completo'];
$perfil_usuario_actual= $_SESSION['tipo_perfil'];
$entidad_salud_usuario_actual= $_SESSION['entidad_asociada_al_nick'];

session_write_close();

$se_creo_usuario=false;

//SELECTOR TIPO ID
$selector_tipo_id="";

$sql_consulta_tipo_id="SELECT * FROM gios_tipo_identificacion_usuarios";
$resultado_query_tipo_id=$coneccionBD->consultar2($sql_consulta_tipo_id);

$selector_tipo_id.="<select id='tipo_identificacion' name='tipo_identificacion' class='campo_azul' onchange='traer_info_persona();'>";
$selector_tipo_id.="<option value='none'>Seleccione un tipo de identificaci&oacuten</option>";
foreach($resultado_query_tipo_id as $tipo_id)
{
	$selector_tipo_id.="<option value='".$tipo_id['abreviacion_tipo_identificacion']."'>".$tipo_id['descripcion_tipo_id']."</option>";
}
$selector_tipo_id.="</select>";
//FIN

//SELECTOR PERFIL
$selector_perfil="";

$sql_consulta_perfil="SELECT * FROM gios_perfiles_sistema ps INNER JOIN perfiles_asociados_perfiles pp on( pp.id_perfil_1='$perfil_usuario_actual' AND ps.id_perfil=pp.id_perfil_2);";
$resultado_query_perfil=$coneccionBD->consultar2($sql_consulta_perfil);

//$selector_perfil.="<select id='perfil' name='perfil' class='campo_azul'>";
//$selector_perfil.="<option value='none'>Seleccione un tipo de perfil</option>";
foreach($resultado_query_perfil as $perfil)
{
	if(intval($perfil['id_perfil'])!=6)
	{
		$selector_perfil.="<option value='".$perfil['id_perfil']."'>".$perfil['nombre_perfil']."</option>";
	}
}
//$selector_perfil.="</select>";
//FIN

date_default_timezone_set ("America/Bogota");
$fecha_actual = date('Y-m-d');


$div_error="<div id='msg_error'></div>";
//imprime lo prncipal de la pagina

$smarty->assign("fecha_creado",$fecha_actual,true);
$smarty->assign("selector_perfil",$selector_perfil,true);
$smarty->assign("selector_tipo_id",$selector_tipo_id,true);
$smarty->assign("nombre", $nombre, true);
$smarty->assign("menu", $menu, true);
$smarty->assign("error", $div_error, true);

//muestra la pagina va al final siempre
$smarty->display('crear_usuario.html.tpl');


$fecha_futura = date('Y-m-d', strtotime('+1 year'));
$fecha_futura_array= explode("-",$fecha_futura);
$fecha_futura_fix=$fecha_futura_array[1]."/".$fecha_futura_array[2]."/".$fecha_futura_array[0];

echo "<script>document.getElementById('fecha_vence').value='".$fecha_futura_fix."';</script>";
echo "<script>document.getElementById('cod_entidad_salud_0').value='".$entidad_salud_usuario_actual."';</script>";

//echo "<script>alert('$HOST_CONF_EMAIL');</script>";

if(isset($_POST['identificacion']))
{
	
	
	$tipo_id_reg=$_POST['tipo_identificacion'];
	$id_reg=$_POST['identificacion'];
	$primer_nombre=$_POST['primer_nombre'];
	$segundo_nombre=$_POST['segundo_nombre'];
	$primer_apellido=$_POST['primer_apellido'];
	$segundo_apellido=$_POST['segundo_apellido'];
	$password=$_POST['password_user'];
	$confirmar_password=$_POST['confirmar_password'];
	$email=$_POST['email'];
	$direccion=$_POST['direccion'];
	$telefono=$_POST['telefono'];
	$celular=$_POST['celular'];
	$fecha_cumple=$_POST['fecha_cumple'];
	$fecha_vence=$_POST['fecha_vence'];
	$fecha_creacion=$_POST['fecha_creacion'];
	$nick_logueo=$_POST['nick_logueo'];
	
	$realizo_rollback=false;
	
	
	
	
	//parte verifica si existe el usuario
	$sql_consulta_existe_persona="SELECT * FROM gios_usuarios_sistema WHERE tipo_identificacion_usuario='$tipo_id_reg' AND identificacion_usuario='$id_reg';";
	$resultado_query_existe_persona=$coneccionBD->consultar2($sql_consulta_existe_persona);
	
	$ya_existe_persona=count($resultado_query_existe_persona)>0;
	
	//fin parte verifica existe el usuario
	
	if($fecha_cumple!="")
	{
	$fecha_cumple=explode("/",$fecha_cumple)[2]."-".explode("/",$fecha_cumple)[0]."-".explode("/",$fecha_cumple)[1];
	}
	if($fecha_vence!="")
	{
	$fecha_vence=explode("/",$fecha_vence)[2]."-".explode("/",$fecha_vence)[0]."-".explode("/",$fecha_vence)[1];
	}
	
	$mensaje_error="";
	
	if($password != $confirmar_password)
	{
		$mensaje_error.="El password no es igual al password confirmado.<br>";
	}
	
	$sql_consulta_nicklogueo="SELECT * FROM gioss_entidad_nicklogueo_perfil_estado_persona WHERE nicklogueo='$nick_logueo';";
	$resultado_query_nicklogueo=$coneccionBD->consultar2($sql_consulta_nicklogueo);
	
	$existe_nick=count($resultado_query_nicklogueo)>0;
	
	if($existe_nick)
	{
		$mensaje_error.="Ese nick de usuario ya existe.<br>";
	}
	
	//mira si entidad esta asociada previamente
	$sql_consulta_entidad="SELECT * FROM gioss_entidad_nicklogueo_perfil_estado_persona WHERE entidad='".$_POST["cod_entidad_salud_0"]."' AND  tipo_id='$tipo_id_reg' AND identificacion_usuario='$id_reg';";
	$resultado_query_entidad=$coneccionBD->consultar2($sql_consulta_entidad);
	
	$existe_entidad_asociada_usuario=count($resultado_query_entidad)>0;
	
	if($existe_entidad_asociada_usuario)
	{
		$mensaje_error.="La entidad ya estaba asociada a la persona con otro usuario, ingrese una entidad distinta.<br>";
	}
	//fin mira si entidad esta asociada previamente
	
	// verifica la entidad y si el perfil del usuario actual puede asociarla
	$sql_consulta_entidad_perfiles_validos="SELECT * FROM gioss_entidades_sector_salud  WHERE codigo_entidad='".$_POST["cod_entidad_salud_0"]."' ;";
	$resultado_query_entidad_perfiles_validos=$coneccionBD->consultar2($sql_consulta_entidad_perfiles_validos);
	
	$existe_entidad_salud_sistema=count($resultado_query_entidad_perfiles_validos)>0;
	if($existe_entidad_salud_sistema)
	{
		$tipo_entidad=$resultado_query_entidad_perfiles_validos[0]["cod_tipo_entidad"];
		$sql_consulta_tipo_entidad_perfiles="SELECT * FROM gioss_tipo_entidades_perfiles  WHERE cod_tipo_entidad='".$tipo_entidad."' ;";
		$resultado_query_tipo_entidad_perfiles_validos=$coneccionBD->consultar2($sql_consulta_tipo_entidad_perfiles);
		if(count($resultado_query_tipo_entidad_perfiles_validos)>0)
		{
			$perfil_entidad=$resultado_query_tipo_entidad_perfiles_validos[0]["cod_perfil"];
			$sql_consulta_perfil_permitido="SELECT * FROM perfiles_asociados_perfiles  WHERE id_perfil_2='".$perfil_entidad."' AND id_perfil_1='".$_POST["perfil_0"]."' ;";
			$resultado_query_perfil_permitido=$coneccionBD->consultar2($sql_consulta_perfil_permitido);
			
			if(count($resultado_query_perfil_permitido)>0)
			{}
			else
			{
				//$mensaje_error.="La entidad no se puede asociar con ese perfil debido a que el perfil de la entidad es $perfil_entidad y el perfil seleccionado es ".$_POST["perfil_0"].".<br>";
			}
		}
	}
	else
	{
		$mensaje_error.="La entidad no existe en el sistema.<br>";
	}
	//fin verifica la entidad y si el perfil del usuario actual puede asociarla
	
	if($perfil_usuario_actual!="5")
	{
		if($perfil_usuario_actual=="2")
		{
			if($entidad_salud_usuario_actual!=$_POST["cod_entidad_salud_0"])
			{
				$mensaje_error.="La entidad de salud(prestadora) es diferente de la entidad a la que pertenece el usuario.<br>";
			}//fin if si la entidad es diferente de la entidad del usuario que registra
		}//fin if si el perfil del usuario actual no del que se creara es administrador prestador
		if($perfil_usuario_actual=="4")
		{
			if($entidad_salud_usuario_actual!=$_POST["cod_entidad_salud_0"])
			{
				//pasa a verificar si la EAPB tiene entidades asociadas entre las cuales pertenesca la entidad que se asocia
				$sql_consulta_prestadores_asociados_eapb="";
				$sql_consulta_prestadores_asociados_eapb.="SELECT ea.codigo_entidad,ea.nombre_de_la_entidad FROM ";
				$sql_consulta_prestadores_asociados_eapb.=" gioss_relacion_entre_entidades_salud re INNER JOIN gioss_entidades_sector_salud ea ON (re.entidad1 = ea.codigo_entidad) ";
				$sql_consulta_prestadores_asociados_eapb.=" WHERE re.entidad2='".$entidad_salud_usuario_actual."' ";
				$error_bd="";
				$resultado_query_prestadores_asociados_eapb=$coneccionBD->consultar_no_warning_get_error($sql_consulta_prestadores_asociados_eapb,$error_bd);
				if($error_bd!="")
				{
					$mensaje_error.="ERROR AL CONSULTAR LAS ENTIDADES ASOCIADAS A LA ENTIDAD. <br>";
				}
				
				if(count($resultado_query_prestadores_asociados_eapb)>0 && is_array($resultado_query_prestadores_asociados_eapb))
				{
					$existe_asociacion_con_prestador=false;
					foreach($resultado_query_prestadores_asociados_eapb as $prestador_asociado)
					{
						if($prestador_asociado["codigo_entidad"]==$_POST["cod_entidad_salud_0"])
						{
							$existe_asociacion_con_prestador=true;
						}
					}
					
					if($existe_asociacion_con_prestador==false)
					{
						$mensaje_error.="La entidad no esta asociada a la entidad a la cual pertenece el usuario. <br>";
					}
				}//fin if resultado
			}//fin if si la entidad es diferente de la entidad del usuario que registra
		}//fin if si el perfil del usuario actual no del que se creara es administrador EAPB
	}//fin if si el perfil del usuario actual no del que se creara es diferente de admon. sistema
	
	$query_registrar_usuario="";
	$query_registrar_usuario.="BEGIN;";
	
	if($ya_existe_persona==false)
	{
		$query_registrar_usuario.="INSERT INTO gios_usuarios_sistema";
		$query_registrar_usuario.="(";
		$query_registrar_usuario.="tipo_identificacion_usuario,identificacion_usuario,primer_nombre_usuario,";
		$query_registrar_usuario.="primer_apellido_usuario,segundo_nombre_usuario,segundo_apellido_usuario,";
		$query_registrar_usuario.="direccion_usuario,telefono_fijo,telefono_celular,";	
		$query_registrar_usuario.="fecha_nacimiento ";	
		$query_registrar_usuario.=")";
		$query_registrar_usuario.=" VALUES";
		$query_registrar_usuario.="(";
		$query_registrar_usuario.="'".$tipo_id_reg."',";
		$query_registrar_usuario.="'".$id_reg."',";
		$query_registrar_usuario.="'".$primer_nombre."',";
		$query_registrar_usuario.="'".$primer_apellido."',";
		$query_registrar_usuario.="'".$segundo_nombre."',";
		$query_registrar_usuario.="'".$segundo_apellido."',";		
		$query_registrar_usuario.="'".$direccion."',";
		$query_registrar_usuario.="'".$telefono."',";
		$query_registrar_usuario.="'".$celular."',";
		$query_registrar_usuario.="'".$fecha_cumple."' ";
		
		$query_registrar_usuario.=");";
	}
	$cont_entidades=0;
	while(isset($_POST["cod_entidad_salud_".$cont_entidades]))
	{
		$query_registrar_usuario.="INSERT INTO gioss_entidad_nicklogueo_perfil_estado_persona ";
		$query_registrar_usuario.="(";
		$query_registrar_usuario.="entidad,nicklogueo,";
		$query_registrar_usuario.="tipo_id,identificacion_usuario,";
		$query_registrar_usuario.="perfil_asociado,estado_nicklogueo,correo_usuario,password,fecha_inicio,fecha_expiracion";
		$query_registrar_usuario.=")";
		$query_registrar_usuario.=" VALUES";
		$query_registrar_usuario.="(";
		$query_registrar_usuario.="'".$_POST["cod_entidad_salud_".$cont_entidades]."',";
		$query_registrar_usuario.="'".$_POST['nick_logueo']."',";
		$query_registrar_usuario.="'".$tipo_id_reg."',";	
		$query_registrar_usuario.="'".$id_reg."',";			
		$query_registrar_usuario.="'".$_POST["perfil_".$cont_entidades]."',";
		$query_registrar_usuario.="'1', ";
		$query_registrar_usuario.="'".$email."',";
		$query_registrar_usuario.="'".$password."', ";
		$query_registrar_usuario.="'".$fecha_creacion."',";
		$query_registrar_usuario.="'".$fecha_vence."' ";
		$query_registrar_usuario.=");";
		$query_registrar_usuario.="COMMIT;";
		$cont_entidades++;
	}
	
	$bool_error=false;
	if($mensaje_error=="")
	{
		$bool_error=$coneccionBD->insertar_no_warning_get_error($query_registrar_usuario,$mensaje_error);
	}
	
	$query_registrar_usuario2="";
	if($mensaje_error!="")
	{
		$query_registrar_usuario2="ROLLBACK;";
		$realizo_rollback=true;
	}
	
	if($mensaje_error=="")
	{
		//$query_registrar_usuario2="COMMIT;";		
	}
	
	$bool_error=$coneccionBD->insertar_no_warning_get_error($query_registrar_usuario2,$mensaje_error2);
	
	
	
	//echo "<script>document.getElementById('error_div').innerHTML=\"".$mensaje_error."\"</script>";
	if($mensaje_error=="" && $realizo_rollback==false)
	{
		echo "<script>document.getElementById('msg_error').innerHTML='El usuario se ha insertado con exito.';</script>";
		echo "<script>alert('El usuario se ha insertado con exito.');</script>";
		$se_creo_usuario=true;
		
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
		$mail->Subject = "Creo Usuario";
		
		$cadena_nicks=" ";
		$cont=0;
		while(isset($_POST["cod_entidad_salud_".$cont]))
		{
		  $cadena_nicks=" $nick_logueo,para la entidad con codigo ".$_POST["cod_entidad_salud_".$cont]." ; ";
		  $cont++;
		}
		
		$mail->AltBody = "Cordial saludo,\n El sistema ha creado su usuario con el password: ".$password.", y los siquientes nicks para logueo: ".$cadena_nicks;

		$mail->MsgHTML("Cordial saludo,\n El sistema ha creado su usuario con el password: ".$password.", y los siquientes nicks para logueo: ".$cadena_nicks."<strong>GIOSS</strong>.");
		/*
		$mail->AddAttachment("../TEMPORALES/ReporteCaracteresEspeciales" . $this->seq . ".txt");
		$mail->AddAttachment("../TEMPORALES/ErroresCampos" . $this->seq . ".csv");
		$mail->AddAttachment("../TEMPORALES/UsuariosDuplicados" . $this->seq . ".csv");
		*/
		$mail->AddAddress($email, "Destinatario");

		$mail->IsHTML(true);

		if (!$mail->Send()) 
		{
			//echo "Error: " . $mail->ErrorInfo;
		} 
		else 
		{
			echo "<script>alert('Se ha enviado un e-mail al usuario.');</script>";
		}

        //fin envio de mail
		
	}
	else
	{
		echo "<script>document.getElementById('msg_error').innerHTML=\"El usuario no se pudo crear. $mensaje_error   \"</script>";
		echo "<script>alert('El usuario no se pudo crear.');</script>";
		$se_creo_usuario=false;
	}
	
}//fin valores enviados por post


if( isset($_POST['identificacion']))
{
	$re_asignar_por_javascript="";
	$re_asignar_por_javascript.="<script>";
	$re_asignar_por_javascript.="document.getElementById('primer_nombre').value='".$_POST["primer_nombre"]."';";
	$re_asignar_por_javascript.="document.getElementById('segundo_nombre').value='".$_POST["segundo_nombre"]."';";
	$re_asignar_por_javascript.="document.getElementById('primer_apellido').value='".$_POST["primer_apellido"]."';";
	$re_asignar_por_javascript.="document.getElementById('segundo_apellido').value='".$_POST["segundo_apellido"]."';";	
	$re_asignar_por_javascript.="document.getElementById('tipo_identificacion').value='".$_POST["tipo_identificacion"]."';";
	$re_asignar_por_javascript.="document.getElementById('identificacion').value='".$_POST["identificacion"]."';";
	$re_asignar_por_javascript.="document.getElementById('password_user').value='".$_POST["password_user"]."';";
	$re_asignar_por_javascript.="document.getElementById('email').value='".$_POST["email"]."';";
	$re_asignar_por_javascript.="document.getElementById('direccion').value='".$_POST["direccion"]."';";
	$re_asignar_por_javascript.="document.getElementById('telefono').value='".$_POST["telefono"]."';";
	$re_asignar_por_javascript.="document.getElementById('celular').value='".$_POST["celular"]."';";
	$re_asignar_por_javascript.="document.getElementById('fecha_cumple').value='".$_POST["fecha_cumple"]."';";
	$re_asignar_por_javascript.="document.getElementById('fecha_vence').value='".$_POST["fecha_vence"]."';";
	$re_asignar_por_javascript.="document.getElementById('fecha_creacion').value='".$_POST["fecha_creacion"]."';";	
	if($se_creo_usuario==false)
	{
	$re_asignar_por_javascript.="document.getElementById('nick_logueo').value='".$_POST["nick_logueo"]."';";	
	}
	else
	{
	$re_asignar_por_javascript.="document.getElementById('nick_logueo').value='".explode("_",$_POST["nick_logueo"])[0]."_".(intval(explode("_",$_POST["nick_logueo"])[1])+1)."';";	
	}
	$cont_entidades=0;
	while(isset($_POST["cod_entidad_salud_".$cont_entidades]))
	{
		$re_asignar_por_javascript.="document.getElementById('cod_entidad_salud_$cont_entidades').value='".$_POST["cod_entidad_salud_".$cont_entidades]."';";
		$re_asignar_por_javascript.="document.getElementById('perfil_$cont_entidades').value='".$_POST["perfil_".$cont_entidades]."';";
		if(isset($_POST["cod_entidad_salud_".($cont_entidades+1)]))
		{
			$re_asignar_por_javascript.="adicionar_entidad($cont_entidades);";
		}
		$cont_entidades++;
	}	
	$re_asignar_por_javascript.="";
	$re_asignar_por_javascript.="</script>";
	
	echo $re_asignar_por_javascript;
}

?>