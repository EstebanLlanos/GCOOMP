<?php
set_time_limit(936000);
ini_set('max_execution_time', 936000);
ini_set('memory_limit', '900M');

require '../librerias_externas/Smarty-3.1.17/libs/Smarty.class.php';
include_once ('../utiles/clase_coneccion_bd.php');
include_once ('../utiles/funciones_crear_menu.php');

$smarty = new Smarty;
$coneccionBD = new conexion();

session_start();

$menu= "";
$nombre= "";

//NOTA condicion positiva aqui, en otras partes es negativa para decir que no inicio sesion
if (isset($_SESSION['usuario']) && isset($_SESSION['tipo_perfil']))
{
	$menu= $_SESSION['menu_logueo_html'];
	$nombre= $_SESSION['nombre_completo'];
	
	session_write_close();
}//si  inicio sesion
else
{
	$sql_consulta_id_menus_sin_perfil ="SELECT * FROM gios_menus_perfiles  INNER JOIN gios_menus_opciones_sistema ON (gios_menus_perfiles.id_menu = gios_menus_opciones_sistema.id_principal) WHERE id_perfil = '6';";
	$resultado_query_menus_sin_perfil=$coneccionBD->consultar2($sql_consulta_id_menus_sin_perfil);
	$menu=crear_menu($resultado_query_menus_sin_perfil);
}//si no inicio sesion

$smarty->assign("nombre", $nombre, true);
$smarty->assign("menu", $menu, true);
$smarty->display('portafolio_servicios.html.tpl');

?>