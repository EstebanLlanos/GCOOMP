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

$menu= $_SESSION['menu_logueo_html'];
$nombre= $_SESSION['nombre_completo'];

$perfil_usuario_actual= $_SESSION['tipo_perfil'];
$entidad_salud_usuario_actual= $_SESSION['entidad_asociada_al_nick'];

$tipo_id=$_SESSION['tipo_id'];
$identificacion=$_SESSION['identificacion'];


$nick_user=$_SESSION['usuario'];

$correo_electronico=$_SESSION['correo'];

session_write_close();

$mensaje="";
$mostrarResultado="none";

date_default_timezone_set ("America/Bogota");
$fecha_actual = date('Y-m-d');
$tiempo_actual = date('h:i:s');

function alphanumericAndSpace( $string )
{
    return preg_replace('/[^a-zA-Z0-9\s,@]/', '', $string);
}

function alphanumericAndSpace2( $string )
{
    return preg_replace('/[^a-zA-Z0-9\s,@<>]/', '', $string);
}

function procesar_mensaje($mensaje)
{
	$mensaje_procesado = str_replace("á","a",$mensaje);
	$mensaje_procesado = str_replace("é","e",$mensaje_procesado);
	$mensaje_procesado = str_replace("í","i",$mensaje_procesado);
	$mensaje_procesado = str_replace("ó","o",$mensaje_procesado);
	$mensaje_procesado = str_replace("ú","u",$mensaje_procesado);
	$mensaje_procesado = str_replace("ñ","n",$mensaje_procesado);
	$mensaje_procesado = str_replace("Á","A",$mensaje_procesado);
	$mensaje_procesado = str_replace("É","E",$mensaje_procesado);
	$mensaje_procesado = str_replace("Í","I",$mensaje_procesado);
	$mensaje_procesado = str_replace("Ó","O",$mensaje_procesado);
	$mensaje_procesado = str_replace("Ú","U",$mensaje_procesado);
	$mensaje_procesado = str_replace("Ñ","N",$mensaje_procesado);
	$mensaje_procesado = str_replace(" "," ",$mensaje_procesado);
	$mensaje_procesado = str_replace("'"," ",$mensaje_procesado);
	$mensaje_procesado = str_replace("\n"," ",$mensaje_procesado);
	$mensaje_procesado = alphanumericAndSpace($mensaje_procesado);
	
	return $mensaje_procesado;
}

//SELECTOR TIPO ID
$selector_tipo_id="";
$selector_tipo_id.="<select id='selector_tipo_id_paciente' name='selector_tipo_id_paciente' class='campo_azul' style='width:70%;'>";
$selector_tipo_id.="<option value='none'>Seleccione un tipo de identificacion</option>";

$sql_tipo_id_paciente="";
$sql_tipo_id_paciente.=" SELECT * FROM gios_tipo_identificacion_usuarios ; ";
$resultado_tipo_id_paciente=$coneccionBD->consultar2($sql_tipo_id_paciente);
foreach($resultado_tipo_id_paciente as $tipo_id)
{
	$selector_tipo_id.="<option value='".$tipo_id['abreviacion_tipo_identificacion']."'>".$tipo_id['descripcion_tipo_id']."</option>";
}

$selector_tipo_id.="</select>";
//FIN SELECTOR TIPO ID


$html_fecha_hora_actual="";
if(!isset($_POST["fecha_hora_hidden"]))
{
	$html_fecha_hora_actual.="<h5>Fecha y hora de la consulta: ".$fecha_actual." ".$tiempo_actual." .</h5>";
	$html_fecha_hora_actual.="<input type='hidden' id='fecha_hora_hidden' name='fecha_hora_hidden' value='".$fecha_actual."_".$tiempo_actual."' />";
}
else
{
	$fecha_hora_array=explode("_",$_POST["fecha_hora_hidden"]);
	$html_fecha_hora_actual.="<h5>Fecha y hora de la consulta: ".$fecha_hora_array[0]." ".$fecha_hora_array[1]." .</h5>";
	$html_fecha_hora_actual.="<input type='hidden' id='fecha_hora_hidden' name='fecha_hora_hidden' value='".$_POST["fecha_hora_hidden"]."' />";
}

$smarty->assign("fecha_actual_consulta", $html_fecha_hora_actual, true);
$smarty->assign("selector_tipo_id_paciente", $selector_tipo_id, true);
$smarty->assign("mensaje_proceso", $mensaje, true);
$smarty->assign("mostrarResultado", $mostrarResultado, true);
$smarty->assign("nombre", $nombre, true);
$smarty->assign("menu", $menu, true);
$smarty->display('HC_registro_atencion.html.tpl');

?>