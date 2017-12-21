<?php
set_time_limit(936000);
ini_set('max_execution_time', 936000);
ini_set('memory_limit', '900M');

require '../librerias_externas/Smarty-3.1.17/libs/Smarty.class.php';
include_once ('../utiles/clase_coneccion_bd.php');
include_once ('../utiles/funciones_crear_menu.php');

require_once "procesar_mensaje.php";

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

session_write_close();

//SELECTOR EAPB-ASOCIADA_ASOCIADA_USUARIO POR LOGUEO

//consultar el tipo de entidad
$sql_query_tipo_entidad_asociada="SELECT * FROM gioss_entidades_sector_salud WHERE codigo_entidad='$entidad_salud_usuario_actual'; ";
$resultado_query_tipo_entidad=$coneccionBD->consultar2($sql_query_tipo_entidad_asociada);
$tipo_entidad=$resultado_query_tipo_entidad[0]["cod_tipo_entidad"];
//fin consultar el tipo de entidad

$eapb="";
$eapb.="<div id='div_eapb'>";
//$eapb.="<select id='eapb' name='eapb' class='campo_azul' onchange='consultar_prestador();' >";


$nombre_entidad="";
$codigo_entidad="";
$nit_entidad="";
$sql_query_tipo_entidad_asociada="SELECT * FROM gioss_entidades_sector_salud WHERE codigo_entidad='$entidad_salud_usuario_actual'; ";
$resultado_query_tipo_entidad=$coneccionBD->consultar2($sql_query_tipo_entidad_asociada);

if(count($resultado_query_tipo_entidad)>0)
{
	foreach($resultado_query_tipo_entidad as $eapb_entidad)
	{
		$nombre_entidad=$eapb_entidad['nombre_de_la_entidad'];
		$codigo_entidad=$eapb_entidad['codigo_entidad'];
		$nit_entidad=$eapb_entidad['numero_identificacion'];
	}
}
$eapb.="<input type='hidden' id='eapb' name='eapb'  value='".$eapb_entidad['codigo_entidad']."'  />";
$eapb.="<label>Nombre Entidad</label><input type='text' id='desc_entidad' name='desc_entidad' value='$nombre_entidad' class='campo_azul' readonly='true'>";
$eapb.="<label>Codigo Entidad</label><input type='text' id='desc_entidad' name='desc_entidad' value='$codigo_entidad' class='campo_azul' readonly='true'>";
$eapb.="<label>Nit Entidad</label><input type='text' id='desc_entidad' name='desc_entidad' value='$nit_entidad' class='campo_azul' readonly='true'>";
//FIN


date_default_timezone_set ("America/Bogota");
$fecha_actual = date('Y-m-d');
$tiempo_actual = date('H:i:s');

$mensaje="";
$mostrarResultado="none";

$smarty->assign("campo_eapb", $eapb, true);

$smarty->assign("mensaje_proceso", $mensaje, true);
$smarty->assign("mostrarResultado", $mostrarResultado, true);
$smarty->assign("nombre", $nombre, true);
$smarty->assign("menu", $menu, true);
$smarty->display('asociar_entidades.html.tpl');

//echo "<script>consultar_prestador();</script>";

if(isset($_FILES["ent_obl_a_rep_file"]) && isset($_POST["oculto_envio"])
   && isset($_POST["eapb"]) && $_POST["eapb"]!="none" && $_POST["oculto_envio"]=="envio" && $_FILES["ent_obl_a_rep_file"]["error"] == 0)
{
	$rutaTemporal = '../TEMPORALES/';
	
	$codigo_eapb=$_POST["eapb"];
	
	$sentido=$_POST["sentido_relacion"];
	
	$mensajes_error_bd="";
	$mensajes_error_normales="";
	$mensajes_exitosos_bd="";
	
	$archivo_entidades_obligadas_a_reportar=$_FILES["ent_obl_a_rep_file"];
	$ruta_archivo_entidades_obligadas_a_reportar = $rutaTemporal . $archivo_entidades_obligadas_a_reportar['name'];
	move_uploaded_file($archivo_entidades_obligadas_a_reportar['tmp_name'], $ruta_archivo_entidades_obligadas_a_reportar);
		
	//archivo que se lee
	$lineas_del_archivo = count(file($ruta_archivo_entidades_obligadas_a_reportar)); 
	$archivo_cargar = fopen($ruta_archivo_entidades_obligadas_a_reportar, 'r') or exit("No se pudo abrir el archivo con los datos");
	$numero_linea=1;
	while (!feof($archivo_cargar)) 
	{
		$linea = fgets($archivo_cargar);
		$linea_res = procesar_mensaje($linea);
		$campos = explode(",",$linea_res);
		
		$cont_pre_campos=0;
		while($cont_pre_campos<count($campos))
		{
			$campos[$cont_pre_campos]=trim($campos[$cont_pre_campos]);
			$cont_pre_campos++;		
		}
		
		if($linea_res!="" && count($campos)==1)
		{
			if($sentido=="2" || $sentido=="0")
			{
				$query_asociar_prestadores_con_eapb="";
				$query_asociar_prestadores_con_eapb.="INSERT INTO gioss_relacion_entre_entidades_salud";
				$query_asociar_prestadores_con_eapb.="(entidad1,entidad2)";
				$query_asociar_prestadores_con_eapb.=" VALUES ";
				$query_asociar_prestadores_con_eapb.="('".$campos[0]."','$codigo_eapb');";
				$error_bd="";
				$bool_funciono_asociacion=$coneccionBD->insertar_no_warning_get_error($query_asociar_prestadores_con_eapb,$error_bd);
				if($error_bd!="")
				{
					$mensajes_error_bd.="ERROR AL ASOCIAR LA ENTIDAD LA LINEA $numero_linea. <br>";							
					$hubo_errores=true;				
					
				}
				else
				{
					$mensajes_exitosos_bd.="Se asocio la entidad ".$campos[0]." a la entidad del usuario $codigo_eapb , debido a que no estaba asociada .<br> ";
				}
			}
			
			if($sentido=="1" || $sentido=="0")
			{
				$query_asociar_prestadores_con_eapb="";
				$query_asociar_prestadores_con_eapb.="INSERT INTO gioss_relacion_entre_entidades_salud";
				$query_asociar_prestadores_con_eapb.="(entidad1,entidad2)";
				$query_asociar_prestadores_con_eapb.=" VALUES ";
				$query_asociar_prestadores_con_eapb.="('$codigo_eapb','".$campos[0]."');";
				$error_bd="";
				$bool_funciono_asociacion=$coneccionBD->insertar_no_warning_get_error($query_asociar_prestadores_con_eapb,$error_bd);
				if($error_bd!="")
				{
					$mensajes_error_bd.="ERROR AL ASOCIAR LA ENTIDAD LA LINEA $numero_linea. <br>";							
					$hubo_errores=true;				
					
				}
				else
				{
					$mensajes_exitosos_bd.="Se asocio la entidad $codigo_eapb a la entidad del usuario  ".$campos[0]." , debido a que no estaba asociada .<br> ";
				}
			}
		}
		else if(count($campos)!=1)
		{
			$mensajes_error_normales.="El numero de campos es incorrecto, deben ser una sola entidad por linea en  $numero_linea y son solo ".count($campos).". <br>";
		}
		else if($linea_res=="")
		{
			$mensajes_error_normales.="La linea $numero_linea esta en blanco. <br>";
		}
		$numero_linea++;
	}//fin while lectura linea
	
	if($mensajes_error_bd!="" || $mensajes_error_normales!="")
	{
		echo "<script>document.getElementById('div_mensaje_error').style.display='inline';</script>";
		echo "<script>document.getElementById('titulo_mensaje_error').innerHTML=\"<u>Error en la carga de las entidades obligadas a reportar:</u>\";</script>";
		echo "<script>document.getElementById('parrafo_mensaje_error').innerHTML=\"$mensajes_error_bd $mensajes_error_normales\";</script>";
	}
	else if($mensajes_exitosos_bd=="")
	{
		echo "<script>document.getElementById('div_mensaje_exito').style.display='inline';</script>";
		echo "<script>document.getElementById('titulo_mensaje_exito').innerHTML=\"<u>Carga de todas las entidades obligadas a reportar fue exitosa:</u>\";</script>";
		echo "<script>document.getElementById('parrafo_mensaje_exito').innerHTML=\"No hubo errores al subir las entidades obligadas a reportar\";</script>";
	}
	
	if($mensajes_exitosos_bd!="")
	{
		echo "<script>document.getElementById('div_mensaje_exito').style.display='inline';</script>";
		echo "<script>document.getElementById('titulo_mensaje_exito').innerHTML=\"<u>Carga de algunas entidades obligadas a reportar fue exitosa:</u>\";</script>";
		echo "<script>document.getElementById('parrafo_mensaje_exito').innerHTML=\"$mensajes_exitosos_bd\";</script>";
	}
}
else if(isset($_POST["oculto_envio"]) && $_POST["oculto_envio"]=="envio")
{
	$mensaje_error="";
	
	
	
	
	if ($_FILES["ent_obl_a_rep_file"]["error"] > 0)
	{
		$mensaje_error.="No hay un archivo subido. <br>";
	}
	
	if($mensaje_error!="")
	{
		echo "<script>document.getElementById('div_mensaje_error').style.display='inline';</script>";
		echo "<script>document.getElementById('titulo_mensaje_error').innerHTML=\"<u>Error en la carga de las entidades obligadas a reportar:</u>\";</script>";
		echo "<script>document.getElementById('parrafo_mensaje_error').innerHTML=\"$mensaje_error\";</script>";
	}
}

?>