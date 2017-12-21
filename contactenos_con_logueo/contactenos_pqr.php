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

$tipo_id=$_SESSION['tipo_id'];
$identificacion=$_SESSION['identificacion'];

$correo_electronico=$_SESSION['correo'];

session_write_close();

$mensaje="";
$mostrarResultado="none";

$smarty->assign("mensaje_proceso", $mensaje, true);
$smarty->assign("mostrarResultado", $mostrarResultado, true);
$smarty->assign("nombre", $nombre, true);
$smarty->assign("menu", $menu, true);
$smarty->display('contactenos_pqr.html.tpl');

//echo "<script>alert('$HOST_CONF_EMAIL');</script>";

if(isset($_POST["selector_receptor"]) && $_POST["selector_receptor"]!="none"
   && isset($_POST["mensaje_pqr"]) && $_POST["mensaje_pqr"]!="Digite su mensaje"
   && $_POST["mensaje_pqr"]!=""
   && $_POST["asunto"]!=""
   )
{
	$asunto=$_POST["asunto"];
	$receptor_mensaje=$_POST["selector_receptor"];
	$mensaje_a_enviar=$_POST["mensaje_pqr"];
	echo "<script>document.getElementById('selector_receptor').value=\"$receptor_mensaje\";</script>";
	echo "<script>document.getElementById('asunto').value=\"$asunto\";</script>";
	echo "<script>document.getElementById('mensaje_pqr').innerHTML=\"$mensaje_a_enviar\";</script>";
	//echo "<script>alert('".$mensaje_a_enviar."');</script>";
	if(strlen($mensaje_a_enviar)<100)
	{
		echo "<script>document.getElementById('div_mensaje_error').style.display='inline';</script>";
		echo "<script>document.getElementById('parrafo_mensaje_error').innerHTML='El mensaje posee menos de 100 caracteres <b>(".strlen($mensaje_a_enviar).")</b>, por lo cual es muy corto para ser enviado';</script>";
	}
	else if(strlen($mensaje_a_enviar)>1000)
	{
		echo "<script>document.getElementById('div_mensaje_error').style.display='inline';</script>";
		echo "<script>document.getElementById('parrafo_mensaje_error').innerHTML='El mensaje posee mas de 1000 caracteres <b>(".strlen($mensaje_a_enviar).")</b>, por lo cual es muy largo para ser enviado';</script>";
	}
	else
	{
		$errores_mensaje="";
		//PARTE ENVIAR E-MAIL
		try
		{
			if($errores_mensaje=="")
			{	
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
				$mail->Subject = $asunto."  ";
				$mail->AltBody = "Cordial saludo,";
		    
				$mail->MsgHTML("Mensaje de $correo_electronico ".$mensaje_a_enviar);
				//$mail->AddAttachment($ruta_zip);
				$mail->AddAddress($receptor_mensaje, "Destinatario");
		    
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
					echo "<script>document.getElementById('div_mensaje_exito').style.display='inline';</script>";
					echo "<script>document.getElementById('parrafo_mensaje_exito').innerHTML='El mensaje fue enviado';</script>";
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
			echo "<script>document.getElementById('div_mensaje_error').style.display='inline';</script>";
			echo "<script>document.getElementById('parrafo_mensaje_error').innerHTML='Hubo un error inesperado al intentar enviar el correo.';</script>";
		}
		//FIN PARTE ENVIAR E-MAIL
		
	}
}
else if(isset($_POST["selector_receptor"]))
{
	echo "<script>document.getElementById('div_mensaje_error').style.display='inline';</script>";
	echo "<script>document.getElementById('parrafo_mensaje_error').innerHTML='faltan datos por diligenciar.';</script>";
}

?>