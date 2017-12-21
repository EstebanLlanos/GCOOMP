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
	
	header ("Location: ../index.html");
}

$menu= $_SESSION['menu_logueo_html'];
$nombre= $_SESSION['nombre_completo'];


$tipo_id=$_SESSION['tipo_id'];
$identificacion=$_SESSION['identificacion'];

$perfil_usuario_actual= $_SESSION['tipo_perfil'];
$entidad_salud_usuario_actual= $_SESSION['entidad_asociada_al_nick'];

$mostrarResultado = "none";
$mensaje="";

//SELECTOR EAPB-ASOCIADA_ASOCIADA_USUARIO
$eapb="";
$eapb.="<div id='div_eapb'>";
$eapb.="<select id='eapb' name='eapb' class='campo_azul'>";
$eapb.="<option value='none'>Seleccione un EAPB</option>";

/*
$sql_consulta_eapb_usuario_prestador="SELECT gios_entidad_administradora.cod_entidad_administradora,gios_entidad_administradora.nom_entidad_administradora FROM ";
$sql_consulta_eapb_usuario_prestador.=" gios_usuario_entidad_prestadora_eapb INNER JOIN gios_entidad_administradora ON (gios_usuario_entidad_prestadora_eapb.cod_entidad_administradora = gios_entidad_administradora.cod_entidad_administradora) ";
$sql_consulta_eapb_usuario_prestador.=" WHERE tipo_identificacion_usuario='".$tipo_id."' AND  identificacion_usuario='".$identificacion."' ; ";
*/
$sql_consulta_eapb_usuario_prestador="SELECT ea.codigo_entidad,ea.nombre_de_la_entidad FROM ";
$sql_consulta_eapb_usuario_prestador.=" gioss_relacion_entre_entidades_salud re INNER JOIN gioss_entidades_sector_salud ea ON (re.entidad2 = ea.codigo_entidad) ";
$sql_consulta_eapb_usuario_prestador.=" WHERE re.entidad1='".$entidad_salud_usuario_actual."' ";
$resultado_query_prestador_usuario=$coneccionBD->consultar2($sql_consulta_eapb_usuario_prestador);

if(count($resultado_query_prestador_usuario)>0)
{
	foreach($resultado_query_prestador_usuario as $eapb_prestador_usuario_res)
	{
		$eapb.="<option value='".$eapb_prestador_usuario_res['codigo_entidad']."'>".$eapb_prestador_usuario_res['nombre_de_la_entidad']."</option>";
	}
}

$eapb.="</select>";
$eapb.="</div>";
//FIN

$selector_periodo="";
$selector_periodo.="<select id='periodo' name='periodo' class='campo_azul' onchange='seleccionar_fecha_de_corte();'>";
$selector_periodo.="<option value='1'>Periodo 1</option>";
$selector_periodo.="<option value='2'>Periodo 2</option>";
$selector_periodo.="<option value='3'>Periodo 3</option>";
$selector_periodo.="<option value='4'>Periodo 4</option>";
$selector_periodo.="</select>";


//SELECTOR DEPARTAMENTO
$selector_departamento="";
$selector_departamento.="<select id='dpto' name='dpto' class='campo_azul' onchange='consultar_mpio();'>";
$selector_departamento.="<option value='none'>Seleccione un departamento</option>";

$query_departamentos="select * from gios_dpto;";
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

//SELECTOR FECHAS DE CORTE
$selector_fechas_corte="";
$selector_fechas_corte.="<select id='fechas_corte' name='fechas_corte' class='campo_azul' onchange='seleccionar_periodo();'>";
$selector_fechas_corte.="<option value='3-31'>3-31</option>";
$selector_fechas_corte.="<option value='6-30'>6-30</option>";
$selector_fechas_corte.="<option value='9-30'>9-30</option>";
$selector_fechas_corte.="<option value='12-31'>12-31</option>";
$selector_fechas_corte.="</select>";
//FIN SELECTOR FECHAS DE CORTE


$mensaje.="<div id='div_mensaje' ></div>";

//IMPRIME O MUESTRA LA PAGINA HTML
$smarty->assign("campo_fechas_corte", $selector_fechas_corte, true);

$smarty->assign("selector_municipio", $selector_municipio, true);
$smarty->assign("selector_departamento", $selector_departamento, true);

$smarty->assign("campo_periodo", $selector_periodo, true);

$smarty->assign("campo_eapb", $eapb, true);
$smarty->assign("nombre", $nombre, true);
$smarty->assign("menu", $menu, true);
$smarty->assign("mensaje_proceso", $mensaje, true);
$smarty->assign("mostrarResultado",$mostrarResultado,true);

$smarty->display('conseapb4505.html.tpl');

//FIN IMPRIME O MEUSTRA LA PAGINA HTML


if(isset($_POST["selector_estado_info"]))
{
	$indice_inicio=$_POST["index_inicio"];
	$indice_fin=$_POST["index_fin"];
	
	$re_asignar_por_javascript="";
	$re_asignar_por_javascript.="<script>";
	$re_asignar_por_javascript.="document.getElementById('selector_estado_info').value='".$_POST["selector_estado_info"]."';";
	$re_asignar_por_javascript.="document.getElementById('rango_resultados').value='".$_POST["rango_resultados"]."';";
	$re_asignar_por_javascript.="document.getElementById('eapb').value='".$_POST["eapb"]."';";
	$re_asignar_por_javascript.="document.getElementById('dpto').value='".$_POST["dpto"]."';";
	$re_asignar_por_javascript.="consultar_mpio();";
	$re_asignar_por_javascript.="document.getElementById('mpio').value='".$_POST["mpio"]."';";
	$re_asignar_por_javascript.="document.getElementById('fechas_corte').value='".$_POST["fechas_corte"]."';";
	$re_asignar_por_javascript.="document.getElementById('periodo').value='".$_POST["periodo"]."';";
	$re_asignar_por_javascript.="document.getElementById('year_de_corte').value='".$_POST["year_de_corte"]."';";
	$re_asignar_por_javascript.="document.getElementById('index_inicio').value='".$_POST["index_inicio"]."';";
	$re_asignar_por_javascript.="document.getElementById('index_fin').value='".$_POST["index_fin"]."';";
	$re_asignar_por_javascript.="";
	$re_asignar_por_javascript.="</script>";
	
	
	echo $re_asignar_por_javascript;
	
		
	$html_para_div_mensaje="";
	
	$query_consulta4505_perfil_eapb="";
	$query_consulta4505_perfil_eapb.="SELECT * FROM ";
	if($_POST["selector_estado_info"]=="validada")
	{
		$query_consulta4505_perfil_eapb.=" gios_datos_validados_exito_r4505 da ";
	}
	else
	{
		$query_consulta4505_perfil_eapb.=" gios_datos_rechazados_r4505 da ";
	}
	$query_consulta4505_perfil_eapb.=" INNER JOIN gioss_entidades_sector_salud ea ON (da.numero_de_identificacion_de_la_entidad_administradora = ea.codigo_entidad) ";
	$hubo_condicion_anterior=false;
	if($_POST["eapb"]!="none" || $_POST["dpto"]!="none" || $_POST["mpio"]!="none" || $_POST["year_de_corte"]!="")
	{
		$query_consulta4505_perfil_eapb.=" WHERE ";
		if($_POST["eapb"]!="none")
		{
			if($hubo_condicion_anterior)
			{
				$query_consulta4505_perfil_eapb.=" AND ";
			}
			$query_consulta4505_perfil_eapb.=" da.numero_de_identificacion_de_la_entidad_administradora='".$_POST["eapb"]."' ";
			$hubo_condicion_anterior=true;
		}
		if($_POST["dpto"]!="none")
		{
			if($hubo_condicion_anterior)
			{
				$query_consulta4505_perfil_eapb.=" AND ";
			}
			$query_consulta4505_perfil_eapb.=" ea.codigo_dpto::integer='".$_POST["dpto"]."' ";
			$hubo_condicion_anterior=true;
		}
		if($_POST["mpio"]!="none")
		{
			if($hubo_condicion_anterior)
			{
				$query_consulta4505_perfil_eapb.=" AND ";
			}
			$query_consulta4505_perfil_eapb.=" ea.cod_mpio::integer='".$_POST["mpio"]."' ";
			$hubo_condicion_anterior=true;
		}
		if($_POST["year_de_corte"]!="")
		{
			$fecha_de_corte_full=$_POST["year_de_corte"]."-".$_POST["fechas_corte"];
			if($hubo_condicion_anterior)
			{
				$query_consulta4505_perfil_eapb.=" AND ";
			}
			$query_consulta4505_perfil_eapb.=" da.fecha_de_corte='".$fecha_de_corte_full."' ";
			$hubo_condicion_anterior=true;
		}
	}//if para ver si se seleccionaron restricciones o buscara todos
	
	
	$query_consulta4505_perfil_eapb.=" ORDER BY numero_fila LIMIT $indice_fin OFFSET $indice_inicio ";
	$query_consulta4505_perfil_eapb.=";";
	
	$resultado_consulta4505_perfil_eapb=$coneccionBD->consultar2($query_consulta4505_perfil_eapb);
	
	$html_para_div_mensaje.="<span style='color:white;'>QUERY: ".$query_consulta4505_perfil_eapb."</span>";
	
	if(count($resultado_consulta4505_perfil_eapb)>0)
	{
	
		
		$html_para_div_mensaje.="<h5 align='left'> Resultado busqueda EAPB para 4505, mostrando ".$indice_fin." resultados: </h5>";
		$html_para_div_mensaje.="";
		
		
		
		$html_para_div_mensaje.="<div id='resultados' style='overflow: scroll;width:900px;height:580px;border-style:solid;border-width:medium;'>";
		$html_para_div_mensaje.="<table>";
		$html_para_div_mensaje.="<tr>";
		$html_para_div_mensaje.="<td style='border-style:solid;border-width:medium;'>numero fila</td>";
		$html_para_div_mensaje.="<td style='border-style:solid;border-width:medium;'>modulo de informacion</td>";
		$html_para_div_mensaje.="<td style='border-style:solid;border-width:medium;'>tema deinformacion</td>";
		$html_para_div_mensaje.="<td style='border-style:solid;border-width:medium;'>fecha de corte</td>";
		$html_para_div_mensaje.="<td style='border-style:solid;border-width:medium;'>tipo de identificacion entidad reportadora</td>";
		$html_para_div_mensaje.="<td style='border-style:solid;border-width:medium;'>numero de identificacion de la entidad administradora</td>";
		$html_para_div_mensaje.="<td style='border-style:solid;border-width:medium;'>tipo de regimen de la informacion reportada</td>";
		$html_para_div_mensaje.="<td style='border-style:solid;border-width:medium;'>tipo de regimen de la informacion reportada</td>";
		$html_para_div_mensaje.="<td style='border-style:solid;border-width:medium;'>consecutivo de archivo</td>";		
		$html_para_div_mensaje.="<td style='border-style:solid;border-width:medium;'>numero de secuencia</td>";
		$html_para_div_mensaje.="<td style='border-style:solid;border-width:medium;'>cod registro especial_pss</td>";
		$html_para_div_mensaje.="<td style='border-style:solid;border-width:medium;'>nit prestador</td>";
		
		$cont_campos=0;
		while($cont_campos<119)
		{
			$html_para_div_mensaje.="<td style='border-style:solid;border-width:medium;'>campo$cont_campos</td>";
			$cont_campos++;
		}
		
		$html_para_div_mensaje.="</tr>";
		
		foreach($resultado_consulta4505_perfil_eapb as $fila4505)
		{
			$html_para_div_mensaje.="<tr>";
			
			$html_para_div_mensaje.="<td style='border-style:solid;border-width:1px;'>".$fila4505["numero_fila"]."</td>";
			$html_para_div_mensaje.="<td style='border-style:solid;border-width:1px;'>".$fila4505["modulo_de_informacion"]."</td>";
			$html_para_div_mensaje.="<td style='border-style:solid;border-width:1px;'>".$fila4505["tema_de_informacion"]."</td>";
			$html_para_div_mensaje.="<td style='border-style:solid;border-width:1px;'>".$fila4505["fecha_de_corte"]."</td>";
			$html_para_div_mensaje.="<td style='border-style:solid;border-width:1px;'>".$fila4505["tipo_de_identificacion_entidad_reportadora"]."</td>";
			$html_para_div_mensaje.="<td style='border-style:solid;border-width:1px;'>".$fila4505["numero_de_identificacion_de_la_entidad_administradora"]."</td>";
			$html_para_div_mensaje.="<td style='border-style:solid;border-width:1px;'>".$fila4505["tipo_de_regimen_de_la_informacion_reportada"]."</td>";
			$html_para_div_mensaje.="<td style='border-style:solid;border-width:1px;'>".$fila4505["tipo_de_regimen_de_la_informacion_reportada"]."</td>";
			$html_para_div_mensaje.="<td style='border-style:solid;border-width:1px;'>".$fila4505["consecutivo_de_archivo"]."</td>";			
			$html_para_div_mensaje.="<td style='border-style:solid;border-width:1px;'>".$fila4505["numero_de_secuencia"]."</td>";
			$html_para_div_mensaje.="<td style='border-style:solid;border-width:1px;'>".$fila4505["cod_registro_especial_pss"]."</td>";
			$html_para_div_mensaje.="<td style='border-style:solid;border-width:1px;'>".$fila4505["nit_prestador"]."</td>";
			
			$cont_campos=0;
			while($cont_campos<119)
			{
				$html_para_div_mensaje.="<td style='border-style:solid;border-width:1px;'>".$fila4505["campo".$cont_campos]."</td>";
				$cont_campos++;
			}
			
			$html_para_div_mensaje.="</tr>";
		}
		
		$html_para_div_mensaje.="</table>";
		$html_para_div_mensaje.="</div>";
		
		//BOTONES NAVEGACION
		$html_para_div_mensaje.="<br></br>";
		
		$html_para_div_mensaje.="<p align='left' width='100%'>";
		$html_para_div_mensaje.="<table>";
		$html_para_div_mensaje.="<tr>";
		$html_para_div_mensaje.="<td style='text-align:left;'>";
		$html_para_div_mensaje.="<input type='button' class='btn btn-success color_boton' value='<-Atras' onclick='enviar_atras();' /> ";
		$html_para_div_mensaje.="<input type='button' class='btn btn-success color_boton' value='Siguiente->' onclick='enviar_adelante();' />";
		$html_para_div_mensaje.="</td>";
		$html_para_div_mensaje.="</tr>";
		$html_para_div_mensaje.="</table>";
		$html_para_div_mensaje.="</p>";
		//FIN BOTONES NAVEGACION
		
		
	}
	
	echo "<script>document.getElementById('div_mensaje').innerHTML=\" ".$html_para_div_mensaje." \";</script>";
}//si se ha enviado el formulario



?>