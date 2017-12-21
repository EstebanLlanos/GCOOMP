<?php
ignore_user_abort(true);
set_time_limit(2592000);
ini_set('max_execution_time', 2592000);
try
{
	//echo "<script>alert('entro aca');</script>";
	ini_set('memory_limit', '4000M');
}
catch(Exception $e)
{
	echo "<script>alert('entro ex');</script>";
	ini_set('memory_limit', '2000M');
}
//echo "<script>alert('entro afuera');</script>";


require '../librerias_externas/Smarty-3.1.17/libs/Smarty.class.php';
include_once ('../utiles/clase_coneccion_bd.php');
include_once ('../utiles/funciones_crear_menu.php');
require_once '../utiles/queries_utiles_bd.php';

require_once 'validador_fixer_4505.php';


require_once '../utiles/configuracion_tipo_entidad.php';

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
$eapb.="<select id='eapb' name='eapb' class='campo_azul'>";
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
$selector_fechas_corte.="<select id='fechas_corte' name='fechas_corte' class='campo_azul' onchange='seleccionar_periodo();'>";
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
$selector_periodo.="<select id='periodo' name='periodo' class='campo_azul' onchange='seleccionar_fecha_de_corte();' style='width:230px;'>";
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

$utilidades = new Utilidades();
$rutaTemporal = '../TEMPORALES/';
$validacionLongitud = true;
$validacionNombreArchivo = true;


$mensaje = "";
$mostrarMsj = "none";

$mensajeExito = "";
$mostrarMsj2 = "none";

$mensaje_proceso="";

//TIPO ENTIDAD

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
						    <!--<option value='ent_territoriales'>Agrupado Entidad Territorial</option>-->
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

//FIN TIPO ENTIDAD

$smarty->assign("proveniente_de_prestador_o_eapb", $proveniente_de_prestador_o_eapb, true);

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
$smarty->display('fixer4505.html.tpl');

//PARTE DONDE GUARDA LOS ARCHIVOS 4505
if(isset($_POST["fecha_remision"]))
{
	$tipo_entidad_que_efectua_el_cargue=$_POST["tipo_archivo_4505"];
	

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

			if ($infoArchivos['name'] != '') 
			{	
				$fecha_para_archivo= date('YmdHis');
				$carpetaOrig4505="ORIGFIX4505".$fecha_para_archivo;
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
				$rutaTemporal = '../TEMPORALES/';

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
		
				if ($array_fecha_corte[1]!= $array_fecha_de_corte_consultada[1]
				    || $array_fecha_corte[2]!= $array_fecha_de_corte_consultada[2]) 
				{
		
				    $validacionNombreArchivo = false;
				    $mostrarMsj = 'inline-block';
				    $mensaje .= 'La fecha corte referenciada en el nombre del archivo no es la correspondiente. Recuerde que la fecha corte referenciada en el archivo debe ser igual a la fecha fin del periodo seleccionado. </br>';
				}
						
				//tipo id entidad prestadora
				
				$array_prestador=explode(";;",$_POST['prestador']);
				$codRegEspecial = $array_prestador[0];
				
				$codigo_eapb= $_POST['eapb'];
				
				$sql_consulta_prestador="";
				if($tipo_entidad_que_efectua_el_cargue=="individual_ips"
					|| $tipo_entidad_que_efectua_el_cargue=="agrupado_ips"
					|| $tipo_entidad_que_efectua_el_cargue=="agrupado_ips120"
					)
				{
					$sql_consulta_prestador.="select * from gioss_entidades_sector_salud WHERE codigo_entidad='".$codRegEspecial."';";
				}
				else if($tipo_entidad_que_efectua_el_cargue=="agrupado_eapb")
				{
					$sql_consulta_prestador.="select * from gioss_entidades_sector_salud WHERE codigo_entidad='".$codigo_eapb."';";
				}
				$resultado_query_prestador=$coneccionBD->consultar2($sql_consulta_prestador);
				
				$codTipoIdEntidad = $resultado_query_prestador[0]["cod_tipo_entidad"];
				
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
						$nitIPS = $resultado_query_prestador[0]["numero_identificacion"];
						
						//adiciona los ceros a la izquierda 
				if (strlen($nitIPS) < 12) 
						{
		
				    for ($i = strlen($nitIPS); $i < 12; $i++) {
		
					$nitIPS = '0' . $nitIPS;
				    }
				}
		
				if ($idIPS != $nitIPS)
				{
		
				    $validacionNombreArchivo = false;
				    $mostrarMsj = 'inline-block';
				    $mensaje .= 'El numero de identificacion de la entidad prestadora referenciado en el nombre del archivo no es la correspondiente al numero de identificacion de la entidad prestadora asociada. Por favor revise el nombre del archivo. </br>';
				}
						
				//se asigna de nuevo despues de la comparacion para la comparacion en la base de datos
				$nitIPS = $resultado_query_prestador[0]["numero_identificacion"];
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
				    $mensaje.=' C (Contributivo), S (Subsidiado), E (Especial), P (Excepcion), N (No Asegurado). </br>';
				}
				$tipo_de_regimen_de_la_informacion_reportada=$tipoRegimen;
		
				$longNumeroRemision = $_POST['numero_remision'];
		
				if (strlen($longNumeroRemision) < 2)
				{
		
				    for ($i = strlen($longNumeroRemision); $i < 2; $i++) {
		
					$longNumeroRemision = '0' . $longNumeroRemision;
				    }
				}

				
				
				
				$query_numero_identificacion_entidad_administradora="";
				$query_numero_identificacion_entidad_administradora.="SELECT nit FROM gios_entidad_administradora WHERE cod_entidad_administradora='$codigo_eapb'; ";
				$resultado_query_nit_eapb=$coneccionBD->consultar2($query_numero_identificacion_entidad_administradora);

                
				$numero_de_identificacion_de_la_epba= $resultado_query_nit_eapb[0]["nit"];
				
				
		
				$numArchivoCargado = substr($tipoArc, 33, -4);
		
				if ($numArchivoCargado != $longNumeroRemision) {
				    $validacionNombreArchivo = false;
				    $mostrarMsj = 'inline-block';
				    $mensaje .= "El numero de remision ingresado no es el mismo numero que el identificado en el numero consecutivo referenciado en el nombre del archivo </br>";
				}
						
				$consecutivo_de_archivo=$numArchivoCargado;
				
				//VERIFICAR SI FUE VALIDADO DE ALGUN MODO
				if($tipo_entidad_que_efectua_el_cargue=="individual_ips")
				{
					//SI FUE VALIDADO CON EXITO
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
					
					if ($bool_existe_en_estado_informacion==true) 
					{
					    $validacionNombreArchivo = false;
					    $mostrarMsj = 'inline-block';
					    $mensaje.="El archivo ".$existe_en_estado_de_informacion[0]["nombre_del_archivo"]." ya fue validado con ";
					    $mensaje.=" exito en la fecha ".$existe_en_estado_de_informacion[0]["fecha_validacion"]." no es necesario que realice la correccion ,";
					    $mensaje.=" con el numero de secuencia ".$existe_en_estado_de_informacion[0]["numero_secuencia"].". </br>";
					}
					//FIN SI FUE VALIDADO CON EXITO
					
					//SI FUE VALIDADO COMO RECHAZADO PARA PODER CORREGIR NO DEJA PASAR SI NO FUE VALIDADO ANTES
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
					$sql_consulta_tabla_estado_informacion.=" codigo_estado_informacion ='2' ";
					$existe_en_estado_de_informacion=$coneccionBD->consultar2($sql_consulta_tabla_estado_informacion);
					
					$bool_existe_en_estado_informacion=false;
					if(is_array($existe_en_estado_de_informacion) && count($existe_en_estado_de_informacion)>0)
					{
						$bool_existe_en_estado_informacion=true;
						//echo "<script>alert('existe registro en estado de informacion 4505')</script>";
					}
					
					//se saca error porque necesita que haya sido validado con anterioridad
					if ($bool_existe_en_estado_informacion==false) 
					{
					    $validacionNombreArchivo = false;
					    $mostrarMsj = 'inline-block';
					    $mensaje.="El archivo ".$nombre_archivo_4505." no ";
					    $mensaje.="fue validado con anterioridad por favor valide el archivo por medio de la interfaz ";
					    $mensaje.=" de carga y validacion 4505 para poder corregir el archivo ,";
					    
					}
					//FIN SI FUE VALIDADO COMO RECHAZADO PARA PODER CORREGIR NO DEJA PASAR SI NO FUE VALIDADO ANTES
				}//fin if prestador archivo individual
				else if($tipo_entidad_que_efectua_el_cargue=="agrupado_eapb")
				{
					//SI FUE VALIDADO CON EXITO
					$sql_consulta_tabla_estado_informacion="";
					$sql_consulta_tabla_estado_informacion.="SELECT * FROM gioss_tabla_estado_informacion_4505 ";
					$sql_consulta_tabla_estado_informacion.=" WHERE ";
					$sql_consulta_tabla_estado_informacion.=" fecha_corte_periodo = '".$_POST["year_de_corte"]."-".$_POST["fechas_corte"]."' ";
					$sql_consulta_tabla_estado_informacion.=" AND ";
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
					    $mensaje.="El archivo ".$existe_en_estado_de_informacion[0]["nombre_del_archivo"]." ya fue validado con ";
					    $mensaje.=" exito en la fecha ".$existe_en_estado_de_informacion[0]["fecha_validacion"]." no es necesario que realice la correccion ,";
					    $mensaje.=" con el numero de secuencia ".$existe_en_estado_de_informacion[0]["numero_secuencia"].". </br>";
					}
					//FIN SI FUE VALIDADO CON EXITO
					
					//SI FUE VALIDADO COMO RECHAZADO PARA PODER CORREGIR NO DEJA PASAR SI NO FUE VALIDADO ANTES
					$sql_consulta_tabla_estado_informacion="";
					$sql_consulta_tabla_estado_informacion.="SELECT * FROM gioss_tabla_estado_informacion_4505 ";
					$sql_consulta_tabla_estado_informacion.=" WHERE ";
					$sql_consulta_tabla_estado_informacion.=" fecha_corte_periodo = '".$_POST["year_de_corte"]."-".$_POST["fechas_corte"]."' ";
					$sql_consulta_tabla_estado_informacion.=" AND ";
					$sql_consulta_tabla_estado_informacion.=" codigo_eapb ='".$codigo_eapb."' ";
					$sql_consulta_tabla_estado_informacion.=" AND ";
					$sql_consulta_tabla_estado_informacion.=" codigo_prestador_servicios ='AGRUP_EAPB' ";
					$sql_consulta_tabla_estado_informacion.=" AND ";
					$sql_consulta_tabla_estado_informacion.=" nombre_del_archivo ='".$nombre_archivo_4505."' ";
					$sql_consulta_tabla_estado_informacion.=" AND ";
					$sql_consulta_tabla_estado_informacion.=" codigo_estado_informacion ='2' ";
					$existe_en_estado_de_informacion=$coneccionBD->consultar2($sql_consulta_tabla_estado_informacion);
					
					$bool_existe_en_estado_informacion=false;
					if(is_array($existe_en_estado_de_informacion) && count($existe_en_estado_de_informacion)>0)
					{
						$bool_existe_en_estado_informacion=true;
						//echo "<script>alert('existe registro en estado de informacion 4505')</script>";
					}
					
					//se saca error porque necesita que haya sido validado con anterioridad
					if ($bool_existe_en_estado_informacion==false) 
					{
					    $validacionNombreArchivo = false;
					    $mostrarMsj = 'inline-block';
					    $mensaje.="El archivo ".$nombre_archivo_4505." no ";
					    $mensaje.="fue validado con anterioridad por favor valide el archivo por medio de la interfaz ";
					    $mensaje.=" de carga y validacion 4505 para poder corregir el archivo ,";
					    
					}
					//FIN SI FUE VALIDADO COMO RECHAZADO PARA PODER CORREGIR NO DEJA PASAR SI NO FUE VALIDADO ANTES
				}//fin if agrupado eapb
				else if($tipo_entidad_que_efectua_el_cargue=="agrupado_ips")
				{
					//SI FUE VALIDADO CON EXITO
					$sql_consulta_tabla_estado_informacion="";
					$sql_consulta_tabla_estado_informacion.="SELECT * FROM gioss_tabla_estado_informacion_4505 ";
					$sql_consulta_tabla_estado_informacion.=" WHERE ";
					$sql_consulta_tabla_estado_informacion.=" fecha_corte_periodo = '".$_POST["year_de_corte"]."-".$_POST["fechas_corte"]."' ";
					$sql_consulta_tabla_estado_informacion.=" AND ";
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
					    $mensaje.="El archivo ".$existe_en_estado_de_informacion[0]["nombre_del_archivo"]." ya fue validado con ";
					    $mensaje.=" exito en la fecha ".$existe_en_estado_de_informacion[0]["fecha_validacion"]." no es necesario que realice la correccion ,";
					    $mensaje.=" con el numero de secuencia ".$existe_en_estado_de_informacion[0]["numero_secuencia"].". </br>";
					}
					//FIN SI FUE VALIDADO CON EXITO
					
					//SI FUE VALIDADO COMO RECHAZADO PARA PODER CORREGIR NO DEJA PASAR SI NO FUE VALIDADO ANTES
					$sql_consulta_tabla_estado_informacion="";
					$sql_consulta_tabla_estado_informacion.="SELECT * FROM gioss_tabla_estado_informacion_4505 ";
					$sql_consulta_tabla_estado_informacion.=" WHERE ";
					$sql_consulta_tabla_estado_informacion.=" fecha_corte_periodo = '".$_POST["year_de_corte"]."-".$_POST["fechas_corte"]."' ";
					$sql_consulta_tabla_estado_informacion.=" AND ";
					$sql_consulta_tabla_estado_informacion.=" codigo_eapb ='AGRUP_IPS' ";
					$sql_consulta_tabla_estado_informacion.=" AND ";
					$sql_consulta_tabla_estado_informacion.=" codigo_prestador_servicios ='".$codRegEspecial."' ";
					$sql_consulta_tabla_estado_informacion.=" AND ";
					$sql_consulta_tabla_estado_informacion.=" nombre_del_archivo ='".$nombre_archivo_4505."' ";
					$sql_consulta_tabla_estado_informacion.=" AND ";
					$sql_consulta_tabla_estado_informacion.=" codigo_estado_informacion ='2' ";
					$existe_en_estado_de_informacion=$coneccionBD->consultar2($sql_consulta_tabla_estado_informacion);
					
					$bool_existe_en_estado_informacion=false;
					if(is_array($existe_en_estado_de_informacion) && count($existe_en_estado_de_informacion)>0)
					{
						$bool_existe_en_estado_informacion=true;
						//echo "<script>alert('existe registro en estado de informacion 4505')</script>";
					}
					
					//se saca error porque necesita que haya sido validado con anterioridad
					if ($bool_existe_en_estado_informacion==false) 
					{
					    $validacionNombreArchivo = false;
					    $mostrarMsj = 'inline-block';
					    $mensaje.="El archivo ".$nombre_archivo_4505." no ";
					    $mensaje.="fue validado con anterioridad por favor valide el archivo por medio de la interfaz ";
					    $mensaje.=" de carga y validacion 4505 para poder corregir el archivo ,";
					    
					}
					//FIN SI FUE VALIDADO COMO RECHAZADO PARA PODER CORREGIR NO DEJA PASAR SI NO FUE VALIDADO ANTES
				}//fin if agrupado ips
				else if($tipo_entidad_que_efectua_el_cargue=="agrupado_ips120")
				{
					//SI FUE VALIDADO CON EXITO
					$sql_consulta_tabla_estado_informacion="";
					$sql_consulta_tabla_estado_informacion.="SELECT * FROM gioss_tabla_estado_informacion_4505 ";
					$sql_consulta_tabla_estado_informacion.=" WHERE ";
					$sql_consulta_tabla_estado_informacion.=" fecha_corte_periodo = '".$_POST["year_de_corte"]."-".$_POST["fechas_corte"]."' ";
					$sql_consulta_tabla_estado_informacion.=" AND ";
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
					    $mensaje.="El archivo ".$existe_en_estado_de_informacion[0]["nombre_del_archivo"]." ya fue validado con ";
					    $mensaje.=" exito en la fecha ".$existe_en_estado_de_informacion[0]["fecha_validacion"]." no es necesario que realice la correccion ,";
					    $mensaje.=" con el numero de secuencia ".$existe_en_estado_de_informacion[0]["numero_secuencia"].". </br>";
					}
					//FIN SI FUE VALIDADO CON EXITO
					
					//SI FUE VALIDADO COMO RECHAZADO PARA PODER CORREGIR NO DEJA PASAR SI NO FUE VALIDADO ANTES
					$sql_consulta_tabla_estado_informacion="";
					$sql_consulta_tabla_estado_informacion.="SELECT * FROM gioss_tabla_estado_informacion_4505 ";
					$sql_consulta_tabla_estado_informacion.=" WHERE ";
					$sql_consulta_tabla_estado_informacion.=" fecha_corte_periodo = '".$_POST["year_de_corte"]."-".$_POST["fechas_corte"]."' ";
					$sql_consulta_tabla_estado_informacion.=" AND ";
					$sql_consulta_tabla_estado_informacion.=" codigo_eapb ='EPS000' ";
					$sql_consulta_tabla_estado_informacion.=" AND ";
					$sql_consulta_tabla_estado_informacion.=" codigo_prestador_servicios ='".$codRegEspecial."' ";
					$sql_consulta_tabla_estado_informacion.=" AND ";
					$sql_consulta_tabla_estado_informacion.=" nombre_del_archivo ='".$nombre_archivo_4505."' ";
					$sql_consulta_tabla_estado_informacion.=" AND ";
					$sql_consulta_tabla_estado_informacion.=" codigo_estado_informacion ='2' ";
					$existe_en_estado_de_informacion=$coneccionBD->consultar2($sql_consulta_tabla_estado_informacion);
					
					$bool_existe_en_estado_informacion=false;
					if(is_array($existe_en_estado_de_informacion) && count($existe_en_estado_de_informacion)>0)
					{
						$bool_existe_en_estado_informacion=true;
						//echo "<script>alert('existe registro en estado de informacion 4505')</script>";
					}
					
					//se saca error porque necesita que haya sido validado con anterioridad
					if ($bool_existe_en_estado_informacion==false) 
					{
					    $validacionNombreArchivo = false;
					    $mostrarMsj = 'inline-block';
					    $mensaje.="El archivo ".$nombre_archivo_4505." no ";
					    $mensaje.="fue validado con anterioridad por favor valide el archivo por medio de la interfaz ";
					    $mensaje.=" de carga y validacion 4505 para poder corregir el archivo ,";
					    
					}
					//FIN SI FUE VALIDADO COMO RECHAZADO PARA PODER CORREGIR NO DEJA PASAR SI NO FUE VALIDADO ANTES
				}//fin if agrupado ips
				//FIN VERIFICAR SI FUE VALIDADO DE ALGUN MODO
				
				//VERIFICA SI EL ARCHIVO YA ESTA SIENDO REPARADO ACTUALMENTE
				
				
				$date_remision_bd=$_POST["year_de_corte"]."-".$_POST["fechas_corte"];
				
				$query_verificacion_esta_siendo_procesado="";
				$query_verificacion_esta_siendo_procesado.=" SELECT * FROM gioss_4505_esta_reparando_ar_actualmente ";
				$query_verificacion_esta_siendo_procesado.=" WHERE fecha_corte_archivo_en_reparacion='".$date_remision_bd."' ";
				if($tipo_entidad_que_efectua_el_cargue=="agrupado_eapb")
				{
					$query_verificacion_esta_siendo_procesado.=" AND codigo_entidad_reportadora='".$codigo_eapb."' ";
				}
				else // valido para agrupado_ips y agrupado_ips120
				{
					$query_verificacion_esta_siendo_procesado.=" AND codigo_entidad_reportadora='".$codRegEspecial."' ";
				}
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
							$mensaje .= "El archivo seleccionado se esta reparando en este momento. Por favor espere a que este mismo archivo termine de reparar.</br>";
							break;
						}
					}
					
				}
				
				//FIN VERIFICA SI EL ARCHIVO YA ESTA SIENDO REPARADO ACTUALMENTE
		
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
	
	//PROCEDE A VALIDAR EL INTERIOR DEL ARCHIVO
	if ($validacionLongitud && $validacionNombreArchivo) 
	{
	
	
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
		
		//se pone AGRUP_EAPB como prestador para evitar realizar cambios en validador_fixer.php
		if($tipo_entidad_que_efectua_el_cargue=="agrupado_eapb")
		{
			$codRegEspecial="AGRUP_EAPB";
		}//fin if
		else if($tipo_entidad_que_efectua_el_cargue=="agrupado_ips")
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
		}//fin else if
		
		$array_res_validacion4505=array();
		$lectura = new LecturaArchivo();
		$array_res_validacion4505 = $lectura->lecturaPyP($arreglo_archivos,
								 $numero_de_identificacion_de_la_entidad_prestadora,
								 $modulo_de_informacion,$tema_de_informacion,
								 $tipo_de_identificacion_entidad_reportadora,
								 $tipo_de_regimen_de_la_informacion_reportada,
								 $consecutivo_de_archivo,
								 $numero_de_identificacion_de_la_epba,
								 $codRegEspecial,
								 $fecha_de_corte_bd,
								 $nombre_archivo_4505,
								 $codPeriodo,
								 $codigo_eapb,
								 $tipo_periodo_tiempo,
								 $tipo_entidad_que_efectua_el_cargue);
		
			
		
	}//fin if
}//fin parte donde guarda 4505


?>