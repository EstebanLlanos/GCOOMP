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

$mensaje="";
$mostrarResultado="none";

$mensaje_perm_estado_reg_recuperados="";
$mensaje_perm_est_final_reg_recuperado="";
$mensaje_perm_tablas_recuperadas="";
$cont_porcentaje_file=0;
$numero_total_registros_tablas=0;

//para el corrector registros despeus de unificar duplicado
$consecutivo_errores=0;


$selector_fechas_corte="";
$selector_fechas_corte.="<input type='hidden' id='fechas_corte' name='fechas_corte' >";


$query_periodos_rips="SELECT * FROM gioss_periodo_informacion ORDER BY cod_periodo_informacion;";
$resultado_query_periodos=$coneccionBD->consultar2_no_crea_cierra($query_periodos_rips);

$selector_periodo="";
$selector_periodo.="<select id='periodo' name='periodo' class='campo_azul' onchange='seleccionar_fecha_de_corte();'>";
$selector_periodo.="<option value='none'>Seleccione un periodo</option>";
foreach($resultado_query_periodos as $key=>$periodo_bd)
{
	if(intval($periodo_bd["cod_periodo_informacion"])>0 && intval($periodo_bd["cod_periodo_informacion"])<5)
	{
		$cod_periodo=$periodo_bd["cod_periodo_informacion"];
		$nombre_periodo=$periodo_bd["nom_periodo_informacion"];
		$fecha_permitida=str_replace("2013","AAAA",$periodo_bd["fec_final_periodo"]);
		$selector_periodo.="<option value='$cod_periodo'>Periodo $cod_periodo ($nombre_periodo $fecha_permitida)</option>";
	}
}
$selector_periodo.="<option value='5'>Todo el A&ntildeo</option>";
$selector_periodo.="</select>";



//consultar el tipo de entidad
$sql_query_tipo_entidad_asociada="SELECT * FROM gioss_entidades_sector_salud WHERE codigo_entidad='$entidad_salud_usuario_actual'; ";
$resultado_query_tipo_entidad=$coneccionBD->consultar2_no_crea_cierra($sql_query_tipo_entidad_asociada);
$tipo_entidad=$resultado_query_tipo_entidad[0]["cod_tipo_entidad"];
//fin consultar el tipo de entidad


//SELECTOR EAPB-ASOCIADA_ASOCIADA_USUARIO
$eapb="";
$eapb.="<div id='div_eapb'>";
$eapb.="<select id='eapb' name='eapb' class='campo_azul'>";
$eapb.="<option value='none'>Seleccione un EAPB</option>";

if(intval($perfil_usuario_actual)==5 && (intval($tipo_entidad)==6 || intval($tipo_entidad)==7 || intval($tipo_entidad)==8 || intval($tipo_entidad)==10) )
{
	$sql_consulta_eapb_usuario_prestador="SELECT ea.codigo_entidad,ea.nombre_de_la_entidad FROM ";
	$sql_consulta_eapb_usuario_prestador.=" gioss_relacion_entre_entidades_salud re INNER JOIN gioss_entidades_sector_salud ea ON (re.entidad2 = ea.codigo_entidad) ";
	$sql_consulta_eapb_usuario_prestador.=" WHERE re.entidad1='".$entidad_salud_usuario_actual."' ";
	$sql_consulta_eapb_usuario_prestador.=";";

	$resultado_query_eapb_usuario=$coneccionBD->consultar2_no_crea_cierra($sql_consulta_eapb_usuario_prestador);

	if(count($resultado_query_eapb_usuario)>0)
	{
		foreach($resultado_query_eapb_usuario as $eapb_prestador_usuario_res)
		{
			$eapb.="<option value='".$eapb_prestador_usuario_res['codigo_entidad']."' selected>".$eapb_prestador_usuario_res['codigo_entidad']." ".$eapb_prestador_usuario_res['nombre_de_la_entidad']."</option>";
		}
	}
}//fin if si el usuario es administrador y la entidad no es eapb, por lo tanto busca la eapb asociada a la entidad
else if((intval($perfil_usuario_actual)==3 || intval($perfil_usuario_actual)==4 || intval($perfil_usuario_actual)==5) && (intval($tipo_entidad)!=6 && intval($tipo_entidad)!=7 && intval($tipo_entidad)!=8 && intval($tipo_entidad)!=10) )
{
	$sql_query_tipo_entidad_asociada="SELECT * FROM gioss_entidades_sector_salud WHERE codigo_entidad='$entidad_salud_usuario_actual'; ";
	$resultado_query_tipo_entidad=$coneccionBD->consultar2_no_crea_cierra($sql_query_tipo_entidad_asociada);

	if(count($resultado_query_tipo_entidad)>0)
	{
		foreach($resultado_query_tipo_entidad as $eapb_entidad)
		{
			$eapb.="<option value='".$eapb_entidad['codigo_entidad']."' selected>".$eapb_entidad['codigo_entidad']." ".$eapb_entidad['nombre_de_la_entidad']."</option>";
		}
	}
}//fin else si el usuario es de perfil 3 o 4 y la entidad es de tipo eapb

$eapb.="<option value='all' selected>TODAS LAS EAPB ASOCIADAS</option>";

$eapb.="</select>";

//SELECTOR PRESTADOR-ASOCIADO-USUARIO
$prestador="";
$prestador.="<select id='prestador' name='prestador' class='campo_azul' onchange='consultar_eapb(\"".$tipo_id."\",\"".$identificacion."\");'>";
$prestador.="<option value='none'>Seleccione un prestador de salud</option>";


if((intval($perfil_usuario_actual)==5 || intval($perfil_usuario_actual)==4 || intval($perfil_usuario_actual)==3)
   && (intval($tipo_entidad)!=6 && intval($tipo_entidad)!=7 && intval($tipo_entidad)!=8 && intval($tipo_entidad)!=10)
   )
{
	//echo "<script>alert('entro_aqui $entidad_salud_usuario_actual');</script>";
	$sql_consulta_prestadores_asociados_eapb="SELECT ea.codigo_entidad,ea.nombre_de_la_entidad,ea.numero_identificacion FROM ";
	$sql_consulta_prestadores_asociados_eapb.=" gioss_relacion_entre_entidades_salud re INNER JOIN gioss_entidades_sector_salud ea ON (re.entidad1 = ea.codigo_entidad) ";
	$sql_consulta_prestadores_asociados_eapb.=" WHERE re.entidad2='".$entidad_salud_usuario_actual."' ORDER BY ea.nombre_de_la_entidad ";
	$resultado_query_prestadores_asociados_eapb=$coneccionBD->consultar2_no_crea_cierra($sql_consulta_prestadores_asociados_eapb);

	if(count($resultado_query_prestadores_asociados_eapb)>0)
	{
		foreach($resultado_query_prestadores_asociados_eapb as $prestador_asociado_eapb)
		{
			$prestador.="<option value='".$prestador_asociado_eapb['codigo_entidad']."' selected>".$prestador_asociado_eapb['codigo_entidad']." ".$prestador_asociado_eapb['nombre_de_la_entidad']." ".$prestador_asociado_eapb['numero_identificacion']."</option>";
		}
	}
}//si el tipo entidad es diferente de 6,7,8,10 aka eapb busca las entidades relacionadas a esta(aparece lista entidades asociadas sin importar tipo)
else if((intval($perfil_usuario_actual)==1 || intval($perfil_usuario_actual)==2 || intval($perfil_usuario_actual)==5)
	&& (intval($tipo_entidad)==6 || intval($tipo_entidad)==7 || intval($tipo_entidad)==8 || intval($tipo_entidad)==10)
	)
{
	$sql_query_tipo_entidad_asociada="SELECT * FROM gioss_entidades_sector_salud WHERE codigo_entidad='$entidad_salud_usuario_actual'; ";
	$resultado_query_tipo_entidad=$coneccionBD->consultar2_no_crea_cierra($sql_query_tipo_entidad_asociada);

	if(count($resultado_query_tipo_entidad)>0)
	{
		foreach($resultado_query_tipo_entidad as $eapb_entidad)
		{
			$prestador.="<option value='".$eapb_entidad['codigo_entidad']."' selected>".$eapb_entidad['codigo_entidad']." ".$eapb_entidad['nombre_de_la_entidad']." ".$eapb_entidad['numero_identificacion']."</option>";
		}
	}
}//fin else if en caso de que el perfil sea 1 o 2 y el tipo de la entidad sea igual a 6,7,8,10 aka ips prestador busca la infromacionr eferente a esta misma (aparece entidad asociada al usuario)

if(intval($tipo_entidad)==6 || intval($tipo_entidad)==7 || intval($tipo_entidad)==8 || intval($tipo_entidad)==10)
{	
	$prestador.="<option value='all' >TODAS LAS IPS ASOCIADAS</option>";
}
else
{	
	$prestador.="<option value='all' selected>TODAS LAS IPS ASOCIADAS</option>";
}

$prestador.="</select>";
//FIN PRESTADOR-ASOCIADO-USUARIO

$smarty->assign("mensaje_proceso", $mensaje, true);
$smarty->assign("mostrarResultado", $mostrarResultado, true);
$smarty->assign("nombre", $nombre, true);
$smarty->assign("menu", $menu, true);
$smarty->assign("campo_eapb", $eapb, true);
$smarty->assign("campo_prestador", $prestador, true);
$smarty->assign("campo_periodo", $selector_periodo, true);
$smarty->assign("campo_fechas_corte", $selector_fechas_corte, true);
$smarty->display('cons_pobl_riesgo.html.tpl');

//INSERT INTO gios_menus_opciones_sistema(id_principal,id_padre,nombre_opcion,descripcion_ayuda,tiene_submenus,ruta_interfaz,prioridad_jerarquica) VALUES ('212','10','Riesgo Población','',FALSE,'..|consulta_poblacion_riesgo|cons_pobl_riesgo.php','33.02');

//INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('212','5');
//INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('212','4');
//INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('212','2');


if(isset($_POST["year_de_corte"])
   && isset($_POST['eapb'])
   && isset($_POST['riesgo_poblacion'])
   && $_POST['riesgo_poblacion']!="none"
   && $_POST['eapb']!="none"
   && $_POST['prestador']!="none"
   && $_POST["year_de_corte"]!=""
   && ctype_digit($_POST["year_de_corte"])
   )
{
	
	
	$year=$_POST['year_de_corte'];
	$mes_dia=$_POST['fechas_corte'];
	$fecha_de_corte=$year."-".$mes_dia;
	$periodo=$_POST['periodo'];
	
	$tipo_tiempo_periodo=$_POST['tipo_tiempo_periodo'];
	
	$cod_prestador = $_POST['prestador'];
	$cod_eapb=$_POST['eapb'];
	
	$sexo=$_POST['sexo'];
	$regimen=$_POST['regimen'];
	
	$riesgo_poblacion_a_consultar_codigo=0;
	
	if(ctype_digit($_POST['riesgo_poblacion']))
	{
		$riesgo_poblacion_a_consultar_codigo=intval($_POST['riesgo_poblacion']);
	}
	else
	{
		$riesgo_poblacion_a_consultar_codigo=999;
	}
		
	if(($riesgo_poblacion_a_consultar_codigo<1 || $riesgo_poblacion_a_consultar_codigo>27)
	   && $riesgo_poblacion_a_consultar_codigo!=999
	   )
	{
		$riesgo_poblacion_a_consultar_codigo=999;
	}
	
	$nombre_tabla_riesgo_poblacion_a_consultar="";
	
	$array_nombres_tabla_riesgo_poblacion=array();
	$array_nombres_tabla_riesgo_poblacion[0]="no_use_start_from_1";
	$array_nombres_tabla_riesgo_poblacion[1]="gioss_poblacion_riesgo_partos_res4505_pyp";
	$array_nombres_tabla_riesgo_poblacion[2]="gioss_poblacion_riesgo_cancer_cervix_res4505_pyp";
	$array_nombres_tabla_riesgo_poblacion[3]="gioss_poblacion_riesgo_adulto_res4505_pyp";
	$array_nombres_tabla_riesgo_poblacion[4]="gioss_poblacion_riesgo_cancer_seno_res4505_pyp";
	$array_nombres_tabla_riesgo_poblacion[5]="gioss_poblacion_riesgo_victima_enfermedad_mental_res4505_pyp";
	$array_nombres_tabla_riesgo_poblacion[6]="gioss_poblacion_riesgo_infeccion_trasmision_sexual_res4505_pyp";
	$array_nombres_tabla_riesgo_poblacion[7]="gioss_poblacion_riesgo_joven_res4505_pyp";
	$array_nombres_tabla_riesgo_poblacion[8]="gioss_poblacion_riesgo_lepra_res4505_pyp";
	$array_nombres_tabla_riesgo_poblacion[9]="gioss_poblacion_riesgo_vacunacion_res4505_pyp";
	$array_nombres_tabla_riesgo_poblacion[10]="gioss_poblacion_riesgo_obesidad_desnutricion_res4505_pyp";
	$array_nombres_tabla_riesgo_poblacion[11]="gioss_poblacion_riesgo_gestacion_res4505_pyp";
	$array_nombres_tabla_riesgo_poblacion[12]="gioss_poblacion_riesgo_victima_maltrato_res4505_pyp";
	$array_nombres_tabla_riesgo_poblacion[13]="gioss_poblacion_riesgo_violencia_sexual_res4505_pyp";
	$array_nombres_tabla_riesgo_poblacion[14]="gioss_poblacion_riesgo_menor_10anos_res4505_pyp";
	$array_nombres_tabla_riesgo_poblacion[15]="gioss_poblacion_riesgo_odontologico_res4505_pyp";
	$array_nombres_tabla_riesgo_poblacion[16]="gioss_poblacion_riesgo_sintomatico_respiratorio_res4505_pyp";
	$array_nombres_tabla_riesgo_poblacion[17]="gioss_poblacion_riesgo_edad_gestacional_nacer_res4505_pyp";
	$array_nombres_tabla_riesgo_poblacion[18]="gioss_poblacion_riesgo_enfermedad_renal_res4505_pyp";
	$array_nombres_tabla_riesgo_poblacion[19]="gioss_poblacion_riesgo_enfermedad_leishmaniasis_res4505_pyp";
	$array_nombres_tabla_riesgo_poblacion[20]="gioss_poblacion_riesgo_control_recien_nacido_res4505_pyp";
	$array_nombres_tabla_riesgo_poblacion[21]="gioss_poblacion_riesgo_enfermedad_anemica_res4505_pyp";
	$array_nombres_tabla_riesgo_poblacion[22]="gioss_poblacion_riesgo_problemas_vision_res4505_pyp";
	$array_nombres_tabla_riesgo_poblacion[23]="gioss_poblacion_riesgo_planificacion_familiar_res4505_pyp";
	$array_nombres_tabla_riesgo_poblacion[24]="gioss_poblacion_riesgo_enfermedad_diabetica_res4505_pyp";
	$array_nombres_tabla_riesgo_poblacion[25]="gioss_poblacion_riesgo_hipotiroidismo_congenito_res4505_pyp";
	$array_nombres_tabla_riesgo_poblacion[26]="gioss_poblacion_riesgo_enfermedad_colesterol_res4505_pyp";
	$array_nombres_tabla_riesgo_poblacion[27]="gioss_poblacion_riesgo_atencion_por_psicologia_res4505_pyp";
	
	$array_nombres_alternativos_para_los_usuarios=array();
	$array_nombres_alternativos_para_los_usuarios[0]="no use start from 1";
	$array_nombres_alternativos_para_los_usuarios[1]="poblacion riesgo partos";
	$array_nombres_alternativos_para_los_usuarios[2]="poblacion riesgo cancer cervix";
	$array_nombres_alternativos_para_los_usuarios[3]="poblacion riesgo adulto";
	$array_nombres_alternativos_para_los_usuarios[4]="poblacion riesgo cancer seno";
	$array_nombres_alternativos_para_los_usuarios[5]="poblacion riesgo enfermedad mental";
	$array_nombres_alternativos_para_los_usuarios[6]="poblacion riesgo infeccion trasmision sexual";
	$array_nombres_alternativos_para_los_usuarios[7]="poblacion riesgo joven";
	$array_nombres_alternativos_para_los_usuarios[8]="poblacion riesgo lepra";
	$array_nombres_alternativos_para_los_usuarios[9]="poblacion riesgo vacunacion";
	$array_nombres_alternativos_para_los_usuarios[10]="poblacion riesgo obesidad desnutricion";
	$array_nombres_alternativos_para_los_usuarios[11]="poblacion riesgo gestacion";
	$array_nombres_alternativos_para_los_usuarios[12]="poblacion riesgo victima maltrato";
	$array_nombres_alternativos_para_los_usuarios[13]="poblacion riesgo violencia sexual";
	$array_nombres_alternativos_para_los_usuarios[14]="poblacion riesgo menor 10anos";
	$array_nombres_alternativos_para_los_usuarios[15]="poblacion riesgo odontologico";
	$array_nombres_alternativos_para_los_usuarios[16]="poblacion riesgo sintomatico respiratorio";
	$array_nombres_alternativos_para_los_usuarios[17]="poblacion riesgo edad gestacional nacer";
	$array_nombres_alternativos_para_los_usuarios[18]="poblacion riesgo enfermedad renal";
	$array_nombres_alternativos_para_los_usuarios[19]="poblacion riesgo enfermedad leishmaniasis";
	$array_nombres_alternativos_para_los_usuarios[20]="poblacion riesgo control recien nacido";
	$array_nombres_alternativos_para_los_usuarios[21]="poblacion riesgo enfermedad anemica";
	$array_nombres_alternativos_para_los_usuarios[22]="poblacion riesgo problemas vision";
	$array_nombres_alternativos_para_los_usuarios[23]="poblacion riesgo planificacion familiar";
	$array_nombres_alternativos_para_los_usuarios[24]="poblacion riesgo enfermedad diabetica";
	$array_nombres_alternativos_para_los_usuarios[25]="poblacion riesgo hipotiroidismo congenito";
	$array_nombres_alternativos_para_los_usuarios[26]="poblacion riesgo enfermedad colesterol";
	$array_nombres_alternativos_para_los_usuarios[27]="poblacion riesgo atencion por psicologia";
	
	//parte pre-reasigna valores a los input
	if(connection_aborted()==false)
	{
		$html_valores_input="";
		$html_valores_input.="<script>
		document.getElementById('year_de_corte').value='$year';
		escribiendo_year_corte();
		</script>";
		echo $html_valores_input;
		ob_flush();
		flush();
		$html_valores_input="";
		$html_valores_input.="<script>
		document.getElementById('tipo_tiempo_periodo').value='$tipo_tiempo_periodo';
		cambio_tipo_tiempo_periodo();
		</script>";
		echo $html_valores_input;
		ob_flush();
		flush();
		$html_valores_input="";
		$html_valores_input.="<script>
		document.getElementById('fechas_corte').value='$mes_dia';
		document.getElementById('periodo').value='$periodo';
		document.getElementById('sexo').value='$sexo';
		document.getElementById('regimen').value='$regimen';
		document.getElementById('prestador').value='$cod_prestador';
		document.getElementById('eapb').value='$cod_eapb';
		document.getElementById('riesgo_poblacion').value='$riesgo_poblacion_a_consultar_codigo';
		</script>";
		echo $html_valores_input;
		ob_flush();
		flush();
		
		//necesita este alert para actualizar el dato de periodo mas abajo
		echo "<script>alert('Inicia el proceso de Consulta');</script>";
	}//fin if
	//fin parte pre-reasigna valores a los input
	
	$array_rutas_archivos_generados=array();
	date_default_timezone_set ("America/Bogota");
	$fecha_actual = date('Y-m-d');
	$tiempo_actual = date('H:i:s');
	$fecha_para_archivo= date('Y-m-d-H-i-s');
	
	$fecha_y_hora_para_view=str_replace(":","",$tiempo_actual).str_replace("-","",$fecha_actual);
	//$fecha_y_hora_para_view=substr($fecha_y_hora_para_view,0,4);
	
	$fecha_ini_bd ="";
	$fecha_fin_bd ="";
	$fecha_de_corte_periodo="";
	
	$mensajes_error_bd="";
	$resultadoDefinitivo="";
	
	//PERIODOS PYP
	if($tipo_tiempo_periodo=="trimestral")
	{
		if(intval($periodo)==1)
		{
		   $fecha_ini_bd="01/01/".$year;
		   $fecha_fin_bd="03/31/".$year;
		   $fecha_de_corte_periodo="03/31/".$year;
		}
		if(intval($periodo)==2)
		{
		   $fecha_ini_bd="04/01/".$year;
		   $fecha_fin_bd="06/30/".$year;
		   $fecha_de_corte_periodo="06/30/".$year;
		}
		if(intval($periodo)==3)
		{
		   $fecha_ini_bd="07/01/".$year;
		   $fecha_fin_bd="09/30/".$year;
		   $fecha_de_corte_periodo="09/30/".$year;
		}
		if(intval($periodo)==4)
		{
		   $fecha_ini_bd="10/01/".$year;
		   $fecha_fin_bd="12/31/".$year;
		   $fecha_de_corte_periodo="12/31/".$year;
		}
		if(intval($periodo)==5)
		{
		   $fecha_ini_bd="01/01/".$year;
		   $fecha_fin_bd="12/31/".$year;
		   $fecha_de_corte_periodo="12/31/".$year;
		}
	}//fin if trimestral
	else if($tipo_tiempo_periodo=="mensual")
	{
		if(intval($periodo)==1)
		{
		   $fecha_ini_bd="01/01/".$year;
		   $fecha_fin_bd="01/31/".$year;
		   $fecha_de_corte_periodo="01/31/".$year;
		}
		if(intval($periodo)==2)
		{
		   $fecha_ini_bd="02/01/".$year;
		   $fecha_fin_bd="02/28/".$year;
		   $fecha_de_corte_periodo="02/28/".$year;
		}
		if(intval($periodo)==3)
		{
		   $fecha_ini_bd="03/01/".$year;
		   $fecha_fin_bd="03/31/".$year;
		   $fecha_de_corte_periodo="03/31/".$year;
		}
		if(intval($periodo)==4)
		{
		   $fecha_ini_bd="04/01/".$year;
		   $fecha_fin_bd="04/30/".$year;
		   $fecha_de_corte_periodo="04/30/".$year;
		}
		if(intval($periodo)==5)
		{
		   $fecha_ini_bd="05/01/".$year;
		   $fecha_fin_bd="05/31/".$year;
		   $fecha_de_corte_periodo="05/31/".$year;
		}
		if(intval($periodo)==6)
		{
		   $fecha_ini_bd="06/01/".$year;
		   $fecha_fin_bd="06/30/".$year;
		   $fecha_de_corte_periodo="06/30/".$year;
		}
		if(intval($periodo)==7)
		{
		   $fecha_ini_bd="07/01/".$year;
		   $fecha_fin_bd="07/31/".$year;
		   $fecha_de_corte_periodo="07/31/".$year;
		}
		if(intval($periodo)==8)
		{
		   $fecha_ini_bd="08/01/".$year;
		   $fecha_fin_bd="08/31/".$year;
		   $fecha_de_corte_periodo="08/31/".$year;
		}
		if(intval($periodo)==9)
		{
		   $fecha_ini_bd="09/01/".$year;
		   $fecha_fin_bd="09/30/".$year;
		   $fecha_de_corte_periodo="09/30/".$year;
		}
		if(intval($periodo)==10)
		{
		   $fecha_ini_bd="10/01/".$year;
		   $fecha_fin_bd="10/31/".$year;
		   $fecha_de_corte_periodo="10/31/".$year;
		}
		if(intval($periodo)==11)
		{
		   $fecha_ini_bd="11/01/".$year;
		   $fecha_fin_bd="11/30/".$year;
		   $fecha_de_corte_periodo="11/30/".$year;
		}
		if(intval($periodo)==12)
		{
		   $fecha_ini_bd="12/01/".$year;
		   $fecha_fin_bd="12/31/".$year;
		   $fecha_de_corte_periodo="12/31/".$year;
		}
		if(intval($periodo)==13)
		{
		   $fecha_ini_bd="01/01/".$year;
		   $fecha_fin_bd="12/31/".$year;
		   $fecha_de_corte_periodo="12/31/".$year;
		}
	}//fin if mensual
	//FIN PERIODOS PYP
	$array_fecha_de_corte_simple=explode("/",$fecha_de_corte_periodo);
	$fecha_de_corte_simple=$array_fecha_de_corte_simple[2].$array_fecha_de_corte_simple[0].$array_fecha_de_corte_simple[1];
	
	//echo "<script>alert(\"".$fecha_ini_bd." ".$fecha_fin_bd."\");</script>";
	
	$array_fibd=explode("/",$fecha_ini_bd);
	$fecha_ini_bd=$array_fibd[2]."-".$array_fibd[0]."-".$array_fibd[1];
	
	$array_ffbd=explode("/",$fecha_fin_bd);
	$fecha_fin_bd=$array_ffbd[2]."-".$array_ffbd[0]."-".$array_ffbd[1];
	
	$array_fcbd=explode("/",$fecha_de_corte_periodo);
	$fecha_corte_bd=$array_fcbd[2]."-".$array_fcbd[0]."-".$array_fcbd[1];
	
	//echo "<script>alert(\"".$fecha_ini_bd." ".$fecha_fin_bd."\");</script>";
	
	$string_periodo= "".$periodo;
	
	if(strlen($string_periodo)!=2)
	{
		$string_periodo="0".$string_periodo;
	}
	
	$nombre_archivo_para_riesgo_poblacion_zip="info_riesgo_poblacion_".$fecha_para_archivo;
	
	//crea directorio para evitar que se descarguen archivos pasados
	$rutaTemporal = '../TEMPORALES/';
	$tiempo_actual_string=explode(":",$tiempo_actual)[0].explode(":",$tiempo_actual)[1].explode(":",$tiempo_actual)[2];
	if(!file_exists($rutaTemporal.$nombre_archivo_para_riesgo_poblacion_zip.$tiempo_actual_string))
	{
		mkdir($rutaTemporal.$nombre_archivo_para_riesgo_poblacion_zip.$tiempo_actual_string, 0700);
	}
	else
	{
		$files_to_erase = glob($rutaTemporal.$nombre_archivo_para_riesgo_poblacion_zip.$tiempo_actual_string."/*"); // get all file names
		foreach($files_to_erase as $file_to_be_erased)
		{ // iterate files
		  if(is_file($file_to_be_erased))
		  {
		    unlink($file_to_be_erased); // delete file
		  }
		}
	}
	$rutaTemporal=$rutaTemporal.$nombre_archivo_para_riesgo_poblacion_zip.$tiempo_actual_string."/";
	//fin crea directorio para evitar que se descarguen archivos pasados
	
	//TABLA RESUMEN
	$style_titulos="";
	$style_titulos.=" style=\"color:white;text-shadow:2px 2px 8px #d9d9d9;\" ";
	
	$cuadro_resumen_estado_cons_riesgo_poblacion_i="";
	$cuadro_resumen_estado_cons_riesgo_poblacion_i.="<table style=text-align:center;width:60%;left:18%;border-style:solid;border-width:5px;position:relative; id=tabla_estado_riego_poblacion>";
	//fila titulos
	$cuadro_resumen_estado_cons_riesgo_poblacion_i.="<tr style=\"background-color:black;\">";
	$cuadro_resumen_estado_cons_riesgo_poblacion_i.="<th style=text-align:left;><span $style_titulos >Nombre Riesgo Poblaci&oacuten:</span></th>";
	$cuadro_resumen_estado_cons_riesgo_poblacion_i.="<th style=text-align:left;><span $style_titulos >Estado:</span></th>";
	$cuadro_resumen_estado_cons_riesgo_poblacion_i.="<th style=text-align:left;><span $style_titulos >Numero registros hasta el momento:</span></th>";
	$cuadro_resumen_estado_cons_riesgo_poblacion_i.="<th style=text-align:left;><span $style_titulos >Numero registros totales:</span></th>";
	$cuadro_resumen_estado_cons_riesgo_poblacion_i.="<th style=text-align:left;><span $style_titulos >Porcentaje:</span></th>";
	$cuadro_resumen_estado_cons_riesgo_poblacion_i.="</tr>";
	//fin fila titulos
	$cuadro_resumen_estado_cons_riesgo_poblacion_m="";
	$cuadro_resumen_estado_cons_riesgo_poblacion_f="";
	$cuadro_resumen_estado_cons_riesgo_poblacion_f.="</table><br>";
	//FIN TABLA RESUMEN
	
	//inicia en 1 ya que la posicion 0 no hay tabla valida
	//sino un placeholder
	$contador_tabla_riesgo_poblacion_actual=1;
	
	while($contador_tabla_riesgo_poblacion_actual<count($array_nombres_tabla_riesgo_poblacion))
	{
		//si el codigo de riesgo poblacion que indica la tabla no es 999 se le
		//asigna al contador el valor de riesgo poblacion
		if($riesgo_poblacion_a_consultar_codigo!=999)
		{
			$contador_tabla_riesgo_poblacion_actual=$riesgo_poblacion_a_consultar_codigo;
		}
		
		$nombre_tabla_riesgo_poblacion_a_consultar=$array_nombres_tabla_riesgo_poblacion[$contador_tabla_riesgo_poblacion_actual];
		
		$nombre_alternativo=$array_nombres_alternativos_para_los_usuarios[$contador_tabla_riesgo_poblacion_actual];
		
		//CREA ARCHIVO PARA LOS REGISTROS DEFINITIVOS
		$ruta_escribir_archivo="";
		$ruta_escribir_archivo=$rutaTemporal.str_replace(" ","_",$array_nombres_alternativos_para_los_usuarios[$contador_tabla_riesgo_poblacion_actual])."_".$fecha_para_archivo.".txt";
		$riesgo_poblacion_file= fopen($ruta_escribir_archivo, "w") or die("fallo la creacion del archivo");
		fclose($riesgo_poblacion_file);
		//FIN CREA ARCHIVO PARA LOS REGISTROS DEFINITIVOS
		
		
		$NOMBRE_VISTA_RIESGO_POBLACION="riesgopob4505_".$contador_tabla_riesgo_poblacion_actual.$nick_user.$fecha_y_hora_para_view;
			
		$sql_datos_riesgo_poblacion ="";
		$sql_datos_riesgo_poblacion.="CREATE OR REPLACE VIEW $NOMBRE_VISTA_RIESGO_POBLACION ";
		$sql_datos_riesgo_poblacion.=" AS  ";		
		
		//solo se miraran los datos validos
		$sql_datos_riesgo_poblacion .="SELECT * from $nombre_tabla_riesgo_poblacion_a_consultar  ";
		$sql_datos_riesgo_poblacion .=" WHERE ";
		$sql_datos_riesgo_poblacion .=" (fecha_de_corte BETWEEN '".$fecha_ini_bd."'  AND '".$fecha_fin_bd."' )  ";
		if($cod_eapb!="all")
		{
			$sql_datos_riesgo_poblacion .=" AND ";
			$sql_datos_riesgo_poblacion .=" codigo_eapb='".$cod_eapb."'  ";
		}
		if($cod_prestador!="all")
		{
			$sql_datos_riesgo_poblacion .=" AND ";
			$sql_datos_riesgo_poblacion .=" cod_prestador_servicios_salud='".$cod_prestador."'  ";
		}
		if($sexo!="A")
		{
			$sql_datos_riesgo_poblacion .=" AND ";
			$sql_datos_riesgo_poblacion .=" campo_10_sexo='".$sexo."'  ";
		}
		if($regimen!="none")
		{
			$sql_datos_riesgo_poblacion .=" AND ";
			$sql_datos_riesgo_poblacion .=" tipo_de_regimen_de_la_informacion_reportada='".$regimen."'  ";
		}
		$sql_datos_riesgo_poblacion .=" ORDER BY campo_3_tipo_de_identificacion_afiliado asc,campo_4_numero_de_identificacion_afiliado asc ";
		$sql_datos_riesgo_poblacion .=";";
		//fin solo se miraran los datos validos
		
		$error_bd_seq="";		
		$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_datos_riesgo_poblacion, $error_bd_seq);
		if($error_bd_seq!="")
		{
			$mensajes_error_bd.=" ERROR al crear vista de consulta riesgo poblacion: ".$error_bd_seq."<br>";
		}
		//echo "<script>alert('".procesar_mensaje($sql_datos_riesgo_poblacion)."');</script>";
		
		//PARTE DONDE INDICA NUMERO DE REGISTROS
		$numero_registros=0;
		$sql_query_numero_registros="";
		$sql_query_numero_registros.="SELECT count(*) as contador FROM $NOMBRE_VISTA_RIESGO_POBLACION ; ";
		$error_bd_seq="";
		$resultado_query_numero_registros=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($sql_query_numero_registros,$error_bd_seq);
		if($error_bd_seq!="")
		{
			$mensajes_error_bd.="ERROR AL CONSULTAR CANTIDAD ELEMENTOS de vista_subiendo: ".$error_bd_seq."<br>";
		}
		$numero_registros=intval($resultado_query_numero_registros[0]["contador"]);
		$lineas_del_archivo = intval($resultado_query_numero_registros[0]["contador"]);
		if($numero_registros==0)
		{
			//$mensajes_error_bd.="No hay registros a reportar. <br>";
		}
		//FIN PARTE DONDE INDICA NUMERO DE REGISTROS
		
		echo "<script>document.getElementById('periodo').value='$periodo';</script>";
		
		//genera reportte si encontro registros
		$titulo_tabla_consultada="";
		$titulo_tabla_consultada=str_replace("_"," ",$nombre_alternativo);
		$titulo_tabla_consultada=str_replace("res4505","",$titulo_tabla_consultada);
		$titulo_tabla_consultada=str_replace("pyp","",$titulo_tabla_consultada);
		$titulo_tabla_consultada=str_replace("gioss ","",$titulo_tabla_consultada);
		$numero_total_registros_tablas+=$numero_registros;
		if($numero_registros>0)
		{
			$cont_linea=1;
			$contador_offset=0;
			$limite=0;
			
			$es_primera_linea=true;
			$numero_registros_bloque=100;	
			
			while($contador_offset<$numero_registros)
			{
				$limite=$numero_registros_bloque;
				
				if( ($contador_offset+$numero_registros_bloque)>=$numero_registros)
				{
					$limite=$numero_registros_bloque+($numero_registros-$contador_offset);
				}
				
				//PARTE QUERY FILAS POR LIMITE
				$sql_query_busqueda="";
				$sql_query_busqueda.="SELECT * FROM $NOMBRE_VISTA_RIESGO_POBLACION LIMIT $limite OFFSET $contador_offset;  ";
				$error_bd_seq="";
				$resultado_query_consulta_vista=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($sql_query_busqueda,$error_bd_seq);			
				if($error_bd_seq!="")
				{
					$mensajes_error_bd.="ERROR AL CONSULTAR de $NOMBRE_VISTA_RIESGO_POBLACION: ".$error_bd_seq."<br>";
				}
				//FIN PARTE QUERY FILAS POR LIMITE
				
				if(count($resultado_query_consulta_vista)>0)
				{
					$riesgo_poblacion_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
					$fue_cerrada_la_gui=false;
					foreach($resultado_query_consulta_vista as $resultado)
					{
						if($fue_cerrada_la_gui==false)
						{
						    if(connection_aborted()==true)
						    {
							$fue_cerrada_la_gui=true;
						    }
						}//fin if verifica si el usuario cerro la pantalla
						
						
						
						//PARTE ESCRIBE TITULOS COLUMNAS EN EL TXT
						if($es_primera_linea)
						{
							
							
							$titulos_columnas="";
							foreach($resultado as $key=>$columna)
							{
								if($titulos_columnas!=""){$titulos_columnas.="|";}
								$titulos_columnas.=$key;
							}
							fwrite($riesgo_poblacion_file, $titulo_tabla_consultada."\n".$titulos_columnas);
							$es_primera_linea=false;
						}//fin if es primera linea
						//FIN PARTE ESCRIBE TITULOS COLUMNAS EN EL TXT
						
						$cadena_escribir_linea="";
						
						foreach($resultado as $key=>$columna)
						{
							if($cadena_escribir_linea!=""){$cadena_escribir_linea.="|";}
							if($columna=="" && $key!=""){$cadena_escribir_linea.="EMPTY";}
							$cadena_escribir_linea.=$columna;
						}
						
						if($cadena_escribir_linea!="")
						{
							fwrite($riesgo_poblacion_file, "\n".$cadena_escribir_linea);
						}//fin if si cadena escribir no esta vacia
						
						//PORCENTAJE
						$muestra_mensaje_nuevo_file=false;
						$porcentaje_file=intval((($cont_linea)*100)/($numero_registros));
						if($porcentaje_file!=$cont_porcentaje_file || ($porcentaje_file==0 && ($cont_linea)==1) || $porcentaje_file==100)
						{
							$cont_porcentaje_file=$porcentaje_file;
							$muestra_mensaje_nuevo_file=true;
						}
						//FIN PORCENTAJE
						
						if($fue_cerrada_la_gui==false && $muestra_mensaje_nuevo_file)
						{
							$style="";
							if(($contador_tabla_riesgo_poblacion_actual%2)==0)
							{
								$style.="style=background-color:#ffffff";
							}//fin if
							else
							{
								$style.="style=background-color:#d9d9d9";
							}
							
							//fila de  la tabla de riesgo actual en proceso
							$cuadro_resumen_estado_cons_riesgo_poblacion_mtmp="";
							$cuadro_resumen_estado_cons_riesgo_poblacion_mtmp.="<tr $style>";
							$cuadro_resumen_estado_cons_riesgo_poblacion_mtmp.="<td style=text-align:left;>$titulo_tabla_consultada</td>";
							$cuadro_resumen_estado_cons_riesgo_poblacion_mtmp.="<td style=text-align:left;>En proceso</td>";
							$cuadro_resumen_estado_cons_riesgo_poblacion_mtmp.="<td style=text-align:left;>$cont_linea</td>";
							$cuadro_resumen_estado_cons_riesgo_poblacion_mtmp.="<td style=text-align:left;>$numero_registros</td>";
							$cuadro_resumen_estado_cons_riesgo_poblacion_mtmp.="<td style=text-align:left;>$porcentaje_file % </td>";
							$cuadro_resumen_estado_cons_riesgo_poblacion_mtmp.="</tr>";
							//fin fila de  la tabla de riesgo actual en proceso
							
							if($porcentaje_file==100)
							{
								//fila de  la tabla de riesgo actual terminada
								$cuadro_resumen_estado_cons_riesgo_poblacion_mtmp="";
								$cuadro_resumen_estado_cons_riesgo_poblacion_mtmp.="<tr $style>";
								$cuadro_resumen_estado_cons_riesgo_poblacion_mtmp.="<td style=text-align:left;>$titulo_tabla_consultada</td>";
								$cuadro_resumen_estado_cons_riesgo_poblacion_mtmp.="<td style=text-align:left;>Recuperada</td>";
								$cuadro_resumen_estado_cons_riesgo_poblacion_mtmp.="<td style=text-align:left;>$cont_linea</td>";
								$cuadro_resumen_estado_cons_riesgo_poblacion_mtmp.="<td style=text-align:left;>$numero_registros</td>";
								$cuadro_resumen_estado_cons_riesgo_poblacion_mtmp.="<td style=text-align:left;>$porcentaje_file % </td>";
								$cuadro_resumen_estado_cons_riesgo_poblacion_mtmp.="</tr>";
								//fin fila de  la tabla de riesgo actual terminada
								//se adiciona la fila terminada a la parte definitiva del medio de la tabla
								$cuadro_resumen_estado_cons_riesgo_poblacion_m.=$cuadro_resumen_estado_cons_riesgo_poblacion_mtmp;
								
							}
							
							$msg_innerHTML_mensaje_div="";
							//union cuadro resumen
							$msg_innerHTML_mensaje_div.=$cuadro_resumen_estado_cons_riesgo_poblacion_i;
							$msg_innerHTML_mensaje_div.=$cuadro_resumen_estado_cons_riesgo_poblacion_m;
							if($porcentaje_file<100)
							{
								$msg_innerHTML_mensaje_div.=$cuadro_resumen_estado_cons_riesgo_poblacion_mtmp;
							}
							$msg_innerHTML_mensaje_div.=$cuadro_resumen_estado_cons_riesgo_poblacion_f;
							//fin union cuadro resumen
							echo "<script>document.getElementById('mensaje_div').innerHTML=' $msg_innerHTML_mensaje_div ';</script>";
							
							ob_flush();
							flush();
						}//fin if
						
						$cont_linea++;
					}//fin foreach
					fclose($riesgo_poblacion_file);
					
				}//fin if
				
				$contador_offset+=$numero_registros_bloque;
				
			}//fin while
			$array_rutas_archivos_generados[]=$ruta_escribir_archivo;
			
			
			
		}//fin if si hay resultados
		else
		{
			$style="";
			if(($contador_tabla_riesgo_poblacion_actual%2)==0)
			{
				$style.="style=background-color:#ffffff";
			}//fin if
			else
			{
				$style.="style=background-color:#d9d9d9";
			}
			
			//fila de  la tabla de riesgo actual terminada
			$cuadro_resumen_estado_cons_riesgo_poblacion_mtmp="";
			$cuadro_resumen_estado_cons_riesgo_poblacion_mtmp.="<tr $style>";
			$cuadro_resumen_estado_cons_riesgo_poblacion_mtmp.="<td style=text-align:left;>$titulo_tabla_consultada</td>";
			$cuadro_resumen_estado_cons_riesgo_poblacion_mtmp.="<td style=text-align:left;>No se recupero</td>";
			$cuadro_resumen_estado_cons_riesgo_poblacion_mtmp.="<td style=text-align:left;>---</td>";
			$cuadro_resumen_estado_cons_riesgo_poblacion_mtmp.="<td style=text-align:left;>---</td>";
			$cuadro_resumen_estado_cons_riesgo_poblacion_mtmp.="<td style=text-align:left;>---</td>";
			$cuadro_resumen_estado_cons_riesgo_poblacion_mtmp.="</tr>";
			//fin fila de  la tabla de riesgo actual terminada
			//se adiciona la fila terminada a la parte definitiva del medio de la tabla
			$cuadro_resumen_estado_cons_riesgo_poblacion_m.=$cuadro_resumen_estado_cons_riesgo_poblacion_mtmp;
			
		}//fin else no hay resultados
		
		//BORRANDO VISTAS
		$sql_borrar_vistas="";
		$sql_borrar_vistas.=" DROP VIEW $NOMBRE_VISTA_RIESGO_POBLACION ; ";
		
		$error_bd="";		
		$bool_funciono=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_borrar_vistas, $error_bd);		
		if($error_bd!="")
		{
			if(connection_aborted()==false)
			{
				echo "<script>alert('error al borrar vistas');</script>";
			}
		}
		//FIN BORRANDO VISTAS
		
		
		//si el codigo de riesgo poblacion que indica la tabla no es 999 
		//se sale del ciclo while
		if($riesgo_poblacion_a_consultar_codigo!=999)
		{
			break;
		}
		$contador_tabla_riesgo_poblacion_actual++;
	}//fin while recorre todas las tablas si se le es indicado
	
	if($numero_total_registros_tablas>0)
	{
		//GENERANDO ARCHIVO ZIP		
		$ruta_zip=$rutaTemporal.$nombre_archivo_para_riesgo_poblacion_zip.'.zip';
		$result_zip = create_zip($array_rutas_archivos_generados,$ruta_zip);
		
		$msg_innerHTML_mensaje_div="";
		//union cuadro resumen
		$msg_innerHTML_mensaje_div.=$cuadro_resumen_estado_cons_riesgo_poblacion_i;
		$msg_innerHTML_mensaje_div.=$cuadro_resumen_estado_cons_riesgo_poblacion_m;
		//$msg_innerHTML_mensaje_div.=$cuadro_resumen_estado_cons_riesgo_poblacion_mtmp; como aca es la parte final no se muestra ya que ya esta contenida en $cuadro_resumen_estado_cons_riesgo_poblacion_m
		$msg_innerHTML_mensaje_div.=$cuadro_resumen_estado_cons_riesgo_poblacion_f;
		//fin union cuadro resumen
		
		$mensaje.="$msg_innerHTML_mensaje_div  Se genero el reporte de forma comprimida. <br> numero total de registros recuperados <b>$numero_total_registros_tablas</b> .";
		if(connection_aborted()==false)
		{
			echo "<script>document.getElementById('grilla').style.display='inline';</script>";
			echo "<script>var ruta_zip= '$ruta_zip'; </script>";
			ob_flush();
			flush();
		}//fin if
		$resultadoDefinitivo.="<input type='button' value='Descargar resultado consulta riesgo poblacion .zip' id='Descargar' class='btn btn-success color_boton' onclick='download_reporte(ruta_zip);' />  ";
		//FIN GENERANDO ARCHIVO ZIP
		
		if($mensajes_error_bd!="")
		{
			$mensajes_error_bd="<br>".procesar_mensaje($mensajes_error_bd);
		}//fin if
		
		if(connection_aborted()==false)
		{
			//echo "<script>document.getElementById('mensaje_div').style.textAlign='center';</script>";
			//echo "<script>document.getElementById('resultado_definitivo').style.textAlign='center';</script>";
			echo "<script>document.getElementById('mensaje_div').innerHTML='$mensaje $mensajes_error_bd'</script>";
			echo "<script>document.getElementById('resultado_definitivo').innerHTML=\"$resultadoDefinitivo\"</script>";
			ob_flush();
			flush();
		}//fin if
	}//fin if si hay registros en al menos una tabla
	else
	{
		//$mensaje.="<br>No se encontraron resultados.";
		if(connection_aborted()==false)
		{
			echo "<script>document.getElementById('div_mensaje_error').style.display='inline';</script>";
			echo "<script>document.getElementById('parrafo_mensaje_error').innerHTML='No se puede generar el archivo, no existen datos asociados a los filtros seleccionados.';</script>";
			ob_flush();
			flush();
		}//fin if hay coneccion
	}//fin else no hay resultados
}//fin if

$coneccionBD->cerrar_conexion();
?>