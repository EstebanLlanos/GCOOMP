<?php
set_time_limit(936000);
ini_set('max_execution_time', 936000);
ini_set('memory_limit', '900M');

require '../librerias_externas/Smarty-3.1.17/libs/Smarty.class.php';
include_once ('../utiles/clase_coneccion_bd.php');
include_once ('../utiles/funciones_crear_menu.php');

require_once '../utiles/crear_zip.php';

include ("../librerias_externas/PHPMailer/class.phpmailer.php");
include ("../librerias_externas/PHPMailer/class.smtp.php");
require_once ("../librerias_externas/PHPExcel/PHPExcel.php");
require_once ("../librerias_externas/PHPExcel/PHPExcel/Writer/Excel2007.php");

require_once '../utiles/configuracion_global_email.php';

$smarty = new Smarty;
$coneccionBD = new conexion();
$coneccionBD->crearConexion();
session_start();

if (!isset($_SESSION['usuario']) && !isset($_SESSION['tipo_perfil']))
{
	
	header ("Location: ../index.php");
}

if(
	isset($_SESSION['tipo_perfil'])
	&& $_SESSION['tipo_perfil']!='5'
	)
{
	header ("Location: ../index.php?no_tiene_permiso=true");
}//fin if


function alphanumericAndSpace( $string )
{
	$string = str_replace("�","a",$string);
	$string = str_replace("�","e",$string);
	$string = str_replace("�","i",$string);
	$string = str_replace("�","o",$string);
	$string = str_replace("�","u",$string);
	$string = str_replace("�","A",$string);
	$string = str_replace("�","E",$string);
	$string = str_replace("�","I",$string);
	$string = str_replace("�","O",$string);
	$string = str_replace("�","U",$string);
	
	$string = str_replace("�","n",$string);
	$string = str_replace("�","N",$string);
    return trim(preg_replace('/[^a-zA-Z0-9\s, ;\-@.\/]/', '', $string));
}

function alphanumericAndSpace_include_br( $string )
{
	$string = str_replace("�","a",$string);
	$string = str_replace("�","e",$string);
	$string = str_replace("�","i",$string);
	$string = str_replace("�","o",$string);
	$string = str_replace("�","u",$string);
	$string = str_replace("�","A",$string);
	$string = str_replace("�","E",$string);
	$string = str_replace("�","I",$string);
	$string = str_replace("�","O",$string);
	$string = str_replace("�","U",$string);
	
	$string = str_replace("�","n",$string);
	$string = str_replace("�","N",$string);
    return trim(preg_replace('/[^a-zA-Z0-9\s, ;\-@.\/\<\>\_\:]/', '', $string));
}

function alphanumericAndSpace4( $string )
{
    $string = str_replace("�","a",$string);
    $string = str_replace("�","e",$string);
    $string = str_replace("�","i",$string);
    $string = str_replace("�","o",$string);
    $string = str_replace("�","u",$string);
    $string = str_replace("�","A",$string);
    $string = str_replace("�","E",$string);
    $string = str_replace("�","I",$string);
    $string = str_replace("�","O",$string);
    $string = str_replace("�","U",$string);
    
    $string = str_replace("�","n",$string);
    $string = str_replace("�","N",$string);
    $cadena = preg_replace('/[^a-zA-Z0-9\s,\-\/\.]/', '', $string);
    return $cadena;
}

function procesar_mensaje($mensaje)
{
	$mensaje_procesado = str_replace("�","a",$mensaje);
	$mensaje_procesado = str_replace("�","e",$mensaje_procesado);
	$mensaje_procesado = str_replace("�","i",$mensaje_procesado);
	$mensaje_procesado = str_replace("�","o",$mensaje_procesado);
	$mensaje_procesado = str_replace("�","u",$mensaje_procesado);
	$mensaje_procesado = str_replace("�","n",$mensaje_procesado);
	$mensaje_procesado = str_replace("�","A",$mensaje_procesado);
	$mensaje_procesado = str_replace("�","E",$mensaje_procesado);
	$mensaje_procesado = str_replace("�","I",$mensaje_procesado);
	$mensaje_procesado = str_replace("�","O",$mensaje_procesado);
	$mensaje_procesado = str_replace("�","U",$mensaje_procesado);
	$mensaje_procesado = str_replace("�","N",$mensaje_procesado);
	$mensaje_procesado = str_replace(" "," ",$mensaje_procesado);
	$mensaje_procesado = str_replace("'"," ",$mensaje_procesado);
	$mensaje_procesado = str_replace("\n"," ",$mensaje_procesado);
	$mensaje_procesado = alphanumericAndSpace_include_br($mensaje_procesado);
	
	return $mensaje_procesado;
}

$menu= $_SESSION['menu_logueo_html'];
$nombre= $_SESSION['nombre_completo'];

$perfil_usuario_actual= $_SESSION['tipo_perfil'];
$entidad_salud_usuario_actual= $_SESSION['entidad_asociada_al_nick'];

$tipo_id=$_SESSION['tipo_id'];
$identificacion=$_SESSION['identificacion'];



$nick_user=$_SESSION['usuario'];

$correo_electronico=$_SESSION['correo'];

$perfil_usuario_actual= $_SESSION['tipo_perfil'];
$entidad_salud_usuario_actual= $_SESSION['entidad_asociada_al_nick'];

session_write_close();

$mensaje="";
$mostrarResultado="none";

$smarty->assign("mensaje_proceso", $mensaje, true);
$smarty->assign("mostrarResultado", $mostrarResultado, true);
$smarty->assign("nombre", $nombre, true);
$smarty->assign("menu", $menu, true);
$smarty->display('cambiar_aqui.html.tpl');

/*
INSERT INTO gios_menus_opciones_sistema(id_principal,id_padre,nombre_opcion,descripcion_ayuda,tiene_submenus,ruta_interfaz,prioridad_jerarquica) VALUES ('212','10','cambiar_aqui','',FALSE,'..|cambiar_aqui|cambiar_aqui.php','33.02');

INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('212','5'); --admin sistema
INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('212','4'); --admin eapb
INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('212','3'); --usuario normal eapb
INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('212','2'); --admin ips
INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('212','1'); --usuario normal ips
*/
$coneccionBD->cerrar_conexion();
?>