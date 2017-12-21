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

$menu= $_SESSION['menu_logueo_html'];
$nombre= $_SESSION['nombre_completo'];
$tipo_id=$_SESSION['tipo_id'];
$identificacion=$_SESSION['identificacion'];

$perfil_usuario_actual= $_SESSION['tipo_perfil'];
$entidad_salud_usuario_actual= $_SESSION['entidad_asociada_al_nick'];

session_write_close();

$mensaje="";
$mostrarResultado="none";

//SELECTOR TIPO ID
$selector_tipo_id="";

$sql_consulta_tipo_id="SELECT * FROM gios_tipo_identificacion_usuarios;";
$resultado_query_tipo_id=$coneccionBD->consultar2($sql_consulta_tipo_id);

$selector_tipo_id.="<select id='tipo_identificacion' name='tipo_identificacion' class='campo_azul'>";
$selector_tipo_id.="<option value='none'>Seleccione un tipo de identificaci&oacuten</option>";
foreach($resultado_query_tipo_id as $tipo_id)
{
	$selector_tipo_id.="<option value='".$tipo_id['abreviacion_tipo_identificacion']."'>".$tipo_id['descripcion_tipo_id']."</option>";
}
$selector_tipo_id.="</select>";
//FIN

//SELECTOR ESTADO USUARIO
$selector_estado="";
$selector_estado.="<select id='estado_usuario' name='estado_usuario' class='campo_azul'>";
$selector_estado.="<option value='none'>Seleccione el estado del usuario</option>";
$sql_consulta_estado="SELECT * FROM gios_estado_usuario;";
$resultado_query_estado=$coneccionBD->consultar2($sql_consulta_estado);
foreach($resultado_query_estado as $estado)
{
	$selector_estado.="<option value='".$estado['id_estado']."'>".$estado['nombre_estado']."</option>";
}

$selector_estado.="</select>";
//FIN SELECTOR ESTADO USUARIO

$mensaje.="<div id='div_mensaje'></div>";

//PARTE QUE DIBUJA LA PAGINA HTML PARTE PRINCIPAL
$smarty->assign("estado_usuario",$selector_estado,true);
$smarty->assign("selector_tipo_id",$selector_tipo_id,true);
$smarty->assign("mensaje_proceso", $mensaje, true);
$smarty->assign("mostrarResultado", $mostrarResultado, true);
$smarty->assign("nombre", $nombre, true);
$smarty->assign("menu", $menu, true);
$smarty->display('eliminar_usuario.html.tpl');
//FIN PARTE QUE DIBUJA LA PAGINA HTML PARTE PRINCIPAL

if(isset($_POST['tipo_identificacion']) && isset($_POST['identificacion']) && isset($_POST['cod_entidad_salud_0']) && $_POST['cod_entidad_salud_0']!="" && $_POST['identificacion']!="" && $_POST["accion"]=="buscar")
{
	$indice_inicio=$_POST["index_inicio"];
	$indice_fin=$_POST["index_fin"];
	
	$re_asignar_por_javascript="";
	$re_asignar_por_javascript.="<script>";
	$re_asignar_por_javascript.="document.getElementById('identificacion').value='".$_POST["identificacion"]."';";
	$re_asignar_por_javascript.="document.getElementById('tipo_identificacion').value='".$_POST["tipo_identificacion"]."';";
	$re_asignar_por_javascript.="document.getElementById('rango_resultados').value='".$_POST["rango_resultados"]."';";
	$re_asignar_por_javascript.="document.getElementById('cod_entidad_salud_0').value='".$_POST["cod_entidad_salud_0"]."';";	
	$re_asignar_por_javascript.="document.getElementById('index_inicio').value='".$_POST["index_inicio"]."';";
	$re_asignar_por_javascript.="document.getElementById('index_fin').value='".$_POST["index_fin"]."';";
	$re_asignar_por_javascript.="document.getElementById('fecha_expiracion_text_box').value='".$_POST["fecha_expiracion_text_box"]."';";
	$re_asignar_por_javascript.="document.getElementById('fecha_expiracion_hid').value='".$_POST["fecha_expiracion_hid"]."';";
	$re_asignar_por_javascript.="";
	$re_asignar_por_javascript.="</script>";
	
	
	echo $re_asignar_por_javascript;
	
	$html_busqueda="";
	
	$sql_usuarios="";
	$sql_usuarios.=" SELECT nu.entidad,nu.nicklogueo,nu.tipo_id, nu.identificacion_usuario AS id_user, nu.perfil_asociado, nu.estado_nicklogueo, us.primer_nombre_usuario, us.primer_apellido_usuario, ";
	$sql_usuarios.=" us.segundo_nombre_usuario, us.segundo_apellido_usuario, ps.nombre_perfil , eus.nombre_estado, us.direccion_usuario, us.telefono_fijo, us.telefono_celular, nu.correo_usuario, nu.password, nu.fecha_expiracion ";
	$sql_usuarios.=" FROM gioss_entidad_nicklogueo_perfil_estado_persona nu INNER JOIN gios_usuarios_sistema us ON ( nu.tipo_id = us.tipo_identificacion_usuario AND nu.identificacion_usuario=us.identificacion_usuario) ";
	$sql_usuarios.=" INNER JOIN gios_perfiles_sistema ps ON (nu.perfil_asociado = ps.id_perfil) ";
	$sql_usuarios.=" INNER JOIN gios_estado_usuario eus ON (nu.estado_nicklogueo = eus.id_estado) ";
	$sql_usuarios.=" WHERE nu.tipo_id='".$_POST['tipo_identificacion']."' ";
	if($_POST["identificacion"]!="")
	{
		$sql_usuarios.=" AND ";
		$sql_usuarios.=" nu.identificacion_usuario='".$_POST["identificacion"]."' ";
	}
	if($_POST["cod_entidad_salud_0"]!="")
	{
		$sql_usuarios.=" AND ";
		$sql_usuarios.=" nu.entidad='".$_POST["cod_entidad_salud_0"]."' ";
	}
	//si no es usuario administrador del sistema solo entra a modificar los usuarios de su propia entidad
	if($perfil_usuario_actual!="5")
	{
		$sql_usuarios.=" AND ";
		$sql_usuarios.=" '".$entidad_salud_usuario_actual."'='".$_POST["cod_entidad_salud_0"]."'";
	}
	//fin si el usuario no es administrador del sistema no puede modificar usuarios de otras entidades
	$sql_usuarios.=" ORDER BY nu.identificacion_usuario LIMIT $indice_fin OFFSET $indice_inicio ";
	$sql_usuarios.=";";
	
	$resultado_usuarios_sistema=$coneccionBD->consultar2($sql_usuarios);
	
	//$html_busqueda.="<span style='color:white;'>QUERY: ".$sql_usuarios."</span>";
	$fecha_expiracion="";
	if(count($resultado_usuarios_sistema)>0)
	{
		
		
		
		
		foreach($resultado_usuarios_sistema as $usuario)
		{
			$fecha_expiracion_temp=trim($usuario["fecha_expiracion"]);
			
			$fecha_expiracion=explode("-",$fecha_expiracion_temp)[1]."/".explode("-",$fecha_expiracion_temp)[2]."/".explode("-",$fecha_expiracion_temp)[0];
			
			
			$re_asignar_por_javascript="";
			$re_asignar_por_javascript.="<script>";
			$re_asignar_por_javascript.="document.getElementById('estado_usuario').value='".$usuario["estado_nicklogueo"]."';";
			$re_asignar_por_javascript.="document.getElementById('email').value='".$usuario["correo_usuario"]."';";
			$re_asignar_por_javascript.="document.getElementById('fecha_expiracion_text_box').value='".$fecha_expiracion."';";
			$re_asignar_por_javascript.="document.getElementById('fecha_expiracion_hid').value='".$fecha_expiracion."';";			
			$re_asignar_por_javascript.="";
			$re_asignar_por_javascript.="</script>";
			
			
			echo $re_asignar_por_javascript;
			
			
			
		}//fin foreach
		
				
		echo "<script>document.getElementById('grilla').style.display='inline';</script>";
		
		
	}//fin if hay resultados
	else
	{
		echo "<script>document.getElementById('grilla').style.display='none';</script>";
		$html_busqueda.="<br></br><h5>NO SE ENCONTRARON COINCIDENCIAS.</h5>";
	}
	
	echo "<script>document.getElementById('div_mensaje').innerHTML=\"".$html_busqueda."\";</script>";
	
	

}//if accion es buscar

/*
if(isset($_POST["fecha_expiracion_hid"]) && $_POST["fecha_expiracion_hid"]!="")
{
	echo "<script>alert('si: ".$_POST["fecha_expiracion_text_box"]." y ".$_POST["fecha_expiracion_hid"]."');</script>";
	echo "<script>document.getElementById('fecha_expiracion_text_box').value='".$_POST["fecha_expiracion_hid"]."';</script>";
}
else
{
	echo "<script>alert('no');</script>";
}
*/


if(isset($_POST['tipo_identificacion']) && isset($_POST['identificacion']) && isset($_POST['cod_entidad_salud_0']) && $_POST['cod_entidad_salud_0']!="" && $_POST['identificacion']!="" && $_POST["accion"]=="cambiar_estado")
{
	
	
	echo "<script>document.getElementById('grilla').style.display='inline';</script>";
	
	$re_asignar_por_javascript="";
	$re_asignar_por_javascript.="<script>";
	$re_asignar_por_javascript.="document.getElementById('identificacion').value='".$_POST["identificacion"]."';";
	$re_asignar_por_javascript.="document.getElementById('tipo_identificacion').value='".$_POST["tipo_identificacion"]."';";
	$re_asignar_por_javascript.="document.getElementById('rango_resultados').value='".$_POST["rango_resultados"]."';";
	$re_asignar_por_javascript.="document.getElementById('cod_entidad_salud_0').value='".$_POST["cod_entidad_salud_0"]."';";	
	$re_asignar_por_javascript.="document.getElementById('index_inicio').value='".$_POST["index_inicio"]."';";
	$re_asignar_por_javascript.="document.getElementById('estado_usuario').value='".$_POST["estado_usuario"]."';";
	$re_asignar_por_javascript.="document.getElementById('index_fin').value='".$_POST["index_fin"]."';";
	$re_asignar_por_javascript.="document.getElementById('fecha_expiracion_text_box').value='".$_POST["fecha_expiracion_text_box"]."';";
	$re_asignar_por_javascript.="document.getElementById('fecha_expiracion_hid').value='".$_POST["fecha_expiracion_hid"]."';";
	$re_asignar_por_javascript.="";
	$re_asignar_por_javascript.="</script>";
		
	echo $re_asignar_por_javascript;
	
	
	$mensaje_error="";
	
	//no se elimina solo se cambia el estado 
	$sql_delete="UPDATE gioss_entidad_nicklogueo_perfil_estado_persona SET estado_nicklogueo='".$_POST["estado_usuario"]."' WHERE tipo_id='".$_POST['tipo_identificacion']."' AND identificacion_usuario='".$_POST["identificacion"]."' AND entidad='".$_POST["cod_entidad_salud_0"]."';";
	$mensaje_error=$coneccionBD->insertar3($sql_delete);
	
	//cambia la fecha de expiracion
	$fecha_exp_temp=$_POST["fecha_expiracion_text_box"];
	$array_fecha_exp=explode("/",$fecha_exp_temp);
	$fecha_expiracion_para_bd=$array_fecha_exp[2]."-".$array_fecha_exp[0]."-".$array_fecha_exp[1];
	//echo "<script>alert('$fecha_expiracion_para_bd');</script>";
	$sql_delete="UPDATE gioss_entidad_nicklogueo_perfil_estado_persona SET fecha_expiracion='$fecha_expiracion_para_bd' WHERE tipo_id='".$_POST['tipo_identificacion']."' AND identificacion_usuario='".$_POST["identificacion"]."' AND entidad='".$_POST["cod_entidad_salud_0"]."';";
	$mensaje_error=$coneccionBD->insertar3($sql_delete);
	
	if($mensaje_error=="")
	{
		$mensaje_error.="El estado del usuario  fue cambiado a ".$_POST["estado_usuario"];
		
		echo "<script>alert('El estado del usuario se ha cambiado con exito.');</script>";
		
		// inicio envio de mail

		$mail = new PHPMailer();
		$mail->IsSMTP();
		$mail->SMTPAuth = true;
		$mail->SMTPSecure = "ssl";
		$mail->Host = "smtp.gmail.com";
		$mail->Port = 465;
		$mail->Username = "sistemagioss@gmail.com";
		$mail->Password = "gioss001";
		$mail->From = "sistemagioss@gmail.com";
		$mail->FromName = "GIOSS";
		$mail->Subject = "Cambio del estado del usuario";
		
		
		
		$mail->AltBody = "Cordial saludo,\n El sistema ha cambiado el estado del usuario ";

		$mail->MsgHTML("Cordial saludo,\n El sistema ha cambiado el estado del usuario ".$_POST['tipo_identificacion']." ".$_POST["identificacion"]." para la entidad ".$_POST["cod_entidad_salud_0"]." al estado ".$_POST["estado_usuario"]." .<strong>GIOSS</strong>.");
		
		/*
		$mail->AddAttachment("../TEMPORALES/ReporteCaracteresEspeciales" . $this->seq . ".txt");
		$mail->AddAttachment("../TEMPORALES/ErroresCampos" . $this->seq . ".csv");
		$mail->AddAttachment("../TEMPORALES/UsuariosDuplicados" . $this->seq . ".csv");
		*/
		$mail->AddAddress($_POST["email"], "Destinatario");

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
		$mensaje_error.="No se pudo cambiar el estado del usuario.";
	}
	
	echo "<script>document.getElementById('div_mensaje').innerHTML=\"".$mensaje_error."\";</script>";
}//fin si se presiono cambiar estado

?>