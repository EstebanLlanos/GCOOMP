<?php
ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time', 0);
ini_set('memory_limit', '2000M');

require '../librerias_externas/Smarty-3.1.17/libs/Smarty.class.php';
include_once ('../utiles/clase_coneccion_bd.php');
include_once ('../utiles/funciones_crear_menu.php');
require_once '../utiles/queries_utiles_bd.php';


require_once 'validadorRIPS.php';

require_once 'validadorRIPS_eapb.php';


include ("../librerias_externas/PHPMailer/class.phpmailer.php");
include ("../librerias_externas/PHPMailer/class.smtp.php");
require_once ("../librerias_externas/PHPExcel/PHPExcel.php");
require_once ("../librerias_externas/PHPExcel/PHPExcel/Writer/Excel2007.php");

require_once '../utiles/crear_zip.php';

$smarty = new Smarty;
$coneccionBD = new conexion();
$utilidades = new Utilidades();

session_start();

if (!isset($_SESSION['usuario']) && !isset($_SESSION['tipo_perfil']))
{
	
	header ("Location: ../index.php");
}

$menu= $_SESSION['menu_logueo_html'];
$nombre= $_SESSION['nombre_completo'];

$perfil_usuario_actual= $_SESSION['tipo_perfil'];
$entidad_salud_usuario_actual= $_SESSION['entidad_asociada_al_nick'];
$nombre_entidad =$_SESSION['nombre_entidad'];

$tipo_id=$_SESSION['tipo_id'];
$identificacion=$_SESSION['identificacion'];

$nick_user=$_SESSION['usuario'];

$correo_electronico=$_SESSION['correo'];

session_write_close();

$mensaje="";
$mostrarResultado="none";

//esta arregla campos antes de subir a bd individualmente junto con procsesar mensaje 2 para tildes
function alphanumericAndSpace2( $string )
{
    return preg_replace('/[^a-zA-Z0-9:\s,;\-@.\/]/', '', $string);
}

function alphanumericAndSpace( $string )
{
    return preg_replace('/[^a-zA-Z0-9\s,@]/', '', $string);
}

function alphanumericAndSpace3( $string )
{
    return preg_replace('/[^a-zA-Z0-9\s, ;\-@.\/\<\>\_]/', '', $string);
}
function linea_campos_fix($string)
{
	//se adiciona coma porque se separara la linea
	return preg_replace("/[^A-Za-z0-9:.,\-\/]/", "", trim($string) );
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

function procesar_mensaje2($mensaje)
{
	$mensaje_procesado = str_replace("á","a",trim($mensaje));
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
	$mensaje_procesado = alphanumericAndSpace2($mensaje_procesado);
	
	if($mensaje_procesado=="")
	{
		$mensaje_procesado.="0";
	}
	
	return $mensaje_procesado;
}

function procesar_mensaje3($mensaje)
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
	$mensaje_procesado = alphanumericAndSpace3($mensaje_procesado);
	
	return $mensaje_procesado;
}

//consultar el tipo de entidad
$sql_query_tipo_entidad_asociada="SELECT * FROM gioss_entidades_sector_salud WHERE codigo_entidad='$entidad_salud_usuario_actual'; ";
$resultado_query_tipo_entidad=$coneccionBD->consultar2($sql_query_tipo_entidad_asociada);
$tipo_entidad=$resultado_query_tipo_entidad[0]["cod_tipo_entidad"];
//fin consultar el tipo de entidad

//SELECTOR PRESTADOR-ASOCIADO-USUARIO
$prestador="";
//consultar_eapb(\"".$tipo_id."\",\"".$identificacion."\");
$prestador.="<select id='prestador' name='prestador' class='campo_azul' >";
$prestador.="<option value='none'>Seleccione un prestador de salud</option>";

if((intval($perfil_usuario_actual)==5 || intval($perfil_usuario_actual)==4 || intval($perfil_usuario_actual)==3)
   && (intval($tipo_entidad)!=6 && intval($tipo_entidad)!=7 && intval($tipo_entidad)!=8 && intval($tipo_entidad)!=10) )
{
	$sql_consulta_prestadores_asociados_eapb="SELECT ea.codigo_entidad,ea.nombre_de_la_entidad FROM ";
	$sql_consulta_prestadores_asociados_eapb.=" gioss_relacion_entre_entidades_salud re INNER JOIN gioss_entidades_sector_salud ea ON (re.entidad1 = ea.codigo_entidad) ";
	$sql_consulta_prestadores_asociados_eapb.=" WHERE re.entidad2='".$entidad_salud_usuario_actual."' ";
	$resultado_query_prestadores_asociados_eapb=$coneccionBD->consultar2($sql_consulta_prestadores_asociados_eapb);

	if(count($resultado_query_prestadores_asociados_eapb)>0)
	{
		foreach($resultado_query_prestadores_asociados_eapb as $prestador_asociado_eapb)
		{
			$prestador.="<option value='".$prestador_asociado_eapb['codigo_entidad']."' selected>".$prestador_asociado_eapb['nombre_de_la_entidad']."</option>";
		}
	}
}//si el tipo entidad es diferente de 6,7,8,10
else if((intval($perfil_usuario_actual)==1 || intval($perfil_usuario_actual)==2 || intval($perfil_usuario_actual)==5) && (intval($tipo_entidad)==6 || intval($tipo_entidad)==7 || intval($tipo_entidad)==8 || intval($tipo_entidad)==10) )
{
	$sql_query_tipo_entidad_asociada="SELECT * FROM gioss_entidades_sector_salud WHERE codigo_entidad='$entidad_salud_usuario_actual'; ";
	$resultado_query_tipo_entidad=$coneccionBD->consultar2($sql_query_tipo_entidad_asociada);

	if(count($resultado_query_tipo_entidad)>0)
	{
		foreach($resultado_query_tipo_entidad as $eapb_entidad)
		{
			$prestador.="<option value='".$eapb_entidad['codigo_entidad']."' selected>".$eapb_entidad['nombre_de_la_entidad']."</option>";
		}
	}
}//fin else if en caso de que el perfil sea 1 o 2 y el tipo de la entidad sea igual a 6,7,8,10


$prestador.="</select>";
//FIN

//SELECTOR EAPB-ASOCIADA_PRESTADOR_ASOCIADO_USUARIO
$eapb="";
$eapb.="<div id='div_eapb'>";
$eapb.="<select id='eapb' name='eapb' class='campo_azul' >";
$eapb.="<option value='none'>Seleccione un EAPB</option>";


if((intval($perfil_usuario_actual)==5 || intval($perfil_usuario_actual)==1 || intval($perfil_usuario_actual)==2) && (intval($tipo_entidad)==6 || intval($tipo_entidad)==7 || intval($tipo_entidad)==8 || intval($tipo_entidad)==10) )
{
	$sql_consulta_eapb_usuario_prestador="SELECT ea.codigo_entidad,ea.nombre_de_la_entidad FROM ";
	$sql_consulta_eapb_usuario_prestador.=" gioss_relacion_entre_entidades_salud re INNER JOIN gioss_entidades_sector_salud ea ON (re.entidad2 = ea.codigo_entidad) ";
	$sql_consulta_eapb_usuario_prestador.=" WHERE re.entidad1='".$entidad_salud_usuario_actual."' ";
	$sql_consulta_eapb_usuario_prestador.=";";

	$resultado_query_eapb_usuario=$coneccionBD->consultar2($sql_consulta_eapb_usuario_prestador);

	if(count($resultado_query_eapb_usuario)>0)
	{
		foreach($resultado_query_eapb_usuario as $eapb_prestador_usuario_res)
		{
			$eapb.="<option value='".$eapb_prestador_usuario_res['codigo_entidad']."' selected>".$eapb_prestador_usuario_res['nombre_de_la_entidad']."</option>";
		}
	}
}//fin if si el usuario es administrador y la entidad no es eapb, por lo tanto busca la eapb asociada a la entidad
else if((intval($perfil_usuario_actual)==3 || intval($perfil_usuario_actual)==4 || intval($perfil_usuario_actual)==5) && (intval($tipo_entidad)!=6 && intval($tipo_entidad)!=7 && intval($tipo_entidad)!=8 && intval($tipo_entidad)!=10) )
{
	$sql_query_tipo_entidad_asociada="SELECT * FROM gioss_entidades_sector_salud WHERE codigo_entidad='$entidad_salud_usuario_actual'; ";
	$resultado_query_tipo_entidad=$coneccionBD->consultar2($sql_query_tipo_entidad_asociada);

	if(count($resultado_query_tipo_entidad)>0)
	{
		foreach($resultado_query_tipo_entidad as $eapb_entidad)
		{
			$eapb.="<option value='".$eapb_entidad['codigo_entidad']."' selected>".$eapb_entidad['nombre_de_la_entidad']."</option>";
		}
	}
}//fin else si el usuario es de perfil 3 y la entidad es de tipo eapb


$eapb.="</select>";
$eapb.="</div>";
//FIN

//SELECTOR FECHAS CORTE
$selector_fechas_corte="";
$selector_fechas_corte.="<select id='fechas_corte' name='fechas_corte' class='campo_azul' onchange='seleccionar_periodo();'>";
$selector_fechas_corte.="<option value='3-31'>3-31</option>";
$selector_fechas_corte.="<option value='6-30'>6-30</option>";
$selector_fechas_corte.="<option value='9-30'>9-30</option>";
$selector_fechas_corte.="<option value='12-31'>12-31</option>";
$selector_fechas_corte.="</select>";
//FIN SELECTOR FECHAS CORTE

//SELECTOR PERIODO
$selector_periodo="";
$selector_periodo.="<select id='periodo' name='periodo' class='campo_azul' onchange='seleccionar_fecha_de_corte();'>";
$selector_periodo.="<option value='1'>Periodo 1</option>";
$selector_periodo.="<option value='2'>Periodo 2</option>";
$selector_periodo.="<option value='3'>Periodo 3</option>";
$selector_periodo.="<option value='4'>Periodo 4</option>";
$selector_periodo.="</select>";
//FIN SELECTOR PERIODO


//SELECTOR DEPARTAMENTO
$selector_departamento="";
$selector_departamento.="<select id='dpto' name='dpto' class='campo_azul' onchange='consultar_mpio();'>";
$selector_departamento.="<option value='none'>Seleccione un departamento</option>";

$query_departamentos="select * from gios_dpto ORDER BY nom_departamento;";
$res_dptos_query=$coneccionBD->consultar2($query_departamentos);

if(count($res_dptos_query)>0)
{
	foreach($res_dptos_query as $departamento)
	{
		$selector_departamento.="<option value='".$departamento['cod_departamento']."'>".$departamento['nom_departamento']."</option>";
	}
}

$selector_departamento.="</select>";
//FIN SELECTOR DEPARTAMENTO

//SELECTOR MUNICIPIO
$selector_municipio="";
$selector_municipio.="<div id='mpio_div'>";
$selector_municipio.="<select id='mpio' name='mpio' class='campo_azul' >";
$selector_municipio.="<option value='none'>Seleccione un municipio</option>";
$selector_municipio.="</select>";
$selector_municipio.="</div>";
//FIN SELECTOR MUNICIPIO

$hidde_nick_user="<input type='hidden' id='act_user' name='act_user' value='$nick_user'/>";

$smarty->assign("campo_dpto", $selector_departamento, true);
$smarty->assign("campo_mpio", $selector_municipio, true);

$smarty->assign("hidden_user", $hidde_nick_user, true);
$smarty->assign("campo_periodo", $selector_periodo, true);
$smarty->assign("campo_fechas_corte", $selector_fechas_corte, true);
$smarty->assign("campo_eapb", $eapb, true);
$smarty->assign("campo_prestador", $prestador, true);

$smarty->assign("mensaje_proceso", $mensaje, true);
$smarty->assign("mostrarResultado", $mostrarResultado, true);
$smarty->assign("nombre", $nombre, true);
$smarty->assign("menu", $menu, true);
$smarty->display('cargaRIPS.html.tpl');

//cargar errores desde bd y meterlos en arrays con keys que corresponden a la llave primaria de estos
$array_tipo_validacion_rips=array();
$array_grupo_validacion_rips=array();
$array_detalle_validacion_rips=array();

//ALTER TABLE gioss_tipo_validacion_rips RENAME TO gioss_tipo_inconsistencias;
//ALTER TABLE gioss_grupo_validacion_rips RENAME TO gioss_grupo_inconsistencias;
//ALTER TABLE gioss_detalle_validacion_rips RENAME TO gioss_detalle_inconsistencias_rips;
$query1_tipo_validacion="SELECT * FROM gioss_tipo_inconsistencias;";
$resultado_query1_tipo_validacion=$coneccionBD->consultar2($query1_tipo_validacion);
foreach($resultado_query1_tipo_validacion as $tipo_validacion)
{
	$array_tipo_validacion_rips[$tipo_validacion["tipo_validacion"]]=$tipo_validacion["descripcion_tipo_validacion"];
}
$query2_grupo_validacion="SELECT * FROM gioss_grupo_inconsistencias;";
$resultado_query2_grupo_validacion=$coneccionBD->consultar2($query2_grupo_validacion);
foreach($resultado_query2_grupo_validacion as $grupo_validacion)
{
	$array_grupo_validacion_rips[$grupo_validacion["grupo_validacion"]]=$grupo_validacion["descripcion_grupo_validacion"];
}
$query3_detalle_validacion="SELECT * FROM gioss_detalle_inconsistencias_rips;";
$resultado_query3_detalle_validacion=$coneccionBD->consultar2($query3_detalle_validacion);
foreach($resultado_query3_detalle_validacion as $detalle_validacion)
{
	$array_detalle_validacion_rips[$detalle_validacion["cod_tipo_validacion"]."_".$detalle_validacion["codigo_grupo_inconsistencia"]."_".$detalle_validacion["cod_detalle_inconsistencia"]]=$detalle_validacion["descripcion_detalle_inconsistencia"];
}	
//fin


date_default_timezone_set ("America/Bogota");
$fecha_actual = date('Y-m-d');
$tiempo_actual = date('H:i:s');

$array_fecha_para_string=explode("-",$fecha_actual);
$array_tiempo_para_string=explode(":",$tiempo_actual);
$string_tiempo_fecha=$array_fecha_para_string[0].$array_fecha_para_string[1].$array_fecha_para_string[2].$array_tiempo_para_string[0].$array_tiempo_para_string[1].$array_tiempo_para_string[2];

$rutaTemporal = '../TEMPORALES/';
$error_mensaje="";

$ruta_archivo_inconsistencias_rips="";
$se_genero_archivo_de_inconsistencias=false;
$verificacion_es_diferente_prestador_en_ct=false;
$verificacion_fecha_diferente_en_ct=false;
$verificacion_numero_remision=false;
$verificacion_ya_se_valido_con_exito=false;

$verificacion_es_diferente_eapb_en_af=false;

$mensaje_advertencia_tiempo="";
if(isset($_POST["nombre_archivo_rips"]) && strlen($_POST["nombre_archivo_rips"])==8)
{
	$mensaje_advertencia_tiempo .="Estimado usuario, se ha iniciado el proceso de validaci&oacuten del archivo,<br> lo que puede tomar varios minutos, dependiendo del volumen de registros.<br>";
	$mensaje_advertencia_tiempo .="Una vez validado, se genera el Logs de errores, el cual se enviar&aacute a su Correo electr&oacutenico o puede descargarlo directamente del aplicat&iacutevo.<br>";
	$mensaje_advertencia_tiempo .="Si la validaci&oacuten es exitosa, los datos se cargar&aacuten en la base de datos y se dar&aacute por aceptada la informaci&oacuten reportada<br>";
}
else if(isset($_POST["nombre_archivo_rips"]) && strlen($_POST["nombre_archivo_rips"])==35)
{
	$mensaje_advertencia_tiempo.="Estimado usuario, se ha iniciado el proceso de filtrado por localizaci&oacuten geografica, teniendo en cuenta el departamento y/o municipio seleccionado,<br>";
	$mensaje_advertencia_tiempo.=" despues de este proceso de filtrado se procedera a realizar la validaci&oacuten del archivo,<br>";
	$mensaje_advertencia_tiempo.=" lo que puede tomar varios minutos, dependiendo del volumen de registros.<br>";
	$mensaje_advertencia_tiempo.="Una vez validado, se generara el log de errores y una copia de los archivos rips enviados pero con los registros filtrados por la localizaci&oacuten seleccionada,<br>";
	$mensaje_advertencia_tiempo.=" el cual se enviar&aacute a su Correo electr&oacutenico o puede descargarlo directamente del aplicat&iacutevo.<br>";
	$mensaje_advertencia_tiempo.="Si la validaci&oacuten es exitosa, los datos se cargar&aacuten en la base de datos y se dar&aacute por aceptada la informaci&oacuten reportada<br>";
}
//PARTE QUE VALIDA LOS ARCHIVOS RIPS PRESTADOR
if(isset($_POST["accion"]) && isset($_POST["nombre_archivo_rips"]) && isset($_POST["date_ruta"])
   && $_POST["accion"]=="validar" && $_POST["date_ruta"]!="" && strlen($_POST["nombre_archivo_rips"])==8  && $_POST["tipo_archivo_rips"]=="ips")
{
	$string_date_ruta=$_POST["date_ruta"];
	$ruta_nueva="rips".$nick_user.$string_date_ruta;
	$nombre_archivo=$_POST["nombre_archivo_rips"];
	$cod_prestador=$_POST["prestador"];
	$cod_eapb=$_POST["eapb"];
	$coincidencias_num_rem=array();
	preg_match("/[0-9][0-9][0-9][0-9][0-9][0-9]/",$nombre_archivo, $coincidencias_num_rem);
	$numero_de_remision=$coincidencias_num_rem[0];
	$fecha_remision=$_POST["fecha_remision"];
	
	$fecha_remision_array=explode("/",$fecha_remision);
	$date_remision_bd=$fecha_remision_array[2]."-".$fecha_remision_array[0]."-".$fecha_remision_array[1];
	
	$ruta_archivo_inconsistencias_rips=$rutaTemporal."inconsistenciasRIPS_".$cod_prestador."_".$string_tiempo_fecha.".csv";
	$file_inconsistencias_rips = fopen($ruta_archivo_inconsistencias_rips, "w") or die("fallo la creacion del archivo");
	
	//array que verifica numero de facturas duplicados
	$numero_facturas=array();
	
	$numero_secuencia_actual="";
	$numero_secuencia_previa_si_fue_validado_con_exito="";
	
	//VERIFICA SI EL ARCHIVO YA ESTA SIENDO VALIDADO ACTUALMENTE
	$bool_se_esta_validando_en_este_momento=false;
	
	$query_verificacion_esta_siendo_procesado="";
	$query_verificacion_esta_siendo_procesado.=" SELECT * FROM gioss_rips_ips_esta_validando_actualmente ";
	$query_verificacion_esta_siendo_procesado.=" WHERE fecha_remision='".$date_remision_bd."' ";
	$query_verificacion_esta_siendo_procesado.=" AND codigo_eapb_reportadora='".$cod_prestador."' ";
	$query_verificacion_esta_siendo_procesado.=" AND nombre_archivo_ct='CT".$numero_de_remision."'  ";
	$query_verificacion_esta_siendo_procesado.=" ; ";
	$resultados_query_verificar_esta_siendo_procesado=$coneccionBD->consultar2($query_verificacion_esta_siendo_procesado);
	if(count($resultados_query_verificar_esta_siendo_procesado)>0)
	{
		$bool_se_esta_validando_en_este_momento=true;
	}
	
	//FIN VERIFICA SI EL ARCHIVO YA ESTA SIENDO VALIDADO ACTUALMENTE
	
	//VERIFICA SI FUE CARGADO CON EXITO
	$sql_query_verificar="";
	$sql_query_verificar.=" SELECT * FROM gioss_tabla_estado_informacion_rips ";
	$sql_query_verificar.=" WHERE fecha_remision='".$date_remision_bd."' ";
	$sql_query_verificar.=" AND codigo_eapb='".$cod_eapb."' ";
	$sql_query_verificar.=" AND codigo_prestador_servicios='".$cod_prestador."' ";
	$sql_query_verificar.=" AND nombre_del_archivo_ct='CT".$numero_de_remision."'  ";
	$sql_query_verificar.=" AND codigo_estado_informacion='1' ; ";
	$resultados_query_verificar=$coneccionBD->consultar2($sql_query_verificar);
	if(count($resultados_query_verificar)>0)
	{
		$verificacion_ya_se_valido_con_exito=true;
		$hubo_inconsistencias_en_ct=true;
		$fecha_validacion_exito_previa=$resultados_query_verificar[0]["fecha_validacion"];
		$numero_secuencia_previa_si_fue_validado_con_exito=$resultados_query_verificar[0]["numero_secuencia"];
		$error_mensaje.="Se&ntildeor usuario, el archivo que intenta validar ya se encuentra cargado con exito, en la fecha $fecha_validacion_exito_previa .<br>";
	}
	//FIN VERIFICA SI FUE CARGADO CON EXITO
	
	//VALIDACION DE LOS CAMPOS DE LOS ARCHIVOS RIPS
	$array_contador_registros_buenos=array();
	$array_contador_registros_malos=array();
	
	$hubo_inconsistencias_en_ct=false;
	$es_valido_nombre_archivo_ct=true;
	$ruta_archivo_ct="";
	$hubo_inconsistencias_en_af=false;
	$es_valido_nombre_archivo_af=true;
	$ruta_archivo_af="";
	$hubo_inconsistencias_en_us=false;
	$es_valido_nombre_archivo_us=true;
	$ruta_archivo_us="";
	$hubo_inconsistencias_en_ac=false;
	$es_valido_nombre_archivo_ac=true;
	$ruta_archivo_ac="";
	$hubo_inconsistencias_en_ah=false;
	$es_valido_nombre_archivo_ah=true;
	$ruta_archivo_ah="";
	$hubo_inconsistencias_en_ad=false;
	$es_valido_nombre_archivo_ad=true;
	$ruta_archivo_ad="";
	$hubo_inconsistencias_en_ap=false;
	$es_valido_nombre_archivo_ap=true;
	$ruta_archivo_ap="";
	$hubo_inconsistencias_en_ap=false;
	$es_valido_nombre_archivo_ap=true;
	$ruta_archivo_ap="";
	$hubo_inconsistencias_en_au=false;
	$es_valido_nombre_archivo_au=true;
	$ruta_archivo_au="";
	$hubo_inconsistencias_en_an=false;
	$es_valido_nombre_archivo_an=true;
	$ruta_archivo_an="";
	$hubo_inconsistencias_en_am=false;
	$es_valido_nombre_archivo_am=true;
	$ruta_archivo_am="";
	$hubo_inconsistencias_en_at=false;
	$es_valido_nombre_archivo_at=true;
	$ruta_archivo_at="";
	
	$ruta_archivo_ct = $rutaTemporal.$ruta_nueva."/"."CT".$numero_de_remision.".txt";
	$ruta_archivo_af = $rutaTemporal.$ruta_nueva."/"."AF".$numero_de_remision.".txt";
	$ruta_archivo_us = $rutaTemporal.$ruta_nueva."/"."US".$numero_de_remision.".txt";
	$ruta_archivo_ac = $rutaTemporal.$ruta_nueva."/"."AC".$numero_de_remision.".txt";
	$ruta_archivo_ah = $rutaTemporal.$ruta_nueva."/"."AH".$numero_de_remision.".txt";
	$ruta_archivo_ad = $rutaTemporal.$ruta_nueva."/"."AD".$numero_de_remision.".txt";
	$ruta_archivo_ap = $rutaTemporal.$ruta_nueva."/"."AP".$numero_de_remision.".txt";
	$ruta_archivo_au = $rutaTemporal.$ruta_nueva."/"."AU".$numero_de_remision.".txt";
	$ruta_archivo_an = $rutaTemporal.$ruta_nueva."/"."AN".$numero_de_remision.".txt";
	$ruta_archivo_am = $rutaTemporal.$ruta_nueva."/"."AM".$numero_de_remision.".txt";
	$ruta_archivo_at = $rutaTemporal.$ruta_nueva."/"."AT".$numero_de_remision.".txt";
	
	$array_rutas_rips=array();
	$array_rutas_rips[]=$ruta_archivo_ct;
	$array_rutas_rips[]=$ruta_archivo_af;
	$array_rutas_rips[]=$ruta_archivo_us;
	if(isset($_POST["AC".$numero_de_remision."_valido"]))
	{
		$array_rutas_rips[]=$ruta_archivo_ac;
	}
	if(isset($_POST["AH".$numero_de_remision."_valido"]))
	{
		$array_rutas_rips[]=$ruta_archivo_ah;
	}
	if(isset($_POST["AD".$numero_de_remision."_valido"]))
	{
		$array_rutas_rips[]=$ruta_archivo_ad;
	}
	if(isset($_POST["AU".$numero_de_remision."_valido"]))
	{
		$array_rutas_rips[]=$ruta_archivo_au;
	}
	if(isset($_POST["AP".$numero_de_remision."_valido"]))
	{
		$array_rutas_rips[]=$ruta_archivo_ap;
	}
	if(isset($_POST["AN".$numero_de_remision."_valido"]))
	{
		$array_rutas_rips[]=$ruta_archivo_an;
	}
	if(isset($_POST["AM".$numero_de_remision."_valido"]))
	{
		$array_rutas_rips[]=$ruta_archivo_am;
	}
	if(isset($_POST["AT".$numero_de_remision."_valido"]))
	{
		$array_rutas_rips[]=$ruta_archivo_at;
	}
	
	$error_mostrar_bd="";
	
	//OBTIENE EL NUMERO DE SECUENCIA INCREMENTAL Y LO ASIGNA EN LA TABLA NUMERO SECUENCIA RIPS
	if($verificacion_ya_se_valido_con_exito==false && $bool_se_esta_validando_en_este_momento==false)
	{
		$numero_secuencia_actual=$utilidades->obtenerSecuencia("gioss_numero_secuencia_rips_3374");
		$sql_query_inserta_seq="";
		$sql_query_inserta_seq.=" INSERT INTO gioss_numero_secuencia_archivos_rips ";
		$sql_query_inserta_seq.=" ( ";
		$sql_query_inserta_seq.=" fecha_de_corte, ";
		$sql_query_inserta_seq.=" codigo_eapb, ";
		$sql_query_inserta_seq.=" codigo_prestador_servicios_salud, ";
		$sql_query_inserta_seq.=" numero_remision, ";
		$sql_query_inserta_seq.=" numero_secuencia ";
		$sql_query_inserta_seq.=" ) ";
		$sql_query_inserta_seq.=" VALUES ";
		$sql_query_inserta_seq.=" ( ";
		$sql_query_inserta_seq.=" '".$date_remision_bd."', ";
		$sql_query_inserta_seq.=" '".$cod_eapb."', ";
		$sql_query_inserta_seq.=" '".$cod_prestador."', ";
		$sql_query_inserta_seq.=" '".$numero_de_remision."', ";
		$sql_query_inserta_seq.=" '".$numero_secuencia_actual."' ";
		$sql_query_inserta_seq.=" ) ";
		$sql_query_inserta_seq.=" ; ";
		$error_bd_seq="";
		$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_query_inserta_seq, $error_bd_seq);
		if($error_bd_seq!="")
		{
			$error_mostrar_bd.=$error_bd_seq."<br>";
		}
	}//fin if si no fue validado con exito
	else
	{
		$numero_secuencia_actual=$numero_secuencia_previa_si_fue_validado_con_exito;		
	}
	//FIN OBTIENE EL NUMERO DE SECUENCIA INCREMENTAL Y LO ASIGNA EN LA TABLA NUMERO SECUENCIA RIPS
	
	//INICIO VALIDACION RIPS PROVENIENTES DE PRESTADOR
	if($verificacion_ya_se_valido_con_exito==false && $bool_se_esta_validando_en_este_momento==false)
	{
		//ARCHIVO CT
		$hubo_inconsistencias_en_ct=false;
		$es_valido_nombre_archivo_ct=true;
		$array_contador_registros_buenos["CT"]=0;
		$array_contador_registros_malos["CT"]=0;
		if(file_exists($ruta_archivo_ct))
		{
									
			// parte donde valida-ct_control
			if($es_valido_nombre_archivo_ct)
			{
				$mensaje_errores_ct="";
				$lineas_del_archivo = count(file($ruta_archivo_ct)); 
				$file_ct = fopen($ruta_archivo_ct, 'r') or exit("No se pudo abrir el archivo");
				
				$nlinea=0;
				while (!feof($file_ct)) 
				{
					$linea_tmp = fgets($file_ct);
					$linea= explode("\n", $linea_tmp)[0];
					$campos = explode(",", $linea);
					
					//verifica el prestador del documento ct con el asociado
					if(isset($campos[0]))
					{				
						if(str_replace(" ","",$campos[0])!=str_replace(" ","",$cod_prestador) && $verificacion_es_diferente_prestador_en_ct==false)
						{
							$verificacion_es_diferente_prestador_en_ct=true;
							$hubo_inconsistencias_en_ct=true;
							$error_mensaje.="El prestador indicado en el archivo de control (CT) no corresponde al prestador asociado.<br>";
						}
					}//fin if
					
					//varifica si la primera fecha en el archivo ct es igual a la fecha de remision registrada
					if(isset($campos[1]))
					{
						try
						{
							$fecha_en_ct=str_replace(" ","",$campos[1]);
							$array_fecha_en_ct=explode("/",$fecha_en_ct);
							
							$array_fecha_remision=explode("/",$fecha_remision);
							$date_reportada=$array_fecha_en_ct[2]."-".$array_fecha_en_ct[1]."-".$array_fecha_en_ct[0];
							$date_remision=$array_fecha_remision[2]."-".$array_fecha_remision[0]."-".$array_fecha_remision[1];
							$interval = date_diff(date_create($date_reportada),date_create($date_remision));
							$tiempo= (float)($interval->format("%r%a"));
							if($tiempo!=0 && $verificacion_fecha_diferente_en_ct==false)
							{
								$verificacion_fecha_diferente_en_ct=true;
								$hubo_inconsistencias_en_ct=true;
								$error_mensaje.="La fecha indicada en el archivo de control (CT) no corresponde a la fecha de remisi&oacuten registrada.<br>";
							}
						}//fin try
						catch(Exception $e)
						{
							$verificacion_fecha_diferente_en_ct=true;
							$hubo_inconsistencias_en_ct=true;
							$error_mensaje.="La fecha indicada en el archivo de control (CT) o registrada no poseen un formato valido.<br>";
						}//fin catch
					}//fin if
					
					//verifica numero de remision
					if(isset($campos[2]))
					{
						$numero_remision_del_ct=substr($campos[2],2,strlen($campos[2]));
						if($numero_de_remision!=$numero_remision_del_ct && $verificacion_numero_remision==false)
						{	
							$verificacion_numero_remision=true;
							$hubo_inconsistencias_en_ct=true;
							$error_mensaje.="El numero de remisi&oacuten indicado en el archivo de control (CT) no corresponde al numero de remisi&oacuten archivo de control ".$numero_remision_del_ct.".<br>";
						}
					}
					
					//pasa a validar los campos
					if(count($campos)==4)
					{
												
						$array_resultados_validacion=validar_ct($campos,$nlinea,$array_tipo_validacion_rips,$array_grupo_validacion_rips,$array_detalle_validacion_rips,$cod_prestador,"CT".$numero_de_remision,$array_rutas_rips,$fecha_remision,$cod_eapb,$numero_de_remision,$ruta_nueva);
											
						if($hubo_inconsistencias_en_ct==false)
						{
							$hubo_inconsistencias_en_ct=$array_resultados_validacion["error"];
						}
						
						if($array_resultados_validacion["error"]==false)
						{
							$array_contador_registros_buenos["CT"]++;
						}
						if($array_resultados_validacion["error"]==true)
						{
							$array_contador_registros_malos["CT"]++;
						}
						
						//escribe los errores
						$mensaje_errores_ct=$array_resultados_validacion["mensaje"];
						$array_mensajes_errores_campos=explode("|",$mensaje_errores_ct);
						foreach($array_mensajes_errores_campos as $msg_error)
						{
							fwrite($file_inconsistencias_rips, $msg_error."\n");
						}
						//fin escribe los errores
						
						$mensaje_contador_errores="revisando linea ".($nlinea+1)." de $lineas_del_archivo del archivo CT,";
						$mensaje_contador_errores.=" tiene ".$array_contador_registros_buenos["CT"]." registros buenos ";
						$mensaje_contador_errores.=" y ".$array_contador_registros_malos["CT"]." registros malos ";
						$html_del_mensaje="";
						$html_del_mensaje.="<table>";
						$html_del_mensaje.="<tr>";
						$html_del_mensaje.="<td colspan=\'2\'>";
						$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
						$html_del_mensaje.="</td>";
						$html_del_mensaje.="</tr>";
						$html_del_mensaje.="<tr>";
						$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
						$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
						$html_del_mensaje.="</td>";
						$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
						$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_contador_errores."</div><div id=\'errores_bd_div\'></div>";
						$html_del_mensaje.="</td>";
						$html_del_mensaje.="</tr>";
						$html_del_mensaje.="</table>";
						if(connection_aborted()==false)
						{
							if(connection_aborted()==false)
							{
								echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
								ob_flush();
								flush();
							}
						}
					}//fin if verifica longitud
					else
					{
						//$error_longitud=$array_tipo_validacion_rips["01"].",Numero de campos,el numero de campos en la linea no corresponden al numero permitido,"."CT".$numero_de_remision.",-1,".($nlinea+1);
						
						$error_longitud=$array_tipo_validacion_rips["03"].",".$array_grupo_validacion_rips["0301"].",".$array_detalle_validacion_rips["03_0301_030101"].","."CT".$numero_de_remision.",-1,".($nlinea+1);
						if($hubo_inconsistencias_en_ct==false)
						{
							$hubo_inconsistencias_en_ct=true;
						}
						fwrite($file_inconsistencias_rips, $error_longitud."\n");
					}//fin else longitud no apropiada
					$nlinea++;
				}
				fclose($file_ct);
			}
			//fin parte valida archivo
			
			if($hubo_inconsistencias_en_ct)
			{
				$se_genero_archivo_de_inconsistencias=true;
				if($verificacion_es_diferente_prestador_en_ct==false && $verificacion_fecha_diferente_en_ct==false && $verificacion_numero_remision==false)
				{
					$error_mensaje.="Hubo inconsistencias en el archivo de control (CT).<br>";
				}
			}
		}//fin if ct_control
		else
		{
			$error_mensaje.="El archivo de control (CT) no existe.<br>";
			$hubo_inconsistencias_en_ct=true;
		}
		//FIN ARCHIVO CT
		
		$condicion_inconcistencias_bloqueo_para_continuar=($hubo_inconsistencias_en_ct==false) ;
		
		//subida a tabla registros rechazados si los registros de
		// CT  estan correctos en un 100%, para los demas
		//se sube teniendo en cuenta cada registo por separado solo para rechazados
		$condicion_bloqueo_subida_bd_rechazados=$hubo_inconsistencias_en_ct==false;
		if($condicion_bloqueo_subida_bd_rechazados)
		{
			//BORRA LOS RECHAZADOS ANTERIORES
			$sql_query_delete="";
			$sql_query_delete.="DELETE FROM gioss_archivo_rechazado_CT ";
			$sql_query_delete.=" WHERE fecha_remision='".$date_remision_bd."' ";
			$sql_query_delete.=" AND codigo_eapb='".$cod_eapb."' ";
			$sql_query_delete.=" AND codigo_prestador_servicios_salud='".$cod_prestador."' ";
			$sql_query_delete.=" AND numero_remision='".$numero_de_remision."' ; ";
			$error_bd_del="";
			$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_query_delete, $error_bd_del);
			
			$sql_query_delete="";
			$sql_query_delete.="DELETE FROM gioss_archivo_rechazado_AF ";
			$sql_query_delete.=" WHERE fecha_remision='".$date_remision_bd."' ";
			$sql_query_delete.=" AND codigo_eapb='".$cod_eapb."' ";
			$sql_query_delete.=" AND codigo_prestador_servicios_salud='".$cod_prestador."' ";
			$sql_query_delete.=" AND numero_remision='".$numero_de_remision."' ; ";
			$error_bd_del="";
			$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_query_delete, $error_bd_del);
			
			$sql_query_delete="";
			$sql_query_delete.="DELETE FROM gioss_archivo_rechazado_US ";
			$sql_query_delete.=" WHERE fecha_remision='".$date_remision_bd."' ";
			$sql_query_delete.=" AND codigo_eapb='".$cod_eapb."' ";
			$sql_query_delete.=" AND codigo_prestador_servicios_salud='".$cod_prestador."' ";
			$sql_query_delete.=" AND numero_remision='".$numero_de_remision."' ; ";
			$error_bd_del="";
			$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_query_delete, $error_bd_del);
			
			$sql_query_delete="";
			$sql_query_delete.="DELETE FROM gioss_archivo_rechazado_AC ";
			$sql_query_delete.=" WHERE fecha_remision='".$date_remision_bd."' ";
			$sql_query_delete.=" AND codigo_eapb='".$cod_eapb."' ";
			$sql_query_delete.=" AND codigo_prestador_servicios_salud='".$cod_prestador."' ";
			$sql_query_delete.=" AND numero_remision='".$numero_de_remision."' ; ";
			$error_bd_del="";
			$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_query_delete, $error_bd_del);
			
			$sql_query_delete="";
			$sql_query_delete.="DELETE FROM gioss_archivo_rechazado_AH ";
			$sql_query_delete.=" WHERE fecha_remision='".$date_remision_bd."' ";
			$sql_query_delete.=" AND codigo_eapb='".$cod_eapb."' ";
			$sql_query_delete.=" AND codigo_prestador_servicios_salud='".$cod_prestador."' ";
			$sql_query_delete.=" AND numero_remision='".$numero_de_remision."' ; ";
			$error_bd_del="";
			$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_query_delete, $error_bd_del);
			
			$sql_query_delete="";
			$sql_query_delete.="DELETE FROM gioss_archivo_rechazado_AD ";
			$sql_query_delete.=" WHERE fecha_remision='".$date_remision_bd."' ";
			$sql_query_delete.=" AND codigo_eapb='".$cod_eapb."' ";
			$sql_query_delete.=" AND codigo_prestador_servicios_salud='".$cod_prestador."' ";
			$sql_query_delete.=" AND numero_remision='".$numero_de_remision."' ; ";
			$error_bd_del="";
			$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_query_delete, $error_bd_del);
			
			$sql_query_delete="";
			$sql_query_delete.="DELETE FROM gioss_archivo_rechazado_AP ";
			$sql_query_delete.=" WHERE fecha_remision='".$date_remision_bd."' ";
			$sql_query_delete.=" AND codigo_eapb='".$cod_eapb."' ";
			$sql_query_delete.=" AND codigo_prestador_servicios_salud='".$cod_prestador."' ";
			$sql_query_delete.=" AND numero_remision='".$numero_de_remision."' ; ";
			$error_bd_del="";
			$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_query_delete, $error_bd_del);
			
			$sql_query_delete="";
			$sql_query_delete.="DELETE FROM gioss_archivo_rechazado_AU ";
			$sql_query_delete.=" WHERE fecha_remision='".$date_remision_bd."' ";
			$sql_query_delete.=" AND codigo_eapb='".$cod_eapb."' ";
			$sql_query_delete.=" AND codigo_prestador_servicios_salud='".$cod_prestador."' ";
			$sql_query_delete.=" AND numero_remision='".$numero_de_remision."' ; ";
			$error_bd_del="";
			$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_query_delete, $error_bd_del);
			
			$sql_query_delete="";
			$sql_query_delete.="DELETE FROM gioss_archivo_rechazado_AN ";
			$sql_query_delete.=" WHERE fecha_remision='".$date_remision_bd."' ";
			$sql_query_delete.=" AND codigo_eapb='".$cod_eapb."' ";
			$sql_query_delete.=" AND codigo_prestador_servicios_salud='".$cod_prestador."' ";
			$sql_query_delete.=" AND numero_remision='".$numero_de_remision."' ; ";
			$error_bd_del="";
			$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_query_delete, $error_bd_del);
			
			$sql_query_delete="";
			$sql_query_delete.="DELETE FROM gioss_archivo_rechazado_AM ";
			$sql_query_delete.=" WHERE fecha_remision='".$date_remision_bd."' ";
			$sql_query_delete.=" AND codigo_eapb='".$cod_eapb."' ";
			$sql_query_delete.=" AND codigo_prestador_servicios_salud='".$cod_prestador."' ";
			$sql_query_delete.=" AND numero_remision='".$numero_de_remision."' ; ";
			$error_bd_del="";
			$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_query_delete, $error_bd_del);
			
			$sql_query_delete="";
			$sql_query_delete.="DELETE FROM gioss_archivo_rechazado_AT ";
			$sql_query_delete.=" WHERE fecha_remision='".$date_remision_bd."' ";
			$sql_query_delete.=" AND codigo_eapb='".$cod_eapb."' ";
			$sql_query_delete.=" AND codigo_prestador_servicios_salud='".$cod_prestador."' ";
			$sql_query_delete.=" AND numero_remision='".$numero_de_remision."' ; ";
			$error_bd_del="";
			$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_query_delete, $error_bd_del);
			
			//FIN BORRA LOS RECHAZADOS ANTERIORES
			
			$query_insert_esta_siendo_procesado="";
			$query_insert_esta_siendo_procesado.=" INSERT INTO gioss_rips_ips_esta_validando_actualmente ";
			$query_insert_esta_siendo_procesado.=" ( ";
			$query_insert_esta_siendo_procesado.=" codigo_eapb_reportadora,";
			$query_insert_esta_siendo_procesado.=" nombre_archivo_ct,";
			$query_insert_esta_siendo_procesado.=" fecha_remision,";
			$query_insert_esta_siendo_procesado.=" fecha_validacion,";
			$query_insert_esta_siendo_procesado.=" hora_validacion,";
			$query_insert_esta_siendo_procesado.=" nick_usuario,";
			$query_insert_esta_siendo_procesado.=" archivos_que_ha_validado_hasta_el_momento";
			$query_insert_esta_siendo_procesado.=" ) ";
			$query_insert_esta_siendo_procesado.=" VALUES ";
			$query_insert_esta_siendo_procesado.=" ( ";
			$query_insert_esta_siendo_procesado.=" '".$cod_prestador."',  ";
			$query_insert_esta_siendo_procesado.=" 'CT".$numero_de_remision."',  ";
			$query_insert_esta_siendo_procesado.=" '".$date_remision_bd."',  ";
			$query_insert_esta_siendo_procesado.=" '".$fecha_actual."',  ";
			$query_insert_esta_siendo_procesado.=" '".$tiempo_actual."',  ";
			$query_insert_esta_siendo_procesado.=" '".$nick_user."',  ";
			$query_insert_esta_siendo_procesado.=" 'CT".$numero_de_remision."'  ";
			$query_insert_esta_siendo_procesado.=" ) ";
			$query_insert_esta_siendo_procesado.=" ; ";
			$error_bd="";
			$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($query_insert_esta_siendo_procesado, $error_bd);
			if($error_bd!="")
			{
				$error_mostrar_bd.="ERROR AL establecer esta siendo procesado  en  CT, ".$error_bd."<br>";
				if(connection_aborted()==false)
				{
					echo "<script>alert('error al establecer esta siendo procesado  CT eapb rips ".procesar_mensaje($error_bd)."  ".procesar_mensaje($query_insert_esta_siendo_procesado)."');</script>";
				}
			}
			
			//falso no hay error, true error en query bd
			$bool_hubo_error_query=false;
			
			
			//CT
			if($es_valido_nombre_archivo_ct  && $bool_hubo_error_query==false && (file_exists($ruta_archivo_ct)==true && $rutaTemporal!=trim($ruta_archivo_ct)))
			{
				//llave primaria fecha_de_rechazo,codigo_eapb,codigo_prestador_servicios_salud,fila,numero_remision
						
				
				
				$lineas_del_archivo = count(file($ruta_archivo_ct)); 
				$file_ct = fopen($ruta_archivo_ct, 'r') or exit("No se pudo abrir el archivo");
				
				$nlinea=0;
				while (!feof($file_ct) && $bool_hubo_error_query==false) 
				{
					
					
					$linea_tmp = fgets($file_ct);
					$linea= explode("\n", $linea_tmp)[0];
					$campos = explode(",", $linea);
					
					//verifica el prestador del documento ct con el asociado
					if(count($campos)==4)
					{
											
						$array_fecha_reportada=explode("/",$campos[1]);
						$date_reportada_bd=$array_fecha_reportada[2]."-".$array_fecha_reportada[1]."-".$array_fecha_reportada[0];
						
						//se pone igual a 1 debido a que el ct debe estar correcto 
						$estado_validado_registro=1;
						
						$sql_rechazados="";
						$sql_rechazados.="INSERT INTO gioss_archivo_rechazado_CT ";
						$sql_rechazados.=" ( ";
						$sql_rechazados.="fecha_de_rechazo ,";
						$sql_rechazados.="codigo_eapb ,";
						$sql_rechazados.="codigo_prestador_servicios_salud ,";
						$sql_rechazados.="fecha_remision ,";
						$sql_rechazados.="nombre_archivo_reportado ,";
						$sql_rechazados.="total_registros  ,";
						$sql_rechazados.="fila ,";
						$sql_rechazados.="numero_remision, ";
						$sql_rechazados.="numero_secuencia, ";
						$sql_rechazados.="hora_validacion, ";
						$sql_rechazados.="estado_validado ";
						$sql_rechazados.=" ) ";
						$sql_rechazados.=" VALUES ";
						$sql_rechazados.=" ( ";
						$sql_rechazados.=" '".$fecha_actual."', ";
						$sql_rechazados.=" '".$cod_eapb."', ";
						$sql_rechazados.=" '".$cod_prestador."', ";
						$sql_rechazados.=" '".$date_remision_bd."', ";
						$sql_rechazados.=" '".procesar_mensaje2($campos[2])."', ";
						$sql_rechazados.=" '".procesar_mensaje2($campos[3])."', ";
						$sql_rechazados.=" '".$nlinea."', ";
						$sql_rechazados.=" '".$numero_de_remision."', ";
						$sql_rechazados.=" '".$numero_secuencia_actual."', ";
						$sql_rechazados.=" '".$tiempo_actual."', ";
						$sql_rechazados.=" '".$estado_validado_registro."' ";
						$sql_rechazados.=" ) ";
						$sql_rechazados.=" ; ";
						
						$error_bd="";
						$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_rechazados, $error_bd);
						if($error_bd!="")
						{
							$error_mostrar_bd.="ERROR AL SUBIR RECHAZADOS EN CT, ".$error_bd."<br>";
						}
					}//fin if
					$nlinea++;
				}//fin while
				fclose($file_ct);
			}//fin if si el archivo ct existe
			//FIN CT
						
			
			
		}//fin if
		
		
		//ARCHIVO AF
		//la variable numero facturas ya se encarga de revisar los duplicados
		$hubo_inconsistencias_en_af=false;
		$es_valido_nombre_archivo_af=true;
		$array_contador_registros_buenos["AF"]=0;
		$array_contador_registros_malos["AF"]=0;
		if(file_exists($ruta_archivo_af) && $condicion_inconcistencias_bloqueo_para_continuar)
		{
						
			// parte donde valida-af
			if($es_valido_nombre_archivo_af)
			{
				$mensaje_errores_af="";
				$lineas_del_archivo = count(file($ruta_archivo_af)); 
				$file_af = fopen($ruta_archivo_af, 'r') or exit("No se pudo abrir el archivo");
				
				$nlinea=0;
				while (!feof($file_af)) 
				{
					$linea_tmp = fgets($file_af);
					$linea= explode("\n", $linea_tmp)[0];
					$campos = explode(",", $linea);
					
					//verifica la entidad administradora a reportar indicada en el archivo de factura AF
					if(isset($campos[8]))
					{				
						if(str_replace(" ","",$campos[8])!=str_replace(" ","",$cod_eapb) && $verificacion_es_diferente_eapb_en_af==false)
						{
							$verificacion_es_diferente_eapb_en_af=true;
							$hubo_inconsistencias_en_af=true;
							$error_mensaje.="La entidad administradora en el archivo de control (AF) no corresponde al codigo de la EAPB a reportar.<br>";
						}
					}//fin if
					
					if(count($campos)==17)
					{
						
						
						$array_resultados_validacion=validar_af($campos,$nlinea,$array_tipo_validacion_rips,$array_grupo_validacion_rips,$array_detalle_validacion_rips,$cod_prestador,"AF".$numero_de_remision,$cod_eapb,$numero_de_remision,$fecha_remision,$array_rutas_rips,$numero_facturas,$ruta_nueva);
						
						if($hubo_inconsistencias_en_af==false)
						{
							$hubo_inconsistencias_en_af=$array_resultados_validacion["error"];
						}
						
						$estado_validado_registro=0;
						if($array_resultados_validacion["error"]==false)
						{
							$array_contador_registros_buenos["AF"]++;
							$estado_validado_registro=1;
						}
						if($array_resultados_validacion["error"]==true)
						{
							$array_contador_registros_malos["AF"]++;
							$estado_validado_registro=2;
						}
						
						if($condicion_bloqueo_subida_bd_rechazados)
						{
							
							$sql_rechazados="";
							$sql_rechazados.="INSERT INTO gioss_archivo_rechazado_AF ";
							$sql_rechazados.=" ( ";
							$sql_rechazados.="fecha_de_rechazo ,";//1
							$sql_rechazados.="fecha_remision ,";
							$sql_rechazados.="codigo_eapb ,";//2
							$sql_rechazados.="codigo_archivo ,";//3
							$sql_rechazados.="codigo_prestador_servicios_salud ,";//4
							$sql_rechazados.="nombre_prestador_servicios_salud ,";//5
							$sql_rechazados.="tipo_identificacion_prestador ,";//6
							$sql_rechazados.="numero_identificacion_prestador ,";//7
							$sql_rechazados.="numero_factura ,";//8
							$sql_rechazados.="fecha_expedicion_factura ,";//9
							$sql_rechazados.="fecha_inicio_factura ,";//10
							$sql_rechazados.="fecha_final_factura ,";//11
							$sql_rechazados.="codigo_entidad_eapb ,";//12
							$sql_rechazados.="nombre_entidad_eapb ,";//13
							$sql_rechazados.="numero_contrato ,";//15
							$sql_rechazados.="plan_beneficio ,";//16
							$sql_rechazados.="numero_poliza ,";//17
							$sql_rechazados.="valor_pago_compartido ,";//18
							$sql_rechazados.="valor_comision ,";//19
							$sql_rechazados.="valor_descuentos ,";//20
							$sql_rechazados.="valor_neto_a_pagar ,";//21
							$sql_rechazados.="fila ,";//22
							$sql_rechazados.="numero_remision, ";//23
							$sql_rechazados.="numero_secuencia, ";
							$sql_rechazados.="estado_validado ";
							$sql_rechazados.=" ) ";
							$sql_rechazados.=" VALUES ";
							$sql_rechazados.=" ( ";
							$sql_rechazados.=" '".$fecha_actual."', ";
							$sql_rechazados.=" '".$date_remision_bd."', ";
							$sql_rechazados.=" '".$cod_eapb."', ";
							$sql_rechazados.=" 'AF".$numero_de_remision."', ";
							$sql_rechazados.=" '".$cod_prestador."', ";
							$cont_tmp=1;
							while($cont_tmp<count($campos))
							{
								if(procesar_mensaje2($campos[$cont_tmp])=="")
								{
									$sql_rechazados.=" '0', ";
								}
								else
								{
									$sql_rechazados.=" '".procesar_mensaje2($campos[$cont_tmp])."', ";
								}
								$cont_tmp++;
							}
							$sql_rechazados.=" '".$nlinea."', ";
							$sql_rechazados.=" '".$numero_de_remision."', ";
							$sql_rechazados.=" '".$numero_secuencia_actual."', ";
							$sql_rechazados.=" '".$estado_validado_registro."' ";
							$sql_rechazados.=" ) ";
							$sql_rechazados.=" ; ";
							
							$error_bd="";
							$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_rechazados, $error_bd);
							if($error_bd!="")
							{
								$error_mostrar_bd.="ERROR AL SUBIR RECHAZADOS EN AF, ".$error_bd."<br>";
							}
						}//fin if
						
						//escribe los errores
						$mensaje_errores_af=$array_resultados_validacion["mensaje"];
						$array_mensajes_errores_campos=explode("|",$mensaje_errores_af);
						foreach($array_mensajes_errores_campos as $msg_error)
						{
							fwrite($file_inconsistencias_rips, $msg_error."\n");
						}
						//fin escribe los errores
						
						$mensaje_contador_errores="revisando linea ".($nlinea+1)." de $lineas_del_archivo del archivo AF,";
						$mensaje_contador_errores.=" tiene ".$array_contador_registros_buenos["AF"]." registros buenos ";
						$mensaje_contador_errores.=" y ".$array_contador_registros_malos["AF"]." registros malos ";
						$html_del_mensaje="";
						$html_del_mensaje.="<table>";
						$html_del_mensaje.="<tr>";
						$html_del_mensaje.="<td colspan=\'2\'>";
						$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
						$html_del_mensaje.="</td>";
						$html_del_mensaje.="</tr>";
						$html_del_mensaje.="<tr>";
						$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
						$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
						$html_del_mensaje.="</td>";
						$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
						$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_contador_errores."</div><div id=\'errores_bd_div\'></div>";
						$html_del_mensaje.="</td>";
						$html_del_mensaje.="</tr>";
						$html_del_mensaje.="</table>";
						if(connection_aborted()==false)
						{							
							echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
							ob_flush();
							flush();							
						}
						$query_update_esta_siendo_procesado="";
						$query_update_esta_siendo_procesado.=" UPDATE gioss_rips_ips_esta_validando_actualmente ";
						$query_update_esta_siendo_procesado.=" SET archivos_que_ha_validado_hasta_el_momento='".$mensaje_contador_errores."' ";
						$query_update_esta_siendo_procesado.=" WHERE fecha_remision='".$date_remision_bd."' ";
						$query_update_esta_siendo_procesado.=" AND codigo_eapb_reportadora='".$cod_prestador."' ";
						$query_update_esta_siendo_procesado.=" AND nombre_archivo_ct='CT".$numero_de_remision."'  ";
						$query_update_esta_siendo_procesado.=" ; ";
						$error_bd="";
						$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($query_update_esta_siendo_procesado, $error_bd);
						
					}//fin if verifica numero campos
					else
					{
						//$error_longitud=$array_tipo_validacion_rips["01"].",Numero de campos,el numero de campos en la linea no corresponden al numero permitido,"."AF".$numero_de_remision.",-1,".($nlinea+1);
						$error_longitud=$array_tipo_validacion_rips["03"].",".$array_grupo_validacion_rips["0301"].",".$array_detalle_validacion_rips["03_0301_030101"].","."AF".$numero_de_remision.",-1,".($nlinea+1);
			
						if($hubo_inconsistencias_en_af==false)
						{
							$hubo_inconsistencias_en_af=true;
						}
						fwrite($file_inconsistencias_rips, $error_longitud."\n");
					}//fin else longitud no apropiada
					$nlinea++;
				}
				fclose($file_af);
			}
			//fin parte valida archivo
			
			if($hubo_inconsistencias_en_af)
			{
				$se_genero_archivo_de_inconsistencias=true;
				$error_mensaje.="Hubo inconsistencias en el archivo de facturas (AF).<br>";
			}
		}
		else if($hubo_inconsistencias_en_ct==false)
		{
			$error_mensaje.="El archivo de facturas (AF) no existe.<br>";
			$hubo_inconsistencias_en_af=true;
		}
		//FIN ARCHIVO AF
		
		//ARCHIVO US
		$array_afiliados_duplicados=array();
		$hubo_inconsistencias_en_us=false;
		$es_valido_nombre_archivo_us=true;
		$array_contador_registros_buenos["US"]=0;
		$array_contador_registros_malos["US"]=0;
		if(file_exists($ruta_archivo_us) && $condicion_inconcistencias_bloqueo_para_continuar)
		{
			// parte donde valida-us
			if($es_valido_nombre_archivo_us)
			{
				$mensaje_errores_us="";
				$lineas_del_archivo = count(file($ruta_archivo_us)); 
				$file_us = fopen($ruta_archivo_us, 'r') or exit("No se pudo abrir el archivo");
				
				$nlinea=0;
				while (!feof($file_us)) 
				{
					$linea_tmp = fgets($file_us);
					$linea= explode("\n", $linea_tmp)[0];
					$campos = explode(",", $linea);
					if(count($campos)==14)
					{
						
						
						$array_resultados_validacion=validar_us($campos,$nlinea,$array_tipo_validacion_rips,$array_grupo_validacion_rips,$array_detalle_validacion_rips,$cod_prestador,"US".$numero_de_remision,$cod_eapb,$numero_de_remision,$fecha_remision,$array_rutas_rips,$ruta_nueva,$array_afiliados_duplicados);
						
						if($hubo_inconsistencias_en_us==false)
						{
							$hubo_inconsistencias_en_us=$array_resultados_validacion["error"];
						}
						
						$estado_validado_registro=0;
						if($array_resultados_validacion["error"]==false)
						{
							$array_contador_registros_buenos["US"]++;
							$estado_validado_registro=1;
						}
						if($array_resultados_validacion["error"]==true)
						{
							$array_contador_registros_malos["US"]++;
							$estado_validado_registro=2;
						}
						
						if($condicion_bloqueo_subida_bd_rechazados)
						{					
							$sql_rechazados="";
							$sql_rechazados.="INSERT INTO gioss_archivo_rechazado_US ";
							$sql_rechazados.=" ( ";
							$sql_rechazados.="fecha_de_rechazo ,";
							$sql_rechazados.="fecha_remision ,";
							$sql_rechazados.="codigo_eapb ,";
							$sql_rechazados.="codigo_prestador_servicios_salud ,";
							$sql_rechazados.="codigo_archivo ,";					
							$sql_rechazados.="tipo_identificacion_usuario ,";
							$sql_rechazados.="numero_identificacion_usuario ,";
							$sql_rechazados.="codigo_entidad_eapb ,";
							$sql_rechazados.="codigo_tipo_usuario ,";
							$sql_rechazados.="primer_apellido ,";
							$sql_rechazados.="segundo_apellido ,";
							$sql_rechazados.="primer_nombre ,";
							$sql_rechazados.="segundo_nombre ,";
							$sql_rechazados.="edad ,";
							$sql_rechazados.="unidad_medida_edad ,";
							$sql_rechazados.="sexo ,";
							$sql_rechazados.="codigo_departamento_residencia ,";
							$sql_rechazados.="codigo_municipio_residencia ,";
							$sql_rechazados.="codigo_zona_residencia ,";
							$sql_rechazados.="fila ,";
							$sql_rechazados.="numero_remision, ";
							$sql_rechazados.="numero_secuencia, ";
							$sql_rechazados.="estado_validado ";
							$sql_rechazados.=" ) ";
							$sql_rechazados.=" VALUES ";
							$sql_rechazados.=" ( ";
							$sql_rechazados.=" '".$fecha_actual."', ";
							$sql_rechazados.=" '".$date_remision_bd."', ";
							$sql_rechazados.=" '".$cod_eapb."', ";
							$sql_rechazados.=" '".$cod_prestador."', ";					
							$sql_rechazados.=" 'US".$numero_de_remision."', ";
							$cont_tmp=0;
							while($cont_tmp<count($campos))
							{
								if(procesar_mensaje2($campos[$cont_tmp])=="")
								{
									$sql_rechazados.=" '0', ";
								}
								else
								{
									$sql_rechazados.=" '".procesar_mensaje2($campos[$cont_tmp])."', ";
								}
								$cont_tmp++;
							}
							$sql_rechazados.=" '".$nlinea."', ";
							$sql_rechazados.=" '".$numero_de_remision."', ";
							$sql_rechazados.=" '".$numero_secuencia_actual."', ";
							$sql_rechazados.=" '".$estado_validado_registro."' ";
							$sql_rechazados.=" ) ";
							$sql_rechazados.=" ; ";
							
							$error_bd="";
							$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_rechazados, $error_bd);
							if($error_bd!="")
							{
								$error_mostrar_bd.="ERROR AL SUBIR RECHAZADOS EN US, ".$error_bd."<br>";
							}
						}//fin
						
						//escribe los errores
						$mensaje_errores_us=$array_resultados_validacion["mensaje"];
						$array_mensajes_errores_campos=explode("|",$mensaje_errores_us);
						foreach($array_mensajes_errores_campos as $msg_error)
						{
							fwrite($file_inconsistencias_rips, $msg_error."\n");
						}
						//fin escribe los errores
						
						$mensaje_contador_errores="revisando linea ".($nlinea+1)." de $lineas_del_archivo del archivo US,";
						$mensaje_contador_errores.=" tiene ".$array_contador_registros_buenos["US"]." registros buenos ";
						$mensaje_contador_errores.=" y ".$array_contador_registros_malos["US"]." registros malos ";
						$html_del_mensaje="";
						$html_del_mensaje.="<table>";
						$html_del_mensaje.="<tr>";
						$html_del_mensaje.="<td colspan=\'2\'>";
						$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
						$html_del_mensaje.="</td>";
						$html_del_mensaje.="</tr>";
						$html_del_mensaje.="<tr>";
						$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
						$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
						$html_del_mensaje.="</td>";
						$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
						$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_contador_errores."</div><div id=\'errores_bd_div\'></div>";
						$html_del_mensaje.="</td>";
						$html_del_mensaje.="</tr>";
						$html_del_mensaje.="</table>";
						if(connection_aborted()==false)
						{
							echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
							ob_flush();
							flush();							
						}
						$query_update_esta_siendo_procesado="";
						$query_update_esta_siendo_procesado.=" UPDATE gioss_rips_ips_esta_validando_actualmente ";
						$query_update_esta_siendo_procesado.=" SET archivos_que_ha_validado_hasta_el_momento='".$mensaje_contador_errores."' ";
						$query_update_esta_siendo_procesado.=" WHERE fecha_remision='".$date_remision_bd."' ";
						$query_update_esta_siendo_procesado.=" AND codigo_eapb_reportadora='".$cod_prestador."' ";
						$query_update_esta_siendo_procesado.=" AND nombre_archivo_ct='CT".$numero_de_remision."'  ";
						$query_update_esta_siendo_procesado.=" ; ";
						$error_bd="";
						$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($query_update_esta_siendo_procesado, $error_bd);
					}//fin verifica numero campos
					else
					{
						//$error_longitud=$array_tipo_validacion_rips["01"].",Numero de campos,el numero de campos en la linea no corresponden al numero permitido,"."US".$numero_de_remision.",-1,".($nlinea+1);
						
						$error_longitud=$array_tipo_validacion_rips["03"].",".$array_grupo_validacion_rips["0301"].",".$array_detalle_validacion_rips["03_0301_030101"].","."US".$numero_de_remision.",-1,".($nlinea+1);
						if($hubo_inconsistencias_en_us==false)
						{
							$hubo_inconsistencias_en_us=true;
						}
						fwrite($file_inconsistencias_rips, $error_longitud."\n");
					}//fin else longitud no apropiada
					$nlinea++;
				}
				fclose($file_us);
			}
			//fin parte valida archivo
			
			if($hubo_inconsistencias_en_us)
			{
				$se_genero_archivo_de_inconsistencias=true;
				$error_mensaje.="Hubo inconsistencias en el archivo de usuarios (US).<br>";
			}
		}
		else if($hubo_inconsistencias_en_ct==false)
		{
			$error_mensaje.="El archivo de usuarios (US) no existe.<br>";
			$hubo_inconsistencias_en_us=true;
		}
		//FIN ARCHIVO US
			
		//ARCHIVO AC
		$hubo_inconsistencias_en_ac=false;
		$es_valido_nombre_archivo_ac=true;
		$array_contador_registros_buenos["AC"]=0;
		$array_contador_registros_malos["AC"]=0;
		if(file_exists($ruta_archivo_ac) && $condicion_inconcistencias_bloqueo_para_continuar)
		{
			// parte donde valida-ac
			if($es_valido_nombre_archivo_ac)
			{
				$mensaje_errores_ac="";
				$lineas_del_archivo = count(file($ruta_archivo_ac)); 
				$file_ac = fopen($ruta_archivo_ac, 'r') or exit("No se pudo abrir el archivo");
				
				
				
				$nlinea=0;
				while (!feof($file_ac)) 
				{
					$linea_tmp = fgets($file_ac);
					$linea= explode("\n", $linea_tmp)[0];
					$campos = explode(",", $linea);
					if(count($campos)==17)
					{							
						$array_resultados_validacion=validar_ac($campos,$nlinea,$array_tipo_validacion_rips,$array_grupo_validacion_rips,$array_detalle_validacion_rips,$cod_prestador,"AC".$numero_de_remision,$cod_eapb,$numero_de_remision,$fecha_remision,$array_rutas_rips,$ruta_nueva,$array_afiliados_duplicados);
						if($hubo_inconsistencias_en_ac==false)
						{
							$hubo_inconsistencias_en_ac=$array_resultados_validacion["error"];
						}
						
						$estado_validado_registro=0;
						if($array_resultados_validacion["error"]==false)
						{
							$array_contador_registros_buenos["AC"]++;
							$estado_validado_registro=1;
						}
						else
						{
							$array_contador_registros_malos["AC"]++;
							$estado_validado_registro=2;
						}
						
						//SE SUBE A BD EL REGISTRO CON ERRORES APARTE
						if($condicion_bloqueo_subida_bd_rechazados)
						{
							
							//OBTENER SEXO Y EDAD DE LAS TABLAS DE AFILIADOS
							$tipo_id_usuario_pes=trim($campos[2]);
							$numero_id_usuario_pes=trim($campos[3]);
							
							//bd existe afiliado, el tipo regimen camp3ak4 1=rc,2=rs,3=rs,4,5=rc
							
							
							$query_edad_sexo_bd="SELECT * FROM gioss_afiliados_eapb_rc WHERE  tipo_id_afiliado= '".$tipo_id_usuario_pes."' AND id_afiliado = '".$numero_id_usuario_pes."' ;";
							$resultados_query_edad_sexo=$coneccionBD->consultar2($query_edad_sexo_bd);
							
							$fecha_nacimiento_1="";
							$sexo_1="";
							if(is_array($resultados_query_edad_sexo))
							{
								$fecha_nacimiento_1=$resultados_query_edad_sexo[0]["fecha_nacimiento"];
								$sexo_1=$resultados_query_edad_sexo[0]["sexo"];
							}
							
						
							$query_edad_sexo_bd="SELECT * FROM gioss_afiliados_regimen_subsidiado WHERE tipo_id_afiliado = '".$tipo_id_usuario_pes."' AND id_afiliado = '".$numero_id_usuario_pes."' ;";
							$resultados_query_edad_sexo=$coneccionBD->consultar2($query_edad_sexo_bd);
							
							$fecha_nacimiento_2="";
							$sexo_2="";
							if(is_array($resultados_query_edad_sexo))
							{
								$fecha_nacimiento_2=$resultados_query_edad_sexo[0]["fecha_nacimiento"];
								$sexo_2=$resultados_query_edad_sexo[0]["sexo"];
							}
						
							$query_edad_sexo_bd="SELECT * FROM gioss_afiliados_eapb_mp WHERE tipo_id_afiliado = '".$tipo_id_usuario_pes."' AND id_afiliado = '".$numero_id_usuario_pes."' ;";
							$resultados_query_edad_sexo=$coneccionBD->consultar2($query_edad_sexo_bd);
							
							$fecha_nacimiento_3="";
							$sexo_3="";
							if(is_array($resultados_query_edad_sexo))
							{
								$fecha_nacimiento_3=$resultados_query_edad_sexo[0]["fecha_nacimiento"];
								$sexo_3=$resultados_query_edad_sexo[0]["sexo"];
							}
							
							$query_edad_sexo_bd="SELECT * FROM gioss_afiliados_eapb_rp WHERE tipo_id_afiliado = '".$tipo_id_usuario_pes."' AND id_afiliado = '".$numero_id_usuario_pes."' ;";
							$resultados_query_edad_sexo=$coneccionBD->consultar2($query_edad_sexo_bd);
							
							$fecha_nacimiento_4="";
							$sexo_4="";
							if(is_array($resultados_query_edad_sexo))
							{
								$fecha_nacimiento_4=$resultados_query_edad_sexo[0]["fecha_nacimiento"];
								$sexo_4=$resultados_query_edad_sexo[0]["sexo"];
							}
							
							$edad_afiliado=0;
							$fecha_nacimiento_definitiva="";
							$array_fecha_definitiva=array();
							$sexo_afiliado="A";
							
							if($sexo_1==$sexo_2
							   && $sexo_1==$sexo_3
							   && $sexo_1==$sexo_4
							   && ($sexo_1=="M"||$sexo_1=="F")					   
							   )
							{
								$sexo_afiliado=$sexo_1;
							}
							else
							{
								$array_comprobacion_sexo=array();
								$array_indice_sexo=array();
								if(!isset($array_comprobacion_sexo[$sexo_1]) && ($sexo_1=="M"||$sexo_1=="F") )
								{
									$array_comprobacion_sexo[$sexo_1]=1;
								}
								else if(($sexo_1=="M"||$sexo_1=="F"))
								{
									$array_comprobacion_sexo[$sexo_1]++;
								}
								
								if(!isset($array_comprobacion_sexo[$sexo_2]) && ($sexo_2=="M"||$sexo_2=="F"))
								{
									$array_comprobacion_sexo[$sexo_2]=1;
								}
								else if($sexo_2=="M"||$sexo_2=="F")
								{
									$array_comprobacion_sexo[$sexo_2]++;
								}
								
								if(!isset($array_comprobacion_sexo[$sexo_3]) && ($sexo_3=="M"||$sexo_3=="F"))
								{
									$array_comprobacion_sexo[$sexo_3]=1;
								}
								else if($sexo_3=="M"||$sexo_3=="F")
								{
									$array_comprobacion_sexo[$sexo_3]++;
								}
								
								if(!isset($array_comprobacion_sexo[$sexo_4]) && ($sexo_4=="M"||$sexo_4=="F"))
								{
									$array_comprobacion_sexo[$sexo_4]=1;
								}
								else if($sexo_4=="M"||$sexo_4=="F")
								{
									$array_comprobacion_sexo[$sexo_4]++;
								}
								
								if(isset($array_comprobacion_sexo["M"]) && isset($array_comprobacion_sexo["F"]))
								{
									if($array_comprobacion_sexo["M"]>$array_comprobacion_sexo["F"])
									{
										$sexo_afiliado="M";
									}
									else
									{
										$sexo_afiliado="F";
									}
								}//fin if
								else if(isset($array_comprobacion_sexo["M"]))
								{
									$sexo_afiliado="M";
								}
								else if(isset($array_comprobacion_sexo["F"]))
								{
									$sexo_afiliado="F";
								}
								
								
							}//fin if comprobacion sexo
							
							$array_fn_1=explode("-",$fecha_nacimiento_1);
							//checkdate mes dia year
							$bool_es_date_fn_1=false;
							if(count($array_fn_1)==3)
							{
								$bool_es_date_fn_1=checkdate($array_fn_1[1],$array_fn_1[2],$array_fn_1[0]);
							}
							
							$array_fn_2=explode("-",$fecha_nacimiento_2);
							//checkdate mes dia year
							$bool_es_date_fn_2=false;
							if(count($array_fn_2)==3)
							{
								$bool_es_date_fn_2=checkdate($array_fn_2[1],$array_fn_2[2],$array_fn_2[0]);
							}
							
							$array_fn_3=explode("-",$fecha_nacimiento_3);
							//checkdate mes dia year
							$bool_es_date_fn_3=false;
							if(count($array_fn_3)==3)
							{
								$bool_es_date_fn_3=checkdate($array_fn_3[1],$array_fn_3[2],$array_fn_3[0]);
							}
							
							$array_fn_4=explode("-",$fecha_nacimiento_4);
							//checkdate mes dia year
							$bool_es_date_fn_4=false;
							if(count($array_fn_4)==3)
							{
								$bool_es_date_fn_4=checkdate($array_fn_4[1],$array_fn_4[2],$array_fn_4[0]);
							}
							
							if($fecha_nacimiento_1==$fecha_nacimiento_2
							   && $fecha_nacimiento_1==$fecha_nacimiento_3
							   && $fecha_nacimiento_1==$fecha_nacimiento_4
							   && $bool_es_date_fn_1==true)
							{
								$fecha_nacimiento_definitiva=$fecha_nacimiento_1;
							}
							else
							{
								$array_comprobacion_fecha_nacimiento=array();
								$array_indice_fecha_nacimiento=array();
								if(!isset($array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1]) && $bool_es_date_fn_1==true )
								{
									$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1]=1;
									$array_indice_fecha_nacimiento[0]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1];
								}
								else if($bool_es_date_fn_1==true)
								{
									$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1]++;
									$array_indice_fecha_nacimiento[0]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1];
								}
								else
								{
									$array_indice_fecha_nacimiento[0]=0;
								}
								
								if(!isset($array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2]) && $bool_es_date_fn_2==true)
								{
									$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2]=1;
									$array_indice_fecha_nacimiento[1]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2];
								}
								else if($bool_es_date_fn_2==true)
								{
									$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2]++;
									$array_indice_fecha_nacimiento[1]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2];
								}
								else
								{
									$array_indice_fecha_nacimiento[1]=0;
								}
								
								if(!isset($array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3]) && $bool_es_date_fn_3==true)
								{
									$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3]=1;
									$array_indice_fecha_nacimiento[2]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3];
								}
								else if($bool_es_date_fn_3==true)
								{
									$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3]++;
									$array_indice_fecha_nacimiento[2]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3];
								}
								else
								{
									$array_indice_fecha_nacimiento[2]=0;
								}
								
								if(!isset($array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4]) && $bool_es_date_fn_4==true)
								{
									$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4]=1;
									$array_indice_fecha_nacimiento[3]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4];
								}
								else if($bool_es_date_fn_4==true)
								{
									$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4]++;
									$array_indice_fecha_nacimiento[3]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4];
								}
								else
								{
									$array_indice_fecha_nacimiento[3]=0;
								}
								$indice_mayor_actual=0;
								$cont_fn=0;
								while($cont_fn<count($array_indice_fecha_nacimiento))
								{
									$cont_fn_2=$cont_fn+1;
									while($cont_fn_2<count($array_indice_fecha_nacimiento))
									{
										if($array_indice_fecha_nacimiento[$cont_fn_2]>$array_indice_fecha_nacimiento[$cont_fn])
										{
											$indice_mayor_actual=$cont_fn_2;
										}
										$cont_fn_2++;
									}
									$cont_fn++;
								}
								if($indice_mayor_actual==0)
								{
									$fecha_nacimiento_definitiva=$fecha_nacimiento_1;
									$array_fecha_definitiva=$array_fn_1;
								}
								if($indice_mayor_actual==1)
								{
									$fecha_nacimiento_definitiva=$fecha_nacimiento_2;
									$array_fecha_definitiva=$array_fn_2;
								}
								if($indice_mayor_actual==2)
								{
									$fecha_nacimiento_definitiva=$fecha_nacimiento_3;
									$array_fecha_definitiva=$array_fn_3;
								}
								if($indice_mayor_actual==3)
								{
									$fecha_nacimiento_definitiva=$fecha_nacimiento_4;
									$array_fecha_definitiva=$array_fn_4;
								}
							}//fin else
							
							//calculo edad con la fecha definitiva
							
							
							
							//fin calculo edad
							try
							{
								if($fecha_nacimiento_definitiva!="")
								{
									$interval_edad = date_diff(date_create($fecha_nacimiento_definitiva),date_create($date_remision_bd));
									$edad_afiliado= intval((float)($interval_edad->format("%r%a"))/365);
									
								}
								else
								{
									$edad_afiliado=999;
								}
							}
							catch(Exception $exc_edad_afiliado)
							{
								if(connection_aborted()==false)
								{
									echo "<script>alert('".procesar_mensaje($e->getMessage())."');</script>";
								}
								
							}
							//FIN OBTENER SEXO Y EDAD DE LAS TABLAS DE AFILIADOS
							
							$sql_rechazados="";
							$sql_rechazados.="INSERT INTO gioss_archivo_rechazado_AC ";
							$sql_rechazados.=" ( ";
							$sql_rechazados.="fecha_de_rechazo ,";
							$sql_rechazados.="fecha_remision ,";
							$sql_rechazados.="codigo_eapb ,";
							$sql_rechazados.="codigo_archivo ,";
							$sql_rechazados.="numero_factura ,";					
							$sql_rechazados.="codigo_prestador_servicios_salud ,";
							$sql_rechazados.="tipo_identificacion_usuario ,";
							$sql_rechazados.="numero_identificacion_usuario ,";;
							$sql_rechazados.="fecha_atencion ,";
							$sql_rechazados.="numero_autorizacion ,";
							$sql_rechazados.="codigo_cups_consulta ,";
							$sql_rechazados.="finalidad_consulta ,";
							$sql_rechazados.="causa_externa_consulta ,";
							$sql_rechazados.="codigo_diagnostico_principal ,";
							$sql_rechazados.="codigo_relacionado_1 ,";
							$sql_rechazados.="codigo_relacionado_2 ,";
							$sql_rechazados.="codigo_relacionado_3 ,";
							$sql_rechazados.="tipo_diagnostico_principal ,";
							$sql_rechazados.="valor_consulta ,";
							$sql_rechazados.="valor_cuota_moderadora ,";
							$sql_rechazados.="valor_neto_pagado ,";
							$sql_rechazados.="fila ,";
							$sql_rechazados.="numero_remision, ";
							$sql_rechazados.="numero_secuencia, ";
							//columnas nuevas para facilitar reportes
							$sql_rechazados.="edad_years_afiliado, ";
							$sql_rechazados.="sexo_afiliado, ";
							$sql_rechazados.="estado_validado ";
							$sql_rechazados.=" ) ";
							$sql_rechazados.=" VALUES ";
							$sql_rechazados.=" ( ";
							$sql_rechazados.=" '".$fecha_actual."', ";
							$sql_rechazados.=" '".$date_remision_bd."', ";
							$sql_rechazados.=" '".$cod_eapb."', ";				
							$sql_rechazados.=" 'AC".$numero_de_remision."', ";
							$cont_tmp=0;
							while($cont_tmp<count($campos))
							{
								if($cont_tmp!=1)
								{
									if(procesar_mensaje2($campos[$cont_tmp])=="")
									{
										$sql_rechazados.=" '0', ";
									}
									else
									{
										$sql_rechazados.=" '".procesar_mensaje2($campos[$cont_tmp])."', ";
									}
								}
								else
								{
									$sql_rechazados.=" '".$cod_prestador."', ";
								}
								$cont_tmp++;
							}
							$sql_rechazados.=" '".$nlinea."', ";
							$sql_rechazados.=" '".$numero_de_remision."', ";
							$sql_rechazados.=" '".$numero_secuencia_actual."', ";
							//campos adicionales para facilitar reportes
							$sql_rechazados.=" '".$edad_afiliado."', ";
							$sql_rechazados.=" '".$sexo_afiliado."', ";
							$sql_rechazados.=" '".$estado_validado_registro."' ";
							$sql_rechazados.=" ) ";
							$sql_rechazados.=" ; ";
							
							$error_bd="";
							$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_rechazados, $error_bd);
							if($error_bd!="")
							{
								$error_mostrar_bd.="ERROR AL SUBIR RECHAZADOS EN AC, ".$error_bd."<br>";
							}
							
						}//fin
						//FIN SE SUBE A BD EL REGISTRO CON ERRORES APARTE
						
						//escribe los errores
						$mensaje_errores_ac=$array_resultados_validacion["mensaje"];
						$array_mensajes_errores_campos=explode("|",$mensaje_errores_ac);
						foreach($array_mensajes_errores_campos as $msg_error)
						{
							fwrite($file_inconsistencias_rips, $msg_error."\n");
						}
						//fin escribe los errores
						
						$mensaje_contador_errores="revisando linea ".($nlinea+1)." de $lineas_del_archivo del archivo AC,";
						$mensaje_contador_errores.=" tiene ".$array_contador_registros_buenos["AC"]." registros buenos ";
						$mensaje_contador_errores.=" y ".$array_contador_registros_malos["AC"]." registros malos ";
						$html_del_mensaje="";
						$html_del_mensaje.="<table>";
						$html_del_mensaje.="<tr>";
						$html_del_mensaje.="<td colspan=\'2\'>";
						$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
						$html_del_mensaje.="</td>";
						$html_del_mensaje.="</tr>";
						$html_del_mensaje.="<tr>";
						$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
						$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
						$html_del_mensaje.="</td>";
						$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
						$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_contador_errores."</div><div id=\'errores_bd_div\'></div>";
						$html_del_mensaje.="</td>";
						$html_del_mensaje.="</tr>";
						$html_del_mensaje.="</table>";
						if(connection_aborted()==false)
						{
							echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
							ob_flush();
							flush();
						}
						$query_update_esta_siendo_procesado="";
						$query_update_esta_siendo_procesado.=" UPDATE gioss_rips_ips_esta_validando_actualmente ";
						$query_update_esta_siendo_procesado.=" SET archivos_que_ha_validado_hasta_el_momento='".$mensaje_contador_errores."' ";
						$query_update_esta_siendo_procesado.=" WHERE fecha_remision='".$date_remision_bd."' ";
						$query_update_esta_siendo_procesado.=" AND codigo_eapb_reportadora='".$cod_prestador."' ";
						$query_update_esta_siendo_procesado.=" AND nombre_archivo_ct='CT".$numero_de_remision."'  ";
						$query_update_esta_siendo_procesado.=" ; ";
						$error_bd="";
						$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($query_update_esta_siendo_procesado, $error_bd);
					}//fin verifica numero campos
					else
					{
						//$error_longitud=$array_tipo_validacion_rips["01"].",Numero de campos,el numero de campos en la linea no corresponden al numero permitido,"."AC".$numero_de_remision.",-1,".($nlinea+1);
						
						$error_longitud=$array_tipo_validacion_rips["03"].",".$array_grupo_validacion_rips["0301"].",".$array_detalle_validacion_rips["03_0301_030101"].","."AC".$numero_de_remision.",-1,".($nlinea+1);
						if($hubo_inconsistencias_en_ac==false)
						{
							$hubo_inconsistencias_en_ac=true;
						}
						fwrite($file_inconsistencias_rips, $error_longitud."\n");
					}//fin else longitud no apropiada
					$nlinea++;
				}
				fclose($file_ac);
			}
			//fin parte valida archivo
			
			if($hubo_inconsistencias_en_ac)
			{
				
				$se_genero_archivo_de_inconsistencias=true;
				$error_mensaje.="Hubo inconsistencias en el archivo de consulta (AC).<br>";
			}
		}
		else if(isset($_POST["AC".$numero_de_remision."_valido"]) && $condicion_inconcistencias_bloqueo_para_continuar)
		{
			$error_mensaje.="El archivo de consulta (AC) no existe.<br>";
			$hubo_inconsistencias_en_af=true;
		}
		//FIN ARCHIVO AC
		
		//ARCHIVO AH 
		$hubo_inconsistencias_en_ah=false;
		$es_valido_nombre_archivo_ah=true;
		$array_contador_registros_buenos["AH"]=0;
		$array_contador_registros_malos["AH"]=0;
		if(file_exists($ruta_archivo_ah) && $condicion_inconcistencias_bloqueo_para_continuar)
		{			
			// parte donde valida-ah
			if($es_valido_nombre_archivo_ah)
			{
				$mensaje_errores_ah="";
				$lineas_del_archivo = count(file($ruta_archivo_ah)); 
				$file_ah = fopen($ruta_archivo_ah, 'r') or exit("No se pudo abrir el archivo");
				
				$nlinea=0;
				while (!feof($file_ah)) 
				{
					$linea_tmp = fgets($file_ah);
					$linea= explode("\n", $linea_tmp)[0];
					$campos = explode(",", $linea);
					if(count($campos)==19)
					{
												
						$array_resultados_validacion=validar_ah($campos,$nlinea,$array_tipo_validacion_rips,$array_grupo_validacion_rips,$array_detalle_validacion_rips,$cod_prestador,"AH".$numero_de_remision,$cod_eapb,$numero_de_remision,$fecha_remision,$array_rutas_rips,$ruta_nueva,$array_afiliados_duplicados);
						if($hubo_inconsistencias_en_ah==false)
						{
							$hubo_inconsistencias_en_ah=$array_resultados_validacion["error"];
						}
						
						
						$estado_validado_registro=0;
						if($array_resultados_validacion["error"]==false)
						{
							$array_contador_registros_buenos["AH"]++;
							$estado_validado_registro=1;
						}
						else
						{
							$array_contador_registros_malos["AH"]++;
							$estado_validado_registro=2;
						}
						
						//SE SUBE A BD EL REGISTRO CON ERRORES APARTE
						if($condicion_bloqueo_subida_bd_rechazados)
						{
							//OBTENER SEXO Y EDAD DE LAS TABLAS DE AFILIADOS
							$tipo_id_usuario_pes=trim($campos[2]);
							$numero_id_usuario_pes=trim($campos[3]);
							
							//bd existe afiliado, el tipo regimen camp3ak4 1=rc,2=rs,3=rs,4,5=rc
							
							
							$query_edad_sexo_bd="SELECT * FROM gioss_afiliados_eapb_rc WHERE  tipo_id_afiliado= '".$tipo_id_usuario_pes."' AND id_afiliado = '".$numero_id_usuario_pes."' ;";
							$resultados_query_edad_sexo=$coneccionBD->consultar2($query_edad_sexo_bd);
							
							$fecha_nacimiento_1="";
							$sexo_1="";
							if(is_array($resultados_query_edad_sexo))
							{
								$fecha_nacimiento_1=$resultados_query_edad_sexo[0]["fecha_nacimiento"];
								$sexo_1=$resultados_query_edad_sexo[0]["sexo"];
							}
							
						
							$query_edad_sexo_bd="SELECT * FROM gioss_afiliados_regimen_subsidiado WHERE tipo_id_afiliado = '".$tipo_id_usuario_pes."' AND id_afiliado = '".$numero_id_usuario_pes."' ;";
							$resultados_query_edad_sexo=$coneccionBD->consultar2($query_edad_sexo_bd);
							
							$fecha_nacimiento_2="";
							$sexo_2="";
							if(is_array($resultados_query_edad_sexo))
							{
								$fecha_nacimiento_2=$resultados_query_edad_sexo[0]["fecha_nacimiento"];
								$sexo_2=$resultados_query_edad_sexo[0]["sexo"];
							}
						
							$query_edad_sexo_bd="SELECT * FROM gioss_afiliados_eapb_mp WHERE tipo_id_afiliado = '".$tipo_id_usuario_pes."' AND id_afiliado = '".$numero_id_usuario_pes."' ;";
							$resultados_query_edad_sexo=$coneccionBD->consultar2($query_edad_sexo_bd);
							
							$fecha_nacimiento_3="";
							$sexo_3="";
							if(is_array($resultados_query_edad_sexo))
							{
								$fecha_nacimiento_3=$resultados_query_edad_sexo[0]["fecha_nacimiento"];
								$sexo_3=$resultados_query_edad_sexo[0]["sexo"];
							}
							
							$query_edad_sexo_bd="SELECT * FROM gioss_afiliados_eapb_rp WHERE tipo_id_afiliado = '".$tipo_id_usuario_pes."' AND id_afiliado = '".$numero_id_usuario_pes."' ;";
							$resultados_query_edad_sexo=$coneccionBD->consultar2($query_edad_sexo_bd);
							
							$fecha_nacimiento_4="";
							$sexo_4="";
							if(is_array($resultados_query_edad_sexo))
							{
								$fecha_nacimiento_4=$resultados_query_edad_sexo[0]["fecha_nacimiento"];
								$sexo_4=$resultados_query_edad_sexo[0]["sexo"];
							}
							
							$edad_afiliado=0;
							$fecha_nacimiento_definitiva="";
							$array_fecha_definitiva=array();
							$sexo_afiliado="A";
							
							if($sexo_1==$sexo_2
							   && $sexo_1==$sexo_3
							   && $sexo_1==$sexo_4
							   && ($sexo_1=="M"||$sexo_1=="F")					   
							   )
							{
								$sexo_afiliado=$sexo_1;
							}
							else
							{
								$array_comprobacion_sexo=array();
								$array_indice_sexo=array();
								if(!isset($array_comprobacion_sexo[$sexo_1]) && ($sexo_1=="M"||$sexo_1=="F") )
								{
									$array_comprobacion_sexo[$sexo_1]=1;
								}
								else if(($sexo_1=="M"||$sexo_1=="F"))
								{
									$array_comprobacion_sexo[$sexo_1]++;
								}
								
								if(!isset($array_comprobacion_sexo[$sexo_2]) && ($sexo_2=="M"||$sexo_2=="F"))
								{
									$array_comprobacion_sexo[$sexo_2]=1;
								}
								else if($sexo_2=="M"||$sexo_2=="F")
								{
									$array_comprobacion_sexo[$sexo_2]++;
								}
								
								if(!isset($array_comprobacion_sexo[$sexo_3]) && ($sexo_3=="M"||$sexo_3=="F"))
								{
									$array_comprobacion_sexo[$sexo_3]=1;
								}
								else if($sexo_3=="M"||$sexo_3=="F")
								{
									$array_comprobacion_sexo[$sexo_3]++;
								}
								
								if(!isset($array_comprobacion_sexo[$sexo_4]) && ($sexo_4=="M"||$sexo_4=="F"))
								{
									$array_comprobacion_sexo[$sexo_4]=1;
								}
								else if($sexo_4=="M"||$sexo_4=="F")
								{
									$array_comprobacion_sexo[$sexo_4]++;
								}
								
								if(isset($array_comprobacion_sexo["M"]) && isset($array_comprobacion_sexo["F"]))
								{
									if($array_comprobacion_sexo["M"]>$array_comprobacion_sexo["F"])
									{
										$sexo_afiliado="M";
									}
									else
									{
										$sexo_afiliado="F";
									}
								}//fin if
								else if(isset($array_comprobacion_sexo["M"]))
								{
									$sexo_afiliado="M";
								}
								else if(isset($array_comprobacion_sexo["F"]))
								{
									$sexo_afiliado="F";
								}
								
								
							}//fin if comprobacion sexo
							
							$array_fn_1=explode("-",$fecha_nacimiento_1);
							//checkdate mes dia year
							$bool_es_date_fn_1=false;
							if(count($array_fn_1)==3)
							{
								$bool_es_date_fn_1=checkdate($array_fn_1[1],$array_fn_1[2],$array_fn_1[0]);
							}
							
							$array_fn_2=explode("-",$fecha_nacimiento_2);
							//checkdate mes dia year
							$bool_es_date_fn_2=false;
							if(count($array_fn_2)==3)
							{
								$bool_es_date_fn_2=checkdate($array_fn_2[1],$array_fn_2[2],$array_fn_2[0]);
							}
							
							$array_fn_3=explode("-",$fecha_nacimiento_3);
							//checkdate mes dia year
							$bool_es_date_fn_3=false;
							if(count($array_fn_3)==3)
							{
								$bool_es_date_fn_3=checkdate($array_fn_3[1],$array_fn_3[2],$array_fn_3[0]);
							}
							
							$array_fn_4=explode("-",$fecha_nacimiento_4);
							//checkdate mes dia year
							$bool_es_date_fn_4=false;
							if(count($array_fn_4)==3)
							{
								$bool_es_date_fn_4=checkdate($array_fn_4[1],$array_fn_4[2],$array_fn_4[0]);
							}
							
							if($fecha_nacimiento_1==$fecha_nacimiento_2
							   && $fecha_nacimiento_1==$fecha_nacimiento_3
							   && $fecha_nacimiento_1==$fecha_nacimiento_4
							   && $bool_es_date_fn_1==true)
							{
								$fecha_nacimiento_definitiva=$fecha_nacimiento_1;
							}
							else
							{
								$array_comprobacion_fecha_nacimiento=array();
								$array_indice_fecha_nacimiento=array();
								if(!isset($array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1]) && $bool_es_date_fn_1==true )
								{
									$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1]=1;
									$array_indice_fecha_nacimiento[0]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1];
								}
								else if($bool_es_date_fn_1==true)
								{
									$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1]++;
									$array_indice_fecha_nacimiento[0]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1];
								}
								else
								{
									$array_indice_fecha_nacimiento[0]=0;
								}
								
								if(!isset($array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2]) && $bool_es_date_fn_2==true)
								{
									$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2]=1;
									$array_indice_fecha_nacimiento[1]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2];
								}
								else if($bool_es_date_fn_2==true)
								{
									$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2]++;
									$array_indice_fecha_nacimiento[1]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2];
								}
								else
								{
									$array_indice_fecha_nacimiento[1]=0;
								}
								
								if(!isset($array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3]) && $bool_es_date_fn_3==true)
								{
									$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3]=1;
									$array_indice_fecha_nacimiento[2]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3];
								}
								else if($bool_es_date_fn_3==true)
								{
									$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3]++;
									$array_indice_fecha_nacimiento[2]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3];
								}
								else
								{
									$array_indice_fecha_nacimiento[2]=0;
								}
								
								if(!isset($array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4]) && $bool_es_date_fn_4==true)
								{
									$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4]=1;
									$array_indice_fecha_nacimiento[3]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4];
								}
								else if($bool_es_date_fn_4==true)
								{
									$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4]++;
									$array_indice_fecha_nacimiento[3]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4];
								}
								else
								{
									$array_indice_fecha_nacimiento[3]=0;
								}
								$indice_mayor_actual=0;
								$cont_fn=0;
								while($cont_fn<count($array_indice_fecha_nacimiento))
								{
									$cont_fn_2=$cont_fn+1;
									while($cont_fn_2<count($array_indice_fecha_nacimiento))
									{
										if($array_indice_fecha_nacimiento[$cont_fn_2]>$array_indice_fecha_nacimiento[$cont_fn])
										{
											$indice_mayor_actual=$cont_fn_2;
										}
										$cont_fn_2++;
									}
									$cont_fn++;
								}
								if($indice_mayor_actual==0)
								{
									$fecha_nacimiento_definitiva=$fecha_nacimiento_1;
									$array_fecha_definitiva=$array_fn_1;
								}
								if($indice_mayor_actual==1)
								{
									$fecha_nacimiento_definitiva=$fecha_nacimiento_2;
									$array_fecha_definitiva=$array_fn_2;
								}
								if($indice_mayor_actual==2)
								{
									$fecha_nacimiento_definitiva=$fecha_nacimiento_3;
									$array_fecha_definitiva=$array_fn_3;
								}
								if($indice_mayor_actual==3)
								{
									$fecha_nacimiento_definitiva=$fecha_nacimiento_4;
									$array_fecha_definitiva=$array_fn_4;
								}
							}//fin else
							
							//calculo edad con la fecha definitiva
							
							
							
							//fin calculo edad
							try
							{
								if($fecha_nacimiento_definitiva!="")
								{
									$interval_edad = date_diff(date_create($fecha_nacimiento_definitiva),date_create($date_remision_bd));
									$edad_afiliado= intval((float)($interval_edad->format("%r%a"))/365);
									
								}
								else
								{
									$edad_afiliado=999;
								}
							}
							catch(Exception $exc_edad_afiliado)
							{
								if(connection_aborted()==false)
								{
									echo "<script>alert('".procesar_mensaje($e->getMessage())."');</script>";
								}
								
							}
							//FIN OBTENER SEXO Y EDAD DE LAS TABLAS DE AFILIADOS
							
							$sql_rechazados="";
							$sql_rechazados.="INSERT INTO gioss_archivo_rechazado_AH ";
							$sql_rechazados.=" ( ";
							$sql_rechazados.="fecha_de_rechazo ,";
							$sql_rechazados.="fecha_remision ,";
							$sql_rechazados.="codigo_eapb ,";
							$sql_rechazados.="codigo_archivo ,";
							$sql_rechazados.="numero_factura ,";					
							$sql_rechazados.="codigo_prestador_servicios_salud ,";
							$sql_rechazados.="tipo_identificacion_usuario ,";
							$sql_rechazados.="numero_identificacion_usuario ,";
							$sql_rechazados.="via_ingreso_institucion ,";
							$sql_rechazados.="fecha_ingreso ,";
							$sql_rechazados.="Hora_ingreso ,";
							$sql_rechazados.="numero_autorizacion ,";
							$sql_rechazados.="causa_externa ,";
							$sql_rechazados.="codigo_diagnostico_principal_ingreso ,";
							$sql_rechazados.="codigo_diagnostico_principal_egreso ,";
							$sql_rechazados.="codigo_relacionado_egreso_1 ,";
							$sql_rechazados.="codigo_relacionado_egreso_2 ,";
							$sql_rechazados.="codigo_relacionado_egreso_3 ,";
							$sql_rechazados.="codigo_diagnostico_complicacion ,";
							$sql_rechazados.="estado_a_salida ,";
							$sql_rechazados.="codigo_diagnostico_muerte ,";
							$sql_rechazados.="Fecha_egreso ,";
							$sql_rechazados.="Hora_egreso ,";
							$sql_rechazados.="fila ,";
							$sql_rechazados.="numero_remision, ";
							$sql_rechazados.="numero_secuencia, ";
							//columnas nuevas para facilitar reportes
							$sql_rechazados.="edad_years_afiliado, ";
							$sql_rechazados.="sexo_afiliado, ";
							$sql_rechazados.="estado_validado ";
							$sql_rechazados.=" ) ";
							$sql_rechazados.=" VALUES ";
							$sql_rechazados.=" ( ";
							$sql_rechazados.=" '".$fecha_actual."', ";
							$sql_rechazados.=" '".$date_remision_bd."', ";
							$sql_rechazados.=" '".$cod_eapb."', ";				
							$sql_rechazados.=" 'AH".$numero_de_remision."', ";
							$cont_tmp=0;
							while($cont_tmp<count($campos))
							{
								if($cont_tmp!=1)
								{
									if(procesar_mensaje2($campos[$cont_tmp])=="")
									{
										$sql_rechazados.=" '0', ";
									}
									else
									{
										$sql_rechazados.=" '".procesar_mensaje2($campos[$cont_tmp])."', ";
									}
								}
								else
								{
									$sql_rechazados.=" '".$cod_prestador."', ";
								}
								$cont_tmp++;
							}
							$sql_rechazados.=" '".$nlinea."', ";
							$sql_rechazados.=" '".$numero_de_remision."', ";
							$sql_rechazados.=" '".$numero_secuencia_actual."', ";
							//campos adicionales para facilitar reportes
							$sql_rechazados.=" '".$edad_afiliado."', ";
							$sql_rechazados.=" '".$sexo_afiliado."', ";
							$sql_rechazados.=" '".$estado_validado_registro."' ";
							$sql_rechazados.=" ) ";
							$sql_rechazados.=" ; ";
							
							$error_bd="";
							$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_rechazados, $error_bd);
							if($error_bd!="")
							{
								$error_mostrar_bd.="ERROR AL SUBIR RECHAZADOS EN AH, ".$error_bd."<br>";
							}
							
						}//fin
						//FIN SE SUBE A BD EL REGISTRO CON ERRORES APARTE
						
						
						//escribe los errores
						$mensaje_errores_ah=$array_resultados_validacion["mensaje"];
						$array_mensajes_errores_campos=explode("|",$mensaje_errores_ah);
						foreach($array_mensajes_errores_campos as $msg_error)
						{
							fwrite($file_inconsistencias_rips, $msg_error."\n");
						}
						//fin escribe los errores
						
						$mensaje_contador_errores="revisando linea ".($nlinea+1)." de $lineas_del_archivo del archivo AH,";
						$mensaje_contador_errores.=" tiene ".$array_contador_registros_buenos["AH"]." registros buenos ";
						$mensaje_contador_errores.=" y ".$array_contador_registros_malos["AH"]." registros malos ";
						$html_del_mensaje="";
						$html_del_mensaje.="<table>";
						$html_del_mensaje.="<tr>";
						$html_del_mensaje.="<td colspan=\'2\'>";
						$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
						$html_del_mensaje.="</td>";
						$html_del_mensaje.="</tr>";
						$html_del_mensaje.="<tr>";
						$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
						$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
						$html_del_mensaje.="</td>";
						$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
						$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_contador_errores."</div><div id=\'errores_bd_div\'></div>";
						$html_del_mensaje.="</td>";
						$html_del_mensaje.="</tr>";
						$html_del_mensaje.="</table>";
						if(connection_aborted()==false)
						{
							echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
							ob_flush();
							flush();
						}
						$query_update_esta_siendo_procesado="";
						$query_update_esta_siendo_procesado.=" UPDATE gioss_rips_ips_esta_validando_actualmente ";
						$query_update_esta_siendo_procesado.=" SET archivos_que_ha_validado_hasta_el_momento='".$mensaje_contador_errores."' ";
						$query_update_esta_siendo_procesado.=" WHERE fecha_remision='".$date_remision_bd."' ";
						$query_update_esta_siendo_procesado.=" AND codigo_eapb_reportadora='".$cod_prestador."' ";
						$query_update_esta_siendo_procesado.=" AND nombre_archivo_ct='CT".$numero_de_remision."'  ";
						$query_update_esta_siendo_procesado.=" ; ";
						$error_bd="";
						$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($query_update_esta_siendo_procesado, $error_bd);
						
					}//fin verifica numero campos
					else
					{
						//$error_longitud=$array_tipo_validacion_rips["01"].",Numero de campos,el numero de campos en la linea no corresponden al numero permitido,"."AH".$numero_de_remision.",-1,".($nlinea+1);
						
						$error_longitud=$array_tipo_validacion_rips["03"].",".$array_grupo_validacion_rips["0301"].",".$array_detalle_validacion_rips["03_0301_030101"].","."AH".$numero_de_remision.",-1,".($nlinea+1);
						if($hubo_inconsistencias_en_ah==false)
						{
							$hubo_inconsistencias_en_ah=true;
						}
						fwrite($file_inconsistencias_rips, $error_longitud."\n");
					}//fin else longitud no apropiada
					$nlinea++;
				}
				fclose($file_ah);
			}
			//fin parte valida archivo
			
			if($hubo_inconsistencias_en_ah)
			{
				$se_genero_archivo_de_inconsistencias=true;
				$error_mensaje.="Hubo inconsistencias en el archivo de hospitalizaci&oacuten (AH).<br>";
			}
			
		}
		else if(isset($_POST["AH".$numero_de_remision."_valido"]) && $condicion_inconcistencias_bloqueo_para_continuar)
		{
			$error_mensaje.="El archivo de hospitalizaci&oacuten (AH) no existe.<br>";
			$hubo_inconsistencias_en_ah=true;
		}
		//FIN ARCHIVO AH
		
		//ARCHIVO AD
		$hubo_inconsistencias_en_ad=false;
		$es_valido_nombre_archivo_ad=true;
		$array_contador_registros_buenos["AD"]=0;
		$array_contador_registros_malos["AD"]=0;
		if(file_exists($ruta_archivo_ad) && $condicion_inconcistencias_bloqueo_para_continuar)
		{			
			// parte donde valida-ad
			if($es_valido_nombre_archivo_ad)
			{
				$mensaje_errores_ad="";
				$lineas_del_archivo = count(file($ruta_archivo_ad)); 
				$file_ad = fopen($ruta_archivo_ad, 'r') or exit("No se pudo abrir el archivo");
				
				$nlinea=0;
				while (!feof($file_ad)) 
				{
					$linea_tmp = fgets($file_ad);
					$linea= explode("\n", $linea_tmp)[0];
					$campos = explode(",", $linea);
					if(count($campos)==6)
					{
						
						$array_resultados_validacion=validar_ad($campos,$nlinea,$array_tipo_validacion_rips,$array_grupo_validacion_rips,$array_detalle_validacion_rips,$cod_prestador,"AD".$numero_de_remision,$cod_eapb,$numero_de_remision,$fecha_remision,$array_rutas_rips,$ruta_nueva);
						if($hubo_inconsistencias_en_ad==false)
						{
							$hubo_inconsistencias_en_ad=$array_resultados_validacion["error"];
						}
						
						
						
						$estado_validado_registro=0;
						if($array_resultados_validacion["error"]==false)
						{
							$array_contador_registros_buenos["AD"]++;
							$estado_validado_registro=1;
						}
						else
						{
							$array_contador_registros_malos["AD"]++;
							$estado_validado_registro=2;
						}
						
						//SE SUBE A BD EL REGISTRO CON ERRORES APARTE
						if($condicion_bloqueo_subida_bd_rechazados)
						{
							
							$sql_rechazados="";
							$sql_rechazados.="INSERT INTO gioss_archivo_rechazado_AD ";
							$sql_rechazados.=" ( ";
							$sql_rechazados.="fecha_de_rechazo ,";
							$sql_rechazados.="fecha_remision ,";
							$sql_rechazados.="codigo_eapb ,";
							$sql_rechazados.="codigo_archivo ,";
							$sql_rechazados.="numero_factura ,";					
							$sql_rechazados.="codigo_prestador_servicios_salud ,";
							$sql_rechazados.="codigo_concepto ,";
							$sql_rechazados.="cantidad ,";
							$sql_rechazados.="valor_unitario ,";
							$sql_rechazados.="valor_total_concepto ,";
							$sql_rechazados.="fila ,";
							$sql_rechazados.="numero_remision, ";
							$sql_rechazados.="numero_secuencia, ";
							$sql_rechazados.="estado_validado ";
							$sql_rechazados.=" ) ";
							$sql_rechazados.=" VALUES ";
							$sql_rechazados.=" ( ";
							$sql_rechazados.=" '".$fecha_actual."', ";
							$sql_rechazados.=" '".$date_remision_bd."', ";
							$sql_rechazados.=" '".$cod_eapb."', ";				
							$sql_rechazados.=" 'AD".$numero_de_remision."', ";
							$cont_tmp=0;
							while($cont_tmp<count($campos))
							{
								if($cont_tmp!=1)
								{
									if(procesar_mensaje2($campos[$cont_tmp])=="")
									{
										$sql_rechazados.=" '0', ";
									}
									else
									{
										$sql_rechazados.=" '".procesar_mensaje2($campos[$cont_tmp])."', ";
									}
								}
								else
								{
									$sql_rechazados.=" '".$cod_prestador."', ";
								}
								$cont_tmp++;
							}
							$sql_rechazados.=" '".$nlinea."', ";
							$sql_rechazados.=" '".$numero_de_remision."', ";
							$sql_rechazados.=" '".$numero_secuencia_actual."', ";
							$sql_rechazados.=" '".$estado_validado_registro."' ";
							$sql_rechazados.=" ) ";
							$sql_rechazados.=" ; ";
							
							$error_bd="";
							$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_rechazados, $error_bd);
							if($error_bd!="")
							{
								$error_mostrar_bd.="ERROR AL SUBIR RECHAZADOS EN AD, ".$error_bd."<br>";
							}
						}//fin 
						//FIN SE SUBE A BD EL REGISTRO CON ERRORES APARTE
						
						//escribe los errores
						$mensaje_errores_ad=$array_resultados_validacion["mensaje"];
						$array_mensajes_errores_campos=explode("|",$mensaje_errores_ad);
						foreach($array_mensajes_errores_campos as $msg_error)
						{
							fwrite($file_inconsistencias_rips, $msg_error."\n");
						}
						//fin escribe los errores
						
						$mensaje_contador_errores="revisando linea ".($nlinea+1)." de $lineas_del_archivo del archivo AD,";
						$mensaje_contador_errores.=" tiene ".$array_contador_registros_buenos["AD"]." registros buenos ";
						$mensaje_contador_errores.=" y ".$array_contador_registros_malos["AD"]." registros malos ";
						$html_del_mensaje="";
						$html_del_mensaje.="<table>";
						$html_del_mensaje.="<tr>";
						$html_del_mensaje.="<td colspan=\'2\'>";
						$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
						$html_del_mensaje.="</td>";
						$html_del_mensaje.="</tr>";
						$html_del_mensaje.="<tr>";
						$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
						$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
						$html_del_mensaje.="</td>";
						$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
						$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_contador_errores."</div><div id=\'errores_bd_div\'></div>";
						$html_del_mensaje.="</td>";
						$html_del_mensaje.="</tr>";
						$html_del_mensaje.="</table>";
						if(connection_aborted()==false)
						{
							echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
							ob_flush();
							flush();
						}
						$query_update_esta_siendo_procesado="";
						$query_update_esta_siendo_procesado.=" UPDATE gioss_rips_ips_esta_validando_actualmente ";
						$query_update_esta_siendo_procesado.=" SET archivos_que_ha_validado_hasta_el_momento='".$mensaje_contador_errores."' ";
						$query_update_esta_siendo_procesado.=" WHERE fecha_remision='".$date_remision_bd."' ";
						$query_update_esta_siendo_procesado.=" AND codigo_eapb_reportadora='".$cod_prestador."' ";
						$query_update_esta_siendo_procesado.=" AND nombre_archivo_ct='CT".$numero_de_remision."'  ";
						$query_update_esta_siendo_procesado.=" ; ";
						$error_bd="";
						$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($query_update_esta_siendo_procesado, $error_bd);
					}//fin verifica numero campos
					else
					{
						//$error_longitud=$array_tipo_validacion_rips["01"].",Numero de campos,el numero de campos en la linea no corresponden al numero permitido,"."AD".$numero_de_remision.",-1,".($nlinea+1);
						
						$error_longitud=$array_tipo_validacion_rips["03"].",".$array_grupo_validacion_rips["0301"].",".$array_detalle_validacion_rips["03_0301_030101"].","."AD".$numero_de_remision.",-1,".($nlinea+1);
						if($hubo_inconsistencias_en_ad==false)
						{
							$hubo_inconsistencias_en_ad=true;
						}
						fwrite($file_inconsistencias_rips, $error_longitud."\n");
					}//fin else longitud no apropiada
					$nlinea++;
				}
				fclose($file_ad);
			}
			//fin parte valida archivo
			
			if($hubo_inconsistencias_en_ad)
			{
				$se_genero_archivo_de_inconsistencias=true;
				$error_mensaje.="Hubo inconsistencias en el archivo de descripci&oacuten (AD).<br>";
			}
		}
		else if(isset($_POST["AD".$numero_de_remision."_valido"]) && $condicion_inconcistencias_bloqueo_para_continuar)
		{
			$error_mensaje.="El archivo de descripci&oacuten (AD) no existe.<br>";
			$hubo_inconsistencias_en_ah=true;
		}
		//FIN ARCHIVO AD
		
		//ARCHIVO AP
		$hubo_inconsistencias_en_ap=false;
		$es_valido_nombre_archivo_ap=true;
		$array_contador_registros_buenos["AP"]=0;
		$array_contador_registros_malos["AP"]=0;
		if(file_exists($ruta_archivo_ap) && $condicion_inconcistencias_bloqueo_para_continuar)
		{						
			// parte donde valida-ap
			if($es_valido_nombre_archivo_ap)
			{
				$mensaje_errores_ap="";
				$lineas_del_archivo = count(file($ruta_archivo_ap)); 
				$file_ap = fopen($ruta_archivo_ap, 'r') or exit("No se pudo abrir el archivo");
				
				$nlinea=0;
				while (!feof($file_ap)) 
				{
					$linea_tmp = fgets($file_ap);
					$linea= explode("\n", $linea_tmp)[0];
					$campos = explode(",", $linea);
					if(count($campos)==15)
					{						
						
						$array_resultados_validacion=validar_ap($campos,$nlinea,$array_tipo_validacion_rips,$array_grupo_validacion_rips,$array_detalle_validacion_rips,$cod_prestador,"AP".$numero_de_remision,$cod_eapb,$numero_de_remision,$fecha_remision,$array_rutas_rips,$ruta_nueva,$array_afiliados_duplicados);
						if($hubo_inconsistencias_en_ap==false)
						{
							$hubo_inconsistencias_en_ap=$array_resultados_validacion["error"];
						}
						
						$estado_validado_registro=0;
						if($array_resultados_validacion["error"]==false)
						{
							$array_contador_registros_buenos["AP"]++;
							$estado_validado_registro=1;
						}
						else
						{
							$array_contador_registros_malos["AP"]++;
							$estado_validado_registro=2;
						}
						
						//SE SUBE A BD EL REGISTRO CON ERRORES APARTE
						if($condicion_bloqueo_subida_bd_rechazados)
						{
							//OBTENER SEXO Y EDAD DE LAS TABLAS DE AFILIADOS
							$tipo_id_usuario_pes=trim($campos[2]);
							$numero_id_usuario_pes=trim($campos[3]);
							
							//bd existe afiliado, el tipo regimen camp3ak4 1=rc,2=rs,3=rs,4,5=rc
							
							
							$query_edad_sexo_bd="SELECT * FROM gioss_afiliados_eapb_rc WHERE  tipo_id_afiliado= '".$tipo_id_usuario_pes."' AND id_afiliado = '".$numero_id_usuario_pes."' ;";
							$resultados_query_edad_sexo=$coneccionBD->consultar2($query_edad_sexo_bd);
							
							$fecha_nacimiento_1="";
							$sexo_1="";
							if(is_array($resultados_query_edad_sexo))
							{
								$fecha_nacimiento_1=$resultados_query_edad_sexo[0]["fecha_nacimiento"];
								$sexo_1=$resultados_query_edad_sexo[0]["sexo"];
							}
							
						
							$query_edad_sexo_bd="SELECT * FROM gioss_afiliados_regimen_subsidiado WHERE tipo_id_afiliado = '".$tipo_id_usuario_pes."' AND id_afiliado = '".$numero_id_usuario_pes."' ;";
							$resultados_query_edad_sexo=$coneccionBD->consultar2($query_edad_sexo_bd);
							
							$fecha_nacimiento_2="";
							$sexo_2="";
							if(is_array($resultados_query_edad_sexo))
							{
								$fecha_nacimiento_2=$resultados_query_edad_sexo[0]["fecha_nacimiento"];
								$sexo_2=$resultados_query_edad_sexo[0]["sexo"];
							}
						
							$query_edad_sexo_bd="SELECT * FROM gioss_afiliados_eapb_mp WHERE tipo_id_afiliado = '".$tipo_id_usuario_pes."' AND id_afiliado = '".$numero_id_usuario_pes."' ;";
							$resultados_query_edad_sexo=$coneccionBD->consultar2($query_edad_sexo_bd);
							
							$fecha_nacimiento_3="";
							$sexo_3="";
							if(is_array($resultados_query_edad_sexo))
							{
								$fecha_nacimiento_3=$resultados_query_edad_sexo[0]["fecha_nacimiento"];
								$sexo_3=$resultados_query_edad_sexo[0]["sexo"];
							}
							
							$query_edad_sexo_bd="SELECT * FROM gioss_afiliados_eapb_rp WHERE tipo_id_afiliado = '".$tipo_id_usuario_pes."' AND id_afiliado = '".$numero_id_usuario_pes."' ;";
							$resultados_query_edad_sexo=$coneccionBD->consultar2($query_edad_sexo_bd);
							
							$fecha_nacimiento_4="";
							$sexo_4="";
							if(is_array($resultados_query_edad_sexo))
							{
								$fecha_nacimiento_4=$resultados_query_edad_sexo[0]["fecha_nacimiento"];
								$sexo_4=$resultados_query_edad_sexo[0]["sexo"];
							}
							
							$edad_afiliado=0;
							$fecha_nacimiento_definitiva="";
							$array_fecha_definitiva=array();
							$sexo_afiliado="A";
							
							if($sexo_1==$sexo_2
							   && $sexo_1==$sexo_3
							   && $sexo_1==$sexo_4
							   && ($sexo_1=="M"||$sexo_1=="F")					   
							   )
							{
								$sexo_afiliado=$sexo_1;
							}
							else
							{
								$array_comprobacion_sexo=array();
								$array_indice_sexo=array();
								if(!isset($array_comprobacion_sexo[$sexo_1]) && ($sexo_1=="M"||$sexo_1=="F") )
								{
									$array_comprobacion_sexo[$sexo_1]=1;
								}
								else if(($sexo_1=="M"||$sexo_1=="F"))
								{
									$array_comprobacion_sexo[$sexo_1]++;
								}
								
								if(!isset($array_comprobacion_sexo[$sexo_2]) && ($sexo_2=="M"||$sexo_2=="F"))
								{
									$array_comprobacion_sexo[$sexo_2]=1;
								}
								else if($sexo_2=="M"||$sexo_2=="F")
								{
									$array_comprobacion_sexo[$sexo_2]++;
								}
								
								if(!isset($array_comprobacion_sexo[$sexo_3]) && ($sexo_3=="M"||$sexo_3=="F"))
								{
									$array_comprobacion_sexo[$sexo_3]=1;
								}
								else if($sexo_3=="M"||$sexo_3=="F")
								{
									$array_comprobacion_sexo[$sexo_3]++;
								}
								
								if(!isset($array_comprobacion_sexo[$sexo_4]) && ($sexo_4=="M"||$sexo_4=="F"))
								{
									$array_comprobacion_sexo[$sexo_4]=1;
								}
								else if($sexo_4=="M"||$sexo_4=="F")
								{
									$array_comprobacion_sexo[$sexo_4]++;
								}
								
								if(isset($array_comprobacion_sexo["M"]) && isset($array_comprobacion_sexo["F"]))
								{
									if($array_comprobacion_sexo["M"]>$array_comprobacion_sexo["F"])
									{
										$sexo_afiliado="M";
									}
									else
									{
										$sexo_afiliado="F";
									}
								}//fin if
								else if(isset($array_comprobacion_sexo["M"]))
								{
									$sexo_afiliado="M";
								}
								else if(isset($array_comprobacion_sexo["F"]))
								{
									$sexo_afiliado="F";
								}
								
								
							}//fin if comprobacion sexo
							
							$array_fn_1=explode("-",$fecha_nacimiento_1);
							//checkdate mes dia year
							$bool_es_date_fn_1=false;
							if(count($array_fn_1)==3)
							{
								$bool_es_date_fn_1=checkdate($array_fn_1[1],$array_fn_1[2],$array_fn_1[0]);
							}
							
							$array_fn_2=explode("-",$fecha_nacimiento_2);
							//checkdate mes dia year
							$bool_es_date_fn_2=false;
							if(count($array_fn_2)==3)
							{
								$bool_es_date_fn_2=checkdate($array_fn_2[1],$array_fn_2[2],$array_fn_2[0]);
							}
							
							$array_fn_3=explode("-",$fecha_nacimiento_3);
							//checkdate mes dia year
							$bool_es_date_fn_3=false;
							if(count($array_fn_3)==3)
							{
								$bool_es_date_fn_3=checkdate($array_fn_3[1],$array_fn_3[2],$array_fn_3[0]);
							}
							
							$array_fn_4=explode("-",$fecha_nacimiento_4);
							//checkdate mes dia year
							$bool_es_date_fn_4=false;
							if(count($array_fn_4)==3)
							{
								$bool_es_date_fn_4=checkdate($array_fn_4[1],$array_fn_4[2],$array_fn_4[0]);
							}
							
							if($fecha_nacimiento_1==$fecha_nacimiento_2
							   && $fecha_nacimiento_1==$fecha_nacimiento_3
							   && $fecha_nacimiento_1==$fecha_nacimiento_4
							   && $bool_es_date_fn_1==true)
							{
								$fecha_nacimiento_definitiva=$fecha_nacimiento_1;
							}
							else
							{
								$array_comprobacion_fecha_nacimiento=array();
								$array_indice_fecha_nacimiento=array();
								if(!isset($array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1]) && $bool_es_date_fn_1==true )
								{
									$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1]=1;
									$array_indice_fecha_nacimiento[0]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1];
								}
								else if($bool_es_date_fn_1==true)
								{
									$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1]++;
									$array_indice_fecha_nacimiento[0]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1];
								}
								else
								{
									$array_indice_fecha_nacimiento[0]=0;
								}
								
								if(!isset($array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2]) && $bool_es_date_fn_2==true)
								{
									$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2]=1;
									$array_indice_fecha_nacimiento[1]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2];
								}
								else if($bool_es_date_fn_2==true)
								{
									$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2]++;
									$array_indice_fecha_nacimiento[1]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2];
								}
								else
								{
									$array_indice_fecha_nacimiento[1]=0;
								}
								
								if(!isset($array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3]) && $bool_es_date_fn_3==true)
								{
									$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3]=1;
									$array_indice_fecha_nacimiento[2]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3];
								}
								else if($bool_es_date_fn_3==true)
								{
									$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3]++;
									$array_indice_fecha_nacimiento[2]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3];
								}
								else
								{
									$array_indice_fecha_nacimiento[2]=0;
								}
								
								if(!isset($array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4]) && $bool_es_date_fn_4==true)
								{
									$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4]=1;
									$array_indice_fecha_nacimiento[3]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4];
								}
								else if($bool_es_date_fn_4==true)
								{
									$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4]++;
									$array_indice_fecha_nacimiento[3]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4];
								}
								else
								{
									$array_indice_fecha_nacimiento[3]=0;
								}
								$indice_mayor_actual=0;
								$cont_fn=0;
								while($cont_fn<count($array_indice_fecha_nacimiento))
								{
									$cont_fn_2=$cont_fn+1;
									while($cont_fn_2<count($array_indice_fecha_nacimiento))
									{
										if($array_indice_fecha_nacimiento[$cont_fn_2]>$array_indice_fecha_nacimiento[$cont_fn])
										{
											$indice_mayor_actual=$cont_fn_2;
										}
										$cont_fn_2++;
									}
									$cont_fn++;
								}
								if($indice_mayor_actual==0)
								{
									$fecha_nacimiento_definitiva=$fecha_nacimiento_1;
									$array_fecha_definitiva=$array_fn_1;
								}
								if($indice_mayor_actual==1)
								{
									$fecha_nacimiento_definitiva=$fecha_nacimiento_2;
									$array_fecha_definitiva=$array_fn_2;
								}
								if($indice_mayor_actual==2)
								{
									$fecha_nacimiento_definitiva=$fecha_nacimiento_3;
									$array_fecha_definitiva=$array_fn_3;
								}
								if($indice_mayor_actual==3)
								{
									$fecha_nacimiento_definitiva=$fecha_nacimiento_4;
									$array_fecha_definitiva=$array_fn_4;
								}
							}//fin else
							
							//calculo edad con la fecha definitiva
							
							
							
							//fin calculo edad
							try
							{
								if($fecha_nacimiento_definitiva!="")
								{
									$interval_edad = date_diff(date_create($fecha_nacimiento_definitiva),date_create($date_remision_bd));
									$edad_afiliado= intval((float)($interval_edad->format("%r%a"))/365);
									
								}
								else
								{
									$edad_afiliado=999;
								}
							}
							catch(Exception $exc_edad_afiliado)
							{
								if(connection_aborted()==false)
								{
									echo "<script>alert('".procesar_mensaje($e->getMessage())."');</script>";
								}
								
							}
							//FIN OBTENER SEXO Y EDAD DE LAS TABLAS DE AFILIADOS
							
							$sql_rechazados="";
							$sql_rechazados.="INSERT INTO gioss_archivo_rechazado_AP ";
							$sql_rechazados.=" ( ";
							$sql_rechazados.="fecha_de_rechazo ,";
							$sql_rechazados.="fecha_remision ,";
							$sql_rechazados.="codigo_eapb ,";
							$sql_rechazados.="codigo_archivo ,";
							$sql_rechazados.="numero_factura ,";					
							$sql_rechazados.="codigo_prestador_servicios_salud ,";
							$sql_rechazados.="tipo_identificacion_usuario ,";
							$sql_rechazados.="numero_identificacion_usuario ,";
							$sql_rechazados.="fecha_procedimiento ,";
							$sql_rechazados.="numero_autorizacion ,";
							$sql_rechazados.="codigo_cups_procedimiento ,";
							$sql_rechazados.="ambito_realizacion_procedimiento ,";
							$sql_rechazados.="finalidad_procedimiento ,";
							$sql_rechazados.="personal_que_atiende ,";
							$sql_rechazados.="diagnostico_principal ,";
							$sql_rechazados.="diagnostico_relacionado ,";
							$sql_rechazados.="diagnostico_complicacion ,";
							$sql_rechazados.="forma_realizacion_acto_quirurgico ,";
							$sql_rechazados.="valor_procedimiento ,";
							$sql_rechazados.="fila ,";
							$sql_rechazados.="numero_remision, ";
							$sql_rechazados.="numero_secuencia, ";
							//columnas nuevas para facilitar reportes
							$sql_rechazados.="edad_years_afiliado, ";
							$sql_rechazados.="sexo_afiliado, ";
							$sql_rechazados.="estado_validado ";
							$sql_rechazados.=" ) ";
							$sql_rechazados.=" VALUES ";
							$sql_rechazados.=" ( ";
							$sql_rechazados.=" '".$fecha_actual."', ";
							$sql_rechazados.=" '".$date_remision_bd."', ";
							$sql_rechazados.=" '".$cod_eapb."', ";				
							$sql_rechazados.=" 'AP".$numero_de_remision."', ";
							$cont_tmp=0;
							while($cont_tmp<count($campos))
							{
								if($cont_tmp!=1)
								{
									if(procesar_mensaje2($campos[$cont_tmp])=="")
									{
										$sql_rechazados.=" '0', ";
									}
									else
									{
										$sql_rechazados.=" '".procesar_mensaje2($campos[$cont_tmp])."', ";
									}
								}
								else
								{
									$sql_rechazados.=" '".$cod_prestador."', ";
								}
								$cont_tmp++;
							}
							$sql_rechazados.=" '".$nlinea."', ";
							$sql_rechazados.=" '".$numero_de_remision."', ";
							$sql_rechazados.=" '".$numero_secuencia_actual."', ";
							//campos adicionales para facilitar reportes
							$sql_rechazados.=" '".$edad_afiliado."', ";
							$sql_rechazados.=" '".$sexo_afiliado."', ";
							$sql_rechazados.=" '".$estado_validado_registro."' ";
							$sql_rechazados.=" ) ";
							$sql_rechazados.=" ; ";
							
							$error_bd="";
							$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_rechazados, $error_bd);
							if($error_bd!="")
							{
								$error_mostrar_bd.="ERROR AL SUBIR RECHAZADOS EN AP, ".$error_bd."<br>";
							}
						}//fin
						//FIN SE SUBE A BD EL REGISTRO CON ERRORES APARTE
						
						//escribe los errores
						$mensaje_errores_ap=$array_resultados_validacion["mensaje"];
						$array_mensajes_errores_campos=explode("|",$mensaje_errores_ap);
						foreach($array_mensajes_errores_campos as $msg_error)
						{
							fwrite($file_inconsistencias_rips, $msg_error."\n");
						}
						//fin escribe los errores
						
						$mensaje_contador_errores="revisando linea ".($nlinea+1)." de $lineas_del_archivo del archivo AP,";
						$mensaje_contador_errores.=" tiene ".$array_contador_registros_buenos["AP"]." registros buenos ";
						$mensaje_contador_errores.=" y ".$array_contador_registros_malos["AP"]." registros malos ";
						$html_del_mensaje="";
						$html_del_mensaje.="<table>";
						$html_del_mensaje.="<tr>";
						$html_del_mensaje.="<td colspan=\'2\'>";
						$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
						$html_del_mensaje.="</td>";
						$html_del_mensaje.="</tr>";
						$html_del_mensaje.="<tr>";
						$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
						$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
						$html_del_mensaje.="</td>";
						$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
						$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_contador_errores."</div><div id=\'errores_bd_div\'></div>";
						$html_del_mensaje.="</td>";
						$html_del_mensaje.="</tr>";
						$html_del_mensaje.="</table>";
						if(connection_aborted()==false)
						{
							echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
							ob_flush();
							flush();
						}
						$query_update_esta_siendo_procesado="";
						$query_update_esta_siendo_procesado.=" UPDATE gioss_rips_ips_esta_validando_actualmente ";
						$query_update_esta_siendo_procesado.=" SET archivos_que_ha_validado_hasta_el_momento='".$mensaje_contador_errores."' ";
						$query_update_esta_siendo_procesado.=" WHERE fecha_remision='".$date_remision_bd."' ";
						$query_update_esta_siendo_procesado.=" AND codigo_eapb_reportadora='".$cod_prestador."' ";
						$query_update_esta_siendo_procesado.=" AND nombre_archivo_ct='CT".$numero_de_remision."'  ";
						$query_update_esta_siendo_procesado.=" ; ";
						$error_bd="";
						$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($query_update_esta_siendo_procesado, $error_bd);
						
					}//fin verifica numero campos
					else
					{
						//$error_longitud=$array_tipo_validacion_rips["01"].",Numero de campos,el numero de campos en la linea no corresponden al numero permitido,"."AP".$numero_de_remision.",-1,".($nlinea+1);
						
						$error_longitud=$array_tipo_validacion_rips["03"].",".$array_grupo_validacion_rips["0301"].",".$array_detalle_validacion_rips["03_0301_030101"].","."AP".$numero_de_remision.",-1,".($nlinea+1);
						if($hubo_inconsistencias_en_ap==false)
						{
							$hubo_inconsistencias_en_ap=true;
						}
						fwrite($file_inconsistencias_rips, $error_longitud."\n");
					}//fin else longitud no apropiada
					$nlinea++;
				}
				fclose($file_ap);
			}
			//fin parte valida archivo
			
			if($hubo_inconsistencias_en_ap)
			{
				$se_genero_archivo_de_inconsistencias=true;
				$error_mensaje.="Hubo inconsistencias en el archivo de procedimientos (AP).<br>";
			}
		}
		else if(isset($_POST["AP".$numero_de_remision."_valido"]) && $condicion_inconcistencias_bloqueo_para_continuar)
		{
			$error_mensaje.="El archivo de procedimientos (AP) no existe.<br>";
			$hubo_inconsistencias_en_ah=true;
		}
		//FIN ARCHIVO AP
		
		//ARCHIVO AU
		$hubo_inconsistencias_en_au=false;
		$es_valido_nombre_archivo_au=true;
		$array_contador_registros_buenos["AU"]=0;
		$array_contador_registros_malos["AU"]=0;
		if(file_exists($ruta_archivo_au) && $condicion_inconcistencias_bloqueo_para_continuar)
		{			
			
			// parte donde valida-au
			if($es_valido_nombre_archivo_au)
			{				
				$mensaje_errores_au="";
				$lineas_del_archivo = count(file($ruta_archivo_au)); 
				$file_au = fopen($ruta_archivo_au, 'r') or exit("No se pudo abrir el archivo");
				
				$nlinea=0;
				while (!feof($file_au)) 
				{
					$linea_tmp = fgets($file_au);
					$linea= explode("\n", $linea_tmp)[0];
					$campos = explode(",", $linea);
					if(count($campos)==17)
					{
						
						
						$array_resultados_validacion=validar_au($campos,$nlinea,$array_tipo_validacion_rips,$array_grupo_validacion_rips,$array_detalle_validacion_rips,$cod_prestador,"AU".$numero_de_remision,$cod_eapb,$numero_de_remision,$fecha_remision,$array_rutas_rips,$ruta_nueva,$array_afiliados_duplicados);
						if($hubo_inconsistencias_en_au==false)
						{
							$hubo_inconsistencias_en_au=$array_resultados_validacion["error"];
						}
						
						$estado_validado_registro=0;
						if($array_resultados_validacion["error"]==false)
						{
							$array_contador_registros_buenos["AU"]++;
							$estado_validado_registro=1;
						}
						else
						{
							$array_contador_registros_malos["AU"]++;
							$estado_validado_registro=2;
						}
						
						//SE SUBE A BD EL REGISTRO CON ERRORES APARTE
						if($condicion_bloqueo_subida_bd_rechazados)
						{
							//OBTENER SEXO Y EDAD DE LAS TABLAS DE AFILIADOS
							$tipo_id_usuario_pes=trim($campos[2]);
							$numero_id_usuario_pes=trim($campos[3]);
							
							//bd existe afiliado, el tipo regimen camp3ak4 1=rc,2=rs,3=rs,4,5=rc
							
							
							$query_edad_sexo_bd="SELECT * FROM gioss_afiliados_eapb_rc WHERE  tipo_id_afiliado= '".$tipo_id_usuario_pes."' AND id_afiliado = '".$numero_id_usuario_pes."' ;";
							$resultados_query_edad_sexo=$coneccionBD->consultar2($query_edad_sexo_bd);
							
							$fecha_nacimiento_1="";
							$sexo_1="";
							if(is_array($resultados_query_edad_sexo))
							{
								$fecha_nacimiento_1=$resultados_query_edad_sexo[0]["fecha_nacimiento"];
								$sexo_1=$resultados_query_edad_sexo[0]["sexo"];
							}
							
						
							$query_edad_sexo_bd="SELECT * FROM gioss_afiliados_regimen_subsidiado WHERE tipo_id_afiliado = '".$tipo_id_usuario_pes."' AND id_afiliado = '".$numero_id_usuario_pes."' ;";
							$resultados_query_edad_sexo=$coneccionBD->consultar2($query_edad_sexo_bd);
							
							$fecha_nacimiento_2="";
							$sexo_2="";
							if(is_array($resultados_query_edad_sexo))
							{
								$fecha_nacimiento_2=$resultados_query_edad_sexo[0]["fecha_nacimiento"];
								$sexo_2=$resultados_query_edad_sexo[0]["sexo"];
							}
						
							$query_edad_sexo_bd="SELECT * FROM gioss_afiliados_eapb_mp WHERE tipo_id_afiliado = '".$tipo_id_usuario_pes."' AND id_afiliado = '".$numero_id_usuario_pes."' ;";
							$resultados_query_edad_sexo=$coneccionBD->consultar2($query_edad_sexo_bd);
							
							$fecha_nacimiento_3="";
							$sexo_3="";
							if(is_array($resultados_query_edad_sexo))
							{
								$fecha_nacimiento_3=$resultados_query_edad_sexo[0]["fecha_nacimiento"];
								$sexo_3=$resultados_query_edad_sexo[0]["sexo"];
							}
							
							$query_edad_sexo_bd="SELECT * FROM gioss_afiliados_eapb_rp WHERE tipo_id_afiliado = '".$tipo_id_usuario_pes."' AND id_afiliado = '".$numero_id_usuario_pes."' ;";
							$resultados_query_edad_sexo=$coneccionBD->consultar2($query_edad_sexo_bd);
							
							$fecha_nacimiento_4="";
							$sexo_4="";
							if(is_array($resultados_query_edad_sexo))
							{
								$fecha_nacimiento_4=$resultados_query_edad_sexo[0]["fecha_nacimiento"];
								$sexo_4=$resultados_query_edad_sexo[0]["sexo"];
							}
							
							$edad_afiliado=0;
							$fecha_nacimiento_definitiva="";
							$array_fecha_definitiva=array();
							$sexo_afiliado="A";
							
							if($sexo_1==$sexo_2
							   && $sexo_1==$sexo_3
							   && $sexo_1==$sexo_4
							   && ($sexo_1=="M"||$sexo_1=="F")					   
							   )
							{
								$sexo_afiliado=$sexo_1;
							}
							else
							{
								$array_comprobacion_sexo=array();
								$array_indice_sexo=array();
								if(!isset($array_comprobacion_sexo[$sexo_1]) && ($sexo_1=="M"||$sexo_1=="F") )
								{
									$array_comprobacion_sexo[$sexo_1]=1;
								}
								else if(($sexo_1=="M"||$sexo_1=="F"))
								{
									$array_comprobacion_sexo[$sexo_1]++;
								}
								
								if(!isset($array_comprobacion_sexo[$sexo_2]) && ($sexo_2=="M"||$sexo_2=="F"))
								{
									$array_comprobacion_sexo[$sexo_2]=1;
								}
								else if($sexo_2=="M"||$sexo_2=="F")
								{
									$array_comprobacion_sexo[$sexo_2]++;
								}
								
								if(!isset($array_comprobacion_sexo[$sexo_3]) && ($sexo_3=="M"||$sexo_3=="F"))
								{
									$array_comprobacion_sexo[$sexo_3]=1;
								}
								else if($sexo_3=="M"||$sexo_3=="F")
								{
									$array_comprobacion_sexo[$sexo_3]++;
								}
								
								if(!isset($array_comprobacion_sexo[$sexo_4]) && ($sexo_4=="M"||$sexo_4=="F"))
								{
									$array_comprobacion_sexo[$sexo_4]=1;
								}
								else if($sexo_4=="M"||$sexo_4=="F")
								{
									$array_comprobacion_sexo[$sexo_4]++;
								}
								
								if(isset($array_comprobacion_sexo["M"]) && isset($array_comprobacion_sexo["F"]))
								{
									if($array_comprobacion_sexo["M"]>$array_comprobacion_sexo["F"])
									{
										$sexo_afiliado="M";
									}
									else
									{
										$sexo_afiliado="F";
									}
								}//fin if
								else if(isset($array_comprobacion_sexo["M"]))
								{
									$sexo_afiliado="M";
								}
								else if(isset($array_comprobacion_sexo["F"]))
								{
									$sexo_afiliado="F";
								}
								
								
							}//fin if comprobacion sexo
							
							$array_fn_1=explode("-",$fecha_nacimiento_1);
							//checkdate mes dia year
							$bool_es_date_fn_1=false;
							if(count($array_fn_1)==3)
							{
								$bool_es_date_fn_1=checkdate($array_fn_1[1],$array_fn_1[2],$array_fn_1[0]);
							}
							
							$array_fn_2=explode("-",$fecha_nacimiento_2);
							//checkdate mes dia year
							$bool_es_date_fn_2=false;
							if(count($array_fn_2)==3)
							{
								$bool_es_date_fn_2=checkdate($array_fn_2[1],$array_fn_2[2],$array_fn_2[0]);
							}
							
							$array_fn_3=explode("-",$fecha_nacimiento_3);
							//checkdate mes dia year
							$bool_es_date_fn_3=false;
							if(count($array_fn_3)==3)
							{
								$bool_es_date_fn_3=checkdate($array_fn_3[1],$array_fn_3[2],$array_fn_3[0]);
							}
							
							$array_fn_4=explode("-",$fecha_nacimiento_4);
							//checkdate mes dia year
							$bool_es_date_fn_4=false;
							if(count($array_fn_4)==3)
							{
								$bool_es_date_fn_4=checkdate($array_fn_4[1],$array_fn_4[2],$array_fn_4[0]);
							}
							
							if($fecha_nacimiento_1==$fecha_nacimiento_2
							   && $fecha_nacimiento_1==$fecha_nacimiento_3
							   && $fecha_nacimiento_1==$fecha_nacimiento_4
							   && $bool_es_date_fn_1==true)
							{
								$fecha_nacimiento_definitiva=$fecha_nacimiento_1;
							}
							else
							{
								$array_comprobacion_fecha_nacimiento=array();
								$array_indice_fecha_nacimiento=array();
								if(!isset($array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1]) && $bool_es_date_fn_1==true )
								{
									$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1]=1;
									$array_indice_fecha_nacimiento[0]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1];
								}
								else if($bool_es_date_fn_1==true)
								{
									$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1]++;
									$array_indice_fecha_nacimiento[0]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1];
								}
								else
								{
									$array_indice_fecha_nacimiento[0]=0;
								}
								
								if(!isset($array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2]) && $bool_es_date_fn_2==true)
								{
									$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2]=1;
									$array_indice_fecha_nacimiento[1]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2];
								}
								else if($bool_es_date_fn_2==true)
								{
									$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2]++;
									$array_indice_fecha_nacimiento[1]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2];
								}
								else
								{
									$array_indice_fecha_nacimiento[1]=0;
								}
								
								if(!isset($array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3]) && $bool_es_date_fn_3==true)
								{
									$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3]=1;
									$array_indice_fecha_nacimiento[2]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3];
								}
								else if($bool_es_date_fn_3==true)
								{
									$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3]++;
									$array_indice_fecha_nacimiento[2]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3];
								}
								else
								{
									$array_indice_fecha_nacimiento[2]=0;
								}
								
								if(!isset($array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4]) && $bool_es_date_fn_4==true)
								{
									$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4]=1;
									$array_indice_fecha_nacimiento[3]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4];
								}
								else if($bool_es_date_fn_4==true)
								{
									$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4]++;
									$array_indice_fecha_nacimiento[3]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4];
								}
								else
								{
									$array_indice_fecha_nacimiento[3]=0;
								}
								$indice_mayor_actual=0;
								$cont_fn=0;
								while($cont_fn<count($array_indice_fecha_nacimiento))
								{
									$cont_fn_2=$cont_fn+1;
									while($cont_fn_2<count($array_indice_fecha_nacimiento))
									{
										if($array_indice_fecha_nacimiento[$cont_fn_2]>$array_indice_fecha_nacimiento[$cont_fn])
										{
											$indice_mayor_actual=$cont_fn_2;
										}
										$cont_fn_2++;
									}
									$cont_fn++;
								}
								if($indice_mayor_actual==0)
								{
									$fecha_nacimiento_definitiva=$fecha_nacimiento_1;
									$array_fecha_definitiva=$array_fn_1;
								}
								if($indice_mayor_actual==1)
								{
									$fecha_nacimiento_definitiva=$fecha_nacimiento_2;
									$array_fecha_definitiva=$array_fn_2;
								}
								if($indice_mayor_actual==2)
								{
									$fecha_nacimiento_definitiva=$fecha_nacimiento_3;
									$array_fecha_definitiva=$array_fn_3;
								}
								if($indice_mayor_actual==3)
								{
									$fecha_nacimiento_definitiva=$fecha_nacimiento_4;
									$array_fecha_definitiva=$array_fn_4;
								}
							}//fin else
							
							//calculo edad con la fecha definitiva
							
							
							
							//fin calculo edad
							try
							{
								if($fecha_nacimiento_definitiva!="")
								{
									$interval_edad = date_diff(date_create($fecha_nacimiento_definitiva),date_create($date_remision_bd));
									$edad_afiliado= intval((float)($interval_edad->format("%r%a"))/365);
									
								}
								else
								{
									$edad_afiliado=999;
								}
							}
							catch(Exception $exc_edad_afiliado)
							{
								if(connection_aborted()==false)
								{
									echo "<script>alert('".procesar_mensaje($e->getMessage())."');</script>";
								}
							}
							//FIN OBTENER SEXO Y EDAD DE LAS TABLAS DE AFILIADOS
							
							$sql_rechazados="";
							$sql_rechazados.="INSERT INTO gioss_archivo_rechazado_AU ";
							$sql_rechazados.=" ( ";
							$sql_rechazados.="fecha_de_rechazo ,";
							$sql_rechazados.="fecha_remision ,";
							$sql_rechazados.="codigo_eapb ,";
							$sql_rechazados.="codigo_archivo ,";
							$sql_rechazados.="numero_factura ,";					
							$sql_rechazados.="codigo_prestador_servicios_salud ,";
							$sql_rechazados.="tipo_identificacion_usuario ,";
							$sql_rechazados.="numero_identificacion_usuario ,";
							$sql_rechazados.="fecha_ingreso ,";
							$sql_rechazados.="Hora_ingreso ,";
							$sql_rechazados.="numero_autorizacion ,";
							$sql_rechazados.="causa_externa ,";
							$sql_rechazados.="codigo_diagnostico_de_salida ,";
							$sql_rechazados.="codigo_diagnostico_realcionado_1 ,";
							$sql_rechazados.="codigo_diagnostico_realcionado_2 ,";
							$sql_rechazados.="codigo_diagnostico_realcionado_3 ,";
							$sql_rechazados.="destino_usuario_salida ,";
							$sql_rechazados.="estado_salida_usuario ,";
							$sql_rechazados.="diagnostico_causa_muerte ,";
							$sql_rechazados.="fecha_salida ,";
							$sql_rechazados.="hora_salida_ingreso ,";
							$sql_rechazados.="fila ,";
							$sql_rechazados.="numero_remision, ";
							$sql_rechazados.="numero_secuencia, ";
							//columnas nuevas para facilitar reportes
							$sql_rechazados.="edad_years_afiliado, ";
							$sql_rechazados.="sexo_afiliado, ";
							$sql_rechazados.="estado_validado ";
							$sql_rechazados.=" ) ";
							$sql_rechazados.=" VALUES ";
							$sql_rechazados.=" ( ";
							$sql_rechazados.=" '".$fecha_actual."', ";
							$sql_rechazados.=" '".$date_remision_bd."', ";
							$sql_rechazados.=" '".$cod_eapb."', ";				
							$sql_rechazados.=" 'AU".$numero_de_remision."', ";
							$cont_tmp=0;
							while($cont_tmp<count($campos))
							{
								if($cont_tmp!=1)
								{
									if(procesar_mensaje2($campos[$cont_tmp])=="")
									{
										$sql_rechazados.=" '0', ";
									}
									else
									{
										$sql_rechazados.=" '".procesar_mensaje2($campos[$cont_tmp])."', ";
									}
								}
								else
								{
									$sql_rechazados.=" '".$cod_prestador."', ";
								}
								$cont_tmp++;
							}
							$sql_rechazados.=" '".$nlinea."', ";
							$sql_rechazados.=" '".$numero_de_remision."', ";
							$sql_rechazados.=" '".$numero_secuencia_actual."', ";
							//campos adicionales para facilitar reportes
							$sql_rechazados.=" '".$edad_afiliado."', ";
							$sql_rechazados.=" '".$sexo_afiliado."', ";
							$sql_rechazados.=" '".$estado_validado_registro."' ";
							$sql_rechazados.=" ) ";
							$sql_rechazados.=" ; ";
							
							$error_bd="";
							$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_rechazados, $error_bd);
							if($error_bd!="")
							{
								$error_mostrar_bd.="ERROR AL SUBIR RECHAZADOS EN AU, ".$error_bd."<br>";
							}
						}//fin
						//FIN SE SUBE A BD EL REGISTRO CON ERRORES APARTE
						
						//escribe los errores
						$mensaje_errores_au=$array_resultados_validacion["mensaje"];
						$array_mensajes_errores_campos=explode("|",$mensaje_errores_au);
						foreach($array_mensajes_errores_campos as $msg_error)
						{
							fwrite($file_inconsistencias_rips, $msg_error."\n");
						}
						//fin escribe los errores
						
						$mensaje_contador_errores="revisando linea ".($nlinea+1)." de $lineas_del_archivo del archivo AU,";
						$mensaje_contador_errores.=" tiene ".$array_contador_registros_buenos["AU"]." registros buenos ";
						$mensaje_contador_errores.=" y ".$array_contador_registros_malos["AU"]." registros malos ";
						$html_del_mensaje="";
						$html_del_mensaje.="<table>";
						$html_del_mensaje.="<tr>";
						$html_del_mensaje.="<td colspan=\'2\'>";
						$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
						$html_del_mensaje.="</td>";
						$html_del_mensaje.="</tr>";
						$html_del_mensaje.="<tr>";
						$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
						$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
						$html_del_mensaje.="</td>";
						$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
						$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_contador_errores."</div><div id=\'errores_bd_div\'></div>";
						$html_del_mensaje.="</td>";
						$html_del_mensaje.="</tr>";
						$html_del_mensaje.="</table>";
						if(connection_aborted()==false)
						{
							echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
							ob_flush();
							flush();
						}
						$query_update_esta_siendo_procesado="";
						$query_update_esta_siendo_procesado.=" UPDATE gioss_rips_ips_esta_validando_actualmente ";
						$query_update_esta_siendo_procesado.=" SET archivos_que_ha_validado_hasta_el_momento='".$mensaje_contador_errores."' ";
						$query_update_esta_siendo_procesado.=" WHERE fecha_remision='".$date_remision_bd."' ";
						$query_update_esta_siendo_procesado.=" AND codigo_eapb_reportadora='".$cod_prestador."' ";
						$query_update_esta_siendo_procesado.=" AND nombre_archivo_ct='CT".$numero_de_remision."'  ";
						$query_update_esta_siendo_procesado.=" ; ";
						$error_bd="";
						$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($query_update_esta_siendo_procesado, $error_bd);
					}//fin verifica numero campos
					else
					{
						//$error_longitud=$array_tipo_validacion_rips["01"].",Numero de campos,el numero de campos en la linea no corresponden al numero permitido,"."AU".$numero_de_remision.",-1,".($nlinea+1);
						
						$error_longitud=$array_tipo_validacion_rips["03"].",".$array_grupo_validacion_rips["0301"].",".$array_detalle_validacion_rips["03_0301_030101"].","."AU".$numero_de_remision.",-1,".($nlinea+1);
						if($hubo_inconsistencias_en_au==false)
						{
							$hubo_inconsistencias_en_au=true;
						}
						fwrite($file_inconsistencias_rips, $error_longitud."\n");
					}//fin else longitud no apropiada
					$nlinea++;
				}
				fclose($file_au);
			}
			//fin parte valida archivo
			
			if($hubo_inconsistencias_en_au)
			{
				$se_genero_archivo_de_inconsistencias=true;
				$error_mensaje.="Hubo inconsistencias en el archivo de urgencias (AU).<br>";
			}
		}
		else if(isset($_POST["AU".$numero_de_remision."_valido"]) && $condicion_inconcistencias_bloqueo_para_continuar)
		{
			$error_mensaje.="El archivo de archivo de urgencias (AU) no existe.<br>";
			$hubo_inconsistencias_en_ah=true;
		}
		//FIN ARCHIVO AU 
		
		
		//ARCHIVO AN
		$hubo_inconsistencias_en_an=false;
		$es_valido_nombre_archivo_an=true;
		$array_contador_registros_buenos["AN"]=0;
		$array_contador_registros_malos["AN"]=0;
		if(file_exists($ruta_archivo_an) && $condicion_inconcistencias_bloqueo_para_continuar)
		{			
			// parte donde valida-an
			if($es_valido_nombre_archivo_an)
			{
				$mensaje_errores_an="";
				$lineas_del_archivo = count(file($ruta_archivo_an)); 
				$file_an = fopen($ruta_archivo_an, 'r') or exit("No se pudo abrir el archivo");
				
				$nlinea=0;
				while (!feof($file_an)) 
				{
					$linea_tmp = fgets($file_an);
					$linea= explode("\n", $linea_tmp)[0];
					$campos = explode(",", $linea);
					if(count($campos)==14)
					{
												
						$array_resultados_validacion=validar_an($campos,$nlinea,$array_tipo_validacion_rips,$array_grupo_validacion_rips,$array_detalle_validacion_rips,$cod_prestador,"AN".$numero_de_remision,$cod_eapb,$numero_de_remision,$fecha_remision,$array_rutas_rips,$ruta_nueva,$array_afiliados_duplicados);
						if($hubo_inconsistencias_en_an==false)
						{
							$hubo_inconsistencias_en_an=$array_resultados_validacion["error"];
						}
						
						
						$estado_validado_registro=0;
						if($array_resultados_validacion["error"]==false)
						{
							$array_contador_registros_buenos["AN"]++;
							$estado_validado_registro=1;
						}
						else
						{
							$array_contador_registros_malos["AN"]++;
							$estado_validado_registro=2;
						}
						
						//SE SUBE A BD EL REGISTRO CON ERRORES APARTE
						if($condicion_bloqueo_subida_bd_rechazados)
						{
							//OBTENER SEXO Y EDAD DE LAS TABLAS DE AFILIADOS
							$tipo_id_usuario_pes=trim($campos[2]);
							$numero_id_usuario_pes=trim($campos[3]);
							
							//bd existe afiliado, el tipo regimen camp3ak4 1=rc,2=rs,3=rs,4,5=rc
							
							
							$query_edad_sexo_bd="SELECT * FROM gioss_afiliados_eapb_rc WHERE  tipo_id_afiliado= '".$tipo_id_usuario_pes."' AND id_afiliado = '".$numero_id_usuario_pes."' ;";
							$resultados_query_edad_sexo=$coneccionBD->consultar2($query_edad_sexo_bd);
							
							$fecha_nacimiento_1="";
							$sexo_1="";
							if(is_array($resultados_query_edad_sexo))
							{
								$fecha_nacimiento_1=$resultados_query_edad_sexo[0]["fecha_nacimiento"];
								$sexo_1=$resultados_query_edad_sexo[0]["sexo"];
							}
							
						
							$query_edad_sexo_bd="SELECT * FROM gioss_afiliados_regimen_subsidiado WHERE tipo_id_afiliado = '".$tipo_id_usuario_pes."' AND id_afiliado = '".$numero_id_usuario_pes."' ;";
							$resultados_query_edad_sexo=$coneccionBD->consultar2($query_edad_sexo_bd);
							
							$fecha_nacimiento_2="";
							$sexo_2="";
							if(is_array($resultados_query_edad_sexo))
							{
								$fecha_nacimiento_2=$resultados_query_edad_sexo[0]["fecha_nacimiento"];
								$sexo_2=$resultados_query_edad_sexo[0]["sexo"];
							}
						
							$query_edad_sexo_bd="SELECT * FROM gioss_afiliados_eapb_mp WHERE tipo_id_afiliado = '".$tipo_id_usuario_pes."' AND id_afiliado = '".$numero_id_usuario_pes."' ;";
							$resultados_query_edad_sexo=$coneccionBD->consultar2($query_edad_sexo_bd);
							
							$fecha_nacimiento_3="";
							$sexo_3="";
							if(is_array($resultados_query_edad_sexo))
							{
								$fecha_nacimiento_3=$resultados_query_edad_sexo[0]["fecha_nacimiento"];
								$sexo_3=$resultados_query_edad_sexo[0]["sexo"];
							}
							
							$query_edad_sexo_bd="SELECT * FROM gioss_afiliados_eapb_rp WHERE tipo_id_afiliado = '".$tipo_id_usuario_pes."' AND id_afiliado = '".$numero_id_usuario_pes."' ;";
							$resultados_query_edad_sexo=$coneccionBD->consultar2($query_edad_sexo_bd);
							
							$fecha_nacimiento_4="";
							$sexo_4="";
							if(is_array($resultados_query_edad_sexo))
							{
								$fecha_nacimiento_4=$resultados_query_edad_sexo[0]["fecha_nacimiento"];
								$sexo_4=$resultados_query_edad_sexo[0]["sexo"];
							}
							
							$edad_afiliado=0;
							$fecha_nacimiento_definitiva="";
							$array_fecha_definitiva=array();
							$sexo_afiliado="A";
							
							if($sexo_1==$sexo_2
							   && $sexo_1==$sexo_3
							   && $sexo_1==$sexo_4
							   && ($sexo_1=="M"||$sexo_1=="F")					   
							   )
							{
								$sexo_afiliado=$sexo_1;
							}
							else
							{
								$array_comprobacion_sexo=array();
								$array_indice_sexo=array();
								if(!isset($array_comprobacion_sexo[$sexo_1]) && ($sexo_1=="M"||$sexo_1=="F") )
								{
									$array_comprobacion_sexo[$sexo_1]=1;
								}
								else if(($sexo_1=="M"||$sexo_1=="F"))
								{
									$array_comprobacion_sexo[$sexo_1]++;
								}
								
								if(!isset($array_comprobacion_sexo[$sexo_2]) && ($sexo_2=="M"||$sexo_2=="F"))
								{
									$array_comprobacion_sexo[$sexo_2]=1;
								}
								else if($sexo_2=="M"||$sexo_2=="F")
								{
									$array_comprobacion_sexo[$sexo_2]++;
								}
								
								if(!isset($array_comprobacion_sexo[$sexo_3]) && ($sexo_3=="M"||$sexo_3=="F"))
								{
									$array_comprobacion_sexo[$sexo_3]=1;
								}
								else if($sexo_3=="M"||$sexo_3=="F")
								{
									$array_comprobacion_sexo[$sexo_3]++;
								}
								
								if(!isset($array_comprobacion_sexo[$sexo_4]) && ($sexo_4=="M"||$sexo_4=="F"))
								{
									$array_comprobacion_sexo[$sexo_4]=1;
								}
								else if($sexo_4=="M"||$sexo_4=="F")
								{
									$array_comprobacion_sexo[$sexo_4]++;
								}
								
								if(isset($array_comprobacion_sexo["M"]) && isset($array_comprobacion_sexo["F"]))
								{
									if($array_comprobacion_sexo["M"]>$array_comprobacion_sexo["F"])
									{
										$sexo_afiliado="M";
									}
									else
									{
										$sexo_afiliado="F";
									}
								}//fin if
								else if(isset($array_comprobacion_sexo["M"]))
								{
									$sexo_afiliado="M";
								}
								else if(isset($array_comprobacion_sexo["F"]))
								{
									$sexo_afiliado="F";
								}
								
								
							}//fin if comprobacion sexo
							
							$array_fn_1=explode("-",$fecha_nacimiento_1);
							//checkdate mes dia year
							$bool_es_date_fn_1=false;
							if(count($array_fn_1)==3)
							{
								$bool_es_date_fn_1=checkdate($array_fn_1[1],$array_fn_1[2],$array_fn_1[0]);
							}
							
							$array_fn_2=explode("-",$fecha_nacimiento_2);
							//checkdate mes dia year
							$bool_es_date_fn_2=false;
							if(count($array_fn_2)==3)
							{
								$bool_es_date_fn_2=checkdate($array_fn_2[1],$array_fn_2[2],$array_fn_2[0]);
							}
							
							$array_fn_3=explode("-",$fecha_nacimiento_3);
							//checkdate mes dia year
							$bool_es_date_fn_3=false;
							if(count($array_fn_3)==3)
							{
								$bool_es_date_fn_3=checkdate($array_fn_3[1],$array_fn_3[2],$array_fn_3[0]);
							}
							
							$array_fn_4=explode("-",$fecha_nacimiento_4);
							//checkdate mes dia year
							$bool_es_date_fn_4=false;
							if(count($array_fn_4)==3)
							{
								$bool_es_date_fn_4=checkdate($array_fn_4[1],$array_fn_4[2],$array_fn_4[0]);
							}
							
							if($fecha_nacimiento_1==$fecha_nacimiento_2
							   && $fecha_nacimiento_1==$fecha_nacimiento_3
							   && $fecha_nacimiento_1==$fecha_nacimiento_4
							   && $bool_es_date_fn_1==true)
							{
								$fecha_nacimiento_definitiva=$fecha_nacimiento_1;
							}
							else
							{
								$array_comprobacion_fecha_nacimiento=array();
								$array_indice_fecha_nacimiento=array();
								if(!isset($array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1]) && $bool_es_date_fn_1==true )
								{
									$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1]=1;
									$array_indice_fecha_nacimiento[0]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1];
								}
								else if($bool_es_date_fn_1==true)
								{
									$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1]++;
									$array_indice_fecha_nacimiento[0]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1];
								}
								else
								{
									$array_indice_fecha_nacimiento[0]=0;
								}
								
								if(!isset($array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2]) && $bool_es_date_fn_2==true)
								{
									$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2]=1;
									$array_indice_fecha_nacimiento[1]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2];
								}
								else if($bool_es_date_fn_2==true)
								{
									$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2]++;
									$array_indice_fecha_nacimiento[1]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2];
								}
								else
								{
									$array_indice_fecha_nacimiento[1]=0;
								}
								
								if(!isset($array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3]) && $bool_es_date_fn_3==true)
								{
									$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3]=1;
									$array_indice_fecha_nacimiento[2]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3];
								}
								else if($bool_es_date_fn_3==true)
								{
									$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3]++;
									$array_indice_fecha_nacimiento[2]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3];
								}
								else
								{
									$array_indice_fecha_nacimiento[2]=0;
								}
								
								if(!isset($array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4]) && $bool_es_date_fn_4==true)
								{
									$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4]=1;
									$array_indice_fecha_nacimiento[3]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4];
								}
								else if($bool_es_date_fn_4==true)
								{
									$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4]++;
									$array_indice_fecha_nacimiento[3]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4];
								}
								else
								{
									$array_indice_fecha_nacimiento[3]=0;
								}
								$indice_mayor_actual=0;
								$cont_fn=0;
								while($cont_fn<count($array_indice_fecha_nacimiento))
								{
									$cont_fn_2=$cont_fn+1;
									while($cont_fn_2<count($array_indice_fecha_nacimiento))
									{
										if($array_indice_fecha_nacimiento[$cont_fn_2]>$array_indice_fecha_nacimiento[$cont_fn])
										{
											$indice_mayor_actual=$cont_fn_2;
										}
										$cont_fn_2++;
									}
									$cont_fn++;
								}
								if($indice_mayor_actual==0)
								{
									$fecha_nacimiento_definitiva=$fecha_nacimiento_1;
									$array_fecha_definitiva=$array_fn_1;
								}
								if($indice_mayor_actual==1)
								{
									$fecha_nacimiento_definitiva=$fecha_nacimiento_2;
									$array_fecha_definitiva=$array_fn_2;
								}
								if($indice_mayor_actual==2)
								{
									$fecha_nacimiento_definitiva=$fecha_nacimiento_3;
									$array_fecha_definitiva=$array_fn_3;
								}
								if($indice_mayor_actual==3)
								{
									$fecha_nacimiento_definitiva=$fecha_nacimiento_4;
									$array_fecha_definitiva=$array_fn_4;
								}
							}//fin else
							
							//calculo edad con la fecha definitiva
							
							
							
							//fin calculo edad
							try
							{
								if($fecha_nacimiento_definitiva!="")
								{
									$interval_edad = date_diff(date_create($fecha_nacimiento_definitiva),date_create($date_remision_bd));
									$edad_afiliado= intval((float)($interval_edad->format("%r%a"))/365);
									
								}
								else
								{
									$edad_afiliado=999;
								}
							}
							catch(Exception $exc_edad_afiliado)
							{
								if(connection_aborted()==false)
								{
									echo "<script>alert('".procesar_mensaje($e->getMessage())."');</script>";
								}
								
							}
							//FIN OBTENER SEXO Y EDAD DE LAS TABLAS DE AFILIADOS
							
							$sql_rechazados="";
							$sql_rechazados.="INSERT INTO gioss_archivo_rechazado_AN ";
							$sql_rechazados.=" ( ";
							$sql_rechazados.="fecha_de_rechazo ,";
							$sql_rechazados.="fecha_remision ,";
							$sql_rechazados.="codigo_eapb ,";
							$sql_rechazados.="codigo_archivo ,";
							$sql_rechazados.="numero_factura ,";					
							$sql_rechazados.="codigo_prestador_servicios_salud ,";
							$sql_rechazados.="tipo_identificacion_madre ,";
							$sql_rechazados.="numero_identificacion_madre ,";
							$sql_rechazados.="fecha_ingreso ,";
							$sql_rechazados.="Hora_ingreso ,";
							$sql_rechazados.="edad_gestacional ,";
							$sql_rechazados.="control_prenatal ,";
							$sql_rechazados.="sexo ,";
							$sql_rechazados.="peso ,";
							$sql_rechazados.="codigo_diagnostico_recien_nacido ,";
							$sql_rechazados.="codigo_diagnostico_causa_muerte ,";
							$sql_rechazados.="fecha_muerte_recien_nacido ,";
							$sql_rechazados.="hora_muerte_recien_nacido ,";
							$sql_rechazados.="fila ,";
							$sql_rechazados.="numero_remision, ";					
							$sql_rechazados.="numero_secuencia, ";
							//columnas nuevas para facilitar reportes
							$sql_rechazados.="edad_years_afiliado, ";
							$sql_rechazados.="sexo_afiliado, ";
							$sql_rechazados.="estado_validado ";
							$sql_rechazados.=" ) ";
							$sql_rechazados.=" VALUES ";
							$sql_rechazados.=" ( ";
							$sql_rechazados.=" '".$fecha_actual."', ";
							$sql_rechazados.=" '".$date_remision_bd."', ";
							$sql_rechazados.=" '".$cod_eapb."', ";				
							$sql_rechazados.=" 'AN".$numero_de_remision."', ";
							$cont_tmp=0;
							while($cont_tmp<count($campos))
							{
								if($cont_tmp!=1)
								{
									if(procesar_mensaje2($campos[$cont_tmp])=="")
									{
										$sql_rechazados.=" '0', ";
									}
									else
									{
										$sql_rechazados.=" '".procesar_mensaje2($campos[$cont_tmp])."', ";
									}
								}
								else
								{
									$sql_rechazados.=" '".$cod_prestador."', ";
								}
								$cont_tmp++;
							}
							$sql_rechazados.=" '".$nlinea."', ";
							$sql_rechazados.=" '".$numero_de_remision."', ";
							$sql_rechazados.=" '".$numero_secuencia_actual."', ";
							//campos adicionales para facilitar reportes
							$sql_rechazados.=" '".$edad_afiliado."', ";
							$sql_rechazados.=" '".$sexo_afiliado."', ";
							$sql_rechazados.=" '".$estado_validado_registro."' ";
							$sql_rechazados.=" ) ";
							$sql_rechazados.=" ; ";
							
							$error_bd="";
							$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_rechazados, $error_bd);
							if($error_bd!="")
							{
								$error_mostrar_bd.="ERROR AL SUBIR RECHAZADOS EN AN, ".$error_bd."<br>";
							}
						}//fin 
						//FIN SE SUBE A BD EL REGISTRO CON ERRORES APARTE
						
						//escribe los errores
						$mensaje_errores_an=$array_resultados_validacion["mensaje"];
						$array_mensajes_errores_campos=explode("|",$mensaje_errores_an);
						foreach($array_mensajes_errores_campos as $msg_error)
						{
							fwrite($file_inconsistencias_rips, $msg_error."\n");
						}
						//fin escribe los errores
						
						$mensaje_contador_errores="revisando linea ".($nlinea+1)." de $lineas_del_archivo del archivo AN,";
						$mensaje_contador_errores.=" tiene ".$array_contador_registros_buenos["AN"]." registros buenos ";
						$mensaje_contador_errores.=" y ".$array_contador_registros_malos["AN"]." registros malos ";
						$html_del_mensaje="";
						$html_del_mensaje.="<table>";
						$html_del_mensaje.="<tr>";
						$html_del_mensaje.="<td colspan=\'2\'>";
						$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
						$html_del_mensaje.="</td>";
						$html_del_mensaje.="</tr>";
						$html_del_mensaje.="<tr>";
						$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
						$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
						$html_del_mensaje.="</td>";
						$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
						$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_contador_errores."</div><div id=\'errores_bd_div\'></div>";
						$html_del_mensaje.="</td>";
						$html_del_mensaje.="</tr>";
						$html_del_mensaje.="</table>";
						if(connection_aborted()==false)
						{
							echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
							ob_flush();
							flush();
						}
						$query_update_esta_siendo_procesado="";
						$query_update_esta_siendo_procesado.=" UPDATE gioss_rips_ips_esta_validando_actualmente ";
						$query_update_esta_siendo_procesado.=" SET archivos_que_ha_validado_hasta_el_momento='".$mensaje_contador_errores."' ";
						$query_update_esta_siendo_procesado.=" WHERE fecha_remision='".$date_remision_bd."' ";
						$query_update_esta_siendo_procesado.=" AND codigo_eapb_reportadora='".$cod_prestador."' ";
						$query_update_esta_siendo_procesado.=" AND nombre_archivo_ct='CT".$numero_de_remision."'  ";
						$query_update_esta_siendo_procesado.=" ; ";
						$error_bd="";
						$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($query_update_esta_siendo_procesado, $error_bd);
					}//fin verificacion numero campos
					else
					{
						//$error_longitud=$array_tipo_validacion_rips["01"].",Numero de campos,el numero de campos en la linea no corresponden al numero permitido,"."AN".$numero_de_remision.",-1,".($nlinea+1);
						
						$error_longitud=$array_tipo_validacion_rips["03"].",".$array_grupo_validacion_rips["0301"].",".$array_detalle_validacion_rips["03_0301_030101"].","."AN".$numero_de_remision.",-1,".($nlinea+1);
						if($hubo_inconsistencias_en_an==false)
						{
							$hubo_inconsistencias_en_an=true;
						}
						fwrite($file_inconsistencias_rips, $error_longitud."\n");
					}//fin else longitud no apropiada
					$nlinea++;
				}
				fclose($file_an);
			}
			//fin parte valida archivo
			
			if($hubo_inconsistencias_en_an)
			{
				$se_genero_archivo_de_inconsistencias=true;
				$error_mensaje.="Hubo inconsistencias en el archivo de recien nacidos (AN).<br>";
			}
		}
		else if(isset($_POST["AN".$numero_de_remision."_valido"]) && $condicion_inconcistencias_bloqueo_para_continuar)
		{
			$error_mensaje.="El archivo de archivo de recien nacidos (AN) no existe.<br>";
			$hubo_inconsistencias_en_ah=true;
		}
		//FIN ARCHIVO AN
		
		//ARCHIVO AM
		$hubo_inconsistencias_en_am=false;
		$es_valido_nombre_archivo_am=true;
		$array_contador_registros_buenos["AM"]=0;
		$array_contador_registros_malos["AM"]=0;
		if(file_exists($ruta_archivo_am) && $condicion_inconcistencias_bloqueo_para_continuar)
		{	
			// parte donde valida-am
			if($es_valido_nombre_archivo_am)
			{
				$mensaje_errores_am="";
				$lineas_del_archivo = count(file($ruta_archivo_am)); 
				$file_am = fopen($ruta_archivo_am, 'r') or exit("No se pudo abrir el archivo");
				
				$nlinea=0;
				while (!feof($file_am)) 
				{
					$linea_tmp = fgets($file_am);
					$linea= explode("\n", $linea_tmp)[0];
					$campos = explode(",", $linea);
					if(count($campos)==14)
					{
												
						$array_resultados_validacion=validar_am($campos,$nlinea,$array_tipo_validacion_rips,$array_grupo_validacion_rips,$array_detalle_validacion_rips,$cod_prestador,"AM".$numero_de_remision,$cod_eapb,$numero_de_remision,$fecha_remision,$array_rutas_rips,$ruta_nueva,$array_afiliados_duplicados);
						if($hubo_inconsistencias_en_am==false)
						{
							$hubo_inconsistencias_en_am=$array_resultados_validacion["error"];
						}
						
						$estado_validado_registro=0;
						if($array_resultados_validacion["error"]==false)
						{
							$array_contador_registros_buenos["AM"]++;
							$estado_validado_registro=1;
						}
						else
						{
							$array_contador_registros_malos["AM"]++;
							$estado_validado_registro=2;
						}
						
						//SE SUBE A BD EL REGISTRO CON ERRORES APARTE
						if($condicion_bloqueo_subida_bd_rechazados)
						{
							//OBTENER SEXO Y EDAD DE LAS TABLAS DE AFILIADOS
							$tipo_id_usuario_pes=trim($campos[2]);
							$numero_id_usuario_pes=trim($campos[3]);
							
							//bd existe afiliado, el tipo regimen camp3ak4 1=rc,2=rs,3=rs,4,5=rc
							
							
							$query_edad_sexo_bd="SELECT * FROM gioss_afiliados_eapb_rc WHERE  tipo_id_afiliado= '".$tipo_id_usuario_pes."' AND id_afiliado = '".$numero_id_usuario_pes."' ;";
							$resultados_query_edad_sexo=$coneccionBD->consultar2($query_edad_sexo_bd);
							
							$fecha_nacimiento_1="";
							$sexo_1="";
							if(is_array($resultados_query_edad_sexo))
							{
								$fecha_nacimiento_1=$resultados_query_edad_sexo[0]["fecha_nacimiento"];
								$sexo_1=$resultados_query_edad_sexo[0]["sexo"];
							}
							
						
							$query_edad_sexo_bd="SELECT * FROM gioss_afiliados_regimen_subsidiado WHERE tipo_id_afiliado = '".$tipo_id_usuario_pes."' AND id_afiliado = '".$numero_id_usuario_pes."' ;";
							$resultados_query_edad_sexo=$coneccionBD->consultar2($query_edad_sexo_bd);
							
							$fecha_nacimiento_2="";
							$sexo_2="";
							if(is_array($resultados_query_edad_sexo))
							{
								$fecha_nacimiento_2=$resultados_query_edad_sexo[0]["fecha_nacimiento"];
								$sexo_2=$resultados_query_edad_sexo[0]["sexo"];
							}
						
							$query_edad_sexo_bd="SELECT * FROM gioss_afiliados_eapb_mp WHERE tipo_id_afiliado = '".$tipo_id_usuario_pes."' AND id_afiliado = '".$numero_id_usuario_pes."' ;";
							$resultados_query_edad_sexo=$coneccionBD->consultar2($query_edad_sexo_bd);
							
							$fecha_nacimiento_3="";
							$sexo_3="";
							if(is_array($resultados_query_edad_sexo))
							{
								$fecha_nacimiento_3=$resultados_query_edad_sexo[0]["fecha_nacimiento"];
								$sexo_3=$resultados_query_edad_sexo[0]["sexo"];
							}
							
							$query_edad_sexo_bd="SELECT * FROM gioss_afiliados_eapb_rp WHERE tipo_id_afiliado = '".$tipo_id_usuario_pes."' AND id_afiliado = '".$numero_id_usuario_pes."' ;";
							$resultados_query_edad_sexo=$coneccionBD->consultar2($query_edad_sexo_bd);
							
							$fecha_nacimiento_4="";
							$sexo_4="";
							if(is_array($resultados_query_edad_sexo))
							{
								$fecha_nacimiento_4=$resultados_query_edad_sexo[0]["fecha_nacimiento"];
								$sexo_4=$resultados_query_edad_sexo[0]["sexo"];
							}
							
							$edad_afiliado=0;
							$fecha_nacimiento_definitiva="";
							$array_fecha_definitiva=array();
							$sexo_afiliado="A";
							
							if($sexo_1==$sexo_2
							   && $sexo_1==$sexo_3
							   && $sexo_1==$sexo_4
							   && ($sexo_1=="M"||$sexo_1=="F")					   
							   )
							{
								$sexo_afiliado=$sexo_1;
							}
							else
							{
								$array_comprobacion_sexo=array();
								$array_indice_sexo=array();
								if(!isset($array_comprobacion_sexo[$sexo_1]) && ($sexo_1=="M"||$sexo_1=="F") )
								{
									$array_comprobacion_sexo[$sexo_1]=1;
								}
								else if(($sexo_1=="M"||$sexo_1=="F"))
								{
									$array_comprobacion_sexo[$sexo_1]++;
								}
								
								if(!isset($array_comprobacion_sexo[$sexo_2]) && ($sexo_2=="M"||$sexo_2=="F"))
								{
									$array_comprobacion_sexo[$sexo_2]=1;
								}
								else if($sexo_2=="M"||$sexo_2=="F")
								{
									$array_comprobacion_sexo[$sexo_2]++;
								}
								
								if(!isset($array_comprobacion_sexo[$sexo_3]) && ($sexo_3=="M"||$sexo_3=="F"))
								{
									$array_comprobacion_sexo[$sexo_3]=1;
								}
								else if($sexo_3=="M"||$sexo_3=="F")
								{
									$array_comprobacion_sexo[$sexo_3]++;
								}
								
								if(!isset($array_comprobacion_sexo[$sexo_4]) && ($sexo_4=="M"||$sexo_4=="F"))
								{
									$array_comprobacion_sexo[$sexo_4]=1;
								}
								else if($sexo_4=="M"||$sexo_4=="F")
								{
									$array_comprobacion_sexo[$sexo_4]++;
								}
								
								if(isset($array_comprobacion_sexo["M"]) && isset($array_comprobacion_sexo["F"]))
								{
									if($array_comprobacion_sexo["M"]>$array_comprobacion_sexo["F"])
									{
										$sexo_afiliado="M";
									}
									else
									{
										$sexo_afiliado="F";
									}
								}//fin if
								else if(isset($array_comprobacion_sexo["M"]))
								{
									$sexo_afiliado="M";
								}
								else if(isset($array_comprobacion_sexo["F"]))
								{
									$sexo_afiliado="F";
								}
								
								
							}//fin if comprobacion sexo
							
							$array_fn_1=explode("-",$fecha_nacimiento_1);
							//checkdate mes dia year
							$bool_es_date_fn_1=false;
							if(count($array_fn_1)==3)
							{
								$bool_es_date_fn_1=checkdate($array_fn_1[1],$array_fn_1[2],$array_fn_1[0]);
							}
							
							$array_fn_2=explode("-",$fecha_nacimiento_2);
							//checkdate mes dia year
							$bool_es_date_fn_2=false;
							if(count($array_fn_2)==3)
							{
								$bool_es_date_fn_2=checkdate($array_fn_2[1],$array_fn_2[2],$array_fn_2[0]);
							}
							
							$array_fn_3=explode("-",$fecha_nacimiento_3);
							//checkdate mes dia year
							$bool_es_date_fn_3=false;
							if(count($array_fn_3)==3)
							{
								$bool_es_date_fn_3=checkdate($array_fn_3[1],$array_fn_3[2],$array_fn_3[0]);
							}
							
							$array_fn_4=explode("-",$fecha_nacimiento_4);
							//checkdate mes dia year
							$bool_es_date_fn_4=false;
							if(count($array_fn_4)==3)
							{
								$bool_es_date_fn_4=checkdate($array_fn_4[1],$array_fn_4[2],$array_fn_4[0]);
							}
							
							if($fecha_nacimiento_1==$fecha_nacimiento_2
							   && $fecha_nacimiento_1==$fecha_nacimiento_3
							   && $fecha_nacimiento_1==$fecha_nacimiento_4
							   && $bool_es_date_fn_1==true)
							{
								$fecha_nacimiento_definitiva=$fecha_nacimiento_1;
							}
							else
							{
								$array_comprobacion_fecha_nacimiento=array();
								$array_indice_fecha_nacimiento=array();
								if(!isset($array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1]) && $bool_es_date_fn_1==true )
								{
									$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1]=1;
									$array_indice_fecha_nacimiento[0]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1];
								}
								else if($bool_es_date_fn_1==true)
								{
									$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1]++;
									$array_indice_fecha_nacimiento[0]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1];
								}
								else
								{
									$array_indice_fecha_nacimiento[0]=0;
								}
								
								if(!isset($array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2]) && $bool_es_date_fn_2==true)
								{
									$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2]=1;
									$array_indice_fecha_nacimiento[1]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2];
								}
								else if($bool_es_date_fn_2==true)
								{
									$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2]++;
									$array_indice_fecha_nacimiento[1]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2];
								}
								else
								{
									$array_indice_fecha_nacimiento[1]=0;
								}
								
								if(!isset($array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3]) && $bool_es_date_fn_3==true)
								{
									$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3]=1;
									$array_indice_fecha_nacimiento[2]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3];
								}
								else if($bool_es_date_fn_3==true)
								{
									$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3]++;
									$array_indice_fecha_nacimiento[2]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3];
								}
								else
								{
									$array_indice_fecha_nacimiento[2]=0;
								}
								
								if(!isset($array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4]) && $bool_es_date_fn_4==true)
								{
									$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4]=1;
									$array_indice_fecha_nacimiento[3]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4];
								}
								else if($bool_es_date_fn_4==true)
								{
									$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4]++;
									$array_indice_fecha_nacimiento[3]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4];
								}
								else
								{
									$array_indice_fecha_nacimiento[3]=0;
								}
								$indice_mayor_actual=0;
								$cont_fn=0;
								while($cont_fn<count($array_indice_fecha_nacimiento))
								{
									$cont_fn_2=$cont_fn+1;
									while($cont_fn_2<count($array_indice_fecha_nacimiento))
									{
										if($array_indice_fecha_nacimiento[$cont_fn_2]>$array_indice_fecha_nacimiento[$cont_fn])
										{
											$indice_mayor_actual=$cont_fn_2;
										}
										$cont_fn_2++;
									}
									$cont_fn++;
								}
								if($indice_mayor_actual==0)
								{
									$fecha_nacimiento_definitiva=$fecha_nacimiento_1;
									$array_fecha_definitiva=$array_fn_1;
								}
								if($indice_mayor_actual==1)
								{
									$fecha_nacimiento_definitiva=$fecha_nacimiento_2;
									$array_fecha_definitiva=$array_fn_2;
								}
								if($indice_mayor_actual==2)
								{
									$fecha_nacimiento_definitiva=$fecha_nacimiento_3;
									$array_fecha_definitiva=$array_fn_3;
								}
								if($indice_mayor_actual==3)
								{
									$fecha_nacimiento_definitiva=$fecha_nacimiento_4;
									$array_fecha_definitiva=$array_fn_4;
								}
							}//fin else
							
							//calculo edad con la fecha definitiva
							
							
							
							//fin calculo edad
							try
							{
								if($fecha_nacimiento_definitiva!="")
								{
									$interval_edad = date_diff(date_create($fecha_nacimiento_definitiva),date_create($date_remision_bd));
									$edad_afiliado= intval((float)($interval_edad->format("%r%a"))/365);
									
								}
								else
								{
									$edad_afiliado=999;
								}
							}
							catch(Exception $exc_edad_afiliado)
							{
								if(connection_aborted()==false)
								{
									echo "<script>alert('".procesar_mensaje($e->getMessage())."');</script>";
								}
								
							}
							//FIN OBTENER SEXO Y EDAD DE LAS TABLAS DE AFILIADOS
							
							$sql_rechazados="";
							$sql_rechazados.="INSERT INTO gioss_archivo_rechazado_AM ";
							$sql_rechazados.=" ( ";
							$sql_rechazados.="fecha_de_rechazo ,";
							$sql_rechazados.="fecha_remision ,";
							$sql_rechazados.="codigo_eapb ,";
							$sql_rechazados.="codigo_archivo ,";
							$sql_rechazados.="numero_factura ,";					
							$sql_rechazados.="codigo_prestador_servicios_salud ,";
							$sql_rechazados.="tipo_identificacion_usuario ,";
							$sql_rechazados.="numero_identificacion_usuario ,";
							$sql_rechazados.="numero_autorizacion ,";
							$sql_rechazados.="codigo_del_medicamento ,";
							$sql_rechazados.="tipo_medicamento ,";
							$sql_rechazados.="nombre_generico_medicamento ,";
							$sql_rechazados.="forma_farmaceutica ,";
							$sql_rechazados.="concetracion_medicamento ,";
							$sql_rechazados.="unidad_medida_medicamento ,";
							$sql_rechazados.="numero_unidades ,";
							$sql_rechazados.="valor_unitario_medicamento ,";
							$sql_rechazados.="valor_total_medicamento ,";
							$sql_rechazados.="fila ,";
							$sql_rechazados.="numero_remision, ";
							$sql_rechazados.="numero_secuencia, ";
							//columnas nuevas para facilitar reportes
							$sql_rechazados.="edad_years_afiliado, ";
							$sql_rechazados.="sexo_afiliado, ";
							$sql_rechazados.="estado_validado ";
							$sql_rechazados.=" ) ";
							$sql_rechazados.=" VALUES ";
							$sql_rechazados.=" ( ";
							$sql_rechazados.=" '".$fecha_actual."', ";
							$sql_rechazados.=" '".$date_remision_bd."', ";
							$sql_rechazados.=" '".$cod_eapb."', ";				
							$sql_rechazados.=" 'AM".$numero_de_remision."', ";
							$cont_tmp=0;
							while($cont_tmp<count($campos))
							{
								if($cont_tmp!=1)
								{
									if(procesar_mensaje2($campos[$cont_tmp])=="")
									{
										$sql_rechazados.=" '0', ";
									}
									else
									{
										$sql_rechazados.=" '".procesar_mensaje2($campos[$cont_tmp])."', ";
									}
								}
								else
								{
									$sql_rechazados.=" '".$cod_prestador."', ";
								}
								$cont_tmp++;
							}
							$sql_rechazados.=" '".$nlinea."', ";
							$sql_rechazados.=" '".$numero_de_remision."', ";
							$sql_rechazados.=" '".$numero_secuencia_actual."', ";
							//campos adicionales para facilitar reportes
							$sql_rechazados.=" '".$edad_afiliado."', ";
							$sql_rechazados.=" '".$sexo_afiliado."', ";
							$sql_rechazados.=" '".$estado_validado_registro."' ";
							$sql_rechazados.=" ) ";
							$sql_rechazados.=" ; ";
							
							$error_bd="";
							$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_rechazados, $error_bd);
							if($error_bd!="")
							{
								$error_mostrar_bd.="ERROR AL SUBIR RECHAZADOS EN AM, ".$error_bd."<br>";
							}
						}//fin 
						//FIN SE SUBE A BD EL REGISTRO CON ERRORES APARTE
						
						//escribe los errores
						$mensaje_errores_am=$array_resultados_validacion["mensaje"];
						$array_mensajes_errores_campos=explode("|",$mensaje_errores_am);
						foreach($array_mensajes_errores_campos as $msg_error)
						{
							fwrite($file_inconsistencias_rips, $msg_error."\n");
						}
						//fin escribe los errores
						
						$mensaje_contador_errores="revisando linea ".($nlinea+1)." de $lineas_del_archivo del archivo AM,";
						$mensaje_contador_errores.=" tiene ".$array_contador_registros_buenos["AM"]." registros buenos ";
						$mensaje_contador_errores.=" y ".$array_contador_registros_malos["AM"]." registros malos ";
						$html_del_mensaje="";
						$html_del_mensaje.="<table>";
						$html_del_mensaje.="<tr>";
						$html_del_mensaje.="<td colspan=\'2\'>";
						$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
						$html_del_mensaje.="</td>";
						$html_del_mensaje.="</tr>";
						$html_del_mensaje.="<tr>";
						$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
						$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
						$html_del_mensaje.="</td>";
						$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
						$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_contador_errores."</div><div id=\'errores_bd_div\'></div>";
						$html_del_mensaje.="</td>";
						$html_del_mensaje.="</tr>";
						$html_del_mensaje.="</table>";
						if(connection_aborted()==false)
						{
							echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
							ob_flush();
							flush();
						}
						$query_update_esta_siendo_procesado="";
						$query_update_esta_siendo_procesado.=" UPDATE gioss_rips_ips_esta_validando_actualmente ";
						$query_update_esta_siendo_procesado.=" SET archivos_que_ha_validado_hasta_el_momento='".$mensaje_contador_errores."' ";
						$query_update_esta_siendo_procesado.=" WHERE fecha_remision='".$date_remision_bd."' ";
						$query_update_esta_siendo_procesado.=" AND codigo_eapb_reportadora='".$cod_prestador."' ";
						$query_update_esta_siendo_procesado.=" AND nombre_archivo_ct='CT".$numero_de_remision."'  ";
						$query_update_esta_siendo_procesado.=" ; ";
						$error_bd="";
						$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($query_update_esta_siendo_procesado, $error_bd);
					}//fin verificacion numero campos
					else
					{
						//$error_longitud=$array_tipo_validacion_rips["01"].",Numero de campos,el numero de campos en la linea no corresponden al numero permitido,"."AM".$numero_de_remision.",-1,".($nlinea+1);
						
						$error_longitud=$array_tipo_validacion_rips["03"].",".$array_grupo_validacion_rips["0301"].",".$array_detalle_validacion_rips["03_0301_030101"].","."AM".$numero_de_remision.",-1,".($nlinea+1);
						if($hubo_inconsistencias_en_am==false)
						{
							$hubo_inconsistencias_en_am=true;
						}
						fwrite($file_inconsistencias_rips, $error_longitud."\n");
					}//fin else longitud no apropiada
					$nlinea++;
				}
				fclose($file_am);
			}
			//fin parte valida archivo
			
			if($hubo_inconsistencias_en_am)
			{
				$se_genero_archivo_de_inconsistencias=true;
				$error_mensaje.="Hubo inconsistencias en el archivo de medicamentos (AM).<br>";
			}
		}
		else if(isset($_POST["AM".$numero_de_remision."_valido"]) && $condicion_inconcistencias_bloqueo_para_continuar)
		{
			$error_mensaje.="El archivo de archivo de medicamentos (AM) no existe.<br>";
			$hubo_inconsistencias_en_ah=true;
		}
		//FIN ARCHIVO AM
		
		//ARCHIVO AT
		$hubo_inconsistencias_en_at=false;
		$es_valido_nombre_archivo_at=true;
		$array_contador_registros_buenos["AT"]=0;
		$array_contador_registros_malos["AT"]=0;
		if(file_exists($ruta_archivo_at) && $condicion_inconcistencias_bloqueo_para_continuar)
		{
			// parte donde valida-at
			if($es_valido_nombre_archivo_at)
			{
				$mensaje_errores_at="";
				$lineas_del_archivo = count(file($ruta_archivo_at)); 
				$file_at = fopen($ruta_archivo_at, 'r') or exit("No se pudo abrir el archivo");
				
				$nlinea=0;
				while (!feof($file_at)) 
				{
					$linea_tmp = fgets($file_at);
					$linea= explode("\n", $linea_tmp)[0];
					$campos = explode(",", $linea);
					if(count($campos)==11)
					{
						
						
						$array_resultados_validacion=validar_at($campos,$nlinea,$array_tipo_validacion_rips,$array_grupo_validacion_rips,$array_detalle_validacion_rips,$cod_prestador,"AT".$numero_de_remision,$cod_eapb,$numero_de_remision,$fecha_remision,$array_rutas_rips,$ruta_nueva,$array_afiliados_duplicados);
						if($hubo_inconsistencias_en_at==false)
						{
							$hubo_inconsistencias_en_at=$array_resultados_validacion["error"];
						}
						
						$estado_validado_registro=0;
						if($array_resultados_validacion["error"]==false)
						{
							$array_contador_registros_buenos["AT"]++;
							$estado_validado_registro=1;
						}
						else
						{
							$array_contador_registros_malos["AT"]++;
							$estado_validado_registro=2;
						}
						
						//SE SUBE A BD EL REGISTRO CON ERRORES APARTE
						if($condicion_bloqueo_subida_bd_rechazados)
						{
							$sql_rechazados="";
							$sql_rechazados.="INSERT INTO gioss_archivo_rechazado_AT ";
							$sql_rechazados.=" ( ";
							$sql_rechazados.="fecha_de_rechazo ,";
							$sql_rechazados.="fecha_remision ,";
							$sql_rechazados.="codigo_eapb ,";
							$sql_rechazados.="codigo_archivo ,";
							$sql_rechazados.="numero_factura ,";					
							$sql_rechazados.="codigo_prestador_servicios_salud ,";
							$sql_rechazados.="tipo_identificacion_usuario ,";
							$sql_rechazados.="numero_identificacion_usuario ,";
							$sql_rechazados.="numero_autorizacion ,";
							$sql_rechazados.="codigo_tipo_servicio ,";
							$sql_rechazados.="codigo_del_servicio ,";
							$sql_rechazados.="nombre_del_servicio ,";
							$sql_rechazados.="cantidad ,";
							$sql_rechazados.="valor_unitario ,";
							$sql_rechazados.="valor_total ,";
							$sql_rechazados.="fila ,";
							$sql_rechazados.="numero_remision, ";
							$sql_rechazados.="numero_secuencia, ";
							$sql_rechazados.="estado_validado ";
							$sql_rechazados.=" ) ";
							$sql_rechazados.=" VALUES ";
							$sql_rechazados.=" ( ";
							$sql_rechazados.=" '".$fecha_actual."', ";
							$sql_rechazados.=" '".$date_remision_bd."', ";
							$sql_rechazados.=" '".$cod_eapb."', ";				
							$sql_rechazados.=" 'AT".$numero_de_remision."', ";
							$cont_tmp=0;
							while($cont_tmp<count($campos))
							{
								if($cont_tmp!=1)
								{
									if(procesar_mensaje2($campos[$cont_tmp])=="")
									{
										$sql_rechazados.=" '0', ";
									}
									else
									{
										$sql_rechazados.=" '".procesar_mensaje2($campos[$cont_tmp])."', ";
									}
								}
								else
								{
									$sql_rechazados.=" '".$cod_prestador."', ";
								}
								$cont_tmp++;
							}
							$sql_rechazados.=" '".$nlinea."', ";
							$sql_rechazados.=" '".$numero_de_remision."', ";
							$sql_rechazados.=" '".$numero_secuencia_actual."', ";
							$sql_rechazados.=" '".$estado_validado_registro."' ";
							$sql_rechazados.=" ) ";
							$sql_rechazados.=" ; ";
							
							$error_bd="";
							$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_rechazados, $error_bd);
							if($error_bd!="")
							{
								$error_mostrar_bd.="ERROR AL SUBIR RECHAZADOS EN AT, ".$error_bd."<br>";
							}
						}//fin 
						//FIN SE SUBE A BD EL REGISTRO CON ERRORES APARTE
						
						//escribe los errores
						$mensaje_errores_at=$array_resultados_validacion["mensaje"];
						$array_mensajes_errores_campos=explode("|",$mensaje_errores_at);
						foreach($array_mensajes_errores_campos as $msg_error)
						{
							fwrite($file_inconsistencias_rips, $msg_error."\n");
						}
						//fin escribe los errores
						
						$mensaje_contador_errores="revisando linea ".($nlinea+1)." de $lineas_del_archivo del archivo AT,";
						$mensaje_contador_errores.=" tiene ".$array_contador_registros_buenos["AT"]." registros buenos ";
						$mensaje_contador_errores.=" y ".$array_contador_registros_malos["AT"]." registros malos ";
						$html_del_mensaje="";
						$html_del_mensaje.="<table>";
						$html_del_mensaje.="<tr>";
						$html_del_mensaje.="<td colspan=\'2\'>";
						$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
						$html_del_mensaje.="</td>";
						$html_del_mensaje.="</tr>";
						$html_del_mensaje.="<tr>";
						$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
						$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
						$html_del_mensaje.="</td>";
						$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
						$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_contador_errores."</div><div id=\'errores_bd_div\'></div>";
						$html_del_mensaje.="</td>";
						$html_del_mensaje.="</tr>";
						$html_del_mensaje.="</table>";
						if(connection_aborted()==false)
						{
							echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
							ob_flush();
							flush();
						}
						$query_update_esta_siendo_procesado="";
						$query_update_esta_siendo_procesado.=" UPDATE gioss_rips_ips_esta_validando_actualmente ";
						$query_update_esta_siendo_procesado.=" SET archivos_que_ha_validado_hasta_el_momento='".$mensaje_contador_errores."' ";
						$query_update_esta_siendo_procesado.=" WHERE fecha_remision='".$date_remision_bd."' ";
						$query_update_esta_siendo_procesado.=" AND codigo_eapb_reportadora='".$cod_prestador."' ";
						$query_update_esta_siendo_procesado.=" AND nombre_archivo_ct='CT".$numero_de_remision."'  ";
						$query_update_esta_siendo_procesado.=" ; ";
						$error_bd="";
						$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($query_update_esta_siendo_procesado, $error_bd);
					}//fin verificacion numero campos
					else
					{
						//$error_longitud=$array_tipo_validacion_rips["01"].",Numero de campos,el numero de campos en la linea no corresponden al numero permitido,"."AT".$numero_de_remision.",-1,".($nlinea+1);
						
						$error_longitud=$array_tipo_validacion_rips["03"].",".$array_grupo_validacion_rips["0301"].",".$array_detalle_validacion_rips["03_0301_030101"].","."AT".$numero_de_remision.",-1,".($nlinea+1);
						if($hubo_inconsistencias_en_at==false)
						{
							$hubo_inconsistencias_en_at=true;
						}
						fwrite($file_inconsistencias_rips, $error_longitud."\n");
					}//fin else longitud no apropiada
					$nlinea++;
				}
				fclose($file_at);
			}
			//fin parte valida archivo
			
			if($hubo_inconsistencias_en_at)
			{
				$se_genero_archivo_de_inconsistencias=true;
				$error_mensaje.="Hubo inconsistencias en el archivo de otros servicios (AT).<br>";
			}
		}
		else if(isset($_POST["AT".$numero_de_remision."_valido"]) && $condicion_inconcistencias_bloqueo_para_continuar)
		{
			$error_mensaje.="El archivo de archivo de otros servicios (AT) no existe.<br>";
			$hubo_inconsistencias_en_ah=true;
		}
		//FIN ARCHIVO AT
		
		//VERIFICA LOS QUE NO ENCONTRO EN US
		$bool_hubo_afiliados_de_us_sin_encontrar_en_archivos_de_servicios=false;
		foreach($array_afiliados_duplicados as $key=>$afiliado_de_us)
		{
			$array_lineas_encontrado_afiliado=explode("...",$afiliado_de_us);
			if(count($array_lineas_encontrado_afiliado)==2)
			{
				//se encontro no hay error
			}
			else
			{
				
				$bool_hubo_afiliados_de_us_sin_encontrar_en_archivos_de_servicios=true;				
				$array_lineas_no_encontrado=explode("-",$afiliado_de_us);
				
				foreach($array_lineas_no_encontrado as $linea_no_encontrado)
				{
					$linea_no_econtrado_int=intval($linea_no_encontrado);
					$error_no_existe_en_otros_archivos=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0106"].",".$array_detalle_validacion_rips["01_0106_010608"]."  $key ,US".$numero_de_remision.",3,".$linea_no_econtrado_int;
					if($hubo_inconsistencias_en_us==false)
					{
						$hubo_inconsistencias_en_us=true;
					}
					fwrite($file_inconsistencias_rips, $error_no_existe_en_otros_archivos."\n");
				}//fin foreach
			}//fin else
		}
		if($bool_hubo_afiliados_de_us_sin_encontrar_en_archivos_de_servicios)
		{
			$error_mensaje.="No se encontraron en al menos un archivo de servicios un(os) de los afiliados en el archivo de usuarios(US) .<br>";
		}
		//FIN VERIFICA LOS QUE NO ENCONTRO EN US
		
		unset($array_afiliados_duplicados);
		
		//cierra el archivo de inconsistencias
		fclose($file_inconsistencias_rips);
		
	}//fin if solo se valida si no fue validado con exito
	//FIN VALIDACION DE LOS CAMPOS DE LOS ARCHIVOS RIPS
	
	//error de bd mostrar
	if($error_mostrar_bd!="")
	{
		$error_mostrar_bd_procesado = str_replace("á","a",$error_mostrar_bd);
		$error_mostrar_bd_procesado = str_replace("é","e",$error_mostrar_bd_procesado);
		$error_mostrar_bd_procesado = str_replace("í","i",$error_mostrar_bd_procesado);
		$error_mostrar_bd_procesado = str_replace("ó","o",$error_mostrar_bd_procesado);
		$error_mostrar_bd_procesado = str_replace("ú","u",$error_mostrar_bd_procesado);
		$error_mostrar_bd_procesado = str_replace("ñ","n",$error_mostrar_bd_procesado);
		$error_mostrar_bd_procesado = str_replace("Á","A",$error_mostrar_bd_procesado);
		$error_mostrar_bd_procesado = str_replace("É","E",$error_mostrar_bd_procesado);
		$error_mostrar_bd_procesado = str_replace("Í","I",$error_mostrar_bd_procesado);
		$error_mostrar_bd_procesado = str_replace("Ó","O",$error_mostrar_bd_procesado);
		$error_mostrar_bd_procesado = str_replace("Ú","U",$error_mostrar_bd_procesado);
		$error_mostrar_bd_procesado = str_replace("Ñ","N",$error_mostrar_bd_procesado);
		$error_mostrar_bd_procesado = str_replace(" "," ",$error_mostrar_bd_procesado);
		$error_mostrar_bd_procesado = str_replace("'"," ",$error_mostrar_bd_procesado);
		$error_mostrar_bd_procesado = str_replace("\n"," ",$error_mostrar_bd_procesado);
		$linea_res= alphanumericAndSpace($error_mostrar_bd_procesado);
		
		$error_mensaje.="ERROR EN SUBIR RECHAZADOS A BD, ".$error_mostrar_bd_procesado;
	}//fin if error en bd
	
	
		
	//PARTE PARA SUBIR VALIDADOS CON EXITO EN BD
	//sube los archivos si han sido validados con exito aqui borra los rechazados que se subieron si termina siendo exitoso
	//debido a que se subira en ambos tipos de tablas si es exitoso preo al final se borra de rechazados
	//para asi permitir el adicionar una bandera a cada registro rechazado si estubo bueno o malo
	if($error_mensaje=="" && $verificacion_ya_se_valido_con_exito==false)
	{
		$sql_exito="";
		$sql_exito.="BEGIN TRANSACTION;";
		//falso no hay error, true error en query bd
		$bool_hubo_error_query=false;
		$error_mostrar_bd="";
		
		$fecha_remision_array=explode("/",$fecha_remision);
		$date_remision_bd=$fecha_remision_array[2]."-".$fecha_remision_array[0]."-".$fecha_remision_array[1];
		
		$error_bd="";
		$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_exito, $error_bd);
		if($error_bd!="")
		{
			$error_mostrar_bd.=$error_bd."<br>";
		}
		
		
		
		//CT
		if($es_valido_nombre_archivo_ct  && $bool_hubo_error_query==false
		   && (file_exists($ruta_archivo_ct)==true && $rutaTemporal!=trim($ruta_archivo_ct)))
		{
			//recordatorio: ya se elimina de rechazados al final
			
			$mensaje_errores_ct="";
			$lineas_del_archivo = count(file($ruta_archivo_ct)); 
			$file_ct = fopen($ruta_archivo_ct, 'r') or exit("No se pudo abrir el archivo");
			
			$nlinea=0;
			while (!feof($file_ct) && $bool_hubo_error_query==false) 
			{
				$linea_tmp = fgets($file_ct);
				$linea= explode("\n", $linea_tmp)[0];
				$campos = explode(",", $linea);
				
				//verifica el prestador del documento ct con el asociado
				if(count($campos)==4)
				{
					$array_fecha_reportada=explode("/",$campos[1]);
					$date_reportada_bd=$array_fecha_reportada[2]."-".$array_fecha_reportada[1]."-".$array_fecha_reportada[0];
					
					$sql_exito="";
					$sql_exito.="INSERT INTO gioss_archivo_cargado_CT ";
					$sql_exito.=" ( ";
					$sql_exito.="fecha_validacion_exito ,";
					$sql_exito.="codigo_eapb ,";
					$sql_exito.="codigo_prestador_servicios_salud ,";
					$sql_exito.="fecha_remision ,";
					$sql_exito.="nombre_archivo_reportado ,";
					$sql_exito.="total_registros  ,";
					$sql_exito.="fila ,";
					$sql_exito.="numero_remision, ";
					$sql_exito.="numero_secuencia, ";
					$sql_exito.="hora_validacion ";
					$sql_exito.=" ) ";
					$sql_exito.=" VALUES ";
					$sql_exito.=" ( ";
					$sql_exito.=" '".$fecha_actual."', ";
					$sql_exito.=" '".$cod_eapb."', ";
					$sql_exito.=" '".$cod_prestador."', ";
					$sql_exito.=" '".$date_remision_bd."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[2])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[3])."', ";
					$sql_exito.=" '".$nlinea."', ";
					$sql_exito.=" '".$numero_de_remision."', ";
					$sql_exito.=" '".$numero_secuencia_actual."', ";
					$sql_exito.=" '".$tiempo_actual."' ";
					$sql_exito.=" ) ";
					$sql_exito.=" ; ";
					
					$error_bd="";
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_exito, $error_bd);
					if($error_bd!="")
					{
						$error_mostrar_bd.="ERROR CT EXITOSO: ".$error_bd."<br>";
					}
					
					$mensaje_contador_errores="Se subio en la base de datos, el registro ".($nlinea+1)." de ";
					$mensaje_contador_errores.=" un total de ".$array_contador_registros_buenos["CT"]." registros buenos para CT. ";
					$html_del_mensaje="";
					$html_del_mensaje.="<table>";
					$html_del_mensaje.="<tr>";
					$html_del_mensaje.="<td colspan=\'2\'>";
					$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="</tr>";
					$html_del_mensaje.="<tr>";
					$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
					$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
					$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_contador_errores."</div><div id=\'errores_bd_div\'></div>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="</tr>";
					$html_del_mensaje.="</table>";
					if(connection_aborted()==false)
					{
						echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
						ob_flush();
						flush();
					}
				}//fin if
				$nlinea++;
				
				
			}//fin while
			fclose($file_ct);
		}//fin if si el archivo ct es valido
		//FIN CT
		
		//AF
		if($es_valido_nombre_archivo_af  && $bool_hubo_error_query==false
		   && (file_exists($ruta_archivo_af)==true && $rutaTemporal!=trim($ruta_archivo_af)))
		{
			/*
			 *abajo ya hay una parte donde elimina los rechazados pero despues de que sube los exitosos
			*/
			
			$lineas_del_archivo = count(file($ruta_archivo_af)); 
			$file_af = fopen($ruta_archivo_af, 'r') or exit("No se pudo abrir el archivo");
			
			$nlinea=0;
			while (!feof($file_af)) 
			{
				$linea_tmp = fgets($file_af);
				$linea= explode("\n", $linea_tmp)[0];
				$campos = explode(",", $linea);
				if(count($campos)==17)
				{
					
					$sql_exito="";
					$sql_exito.="INSERT INTO gioss_archivo_cargado_AF ";
					$sql_exito.=" ( ";
					$sql_exito.="fecha_validacion_exito ,";
					$sql_exito.="fecha_remision ,";
					$sql_exito.="codigo_eapb ,";
					$sql_exito.="codigo_archivo ,";
					$sql_exito.="codigo_prestador_servicios_salud ,";
					$sql_exito.="nombre_prestador_servicios_salud ,";
					$sql_exito.="tipo_identificacion_prestador ,";
					$sql_exito.="numero_identificacion_prestador ,";
					$sql_exito.="numero_factura ,";
					$sql_exito.="fecha_expedicion_factura ,";
					$sql_exito.="fecha_inicio_factura ,";
					$sql_exito.="fecha_final_factura ,";
					$sql_exito.="codigo_entidad_eapb ,";
					$sql_exito.="nombre_entidad_eapb ,";
					$sql_exito.="numero_contrato ,";
					$sql_exito.="plan_beneficio ,";
					$sql_exito.="numero_poliza ,";
					$sql_exito.="valor_pago_compartido ,";
					$sql_exito.="valor_comision ,";
					$sql_exito.="valor_descuentos ,";
					$sql_exito.="valor_neto_a_pagar ,";
					$sql_exito.="fila ,";
					$sql_exito.="numero_remision, ";
					$sql_exito.="numero_secuencia ";					
					$sql_exito.=" ) ";
					$sql_exito.=" VALUES ";
					$sql_exito.=" ( ";
					$sql_exito.=" '".$fecha_actual."', ";
					$sql_exito.=" '".$date_remision_bd."', ";
					$sql_exito.=" '".$cod_eapb."', ";
					$sql_exito.=" 'AF".$numero_de_remision."', ";
					$cont_tmp=0;
					while($cont_tmp<count($campos))
					{
						if(procesar_mensaje2($campos[$cont_tmp])=="")
						{
							$sql_exito.=" '0', ";
						}
						else
						{
							$sql_exito.=" '".procesar_mensaje2($campos[$cont_tmp])."', ";
						}
						$cont_tmp++;
					}
					$sql_exito.=" '".$nlinea."', ";
					$sql_exito.=" '".$numero_de_remision."', ";
					$sql_exito.=" '".$numero_secuencia_actual."' ";
					$sql_exito.=" ) ";
					$sql_exito.=" ; ";
					
					$error_bd="";
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_exito, $error_bd);
					if($error_bd!="")
					{
						$error_mostrar_bd.="ERROR AF EXITOSO ".$error_bd."<br>";
					}
					
					$mensaje_contador_errores="Se subio en la base de datos, el registro ".($nlinea+1)." de ";
					$mensaje_contador_errores.=" un total de ".$array_contador_registros_buenos["AF"]." registros buenos para AF. ";
					$html_del_mensaje="";
					$html_del_mensaje.="<table>";
					$html_del_mensaje.="<tr>";
					$html_del_mensaje.="<td colspan=\'2\'>";
					$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="</tr>";
					$html_del_mensaje.="<tr>";
					$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
					$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
					$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_contador_errores."</div><div id=\'errores_bd_div\'></div>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="</tr>";
					$html_del_mensaje.="</table>";
					if(connection_aborted()==false)
					{
						echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
						ob_flush();
						flush();
					}
				}//fin if si el archivo af existe
				$nlinea++;
			}
			fclose($file_af);
		}//fin if si el archivo af es valido
		//FIN AF
		
		//US
		if($es_valido_nombre_archivo_us  && $bool_hubo_error_query==false
		   && (file_exists($ruta_archivo_us)==true && $rutaTemporal!=trim($ruta_archivo_us)))
		{
			/*
			 *abajo ya hay una parte donde elimina los rechazados pero despues de que sube los exitosos
			*/
			
			$lineas_del_archivo = count(file($ruta_archivo_us)); 
			$file_us = fopen($ruta_archivo_us, 'r') or exit("No se pudo abrir el archivo");
			
			$nlinea=0;
			while (!feof($file_us)) 
			{
				$linea_tmp = fgets($file_us);
				$linea= explode("\n", $linea_tmp)[0];
				$campos = explode(",", $linea);
				if(count($campos)==14)
				{					
					$sql_exito="";
					$sql_exito.="INSERT INTO gioss_archivo_cargado_US ";
					$sql_exito.=" ( ";
					$sql_exito.="fecha_validacion_exito ,";
					$sql_exito.="fecha_remision ,";
					$sql_exito.="codigo_eapb ,";
					$sql_exito.="codigo_prestador_servicios_salud ,";
					$sql_exito.="codigo_archivo ,";					
					$sql_exito.="tipo_identificacion_usuario ,";
					$sql_exito.="numero_identificacion_usuario ,";
					$sql_exito.="codigo_entidad_eapb ,";
					$sql_exito.="codigo_tipo_usuario ,";
					$sql_exito.="primer_apellido ,";
					$sql_exito.="segundo_apellido ,";
					$sql_exito.="primer_nombre ,";
					$sql_exito.="segundo_nombre ,";
					$sql_exito.="edad ,";
					$sql_exito.="unidad_medida_edad ,";
					$sql_exito.="sexo ,";
					$sql_exito.="codigo_departamento_residencia ,";
					$sql_exito.="codigo_municipio_residencia ,";
					$sql_exito.="codigo_zona_residencia ,";
					$sql_exito.="fila ,";
					$sql_exito.="numero_remision, ";					
					$sql_exito.="numero_secuencia ";
					$sql_exito.=" ) ";
					$sql_exito.=" VALUES ";
					$sql_exito.=" ( ";
					$sql_exito.=" '".$fecha_actual."', ";
					$sql_exito.=" '".$date_remision_bd."', ";
					$sql_exito.=" '".$cod_eapb."', ";
					$sql_exito.=" '".$cod_prestador."', ";					
					$sql_exito.=" 'US".$numero_de_remision."', ";
					$cont_tmp=0;
					while($cont_tmp<count($campos))
					{
						if(procesar_mensaje2($campos[$cont_tmp])=="")
						{
							$sql_exito.=" '0', ";
						}
						else
						{
							$sql_exito.=" '".procesar_mensaje2($campos[$cont_tmp])."', ";
						}
						$cont_tmp++;
					}
					$sql_exito.=" '".$nlinea."', ";
					$sql_exito.=" '".$numero_de_remision."', ";
					$sql_exito.=" '".$numero_secuencia_actual."' ";
					$sql_exito.=" ) ";
					$sql_exito.=" ; ";
					
					$error_bd="";
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_exito, $error_bd);
					if($error_bd!="")
					{
						$error_mostrar_bd.=" ERROR US EXITOSO ".$error_bd."<br>";
					}
					
					$mensaje_contador_errores="Se subio en la base de datos, el registro ".($nlinea+1)." de ";
					$mensaje_contador_errores.=" un total de ".$array_contador_registros_buenos["US"]." registros buenos para US. ";
					$html_del_mensaje="";
					$html_del_mensaje.="<table>";
					$html_del_mensaje.="<tr>";
					$html_del_mensaje.="<td colspan=\'2\'>";
					$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="</tr>";
					$html_del_mensaje.="<tr>";
					$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
					$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
					$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_contador_errores."</div><div id=\'errores_bd_div\'></div>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="</tr>";
					$html_del_mensaje.="</table>";
					if(connection_aborted()==false)
					{
						echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
						ob_flush();
						flush();
					}
				}//fin verifica numero campos
				$nlinea++;
			}
			fclose($file_us);
		}
		//FIN US
		
		//AC
		if($es_valido_nombre_archivo_ac && $bool_hubo_error_query==false
		   && (file_exists($ruta_archivo_ac)==true && $rutaTemporal!=trim($ruta_archivo_ac)))
		{
			/*
			 *abajo ya hay una parte donde elimina los rechazados pero despues de que sube los exitosos
			*/
			
			$lineas_del_archivo = count(file($ruta_archivo_ac)); 
			$file_ac = fopen($ruta_archivo_ac, 'r') or exit("No se pudo abrir el archivo");
			
			$nlinea=0;
			while (!feof($file_ac)) 
			{
				$linea_tmp = fgets($file_ac);
				$linea= explode("\n", $linea_tmp)[0];
				$campos = explode(",", $linea);
				if(count($campos)==17)
				{
					//obtener sexo y edad de las tablas de afiliados
					$tipo_id_usuario_pes=trim($campos[2]);
					$numero_id_usuario_pes=trim($campos[3]);
					
					//bd existe afiliado, el tipo regimen camp3ak4 1=rc,2=rs,3=rs,4,5=rc
					
					
					$query_edad_sexo_bd="SELECT * FROM gioss_afiliados_eapb_rc WHERE  tipo_id_afiliado= '".$tipo_id_usuario_pes."' AND id_afiliado = '".$numero_id_usuario_pes."' ;";
					$resultados_query_edad_sexo=$coneccionBD->consultar2($query_edad_sexo_bd);
					
					$fecha_nacimiento_1="";
					$sexo_1="";
					if(is_array($resultados_query_edad_sexo))
					{
						$fecha_nacimiento_1=$resultados_query_edad_sexo[0]["fecha_nacimiento"];
						$sexo_1=$resultados_query_edad_sexo[0]["sexo"];
					}
					
				
					$query_edad_sexo_bd="SELECT * FROM gioss_afiliados_regimen_subsidiado WHERE tipo_id_afiliado = '".$tipo_id_usuario_pes."' AND id_afiliado = '".$numero_id_usuario_pes."' ;";
					$resultados_query_edad_sexo=$coneccionBD->consultar2($query_edad_sexo_bd);
					
					$fecha_nacimiento_2="";
					$sexo_2="";
					if(is_array($resultados_query_edad_sexo))
					{
						$fecha_nacimiento_2=$resultados_query_edad_sexo[0]["fecha_nacimiento"];
						$sexo_2=$resultados_query_edad_sexo[0]["sexo"];
					}
				
					$query_edad_sexo_bd="SELECT * FROM gioss_afiliados_eapb_mp WHERE tipo_id_afiliado = '".$tipo_id_usuario_pes."' AND id_afiliado = '".$numero_id_usuario_pes."' ;";
					$resultados_query_edad_sexo=$coneccionBD->consultar2($query_edad_sexo_bd);
					
					$fecha_nacimiento_3="";
					$sexo_3="";
					if(is_array($resultados_query_edad_sexo))
					{
						$fecha_nacimiento_3=$resultados_query_edad_sexo[0]["fecha_nacimiento"];
						$sexo_3=$resultados_query_edad_sexo[0]["sexo"];
					}
					
					$query_edad_sexo_bd="SELECT * FROM gioss_afiliados_eapb_rp WHERE tipo_id_afiliado = '".$tipo_id_usuario_pes."' AND id_afiliado = '".$numero_id_usuario_pes."' ;";
					$resultados_query_edad_sexo=$coneccionBD->consultar2($query_edad_sexo_bd);
					
					$fecha_nacimiento_4="";
					$sexo_4="";
					if(is_array($resultados_query_edad_sexo))
					{
						$fecha_nacimiento_4=$resultados_query_edad_sexo[0]["fecha_nacimiento"];
						$sexo_4=$resultados_query_edad_sexo[0]["sexo"];
					}
					
					$edad_afiliado=0;
					$fecha_nacimiento_definitiva="";
					$array_fecha_definitiva=array();
					$sexo_afiliado="A";
					
					if($sexo_1==$sexo_2
					   && $sexo_1==$sexo_3
					   && $sexo_1==$sexo_4
					   && ($sexo_1=="M"||$sexo_1=="F")					   
					   )
					{
						$sexo_afiliado=$sexo_1;
					}
					else
					{
						$array_comprobacion_sexo=array();
						$array_indice_sexo=array();
						if(!isset($array_comprobacion_sexo[$sexo_1]) && ($sexo_1=="M"||$sexo_1=="F") )
						{
							$array_comprobacion_sexo[$sexo_1]=1;
						}
						else if(($sexo_1=="M"||$sexo_1=="F"))
						{
							$array_comprobacion_sexo[$sexo_1]++;
						}
						
						if(!isset($array_comprobacion_sexo[$sexo_2]) && ($sexo_2=="M"||$sexo_2=="F"))
						{
							$array_comprobacion_sexo[$sexo_2]=1;
						}
						else if($sexo_2=="M"||$sexo_2=="F")
						{
							$array_comprobacion_sexo[$sexo_2]++;
						}
						
						if(!isset($array_comprobacion_sexo[$sexo_3]) && ($sexo_3=="M"||$sexo_3=="F"))
						{
							$array_comprobacion_sexo[$sexo_3]=1;
						}
						else if($sexo_3=="M"||$sexo_3=="F")
						{
							$array_comprobacion_sexo[$sexo_3]++;
						}
						
						if(!isset($array_comprobacion_sexo[$sexo_4]) && ($sexo_4=="M"||$sexo_4=="F"))
						{
							$array_comprobacion_sexo[$sexo_4]=1;
						}
						else if($sexo_4=="M"||$sexo_4=="F")
						{
							$array_comprobacion_sexo[$sexo_4]++;
						}
						
						if(isset($array_comprobacion_sexo["M"]) && isset($array_comprobacion_sexo["F"]))
						{
							if($array_comprobacion_sexo["M"]>$array_comprobacion_sexo["F"])
							{
								$sexo_afiliado="M";
							}
							else
							{
								$sexo_afiliado="F";
							}
						}//fin if
						else if(isset($array_comprobacion_sexo["M"]))
						{
							$sexo_afiliado="M";
						}
						else if(isset($array_comprobacion_sexo["F"]))
						{
							$sexo_afiliado="F";
						}
						
						
					}//fin if comprobacion sexo
					
					$array_fn_1=explode("-",$fecha_nacimiento_1);
					//checkdate mes dia year
					$bool_es_date_fn_1=false;
					if(count($array_fn_1)==3)
					{
						$bool_es_date_fn_1=checkdate($array_fn_1[1],$array_fn_1[2],$array_fn_1[0]);
					}
					
					$array_fn_2=explode("-",$fecha_nacimiento_2);
					//checkdate mes dia year
					$bool_es_date_fn_2=false;
					if(count($array_fn_2)==3)
					{
						$bool_es_date_fn_2=checkdate($array_fn_2[1],$array_fn_2[2],$array_fn_2[0]);
					}
					
					$array_fn_3=explode("-",$fecha_nacimiento_3);
					//checkdate mes dia year
					$bool_es_date_fn_3=false;
					if(count($array_fn_3)==3)
					{
						$bool_es_date_fn_3=checkdate($array_fn_3[1],$array_fn_3[2],$array_fn_3[0]);
					}
					
					$array_fn_4=explode("-",$fecha_nacimiento_4);
					//checkdate mes dia year
					$bool_es_date_fn_4=false;
					if(count($array_fn_4)==3)
					{
						$bool_es_date_fn_4=checkdate($array_fn_4[1],$array_fn_4[2],$array_fn_4[0]);
					}
					
					if($fecha_nacimiento_1==$fecha_nacimiento_2
					   && $fecha_nacimiento_1==$fecha_nacimiento_3
					   && $fecha_nacimiento_1==$fecha_nacimiento_4
					   && $bool_es_date_fn_1==true)
					{
						$fecha_nacimiento_definitiva=$fecha_nacimiento_1;
					}
					else
					{
						$array_comprobacion_fecha_nacimiento=array();
						$array_indice_fecha_nacimiento=array();
						if(!isset($array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1]) && $bool_es_date_fn_1==true )
						{
							$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1]=1;
							$array_indice_fecha_nacimiento[0]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1];
						}
						else if($bool_es_date_fn_1==true)
						{
							$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1]++;
							$array_indice_fecha_nacimiento[0]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1];
						}
						else
						{
							$array_indice_fecha_nacimiento[0]=0;
						}
						
						if(!isset($array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2]) && $bool_es_date_fn_2==true)
						{
							$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2]=1;
							$array_indice_fecha_nacimiento[1]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2];
						}
						else if($bool_es_date_fn_2==true)
						{
							$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2]++;
							$array_indice_fecha_nacimiento[1]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2];
						}
						else
						{
							$array_indice_fecha_nacimiento[1]=0;
						}
						
						if(!isset($array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3]) && $bool_es_date_fn_3==true)
						{
							$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3]=1;
							$array_indice_fecha_nacimiento[2]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3];
						}
						else if($bool_es_date_fn_3==true)
						{
							$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3]++;
							$array_indice_fecha_nacimiento[2]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3];
						}
						else
						{
							$array_indice_fecha_nacimiento[2]=0;
						}
						
						if(!isset($array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4]) && $bool_es_date_fn_4==true)
						{
							$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4]=1;
							$array_indice_fecha_nacimiento[3]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4];
						}
						else if($bool_es_date_fn_4==true)
						{
							$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4]++;
							$array_indice_fecha_nacimiento[3]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4];
						}
						else
						{
							$array_indice_fecha_nacimiento[3]=0;
						}
						$indice_mayor_actual=0;
						$cont_fn=0;
						while($cont_fn<count($array_indice_fecha_nacimiento))
						{
							$cont_fn_2=$cont_fn+1;
							while($cont_fn_2<count($array_indice_fecha_nacimiento))
							{
								if($array_indice_fecha_nacimiento[$cont_fn_2]>$array_indice_fecha_nacimiento[$cont_fn])
								{
									$indice_mayor_actual=$cont_fn_2;
								}
								$cont_fn_2++;
							}
							$cont_fn++;
						}
						if($indice_mayor_actual==0)
						{
							$fecha_nacimiento_definitiva=$fecha_nacimiento_1;
							$array_fecha_definitiva=$array_fn_1;
						}
						if($indice_mayor_actual==1)
						{
							$fecha_nacimiento_definitiva=$fecha_nacimiento_2;
							$array_fecha_definitiva=$array_fn_2;
						}
						if($indice_mayor_actual==2)
						{
							$fecha_nacimiento_definitiva=$fecha_nacimiento_3;
							$array_fecha_definitiva=$array_fn_3;
						}
						if($indice_mayor_actual==3)
						{
							$fecha_nacimiento_definitiva=$fecha_nacimiento_4;
							$array_fecha_definitiva=$array_fn_4;
						}
					}//fin else
					
					//calculo edad con la fecha definitiva
					
					
					
					//fin calculo edad
					try
					{
						if($fecha_nacimiento_definitiva!="")
						{
							$interval_edad = date_diff(date_create($fecha_nacimiento_definitiva),date_create($date_remision_bd));
							$edad_afiliado= intval((float)($interval_edad->format("%r%a"))/365);
							
						}
						else
						{
							$edad_afiliado=999;
						}
					}
					catch(Exception $exc_edad_afiliado)
					{
						if(connection_aborted()==false)
						{
							echo "<script>alert('".procesar_mensaje($e->getMessage())."');</script>";
						}
					}
					//fin obtener sexo y edad de las tablas de afiliados
					
					$sql_exito="";
					$sql_exito.="INSERT INTO gioss_archivo_cargado_AC ";
					$sql_exito.=" ( ";
					$sql_exito.="fecha_validacion_exito ,";
					$sql_exito.="fecha_remision ,";
					$sql_exito.="codigo_eapb ,";
					$sql_exito.="codigo_archivo ,";
					$sql_exito.="numero_factura ,";					
					$sql_exito.="codigo_prestador_servicios_salud ,";
					$sql_exito.="tipo_identificacion_usuario ,";
					$sql_exito.="numero_identificacion_usuario ,";
					$sql_exito.="fecha_atencion ,";
					$sql_exito.="numero_autorizacion ,";
					$sql_exito.="codigo_cups_consulta ,";
					$sql_exito.="finalidad_consulta ,";
					$sql_exito.="causa_externa_consulta ,";
					$sql_exito.="codigo_diagnostico_principal ,";
					$sql_exito.="codigo_relacionado_1 ,";
					$sql_exito.="codigo_relacionado_2 ,";
					$sql_exito.="codigo_relacionado_3 ,";
					$sql_exito.="tipo_diagnostico_principal ,";
					$sql_exito.="valor_consulta ,";
					$sql_exito.="valor_cuota_moderadora ,";
					$sql_exito.="valor_neto_pagado ,";
					$sql_exito.="fila ,";
					$sql_exito.="numero_remision, ";
					$sql_exito.="numero_secuencia, ";
					//columnas nuevas para facilitar reportes
					$sql_exito.="edad_years_afiliado, ";
					$sql_exito.="sexo_afiliado ";
					$sql_exito.=" ) ";
					$sql_exito.=" VALUES ";
					$sql_exito.=" ( ";
					$sql_exito.=" '".$fecha_actual."', ";
					$sql_exito.=" '".$date_remision_bd."', ";
					$sql_exito.=" '".$cod_eapb."', ";				
					$sql_exito.=" 'AC".$numero_de_remision."', ";
					$cont_tmp=0;
					while($cont_tmp<count($campos))
					{
						if(procesar_mensaje2($campos[$cont_tmp])=="")
						{
							$sql_exito.=" '0', ";
						}
						else
						{
							$sql_exito.=" '".procesar_mensaje2($campos[$cont_tmp])."', ";
						}
						$cont_tmp++;
					}
					$sql_exito.=" '".$nlinea."', ";
					$sql_exito.=" '".$numero_de_remision."', ";
					$sql_exito.=" '".$numero_secuencia_actual."', ";
					//campos adicionales para facilitar reportes
					$sql_exito.=" '".$edad_afiliado."', ";
					$sql_exito.=" '".$sexo_afiliado."' ";
					$sql_exito.=" ) ";
					$sql_exito.=" ; ";
					
					$error_bd="";
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_exito, $error_bd);
					if($error_bd!="")
					{
						$error_mostrar_bd.="ERROR AC EXITOSO: ".$error_bd."<br>";
					}
					
					$mensaje_contador_errores="Se subio en la base de datos, el registro ".($nlinea+1)." de ";
					$mensaje_contador_errores.=" un total de ".$array_contador_registros_buenos["AC"]." registros buenos para AC. ";
					$html_del_mensaje="";
					$html_del_mensaje.="<table>";
					$html_del_mensaje.="<tr>";
					$html_del_mensaje.="<td colspan=\'2\'>";
					$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="</tr>";
					$html_del_mensaje.="<tr>";
					$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
					$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
					$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_contador_errores."</div><div id=\'errores_bd_div\'></div>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="</tr>";
					$html_del_mensaje.="</table>";
					if(connection_aborted()==false)
					{
						echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
						ob_flush();
						flush();
					}
					
				}//fin verifica numero campos
				$nlinea++;
			}
			fclose($file_ac);
		}
		//FIN AC
		
		//AH
		if($es_valido_nombre_archivo_ah && $bool_hubo_error_query==false
		   && (file_exists($ruta_archivo_ah)==true && $rutaTemporal!=trim($ruta_archivo_ah)))
		{
			/*
			 *abajo ya hay una parte donde elimina los rechazados pero despues de que sube los exitosos
			*/
			
			$lineas_del_archivo = count(file($ruta_archivo_ah)); 
			$file_ah = fopen($ruta_archivo_ah, 'r') or exit("No se pudo abrir el archivo");
			
			$nlinea=0;
			while (!feof($file_ah)) 
			{
				$linea_tmp = fgets($file_ah);
				$linea= explode("\n", $linea_tmp)[0];
				$campos = explode(",", $linea);
				if(count($campos)==19)
				{				
					//obtener sexo y edad de las tablas de afiliados
					$tipo_id_usuario_pes=trim($campos[2]);
					$numero_id_usuario_pes=trim($campos[3]);
					
					//bd existe afiliado, el tipo regimen camp3ak4 1=rc,2=rs,3=rs,4,5=rc
					
					
					$query_edad_sexo_bd="SELECT * FROM gioss_afiliados_eapb_rc WHERE  tipo_id_afiliado= '".$tipo_id_usuario_pes."' AND id_afiliado = '".$numero_id_usuario_pes."' ;";
					$resultados_query_edad_sexo=$coneccionBD->consultar2($query_edad_sexo_bd);
					
					$fecha_nacimiento_1="";
					$sexo_1="";
					if(is_array($resultados_query_edad_sexo))
					{
						$fecha_nacimiento_1=$resultados_query_edad_sexo[0]["fecha_nacimiento"];
						$sexo_1=$resultados_query_edad_sexo[0]["sexo"];
					}
					
				
					$query_edad_sexo_bd="SELECT * FROM gioss_afiliados_regimen_subsidiado WHERE tipo_id_afiliado = '".$tipo_id_usuario_pes."' AND id_afiliado = '".$numero_id_usuario_pes."' ;";
					$resultados_query_edad_sexo=$coneccionBD->consultar2($query_edad_sexo_bd);
					
					$fecha_nacimiento_2="";
					$sexo_2="";
					if(is_array($resultados_query_edad_sexo))
					{
						$fecha_nacimiento_2=$resultados_query_edad_sexo[0]["fecha_nacimiento"];
						$sexo_2=$resultados_query_edad_sexo[0]["sexo"];
					}
				
					$query_edad_sexo_bd="SELECT * FROM gioss_afiliados_eapb_mp WHERE tipo_id_afiliado = '".$tipo_id_usuario_pes."' AND id_afiliado = '".$numero_id_usuario_pes."' ;";
					$resultados_query_edad_sexo=$coneccionBD->consultar2($query_edad_sexo_bd);
					
					$fecha_nacimiento_3="";
					$sexo_3="";
					if(is_array($resultados_query_edad_sexo))
					{
						$fecha_nacimiento_3=$resultados_query_edad_sexo[0]["fecha_nacimiento"];
						$sexo_3=$resultados_query_edad_sexo[0]["sexo"];
					}
					
					$query_edad_sexo_bd="SELECT * FROM gioss_afiliados_eapb_rp WHERE tipo_id_afiliado = '".$tipo_id_usuario_pes."' AND id_afiliado = '".$numero_id_usuario_pes."' ;";
					$resultados_query_edad_sexo=$coneccionBD->consultar2($query_edad_sexo_bd);
					
					$fecha_nacimiento_4="";
					$sexo_4="";
					if(is_array($resultados_query_edad_sexo))
					{
						$fecha_nacimiento_4=$resultados_query_edad_sexo[0]["fecha_nacimiento"];
						$sexo_4=$resultados_query_edad_sexo[0]["sexo"];
					}
					
					$edad_afiliado=0;
					$fecha_nacimiento_definitiva="";
					$array_fecha_definitiva=array();
					$sexo_afiliado="A";
					
					if($sexo_1==$sexo_2
					   && $sexo_1==$sexo_3
					   && $sexo_1==$sexo_4
					   && ($sexo_1=="M"||$sexo_1=="F")					   
					   )
					{
						$sexo_afiliado=$sexo_1;
					}
					else
					{
						$array_comprobacion_sexo=array();
						$array_indice_sexo=array();
						if(!isset($array_comprobacion_sexo[$sexo_1]) && ($sexo_1=="M"||$sexo_1=="F") )
						{
							$array_comprobacion_sexo[$sexo_1]=1;
						}
						else if(($sexo_1=="M"||$sexo_1=="F"))
						{
							$array_comprobacion_sexo[$sexo_1]++;
						}
						
						if(!isset($array_comprobacion_sexo[$sexo_2]) && ($sexo_2=="M"||$sexo_2=="F"))
						{
							$array_comprobacion_sexo[$sexo_2]=1;
						}
						else if($sexo_2=="M"||$sexo_2=="F")
						{
							$array_comprobacion_sexo[$sexo_2]++;
						}
						
						if(!isset($array_comprobacion_sexo[$sexo_3]) && ($sexo_3=="M"||$sexo_3=="F"))
						{
							$array_comprobacion_sexo[$sexo_3]=1;
						}
						else if($sexo_3=="M"||$sexo_3=="F")
						{
							$array_comprobacion_sexo[$sexo_3]++;
						}
						
						if(!isset($array_comprobacion_sexo[$sexo_4]) && ($sexo_4=="M"||$sexo_4=="F"))
						{
							$array_comprobacion_sexo[$sexo_4]=1;
						}
						else if($sexo_4=="M"||$sexo_4=="F")
						{
							$array_comprobacion_sexo[$sexo_4]++;
						}
						
						if(isset($array_comprobacion_sexo["M"]) && isset($array_comprobacion_sexo["F"]))
						{
							if($array_comprobacion_sexo["M"]>$array_comprobacion_sexo["F"])
							{
								$sexo_afiliado="M";
							}
							else
							{
								$sexo_afiliado="F";
							}
						}//fin if
						else if(isset($array_comprobacion_sexo["M"]))
						{
							$sexo_afiliado="M";
						}
						else if(isset($array_comprobacion_sexo["F"]))
						{
							$sexo_afiliado="F";
						}
						
						
					}//fin if comprobacion sexo
					
					$array_fn_1=explode("-",$fecha_nacimiento_1);
					//checkdate mes dia year
					$bool_es_date_fn_1=false;
					if(count($array_fn_1)==3)
					{
						$bool_es_date_fn_1=checkdate($array_fn_1[1],$array_fn_1[2],$array_fn_1[0]);
					}
					
					$array_fn_2=explode("-",$fecha_nacimiento_2);
					//checkdate mes dia year
					$bool_es_date_fn_2=false;
					if(count($array_fn_2)==3)
					{
						$bool_es_date_fn_2=checkdate($array_fn_2[1],$array_fn_2[2],$array_fn_2[0]);
					}
					
					$array_fn_3=explode("-",$fecha_nacimiento_3);
					//checkdate mes dia year
					$bool_es_date_fn_3=false;
					if(count($array_fn_3)==3)
					{
						$bool_es_date_fn_3=checkdate($array_fn_3[1],$array_fn_3[2],$array_fn_3[0]);
					}
					
					$array_fn_4=explode("-",$fecha_nacimiento_4);
					//checkdate mes dia year
					$bool_es_date_fn_4=false;
					if(count($array_fn_4)==3)
					{
						$bool_es_date_fn_4=checkdate($array_fn_4[1],$array_fn_4[2],$array_fn_4[0]);
					}
					
					if($fecha_nacimiento_1==$fecha_nacimiento_2
					   && $fecha_nacimiento_1==$fecha_nacimiento_3
					   && $fecha_nacimiento_1==$fecha_nacimiento_4
					   && $bool_es_date_fn_1==true)
					{
						$fecha_nacimiento_definitiva=$fecha_nacimiento_1;
					}
					else
					{
						$array_comprobacion_fecha_nacimiento=array();
						$array_indice_fecha_nacimiento=array();
						if(!isset($array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1]) && $bool_es_date_fn_1==true )
						{
							$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1]=1;
							$array_indice_fecha_nacimiento[0]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1];
						}
						else if($bool_es_date_fn_1==true)
						{
							$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1]++;
							$array_indice_fecha_nacimiento[0]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1];
						}
						else
						{
							$array_indice_fecha_nacimiento[0]=0;
						}
						
						if(!isset($array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2]) && $bool_es_date_fn_2==true)
						{
							$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2]=1;
							$array_indice_fecha_nacimiento[1]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2];
						}
						else if($bool_es_date_fn_2==true)
						{
							$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2]++;
							$array_indice_fecha_nacimiento[1]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2];
						}
						else
						{
							$array_indice_fecha_nacimiento[1]=0;
						}
						
						if(!isset($array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3]) && $bool_es_date_fn_3==true)
						{
							$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3]=1;
							$array_indice_fecha_nacimiento[2]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3];
						}
						else if($bool_es_date_fn_3==true)
						{
							$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3]++;
							$array_indice_fecha_nacimiento[2]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3];
						}
						else
						{
							$array_indice_fecha_nacimiento[2]=0;
						}
						
						if(!isset($array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4]) && $bool_es_date_fn_4==true)
						{
							$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4]=1;
							$array_indice_fecha_nacimiento[3]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4];
						}
						else if($bool_es_date_fn_4==true)
						{
							$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4]++;
							$array_indice_fecha_nacimiento[3]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4];
						}
						else
						{
							$array_indice_fecha_nacimiento[3]=0;
						}
						$indice_mayor_actual=0;
						$cont_fn=0;
						while($cont_fn<count($array_indice_fecha_nacimiento))
						{
							$cont_fn_2=$cont_fn+1;
							while($cont_fn_2<count($array_indice_fecha_nacimiento))
							{
								if($array_indice_fecha_nacimiento[$cont_fn_2]>$array_indice_fecha_nacimiento[$cont_fn])
								{
									$indice_mayor_actual=$cont_fn_2;
								}
								$cont_fn_2++;
							}
							$cont_fn++;
						}
						if($indice_mayor_actual==0)
						{
							$fecha_nacimiento_definitiva=$fecha_nacimiento_1;
							$array_fecha_definitiva=$array_fn_1;
						}
						if($indice_mayor_actual==1)
						{
							$fecha_nacimiento_definitiva=$fecha_nacimiento_2;
							$array_fecha_definitiva=$array_fn_2;
						}
						if($indice_mayor_actual==2)
						{
							$fecha_nacimiento_definitiva=$fecha_nacimiento_3;
							$array_fecha_definitiva=$array_fn_3;
						}
						if($indice_mayor_actual==3)
						{
							$fecha_nacimiento_definitiva=$fecha_nacimiento_4;
							$array_fecha_definitiva=$array_fn_4;
						}
					}//fin else
					
					//calculo edad con la fecha definitiva
					
					
					
					//fin calculo edad
					try
					{
						if($fecha_nacimiento_definitiva!="")
						{
							$interval_edad = date_diff(date_create($fecha_nacimiento_definitiva),date_create($date_remision_bd));
							$edad_afiliado= intval((float)($interval_edad->format("%r%a"))/365);
							
						}
						else
						{
							$edad_afiliado=999;
						}
					}
					catch(Exception $exc_edad_afiliado)
					{
						if(connection_aborted()==false)
						{
							echo "<script>alert('".procesar_mensaje($e->getMessage())."');</script>";
						}
						
					}
					//fin obtener sexo y edad de las tablas de afiliados
					
					$sql_exito="";
					$sql_exito.="INSERT INTO gioss_archivo_cargado_AH ";
					$sql_exito.=" ( ";
					$sql_exito.="fecha_validacion_exito ,";
					$sql_exito.="fecha_remision ,";
					$sql_exito.="codigo_eapb ,";
					$sql_exito.="codigo_archivo ,";
					$sql_exito.="numero_factura ,";					
					$sql_exito.="codigo_prestador_servicios_salud ,";
					$sql_exito.="tipo_identificacion_usuario ,";
					$sql_exito.="numero_identificacion_usuario ,";
					$sql_exito.="via_ingreso_institucion ,";
					$sql_exito.="fecha_ingreso ,";
					$sql_exito.="Hora_ingreso ,";
					$sql_exito.="numero_autorizacion ,";
					$sql_exito.="causa_externa ,";
					$sql_exito.="codigo_diagnostico_principal_ingreso ,";
					$sql_exito.="codigo_diagnostico_principal_egreso ,";
					$sql_exito.="codigo_relacionado_egreso_1 ,";
					$sql_exito.="codigo_relacionado_egreso_2 ,";
					$sql_exito.="codigo_relacionado_egreso_3 ,";
					$sql_exito.="codigo_diagnostico_complicacion ,";
					$sql_exito.="estado_a_salida ,";
					$sql_exito.="codigo_diagnostico_muerte ,";
					$sql_exito.="Fecha_egreso ,";
					$sql_exito.="Hora_egreso ,";
					$sql_exito.="fila ,";
					$sql_exito.="numero_remision, ";
					$sql_exito.="numero_secuencia, ";
					//columnas nuevas para facilitar reportes
					$sql_exito.="edad_years_afiliado, ";
					$sql_exito.="sexo_afiliado ";
					$sql_exito.=" ) ";
					$sql_exito.=" VALUES ";
					$sql_exito.=" ( ";
					$sql_exito.=" '".$fecha_actual."', ";
					$sql_exito.=" '".$date_remision_bd."', ";
					$sql_exito.=" '".$cod_eapb."', ";				
					$sql_exito.=" 'AH".$numero_de_remision."', ";
					$cont_tmp=0;
					while($cont_tmp<count($campos))
					{
						if(procesar_mensaje2($campos[$cont_tmp])=="")
						{
							$sql_exito.=" '0', ";
						}
						else
						{
							$sql_exito.=" '".procesar_mensaje2($campos[$cont_tmp])."', ";
						}
						$cont_tmp++;
					}
					$sql_exito.=" '".$nlinea."', ";
					$sql_exito.=" '".$numero_de_remision."', ";
					$sql_exito.=" '".$numero_secuencia_actual."', ";
					//campos adicionales para facilitar reportes
					$sql_exito.=" '".$edad_afiliado."', ";
					$sql_exito.=" '".$sexo_afiliado."' ";
					$sql_exito.=" ) ";
					$sql_exito.=" ; ";
					
					$error_bd="";
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_exito, $error_bd);
					if($error_bd!="")
					{
						$error_mostrar_bd.="ERROR AH EXITOSO ".$error_bd."<br>";
					}
					
					$mensaje_contador_errores="Se subio en la base de datos, el registro ".($nlinea+1)." de ";
					$mensaje_contador_errores.=" un total de ".$array_contador_registros_buenos["AH"]." registros buenos para AH. ";
					$html_del_mensaje="";
					$html_del_mensaje.="<table>";
					$html_del_mensaje.="<tr>";
					$html_del_mensaje.="<td colspan=\'2\'>";
					$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="</tr>";
					$html_del_mensaje.="<tr>";
					$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
					$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
					$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_contador_errores."</div><div id=\'errores_bd_div\'></div>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="</tr>";
					$html_del_mensaje.="</table>";
					if(connection_aborted()==false)
					{
						echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
						ob_flush();
						flush();
					}
					
				}//fin verifica numero campos
				$nlinea++;
			}
			fclose($file_ah);
		}
		//FIN AH
		
		//AD
		if($es_valido_nombre_archivo_ad && $bool_hubo_error_query==false
		   && (file_exists($ruta_archivo_ad)==true && $rutaTemporal!=trim($ruta_archivo_ad)))
		{
			/*
			 *abajo ya hay una parte donde elimina los rechazados pero despues de que sube los exitosos
			*/
			
			$mensaje_errores_ad="";
			$lineas_del_archivo = count(file($ruta_archivo_ad)); 
			$file_ad = fopen($ruta_archivo_ad, 'r') or exit("No se pudo abrir el archivo");
			
			$nlinea=0;
			while (!feof($file_ad)) 
			{
				$linea_tmp = fgets($file_ad);
				$linea= explode("\n", $linea_tmp)[0];
				$campos = explode(",", $linea);
				if(count($campos)==6)
				{
					
					$sql_exito="";
					$sql_exito.="INSERT INTO gioss_archivo_cargado_AD ";
					$sql_exito.=" ( ";
					$sql_exito.="fecha_validacion_exito ,";
					$sql_exito.="fecha_remision ,";
					$sql_exito.="codigo_eapb ,";
					$sql_exito.="codigo_archivo ,";
					$sql_exito.="numero_factura ,";					
					$sql_exito.="codigo_prestador_servicios_salud ,";
					$sql_exito.="codigo_concepto ,";
					$sql_exito.="cantidad ,";
					$sql_exito.="valor_unitario ,";
					$sql_exito.="valor_total_concepto ,";
					$sql_exito.="fila ,";
					$sql_exito.="numero_remision, ";					
					$sql_exito.="numero_secuencia ";
					$sql_exito.=" ) ";
					$sql_exito.=" VALUES ";
					$sql_exito.=" ( ";
					$sql_exito.=" '".$fecha_actual."', ";
					$sql_exito.=" '".$date_remision_bd."', ";
					$sql_exito.=" '".$cod_eapb."', ";				
					$sql_exito.=" 'AD".$numero_de_remision."', ";
					$cont_tmp=0;
					while($cont_tmp<count($campos))
					{
						if(procesar_mensaje2($campos[$cont_tmp])=="")
						{
							$sql_exito.=" '0', ";
						}
						else
						{
							$sql_exito.=" '".procesar_mensaje2($campos[$cont_tmp])."', ";
						}
						$cont_tmp++;
					}
					$sql_exito.=" '".$nlinea."', ";
					$sql_exito.=" '".$numero_de_remision."', ";
					$sql_exito.=" '".$numero_secuencia_actual."' ";
					$sql_exito.=" ) ";
					$sql_exito.=" ; ";
					
					$error_bd="";
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_exito, $error_bd);
					if($error_bd!="")
					{
						$error_mostrar_bd.="ERROR AD EXITOSO ".$error_bd."<br>";
					}
					
					$mensaje_contador_errores="Se subio en la base de datos, el registro ".($nlinea+1)." de ";
					$mensaje_contador_errores.=" un total de ".$array_contador_registros_buenos["AD"]." registros buenos para AD. ";
					$html_del_mensaje="";
					$html_del_mensaje.="<table>";
					$html_del_mensaje.="<tr>";
					$html_del_mensaje.="<td colspan=\'2\'>";
					$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="</tr>";
					$html_del_mensaje.="<tr>";
					$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
					$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
					$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_contador_errores."</div><div id=\'errores_bd_div\'></div>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="</tr>";
					$html_del_mensaje.="</table>";
					if(connection_aborted()==false)
					{
						echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
						ob_flush();
						flush();
					}
				}//fin verifica numero campos
				$nlinea++;
			}
			fclose($file_ad);
		}
		//FIN AD
		
		//AP
		if($es_valido_nombre_archivo_ap && $bool_hubo_error_query==false
		   && (file_exists($ruta_archivo_ap)==true && $rutaTemporal!=trim($ruta_archivo_ap)))
		{
			/*
			 *abajo ya hay una parte donde elimina los rechazados pero despues de que sube los exitosos
			*/
			
			$lineas_del_archivo = count(file($ruta_archivo_ap)); 
			$file_ap = fopen($ruta_archivo_ap, 'r') or exit("No se pudo abrir el archivo");
			
			$nlinea=0;
			while (!feof($file_ap)) 
			{
				$linea_tmp = fgets($file_ap);
				$linea= explode("\n", $linea_tmp)[0];
				$campos = explode(",", $linea);
				if(count($campos)==15)
				{
					//OBTENER SEXO Y EDAD DE LAS TABLAS DE AFILIADOS
					$tipo_id_usuario_pes=trim($campos[2]);
					$numero_id_usuario_pes=trim($campos[3]);
					
					//bd existe afiliado, el tipo regimen camp3ak4 1=rc,2=rs,3=rs,4,5=rc
					
					
					$query_edad_sexo_bd="SELECT * FROM gioss_afiliados_eapb_rc WHERE  tipo_id_afiliado= '".$tipo_id_usuario_pes."' AND id_afiliado = '".$numero_id_usuario_pes."' ;";
					$resultados_query_edad_sexo=$coneccionBD->consultar2($query_edad_sexo_bd);
					
					$fecha_nacimiento_1="";
					$sexo_1="";
					if(is_array($resultados_query_edad_sexo))
					{
						$fecha_nacimiento_1=$resultados_query_edad_sexo[0]["fecha_nacimiento"];
						$sexo_1=$resultados_query_edad_sexo[0]["sexo"];
					}
					
				
					$query_edad_sexo_bd="SELECT * FROM gioss_afiliados_regimen_subsidiado WHERE tipo_id_afiliado = '".$tipo_id_usuario_pes."' AND id_afiliado = '".$numero_id_usuario_pes."' ;";
					$resultados_query_edad_sexo=$coneccionBD->consultar2($query_edad_sexo_bd);
					
					$fecha_nacimiento_2="";
					$sexo_2="";
					if(is_array($resultados_query_edad_sexo))
					{
						$fecha_nacimiento_2=$resultados_query_edad_sexo[0]["fecha_nacimiento"];
						$sexo_2=$resultados_query_edad_sexo[0]["sexo"];
					}
				
					$query_edad_sexo_bd="SELECT * FROM gioss_afiliados_eapb_mp WHERE tipo_id_afiliado = '".$tipo_id_usuario_pes."' AND id_afiliado = '".$numero_id_usuario_pes."' ;";
					$resultados_query_edad_sexo=$coneccionBD->consultar2($query_edad_sexo_bd);
					
					$fecha_nacimiento_3="";
					$sexo_3="";
					if(is_array($resultados_query_edad_sexo))
					{
						$fecha_nacimiento_3=$resultados_query_edad_sexo[0]["fecha_nacimiento"];
						$sexo_3=$resultados_query_edad_sexo[0]["sexo"];
					}
					
					$query_edad_sexo_bd="SELECT * FROM gioss_afiliados_eapb_rp WHERE tipo_id_afiliado = '".$tipo_id_usuario_pes."' AND id_afiliado = '".$numero_id_usuario_pes."' ;";
					$resultados_query_edad_sexo=$coneccionBD->consultar2($query_edad_sexo_bd);
					
					$fecha_nacimiento_4="";
					$sexo_4="";
					if(is_array($resultados_query_edad_sexo))
					{
						$fecha_nacimiento_4=$resultados_query_edad_sexo[0]["fecha_nacimiento"];
						$sexo_4=$resultados_query_edad_sexo[0]["sexo"];
					}
					
					$edad_afiliado=0;
					$fecha_nacimiento_definitiva="";
					$array_fecha_definitiva=array();
					$sexo_afiliado="A";
					
					if($sexo_1==$sexo_2
					   && $sexo_1==$sexo_3
					   && $sexo_1==$sexo_4
					   && ($sexo_1=="M"||$sexo_1=="F")					   
					   )
					{
						$sexo_afiliado=$sexo_1;
					}
					else
					{
						$array_comprobacion_sexo=array();
						$array_indice_sexo=array();
						if(!isset($array_comprobacion_sexo[$sexo_1]) && ($sexo_1=="M"||$sexo_1=="F") )
						{
							$array_comprobacion_sexo[$sexo_1]=1;
						}
						else if(($sexo_1=="M"||$sexo_1=="F"))
						{
							$array_comprobacion_sexo[$sexo_1]++;
						}
						
						if(!isset($array_comprobacion_sexo[$sexo_2]) && ($sexo_2=="M"||$sexo_2=="F"))
						{
							$array_comprobacion_sexo[$sexo_2]=1;
						}
						else if($sexo_2=="M"||$sexo_2=="F")
						{
							$array_comprobacion_sexo[$sexo_2]++;
						}
						
						if(!isset($array_comprobacion_sexo[$sexo_3]) && ($sexo_3=="M"||$sexo_3=="F"))
						{
							$array_comprobacion_sexo[$sexo_3]=1;
						}
						else if($sexo_3=="M"||$sexo_3=="F")
						{
							$array_comprobacion_sexo[$sexo_3]++;
						}
						
						if(!isset($array_comprobacion_sexo[$sexo_4]) && ($sexo_4=="M"||$sexo_4=="F"))
						{
							$array_comprobacion_sexo[$sexo_4]=1;
						}
						else if($sexo_4=="M"||$sexo_4=="F")
						{
							$array_comprobacion_sexo[$sexo_4]++;
						}
						
						if(isset($array_comprobacion_sexo["M"]) && isset($array_comprobacion_sexo["F"]))
						{
							if($array_comprobacion_sexo["M"]>$array_comprobacion_sexo["F"])
							{
								$sexo_afiliado="M";
							}
							else
							{
								$sexo_afiliado="F";
							}
						}//fin if
						else if(isset($array_comprobacion_sexo["M"]))
						{
							$sexo_afiliado="M";
						}
						else if(isset($array_comprobacion_sexo["F"]))
						{
							$sexo_afiliado="F";
						}
						
						
					}//fin if comprobacion sexo
					
					$array_fn_1=explode("-",$fecha_nacimiento_1);
					//checkdate mes dia year
					$bool_es_date_fn_1=false;
					if(count($array_fn_1)==3)
					{
						$bool_es_date_fn_1=checkdate($array_fn_1[1],$array_fn_1[2],$array_fn_1[0]);
					}
					
					$array_fn_2=explode("-",$fecha_nacimiento_2);
					//checkdate mes dia year
					$bool_es_date_fn_2=false;
					if(count($array_fn_2)==3)
					{
						$bool_es_date_fn_2=checkdate($array_fn_2[1],$array_fn_2[2],$array_fn_2[0]);
					}
					
					$array_fn_3=explode("-",$fecha_nacimiento_3);
					//checkdate mes dia year
					$bool_es_date_fn_3=false;
					if(count($array_fn_3)==3)
					{
						$bool_es_date_fn_3=checkdate($array_fn_3[1],$array_fn_3[2],$array_fn_3[0]);
					}
					
					$array_fn_4=explode("-",$fecha_nacimiento_4);
					//checkdate mes dia year
					$bool_es_date_fn_4=false;
					if(count($array_fn_4)==3)
					{
						$bool_es_date_fn_4=checkdate($array_fn_4[1],$array_fn_4[2],$array_fn_4[0]);
					}
					
					if($fecha_nacimiento_1==$fecha_nacimiento_2
					   && $fecha_nacimiento_1==$fecha_nacimiento_3
					   && $fecha_nacimiento_1==$fecha_nacimiento_4
					   && $bool_es_date_fn_1==true)
					{
						$fecha_nacimiento_definitiva=$fecha_nacimiento_1;
					}
					else
					{
						$array_comprobacion_fecha_nacimiento=array();
						$array_indice_fecha_nacimiento=array();
						if(!isset($array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1]) && $bool_es_date_fn_1==true )
						{
							$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1]=1;
							$array_indice_fecha_nacimiento[0]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1];
						}
						else if($bool_es_date_fn_1==true)
						{
							$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1]++;
							$array_indice_fecha_nacimiento[0]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1];
						}
						else
						{
							$array_indice_fecha_nacimiento[0]=0;
						}
						
						if(!isset($array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2]) && $bool_es_date_fn_2==true)
						{
							$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2]=1;
							$array_indice_fecha_nacimiento[1]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2];
						}
						else if($bool_es_date_fn_2==true)
						{
							$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2]++;
							$array_indice_fecha_nacimiento[1]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2];
						}
						else
						{
							$array_indice_fecha_nacimiento[1]=0;
						}
						
						if(!isset($array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3]) && $bool_es_date_fn_3==true)
						{
							$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3]=1;
							$array_indice_fecha_nacimiento[2]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3];
						}
						else if($bool_es_date_fn_3==true)
						{
							$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3]++;
							$array_indice_fecha_nacimiento[2]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3];
						}
						else
						{
							$array_indice_fecha_nacimiento[2]=0;
						}
						
						if(!isset($array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4]) && $bool_es_date_fn_4==true)
						{
							$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4]=1;
							$array_indice_fecha_nacimiento[3]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4];
						}
						else if($bool_es_date_fn_4==true)
						{
							$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4]++;
							$array_indice_fecha_nacimiento[3]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4];
						}
						else
						{
							$array_indice_fecha_nacimiento[3]=0;
						}
						$indice_mayor_actual=0;
						$cont_fn=0;
						while($cont_fn<count($array_indice_fecha_nacimiento))
						{
							$cont_fn_2=$cont_fn+1;
							while($cont_fn_2<count($array_indice_fecha_nacimiento))
							{
								if($array_indice_fecha_nacimiento[$cont_fn_2]>$array_indice_fecha_nacimiento[$cont_fn])
								{
									$indice_mayor_actual=$cont_fn_2;
								}
								$cont_fn_2++;
							}
							$cont_fn++;
						}
						if($indice_mayor_actual==0)
						{
							$fecha_nacimiento_definitiva=$fecha_nacimiento_1;
							$array_fecha_definitiva=$array_fn_1;
						}
						if($indice_mayor_actual==1)
						{
							$fecha_nacimiento_definitiva=$fecha_nacimiento_2;
							$array_fecha_definitiva=$array_fn_2;
						}
						if($indice_mayor_actual==2)
						{
							$fecha_nacimiento_definitiva=$fecha_nacimiento_3;
							$array_fecha_definitiva=$array_fn_3;
						}
						if($indice_mayor_actual==3)
						{
							$fecha_nacimiento_definitiva=$fecha_nacimiento_4;
							$array_fecha_definitiva=$array_fn_4;
						}
					}//fin else
					
					//calculo edad con la fecha definitiva
					
					
					
					//fin calculo edad
					try
					{
						if($fecha_nacimiento_definitiva!="")
						{
							$interval_edad = date_diff(date_create($fecha_nacimiento_definitiva),date_create($date_remision_bd));
							$edad_afiliado= intval((float)($interval_edad->format("%r%a"))/365);
							
						}
						else
						{
							$edad_afiliado=999;
						}
					}
					catch(Exception $exc_edad_afiliado)
					{
						if(connection_aborted()==false)
						{
							echo "<script>alert('".procesar_mensaje($e->getMessage())."');</script>";
						}
						
					}
					//FIN OBTENER SEXO Y EDAD DE LAS TABLAS DE AFILIADOS
					
					$sql_exito="";
					$sql_exito.="INSERT INTO gioss_archivo_cargado_AP ";
					$sql_exito.=" ( ";
					$sql_exito.="fecha_validacion_exito ,";
					$sql_exito.="fecha_remision ,";
					$sql_exito.="codigo_eapb ,";
					$sql_exito.="codigo_archivo ,";
					$sql_exito.="numero_factura ,";					
					$sql_exito.="codigo_prestador_servicios_salud ,";
					$sql_exito.="tipo_identificacion_usuario ,";
					$sql_exito.="numero_identificacion_usuario ,";
					$sql_exito.="fecha_procedimiento ,";
					$sql_exito.="numero_autorizacion ,";
					$sql_exito.="codigo_cups_procedimiento ,";
					$sql_exito.="ambito_realizacion_procedimiento ,";
					$sql_exito.="finalidad_procedimiento ,";
					$sql_exito.="personal_que_atiende ,";
					$sql_exito.="diagnostico_principal ,";
					$sql_exito.="diagnostico_relacionado ,";
					$sql_exito.="diagnostico_complicacion ,";
					$sql_exito.="forma_realizacion_acto_quirurgico ,";
					$sql_exito.="valor_procedimiento ,";
					$sql_exito.="fila ,";
					$sql_exito.="numero_remision, ";
					$sql_exito.="numero_secuencia, ";
					//columnas nuevas para facilitar reportes
					$sql_exito.="edad_years_afiliado, ";
					$sql_exito.="sexo_afiliado ";
					$sql_exito.=" ) ";
					$sql_exito.=" VALUES ";
					$sql_exito.=" ( ";
					$sql_exito.=" '".$fecha_actual."', ";
					$sql_exito.=" '".$date_remision_bd."', ";
					$sql_exito.=" '".$cod_eapb."', ";				
					$sql_exito.=" 'AP".$numero_de_remision."', ";
					$cont_tmp=0;
					while($cont_tmp<count($campos))
					{
						if(procesar_mensaje2($campos[$cont_tmp])=="")
						{
							$sql_exito.=" '0', ";
						}
						else
						{
							$sql_exito.=" '".procesar_mensaje2($campos[$cont_tmp])."', ";
						}
						$cont_tmp++;
					}
					$sql_exito.=" '".$nlinea."', ";
					$sql_exito.=" '".$numero_de_remision."', ";					
					$sql_exito.=" '".$numero_secuencia_actual."', ";
					//campos adicionales para facilitar reportes
					$sql_exito.=" '".$edad_afiliado."', ";
					$sql_exito.=" '".$sexo_afiliado."' ";
					$sql_exito.=" ) ";
					$sql_exito.=" ; ";
					
					$error_bd="";
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_exito, $error_bd);
					if($error_bd!="")
					{
						$error_mostrar_bd.="ERROR AP EXITOSO ".$error_bd."<br>";
					}
					
					$mensaje_contador_errores="Se subio en la base de datos, el registro ".($nlinea+1)." de ";
					$mensaje_contador_errores.=" un total de ".$array_contador_registros_buenos["AP"]." registros buenos para AP. ";
					$html_del_mensaje="";
					$html_del_mensaje.="<table>";
					$html_del_mensaje.="<tr>";
					$html_del_mensaje.="<td colspan=\'2\'>";
					$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="</tr>";
					$html_del_mensaje.="<tr>";
					$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
					$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
					$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_contador_errores."</div><div id=\'errores_bd_div\'></div>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="</tr>";
					$html_del_mensaje.="</table>";
					if(connection_aborted()==false)
					{
						echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
						ob_flush();
						flush();
					}
				}//fin verifica numero campos
				$nlinea++;
			}
			fclose($file_ap);
		}
		//FIN AP
		
		//AU
		if($es_valido_nombre_archivo_au && $bool_hubo_error_query==false
		   && (file_exists($ruta_archivo_au)==true && $rutaTemporal!=trim($ruta_archivo_au)))
		{
			/*
			 *abajo ya hay una parte donde elimina los rechazados pero despues de que sube los exitosos
			*/
			
			$lineas_del_archivo = count(file($ruta_archivo_au)); 
			$file_au = fopen($ruta_archivo_au, 'r') or exit("No se pudo abrir el archivo");
			
			$nlinea=0;
			while (!feof($file_au)) 
			{
				$linea_tmp = fgets($file_au);
				$linea= explode("\n", $linea_tmp)[0];
				$campos = explode(",", $linea);
				if(count($campos)==17)
				{
					//OBTENER SEXO Y EDAD DE LAS TABLAS DE AFILIADOS
					$tipo_id_usuario_pes=trim($campos[2]);
					$numero_id_usuario_pes=trim($campos[3]);
					
					//bd existe afiliado, el tipo regimen camp3ak4 1=rc,2=rs,3=rs,4,5=rc
					
					
					$query_edad_sexo_bd="SELECT * FROM gioss_afiliados_eapb_rc WHERE  tipo_id_afiliado= '".$tipo_id_usuario_pes."' AND id_afiliado = '".$numero_id_usuario_pes."' ;";
					$resultados_query_edad_sexo=$coneccionBD->consultar2($query_edad_sexo_bd);
					
					$fecha_nacimiento_1="";
					$sexo_1="";
					if(is_array($resultados_query_edad_sexo))
					{
						$fecha_nacimiento_1=$resultados_query_edad_sexo[0]["fecha_nacimiento"];
						$sexo_1=$resultados_query_edad_sexo[0]["sexo"];
					}
					
				
					$query_edad_sexo_bd="SELECT * FROM gioss_afiliados_regimen_subsidiado WHERE tipo_id_afiliado = '".$tipo_id_usuario_pes."' AND id_afiliado = '".$numero_id_usuario_pes."' ;";
					$resultados_query_edad_sexo=$coneccionBD->consultar2($query_edad_sexo_bd);
					
					$fecha_nacimiento_2="";
					$sexo_2="";
					if(is_array($resultados_query_edad_sexo))
					{
						$fecha_nacimiento_2=$resultados_query_edad_sexo[0]["fecha_nacimiento"];
						$sexo_2=$resultados_query_edad_sexo[0]["sexo"];
					}
				
					$query_edad_sexo_bd="SELECT * FROM gioss_afiliados_eapb_mp WHERE tipo_id_afiliado = '".$tipo_id_usuario_pes."' AND id_afiliado = '".$numero_id_usuario_pes."' ;";
					$resultados_query_edad_sexo=$coneccionBD->consultar2($query_edad_sexo_bd);
					
					$fecha_nacimiento_3="";
					$sexo_3="";
					if(is_array($resultados_query_edad_sexo))
					{
						$fecha_nacimiento_3=$resultados_query_edad_sexo[0]["fecha_nacimiento"];
						$sexo_3=$resultados_query_edad_sexo[0]["sexo"];
					}
					
					$query_edad_sexo_bd="SELECT * FROM gioss_afiliados_eapb_rp WHERE tipo_id_afiliado = '".$tipo_id_usuario_pes."' AND id_afiliado = '".$numero_id_usuario_pes."' ;";
					$resultados_query_edad_sexo=$coneccionBD->consultar2($query_edad_sexo_bd);
					
					$fecha_nacimiento_4="";
					$sexo_4="";
					if(is_array($resultados_query_edad_sexo))
					{
						$fecha_nacimiento_4=$resultados_query_edad_sexo[0]["fecha_nacimiento"];
						$sexo_4=$resultados_query_edad_sexo[0]["sexo"];
					}
					
					$edad_afiliado=0;
					$fecha_nacimiento_definitiva="";
					$array_fecha_definitiva=array();
					$sexo_afiliado="A";
					
					if($sexo_1==$sexo_2
					   && $sexo_1==$sexo_3
					   && $sexo_1==$sexo_4
					   && ($sexo_1=="M"||$sexo_1=="F")					   
					   )
					{
						$sexo_afiliado=$sexo_1;
					}
					else
					{
						$array_comprobacion_sexo=array();
						$array_indice_sexo=array();
						if(!isset($array_comprobacion_sexo[$sexo_1]) && ($sexo_1=="M"||$sexo_1=="F") )
						{
							$array_comprobacion_sexo[$sexo_1]=1;
						}
						else if(($sexo_1=="M"||$sexo_1=="F"))
						{
							$array_comprobacion_sexo[$sexo_1]++;
						}
						
						if(!isset($array_comprobacion_sexo[$sexo_2]) && ($sexo_2=="M"||$sexo_2=="F"))
						{
							$array_comprobacion_sexo[$sexo_2]=1;
						}
						else if($sexo_2=="M"||$sexo_2=="F")
						{
							$array_comprobacion_sexo[$sexo_2]++;
						}
						
						if(!isset($array_comprobacion_sexo[$sexo_3]) && ($sexo_3=="M"||$sexo_3=="F"))
						{
							$array_comprobacion_sexo[$sexo_3]=1;
						}
						else if($sexo_3=="M"||$sexo_3=="F")
						{
							$array_comprobacion_sexo[$sexo_3]++;
						}
						
						if(!isset($array_comprobacion_sexo[$sexo_4]) && ($sexo_4=="M"||$sexo_4=="F"))
						{
							$array_comprobacion_sexo[$sexo_4]=1;
						}
						else if($sexo_4=="M"||$sexo_4=="F")
						{
							$array_comprobacion_sexo[$sexo_4]++;
						}
						
						if(isset($array_comprobacion_sexo["M"]) && isset($array_comprobacion_sexo["F"]))
						{
							if($array_comprobacion_sexo["M"]>$array_comprobacion_sexo["F"])
							{
								$sexo_afiliado="M";
							}
							else
							{
								$sexo_afiliado="F";
							}
						}//fin if
						else if(isset($array_comprobacion_sexo["M"]))
						{
							$sexo_afiliado="M";
						}
						else if(isset($array_comprobacion_sexo["F"]))
						{
							$sexo_afiliado="F";
						}
						
						
					}//fin if comprobacion sexo
					
					$array_fn_1=explode("-",$fecha_nacimiento_1);
					//checkdate mes dia year
					$bool_es_date_fn_1=false;
					if(count($array_fn_1)==3)
					{
						$bool_es_date_fn_1=checkdate($array_fn_1[1],$array_fn_1[2],$array_fn_1[0]);
					}
					
					$array_fn_2=explode("-",$fecha_nacimiento_2);
					//checkdate mes dia year
					$bool_es_date_fn_2=false;
					if(count($array_fn_2)==3)
					{
						$bool_es_date_fn_2=checkdate($array_fn_2[1],$array_fn_2[2],$array_fn_2[0]);
					}
					
					$array_fn_3=explode("-",$fecha_nacimiento_3);
					//checkdate mes dia year
					$bool_es_date_fn_3=false;
					if(count($array_fn_3)==3)
					{
						$bool_es_date_fn_3=checkdate($array_fn_3[1],$array_fn_3[2],$array_fn_3[0]);
					}
					
					$array_fn_4=explode("-",$fecha_nacimiento_4);
					//checkdate mes dia year
					$bool_es_date_fn_4=false;
					if(count($array_fn_4)==3)
					{
						$bool_es_date_fn_4=checkdate($array_fn_4[1],$array_fn_4[2],$array_fn_4[0]);
					}
					
					if($fecha_nacimiento_1==$fecha_nacimiento_2
					   && $fecha_nacimiento_1==$fecha_nacimiento_3
					   && $fecha_nacimiento_1==$fecha_nacimiento_4
					   && $bool_es_date_fn_1==true)
					{
						$fecha_nacimiento_definitiva=$fecha_nacimiento_1;
					}
					else
					{
						$array_comprobacion_fecha_nacimiento=array();
						$array_indice_fecha_nacimiento=array();
						if(!isset($array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1]) && $bool_es_date_fn_1==true )
						{
							$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1]=1;
							$array_indice_fecha_nacimiento[0]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1];
						}
						else if($bool_es_date_fn_1==true)
						{
							$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1]++;
							$array_indice_fecha_nacimiento[0]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1];
						}
						else
						{
							$array_indice_fecha_nacimiento[0]=0;
						}
						
						if(!isset($array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2]) && $bool_es_date_fn_2==true)
						{
							$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2]=1;
							$array_indice_fecha_nacimiento[1]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2];
						}
						else if($bool_es_date_fn_2==true)
						{
							$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2]++;
							$array_indice_fecha_nacimiento[1]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2];
						}
						else
						{
							$array_indice_fecha_nacimiento[1]=0;
						}
						
						if(!isset($array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3]) && $bool_es_date_fn_3==true)
						{
							$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3]=1;
							$array_indice_fecha_nacimiento[2]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3];
						}
						else if($bool_es_date_fn_3==true)
						{
							$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3]++;
							$array_indice_fecha_nacimiento[2]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3];
						}
						else
						{
							$array_indice_fecha_nacimiento[2]=0;
						}
						
						if(!isset($array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4]) && $bool_es_date_fn_4==true)
						{
							$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4]=1;
							$array_indice_fecha_nacimiento[3]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4];
						}
						else if($bool_es_date_fn_4==true)
						{
							$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4]++;
							$array_indice_fecha_nacimiento[3]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4];
						}
						else
						{
							$array_indice_fecha_nacimiento[3]=0;
						}
						$indice_mayor_actual=0;
						$cont_fn=0;
						while($cont_fn<count($array_indice_fecha_nacimiento))
						{
							$cont_fn_2=$cont_fn+1;
							while($cont_fn_2<count($array_indice_fecha_nacimiento))
							{
								if($array_indice_fecha_nacimiento[$cont_fn_2]>$array_indice_fecha_nacimiento[$cont_fn])
								{
									$indice_mayor_actual=$cont_fn_2;
								}
								$cont_fn_2++;
							}
							$cont_fn++;
						}
						if($indice_mayor_actual==0)
						{
							$fecha_nacimiento_definitiva=$fecha_nacimiento_1;
							$array_fecha_definitiva=$array_fn_1;
						}
						if($indice_mayor_actual==1)
						{
							$fecha_nacimiento_definitiva=$fecha_nacimiento_2;
							$array_fecha_definitiva=$array_fn_2;
						}
						if($indice_mayor_actual==2)
						{
							$fecha_nacimiento_definitiva=$fecha_nacimiento_3;
							$array_fecha_definitiva=$array_fn_3;
						}
						if($indice_mayor_actual==3)
						{
							$fecha_nacimiento_definitiva=$fecha_nacimiento_4;
							$array_fecha_definitiva=$array_fn_4;
						}
					}//fin else
					
					//calculo edad con la fecha definitiva
					
					
					
					//fin calculo edad
					try
					{
						if($fecha_nacimiento_definitiva!="")
						{
							$interval_edad = date_diff(date_create($fecha_nacimiento_definitiva),date_create($date_remision_bd));
							$edad_afiliado= intval((float)($interval_edad->format("%r%a"))/365);
							
						}
						else
						{
							$edad_afiliado=999;
						}
					}
					catch(Exception $exc_edad_afiliado)
					{
						if(connection_aborted()==false)
						{
							echo "<script>alert('".procesar_mensaje($e->getMessage())."');</script>";
						}
						
					}
					//FIN OBTENER SEXO Y EDAD DE LAS TABLAS DE AFILIADOS
					
					$sql_exito="";
					$sql_exito.="INSERT INTO gioss_archivo_cargado_AU ";
					$sql_exito.=" ( ";
					$sql_exito.="fecha_validacion_exito ,";
					$sql_exito.="fecha_remision ,";
					$sql_exito.="codigo_eapb ,";
					$sql_exito.="codigo_archivo ,";
					$sql_exito.="numero_factura ,";					
					$sql_exito.="codigo_prestador_servicios_salud ,";
					$sql_exito.="tipo_identificacion_usuario ,";
					$sql_exito.="numero_identificacion_usuario ,";
					$sql_exito.="fecha_ingreso ,";
					$sql_exito.="Hora_ingreso ,";
					$sql_exito.="numero_autorizacion ,";
					$sql_exito.="causa_externa ,";
					$sql_exito.="codigo_diagnostico_de_salida ,";
					$sql_exito.="codigo_diagnostico_realcionado_1 ,";
					$sql_exito.="codigo_diagnostico_realcionado_2 ,";
					$sql_exito.="codigo_diagnostico_realcionado_3 ,";
					$sql_exito.="destino_usuario_salida ,";
					$sql_exito.="estado_salida_usuario ,";
					$sql_exito.="diagnostico_causa_muerte ,";
					$sql_exito.="fecha_salida ,";
					$sql_exito.="hora_salida_ingreso ,";
					$sql_exito.="fila ,";
					$sql_exito.="numero_remision, ";
					$sql_exito.="numero_secuencia, ";
					//columnas nuevas para facilitar reportes
					$sql_exito.="edad_years_afiliado, ";
					$sql_exito.="sexo_afiliado ";
					$sql_exito.=" ) ";
					$sql_exito.=" VALUES ";
					$sql_exito.=" ( ";
					$sql_exito.=" '".$fecha_actual."', ";
					$sql_exito.=" '".$date_remision_bd."', ";
					$sql_exito.=" '".$cod_eapb."', ";				
					$sql_exito.=" 'AU".$numero_de_remision."', ";
					$cont_tmp=0;
					while($cont_tmp<count($campos))
					{
						if(procesar_mensaje2($campos[$cont_tmp])=="")
						{
							$sql_exito.=" '0', ";
						}
						else
						{
							$sql_exito.=" '".procesar_mensaje2($campos[$cont_tmp])."', ";
						}
						$cont_tmp++;
					}
					$sql_exito.=" '".$nlinea."', ";
					$sql_exito.=" '".$numero_de_remision."', ";
					$sql_exito.=" '".$numero_secuencia_actual."', ";
					//campos adicionales para facilitar reportes
					$sql_exito.=" '".$edad_afiliado."', ";
					$sql_exito.=" '".$sexo_afiliado."' ";
					$sql_exito.=" ) ";
					$sql_exito.=" ; ";
					
					$error_bd="";
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_exito, $error_bd);
					if($error_bd!="")
					{
						$error_mostrar_bd.="ERROR AU EXITOSO ".$error_bd."<br>";
					}
					
					$mensaje_contador_errores="Se subio en la base de datos, el registro ".($nlinea+1)." de ";
					$mensaje_contador_errores.=" un total de ".$array_contador_registros_buenos["AU"]." registros buenos para AU. ";
					$html_del_mensaje="";
					$html_del_mensaje.="<table>";
					$html_del_mensaje.="<tr>";
					$html_del_mensaje.="<td colspan=\'2\'>";
					$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="</tr>";
					$html_del_mensaje.="<tr>";
					$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
					$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
					$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_contador_errores."</div><div id=\'errores_bd_div\'></div>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="</tr>";
					$html_del_mensaje.="</table>";
					if(connection_aborted()==false)
					{
						echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
						ob_flush();
						flush();
					}
				}//fin verifica numero campos
				$nlinea++;
			}
			fclose($file_au);
		}
		//FIN AU
		
		//AN
		if($es_valido_nombre_archivo_an && $bool_hubo_error_query==false
		   && (file_exists($ruta_archivo_an)==true && $rutaTemporal!=trim($ruta_archivo_an)))
		{
			/*
			 *abajo ya hay una parte donde elimina los rechazados pero despues de que sube los exitosos
			*/
			
			$lineas_del_archivo = count(file($ruta_archivo_an)); 
			$file_an = fopen($ruta_archivo_an, 'r') or exit("No se pudo abrir el archivo");
			
			$nlinea=0;
			while (!feof($file_an)) 
			{
				$linea_tmp = fgets($file_an);
				$linea= explode("\n", $linea_tmp)[0];
				$campos = explode(",", $linea);
				if(count($campos)==14)
				{
					//OBTENER SEXO Y EDAD DE LAS TABLAS DE AFILIADOS
					$tipo_id_usuario_pes=trim($campos[2]);
					$numero_id_usuario_pes=trim($campos[3]);
					
					//bd existe afiliado, el tipo regimen camp3ak4 1=rc,2=rs,3=rs,4,5=rc
					
					
					$query_edad_sexo_bd="SELECT * FROM gioss_afiliados_eapb_rc WHERE  tipo_id_afiliado= '".$tipo_id_usuario_pes."' AND id_afiliado = '".$numero_id_usuario_pes."' ;";
					$resultados_query_edad_sexo=$coneccionBD->consultar2($query_edad_sexo_bd);
					
					$fecha_nacimiento_1="";
					$sexo_1="";
					if(is_array($resultados_query_edad_sexo))
					{
						$fecha_nacimiento_1=$resultados_query_edad_sexo[0]["fecha_nacimiento"];
						$sexo_1=$resultados_query_edad_sexo[0]["sexo"];
					}
					
				
					$query_edad_sexo_bd="SELECT * FROM gioss_afiliados_regimen_subsidiado WHERE tipo_id_afiliado = '".$tipo_id_usuario_pes."' AND id_afiliado = '".$numero_id_usuario_pes."' ;";
					$resultados_query_edad_sexo=$coneccionBD->consultar2($query_edad_sexo_bd);
					
					$fecha_nacimiento_2="";
					$sexo_2="";
					if(is_array($resultados_query_edad_sexo))
					{
						$fecha_nacimiento_2=$resultados_query_edad_sexo[0]["fecha_nacimiento"];
						$sexo_2=$resultados_query_edad_sexo[0]["sexo"];
					}
				
					$query_edad_sexo_bd="SELECT * FROM gioss_afiliados_eapb_mp WHERE tipo_id_afiliado = '".$tipo_id_usuario_pes."' AND id_afiliado = '".$numero_id_usuario_pes."' ;";
					$resultados_query_edad_sexo=$coneccionBD->consultar2($query_edad_sexo_bd);
					
					$fecha_nacimiento_3="";
					$sexo_3="";
					if(is_array($resultados_query_edad_sexo))
					{
						$fecha_nacimiento_3=$resultados_query_edad_sexo[0]["fecha_nacimiento"];
						$sexo_3=$resultados_query_edad_sexo[0]["sexo"];
					}
					
					$query_edad_sexo_bd="SELECT * FROM gioss_afiliados_eapb_rp WHERE tipo_id_afiliado = '".$tipo_id_usuario_pes."' AND id_afiliado = '".$numero_id_usuario_pes."' ;";
					$resultados_query_edad_sexo=$coneccionBD->consultar2($query_edad_sexo_bd);
					
					$fecha_nacimiento_4="";
					$sexo_4="";
					if(is_array($resultados_query_edad_sexo))
					{
						$fecha_nacimiento_4=$resultados_query_edad_sexo[0]["fecha_nacimiento"];
						$sexo_4=$resultados_query_edad_sexo[0]["sexo"];
					}
					
					$edad_afiliado=0;
					$fecha_nacimiento_definitiva="";
					$array_fecha_definitiva=array();
					$sexo_afiliado="A";
					
					if($sexo_1==$sexo_2
					   && $sexo_1==$sexo_3
					   && $sexo_1==$sexo_4
					   && ($sexo_1=="M"||$sexo_1=="F")					   
					   )
					{
						$sexo_afiliado=$sexo_1;
					}
					else
					{
						$array_comprobacion_sexo=array();
						$array_indice_sexo=array();
						if(!isset($array_comprobacion_sexo[$sexo_1]) && ($sexo_1=="M"||$sexo_1=="F") )
						{
							$array_comprobacion_sexo[$sexo_1]=1;
						}
						else if(($sexo_1=="M"||$sexo_1=="F"))
						{
							$array_comprobacion_sexo[$sexo_1]++;
						}
						
						if(!isset($array_comprobacion_sexo[$sexo_2]) && ($sexo_2=="M"||$sexo_2=="F"))
						{
							$array_comprobacion_sexo[$sexo_2]=1;
						}
						else if($sexo_2=="M"||$sexo_2=="F")
						{
							$array_comprobacion_sexo[$sexo_2]++;
						}
						
						if(!isset($array_comprobacion_sexo[$sexo_3]) && ($sexo_3=="M"||$sexo_3=="F"))
						{
							$array_comprobacion_sexo[$sexo_3]=1;
						}
						else if($sexo_3=="M"||$sexo_3=="F")
						{
							$array_comprobacion_sexo[$sexo_3]++;
						}
						
						if(!isset($array_comprobacion_sexo[$sexo_4]) && ($sexo_4=="M"||$sexo_4=="F"))
						{
							$array_comprobacion_sexo[$sexo_4]=1;
						}
						else if($sexo_4=="M"||$sexo_4=="F")
						{
							$array_comprobacion_sexo[$sexo_4]++;
						}
						
						if(isset($array_comprobacion_sexo["M"]) && isset($array_comprobacion_sexo["F"]))
						{
							if($array_comprobacion_sexo["M"]>$array_comprobacion_sexo["F"])
							{
								$sexo_afiliado="M";
							}
							else
							{
								$sexo_afiliado="F";
							}
						}//fin if
						else if(isset($array_comprobacion_sexo["M"]))
						{
							$sexo_afiliado="M";
						}
						else if(isset($array_comprobacion_sexo["F"]))
						{
							$sexo_afiliado="F";
						}
						
						
					}//fin if comprobacion sexo
					
					$array_fn_1=explode("-",$fecha_nacimiento_1);
					//checkdate mes dia year
					$bool_es_date_fn_1=false;
					if(count($array_fn_1)==3)
					{
						$bool_es_date_fn_1=checkdate($array_fn_1[1],$array_fn_1[2],$array_fn_1[0]);
					}
					
					$array_fn_2=explode("-",$fecha_nacimiento_2);
					//checkdate mes dia year
					$bool_es_date_fn_2=false;
					if(count($array_fn_2)==3)
					{
						$bool_es_date_fn_2=checkdate($array_fn_2[1],$array_fn_2[2],$array_fn_2[0]);
					}
					
					$array_fn_3=explode("-",$fecha_nacimiento_3);
					//checkdate mes dia year
					$bool_es_date_fn_3=false;
					if(count($array_fn_3)==3)
					{
						$bool_es_date_fn_3=checkdate($array_fn_3[1],$array_fn_3[2],$array_fn_3[0]);
					}
					
					$array_fn_4=explode("-",$fecha_nacimiento_4);
					//checkdate mes dia year
					$bool_es_date_fn_4=false;
					if(count($array_fn_4)==3)
					{
						$bool_es_date_fn_4=checkdate($array_fn_4[1],$array_fn_4[2],$array_fn_4[0]);
					}
					
					if($fecha_nacimiento_1==$fecha_nacimiento_2
					   && $fecha_nacimiento_1==$fecha_nacimiento_3
					   && $fecha_nacimiento_1==$fecha_nacimiento_4
					   && $bool_es_date_fn_1==true)
					{
						$fecha_nacimiento_definitiva=$fecha_nacimiento_1;
					}
					else
					{
						$array_comprobacion_fecha_nacimiento=array();
						$array_indice_fecha_nacimiento=array();
						if(!isset($array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1]) && $bool_es_date_fn_1==true )
						{
							$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1]=1;
							$array_indice_fecha_nacimiento[0]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1];
						}
						else if($bool_es_date_fn_1==true)
						{
							$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1]++;
							$array_indice_fecha_nacimiento[0]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1];
						}
						else
						{
							$array_indice_fecha_nacimiento[0]=0;
						}
						
						if(!isset($array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2]) && $bool_es_date_fn_2==true)
						{
							$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2]=1;
							$array_indice_fecha_nacimiento[1]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2];
						}
						else if($bool_es_date_fn_2==true)
						{
							$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2]++;
							$array_indice_fecha_nacimiento[1]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2];
						}
						else
						{
							$array_indice_fecha_nacimiento[1]=0;
						}
						
						if(!isset($array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3]) && $bool_es_date_fn_3==true)
						{
							$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3]=1;
							$array_indice_fecha_nacimiento[2]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3];
						}
						else if($bool_es_date_fn_3==true)
						{
							$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3]++;
							$array_indice_fecha_nacimiento[2]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3];
						}
						else
						{
							$array_indice_fecha_nacimiento[2]=0;
						}
						
						if(!isset($array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4]) && $bool_es_date_fn_4==true)
						{
							$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4]=1;
							$array_indice_fecha_nacimiento[3]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4];
						}
						else if($bool_es_date_fn_4==true)
						{
							$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4]++;
							$array_indice_fecha_nacimiento[3]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4];
						}
						else
						{
							$array_indice_fecha_nacimiento[3]=0;
						}
						$indice_mayor_actual=0;
						$cont_fn=0;
						while($cont_fn<count($array_indice_fecha_nacimiento))
						{
							$cont_fn_2=$cont_fn+1;
							while($cont_fn_2<count($array_indice_fecha_nacimiento))
							{
								if($array_indice_fecha_nacimiento[$cont_fn_2]>$array_indice_fecha_nacimiento[$cont_fn])
								{
									$indice_mayor_actual=$cont_fn_2;
								}
								$cont_fn_2++;
							}
							$cont_fn++;
						}
						if($indice_mayor_actual==0)
						{
							$fecha_nacimiento_definitiva=$fecha_nacimiento_1;
							$array_fecha_definitiva=$array_fn_1;
						}
						if($indice_mayor_actual==1)
						{
							$fecha_nacimiento_definitiva=$fecha_nacimiento_2;
							$array_fecha_definitiva=$array_fn_2;
						}
						if($indice_mayor_actual==2)
						{
							$fecha_nacimiento_definitiva=$fecha_nacimiento_3;
							$array_fecha_definitiva=$array_fn_3;
						}
						if($indice_mayor_actual==3)
						{
							$fecha_nacimiento_definitiva=$fecha_nacimiento_4;
							$array_fecha_definitiva=$array_fn_4;
						}
					}//fin else
					
					//calculo edad con la fecha definitiva
					
					
					
					//fin calculo edad
					try
					{
						if($fecha_nacimiento_definitiva!="")
						{
							$interval_edad = date_diff(date_create($fecha_nacimiento_definitiva),date_create($date_remision_bd));
							$edad_afiliado= intval((float)($interval_edad->format("%r%a"))/365);
							
						}
						else
						{
							$edad_afiliado=999;
						}
					}
					catch(Exception $exc_edad_afiliado)
					{
						if(connection_aborted()==false)
						{
							echo "<script>alert('".procesar_mensaje($e->getMessage())."');</script>";
						}
						
					}
					//FIN OBTENER SEXO Y EDAD DE LAS TABLAS DE AFILIADOS
					
					$sql_exito="";
					$sql_exito.="INSERT INTO gioss_archivo_cargado_AN ";
					$sql_exito.=" ( ";
					$sql_exito.="fecha_validacion_exito ,";
					$sql_exito.="fecha_remision ,";
					$sql_exito.="codigo_eapb ,";
					$sql_exito.="codigo_archivo ,";
					$sql_exito.="numero_factura ,";					
					$sql_exito.="codigo_prestador_servicios_salud ,";
					$sql_exito.="tipo_identificacion_madre ,";
					$sql_exito.="numero_identificacion_madre ,";
					$sql_exito.="fecha_ingreso ,";
					$sql_exito.="Hora_ingreso ,";
					$sql_exito.="edad_gestacional ,";
					$sql_exito.="control_prenatal ,";
					$sql_exito.="sexo ,";
					$sql_exito.="peso ,";
					$sql_exito.="codigo_diagnostico_recien_nacido ,";
					$sql_exito.="codigo_diagnostico_causa_muerte ,";
					$sql_exito.="fecha_muerte_recien_nacido ,";
					$sql_exito.="hora_muerte_recien_nacido ,";
					$sql_exito.="fila ,";
					$sql_exito.="numero_remision, ";
					$sql_exito.="numero_secuencia, ";
					//columnas nuevas para facilitar reportes
					$sql_exito.="edad_years_afiliado, ";
					$sql_exito.="sexo_afiliado ";
					$sql_exito.=" ) ";
					$sql_exito.=" VALUES ";
					$sql_exito.=" ( ";
					$sql_exito.=" '".$fecha_actual."', ";
					$sql_exito.=" '".$date_remision_bd."', ";
					$sql_exito.=" '".$cod_eapb."', ";				
					$sql_exito.=" 'AN".$numero_de_remision."', ";
					$cont_tmp=0;
					while($cont_tmp<count($campos))
					{
						if(procesar_mensaje2($campos[$cont_tmp])=="")
						{
							$sql_exito.=" '0', ";
						}
						else
						{
							$sql_exito.=" '".procesar_mensaje2($campos[$cont_tmp])."', ";
						}
						$cont_tmp++;
					}
					$sql_exito.=" '".$nlinea."', ";
					$sql_exito.=" '".$numero_de_remision."', ";
					$sql_exito.=" '".$numero_secuencia_actual."', ";
					//campos adicionales para facilitar reportes
					$sql_exito.=" '".$edad_afiliado."', ";
					$sql_exito.=" '".$sexo_afiliado."' ";
					$sql_exito.=" ) ";
					$sql_exito.=" ; ";
					
					$error_bd="";
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_exito, $error_bd);
					if($error_bd!="")
					{
						$error_mostrar_bd.="ERROR AN EXITOSO ".$error_bd."<br>";
					}
					
					$mensaje_contador_errores="Se subio en la base de datos, el registro ".($nlinea+1)." de ";
					$mensaje_contador_errores.=" un total de ".$array_contador_registros_buenos["AN"]." registros buenos para AN. ";
					$html_del_mensaje="";
					$html_del_mensaje.="<table>";
					$html_del_mensaje.="<tr>";
					$html_del_mensaje.="<td colspan=\'2\'>";
					$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="</tr>";
					$html_del_mensaje.="<tr>";
					$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
					$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
					$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_contador_errores."</div><div id=\'errores_bd_div\'></div>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="</tr>";
					$html_del_mensaje.="</table>";
					if(connection_aborted()==false)
					{
						echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
						ob_flush();
						flush();
					}
				}//fin verificacion numero campos
				$nlinea++;
			}
			fclose($file_an);
		}
		//FIN AN
		
		//AM
		if($es_valido_nombre_archivo_am  && $bool_hubo_error_query==false
		   && (file_exists($ruta_archivo_am)==true && $rutaTemporal!=trim($ruta_archivo_am)))
		{
			/*
			 *abajo ya hay una parte donde elimina los rechazados pero despues de que sube los exitosos
			*/
			
			$lineas_del_archivo = count(file($ruta_archivo_am)); 
			$file_am = fopen($ruta_archivo_am, 'r') or exit("No se pudo abrir el archivo");
			
			$nlinea=0;
			while (!feof($file_am)) 
			{
				$linea_tmp = fgets($file_am);
				$linea= explode("\n", $linea_tmp)[0];
				$campos = explode(",", $linea);
				if(count($campos)==14)
				{
					//OBTENER SEXO Y EDAD DE LAS TABLAS DE AFILIADOS
					$tipo_id_usuario_pes=trim($campos[2]);
					$numero_id_usuario_pes=trim($campos[3]);
					
					//bd existe afiliado, el tipo regimen camp3ak4 1=rc,2=rs,3=rs,4,5=rc
					
					
					$query_edad_sexo_bd="SELECT * FROM gioss_afiliados_eapb_rc WHERE  tipo_id_afiliado= '".$tipo_id_usuario_pes."' AND id_afiliado = '".$numero_id_usuario_pes."' ;";
					$resultados_query_edad_sexo=$coneccionBD->consultar2($query_edad_sexo_bd);
					
					$fecha_nacimiento_1="";
					$sexo_1="";
					if(is_array($resultados_query_edad_sexo))
					{
						$fecha_nacimiento_1=$resultados_query_edad_sexo[0]["fecha_nacimiento"];
						$sexo_1=$resultados_query_edad_sexo[0]["sexo"];
					}
					
				
					$query_edad_sexo_bd="SELECT * FROM gioss_afiliados_regimen_subsidiado WHERE tipo_id_afiliado = '".$tipo_id_usuario_pes."' AND id_afiliado = '".$numero_id_usuario_pes."' ;";
					$resultados_query_edad_sexo=$coneccionBD->consultar2($query_edad_sexo_bd);
					
					$fecha_nacimiento_2="";
					$sexo_2="";
					if(is_array($resultados_query_edad_sexo))
					{
						$fecha_nacimiento_2=$resultados_query_edad_sexo[0]["fecha_nacimiento"];
						$sexo_2=$resultados_query_edad_sexo[0]["sexo"];
					}
				
					$query_edad_sexo_bd="SELECT * FROM gioss_afiliados_eapb_mp WHERE tipo_id_afiliado = '".$tipo_id_usuario_pes."' AND id_afiliado = '".$numero_id_usuario_pes."' ;";
					$resultados_query_edad_sexo=$coneccionBD->consultar2($query_edad_sexo_bd);
					
					$fecha_nacimiento_3="";
					$sexo_3="";
					if(is_array($resultados_query_edad_sexo))
					{
						$fecha_nacimiento_3=$resultados_query_edad_sexo[0]["fecha_nacimiento"];
						$sexo_3=$resultados_query_edad_sexo[0]["sexo"];
					}
					
					$query_edad_sexo_bd="SELECT * FROM gioss_afiliados_eapb_rp WHERE tipo_id_afiliado = '".$tipo_id_usuario_pes."' AND id_afiliado = '".$numero_id_usuario_pes."' ;";
					$resultados_query_edad_sexo=$coneccionBD->consultar2($query_edad_sexo_bd);
					
					$fecha_nacimiento_4="";
					$sexo_4="";
					if(is_array($resultados_query_edad_sexo))
					{
						$fecha_nacimiento_4=$resultados_query_edad_sexo[0]["fecha_nacimiento"];
						$sexo_4=$resultados_query_edad_sexo[0]["sexo"];
					}
					
					$edad_afiliado=0;
					$fecha_nacimiento_definitiva="";
					$array_fecha_definitiva=array();
					$sexo_afiliado="A";
					
					if($sexo_1==$sexo_2
					   && $sexo_1==$sexo_3
					   && $sexo_1==$sexo_4
					   && ($sexo_1=="M"||$sexo_1=="F")					   
					   )
					{
						$sexo_afiliado=$sexo_1;
					}
					else
					{
						$array_comprobacion_sexo=array();
						$array_indice_sexo=array();
						if(!isset($array_comprobacion_sexo[$sexo_1]) && ($sexo_1=="M"||$sexo_1=="F") )
						{
							$array_comprobacion_sexo[$sexo_1]=1;
						}
						else if(($sexo_1=="M"||$sexo_1=="F"))
						{
							$array_comprobacion_sexo[$sexo_1]++;
						}
						
						if(!isset($array_comprobacion_sexo[$sexo_2]) && ($sexo_2=="M"||$sexo_2=="F"))
						{
							$array_comprobacion_sexo[$sexo_2]=1;
						}
						else if($sexo_2=="M"||$sexo_2=="F")
						{
							$array_comprobacion_sexo[$sexo_2]++;
						}
						
						if(!isset($array_comprobacion_sexo[$sexo_3]) && ($sexo_3=="M"||$sexo_3=="F"))
						{
							$array_comprobacion_sexo[$sexo_3]=1;
						}
						else if($sexo_3=="M"||$sexo_3=="F")
						{
							$array_comprobacion_sexo[$sexo_3]++;
						}
						
						if(!isset($array_comprobacion_sexo[$sexo_4]) && ($sexo_4=="M"||$sexo_4=="F"))
						{
							$array_comprobacion_sexo[$sexo_4]=1;
						}
						else if($sexo_4=="M"||$sexo_4=="F")
						{
							$array_comprobacion_sexo[$sexo_4]++;
						}
						
						if(isset($array_comprobacion_sexo["M"]) && isset($array_comprobacion_sexo["F"]))
						{
							if($array_comprobacion_sexo["M"]>$array_comprobacion_sexo["F"])
							{
								$sexo_afiliado="M";
							}
							else
							{
								$sexo_afiliado="F";
							}
						}//fin if
						else if(isset($array_comprobacion_sexo["M"]))
						{
							$sexo_afiliado="M";
						}
						else if(isset($array_comprobacion_sexo["F"]))
						{
							$sexo_afiliado="F";
						}
						
						
					}//fin if comprobacion sexo
					
					$array_fn_1=explode("-",$fecha_nacimiento_1);
					//checkdate mes dia year
					$bool_es_date_fn_1=false;
					if(count($array_fn_1)==3)
					{
						$bool_es_date_fn_1=checkdate($array_fn_1[1],$array_fn_1[2],$array_fn_1[0]);
					}
					
					$array_fn_2=explode("-",$fecha_nacimiento_2);
					//checkdate mes dia year
					$bool_es_date_fn_2=false;
					if(count($array_fn_2)==3)
					{
						$bool_es_date_fn_2=checkdate($array_fn_2[1],$array_fn_2[2],$array_fn_2[0]);
					}
					
					$array_fn_3=explode("-",$fecha_nacimiento_3);
					//checkdate mes dia year
					$bool_es_date_fn_3=false;
					if(count($array_fn_3)==3)
					{
						$bool_es_date_fn_3=checkdate($array_fn_3[1],$array_fn_3[2],$array_fn_3[0]);
					}
					
					$array_fn_4=explode("-",$fecha_nacimiento_4);
					//checkdate mes dia year
					$bool_es_date_fn_4=false;
					if(count($array_fn_4)==3)
					{
						$bool_es_date_fn_4=checkdate($array_fn_4[1],$array_fn_4[2],$array_fn_4[0]);
					}
					
					if($fecha_nacimiento_1==$fecha_nacimiento_2
					   && $fecha_nacimiento_1==$fecha_nacimiento_3
					   && $fecha_nacimiento_1==$fecha_nacimiento_4
					   && $bool_es_date_fn_1==true)
					{
						$fecha_nacimiento_definitiva=$fecha_nacimiento_1;
					}
					else
					{
						$array_comprobacion_fecha_nacimiento=array();
						$array_indice_fecha_nacimiento=array();
						if(!isset($array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1]) && $bool_es_date_fn_1==true )
						{
							$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1]=1;
							$array_indice_fecha_nacimiento[0]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1];
						}
						else if($bool_es_date_fn_1==true)
						{
							$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1]++;
							$array_indice_fecha_nacimiento[0]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_1];
						}
						else
						{
							$array_indice_fecha_nacimiento[0]=0;
						}
						
						if(!isset($array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2]) && $bool_es_date_fn_2==true)
						{
							$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2]=1;
							$array_indice_fecha_nacimiento[1]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2];
						}
						else if($bool_es_date_fn_2==true)
						{
							$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2]++;
							$array_indice_fecha_nacimiento[1]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_2];
						}
						else
						{
							$array_indice_fecha_nacimiento[1]=0;
						}
						
						if(!isset($array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3]) && $bool_es_date_fn_3==true)
						{
							$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3]=1;
							$array_indice_fecha_nacimiento[2]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3];
						}
						else if($bool_es_date_fn_3==true)
						{
							$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3]++;
							$array_indice_fecha_nacimiento[2]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_3];
						}
						else
						{
							$array_indice_fecha_nacimiento[2]=0;
						}
						
						if(!isset($array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4]) && $bool_es_date_fn_4==true)
						{
							$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4]=1;
							$array_indice_fecha_nacimiento[3]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4];
						}
						else if($bool_es_date_fn_4==true)
						{
							$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4]++;
							$array_indice_fecha_nacimiento[3]=$array_comprobacion_fecha_nacimiento[$fecha_nacimiento_4];
						}
						else
						{
							$array_indice_fecha_nacimiento[3]=0;
						}
						$indice_mayor_actual=0;
						$cont_fn=0;
						while($cont_fn<count($array_indice_fecha_nacimiento))
						{
							$cont_fn_2=$cont_fn+1;
							while($cont_fn_2<count($array_indice_fecha_nacimiento))
							{
								if($array_indice_fecha_nacimiento[$cont_fn_2]>$array_indice_fecha_nacimiento[$cont_fn])
								{
									$indice_mayor_actual=$cont_fn_2;
								}
								$cont_fn_2++;
							}
							$cont_fn++;
						}
						if($indice_mayor_actual==0)
						{
							$fecha_nacimiento_definitiva=$fecha_nacimiento_1;
							$array_fecha_definitiva=$array_fn_1;
						}
						if($indice_mayor_actual==1)
						{
							$fecha_nacimiento_definitiva=$fecha_nacimiento_2;
							$array_fecha_definitiva=$array_fn_2;
						}
						if($indice_mayor_actual==2)
						{
							$fecha_nacimiento_definitiva=$fecha_nacimiento_3;
							$array_fecha_definitiva=$array_fn_3;
						}
						if($indice_mayor_actual==3)
						{
							$fecha_nacimiento_definitiva=$fecha_nacimiento_4;
							$array_fecha_definitiva=$array_fn_4;
						}
					}//fin else
					
					//calculo edad con la fecha definitiva
					
					
					
					//fin calculo edad
					try
					{
						if($fecha_nacimiento_definitiva!="")
						{
							$interval_edad = date_diff(date_create($fecha_nacimiento_definitiva),date_create($date_remision_bd));
							$edad_afiliado= intval((float)($interval_edad->format("%r%a"))/365);
							
						}
						else
						{
							$edad_afiliado=999;
						}
					}
					catch(Exception $exc_edad_afiliado)
					{
						if(connection_aborted()==false)
						{
							echo "<script>alert('".procesar_mensaje($e->getMessage())."');</script>";
						}
						
					}
					//FIN OBTENER SEXO Y EDAD DE LAS TABLAS DE AFILIADOS
					
					$sql_exito="";
					$sql_exito.="INSERT INTO gioss_archivo_cargado_AM ";
					$sql_exito.=" ( ";
					$sql_exito.="fecha_validacion_exito ,";
					$sql_exito.="fecha_remision ,";
					$sql_exito.="codigo_eapb ,";
					$sql_exito.="codigo_archivo ,";
					$sql_exito.="numero_factura ,";					
					$sql_exito.="codigo_prestador_servicios_salud ,";
					$sql_exito.="tipo_identificacion_usuario ,";
					$sql_exito.="numero_identificacion_usuario ,";
					$sql_exito.="numero_autorizacion ,";
					$sql_exito.="codigo_del_medicamento ,";
					$sql_exito.="tipo_medicamento ,";
					$sql_exito.="nombre_generico_medicamento ,";
					$sql_exito.="forma_farmaceutica ,";
					$sql_exito.="concetracion_medicamento ,";
					$sql_exito.="unidad_medida_medicamento ,";
					$sql_exito.="numero_unidades ,";
					$sql_exito.="valor_unitario_medicamento ,";
					$sql_exito.="valor_total_medicamento ,";
					$sql_exito.="fila ,";
					$sql_exito.="numero_remision, ";
					$sql_exito.="numero_secuencia, ";
					//columnas nuevas para facilitar reportes
					$sql_exito.="edad_years_afiliado, ";
					$sql_exito.="sexo_afiliado ";
					$sql_exito.=" ) ";
					$sql_exito.=" VALUES ";
					$sql_exito.=" ( ";
					$sql_exito.=" '".$fecha_actual."', ";
					$sql_exito.=" '".$date_remision_bd."', ";
					$sql_exito.=" '".$cod_eapb."', ";				
					$sql_exito.=" 'AM".$numero_de_remision."', ";
					$cont_tmp=0;
					while($cont_tmp<count($campos))
					{
						if(procesar_mensaje2($campos[$cont_tmp])=="")
						{
							$sql_exito.=" '0', ";
						}
						else
						{
							$sql_exito.=" '".procesar_mensaje2($campos[$cont_tmp])."', ";
						}
						$cont_tmp++;
					}
					$sql_exito.=" '".$nlinea."', ";
					$sql_exito.=" '".$numero_de_remision."', ";
					$sql_exito.=" '".$numero_secuencia_actual."', ";
					//campos adicionales para facilitar reportes
					$sql_exito.=" '".$edad_afiliado."', ";
					$sql_exito.=" '".$sexo_afiliado."' ";
					$sql_exito.=" ) ";
					$sql_exito.=" ; ";
					
					$error_bd="";
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_exito, $error_bd);
					if($error_bd!="")
					{
						$error_mostrar_bd.=" ERROR AM EXITOSO ".$error_bd."<br>";
					}
					
					$mensaje_contador_errores="Se subio en la base de datos, el registro ".($nlinea+1)." de ";
					$mensaje_contador_errores.=" un total de ".$array_contador_registros_buenos["AM"]." registros buenos para AM. ";
					$html_del_mensaje="";
					$html_del_mensaje.="<table>";
					$html_del_mensaje.="<tr>";
					$html_del_mensaje.="<td colspan=\'2\'>";
					$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="</tr>";
					$html_del_mensaje.="<tr>";
					$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
					$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
					$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_contador_errores."</div><div id=\'errores_bd_div\'></div>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="</tr>";
					$html_del_mensaje.="</table>";
					if(connection_aborted()==false)
					{
						echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
						ob_flush();
						flush();
					}
				}//fin verificacion numero campos
				$nlinea++;
			}
			fclose($file_am);
		}
		//FIN AM
		
		//AT
		if($es_valido_nombre_archivo_at && $bool_hubo_error_query==false
		   && (file_exists($ruta_archivo_at)==true && $rutaTemporal!=trim($ruta_archivo_at)))
		{
			/*
			 *abajo ya hay una parte donde elimina los rechazados pero despues de que sube los exitosos
			*/
			$lineas_del_archivo = count(file($ruta_archivo_at)); 
			$file_at = fopen($ruta_archivo_at, 'r') or exit("No se pudo abrir el archivo");
			
			$nlinea=0;
			while (!feof($file_at)) 
			{
				$linea_tmp = fgets($file_at);
				$linea= explode("\n", $linea_tmp)[0];
				$campos = explode(",", $linea);
				if(count($campos)==11)
				{
					
					$sql_exito="";
					$sql_exito.="INSERT INTO gioss_archivo_cargado_AT ";
					$sql_exito.=" ( ";
					$sql_exito.="fecha_validacion_exito ,";
					$sql_exito.="fecha_remision ,";
					$sql_exito.="codigo_eapb ,";
					$sql_exito.="codigo_archivo ,";
					$sql_exito.="numero_factura ,";					
					$sql_exito.="codigo_prestador_servicios_salud ,";
					$sql_exito.="tipo_identificacion_usuario ,";
					$sql_exito.="numero_identificacion_usuario ,";
					$sql_exito.="numero_autorizacion ,";
					$sql_exito.="codigo_tipo_servicio ,";
					$sql_exito.="codigo_del_servicio ,";
					$sql_exito.="nombre_del_servicio ,";
					$sql_exito.="cantidad ,";
					$sql_exito.="valor_unitario ,";
					$sql_exito.="valor_total ,";
					$sql_exito.="fila ,";
					$sql_exito.="numero_remision, ";
					$sql_exito.="numero_secuencia ";
					$sql_exito.=" ) ";
					$sql_exito.=" VALUES ";
					$sql_exito.=" ( ";
					$sql_exito.=" '".$fecha_actual."', ";
					$sql_exito.=" '".$date_remision_bd."', ";
					$sql_exito.=" '".$cod_eapb."', ";				
					$sql_exito.=" 'AT".$numero_de_remision."', ";
					$cont_tmp=0;
					while($cont_tmp<count($campos))
					{
						if(procesar_mensaje2($campos[$cont_tmp])=="")
						{
							$sql_exito.=" '0', ";
						}
						else
						{
							$sql_exito.=" '".procesar_mensaje2($campos[$cont_tmp])."', ";
						}
						$cont_tmp++;
					}
					$sql_exito.=" '".$nlinea."', ";
					$sql_exito.=" '".$numero_de_remision."', ";
					$sql_exito.=" '".$numero_secuencia_actual."' ";
					$sql_exito.=" ) ";
					$sql_exito.=" ; ";
					
					$error_bd="";
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_exito, $error_bd);
					if($error_bd!="")
					{
						$error_mostrar_bd.=" ERROR AT EXITOSO: ".$error_bd."<br>";
					}
					
					$mensaje_contador_errores="Se subio en la base de datos, el registro ".($nlinea+1)." de ";
					$mensaje_contador_errores.=" un total de ".$array_contador_registros_buenos["AT"]." registros buenos para AT. ";
					$html_del_mensaje="";
					$html_del_mensaje.="<table>";
					$html_del_mensaje.="<tr>";
					$html_del_mensaje.="<td colspan=\'2\'>";
					$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="</tr>";
					$html_del_mensaje.="<tr>";
					$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
					$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
					$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_contador_errores."</div><div id=\'errores_bd_div\'></div>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="</tr>";
					$html_del_mensaje.="</table>";
					if(connection_aborted()==false)
					{
						echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
						ob_flush();
						flush();
					}
				}//fin verificacion numero campos
				$nlinea++;
			}
			fclose($file_at);
		}
		//FIN AT
		
		
		
		//PARTE GIOSS_REGISTROS_CARGADOS_EXITO_RIPS
			
		$query_nombre_prestador="";
		$query_nombre_prestador.="SELECT nombre_de_la_entidad FROM gioss_entidades_sector_salud WHERE codigo_entidad='".$cod_prestador."' ; ";
		$resultado_query_nombre_prestador=$coneccionBD->consultar2($query_nombre_prestador);
		
		$nombre_prestador="";
		if(count($resultado_query_nombre_prestador)>0)
		{
			$nombre_prestador=$resultado_query_nombre_prestador[0]["nombre_de_la_entidad"];
		}
		
		$query_nombre_eapb="";
		$query_nombre_eapb.="SELECT nombre_de_la_entidad FROM gioss_entidades_sector_salud WHERE codigo_entidad='".$cod_eapb."' ; ";
		$resultado_query_nombre_eapb=$coneccionBD->consultar2($query_nombre_eapb);
		
		$nombre_eapb="";
		if(count($resultado_query_nombre_eapb)>0 )
		{
			$nombre_eapb=$resultado_query_nombre_eapb[0]["nombre_de_la_entidad"];
		}
				
		$numero_reg_ct=0;
		if(file_exists($ruta_archivo_ct)==true && $rutaTemporal!=trim($ruta_archivo_ct))
		{
			$numero_reg_ct=count(file($ruta_archivo_ct));
		}
		
		$numero_reg_af=0;
		if(file_exists($ruta_archivo_af)==true && $rutaTemporal!=trim($ruta_archivo_af))
		{
			$numero_reg_af=count(file($ruta_archivo_af));
		}
		
		$numero_reg_us=0;
		if(file_exists($ruta_archivo_us)==true && $rutaTemporal!=trim($ruta_archivo_us))
		{
			$numero_reg_us=count(file($ruta_archivo_us));
		}
		
		$numero_reg_ac=0;
		if(file_exists($ruta_archivo_ac)==true && $rutaTemporal!=trim($ruta_archivo_ac))
		{
			$numero_reg_ac=count(file($ruta_archivo_ac));
		}
		
		$numero_reg_ah=0;
		if(file_exists($ruta_archivo_ah)==true && $rutaTemporal!=trim($ruta_archivo_ah))
		{
			$numero_reg_ah=count(file($ruta_archivo_ah));
		}
		
		$numero_reg_ad=0;
		if(file_exists($ruta_archivo_ad)==true && $rutaTemporal!=trim($ruta_archivo_ad))
		{
			$numero_reg_ad=count(file($ruta_archivo_ad));
		}
		
		$numero_reg_ap=0;
		if(file_exists($ruta_archivo_ap)==true && $rutaTemporal!=trim($ruta_archivo_ap))
		{
			$numero_reg_ap=count(file($ruta_archivo_ap));
		}
		
		$numero_reg_au=0;
		if(file_exists($ruta_archivo_au)==true && $rutaTemporal!=trim($ruta_archivo_au))
		{
			$numero_reg_au=count(file($ruta_archivo_au));
		}
		
		$numero_reg_an=0;
		if(file_exists($ruta_archivo_an)==true && $rutaTemporal!=trim($ruta_archivo_an))
		{
			$numero_reg_an=count(file($ruta_archivo_an));
		}
		
		$numero_reg_am=0;
		if(file_exists($ruta_archivo_am)==true && $rutaTemporal!=trim($ruta_archivo_am))
		{
			$numero_reg_am=count(file($ruta_archivo_am));
		}
		
		$numero_reg_at=0;
		if(file_exists($ruta_archivo_at)==true && $rutaTemporal!=trim($ruta_archivo_at))
		{
			$numero_reg_at=count(file($ruta_archivo_at));
		}
		
		
		//if si hayo los nombres de las entidades inserta en la tabla exitosos
		if(count($resultado_query_nombre_eapb)>0 && count($resultado_query_nombre_prestador)>0)
		{
			$query_registrar_cargado_con_exito="";
			$query_registrar_cargado_con_exito.="INSERT INTO gioss_registros_cargados_exito_rips ";
			$query_registrar_cargado_con_exito.="(";
			$query_registrar_cargado_con_exito.="codigo_entidad_prestadora,";
			$query_registrar_cargado_con_exito.="nombre_entidad_prestadora,";
			$query_registrar_cargado_con_exito.="codigo_eapb,";
			$query_registrar_cargado_con_exito.="nombre_eapb,";
			$query_registrar_cargado_con_exito.="numero_secuencia_validacion,";
			$query_registrar_cargado_con_exito.="nombre_archivo_ct,";
			$query_registrar_cargado_con_exito.="fecha_validacion,";
			$query_registrar_cargado_con_exito.="numero_registros_ct,";
			$query_registrar_cargado_con_exito.="numero_registros_af,";
			$query_registrar_cargado_con_exito.="numero_registros_us,";
			$query_registrar_cargado_con_exito.="numero_registros_ac,";
			$query_registrar_cargado_con_exito.="numero_registros_ap,";
			$query_registrar_cargado_con_exito.="numero_registros_ah,";
			$query_registrar_cargado_con_exito.="numero_registros_au,";
			$query_registrar_cargado_con_exito.="numero_registros_an,";
			$query_registrar_cargado_con_exito.="numero_registros_am,";
			$query_registrar_cargado_con_exito.="numero_registros_at,";
			$query_registrar_cargado_con_exito.="mensaje_aceptacion";			
			$query_registrar_cargado_con_exito.=")";
			$query_registrar_cargado_con_exito.="VALUES";
			$query_registrar_cargado_con_exito.="(";
			$query_registrar_cargado_con_exito.="'".$cod_prestador."',";
			$query_registrar_cargado_con_exito.="'".$nombre_prestador."',";
			$query_registrar_cargado_con_exito.="'".$cod_eapb."',";
			$query_registrar_cargado_con_exito.="'".$nombre_eapb."',";
			$query_registrar_cargado_con_exito.="'".$numero_secuencia_actual."',";
			$query_registrar_cargado_con_exito.="'CT".$numero_de_remision."',";
			$query_registrar_cargado_con_exito.="'".$fecha_actual."',";
			$query_registrar_cargado_con_exito.="'$numero_reg_ct',";
			$query_registrar_cargado_con_exito.="'$numero_reg_af',";
			$query_registrar_cargado_con_exito.="'$numero_reg_us',";
			$query_registrar_cargado_con_exito.="'$numero_reg_ac',";
			$query_registrar_cargado_con_exito.="'$numero_reg_ap',";
			$query_registrar_cargado_con_exito.="'$numero_reg_ah',";
			$query_registrar_cargado_con_exito.="'$numero_reg_au',";
			$query_registrar_cargado_con_exito.="'$numero_reg_an',";
			$query_registrar_cargado_con_exito.="'$numero_reg_am',";
			$query_registrar_cargado_con_exito.="'$numero_reg_at',";
			$query_registrar_cargado_con_exito.="'Archivos validados con exito y cargados en el sistema'";
			$query_registrar_cargado_con_exito.=");";
			$error_bd_seq="";
			$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($query_registrar_cargado_con_exito, $error_bd_seq);
			if($error_bd_seq!="")
			{
				$error_mostrar_bd.=$error_bd_seq."<br>";
			}
		}//fin if si hayo los nombres de las entidades inserta en la tabla exitosos
		
		//FIN PARTE GIOSS_REGISTROS_CARGADOS_EXITO_RIPS
		
		//TERMINA
		if($bool_hubo_error_query==false)
		{
			$sql_exito="";
			$sql_exito.="COMMIT;";
			
			$error_bd="";
			$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_exito, $error_bd);
			if($error_bd!="")
			{
				$error_mostrar_bd.=$error_bd."<br>";
			}
			else
			{
				//en caso de que no haya errores al introducir loscampos en la parte de exito se borra los que hubieron en la parte de rechazo
				$sql_query_delete="";
				$sql_query_delete.="DELETE FROM gioss_archivo_rechazado_CT ";
				$sql_query_delete.=" WHERE fecha_remision='".$date_remision_bd."' ";
				$sql_query_delete.=" AND codigo_eapb='".$cod_eapb."' ";
				$sql_query_delete.=" AND codigo_prestador_servicios_salud='".$cod_prestador."' ";
				$sql_query_delete.=" AND numero_remision='".$numero_de_remision."' ; ";
				$error_bd_del="";
				$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_query_delete, $error_bd_del);
				
				$sql_query_delete="";
				$sql_query_delete.="DELETE FROM gioss_archivo_rechazado_AF ";
				$sql_query_delete.=" WHERE fecha_remision='".$date_remision_bd."' ";
				$sql_query_delete.=" AND codigo_eapb='".$cod_eapb."' ";
				$sql_query_delete.=" AND codigo_prestador_servicios_salud='".$cod_prestador."' ";
				$sql_query_delete.=" AND numero_remision='".$numero_de_remision."' ; ";
				$error_bd_del="";
				$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_query_delete, $error_bd_del);
				
				$sql_query_delete="";
				$sql_query_delete.="DELETE FROM gioss_archivo_rechazado_US ";
				$sql_query_delete.=" WHERE fecha_remision='".$date_remision_bd."' ";
				$sql_query_delete.=" AND codigo_eapb='".$cod_eapb."' ";
				$sql_query_delete.=" AND codigo_prestador_servicios_salud='".$cod_prestador."' ";
				$sql_query_delete.=" AND numero_remision='".$numero_de_remision."' ; ";
				$error_bd_del="";
				$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_query_delete, $error_bd_del);
				
				$sql_query_delete="";
				$sql_query_delete.="DELETE FROM gioss_archivo_rechazado_AC ";
				$sql_query_delete.=" WHERE fecha_remision='".$date_remision_bd."' ";
				$sql_query_delete.=" AND codigo_eapb='".$cod_eapb."' ";
				$sql_query_delete.=" AND codigo_prestador_servicios_salud='".$cod_prestador."' ";
				$sql_query_delete.=" AND numero_remision='".$numero_de_remision."' ; ";
				$error_bd_del="";
				$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_query_delete, $error_bd_del);
				
				$sql_query_delete="";
				$sql_query_delete.="DELETE FROM gioss_archivo_rechazado_AH ";
				$sql_query_delete.=" WHERE fecha_remision='".$date_remision_bd."' ";
				$sql_query_delete.=" AND codigo_eapb='".$cod_eapb."' ";
				$sql_query_delete.=" AND codigo_prestador_servicios_salud='".$cod_prestador."' ";
				$sql_query_delete.=" AND numero_remision='".$numero_de_remision."' ; ";
				$error_bd_del="";
				$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_query_delete, $error_bd_del);
				
				$sql_query_delete="";
				$sql_query_delete.="DELETE FROM gioss_archivo_rechazado_AD ";
				$sql_query_delete.=" WHERE fecha_remision='".$date_remision_bd."' ";
				$sql_query_delete.=" AND codigo_eapb='".$cod_eapb."' ";
				$sql_query_delete.=" AND codigo_prestador_servicios_salud='".$cod_prestador."' ";
				$sql_query_delete.=" AND numero_remision='".$numero_de_remision."' ; ";
				$error_bd_del="";
				$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_query_delete, $error_bd_del);
				
				$sql_query_delete="";
				$sql_query_delete.="DELETE FROM gioss_archivo_rechazado_AP ";
				$sql_query_delete.=" WHERE fecha_remision='".$date_remision_bd."' ";
				$sql_query_delete.=" AND codigo_eapb='".$cod_eapb."' ";
				$sql_query_delete.=" AND codigo_prestador_servicios_salud='".$cod_prestador."' ";
				$sql_query_delete.=" AND numero_remision='".$numero_de_remision."' ; ";
				$error_bd_del="";
				$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_query_delete, $error_bd_del);
				
				$sql_query_delete="";
				$sql_query_delete.="DELETE FROM gioss_archivo_rechazado_AU ";
				$sql_query_delete.=" WHERE fecha_remision='".$date_remision_bd."' ";
				$sql_query_delete.=" AND codigo_eapb='".$cod_eapb."' ";
				$sql_query_delete.=" AND codigo_prestador_servicios_salud='".$cod_prestador."' ";
				$sql_query_delete.=" AND numero_remision='".$numero_de_remision."' ; ";
				$error_bd_del="";
				$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_query_delete, $error_bd_del);
				
				$sql_query_delete="";
				$sql_query_delete.="DELETE FROM gioss_archivo_rechazado_AN ";
				$sql_query_delete.=" WHERE fecha_remision='".$date_remision_bd."' ";
				$sql_query_delete.=" AND codigo_eapb='".$cod_eapb."' ";
				$sql_query_delete.=" AND codigo_prestador_servicios_salud='".$cod_prestador."' ";
				$sql_query_delete.=" AND numero_remision='".$numero_de_remision."' ; ";
				$error_bd_del="";
				$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_query_delete, $error_bd_del);
				
				$sql_query_delete="";
				$sql_query_delete.="DELETE FROM gioss_archivo_rechazado_AM ";
				$sql_query_delete.=" WHERE fecha_remision='".$date_remision_bd."' ";
				$sql_query_delete.=" AND codigo_eapb='".$cod_eapb."' ";
				$sql_query_delete.=" AND codigo_prestador_servicios_salud='".$cod_prestador."' ";
				$sql_query_delete.=" AND numero_remision='".$numero_de_remision."' ; ";
				$error_bd_del="";
				$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_query_delete, $error_bd_del);
				
				$sql_query_delete="";
				$sql_query_delete.="DELETE FROM gioss_archivo_rechazado_AT ";
				$sql_query_delete.=" WHERE fecha_remision='".$date_remision_bd."' ";
				$sql_query_delete.=" AND codigo_eapb='".$cod_eapb."' ";
				$sql_query_delete.=" AND codigo_prestador_servicios_salud='".$cod_prestador."' ";
				$sql_query_delete.=" AND numero_remision='".$numero_de_remision."' ; ";
				$error_bd_del="";
				$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_query_delete, $error_bd_del);
				
				//finaliza delete en rechazados				
				
			}//fin else
		}//fin if termino query exitosamente hace commit
		else
		{
			$sql_exito="";
			$sql_exito.="ROLLBACK;";
			
			$error_bd="";
			$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_exito, $error_bd);
			if($error_bd!="")
			{
				$error_mostrar_bd.=$error_bd."<br>";
			}
		}//fin else hace rollback si hubo error
		
		//error de bd mostrar
		if($error_mostrar_bd!="")
		{
			$error_mostrar_bd_procesado = str_replace("á","a",$error_mostrar_bd);
			$error_mostrar_bd_procesado = str_replace("é","e",$error_mostrar_bd_procesado);
			$error_mostrar_bd_procesado = str_replace("í","i",$error_mostrar_bd_procesado);
			$error_mostrar_bd_procesado = str_replace("ó","o",$error_mostrar_bd_procesado);
			$error_mostrar_bd_procesado = str_replace("ú","u",$error_mostrar_bd_procesado);
			$error_mostrar_bd_procesado = str_replace("ñ","n",$error_mostrar_bd_procesado);
			$error_mostrar_bd_procesado = str_replace("Á","A",$error_mostrar_bd_procesado);
			$error_mostrar_bd_procesado = str_replace("É","E",$error_mostrar_bd_procesado);
			$error_mostrar_bd_procesado = str_replace("Í","I",$error_mostrar_bd_procesado);
			$error_mostrar_bd_procesado = str_replace("Ó","O",$error_mostrar_bd_procesado);
			$error_mostrar_bd_procesado = str_replace("Ú","U",$error_mostrar_bd_procesado);
			$error_mostrar_bd_procesado = str_replace("Ñ","N",$error_mostrar_bd_procesado);
			$error_mostrar_bd_procesado = str_replace(" "," ",$error_mostrar_bd_procesado);
			$error_mostrar_bd_procesado = str_replace("'"," ",$error_mostrar_bd_procesado);
			$error_mostrar_bd_procesado = str_replace("\n"," ",$error_mostrar_bd_procesado);
			$linea_res= alphanumericAndSpace($error_mostrar_bd_procesado);
			
			$error_mensaje.="ERROR EN SUBIR VALIDADOS CON EXITO A BD, ".$error_mostrar_bd_procesado;
		}//fin if error en bd
		
	}//fin if validados con exito subir a bd
	//FIN PARTE SUBIR VALIDADOS EN BD
	
	
	
	//PARTE CONSOLIDADO ESTADO VALIDACION RIPS
	$estado_validacion_rips=2;
	if($error_mensaje=="")
	{
		$estado_validacion_rips=1;
	}
	
	$query_id_info_prestador="";
	$query_id_info_prestador.="SELECT * FROM gios_prestador_servicios_salud WHERE cod_registro_especial_pss='".$cod_prestador."' ; ";
	$resultado_query_id_info_prestador=$coneccionBD->consultar2($query_id_info_prestador);
	
	$tipo_id_prestador="";
	$nit_prestador="";
	$codigo_depto_prestador="";
	$codigo_municipio_prestador="";
	if(count($resultado_query_id_info_prestador)>0)
	{
		$tipo_id_prestador=$resultado_query_id_info_prestador[0]["cod_tipo_identificacion"];
		$nit_prestador=$resultado_query_id_info_prestador[0]["num_tipo_identificacion"];
		$codigo_depto_prestador=$resultado_query_id_info_prestador[0]["cod_depto"];
		$codigo_municipio_prestador=$resultado_query_id_info_prestador[0]["cod_municipio"];
	}		
	
	
	
	$fecha_bd_array=explode("-",$date_remision_bd);
	$dia_bd=$fecha_bd_array[2];
	$mes_bd=$fecha_bd_array[1];
	$year_bd=$fecha_bd_array[0];
	
	$fecha_ini_periodo="";
	$fecha_fin_periodo="";
	
	if(intval($mes_bd)==1 || intval($mes_bd)==3 || intval($mes_bd)==5 || intval($mes_bd)==7 || intval($mes_bd)==8 || intval($mes_bd)==10 || intval($mes_bd)==12)
	{
		$fecha_ini_periodo=$year_bd."-".$mes_bd."-01";
		$fecha_fin_periodo=$year_bd."-".$mes_bd."-31";
	}
	
	if( intval($mes_bd)==4 || intval($mes_bd)==6 || intval($mes_bd)==9 || intval($mes_bd)==11 )
	{
		$fecha_ini_periodo=$year_bd."-".$mes_bd."-01";
		$fecha_fin_periodo=$year_bd."-".$mes_bd."-30";
	}
	
	if( intval($mes_bd)==2)
	{
		$fecha_ini_periodo=$year_bd."-".$mes_bd."-01";
		$fecha_fin_periodo=$year_bd."-".$mes_bd."-28";
	}
	
	$errores_bd_estado_validacion="";
	
		
	
	if(count($resultado_query_id_info_prestador)>0 && $verificacion_ya_se_valido_con_exito==false && $bool_se_esta_validando_en_este_momento==false)
	{	   
		$query_registrar_estado_validacion="";
		$query_registrar_estado_validacion.="INSERT INTO gioss_tabla_consolidacion_registros_validados_rips ";
		$query_registrar_estado_validacion.="(";
		$query_registrar_estado_validacion.="estado_validacion,";
		$query_registrar_estado_validacion.="fecha_validacion,";
		$query_registrar_estado_validacion.="numero_secuencia,";
		$query_registrar_estado_validacion.="nombre_archivo_control,";
		$query_registrar_estado_validacion.="fecha_remision_ct,";
		$query_registrar_estado_validacion.="tipo_identificacion_entidad_reportadora,";
		$query_registrar_estado_validacion.="numero_identificacion_entidad_reportadora,";
		$query_registrar_estado_validacion.="codigo_eapb,";
		$query_registrar_estado_validacion.="fecha_inicio_periodo,";
		$query_registrar_estado_validacion.="fecha_final_periodo,";	
		$query_registrar_estado_validacion.="codigo_entidad_reportadora,";
		$query_registrar_estado_validacion.="codigo_depto_prestador,";
		$query_registrar_estado_validacion.="codigo_municipio_prestador";
		$query_registrar_estado_validacion.=")";
		$query_registrar_estado_validacion.="VALUES";
		$query_registrar_estado_validacion.="(";
		$query_registrar_estado_validacion.="'".$estado_validacion_rips."',";
		$query_registrar_estado_validacion.="'".$fecha_actual."',";
		$query_registrar_estado_validacion.="'".$numero_secuencia_actual."',";
		$query_registrar_estado_validacion.="'CT".$numero_de_remision."',";
		$query_registrar_estado_validacion.="'".$date_remision_bd."',";
		$query_registrar_estado_validacion.="'".$tipo_id_prestador."',";
		$query_registrar_estado_validacion.="'".$nit_prestador."',";
		$query_registrar_estado_validacion.="'".$cod_eapb."',";
		$query_registrar_estado_validacion.="'$fecha_ini_periodo',";
		$query_registrar_estado_validacion.="'$fecha_fin_periodo',";
		$query_registrar_estado_validacion.="'".$cod_prestador."',";
		$query_registrar_estado_validacion.="'".$codigo_depto_prestador."',";
		$query_registrar_estado_validacion.="'".$codigo_municipio_prestador."'";
		$query_registrar_estado_validacion.=");";
		$error_bd_seq="";
		$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($query_registrar_estado_validacion, $error_bd_seq);		
		if($error_bd_seq!="")
		{
			$errores_bd_estado_validacion.=$error_bd_seq."<br>";
			if(connection_aborted()==false)
			{
				echo "<script>alert('".procesar_mensaje($errores_bd_estado_validacion)."');</script>";
			}
		}
	}
		
	//FIN PARTE CONSOLIDADO ESTADO VALIDACION RIPS
	
	
	
	//VERIFICACION EN FACTURAS RADICADAS CON LAS FACTURAS RECIEN INSERTADAS
	$numero_registros_vista=0;
	$contador_offset=0;
	$nombre_vista_facturas_rips_recien_insertadas="vfactrir_".$nick_user."_".$tipo_id."_".$identificacion;
	if($estado_validacion_rips==1 && $bool_se_esta_validando_en_este_momento==false && $verificacion_ya_se_valido_con_exito==false)
	{
		$query_consultar_facturas_rips_recien_insertadas="";
		$query_consultar_facturas_rips_recien_insertadas.=" CREATE OR REPLACE VIEW $nombre_vista_facturas_rips_recien_insertadas AS ";
		$query_consultar_facturas_rips_recien_insertadas.=" SELECT * FROM gioss_archivo_cargado_af ";
		$query_consultar_facturas_rips_recien_insertadas.=" WHERE numero_secuencia='".$numero_secuencia_actual."' ";
		$query_consultar_facturas_rips_recien_insertadas.=" ORDER BY numero_factura; ";
		$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($query_consultar_facturas_rips_recien_insertadas, $error_bd_seq);		
		if($error_bd_seq!="")
		{
			$errores_bd_estado_informacion.=$error_bd_seq."<br>";
			if(connection_aborted()==false)
			{
				echo "<script>alert('Error crear vista facturas cargadas con exito: ".procesar_mensaje($error_bd_seq)."');</script>";
				echo "<script>alert('query: ".procesar_mensaje($query_consultar_facturas_rips_recien_insertadas)."');</script>";
			}
		}
		
		if($error_bd_seq=="")
		{
			$query_numero_registros_vistas_facturas_recien_insertados="";
			$query_numero_registros_vistas_facturas_recien_insertados.=" SELECT count(*) AS registros_vista FROM $nombre_vista_facturas_rips_recien_insertadas ; ";
			$resultado_query_numero_registros=$coneccionBD->consultar2($query_numero_registros_vistas_facturas_recien_insertados);
			$numero_registros_vista=$resultado_query_numero_registros[0]["registros_vista"];
			
			while($contador_offset<$numero_registros_vista)
			{
				$limite=2000;
				
				if( ($contador_offset+2000)>=$numero_registros_vista)
				{
					$limite=2000+($numero_registros_vista-$contador_offset);
					$bool_ultima_seccion_para_ventana=true;
				}//fin if
				
				$query_consulta_desde_vista="";
				$query_consulta_desde_vista.=" SELECT * FROM $nombre_vista_facturas_rips_recien_insertadas LIMIT $limite OFFSET $contador_offset; ";
				$resultado_consulta_desde_la_vista=$coneccionBD->consultar2($query_consulta_desde_vista);
				
				if(is_array($resultado_consulta_desde_la_vista) && count($resultado_consulta_desde_la_vista)>0)
				{
					foreach($resultado_consulta_desde_la_vista as $resultado_de_facturas)
					{
						$numero_factura_recien_insertado=$resultado_de_facturas["numero_factura"];
						
						$query_busca_en_facturas_radicadas="";
						$query_busca_en_facturas_radicadas.=" SELECT * FROM gioss_facturas_radicadas ";
						$query_busca_en_facturas_radicadas.=" WHERE numero_factura='$numero_factura_recien_insertado'  ";
						$query_busca_en_facturas_radicadas.=" AND estado_verificacion_factura<>'1' ; ";
						$resultado_esta_en_facturas_radicadas=$coneccionBD->consultar2($query_busca_en_facturas_radicadas);
						
						if(is_array($resultado_esta_en_facturas_radicadas) && count($resultado_esta_en_facturas_radicadas)>0)
						{
							$query_actualizar_para_coincidentes_rips_con_radicadas="";
							$query_actualizar_para_coincidentes_rips_con_radicadas.=" UPDATE gioss_facturas_radicadas SET ";
							$query_actualizar_para_coincidentes_rips_con_radicadas.=" estado_verificacion_factura='1', ";
							$query_actualizar_para_coincidentes_rips_con_radicadas.=" nombre_archivo_af='".$resultado_de_facturas["codigo_archivo"]."', ";
							$query_actualizar_para_coincidentes_rips_con_radicadas.=" numero_factura_rips='".$resultado_de_facturas["numero_factura"]."', ";
							$query_actualizar_para_coincidentes_rips_con_radicadas.=" fecha_validacion_af='".$resultado_de_facturas["fecha_validacion_exito"]."', ";
							$query_actualizar_para_coincidentes_rips_con_radicadas.=" valor_factura_rips='".$resultado_de_facturas["valor_neto_a_pagar"]."' ";
							$query_actualizar_para_coincidentes_rips_con_radicadas.=" WHERE ";
							$query_actualizar_para_coincidentes_rips_con_radicadas.=" numero_factura='".$numero_factura_recien_insertado."' ";
							$query_actualizar_para_coincidentes_rips_con_radicadas.=";";
							$error_bd_act_fact_coincidente="";			
							$bool_funciono=$coneccionBD->insertar_no_warning_get_error($query_actualizar_para_coincidentes_rips_con_radicadas, $error_bd_act_fact_coincidente);
							if($error_bd_act_fact_coincidente!="")
							{
								if(connection_aborted()==false)
								{
									echo "<script>alert('Errores actualizar facturas radicadas: ".procesar_mensaje($error_bd_act_fact_coincidente)."');</script>";
									echo "<script>alert('query: ".procesar_mensaje($query_actualizar_para_coincidentes_rips_con_radicadas)."');</script>";
								}
							}//fin if hay error
						}//fin if
					}//fin foreach
				}//fin if
				
				$contador_offset+=2000;
			}//fin while
		}//fin if si no hubo error al crear la vista
		
	}//fin if
	else if($estado_validacion_rips==2 && $bool_se_esta_validando_en_este_momento==false && $verificacion_ya_se_valido_con_exito==false)
	{
		$query_consultar_facturas_rips_recien_insertadas="";
		$query_consultar_facturas_rips_recien_insertadas.=" CREATE OR REPLACE VIEW $nombre_vista_facturas_rips_recien_insertadas AS ";
		$query_consultar_facturas_rips_recien_insertadas.=" SELECT * FROM gioss_archivo_rechazado_af ";
		$query_consultar_facturas_rips_recien_insertadas.=" WHERE numero_secuencia='".$numero_secuencia_actual."' ";
		$query_consultar_facturas_rips_recien_insertadas.=" ORDER BY numero_factura; ";
		$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($query_consultar_facturas_rips_recien_insertadas, $error_bd_seq);		
		if($error_bd_seq!="")
		{
			$errores_bd_estado_informacion.=$error_bd_seq."<br>";
			if(connection_aborted()==false)
			{
				echo "<script>alert('Errores crear vista facturas rechazadas: ".procesar_mensaje($error_bd_seq)."');</script>";
				echo "<script>alert('query: ".procesar_mensaje($query_consultar_facturas_rips_recien_insertadas)."');</script>";
			}
		}
		
		if($error_bd_seq=="")
		{
			$query_numero_registros_vistas_facturas_recien_insertados="";
			$query_numero_registros_vistas_facturas_recien_insertados.=" SELECT count(*) AS registros_vista FROM $nombre_vista_facturas_rips_recien_insertadas ; ";
			$resultado_query_numero_registros=$coneccionBD->consultar2($query_numero_registros_vistas_facturas_recien_insertados);
			$numero_registros_vista=$resultado_query_numero_registros[0]["registros_vista"];
			
			while($contador_offset<$numero_registros_vista)
			{
				$limite=2000;
				
				if( ($contador_offset+2000)>=$numero_registros_vista)
				{
					$limite=2000+($numero_registros_vista-$contador_offset);
					$bool_ultima_seccion_para_ventana=true;
				}//fin if
				
				$query_consulta_desde_vista="";
				$query_consulta_desde_vista.=" SELECT * FROM $nombre_vista_facturas_rips_recien_insertadas LIMIT $limite OFFSET $contador_offset; ";
				$resultado_consulta_desde_la_vista=$coneccionBD->consultar2($query_consulta_desde_vista);
				
				if(is_array($resultado_consulta_desde_la_vista) && count($resultado_consulta_desde_la_vista)>0)
				{
					foreach($resultado_consulta_desde_la_vista as $resultado_de_facturas)
					{
						$numero_factura_recien_insertado=$resultado_de_facturas["numero_factura"];
						
						$query_busca_en_facturas_radicadas="";
						$query_busca_en_facturas_radicadas.=" SELECT * FROM gioss_facturas_radicadas ";
						$query_busca_en_facturas_radicadas.=" WHERE numero_factura='$numero_factura_recien_insertado'  ";
						$query_busca_en_facturas_radicadas.=" AND estado_verificacion_factura<>'1' ; ";
						$resultado_esta_en_facturas_radicadas=$coneccionBD->consultar2($query_busca_en_facturas_radicadas);
						
						if(is_array($resultado_esta_en_facturas_radicadas) && count($resultado_esta_en_facturas_radicadas)>0)
						{
							$query_actualizar_para_coincidentes_rips_con_radicadas="";
							$query_actualizar_para_coincidentes_rips_con_radicadas.=" UPDATE gioss_facturas_radicadas SET ";
							$query_actualizar_para_coincidentes_rips_con_radicadas.=" estado_verificacion_factura='2', ";
							$query_actualizar_para_coincidentes_rips_con_radicadas.=" nombre_archivo_af='".$resultado_de_facturas["codigo_archivo"]."', ";
							$query_actualizar_para_coincidentes_rips_con_radicadas.=" numero_factura_rips='".$resultado_de_facturas["numero_factura"]."', ";
							$query_actualizar_para_coincidentes_rips_con_radicadas.=" fecha_validacion_af='".$resultado_de_facturas["fecha_de_rechazo"]."', ";
							$query_actualizar_para_coincidentes_rips_con_radicadas.=" valor_factura_rips='".$resultado_de_facturas["valor_neto_a_pagar"]."' ";
							$query_actualizar_para_coincidentes_rips_con_radicadas.=" WHERE ";
							$query_actualizar_para_coincidentes_rips_con_radicadas.=" numero_factura='".$numero_factura_recien_insertado."' ";
							$query_actualizar_para_coincidentes_rips_con_radicadas.=";";
							$error_bd_act_fact_coincidente="";			
							$bool_funciono=$coneccionBD->insertar_no_warning_get_error($query_actualizar_para_coincidentes_rips_con_radicadas, $error_bd_act_fact_coincidente);
							if($error_bd_act_fact_coincidente!="")
							{
								if(connection_aborted()==false)
								{
									echo "<script>alert('Errores actualizar facturas radicadas: ".procesar_mensaje($error_bd_act_fact_coincidente)."');</script>";
									echo "<script>alert('query: ".procesar_mensaje($query_actualizar_para_coincidentes_rips_con_radicadas)."');</script>";
								}
							}//fin if hay error
						}//fin if 
					}//fin foreach
				}//fin if
				
				$contador_offset+=2000;
			}//fin while
		}//fin si no hubo error al crear vista
		
	}//fin else
	
	//borrando vistas
	
	if($bool_se_esta_validando_en_este_momento==false && $verificacion_ya_se_valido_con_exito==false)
	{
		$sql_borrar_vistas="";
		$sql_borrar_vistas.=" DROP VIEW $nombre_vista_facturas_rips_recien_insertadas ; ";
		$error_bd="";		
		$bool_funciono=$coneccionBD->insertar_no_warning_get_error($sql_borrar_vistas, $error_bd);		
		if($error_bd!="")
		{
			if(connection_aborted()==false)
			{
				echo "<script>alert('error al borrar vista(s) ".procesar_mensaje($error_bd)."');</script>";
			}
		}
	}
	
	//fin borrando vistas
	
	//FIN VERIFICACION EN FACTURAS RADICADAS
	
	//PARTE SUBIR INCONSISTENCIAS ENCONTRADAS A LA BASE DE DATOS
	$exito_mensaje="";
	$mostrar_error_bd_inconsistencias="";
	$numero_total_errores=0;
	$cont_errores_rips=1;
	if($verificacion_ya_se_valido_con_exito==false && $bool_se_esta_validando_en_este_momento==false)
	{
		$numero_total_errores=count(file($ruta_archivo_inconsistencias_rips));	
		$file_incons_leer_rips = new SplFileObject($ruta_archivo_inconsistencias_rips);
		$cont_lineas_errores=0;
		while ($cont_lineas_errores<$numero_total_errores) 
		{
			$file_incons_leer_rips->seek($cont_lineas_errores);
			$linea_tmp = $file_incons_leer_rips->current();
			$linea= explode("\n", $linea_tmp)[0];
			$campos = explode(",", $linea);
			if(count($campos)==6)
			{
				$array_codigos_detalle=explode(";;",$campos[2]);
				$cod_detalle_inconsistencia=$array_codigos_detalle[0];
				$detalle_inconsistencia=$array_codigos_detalle[1];
				$array_codigos_inconsistencias=explode("_",$cod_detalle_inconsistencia);
				
				$cod_tipo_inconsistencia=$array_codigos_inconsistencias[0];
				$cod_grupo_inconsistencia=$array_codigos_inconsistencias[1];
				$cod_detalle_inconsistencia_solo=$array_codigos_inconsistencias[2];
				
				$nom_archivo=$campos[3];
				$num_campo=$campos[4];
				$num_fila=$campos[5];
				
				$sql_insertar_inconsistencia_rips="";
				$sql_insertar_inconsistencia_rips.=" INSERT INTO gioss_reporte_inconsistencia_archivos_rips ";
				$sql_insertar_inconsistencia_rips.=" ( ";
				$sql_insertar_inconsistencia_rips.=" numero_orden, ";
				$sql_insertar_inconsistencia_rips.=" nombre_archivo_ct, ";
				$sql_insertar_inconsistencia_rips.=" cod_tipo_inconsitencia, ";
				$sql_insertar_inconsistencia_rips.=" nombre_tipo_inconsistencia, ";
				$sql_insertar_inconsistencia_rips.=" cod_grupo_inconsistencia, ";
				$sql_insertar_inconsistencia_rips.=" nombre_grupo_inconsistencia, ";
				$sql_insertar_inconsistencia_rips.=" cod_detalle_inconsistencia, ";
				$sql_insertar_inconsistencia_rips.=" detalle_inconsistencia, ";
				$sql_insertar_inconsistencia_rips.=" nombre_archivo_rips, ";
				$sql_insertar_inconsistencia_rips.=" numero_linea, ";
				$sql_insertar_inconsistencia_rips.=" numero_campo ";
				$sql_insertar_inconsistencia_rips.=" ) ";
				$sql_insertar_inconsistencia_rips.=" VALUES ";
				$sql_insertar_inconsistencia_rips.=" ( ";
				$sql_insertar_inconsistencia_rips.=" '".$numero_secuencia_actual."', ";
				$sql_insertar_inconsistencia_rips.=" 'CT".$numero_de_remision."', ";
				$sql_insertar_inconsistencia_rips.=" '".$cod_tipo_inconsistencia."', ";
				$sql_insertar_inconsistencia_rips.=" '".$array_tipo_validacion_rips[$cod_tipo_inconsistencia]."', ";
				$sql_insertar_inconsistencia_rips.=" '".$cod_grupo_inconsistencia."', ";
				$sql_insertar_inconsistencia_rips.=" '".$array_grupo_validacion_rips[$cod_grupo_inconsistencia]."', ";
				$sql_insertar_inconsistencia_rips.=" '".$cod_detalle_inconsistencia_solo."', ";
				$sql_insertar_inconsistencia_rips.=" '".$detalle_inconsistencia."', ";
				$sql_insertar_inconsistencia_rips.=" '".$nom_archivo."', ";
				$sql_insertar_inconsistencia_rips.=" '".$num_fila."', ";
				$sql_insertar_inconsistencia_rips.=" '".$num_campo."' ";
				$sql_insertar_inconsistencia_rips.=" ); ";
				$error_bd_ins="";
				$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_insertar_inconsistencia_rips, $error_bd_ins);
				if($error_bd_ins!="")
				{
					$mostrar_error_bd_inconsistencias.="ERROR AL REPORTAR INCONSISTENCIAS ".$cod_tipo_inconsistencia."_".$cod_detalle_inconsistencia_solo." : ".$error_bd_ins."<br>";
				}
				
				//PARTE INDICA  QUE SUBE ERRORES A BD
				$porcentaje_errores_subidos=0;
				if($numero_total_errores>0)
				{
					$porcentaje_errores_subidos=($cont_lineas_errores*100)/$numero_total_errores;
				}
				$mensaje_contador_errores="Subiendo errores encontrados al sistema ".$porcentaje_errores_subidos."% de 100%  ";
				$html_del_mensaje="";
				$html_del_mensaje.="<table>";
				$html_del_mensaje.="<tr>";
				$html_del_mensaje.="<td colspan=\'2\'>";
				$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
				$html_del_mensaje.="</td>";
				$html_del_mensaje.="</tr>";
				$html_del_mensaje.="<tr>";
				$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
				$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
				$html_del_mensaje.="</td>";
				$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
				$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_contador_errores."</div><div id=\'errores_bd_div\'></div>";
				$html_del_mensaje.="</td>";
				$html_del_mensaje.="</tr>";
				$html_del_mensaje.="</table>";
				if(connection_aborted()==false)
				{
					echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
					ob_flush();
					flush();
				}
				
				$cont_errores_rips++;
				//FIN PARTE INDICA  QUE SUBE ERRORES A BD
			}//fin if
			$cont_lineas_errores++;
		}//fin while
		
		//error de bd mostrar
		if($mostrar_error_bd_inconsistencias!="")
		{
			$error_mensaje.=procesar_mensaje3($mostrar_error_bd_inconsistencias);
			
		}//fin if error en bd
	}//fin if si no fue validado con exito anteriormente
	//FIN PARTE SUBIR INCONSISTENCIAS ENCONTRADAS ALA BASE DE DATOS
	
	
	//PARTE DATOS  A SUBIR PARA LA TABLA DE ESTADO DE INFORMACION RIPS
	
	$errores_bd_estado_informacion="";
			
	$numero_reg_ct=0;
	if(file_exists($ruta_archivo_ct)==true && $rutaTemporal!=trim($ruta_archivo_ct))
	{
		$numero_reg_ct=count(file($ruta_archivo_ct));
	}
	
	$numero_reg_af=0;
	if(file_exists($ruta_archivo_af)==true && $rutaTemporal!=trim($ruta_archivo_af))
	{
		$numero_reg_af=count(file($ruta_archivo_af));
	}
	
	$numero_reg_us=0;
	if(file_exists($ruta_archivo_us)==true && $rutaTemporal!=trim($ruta_archivo_us))
	{
		$numero_reg_us=count(file($ruta_archivo_us));
	}
	
	$numero_reg_ac=0;
	if(file_exists($ruta_archivo_ac)==true && $rutaTemporal!=trim($ruta_archivo_ac))
	{
		$numero_reg_ac=count(file($ruta_archivo_ac));
	}
	
	$numero_reg_ah=0;
	if(file_exists($ruta_archivo_ah)==true && $rutaTemporal!=trim($ruta_archivo_ah))
	{
		$numero_reg_ah=count(file($ruta_archivo_ah));
	}
	
	$numero_reg_ad=0;
	if(file_exists($ruta_archivo_ad)==true && $rutaTemporal!=trim($ruta_archivo_ad))
	{
		$numero_reg_ad=count(file($ruta_archivo_ad));
	}
			
	$numero_reg_ap=0;
	if(file_exists($ruta_archivo_ap)==true && $rutaTemporal!=trim($ruta_archivo_ap))
	{
		$numero_reg_ap=count(file($ruta_archivo_ap));
	}
	
	$numero_reg_au=0;
	if(file_exists($ruta_archivo_au)==true && $rutaTemporal!=trim($ruta_archivo_au))
	{
		$numero_reg_au=count(file($ruta_archivo_au));
	}
	
	$numero_reg_an=0;
	if(file_exists($ruta_archivo_an)==true && $rutaTemporal!=trim($ruta_archivo_an))
	{
		$numero_reg_an=count(file($ruta_archivo_an));
	}
	
	$numero_reg_am=0;
	if(file_exists($ruta_archivo_am)==true && $rutaTemporal!=trim($ruta_archivo_am))
	{
		$numero_reg_am=count(file($ruta_archivo_am));
	}
	
	$numero_reg_at=0;
	if(file_exists($ruta_archivo_at)==true && $rutaTemporal!=trim($ruta_archivo_at))
	{
		$numero_reg_at=count(file($ruta_archivo_at));
	}
	
	$query_info_eapb="SELECT * FROM gios_entidad_administradora WHERE cod_entidad_administradora='$cod_eapb' ;";
	$resultado_query_info_eapb=$coneccionBD->consultar2($query_info_eapb);
	$nombre_eapb="";
	if(count($resultado_query_info_eapb)>0)
	{
		$nombre_eapb=$resultado_query_info_eapb[0]["nom_entidad_administradora"];
	}
	
	$query_id_info_prestador="";
	$query_id_info_prestador.="SELECT * FROM gios_prestador_servicios_salud WHERE cod_registro_especial_pss='".$cod_prestador."' ; ";
	$resultado_query_id_info_prestador=$coneccionBD->consultar2($query_id_info_prestador);
	
	$nombre_prestador="";
	$tipo_id_prestador="";
	$nit_prestador="";
	$codigo_depto_prestador="";
	$codigo_municipio_prestador="";
	if(count($resultado_query_id_info_prestador)>0)
	{
		$tipo_id_prestador=$resultado_query_id_info_prestador[0]["cod_tipo_identificacion"];
		$nit_prestador=$resultado_query_id_info_prestador[0]["num_tipo_identificacion"];
		$codigo_depto_prestador=$resultado_query_id_info_prestador[0]["cod_depto"];
		$codigo_municipio_prestador=$resultado_query_id_info_prestador[0]["cod_municipio"];
		$nombre_prestador=$resultado_query_id_info_prestador[0]["nom_entidad_prestadora"];
	}		
	
	$query_descripcion_estado_validacion="";
	$query_descripcion_estado_validacion.=" SELECT * FROM gioss_estado_validacion_archivos WHERE codigo_estado_validacion='$estado_validacion_rips' ; ";
	$resultado_query_descripcion_estado_validacion=$coneccionBD->consultar2($query_descripcion_estado_validacion);
	$descripcion_estado_validacion=$resultado_query_descripcion_estado_validacion[0]["descripcion_estado_validacion"];
	
	$query_nombre_dpt="SELECT * FROM gios_dpto WHERE cod_departamento='$codigo_depto_prestador' ; ";
	$resultado_query_dpto=$coneccionBD->consultar2($query_nombre_dpt);
	$nombre_dpto="";
	if(count($resultado_query_dpto)>0)
	{
		$nombre_dpto=$resultado_query_dpto[0]["nom_departamento"];
	}
	
	$query_nombre_mpio="SELECT * FROM gios_mpio WHERE cod_municipio='$codigo_municipio_prestador' ; ";
	$resultado_query_mpio=$coneccionBD->consultar2($query_nombre_mpio);
	$nombre_mpio="";
	if(count($resultado_query_mpio)>0)
	{
		$nombre_mpio=$resultado_query_mpio[0]["nom_municipio"];
	}
	
	$acumulado_factura_af=0.0;
	if($estado_validacion_rips==1)
	{
		$query_acumulado_factura="";
		$query_acumulado_factura.="SELECT SUM(COALESCE(valor_neto_a_pagar::numeric,0)) AS acumulado_valor_neto FROM gioss_archivo_cargado_af WHERE numero_secuencia='$numero_secuencia_actual' ";
		$resultado_query_acumulado_factura=$coneccionBD->consultar2($query_acumulado_factura);
		if(is_array($resultado_query_acumulado_factura) && count($resultado_query_acumulado_factura)>0)
		{
			$acumulado_factura_af=$resultado_query_acumulado_factura[0]["acumulado_valor_neto"];
		}
	}
	if($estado_validacion_rips==2)
	{
		$query_acumulado_factura="";
		$query_acumulado_factura.="SELECT SUM(COALESCE(valor_neto_a_pagar::numeric,0)) AS acumulado_valor_neto FROM gioss_archivo_rechazado_af WHERE numero_secuencia='$numero_secuencia_actual' ";
		$resultado_query_acumulado_factura=$coneccionBD->consultar2($query_acumulado_factura);
		if(is_array($resultado_query_acumulado_factura) && count($resultado_query_acumulado_factura)>0)
		{
			$acumulado_factura_af=$resultado_query_acumulado_factura[0]["acumulado_valor_neto"];
		}
	}
	
	$valor_neto_a_pagar=0;
	if(floatval($acumulado_factura_af)>0)
	{
		$valor_neto_a_pagar=$acumulado_factura_af;
	}
		
	
	if(count($resultado_query_id_info_prestador)>0 && count($resultado_query_info_eapb)>0 && count($resultado_query_dpto)>0 && count($resultado_query_mpio)>0
	   && $verificacion_ya_se_valido_con_exito==false && $bool_se_esta_validando_en_este_momento==false
	   //&& $verificacion_es_diferente_prestador_en_ct==false
	   )
	{	   
		$query_registrar_estado_informacion="";
		$query_registrar_estado_informacion.="INSERT INTO gioss_tabla_estado_informacion_rips ";
		$query_registrar_estado_informacion.="(";
		$query_registrar_estado_informacion.="codigo_estado_informacion,";//1
		$query_registrar_estado_informacion.="nombre_estado_informacion,";//2
		$query_registrar_estado_informacion.="fecha_validacion,";//3
		$query_registrar_estado_informacion.="numero_secuencia,";//4
		$query_registrar_estado_informacion.="codigo_eapb,";//5
		$query_registrar_estado_informacion.="nombre_eapb,";//6
		$query_registrar_estado_informacion.="codigo_prestador_servicios,";//7
		$query_registrar_estado_informacion.="nombre_prestador_servicios,";//8
		$query_registrar_estado_informacion.="tipo_identificacion_prestador,";//9
		$query_registrar_estado_informacion.="numero_identificacion_prestador,";//10	
		$query_registrar_estado_informacion.="nombre_del_archivo_ct,";//11
		$query_registrar_estado_informacion.="valor_neto_a_pagar,";//12
		$query_registrar_estado_informacion.="numero_registros_af,";//13
		$query_registrar_estado_informacion.="numero_registros_us,";//14
		$query_registrar_estado_informacion.="numero_registros_ac,";//15
		$query_registrar_estado_informacion.="numero_registros_ap,";//16
		$query_registrar_estado_informacion.="numero_registros_au,";//17
		$query_registrar_estado_informacion.="numero_registros_ah,";//18
		$query_registrar_estado_informacion.="numero_registros_an,";//19
		$query_registrar_estado_informacion.="numero_registros_am,";//20
		$query_registrar_estado_informacion.="numero_registros_at,";//21
		$query_registrar_estado_informacion.="numero_registros_ct,";//27
		$query_registrar_estado_informacion.="codigo_departamento,";//22
		$query_registrar_estado_informacion.="nombre_del_departamento,";//23
		$query_registrar_estado_informacion.="codigo_municipio,";//24
		$query_registrar_estado_informacion.="nombre_de_municipio,";//25
		$query_registrar_estado_informacion.="fecha_remision";//26
		$query_registrar_estado_informacion.=")";
		$query_registrar_estado_informacion.="VALUES";
		$query_registrar_estado_informacion.="(";
		$query_registrar_estado_informacion.="'".$estado_validacion_rips."',";//1
		$query_registrar_estado_informacion.="'".$descripcion_estado_validacion."',";//2
		$query_registrar_estado_informacion.="'".$fecha_actual."',";//3
		$query_registrar_estado_informacion.="'".$numero_secuencia_actual."',";//4		
		$query_registrar_estado_informacion.="'".$cod_eapb."',";//5
		$query_registrar_estado_informacion.="'".$nombre_eapb."',";//6
		$query_registrar_estado_informacion.="'".$cod_prestador."',";//7
		$query_registrar_estado_informacion.="'".$nombre_prestador."',";//8
		$query_registrar_estado_informacion.="'".$tipo_id_prestador."',";//9
		$query_registrar_estado_informacion.="'".$nit_prestador."',";//10
		$query_registrar_estado_informacion.="'CT".$numero_de_remision."',";//11
		$query_registrar_estado_informacion.="'$valor_neto_a_pagar',";//12	
		$query_registrar_estado_informacion.="'$numero_reg_af',";//13
		$query_registrar_estado_informacion.="'$numero_reg_us',";//14
		$query_registrar_estado_informacion.="'$numero_reg_ac',";//15
		$query_registrar_estado_informacion.="'$numero_reg_ap',";//16
		$query_registrar_estado_informacion.="'$numero_reg_au',";//17
		$query_registrar_estado_informacion.="'$numero_reg_ah',";//18
		$query_registrar_estado_informacion.="'$numero_reg_an',";//19
		$query_registrar_estado_informacion.="'$numero_reg_am',";//20
		$query_registrar_estado_informacion.="'$numero_reg_at',";//21
		$query_registrar_estado_informacion.="'$numero_reg_ct',";//27	
		$query_registrar_estado_informacion.="'".$codigo_depto_prestador."',";//22
		$query_registrar_estado_informacion.="'".$nombre_dpto."',";//23
		$query_registrar_estado_informacion.="'".$codigo_municipio_prestador."',";//24
		$query_registrar_estado_informacion.="'".$nombre_mpio."',";//25
		$query_registrar_estado_informacion.="'".$date_remision_bd."' ";//26
		$query_registrar_estado_informacion.=");";
		$error_bd_seq="";
		$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($query_registrar_estado_informacion, $error_bd_seq);		
		if($error_bd_seq!="")
		{
			$errores_bd_estado_informacion.=$error_bd_seq."<br>";
			if(connection_aborted()==false)
			{
				echo "<script>alert('Errores insertar tabla estado informacion: ".procesar_mensaje($error_bd_seq)."');</script>";
				echo "<script>alert('query: ".procesar_mensaje($query_registrar_estado_informacion)."');</script>";
			}
		}
	}
	
	//FIN PARTE DATOS  A SUBIR PARA LA TABLA DE ESTADO DE INFORMACION RIPS
	
	if($bool_se_esta_validando_en_este_momento==false)
	{
		$sql_query_delete_esta_siendo_procesado="";
		$sql_query_delete_esta_siendo_procesado.=" DELETE FROM gioss_rips_ips_esta_validando_actualmente ";
		$sql_query_delete_esta_siendo_procesado.=" WHERE fecha_remision='".$date_remision_bd."' ";
		$sql_query_delete_esta_siendo_procesado.=" AND codigo_eapb_reportadora='".$cod_prestador."' ";
		$sql_query_delete_esta_siendo_procesado.=" AND nombre_archivo_ct='CT".$numero_de_remision."'  ";
		$sql_query_delete_esta_siendo_procesado.=" ; ";
		$error_bd_del="";
		$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_query_delete_esta_siendo_procesado, $error_bd_del);
		if($error_bd_del!="")
		{
			if(connection_aborted()==false)
			{
				echo "<script>alert('".procesar_mensaje($error_bd_seq)."');</script>";
			}
		}
	}
	
	//PARTE BOTONES DESCARGA 
		
	if($verificacion_ya_se_valido_con_exito==false && $bool_se_esta_validando_en_este_momento==false)
	{
		//PARTE NUEVO REPORTE(NUEVA ESTRUCTURA)
		
		//POR NUMERO DE SECUENCIA
		$numero_secuencia_para_bd=$numero_secuencia_actual;
		$ruta_archivo_inconsistencias_traer="";
		$sql_numero_registros="";
		$sql_numero_registros.="SELECT count(*) as contador FROM gioss_reporte_inconsistencia_archivos_rips WHERE numero_orden='$numero_secuencia_para_bd';  ";
		$resultado_query_numero_registros=$coneccionBD->consultar2($sql_numero_registros);
		$numero_registros=intval($resultado_query_numero_registros[0]["contador"]);
		
		$nombre_vista_inconsistencias="vincosrips_".$nick_user."_".$tipo_id."_".$identificacion;
		
		$sql_vista_inconsistencias="";
		$sql_vista_inconsistencias.="CREATE OR REPLACE VIEW $nombre_vista_inconsistencias ";
		$sql_vista_inconsistencias.=" AS SELECT * FROM gioss_reporte_inconsistencia_archivos_rips WHERE numero_orden='$numero_secuencia_para_bd' order by numero_linea, numero_campo ; ";
		$error_bd_seq="";
		$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_vista_inconsistencias, $error_bd_seq);
		
		
		
		$cont_linea=1;
		$contador_offset=0;
		$hubo_resultados=false;
		$puso_titulos=false;
		$nombre_archivo_inconsistencias="";
		while($contador_offset<$numero_registros)
		{
			$limite=2000;
			
			if( ($contador_offset+2000)>=$numero_registros)
			{
				$limite=2000+($numero_registros-$contador_offset);
			}
		
			//Ejemplo: SELECT *  FROM vista_inconsistencias_rips_".$nick_user."_".$tipo_id."_".$identificacion." WHERE numero_orden='29'  order by numero_linea, numero_campo limit 5 offset 0; 
			$sql_query_busqueda="";
			$sql_query_busqueda.="SELECT * FROM $nombre_vista_inconsistencias LIMIT $limite OFFSET $contador_offset;  ";
			$resultado_query_inconsistencias=$coneccionBD->consultar2($sql_query_busqueda);
		
			if(count($resultado_query_inconsistencias)>0)
			{
				
				$nombre_ct=$resultado_query_inconsistencias[0]["nombre_archivo_ct"];
				$numero_seq=$resultado_query_inconsistencias[0]["numero_orden"];
				$nombre_archivo_inconsistencias="inconsistencias-ct".$numero_de_remision."-".$numero_secuencia_actual."-".$string_tiempo_fecha.".csv";
				$ruta_archivo_inconsistencias_traer=$rutaTemporal.$nombre_archivo_inconsistencias;
				
				//PARTE INDICAR HUBO RESULTADOS UNA SOLA VEZ Y CREAR EL ARCHIVO EN BLANCO
				if($hubo_resultados==false)
				{
					$file_inconsistencias= fopen($ruta_archivo_inconsistencias_traer, "w") or die("fallo la creacion del archivo");
					fclose($file_inconsistencias);
				}
				
				$hubo_resultados=true;
				//FIN PARTE INDICAR HUBO RESULTADOS UNA SOLA VEZ Y CREAR EL ARCHIVO EN BLANCO
				
				
				$file_inconsistencias= fopen($ruta_archivo_inconsistencias_traer, "a") or die("fallo la creacion del archivo");
				
				if($puso_titulos==false)
				{
					$titulos="";
					$titulos.="consecutivo,numero de secuencia,codigo tipo inconsistencia,tipo inconsistencia,codigo grupo inconsistencia,grupo inconsistencia,";
					$titulos.="codigo detalle inconsistencia,detalle inconsistencia,nombre archivo rips, numero de linea, numero de campo";
					fwrite($file_inconsistencias, $titulos."\n");
					$puso_titulos=true;
				}//fin if
				
				foreach($resultado_query_inconsistencias as $resultado)
				{
					$linea_inconsistencia="";
					$linea_inconsistencia.=$cont_linea.",".$resultado["nombre_archivo_ct"].",".$resultado["cod_tipo_inconsitencia"].",";
					$linea_inconsistencia.=$resultado["nombre_tipo_inconsistencia"].",".$resultado["cod_grupo_inconsistencia"].",".$resultado["nombre_grupo_inconsistencia"].",";
					$linea_inconsistencia.=$resultado["cod_detalle_inconsistencia"].",".$resultado["detalle_inconsistencia"].",".$resultado["nombre_archivo_rips"].",";
					$linea_inconsistencia.=$resultado["numero_linea"].",".$resultado["numero_campo"];
					fwrite($file_inconsistencias, $linea_inconsistencia."\n");
					
					$mensaje_contador_errores="Escribiendo errores en el log los errores hallados, $cont_linea de $numero_registros ";
					$html_del_mensaje="";
					$html_del_mensaje.="<table>";
					$html_del_mensaje.="<tr>";
					$html_del_mensaje.="<td colspan=\'2\'>";
					$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="</tr>";
					$html_del_mensaje.="<tr>";
					$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
					$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
					$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_contador_errores."</div><div id=\'errores_bd_div\'></div>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="</tr>";
					$html_del_mensaje.="</table>";
					if(connection_aborted()==false)
					{
						echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
						ob_flush();
						flush();
					}
					
					$cont_linea++;
				}
				fclose($file_inconsistencias);
			
				
				
				
			}//fin if hallo resultados
			
			$contador_offset+=2000;
		
		}//fin while
		
		//borrando vistas
		
		$sql_borrar_vistas="";
		$sql_borrar_vistas.=" DROP VIEW $nombre_vista_inconsistencias ; ";
		
		$error_bd="";		
		$bool_funciono=$coneccionBD->insertar_no_warning_get_error($sql_borrar_vistas, $error_bd);		
		if($error_bd!="")
		{
			$mensajes_error_bd.="error al borrar vistas ".$error_bd."<br>";
		}
		
		//fin borrando vistas
		//FIN PARTE NUEVO REPORTE
		
		if(connection_aborted()==false)
		{
			echo "<script>document.getElementById('mensaje').innerHTML=' Se ha terminado de revisar los archivos RIPS';</script>";
			ob_flush();
			flush();
		}
		
		//CREAR ZIP
		$archivos_a_comprimir=array();
		$archivos_a_comprimir[0]=$ruta_archivo_inconsistencias_traer;
		$ruta_zip=$rutaTemporal."inconsistencias_CT".$numero_de_remision."_".$numero_secuencia_actual."_".$string_tiempo_fecha.".zip";
		$result_zip = create_zip($archivos_a_comprimir,$ruta_zip);
		if(connection_aborted()==false)
		{
			echo "<script>var ruta_zip= '$ruta_zip'; </script>";
			ob_flush();
			flush();
		}
		//FIN CREAR ZIP
		
		//SI SE ESCRIBIERON ERRORES OBLIGATORIOS EN EL ARCHIVO DE INCONSISTENCIAS DE LOS RIPS
		if(
		   $se_genero_archivo_de_inconsistencias
		   && $verificacion_ya_se_valido_con_exito==false
		   )
		{
			$error_mensaje.=" <input type=\'button\' value=\'Descargar archivo de inconsistencias para RIPS\'  class=\'btn btn-success color_boton\' onclick=\"download_inconsistencias_campos(\'$ruta_zip\');\"/> ";
		}
		
		//SI HUBIERON ERRORES DE TIPO INFORMATIVOS
		if($error_mensaje=="")
		{
			//verifica si hay algo escrito en el archivo con formato viejo
			$hay_errores_escritos=false;
			$file_incons_leer_rips = fopen($ruta_archivo_inconsistencias_traer, "r") or die("fallo la apertura del archivo");
			while (!feof($file_incons_leer_rips)) 
			{
				$linea_tmp = fgets($file_incons_leer_rips);
				$linea= explode("\n", $linea_tmp)[0];
				$campos = explode(",", $linea);
				if($linea!="" && count($campos)>1)
				{
					$hay_errores_escritos=true;
				}
			}//fin while
			fclose($file_incons_leer_rips);
			//fin verifica
			
			if($hay_errores_escritos)
			{
				$exito_mensaje.=" <input type=\'button\' value=\'Descargar archivo de inconsistencias informativas para RIPS\'  class=\'btn btn-success color_boton\' onclick=\"download_inconsistencias_campos(\'$ruta_zip\');\"/> ";
			}
		}//fin if para mostrar descarga si hubo errores informativos en caso de haber sido cargado con exito
	}//fin if si no se valido con exito anteriormente
	//FIN PARTE BOTONES DESCARGA 

	//PARTE MENSAJES FINALES
	if($error_mensaje!="")
	{
		if(connection_aborted()==false)
		{
			echo "<script>document.getElementById('div_mensaje_error').style.display='inline';</script>";
		}
		if($verificacion_ya_se_valido_con_exito==true)
		{
			if(connection_aborted()==false)
			{
				echo "<script>document.getElementById('titulo_mensaje_error').innerHTML='Error el archivo ya fue validado y cargado con exito:';</script>";
				echo "<script>document.getElementById('parrafo_mensaje_error').innerHTML='$error_mensaje con el numero de secuencia $numero_secuencia_actual . <br> Puede verificar a traves de la opci&oacuten consulta-consulta validacion en el menu de informaci&oacuten obligatoria resoluci&oacuten 3374 RIPS';</script>";
			}
		}
		else if($bool_se_esta_validando_en_este_momento==true)
		{
			if(connection_aborted()==false)
			{
				echo "<script>document.getElementById('titulo_mensaje_error').innerHTML='Error el archivo se esta validando en este momento:';</script>";
				echo "<script>document.getElementById('parrafo_mensaje_error').innerHTML='$error_mensaje <br> ';</script>";
			}
		}
		else
		{
			if(connection_aborted()==false)
			{
				echo "<script>document.getElementById('parrafo_mensaje_error').innerHTML='$error_mensaje con el numero de secuencia $numero_secuencia_actual';</script>";
			}
		}
		if(connection_aborted()==false)
		{
			ob_flush();
			flush();
		}
	}
	else
	{
		if(connection_aborted()==false)
		{
			echo "<script>document.getElementById('div_mensaje_exito').style.display='inline';</script>";	
			echo "<script>document.getElementById('parrafo_mensaje_exito').innerHTML='$exito_mensaje con el numero de secuencia $numero_secuencia_actual';</script>";
			ob_flush();
			flush();
		}
	}
	
		
	//FIN PARTE MENSAJES FINALES
	
	//PARTE ENVIAR E-MAIL
	try
	{
		if($error_mensaje!="")
		{	
			//si hubo errores obligatorios
			
			// inicio envio de mail

			$mail = new PHPMailer();
			$mail->IsSMTP();
			$mail->SMTPAuth = true;
			$mail->SMTPSecure = "ssl";
			$mail->Host = "smtp.gmail.com";
			$mail->Port = 465;
			$mail->Username = "sistemagioss@gmail.com";
			$mail->Password = "gioss001";
			$mail->From = "sistemagioss@gmail.com";
			$mail->FromName = "GIOSS";
			$mail->Subject = "Inconsistencias RIPS 3374 ";
			$mail->AltBody = "Cordial saludo,\n El sistema ha determinado que su archivo(s)  contiene diversas inconsistencias,\n las cuales pueden ser: campos con información inconsistente, usuarios duplicados ó el uso de caracteres especiales(acentos,'Ñ' o É,Ý, ¥, ¤, ´)";
	    
			$mail->MsgHTML("Cordial saludo,\n El sistema ha determinado que su archivo(s)  contiene diversos errores y va con el numero de secuencia $numero_secuencia_actual.<strong>GIOSS</strong>.");
			if($verificacion_ya_se_valido_con_exito==false)
			{
				$mail->AddAttachment($ruta_zip);
			}
			$mail->AddAddress($correo_electronico, "Destinatario");
	    
			$mail->IsHTML(true);
	    
			if (!$mail->Send()) 
			{
			    
			}
			else 
			{
				if(connection_aborted()==false)
				{
					echo "<script>alert('Se ha enviado una copia del log con las inconsistencias encontradas a su correo $correo_electronico')</script>";
				}
			}
	    
			//fin envio de mail
		}
		else if($bool_se_esta_validando_en_este_momento==false)
		{
			//si no hubo errores obligatorios
			
			// inicio envio de mail

			$mail = new PHPMailer();
			$mail->IsSMTP();
			$mail->SMTPAuth = true;
			$mail->SMTPSecure = "ssl";
			$mail->Host = "smtp.gmail.com";
			$mail->Port = 465;
			$mail->Username = "sistemagioss@gmail.com";
			$mail->Password = "gioss001";
			$mail->From = "sistemagioss@gmail.com";
			$mail->FromName = "GIOSS";
			$mail->Subject = "Carga archivos RIPS 3374 ";
			$mail->AltBody = "Cordial saludo,\n El sistema ha determinado que sus archivos fue validado con exito";
	    
			$mail->MsgHTML("Cordial saludo,\n El sistema ha determinado que sus archivo no contiene errores obligatorios y fue cargado con exito con el numero de secuencia $numero_secuencia_actual.<strong>GIOSS</strong>.");
			$mail->AddAttachment($ruta_zip);
			$mail->AddAddress($correo_electronico, "Destinatario");
	    
			$mail->IsHTML(true);
	    
			if (!$mail->Send()) 
			{
			    
			}
			else 
			{
				if(connection_aborted()==false)
				{
					echo "<script>alert('Se ha enviado una copia del log con las inconsistencias  informativas encontradas a su correo $correo_electronico')</script>";
				}
			}
	    
			//fin envio de mail
		}
	}
	catch(Exception $e)
	{
	}
	//FIN PARTE ENVIAR E-MAIL
}
//FIN PARTE QUE VALIDA LOAS ARCHIVOS RIPS PRESTADOR

//PARTE QUE VALIDA LOS ARCHIVOS RIPS EAPB
if(isset($_POST["accion"])
   && isset($_POST["nombre_archivo_rips"])
   && isset($_POST["date_ruta"])
   && isset($_POST["numero_remision_archivos_rips_eapb"])
   && isset($_POST["dpto"])
   && $_POST["accion"]=="validar" && $_POST["date_ruta"]!=""
   && strlen($_POST["nombre_archivo_rips"])==35
   && $_POST["numero_remision_archivos_rips_eapb"]!=""
   && $_POST["dpto"]!="none" && $_POST["dpto"]!=""
   && $_POST["tipo_archivo_rips"]=="eapb")
{
	$string_date_ruta=$_POST["date_ruta"];
	$ruta_nueva="rips".$nick_user.$string_date_ruta;
	$nombre_archivo=$_POST["nombre_archivo_rips"];
	//aqui el prestador es la entidad reportadora
	$cod_prestador=$_POST["prestador"];
	$cod_entidad_reportadora_eapb_fact=$_POST["prestador"];
	$cod_eapb=$_POST["eapb"];
	$numero_de_remision=$_POST["numero_remision_archivos_rips_eapb"];
	$fecha_remision=$_POST["fecha_remision"];
	
	$periodo_rips_archivo=intval(substr($_POST["numero_remision_archivos_rips_eapb"],0,2));
	
	$fecha_remision_array=explode("/",$fecha_remision);
	$date_remision_bd=$fecha_remision_array[2]."-".$fecha_remision_array[0]."-".$fecha_remision_array[1];
	
	$ruta_archivo_inconsistencias_rips=$rutaTemporal."inconsistenciasRIPS_".$cod_prestador."_".$string_tiempo_fecha.".csv";
	$file_inconsistencias_rips = fopen($ruta_archivo_inconsistencias_rips, "w") or die("fallo la creacion del archivo");
	
	$numero_facturas=array();
	
	$numero_secuencia_actual="";
	$numero_secuencia_previa_si_fue_validado_con_exito="";
	
	
	//SEPARACION GEOGRAFICA
	$ruta_archivo_ct = $rutaTemporal.$ruta_nueva."/"."CT".$numero_de_remision.".txt";
	$ruta_archivo_us = $rutaTemporal.$ruta_nueva."/"."US".$numero_de_remision.".txt";
	$ruta_archivo_ac = $rutaTemporal.$ruta_nueva."/"."AC".$numero_de_remision.".txt";
	$ruta_archivo_ah = $rutaTemporal.$ruta_nueva."/"."AH".$numero_de_remision.".txt";
	$ruta_archivo_ap = $rutaTemporal.$ruta_nueva."/"."AP".$numero_de_remision.".txt";
	$ruta_archivo_au = $rutaTemporal.$ruta_nueva."/"."AU".$numero_de_remision.".txt";
	$ruta_archivo_an = $rutaTemporal.$ruta_nueva."/"."AN".$numero_de_remision.".txt";
	$ruta_archivo_am = $rutaTemporal.$ruta_nueva."/"."AM".$numero_de_remision.".txt";
	$ruta_archivo_av = $rutaTemporal.$ruta_nueva."/"."AV".$numero_de_remision.".txt";
	
	
	$ruta_separado_localizacion=$rutaTemporal.$ruta_nueva."/"."localizacion";
	if(!file_exists($ruta_separado_localizacion))
	{
		mkdir($ruta_separado_localizacion, 0700);
	}//fin if
	
	
	$ruta_ct_filtrado=$ruta_separado_localizacion."/"."CT".$numero_de_remision.".txt";
	$ruta_us_filtrado=$ruta_separado_localizacion."/"."US".$numero_de_remision.".txt";
	$ruta_ac_filtrado=$ruta_separado_localizacion."/"."AC".$numero_de_remision.".txt";
	$ruta_ah_filtrado=$ruta_separado_localizacion."/"."AH".$numero_de_remision.".txt";
	$ruta_ap_filtrado=$ruta_separado_localizacion."/"."AP".$numero_de_remision.".txt";
	$ruta_au_filtrado=$ruta_separado_localizacion."/"."AU".$numero_de_remision.".txt";
	$ruta_an_filtrado=$ruta_separado_localizacion."/"."AN".$numero_de_remision.".txt";
	$ruta_am_filtrado=$ruta_separado_localizacion."/"."AM".$numero_de_remision.".txt";
	$ruta_av_filtrado=$ruta_separado_localizacion."/"."AV".$numero_de_remision.".txt";
	
	$departamento_filtro=$_POST["dpto"];
	$municipio_filtro=$_POST["mpio"];
	
	$mpio_filtro_bd="";
			
	if($municipio_filtro!="none")
	{
		$mpio_filtro=explode($departamento_filtro,$municipio_filtro);
		if(isset($mpio_filtro[1]))
		{
			$mpio_filtro_bd=$mpio_filtro[1];
		}
		else
		{
			$mpio_filtro_bd=$municipio_filtro;
		}
	}
	else
	{
		$mpio_filtro_bd="000";
	}
	
	$array_afiliados_filtrados_localizacion=array();
	$lineas_del_archivo_us=0;
	$lineas_del_archivo_ac=0;
	$lineas_del_archivo_ah=0;
	$lineas_del_archivo_ap=0;
	$lineas_del_archivo_au=0;
	$lineas_del_archivo_an=0;
	$lineas_del_archivo_am=0;
	$lineas_del_archivo_av=0;
	
	$lineas_del_archivo_us_fl=0;
	$lineas_del_archivo_ac_fl=0;
	$lineas_del_archivo_ah_fl=0;
	$lineas_del_archivo_ap_fl=0;
	$lineas_del_archivo_au_fl=0;
	$lineas_del_archivo_an_fl=0;
	$lineas_del_archivo_am_fl=0;
	$lineas_del_archivo_av_fl=0;
	if($departamento_filtro!="none")
	{
		$mensaje_filtro="Filtrando por localizacion el archivo US...";
		$mensaje_filtro.="  Por Favor Espere. ";
		$html_del_mensaje="";
		$html_del_mensaje.="<table>";
		$html_del_mensaje.="<tr>";
		$html_del_mensaje.="<td colspan=\'2\'>";
		$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
		$html_del_mensaje.="</td>";
		$html_del_mensaje.="</tr>";
		$html_del_mensaje.="<tr>";
		$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
		$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
		$html_del_mensaje.="</td>";
		$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
		$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_filtro."</div><div id=\'errores_bd_div\'></div>";
		$html_del_mensaje.="</td>";
		$html_del_mensaje.="</tr>";
		$html_del_mensaje.="</table>";
		if(connection_aborted()==false)
		{
			echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
			ob_flush();
			flush();
		}
		
		$lineas_del_archivo_us = count(file($ruta_archivo_us)); 
		$file_us = fopen($ruta_archivo_us, 'r') or exit("No se pudo abrir el archivo");
		$file_us_filtrado=fopen($ruta_us_filtrado, 'w') or exit("No se pudo abrir o crear el archivo");
		$nlinea=0;
		while (!feof($file_us)) 
		{
			$linea_tmp = fgets($file_us);
			$linea= explode("\n", $linea_tmp)[0];
			$linea = str_replace(array("\n", "\t", "\r"), '', $linea);
			$linea_fixer=preg_replace("/[^A-Za-z0-9:.\-\/\,\_\s]/", "", $linea );
			$campos = explode(",", $linea);
			if(count($campos)==12)
			{
				if($campos[9]==trim($departamento_filtro))
				{
					if($municipio_filtro!="none"
					   && $municipio_filtro!=""
					   && trim($departamento_filtro).$campos[10]==trim($municipio_filtro))
					{						
						$array_afiliados_filtrados_localizacion[$campos[1]."_".$campos[2]]=1;
						if($lineas_del_archivo_us_fl==0)
						{
							fwrite($file_us_filtrado, $linea_fixer);
						}
						else
						{
							fwrite($file_us_filtrado, "\n".$linea_fixer);
						}
						$lineas_del_archivo_us_fl++;
					}
					else if($municipio_filtro=="none"
						|| $municipio_filtro=="")
					{
						$array_afiliados_filtrados_localizacion[$campos[1]."_".$campos[2]]=1;
						if($lineas_del_archivo_us_fl==0)
						{
							fwrite($file_us_filtrado, $linea_fixer);
						}
						else
						{
							fwrite($file_us_filtrado, "\n".$linea_fixer);
						}
						$lineas_del_archivo_us_fl++;
					}
				}//fin if si coincide 
			}//fin if campos us correctos
			$nlinea++;
		}//fin while
		fclose($file_us_filtrado);
		fclose($file_us);
	}//fin if si el departamento seleccionado es diferente de none
	
	if(count($array_afiliados_filtrados_localizacion)>0)
	{
		//FILTRO AC
		if(file_exists($ruta_archivo_ac))
		{
			$mensaje_filtro="Filtrando por localizacion el archivo AC...";
			$mensaje_filtro.="  Por Favor Espere. ";
			$html_del_mensaje="";
			$html_del_mensaje.="<table>";
			$html_del_mensaje.="<tr>";
			$html_del_mensaje.="<td colspan=\'2\'>";
			$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
			$html_del_mensaje.="</td>";
			$html_del_mensaje.="</tr>";
			$html_del_mensaje.="<tr>";
			$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
			$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
			$html_del_mensaje.="</td>";
			$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
			$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_filtro."</div><div id=\'errores_bd_div\'></div>";
			$html_del_mensaje.="</td>";
			$html_del_mensaje.="</tr>";
			$html_del_mensaje.="</table>";
			if(connection_aborted()==false)
			{
				echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
				ob_flush();
				flush();
			}
		
			$lineas_del_archivo_ac = count(file($ruta_archivo_ac)); 
			$file_ac = fopen($ruta_archivo_ac, 'r') or exit("No se pudo abrir el archivo");
			$file_ac_filtrado=fopen($ruta_ac_filtrado, 'w') or exit("No se pudo abrir o crear el archivo");
			$nlinea=0;
			while (!feof($file_ac)) 
			{
				$linea_tmp = fgets($file_ac);
				$linea= explode("\n", $linea_tmp)[0];
				$linea = str_replace(array("\n", "\t", "\r"), '', $linea);
				$linea_fixer=preg_replace("/[^A-Za-z0-9:.\-\/\,\_\s]/", "", $linea );
				$campos = explode(",", $linea);
				if(count($campos)==17)
				{
					if(array_key_exists($campos[3]."_".$campos[4],$array_afiliados_filtrados_localizacion))
					{
						if($lineas_del_archivo_ac_fl==0)
						{
							fwrite($file_ac_filtrado, $linea_fixer);
						}
						else
						{
							fwrite($file_ac_filtrado, "\n".$linea_fixer);
						}
						$lineas_del_archivo_ac_fl++;
					}
				}
				$nlinea++;
			}
			fclose($file_ac_filtrado);
			fclose($file_ac);
		}//fin if
		//FIN FILTRO AC
		
		//FILTRO AH
		if(file_exists($ruta_archivo_ah))
		{
			$mensaje_filtro="Filtrando por localizacion el archivo AH...";
			$mensaje_filtro.="  Por Favor Espere. ";
			$html_del_mensaje="";
			$html_del_mensaje.="<table>";
			$html_del_mensaje.="<tr>";
			$html_del_mensaje.="<td colspan=\'2\'>";
			$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
			$html_del_mensaje.="</td>";
			$html_del_mensaje.="</tr>";
			$html_del_mensaje.="<tr>";
			$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
			$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
			$html_del_mensaje.="</td>";
			$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
			$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_filtro."</div><div id=\'errores_bd_div\'></div>";
			$html_del_mensaje.="</td>";
			$html_del_mensaje.="</tr>";
			$html_del_mensaje.="</table>";
			if(connection_aborted()==false)
			{
				echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
				ob_flush();
				flush();
			}
			
			$lineas_del_archivo_ah = count(file($ruta_archivo_ah)); 
			$file_ah = fopen($ruta_archivo_ah, 'r') or exit("No se pudo abrir el archivo");
			$file_ah_filtrado=fopen($ruta_ah_filtrado, 'w') or exit("No se pudo abrir o crear el archivo");		
			$nlinea=0;
			while (!feof($file_ah)) 
			{
				$linea_tmp = fgets($file_ah);
				$linea= explode("\n", $linea_tmp)[0];
				$linea = str_replace(array("\n", "\t", "\r"), '', $linea);
				$linea_fixer=preg_replace("/[^A-Za-z0-9:.\-\/\,\_\s]/", "", $linea );
				$campos = explode(",", $linea);
				if(count($campos)==19)
				{
					if(array_key_exists($campos[3]."_".$campos[4],$array_afiliados_filtrados_localizacion))
					{
						if($lineas_del_archivo_ah_fl==0)
						{
							fwrite($file_ah_filtrado, $linea_fixer);
						}
						else
						{
							fwrite($file_ah_filtrado, "\n".$linea_fixer);
						}
						$lineas_del_archivo_ah_fl++;
					}
				}
				$nlinea++;
			}
			fclose($file_ah_filtrado);
			fclose($file_ah);
		}//fin if
		//FIN FILTRO AH
		
		//FILTRO AP
		if(file_exists($ruta_archivo_ap))
		{
			$mensaje_filtro="Filtrando por localizacion el archivo AP...";
			$mensaje_filtro.="  Por Favor Espere. ";
			$html_del_mensaje="";
			$html_del_mensaje.="<table>";
			$html_del_mensaje.="<tr>";
			$html_del_mensaje.="<td colspan=\'2\'>";
			$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
			$html_del_mensaje.="</td>";
			$html_del_mensaje.="</tr>";
			$html_del_mensaje.="<tr>";
			$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
			$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
			$html_del_mensaje.="</td>";
			$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
			$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_filtro."</div><div id=\'errores_bd_div\'></div>";
			$html_del_mensaje.="</td>";
			$html_del_mensaje.="</tr>";
			$html_del_mensaje.="</table>";
			if(connection_aborted()==false)
			{
				echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
				ob_flush();
				flush();
			}
			
			$lineas_del_archivo_ap = count(file($ruta_archivo_ap)); 
			$file_ap = fopen($ruta_archivo_ap, 'r') or exit("No se pudo abrir el archivo");
			$file_ap_filtrado=fopen($ruta_ap_filtrado, 'w') or exit("No se pudo abrir o crear el archivo");
			$nlinea=0;
			while (!feof($file_ap)) 
			{
				$linea_tmp = fgets($file_ap);
				$linea= explode("\n", $linea_tmp)[0];
				$linea = str_replace(array("\n", "\t", "\r"), '', $linea);
				$linea_fixer=preg_replace("/[^A-Za-z0-9:.\-\/\,\_\s]/", "", $linea );
				$campos = explode(",", $linea);
				if(count($campos)==14)
				{
					if(array_key_exists($campos[3]."_".$campos[4],$array_afiliados_filtrados_localizacion))
					{
						if($lineas_del_archivo_ap_fl==0)
						{
							fwrite($file_ap_filtrado, $linea_fixer);
						}
						else
						{
							fwrite($file_ap_filtrado, "\n".$linea_fixer);
						}
						$lineas_del_archivo_ap_fl++;
					}
				}
				$nlinea++;
			}
			fclose($file_ap_filtrado);
			fclose($file_ap);
		}//fin if
		//FIN FILTRO AP
		
		//FILTRO AU
		if(file_exists($ruta_archivo_au))
		{
			$mensaje_filtro="Filtrando por localizacion el archivo AU...";
			$mensaje_filtro.="  Por Favor Espere. ";
			$html_del_mensaje="";
			$html_del_mensaje.="<table>";
			$html_del_mensaje.="<tr>";
			$html_del_mensaje.="<td colspan=\'2\'>";
			$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
			$html_del_mensaje.="</td>";
			$html_del_mensaje.="</tr>";
			$html_del_mensaje.="<tr>";
			$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
			$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
			$html_del_mensaje.="</td>";
			$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
			$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_filtro."</div><div id=\'errores_bd_div\'></div>";
			$html_del_mensaje.="</td>";
			$html_del_mensaje.="</tr>";
			$html_del_mensaje.="</table>";
			if(connection_aborted()==false)
			{
				echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
				ob_flush();
				flush();
			}
			
			$lineas_del_archivo_au = count(file($ruta_archivo_au)); 
			$file_au = fopen($ruta_archivo_au, 'r') or exit("No se pudo abrir el archivo");
			$file_au_filtrado=fopen($ruta_au_filtrado, 'w') or exit("No se pudo abrir o crear el archivo");
			$nlinea=0;
			while (!feof($file_au)) 
			{
				$linea_tmp = fgets($file_au);
				$linea= explode("\n", $linea_tmp)[0];
				$linea = str_replace(array("\n", "\t", "\r"), '', $linea);
				$linea_fixer=preg_replace("/[^A-Za-z0-9:.\-\/\,\_\s]/", "", $linea );
				$campos = explode(",", $linea);
				if(count($campos)==15)
				{
					if(array_key_exists($campos[3]."_".$campos[4],$array_afiliados_filtrados_localizacion))
					{
						if($lineas_del_archivo_au_fl==0)
						{
							fwrite($file_au_filtrado, $linea_fixer);
						}
						else
						{
							fwrite($file_au_filtrado, "\n".$linea_fixer);
						}
						$lineas_del_archivo_au_fl++;
					}
				}
				$nlinea++;
			}
			fclose($file_au_filtrado);
			fclose($file_au);
		}//fin if
		//FIN FILTRO AU
		
		//FILTRO AN
		if(file_exists($ruta_archivo_an))
		{
			$mensaje_filtro="Filtrando por localizacion el archivo AN...";
			$mensaje_filtro.="  Por Favor Espere. ";
			$html_del_mensaje="";
			$html_del_mensaje.="<table>";
			$html_del_mensaje.="<tr>";
			$html_del_mensaje.="<td colspan=\'2\'>";
			$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
			$html_del_mensaje.="</td>";
			$html_del_mensaje.="</tr>";
			$html_del_mensaje.="<tr>";
			$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
			$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
			$html_del_mensaje.="</td>";
			$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
			$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_filtro."</div><div id=\'errores_bd_div\'></div>";
			$html_del_mensaje.="</td>";
			$html_del_mensaje.="</tr>";
			$html_del_mensaje.="</table>";
			if(connection_aborted()==false)
			{
				echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
				ob_flush();
				flush();
			}
			
			$lineas_del_archivo_an = count(file($ruta_archivo_an)); 
			$file_an = fopen($ruta_archivo_an, 'r') or exit("No se pudo abrir el archivo");
			$file_an_filtrado=fopen($ruta_an_filtrado, 'w') or exit("No se pudo abrir o crear el archivo");
			$nlinea=0;
			while (!feof($file_an)) 
			{
				$linea_tmp = fgets($file_an);
				$linea= explode("\n", $linea_tmp)[0];
				$linea = str_replace(array("\n", "\t", "\r"), '', $linea);
				$linea_fixer=preg_replace("/[^A-Za-z0-9:.\-\/\,\_\s]/", "", $linea );
				$campos = explode(",", $linea);
				if(count($campos)==15)
				{
					if(array_key_exists($campos[3]."_".$campos[4],$array_afiliados_filtrados_localizacion))
					{
						if($lineas_del_archivo_an_fl==0)
						{
							fwrite($file_an_filtrado, $linea_fixer);
						}
						else
						{
							fwrite($file_an_filtrado, "\n".$linea_fixer);
						}
						$lineas_del_archivo_an_fl++;
					}
				}
				$nlinea++;
			}
			fclose($file_an_filtrado);
			fclose($file_an);
		}//fin if
		//FIN FILTRO AN
		
		//FILTRO AM
		if(file_exists($ruta_archivo_am))
		{
			$mensaje_filtro="Filtrando por localizacion el archivo AM...";
			$mensaje_filtro.="  Por Favor Espere. ";
			$html_del_mensaje="";
			$html_del_mensaje.="<table>";
			$html_del_mensaje.="<tr>";
			$html_del_mensaje.="<td colspan=\'2\'>";
			$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
			$html_del_mensaje.="</td>";
			$html_del_mensaje.="</tr>";
			$html_del_mensaje.="<tr>";
			$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
			$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
			$html_del_mensaje.="</td>";
			$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
			$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_filtro."</div><div id=\'errores_bd_div\'></div>";
			$html_del_mensaje.="</td>";
			$html_del_mensaje.="</tr>";
			$html_del_mensaje.="</table>";
			if(connection_aborted()==false)
			{
				echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
				ob_flush();
				flush();
			}
			
			$lineas_del_archivo_am = count(file($ruta_archivo_am)); 
			$file_am = fopen($ruta_archivo_am, 'r') or exit("No se pudo abrir el archivo");
			$file_am_filtrado=fopen($ruta_am_filtrado, 'w') or exit("No se pudo abrir o crear el archivo");
			$nlinea=0;
			while (!feof($file_am)) 
			{
				$linea_tmp = fgets($file_am);
				$linea= explode("\n", $linea_tmp)[0];
				$linea = str_replace(array("\n", "\t", "\r"), '', $linea);
				$linea_fixer=preg_replace("/[^A-Za-z0-9:.\-\/\,\_\s]/", "", $linea );
				$campos = explode(",", $linea);
				if(count($campos)==15)
				{
					if(array_key_exists($campos[3]."_".$campos[4],$array_afiliados_filtrados_localizacion))
					{
						if($lineas_del_archivo_am_fl==0)
						{
							fwrite($file_am_filtrado, $linea_fixer);
						}
						else
						{
							fwrite($file_am_filtrado, "\n".$linea_fixer);
						}
						$lineas_del_archivo_am_fl++;
					}
				}
				$nlinea++;
			}
			fclose($file_am_filtrado);
			fclose($file_am);
		}//fin if
		//FIN FILTRO AM
		
		//FILTRO AV
		if(file_exists($ruta_archivo_av))
		{
			$mensaje_filtro="Filtrando por localizacion el archivo AV...";
			$mensaje_filtro.="  Por Favor Espere. ";
			$html_del_mensaje="";
			$html_del_mensaje.="<table>";
			$html_del_mensaje.="<tr>";
			$html_del_mensaje.="<td colspan=\'2\'>";
			$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
			$html_del_mensaje.="</td>";
			$html_del_mensaje.="</tr>";
			$html_del_mensaje.="<tr>";
			$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
			$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
			$html_del_mensaje.="</td>";
			$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
			$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_filtro."</div><div id=\'errores_bd_div\'></div>";
			$html_del_mensaje.="</td>";
			$html_del_mensaje.="</tr>";
			$html_del_mensaje.="</table>";
			if(connection_aborted()==false)
			{
				echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
				ob_flush();
				flush();
			}
			
			$lineas_del_archivo_av = count(file($ruta_archivo_av)); 
			$file_av = fopen($ruta_archivo_av, 'r') or exit("No se pudo abrir el archivo");
			$file_av_filtrado=fopen($ruta_ac_filtrado, 'w') or exit("No se pudo abrir o crear el archivo");
			$nlinea=0;
			while (!feof($file_av)) 
			{
				$linea_tmp = fgets($file_av);
				$linea= explode("\n", $linea_tmp)[0];
				$linea = str_replace(array("\n", "\t", "\r"), '', $linea);
				$linea_fixer=preg_replace("/[^A-Za-z0-9:.\-\/\,\_\s]/", "", $linea );
				$campos = explode(",", $linea);
				if(count($campos)==16)
				{
					if($lineas_del_archivo_av_fl==0)
					{
						fwrite($file_av_filtrado, $linea_fixer);
					}
					else
					{
						fwrite($file_av_filtrado, "\n".$linea_fixer);
					}
					$lineas_del_archivo_av_fl++;
				}
				$nlinea++;
			}
			fclose($file_av_filtrado);
			fclose($file_av);
		}//fin if
		//FIN FILTRO AV
		
	}//fin if hay usuarios por los cuales filtrar
	
	//construccion del CT de los archivos filtrados por localizacion
	//formato DD/MM/AAAA
	$bool_fecha_del_ct_erronea=false;
	$fecha_remision_para_ct_filtrado=$fecha_remision_array[1]."/".$fecha_remision_array[0]."/".$fecha_remision_array[2];
	$file_ct_viejo=new SplFileObject($ruta_archivo_ct);	
	$file_ct_viejo->seek(0);
	$linea_tmp = $file_ct_viejo->current();
	$linea= str_replace(array("\n","\r"),'', $linea_tmp);
	$campos = explode(",", $linea);
	if(count($campos)==4)
	{
		$array_fecha_ct_viejo=explode("/",$campos[1]);
		if(count($array_fecha_ct_viejo)==3)
		{
			if(checkdate($array_fecha_ct_viejo[1],$array_fecha_ct_viejo[0],$array_fecha_ct_viejo[2]))
			{
				$fecha_remision_para_ct_filtrado=$array_fecha_ct_viejo[0]."/".$array_fecha_ct_viejo[1]."/".$array_fecha_ct_viejo[2];
			}
			else
			{
				$bool_fecha_del_ct_erronea=true;
			}
		}
		else
		{
			$bool_fecha_del_ct_erronea=true;
		}
	}
	else
	{
		$bool_fecha_del_ct_erronea=true;
	}
	if($bool_fecha_del_ct_erronea==true)
	{
		$error_mensaje.="Problema al construir el CT de los archivos filtrados por la localizacion, la fecha registrada en la primera linea de este no es una fecha valida";
	}
	
	$codigo_entidad_reportadora_para_ct_de_filtrados=$cod_prestador;
	
	$file_ct_filtrado=fopen($ruta_ct_filtrado, 'w') or exit("No se pudo abrir o crear el archivo");
	$bool_es_primera_linea_ct_filtrado=true;
	
	$lineas_del_archivo_ct_fl=0;
	
	if($lineas_del_archivo_us_fl>0)
	{
		$linea_tmp=$codigo_entidad_reportadora_para_ct_de_filtrados.",".$fecha_remision_para_ct_filtrado.",US".$numero_de_remision.",".$lineas_del_archivo_us_fl;
		if($bool_es_primera_linea_ct_filtrado==true)
		{
			$bool_es_primera_linea_ct_filtrado=false;
			
			fwrite($file_ct_filtrado, $linea_tmp);
			
			$lineas_del_archivo_ct_fl++;
		}
		else
		{
			fwrite($file_ct_filtrado, "\n".$linea_tmp);
			
			$lineas_del_archivo_ct_fl++;
		}
	}
	
	if($lineas_del_archivo_ac_fl>0)
	{
		$linea_tmp=$codigo_entidad_reportadora_para_ct_de_filtrados.",".$fecha_remision_para_ct_filtrado.",AC".$numero_de_remision.",".$lineas_del_archivo_ac_fl;
		if($bool_es_primera_linea_ct_filtrado==true)
		{
			$bool_es_primera_linea_ct_filtrado=false;
			
			fwrite($file_ct_filtrado, $linea_tmp);
			
			$lineas_del_archivo_ct_fl++;
		}
		else
		{
			fwrite($file_ct_filtrado, "\n".$linea_tmp);
			
			$lineas_del_archivo_ct_fl++;
		}
	}
	
	if($lineas_del_archivo_ah_fl>0)
	{
		$linea_tmp=$codigo_entidad_reportadora_para_ct_de_filtrados.",".$fecha_remision_para_ct_filtrado.",AH".$numero_de_remision.",".$lineas_del_archivo_ah_fl;
		if($bool_es_primera_linea_ct_filtrado==true)
		{
			$bool_es_primera_linea_ct_filtrado=false;
			
			fwrite($file_ct_filtrado, $linea_tmp);
			
			$lineas_del_archivo_ct_fl++;
		}
		else
		{
			fwrite($file_ct_filtrado, "\n".$linea_tmp);
			
			$lineas_del_archivo_ct_fl++;
		}
	}
	
	if($lineas_del_archivo_ap_fl>0)
	{
		$linea_tmp=$codigo_entidad_reportadora_para_ct_de_filtrados.",".$fecha_remision_para_ct_filtrado.",AP".$numero_de_remision.",".$lineas_del_archivo_ap_fl;
		if($bool_es_primera_linea_ct_filtrado==true)
		{
			$bool_es_primera_linea_ct_filtrado=false;
			
			fwrite($file_ct_filtrado, $linea_tmp);
			
			$lineas_del_archivo_ct_fl++;
		}
		else
		{
			fwrite($file_ct_filtrado, "\n".$linea_tmp);
			
			$lineas_del_archivo_ct_fl++;
		}
	}
	
	if($lineas_del_archivo_au_fl>0)
	{
		$linea_tmp=$codigo_entidad_reportadora_para_ct_de_filtrados.",".$fecha_remision_para_ct_filtrado.",AU".$numero_de_remision.",".$lineas_del_archivo_au_fl;
		if($bool_es_primera_linea_ct_filtrado==true)
		{
			$bool_es_primera_linea_ct_filtrado=false;
			
			fwrite($file_ct_filtrado, $linea_tmp);
			
			$lineas_del_archivo_ct_fl++;
		}
		else
		{
			fwrite($file_ct_filtrado, "\n".$linea_tmp);
			
			$lineas_del_archivo_ct_fl++;
		}
	}
	
	if($lineas_del_archivo_an_fl>0)
	{
		$linea_tmp=$codigo_entidad_reportadora_para_ct_de_filtrados.",".$fecha_remision_para_ct_filtrado.",AN".$numero_de_remision.",".$lineas_del_archivo_an_fl;
		if($bool_es_primera_linea_ct_filtrado==true)
		{
			$bool_es_primera_linea_ct_filtrado=false;
			
			fwrite($file_ct_filtrado, $linea_tmp);
			
			$lineas_del_archivo_ct_fl++;
		}
		else
		{
			fwrite($file_ct_filtrado, "\n".$linea_tmp);
			
			$lineas_del_archivo_ct_fl++;
		}
	}
	
	if($lineas_del_archivo_am_fl>0)
	{
		$linea_tmp=$codigo_entidad_reportadora_para_ct_de_filtrados.",".$fecha_remision_para_ct_filtrado.",AM".$numero_de_remision.",".$lineas_del_archivo_am_fl;
		if($bool_es_primera_linea_ct_filtrado==true)
		{
			$bool_es_primera_linea_ct_filtrado=false;
			
			fwrite($file_ct_filtrado, $linea_tmp);
			
			$lineas_del_archivo_ct_fl++;
		}
		else
		{
			fwrite($file_ct_filtrado, "\n".$linea_tmp);
			
			$lineas_del_archivo_ct_fl++;
		}
	}
	
	if($lineas_del_archivo_av_fl>0)
	{
		$linea_tmp=$codigo_entidad_reportadora_para_ct_de_filtrados.",".$fecha_remision_para_ct_filtrado.",AV".$numero_de_remision.",".$lineas_del_archivo_av_fl;
		if($bool_es_primera_linea_ct_filtrado==true)
		{
			$bool_es_primera_linea_ct_filtrado=false;
			
			fwrite($file_ct_filtrado, $linea_tmp);
			
			$lineas_del_archivo_ct_fl++;
		}
		else
		{
			fwrite($file_ct_filtrado, "\n".$linea_tmp);
			
			$lineas_del_archivo_ct_fl++;
		}
	}
	
	fclose($file_ct_filtrado);
		
	//fin construccion del CT de los archivos filtrados por localizacion
	
	unset( $array_afiliados_filtrados_localizacion );
	//FIN SEPARACION GEOGRAFICA
	
	
	//VERIFICA SI EL ARCHIVO YA ESTA SIENDO VALIDADO ACTUALMENTE
	$bool_se_esta_validando_en_este_momento=false;
	
	$query_verificacion_esta_siendo_procesado="";
	$query_verificacion_esta_siendo_procesado.=" SELECT * FROM gioss_rips_eapb_esta_validando_actualmente ";
	$query_verificacion_esta_siendo_procesado.=" WHERE fecha_remision='".$date_remision_bd."' ";
	$query_verificacion_esta_siendo_procesado.=" AND codigo_eapb_reportadora='".$cod_prestador."' ";
	$query_verificacion_esta_siendo_procesado.=" AND nombre_archivo='".$nombre_archivo."'  ";
	$query_verificacion_esta_siendo_procesado.=" AND codigo_departamento='".$departamento_filtro."'  ";
	$query_verificacion_esta_siendo_procesado.=" AND codigo_municipio='".$municipio_filtro."'  ";
	$query_verificacion_esta_siendo_procesado.=" ; ";
	$resultados_query_verificar_esta_siendo_procesado=$coneccionBD->consultar2($query_verificacion_esta_siendo_procesado);
	if(count($resultados_query_verificar_esta_siendo_procesado)>0)
	{
		$bool_se_esta_validando_en_este_momento=true;
	}
	
	//FIN VERIFICA SI EL ARCHIVO YA ESTA SIENDO VALIDADO ACTUALMENTE
		
	//VERIFICA SI FUE CARGADO CON EXITO
	$sql_query_verificar="";
	$sql_query_verificar.=" SELECT * FROM gioss_tabla_estado_informacion_eapb_rips ";
	$sql_query_verificar.=" WHERE fecha_remision='".$date_remision_bd."' ";
	$sql_query_verificar.=" AND codigo_eapb_reportadora='".$cod_prestador."' ";
	$sql_query_verificar.=" AND nombre_archivo='".$nombre_archivo."'  ";
	$sql_query_verificar.=" AND codigo_departamento='".$departamento_filtro."'  ";
	$sql_query_verificar.=" AND codigo_municipio='".$municipio_filtro."'  ";
	$sql_query_verificar.=" AND codigo_estado_informacion='1' ; ";
	$resultados_query_verificar=$coneccionBD->consultar2($sql_query_verificar);
	if(count($resultados_query_verificar)>0)
	{
		$verificacion_ya_se_valido_con_exito=true;
		$hubo_inconsistencias_en_ct=true;
		$fecha_validacion_exito_previa=$resultados_query_verificar[0]["fecha_validacion"];
		$numero_secuencia_previa_si_fue_validado_con_exito=$resultados_query_verificar[0]["numero_secuencia"];
		$error_mensaje.="Se&ntildeor usuario, el archivo que intenta validar ya se encuentra cargado con exito, en la fecha $fecha_validacion_exito_previa .<br>";
	}
	//FIN VERIFICA SI FUE CARGADO CON EXITO
	
	//VALIDACION DE LOS CAMPOS DE LOS ARCHIVOS RIPS
	$array_contador_registros_buenos=array();
	$array_contador_registros_malos=array();
	
	$hubo_inconsistencias_en_ct=false;
	$es_valido_nombre_archivo_ct=true;
	$ruta_archivo_ct="";
	$hubo_inconsistencias_en_us=false;
	$es_valido_nombre_archivo_us=true;
	$ruta_archivo_us="";
	$hubo_inconsistencias_en_ac=false;
	$es_valido_nombre_archivo_ac=true;
	$ruta_archivo_ac="";
	$hubo_inconsistencias_en_ah=false;
	$es_valido_nombre_archivo_ah=true;
	$ruta_archivo_ah="";
	$hubo_inconsistencias_en_ap=false;
	$es_valido_nombre_archivo_ap=true;
	$ruta_archivo_ap="";
	$hubo_inconsistencias_en_ap=false;
	$es_valido_nombre_archivo_ap=true;
	$ruta_archivo_ap="";
	$hubo_inconsistencias_en_au=false;
	$es_valido_nombre_archivo_au=true;
	$ruta_archivo_au="";
	$hubo_inconsistencias_en_an=false;
	$es_valido_nombre_archivo_an=true;
	$ruta_archivo_an="";
	$hubo_inconsistencias_en_am=false;
	$es_valido_nombre_archivo_am=true;
	$ruta_archivo_am="";
	$hubo_inconsistencias_en_av=false;
	$es_valido_nombre_archivo_av=true;
	$ruta_archivo_av="";
	
	if($lineas_del_archivo_ct_fl>0)
	{
		
		$ruta_archivo_ct = $ruta_ct_filtrado;
		$ruta_archivo_us = $ruta_us_filtrado;
		$ruta_archivo_ac = $ruta_ac_filtrado;
		$ruta_archivo_ah = $ruta_ah_filtrado;
		$ruta_archivo_ap = $ruta_ap_filtrado;
		$ruta_archivo_au = $ruta_au_filtrado;
		$ruta_archivo_an = $ruta_an_filtrado;
		$ruta_archivo_am = $ruta_am_filtrado;
		$ruta_archivo_av = $ruta_av_filtrado;
		
		$ruta_nueva="rips".$nick_user.$string_date_ruta."/"."localizacion";
	}
	
	$array_rutas_rips=array();
	$array_rutas_rips[]=$ruta_archivo_ct;
	$array_rutas_rips[]=$ruta_archivo_us;
	if(isset($_POST["AC".$numero_de_remision."_valido"]))
	{
		$array_rutas_rips[]=$ruta_archivo_ac;
	}
	if(isset($_POST["AH".$numero_de_remision."_valido"]))
	{
		$array_rutas_rips[]=$ruta_archivo_ah;
	}
	
	if(isset($_POST["AU".$numero_de_remision."_valido"]))
	{
		$array_rutas_rips[]=$ruta_archivo_au;
	}
	if(isset($_POST["AP".$numero_de_remision."_valido"]))
	{
		$array_rutas_rips[]=$ruta_archivo_ap;
	}
	if(isset($_POST["AN".$numero_de_remision."_valido"]))
	{
		$array_rutas_rips[]=$ruta_archivo_an;
	}
	if(isset($_POST["AM".$numero_de_remision."_valido"]))
	{
		$array_rutas_rips[]=$ruta_archivo_am;
	}	
	if(isset($_POST["AV".$numero_de_remision."_valido"]))
	{
		$array_rutas_rips[]=$ruta_archivo_av;
	}
	
	$error_mostrar_bd="";
	
	//OBTIENE EL NUMERO DE SECUENCIA INCREMENTAL Y LO ASIGNA EN LA TABLA NUMERO SECUENCIA RIPS
	if($verificacion_ya_se_valido_con_exito==false && $bool_fecha_del_ct_erronea==false && $bool_se_esta_validando_en_este_momento==false)
	{
		$numero_secuencia_actual=$utilidades->obtenerSecuencia("gioss_numero_secuencia_rips_3374");
		$sql_query_inserta_seq="";
		$sql_query_inserta_seq.=" INSERT INTO gioss_numero_secuencia_archivos_rips ";
		$sql_query_inserta_seq.=" ( ";
		$sql_query_inserta_seq.=" fecha_de_corte, ";
		$sql_query_inserta_seq.=" codigo_eapb, ";
		$sql_query_inserta_seq.=" codigo_prestador_servicios_salud, ";
		$sql_query_inserta_seq.=" numero_remision, ";
		$sql_query_inserta_seq.=" numero_secuencia ";
		$sql_query_inserta_seq.=" ) ";
		$sql_query_inserta_seq.=" VALUES ";
		$sql_query_inserta_seq.=" ( ";
		$sql_query_inserta_seq.=" '".$date_remision_bd."', ";
		$sql_query_inserta_seq.=" '".$cod_eapb."', ";
		$sql_query_inserta_seq.=" '".$cod_prestador."', ";
		$sql_query_inserta_seq.=" '".$numero_de_remision."', ";
		$sql_query_inserta_seq.=" '".$numero_secuencia_actual."' ";
		$sql_query_inserta_seq.=" ) ";
		$sql_query_inserta_seq.=" ; ";
		$error_bd_seq="";
		$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_query_inserta_seq, $error_bd_seq);
		if($error_bd_seq!="")
		{
			$error_mostrar_bd.=$error_bd_seq."<br>";
		}
	}//fin if si no fue validado con exito
	else
	{
		$numero_secuencia_actual=$numero_secuencia_previa_si_fue_validado_con_exito;		
	}
	//FIN OBTIENE EL NUMERO DE SECUENCIA INCREMENTAL Y LO ASIGNA EN LA TABLA NUMERO SECUENCIA RIPS
	
	$porcentaje_base_salto=0.25;//por ciento
	//INICIO VALIDACION RIPS PROVENIENTES DE EAPB
	if($verificacion_ya_se_valido_con_exito==false && $bool_fecha_del_ct_erronea==false && $bool_se_esta_validando_en_este_momento==false)
	{
		//ARCHIVO CT
		$hubo_inconsistencias_en_ct=false;
		$es_valido_nombre_archivo_ct=true;
		$array_contador_registros_buenos["CT"]=0;
		$array_contador_registros_malos["CT"]=0;
		if(file_exists($ruta_archivo_ct))
		{
									
			// parte donde valida-ct_control
			if($es_valido_nombre_archivo_ct)
			{
				$mensaje_errores_ct="";
				$lineas_del_archivo = count(file($ruta_archivo_ct)); 
				$file_ct = fopen($ruta_archivo_ct, 'r') or exit("No se pudo abrir el archivo");
				
				$nlinea=0;
				while (!feof($file_ct)) 
				{
					$linea_tmp = fgets($file_ct);
					$linea= explode("\n", $linea_tmp)[0];
					$campos = explode(",", $linea);
					
					//verifica la entidad reportadora del documento ct con el asociado
					if(isset($campos[0]))
					{				
						if(str_replace(" ","",$campos[0])!=str_replace(" ","",$cod_prestador) && $verificacion_es_diferente_prestador_en_ct==false)
						{
							$verificacion_es_diferente_prestador_en_ct=true;
							$hubo_inconsistencias_en_ct=true;
							$error_mensaje.="El prestador indicado en el archivo de control (CT) no corresponde al prestador asociado.<br>";
						}
					}//fin if
					
					//varifica si la primera fecha en el archivo ct es igual a la fecha de remision registrada
					if(isset($campos[1]))
					{
						try
						{
							$fecha_en_ct=str_replace(" ","",$campos[1]);
							$array_fecha_en_ct=explode("/",$fecha_en_ct);
							
							$array_fecha_remision=explode("/",$fecha_remision);
							$date_reportada=$array_fecha_en_ct[2]."-".$array_fecha_en_ct[1]."-".$array_fecha_en_ct[0];
							$date_remision=$array_fecha_remision[2]."-".$array_fecha_remision[0]."-".$array_fecha_remision[1];
							$interval = date_diff(date_create($date_reportada),date_create($date_remision));
							$tiempo= (float)($interval->format("%r%a"));
							
							if($tiempo!=0 && $verificacion_fecha_diferente_en_ct==false)
							{
								$verificacion_fecha_diferente_en_ct=true;
								$hubo_inconsistencias_en_ct=true;
								$error_mensaje.="La fecha indicada en el archivo de control (CT) no corresponde a la fecha de remisi&oacuten registrada.<br>";
							}
						}//fin try
						catch(Exception $e)
						{
							$verificacion_fecha_diferente_en_ct=true;
							$hubo_inconsistencias_en_ct=true;
							$error_mensaje.="La fecha indicada en el archivo de control (CT) o registrada no poseen un formato valido.<br>";
						}//fin catch
					}//fin if
					
					//verifica numero de remision
					if(isset($campos[2]))
					{
						$numero_remision_del_ct=substr($campos[2],2,strlen($campos[2]));
						if($numero_de_remision!=$numero_remision_del_ct && $verificacion_numero_remision==false)
						{	
							$verificacion_numero_remision=true;
							$hubo_inconsistencias_en_ct=true;
							$error_mensaje.="El numero de remisi&oacuten indicado en el archivo de control (CT) no corresponde al numero de remisi&oacuten archivo de control ".$numero_remision_del_ct.".<br>";
						}
					}
					
					//pasa a validar los campos
					if(count($campos)==4)
					{
												
						$array_resultados_validacion=validar_eapb_ct($campos,$nlinea,$array_tipo_validacion_rips,$array_grupo_validacion_rips,$array_detalle_validacion_rips,$cod_prestador,"CT".$numero_de_remision,$array_rutas_rips,$fecha_remision,$cod_eapb,$numero_de_remision,$ruta_nueva);
											
						if($hubo_inconsistencias_en_ct==false)
						{
							$hubo_inconsistencias_en_ct=$array_resultados_validacion["error"];
						}
						
						if($array_resultados_validacion["error"]==false)
						{
							$array_contador_registros_buenos["CT"]++;
						}
						if($array_resultados_validacion["error"]==true)
						{
							$array_contador_registros_malos["CT"]++;
						}
						
						//escribe los errores
						$mensaje_errores_ct=$array_resultados_validacion["mensaje"];
						$array_mensajes_errores_campos=explode("|",$mensaje_errores_ct);
						foreach($array_mensajes_errores_campos as $msg_error)
						{
							fwrite($file_inconsistencias_rips, $msg_error."\n");
						}
						//fin escribe los errores
						
						$mensaje_contador_errores="revisando linea ".($nlinea+1)." de $lineas_del_archivo del archivo CT,";
						$mensaje_contador_errores.=" tiene ".$array_contador_registros_buenos["CT"]." registros buenos ";
						$mensaje_contador_errores.=" y ".$array_contador_registros_malos["CT"]." registros malos ";
						$html_del_mensaje="";
						$html_del_mensaje.="<table>";
						$html_del_mensaje.="<tr>";
						$html_del_mensaje.="<td colspan=\'2\'>";
						$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
						$html_del_mensaje.="</td>";
						$html_del_mensaje.="</tr>";
						$html_del_mensaje.="<tr>";
						$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
						$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
						$html_del_mensaje.="</td>";
						$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
						$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_contador_errores."</div><div id=\'errores_bd_div\'></div>";
						$html_del_mensaje.="</td>";
						$html_del_mensaje.="</tr>";
						$html_del_mensaje.="</table>";
						if(connection_aborted()==false)
						{
							echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
							ob_flush();
							flush();
						}
					}//fin if verifica longitud
					else
					{
						//$error_longitud=$array_tipo_validacion_rips["01"].",Numero de campos,el numero de campos en la linea no corresponden al numero permitido,"."CT".$numero_de_remision.",-1,".($nlinea+1);
						
						$error_longitud=$array_tipo_validacion_rips["03"].",".$array_grupo_validacion_rips["0301"].",".$array_detalle_validacion_rips["03_0301_030101"].","."CT".$numero_de_remision.",-1,".($nlinea+1);
						if($hubo_inconsistencias_en_ct==false)
						{
							$hubo_inconsistencias_en_ct=true;
						}
						fwrite($file_inconsistencias_rips, $error_longitud."\n");
					}//fin else longitud no apropiada
					$nlinea++;
				}
				fclose($file_ct);
			}
			//fin parte valida archivo
			
			if($hubo_inconsistencias_en_ct)
			{
				$se_genero_archivo_de_inconsistencias=true;
				if($verificacion_es_diferente_prestador_en_ct==false && $verificacion_fecha_diferente_en_ct==false && $verificacion_numero_remision==false)
				{
					$error_mensaje.="Hubo inconsistencias en el archivo de control (CT).<br>";
				}
			}
		}//fin if ct_control
		else
		{
			$error_mensaje.="El archivo de control (CT) no existe.<br>";
			$hubo_inconsistencias_en_ct=true;
		}
		//FIN ARCHIVO CT
		
		$condicion_inconcistencias_bloqueo_para_continuar=($hubo_inconsistencias_en_ct==false) ;
		
		//subida a tabla registros rechazados si los registros de
		// CT  estan correctos en un 100%, para los demas
		//se sube teniendo en cuenta cada registo por separado solo para rechazados
		$condicion_bloqueo_subida_bd_rechazados=($hubo_inconsistencias_en_ct==false);
		if($condicion_bloqueo_subida_bd_rechazados)
		{
			//BORRA LOS RECHAZADOS ANTERIORES
			$sql_query_delete="";
			$sql_query_delete.="DELETE FROM gioss_registros_validados_rechazados_rips_ct_eapb ";
			$sql_query_delete.=" WHERE fecha_remision='".$date_remision_bd."' ";
			$sql_query_delete.=" AND codigo_entidad_eapb='".$cod_entidad_reportadora_eapb_fact."' ";
			$sql_query_delete.=" AND dpto_mpio='".$departamento_filtro.$mpio_filtro_bd."' ";
			$sql_query_delete.=" AND nombre_archivo='".$nombre_archivo."' ; ";
			$error_bd_del="";
			$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_query_delete, $error_bd_del);
			if($error_bd_del!="")
			{
				if(connection_aborted()==false)
				{
					echo "<script>alert('error ELIMINAR RECHAZADOS CT eapb rips ".procesar_mensaje($error_bd_del)."  ".procesar_mensaje($sql_query_delete)."');</script>";
				}
			}
			else
			{
				
			}
			
			
			$sql_query_delete="";
			$sql_query_delete.="DELETE FROM gioss_registros_validados_rechazados_rips_us_eapb ";
			$sql_query_delete.=" WHERE fecha_remision='".$date_remision_bd."' ";
			$sql_query_delete.=" AND codigo_entidad_eapb='".$cod_entidad_reportadora_eapb_fact."' ";
			$sql_query_delete.=" AND dpto_mpio='".$departamento_filtro.$mpio_filtro_bd."' ";
			$sql_query_delete.=" AND nombre_archivo='".$nombre_archivo."' ; ";
			$error_bd_del="";
			$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_query_delete, $error_bd_del);
			
			$sql_query_delete="";
			$sql_query_delete.="DELETE FROM gioss_registros_validados_rechazados_rips_ac_eapb ";
			$sql_query_delete.=" WHERE fecha_remision='".$date_remision_bd."' ";
			$sql_query_delete.=" AND codigo_entidad_eapb='".$cod_entidad_reportadora_eapb_fact."' ";
			$sql_query_delete.=" AND dpto_mpio='".$departamento_filtro.$mpio_filtro_bd."' ";
			$sql_query_delete.=" AND nombre_archivo='".$nombre_archivo."' ; ";
			$error_bd_del="";
			$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_query_delete, $error_bd_del);
			
			$sql_query_delete="";
			$sql_query_delete.="DELETE FROM gioss_registros_validados_rechazados_rips_ah_eapb ";
			$sql_query_delete.=" WHERE fecha_remision='".$date_remision_bd."' ";
			$sql_query_delete.=" AND codigo_entidad_eapb='".$cod_entidad_reportadora_eapb_fact."' ";
			$sql_query_delete.=" AND dpto_mpio='".$departamento_filtro.$mpio_filtro_bd."' ";
			$sql_query_delete.=" AND nombre_archivo='".$nombre_archivo."' ; ";
			$error_bd_del="";
			$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_query_delete, $error_bd_del);
			
			
			$sql_query_delete="";
			$sql_query_delete.="DELETE FROM gioss_registros_validados_rechazados_rips_ap_eapb ";
			$sql_query_delete.=" WHERE fecha_remision='".$date_remision_bd."' ";
			$sql_query_delete.=" AND codigo_entidad_eapb='".$cod_entidad_reportadora_eapb_fact."' ";
			$sql_query_delete.=" AND dpto_mpio='".$departamento_filtro.$mpio_filtro_bd."' ";
			$sql_query_delete.=" AND nombre_archivo='".$nombre_archivo."' ; ";
			$error_bd_del="";
			$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_query_delete, $error_bd_del);
			
			$sql_query_delete="";
			$sql_query_delete.="DELETE FROM gioss_registros_validados_rechazados_rips_au_eapb ";
			$sql_query_delete.=" WHERE fecha_remision='".$date_remision_bd."' ";
			$sql_query_delete.=" AND codigo_entidad_eapb='".$cod_entidad_reportadora_eapb_fact."' ";
			$sql_query_delete.=" AND dpto_mpio='".$departamento_filtro.$mpio_filtro_bd."' ";
			$sql_query_delete.=" AND nombre_archivo='".$nombre_archivo."' ; ";
			$error_bd_del="";
			$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_query_delete, $error_bd_del);
			
			$sql_query_delete="";
			$sql_query_delete.="DELETE FROM gioss_registros_validados_rechazados_rips_an_eapb ";
			$sql_query_delete.=" WHERE fecha_remision='".$date_remision_bd."' ";
			$sql_query_delete.=" AND codigo_entidad_eapb='".$cod_entidad_reportadora_eapb_fact."' ";
			$sql_query_delete.=" AND dpto_mpio='".$departamento_filtro.$mpio_filtro_bd."' ";
			$sql_query_delete.=" AND nombre_archivo='".$nombre_archivo."' ; ";
			$error_bd_del="";
			$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_query_delete, $error_bd_del);
			
			$sql_query_delete="";
			$sql_query_delete.="DELETE FROM gioss_registros_validados_rechazados_rips_am_eapb ";
			$sql_query_delete.=" WHERE fecha_remision='".$date_remision_bd."' ";
			$sql_query_delete.=" AND codigo_entidad_eapb='".$cod_entidad_reportadora_eapb_fact."' ";
			$sql_query_delete.=" AND dpto_mpio='".$departamento_filtro.$mpio_filtro_bd."' ";
			$sql_query_delete.=" AND nombre_archivo='".$nombre_archivo."' ; ";
			$error_bd_del="";
			$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_query_delete, $error_bd_del);
			
			
			$sql_query_delete="";
			$sql_query_delete.="DELETE FROM gioss_registros_validados_rechazados_rips_av_eapb ";
			$sql_query_delete.=" WHERE fecha_remision='".$date_remision_bd."' ";
			$sql_query_delete.=" AND codigo_entidad_eapb='".$cod_entidad_reportadora_eapb_fact."' ";
			$sql_query_delete.=" AND dpto_mpio='".$departamento_filtro.$mpio_filtro_bd."' ";
			$sql_query_delete.=" AND nombre_archivo='".$nombre_archivo."' ; ";
			$error_bd_del="";
			$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_query_delete, $error_bd_del);
			
			//FIN BORRA LOS RECHAZADOS ANTERIORES
			
			
			$query_insert_esta_siendo_procesado="";
			$query_insert_esta_siendo_procesado.=" INSERT INTO gioss_rips_eapb_esta_validando_actualmente ";
			$query_insert_esta_siendo_procesado.=" ( ";
			$query_insert_esta_siendo_procesado.=" codigo_eapb_reportadora,";
			$query_insert_esta_siendo_procesado.=" nombre_archivo,";
			$query_insert_esta_siendo_procesado.=" codigo_departamento,";
			$query_insert_esta_siendo_procesado.=" codigo_municipio,";
			$query_insert_esta_siendo_procesado.=" fecha_remision,";
			$query_insert_esta_siendo_procesado.=" fecha_validacion,";
			$query_insert_esta_siendo_procesado.=" hora_validacion,";
			$query_insert_esta_siendo_procesado.=" nick_usuario,";
			$query_insert_esta_siendo_procesado.=" archivos_que_ha_validado_hasta_el_momento";
			$query_insert_esta_siendo_procesado.=" ) ";
			$query_insert_esta_siendo_procesado.=" VALUES ";
			$query_insert_esta_siendo_procesado.=" ( ";
			$query_insert_esta_siendo_procesado.=" '".$cod_prestador."',  ";
			$query_insert_esta_siendo_procesado.=" '".$nombre_archivo."',  ";
			$query_insert_esta_siendo_procesado.=" '".$departamento_filtro."',  ";
			$query_insert_esta_siendo_procesado.=" '".$municipio_filtro."',  ";
			$query_insert_esta_siendo_procesado.=" '".$date_remision_bd."',  ";
			$query_insert_esta_siendo_procesado.=" '".$fecha_actual."',  ";
			$query_insert_esta_siendo_procesado.=" '".$tiempo_actual."',  ";
			$query_insert_esta_siendo_procesado.=" '".$nick_user."',  ";
			$query_insert_esta_siendo_procesado.=" 'CT".$numero_de_remision."'  ";
			$query_insert_esta_siendo_procesado.=" ) ";
			$query_insert_esta_siendo_procesado.=" ; ";
			$error_bd="";
			$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($query_insert_esta_siendo_procesado, $error_bd);
			if($error_bd!="")
			{
				$error_mostrar_bd.="ERROR AL establecer esta siendo procesado  en  CT, ".$error_bd."<br>";
				if(connection_aborted()==false)
				{
					echo "<script>alert('error al establecer esta siendo procesado  CT eapb rips ".procesar_mensaje($error_bd)."  ".procesar_mensaje($query_insert_esta_siendo_procesado)."');</script>";
				}
			}
			
			
			$bool_hubo_error_query=false;
			
			
			//CT
			if($es_valido_nombre_archivo_ct  && $bool_hubo_error_query==false && (file_exists($ruta_archivo_ct)==true && $rutaTemporal!=trim($ruta_archivo_ct)))
			{
				//llave primaria fecha_de_rechazo,codigo_eapb,codigo_prestador_servicios_salud,fila,numero_remision
				
						
				
				
				$lineas_del_archivo = count(file($ruta_archivo_ct)); 
				$file_ct = fopen($ruta_archivo_ct, 'r') or exit("No se pudo abrir el archivo");
				
				$nlinea=0;
				while (!feof($file_ct) && $bool_hubo_error_query==false) 
				{
					
					
					$linea_tmp = fgets($file_ct);
					$linea= explode("\n", $linea_tmp)[0];
					$campos = explode(",", $linea);
					
					//verifica el prestador del documento ct con el asociado
					if(count($campos)==4)
					{
											
						$array_fecha_reportada=explode("/",$campos[1]);
						$date_reportada_bd=$array_fecha_reportada[2]."-".$array_fecha_reportada[1]."-".$array_fecha_reportada[0];
						
						//se pone igual a 1 debido a que el ct debe estar correcto 
						$estado_validado_registro=1;
						
						$sql_rechazados="";
						$sql_rechazados.="INSERT INTO gioss_registros_validados_rechazados_rips_ct_eapb ";
						$sql_rechazados.=" ( ";
						$sql_rechazados.="fecha_validacion ,";
						$sql_rechazados.="nombre_archivo ,";
						$sql_rechazados.="codigo_entidad_eapb ,";
						$sql_rechazados.="fecha_remision ,";						
						$sql_rechazados.="codigo_archivo ,";
						$sql_rechazados.="total_registros  ,";
						$sql_rechazados.="fila_integer ,";
						$sql_rechazados.="numero_remision, ";
						$sql_rechazados.="numero_secuencia, ";
						$sql_rechazados.="hora_validacion, ";
						$sql_rechazados.="periodo_corte, ";
						$sql_rechazados.="estado_registro, ";
						$sql_rechazados.="dpto_mpio ";
						$sql_rechazados.=" ) ";
						$sql_rechazados.=" VALUES ";
						$sql_rechazados.=" ( ";
						$sql_rechazados.=" '".$fecha_actual."', ";
						$sql_rechazados.=" '".$nombre_archivo."', ";
						$sql_rechazados.=" '".procesar_mensaje2($campos[0])."', ";
						$sql_rechazados.=" '".procesar_mensaje2($campos[1])."', ";
						$sql_rechazados.=" '".procesar_mensaje2($campos[2])."', ";
						$sql_rechazados.=" '".procesar_mensaje2($campos[3])."', ";
						$sql_rechazados.=" '".$nlinea."', ";
						$sql_rechazados.=" '".$numero_de_remision."', ";
						$sql_rechazados.=" '".$numero_secuencia_actual."', ";
						$sql_rechazados.=" '".$tiempo_actual."', ";
						$sql_rechazados.=" '".$periodo_rips_archivo."', ";
						$sql_rechazados.=" '".$estado_validado_registro."', ";
						$sql_rechazados.=" '".$departamento_filtro.$mpio_filtro_bd."' ";
						$sql_rechazados.=" ) ";
						$sql_rechazados.=" ; ";
						
						$error_bd="";
						$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_rechazados, $error_bd);
						if($error_bd!="")
						{
							$error_mostrar_bd.="ERROR AL SUBIR RECHAZADOS EN CT, ".$error_bd."<br>";
							if(connection_aborted()==false)
							{
								echo "<script>alert('error CT eapb rips ".procesar_mensaje($error_bd)."  ".procesar_mensaje($sql_rechazados)."');</script>";
							}
						}
					}//fin if
					$nlinea++;
				}//fin while
				fclose($file_ct);
			}//fin if si el archivo ct existe
			//FIN CT
						
			
			
		}//fin if
						
		//ARCHIVO US
		$array_afiliados_duplicados=array();
		$hubo_inconsistencias_en_us=false;
		$es_valido_nombre_archivo_us=true;
		$array_contador_registros_buenos["US"]=0;
		$array_contador_registros_malos["US"]=0;
		if(file_exists($ruta_archivo_us) && $condicion_inconcistencias_bloqueo_para_continuar)
		{
			// parte donde valida-us
			if($es_valido_nombre_archivo_us)
			{
				$mensaje_errores_us="";
				$lineas_del_archivo = count(file($ruta_archivo_us)); 
				$file_us = fopen($ruta_archivo_us, 'r') or exit("No se pudo abrir el archivo");
				
				$por_ciento_del_total_lineas=intval(($lineas_del_archivo*$porcentaje_base_salto)/100);
				$acumulaciones_linea_por_porcentaje=$por_ciento_del_total_lineas;
				
				$nlinea=0;
				while (!feof($file_us)) 
				{
					$linea_tmp = fgets($file_us);
					$linea= explode("\n", $linea_tmp)[0];
					$campos = explode(",", $linea);
					if(count($campos)==12)
					{	
						$array_resultados_validacion=validar_eapb_us($campos,$nlinea,$array_tipo_validacion_rips,$array_grupo_validacion_rips,$array_detalle_validacion_rips,$cod_prestador,"US".$numero_de_remision,$cod_eapb,$numero_de_remision,$fecha_remision,$array_rutas_rips,$ruta_nueva,$array_afiliados_duplicados);
						
						if($hubo_inconsistencias_en_us==false)
						{
							$hubo_inconsistencias_en_us=$array_resultados_validacion["error"];
						}
						
						$estado_validado_registro=0;
						if($array_resultados_validacion["error"]==false)
						{
							$array_contador_registros_buenos["US"]++;
							$estado_validado_registro=1;
						}
						if($array_resultados_validacion["error"]==true)
						{
							$array_contador_registros_malos["US"]++;
							$estado_validado_registro=2;
						}
						
						if($condicion_bloqueo_subida_bd_rechazados)
						{					
							$sql_rechazados="";
							$sql_rechazados.="INSERT INTO gioss_registros_validados_rechazados_rips_us_eapb ";
							$sql_rechazados.=" ( ";							
							$sql_rechazados.="fecha_remision ,";
							$sql_rechazados.="nombre_archivo ,";
							$sql_rechazados.="codigo_entidad_eapb ,";												
							$sql_rechazados.="tipo_identificacion_usuario ,";
							$sql_rechazados.="numero_identificacion_usuario ,";
							$sql_rechazados.="codigo_tipo_usuario ,";
							$sql_rechazados.="codigo_tipo_afiliado ,";
							$sql_rechazados.="codigo_ocupacion ,";
							$sql_rechazados.="edad ,";
							$sql_rechazados.="unidad_medida_edad ,";
							$sql_rechazados.="sexo ,";
							$sql_rechazados.="codigo_departamento_residencia ,";
							$sql_rechazados.="codigo_municipio_residencia ,";
							$sql_rechazados.="codigo_zona_residencia ,";
							$sql_rechazados.="fecha_validacion ,";
							$sql_rechazados.="hora_validacion ,";
							$sql_rechazados.="fila_integer ,";
							$sql_rechazados.="numero_remision, ";
							$sql_rechazados.="numero_secuencia, ";
							$sql_rechazados.="periodo_corte, ";
							$sql_rechazados.="estado_registro, ";
							$sql_rechazados.="dpto_mpio ";
							$sql_rechazados.=" ) ";
							$sql_rechazados.=" VALUES ";
							$sql_rechazados.=" ( ";
							$sql_rechazados.=" '".$date_remision_bd."', ";			
							$sql_rechazados.=" '".$nombre_archivo."', ";							
							$sql_rechazados.=" '".procesar_mensaje2($campos[0])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[1])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[2])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[3])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[4])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[5])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[6])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[7])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[8])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[9])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[10])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[11])."', ";
							$sql_rechazados.=" '".$fecha_actual."', ";
							$sql_rechazados.=" '".$tiempo_actual."', ";
							$sql_rechazados.=" '".$nlinea."', ";
							$sql_rechazados.=" '".$numero_de_remision."', ";
							$sql_rechazados.=" '".$numero_secuencia_actual."', ";
							$sql_rechazados.=" '".$periodo_rips_archivo."', ";
							$sql_rechazados.=" '".$estado_validado_registro."', ";
							$sql_rechazados.=" '".$departamento_filtro.$mpio_filtro_bd."' ";
							$sql_rechazados.=" ) ";
							$sql_rechazados.=" ; ";
							
							$error_bd="";
							$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_rechazados, $error_bd);
							if($error_bd!="")
							{
								$error_mostrar_bd.="ERROR AL SUBIR RECHAZADOS EN US, ".$error_bd."<br>";
							}
						}//fin
						
						//escribe los errores
						$mensaje_errores_us=$array_resultados_validacion["mensaje"];
						$array_mensajes_errores_campos=explode("|",$mensaje_errores_us);
						foreach($array_mensajes_errores_campos as $msg_error)
						{
							fwrite($file_inconsistencias_rips, $msg_error."\n");
						}
						//fin escribe los errores
						
						if(($nlinea+1)>=$acumulaciones_linea_por_porcentaje)
						{						
							$mensaje_contador_errores="revisando linea ".($nlinea+1)." de $lineas_del_archivo del archivo US,";
							$mensaje_contador_errores.=" tiene ".$array_contador_registros_buenos["US"]." registros buenos ";
							$mensaje_contador_errores.=" y ".$array_contador_registros_malos["US"]." registros malos ";
							$html_del_mensaje="";
							$html_del_mensaje.="<table>";
							$html_del_mensaje.="<tr>";
							$html_del_mensaje.="<td colspan=\'2\'>";
							$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
							$html_del_mensaje.="</td>";
							$html_del_mensaje.="</tr>";
							$html_del_mensaje.="<tr>";
							$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
							$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
							$html_del_mensaje.="</td>";
							$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
							$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_contador_errores."</div><div id=\'errores_bd_div\'></div>";
							$html_del_mensaje.="</td>";
							$html_del_mensaje.="</tr>";
							$html_del_mensaje.="</table>";
							if(connection_aborted()==false)
							{
								echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
								ob_flush();
								flush();
							}
							
							
							$query_update_esta_siendo_procesado="";
							$query_update_esta_siendo_procesado.=" UPDATE gioss_rips_eapb_esta_validando_actualmente ";
							$query_update_esta_siendo_procesado.=" SET archivos_que_ha_validado_hasta_el_momento='".$mensaje_contador_errores."' ";
							$query_update_esta_siendo_procesado.=" WHERE fecha_remision='".$date_remision_bd."' ";
							$query_update_esta_siendo_procesado.=" AND codigo_eapb_reportadora='".$cod_prestador."' ";
							$query_update_esta_siendo_procesado.=" AND nombre_archivo='".$nombre_archivo."'  ";
							$query_update_esta_siendo_procesado.=" AND codigo_departamento='".$departamento_filtro."'  ";
							$query_update_esta_siendo_procesado.=" AND codigo_municipio='".$municipio_filtro."'  ";
							$query_update_esta_siendo_procesado.=" ; ";
							$error_bd="";
							$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($query_update_esta_siendo_procesado, $error_bd);
							
							
							$acumulaciones_linea_por_porcentaje=$acumulaciones_linea_por_porcentaje+$por_ciento_del_total_lineas;
						}
					}//fin verifica numero campos
					else
					{
						//$error_longitud=$array_tipo_validacion_rips["01"].",Numero de campos,el numero de campos en la linea no corresponden al numero permitido,"."US".$numero_de_remision.",-1,".($nlinea+1);
						
						$error_longitud=$array_tipo_validacion_rips["03"].",".$array_grupo_validacion_rips["0301"].",".$array_detalle_validacion_rips["03_0301_030101"].","."US".$numero_de_remision.",-1,".($nlinea+1);
						if($hubo_inconsistencias_en_us==false)
						{
							$hubo_inconsistencias_en_us=true;
						}
						fwrite($file_inconsistencias_rips, $error_longitud."\n");
					}//fin else longitud no apropiada
					$nlinea++;
				}
				fclose($file_us);
			}
			//fin parte valida archivo
			
			if($hubo_inconsistencias_en_us)
			{
				$se_genero_archivo_de_inconsistencias=true;
				$error_mensaje.="Hubo inconsistencias en el archivo de usuarios (US).<br>";
			}
		}
		else if($hubo_inconsistencias_en_ct==false)
		{
			$error_mensaje.="El archivo de usuarios (US) no existe.<br>";
			$hubo_inconsistencias_en_us=true;
		}
		//FIN ARCHIVO US
		
		$numeros_de_factura_por_ti_nit_ips=array();
		
		//ARCHIVO AC
		$hubo_inconsistencias_en_ac=false;
		$es_valido_nombre_archivo_ac=true;
		$array_contador_registros_buenos["AC"]=0;
		$array_contador_registros_malos["AC"]=0;
		if(file_exists($ruta_archivo_ac) && $condicion_inconcistencias_bloqueo_para_continuar)
		{
			// parte donde valida-ac
			if($es_valido_nombre_archivo_ac)
			{
				$mensaje_errores_ac="";
				$lineas_del_archivo = count(file($ruta_archivo_ac)); 
				$file_ac = fopen($ruta_archivo_ac, 'r') or exit("No se pudo abrir el archivo");
				
				
				
				$por_ciento_del_total_lineas=intval(($lineas_del_archivo*$porcentaje_base_salto)/100);
				$acumulaciones_linea_por_porcentaje=$por_ciento_del_total_lineas;
				
				$nlinea=0;
				while (!feof($file_ac)) 
				{
					$linea_tmp = fgets($file_ac);
					$linea= explode("\n", $linea_tmp)[0];
					$campos = explode(",", $linea);
					if(count($campos)==17)
					{							
						$array_resultados_validacion=validar_eapb_ac($campos,$nlinea,$array_tipo_validacion_rips,$array_grupo_validacion_rips,$array_detalle_validacion_rips,$cod_prestador,"AC".$numero_de_remision,$cod_eapb,$numero_de_remision,$fecha_remision,$array_rutas_rips,$ruta_nueva,$array_afiliados_duplicados,$numeros_de_factura_por_ti_nit_ips,$nombre_archivo,$departamento_filtro,$mpio_filtro_bd);
						if($hubo_inconsistencias_en_ac==false)
						{
							$hubo_inconsistencias_en_ac=$array_resultados_validacion["error"];
						}
						
						$estado_validado_registro=0;
						if($array_resultados_validacion["error"]==false)
						{
							$array_contador_registros_buenos["AC"]++;
							$estado_validado_registro=1;
						}
						else
						{
							$array_contador_registros_malos["AC"]++;
							$estado_validado_registro=2;
						}
						
						//SE SUBE A BD EL REGISTRO CON ERRORES APARTE
						if($condicion_bloqueo_subida_bd_rechazados)
						{							
							
							$sql_rechazados="";
							$sql_rechazados.="INSERT INTO gioss_registros_validados_rechazados_rips_ac_eapb ";
							$sql_rechazados.=" ( ";							
							$sql_rechazados.="nombre_archivo ,";							
							$sql_rechazados.="codigo_entidad_eapb ,";
							$sql_rechazados.="codigo_prestador_servcios_salud ,";
							$sql_rechazados.="numero_factura ,";
							$sql_rechazados.="tipo_identificacion_usuario ,";
							$sql_rechazados.="numero_identificacion_usuario ,";;
							$sql_rechazados.="fecha_atencion ,";
							$sql_rechazados.="codigo_cups_consulta ,";
							$sql_rechazados.="finalidad_consulta ,";
							$sql_rechazados.="causa_externa_consulta ,";
							$sql_rechazados.="codigo_diagnostico_principal ,";
							$sql_rechazados.="codigo_relacionado_1 ,";
							$sql_rechazados.="codigo_relacionado_2 ,";
							$sql_rechazados.="codigo_relacionado_3 ,";
							$sql_rechazados.="tipo_diagnostico_principal ,";
							$sql_rechazados.="valor_consulta ,";
							$sql_rechazados.="valor_cuota_moderadora ,";
							$sql_rechazados.="valor_neto_pagado ,";							
							$sql_rechazados.="fecha_validacion ,";
							$sql_rechazados.="hora_validacion ,";
							$sql_rechazados.="fecha_remision ,";
							$sql_rechazados.="fila_integer ,";
							$sql_rechazados.="numero_remision, ";
							$sql_rechazados.="numero_secuencia, ";
							$sql_rechazados.="periodo_corte, ";
							$sql_rechazados.="estado_registro, ";
							$sql_rechazados.="dpto_mpio ";
							$sql_rechazados.=" ) ";
							$sql_rechazados.=" VALUES ";
							$sql_rechazados.=" ( ";
							$sql_rechazados.=" '".$nombre_archivo."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[0])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[1])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[2])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[3])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[4])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[5])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[6])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[7])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[8])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[9])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[10])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[11])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[12])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[13])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[14])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[15])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[16])."', ";
							$sql_rechazados.=" '".$fecha_actual."', ";
							$sql_rechazados.=" '".$tiempo_actual."', ";
							$sql_rechazados.=" '".$date_remision_bd."', ";
							$sql_rechazados.=" '".$nlinea."', ";
							$sql_rechazados.=" '".$numero_de_remision."', ";
							$sql_rechazados.=" '".$numero_secuencia_actual."', ";
							$sql_rechazados.=" '".$periodo_rips_archivo."', ";
							$sql_rechazados.=" '".$estado_validado_registro."', ";
							$sql_rechazados.=" '".$departamento_filtro.$mpio_filtro_bd."' ";
							$sql_rechazados.=" ) ";
							$sql_rechazados.=" ; ";
							
							$error_bd="";
							$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_rechazados, $error_bd);
							if($error_bd!="")
							{
								$error_mostrar_bd.="ERROR AL SUBIR RECHAZADOS EN AC, ".$error_bd."<br>";
							}
							
						}//fin
						//FIN SE SUBE A BD EL REGISTRO CON ERRORES APARTE
						
						//escribe los errores
						$mensaje_errores_ac=$array_resultados_validacion["mensaje"];
						$array_mensajes_errores_campos=explode("|",$mensaje_errores_ac);
						foreach($array_mensajes_errores_campos as $msg_error)
						{
							fwrite($file_inconsistencias_rips, $msg_error."\n");
						}
						//fin escribe los errores
						
						if(($nlinea+1)>=$acumulaciones_linea_por_porcentaje)
						{
							$mensaje_contador_errores="revisando linea ".($nlinea+1)." de $lineas_del_archivo del archivo AC,";
							$mensaje_contador_errores.=" tiene ".$array_contador_registros_buenos["AC"]." registros buenos ";
							$mensaje_contador_errores.=" y ".$array_contador_registros_malos["AC"]." registros malos ";
							$html_del_mensaje="";
							$html_del_mensaje.="<table>";
							$html_del_mensaje.="<tr>";
							$html_del_mensaje.="<td colspan=\'2\'>";
							$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
							$html_del_mensaje.="</td>";
							$html_del_mensaje.="</tr>";
							$html_del_mensaje.="<tr>";
							$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
							$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
							$html_del_mensaje.="</td>";
							$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
							$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_contador_errores."</div><div id=\'errores_bd_div\'></div>";
							$html_del_mensaje.="</td>";
							$html_del_mensaje.="</tr>";
							$html_del_mensaje.="</table>";
							if(connection_aborted()==false)
							{
								echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
								ob_flush();
								flush();
							}
							
							$query_update_esta_siendo_procesado="";
							$query_update_esta_siendo_procesado.=" UPDATE gioss_rips_eapb_esta_validando_actualmente ";
							$query_update_esta_siendo_procesado.=" SET archivos_que_ha_validado_hasta_el_momento='".$mensaje_contador_errores."' ";
							$query_update_esta_siendo_procesado.=" WHERE fecha_remision='".$date_remision_bd."' ";
							$query_update_esta_siendo_procesado.=" AND codigo_eapb_reportadora='".$cod_prestador."' ";
							$query_update_esta_siendo_procesado.=" AND nombre_archivo='".$nombre_archivo."'  ";
							$query_update_esta_siendo_procesado.=" AND codigo_departamento='".$departamento_filtro."'  ";
							$query_update_esta_siendo_procesado.=" AND codigo_municipio='".$municipio_filtro."'  ";
							$query_update_esta_siendo_procesado.=" ; ";
							$error_bd="";
							$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($query_update_esta_siendo_procesado, $error_bd);
							
							$acumulaciones_linea_por_porcentaje=$acumulaciones_linea_por_porcentaje+$por_ciento_del_total_lineas;
						}
						
					}//fin verifica numero campos
					else
					{
						//$error_longitud=$array_tipo_validacion_rips["01"].",Numero de campos,el numero de campos en la linea no corresponden al numero permitido,"."AC".$numero_de_remision.",-1,".($nlinea+1);
						
						$error_longitud=$array_tipo_validacion_rips["03"].",".$array_grupo_validacion_rips["0301"].",".$array_detalle_validacion_rips["03_0301_030101"].","."AC".$numero_de_remision.",-1,".($nlinea+1);
						if($hubo_inconsistencias_en_ac==false)
						{
							$hubo_inconsistencias_en_ac=true;
						}
						fwrite($file_inconsistencias_rips, $error_longitud."\n");
					}//fin else longitud no apropiada
					$nlinea++;
				}
				fclose($file_ac);
			}
			//fin parte valida archivo
			
			if($hubo_inconsistencias_en_ac)
			{
				
				$se_genero_archivo_de_inconsistencias=true;
				$error_mensaje.="Hubo inconsistencias en el archivo de consulta (AC).<br>";
			}
		}
		else if(isset($_POST["AC".$numero_de_remision."_valido"]) && $condicion_inconcistencias_bloqueo_para_continuar)
		{
			$error_mensaje.="El archivo de consulta (AC) no existe.<br>";
			$hubo_inconsistencias_en_af=true;
		}
		//FIN ARCHIVO AC
		
		//ARCHIVO AH 
		$hubo_inconsistencias_en_ah=false;
		$es_valido_nombre_archivo_ah=true;
		$array_contador_registros_buenos["AH"]=0;
		$array_contador_registros_malos["AH"]=0;
		if(file_exists($ruta_archivo_ah) && $condicion_inconcistencias_bloqueo_para_continuar)
		{			
			// parte donde valida-ah
			if($es_valido_nombre_archivo_ah)
			{
				$mensaje_errores_ah="";
				$lineas_del_archivo = count(file($ruta_archivo_ah)); 
				$file_ah = fopen($ruta_archivo_ah, 'r') or exit("No se pudo abrir el archivo");
				
				$por_ciento_del_total_lineas=intval(($lineas_del_archivo*$porcentaje_base_salto)/100);
				$acumulaciones_linea_por_porcentaje=$por_ciento_del_total_lineas;
				
				$nlinea=0;
				while (!feof($file_ah)) 
				{
					$linea_tmp = fgets($file_ah);
					$linea= explode("\n", $linea_tmp)[0];
					$campos = explode(",", $linea);
					if(count($campos)==19)
					{
												
						$array_resultados_validacion=validar_eapb_ah($campos,$nlinea,$array_tipo_validacion_rips,$array_grupo_validacion_rips,$array_detalle_validacion_rips,$cod_prestador,"AH".$numero_de_remision,$cod_eapb,$numero_de_remision,$fecha_remision,$array_rutas_rips,$ruta_nueva,$array_afiliados_duplicados,$numeros_de_factura_por_ti_nit_ips,$nombre_archivo,$departamento_filtro,$mpio_filtro_bd);
						if($hubo_inconsistencias_en_ah==false)
						{
							$hubo_inconsistencias_en_ah=$array_resultados_validacion["error"];
						}
						
						
						$estado_validado_registro=0;
						if($array_resultados_validacion["error"]==false)
						{
							$array_contador_registros_buenos["AH"]++;
							$estado_validado_registro=1;
						}
						else
						{
							$array_contador_registros_malos["AH"]++;
							$estado_validado_registro=2;
						}
						
						//SE SUBE A BD EL REGISTRO CON ERRORES APARTE
						if($condicion_bloqueo_subida_bd_rechazados)
						{							
							
							$sql_rechazados="";
							$sql_rechazados.="INSERT INTO gioss_registros_validados_rechazados_rips_ah_eapb ";
							$sql_rechazados.=" ( ";
							$sql_rechazados.="nombre_archivo ,";//1
							$sql_rechazados.="codigo_entidad_eapb ,";//2 //0
							$sql_rechazados.="codigo_prestador_servicios_salud ,";//3//1
							$sql_rechazados.="numero_factura ,";		//4	//2		
							$sql_rechazados.="tipo_identificacion_usuario ,";//5 //3
							$sql_rechazados.="numero_identificacion_usuario ,";//6 //4
							$sql_rechazados.="via_ingreso_institucion ,";//7 //5
							$sql_rechazados.="fecha_ingreso ,";//8 //6
							$sql_rechazados.="hora_ingreso ,";//9 //7
							$sql_rechazados.="causa_externa ,";//10 //8
							$sql_rechazados.="codigo_diagnostico_principal_ingreso ,";//11 //9
							$sql_rechazados.="codigo_diagnostico_principal_egreso ,";//12 //10
							$sql_rechazados.="codigo_relacionado_egreso_1 ,";//13 //11
							$sql_rechazados.="codigo_relacionado_egreso_2 ,";//14 //12
							$sql_rechazados.="codigo_relacionado_egreso_3 ,";//15 //13
							$sql_rechazados.="codigo_diagnostico_complicacion ,";//16 //14
							$sql_rechazados.="estado_a_salida ,";//17 //15
							$sql_rechazados.="codigo_diagnostico_muerte ,";//18 //16
							$sql_rechazados.="fecha_egreso ,";//19 //17
							$sql_rechazados.="hora_egreso ,";//20 //18
							$sql_rechazados.="fecha_validacion ,";//21
							$sql_rechazados.="hora_validacion ,";//22							
							$sql_rechazados.="fecha_remision ,";//23
							$sql_rechazados.="fila_integer, ";//24
							$sql_rechazados.="numero_remision, ";//25
							$sql_rechazados.="numero_secuencia, ";//26
							$sql_rechazados.="periodo_corte, ";//27
							$sql_rechazados.="estado_registro, ";
							$sql_rechazados.="dpto_mpio ";
							$sql_rechazados.=" ) ";
							$sql_rechazados.=" VALUES ";
							$sql_rechazados.=" ( ";							
							$sql_rechazados.=" '".$nombre_archivo."', ";	//1
							$sql_rechazados.=" '".procesar_mensaje2($campos[0])."', ";//2 0
							$sql_rechazados.=" '".procesar_mensaje2($campos[1])."', ";//3 1
							$sql_rechazados.=" '".procesar_mensaje2($campos[2])."', ";//4 2
							$sql_rechazados.=" '".procesar_mensaje2($campos[3])."', ";//5 3
							$sql_rechazados.=" '".procesar_mensaje2($campos[4])."', ";//6 4
							$sql_rechazados.=" '".procesar_mensaje2($campos[5])."', ";//7 5
							$sql_rechazados.=" '".procesar_mensaje2($campos[6])."', ";//7 6
							$sql_rechazados.=" '".procesar_mensaje2($campos[7])."', ";//8 7
							$sql_rechazados.=" '".procesar_mensaje2($campos[8])."', ";//9 8
							$sql_rechazados.=" '".procesar_mensaje2($campos[9])."', ";//10 9
							$sql_rechazados.=" '".procesar_mensaje2($campos[10])."', ";//11 10
							$sql_rechazados.=" '".procesar_mensaje2($campos[11])."', ";//12 11
							$sql_rechazados.=" '".procesar_mensaje2($campos[12])."', ";//13 12
							$sql_rechazados.=" '".procesar_mensaje2($campos[13])."', ";//14 13
							$sql_rechazados.=" '".procesar_mensaje2($campos[14])."', ";//15 14
							$sql_rechazados.=" '".procesar_mensaje2($campos[15])."', ";//16 15 
							$sql_rechazados.=" '".procesar_mensaje2($campos[16])."', ";//17 16
							$sql_rechazados.=" '".procesar_mensaje2($campos[17])."', ";//18 17
							$sql_rechazados.=" '".procesar_mensaje2($campos[18])."', ";//19 18
							$sql_rechazados.=" '".$fecha_actual."', ";//20
							$sql_rechazados.=" '".$tiempo_actual."', ";//21
							$sql_rechazados.=" '".$date_remision_bd."', ";//22
							$sql_rechazados.=" '".$nlinea."', ";//23
							$sql_rechazados.=" '".$numero_de_remision."', ";//24
							$sql_rechazados.=" '".$numero_secuencia_actual."', ";//25
							$sql_rechazados.=" '".$periodo_rips_archivo."', ";//26
							$sql_rechazados.=" '".$estado_validado_registro."', ";
							$sql_rechazados.=" '".$departamento_filtro.$mpio_filtro_bd."' ";
							$sql_rechazados.=" ) ";
							$sql_rechazados.=" ; ";
							
							$error_bd="";
							$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_rechazados, $error_bd);
							if($error_bd!="")
							{
								$error_mostrar_bd.="ERROR AL SUBIR RECHAZADOS EN AH, ".$error_bd."<br>";
							}
							
						}//fin
						//FIN SE SUBE A BD EL REGISTRO CON ERRORES APARTE
						
						
						//escribe los errores
						$mensaje_errores_ah=$array_resultados_validacion["mensaje"];
						$array_mensajes_errores_campos=explode("|",$mensaje_errores_ah);
						foreach($array_mensajes_errores_campos as $msg_error)
						{
							fwrite($file_inconsistencias_rips, $msg_error."\n");
						}
						//fin escribe los errores
						
						if(($nlinea+1)>=$acumulaciones_linea_por_porcentaje)
						{
							$mensaje_contador_errores="revisando linea ".($nlinea+1)." de $lineas_del_archivo del archivo AH,";
							$mensaje_contador_errores.=" tiene ".$array_contador_registros_buenos["AH"]." registros buenos ";
							$mensaje_contador_errores.=" y ".$array_contador_registros_malos["AH"]." registros malos ";
							$html_del_mensaje="";
							$html_del_mensaje.="<table>";
							$html_del_mensaje.="<tr>";
							$html_del_mensaje.="<td colspan=\'2\'>";
							$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
							$html_del_mensaje.="</td>";
							$html_del_mensaje.="</tr>";
							$html_del_mensaje.="<tr>";
							$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
							$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
							$html_del_mensaje.="</td>";
							$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
							$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_contador_errores."</div><div id=\'errores_bd_div\'></div>";
							$html_del_mensaje.="</td>";
							$html_del_mensaje.="</tr>";
							$html_del_mensaje.="</table>";
							if(connection_aborted()==false)
							{
								echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
								ob_flush();
								flush();
							}
							
							$query_update_esta_siendo_procesado="";
							$query_update_esta_siendo_procesado.=" UPDATE gioss_rips_eapb_esta_validando_actualmente ";
							$query_update_esta_siendo_procesado.=" SET archivos_que_ha_validado_hasta_el_momento='".$mensaje_contador_errores."' ";
							$query_update_esta_siendo_procesado.=" WHERE fecha_remision='".$date_remision_bd."' ";
							$query_update_esta_siendo_procesado.=" AND codigo_eapb_reportadora='".$cod_prestador."' ";
							$query_update_esta_siendo_procesado.=" AND nombre_archivo='".$nombre_archivo."'  ";
							$query_update_esta_siendo_procesado.=" AND codigo_departamento='".$departamento_filtro."'  ";
							$query_update_esta_siendo_procesado.=" AND codigo_municipio='".$municipio_filtro."'  ";
							$query_update_esta_siendo_procesado.=" ; ";
							$error_bd="";
							$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($query_update_esta_siendo_procesado, $error_bd);
							
							$acumulaciones_linea_por_porcentaje=$acumulaciones_linea_por_porcentaje+$por_ciento_del_total_lineas;
						}
						
					}//fin verifica numero campos
					else
					{
						//$error_longitud=$array_tipo_validacion_rips["01"].",Numero de campos,el numero de campos en la linea no corresponden al numero permitido,"."AH".$numero_de_remision.",-1,".($nlinea+1);
						
						$error_longitud=$array_tipo_validacion_rips["03"].",".$array_grupo_validacion_rips["0301"].",".$array_detalle_validacion_rips["03_0301_030101"].","."AH".$numero_de_remision.",-1,".($nlinea+1);
						if($hubo_inconsistencias_en_ah==false)
						{
							$hubo_inconsistencias_en_ah=true;
						}
						fwrite($file_inconsistencias_rips, $error_longitud."\n");
					}//fin else longitud no apropiada
					$nlinea++;
				}
				fclose($file_ah);
			}
			//fin parte valida archivo
			
			if($hubo_inconsistencias_en_ah)
			{
				$se_genero_archivo_de_inconsistencias=true;
				$error_mensaje.="Hubo inconsistencias en el archivo de hospitalizaci&oacuten (AH).<br>";
			}
			
		}
		else if(isset($_POST["AH".$numero_de_remision."_valido"]) && $condicion_inconcistencias_bloqueo_para_continuar)
		{
			$error_mensaje.="El archivo de hospitalizaci&oacuten (AH) no existe.<br>";
			$hubo_inconsistencias_en_ah=true;
		}
		//FIN ARCHIVO AH
		
		
		
		//ARCHIVO AP
		$hubo_inconsistencias_en_ap=false;
		$es_valido_nombre_archivo_ap=true;
		$array_contador_registros_buenos["AP"]=0;
		$array_contador_registros_malos["AP"]=0;
		if(file_exists($ruta_archivo_ap) && $condicion_inconcistencias_bloqueo_para_continuar)
		{						
			// parte donde valida-ap
			if($es_valido_nombre_archivo_ap)
			{
				$mensaje_errores_ap="";
				$lineas_del_archivo = count(file($ruta_archivo_ap)); 
				$file_ap = fopen($ruta_archivo_ap, 'r') or exit("No se pudo abrir el archivo");
				
				$por_ciento_del_total_lineas=intval(($lineas_del_archivo*$porcentaje_base_salto)/100);
				$acumulaciones_linea_por_porcentaje=$por_ciento_del_total_lineas;
				
				$nlinea=0;
				while (!feof($file_ap)) 
				{
					$linea_tmp = fgets($file_ap);
					$linea= explode("\n", $linea_tmp)[0];
					$campos = explode(",", $linea);
					if(count($campos)==14)
					{						
						
						$array_resultados_validacion=validar_eapb_ap($campos,$nlinea,$array_tipo_validacion_rips,$array_grupo_validacion_rips,$array_detalle_validacion_rips,$cod_prestador,"AP".$numero_de_remision,$cod_eapb,$numero_de_remision,$fecha_remision,$array_rutas_rips,$ruta_nueva,$array_afiliados_duplicados,$numeros_de_factura_por_ti_nit_ips,$nombre_archivo,$departamento_filtro,$mpio_filtro_bd);
						if($hubo_inconsistencias_en_ap==false)
						{
							$hubo_inconsistencias_en_ap=$array_resultados_validacion["error"];
						}
						
						$estado_validado_registro=0;
						if($array_resultados_validacion["error"]==false)
						{
							$array_contador_registros_buenos["AP"]++;
							$estado_validado_registro=1;
						}
						else
						{
							$array_contador_registros_malos["AP"]++;
							$estado_validado_registro=2;
						}
						
						//SE SUBE A BD EL REGISTRO CON ERRORES APARTE
						if($condicion_bloqueo_subida_bd_rechazados)
						{							
							
							$sql_rechazados="";
							$sql_rechazados.="INSERT INTO gioss_registros_validados_rechazados_rips_ap_eapb ";
							$sql_rechazados.=" ( ";
							$sql_rechazados.="nombre_archivo ,";
							$sql_rechazados.="codigo_entidad_eapb ,";
							$sql_rechazados.="codigo_prestador_servicios_salud ,";
							$sql_rechazados.="numero_factura ,";					
							$sql_rechazados.="tipo_identificacion_usuario ,";
							$sql_rechazados.="numero_identificacion_usuario ,";
							$sql_rechazados.="fecha_procedimiento ,";
							$sql_rechazados.="codigo_cups_procedimiento ,";
							$sql_rechazados.="ambito_realizacion_procedimiento ,";
							$sql_rechazados.="finalidad_procedimiento ,";
							$sql_rechazados.="personal_que_atiende ,";
							$sql_rechazados.="diagnostico_principal ,";
							$sql_rechazados.="diagnostico_relacionado ,";
							$sql_rechazados.="diagnostico_complicacion ,";
							$sql_rechazados.="valor_procedimiento ,";
							$sql_rechazados.="fecha_validacion ,";
							$sql_rechazados.="hora_validacion ,";
							$sql_rechazados.="fecha_remision ,";
							$sql_rechazados.="fila_integer  ,";
							$sql_rechazados.="numero_remision, ";
							$sql_rechazados.="numero_secuencia, ";
							$sql_rechazados.="periodo_corte, ";
							$sql_rechazados.="estado_registro, ";
							$sql_rechazados.="dpto_mpio ";
							$sql_rechazados.=" ) ";
							$sql_rechazados.=" VALUES ";
							$sql_rechazados.=" ( ";							
							$sql_rechazados.=" '".$nombre_archivo."', ";	
							$sql_rechazados.=" '".procesar_mensaje2($campos[0])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[1])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[2])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[3])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[4])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[5])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[6])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[7])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[8])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[9])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[10])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[11])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[12])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[13])."', ";
							$sql_rechazados.=" '".$fecha_actual."', ";
							$sql_rechazados.=" '".$tiempo_actual."', ";
							$sql_rechazados.=" '".$date_remision_bd."', ";
							$sql_rechazados.=" '".$nlinea."', ";
							$sql_rechazados.=" '".$numero_de_remision."', ";
							$sql_rechazados.=" '".$numero_secuencia_actual."', ";
							$sql_rechazados.=" '".$periodo_rips_archivo."', ";
							$sql_rechazados.=" '".$estado_validado_registro."', ";
							$sql_rechazados.=" '".$departamento_filtro.$mpio_filtro_bd."' ";
							$sql_rechazados.=" ) ";
							$sql_rechazados.=" ; ";
							
							$error_bd="";
							$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_rechazados, $error_bd);
							if($error_bd!="")
							{
								$error_mostrar_bd.="ERROR AL SUBIR RECHAZADOS EN AP, ".$error_bd."<br>";
							}
						}//fin
						//FIN SE SUBE A BD EL REGISTRO CON ERRORES APARTE
						
						//escribe los errores
						$mensaje_errores_ap=$array_resultados_validacion["mensaje"];
						$array_mensajes_errores_campos=explode("|",$mensaje_errores_ap);
						foreach($array_mensajes_errores_campos as $msg_error)
						{
							fwrite($file_inconsistencias_rips, $msg_error."\n");
						}
						//fin escribe los errores
						
						if(($nlinea+1)>=$acumulaciones_linea_por_porcentaje)
						{
							$mensaje_contador_errores="revisando linea ".($nlinea+1)." de $lineas_del_archivo del archivo AP,";
							$mensaje_contador_errores.=" tiene ".$array_contador_registros_buenos["AP"]." registros buenos ";
							$mensaje_contador_errores.=" y ".$array_contador_registros_malos["AP"]." registros malos ";
							$html_del_mensaje="";
							$html_del_mensaje.="<table>";
							$html_del_mensaje.="<tr>";
							$html_del_mensaje.="<td colspan=\'2\'>";
							$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
							$html_del_mensaje.="</td>";
							$html_del_mensaje.="</tr>";
							$html_del_mensaje.="<tr>";
							$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
							$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
							$html_del_mensaje.="</td>";
							$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
							$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_contador_errores."</div><div id=\'errores_bd_div\'></div>";
							$html_del_mensaje.="</td>";
							$html_del_mensaje.="</tr>";
							$html_del_mensaje.="</table>";
							if(connection_aborted()==false)
							{
								echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
								ob_flush();
								flush();
							}
							
							$query_update_esta_siendo_procesado="";
							$query_update_esta_siendo_procesado.=" UPDATE gioss_rips_eapb_esta_validando_actualmente ";
							$query_update_esta_siendo_procesado.=" SET archivos_que_ha_validado_hasta_el_momento='".$mensaje_contador_errores."' ";
							$query_update_esta_siendo_procesado.=" WHERE fecha_remision='".$date_remision_bd."' ";
							$query_update_esta_siendo_procesado.=" AND codigo_eapb_reportadora='".$cod_prestador."' ";
							$query_update_esta_siendo_procesado.=" AND nombre_archivo='".$nombre_archivo."'  ";
							$query_update_esta_siendo_procesado.=" AND codigo_departamento='".$departamento_filtro."'  ";
							$query_update_esta_siendo_procesado.=" AND codigo_municipio='".$municipio_filtro."'  ";
							$query_update_esta_siendo_procesado.=" ; ";
							$error_bd="";
							$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($query_update_esta_siendo_procesado, $error_bd);
							
							$acumulaciones_linea_por_porcentaje=$acumulaciones_linea_por_porcentaje+$por_ciento_del_total_lineas;
						}
						
					}//fin verifica numero campos
					else
					{
						//$error_longitud=$array_tipo_validacion_rips["01"].",Numero de campos,el numero de campos en la linea no corresponden al numero permitido,"."AP".$numero_de_remision.",-1,".($nlinea+1);
						
						$error_longitud=$array_tipo_validacion_rips["03"].",".$array_grupo_validacion_rips["0301"].",".$array_detalle_validacion_rips["03_0301_030101"].","."AP".$numero_de_remision.",-1,".($nlinea+1);
						if($hubo_inconsistencias_en_ap==false)
						{
							$hubo_inconsistencias_en_ap=true;
						}
						fwrite($file_inconsistencias_rips, $error_longitud."\n");
					}//fin else longitud no apropiada
					$nlinea++;
				}
				fclose($file_ap);
			}
			//fin parte valida archivo
			
			if($hubo_inconsistencias_en_ap)
			{
				$se_genero_archivo_de_inconsistencias=true;
				$error_mensaje.="Hubo inconsistencias en el archivo de procedimientos (AP).<br>";
			}
		}
		else if(isset($_POST["AP".$numero_de_remision."_valido"]) && $condicion_inconcistencias_bloqueo_para_continuar)
		{
			$error_mensaje.="El archivo de procedimientos (AP) no existe.<br>";
			$hubo_inconsistencias_en_ah=true;
		}
		//FIN ARCHIVO AP
		
		//ARCHIVO AU
		$hubo_inconsistencias_en_au=false;
		$es_valido_nombre_archivo_au=true;
		$array_contador_registros_buenos["AU"]=0;
		$array_contador_registros_malos["AU"]=0;
		if(file_exists($ruta_archivo_au) && $condicion_inconcistencias_bloqueo_para_continuar)
		{			
			
			// parte donde valida-au
			if($es_valido_nombre_archivo_au)
			{				
				$mensaje_errores_au="";
				$lineas_del_archivo = count(file($ruta_archivo_au)); 
				$file_au = fopen($ruta_archivo_au, 'r') or exit("No se pudo abrir el archivo");
				
				$por_ciento_del_total_lineas=intval(($lineas_del_archivo*$porcentaje_base_salto)/100);
				$acumulaciones_linea_por_porcentaje=$por_ciento_del_total_lineas;
				
				$nlinea=0;
				while (!feof($file_au)) 
				{
					$linea_tmp = fgets($file_au);
					$linea= explode("\n", $linea_tmp)[0];
					$campos = explode(",", $linea);
					if(count($campos)==15)
					{
						
						
						$array_resultados_validacion=validar_eapb_au($campos,$nlinea,$array_tipo_validacion_rips,$array_grupo_validacion_rips,$array_detalle_validacion_rips,$cod_prestador,"AU".$numero_de_remision,$cod_eapb,$numero_de_remision,$fecha_remision,$array_rutas_rips,$ruta_nueva,$array_afiliados_duplicados,$numeros_de_factura_por_ti_nit_ips,$nombre_archivo,$departamento_filtro,$mpio_filtro_bd);
						if($hubo_inconsistencias_en_au==false)
						{
							$hubo_inconsistencias_en_au=$array_resultados_validacion["error"];
						}
						
						$estado_validado_registro=0;
						if($array_resultados_validacion["error"]==false)
						{
							$array_contador_registros_buenos["AU"]++;
							$estado_validado_registro=1;
						}
						else
						{
							$array_contador_registros_malos["AU"]++;
							$estado_validado_registro=2;
						}
						
						//SE SUBE A BD EL REGISTRO CON ERRORES APARTE
						if($condicion_bloqueo_subida_bd_rechazados)
						{
							
							
							$sql_rechazados="";
							$sql_rechazados.="INSERT INTO gioss_registros_validados_rechazados_rips_au_eapb ";
							$sql_rechazados.=" ( ";
							$sql_rechazados.="nombre_archivo ,";
							//ini campos archivo
							$sql_rechazados.="codigo_entidad_eapb ,";
							$sql_rechazados.="codigo_prestador_servcios_salud ,";
							$sql_rechazados.="numero_factura ,";					
							$sql_rechazados.="tipo_identificacion_usuario ,";
							$sql_rechazados.="numero_identificacion_usuario ,";
							$sql_rechazados.="fecha_ingreso ,";
							$sql_rechazados.="causa_externa ,";
							$sql_rechazados.="codigo_diagnostico_de_salida ,";
							$sql_rechazados.="codigo_diagnostico_relacionado_1 ,";
							$sql_rechazados.="codigo_diagnostico_relacionado_2 ,";
							$sql_rechazados.="codigo_diagnostico_relacionado_3 ,";
							$sql_rechazados.="destino_usuario_salida ,";
							$sql_rechazados.="estado_salida_usuario ,";
							$sql_rechazados.="diagnostico_causa_muerte ,";
							$sql_rechazados.="fecha_salida ,";
							//fin campos archivo
							$sql_rechazados.="fecha_validacion ,";
							$sql_rechazados.="hora_validacion ,";
							$sql_rechazados.="fecha_remision ,";
							$sql_rechazados.="fila_integer  ,";
							$sql_rechazados.="numero_remision, ";
							$sql_rechazados.="numero_secuencia, ";
							$sql_rechazados.="periodo_corte, ";
							$sql_rechazados.="estado_registro, ";
							$sql_rechazados.="dpto_mpio ";
							$sql_rechazados.=" ) ";
							$sql_rechazados.=" VALUES ";
							$sql_rechazados.=" ( ";							
							$sql_rechazados.=" '".$nombre_archivo."', ";	
							$sql_rechazados.=" '".procesar_mensaje2($campos[0])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[1])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[2])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[3])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[4])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[5])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[6])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[7])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[8])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[9])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[10])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[11])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[12])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[13])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[14])."', ";
							$sql_rechazados.=" '".$fecha_actual."', ";
							$sql_rechazados.=" '".$tiempo_actual."', ";
							$sql_rechazados.=" '".$date_remision_bd."', ";
							$sql_rechazados.=" '".$nlinea."', ";
							$sql_rechazados.=" '".$numero_de_remision."', ";
							$sql_rechazados.=" '".$numero_secuencia_actual."', ";
							$sql_rechazados.=" '".$periodo_rips_archivo."', ";
							$sql_rechazados.=" '".$estado_validado_registro."', ";
							$sql_rechazados.=" '".$departamento_filtro.$mpio_filtro_bd."' ";
							$sql_rechazados.=" ) ";
							$sql_rechazados.=" ; ";
							
							$error_bd="";
							$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_rechazados, $error_bd);
							if($error_bd!="")
							{
								$error_mostrar_bd.="ERROR AL SUBIR RECHAZADOS EN AU, ".$error_bd."<br>";
							}
						}//fin
						//FIN SE SUBE A BD EL REGISTRO CON ERRORES APARTE
						
						//escribe los errores
						$mensaje_errores_au=$array_resultados_validacion["mensaje"];
						$array_mensajes_errores_campos=explode("|",$mensaje_errores_au);
						foreach($array_mensajes_errores_campos as $msg_error)
						{
							fwrite($file_inconsistencias_rips, $msg_error."\n");
						}
						//fin escribe los errores
						
						if(($nlinea+1)>=$acumulaciones_linea_por_porcentaje)
						{
							$mensaje_contador_errores="revisando linea ".($nlinea+1)." de $lineas_del_archivo del archivo AU,";
							$mensaje_contador_errores.=" tiene ".$array_contador_registros_buenos["AU"]." registros buenos ";
							$mensaje_contador_errores.=" y ".$array_contador_registros_malos["AU"]." registros malos ";
							$html_del_mensaje="";
							$html_del_mensaje.="<table>";
							$html_del_mensaje.="<tr>";
							$html_del_mensaje.="<td colspan=\'2\'>";
							$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
							$html_del_mensaje.="</td>";
							$html_del_mensaje.="</tr>";
							$html_del_mensaje.="<tr>";
							$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
							$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
							$html_del_mensaje.="</td>";
							$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
							$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_contador_errores."</div><div id=\'errores_bd_div\'></div>";
							$html_del_mensaje.="</td>";
							$html_del_mensaje.="</tr>";
							$html_del_mensaje.="</table>";
							if(connection_aborted()==false)
							{
								echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
								ob_flush();
								flush();
							}
							
							$query_update_esta_siendo_procesado="";
							$query_update_esta_siendo_procesado.=" UPDATE gioss_rips_eapb_esta_validando_actualmente ";
							$query_update_esta_siendo_procesado.=" SET archivos_que_ha_validado_hasta_el_momento='".$mensaje_contador_errores."' ";
							$query_update_esta_siendo_procesado.=" WHERE fecha_remision='".$date_remision_bd."' ";
							$query_update_esta_siendo_procesado.=" AND codigo_eapb_reportadora='".$cod_prestador."' ";
							$query_update_esta_siendo_procesado.=" AND nombre_archivo='".$nombre_archivo."'  ";
							$query_update_esta_siendo_procesado.=" AND codigo_departamento='".$departamento_filtro."'  ";
							$query_update_esta_siendo_procesado.=" AND codigo_municipio='".$municipio_filtro."'  ";
							$query_update_esta_siendo_procesado.=" ; ";
							$error_bd="";
							$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($query_update_esta_siendo_procesado, $error_bd);
							
							$acumulaciones_linea_por_porcentaje=$acumulaciones_linea_por_porcentaje+$por_ciento_del_total_lineas;
						}
					}//fin verifica numero campos
					else
					{
						//$error_longitud=$array_tipo_validacion_rips["01"].",Numero de campos,el numero de campos en la linea no corresponden al numero permitido,"."AU".$numero_de_remision.",-1,".($nlinea+1);
						
						$error_longitud=$array_tipo_validacion_rips["03"].",".$array_grupo_validacion_rips["0301"].",".$array_detalle_validacion_rips["03_0301_030101"].","."AU".$numero_de_remision.",-1,".($nlinea+1);
						if($hubo_inconsistencias_en_au==false)
						{
							$hubo_inconsistencias_en_au=true;
						}
						fwrite($file_inconsistencias_rips, $error_longitud."\n");
					}//fin else longitud no apropiada
					$nlinea++;
				}
				fclose($file_au);
			}
			//fin parte valida archivo
			
			if($hubo_inconsistencias_en_au)
			{
				$se_genero_archivo_de_inconsistencias=true;
				$error_mensaje.="Hubo inconsistencias en el archivo de urgencias (AU).<br>";
			}
		}
		else if(isset($_POST["AU".$numero_de_remision."_valido"]) && $condicion_inconcistencias_bloqueo_para_continuar)
		{
			$error_mensaje.="El archivo de archivo de urgencias (AU) no existe.<br>";
			$hubo_inconsistencias_en_ah=true;
		}
		//FIN ARCHIVO AU 
		
		
		//ARCHIVO AN
		$hubo_inconsistencias_en_an=false;
		$es_valido_nombre_archivo_an=true;
		$array_contador_registros_buenos["AN"]=0;
		$array_contador_registros_malos["AN"]=0;
		if(file_exists($ruta_archivo_an) && $condicion_inconcistencias_bloqueo_para_continuar)
		{			
			// parte donde valida-an
			if($es_valido_nombre_archivo_an)
			{
				$mensaje_errores_an="";
				$lineas_del_archivo = count(file($ruta_archivo_an)); 
				$file_an = fopen($ruta_archivo_an, 'r') or exit("No se pudo abrir el archivo");
				
				$por_ciento_del_total_lineas=intval(($lineas_del_archivo*$porcentaje_base_salto)/100);
				$acumulaciones_linea_por_porcentaje=$por_ciento_del_total_lineas;
				
				$nlinea=0;
				while (!feof($file_an)) 
				{
					$linea_tmp = fgets($file_an);
					$linea= explode("\n", $linea_tmp)[0];
					$campos = explode(",", $linea);
					if(count($campos)==15)
					{
												
						$array_resultados_validacion=validar_eapb_an($campos,$nlinea,$array_tipo_validacion_rips,$array_grupo_validacion_rips,$array_detalle_validacion_rips,$cod_prestador,"AN".$numero_de_remision,$cod_eapb,$numero_de_remision,$fecha_remision,$array_rutas_rips,$ruta_nueva,$array_afiliados_duplicados,$numeros_de_factura_por_ti_nit_ips,$nombre_archivo,$departamento_filtro,$mpio_filtro_bd);
						if($hubo_inconsistencias_en_an==false)
						{
							$hubo_inconsistencias_en_an=$array_resultados_validacion["error"];
						}
						
						
						$estado_validado_registro=0;
						if($array_resultados_validacion["error"]==false)
						{
							$array_contador_registros_buenos["AN"]++;
							$estado_validado_registro=1;
						}
						else
						{
							$array_contador_registros_malos["AN"]++;
							$estado_validado_registro=2;
						}
						
						//SE SUBE A BD EL REGISTRO CON ERRORES APARTE
						if($condicion_bloqueo_subida_bd_rechazados)
						{
							$sql_rechazados="";
							$sql_rechazados.="INSERT INTO gioss_registros_validados_rechazados_rips_an_eapb ";
							$sql_rechazados.=" ( ";
							$sql_rechazados.="nombre_archivo ,";
							//ini campos archivo
							$sql_rechazados.="codigo_entidad_eapb ,";
							$sql_rechazados.="codigo_prestador_servicios_salud ,";
							$sql_rechazados.="numero_factura ,";					
							$sql_rechazados.="tipo_identificacion_madre ,";
							$sql_rechazados.="numero_identificacion_madre ,";
							$sql_rechazados.="fecha_ingreso ,";
							$sql_rechazados.="hora_ingreso ,";
							$sql_rechazados.="edad_gestacional ,";
							$sql_rechazados.="control_prenatal ,";
							$sql_rechazados.="sexo ,";
							$sql_rechazados.="peso  ,";
							$sql_rechazados.="codigo_diagnostico_recien_nacido ,";
							$sql_rechazados.="codigo_diagnostico_causa_muerte ,";
							$sql_rechazados.="fecha_muerte_recien_nacido ,";
							$sql_rechazados.="hora_muerte_recien_nacido ,";
							//fin campos archivo
							$sql_rechazados.="fecha_validacion ,";
							$sql_rechazados.="hora_validacion ,";
							$sql_rechazados.="fecha_remision ,";
							$sql_rechazados.="fila_integer  ,";
							$sql_rechazados.="numero_remision, ";
							$sql_rechazados.="numero_secuencia, ";
							$sql_rechazados.="periodo_corte, ";
							$sql_rechazados.="estado_registro, ";
							$sql_rechazados.="dpto_mpio ";
							$sql_rechazados.=" ) ";
							$sql_rechazados.=" VALUES ";
							$sql_rechazados.=" ( ";							
							$sql_rechazados.=" '".$nombre_archivo."', ";	
							$sql_rechazados.=" '".procesar_mensaje2($campos[0])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[1])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[2])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[3])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[4])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[5])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[6])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[7])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[8])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[9])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[10])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[11])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[12])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[13])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[14])."', ";
							$sql_rechazados.=" '".$fecha_actual."', ";
							$sql_rechazados.=" '".$tiempo_actual."', ";
							$sql_rechazados.=" '".$date_remision_bd."', ";
							$sql_rechazados.=" '".$nlinea."', ";
							$sql_rechazados.=" '".$numero_de_remision."', ";
							$sql_rechazados.=" '".$numero_secuencia_actual."', ";
							$sql_rechazados.=" '".$periodo_rips_archivo."', ";
							$sql_rechazados.=" '".$estado_validado_registro."', ";
							$sql_rechazados.=" '".$departamento_filtro.$mpio_filtro_bd."' ";
							$sql_rechazados.=" ) ";
							$sql_rechazados.=" ; ";
							
							$error_bd="";
							$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_rechazados, $error_bd);
							if($error_bd!="")
							{
								$error_mostrar_bd.="ERROR AL SUBIR RECHAZADOS EN AN, ".$error_bd."<br>";
							}
						}//fin 
						//FIN SE SUBE A BD EL REGISTRO CON ERRORES APARTE
						
						//escribe los errores
						$mensaje_errores_an=$array_resultados_validacion["mensaje"];
						$array_mensajes_errores_campos=explode("|",$mensaje_errores_an);
						foreach($array_mensajes_errores_campos as $msg_error)
						{
							fwrite($file_inconsistencias_rips, $msg_error."\n");
						}
						//fin escribe los errores
						
						if(($nlinea+1)>=$acumulaciones_linea_por_porcentaje)
						{
							$mensaje_contador_errores="revisando linea ".($nlinea+1)." de $lineas_del_archivo del archivo AN,";
							$mensaje_contador_errores.=" tiene ".$array_contador_registros_buenos["AN"]." registros buenos ";
							$mensaje_contador_errores.=" y ".$array_contador_registros_malos["AN"]." registros malos ";
							$html_del_mensaje="";
							$html_del_mensaje.="<table>";
							$html_del_mensaje.="<tr>";
							$html_del_mensaje.="<td colspan=\'2\'>";
							$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
							$html_del_mensaje.="</td>";
							$html_del_mensaje.="</tr>";
							$html_del_mensaje.="<tr>";
							$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
							$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
							$html_del_mensaje.="</td>";
							$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
							$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_contador_errores."</div><div id=\'errores_bd_div\'></div>";
							$html_del_mensaje.="</td>";
							$html_del_mensaje.="</tr>";
							$html_del_mensaje.="</table>";
							if(connection_aborted()==false)
							{
								echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
								ob_flush();
								flush();
							}
							
							$query_update_esta_siendo_procesado="";
							$query_update_esta_siendo_procesado.=" UPDATE gioss_rips_eapb_esta_validando_actualmente ";
							$query_update_esta_siendo_procesado.=" SET archivos_que_ha_validado_hasta_el_momento='".$mensaje_contador_errores."' ";
							$query_update_esta_siendo_procesado.=" WHERE fecha_remision='".$date_remision_bd."' ";
							$query_update_esta_siendo_procesado.=" AND codigo_eapb_reportadora='".$cod_prestador."' ";
							$query_update_esta_siendo_procesado.=" AND nombre_archivo='".$nombre_archivo."'  ";
							$query_update_esta_siendo_procesado.=" AND codigo_departamento='".$departamento_filtro."'  ";
							$query_update_esta_siendo_procesado.=" AND codigo_municipio='".$municipio_filtro."'  ";
							$query_update_esta_siendo_procesado.=" ; ";
							$error_bd="";
							$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($query_update_esta_siendo_procesado, $error_bd);
							
							
							$acumulaciones_linea_por_porcentaje=$acumulaciones_linea_por_porcentaje+$por_ciento_del_total_lineas;
						}
						
					}//fin verificacion numero campos
					else
					{
						//$error_longitud=$array_tipo_validacion_rips["01"].",Numero de campos,el numero de campos en la linea no corresponden al numero permitido,"."AN".$numero_de_remision.",-1,".($nlinea+1);
						
						$error_longitud=$array_tipo_validacion_rips["03"].",".$array_grupo_validacion_rips["0301"].",".$array_detalle_validacion_rips["03_0301_030101"].","."AN".$numero_de_remision.",-1,".($nlinea+1);
						if($hubo_inconsistencias_en_an==false)
						{
							$hubo_inconsistencias_en_an=true;
						}
						fwrite($file_inconsistencias_rips, $error_longitud."\n");
					}//fin else longitud no apropiada
					$nlinea++;
				}
				fclose($file_an);
			}
			//fin parte valida archivo
			
			if($hubo_inconsistencias_en_an)
			{
				$se_genero_archivo_de_inconsistencias=true;
				$error_mensaje.="Hubo inconsistencias en el archivo de recien nacidos (AN).<br>";
			}
		}
		else if(isset($_POST["AN".$numero_de_remision."_valido"]) && $condicion_inconcistencias_bloqueo_para_continuar)
		{
			$error_mensaje.="El archivo de archivo de recien nacidos (AN) no existe.<br>";
			$hubo_inconsistencias_en_ah=true;
		}
		//FIN ARCHIVO AN
		
		//ARCHIVO AM
		$hubo_inconsistencias_en_am=false;
		$es_valido_nombre_archivo_am=true;
		$array_contador_registros_buenos["AM"]=0;
		$array_contador_registros_malos["AM"]=0;
		if(file_exists($ruta_archivo_am) && $condicion_inconcistencias_bloqueo_para_continuar)
		{	
			// parte donde valida-am
			if($es_valido_nombre_archivo_am)
			{
				$mensaje_errores_am="";
				$lineas_del_archivo = count(file($ruta_archivo_am)); 
				$file_am = fopen($ruta_archivo_am, 'r') or exit("No se pudo abrir el archivo");
				
				$por_ciento_del_total_lineas=intval(($lineas_del_archivo*$porcentaje_base_salto)/100);
				$acumulaciones_linea_por_porcentaje=$por_ciento_del_total_lineas;
				
				$nlinea=0;
				while (!feof($file_am)) 
				{
					$linea_tmp = fgets($file_am);
					$linea= explode("\n", $linea_tmp)[0];
					$campos = explode(",", $linea);
					if(count($campos)==15)
					{
												
						$array_resultados_validacion=validar_eapb_am($campos,$nlinea,$array_tipo_validacion_rips,$array_grupo_validacion_rips,$array_detalle_validacion_rips,$cod_prestador,"AM".$numero_de_remision,$cod_eapb,$numero_de_remision,$fecha_remision,$array_rutas_rips,$ruta_nueva,$array_afiliados_duplicados,$numeros_de_factura_por_ti_nit_ips,$nombre_archivo,$departamento_filtro,$mpio_filtro_bd);
						if($hubo_inconsistencias_en_am==false)
						{
							$hubo_inconsistencias_en_am=$array_resultados_validacion["error"];
						}
						
						$estado_validado_registro=0;
						if($array_resultados_validacion["error"]==false)
						{
							$array_contador_registros_buenos["AM"]++;
							$estado_validado_registro=1;
						}
						else
						{
							$array_contador_registros_malos["AM"]++;
							$estado_validado_registro=2;
						}
						
						//SE SUBE A BD EL REGISTRO CON ERRORES APARTE
						if($condicion_bloqueo_subida_bd_rechazados)
						{
							
							
							$sql_rechazados="";
							$sql_rechazados.="INSERT INTO gioss_registros_validados_rechazados_rips_am_eapb ";
							$sql_rechazados.=" ( ";
							$sql_rechazados.="nombre_archivo ,";
							//ini campos archivo
							$sql_rechazados.="codigo_entidad_eapb ,";
							$sql_rechazados.="codigo_prestador_servicios_salud ,";
							$sql_rechazados.="numero_factura ,";					
							$sql_rechazados.="tipo_identificacion_usuario ,";
							$sql_rechazados.="numero_identificacion_usuario ,";
							$sql_rechazados.="edad ,";
							$sql_rechazados.="unidad_medida_edad ,";
							$sql_rechazados.="nombre_generico_medicamento ,";
							$sql_rechazados.="tipo_medicamento ,";
							$sql_rechazados.="forma_farmaceutica ,";
							$sql_rechazados.="concetracion_medicamento  ,";
							$sql_rechazados.="unidad_medida_medicamento ,";
							$sql_rechazados.="numero_unidades ,";
							$sql_rechazados.="valor_unitario_medicamento ,";
							$sql_rechazados.="valor_total_medicamento ,";
							//fin campos archivo
							$sql_rechazados.="fecha_validacion ,";
							$sql_rechazados.="hora_validacion ,";
							$sql_rechazados.="fecha_remision ,";
							$sql_rechazados.="fila_integer  ,";
							$sql_rechazados.="numero_remision, ";
							$sql_rechazados.="numero_secuencia, ";
							$sql_rechazados.="periodo_corte, ";
							$sql_rechazados.="estado_registro, ";
							$sql_rechazados.="dpto_mpio ";
							$sql_rechazados.=" ) ";
							$sql_rechazados.=" VALUES ";
							$sql_rechazados.=" ( ";							
							$sql_rechazados.=" '".$nombre_archivo."', ";	
							$sql_rechazados.=" '".procesar_mensaje2($campos[0])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[1])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[2])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[3])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[4])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[5])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[6])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[7])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[8])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[9])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[10])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[11])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[12])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[13])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[14])."', ";
							$sql_rechazados.=" '".$fecha_actual."', ";
							$sql_rechazados.=" '".$tiempo_actual."', ";
							$sql_rechazados.=" '".$date_remision_bd."', ";
							$sql_rechazados.=" '".$nlinea."', ";
							$sql_rechazados.=" '".$numero_de_remision."', ";
							$sql_rechazados.=" '".$numero_secuencia_actual."', ";
							$sql_rechazados.=" '".$periodo_rips_archivo."', ";
							$sql_rechazados.=" '".$estado_validado_registro."', ";
							$sql_rechazados.=" '".$departamento_filtro.$mpio_filtro_bd."' ";
							$sql_rechazados.=" ) ";
							$sql_rechazados.=" ; ";
							
							$error_bd="";
							$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_rechazados, $error_bd);
							if($error_bd!="")
							{
								$error_mostrar_bd.="ERROR AL SUBIR RECHAZADOS EN AM, ".$error_bd."<br>";
							}
						}//fin 
						//FIN SE SUBE A BD EL REGISTRO CON ERRORES APARTE
						
						//escribe los errores
						$mensaje_errores_am=$array_resultados_validacion["mensaje"];
						$array_mensajes_errores_campos=explode("|",$mensaje_errores_am);
						foreach($array_mensajes_errores_campos as $msg_error)
						{
							fwrite($file_inconsistencias_rips, $msg_error."\n");
						}
						//fin escribe los errores
						
						if(($nlinea+1)>=$acumulaciones_linea_por_porcentaje)
						{
							$mensaje_contador_errores="revisando linea ".($nlinea+1)." de $lineas_del_archivo del archivo AM,";
							$mensaje_contador_errores.=" tiene ".$array_contador_registros_buenos["AM"]." registros buenos ";
							$mensaje_contador_errores.=" y ".$array_contador_registros_malos["AM"]." registros malos ";
							$html_del_mensaje="";
							$html_del_mensaje.="<table>";
							$html_del_mensaje.="<tr>";
							$html_del_mensaje.="<td colspan=\'2\'>";
							$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
							$html_del_mensaje.="</td>";
							$html_del_mensaje.="</tr>";
							$html_del_mensaje.="<tr>";
							$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
							$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
							$html_del_mensaje.="</td>";
							$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
							$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_contador_errores."</div><div id=\'errores_bd_div\'></div>";
							$html_del_mensaje.="</td>";
							$html_del_mensaje.="</tr>";
							$html_del_mensaje.="</table>";
							if(connection_aborted()==false)
							{
								echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
								ob_flush();
								flush();
							}
							
							$query_update_esta_siendo_procesado="";
							$query_update_esta_siendo_procesado.=" UPDATE gioss_rips_eapb_esta_validando_actualmente ";
							$query_update_esta_siendo_procesado.=" SET archivos_que_ha_validado_hasta_el_momento='".$mensaje_contador_errores."' ";
							$query_update_esta_siendo_procesado.=" WHERE fecha_remision='".$date_remision_bd."' ";
							$query_update_esta_siendo_procesado.=" AND codigo_eapb_reportadora='".$cod_prestador."' ";
							$query_update_esta_siendo_procesado.=" AND nombre_archivo='".$nombre_archivo."'  ";
							$query_update_esta_siendo_procesado.=" AND codigo_departamento='".$departamento_filtro."'  ";
							$query_update_esta_siendo_procesado.=" AND codigo_municipio='".$municipio_filtro."'  ";
							$query_update_esta_siendo_procesado.=" ; ";
							$error_bd="";
							$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($query_update_esta_siendo_procesado, $error_bd);
														
							$acumulaciones_linea_por_porcentaje=$acumulaciones_linea_por_porcentaje+$por_ciento_del_total_lineas;
						}
					}//fin verificacion numero campos
					else
					{
						//$error_longitud=$array_tipo_validacion_rips["01"].",Numero de campos,el numero de campos en la linea no corresponden al numero permitido,"."AM".$numero_de_remision.",-1,".($nlinea+1);
						
						$error_longitud=$array_tipo_validacion_rips["03"].",".$array_grupo_validacion_rips["0301"].",".$array_detalle_validacion_rips["03_0301_030101"].","."AM".$numero_de_remision.",-1,".($nlinea+1);
						if($hubo_inconsistencias_en_am==false)
						{
							$hubo_inconsistencias_en_am=true;
						}
						fwrite($file_inconsistencias_rips, $error_longitud."\n");
					}//fin else longitud no apropiada
					$nlinea++;
				}
				fclose($file_am);
			}
			//fin parte valida archivo
			
			if($hubo_inconsistencias_en_am)
			{
				$se_genero_archivo_de_inconsistencias=true;
				$error_mensaje.="Hubo inconsistencias en el archivo de medicamentos (AM).<br>";
			}
		}
		else if(isset($_POST["AM".$numero_de_remision."_valido"]) && $condicion_inconcistencias_bloqueo_para_continuar)
		{
			$error_mensaje.="El archivo de archivo de medicamentos (AM) no existe.<br>";
			$hubo_inconsistencias_en_ah=true;
		}
		//FIN ARCHIVO AM
		
		//ARCHIVO AV
		$hubo_inconsistencias_en_av=false;
		$es_valido_nombre_archivo_av=true;
		$array_contador_registros_buenos["AV"]=0;
		$array_contador_registros_malos["AV"]=0;
		if(file_exists($ruta_archivo_av) && $condicion_inconcistencias_bloqueo_para_continuar)
		{
			// parte donde valida-av
			if($es_valido_nombre_archivo_av)
			{
				$mensaje_errores_av="";
				$lineas_del_archivo = count(file($ruta_archivo_av)); 
				$file_av = fopen($ruta_archivo_av, 'r') or exit("No se pudo abrir el archivo");
				
				$por_ciento_del_total_lineas=intval(($lineas_del_archivo*$porcentaje_base_salto)/100);
				$acumulaciones_linea_por_porcentaje=$por_ciento_del_total_lineas;
				
				$nlinea=0;
				while (!feof($file_av)) 
				{
					$linea_tmp = fgets($file_av);
					$linea= explode("\n", $linea_tmp)[0];
					$campos = explode(",", $linea);
					if(count($campos)==16)
					{
						
						
						$array_resultados_validacion=validar_eapb_av($campos,$nlinea,$array_tipo_validacion_rips,$array_grupo_validacion_rips,$array_detalle_validacion_rips,$cod_prestador,"AV".$numero_de_remision,$cod_eapb,$numero_de_remision,$fecha_remision,$array_rutas_rips,$ruta_nueva);
						if($hubo_inconsistencias_en_av==false)
						{
							$hubo_inconsistencias_en_av=$array_resultados_validacion["error"];
						}
						
						$estado_validado_registro=0;
						if($array_resultados_validacion["error"]==false)
						{
							$array_contador_registros_buenos["AV"]++;
							$estado_validado_registro=1;
						}
						else
						{
							$array_contador_registros_malos["AV"]++;
							$estado_validado_registro=2;
						}
						
						//SE SUBE A BD EL REGISTRO CON ERRORES APARTE
						if($condicion_bloqueo_subida_bd_rechazados)
						{
							$sql_rechazados="";
							$sql_rechazados.="INSERT INTO gioss_registros_validados_rechazados_rips_av_eapb ";
							$sql_rechazados.=" ( ";
							$sql_rechazados.="nombre_archivo ,";
							//ini campos archivo
							$sql_rechazados.="codigo_entidad_eapb ,";
							$sql_rechazados.="year_actual ,";
							$sql_rechazados.="mes ,";
							$sql_rechazados.="pagado_consultas ,";					
							$sql_rechazados.="pagado_procedimientos_diagnosticos ,";
							$sql_rechazados.="pagado_procedimientos_quirurgicos ,";
							$sql_rechazados.="pagado_procedimientos_pyp ,";
							$sql_rechazados.="pagado_estancias ,";
							$sql_rechazados.="pagado_honorarios ,";
							$sql_rechazados.="pagado_derechos_sala ,";
							$sql_rechazados.="pagado_materiales_insumos ,";
							$sql_rechazados.="pagado_banco_sagre  ,";
							$sql_rechazados.="pagado_protesis_ortesis ,";
							$sql_rechazados.="pagado_medicamentos_pos ,";
							$sql_rechazados.="pagado_medicamentos_no_pos ,";
							$sql_rechazados.="pagado_traslado_pacientes ,";
							//fin campos archivo
							$sql_rechazados.="fecha_validacion ,";
							$sql_rechazados.="hora_validacion ,";
							$sql_rechazados.="fecha_remision ,";
							$sql_rechazados.="fila_integer  ,";
							$sql_rechazados.="numero_remision, ";
							$sql_rechazados.="numero_secuencia, ";
							$sql_rechazados.="periodo_corte, ";
							$sql_rechazados.="estado_registro, ";
							$sql_rechazados.="dpto_mpio ";
							$sql_rechazados.=" ) ";
							$sql_rechazados.=" VALUES ";
							$sql_rechazados.=" ( ";							
							$sql_rechazados.=" '".$nombre_archivo."', ";	
							$sql_rechazados.=" '".procesar_mensaje2($campos[0])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[1])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[2])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[3])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[4])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[5])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[6])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[7])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[8])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[9])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[10])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[11])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[12])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[13])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[14])."', ";
							$sql_rechazados.=" '".procesar_mensaje2($campos[15])."', ";
							$sql_rechazados.=" '".$fecha_actual."', ";
							$sql_rechazados.=" '".$tiempo_actual."', ";
							$sql_rechazados.=" '".$date_remision_bd."', ";
							$sql_rechazados.=" '".$nlinea."', ";
							$sql_rechazados.=" '".$numero_de_remision."', ";
							$sql_rechazados.=" '".$numero_secuencia_actual."', ";
							$sql_rechazados.=" '".$periodo_rips_archivo."', ";
							$sql_rechazados.=" '".$estado_validado_registro."', ";
							$sql_rechazados.=" '".$departamento_filtro.$mpio_filtro_bd."' ";
							$sql_rechazados.=" ) ";
							$sql_rechazados.=" ; ";
							
							$error_bd="";
							$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_rechazados, $error_bd);
							if($error_bd!="")
							{
								$error_mostrar_bd.="ERROR AL SUBIR RECHAZADOS EN AV, ".$error_bd."<br>";
							}
						}//fin 
						//FIN SE SUBE A BD EL REGISTRO CON ERRORES APARTE
						
						//escribe los errores
						$mensaje_errores_av=$array_resultados_validacion["mensaje"];
						$array_mensajes_errores_campos=explode("|",$mensaje_errores_av);
						foreach($array_mensajes_errores_campos as $msg_error)
						{
							fwrite($file_inconsistencias_rips, $msg_error."\n");
						}
						//fin escribe los errores
						
						if(($nlinea+1)>=$acumulaciones_linea_por_porcentaje)
						{
							$mensaje_contador_errores="revisando linea ".($nlinea+1)." de $lineas_del_archivo del archivo AV,";
							$mensaje_contador_errores.=" tiene ".$array_contador_registros_buenos["AV"]." registros buenos ";
							$mensaje_contador_errores.=" y ".$array_contador_registros_malos["AV"]." registros malos ";
							$html_del_mensaje="";
							$html_del_mensaje.="<table>";
							$html_del_mensaje.="<tr>";
							$html_del_mensaje.="<td colspan=\'2\'>";
							$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
							$html_del_mensaje.="</td>";
							$html_del_mensaje.="</tr>";
							$html_del_mensaje.="<tr>";
							$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
							$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
							$html_del_mensaje.="</td>";
							$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
							$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_contador_errores."</div><div id=\'errores_bd_div\'></div>";
							$html_del_mensaje.="</td>";
							$html_del_mensaje.="</tr>";
							$html_del_mensaje.="</table>";
							if(connection_aborted()==false)
							{
								echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
								ob_flush();
								flush();
							}
							
							$query_update_esta_siendo_procesado="";
							$query_update_esta_siendo_procesado.=" UPDATE gioss_rips_eapb_esta_validando_actualmente ";
							$query_update_esta_siendo_procesado.=" SET archivos_que_ha_validado_hasta_el_momento='".$mensaje_contador_errores."' ";
							$query_update_esta_siendo_procesado.=" WHERE fecha_remision='".$date_remision_bd."' ";
							$query_update_esta_siendo_procesado.=" AND codigo_eapb_reportadora='".$cod_prestador."' ";
							$query_update_esta_siendo_procesado.=" AND nombre_archivo='".$nombre_archivo."'  ";
							$query_update_esta_siendo_procesado.=" AND codigo_departamento='".$departamento_filtro."'  ";
							$query_update_esta_siendo_procesado.=" AND codigo_municipio='".$municipio_filtro."'  ";
							$query_update_esta_siendo_procesado.=" ; ";
							$error_bd="";
							$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_rechazados, $error_bd);
														
							$acumulaciones_linea_por_porcentaje=$acumulaciones_linea_por_porcentaje+$por_ciento_del_total_lineas;
						}
						
					}//fin verificacion numero campos
					else
					{
						//$error_longitud=$array_tipo_validacion_rips["01"].",Numero de campos,el numero de campos en la linea no corresponden al numero permitido,"."AV".$numero_de_remision.",-1,".($nlinea+1);
						
						$error_longitud=$array_tipo_validacion_rips["03"].",".$array_grupo_validacion_rips["0301"].",".$array_detalle_validacion_rips["03_0301_030101"].","."AV".$numero_de_remision.",-1,".($nlinea+1);
						if($hubo_inconsistencias_en_av==false)
						{
							$hubo_inconsistencias_en_av=true;
						}
						fwrite($file_inconsistencias_rips, $error_longitud."\n");
					}//fin else longitud no apropiada
					$nlinea++;
				}
				fclose($file_av);
			}
			//fin parte valida archivo
			
			if($hubo_inconsistencias_en_av)
			{
				$se_genero_archivo_de_inconsistencias=true;
				$error_mensaje.="Hubo inconsistencias en el archivo de otros servicios (AV).<br>";
			}
		}
		else if(isset($_POST["AV".$numero_de_remision."_valido"]) && $condicion_inconcistencias_bloqueo_para_continuar)
		{
			$error_mensaje.="El archivo de archivo de otros servicios (AV) no existe.<br>";
			$hubo_inconsistencias_en_ah=true;
		}
		//FIN ARCHIVO AV
		
		
		//VERIFICA LOS QUE NO ENCONTRO EN US
		$bool_hubo_afiliados_de_us_sin_encontrar_en_archivos_de_servicios=false;
		foreach($array_afiliados_duplicados as $key=>$afiliado_de_us)
		{
			$array_lineas_encontrado_afiliado=explode("...",$afiliado_de_us);
			if(count($array_lineas_encontrado_afiliado)==2)
			{
				//se encontro no hay error
			}
			else
			{
				
				$bool_hubo_afiliados_de_us_sin_encontrar_en_archivos_de_servicios=true;				
				$array_lineas_no_encontrado=explode("-",$afiliado_de_us);
				
				foreach($array_lineas_no_encontrado as $linea_no_encontrado)
				{
					$linea_no_econtrado_int=intval($linea_no_encontrado);
					$error_no_existe_en_otros_archivos=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0106"].",".$array_detalle_validacion_rips["01_0106_010608"]."  $key ,US".$numero_de_remision.",3,".$linea_no_econtrado_int;
					if($hubo_inconsistencias_en_us==false)
					{
						$hubo_inconsistencias_en_us=true;
					}
					fwrite($file_inconsistencias_rips, $error_no_existe_en_otros_archivos."\n");
				}//fin foreach
			}//fin else
		}
		if($bool_hubo_afiliados_de_us_sin_encontrar_en_archivos_de_servicios)
		{
			$error_mensaje.="No se encontraron en al menos un archivo de servicios un(os) de los afiliados en el archivo de usuarios(US) .<br>";
		}
		//FIN VERIFICA LOS QUE NO ENCONTRO EN US
		
		unset($array_afiliados_duplicados);
		
		
		//SUBIENDO FACTURAS
		$estado_val_para_fact=0;
		if($error_mensaje=="" && $verificacion_ya_se_valido_con_exito==false)
		{
			$estado_val_para_fact=1;
		}
		else
		{
			$estado_val_para_fact=2;
		}
		foreach($numeros_de_factura_por_ti_nit_ips as $key=>$num_fact_ips)
		{
			
			
			$array_fact_ips=explode("_",$key);
			$num_fact_fact=$array_fact_ips[0];
			$cod_ips_fact=$array_fact_ips[1];
			$query_verificar_existe_factura="";
			$query_verificar_existe_factura.=" SELECT * FROM gioss_numero_factura_validacion_eapb ";
			$query_verificar_existe_factura.=" WHERE ";
			$query_verificar_existe_factura.=" codigo_eapb='$cod_entidad_reportadora_eapb_fact' ";
			$query_verificar_existe_factura.=" AND ";
			$query_verificar_existe_factura.=" codigo_ips='$cod_ips_fact' ";
			$query_verificar_existe_factura.=" AND ";
			$query_verificar_existe_factura.=" numero_factura='$num_fact_fact' ";
			$query_verificar_existe_factura.=" AND ";
			$query_verificar_existe_factura.=" fecha_remision='$date_remision_bd' ";
			$query_verificar_existe_factura.=" AND ";
			$query_verificar_existe_factura.=" nombre_archivo_rips='$nombre_archivo' ";
			$query_verificar_existe_factura.=" AND ";
			$query_verificar_existe_factura.=" codigo_dpto='$departamento_filtro' ";
			$query_verificar_existe_factura.=" AND ";
			$query_verificar_existe_factura.=" codigo_mpio='$mpio_filtro_bd' ";
			$query_verificar_existe_factura.=" ; ";
			$resultado_query_existe_factura=$coneccionBD->consultar2($query_verificar_existe_factura);
			
			//si no estan en la tabla despues de buscarlos se insertan
			if(count($resultado_query_existe_factura)==0 || !is_array($resultado_query_existe_factura))
			{
				$insert_factura="";
				$insert_factura.="INSERT INTO gioss_numero_factura_validacion_eapb";
				$insert_factura.="(";
				$insert_factura.="codigo_eapb,";
				$insert_factura.="codigo_ips,";
				$insert_factura.="numero_factura,";
				$insert_factura.="fecha_validacion,";
				$insert_factura.="fecha_remision,";
				$insert_factura.="nombre_archivo_rips,";
				$insert_factura.="estado_validacion,";
				$insert_factura.="periodo_presentado,";
				$insert_factura.="codigo_dpto,";
				$insert_factura.="codigo_mpio";
				$insert_factura.=")";
				$insert_factura.=" VALUES ";
				$insert_factura.="(";
				$insert_factura.="'".$cod_entidad_reportadora_eapb_fact."',";
				$insert_factura.="'".$cod_ips_fact."',";
				$insert_factura.="'".$num_fact_fact."',";
				$insert_factura.="'".$fecha_actual."',";
				$insert_factura.="'".$date_remision_bd."',";
				$insert_factura.="'".$nombre_archivo."',";
				$insert_factura.="'".$estado_val_para_fact."',";
				$insert_factura.="'".$periodo_rips_archivo."',";				
				$insert_factura.="'".$departamento_filtro."',";
				$insert_factura.="'".$mpio_filtro_bd."'";
				$insert_factura.=")";
				$error_bd_seq="";
				$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($insert_factura, $error_bd_seq);		
				if($error_bd_seq!="")
				{
					if(connection_aborted()==false)
					{
						echo "<script>alert('Errores insertar tabla FACTURA DE VALIDACION EAPB: ".procesar_mensaje($error_bd_seq)."');</script>";
						echo "<script>alert('query: ".procesar_mensaje($insert_factura)."');</script>";
					}
				}
				
			}
			else if($estado_val_para_fact==1)
			{
				$query_actualizar_estado_factura_validacion_eapb="";
				$query_actualizar_estado_factura_validacion_eapb.=" UPDATE gioss_numero_factura_validacion_eapb ";
				$query_actualizar_estado_factura_validacion_eapb.=" SET estado_validacion='$estado_val_para_fact' ";
				$query_actualizar_estado_factura_validacion_eapb.=" WHERE ";
				$query_actualizar_estado_factura_validacion_eapb.=" codigo_eapb='$cod_entidad_reportadora_eapb_fact' ";
				$query_actualizar_estado_factura_validacion_eapb.=" AND ";
				$query_actualizar_estado_factura_validacion_eapb.=" codigo_ips='$cod_ips_fact' ";
				$query_actualizar_estado_factura_validacion_eapb.=" AND ";
				$query_actualizar_estado_factura_validacion_eapb.=" numero_factura='$num_fact_fact' ";
				$query_actualizar_estado_factura_validacion_eapb.=" AND ";
				$query_actualizar_estado_factura_validacion_eapb.=" fecha_remision='$date_remision_bd' ";
				$query_actualizar_estado_factura_validacion_eapb.=" AND ";
				$query_actualizar_estado_factura_validacion_eapb.=" nombre_archivo_rips='$nombre_archivo' ";
				$query_actualizar_estado_factura_validacion_eapb.=" AND ";
				$query_actualizar_estado_factura_validacion_eapb.=" codigo_dpto='$departamento_filtro' ";
				$query_actualizar_estado_factura_validacion_eapb.=" AND ";
				$query_actualizar_estado_factura_validacion_eapb.=" codigo_mpio='$mpio_filtro_bd' ";
				$query_actualizar_estado_factura_validacion_eapb.=" ; ";
				$error_bd_seq="";
				$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($query_actualizar_estado_factura_validacion_eapb, $error_bd_seq);		
				if($error_bd_seq!="")
				{
					if(connection_aborted()==false)
					{
						echo "<script>alert('Errores actualizar estado tabla FACTURA DE VALIDACION EAPB: ".procesar_mensaje($error_bd_seq)."');</script>";
						echo "<script>alert('query: ".procesar_mensaje($query_actualizar_estado_factura_validacion_eapb)."');</script>";
					}
				}
			}
		}//fin foreach
		//FIN SUBE FACTURAS
		
		
		unset($numeros_de_factura_por_ti_nit_ips);
		//cierra el archivo de inconsistencias
		fclose($file_inconsistencias_rips);
		
	}//fin if solo se valida si no fue validado con exito
	//FIN VALIDACION DE LOS CAMPOS DE LOS ARCHIVOS RIPS
	
	
	//error de bd mostrar
	if($error_mostrar_bd!="")
	{
		$error_mostrar_bd_procesado = str_replace("á","a",$error_mostrar_bd);
		$error_mostrar_bd_procesado = str_replace("é","e",$error_mostrar_bd_procesado);
		$error_mostrar_bd_procesado = str_replace("í","i",$error_mostrar_bd_procesado);
		$error_mostrar_bd_procesado = str_replace("ó","o",$error_mostrar_bd_procesado);
		$error_mostrar_bd_procesado = str_replace("ú","u",$error_mostrar_bd_procesado);
		$error_mostrar_bd_procesado = str_replace("ñ","n",$error_mostrar_bd_procesado);
		$error_mostrar_bd_procesado = str_replace("Á","A",$error_mostrar_bd_procesado);
		$error_mostrar_bd_procesado = str_replace("É","E",$error_mostrar_bd_procesado);
		$error_mostrar_bd_procesado = str_replace("Í","I",$error_mostrar_bd_procesado);
		$error_mostrar_bd_procesado = str_replace("Ó","O",$error_mostrar_bd_procesado);
		$error_mostrar_bd_procesado = str_replace("Ú","U",$error_mostrar_bd_procesado);
		$error_mostrar_bd_procesado = str_replace("Ñ","N",$error_mostrar_bd_procesado);
		$error_mostrar_bd_procesado = str_replace(" "," ",$error_mostrar_bd_procesado);
		$error_mostrar_bd_procesado = str_replace("'"," ",$error_mostrar_bd_procesado);
		$error_mostrar_bd_procesado = str_replace("\n"," ",$error_mostrar_bd_procesado);
		$linea_res= alphanumericAndSpace($error_mostrar_bd_procesado);
		
		$error_mensaje.="ERROR EN SUBIR RECHAZADOS A BD, ".$error_mostrar_bd_procesado;
		
	}//fin if error en bd
	
		
	//PARTE PARA SUBIR VALIDADOS CON EXITO EN BD
	//sube los archivos si han sido validados con exito aqui borra los rechazados que se subieron si termina siendo exitoso
	//debido a que se subira en ambos tipos de tablas si es exitoso preo al final se borra de rechazados
	//para asi permitir el adicionar una bandera a cada registro rechazado si estubo bueno o malo
	if($error_mensaje=="" && $verificacion_ya_se_valido_con_exito==false && $bool_fecha_del_ct_erronea==false && $bool_se_esta_validando_en_este_momento==false)
	{
		$sql_exito="";
		$sql_exito.="BEGIN TRANSACTION;";
		//falso no hay error, true error en query bd
		$bool_hubo_error_query=false;
		$error_mostrar_bd="";
		
		$fecha_remision_array=explode("/",$fecha_remision);
		$date_remision_bd=$fecha_remision_array[2]."-".$fecha_remision_array[0]."-".$fecha_remision_array[1];
		
		$error_bd="";
		$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_exito, $error_bd);
		if($error_bd!="")
		{
			$error_mostrar_bd.=$error_bd."<br>";
		}
		
		
		
		//CT
		if($es_valido_nombre_archivo_ct  && $bool_hubo_error_query==false
		   && (file_exists($ruta_archivo_ct)==true && $rutaTemporal!=trim($ruta_archivo_ct)))
		{
			//recordatorio: ya se elimina de rechazados al final
			
			$mensaje_errores_ct="";
			$lineas_del_archivo = count(file($ruta_archivo_ct)); 
			$file_ct = fopen($ruta_archivo_ct, 'r') or exit("No se pudo abrir el archivo");
			
			$nlinea=0;
			while (!feof($file_ct) && $bool_hubo_error_query==false) 
			{
				$linea_tmp = fgets($file_ct);
				$linea= explode("\n", $linea_tmp)[0];
				$campos = explode(",", $linea);
				
				//verifica el prestador del documento ct con el asociado
				if(count($campos)==4)
				{
					$array_fecha_reportada=explode("/",$campos[1]);
					$date_reportada_bd=$array_fecha_reportada[2]."-".$array_fecha_reportada[1]."-".$array_fecha_reportada[0];
					$sql_exito="";
					$sql_exito.="INSERT INTO gioss_registros_validados_exito_rips_ct_eapb ";
					$sql_exito.=" ( ";
					$sql_exito.="fecha_validacion ,";
					$sql_exito.="nombre_archivo ,";
					$sql_exito.="codigo_entidad_eapb ,";
					$sql_exito.="fecha_remision ,";						
					$sql_exito.="codigo_archivo ,";
					$sql_exito.="total_registros  ,";
					$sql_exito.="fila_integer ,";
					$sql_exito.="numero_remision, ";
					$sql_exito.="numero_secuencia, ";
					$sql_exito.="hora_validacion, ";
					$sql_exito.="periodo_corte, ";
					$sql_exito.="estado_registro, ";
					$sql_exito.="dpto_mpio ";
					$sql_exito.=" ) ";
					$sql_exito.=" VALUES ";
					$sql_exito.=" ( ";
					$sql_exito.=" '".$fecha_actual."', ";
					$sql_exito.=" '".$nombre_archivo."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[0])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[1])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[2])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[3])."', ";
					$sql_exito.=" '".$nlinea."', ";
					$sql_exito.=" '".$numero_de_remision."', ";
					$sql_exito.=" '".$numero_secuencia_actual."', ";
					$sql_exito.=" '".$tiempo_actual."', ";
					$sql_exito.=" '".$periodo_rips_archivo."', ";
					$sql_exito.=" '".$estado_validado_registro."', ";
					$sql_exito.=" '".$departamento_filtro.$mpio_filtro_bd."' ";
					$sql_exito.=" ) ";
					$sql_exito.=" ; ";
					
					$error_bd="";
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_exito, $error_bd);
					if($error_bd!="")
					{
						$error_mostrar_bd.="ERROR CT EXITOSO: ".$error_bd."<br>";
					}
					
					$mensaje_contador_errores="Se subio en la base de datos, el registro ".($nlinea+1)." de ";
					$mensaje_contador_errores.=" un total de ".$array_contador_registros_buenos["CT"]." registros buenos para CT. ";
					$html_del_mensaje="";
					$html_del_mensaje.="<table>";
					$html_del_mensaje.="<tr>";
					$html_del_mensaje.="<td colspan=\'2\'>";
					$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="</tr>";
					$html_del_mensaje.="<tr>";
					$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
					$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
					$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_contador_errores."</div><div id=\'errores_bd_div\'></div>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="</tr>";
					$html_del_mensaje.="</table>";
					if(connection_aborted()==false)
					{
						echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
						ob_flush();
						flush();
					}
					
				}//fin if
				$nlinea++;
				
				
			}//fin while
			fclose($file_ct);
		}//fin if si el archivo ct es valido
		//FIN CT
		
		//US
		if($es_valido_nombre_archivo_us  && $bool_hubo_error_query==false
		   && (file_exists($ruta_archivo_us)==true && $rutaTemporal!=trim($ruta_archivo_us)))
		{
			/*
			 *abajo ya hay una parte donde elimina los rechazados pero despues de que sube los exitosos
			*/
			
			$lineas_del_archivo = count(file($ruta_archivo_us)); 
			$file_us = fopen($ruta_archivo_us, 'r') or exit("No se pudo abrir el archivo");
			
			$nlinea=0;
			while (!feof($file_us)) 
			{
				$linea_tmp = fgets($file_us);
				$linea= explode("\n", $linea_tmp)[0];
				$campos = explode(",", $linea);
				if(count($campos)==12)
				{					
					$sql_exito="";
					$sql_exito.="INSERT INTO gioss_registros_validados_exito_rips_us_eapb ";
					$sql_exito.=" ( ";							
					$sql_exito.="fecha_remision ,";
					$sql_exito.="nombre_archivo ,";
					$sql_exito.="codigo_entidad_eapb ,";												
					$sql_exito.="tipo_identificacion_usuario ,";
					$sql_exito.="numero_identificacion_usuario ,";
					$sql_exito.="codigo_tipo_usuario ,";
					$sql_exito.="codigo_tipo_afiliado ,";
					$sql_exito.="codigo_ocupacion ,";
					$sql_exito.="edad ,";
					$sql_exito.="unidad_medida_edad ,";
					$sql_exito.="sexo ,";
					$sql_exito.="codigo_departamento_residencia ,";
					$sql_exito.="codigo_municipio_residencia ,";
					$sql_exito.="codigo_zona_residencia ,";
					$sql_exito.="fecha_validacion ,";
					$sql_exito.="hora_validacion ,";
					$sql_exito.="fila_integer ,";
					$sql_exito.="numero_remision, ";
					$sql_exito.="numero_secuencia, ";
					$sql_exito.="periodo_corte, ";
					$sql_exito.="estado_registro, ";
					$sql_exito.="dpto_mpio ";
					$sql_exito.=" ) ";
					$sql_exito.=" VALUES ";
					$sql_exito.=" ( ";
					$sql_exito.=" '".$date_remision_bd."', ";			
					$sql_exito.=" '".$nombre_archivo."', ";							
					$sql_exito.=" '".procesar_mensaje2($campos[0])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[1])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[2])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[3])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[4])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[5])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[6])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[7])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[8])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[9])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[10])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[11])."', ";
					$sql_exito.=" '".$fecha_actual."', ";
					$sql_exito.=" '".$tiempo_actual."', ";
					$sql_exito.=" '".$nlinea."', ";
					$sql_exito.=" '".$numero_de_remision."', ";
					$sql_exito.=" '".$numero_secuencia_actual."', ";
					$sql_exito.=" '".$periodo_rips_archivo."', ";
					$sql_exito.=" '".$estado_validado_registro."', ";
					$sql_exito.=" '".$departamento_filtro.$mpio_filtro_bd."' ";
					$sql_exito.=" ) ";
					$sql_exito.=" ; ";
					
					$error_bd="";
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_exito, $error_bd);
					if($error_bd!="")
					{
						$error_mostrar_bd.=" ERROR US EXITOSO ".$error_bd."<br>";
					}
					
					$mensaje_contador_errores="Se subio en la base de datos, el registro ".($nlinea+1)." de ";
					$mensaje_contador_errores.=" un total de ".$array_contador_registros_buenos["US"]." registros buenos para US. ";
					$html_del_mensaje="";
					$html_del_mensaje.="<table>";
					$html_del_mensaje.="<tr>";
					$html_del_mensaje.="<td colspan=\'2\'>";
					$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="</tr>";
					$html_del_mensaje.="<tr>";
					$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
					$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
					$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_contador_errores."</div><div id=\'errores_bd_div\'></div>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="</tr>";
					$html_del_mensaje.="</table>";
					if(connection_aborted()==false)
					{
						echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
						ob_flush();
						flush();
					}
				}//fin verifica numero campos
				$nlinea++;
			}
			fclose($file_us);
		}
		//FIN US
		
		//AC
		if($es_valido_nombre_archivo_ac && $bool_hubo_error_query==false
		   && (file_exists($ruta_archivo_ac)==true && $rutaTemporal!=trim($ruta_archivo_ac)))
		{
			/*
			 *abajo ya hay una parte donde elimina los rechazados pero despues de que sube los exitosos
			*/
			
			$lineas_del_archivo = count(file($ruta_archivo_ac)); 
			$file_ac = fopen($ruta_archivo_ac, 'r') or exit("No se pudo abrir el archivo");
			
			$nlinea=0;
			while (!feof($file_ac)) 
			{
				$linea_tmp = fgets($file_ac);
				$linea= explode("\n", $linea_tmp)[0];
				$campos = explode(",", $linea);
				if(count($campos)==17)
				{
					$sql_exito="";
					$sql_exito.="INSERT INTO gioss_registros_validados_exito_rips_ac_eapb ";
					$sql_exito.=" ( ";							
					$sql_exito.="nombre_archivo ,";							
					$sql_exito.="codigo_entidad_eapb ,";
					$sql_exito.="codigo_prestador_servcios_salud ,";
					$sql_exito.="numero_factura ,";
					$sql_exito.="tipo_identificacion_usuario ,";
					$sql_exito.="numero_identificacion_usuario ,";;
					$sql_exito.="fecha_atencion ,";
					$sql_exito.="codigo_cups_consulta ,";
					$sql_exito.="finalidad_consulta ,";
					$sql_exito.="causa_externa_consulta ,";
					$sql_exito.="codigo_diagnostico_principal ,";
					$sql_exito.="codigo_relacionado_1 ,";
					$sql_exito.="codigo_relacionado_2 ,";
					$sql_exito.="codigo_relacionado_3 ,";
					$sql_exito.="tipo_diagnostico_principal ,";
					$sql_exito.="valor_consulta ,";
					$sql_exito.="valor_cuota_moderadora ,";
					$sql_exito.="valor_neto_pagado ,";							
					$sql_exito.="fecha_validacion ,";
					$sql_exito.="hora_validacion ,";
					$sql_exito.="fecha_remision ,";
					$sql_exito.="fila_integer ,";
					$sql_exito.="numero_remision, ";
					$sql_exito.="numero_secuencia, ";
					$sql_exito.="periodo_corte, ";
					$sql_exito.="estado_registro, ";
					$sql_exito.="dpto_mpio ";
					$sql_exito.=" ) ";
					$sql_exito.=" VALUES ";
					$sql_exito.=" ( ";
					$sql_exito.=" '".$nombre_archivo."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[0])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[1])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[2])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[3])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[4])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[5])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[6])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[7])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[8])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[9])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[10])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[11])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[12])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[13])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[14])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[15])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[16])."', ";
					$sql_exito.=" '".$fecha_actual."', ";
					$sql_exito.=" '".$tiempo_actual."', ";
					$sql_exito.=" '".$date_remision_bd."', ";
					$sql_exito.=" '".$nlinea."', ";
					$sql_exito.=" '".$numero_de_remision."', ";
					$sql_exito.=" '".$numero_secuencia_actual."', ";
					$sql_exito.=" '".$periodo_rips_archivo."', ";
					$sql_exito.=" '".$estado_validado_registro."', ";
					$sql_exito.=" '".$departamento_filtro.$mpio_filtro_bd."' ";
					$sql_exito.=" ) ";
					$sql_exito.=" ; ";
					
					$error_bd="";
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_exito, $error_bd);
					if($error_bd!="")
					{
						$error_mostrar_bd.="ERROR AC EXITOSO: ".$error_bd."<br>";
					}
					
					$mensaje_contador_errores="Se subio en la base de datos, el registro ".($nlinea+1)." de ";
					$mensaje_contador_errores.=" un total de ".$array_contador_registros_buenos["AC"]." registros buenos para AC. ";
					$html_del_mensaje="";
					$html_del_mensaje.="<table>";
					$html_del_mensaje.="<tr>";
					$html_del_mensaje.="<td colspan=\'2\'>";
					$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="</tr>";
					$html_del_mensaje.="<tr>";
					$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
					$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
					$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_contador_errores."</div><div id=\'errores_bd_div\'></div>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="</tr>";
					$html_del_mensaje.="</table>";
					if(connection_aborted()==false)
					{
						echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
						ob_flush();
						flush();
					}
					
				}//fin verifica numero campos
				$nlinea++;
			}
			fclose($file_ac);
		}
		//FIN AC
		
		//AH
		if($es_valido_nombre_archivo_ah && $bool_hubo_error_query==false
		   && (file_exists($ruta_archivo_ah)==true && $rutaTemporal!=trim($ruta_archivo_ah)))
		{
			/*
			 *abajo ya hay una parte donde elimina los rechazados pero despues de que sube los exitosos
			*/
			
			$lineas_del_archivo = count(file($ruta_archivo_ah)); 
			$file_ah = fopen($ruta_archivo_ah, 'r') or exit("No se pudo abrir el archivo");
			
			$nlinea=0;
			while (!feof($file_ah)) 
			{
				$linea_tmp = fgets($file_ah);
				$linea= explode("\n", $linea_tmp)[0];
				$campos = explode(",", $linea);
				if(count($campos)==19)
				{				
					$sql_exito="";
					$sql_exito.="INSERT INTO gioss_registros_validados_exito_rips_ah_eapb ";
					$sql_exito.=" ( ";
					$sql_exito.="nombre_archivo ,";
					$sql_exito.="codigo_entidad_eapb ,";
					$sql_exito.="codigo_prestador_servicios_salud ,";
					$sql_exito.="numero_factura ,";					
					$sql_exito.="tipo_identificacion_usuario ,";
					$sql_exito.="numero_identificacion_usuario ,";
					$sql_exito.="via_ingreso_institucion ,";
					$sql_exito.="fecha_ingreso ,";
					$sql_exito.="hora_ingreso ,";
					$sql_exito.="causa_externa ,";
					$sql_exito.="codigo_diagnostico_principal_ingreso ,";
					$sql_exito.="codigo_diagnostico_principal_egreso ,";
					$sql_exito.="codigo_relacionado_egreso_1 ,";
					$sql_exito.="codigo_relacionado_egreso_2 ,";
					$sql_exito.="codigo_relacionado_egreso_3 ,";
					$sql_exito.="codigo_diagnostico_complicacion ,";
					$sql_exito.="estado_a_salida ,";
					$sql_exito.="codigo_diagnostico_muerte ,";
					$sql_exito.="fecha_egreso ,";
					$sql_exito.="hora_egreso ,";
					$sql_exito.="fecha_validacion ,";
					$sql_exito.="hora_validacion ,";							
					$sql_exito.="fecha_remision ,";
					$sql_exito.="fila_integer, ";
					$sql_exito.="numero_remision, ";
					$sql_exito.="numero_secuencia, ";
					$sql_exito.="periodo_corte, ";
					$sql_exito.="estado_registro, ";
					$sql_exito.="dpto_mpio ";
					$sql_exito.=" ) ";
					$sql_exito.=" VALUES ";
					$sql_exito.=" ( ";							
					$sql_exito.=" '".$nombre_archivo."', ";	
					$cont_tmp=0;
					$sql_exito.=" '".procesar_mensaje2($campos[0])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[1])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[2])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[3])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[4])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[5])."', ";					
					$sql_exito.=" '".procesar_mensaje2($campos[6])."', ";//7 6
					$sql_exito.=" '".procesar_mensaje2($campos[7])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[8])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[9])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[10])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[11])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[12])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[13])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[14])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[15])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[16])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[17])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[18])."', ";
					$sql_exito.=" '".$fecha_actual."', ";
					$sql_exito.=" '".$tiempo_actual."', ";
					$sql_exito.=" '".$date_remision_bd."', ";
					$sql_exito.=" '".$nlinea."', ";
					$sql_exito.=" '".$numero_de_remision."', ";
					$sql_exito.=" '".$numero_secuencia_actual."', ";
					$sql_exito.=" '".$periodo_rips_archivo."', ";
					$sql_exito.=" '".$estado_validado_registro."', ";
					$sql_exito.=" '".$departamento_filtro.$mpio_filtro_bd."' ";
					$sql_exito.=" ) ";
					$sql_exito.=" ; ";
					
					$error_bd="";
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_exito, $error_bd);
					if($error_bd!="")
					{
						$error_mostrar_bd.="ERROR AH EXITOSO ".$error_bd."<br>";
					}
					
					$mensaje_contador_errores="Se subio en la base de datos, el registro ".($nlinea+1)." de ";
					$mensaje_contador_errores.=" un total de ".$array_contador_registros_buenos["AH"]." registros buenos para AH. ";
					$html_del_mensaje="";
					$html_del_mensaje.="<table>";
					$html_del_mensaje.="<tr>";
					$html_del_mensaje.="<td colspan=\'2\'>";
					$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="</tr>";
					$html_del_mensaje.="<tr>";
					$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
					$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
					$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_contador_errores."</div><div id=\'errores_bd_div\'></div>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="</tr>";
					$html_del_mensaje.="</table>";
					if(connection_aborted()==false)
					{
						echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
						ob_flush();
						flush();
					}
					
				}//fin verifica numero campos
				$nlinea++;
			}
			fclose($file_ah);
		}
		//FIN AH
		
		
		//AP
		if($es_valido_nombre_archivo_ap && $bool_hubo_error_query==false
		   && (file_exists($ruta_archivo_ap)==true && $rutaTemporal!=trim($ruta_archivo_ap)))
		{
			/*
			 *abajo ya hay una parte donde elimina los rechazados pero despues de que sube los exitosos
			*/
			
			$lineas_del_archivo = count(file($ruta_archivo_ap)); 
			$file_ap = fopen($ruta_archivo_ap, 'r') or exit("No se pudo abrir el archivo");
			
			$nlinea=0;
			while (!feof($file_ap)) 
			{
				$linea_tmp = fgets($file_ap);
				$linea= explode("\n", $linea_tmp)[0];
				$campos = explode(",", $linea);
				if(count($campos)==14)
				{
					$sql_exito="";
					$sql_exito.="INSERT INTO gioss_registros_validados_exito_rips_ap_eapb ";
					$sql_exito.=" ( ";
					$sql_exito.="nombre_archivo ,";
					$sql_exito.="codigo_entidad_eapb ,";
					$sql_exito.="codigo_prestador_servicios_salud ,";
					$sql_exito.="numero_factura ,";					
					$sql_exito.="tipo_identificacion_usuario ,";
					$sql_exito.="numero_identificacion_usuario ,";
					$sql_exito.="fecha_procedimiento ,";
					$sql_exito.="codigo_cups_procedimiento ,";
					$sql_exito.="ambito_realizacion_procedimiento ,";
					$sql_exito.="finalidad_procedimiento ,";
					$sql_exito.="personal_que_atiende ,";
					$sql_exito.="diagnostico_principal ,";
					$sql_exito.="diagnostico_relacionado ,";
					$sql_exito.="diagnostico_complicacion ,";
					$sql_exito.="valor_procedimiento ,";
					$sql_exito.="fecha_validacion ,";
					$sql_exito.="hora_validacion ,";
					$sql_exito.="fecha_remision ,";
					$sql_exito.="fila_integer  ,";
					$sql_exito.="numero_remision, ";
					$sql_exito.="numero_secuencia, ";
					$sql_exito.="periodo_corte, ";
					$sql_exito.="estado_registro, ";
					$sql_exito.="dpto_mpio ";
					$sql_exito.=" ) ";
					$sql_exito.=" VALUES ";
					$sql_exito.=" ( ";							
					$sql_exito.=" '".$nombre_archivo."', ";	
					$sql_exito.=" '".procesar_mensaje2($campos[0])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[1])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[2])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[3])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[4])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[5])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[6])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[7])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[8])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[9])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[10])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[11])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[12])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[13])."', ";
					$sql_exito.=" '".$fecha_actual."', ";
					$sql_exito.=" '".$tiempo_actual."', ";
					$sql_exito.=" '".$date_remision_bd."', ";
					$sql_exito.=" '".$nlinea."', ";
					$sql_exito.=" '".$numero_de_remision."', ";
					$sql_exito.=" '".$numero_secuencia_actual."', ";
					$sql_exito.=" '".$periodo_rips_archivo."', ";
					$sql_exito.=" '".$estado_validado_registro."', ";
					$sql_exito.=" '".$departamento_filtro.$mpio_filtro_bd."' ";
					$sql_exito.=" ) ";
					$sql_exito.=" ; ";
					
					$error_bd="";
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_exito, $error_bd);
					if($error_bd!="")
					{
						$error_mostrar_bd.="ERROR AP EXITOSO ".$error_bd."<br>";
					}
					
					$mensaje_contador_errores="Se subio en la base de datos, el registro ".($nlinea+1)." de ";
					$mensaje_contador_errores.=" un total de ".$array_contador_registros_buenos["AP"]." registros buenos para AP. ";
					$html_del_mensaje="";
					$html_del_mensaje.="<table>";
					$html_del_mensaje.="<tr>";
					$html_del_mensaje.="<td colspan=\'2\'>";
					$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="</tr>";
					$html_del_mensaje.="<tr>";
					$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
					$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
					$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_contador_errores."</div><div id=\'errores_bd_div\'></div>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="</tr>";
					$html_del_mensaje.="</table>";
					if(connection_aborted()==false)
					{
						echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
						ob_flush();
						flush();
					}
				}//fin verifica numero campos
				$nlinea++;
			}
			fclose($file_ap);
		}
		//FIN AP
		
		//AU
		if($es_valido_nombre_archivo_au && $bool_hubo_error_query==false
		   && (file_exists($ruta_archivo_au)==true && $rutaTemporal!=trim($ruta_archivo_au)))
		{
			/*
			 *abajo ya hay una parte donde elimina los rechazados pero despues de que sube los exitosos
			*/
			
			$lineas_del_archivo = count(file($ruta_archivo_au)); 
			$file_au = fopen($ruta_archivo_au, 'r') or exit("No se pudo abrir el archivo");
			
			$nlinea=0;
			while (!feof($file_au)) 
			{
				$linea_tmp = fgets($file_au);
				$linea= explode("\n", $linea_tmp)[0];
				$campos = explode(",", $linea);
				if(count($campos)==15)
				{
					$sql_exito="";
					$sql_exito.="INSERT INTO gioss_registros_validados_exito_rips_au_eapb ";
					$sql_exito.=" ( ";
					$sql_exito.="nombre_archivo ,";
					//ini campos archivo
					$sql_exito.="codigo_entidad_eapb ,";
					$sql_exito.="codigo_prestador_servcios_salud ,";
					$sql_exito.="numero_factura ,";					
					$sql_exito.="tipo_identificacion_usuario ,";
					$sql_exito.="numero_identificacion_usuario ,";
					$sql_exito.="fecha_ingreso ,";
					$sql_exito.="causa_externa ,";
					$sql_exito.="codigo_diagnostico_de_salida ,";
					$sql_exito.="codigo_diagnostico_relacionado_1 ,";
					$sql_exito.="codigo_diagnostico_relacionado_2 ,";
					$sql_exito.="codigo_diagnostico_relacionado_3 ,";
					$sql_exito.="destino_usuario_salida ,";
					$sql_exito.="estado_salida_usuario ,";
					$sql_exito.="diagnostico_causa_muerte ,";
					$sql_exito.="fecha_salida ,";
					//fin campos archivo
					$sql_exito.="fecha_validacion ,";
					$sql_exito.="hora_validacion ,";
					$sql_exito.="fecha_remision ,";
					$sql_exito.="fila_integer  ,";
					$sql_exito.="numero_remision, ";
					$sql_exito.="numero_secuencia, ";
					$sql_exito.="periodo_corte, ";
					$sql_exito.="estado_registro, ";
					$sql_exito.="dpto_mpio ";
					$sql_exito.=" ) ";
					$sql_exito.=" VALUES ";
					$sql_exito.=" ( ";							
					$sql_exito.=" '".$nombre_archivo."', ";	
					$sql_exito.=" '".procesar_mensaje2($campos[0])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[1])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[2])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[3])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[4])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[5])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[6])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[7])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[8])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[9])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[10])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[11])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[12])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[13])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[14])."', ";
					$sql_exito.=" '".$fecha_actual."', ";
					$sql_exito.=" '".$tiempo_actual."', ";
					$sql_exito.=" '".$date_remision_bd."', ";
					$sql_exito.=" '".$nlinea."', ";
					$sql_exito.=" '".$numero_de_remision."', ";
					$sql_exito.=" '".$numero_secuencia_actual."', ";
					$sql_exito.=" '".$periodo_rips_archivo."', ";
					$sql_exito.=" '".$estado_validado_registro."', ";
					$sql_exito.=" '".$departamento_filtro.$mpio_filtro_bd."' ";
					$sql_exito.=" ) ";
					$sql_exito.=" ; ";
					
					$error_bd="";
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_exito, $error_bd);
					if($error_bd!="")
					{
						$error_mostrar_bd.="ERROR AU EXITOSO ".$error_bd."<br>";
					}
					
					$mensaje_contador_errores="Se subio en la base de datos, el registro ".($nlinea+1)." de ";
					$mensaje_contador_errores.=" un total de ".$array_contador_registros_buenos["AU"]." registros buenos para AU. ";
					$html_del_mensaje="";
					$html_del_mensaje.="<table>";
					$html_del_mensaje.="<tr>";
					$html_del_mensaje.="<td colspan=\'2\'>";
					$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="</tr>";
					$html_del_mensaje.="<tr>";
					$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
					$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
					$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_contador_errores."</div><div id=\'errores_bd_div\'></div>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="</tr>";
					$html_del_mensaje.="</table>";
					if(connection_aborted()==false)
					{
						echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
						ob_flush();
						flush();
					}
				}//fin verifica numero campos
				$nlinea++;
			}
			fclose($file_au);
		}
		//FIN AU
		
		//AN
		if($es_valido_nombre_archivo_an && $bool_hubo_error_query==false
		   && (file_exists($ruta_archivo_an)==true && $rutaTemporal!=trim($ruta_archivo_an)))
		{
			/*
			 *abajo ya hay una parte donde elimina los rechazados pero despues de que sube los exitosos
			*/
			
			$lineas_del_archivo = count(file($ruta_archivo_an)); 
			$file_an = fopen($ruta_archivo_an, 'r') or exit("No se pudo abrir el archivo");
			
			$nlinea=0;
			while (!feof($file_an)) 
			{
				$linea_tmp = fgets($file_an);
				$linea= explode("\n", $linea_tmp)[0];
				$campos = explode(",", $linea);
				if(count($campos)==15)
				{
					$sql_exito="";
					$sql_exito.="INSERT INTO gioss_registros_validados_exito_rips_an_eapb ";
					$sql_exito.=" ( ";
					$sql_exito.="nombre_archivo ,";
					//ini campos archivo
					$sql_exito.="codigo_entidad_eapb ,";
					$sql_exito.="codigo_prestador_servicios_salud ,";
					$sql_exito.="numero_factura ,";					
					$sql_exito.="tipo_identificacion_madre ,";
					$sql_exito.="numero_identificacion_madre ,";
					$sql_exito.="fecha_ingreso ,";
					$sql_exito.="hora_ingreso ,";
					$sql_exito.="edad_gestacional ,";
					$sql_exito.="control_prenatal ,";
					$sql_exito.="sexo ,";
					$sql_exito.="peso  ,";
					$sql_exito.="codigo_diagnostico_recien_nacido ,";
					$sql_exito.="codigo_diagnostico_causa_muerte ,";
					$sql_exito.="fecha_muerte_recien_nacido ,";
					$sql_exito.="hora_muerte_recien_nacido ,";
					//fin campos archivo
					$sql_exito.="fecha_validacion ,";
					$sql_exito.="hora_validacion ,";
					$sql_exito.="fecha_remision ,";
					$sql_exito.="fila_integer  ,";
					$sql_exito.="numero_remision, ";
					$sql_exito.="numero_secuencia, ";
					$sql_exito.="periodo_corte, ";
					$sql_exito.="estado_registro, ";
					$sql_exito.="dpto_mpio ";
					$sql_exito.=" ) ";
					$sql_exito.=" VALUES ";
					$sql_exito.=" ( ";							
					$sql_exito.=" '".$nombre_archivo."', ";	
					$sql_exito.=" '".procesar_mensaje2($campos[0])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[1])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[2])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[3])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[4])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[5])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[6])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[7])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[8])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[9])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[10])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[11])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[12])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[13])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[14])."', ";
					$sql_exito.=" '".$fecha_actual."', ";
					$sql_exito.=" '".$tiempo_actual."', ";
					$sql_exito.=" '".$date_remision_bd."', ";
					$sql_exito.=" '".$nlinea."', ";
					$sql_exito.=" '".$numero_de_remision."', ";
					$sql_exito.=" '".$numero_secuencia_actual."', ";
					$sql_exito.=" '".$periodo_rips_archivo."', ";
					$sql_exito.=" '".$estado_validado_registro."', ";
					$sql_exito.=" '".$departamento_filtro.$mpio_filtro_bd."' ";
					$sql_exito.=" ) ";
					$sql_exito.=" ; ";
					
					$error_bd="";
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_exito, $error_bd);
					if($error_bd!="")
					{
						$error_mostrar_bd.="ERROR AN EXITOSO ".$error_bd."<br>";
					}
					
					$mensaje_contador_errores="Se subio en la base de datos, el registro ".($nlinea+1)." de ";
					$mensaje_contador_errores.=" un total de ".$array_contador_registros_buenos["AN"]." registros buenos para AN. ";
					$html_del_mensaje="";
					$html_del_mensaje.="<table>";
					$html_del_mensaje.="<tr>";
					$html_del_mensaje.="<td colspan=\'2\'>";
					$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="</tr>";
					$html_del_mensaje.="<tr>";
					$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
					$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
					$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_contador_errores."</div><div id=\'errores_bd_div\'></div>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="</tr>";
					$html_del_mensaje.="</table>";
					if(connection_aborted()==false)
					{
						echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
						ob_flush();
						flush();
					}
				}//fin verificacion numero campos
				$nlinea++;
			}
			fclose($file_an);
		}
		//FIN AN
		
		//AM
		if($es_valido_nombre_archivo_am  && $bool_hubo_error_query==false
		   && (file_exists($ruta_archivo_am)==true && $rutaTemporal!=trim($ruta_archivo_am)))
		{
			/*
			 *abajo ya hay una parte donde elimina los rechazados pero despues de que sube los exitosos
			*/
			
			$lineas_del_archivo = count(file($ruta_archivo_am)); 
			$file_am = fopen($ruta_archivo_am, 'r') or exit("No se pudo abrir el archivo");
			
			$nlinea=0;
			while (!feof($file_am)) 
			{
				$linea_tmp = fgets($file_am);
				$linea= explode("\n", $linea_tmp)[0];
				$campos = explode(",", $linea);
				if(count($campos)==15)
				{
					$sql_exito="";
					$sql_exito.="INSERT INTO gioss_registros_validados_exito_rips_am_eapb ";
					$sql_exito.=" ( ";
					$sql_exito.="nombre_archivo ,";
					//ini campos archivo
					$sql_exito.="codigo_entidad_eapb ,";
					$sql_exito.="codigo_prestador_servicios_salud ,";
					$sql_exito.="numero_factura ,";					
					$sql_exito.="tipo_identificacion_usuario ,";
					$sql_exito.="numero_identificacion_usuario ,";
					$sql_exito.="edad ,";
					$sql_exito.="unidad_medida_edad ,";
					$sql_exito.="nombre_generico_medicamento ,";
					$sql_exito.="tipo_medicamento ,";
					$sql_exito.="forma_farmaceutica ,";
					$sql_exito.="concetracion_medicamento  ,";
					$sql_exito.="unidad_medida_medicamento ,";
					$sql_exito.="numero_unidades ,";
					$sql_exito.="valor_unitario_medicamento ,";
					$sql_exito.="valor_total_medicamento ,";
					//fin campos archivo
					$sql_exito.="fecha_validacion ,";
					$sql_exito.="hora_validacion ,";
					$sql_exito.="fecha_remision ,";
					$sql_exito.="fila_integer  ,";
					$sql_exito.="numero_remision, ";
					$sql_exito.="numero_secuencia, ";
					$sql_exito.="periodo_corte, ";
					$sql_exito.="estado_registro, ";
					$sql_exito.="dpto_mpio ";
					$sql_exito.=" ) ";
					$sql_exito.=" VALUES ";
					$sql_exito.=" ( ";							
					$sql_exito.=" '".$nombre_archivo."', ";	
					$sql_exito.=" '".procesar_mensaje2($campos[0])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[1])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[2])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[3])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[4])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[5])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[6])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[7])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[8])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[9])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[10])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[11])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[12])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[13])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[14])."', ";
					$sql_exito.=" '".$fecha_actual."', ";
					$sql_exito.=" '".$tiempo_actual."', ";
					$sql_exito.=" '".$date_remision_bd."', ";
					$sql_exito.=" '".$nlinea."', ";
					$sql_exito.=" '".$numero_de_remision."', ";
					$sql_exito.=" '".$numero_secuencia_actual."', ";
					$sql_exito.=" '".$periodo_rips_archivo."', ";
					$sql_exito.=" '".$estado_validado_registro."', ";
					$sql_exito.=" '".$departamento_filtro.$mpio_filtro_bd."' ";
					$sql_exito.=" ) ";
					$sql_exito.=" ; ";
					
					$error_bd="";
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_exito, $error_bd);
					if($error_bd!="")
					{
						$error_mostrar_bd.=" ERROR AM EXITOSO ".$error_bd."<br>";
					}
					
					$mensaje_contador_errores="Se subio en la base de datos, el registro ".($nlinea+1)." de ";
					$mensaje_contador_errores.=" un total de ".$array_contador_registros_buenos["AM"]." registros buenos para AM. ";
					$html_del_mensaje="";
					$html_del_mensaje.="<table>";
					$html_del_mensaje.="<tr>";
					$html_del_mensaje.="<td colspan=\'2\'>";
					$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="</tr>";
					$html_del_mensaje.="<tr>";
					$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
					$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
					$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_contador_errores."</div><div id=\'errores_bd_div\'></div>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="</tr>";
					$html_del_mensaje.="</table>";
					if(connection_aborted()==false)
					{
						echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
						ob_flush();
						flush();
					}
				}//fin verificacion numero campos
				$nlinea++;
			}
			fclose($file_am);
		}
		//FIN AM
		
		//AV
		if($es_valido_nombre_archivo_av && $bool_hubo_error_query==false
		   && (file_exists($ruta_archivo_av)==true && $rutaTemporal!=trim($ruta_archivo_av)))
		{
			/*
			 *abajo ya hay una parte donde elimina los rechazados pero despues de que sube los exitosos
			*/
			$lineas_del_archivo = count(file($ruta_archivo_av)); 
			$file_av = fopen($ruta_archivo_av, 'r') or exit("No se pudo abrir el archivo");
			
			$nlinea=0;
			while (!feof($file_av)) 
			{
				$linea_tmp = fgets($file_av);
				$linea= explode("\n", $linea_tmp)[0];
				$campos = explode(",", $linea);
				if(count($campos)==16)
				{
					
					$sql_exito="";
					$sql_exito.="INSERT INTO gioss_registros_validados_exito_rips_av_eapb ";
					$sql_exito.=" ( ";
					$sql_exito.="nombre_archivo ,";
					//ini campos archivo
					$sql_exito.="codigo_entidad_eapb ,";
					$sql_exito.="year_actual ,";
					$sql_exito.="mes ,";
					$sql_exito.="pagado_consultas ,";					
					$sql_exito.="pagado_procedimientos_diagnosticos ,";
					$sql_exito.="pagado_procedimientos_quirurgicos ,";
					$sql_exito.="pagado_procedimientos_pyp ,";
					$sql_exito.="pagado_estancias ,";
					$sql_exito.="pagado_honorarios ,";
					$sql_exito.="pagado_derechos_sala ,";
					$sql_exito.="pagado_materiales_insumos ,";
					$sql_exito.="pagado_banco_sagre  ,";
					$sql_exito.="pagado_protesis_ortesis ,";
					$sql_exito.="pagado_medicamentos_pos ,";
					$sql_exito.="pagado_medicamentos_no_pos ,";
					$sql_exito.="pagado_traslado_pacientes ,";
					//fin campos archivo
					$sql_exito.="fecha_validacion ,";
					$sql_exito.="hora_validacion ,";
					$sql_exito.="fecha_remision ,";
					$sql_exito.="fila_integer  ,";
					$sql_exito.="numero_remision, ";
					$sql_exito.="numero_secuencia, ";
					$sql_exito.="periodo_corte, ";
					$sql_exito.="estado_registro, ";
					$sql_exito.="dpto_mpio ";
					$sql_exito.=" ) ";
					$sql_exito.=" VALUES ";
					$sql_exito.=" ( ";							
					$sql_exito.=" '".$nombre_archivo."', ";	
					$sql_exito.=" '".procesar_mensaje2($campos[0])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[1])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[2])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[3])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[4])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[5])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[6])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[7])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[8])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[9])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[10])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[11])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[12])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[13])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[14])."', ";
					$sql_exito.=" '".procesar_mensaje2($campos[15])."', ";
					$sql_exito.=" '".$fecha_actual."', ";
					$sql_exito.=" '".$tiempo_actual."', ";
					$sql_exito.=" '".$date_remision_bd."', ";
					$sql_exito.=" '".$nlinea."', ";
					$sql_exito.=" '".$numero_de_remision."', ";
					$sql_exito.=" '".$numero_secuencia_actual."', ";
					$sql_exito.=" '".$periodo_rips_archivo."', ";
					$sql_exito.=" '".$estado_validado_registro."', ";
					$sql_exito.=" '".$departamento_filtro.$mpio_filtro_bd."' ";
					$sql_exito.=" ) ";
					$sql_exito.=" ; ";
					
					$error_bd="";
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_exito, $error_bd);
					if($error_bd!="")
					{
						$error_mostrar_bd.=" ERROR AV EXITOSO: ".$error_bd."<br>";
					}
					
					$mensaje_contador_errores="Se subio en la base de datos, el registro ".($nlinea+1)." de ";
					$mensaje_contador_errores.=" un total de ".$array_contador_registros_buenos["AV"]." registros buenos para AV. ";
					$html_del_mensaje="";
					$html_del_mensaje.="<table>";
					$html_del_mensaje.="<tr>";
					$html_del_mensaje.="<td colspan=\'2\'>";
					$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="</tr>";
					$html_del_mensaje.="<tr>";
					$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
					$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
					$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_contador_errores."</div><div id=\'errores_bd_div\'></div>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="</tr>";
					$html_del_mensaje.="</table>";
					if(connection_aborted()==false)
					{
						echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
						ob_flush();
						flush();
					}
				}//fin verificacion numero campos
				$nlinea++;
			}
			fclose($file_av);
		}
		//FIN AV
		
		
		
		//TERMINA
		if($bool_hubo_error_query==false)
		{
			$sql_exito="";
			$sql_exito.="COMMIT;";
			
			$error_bd="";
			$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_exito, $error_bd);
			if($error_bd!="")
			{
				$error_mostrar_bd.=$error_bd."<br>";
			}
			else
			{
				//EN CASO DE QUE NO HAYA ERRORES AL INTRODUCIR LOSCAMPOS EN LA PARTE DE EXITO SE BORRA LOS QUE HUBIERON EN LA PARTE DE RECHAZO
				//BORRA LOS RECHAZADOS ANTERIORES
				$sql_query_delete="";
				$sql_query_delete.="DELETE FROM gioss_registros_validados_rechazados_rips_ct_eapb ";
				$sql_query_delete.=" WHERE fecha_remision='".$date_remision_bd."' ";
				$sql_query_delete.=" AND codigo_entidad_eapb='".$cod_entidad_reportadora_eapb_fact."' ";
				$sql_query_delete.=" AND dpto_mpio='".$departamento_filtro.$mpio_filtro_bd."' ";
				$sql_query_delete.=" AND nombre_archivo='".$nombre_archivo."' ; ";
				$error_bd_del="";
				$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_query_delete, $error_bd_del);
				
				
				$sql_query_delete="";
				$sql_query_delete.="DELETE FROM gioss_registros_validados_rechazados_rips_us_eapb ";
				$sql_query_delete.=" WHERE fecha_remision='".$date_remision_bd."' ";
				$sql_query_delete.=" AND codigo_entidad_eapb='".$cod_entidad_reportadora_eapb_fact."' ";
				$sql_query_delete.=" AND dpto_mpio='".$departamento_filtro.$mpio_filtro_bd."' ";
				$sql_query_delete.=" AND nombre_archivo='".$nombre_archivo."' ; ";
				$error_bd_del="";
				$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_query_delete, $error_bd_del);
				
				$sql_query_delete="";
				$sql_query_delete.="DELETE FROM gioss_registros_validados_rechazados_rips_ac_eapb ";
				$sql_query_delete.=" WHERE fecha_remision='".$date_remision_bd."' ";
				$sql_query_delete.=" AND codigo_entidad_eapb='".$cod_entidad_reportadora_eapb_fact."' ";
				$sql_query_delete.=" AND dpto_mpio='".$departamento_filtro.$mpio_filtro_bd."' ";
				$sql_query_delete.=" AND nombre_archivo='".$nombre_archivo."' ; ";
				$error_bd_del="";
				$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_query_delete, $error_bd_del);
				
				$sql_query_delete="";
				$sql_query_delete.="DELETE FROM gioss_registros_validados_rechazados_rips_ah_eapb ";
				$sql_query_delete.=" WHERE fecha_remision='".$date_remision_bd."' ";
				$sql_query_delete.=" AND codigo_entidad_eapb='".$cod_entidad_reportadora_eapb_fact."' ";
				$sql_query_delete.=" AND dpto_mpio='".$departamento_filtro.$mpio_filtro_bd."' ";
				$sql_query_delete.=" AND nombre_archivo='".$nombre_archivo."' ; ";
				$error_bd_del="";
				$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_query_delete, $error_bd_del);
				
				
				$sql_query_delete="";
				$sql_query_delete.="DELETE FROM gioss_registros_validados_rechazados_rips_ap_eapb ";
				$sql_query_delete.=" WHERE fecha_remision='".$date_remision_bd."' ";
				$sql_query_delete.=" AND codigo_entidad_eapb='".$cod_entidad_reportadora_eapb_fact."' ";
				$sql_query_delete.=" AND dpto_mpio='".$departamento_filtro.$mpio_filtro_bd."' ";
				$sql_query_delete.=" AND nombre_archivo='".$nombre_archivo."' ; ";
				$error_bd_del="";
				$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_query_delete, $error_bd_del);
				
				$sql_query_delete="";
				$sql_query_delete.="DELETE FROM gioss_registros_validados_rechazados_rips_au_eapb ";
				$sql_query_delete.=" WHERE fecha_remision='".$date_remision_bd."' ";
				$sql_query_delete.=" AND codigo_entidad_eapb='".$cod_entidad_reportadora_eapb_fact."' ";
				$sql_query_delete.=" AND dpto_mpio='".$departamento_filtro.$mpio_filtro_bd."' ";
				$sql_query_delete.=" AND nombre_archivo='".$nombre_archivo."' ; ";
				$error_bd_del="";
				$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_query_delete, $error_bd_del);
				
				$sql_query_delete="";
				$sql_query_delete.="DELETE FROM gioss_registros_validados_rechazados_rips_an_eapb ";
				$sql_query_delete.=" WHERE fecha_remision='".$date_remision_bd."' ";
				$sql_query_delete.=" AND codigo_entidad_eapb='".$cod_entidad_reportadora_eapb_fact."' ";
				$sql_query_delete.=" AND dpto_mpio='".$departamento_filtro.$mpio_filtro_bd."' ";
				$sql_query_delete.=" AND nombre_archivo='".$nombre_archivo."' ; ";
				$error_bd_del="";
				$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_query_delete, $error_bd_del);
				
				$sql_query_delete="";
				$sql_query_delete.="DELETE FROM gioss_registros_validados_rechazados_rips_am_eapb ";
				$sql_query_delete.=" WHERE fecha_remision='".$date_remision_bd."' ";
				$sql_query_delete.=" AND codigo_entidad_eapb='".$cod_entidad_reportadora_eapb_fact."' ";
				$sql_query_delete.=" AND dpto_mpio='".$departamento_filtro.$mpio_filtro_bd."' ";
				$sql_query_delete.=" AND nombre_archivo='".$nombre_archivo."' ; ";
				$error_bd_del="";
				$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_query_delete, $error_bd_del);
				
				
				$sql_query_delete="";
				$sql_query_delete.="DELETE FROM gioss_registros_validados_rechazados_rips_av_eapb ";
				$sql_query_delete.=" WHERE fecha_remision='".$date_remision_bd."' ";
				$sql_query_delete.=" AND codigo_entidad_eapb='".$cod_entidad_reportadora_eapb_fact."' ";
				$sql_query_delete.=" AND dpto_mpio='".$departamento_filtro.$mpio_filtro_bd."' ";
				$sql_query_delete.=" AND nombre_archivo='".$nombre_archivo."' ; ";
				$error_bd_del="";
				$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_query_delete, $error_bd_del);
				
				//FIN BORRA LOS RECHAZADOS ANTERIORES
				
				//finaliza delete en rechazados				
				
			}//fin else
		}//fin if termino query exitosamente hace commit
		else
		{
			$sql_exito="";
			$sql_exito.="ROLLBACK;";
			
			$error_bd="";
			$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_exito, $error_bd);
			if($error_bd!="")
			{
				$error_mostrar_bd.=$error_bd."<br>";
			}
		}//fin else hace rollback si hubo error
		
		//error de bd mostrar
		if($error_mostrar_bd!="")
		{
			$error_mostrar_bd_procesado = str_replace("á","a",$error_mostrar_bd);
			$error_mostrar_bd_procesado = str_replace("é","e",$error_mostrar_bd_procesado);
			$error_mostrar_bd_procesado = str_replace("í","i",$error_mostrar_bd_procesado);
			$error_mostrar_bd_procesado = str_replace("ó","o",$error_mostrar_bd_procesado);
			$error_mostrar_bd_procesado = str_replace("ú","u",$error_mostrar_bd_procesado);
			$error_mostrar_bd_procesado = str_replace("ñ","n",$error_mostrar_bd_procesado);
			$error_mostrar_bd_procesado = str_replace("Á","A",$error_mostrar_bd_procesado);
			$error_mostrar_bd_procesado = str_replace("É","E",$error_mostrar_bd_procesado);
			$error_mostrar_bd_procesado = str_replace("Í","I",$error_mostrar_bd_procesado);
			$error_mostrar_bd_procesado = str_replace("Ó","O",$error_mostrar_bd_procesado);
			$error_mostrar_bd_procesado = str_replace("Ú","U",$error_mostrar_bd_procesado);
			$error_mostrar_bd_procesado = str_replace("Ñ","N",$error_mostrar_bd_procesado);
			$error_mostrar_bd_procesado = str_replace(" "," ",$error_mostrar_bd_procesado);
			$error_mostrar_bd_procesado = str_replace("'"," ",$error_mostrar_bd_procesado);
			$error_mostrar_bd_procesado = str_replace("\n"," ",$error_mostrar_bd_procesado);
			$linea_res= alphanumericAndSpace($error_mostrar_bd_procesado);
			
			$error_mensaje.="ERROR EN SUBIR VALIDADOS CON EXITO A BD, ".$error_mostrar_bd_procesado;
		}//fin if error en bd
		
	}//fin if validados con exito subir a bd
	//FIN PARTE SUBIR VALIDADOS EN BD
	
	
	
	//PARTE CONSOLIDADO ESTADO VALIDACION RIPS
	$estado_validacion_rips=2;
	if($error_mensaje=="")
	{
		$estado_validacion_rips=1;
	}
	
	$query_id_info_prestador="";
	$query_id_info_prestador.="SELECT * FROM gios_prestador_servicios_salud WHERE cod_registro_especial_pss='".$cod_prestador."' ; ";
	$resultado_query_id_info_prestador=$coneccionBD->consultar2($query_id_info_prestador);
	
	$tipo_id_prestador="";
	$nit_prestador="";
	$codigo_depto_prestador="";
	$codigo_municipio_prestador="";
	if(count($resultado_query_id_info_prestador)>0)
	{
		$tipo_id_prestador=$resultado_query_id_info_prestador[0]["cod_tipo_identificacion"];
		$nit_prestador=$resultado_query_id_info_prestador[0]["num_tipo_identificacion"];
		$codigo_depto_prestador=$resultado_query_id_info_prestador[0]["cod_depto"];
		$codigo_municipio_prestador=$resultado_query_id_info_prestador[0]["cod_municipio"];
	}		
	
	
	
	$fecha_bd_array=explode("-",$date_remision_bd);
	$dia_bd=$fecha_bd_array[2];
	$mes_bd=$fecha_bd_array[1];
	$year_bd=$fecha_bd_array[0];
	
	$fecha_ini_periodo="";
	$fecha_fin_periodo="";
	
	if(intval($mes_bd)==1 || intval($mes_bd)==3 || intval($mes_bd)==5 || intval($mes_bd)==7 || intval($mes_bd)==8 || intval($mes_bd)==10 || intval($mes_bd)==12)
	{
		$fecha_ini_periodo=$year_bd."-".$mes_bd."-01";
		$fecha_fin_periodo=$year_bd."-".$mes_bd."-31";
	}
	
	if( intval($mes_bd)==4 || intval($mes_bd)==6 || intval($mes_bd)==9 || intval($mes_bd)==11 )
	{
		$fecha_ini_periodo=$year_bd."-".$mes_bd."-01";
		$fecha_fin_periodo=$year_bd."-".$mes_bd."-30";
	}
	
	if( intval($mes_bd)==2)
	{
		$fecha_ini_periodo=$year_bd."-".$mes_bd."-01";
		$fecha_fin_periodo=$year_bd."-".$mes_bd."-28";
	}
	
	$errores_bd_estado_validacion="";
	
		
	
	if(count($resultado_query_id_info_prestador)>0 && $verificacion_ya_se_valido_con_exito==false && $bool_fecha_del_ct_erronea==false)
	{	   
		$query_registrar_estado_validacion="";
		$query_registrar_estado_validacion.="INSERT INTO gioss_tabla_consolidacion_registros_validados_rips ";
		$query_registrar_estado_validacion.="(";
		$query_registrar_estado_validacion.="estado_validacion,";
		$query_registrar_estado_validacion.="fecha_validacion,";
		$query_registrar_estado_validacion.="numero_secuencia,";
		$query_registrar_estado_validacion.="nombre_archivo_control,";
		$query_registrar_estado_validacion.="fecha_remision_ct,";
		$query_registrar_estado_validacion.="tipo_identificacion_entidad_reportadora,";
		$query_registrar_estado_validacion.="numero_identificacion_entidad_reportadora,";
		$query_registrar_estado_validacion.="codigo_eapb,";
		$query_registrar_estado_validacion.="fecha_inicio_periodo,";
		$query_registrar_estado_validacion.="fecha_final_periodo,";	
		$query_registrar_estado_validacion.="codigo_entidad_reportadora,";
		$query_registrar_estado_validacion.="codigo_depto_prestador,";
		$query_registrar_estado_validacion.="codigo_municipio_prestador";
		$query_registrar_estado_validacion.=")";
		$query_registrar_estado_validacion.="VALUES";
		$query_registrar_estado_validacion.="(";
		$query_registrar_estado_validacion.="'".$estado_validacion_rips."',";
		$query_registrar_estado_validacion.="'".$fecha_actual."',";
		$query_registrar_estado_validacion.="'".$numero_secuencia_actual."',";
		$query_registrar_estado_validacion.="'CT".$numero_de_remision."',";
		$query_registrar_estado_validacion.="'".$date_remision_bd."',";
		$query_registrar_estado_validacion.="'".$tipo_id_prestador."',";
		$query_registrar_estado_validacion.="'".$nit_prestador."',";
		$query_registrar_estado_validacion.="'".$cod_eapb."',";
		$query_registrar_estado_validacion.="'$fecha_ini_periodo',";
		$query_registrar_estado_validacion.="'$fecha_fin_periodo',";
		$query_registrar_estado_validacion.="'".$cod_prestador."',";
		$query_registrar_estado_validacion.="'".$codigo_depto_prestador."',";
		$query_registrar_estado_validacion.="'".$codigo_municipio_prestador."'";
		$query_registrar_estado_validacion.=");";
		$error_bd_seq="";
		//$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($query_registrar_estado_validacion, $error_bd_seq);		
		if($error_bd_seq!="")
		{
			$errores_bd_estado_validacion.=$error_bd_seq."<br>";
			if(connection_aborted()==false)
			{
				echo "<script>alert('".procesar_mensaje($errores_bd_estado_validacion)."');</script>";
			}
		}
	}
	
	//FIN PARTE CONSOLIDADO ESTADO VALIDACION RIPS
	
	
	
	
	//PARTE SUBIR INCONSISTENCIAS ENCONTRADAS A LA BASE DE DATOS
	$exito_mensaje="";
	$mostrar_error_bd_inconsistencias="";
	$numero_total_errores=0;
	$cont_errores_rips=1;
	if($verificacion_ya_se_valido_con_exito==false && $bool_fecha_del_ct_erronea==false && $bool_se_esta_validando_en_este_momento==false)
	{
		$numero_total_errores=count(file($ruta_archivo_inconsistencias_rips));	
		$file_incons_leer_rips = new SplFileObject($ruta_archivo_inconsistencias_rips);
		$cont_lineas_errores=0;
		while ($cont_lineas_errores<$numero_total_errores) 
		{
			$file_incons_leer_rips->seek($cont_lineas_errores);
			$linea_tmp = $file_incons_leer_rips->current();
			$linea= explode("\n", $linea_tmp)[0];
			$campos = explode(",", $linea);
			if(count($campos)==6)
			{
				$array_codigos_detalle=explode(";;",$campos[2]);
				$cod_detalle_inconsistencia=$array_codigos_detalle[0];
				$detalle_inconsistencia=$array_codigos_detalle[1];
				$array_codigos_inconsistencias=explode("_",$cod_detalle_inconsistencia);
				
				$cod_tipo_inconsistencia=$array_codigos_inconsistencias[0];
				$cod_grupo_inconsistencia=$array_codigos_inconsistencias[1];
				$cod_detalle_inconsistencia_solo=$array_codigos_inconsistencias[2];
				
				$nom_archivo=$campos[3];
				$num_campo=$campos[4];
				$num_fila=$campos[5];
				
				$sql_insertar_inconsistencia_rips="";
				$sql_insertar_inconsistencia_rips.=" INSERT INTO gioss_reporte_inconsistencia_archivos_rips_eapb ";
				$sql_insertar_inconsistencia_rips.=" ( ";
				$sql_insertar_inconsistencia_rips.=" numero_orden, ";
				$sql_insertar_inconsistencia_rips.=" nombre_rips_comprimido, ";
				$sql_insertar_inconsistencia_rips.=" nombre_archivo_ct, ";
				$sql_insertar_inconsistencia_rips.=" cod_tipo_inconsitencia, ";
				$sql_insertar_inconsistencia_rips.=" nombre_tipo_inconsistencia, ";
				$sql_insertar_inconsistencia_rips.=" cod_grupo_inconsistencia, ";
				$sql_insertar_inconsistencia_rips.=" nombre_grupo_inconsistencia, ";
				$sql_insertar_inconsistencia_rips.=" cod_detalle_inconsistencia, ";
				$sql_insertar_inconsistencia_rips.=" detalle_inconsistencia, ";
				$sql_insertar_inconsistencia_rips.=" nombre_archivo_rips, ";
				$sql_insertar_inconsistencia_rips.=" numero_linea, ";
				$sql_insertar_inconsistencia_rips.=" numero_campo, ";
				$sql_insertar_inconsistencia_rips.=" dpto_mpio ";
				$sql_insertar_inconsistencia_rips.=" ) ";
				$sql_insertar_inconsistencia_rips.=" VALUES ";
				$sql_insertar_inconsistencia_rips.=" ( ";
				$sql_insertar_inconsistencia_rips.=" '".$numero_secuencia_actual."', ";
				$sql_insertar_inconsistencia_rips.=" '".$nombre_archivo."', ";
				$sql_insertar_inconsistencia_rips.=" 'CT".$numero_de_remision."', ";
				$sql_insertar_inconsistencia_rips.=" '".$cod_tipo_inconsistencia."', ";
				$sql_insertar_inconsistencia_rips.=" '".$array_tipo_validacion_rips[$cod_tipo_inconsistencia]."', ";
				$sql_insertar_inconsistencia_rips.=" '".$cod_grupo_inconsistencia."', ";
				$sql_insertar_inconsistencia_rips.=" '".$array_grupo_validacion_rips[$cod_grupo_inconsistencia]."', ";
				$sql_insertar_inconsistencia_rips.=" '".$cod_detalle_inconsistencia_solo."', ";
				$sql_insertar_inconsistencia_rips.=" '".$detalle_inconsistencia."', ";
				$sql_insertar_inconsistencia_rips.=" '".$nom_archivo."', ";
				$sql_insertar_inconsistencia_rips.=" '".$num_fila."', ";
				$sql_insertar_inconsistencia_rips.=" '".$num_campo."', ";
				$sql_insertar_inconsistencia_rips.=" '".$departamento_filtro.$mpio_filtro_bd."' ";
				$sql_insertar_inconsistencia_rips.=" ); ";
				$error_bd_ins="";
				$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_insertar_inconsistencia_rips, $error_bd_ins);
				if($error_bd_ins!="")
				{
					$mostrar_error_bd_inconsistencias.="ERROR AL REPORTAR INCONSISTENCIAS ".$cod_tipo_inconsistencia."_".$cod_detalle_inconsistencia_solo." : ".$error_bd_ins."<br>";
				}
				
				//PARTE INDICA  QUE SUBE ERRORES A BD
				$porcentaje_errores_subidos=0;
				if($numero_total_errores>0)
				{
					$porcentaje_errores_subidos=($cont_lineas_errores*100)/$numero_total_errores;
				}
				$mensaje_contador_errores="Subiendo errores encontrados al sistema ".$porcentaje_errores_subidos."% de 100%  ";
				$html_del_mensaje="";
				$html_del_mensaje.="<table>";
				$html_del_mensaje.="<tr>";
				$html_del_mensaje.="<td colspan=\'2\'>";
				$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
				$html_del_mensaje.="</td>";
				$html_del_mensaje.="</tr>";
				$html_del_mensaje.="<tr>";
				$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
				$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
				$html_del_mensaje.="</td>";
				$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
				$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_contador_errores."</div><div id=\'errores_bd_div\'></div>";
				$html_del_mensaje.="</td>";
				$html_del_mensaje.="</tr>";
				$html_del_mensaje.="</table>";
				if(connection_aborted()==false)
				{
					echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
					ob_flush();
					flush();
				}
				
				$cont_errores_rips++;
				//FIN PARTE INDICA  QUE SUBE ERRORES A BD
			}//fin if
			
			$cont_lineas_errores++;
		}//fin while
		
		//error de bd mostrar
		if($mostrar_error_bd_inconsistencias!="")
		{
			$error_mensaje.=procesar_mensaje3($mostrar_error_bd_inconsistencias);
			
		}//fin if error en bd
	}//fin if si no fue validado con exito anteriormente
	//FIN PARTE SUBIR INCONSISTENCIAS ENCONTRADAS ALA BASE DE DATOS
	
	//PARTE DATOS  A SUBIR PARA LA TABLA DE ESTADO DE INFORMACION RIPS
	
	$errores_bd_estado_informacion="";
			
	$numero_reg_ct=0;
	if(file_exists($ruta_archivo_ct)==true && $rutaTemporal!=trim($ruta_archivo_ct))
	{
		$numero_reg_ct=count(file($ruta_archivo_ct));
	}
	
	
	
	$numero_reg_us=0;
	if(file_exists($ruta_archivo_us)==true && $rutaTemporal!=trim($ruta_archivo_us))
	{
		$numero_reg_us=count(file($ruta_archivo_us));
	}
	
	$numero_reg_ac=0;
	if(file_exists($ruta_archivo_ac)==true && $rutaTemporal!=trim($ruta_archivo_ac))
	{
		$numero_reg_ac=count(file($ruta_archivo_ac));
	}
	
	$numero_reg_ah=0;
	if(file_exists($ruta_archivo_ah)==true && $rutaTemporal!=trim($ruta_archivo_ah))
	{
		$numero_reg_ah=count(file($ruta_archivo_ah));
	}
	
	
			
	$numero_reg_ap=0;
	if(file_exists($ruta_archivo_ap)==true && $rutaTemporal!=trim($ruta_archivo_ap))
	{
		$numero_reg_ap=count(file($ruta_archivo_ap));
	}
	
	$numero_reg_au=0;
	if(file_exists($ruta_archivo_au)==true && $rutaTemporal!=trim($ruta_archivo_au))
	{
		$numero_reg_au=count(file($ruta_archivo_au));
	}
	
	$numero_reg_an=0;
	if(file_exists($ruta_archivo_an)==true && $rutaTemporal!=trim($ruta_archivo_an))
	{
		$numero_reg_an=count(file($ruta_archivo_an));
	}
	
	$numero_reg_am=0;
	if(file_exists($ruta_archivo_am)==true && $rutaTemporal!=trim($ruta_archivo_am))
	{
		$numero_reg_am=count(file($ruta_archivo_am));
	}
	
	
	$numero_reg_av=0;
	if(file_exists($ruta_archivo_av)==true && $rutaTemporal!=trim($ruta_archivo_av))
	{
		$numero_reg_av=count(file($ruta_archivo_av));
	}
	
	$codigo_entidad_reportadora=$cod_prestador;
		
	$query_id_info_prestador="";
	$query_id_info_prestador.="SELECT * FROM gios_prestador_servicios_salud WHERE cod_registro_especial_pss='".$codigo_entidad_reportadora."' ; ";
	$resultado_query_id_info_prestador=$coneccionBD->consultar2($query_id_info_prestador);
	
	$nombre_entidad_reportadora="";
	$codigo_depto_entidad_reportadora="";
	$codigo_municipio_entidad_reportadora="";
	if(count($resultado_query_id_info_prestador)>0)
	{
		$codigo_depto_entidad_reportadora=$resultado_query_id_info_prestador[0]["cod_depto"];
		$codigo_municipio_entidad_reportadora=$resultado_query_id_info_prestador[0]["cod_municipio"];
		$nombre_entidad_reportadora=$resultado_query_id_info_prestador[0]["nom_entidad_prestadora"];
	}
	
	//se usa $cod_prestador porque puede se una eapb la entidad que reporta
	$query_info_eapb="SELECT * FROM gios_entidad_administradora WHERE cod_entidad_administradora='$codigo_entidad_reportadora' ;";
	$resultado_query_info_eapb=$coneccionBD->consultar2($query_info_eapb);
	if(count($resultado_query_info_eapb)>0)
	{
		$nombre_entidad_reportadora=$resultado_query_info_eapb[0]["nom_entidad_administradora"];
		$codigo_depto_entidad_reportadora=$resultado_query_id_info_prestador[0]["dpto"];
		$codigo_municipio_entidad_reportadora=$resultado_query_id_info_prestador[0]["mpio"];
	}
	
	$query_descripcion_estado_validacion="";
	$query_descripcion_estado_validacion.=" SELECT * FROM gioss_estado_validacion_archivos WHERE codigo_estado_validacion='$estado_validacion_rips' ; ";
	$resultado_query_descripcion_estado_validacion=$coneccionBD->consultar2($query_descripcion_estado_validacion);
	$descripcion_estado_validacion=$resultado_query_descripcion_estado_validacion[0]["descripcion_estado_validacion"];
	
	$query_nombre_dpt="SELECT * FROM gios_dpto WHERE cod_departamento='$departamento_filtro' ; ";
	$resultado_query_dpto=$coneccionBD->consultar2($query_nombre_dpt);
	$nombre_dpto="";
	if(count($resultado_query_dpto)>0)
	{
		$nombre_dpto=$resultado_query_dpto[0]["nom_departamento"];
	}
	
	$query_nombre_mpio="SELECT * FROM gios_mpio WHERE cod_municipio='$municipio_filtro' ; ";
	$resultado_query_mpio=$coneccionBD->consultar2($query_nombre_mpio);
	$nombre_mpio="";
	if(count($resultado_query_mpio)>0)
	{
		$nombre_mpio=$resultado_query_mpio[0]["nom_municipio"];
	}
	
	
		
	
	if( (count($resultado_query_id_info_prestador)>0 || count($resultado_query_info_eapb)>0 )
	   && count($resultado_query_dpto)>0
	   && count($resultado_query_mpio)>0
	   && $verificacion_ya_se_valido_con_exito==false
	   && $bool_fecha_del_ct_erronea==false
	   && $bool_se_esta_validando_en_este_momento==false
	   )
	{	   
		$query_registrar_estado_informacion="";
		$query_registrar_estado_informacion.="INSERT INTO gioss_tabla_estado_informacion_eapb_rips ";
		$query_registrar_estado_informacion.="(";
		$query_registrar_estado_informacion.="codigo_estado_informacion,";//1
		$query_registrar_estado_informacion.="nombre_estado_informacion,";//2		
		$query_registrar_estado_informacion.="fecha_remision,";//26
		$query_registrar_estado_informacion.="fecha_validacion,";//3
		$query_registrar_estado_informacion.="numero_secuencia,";//4
		$query_registrar_estado_informacion.="codigo_eapb_reportadora,";//5
		$query_registrar_estado_informacion.="nombre_eapb_reportadora,";//6
		$query_registrar_estado_informacion.="nombre_archivo,";//11
		$query_registrar_estado_informacion.="numero_registros_ct,";//13
		$query_registrar_estado_informacion.="numero_registros_us,";//14
		$query_registrar_estado_informacion.="numero_registros_ac,";//15
		$query_registrar_estado_informacion.="numero_registros_ap,";//16
		$query_registrar_estado_informacion.="numero_registros_au,";//17
		$query_registrar_estado_informacion.="numero_registros_ah,";//18
		$query_registrar_estado_informacion.="numero_registros_an,";//19
		$query_registrar_estado_informacion.="numero_registros_am,";//20
		$query_registrar_estado_informacion.="numero_registros_av,";//21
		$query_registrar_estado_informacion.="codigo_departamento,";//22
		$query_registrar_estado_informacion.="nombre_del_departamento,";//23
		$query_registrar_estado_informacion.="codigo_municipio,";//24
		$query_registrar_estado_informacion.="nombre_de_municipio";//25
		$query_registrar_estado_informacion.=")";
		$query_registrar_estado_informacion.="VALUES";
		$query_registrar_estado_informacion.="(";
		$query_registrar_estado_informacion.="'".$estado_validacion_rips."',";//1
		$query_registrar_estado_informacion.="'".$descripcion_estado_validacion."',";//2		
		$query_registrar_estado_informacion.="'".$date_remision_bd."', ";//26
		$query_registrar_estado_informacion.="'".$fecha_actual."',";//3
		$query_registrar_estado_informacion.="'".$numero_secuencia_actual."',";//4
		$query_registrar_estado_informacion.="'".$codigo_entidad_reportadora."',";//7
		$query_registrar_estado_informacion.="'".$nombre_entidad_reportadora."',";//8
		$query_registrar_estado_informacion.="'".$nombre_archivo."',";//11
		$query_registrar_estado_informacion.="'$numero_reg_ct',";//27	
		$query_registrar_estado_informacion.="'$numero_reg_us',";//14
		$query_registrar_estado_informacion.="'$numero_reg_ac',";//15
		$query_registrar_estado_informacion.="'$numero_reg_ap',";//16
		$query_registrar_estado_informacion.="'$numero_reg_au',";//17
		$query_registrar_estado_informacion.="'$numero_reg_ah',";//18
		$query_registrar_estado_informacion.="'$numero_reg_an',";//19
		$query_registrar_estado_informacion.="'$numero_reg_am',";//20
		$query_registrar_estado_informacion.="'$numero_reg_av',";//21
		$query_registrar_estado_informacion.="'".$departamento_filtro."',";//22
		$query_registrar_estado_informacion.="'".$nombre_dpto."',";//23
		$query_registrar_estado_informacion.="'".$municipio_filtro."',";//24
		$query_registrar_estado_informacion.="'".$nombre_mpio."'";//25
		$query_registrar_estado_informacion.=");";
		$error_bd_seq="";
		$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($query_registrar_estado_informacion, $error_bd_seq);		
		if($error_bd_seq!="")
		{
			$errores_bd_estado_informacion.=$error_bd_seq."<br>";
			if(connection_aborted()==false)
			{
				echo "<script>alert('Errores insertar tabla estado informacion: ".procesar_mensaje($error_bd_seq)."');</script>";
				echo "<script>alert('query: ".procesar_mensaje($query_registrar_estado_informacion)."');</script>";
			}
		}
	}
	
	//FIN PARTE DATOS  A SUBIR PARA LA TABLA DE ESTADO DE INFORMACION RIPS
	
	if($bool_se_esta_validando_en_este_momento==false)
	{
		$sql_query_delete_esta_siendo_procesado="";
		$sql_query_delete_esta_siendo_procesado.=" DELETE FROM gioss_rips_eapb_esta_validando_actualmente ";
		$sql_query_delete_esta_siendo_procesado.=" WHERE fecha_remision='".$date_remision_bd."' ";
		$sql_query_delete_esta_siendo_procesado.=" AND codigo_eapb_reportadora='".$cod_prestador."' ";
		$sql_query_delete_esta_siendo_procesado.=" AND nombre_archivo='".$nombre_archivo."'  ";
		$sql_query_delete_esta_siendo_procesado.=" AND codigo_departamento='".$departamento_filtro."'  ";
		$sql_query_delete_esta_siendo_procesado.=" AND codigo_municipio='".$municipio_filtro."'  ";
		$sql_query_delete_esta_siendo_procesado.=" ; ";
		$error_bd_del="";
		$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_query_delete_esta_siendo_procesado, $error_bd_del);
	}
	
	//PARTE BOTONES DESCARGA Y CONSULTA INCONSISTENCIAS SUBIDAS
		
	if($verificacion_ya_se_valido_con_exito==false && $bool_fecha_del_ct_erronea==false && $bool_se_esta_validando_en_este_momento==false)
	{
		//PARTE NUEVO REPORTE(NUEVA ESTRUCTURA)
		
		//POR NUMERO DE SECUENCIA
		$numero_secuencia_para_bd=$numero_secuencia_actual;
		$ruta_archivo_inconsistencias_traer="";
		$sql_numero_registros="";
		$sql_numero_registros.="SELECT count(*) as contador FROM gioss_reporte_inconsistencia_archivos_rips_eapb WHERE numero_orden='$numero_secuencia_para_bd';  ";
		$resultado_query_numero_registros=$coneccionBD->consultar2($sql_numero_registros);
		$numero_registros=intval($resultado_query_numero_registros[0]["contador"]);
		
		$nombre_vista_inconsistencias="vincosrips_".$nick_user."_".$tipo_id."_".$identificacion;
		
		$sql_vista_inconsistencias="";
		$sql_vista_inconsistencias.="CREATE OR REPLACE VIEW $nombre_vista_inconsistencias ";
		$sql_vista_inconsistencias.=" AS SELECT * FROM gioss_reporte_inconsistencia_archivos_rips_eapb WHERE numero_orden='$numero_secuencia_para_bd' order by nombre_archivo_rips,numero_linea, numero_campo ; ";
		$error_bd_seq="";
		$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_vista_inconsistencias, $error_bd_seq);
		
		
		
		$cont_linea=1;
		$contador_offset=0;
		$hubo_resultados=false;
		$puso_titulos=false;
		$nombre_archivo_inconsistencias="";
		while($contador_offset<$numero_registros)
		{
			$limite=2000;
			
			if( ($contador_offset+2000)>=$numero_registros)
			{
				$limite=2000+($numero_registros-$contador_offset);
			}
		
			//Ejemplo: SELECT *  FROM vista_inconsistencias_rips_".$nick_user."_".$tipo_id."_".$identificacion." WHERE numero_orden='29'  order by numero_linea, numero_campo limit 5 offset 0; 
			$sql_query_busqueda="";
			$sql_query_busqueda.="SELECT * FROM $nombre_vista_inconsistencias LIMIT $limite OFFSET $contador_offset;  ";
			$resultado_query_inconsistencias=array();
			$resultado_query_inconsistencias=$coneccionBD->consultar2($sql_query_busqueda);
		
			if(count($resultado_query_inconsistencias)>0)
			{
				
				$nombre_ct=$resultado_query_inconsistencias[0]["nombre_archivo_ct"];
				$numero_seq=$resultado_query_inconsistencias[0]["numero_orden"];
				$nombre_archivo_inconsistencias="inconsistencias-ct".$numero_de_remision."-".$numero_secuencia_actual."-".$string_tiempo_fecha.".csv";
				$ruta_archivo_inconsistencias_traer=$rutaTemporal.$nombre_archivo_inconsistencias;
				
				//PARTE INDICAR HUBO RESULTADOS UNA SOLA VEZ Y CREAR EL ARCHIVO EN BLANCO
				if($hubo_resultados==false)
				{
					$file_inconsistencias= fopen($ruta_archivo_inconsistencias_traer, "w") or die("fallo la creacion del archivo");
					fclose($file_inconsistencias);
				}
				
				$hubo_resultados=true;
				//FIN PARTE INDICAR HUBO RESULTADOS UNA SOLA VEZ Y CREAR EL ARCHIVO EN BLANCO
				
				
				$file_inconsistencias= fopen($ruta_archivo_inconsistencias_traer, "a") or die("fallo la creacion del archivo");
				
				if($puso_titulos==false)
				{
					$titulos="";
					$titulos.="consecutivo,numero de secuencia,codigo tipo inconsistencia,tipo inconsistencia,codigo grupo inconsistencia,grupo inconsistencia,";
					$titulos.="codigo detalle inconsistencia,detalle inconsistencia,nombre archivo rips, numero de linea, numero de campo";
					fwrite($file_inconsistencias, $titulos."\n");
					$puso_titulos=true;
				}
				
				foreach($resultado_query_inconsistencias as $resultado)
				{
					$linea_inconsistencia="";
					$linea_inconsistencia.=$cont_linea.",".$resultado["nombre_archivo_ct"].",".$resultado["cod_tipo_inconsitencia"].",";
					$linea_inconsistencia.=$resultado["nombre_tipo_inconsistencia"].",".$resultado["cod_grupo_inconsistencia"].",".$resultado["nombre_grupo_inconsistencia"].",";
					$linea_inconsistencia.=$resultado["cod_detalle_inconsistencia"].",".$resultado["detalle_inconsistencia"].",".$resultado["nombre_archivo_rips"].",";
					$linea_inconsistencia.=$resultado["numero_linea"].",".$resultado["numero_campo"];
					fwrite($file_inconsistencias, $linea_inconsistencia."\n");
					
					$mensaje_contador_errores="Escribiendo errores en el log los errores hallados, $cont_linea de $numero_registros ";
					$html_del_mensaje="";
					$html_del_mensaje.="<table>";
					$html_del_mensaje.="<tr>";
					$html_del_mensaje.="<td colspan=\'2\'>";
					$html_del_mensaje.="<p id=\'advertencia\'>".$mensaje_advertencia_tiempo."</p>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="</tr>";
					$html_del_mensaje.="<tr>";
					$html_del_mensaje.="<td style=\'width:50%;text-align:right;\'>";
					$html_del_mensaje.="<img id=\'loading\' src=\'../assets/imagenes/loader.gif\' alt=\'Cargando\' title=\'Espere\'>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="<td style=\'width:50%;text-align:left;\'>";
					$html_del_mensaje.="<div id=\'estado_validacion\'>".$mensaje_contador_errores."</div><div id=\'errores_bd_div\'></div>";
					$html_del_mensaje.="</td>";
					$html_del_mensaje.="</tr>";
					$html_del_mensaje.="</table>";
					if(connection_aborted()==false)
					{
						echo "<script>document.getElementById('mensaje').innerHTML='$html_del_mensaje';</script>";
						ob_flush();
						flush();
					}
					
					$cont_linea++;
				}
				fclose($file_inconsistencias);
			
				
				
				
			}//fin if hallo resultados
			
			$contador_offset+=2000;
		
		}//fin while
		
		//borrando vistas
		
		$sql_borrar_vistas="";
		$sql_borrar_vistas.=" DROP VIEW $nombre_vista_inconsistencias ; ";
		
		$error_bd="";		
		$bool_funciono=$coneccionBD->insertar_no_warning_get_error($sql_borrar_vistas, $error_bd);		
		if($error_bd!="")
		{
			$mensajes_error_bd.="error al borrar vistas ".$error_bd."<br>";
		}
		
		//fin borrando vistas
		//FIN PARTE NUEVO REPORTE
		
		if(connection_aborted()==false)
		{
			echo "<script>document.getElementById('mensaje').innerHTML=' Se ha terminado de revisar los archivos RIPS';</script>";
			ob_flush();
			flush();
		}
		
		//CREAR ZIP
		$archivos_a_comprimir=array();
		$archivos_a_comprimir[0]=$ruta_archivo_inconsistencias_traer;
		$ruta_zip=$rutaTemporal."inconsistencias_CT".$numero_de_remision."_".$numero_secuencia_actual."_".$string_tiempo_fecha.".zip";
		$result_zip = create_zip($archivos_a_comprimir,$ruta_zip);
		if(connection_aborted()==false)
		{
			echo "<script>var ruta_zip= '$ruta_zip'; </script>";		
			ob_flush();
			flush();
		}
		//FIN CREAR ZIP
		
		//ZIP Y BOTON ARCHIVOS RIPS FILTRADOS
		$archivos_filtrados_a_comprimir=glob($ruta_separado_localizacion."/*");
		$ruta_zip_filtrados=$ruta_separado_localizacion."/".$nombre_archivo.".zip";
		if($departamento_filtro!="none")
		{
			if($municipio_filtro!="none")
			{
				$ruta_zip_filtrados=$ruta_separado_localizacion."/".$nombre_archivo.$municipio_filtro.".zip";
			}
			else
			{
				$ruta_zip_filtrados=$ruta_separado_localizacion."/".$nombre_archivo.$departamento_filtro."000.zip";
			}
		}
		$resultado_zip_filtrados=create_zip($archivos_filtrados_a_comprimir,$ruta_zip_filtrados);
		if(connection_aborted()==false)
		{
			echo "<script>var ruta_filtrados_zip= '$ruta_zip_filtrados'; </script>";		
			ob_flush();
			flush();
		}
		if($se_genero_archivo_de_inconsistencias   && $verificacion_ya_se_valido_con_exito==false  )
		{
			$error_mensaje.=" <input type=\'button\' value=\'Descargar ZIP de los RIPS filtrados por localizacion\'  class=\'btn btn-success color_boton\' onclick=\"download_inconsistencias_campos(\'$ruta_zip_filtrados\');\"/> ";
		}
		else
		{
			$exito_mensaje.=" <input type=\'button\' value=\'Descargar ZIP de los RIPS filtrados por localizacion\'  class=\'btn btn-success color_boton\' onclick=\"download_inconsistencias_campos(\'$ruta_zip_filtrados\');\"/> ";
		}
		//FIN ZIP Y BOTON ARCHIVOS RIPS FILTRADOS
		
		//SI SE ESCRIBIERON ERRORES OBLIGATORIOS EN EL ARCHIVO DE INCONSISTENCIAS DE LOS RIPS
		if(
		   $se_genero_archivo_de_inconsistencias
		   && $verificacion_ya_se_valido_con_exito==false
		   )
		{
			$error_mensaje.=" <input type=\'button\' value=\'Descargar archivo de inconsistencias para RIPS\'  class=\'btn btn-success color_boton\' onclick=\"download_inconsistencias_campos(\'$ruta_zip\');\"/> ";
		}
		
		//SI HUBIERON ERRORES DE TIPO INFORMATIVOS
		if($error_mensaje=="")
		{
			//verifica si hay algo escrito en el archivo con formato viejo
			$hay_errores_escritos=false;
			$file_incons_leer_rips = fopen($ruta_archivo_inconsistencias_traer, "r") or die("fallo la apertura del archivo");
			while (!feof($file_incons_leer_rips)) 
			{
				$linea_tmp = fgets($file_incons_leer_rips);
				$linea= explode("\n", $linea_tmp)[0];
				$campos = explode(",", $linea);
				if($linea!="" && count($campos)>1)
				{
					$hay_errores_escritos=true;
				}
			}//fin while
			fclose($file_incons_leer_rips);
			//fin verifica
			
			if($hay_errores_escritos)
			{
				$exito_mensaje.=" <input type=\'button\' value=\'Descargar archivo de inconsistencias informativas para RIPS\'  class=\'btn btn-success color_boton\' onclick=\"download_inconsistencias_campos(\'$ruta_zip\');\"/> ";
			}
		}//fin if para mostrar descarga si hubo errores informativos en caso de haber sido cargado con exito
	}//fin if si no se valido con exito anteriormente
	//FIN PARTE BOTONES DESCARGA 

	//PARTE MENSAJES FINALES
	if($error_mensaje!="")
	{	if(connection_aborted()==false)
		{
			echo "<script>document.getElementById('div_mensaje_error').style.display='inline';</script>";
		}
		if($verificacion_ya_se_valido_con_exito==true)
		{
			if(connection_aborted()==false)
			{
				echo "<script>document.getElementById('titulo_mensaje_error').innerHTML='Error el archivo ya fue validado y cargado con exito:';</script>";
				echo "<script>document.getElementById('parrafo_mensaje_error').innerHTML='$error_mensaje <br> con el numero de secuencia $numero_secuencia_actual . <br> Puede verificar a traves de la opci&oacuten consulta-consulta validacion en el menu de informaci&oacuten obligatoria resoluci&oacuten 3374 RIPS';</script>";
			}
		}
		else if($bool_se_esta_validando_en_este_momento==true)
		{
			if(connection_aborted()==false)
			{
				echo "<script>document.getElementById('titulo_mensaje_error').innerHTML='Error el archivo se esta validando en este momento:';</script>";
				echo "<script>document.getElementById('parrafo_mensaje_error').innerHTML='$error_mensaje <br> ';</script>";
			}
		}
		else
		{
			if(connection_aborted()==false)
			{
				echo "<script>document.getElementById('parrafo_mensaje_error').innerHTML='$error_mensaje <br> con el numero de secuencia $numero_secuencia_actual';</script>";
			}
		}
		if(connection_aborted()==false)
		{
			ob_flush();
			flush();
		}
	}
	else
	{
		if(connection_aborted()==false)
		{
			echo "<script>document.getElementById('div_mensaje_exito').style.display='inline';</script>";	
			echo "<script>document.getElementById('parrafo_mensaje_exito').innerHTML='$exito_mensaje <br> con el numero de secuencia $numero_secuencia_actual';</script>";
			ob_flush();
			flush();
		}
	}
	
		
	//FIN PARTE MENSAJES FINALES
	
	//PARTE ENVIAR E-MAIL
	try
	{
		if($error_mensaje!="")
		{	
			//si hubo errores obligatorios
			
			// inicio envio de mail

			$mail = new PHPMailer();
			$mail->IsSMTP();
			$mail->SMTPAuth = true;
			$mail->SMTPSecure = "ssl";
			$mail->Host = "smtp.gmail.com";
			$mail->Port = 465;
			$mail->Username = "sistemagioss@gmail.com";
			$mail->Password = "gioss001";
			$mail->From = "sistemagioss@gmail.com";
			$mail->FromName = "GIOSS";
			$mail->Subject = "Inconsistencias RIPS 3374 ";
			$mail->AltBody = "Cordial saludo,\n El sistema ha determinado que su archivo(s)  contiene diversas inconsistencias,\n las cuales pueden ser: campos con información inconsistente, usuarios duplicados ó el uso de caracteres especiales(acentos,'Ñ' o É,Ý, ¥, ¤, ´)";
	    
			$mail->MsgHTML("Cordial saludo,\n El sistema ha determinado que su archivo(s)  contiene diversos errores y va con el numero de secuencia $numero_secuencia_actual.<strong>GIOSS</strong>.");
			if($verificacion_ya_se_valido_con_exito==false)
			{
				$mail->AddAttachment($ruta_zip);
			}
			$mail->AddAttachment($ruta_zip_filtrados);
			$mail->AddAddress($correo_electronico, "Destinatario");
	    
			$mail->IsHTML(true);
	    
			if (!$mail->Send()) 
			{
			    
			}
			else 
			{
				if(connection_aborted()==false)
				{
					echo "<script>alert('Se ha enviado una copia del log con las inconsistencias encontradas a su correo $correo_electronico')</script>";
				}
			}
	    
			//fin envio de mail
		}
		else if($bool_se_esta_validando_en_este_momento==false)
		{
			//si no hubo errores obligatorios
			
			// inicio envio de mail

			$mail = new PHPMailer();
			$mail->IsSMTP();
			$mail->SMTPAuth = true;
			$mail->SMTPSecure = "ssl";
			$mail->Host = "smtp.gmail.com";
			$mail->Port = 465;
			$mail->Username = "sistemagioss@gmail.com";
			$mail->Password = "gioss001";
			$mail->From = "sistemagioss@gmail.com";
			$mail->FromName = "GIOSS";
			$mail->Subject = "Carga archivos RIPS 3374 ";
			$mail->AltBody = "Cordial saludo,\n El sistema ha determinado que sus archivos fue validado con exito";
	    
			$mail->MsgHTML("Cordial saludo,\n El sistema ha determinado que sus archivo no contiene errores obligatorios y fue cargado con exito con el numero de secuencia $numero_secuencia_actual.<strong>GIOSS</strong>.");
			$mail->AddAttachment($ruta_zip);
			$mail->AddAddress($correo_electronico, "Destinatario");
			$mail->AddAttachment($ruta_zip_filtrados);
			$mail->IsHTML(true);
	    
			if (!$mail->Send()) 
			{
			    
			}
			else 
			{
				if(connection_aborted()==false)
				{
					echo "<script>alert('Se ha enviado una copia del log con las inconsistencias  informativas encontradas a su correo $correo_electronico')</script>";
				}
			}
	    
			//fin envio de mail
		}
	}
	catch(Exception $e)
	{
	}
	//FIN PARTE ENVIAR E-MAIL
}
//FIN PARTE QUE VALIDA LOAS ARCHIVOS RIPS EAPB

?>