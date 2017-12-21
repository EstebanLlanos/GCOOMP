<?php
set_time_limit(936000);
ini_set('max_execution_time', 936000);
ini_set('memory_limit', '900M');

require '../librerias_externas/Smarty-3.1.17/libs/Smarty.class.php';
include_once ('../utiles/clase_coneccion_bd.php');
include_once ('../utiles/funciones_crear_menu.php');

include ("../librerias_externas/PHPMailer/class.phpmailer.php");
include ("../librerias_externas/PHPMailer/class.smtp.php");

$smarty = new Smarty;
$coneccionBD = new conexion();

session_start();

if (!isset($_SESSION['usuario']) && !isset($_SESSION['tipo_perfil']))
{
	
	header ("Location: ../index.php");
}

//$menu= $_SESSION['menu_logueo_html'];
$nombre= $_SESSION['nombre_completo'];

session_write_close();

//SELECTOR TIPO ID
$selector_tipo_id="";

$sql_consulta_tipo_id="SELECT * FROM gios_tipo_identificacion_usuarios;";
$resultado_query_tipo_id=$coneccionBD->consultar2($sql_consulta_tipo_id);

$selector_tipo_id.="<select id='tipo_identificacion' name='tipo_identificacion' class='campo_azul' disabled>";
$selector_tipo_id.="<option value='none'>Seleccione un tipo de identificaci&oacuten</option>";
foreach($resultado_query_tipo_id as $tipo_id)
{
	$selector_tipo_id.="<option value='".$tipo_id['abreviacion_tipo_identificacion']."'>".$tipo_id['descripcion_tipo_id']."</option>";
}
$selector_tipo_id.="</select>";
//FIN

//SELECTOR PERFIL
$selector_perfil="";

$sql_consulta_perfil="SELECT * FROM gios_perfiles_sistema;";
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

//SELECTOR ESTADO USUARIO
$selector_estado="";
//$selector_estado.="<select id='estado_usuario' name='estado_usuario' class='campo_azul'>";
//$selector_estado.="<option value='none'>Seleccione el estado del usuario</option>";


$sql_consulta_estado="SELECT * FROM gios_estado_usuario;";
$resultado_query_estado=$coneccionBD->consultar2($sql_consulta_estado);
foreach($resultado_query_estado as $estado)
{
	$selector_estado.="<option value='".$estado['id_estado']."'>".$estado['nombre_estado']."</option>";
}

$selector_estado.="</select>";
//FIN SELECTOR ESTADO USUARIO

date_default_timezone_set ("America/Bogota");
$fecha_actual = date('Y-m-d');

$div_error="<div id='msg_error'></div>";
//imprime lo prncipal de la pagina

$smarty->assign("estado_usuario",$selector_estado,true);
$smarty->assign("fecha_creado",$fecha_actual,true);
$smarty->assign("selector_perfil",$selector_perfil,true);
$smarty->assign("selector_tipo_id",$selector_tipo_id,true);
$smarty->assign("nombre", $nombre, true);
//$smarty->assign("menu", $menu, true);
$smarty->assign("error", $div_error, true);

//muestra la pagina va al final siempre
$smarty->display('mod_datos_usuario.html.tpl');


$mensaje_error="";
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
	$fecha_ultimo_acceso=$_POST['fecha_ultimo_acceso'];
	
	$realizo_rollback=false;
	
	
	if($fecha_cumple!="")
	{
	$fecha_cumple=explode("/",$fecha_cumple)[2]."-".explode("/",$fecha_cumple)[0]."-".explode("/",$fecha_cumple)[1];
	}
	if($fecha_vence!="")
	{
	$fecha_vence=explode("/",$fecha_vence)[2]."-".explode("/",$fecha_vence)[0]."-".explode("/",$fecha_vence)[1];
	}
	
	
	if($password != $confirmar_password)
	{
		$mensaje_error.="El password no es igual al password confirmado.<br>";
	}
	
	$query_actualizar_usuario="";
	$query_actualizar_usuario.="BEGIN;";
	$query_actualizar_usuario.="UPDATE gios_usuarios_sistema SET";
	$query_actualizar_usuario.=" ";
	$query_actualizar_usuario.=" primer_nombre_usuario='".$primer_nombre."',";
	$query_actualizar_usuario.=" primer_apellido_usuario='".$primer_apellido."',segundo_nombre_usuario='".$segundo_nombre."',segundo_apellido_usuario='".$segundo_apellido."',";
	$query_actualizar_usuario.=" correo_usuario='".$email."',clave_usuario='".$password."',";	
	$query_actualizar_usuario.=" direccion_usuario='".$direccion."',telefono_fijo='".$telefono."',telefono_celular='".$celular."',";	
	$query_actualizar_usuario.=" fecha_creacion='".$fecha_creacion."',fecha_nacimiento='".$fecha_cumple."',fecha_cambio_pass='".$fecha_actual."',";	
	$query_actualizar_usuario.=" fecha_ultimo_acceso='".$fecha_ultimo_acceso."',fecha_inicio='".$fecha_creacion."',fecha_expiracion='".$fecha_vence."' ";	
	$query_actualizar_usuario.=" ";
	$query_actualizar_usuario.=" WHERE ";
	$query_actualizar_usuario.=" ";
	$query_actualizar_usuario.=" tipo_identificacion_usuario= '".$tipo_id_reg."' AND identificacion_usuario='".$id_reg."' ";	
	$query_actualizar_usuario.=";";
	
	$cont_entidades=0;
	while(isset($_POST["cod_entidad_salud_".$cont_entidades]))
	{
		
		$query_actualizar_usuario.="UPDATE gioss_entidad_nicklogueo_perfil_estado_persona SET ";
		$query_actualizar_usuario.=" ";
		$query_actualizar_usuario.=" entidad ='".$_POST["cod_entidad_salud_".$cont_entidades]."', nicklogueo='".$_POST["nick_usuario_".$cont_entidades]."', ";
		$query_actualizar_usuario.=" tipo_id='".$tipo_id_reg."', identificacion_usuario='".$id_reg."',";
		$query_actualizar_usuario.=" perfil_asociado= '".$_POST["perfil_".$cont_entidades]."', estado_nicklogueo='".$_POST["estado_usuario_".$cont_entidades]."' ";
		$query_actualizar_usuario.=" ";
		$query_actualizar_usuario.=" WHERE ";
		$query_actualizar_usuario.=" ";
		$query_actualizar_usuario.=" entidad ='".$_POST["cod_entidad_salud_".$cont_entidades]."' AND nicklogueo='".$_POST["nick_usuario_".$cont_entidades]."' ";
		$query_actualizar_usuario.=" ;";
		
		$cont_entidades++;
	}
	
	if($mensaje_error=="")
	{
		$mensaje_error=$coneccionBD->insertar3($query_actualizar_usuario);
	}
	
	$query_actualizar_usuario2="";
	if($mensaje_error!="")
	{
		$query_actualizar_usuario2="ROLLBACK;";
		$realizo_rollback=true;
	}
	
	if($mensaje_error=="")
	{
		$query_actualizar_usuario2="COMMIT;";		
	}
	
	$mensaje_error2=$coneccionBD->insertar3($query_actualizar_usuario2);
	
	//echo "<script>document.getElementById('error_div').innerHTML=\"".$mensaje_error."\"</script>";
	if($mensaje_error=="" && $realizo_rollback==false)
	{
		echo "<script>document.getElementById('msg_error').innerHTML='El usuario se ha modificado con exito.';</script>";
		echo "<script>alert('El usuario se ha modificado con exito.');</script>";
		
		// inicio envio de mail

		$mail = new PHPMailer();
		$mail->IsSMTP();
		$mail->SMTPAuth = true;
		$mail->SMTPSecure = "ssl";
		$mail->Host = "smtp.gmail.com";
		$mail->Port = 465;
		$mail->Username = "aviss@geniar.net";
		$mail->Password = "avs2013s";
		$mail->From = "aviss@geniar.net";
		$mail->FromName = "GIOSS";
		$mail->Subject = "Modifico usuario";
		
		$cadena_nicks=" ";
		$cont=0;
		while(isset($_POST["cod_entidad_salud_".$cont]))
		{
		  $cadena_nicks=" ".$_POST["nick_usuario_".$cont].", para la entidad con codigo ".$_POST["cod_entidad_salud_".$cont]." ; ";
		  $cont++;
		}
		
		$mail->AltBody = "Cordial saludo,\n El sistema ha modificado su usuario con el password: ".$password.", y los siquientes nicks para logueo: ".$cadena_nicks;

		$mail->MsgHTML("Cordial saludo,\n El sistema ha modificado el usuario.<strong>GIOSS</strong>.");
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
		echo "<script>document.getElementById('msg_error').innerHTML=\"El usuario no se pudo modificar.\"</script>";
		echo "<script>alert('El usuario no se pudo modificar.');</script>";
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
	$re_asignar_por_javascript.="document.getElementById('fecha_ultimo_acceso').value='".$_POST["fecha_ultimo_acceso"]."';";
	
	$cont_entidades=0;
	while(isset($_POST["cod_entidad_salud_".$cont_entidades]))
	{
		$re_asignar_por_javascript.="document.getElementById('cod_entidad_salud_$cont_entidades').value='".$_POST["cod_entidad_salud_".$cont_entidades]."';";
		$re_asignar_por_javascript.="document.getElementById('perfil_$cont_entidades').value='".$_POST["perfil_".$cont_entidades]."';";
		$re_asignar_por_javascript.="document.getElementById('estado_usuario_$cont_entidades').value='".$_POST["estado_usuario_".$cont_entidades]."';";
		$re_asignar_por_javascript.="document.getElementById('nick_usuario_$cont_entidades').value='".$_POST["nick_usuario_".$cont_entidades]."';";
		if(isset($_POST["cod_entidad_salud_".($cont_entidades+1)]))
		{
			$re_asignar_por_javascript.="adicionar_entidad($cont_entidades);";
		}
		$cont_entidades++;
	}	
	$re_asignar_por_javascript.="";
	$re_asignar_por_javascript.="</script>";
	
	echo $re_asignar_por_javascript;
}//reasigna los valores de post

//esto solo se ejecuta cuando viene de la interfaz de consulta del usuario a editar
if(isset($_GET['tipoid_ed']) && isset($_GET['id_ed']) && isset($_GET['nick_usuario']))
{
	$sql_usuarios="";	
	$sql_usuarios.=" SELECT nu.entidad,nu.nicklogueo,nu.tipo_id, nu.identificacion_usuario AS id_user, nu.perfil_asociado, nu.estado_nicklogueo, us.primer_nombre_usuario, us.primer_apellido_usuario, ";
	$sql_usuarios.=" us.segundo_nombre_usuario, us.segundo_apellido_usuario, us.clave_usuario , us.direccion_usuario, us.telefono_fijo, us.telefono_celular, us.fecha_nacimiento, us.fecha_expiracion,  ";
	$sql_usuarios.=" us.perfil_usuario, us.fecha_inicio, us.fecha_ultimo_acceso, us.correo_usuario ";
	$sql_usuarios.=" FROM gioss_entidad_nicklogueo_perfil_estado_persona nu INNER JOIN gios_usuarios_sistema us ON ( nu.tipo_id = us.tipo_identificacion_usuario AND nu.identificacion_usuario=us.identificacion_usuario) ";
	$sql_usuarios.=" WHERE nu.tipo_id='".$_GET['tipoid_ed']."' ";	
	$sql_usuarios.=" AND ";
	$sql_usuarios.=" nu.identificacion_usuario='".$_GET['id_ed']."'";	
	$sql_usuarios.=" AND ";
	$sql_usuarios.=" nu.nicklogueo='".$_GET['nick_usuario']."'";
	$sql_usuarios.=";";
	$resultado_usuarios_sistema=$coneccionBD->consultar2($sql_usuarios);
	
	if(count($resultado_usuarios_sistema)>0)
	{
		foreach($resultado_usuarios_sistema as $usuario)
		{
			$tipo_id_reg=$usuario['tipo_id'];
			$id_reg=$usuario['id_user'];
			$primer_nombre=$usuario['primer_nombre_usuario'];
			$segundo_nombre=$usuario['segundo_nombre_usuario'];
			$primer_apellido=$usuario['primer_apellido_usuario'];
			$segundo_apellido=$usuario['segundo_apellido_usuario'];
			$password=$usuario['clave_usuario'];
			$confirmar_password=$usuario['clave_usuario'];
			$email=$usuario['correo_usuario'];
			$direccion=$usuario['direccion_usuario'];
			$telefono=$usuario['telefono_fijo'];
			$celular=$usuario['telefono_celular'];
			$fecha_cumple=$usuario['fecha_nacimiento'];
			$fecha_vence=$usuario['fecha_expiracion'];
			$perfil=$usuario['perfil_asociado'];
			$fecha_creacion=$usuario['fecha_inicio'];
			$fecha_ultimo_acceso=$usuario['fecha_ultimo_acceso'];
			$estado_usuario=$usuario['estado_nicklogueo'];
			$nick_usuario=$usuario['nicklogueo'];
			$entidad=$usuario['entidad'];
			
			
				
			//echo "<script>alert('$fecha_vence');</script>";
			if($fecha_cumple!="")
			{
			$fecha_cumple=explode("-",$fecha_cumple)[1]."/".explode("-",$fecha_cumple)[2]."/".explode("-",$fecha_cumple)[0];
			}
			if($fecha_vence!="")
			{
			$fecha_vence=explode("-",$fecha_vence)[1]."/".explode("-",$fecha_vence)[2]."/".explode("-",$fecha_vence)[0];
			}
			//echo "<script>alert('$fecha_vence');</script>";
			
			$re_asignar_por_javascript="";
			$re_asignar_por_javascript.="<script>";
			$re_asignar_por_javascript.="document.getElementById('primer_nombre').value='".$primer_nombre."';";
			$re_asignar_por_javascript.="document.getElementById('segundo_nombre').value='".$segundo_nombre."';";
			$re_asignar_por_javascript.="document.getElementById('primer_apellido').value='".$primer_apellido."';";
			$re_asignar_por_javascript.="document.getElementById('segundo_apellido').value='".$segundo_apellido."';";	
			$re_asignar_por_javascript.="document.getElementById('tipo_identificacion').value='".$tipo_id_reg."';";
			$re_asignar_por_javascript.="document.getElementById('identificacion').value='".$id_reg."';";			
			$re_asignar_por_javascript.="document.getElementById('password_user').value='".$password."';";
			$re_asignar_por_javascript.="document.getElementById('confirmar_password').value='".$confirmar_password."';";
			$re_asignar_por_javascript.="document.getElementById('email').value='".$email."';";
			$re_asignar_por_javascript.="document.getElementById('direccion').value='".$direccion."';";
			$re_asignar_por_javascript.="document.getElementById('telefono').value='".$telefono."';";
			$re_asignar_por_javascript.="document.getElementById('celular').value='".$celular."';";
			$re_asignar_por_javascript.="document.getElementById('fecha_cumple').value='".$fecha_cumple."';";
			$re_asignar_por_javascript.="document.getElementById('fecha_vence').value='".$fecha_vence."';";			
			$re_asignar_por_javascript.="document.getElementById('fecha_creacion').value='".$fecha_creacion."';";
			$re_asignar_por_javascript.="document.getElementById('fecha_ultimo_acceso').value='".$fecha_ultimo_acceso."';";
			
			$re_asignar_por_javascript.="document.getElementById('cod_entidad_salud_0').value='".$entidad."';";
			$re_asignar_por_javascript.="document.getElementById('nick_usuario_0').value='".$nick_usuario."';";
			$re_asignar_por_javascript.="document.getElementById('perfil_0').value='".$perfil."';";
			$re_asignar_por_javascript.="document.getElementById('estado_usuario_0').value='".$estado_usuario."';";
			
			$re_asignar_por_javascript.="";
			$re_asignar_por_javascript.="</script>";
			
			
			echo $re_asignar_por_javascript;
		}
	}
}//fin datos por get y consulta bd el usuario

?>