<?php
set_time_limit(936000);
ini_set('max_execution_time', 936000);
ini_set('memory_limit', '900M');

require '../librerias_externas/Smarty-3.1.17/libs/Smarty.class.php';
include_once ('../utiles/clase_coneccion_bd.php');
include_once ('../utiles/funciones_crear_menu.php');

require_once 'procesar_mensaje.php';

$smarty = new Smarty;
$coneccionBD = new conexion();

session_start();

if (!isset($_SESSION['usuario']) && !isset($_SESSION['tipo_perfil']))
{
	
	header ("Location: ../index.php");
}

$menu= $_SESSION['menu_logueo_html'];
$nombre= $_SESSION['nombre_completo'];

$tipo_id=$_SESSION['tipo_id'];
$identificacion=$_SESSION['identificacion'];

$nick_user=$_SESSION['usuario'];

$perfil_usuario_actual= $_SESSION['tipo_perfil'];
$entidad_salud_usuario_actual= $_SESSION['entidad_asociada_al_nick'];

session_write_close();

$mostrarResultado = "<div id='mostrar_resultado_div'></div>";
$mensaje="<div id='mensaje_div'></div>";

//SELECTOR EAPB-ASOCIADA_ASOCIADA_USUARIO POR LOGUEO

//consultar el tipo de entidad
$sql_query_tipo_entidad_asociada="SELECT * FROM gioss_entidades_sector_salud WHERE codigo_entidad='$entidad_salud_usuario_actual'; ";
$resultado_query_tipo_entidad=$coneccionBD->consultar2($sql_query_tipo_entidad_asociada);
$tipo_entidad=$resultado_query_tipo_entidad[0]["cod_tipo_entidad"];
//fin consultar el tipo de entidad

$eapb="";
$eapb.="<div id='div_eapb'>";
$eapb.="<select id='eapb' name='eapb' class='campo_azul' onchange='consultar_prestador();' >";
$eapb.="<option value='none'>Seleccione un EAPB</option>";

if(intval($perfil_usuario_actual)==5 && (intval($tipo_entidad)==6 || intval($tipo_entidad)==7 || intval($tipo_entidad)==8 || intval($tipo_entidad)==10) )
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


//SELECTOR PRESTADORES ASOCIADOS EAPB 
$prestador="";
$prestador.="<div id='div_prestador'>";
$prestador.="<select id='prestador' name='prestador' class='campo_azul' >";
$prestador.="<option value='none'>Seleccione un prestador de salud</option>";
$prestador.="</select>";
$prestador.="</div>";
//FIN



//SELECTOR FECHAS MESES PERIDOS RIPS
$query_periodos_rips="SELECT * FROM gioss_periodos_reporte_rips;";
$resultado_query_periodos=$coneccionBD->consultar2($query_periodos_rips);
$selector_periodo="";
$selector_periodo.="<select id='periodo' name='periodo' class='campo_azul'  >\n";
$selector_periodo.="<option value='none'>Seleccione un periodo</option>\n";
foreach($resultado_query_periodos as $key=>$periodo)
{
	$cod_periodo=$periodo["cod_periodo"];
	$nombre_periodo=$periodo["nombre_periodo"];
	$fecha_de_corte=$periodo["fecha_corte"];
	$selector_periodo.="<option value='$fecha_de_corte'>Periodo $cod_periodo ($nombre_periodo $fecha_de_corte)</option>\n";
}
$selector_periodo.="</select>\n";
//FIN SELECTOR FECHAS MESES PERIDOS RIPS

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


$smarty->assign("mensaje_proceso", $mensaje, true);
$smarty->assign("mostrarResultado",$mostrarResultado,true);

$smarty->assign("campo_dpto", $selector_departamento, true);
$smarty->assign("campo_mpio", $selector_municipio, true);
$smarty->assign("campo_fechas_corte", $selector_periodo, true);
$smarty->assign("campo_prestador", $prestador, true);
$smarty->assign("campo_eapb", $eapb, true);
$smarty->assign("nombre", $nombre, true);
$smarty->assign("menu", $menu, true);
$smarty->display('cumplimiento_info_fact_RIPS3374.html.tpl');

echo "<script>consultar_prestador();</script>";

//PARTE BUSQUEDA
$rutaTemporal = '../TEMPORALES/';

$numero_registros_vista=0;
$contador_offset=0;
$hubo_resultados=false;

if(isset($_POST["eapb"]) && isset($_POST["year_de_radicacion"]) && $_POST["eapb"]!="none" && ctype_digit($_POST["year_de_radicacion"]))
{
	$year=$_POST["year_de_radicacion"];
	$periodo=$_POST["periodo"];
	$fecha_inicio=$year."-01-01";
	$fecha_fin=$year."-12-31";
	if($periodo!="none" && ctype_digit($year))
	{
		$mes=explode("/",$periodo)[1];
		$dia_fin=explode("/",$periodo)[0];
		$fecha_inicio=$year."-".$mes."-01";
		$fecha_fin=$year."-".$mes."-".$dia_fin;
	}
	
	$cod_eapb=$_POST["eapb"];
	
	$codigo_departamento="";
	if(isset($_POST["dpto"]) && $_POST["dpto"]!="none")
	{
		$codigo_departamento=$_POST["dpto"];
	}
	
	$codigo_municipio="";
	if(isset($_POST["mpio"]) && $_POST["mpio"]!="none")
	{
		$codigo_municipio=$_POST["mpio"];
	}
	
	$cod_prestador="";
	if(isset($_POST["prestador"]) && $_POST["prestador"]!="none")
	{
		$cod_prestador=$_POST["prestador"];
	}
	
	$nombre_vista_para_cumplimiento_info_factura="vcifact_".$nick_user."_".$tipo_id."_".$identificacion;
	
	$sql_vista_cumplimiento_facturas="";
	$sql_vista_cumplimiento_facturas.="CREATE OR REPLACE VIEW $nombre_vista_para_cumplimiento_info_factura ";
	$sql_vista_cumplimiento_facturas.=" AS SELECT * ";
	$sql_vista_cumplimiento_facturas.=" FROM gioss_facturas_radicadas ft ";
	//LEER se hace left join para poder permitir la busqueda por departamento y ciudad
	$sql_vista_cumplimiento_facturas.=" LEFT JOIN  gios_prestador_servicios_salud ps ";
	$sql_vista_cumplimiento_facturas.=" ON (ft.codigo_prestador=ps.cod_registro_especial_pss) WHERE  ";
	$sql_vista_cumplimiento_facturas.=" codigo_eapb='$cod_eapb' ";
	if($codigo_departamento!="")
	{
		$sql_vista_cumplimiento_facturas.=" AND ";
		$sql_vista_cumplimiento_facturas.=" cod_depto='$codigo_departamento' ";
	}
	if($codigo_departamento!="" && $codigo_municipio!="")
	{
		$sql_vista_cumplimiento_facturas.=" AND ";
		$sql_vista_cumplimiento_facturas.=" cod_municipio='$codigo_municipio' ";
	}
	if($cod_prestador!="")
	{
		$sql_vista_cumplimiento_facturas.=" AND ";
		$sql_vista_cumplimiento_facturas.=" codigo_prestador='$cod_prestador' ";
	}
	if(ctype_digit($year))
	{
		$sql_vista_cumplimiento_facturas.=" AND ";
		$sql_vista_cumplimiento_facturas.=" (fecha_radicacion BETWEEN '$fecha_inicio' AND '$fecha_fin') ";
	}
	$sql_vista_cumplimiento_facturas.=" order by fecha_radicacion ; ";
	$error_bd_seq="";
	$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_vista_cumplimiento_facturas, $error_bd_seq);
	if($error_bd_seq!="")
	{
		echo "<script>alert('error al crear vista(s) ".procesar_mensaje($error_bd_seq)." ".procesar_mensaje($sql_vista_cumplimiento_facturas)."');</script>";
	}
	
	//echo "<script>alert(' ".procesar_mensaje($sql_vista_cumplimiento_facturas)."');</script>";
	
	$numero_registros_vista=0;
	$sql_numero_registros_vista="";
	$sql_numero_registros_vista.="SELECT count(*) as contador FROM $nombre_vista_para_cumplimiento_info_factura  ";	
	$sql_numero_registros_vista.=" ; ";
	$resultado_query_numero_registros=$coneccionBD->consultar2($sql_numero_registros_vista);
	if(count($resultado_query_numero_registros) && is_array($resultado_query_numero_registros))
	{
		$numero_registros_vista=intval($resultado_query_numero_registros[0]["contador"]);
	}
	//echo "<script>alert('se genero vista $numero_registros_vista');</script>";
	
	$contador_offset=0;
	$hubo_resultados=false;
	$cont_linea=1;
	$bool_ultima_seccion_para_ventana=false;
	$html_nueva_ventana="";
	while($contador_offset<$numero_registros_vista)
	{
		$limite=2000;
		
		if( ($contador_offset+2000)>=$numero_registros_vista)
		{
			$limite=2000+($numero_registros_vista-$contador_offset);
			$bool_ultima_seccion_para_ventana=true;
		}
	
		//Ejemplo: SELECT *  FROM vista_inconsistencias_rips WHERE numero_orden='29'  order by numero_linea, numero_campo limit 5 offset 0; 
		$sql_query_busqueda="";
		$sql_query_busqueda.="SELECT * FROM $nombre_vista_para_cumplimiento_info_factura LIMIT $limite OFFSET $contador_offset;  ";
		$resultado_para_cumplimiento_de_facturas=$coneccionBD->consultar2($sql_query_busqueda);
	
		if(count($resultado_para_cumplimiento_de_facturas)>0)
		{
			
			$nombre_archivo_calidad_de_datos=$cod_eapb."_cumplimiento_factura_rips.csv";
			$ruta_archivo_cumplimiento_facturas=$rutaTemporal.$nombre_archivo_calidad_de_datos;
			
			//PARTE INDICAR HUBO RESULTADOS UNA SOLA VEZ Y CREAR EL ARCHIVO EN BLANCO
			if($hubo_resultados==false)
			{
				$file_cumplimiento_facturas= fopen($ruta_archivo_cumplimiento_facturas, "w") or die("fallo la creacion del archivo");
				fclose($file_cumplimiento_facturas);
			
				$html_abrir_ventana="";
				$html_abrir_ventana.="<script>";
				$html_abrir_ventana.="ventana_detalle=window.open ('','ventana_info_calidad_de_datos', config='height=600,width=1280, toolbar=no, menubar=no, scrollbars=yes, resizable=yes,location=no, directories=no, status=no');";
				$html_abrir_ventana.="</script>";
				echo $html_abrir_ventana;
				
				$html_nueva_ventana="";
				$html_nueva_ventana.="<html>";
				
				$html_nueva_ventana.="<head>";
				$html_nueva_ventana.="<title>Reporte de Calidad de Datos</title>";
				$html_nueva_ventana.="<style>";
				$html_nueva_ventana.="table";
				$html_nueva_ventana.="{";
				$html_nueva_ventana.="    border-collapse: collapse;";
				$html_nueva_ventana.="}";
				$html_nueva_ventana.="table,th,td";
				$html_nueva_ventana.="{";
				$html_nueva_ventana.="    border: 1px solid black;";
				$html_nueva_ventana.="}";
				$html_nueva_ventana.="td,th";
				$html_nueva_ventana.="{";
				$html_nueva_ventana.="    padding: 15px;";
				$html_nueva_ventana.="    text-align: right;";
				$html_nueva_ventana.="}";
				$html_nueva_ventana.="th";
				$html_nueva_ventana.="{";
				//$html_nueva_ventana.="   background-color: #0000FF;";
				$html_nueva_ventana.="   background: -webkit-linear-gradient(#0066FF, #001433); /* For Safari 5.1 to 6.0 */";
				$html_nueva_ventana.="   background: -o-linear-gradient(#0066FF, #001433); /* For Opera 11.1 to 12.0 */";
				$html_nueva_ventana.="   background: -moz-linear-gradient(#0066FF, #001433); /* For Firefox 3.6 to 15 */";
				$html_nueva_ventana.="   background: linear-gradient(#0066FF, #001433); /* Standard syntax */";
				$html_nueva_ventana.="   color: white;";
				$html_nueva_ventana.="}";
				$html_nueva_ventana.="td:hover";
				$html_nueva_ventana.="{";
				$html_nueva_ventana.="    background-color: #0066FF;";
				$html_nueva_ventana.="    color: white;";
				$html_nueva_ventana.="}";
				$html_nueva_ventana.=".resultados";
				$html_nueva_ventana.="{";
				//$html_nueva_ventana.="    background-color: #FF0000;";
				$html_nueva_ventana.="   background: -webkit-linear-gradient(#FF0000, #990000); /* For Safari 5.1 to 6.0 */";
				$html_nueva_ventana.="   background: -o-linear-gradient(#FF0000, #990000); /* For Opera 11.1 to 12.0 */";
				$html_nueva_ventana.="   background: -moz-linear-gradient(#FF0000, #990000); /* For Firefox 3.6 to 15 */";
				$html_nueva_ventana.="   background: linear-gradient(#FF0000, #990000); /* Standard syntax */";
				$html_nueva_ventana.="    color: white;";
				$html_nueva_ventana.="}";
				$html_nueva_ventana.=".resultados:hover";
				$html_nueva_ventana.="{";
				//$html_nueva_ventana.="    background-color: #990000;";
				$html_nueva_ventana.="   background: -webkit-linear-gradient(#990000, #FF0000); /* For Safari 5.1 to 6.0 */";
				$html_nueva_ventana.="   background: -o-linear-gradient(#990000, #FF0000); /* For Opera 11.1 to 12.0 */";
				$html_nueva_ventana.="   background: -moz-linear-gradient(#990000, #FF0000); /* For Firefox 3.6 to 15 */";
				$html_nueva_ventana.="   background: linear-gradient(#990000, #FF0000); /* Standard syntax */";
				$html_nueva_ventana.="    color: white;";
				$html_nueva_ventana.="}";
				$html_nueva_ventana.="</style>";
				$html_nueva_ventana.="</head>";
				$html_nueva_ventana.="<body>";
				
				
				
				
				$file_cumplimiento_facturas= fopen($ruta_archivo_cumplimiento_facturas, "a") or die("fallo la creacion del archivo");
				
				
				//encabezado
				$html_encabezado="";
				$html_encabezado.="<table id='tabla_encabezado_ventana' align='center'>";
				$html_encabezado.="<tr>";
				
				$html_encabezado.="<td>Departamento seleccionado:</td>";
				$depto_seleccionado="";
				if($codigo_departamento!="")
				{
					$query_departamentos_sel="select * from gios_dpto WHERE cod_departamento='$codigo_departamento' ORDER BY nom_departamento ;";
					$res_dptos_query_sel=$coneccionBD->consultar2($query_departamentos_sel);
					if(is_array($res_dptos_query_sel))
					{
						$depto_seleccionado=$res_dptos_query_sel[0]["nom_departamento"];
					}
					
					$html_encabezado.="<td>$depto_seleccionado</td>";
				}
				else
				{
					$html_encabezado.="<td>Todos</td>";
					$depto_seleccionado="Todos";
				}
				
				$html_encabezado.="<td>Municipio seleccionado:</td>";
				$mpio_seleccionado="";				
				if($codigo_departamento!="" && $codigo_municipio!="")
				{
					$query_mpios_sel="select * from gios_mpio  WHERE cod_departamento='$codigo_departamento' AND  cod_municipio='$codigo_municipio' ORDER BY nom_municipio;";
					$res_mpios_query_sel=$coneccionBD->consultar2($query_mpios_sel);
					if(is_array($res_mpios_query_sel))
					{
						$mpio_seleccionado=$res_mpios_query_sel[0]["nom_municipio"];
					}
					$html_encabezado.="<td>$mpio_seleccionado</td>";
				}
				else
				{
					$html_encabezado.="<td>Todos</td>";
					$mpio_seleccionado="Todos";
				}
				
				$prestador_seleccionado="";
				$html_encabezado.="<td>Prestador seleccionado:</td>";
				if($cod_prestador!="")
				{
					$prestador_seleccionado=$cod_prestador;
					$html_encabezado.="<td>$prestador_seleccionado</td>";
				}
				else
				{
					$html_encabezado.="<td>Todos</td>";
					$prestador_seleccionado="Todos";
				}
				
				$html_encabezado.="<td>A&ntildeo seleccionado:</td>";
				$year_seleccionado="";
				if($year!="")
				{
					$year_seleccionado=$year;
					$html_encabezado.="<td>$year_seleccionado</td>";
				}
				else
				{
					$html_encabezado.="<td>Todos</td>";
					$year_seleccionado="Todos";
				}
				
				$html_encabezado.="<td>Periodo seleccionado:</td>";
				$periodo_seleccionado="";
				if($periodo!="none")
				{
					$periodo_seleccionado=$periodo;
					$html_encabezado.="<td>$periodo_seleccionado</td>";
				}
				else
				{
					$html_encabezado.="<td>Todos</td>";
					$periodo_seleccionado="Todos";
				}
				
				
				$html_encabezado.="</tr>";
				$html_encabezado.="</table>";
				$html_nueva_ventana.=$html_encabezado;
				
				$linea_encabezado="Departamento,$depto_seleccionado,Municipio,$mpio_seleccionado,Prestador,$prestador_seleccionado,Year,$year_seleccionado,Periodo,$periodo";
				
				fwrite($file_cumplimiento_facturas, $linea_encabezado."\n");
				//encabezado
				
				
				$titulos="";
				$titulos.="Cod. Depto. Prestador,Nombre Depto. Prestador,Cod. Mpio. Prestador,Nombre Mpio. Prestador,Tipo ID Prestador,";
				$titulos.="N. ID Prestador,Cod. Prestador de Servicios,Nombre Prestador de Servicios,Fecha de Radicacion de la Factura,Numero de la Factura Radicada,";
				$titulos.="Valor de la Factura Radicada,Soporte de Factura RIPS,Nombre Archivo AF,Fecha Validacion Archivo AF,Valor Factura Soportada en RIPS";
				fwrite($file_cumplimiento_facturas, $titulos."\n");
				
				$html_nueva_ventana.="<table id='tabla_ventana_estado_info' >";
				$html_nueva_ventana.="<tr>";
				$array_titulos=explode(",",$titulos);
				$cont_titulos=0;
				foreach($array_titulos as $titulo_columna)
				{
					$html_nueva_ventana.="<th>$titulo_columna</th>";				
					$cont_titulos++;
				}
				$html_nueva_ventana.="</tr>";
			}//fin if
			
			$hubo_resultados=true;
			//FIN PARTE INDICAR HUBO RESULTADOS UNA SOLA VEZ Y CREAR EL ARCHIVO EN BLANCO
			
			$file_cumplimiento_facturas= fopen($ruta_archivo_cumplimiento_facturas, "a") or die("fallo la creacion del archivo");
			
			foreach($resultado_para_cumplimiento_de_facturas as $resultado)
			{
				$linea_cumplimiento_factura="";
				
				$query_datos_prestador="";
				$query_datos_prestador.="SELECT * FROM gios_prestador_servicios_salud WHERE cod_registro_especial_pss='".$resultado["codigo_prestador"]."';";
				$resultado_para_datos_prestador=$coneccionBD->consultar2($query_datos_prestador);
				
				//datos prestador
				if(is_array($resultado_para_datos_prestador) && count($resultado_para_datos_prestador)>0)
				{
					//cod dpto
					$linea_cumplimiento_factura.=$resultado_para_datos_prestador[0]["cod_depto"].",";//1
					$query_nombre_depto="";
					$query_nombre_depto.="SELECT * FROM gios_dpto WHERE cod_pais='57' AND cod_departamento='".$resultado_para_datos_prestador[0]["cod_depto"]."'; ";
					$resultado_nombre_depto=$coneccionBD->consultar2($query_nombre_depto);
					$linea_cumplimiento_factura.=$resultado_nombre_depto[0]["nom_departamento"].",";//2
					$linea_cumplimiento_factura.=$resultado_para_datos_prestador[0]["cod_municipio"].",";//3
					$query_nombre_mpio="";
					$query_nombre_mpio.="SELECT * FROM gios_mpio WHERE cod_departamento='".$resultado_para_datos_prestador[0]["cod_depto"]."' ";
					$query_nombre_mpio.=" AND cod_municipio='".$resultado_para_datos_prestador[0]["cod_municipio"]."'; ";
					$resultado_nombre_mpio=$coneccionBD->consultar2($query_nombre_mpio);
					$linea_cumplimiento_factura.=$resultado_nombre_mpio[0]["nom_municipio"].",";//4
					$linea_cumplimiento_factura.=$resultado_para_datos_prestador[0]["cod_tipo_identificacion"].",";//5
					$linea_cumplimiento_factura.=$resultado_para_datos_prestador[0]["num_tipo_identificacion"].",";//6
					$linea_cumplimiento_factura.=$resultado["codigo_prestador"].",";//7
					$linea_cumplimiento_factura.=$resultado_para_datos_prestador[0]["nom_entidad_prestadora"].",";//8
				}//fin if si hubo resultados
				//fin datos prestador
				
				$linea_cumplimiento_factura.=$resultado["fecha_radicacion"].",";//9
				$linea_cumplimiento_factura.=$resultado["numero_factura"].",";//10
				$linea_cumplimiento_factura.=$resultado["valor_factura"].",";//11
				$linea_cumplimiento_factura.=$resultado["estado_verificacion_factura"].",";//12
				$linea_cumplimiento_factura.=$resultado["nombre_archivo_af"].",";//13
				$linea_cumplimiento_factura.=$resultado["fecha_validacion_af"].",";//14
				$linea_cumplimiento_factura.=$resultado["valor_factura_rips"];//15
				
				fwrite($file_cumplimiento_facturas, $linea_cumplimiento_factura."\n");
				
				echo "<script>document.getElementById('mensaje_div').innerHTML='Por favor espere, $cont_linea registros recuperados de $numero_registros_vista.';</script>";
				
				$html_nueva_ventana.="<tr>";
				$array_linea_cumplimiento_factura=explode(",",$linea_cumplimiento_factura);
				$cont_columnas_estado_informacion=0;
				foreach($array_linea_cumplimiento_factura as $columna_cumplimiento_factura)
				{
					$html_nueva_ventana.="<td>$columna_cumplimiento_factura</td>";					
					$cont_columnas_estado_informacion++;
				}
				$html_nueva_ventana.="</tr>";
				
				$cont_linea++;
			}
			fclose($file_cumplimiento_facturas);
		
			if($bool_ultima_seccion_para_ventana==true)
			{
				$html_nueva_ventana.="</table>";
			
				$html_nueva_ventana.="</body>";
				$html_nueva_ventana.="</html>";
			
				$insertar_html_nueva_ventana="";
				$insertar_html_nueva_ventana.="<script>ventana_detalle.document.write(\"$html_nueva_ventana\");</script>";
				echo $insertar_html_nueva_ventana;
			}
			
			
		}//fin if hallo resultados
		
		$contador_offset+=2000;
	
	}//fin while
	
	if($hubo_resultados)
	{
		echo "<script>document.getElementById('grilla').style.display='inline';</script>";
		
		$html_reabrir_ventana="";
		$html_reabrir_ventana.="<script>";
		$html_reabrir_ventana.="function re_abrir_nueva_ventana()";
		$html_reabrir_ventana.="{";
		$html_reabrir_ventana.="ventana_detalle=window.open ('','ventana_info_calidad_de_datos', config='height=600,width=1280, toolbar=no, menubar=no, scrollbars=yes, resizable=yes,location=no, directories=no, status=no');";
		$html_reabrir_ventana.="ventana_detalle.document.body.innerHTML='';";
		$html_reabrir_ventana.="ventana_detalle.document.write(\"$html_nueva_ventana\");";	
		$html_reabrir_ventana.="}";
		$html_reabrir_ventana.="</script>";
		echo $html_reabrir_ventana;
		
		$boton_ventana=" <input type=\'button\' value=\'Ver la ventana de calidad de datos para RIPS\'  class=\'btn btn-success color_boton\' onclick=\"re_abrir_nueva_ventana();\"/> ";	
		
			
		$boton_descarga=" <input type=\'button\' value=\'Descargar archivo con el reporte de calidad de datos para RIPS\'  class=\'btn btn-success color_boton\' onclick=\"download_archivo(\'$ruta_archivo_cumplimiento_facturas\');\"/> ";
	
		echo "<script>document.getElementById('mostrar_resultado_div').innerHTML='$boton_ventana $boton_descarga';</script>";
		
	}
	else
	{
		echo "<script>document.getElementById('div_mensaje_error').style.display='inline';</script>";
		echo "<script>document.getElementById('parrafo_mensaje_error').innerHTML='No se hallaron datos en el periodo especificado para generar el reporte.';</script>";
	}
	
	
	//borrando vistas
	$sql_borrar_vistas="";
	$sql_borrar_vistas.=" DROP VIEW $nombre_vista_para_cumplimiento_info_factura ; ";
	
	$error_bd="";		
	$bool_funciono=$coneccionBD->insertar_no_warning_get_error($sql_borrar_vistas, $error_bd);		
	if($error_bd!="")
	{
		echo "<script>alert('error al borrar vista(s) ".procesar_mensaje($error_bd)."');</script>";
	}
	
	//fin borrando vistas
}//fin isset year y periodo

//FIN PARTE BUSQUEDA

?>