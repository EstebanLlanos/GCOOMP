<?php
set_time_limit(936000);
ini_set('max_execution_time', 936000);
ini_set('memory_limit', '900M');

require '../librerias_externas/Smarty-3.1.17/libs/Smarty.class.php';
include_once ('../utiles/clase_coneccion_bd.php');
include_once ('../utiles/funciones_crear_menu.php');

$smarty = new Smarty;
$coneccionBD = new conexion();
$coneccionBD->crearConexion();
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

session_write_close();

$mensaje="";
$mostrarResultado="none";

$perfiles="";
$perfiles.="
<select id='selector_perfiles' name='selector_perfiles' class='campo_azul' onchange='consultar_interfaces();'>
<option value='none'>Seleccione un Perfil</option>
";

$query_perfiles=" SELECT * FROM gios_perfiles_sistema WHERE id_perfil<>'6' ORDER BY nombre_perfil; ";
$resultados_perfiles=$coneccionBD->consultar2_no_crea_cierra($query_perfiles);
if(is_array($resultados_perfiles) && count($resultados_perfiles)>0)
{
	foreach($resultados_perfiles as $perfil_res)
	{
		$perfiles.="<option value='".$perfil_res['id_perfil']."'>".$perfil_res['nombre_perfil']."</option>";
	}
}

$perfiles.="</select>";

$smarty->assign("perfiles", $perfiles, true);
$smarty->assign("mensaje_proceso", $mensaje, true);
$smarty->assign("mostrarResultado", $mostrarResultado, true);
$smarty->assign("nombre", $nombre, true);
$smarty->assign("menu", $menu, true);
$smarty->display('act_perfil_interfaz.html.tpl');



$coneccionBD->cerrar_conexion();
?>