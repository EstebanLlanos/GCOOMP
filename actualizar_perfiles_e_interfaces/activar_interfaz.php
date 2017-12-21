<?php
set_time_limit(936000);
ini_set('max_execution_time', 936000);
ini_set('memory_limit', '900M');

require '../librerias_externas/Smarty-3.1.17/libs/Smarty.class.php';
include_once ('../utiles/clase_coneccion_bd.php');
include_once ('../utiles/funciones_crear_menu.php');

$coneccionBD = new conexion();
$coneccionBD->crearConexion();

if(isset($_REQUEST["perfil_id"]) && isset($_REQUEST["id_principal"]) && isset($_REQUEST["action"]) )
{
	$perfil_id=$_REQUEST["perfil_id"];
	$id_principal=$_REQUEST["id_principal"];
	$nombre_menu=$_REQUEST["nombre_menu"];
	$action=$_REQUEST["action"];
	
	$mesaje_error="";
	if($action=="adicionar")
	{
		$query_asociar_menu_perfil="INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES('$id_principal','$perfil_id'); ";
		$error_bd="";
		$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($query_asociar_menu_perfil,$error_bd);
		$mesaje_error.=$error_bd;
	}
	else if($action=="quitar")
	{
		$query_desasociar_menu_perfil="DELETE FROM gios_menus_perfiles WHERE id_menu='$id_principal' AND id_perfil='$perfil_id'; ";
		$error_bd="";
		$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($query_desasociar_menu_perfil,$error_bd);
		$mesaje_error.=$error_bd;
	}
	
	if($mesaje_error!="")
	{
		echo "Hubo un error al actualizar el perfil.";
	}
	else
	{
		echo "El perfil se ha actualizado.";
	}
}

$coneccionBD->cerrar_conexion();
?>