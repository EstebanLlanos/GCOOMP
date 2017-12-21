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


//consultar en gios_usuario_entidad_prestadora_eapb , la cual contiene la relacion entre usuario-ips-eapb 

//SELECTOR PRESTADOR-ASOCIADO-USUARIO
$prestador="";
$prestador.="<select id='prestador' name='prestador' class='campo_azul' onchange='consultar_eapb(\"".$tipo_id."\",\"".$identificacion."\");'>";
$prestador.="<option value='none'>Seleccione un prestador de salud</option>";

/*
$sql_consulta_prestador_usuario="SELECT gios_prestador_servicios_salud.cod_registro_especial_pss,gios_prestador_servicios_salud.nom_entidad_prestadora FROM ";
$sql_consulta_prestador_usuario.=" gios_usuario_entidad_prestadora_eapb INNER JOIN gios_prestador_servicios_salud ON (gios_usuario_entidad_prestadora_eapb.cod_registro_especial_pss = gios_prestador_servicios_salud.cod_registro_especial_pss) ";
$sql_consulta_prestador_usuario.=" WHERE tipo_identificacion_usuario='".$tipo_id."' AND  identificacion_usuario='".$identificacion."'; ";
*/
$sql_consulta_prestador_usuario="SELECT pss.codigo_entidad,pss.nombre_de_la_entidad FROM ";
$sql_consulta_prestador_usuario.=" gioss_entidad_nicklogueo_perfil_estado_persona nu INNER JOIN gioss_entidades_sector_salud pss ON (nu.entidad = pss.codigo_entidad) ";
$sql_consulta_prestador_usuario.=" WHERE nu.tipo_id='".$tipo_id."' AND  nu.identificacion_usuario='".$identificacion."' AND nu.entidad='".$entidad_salud_usuario_actual."'; ";
$resultado_query_prestador_usuario=$coneccionBD->consultar2($sql_consulta_prestador_usuario);

if(count($resultado_query_prestador_usuario)>0)
{
	foreach($resultado_query_prestador_usuario as $prestador_usuario_res)
	{
		$prestador.="<option value='".$prestador_usuario_res['codigo_entidad']."'>".$prestador_usuario_res['nombre_de_la_entidad']."</option>";
	}
}


$prestador.="</select>";
//FIN

//SELECTOR EAPB-ASOCIADA_PRESTADOR_ASOCIADO_USUARIO
$eapb="";
$eapb.="<div id='div_eapb'>";
$eapb.="<select id='eapb' name='eapb' class='campo_azul'>";
$eapb.="<option value='none'>Seleccione un EAPB</option>";
$sql_consulta_eapb_usuario_prestador="SELECT ea.codigo_entidad,ea.nombre_de_la_entidad FROM ";
$sql_consulta_eapb_usuario_prestador.=" gioss_relacion_entre_entidades_salud re INNER JOIN gioss_entidades_sector_salud ea ON (re.entidad2 = ea.codigo_entidad) ";
$sql_consulta_eapb_usuario_prestador.=" WHERE re.entidad1='".$entidad_salud_usuario_actual."' ";
$sql_consulta_eapb_usuario_prestador.=";";

$resultado_query_eapb_usuario=$coneccionBD->consultar2($sql_consulta_eapb_usuario_prestador);

if(count($resultado_query_eapb_usuario)>0)
{
	foreach($resultado_query_eapb_usuario as $eapb_prestador_usuario_res)
	{
		$eapb.="<option value='".$eapb_prestador_usuario_res['codigo_entidad']."'>".$eapb_prestador_usuario_res['nombre_de_la_entidad']."</option>";
	}
}
$eapb.="</select>";
$eapb.="</div>";
//FIN

//SELECTOR FECHAS DE CORTE
$selector_fechas_corte="";
$selector_fechas_corte.="<select id='fechas_corte' name='fechas_corte' class='campo_azul' onchange='seleccionar_periodo();'>";
$selector_fechas_corte.="<option value='3-31'>3-31</option>";
$selector_fechas_corte.="<option value='6-30'>6-30</option>";
$selector_fechas_corte.="<option value='9-30'>9-30</option>";
$selector_fechas_corte.="<option value='12-31'>12-31</option>";
$selector_fechas_corte.="</select>";
//FIN SELECTOR FECHAS DE CORTE

//SELECTOR PERIODO
$selector_periodo="";
$selector_periodo.="<select id='periodo' name='periodo' class='campo_azul' onchange='seleccionar_fecha_de_corte();'>";
$selector_periodo.="<option value='1'>Periodo 1</option>";
$selector_periodo.="<option value='2'>Periodo 2</option>";
$selector_periodo.="<option value='3'>Periodo 3</option>";
$selector_periodo.="<option value='4'>Periodo 4</option>";
$selector_periodo.="</select>";
//FIN SELECTOR PERIODO

//DIV PARA EL RESULTADO DE LA QUERY
$mensaje.="<div id='div_mensaje' ></div>";
//FIN

//IMPRIME O MUESTRA LA PAGINA HTML
$smarty->assign("campo_fechas_corte", $selector_fechas_corte, true);
$smarty->assign("mensaje_proceso", $mensaje, true);
$smarty->assign("mostrarResultado",$mostrarResultado,true);

$smarty->assign("campo_periodo", $selector_periodo, true);
$smarty->assign("campo_eapb", $eapb, true);
$smarty->assign("campo_prestador", $prestador, true);

$smarty->assign("nombre", $nombre, true);
$smarty->assign("menu", $menu, true);
$smarty->display('consprestador4505.html.tpl');
//FIN IMPRIME O MUESTRA LA PAGINA HTML

if(isset($_POST["selector_estado_info"]))
{
	$indice_inicio=$_POST["index_inicio"];
	$indice_fin=$_POST["index_fin"];
	
	$re_asignar_por_javascript="";
	$re_asignar_por_javascript.="<script>";
	$re_asignar_por_javascript.="document.getElementById('selector_estado_info').value='".$_POST["selector_estado_info"]."';";
	$re_asignar_por_javascript.="document.getElementById('rango_resultados').value='".$_POST["rango_resultados"]."';";
	$re_asignar_por_javascript.="document.getElementById('prestador').value='".$_POST["prestador"]."';";
	$re_asignar_por_javascript.="consultar_eapb(\"".$tipo_id."\",\"".$identificacion."\");";
	$re_asignar_por_javascript.="document.getElementById('eapb').value='".$_POST["eapb"]."';";
	$re_asignar_por_javascript.="document.getElementById('fechas_corte').value='".$_POST["fechas_corte"]."';";
	$re_asignar_por_javascript.="document.getElementById('periodo').value='".$_POST["periodo"]."';";
	$re_asignar_por_javascript.="document.getElementById('year_de_corte').value='".$_POST["year_de_corte"]."';";
	$re_asignar_por_javascript.="document.getElementById('index_inicio').value='".$_POST["index_inicio"]."';";
	$re_asignar_por_javascript.="document.getElementById('index_fin').value='".$_POST["index_fin"]."';";
	$re_asignar_por_javascript.="";
	$re_asignar_por_javascript.="</script>";
	
	
	echo $re_asignar_por_javascript;
	
	//PARTE QUERY PAGINADA	
	$html_para_div_mensaje="";
	
	$query_consulta4505_perfil_prestador="";
	$query_consulta4505_perfil_prestador.="SELECT * FROM ";
	if($_POST["selector_estado_info"]=="validada")
	{
		$query_consulta4505_perfil_prestador.=" gios_datos_validados_exito_r4505 da ";
	}
	else
	{
		$query_consulta4505_perfil_prestador.=" gios_datos_rechazados_r4505 da ";
	}
	
	$hubo_condicion_anterior=false;
	if($_POST["eapb"]!="none" || $_POST["prestador"]!="none"  || $_POST["year_de_corte"]!="")
	{
		$query_consulta4505_perfil_prestador.=" WHERE ";
		
		if($_POST["prestador"]!="none")
		{
			if($hubo_condicion_anterior)
			{
				$query_consulta4505_perfil_prestador.=" AND ";
			}
			$query_consulta4505_perfil_prestador.=" da.cod_registro_especial_pss='".$_POST["prestador"]."' ";
			$hubo_condicion_anterior=true;
		}
		
		if($_POST["eapb"]!="none")
		{
			if($hubo_condicion_anterior)
			{
				$query_consulta4505_perfil_prestador.=" AND ";
			}
			$query_consulta4505_perfil_prestador.=" da.numero_de_identificacion_de_la_entidad_administradora='".$_POST["eapb"]."' ";
			$hubo_condicion_anterior=true;
		}
		
		
		if($_POST["year_de_corte"]!="")
		{
			$fecha_de_corte_full=$_POST["year_de_corte"]."-".$_POST["fechas_corte"];
			if($hubo_condicion_anterior)
			{
				$query_consulta4505_perfil_prestador.=" AND ";
			}
			$query_consulta4505_perfil_prestador.=" da.fecha_de_corte='".$fecha_de_corte_full."' ";
			$hubo_condicion_anterior=true;
		}
	}//if para ver si se seleccionaron restricciones o buscara todos
	
	
	$query_consulta4505_perfil_prestador.=" ORDER BY numero_fila LIMIT $indice_fin OFFSET $indice_inicio ";
	$query_consulta4505_perfil_prestador.=";";
	
	$resultado_consulta4505_perfil_prestador=$coneccionBD->consultar2($query_consulta4505_perfil_prestador);
	
	$html_para_div_mensaje.="<span style='color:white;'>QUERY: ".$query_consulta4505_perfil_prestador."</span>";
	
	if(count($resultado_consulta4505_perfil_prestador)>0)
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
		
		foreach($resultado_consulta4505_perfil_prestador as $fila4505)
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
		
		
	}//FIN IF ENCONTRO RESULTADOS QUERY
	else
	{
		$html_para_div_mensaje.="<br></br><h5>NO SE ENCONTRARON COINCIDENCIAS.</h5>";
	}
	
	//PARTE QUERY PAGINADA	
	
	echo "<script>document.getElementById('div_mensaje').innerHTML=\" ".$html_para_div_mensaje." \";</script>";
}//si se ha enviado el formulario

?>