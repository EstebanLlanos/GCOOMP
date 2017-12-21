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

$menu= "";
$nombre= "";

$sql_consulta_id_menus_sin_perfil ="SELECT * FROM gios_menus_perfiles  INNER JOIN gios_menus_opciones_sistema ON (gios_menus_perfiles.id_menu = gios_menus_opciones_sistema.id_principal) WHERE id_perfil = '6';";
$resultado_query_menus_sin_perfil=$coneccionBD->consultar2($sql_consulta_id_menus_sin_perfil);
$menu=crear_menu($resultado_query_menus_sin_perfil);

//SELECTOR TIPO ID
$selector_tipo_id="";

$sql_consulta_tipo_id="SELECT * FROM gios_tipo_identificacion_usuarios;";
$resultado_query_tipo_id=$coneccionBD->consultar2($sql_consulta_tipo_id);

$selector_tipo_id.="<select id='tipo_identificacion' name='tipo_identificacion' class='campo_azul'>";
foreach($resultado_query_tipo_id as $tipo_id)
{
	$selector_tipo_id.="<option value='".$tipo_id['abreviacion_tipo_identificacion']."'>".$tipo_id['descripcion_tipo_id']."</option>";
}
$selector_tipo_id.="</select>";
//FIN

$smarty->assign("tipo_id_selector", $selector_tipo_id, true);
$smarty->assign("nombre", $nombre, true);
$smarty->assign("menu", $menu, true);
$smarty->display('olvido_password.html.tpl');

if(isset($_POST["tipo_identificacion"]) && isset($_POST["identificacion"]) && isset($_POST["codigo_entidad"]) && isset($_POST["fecha_nacimiento"]) )
{

	if($_POST["tipo_identificacion"]!="none" && $_POST["identificacion"]!="" && $_POST["codigo_entidad"]!="" && $_POST["fecha_nacimiento"]!="")
	{
		$tipo_id=$_POST["tipo_identificacion"];
		$identificacion=$_POST["identificacion"];
		$cod_entidad=$_POST["codigo_entidad"];
		$fecha_nacimiento=$_POST["fecha_nacimiento"];
		$query_existe_usuario="";
		$query_existe_usuario.="SELECT nu.nicklogueo, nu.password, nu.correo_usuario, nu.estado_nicklogueo, nu.fecha_expiracion ";
		$query_existe_usuario.=" FROM gioss_entidad_nicklogueo_perfil_estado_persona nu INNER JOIN gios_usuarios_sistema usis ";
		$query_existe_usuario.="ON ( nu.tipo_id=usis.tipo_identificacion_usuario AND nu.identificacion_usuario=usis.identificacion_usuario)";
		$query_existe_usuario.=" WHERE nu.tipo_id='$tipo_id' AND nu.identificacion_usuario='$identificacion' AND nu.entidad='$cod_entidad' AND usis.fecha_nacimiento='$fecha_nacimiento';";
		$resultado_existe_usuario=$coneccionBD->consultar2($query_existe_usuario);
		
		if(count($resultado_existe_usuario)>0)
		{
			$usuario_encontrado=$resultado_existe_usuario[0];
			$correo_usuario=$usuario_encontrado["correo_usuario"];
			$nick=$usuario_encontrado["nicklogueo"];
			$password=$usuario_encontrado["password"];
			
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
			$mail->Subject = "Olvido Password ";
			$mail->AltBody = "Cordial saludo,\n ";

			$mail->MsgHTML("Cordial saludo,\n El sistema ha recuperado el password de su usuario \"$password\".<strong>GIOSS</strong>.");
			$mail->AddAddress($correo_usuario, "Destinatario");

			$mail->IsHTML(true);

			if (!$mail->Send()) 
			{
				//echo "Error: " . $mail->ErrorInfo;
				echo "<script>ventana_mensaje_estilizada('No se pudo enviar el password de su usuario a su correo','Error:')</script>";
			} else 
			{
				// echo "Mensaje enviado.";
				echo "<script>ventana_mensaje_estilizada('Se ha enviado el password de su usuario a su correo: $correo_usuario','Password encontrado:')</script>";
			}

			//fin envio de mail
		}
		else
		{
			echo "<script>ventana_mensaje_estilizada('El usuario no existe','Error:')</script>";
		}
	}//fin los datos no son en blanco
	else 
	{
		echo "<script>ventana_mensaje_estilizada('Registre los campos','Error:')</script>";
	}
}//cuando envia los datos
?>