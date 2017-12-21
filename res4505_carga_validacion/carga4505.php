<?php
ignore_user_abort(true);
set_time_limit(2592000);
ini_set('max_execution_time', 2592000);
ini_set('memory_limit', '900M');

include_once ('../utiles/ruta_temporales_files.php');

require '../librerias_externas/Smarty-3.1.17/libs/Smarty.class.php';
include_once ('../utiles/clase_coneccion_bd.php');
include_once ('../utiles/funciones_crear_menu.php');
require_once '../utiles/queries_utiles_bd.php';

require_once '../utiles/configuracion_tipo_entidad.php';

require_once 'validar4505.php';

$smarty = new Smarty;
$coneccionBD = new conexion();

session_start();

if (!isset($_SESSION['usuario']) && !isset($_SESSION['tipo_perfil']))
{
	
	header ("Location: ../index.php");
}

$menu= $_SESSION['menu_logueo_html'];
$nombre= $_SESSION['nombre_completo'];

//echo "<script>alert('".$_SESSION['tipo_id'].",".$_SESSION['identificacion']."');</script>";

$tipo_id=$_SESSION['tipo_id'];
$identificacion=$_SESSION['identificacion'];

$nick_user=$_SESSION['usuario'];

$perfil_usuario_actual= $_SESSION['tipo_perfil'];
$entidad_salud_usuario_actual= $_SESSION['entidad_asociada_al_nick'];

session_write_close();

//consultar en gios_usuario_entidad_prestadora_eapb , la cual contiene la relacion entre usuario-ips-eapb

//consultar el tipo de entidad
$sql_query_tipo_entidad_asociada="SELECT * FROM gioss_entidades_sector_salud WHERE codigo_entidad='$entidad_salud_usuario_actual'; ";
$resultado_query_tipo_entidad=$coneccionBD->consultar2($sql_query_tipo_entidad_asociada);
$tipo_entidad=$resultado_query_tipo_entidad[0]["cod_tipo_entidad"];
//fin consultar el tipo de entidad

//SELECTOR PRESTADOR-ASOCIADO-USUARIO
$prestador="";
$prestador.="<select id='prestador' name='prestador' class='campo_azul' onchange='consultar_eapb(\"".$tipo_id."\",\"".$identificacion."\");validar_antes_seleccionar_archivos();'>";
$prestador.="<option value='none'>Seleccione un prestador de salud</option>";

if((intval($perfil_usuario_actual)==5 || intval($perfil_usuario_actual)==4 || intval($perfil_usuario_actual)==3)
   && (intval($tipo_entidad)!=6 && intval($tipo_entidad)!=7 && intval($tipo_entidad)!=8 && intval($tipo_entidad)!=10)
   )
{
	//echo "<script>alert('entro_aqui $entidad_salud_usuario_actual');</script>";
	$sql_consulta_prestadores_asociados_eapb="SELECT ea.codigo_entidad,ea.nombre_de_la_entidad,ea.numero_identificacion FROM ";
	$sql_consulta_prestadores_asociados_eapb.=" gioss_relacion_entre_entidades_salud re INNER JOIN gioss_entidades_sector_salud ea ON (re.entidad1 = ea.codigo_entidad) ";
	$sql_consulta_prestadores_asociados_eapb.=" WHERE re.entidad2='".$entidad_salud_usuario_actual."' ORDER BY ea.nombre_de_la_entidad ";
	$resultado_query_prestadores_asociados_eapb=$coneccionBD->consultar2($sql_consulta_prestadores_asociados_eapb);

	if(count($resultado_query_prestadores_asociados_eapb)>0)
	{
		foreach($resultado_query_prestadores_asociados_eapb as $prestador_asociado_eapb)
		{
			$prestador.="<option value='".$prestador_asociado_eapb['codigo_entidad'].";;".$prestador_asociado_eapb['numero_identificacion']."' selected>".$prestador_asociado_eapb['codigo_entidad']." ".$prestador_asociado_eapb['nombre_de_la_entidad']." ".$prestador_asociado_eapb['numero_identificacion']."</option>";
		}
	}
}//si el tipo entidad es diferente de 6,7,8,10 aka eapb busca las entidades relacionadas a esta(aparece lista entidades asociadas sin importar tipo)
else if((intval($perfil_usuario_actual)==1 || intval($perfil_usuario_actual)==2 || intval($perfil_usuario_actual)==5)
	&& (intval($tipo_entidad)==6 || intval($tipo_entidad)==7 || intval($tipo_entidad)==8 || intval($tipo_entidad)==10)
	)
{
	$sql_query_tipo_entidad_asociada="SELECT * FROM gioss_entidades_sector_salud WHERE codigo_entidad='$entidad_salud_usuario_actual'; ";
	$resultado_query_tipo_entidad=$coneccionBD->consultar2($sql_query_tipo_entidad_asociada);

	if(count($resultado_query_tipo_entidad)>0)
	{
		foreach($resultado_query_tipo_entidad as $eapb_entidad)
		{
			$prestador.="<option value='".$eapb_entidad['codigo_entidad'].";;".$eapb_entidad['numero_identificacion']."' selected>".$eapb_entidad['codigo_entidad']." ".$eapb_entidad['nombre_de_la_entidad']." ".$eapb_entidad['numero_identificacion']."</option>";
		}
	}
}//fin else if en caso de que el perfil sea 1 o 2 y el tipo de la entidad sea igual a 6,7,8,10 aka ips prestador busca la infromacionr eferente a esta misma (aparece entidad asociada al usuario)


$prestador.="</select>";
//FIN PRESTADOR-ASOCIADO-USUARIO

//SELECTOR EAPB-ASOCIADA_PRESTADOR_ASOCIADO_USUARIO
$eapb="";
$eapb.="<div id='div_eapb'>";
$eapb.="<select id='eapb' name='eapb' class='campo_azul' onchange='validar_antes_seleccionar_archivos();' >";
$eapb.="<option value='none'>Seleccione un EAPB</option>";

//si el tipo de la entidad es 6,7,8,10 aka ips prestador
if((intval($perfil_usuario_actual)==5 || intval($perfil_usuario_actual)==1 || intval($perfil_usuario_actual)==2)
   && (intval($tipo_entidad)==6 || intval($tipo_entidad)==7 || intval($tipo_entidad)==8 || intval($tipo_entidad)==10)
   )
{
	$sql_consulta_eapb_usuario_prestador="SELECT ea.codigo_entidad,ea.nombre_de_la_entidad,ea.numero_identificacion FROM ";
	$sql_consulta_eapb_usuario_prestador.=" gioss_relacion_entre_entidades_salud re INNER JOIN gioss_entidades_sector_salud ea ON (re.entidad2 = ea.codigo_entidad) ";
	$sql_consulta_eapb_usuario_prestador.=" WHERE re.entidad1='".$entidad_salud_usuario_actual."' ";
	$sql_consulta_eapb_usuario_prestador.=" ORDER BY ea.nombre_de_la_entidad ";
	$sql_consulta_eapb_usuario_prestador.=";";

	$resultado_query_eapb_usuario=$coneccionBD->consultar2($sql_consulta_eapb_usuario_prestador);

	if(count($resultado_query_eapb_usuario)>0)
	{
		foreach($resultado_query_eapb_usuario as $eapb_prestador_usuario_res)
		{
			$eapb.="<option value='".$eapb_prestador_usuario_res['codigo_entidad']."' selected>".$eapb_prestador_usuario_res['codigo_entidad']." ".$eapb_prestador_usuario_res['nombre_de_la_entidad']." ".$eapb_prestador_usuario_res['numero_identificacion']."</option>";
		}
	}
}//fin if si el usuario es administrador y la entidad asociada es prestadora, por lo tanto busca la informacion las entidades asociadas(sin importar tipo) a la entidad(aparece lista eapb)
else if((intval($perfil_usuario_actual)==3 || intval($perfil_usuario_actual)==4 || intval($perfil_usuario_actual)==5)
	&& (intval($tipo_entidad)!=6 && intval($tipo_entidad)!=7 && intval($tipo_entidad)!=8 && intval($tipo_entidad)!=10)
	)
{
	$sql_query_tipo_entidad_asociada="SELECT * FROM gioss_entidades_sector_salud WHERE codigo_entidad='$entidad_salud_usuario_actual' ORDER BY nombre_de_la_entidad; ";
	$resultado_query_tipo_entidad=$coneccionBD->consultar2($sql_query_tipo_entidad_asociada);

	if(count($resultado_query_tipo_entidad)>0)
	{
		foreach($resultado_query_tipo_entidad as $eapb_entidad)
		{
			$eapb.="<option value='".$eapb_entidad['codigo_entidad']."' selected>".$eapb_entidad['codigo_entidad']." ".$eapb_entidad['nombre_de_la_entidad']." ".$eapb_entidad['numero_identificacion']."</option>";
		}
	}
}//fin else si el usuario es de perfil 3 y la entidad asociada al usuario es de tipo eapb(solo aparece ella misma)


$eapb.="</select>";
$eapb.="</div>";
//FIN EAPB-ASOCIADA_PRESTADOR_ASOCIADO_USUARIO


$selector_fechas_corte="";
$selector_fechas_corte.="<select id='fechas_corte' name='fechas_corte' class='campo_azul' onchange='seleccionar_periodo();validar_antes_seleccionar_archivos();' style='width:230px;'>";
/*
$selector_fechas_corte.="<option value='3-31'>3-31</option>";
$selector_fechas_corte.="<option value='6-30'>6-30</option>";
$selector_fechas_corte.="<option value='9-30'>9-30</option>";
$selector_fechas_corte.="<option value='12-31'>12-31</option>";
*/
$selector_fechas_corte.="<option value='1-31'>1-31</option>";
$selector_fechas_corte.="<option value='2-28'>2-28</option>";
$selector_fechas_corte.="<option value='3-31'>3-31</option>";
$selector_fechas_corte.="<option value='4-30'>4-30</option>";
$selector_fechas_corte.="<option value='5-31'>5-31</option>";
$selector_fechas_corte.="<option value='6-30'>6-30</option>";
$selector_fechas_corte.="<option value='7-31'>7-31</option>";
$selector_fechas_corte.="<option value='8-31'>8-31</option>";
$selector_fechas_corte.="<option value='9-30'>9-30</option>";
$selector_fechas_corte.="<option value='10-31'>10-31</option>";
$selector_fechas_corte.="<option value='11-30'>11-30</option>";
$selector_fechas_corte.="<option value='12-31'>12-31</option>";

$selector_fechas_corte.="</select>";

$selector_periodo="";
$selector_periodo.="<select id='periodo' name='periodo' class='campo_azul' onchange='seleccionar_fecha_de_corte();validar_antes_seleccionar_archivos();' style='width:230px;'>";
/*
$selector_periodo.="<option value='1'>Periodo 1</option>";
$selector_periodo.="<option value='2'>Periodo 2</option>";
$selector_periodo.="<option value='3'>Periodo 3</option>";
$selector_periodo.="<option value='4'>Periodo 4</option>";
*/

$selector_periodo.="<option value='1'>Periodo 1</option>";
$selector_periodo.="<option value='2'>Periodo 2</option>";
$selector_periodo.="<option value='3'>Periodo 3</option>";
$selector_periodo.="<option value='4'>Periodo 4</option>";
$selector_periodo.="<option value='5'>Periodo 5</option>";
$selector_periodo.="<option value='6'>Periodo 6</option>";
$selector_periodo.="<option value='7'>Periodo 7</option>";
$selector_periodo.="<option value='8'>Periodo 8</option>";
$selector_periodo.="<option value='9'>Periodo 9</option>";
$selector_periodo.="<option value='10'>Periodo 10</option>";
$selector_periodo.="<option value='11'>Periodo 11</option>";
$selector_periodo.="<option value='12'>Periodo 12</option>";
$selector_periodo.="</select>";

//SELECTOR DEPARTAMENTO
$selector_departamento="";
$selector_departamento.="<select id='dpto' name='dpto' class='campo_azul' onchange='consultar_mpio();validar_antes_seleccionar_archivos();' style='width:230px;'>";
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
$selector_municipio.="<select id='mpio' name='mpio' class='campo_azul' style='width:230px;' onchange='validar_antes_seleccionar_archivos();'>";
$selector_municipio.="<option value='none'>Seleccione un municipio</option>";
$selector_municipio.="</select>";
$selector_municipio.="</div>";
//FIN SELECTOR MUNICIPIO

$utilidades = new Utilidades();
$rutaTemporal = "../TEMPORALES/";
$validacionLongitud = true;
$validacionNombreArchivo = true;

//echo "<script>alert('"."../TEMPORALES/"." ".$rutaTemporal."');</script>";

$proveniente_de_prestador_o_eapb="";
if($TIPO_ENTIDAD_DE_LA_VERSION=="GENERAL"
   || $TIPO_ENTIDAD_DE_LA_VERSION=="EPS"
   || $TIPO_ENTIDAD_DE_LA_VERSION=="SECRETARIA"
   )
{
$proveniente_de_prestador_o_eapb.="<tr><td style='text-align:left;'><h5 id='sub_titulo_tipo_entidad' style=\"color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;\">Tipo Entidad que realiza el cargue</h5></td></tr>
					<tr>
					    <td style='text-align:left;'>
						<select id='tipo_archivo_4505' name='tipo_archivo_4505' class='campo_azul' onchange='mostrar_selectores_geograficos();validar_antes_seleccionar_archivos();' style='width:230px;'>							    
						    <option value='individual_ips'>Prestador Individual</option>
						    <option value='ent_territoriales'>Agrupado Entidad Territorial</option>
						    <option value='agrupado_eapb'>Agrupado EAPB</option>
						</select>
					    </td>
					</tr>
				";
}
else if($TIPO_ENTIDAD_DE_LA_VERSION=="IPS")
{
	$proveniente_de_prestador_o_eapb.="
	<!--
	<tr>
	<td style='text-align:left;'><b><br>El tipo de archivo PyP 4505 a validar <br> proveendra de una IPS o prestador.<br>&nbsp;<b/>
	</td>
	</tr>
	-->
	<tr>
	<td>
	<input type='hidden' id='tipo_archivo_4505' name='tipo_archivo_4505' value='individual_ips'/>			
	</td>
	</tr>
	";
}


$mensaje = "";
$mostrarMsj = "none";

$mensajeExito = "";
$mostrarMsj2 = "none";

$mensaje_proceso="";

$tipo_entidad_asociada_hidden="";
$tipo_entidad_asociada_hidden.="
	<tr>
	<td>
	<input type='hidden' id='tipo_entidad_asociada_hidden' name='tipo_entidad_asociada_hidden' value='$tipo_entidad'/>			
	</td>
	</tr>
";

$smarty->assign("tipo_entidad_asociada_hidden", $tipo_entidad_asociada_hidden, true);

$smarty->assign("proveniente_de_prestador_o_eapb", $proveniente_de_prestador_o_eapb, true);
$smarty->assign("campo_dpto", $selector_departamento, true);
$smarty->assign("campo_mpio", $selector_municipio, true);

$smarty->assign('mensaje_proceso', $mensaje_proceso, true);
$smarty->assign('mensajeError', $mensaje, true);
$smarty->assign('mostrarMsj', $mostrarMsj, true);
$smarty->assign('mensajeExito', $mensajeExito, true);
$smarty->assign('mostrarMsj2', $mostrarMsj2, true);


$smarty->assign("campo_periodo", $selector_periodo, true);
$smarty->assign("campo_fechas_corte", $selector_fechas_corte, true);
$smarty->assign("campo_eapb", $eapb, true);
$smarty->assign("campo_prestador", $prestador, true);
$smarty->assign("nombre", $nombre, true);
$smarty->assign("menu", $menu, true);
$smarty->display('carga4505.html.tpl');

//echo "<script>mostrar_ventana_tablas_riesgo_poblacion();</script>";

//PARTE DONDE GUARDA LOS ARCHIVOS 4505
if(isset($_POST["fecha_remision"]))
{
	
	//echo "<script>alert('entro');</script>";
	
	
	$tipo_entidad_que_efectua_el_cargue=$_POST["tipo_archivo_4505"];
	$cod_dpto_filtro="none";
	$cod_mpio_filtro="none";
	if(isset($_POST["tipo_archivo_4505"]) && $tipo_entidad_que_efectua_el_cargue=="ent_territoriales")
	{
		$cod_dpto_filtro=$_POST["dpto"];
		$cod_mpio_filtro=$_POST["mpio"];
		
		if($cod_dpto_filtro!="none" && $cod_mpio_filtro="none")
		{
			$cod_mpio_filtro="000";
		}
	}

	$arreglo_archivos = Array();
	$i = 0;
	
	$numero_de_identificacion_de_la_entidad_prestadora="";
	$numero_de_identificacion_de_la_epba="";
	$codigo_eapb="";
	$modulo_de_informacion="";
	$tema_de_informacion="";
	$tipo_de_identificacion_entidad_reportadora="";
	$tipo_de_regimen_de_la_informacion_reportada="";
	$consecutivo_de_archivo="";
	$codRegEspecial="";
	$nombre_archivo_4505="";
	
	//REVISA LA CABECERA DEL ARCHIVO 4505 SUBIDO
	foreach ($_FILES as $key => $infoArchivos) 
	{
		//CONDICION SI EL ARCHIVO SUBIDO SUPERA LOS 1024MB
		if ($infoArchivos['size'] > 1024000000)
		{
			$mostrarMsj = 'inline-block';
			$mensaje .= "El tama&ntildeo del archivo no debe superar 1024 MegaBytes de tamaño. Por favor verifique el tama&ntildeo de su archivo.";
			$validacionLongitud = false;
			$validacionNombreArchivo = false;
		} 
		else 
		{
			//echo "<script>alert('entro2');</script>";
			if ($infoArchivos['name'] != '') 
			{	
				$fecha_para_archivo= date('YmdHis');
				$carpetaOrig4505="ORIGVAL4505".$fecha_para_archivo;
			    if(!file_exists($rutaTemporal.$carpetaOrig4505))
			    {
				    mkdir($rutaTemporal.$carpetaOrig4505, 0777, true);
			    }//fin if

				$nombre_archivo_4505=str_replace(".TXT",".txt",$infoArchivos['name']);
				$rutaTemporal = $rutaTemporal .$carpetaOrig4505."/". $infoArchivos['name'];
				move_uploaded_file($infoArchivos['tmp_name'], $rutaTemporal);
				$arreglo_archivos[$i]['tipo_archivo'] = $key;
				$arreglo_archivos[$i]['nombre_archivo'] = $infoArchivos['name'];
				$arreglo_archivos[$i]['archivo'] = $rutaTemporal;
				$rutaTemporal = "../TEMPORALES/";

				$tipoArc = $arreglo_archivos[$i]['nombre_archivo'];

				//Validacion de longitud de nombre
				if (strlen($arreglo_archivos[$i]['nombre_archivo']) < 39)
				{
				    $longitud_nombre_archivo=strlen($arreglo_archivos[$i]['nombre_archivo']);
				    $validacionLongitud = false;
				    $mostrarMsj = 'inline-block';
				    $mensaje .= 'La longitud del nombre del archivo ('.$longitud_nombre_archivo.') es incorrecta. Por favor revise que el nombre del archivo cumple el parametro establecido de 39 caracteres.<br/>';
				    break;
				}
		
				//Validacion de 
				if (substr($tipoArc, 0, -36) != "SGD") {
				    $validacionNombreArchivo = false;
				    $mostrarMsj = 'inline-block';
				    $mensaje .= 'El identificador del modulo de informacion no es el permitido en el nombre del archivo. Recuerde que que el identificante del modulo es SGD </br>';
				    break;
				}
						
						$modulo_de_informacion=substr($tipoArc, 0, -36);
		
				if (substr($tipoArc, 3, -33) != "280") {
				    $validacionNombreArchivo = false;
				    $mostrarMsj = 'inline-block';
				    $mensaje .= 'El numero correspondiente al tipo de fuente no es el permitido. Recuerde que el codigo correspondiente al tipo de fuente es 280 </br>';
				    break;
				}
		
				if (substr($tipoArc, 6, -29) != "RPED") {
				    $validacionNombreArchivo = false;
				    $mostrarMsj = 'inline-block';
				    $mensaje .= 'El identificador del tema fuente no es el permitido. Recuerde que el identificador del tema fuente a definir en el archivo es RPED </br>';
				    break;
				}
						
				$tema_de_informacion=substr($tipoArc, 6, -29);
		
				$fechaCorte = substr($tipoArc, 10, -21);
		
				$year = substr($fechaCorte, 0, -4);
				$month = substr($fechaCorte, 4, -2);
				$day = substr($fechaCorte, 6);
				
				$fechaCorte = "" . $year . "-" . $month . "-" . $day . "";
				
				if(isset($_POST['tipo_periodo_tiempo']))
				{
					if($_POST['tipo_periodo_tiempo']=="trimestral")
					{
						//funcion de utiles que se puede dejar
						$fechafinPeriodo = $utilidades->obtenerFechaFinPeriodo($_POST['periodo']);
					}
					else if($_POST['tipo_periodo_tiempo']=="mensual")
					{
						//funcion de utiles que se puede dejar
						$fechafinPeriodo = $utilidades->obtenerFechaFinPeriodoMensual4505($_POST['periodo']);
					}
				}
				else
				{				
					//funcion de utiles que se puede dejar
					$fechafinPeriodo = $utilidades->obtenerFechaFinPeriodo($_POST['periodo']);
				}
						
				//echo "<script>alert('$fechaCorte , ".$fechafinPeriodo[0]['fec_final_periodo']."');</script>";
				
				$array_fecha_corte=explode("-",$fechaCorte);
				$array_fecha_de_corte_consultada=explode("-",$fechafinPeriodo[0]['fec_final_periodo']);
				
				//echo "<script>alert('".$array_fecha_corte[1]." ".$array_fecha_corte[2]." ".$array_fecha_de_corte_consultada[1]." ".$array_fecha_de_corte_consultada[2]."');</script>";
				
				//solo se toma el dia y mes porque no se compara el year
				if (intval($array_fecha_corte[1])!= intval($array_fecha_de_corte_consultada[1])
				    || intval($array_fecha_corte[2])!= intval($array_fecha_de_corte_consultada[2])) 
				{
		
				    $validacionNombreArchivo = false;
				    $mostrarMsj = 'inline-block';
				    $mensaje.= 'La fecha corte referenciada en el nombre del archivo no es la correspondiente. ';
				    $mensaje.='Recuerde que la fecha corte referenciada en el archivo debe ser igual a la fecha fin del periodo seleccionado. </br>';
				}
						
				//tipo id entidad prestadora
				
				$array_prestador=explode(";;",$_POST['prestador']);
				$codRegEspecial = $array_prestador[0];
				
				$codigo_eapb= $_POST['eapb'];

				if($tipo_entidad_que_efectua_el_cargue=="agrupado_ips")
				{
					$codigo_eapb="AGRUP_IPS";

					try
					{

						$insertar_entidad_agrup_ips="insert into gioss_entidades_sector_salud (codigo_entidad,cod_tipo_entidad) values('AGRUP_IPS','4') ;";
						$error_bd_agrup_ips="";
						$bool_hubo_error_query_agrup_ips=$coneccionBD->insertar_no_warning_get_error($insertar_entidad_agrup_ips, $error_bd_agrup_ips);
						if($error_bd_agrup_ips!="")
						{
							//no necesita imprimir este error por pantalla
						}//fin if

					}
					catch(Exception $e)
					{}

					try
					{

						$insertar_entidad_agrup_ips="insert into gios_entidad_administradora (cod_entidad_administradora,codigo_tipo_entidad,nom_entidad_administradora) values('AGRUP_IPS','4','PLACEHOLDER AGRUPADO IPS') ;";
						$error_bd_agrup_ips="";
						$bool_hubo_error_query_agrup_ips=$coneccionBD->insertar_no_warning_get_error($insertar_entidad_agrup_ips, $error_bd_agrup_ips);
						if($error_bd_agrup_ips!="")
						{
							//no necesita imprimir este error por pantalla
						}//fin if

					}
					catch(Exception $e)
					{}
				}//fin else if /if
				else if($tipo_entidad_que_efectua_el_cargue=="agrupado_ips120")
				{
					$codigo_eapb="EPS000";
					
					try
					{

						$insertar_entidad_agrup_ips120="insert into gioss_entidades_sector_salud (codigo_entidad,cod_tipo_entidad) values('EPS000','4') ;";
						$error_bd_agrup_ips120="";
						$bool_hubo_error_query_agrup_ips120=$coneccionBD->insertar_no_warning_get_error($insertar_entidad_agrup_ips120, $error_bd_agrup_ips120);
						if($error_bd_agrup_ips120!="")
						{
							//no necesita imprimir este error por pantalla
						}//fin if

					}
					catch(Exception $e)
					{}

					try
					{

						$insertar_entidad_agrup_ips120="insert into gios_entidad_administradora (cod_entidad_administradora,codigo_tipo_entidad,nom_entidad_administradora) values('EPS000','4','PLACEHOLDER AGRUPADO IPS') ;";
						$error_bd_agrup_ips120="";
						$bool_hubo_error_query_agrup_ips120=$coneccionBD->insertar_no_warning_get_error($insertar_entidad_agrup_ips120, $error_bd_agrup_ips120);
						if($error_bd_agrup_ips120!="")
						{
							//no necesita imprimir este error por pantalla
						}//fin if

					}
					catch(Exception $e)
					{}
				}//fin else if
				
				$sql_consulta_entidad_reportadora="";
				if($tipo_entidad_que_efectua_el_cargue=="individual_ips"
				   || $tipo_entidad_que_efectua_el_cargue=="ent_territoriales"
				   || $tipo_entidad_que_efectua_el_cargue=="agrupado_ips"
				   || $tipo_entidad_que_efectua_el_cargue=="agrupado_ips120"
				   )
				{
					$sql_consulta_entidad_reportadora.="select * from gioss_entidades_sector_salud WHERE codigo_entidad='".$codRegEspecial."';";
					//echo "<script>alert('$codRegEspecial');</script>";
				}
				else if($tipo_entidad_que_efectua_el_cargue=="agrupado_eapb")
				{
					$sql_consulta_entidad_reportadora.="select * from gioss_entidades_sector_salud WHERE codigo_entidad='".$codigo_eapb."';";
				}
				$resultado_query_entidad_reportadora=$coneccionBD->consultar2($sql_consulta_entidad_reportadora);
				
				$codTipoIdEntidad = $resultado_query_entidad_reportadora[0]["cod_tipo_entidad"];
				
				if(strlen($codTipoIdEntidad)==1)
				{
					$codTipoIdEntidad="0".$codTipoIdEntidad;
				}
				
				if(intval($codTipoIdEntidad)!=6
				   && intval($codTipoIdEntidad)!=7
				   && intval($codTipoIdEntidad)!=8
				   && intval($codTipoIdEntidad)!=9
				   && $tipo_entidad_que_efectua_el_cargue=="individual_ips"
				   )
				{
					$validacionNombreArchivo = false;
					$mostrarMsj = 'inline-block';
					$mensaje .= 'La entidad de salud asociada al usuario no es una entidad prestadora de salud (debe ser una de las siguentes: IPS privada, IPS publica, profesional independiente, transporte especial )  </br>';
				}
				
		
				$tipoIdIps = substr($tipoArc, 18, -19);
		
				if ($tipoIdIps!="NI" && $tipoIdIps!="DI" && $tipoIdIps!="MU" && $tipoIdIps!="DE") 
				{
		
				    $validacionNombreArchivo = false;
				    $mostrarMsj = 'inline-block';
				    $mensaje .= 'El el segmento NI no se encuentra. Por favor revise el nombre del archivo. </br>';
				}
						
				$tipo_de_identificacion_entidad_reportadora=$tipoIdIps;
						
				//numero de identificacion de la entidad prestadora != del codigo especial ips
		
				$idIPS = substr($tipoArc, 20, -7);
		
				//se consulta de bd para comparar con la cabezera
				$nitIPS = $resultado_query_entidad_reportadora[0]["numero_identificacion"];
				
				//echo "<script>alert('$idIPS ".strlen($idIPS)." $nitIPS ".strlen($nitIPS)."');</script>";
				
				//adiciona los ceros a la izquierda 
				if (strlen($nitIPS) < 12) 
				{
		
				    for ($i = strlen($nitIPS); $i < 12; $i++)
				    {
		
					$nitIPS = '0' . $nitIPS;
				    }
				}
				
				//echo "<script>alert('$idIPS ".strlen($idIPS)." $nitIPS ".strlen($nitIPS)."');</script>";
		
				if ($idIPS != $nitIPS)
				{
				    $sql_consulta_descripcion_por_nit_1="";
				    $sql_consulta_descripcion_por_nit_1.="select * from gioss_entidades_sector_salud WHERE numero_identificacion='".intval($idIPS)."';";
				    $resultado_query_descripcion_por_nit_1=$coneccionBD->consultar2($sql_consulta_descripcion_por_nit_1);
				    $nombre_entidad_1="";
				    if(is_array($resultado_query_descripcion_por_nit_1))
				    {
					$nombre_entidad_1=$resultado_query_descripcion_por_nit_1[0]["nombre_de_la_entidad"];
				    }
				    
				    $sql_consulta_descripcion_por_nit_2="";
				    $sql_consulta_descripcion_por_nit_2.="select * from gioss_entidades_sector_salud WHERE numero_identificacion='".intval($nitIPS)."';";
				    $resultado_query_descripcion_por_nit_2=$coneccionBD->consultar2($sql_consulta_descripcion_por_nit_2);
				    $nombre_entidad_2="";
				    if(is_array($resultado_query_descripcion_por_nit_2))
				    {
					$nombre_entidad_2=$resultado_query_descripcion_por_nit_2[0]["nombre_de_la_entidad"];
				    }
				    
				    $validacionNombreArchivo = false;
				    $mostrarMsj = 'inline-block';
				    $mensaje .= 'El numero de identificacion de la entidad reportadora referenciado  en el nombre del archivo '.$idIPS.' '.$nombre_entidad_1.'  no es la correspondiente al numero de identificacion de la entidad prestadora asociada '.$nitIPS.'. '.$nombre_entidad_2.' Por favor revise el nombre del archivo. </br>';
				}
						
				//se asigna de nuevo despues de la comparacion para la comparacion en la base de datos
				$nitIPS = $resultado_query_entidad_reportadora[0]["numero_identificacion"];
				$numero_de_identificacion_de_la_entidad_prestadora=$nitIPS;
				
				//tipo regimen
		
				$tipoRegimen = substr($tipoArc, 32, -6);
				
				$query_tipo_regimen="";
				$query_tipo_regimen.=" SELECT * FROM gioss_tipo_regimen_salud_4505 WHERE cod_tipo_regimen='$tipoRegimen'; ";
				$resultado_tipo_regimen_valido=$coneccionBD->consultar2($query_tipo_regimen);
		
				if (!is_array($resultado_tipo_regimen_valido) || count($resultado_tipo_regimen_valido)==0)
				{
		
				    $validacionNombreArchivo = false;
				    $mostrarMsj = 'inline-block';
				    $mensaje.='El tipo de regimen referenciado en el nombre del archivo no es alguno de los permitidos.';
				    $mensaje.=' Por favor revise el nombre del archivo ';
				    $mensaje.='y recuerde que los valores permitidos son:';
				    $mensaje.=' C (Contributivo), S (Subsidiado), E (Especial), P (Excepcion), N (No Asegurado), O (Prepagada). </br>';
				}
				
				$tipo_de_regimen_de_la_informacion_reportada=$tipoRegimen;
		
				

				
				
				
				$query_numero_identificacion_entidad_administradora="";
				$query_numero_identificacion_entidad_administradora.="SELECT nit FROM gios_entidad_administradora WHERE cod_entidad_administradora='$codigo_eapb'; ";
				$resultado_query_nit_eapb=$coneccionBD->consultar2($query_numero_identificacion_entidad_administradora);

                
				$numero_de_identificacion_de_la_epba= $resultado_query_nit_eapb[0]["nit"];
				
				
		
				$numArchivoCargado = substr($tipoArc, 33, -4);
		
				if (!ctype_digit($numArchivoCargado) && strlen($numArchivoCargado)>2)
				{
				    $validacionNombreArchivo = false;
				    $mostrarMsj = 'inline-block';
				    $mensaje .= "El numero de remision en el nombre del archivo no son digitos numericos </br>";
				}
						
				$consecutivo_de_archivo=$numArchivoCargado;
				
				if($tipo_entidad_que_efectua_el_cargue=="individual_ips")
				{
					//VERIFICACION YA FUE VALIDADO CON EXITO
					$sql_datos_verificar_si_hay_datos_cargados_con_exito ="";				
					$sql_datos_verificar_si_hay_datos_cargados_con_exito .="SELECT * from gios_datos_validados_exito_r4505 WHERE ";				
					$sql_datos_verificar_si_hay_datos_cargados_con_exito .=" codigo_eapb ='".$codigo_eapb."' ";
					$sql_datos_verificar_si_hay_datos_cargados_con_exito .=" AND ";
					$sql_datos_verificar_si_hay_datos_cargados_con_exito .=" fecha_de_corte = '".$_POST["year_de_corte"]."-".$_POST["fechas_corte"]."'  ";
					$sql_datos_verificar_si_hay_datos_cargados_con_exito .=" AND ";
					$sql_datos_verificar_si_hay_datos_cargados_con_exito .=" cod_prestador_servicios_salud ='".$codRegEspecial."' ";
					$sql_datos_verificar_si_hay_datos_cargados_con_exito .=" AND ";
					$sql_datos_verificar_si_hay_datos_cargados_con_exito .=" nombre_archivo ='".$nombre_archivo_4505."' ";
					$sql_datos_verificar_si_hay_datos_cargados_con_exito .=" AND ";
					$sql_datos_verificar_si_hay_datos_cargados_con_exito .=" consecutivo_de_archivo::integer='".$numArchivoCargado."' ";
					$sql_datos_verificar_si_hay_datos_cargados_con_exito .=";";
					$resultado_ya_hay_datos_cargados_con_exito=$coneccionBD->consultar2($sql_datos_verificar_si_hay_datos_cargados_con_exito);
					
					$bool_hay_datos_cargados_con_exito=false;
					
					if(is_array($resultado_ya_hay_datos_cargados_con_exito) && count($resultado_ya_hay_datos_cargados_con_exito)>0)
					{
						$bool_hay_datos_cargados_con_exito=true;
						//echo "<script>alert('existen datos cargados con exito')</script>";
					}
					
					$sql_consulta_tabla_estado_informacion="";
					$sql_consulta_tabla_estado_informacion.="SELECT * FROM gioss_tabla_estado_informacion_4505 ";
					$sql_consulta_tabla_estado_informacion.=" WHERE ";
					$sql_consulta_tabla_estado_informacion.=" fecha_corte_periodo = '".$_POST["year_de_corte"]."-".$_POST["fechas_corte"]."' ";
					$sql_consulta_tabla_estado_informacion.=" AND ";
					$sql_consulta_tabla_estado_informacion.=" codigo_eapb ='".$codigo_eapb."' ";
					$sql_consulta_tabla_estado_informacion.=" AND ";
					$sql_consulta_tabla_estado_informacion.=" codigo_prestador_servicios ='".$codRegEspecial."' ";
					$sql_consulta_tabla_estado_informacion.=" AND ";
					$sql_consulta_tabla_estado_informacion.=" nombre_del_archivo ='".$nombre_archivo_4505."' ";
					$sql_consulta_tabla_estado_informacion.=" AND ";
					$sql_consulta_tabla_estado_informacion.=" codigo_estado_informacion ='1' ";
					$existe_en_estado_de_informacion=$coneccionBD->consultar2($sql_consulta_tabla_estado_informacion);
					
					$bool_existe_en_estado_informacion=false;
					
					if(is_array($existe_en_estado_de_informacion) && count($existe_en_estado_de_informacion)>0)
					{
						$bool_existe_en_estado_informacion=true;
						//echo "<script>alert('existe registro en estado de informacion 4505')</script>";
					}
	
					if ($bool_hay_datos_cargados_con_exito==true && $bool_existe_en_estado_informacion==true) 
					{
					    $validacionNombreArchivo = false;
					    $mostrarMsj = 'inline-block';
					    $mensaje.="El archivo ".$existe_en_estado_de_informacion[0]["nombre_del_archivo"]." ya fue validado con exito en la fecha ".$existe_en_estado_de_informacion[0]["fecha_validacion"]." ,";
					    $mensaje.=" con el numero de secuencia ".$existe_en_estado_de_informacion[0]["numero_secuencia"].". </br>";
					}
					else
					{
						if($bool_hay_datos_cargados_con_exito==true)
						{
							//DELETE DE DATOS VALIDADOS
							//si no se completo el proceso hasta llenar la
							//tabla de estado de informacion se borran de
							//validados con exito porque estubo incompleto
							$sql_delete_validados_exito_sin_completar="";
							$sql_delete_validados_exito_sin_completar.=" DELETE FROM gios_datos_validados_exito_r4505 WHERE ";
							$sql_delete_validados_exito_sin_completar.=" cod_prestador_servicios_salud='".$codRegEspecial."'  ";
							$sql_delete_validados_exito_sin_completar.=" AND ";
							$sql_delete_validados_exito_sin_completar.=" codigo_eapb='".$codigo_eapb."' ";
							$sql_delete_validados_exito_sin_completar.=" AND ";
							$sql_delete_validados_exito_sin_completar.=" fecha_de_corte = '".$_POST["year_de_corte"]."-".$_POST["fechas_corte"]."'  ";
							$sql_delete_validados_exito_sin_completar.=" AND ";
							$sql_delete_validados_exito_sin_completar.=" nombre_archivo ='".$nombre_archivo_4505."'  ";
							$sql_delete_validados_exito_sin_completar.=" AND ";
							$sql_delete_validados_exito_sin_completar.=" consecutivo_de_archivo::integer='".$numArchivoCargado."'  ";
							$sql_delete_validados_exito_sin_completar.=" ; ";
							$error_bd_seq="";
							$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_delete_validados_exito_sin_completar, $error_bd_seq);
							if($error_bd_seq!="")
							{
								echo "<script>alert('hubo error al borrar datos de una operacion de validacion exitosa realizada previamente');</script>";
							}
							//FIN DELETE VALIDADOS
							//si no se completo el proceso hasta llenar la tabla de estado de informacion
						}//fin if solo si habia datos cargados como exitosos pero no se completo el rpoceso de validacion
					}
					
					//VERIFICACION YA FUE VALIDADO CON EXITO
				}//fin if si no es proveniente de eapb sino de una ips
				else if($tipo_entidad_que_efectua_el_cargue=="ent_territoriales")
				{
					//VERIFICACION YA FUE VALIDADO CON EXITO
					$sql_datos_verificar_si_hay_datos_cargados_con_exito ="";				
					$sql_datos_verificar_si_hay_datos_cargados_con_exito .="SELECT * from gioss_archivo_4505_exitoso_para_eapb WHERE ";				
					$sql_datos_verificar_si_hay_datos_cargados_con_exito .=" codigo_eapb ='".$codRegEspecial."' ";
					$sql_datos_verificar_si_hay_datos_cargados_con_exito .=" AND ";
					$sql_datos_verificar_si_hay_datos_cargados_con_exito .=" fecha_de_corte = '".$_POST["year_de_corte"]."-".$_POST["fechas_corte"]."'  ";
					$sql_datos_verificar_si_hay_datos_cargados_con_exito .=" AND ";
					$sql_datos_verificar_si_hay_datos_cargados_con_exito .=" nombre_archivo ='".$nombre_archivo_4505."' ";
					$sql_datos_verificar_si_hay_datos_cargados_con_exito .=" AND ";
					$sql_datos_verificar_si_hay_datos_cargados_con_exito .=" consecutivo_de_archivo::integer='".$numArchivoCargado."' ";
					$sql_datos_verificar_si_hay_datos_cargados_con_exito .=" AND ";
					$sql_datos_verificar_si_hay_datos_cargados_con_exito .=" codigo_departamento='".$cod_dpto_filtro."' ";
					$sql_datos_verificar_si_hay_datos_cargados_con_exito .=" AND ";
					$sql_datos_verificar_si_hay_datos_cargados_con_exito .=" codigo_municipio='".$cod_mpio_filtro."' ";
					$sql_datos_verificar_si_hay_datos_cargados_con_exito .=";";
					$resultado_ya_hay_datos_cargados_con_exito=$coneccionBD->consultar2($sql_datos_verificar_si_hay_datos_cargados_con_exito);
					
					$bool_hay_datos_cargados_con_exito=false;
					
					if(is_array($resultado_ya_hay_datos_cargados_con_exito) && count($resultado_ya_hay_datos_cargados_con_exito)>0)
					{
						$bool_hay_datos_cargados_con_exito=true;
						//echo "<script>alert('existen datos cargados con exito')</script>";
					}
					
					$sql_consulta_tabla_estado_informacion="";
					$sql_consulta_tabla_estado_informacion.="SELECT * FROM gioss_tabla_estado_informacion_4505_eapb ";
					$sql_consulta_tabla_estado_informacion.=" WHERE ";
					$sql_consulta_tabla_estado_informacion.=" fecha_corte_periodo = '".$_POST["year_de_corte"]."-".$_POST["fechas_corte"]."' ";
					$sql_consulta_tabla_estado_informacion.=" AND ";
					$sql_consulta_tabla_estado_informacion.=" codigo_eapb ='".$codRegEspecial."' ";
					$sql_consulta_tabla_estado_informacion.=" AND ";
					$sql_consulta_tabla_estado_informacion.=" nombre_del_archivo ='".$nombre_archivo_4505."' ";
					$sql_consulta_tabla_estado_informacion.=" AND ";
					$sql_consulta_tabla_estado_informacion.=" codigo_departamento ='".$cod_dpto_filtro."' ";
					$sql_consulta_tabla_estado_informacion.=" AND ";
					$sql_consulta_tabla_estado_informacion.=" codigo_municipio ='".$cod_mpio_filtro."' ";
					$sql_consulta_tabla_estado_informacion.=" AND ";
					$sql_consulta_tabla_estado_informacion.=" codigo_estado_informacion ='1' ";
					$existe_en_estado_de_informacion=$coneccionBD->consultar2($sql_consulta_tabla_estado_informacion);
					
					$bool_existe_en_estado_informacion=false;
					
					if(is_array($existe_en_estado_de_informacion) && count($existe_en_estado_de_informacion)>0)
					{
						$bool_existe_en_estado_informacion=true;
						//echo "<script>alert('existe registro en estado de informacion 4505')</script>";
					}
	
					if ($bool_hay_datos_cargados_con_exito==true && $bool_existe_en_estado_informacion==true) 
					{
					    $validacionNombreArchivo = false;
					    $mostrarMsj = 'inline-block';
					    $mensaje.="El archivo ".$existe_en_estado_de_informacion[0]["nombre_del_archivo"]." ya fue validado con exito en la fecha ".$existe_en_estado_de_informacion[0]["fecha_validacion"]." ,";
					    $mensaje.=" con el numero de secuencia ".$existe_en_estado_de_informacion[0]["numero_secuencia"].". </br>";
					}
					else
					{
						if($bool_hay_datos_cargados_con_exito==true)
						{
							//DELETE DE DATOS VALIDADOS
							//si no se completo el proceso hasta llenar la
							//tabla de estado de informacion se borran de
							//validados con exito porque estubo incompleto
							$sql_delete_validados_exito_sin_completar="";
							$sql_delete_validados_exito_sin_completar.=" DELETE FROM gioss_archivo_4505_exitoso_para_eapb WHERE ";
							$sql_delete_validados_exito_sin_completar.=" codigo_eapb='".$codRegEspecial."' ";
							$sql_delete_validados_exito_sin_completar.=" AND ";
							$sql_delete_validados_exito_sin_completar.=" fecha_de_corte = '".$_POST["year_de_corte"]."-".$_POST["fechas_corte"]."'  ";
							$sql_delete_validados_exito_sin_completar.=" AND ";
							$sql_delete_validados_exito_sin_completar.=" nombre_archivo ='".$nombre_archivo_4505."'  ";
							$sql_delete_validados_exito_sin_completar.=" AND ";
							$sql_delete_validados_exito_sin_completar.=" consecutivo_de_archivo::integer='".$numArchivoCargado."'  ";
							$sql_delete_validados_exito_sin_completar.=" AND ";
							$sql_delete_validados_exito_sin_completar.=" codigo_departamento='".$cod_dpto_filtro."' ";
							$sql_delete_validados_exito_sin_completar.=" AND ";
							$sql_delete_validados_exito_sin_completar.=" codigo_municipio='".$cod_mpio_filtro."' ";
							$sql_delete_validados_exito_sin_completar.=" ; ";
							$error_bd_seq="";
							$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_delete_validados_exito_sin_completar, $error_bd_seq);
							if($error_bd_seq!="")
							{
								echo "<script>alert('hubo error al borrar datos de una operacion de validacion exitosa realizada previamente');</script>";
							}
							//FIN DELETE VALIDADOS
							//si no se completo el proceso hasta llenar la tabla de estado de informacion
						}//fin if solo si habia datos cargados como exitosos pero no se completo el rpoceso de validacion
					}
					
					//VERIFICACION YA FUE VALIDADO CON EXITO
				}//fin else if proviene de una entidad territorial
				else if($tipo_entidad_que_efectua_el_cargue=="agrupado_eapb")
				{
					$sql_consulta_tabla_estado_informacion="";
					$sql_consulta_tabla_estado_informacion.="SELECT * FROM gioss_tabla_estado_informacion_4505 ";
					$sql_consulta_tabla_estado_informacion.=" WHERE ";
					$sql_consulta_tabla_estado_informacion.=" fecha_corte_periodo = '".$_POST["year_de_corte"]."-".$_POST["fechas_corte"]."' ";
					$sql_consulta_tabla_estado_informacion.=" AND ";
					//el segundo sera la(s) entidad eapb por lo genera, revisar la parte con entidades territoriales
					$sql_consulta_tabla_estado_informacion.=" codigo_eapb ='".$codigo_eapb."' ";
					$sql_consulta_tabla_estado_informacion.=" AND ";
					$sql_consulta_tabla_estado_informacion.=" codigo_prestador_servicios ='AGRUP_EAPB' ";
					$sql_consulta_tabla_estado_informacion.=" AND ";
					$sql_consulta_tabla_estado_informacion.=" nombre_del_archivo ='".$nombre_archivo_4505."' ";
					$sql_consulta_tabla_estado_informacion.=" AND ";
					$sql_consulta_tabla_estado_informacion.=" codigo_estado_informacion ='1' ";
					$existe_en_estado_de_informacion=$coneccionBD->consultar2($sql_consulta_tabla_estado_informacion);
					
					$bool_existe_en_estado_informacion=false;
					
					if(is_array($existe_en_estado_de_informacion) && count($existe_en_estado_de_informacion)>0)
					{
						$bool_existe_en_estado_informacion=true;
						//echo "<script>alert('existe registro en estado de informacion 4505')</script>";
					}
	
					if ($bool_existe_en_estado_informacion==true) 
					{
					    $validacionNombreArchivo = false;
					    $mostrarMsj = 'inline-block';
					    $mensaje.="El archivo ".$existe_en_estado_de_informacion[0]["nombre_del_archivo"]." ya fue validado con exito en la fecha ".$existe_en_estado_de_informacion[0]["fecha_validacion"]." ,";
					    $mensaje.=" con el numero de secuencia ".$existe_en_estado_de_informacion[0]["numero_secuencia"].". </br>";
					}
				}//fin if proviene de eapb archivo agrupado
				else if($tipo_entidad_que_efectua_el_cargue=="agrupado_ips")
				{
					$sql_consulta_tabla_estado_informacion="";
					$sql_consulta_tabla_estado_informacion.="SELECT * FROM gioss_tabla_estado_informacion_4505 ";
					$sql_consulta_tabla_estado_informacion.=" WHERE ";
					$sql_consulta_tabla_estado_informacion.=" fecha_corte_periodo = '".$_POST["year_de_corte"]."-".$_POST["fechas_corte"]."' ";
					$sql_consulta_tabla_estado_informacion.=" AND ";
					//el segundo sera la(s) entidad eapb por lo genera, revisar la parte con entidades territoriales
					$sql_consulta_tabla_estado_informacion.=" codigo_eapb ='AGRUP_IPS' ";
					$sql_consulta_tabla_estado_informacion.=" AND ";
					$sql_consulta_tabla_estado_informacion.=" codigo_prestador_servicios ='".$codRegEspecial."' ";
					$sql_consulta_tabla_estado_informacion.=" AND ";
					$sql_consulta_tabla_estado_informacion.=" nombre_del_archivo ='".$nombre_archivo_4505."' ";
					$sql_consulta_tabla_estado_informacion.=" AND ";
					$sql_consulta_tabla_estado_informacion.=" codigo_estado_informacion ='1' ";
					$existe_en_estado_de_informacion=$coneccionBD->consultar2($sql_consulta_tabla_estado_informacion);
					
					$bool_existe_en_estado_informacion=false;
					
					if(is_array($existe_en_estado_de_informacion) && count($existe_en_estado_de_informacion)>0)
					{
						$bool_existe_en_estado_informacion=true;
						//echo "<script>alert('existe registro en estado de informacion 4505')</script>";
					}
	
					if ($bool_existe_en_estado_informacion==true) 
					{
					    $validacionNombreArchivo = false;
					    $mostrarMsj = 'inline-block';
					    $mensaje.="El archivo ".$existe_en_estado_de_informacion[0]["nombre_del_archivo"]." ya fue validado con exito en la fecha ".$existe_en_estado_de_informacion[0]["fecha_validacion"]." ,";
					    $mensaje.=" con el numero de secuencia ".$existe_en_estado_de_informacion[0]["numero_secuencia"].". </br>";
					}
				}//fin if proviene de ips archivo agrupado
				else if($tipo_entidad_que_efectua_el_cargue=="agrupado_ips120")
				{
					$sql_consulta_tabla_estado_informacion="";
					$sql_consulta_tabla_estado_informacion.="SELECT * FROM gioss_tabla_estado_informacion_4505 ";
					$sql_consulta_tabla_estado_informacion.=" WHERE ";
					$sql_consulta_tabla_estado_informacion.=" fecha_corte_periodo = '".$_POST["year_de_corte"]."-".$_POST["fechas_corte"]."' ";
					$sql_consulta_tabla_estado_informacion.=" AND ";
					//el segundo sera la(s) entidad eapb por lo genera, revisar la parte con entidades territoriales
					$sql_consulta_tabla_estado_informacion.=" codigo_eapb ='EPS000' ";
					$sql_consulta_tabla_estado_informacion.=" AND ";
					$sql_consulta_tabla_estado_informacion.=" codigo_prestador_servicios ='".$codRegEspecial."' ";
					$sql_consulta_tabla_estado_informacion.=" AND ";
					$sql_consulta_tabla_estado_informacion.=" nombre_del_archivo ='".$nombre_archivo_4505."' ";
					$sql_consulta_tabla_estado_informacion.=" AND ";
					$sql_consulta_tabla_estado_informacion.=" codigo_estado_informacion ='1' ";
					$existe_en_estado_de_informacion=$coneccionBD->consultar2($sql_consulta_tabla_estado_informacion);
					
					$bool_existe_en_estado_informacion=false;
					
					if(is_array($existe_en_estado_de_informacion) && count($existe_en_estado_de_informacion)>0)
					{
						$bool_existe_en_estado_informacion=true;
						//echo "<script>alert('existe registro en estado de informacion 4505')</script>";
					}
	
					if ($bool_existe_en_estado_informacion==true) 
					{
					    $validacionNombreArchivo = false;
					    $mostrarMsj = 'inline-block';
					    $mensaje.="El archivo ".$existe_en_estado_de_informacion[0]["nombre_del_archivo"]." ya fue validado con exito en la fecha ".$existe_en_estado_de_informacion[0]["fecha_validacion"]." ,";
					    $mensaje.=" con el numero de secuencia ".$existe_en_estado_de_informacion[0]["numero_secuencia"].". </br>";
					}
				}//fin if proviene de ips archivo agrupado
				
				//VERIFICA SI EL ARCHIVO YA ESTA SIENDO VALIDADO ACTUALMENTE
				
				
				$date_remision_bd=$_POST["year_de_corte"]."-".$_POST["fechas_corte"];
				
				if($tipo_entidad_que_efectua_el_cargue=="individual_ips"
				   || $tipo_entidad_que_efectua_el_cargue=="ent_territoriales"
				   || $tipo_entidad_que_efectua_el_cargue=="agrupado_ips"
				   || $tipo_entidad_que_efectua_el_cargue=="agrupado_ips120"
				   )
				{
					$query_verificacion_esta_siendo_procesado="";
					$query_verificacion_esta_siendo_procesado.=" SELECT * FROM gioss_4505_esta_validando_actualmente ";
					$query_verificacion_esta_siendo_procesado.=" WHERE fecha_remision='".$date_remision_bd."' ";
					$query_verificacion_esta_siendo_procesado.=" AND codigo_entidad_reportadora='".$codRegEspecial."' ";
					$query_verificacion_esta_siendo_procesado.=" AND nombre_archivo='".$nombre_archivo_4505."'  ";
					$query_verificacion_esta_siendo_procesado.=" ; ";
					$resultados_query_verificar_esta_siendo_procesado=$coneccionBD->consultar2($query_verificacion_esta_siendo_procesado);
					if(count($resultados_query_verificar_esta_siendo_procesado)>0)
					{
						foreach($resultados_query_verificar_esta_siendo_procesado as $estado_tiempo_real_archivo)
						{
							if($estado_tiempo_real_archivo["esta_ejecutando"]=="SI")
							{
								$validacionNombreArchivo = false;
								$mostrarMsj = 'inline-block';
								$mensaje .= "El archivo seleccionado se esta validando en este momento. Por favor espere a que este mismo archivo termine de validar</br>";
								break;
							}
						}
						
					}//fin if
				}//fin es individual o ¿ent_territoriales?
				else if($tipo_entidad_que_efectua_el_cargue=="agrupado_eapb")
				{
					//para eapb agrupado usaremos en la parte de codigo entidad reportadora el de la eapb
					$query_verificacion_esta_siendo_procesado="";
					$query_verificacion_esta_siendo_procesado.=" SELECT * FROM gioss_4505_esta_validando_actualmente ";
					$query_verificacion_esta_siendo_procesado.=" WHERE fecha_remision='".$date_remision_bd."' ";
					$query_verificacion_esta_siendo_procesado.=" AND codigo_entidad_reportadora='".$codigo_eapb."' ";
					$query_verificacion_esta_siendo_procesado.=" AND nombre_archivo='".$nombre_archivo_4505."'  ";
					$query_verificacion_esta_siendo_procesado.=" ; ";
					$resultados_query_verificar_esta_siendo_procesado=$coneccionBD->consultar2($query_verificacion_esta_siendo_procesado);
					if(count($resultados_query_verificar_esta_siendo_procesado)>0)
					{
						foreach($resultados_query_verificar_esta_siendo_procesado as $estado_tiempo_real_archivo)
						{
							if($estado_tiempo_real_archivo["esta_ejecutando"]=="SI")
							{
								$validacionNombreArchivo = false;
								$mostrarMsj = 'inline-block';
								$mensaje .= "El archivo seleccionado se esta validando en este momento. Por favor espere a que este mismo archivo termine de validar</br>";
								break;
							}
						}
						
					}//fin if
				}//fin if para cuando es agrupado eapb
				
				//FIN VERIFICA SI EL ARCHIVO YA ESTA SIENDO VALIDADO ACTUALMENTE
		
				$tipoArchivo = substr($tipoArc, 36);
		
				if ($tipoArchivo != 'TXT' && $tipoArchivo != 'txt')
				{
				    $validacionNombreArchivo = false;
				    $mostrarMsj = 'inline-block';
				    $mensaje .= "El tipo de archivo seleccionado no es el indicado. Por favor recuerde que tiene que ser de tipo .txt </br>";
				}
		
				$i++;
			}//fin if nombre diferente de vacio
		}//fin else
	}//fin foreach
	//FIN
	if(count($_FILES)==0)
	{
		$validacionLongitud = false;
		$validacionNombreArchivo = false;
		echo "<script>alert('No se cargo ningun archivo.');</script>";
	}
	
	if($validacionNombreArchivo==false && $mensaje!="")
	{
		
		
		echo "<script>document.getElementById('div_mensaje_error').style.display='inline';</script>";
		echo "<script>document.getElementById('parrafo_error').innerHTML='$mensaje';</script>";
		
		ob_flush();
		flush();
	}
	
	if($validacionLongitud==false && $mensaje!="")
	{
		
		
		echo "<script>document.getElementById('div_mensaje_error').style.display='inline';</script>";
		echo "<script>document.getElementById('parrafo_error').innerHTML='$mensaje';</script>";
		
		ob_flush();
		flush();
	}
	
	//echo "<script>alert(' long $validacionLongitud nom $validacionNombreArchivo');</script>";
	
	//PROCEDE A VALIDAR EL INTERIOR DEL ARCHIVO
	if ($validacionLongitud && $validacionNombreArchivo) 
	{
		//echo "<script>alert('entro4');</script>";
	
		if (sizeof($arreglo_archivos) < 1)
		{
		    $mostrarMsj = 'inline-block';
		    $mensaje = "Por favor seleccione un archivo a cargar";
		}
	
		$codPeriodo = $_POST['periodo'];
			
		$fecha_de_corte_bd=$_POST["year_de_corte"]."-".$_POST["fechas_corte"];
	
		//echo "<script>alert('$nombre_archivo_4505');</script>";
		
		$tipo_periodo_tiempo="trimestral";
		if(isset($_POST['tipo_periodo_tiempo']))
		{
			$tipo_periodo_tiempo=$_POST['tipo_periodo_tiempo'];
		}
		
		
		$array_res_validacion4505=array();
		$lectura = new LecturaArchivo();
		$array_res_validacion4505 = $lectura->lecturaPyP($arreglo_archivos,
								 $numero_de_identificacion_de_la_entidad_prestadora,
								 $modulo_de_informacion,
								 $tema_de_informacion,
								 $tipo_de_identificacion_entidad_reportadora,
								 $tipo_de_regimen_de_la_informacion_reportada,
								 $consecutivo_de_archivo,
								 $numero_de_identificacion_de_la_epba,
								 $codRegEspecial,
								 $fecha_de_corte_bd,
								 $nombre_archivo_4505,
								 $codPeriodo,
								 $codigo_eapb,
								 $tipo_entidad_que_efectua_el_cargue,
								 $cod_dpto_filtro,
								 $cod_mpio_filtro,
								 $nick_user,
								 $tipo_periodo_tiempo);
		
			
		
	}//fin if
}//fin parte donde guarda 4505

/*
echo "<script>$( '#eapb' ).combobox();
$( '#prestador' ).combobox();
</script>";
*/


?>