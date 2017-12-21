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

$mensaje.="<div id='div_mensaje'></div>";

//SELECTOR PERFIL
$selector_perfil="";

$sql_consulta_perfil="SELECT * FROM gios_perfiles_sistema ps INNER JOIN perfiles_asociados_perfiles pp on( pp.id_perfil_1='$perfil_usuario_actual' AND ps.id_perfil=pp.id_perfil_2);";
$resultado_query_perfil=$coneccionBD->consultar2($sql_consulta_perfil);

foreach($resultado_query_perfil as $perfil)
{
	if(intval($perfil['id_perfil'])!=6)
	{
		$selector_perfil.="<option value='".$perfil['id_perfil']."'>".$perfil['nombre_perfil']."</option>";
	}
}
//FIN

//PARTE QUE DIBUJA LA PAGINA HTML PARTE PRINCIPAL
$smarty->assign("selector_perfil",$selector_perfil,true);
$smarty->assign("selector_tipo_id",$selector_tipo_id,true);
$smarty->assign("mensaje_proceso", $mensaje, true);
$smarty->assign("mostrarResultado", $mostrarResultado, true);
$smarty->assign("nombre", $nombre, true);
$smarty->assign("menu", $menu, true);
$smarty->display('editar_usuario.html.tpl');
//FIN PARTE QUE DIBUJA LA PAGINA HTML PARTE PRINCIPAL

//echo "<script>alert('$HOST_CONF_EMAIL');</script>";

if(isset($_POST['tipo_identificacion']) && isset($_POST['cod_entidad_salud_0']) && $_POST['cod_entidad_salud_0']!="" && $_POST["identificacion"]!="" && $_POST["tipo_accion_post"]=="consultar")
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
		$sql_usuarios.=" nu.identificacion_usuario='".$_POST["identificacion"]."'";
	}
	if($_POST["cod_entidad_salud_0"]!="")
	{
		$sql_usuarios.=" AND ";
		$sql_usuarios.=" nu.entidad='".$_POST["cod_entidad_salud_0"]."'";
	}
	//si no es usuario administrador del sistema solo entra a modificar los usuarios de su propia entidad
	/*
	if($perfil_usuario_actual!="5")
	{
		$sql_usuarios.=" AND ";
		$sql_usuarios.=" '".$entidad_salud_usuario_actual."'='".$_POST["cod_entidad_salud_0"]."'";
	}
	*/
	//fin si el usuario no es administrador del sistema no puede modificar usuarios de otras entidades
	$sql_usuarios.=" ORDER BY nu.identificacion_usuario LIMIT $indice_fin OFFSET $indice_inicio ";
	$sql_usuarios.=";";
	
	$resultado_usuarios_sistema=$coneccionBD->consultar2($sql_usuarios);
	
	$html_busqueda.="<span style='color:white;'>QUERY: ".$sql_usuarios."</span>";
	
	//encontro el usuario en la base de datos
	if(count($resultado_usuarios_sistema)>0)
	{
		
		
		foreach($resultado_usuarios_sistema as $usuario)
		{
		
			$re_asignar_por_javascript="";
			$re_asignar_por_javascript.="<script>";
			$re_asignar_por_javascript.="document.getElementById('password_user').value='".$usuario["password"]."';";
			$re_asignar_por_javascript.="document.getElementById('email').value='".$usuario["correo_usuario"]."';";
			$re_asignar_por_javascript.="document.getElementById('direccion').value='".$usuario["direccion_usuario"]."';";
			$re_asignar_por_javascript.="document.getElementById('telefono').value='".$usuario["telefono_fijo"]."';";
			$re_asignar_por_javascript.="document.getElementById('celular').value='".$usuario["telefono_celular"]."';";	
			$re_asignar_por_javascript.="document.getElementById('perfil_0').value='".$usuario["perfil_asociado"]."';";	
			$fecha_vence=$usuario["fecha_expiracion"];
			
			$fecha_vence_mod="";
			if($fecha_vence!="")
			{
				$fecha_vence_mod=explode("-",$fecha_vence)[1]."/".explode("-",$fecha_vence)[2]."/".explode("-",$fecha_vence)[0];
			}
			//echo "<script>alert('$fecha_vence $fecha_vence_mod');</script>";
			$re_asignar_por_javascript.="document.getElementById('fecha_vence').value='".$fecha_vence_mod."';";			
			$re_asignar_por_javascript.="document.getElementById('nick_logueo').value='".$usuario["nicklogueo"]."';";
			$re_asignar_por_javascript.="";
			$re_asignar_por_javascript.="</script>";
			
			
			echo $re_asignar_por_javascript;
		
			
		}//fin foreach
		
		echo "<script>document.getElementById('grilla').style.display='inline'</script>";
	}//fin if hay resultados
	else
	{
		echo "<script>document.getElementById('grilla').style.display='none'</script>";
		$html_busqueda.="<br></br><h5>NO SE ENCONTRARON COINCIDENCIAS.</h5>";
	}
	
	echo "<script>document.getElementById('div_mensaje').innerHTML=\"".$html_busqueda."\"</script>";

}//fin accion consultar

//en caso de presionar actualizar
if(isset($_POST['tipo_identificacion']) && isset($_POST['cod_entidad_salud_0']) && $_POST['cod_entidad_salud_0']!="" && $_POST["identificacion"]!="" && $_POST["tipo_accion_post"]=="actualizar")
{
	$re_asignar_por_javascript="";
	$re_asignar_por_javascript.="<script>";
	$re_asignar_por_javascript.="document.getElementById('identificacion').value='".$_POST["identificacion"]."';";
	$re_asignar_por_javascript.="document.getElementById('tipo_identificacion').value='".$_POST["tipo_identificacion"]."';";
	$re_asignar_por_javascript.="document.getElementById('rango_resultados').value='".$_POST["rango_resultados"]."';";
	$re_asignar_por_javascript.="document.getElementById('cod_entidad_salud_0').value='".$_POST["cod_entidad_salud_0"]."';";
	$re_asignar_por_javascript.="document.getElementById('index_inicio').value='".$_POST["index_inicio"]."';";
	$re_asignar_por_javascript.="document.getElementById('index_fin').value='".$_POST["index_fin"]."';";
	$re_asignar_por_javascript.="document.getElementById('password_user').value='".$_POST["password_user"]."';";	
	$re_asignar_por_javascript.="document.getElementById('email').value='".$_POST["email"]."';";
	$re_asignar_por_javascript.="document.getElementById('direccion').value='".$_POST["direccion"]."';";
	$re_asignar_por_javascript.="document.getElementById('telefono').value='".$_POST["telefono"]."';";
	$re_asignar_por_javascript.="document.getElementById('celular').value='".$_POST["celular"]."';";
	$re_asignar_por_javascript.="document.getElementById('fecha_vence').value='".$_POST["fecha_vence"]."';";
	$re_asignar_por_javascript.="document.getElementById('perfil_0').value='".$_POST["perfil_0"]."';";	
	$re_asignar_por_javascript.="document.getElementById('nick_logueo').value='".$_POST["nick_logueo"]."';";
	$re_asignar_por_javascript.="";
	$re_asignar_por_javascript.="</script>";
	
	echo $re_asignar_por_javascript;
	
	echo "<script>document.getElementById('grilla').style.display='inline'</script>";
	
	//conversion fecha para formato en bd
	$fecha_vencimiento_bd=$_POST["fecha_vence"];
	
	if($fecha_vencimiento_bd!="")
	{
	$fecha_vencimiento_bd=explode("/",$fecha_vencimiento_bd)[2]."-".explode("/",$fecha_vencimiento_bd)[0]."-".explode("/",$fecha_vencimiento_bd)[1];
	}
	//fin conversion  formato fecha
	
	//verificacion datos antes de actualizar
	$mensaje_error="";
	$realizo_rollback=false;
	
	// verifica la entidad y si el perfil del usuario actual puede asociarla
	$sql_consulta_entidad_perfiles_validos="SELECT * FROM gioss_entidades_sector_salud  WHERE codigo_entidad='".$_POST["cod_entidad_salud_0"]."' ;";
	$resultado_query_entidad_perfiles_validos=$coneccionBD->consultar2($sql_consulta_entidad_perfiles_validos);
	
	$existe_entidad_salud_sistema=count($resultado_query_entidad_perfiles_validos)>0;
	if($existe_entidad_salud_sistema)
	{
		/*
		$tipo_entidad=$resultado_query_entidad_perfiles_validos[0]["cod_tipo_entidad"];
		$sql_consulta_tipo_entidad_perfiles="SELECT * FROM gioss_tipo_entidades_perfiles  WHERE cod_tipo_entidad='".$tipo_entidad."' ;";
		$resultado_query_tipo_entidad_perfiles_validos=$coneccionBD->consultar2($sql_consulta_tipo_entidad_perfiles);
		if(count($resultado_query_tipo_entidad_perfiles_validos)>0)
		{
			$perfil_entidad=$resultado_query_tipo_entidad_perfiles_validos[0]["cod_perfil"];
			//echo "<script>alert('perfil_entidad: $perfil_entidad , perfil seleccionado: = ".$_POST["perfil_0"]."');</script>";
			$sql_consulta_perfil_permitido="SELECT * FROM perfiles_asociados_perfiles  WHERE id_perfil_2='".$perfil_entidad."' AND id_perfil_1='".$_POST["perfil_0"]."' ;";
			$resultado_query_perfil_permitido=$coneccionBD->consultar2($sql_consulta_perfil_permitido);
			echo "<script>alert(\"$perfil_entidad  ".$_POST["perfil_0"]."\");</script>";
			if(count($resultado_query_perfil_permitido)>0)
			{}
			else
			{
				$mensaje_error.="La entidad no se puede asociar con ese perfil.<br>";
			}
		}
		*/
	}
	else
	{
		$mensaje_error.="La entidad no existe en el sistema.<br>";
	}
	//fin verifica la entidad y si el perfil del usuario actual puede asociarla
	
	//fin verificacion
	
	//parte actualizacion en base de datos
	$query_actualizar_usuario="";
	$query_actualizar_usuario.="BEGIN;";
	$query_actualizar_usuario.="UPDATE gios_usuarios_sistema SET";
	$query_actualizar_usuario.=" ";
	$query_actualizar_usuario.=" direccion_usuario='".$_POST["direccion"]."',telefono_fijo='".$_POST["telefono"]."',telefono_celular='".$_POST["celular"]."' ";		
	$query_actualizar_usuario.=" ";
	$query_actualizar_usuario.=" WHERE ";
	$query_actualizar_usuario.=" ";
	$query_actualizar_usuario.=" tipo_identificacion_usuario= '".$_POST["tipo_identificacion"]."' AND identificacion_usuario='".$_POST["identificacion"]."' ";	
	$query_actualizar_usuario.=";";
	
		
	$query_actualizar_usuario.="UPDATE gioss_entidad_nicklogueo_perfil_estado_persona SET ";
	$query_actualizar_usuario.=" ";
	$query_actualizar_usuario.=" nicklogueo='".$_POST["nick_logueo"]."', ";
	$query_actualizar_usuario.=" fecha_expiracion='".$fecha_vencimiento_bd."', ";	
	$query_actualizar_usuario.=" correo_usuario='".$_POST["email"]."', ";	
	$query_actualizar_usuario.=" perfil_asociado= '".$_POST["perfil_0"]."', ";
	$query_actualizar_usuario.=" password='".$_POST["password_user"]."' ";
	$query_actualizar_usuario.=" ";
	$query_actualizar_usuario.=" WHERE ";
	$query_actualizar_usuario.=" ";
	$query_actualizar_usuario.=" entidad='".$_POST["cod_entidad_salud_0"]."' AND tipo_id ='".$_POST["tipo_identificacion"]."' AND identificacion_usuario='".$_POST["identificacion"]."' ";
	$query_actualizar_usuario.=" ;";
	$query_actualizar_usuario.="COMMIT;";
	
	$bool_error=false;
	//echo "<script>alert(\"entro a $mensaje_error\");</script>";
	$error_bd="";
	if($mensaje_error=="")
	{
		$bool_error=$coneccionBD->insertar_no_warning_get_error($query_actualizar_usuario,$error_bd);
		if($error_bd!="")
		{
			$mensaje_error.="Hubo un error al hacer update. <br>";
		}
	}
	
	$query_actualizar_usuario2="";
	if($mensaje_error!="")
	{
		$query_actualizar_usuario2="ROLLBACK;";
		$realizo_rollback=true;
	}
	
	if($mensaje_error=="")
	{
		//$query_actualizar_usuario2="COMMIT;";		
	}
	
	$error_bd="";
	$bool_error=$coneccionBD->insertar_no_warning_get_error($query_actualizar_usuario2,$error_bd);
	if($error_bd!="")
	{
		$mensaje_error.="Hubo un error al hacer rollback. <br>";
	}
	
	//echo "<script>document.getElementById('error_div').innerHTML=\"".$mensaje_error."\"</script>";
	if($mensaje_error=="" && $realizo_rollback==false)
	{
		echo "<script>document.getElementById('div_mensaje').innerHTML='El usuario se ha modificado con exito.';</script>";
		echo "<script>alert('El usuario se ha modificado con exito.');</script>";
		
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
		$mail->Subject = "Modifico usuario";
		
		$cadena_nicks=" ";
		$cont=0;
		while(isset($_POST["cod_entidad_salud_".$cont]))
		{
		  $cadena_nicks=" ".$_POST["nick_logueo"].", para la entidad con codigo ".$_POST["cod_entidad_salud_".$cont]." ; ";
		  $cont++;
		}
		
		$mail->AltBody = "Cordial saludo,\n El sistema ha modificado su usuario ";

		$mail->MsgHTML("Cordial saludo,\n El sistema ha modificado el usuario con el password: ".$_POST["password_user"].", y los siquientes nicks para logueo: ".$cadena_nicks.".<strong>GIOSS</strong>.");
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
		echo "<script>document.getElementById('div_mensaje').innerHTML=\"<span style='color:red;'>El usuario no se pudo modificar. <br> $mensaje_error </span>\"</script>";
		echo "<script>alert('El usuario no se pudo modificar. ');</script>";
	}
	//fin parte actualizacion en base de datos
}// fin en caso de presionar actualizar

?>