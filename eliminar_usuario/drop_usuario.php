<?php
include_once ('../utiles/clase_coneccion_bd.php');

if(isset($_REQUEST["nick_usuario"]))
{
	$nick_usuario=$_REQUEST["nick_usuario"];
	
	$coneccionBD = new conexion();
	//no se elimina solo se cambia el estado a inactivo
	$sql_delete="UPDATE gioss_entidad_nicklogueo_perfil_estado_persona SET estado_nicklogueo='2' WHERE nicklogueo='$nick_usuario' ;";
	$mensaje_error=$coneccionBD->insertar3($sql_delete);
	
	if($mensaje_error=="")
	{
		echo $nick_usuario." fue desactivado";
	}
	else
	{
		echo $mensaje_error;
	}
}
?>