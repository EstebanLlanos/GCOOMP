<?php
set_time_limit(936000);
ini_set('max_execution_time', 936000);
ini_set('memory_limit', '900M');

require '../librerias_externas/Smarty-3.1.17/libs/Smarty.class.php';
include_once ('../utiles/clase_coneccion_bd.php');
include_once ('../utiles/funciones_crear_menu.php');

$coneccionBD = new conexion();
$coneccionBD->crearConexion();

if(isset($_REQUEST["nombre_nuevo_perfil"]) && isset($_REQUEST["tendra_derechos_admin"])  )
{
	$nombre_nuevo_perfil=$_REQUEST["nombre_nuevo_perfil"];
	$tendra_derechos_admin=$_REQUEST["tendra_derechos_admin"];
	
	
	$mesaje_error="";
	$query_consultar_max_id_perfil="SELECT * FROM gios_perfiles_sistema WHERE id_perfil=(select max(id_perfil) from gios_perfiles_sistema);";
	$resultados_max_id_perfil=$coneccionBD->consultar2_no_crea_cierra($query_consultar_max_id_perfil);
	if(is_array($resultados_max_id_perfil) && count($resultados_max_id_perfil)>0)
	{
		$maximo_id_perfil_actual=intval($resultados_max_id_perfil[0]["id_perfil"]);
		$nuevo_id_perfil=$maximo_id_perfil_actual+1;
		
		$query_insertar_nuevo_perfil="INSERT INTO gios_perfiles_sistema(id_perfil,nombre_perfil,permisos_administrador)
		VALUES('$nuevo_id_perfil','$nombre_nuevo_perfil','$tendra_derechos_admin');
		";
		$error_bd="";
		$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($query_insertar_nuevo_perfil,$error_bd);
		$mesaje_error.=$error_bd;

		$query_asociar_admin_con_nuevo_perfil="INSERT INTO perfiles_asociados_perfiles(id_perfil_1,id_perfil_2)
		VALUES('5','$nuevo_id_perfil');
		";
		$error_bd="";
		$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($query_asociar_admin_con_nuevo_perfil,$error_bd);
		$mesaje_error.=$error_bd;
	}
	else
	{
		$nuevo_id_perfil=0;
		
		$query_insertar_nuevo_perfil="INSERT INTO gios_perfiles_sistema(id_perfil,nombre_perfil,permisos_administrador)
		VALUES('$nuevo_id_perfil','$nombre_nuevo_perfil','$tendra_derechos_admin');
		";
		$error_bd="";
		$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($query_insertar_nuevo_perfil,$error_bd);
		$mesaje_error.=$error_bd;
	}//fin else
	
	
	if($mesaje_error!="")
	{
		echo "Hubo un error al crear el perfil.";
	}
	else
	{
		echo "El perfil se ha creado.";
	}
}

$coneccionBD->cerrar_conexion();
?>