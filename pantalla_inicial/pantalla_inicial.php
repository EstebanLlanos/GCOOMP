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

if (!isset($_SESSION['usuario']) && !isset($_SESSION['tipo_perfil']))
{
	
	header ("Location: ../index.php");
}

if(isset($_REQUEST["no_tiene_permiso"]) )
{
	echo "<script>alert('El Usuario No tiene Permiso Para Acceder');</script>";
}//fin if

$menu= $_SESSION['menu_logueo_html'];
$nombre= $_SESSION['nombre_completo'];

$perfil_usuario_actual= $_SESSION['tipo_perfil'];
$entidad_salud_usuario_actual= $_SESSION['entidad_asociada_al_nick'];
$nombre_entidad =$_SESSION['nombre_entidad'];

$fecha_ultimo_acceso=$_SESSION['ultima_fecha_acceso'];
$hora_ultimo_acceso=$_SESSION['ultima_hora_acceso'];

$nick_user=$_SESSION['usuario'];

session_write_close();

$string_ultimo_acceso="Fecha y hora del ultimo acceso realizado por ".$nombre."  fue el ".$fecha_ultimo_acceso." a las ".$hora_ultimo_acceso.".";

$smarty->assign("nick_hidden", $nick_user, true);
$smarty->assign("nombre", $nombre."<br>".$nombre_entidad, true);
$smarty->assign("info_ultimo_acceso", $string_ultimo_acceso, true);
$smarty->assign("menu", $menu, true);
$smarty->display('pantalla_inicial.html.tpl');

echo "<script>administrador_de_tareas_ajax('$nick_user')</script>";
echo "<script>var admon_tareas=setInterval(function(){administrador_de_tareas_ajax('$nick_user')},15000);</script>";

?>