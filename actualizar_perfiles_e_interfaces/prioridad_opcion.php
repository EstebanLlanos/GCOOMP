<?php
set_time_limit(936000);
ini_set('max_execution_time', 936000);
ini_set('memory_limit', '900M');

require '../librerias_externas/Smarty-3.1.17/libs/Smarty.class.php';
include_once ('../utiles/clase_coneccion_bd.php');
include_once ('../utiles/funciones_crear_menu.php');

$coneccionBD = new conexion();
$coneccionBD->crearConexion();

$valor_actual="";
if(isset($_REQUEST['valor_actual'])==true)
{
	$valor_actual=trim($_REQUEST['valor_actual']);
}//fin if


$id_principal="";
if(isset($_REQUEST['id_principal'])==true)
{
	$id_principal=trim($_REQUEST['id_principal']);
}//fin if

if($valor_actual!="" && $id_principal!="")
{
	

	$query_update_prioridad="UPDATE gios_menus_opciones_sistema SET prioridad_jerarquica='$valor_actual' WHERE id_principal='$id_principal' ; ";
	$error_bd="";
	$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($query_update_prioridad,$error_bd);
	if($error_bd!="")
	{
		echo "ERROR";
	}//fin if
	else
	{
		echo "OK";
	}//fin else
}//fin if
$coneccionBD->cerrar_conexion();
?>