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
	//header ("Location: ../index.php?no_tiene_permiso=true");
}//fin if


function alphanumericAndSpace( $string )
{
	$string = str_replace("á","a",$string);
	$string = str_replace("é","e",$string);
	$string = str_replace("í","i",$string);
	$string = str_replace("ó","o",$string);
	$string = str_replace("ú","u",$string);
	$string = str_replace("Á","A",$string);
	$string = str_replace("É","E",$string);
	$string = str_replace("Í","I",$string);
	$string = str_replace("Ó","O",$string);
	$string = str_replace("Ú","U",$string);
	
	$string = str_replace("ñ","n",$string);
	$string = str_replace("Ñ","N",$string);
    return trim(preg_replace('/[^a-zA-Z0-9\s, ;\-@.\/]/', '', $string));
}

function alphanumericAndSpace_include_br( $string )
{
	$string = str_replace("á","a",$string);
	$string = str_replace("é","e",$string);
	$string = str_replace("í","i",$string);
	$string = str_replace("ó","o",$string);
	$string = str_replace("ú","u",$string);
	$string = str_replace("Á","A",$string);
	$string = str_replace("É","E",$string);
	$string = str_replace("Í","I",$string);
	$string = str_replace("Ó","O",$string);
	$string = str_replace("Ú","U",$string);
	
	$string = str_replace("ñ","n",$string);
	$string = str_replace("Ñ","N",$string);
    return trim(preg_replace('/[^a-zA-Z0-9\s, ;\-@.\/\<\>\_\:]/', '', $string));
}

function alphanumericAndSpace4( $string )
{
    $string = str_replace("á","a",$string);
    $string = str_replace("é","e",$string);
    $string = str_replace("í","i",$string);
    $string = str_replace("ó","o",$string);
    $string = str_replace("ú","u",$string);
    $string = str_replace("Á","A",$string);
    $string = str_replace("É","E",$string);
    $string = str_replace("Í","I",$string);
    $string = str_replace("Ó","O",$string);
    $string = str_replace("Ú","U",$string);
    
    $string = str_replace("ñ","n",$string);
    $string = str_replace("Ñ","N",$string);
    $cadena = preg_replace('/[^a-zA-Z0-9\s,\-\/\.]/', '', $string);
    return $cadena;
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


//consultar el tipo de entidad
$sql_query_tipo_entidad_asociada="SELECT * FROM gioss_entidades_sector_salud WHERE codigo_entidad='$entidad_salud_usuario_actual'; ";
$resultado_query_tipo_entidad=$coneccionBD->consultar2_no_crea_cierra($sql_query_tipo_entidad_asociada);
$tipo_entidad=$resultado_query_tipo_entidad[0]["cod_tipo_entidad"];
//fin consultar el tipo de entidad

//descripcion tipo entidad
$sql_query_descripcion_tipo_entidad_asociada="SELECT * FROM gioss_tipo_entidad WHERE codigo_tipo_entidad='$tipo_entidad'; ";
$resultado_query_descripcion_tipo_entidad=$coneccionBD->consultar2_no_crea_cierra($sql_query_descripcion_tipo_entidad_asociada);
$descripcion_tipo_entidad=$resultado_query_descripcion_tipo_entidad[0]["descripcion"];
//fin descripcion tipo entidad

$info="";
$info.="<div id='div_info'>";
//$info.="<select id='eapb' name='eapb' class='campo_azul' onchange='consultar_prestador();' >";


$nombre_entidad="";
$codigo_entidad="";
$nit_entidad="";
$sql_query_tipo_entidad_asociada="SELECT * FROM gioss_entidades_sector_salud WHERE codigo_entidad='$entidad_salud_usuario_actual'; ";
$resultado_query_tipo_entidad=$coneccionBD->consultar2_no_crea_cierra($sql_query_tipo_entidad_asociada);

if(count($resultado_query_tipo_entidad)>0)
{
	foreach($resultado_query_tipo_entidad as $info_entidad)
	{
		$nombre_entidad=$info_entidad['nombre_de_la_entidad'];
		$codigo_entidad=$info_entidad['codigo_entidad'];
		$nit_entidad=$info_entidad['numero_identificacion'];
	}
}
$info.="
<table style='text-align:left;'>

<tr>
<td style='text-align:left;width:150px;'>
<input type='hidden' id='eapb' name='eapb'  value='".$info_entidad['codigo_entidad']."'  />
<input type='text' value='Nombre Entidad' class='campo_azul' readonly='true' style='width:105px'>
</td>
<td style='text-align:left;'>
<input type='text' id='desc_entidad' name='desc_entidad' value='$nombre_entidad' class='campo_azul' style='width:200px !important;' readonly='true'>
</td>
</tr>

<tr>
<td style='text-align:left;width:150px;'>
<input type='text' value='Codigo Entidad' class='campo_azul' readonly='true' style='width:105px'>
</td>
<td style='text-align:left;'>
<input type='text' id='cod_entidad' name='cod_entidad' value='$codigo_entidad' class='campo_azul' readonly='true'>
</td>
</tr>

<tr>
<td style='text-align:left;width:150px;'>
<input type='text' value='Nit Entidad' class='campo_azul' readonly='true' style='width:105px'>
</td>
<td style='text-align:left;'>
<input type='text' id='nit_entidad' name='nit_entidad' value='$nit_entidad' class='campo_azul' readonly='true'>
</td>
</tr>

<tr>
<td style='text-align:left;width:150px;'>
<input type='text' value='Tipo Entidad' class='campo_azul' readonly='true' style='width:105px'>
</td>
<td style='text-align:left;'>
<input type='text' id='tipo_entidad' name='tipo_entidad' value='$descripcion_tipo_entidad' class='campo_azul' readonly='true'>
</td>
</tr>

</table>
";
//FIN



//SELECTOR COHORTE
$query_tipo_cohorte="SELECT * FROM tabla_auditoria_tipo_cohortes ORDER BY codigo;";
$resultado_query_tipo_cohorte=$coneccionBD->consultar2_no_crea_cierra($query_tipo_cohorte);

$selector_cohorte="";
$selector_cohorte.="<table style='text-align:left;'>
<tr>
<td style='text-align:left;width:250px;'>
<input type='text' value='COHORTE' class='campo_azul' readonly='true'>
</td>
<td style='text-align:left;'>
<select id='tipo_cohorte' name='tipo_cohorte' class='campo_azul' style='width:350px;' >";
$selector_cohorte.="<option value='none'>Seleccione COHORTE</option>";
foreach($resultado_query_tipo_cohorte as $key=>$tipo_cohorte)
{
	$codigo=$tipo_cohorte["codigo"];
	$descripcion=$tipo_cohorte["descripcion"];
	$selector_cohorte.="<option value='".$codigo."'>".$descripcion."</option>";
}
$selector_cohorte.="</select>
</td>
</tr>
</table>
";
//SELECTOR COHORTE

$mensaje="";
$mostrarResultado="none";

$smarty->assign("selector_cohorte", $selector_cohorte, true);
$smarty->assign("info_entidad", $info, true);
$smarty->assign("mensaje_proceso", $mensaje, true);
$smarty->assign("mostrarResultado", $mostrarResultado, true);
$smarty->assign("nombre", $nombre, true);
$smarty->assign("menu", $menu, true);
$smarty->display('audsoporclinicos_ERC.html.tpl');

$html_script_expandir="
<script>
Expand(document.getElementById('desc_entidad'));
Expand(document.getElementById('cod_entidad'));
Expand(document.getElementById('nit_entidad'));
Expand(document.getElementById('tipo_entidad'));
</script>";

echo $html_script_expandir;


/*
INSERT INTO gios_menus_opciones_sistema(id_principal,id_padre,nombre_opcion,descripcion_ayuda,tiene_submenus,ruta_interfaz,prioridad_jerarquica) VALUES ('230','171','Auditoria','',FALSE,'..|audsoporclinicos_HF|audsoporclinicos_HF.php','50.01');

INSERT INTO gios_menus_opciones_sistema(id_principal,id_padre,nombre_opcion,descripcion_ayuda,tiene_submenus,ruta_interfaz,prioridad_jerarquica) VALUES ('231','13','Auditoria','',FALSE,'..|audsoporclinicos_CANCER|audsoporclinicos_CANCER.php','50.01');


INSERT INTO gios_menus_opciones_sistema(id_principal,id_padre,nombre_opcion,descripcion_ayuda,tiene_submenus,ruta_interfaz,prioridad_jerarquica) VALUES ('232','186','Auditoria','',FALSE,'..|audsoporclinicos_AR|audsoporclinicos_AR.php','50.01');


INSERT INTO gios_menus_opciones_sistema(id_principal,id_padre,nombre_opcion,descripcion_ayuda,tiene_submenus,ruta_interfaz,prioridad_jerarquica) VALUES ('233','12','Auditoria','',FALSE,'..|audsoporclinicos_VIH|audsoporclinicos_VIH.php','50.01');


INSERT INTO gios_menus_opciones_sistema(id_principal,id_padre,nombre_opcion,descripcion_ayuda,tiene_submenus,ruta_interfaz,prioridad_jerarquica) VALUES ('234','131','Auditoria','',FALSE,'..|audsoporclinicos_ERC|audsoporclinicos_ERC.php','50.01');

INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('230','5'); --admin sistema

INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('231','5'); --admin sistema

INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('232','5'); --admin sistema

INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('233','5'); --admin sistema

INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('234','5'); --admin sistema
--parte perfil auditoria
INSERT INTO gios_perfiles_sistema VALUES (14, 'Auditoria', 'NO');

INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('69','14'); --auditor
INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('68','14'); --auditor
INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('109','14'); --auditor
INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('171','14'); --auditor
INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('13','14'); --auditor
INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('186','14'); --auditor
INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('12','14'); --auditor
INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('131','14'); --auditor
INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('230','14'); --auditor
INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('231','14'); --auditor
INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('232','14'); --auditor
INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('233','14'); --auditor
INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('234','14'); --auditor

--cambio restriccion primary key para permitir un usuario con tipo y numero identificacion y entidad tener varios login con diferentes perfiles

alter table gioss_entidad_nicklogueo_perfil_estado_persona drop constraint gioss_entidad_nicklogueo_perfil_estado_persona_pkey;
alter table gioss_entidad_nicklogueo_perfil_estado_persona add constraint gioss_entidad_nicklogueo_perfil_estado_persona_pkey PRIMARY KEY (entidad, tipo_id, identificacion_usuario,perfil_asociado);

--nuevo usuario

INSERT INTO gioss_entidad_nicklogueo_perfil_estado_persona VALUES ('EMP028', 'Auditor_1', 'CC', '1024488857', 14, 1, 'jdmejia2009@gmail.com', '2018-01-05', '2019-01-05', '2018-01-11', 'omega002', '12:00:49');

*/


date_default_timezone_set ("America/Bogota");
$fecha_actual = date('Y-m-d');
$tiempo_actual = date('H:i:s');
$tiempo_actual_string=str_replace(":","-",$tiempo_actual);

$fecha_para_archivo=str_replace("-", "", $fecha_actual ).str_replace(":", "", $tiempo_actual );

$rutaTemporal = '../TEMPORALES/';

$host="127.0.0.1";
$port="5432";
$dbname="datagios_pcaltocostogioss";
$user="giossuser";
$pass="giossalpha";

$coneccionAlternaBD= new conexion();
$coneccionAlternaBD->crearConexionCustom($host,$port,$dbname,$user,$pass);


if(isset($_FILES["archivo_a_subir"])==true)
{
	$fecha_para_archivo= date('YmdHis');
	$carpetaOrig4505="ORIGAUDHF".$fecha_para_archivo;
    if(!file_exists($rutaTemporal.$carpetaOrig4505))
    {
	    mkdir($rutaTemporal.$carpetaOrig4505, 0777, true);
    }//fin if
    
	$nombre_archivo=explode(".",$_FILES["archivo_a_subir"]["name"])[0];

	
}//fin if


$coneccionAlternaBD->cerrar_conexion();

$coneccionBD->cerrar_conexion();
?>