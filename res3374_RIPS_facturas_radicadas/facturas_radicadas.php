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
$eapb.="<select id='eapb' name='eapb' class='campo_azul'  >";
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

//selector periodos
$query_periodos_rips="SELECT * FROM gioss_periodos_reporte_rips;";
$resultado_query_periodos=$coneccionBD->consultar2($query_periodos_rips);

$selector_periodo="";
$selector_periodo.="<select id='periodo' name='periodo' class='campo_azul' onchange='seleccionar_fecha_de_corte();'>";
$selector_periodo.="<option value='none'>Seleccione un periodo</option>";
foreach($resultado_query_periodos as $key=>$periodo_bd)
{
	$cod_periodo=$periodo_bd["cod_periodo"];
	$nombre_periodo=$periodo_bd["nombre_periodo"];
	$fecha_inicio=$periodo_bd["fecha_inicio_periodo"];
	$fecha_final=$periodo_bd["fecha_final_periodo"];
	$selector_periodo.="<option value='$cod_periodo'>Periodo $cod_periodo ($nombre_periodo)</option>";
}
$selector_periodo.="</select>";
//fin selector periodos

/*
//SELECTOR PRESTADORES ASOCIADOS EAPB 
$prestador="";
$prestador.="<div id='div_prestador'>";
$prestador.="<select id='prestador' name='prestador' class='campo_azul' >";
$prestador.="<option value='none'>Seleccione un prestador de salud</option>";
$prestador.="</select>";
$prestador.="</div>";
//FIN
*/

date_default_timezone_set ("America/Bogota");
$fecha_actual = date('Y-m-d');
$tiempo_actual = date('H:i:s');

$mensaje="";
$mostrarResultado="none";

$smarty->assign("campo_periodo", $selector_periodo, true);
$smarty->assign("campo_eapb", $eapb, true);

$smarty->assign("mensaje_proceso", $mensaje, true);
$smarty->assign("mostrarResultado", $mostrarResultado, true);
$smarty->assign("nombre", $nombre, true);
$smarty->assign("menu", $menu, true);
$smarty->display('facturas_radicadas.html.tpl');

//echo "<script>consultar_prestador();</script>";

if(isset($_FILES["facturas_file"]) && isset($_POST["oculto_envio"])
   &&  isset($_POST["eapb"]) 
   &&  $_POST["eapb"]!="none" 
   &&  $_POST["oculto_envio"]=="envio"
   && $_FILES["facturas_file"]["error"] == 0)
{
	$rutaTemporal = '../TEMPORALES/';
	
	$codigo_eapb=$_POST["eapb"];
	
	$mensajes_error_bd="";
	$mensajes_error_normales="";
	$mensajes_exitosos_bd="";
	
	$archivo_facturas=$_FILES["facturas_file"];
	$ruta_archivo_facturas = $rutaTemporal . $archivo_facturas['name'];
	move_uploaded_file($archivo_facturas['tmp_name'], $ruta_archivo_facturas);
		
	//archivo que se lee
	$lineas_del_archivo = count(file($ruta_archivo_facturas)); 
	$archivo_cargar = fopen($ruta_archivo_facturas, 'r') or exit("No se pudo abrir el archivo con los datos");
	$numero_linea=0;
	$linea_res="";
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
		
		//validacion antes de cargar
		$hubo_errores=false;
		$existe_factura=false;
		$mensaje_existe_factura="";
		$fecha_para_bd_radicacion="";
		$fecha_para_bd_factura="";
		if($linea_res!="" && count($campos)==10)
		{
			if($_POST["eapb"]!=$campos[0])
			{
				$mensajes_error_normales.="El codigo de la EAPB en el archivo, con linea $numero_linea no corresponde con El codigo EAPB registrado. <br>";
				$hubo_errores=true;
			}
			else
			{
				$sql_consulta_prestadores_asociados_eapb="SELECT ea.codigo_entidad,ea.nombre_de_la_entidad FROM ";
				$sql_consulta_prestadores_asociados_eapb.=" gioss_relacion_entre_entidades_salud re INNER JOIN gioss_entidades_sector_salud ea ON (re.entidad1 = ea.codigo_entidad) ";
				$sql_consulta_prestadores_asociados_eapb.=" WHERE re.entidad2='".$codigo_eapb."' ";
				$error_bd="";
				$resultado_query_prestadores_asociados_eapb=$coneccionBD->consultar_no_warning_get_error($sql_consulta_prestadores_asociados_eapb,$error_bd);
				if($error_bd!="")
				{
					$mensajes_error_bd.="ERROR AL CONSULTAR LOS PRESTADORES ASOCIADOS DE LA EAPB. <br>";
					$hubo_errores=true;
				}
				
				if(count($resultado_query_prestadores_asociados_eapb)>0)
				{
					$existe_asociacion_con_prestador=false;
					foreach($resultado_query_prestadores_asociados_eapb as $prestador_asociado)
					{
						if($prestador_asociado["codigo_entidad"]==$campos[1])
						{
							$existe_asociacion_con_prestador=true;
						}
					}
					
					if($existe_asociacion_con_prestador==false)
					{
						$mensajes_error_normales.="La EAPB no esta asociada con el prestador indicado en la linea $numero_linea <br>";
						$hubo_errores=true;
					}
				}
				else
				{
					$mensajes_error_normales.="La EAPB no tiene prestadores asociados <br>";
					$hubo_errores=true;
				}
			}
						
			
			$bool_fecha_factura_correcta=false;
			$fecha_factura_archivo=$campos[8];
			$array_fecha_factura_archivo=explode("/",$fecha_factura_archivo);
			if(count($array_fecha_factura_archivo)==3)
			{
				if(checkdate(intval($array_fecha_factura_archivo[1]),intval($array_fecha_factura_archivo[0]),intval($array_fecha_factura_archivo[2])))
				{
					$bool_fecha_factura_correcta=true;
					
					
				}
				else
				{
					$mensajes_error_normales.="La fecha de factura es invalida en la linea $numero_linea .<br>";
					$hubo_errores=true;
				}
			}
			else
			{
				$mensajes_error_normales.="La fecha de factura es invalida en la linea $numero_linea . <br>";
				$hubo_errores=true;
			}
			
			$bool_fecha_radicacion_correcta=false;
			$fecha_radicacion=$campos[7];
			$array_fecha_radicacion=explode("/",$campos[7]);			
			if(count($array_fecha_radicacion)==3)
			{
				if(checkdate(intval($array_fecha_radicacion[1]),intval($array_fecha_radicacion[0]),intval($array_fecha_radicacion[2])))
				{
					$bool_fecha_radicacion_correcta=true;
					
					
				}
				else
				{
					$mensajes_error_normales.="La fecha de radicacion es invalida en la linea $numero_linea .<br>";
					$hubo_errores=true;
				}
			}
			else
			{
				$mensajes_error_normales.="La fecha de radicacion es invalida en la linea $numero_linea . <br>";
				$hubo_errores=true;
			}
			
			if($bool_fecha_factura_correcta==true && $bool_fecha_radicacion_correcta==true)
			{
				$date_radicacion_archivo=$array_fecha_radicacion[2]."-".$array_fecha_radicacion[1]."-".$array_fecha_radicacion[0];
				$date_factura_archivo=$array_fecha_factura_archivo[2]."-".$array_fecha_factura_archivo[1]."-".$array_fecha_factura_archivo[0];
				$fecha_para_bd_radicacion=$date_radicacion_archivo;
				$fecha_para_bd_factura=$date_factura_archivo;
				
				//echo "<script>alert('$fecha_para_bd_radicacion $fecha_para_bd_factura');</script>";
				
				$interval_ing_egr = date_diff(date_create($date_factura_archivo),date_create($date_radicacion_archivo));
				$tiempo_ie= (float)($interval_ing_egr->format("%r%a"));
				if($tiempo_ie<0)
				{
					$mensajes_error_normales.="La fecha de radicacion  es menor a la fecha de factura de la linea $numero_linea .  <br>";
					$hubo_errores=true;
				}
			}
			
			$query_verificar_codigo_modalidad_contratacion="";
			$query_verificar_codigo_modalidad_contratacion.="SELECT * FROM gioss_modalidad_de_contratacion WHERE codigo_modalidad='".$campos[9]."' ; ";
			$resultados_verificar_codigo_modalidad_contratacion=array();
			$resultados_verificar_codigo_modalidad_contratacion=$coneccionBD->consultar_no_warning_get_error($query_verificar_codigo_modalidad_contratacion, $error_bd);
			if($error_bd!="")
			{
				$mensajes_error_bd.="ERROR al verificar la modalidad de contratacion ".procesar_mensaje($error_bd)." <br> ";
				
				
			}
			
			if(!is_array($resultados_verificar_codigo_modalidad_contratacion))
			{
				
				$mensajes_error_normales.="El codigo ".$campos[9]." para modalidad de contratacion es invalido . <br>";
				$hubo_errores=true;
			}
			
			$query_verificar_fue_radicada_factura="";
			$query_verificar_fue_radicada_factura.="SELECT * FROM gioss_facturas_radicadas WHERE ";
			$query_verificar_fue_radicada_factura.=" codigo_eapb='".$campos[0]."' ";
			$query_verificar_fue_radicada_factura.=" AND codigo_prestador='".$campos[1]."' ";
			$query_verificar_fue_radicada_factura.=" AND numero_factura='".$campos[5]."' ";
			$query_verificar_fue_radicada_factura.=" ; ";
			$error_bd="";
			$resultados_facturas_fueron_radicadas=array();
			$resultados_facturas_fueron_radicadas=$coneccionBD->consultar_no_warning_get_error($query_verificar_fue_radicada_factura, $error_bd);
			if($error_bd!="")
			{
				$mensajes_error_bd.="ERROR al consultar si factura fisica fue registrada ".procesar_mensaje($error_bd)." <br> ";
				
				
			}
			if(count($resultados_facturas_fueron_radicadas)>0 && is_array($resultados_facturas_fueron_radicadas))
			{
				//$mensajes_error_normales.="La factura con numero ".$campos[8]." en la linea $numero_linea ya fue radicada . <br>";
				//$hubo_errores=true;
				$existe_factura=true;
			}
			
		}//fin if
		//fin validacion antes de cargar
		
		if($linea_res!="" && count($campos)==10 && $hubo_errores==false)
		{
			//echo "<script>alert('$fecha_para_bd_radicacion $fecha_para_bd_factura');</script>";
			//echo "<script>alert('".$linea_res." ".$ruta_archivo_facturas."');</script>";
			$query_upsert_facturas="";
			if($existe_factura==false)
			{
				//echo "<script>alert('inserto');</script>";
				$query_upsert_facturas.="INSERT into gioss_facturas_radicadas ";
				$query_upsert_facturas.="(";
				$query_upsert_facturas.="codigo_eapb,";
				$query_upsert_facturas.="codigo_prestador,";
				$query_upsert_facturas.="tipo_identificacion,";
				$query_upsert_facturas.="numero_identificacion,";
				$query_upsert_facturas.="tipo_regimen,";
				$query_upsert_facturas.="numero_factura,";
				$query_upsert_facturas.="valor_factura,";
				$query_upsert_facturas.="fecha_radicacion,";
				$query_upsert_facturas.="fecha_factura,";
				$query_upsert_facturas.="modalidad_contratacion";
				$query_upsert_facturas.=")";
				$query_upsert_facturas.=" VALUES ";
				$query_upsert_facturas.="(";
				$cont_campos=0;
				while($cont_campos<count($campos))
				{
					if($cont_campos<(count($campos)-1) && $cont_campos!=7 && $cont_campos!=8) //los demas campos
					{
						$query_upsert_facturas.="'".$campos[$cont_campos]."',";
					}
					else if($cont_campos==(count($campos)-1))//ultimo campo sin coma
					{
						$query_upsert_facturas.="'".$campos[$cont_campos]."'";
					}
					if($cont_campos==7)
					{
						$query_upsert_facturas.="'".$fecha_para_bd_radicacion."',";
					}
					if($cont_campos==8)
					{
						$query_upsert_facturas.="'".$fecha_para_bd_factura."',";
					}
					$cont_campos++;
				}
				$query_upsert_facturas.=")";
				$query_upsert_facturas.=";";
				
			}//fin en caso de que no existe
			if($existe_factura==true)
			{
				//echo "<script>alert('actualizo');</script>";
				$query_upsert_facturas.="UPDATE gioss_facturas_radicadas ";
				$query_upsert_facturas.=" SET ";
				$query_upsert_facturas.="codigo_eapb='".$campos[0]."', ";
				$query_upsert_facturas.="codigo_prestador='".$campos[1]."',";
				$query_upsert_facturas.="tipo_identificacion='".$campos[2]."',";
				$query_upsert_facturas.="numero_identificacion='".$campos[3]."',";
				$query_upsert_facturas.="tipo_regimen='".$campos[4]."',";
				$query_upsert_facturas.="numero_factura='".$campos[5]."',";
				$query_upsert_facturas.="valor_factura='".$campos[6]."',";
				$query_upsert_facturas.="fecha_radicacion='".$fecha_para_bd_radicacion."',";
				$query_upsert_facturas.="fecha_factura='".$fecha_para_bd_factura."',";
				$query_upsert_facturas.="modalidad_contratacion='".$campos[9]."'";
				$query_upsert_facturas.=" WHERE ";
				$query_upsert_facturas.=" codigo_eapb='".$campos[0]."' ";
				$query_upsert_facturas.=" AND codigo_prestador='".$campos[1]."' ";
				$query_upsert_facturas.=" AND numero_factura='".$campos[5]."' ";
				$query_upsert_facturas.=";";
			}//fin en caso de actualizar
			$error_bd_upsert="";			
			$bool_funciono=$coneccionBD->insertar_no_warning_get_error($query_upsert_facturas, $error_bd_upsert);
			if($error_bd_upsert!="")
			{
				$mensajes_error_bd.="ERROR AL CARGAR LA FACTURA ".$campos[5]." : ".procesar_mensaje($error_bd_upsert)." <br> ";
			}
			else
			{
				if($existe_factura==false)
				{
					$mensajes_exitosos_bd.="La factura ".$campos[5]." de la linea $numero_linea se subio correctamente. <br> ";
				}
				else
				{
					$mensajes_exitosos_bd.="La factura ".$campos[5]." de la linea $numero_linea se actualizo correctamente. <br> ";
				}
			}
			
			if($error_bd_upsert=="" && $existe_factura==false)
			{
				$consultar_existencia_factura_fisica_en_rips_exitoso="";
				$consultar_existencia_factura_fisica_en_rips_exitoso.="SELECT * FROM gioss_archivo_cargado_af ";
				$consultar_existencia_factura_fisica_en_rips_exitoso.=" WHERE ";
				$consultar_existencia_factura_fisica_en_rips_exitoso.=" numero_factura='".$campos[5]."' ";
				$consultar_existencia_factura_fisica_en_rips_exitoso.=" ; ";
				$error_bd="";
				$resultados_factura_rips_coincidente_exitoso=array();
				$resultados_factura_rips_coincidente_exitoso=$coneccionBD->consultar_no_warning_get_error($consultar_existencia_factura_fisica_en_rips_exitoso, $error_bd);
				if(count($resultados_factura_rips_coincidente_exitoso)>0 && is_array($resultados_factura_rips_coincidente_exitoso))
				{
					$query_actualizar_para_coincidentes_rips_con_radicadas="";
					$query_actualizar_para_coincidentes_rips_con_radicadas.=" UPDATE gioss_facturas_radicadas SET ";
					$query_actualizar_para_coincidentes_rips_con_radicadas.=" estado_verificacion_factura='1', ";
					$query_actualizar_para_coincidentes_rips_con_radicadas.=" nombre_archivo_af='".$resultados_factura_rips_coincidente_exitoso[0]["codigo_archivo"]."', ";
					$query_actualizar_para_coincidentes_rips_con_radicadas.=" numero_factura_rips='".$resultados_factura_rips_coincidente_exitoso[0]["numero_factura"]."', ";
					$query_actualizar_para_coincidentes_rips_con_radicadas.=" fecha_validacion_af='".$resultados_factura_rips_coincidente_exitoso[0]["fecha_validacion_exito"]."', ";
					$query_actualizar_para_coincidentes_rips_con_radicadas.=" valor_factura_rips='".$resultados_factura_rips_coincidente_exitoso[0]["valor_neto_a_pagar"]."' ";
					$query_actualizar_para_coincidentes_rips_con_radicadas.=" WHERE ";
					$query_actualizar_para_coincidentes_rips_con_radicadas.=" codigo_eapb='".$campos[0]."' ";
					$query_actualizar_para_coincidentes_rips_con_radicadas.=" AND codigo_prestador='".$campos[1]."' ";
					$query_actualizar_para_coincidentes_rips_con_radicadas.=" AND numero_factura='".$campos[5]."' ";
					$query_actualizar_para_coincidentes_rips_con_radicadas.=";";
					$error_bd_act_fact_coincidente="";			
					$bool_funciono=$coneccionBD->insertar_no_warning_get_error($query_actualizar_para_coincidentes_rips_con_radicadas, $error_bd_act_fact_coincidente);
					if($error_bd_act_fact_coincidente!="")
					{
						$mensajes_error_bd.="ERROR AL ACTUALIZAR COINCIDENTE EXITOSO ".$campos[5]." : ".procesar_mensaje($error_bd_act_fact_coincidente)." <br> ";
					}
				}//fin if
				else
				{
					$consultar_existencia_factura_fisica_en_rips_rechazado="";
					$consultar_existencia_factura_fisica_en_rips_rechazado.="SELECT * FROM gioss_archivo_rechazado_af ";
					$consultar_existencia_factura_fisica_en_rips_rechazado.=" WHERE ";
					$consultar_existencia_factura_fisica_en_rips_rechazado.=" numero_factura='".$campos[5]."' ";
					$consultar_existencia_factura_fisica_en_rips_rechazado.=" AND ";
					$consultar_existencia_factura_fisica_en_rips_rechazado.=" numero_secuencia=(select max(numero_secuencia) FROM gioss_archivo_rechazado_af WHERE numero_factura='".$campos[5]."' ) ";
					$consultar_existencia_factura_fisica_en_rips_rechazado.=" ; ";
					$error_bd="";
					$resultados_factura_rips_coincidente_rechazado=array();
					$resultados_factura_rips_coincidente_rechazado=$coneccionBD->consultar_no_warning_get_error($consultar_existencia_factura_fisica_en_rips_rechazado, $error_bd);
					if(count($resultados_factura_rips_coincidente_rechazado)>0 && is_array($resultados_factura_rips_coincidente_rechazado))
					{
						$query_actualizar_para_coincidentes_rips_con_radicadas="";
						$query_actualizar_para_coincidentes_rips_con_radicadas.=" UPDATE gioss_facturas_radicadas SET ";
						$query_actualizar_para_coincidentes_rips_con_radicadas.=" estado_verificacion_factura='2', ";
						$query_actualizar_para_coincidentes_rips_con_radicadas.=" nombre_archivo_af='".$resultados_factura_rips_coincidente_rechazado[0]["codigo_archivo"]."', ";
						$query_actualizar_para_coincidentes_rips_con_radicadas.=" numero_factura_rips='".$resultados_factura_rips_coincidente_rechazado[0]["numero_factura"]."', ";
						$query_actualizar_para_coincidentes_rips_con_radicadas.=" fecha_validacion_af='".$resultados_factura_rips_coincidente_rechazado[0]["fecha_de_rechazo"]."', ";
						$query_actualizar_para_coincidentes_rips_con_radicadas.=" valor_factura_rips='".floatval($resultados_factura_rips_coincidente_rechazado[0]["valor_neto_a_pagar"])."' ";
						$query_actualizar_para_coincidentes_rips_con_radicadas.=" WHERE ";
						$query_actualizar_para_coincidentes_rips_con_radicadas.=" codigo_eapb='".$campos[0]."' ";
						$query_actualizar_para_coincidentes_rips_con_radicadas.=" AND codigo_prestador='".$campos[1]."' ";
						$query_actualizar_para_coincidentes_rips_con_radicadas.=" AND numero_factura='".$campos[5]."' ";
						$query_actualizar_para_coincidentes_rips_con_radicadas.=";";
						$error_bd_act_fact_coincidente="";			
						$bool_funciono=$coneccionBD->insertar_no_warning_get_error($query_actualizar_para_coincidentes_rips_con_radicadas, $error_bd_act_fact_coincidente);
						if($error_bd_act_fact_coincidente!="")
						{
							$mensajes_error_bd.="ERROR AL ACTUALIZAR COINCIDENTE ".$campos[5]." : ".procesar_mensaje($error_bd_act_fact_coincidente)." <br> ";
						}
					}//fin if
					else
					{
						$query_actualizar_para_coincidentes_rips_con_radicadas="";
						$query_actualizar_para_coincidentes_rips_con_radicadas.=" UPDATE gioss_facturas_radicadas SET ";
						$query_actualizar_para_coincidentes_rips_con_radicadas.=" estado_verificacion_factura='3' ";
						$query_actualizar_para_coincidentes_rips_con_radicadas.=" WHERE ";
						$query_actualizar_para_coincidentes_rips_con_radicadas.=" codigo_eapb='".$campos[0]."' ";
						$query_actualizar_para_coincidentes_rips_con_radicadas.=" AND codigo_prestador='".$campos[1]."' ";
						$query_actualizar_para_coincidentes_rips_con_radicadas.=" AND numero_factura='".$campos[5]."' ";
						$query_actualizar_para_coincidentes_rips_con_radicadas.=";";
						$error_bd_act_fact_coincidente="";			
						$bool_funciono=$coneccionBD->insertar_no_warning_get_error($query_actualizar_para_coincidentes_rips_con_radicadas, $error_bd_act_fact_coincidente);
						if($error_bd_act_fact_coincidente!="")
						{
							$mensajes_error_bd.="ERROR AL ACTUALIZAR COINCIDENTE ".$campos[5]." : ".procesar_mensaje($error_bd_act_fact_coincidente)." <br> ";
						}
					}//fin else si no se encuentra en ningun lado
				}//fin else if
				
				if($error_bd!="")
				{
					$mensajes_error_bd.="ERROR al buscar factura coincidente ".$campos[5]." : ".procesar_mensaje($error_bd_upsert)." <br> ";
				}
				
			}//fin if para verificar 
						
		}//fin if
		else if(count($campos)!=10 && $linea_res!="")
		{
			$mensajes_error_normales.="El numero de campos(".count($campos).") en la linea ".($numero_linea+1)." (contando desde 1) es incorrecto. <br>";
		}
		if($linea_res=="")
		{
			$mensajes_error_normales.="La linea ".($numero_linea+1)." esta en blanco. <br>";
		}
		$numero_linea++;
	}//fin while
	fclose($archivo_cargar);
	
	if($mensajes_error_bd!="" || $mensajes_error_normales!="")
	{
		echo "<script>document.getElementById('div_mensaje_error').style.display='inline';</script>";
		echo "<script>document.getElementById('titulo_mensaje_error').innerHTML=\"<u>Error en la carga de las facturas:</u>\";</script>";
		echo "<script>document.getElementById('parrafo_mensaje_error').innerHTML=\"$mensajes_error_bd $mensajes_error_normales\";</script>";
	}
	else
	{
		echo "<script>document.getElementById('div_mensaje_exito').style.display='inline';</script>";
		echo "<script>document.getElementById('titulo_mensaje_exito').innerHTML=\"<u>Carga de todas las facturas exitosa:</u>\";</script>";
		echo "<script>document.getElementById('parrafo_mensaje_exito').innerHTML=\"No hubo errores al subir las facturas\";</script>";
	}
	
	if($mensajes_exitosos_bd!="")
	{
		echo "<script>document.getElementById('div_mensaje_exito').style.display='inline';</script>";
		echo "<script>document.getElementById('titulo_mensaje_exito').innerHTML=\"<u>Carga de algunas facturas fue exitosa:</u>\";</script>";
		echo "<script>document.getElementById('parrafo_mensaje_exito').innerHTML=\"$mensajes_exitosos_bd\";</script>";
	}
}
else if(isset($_POST["oculto_envio"]) && $_POST["oculto_envio"]=="envio")
{
	$mensaje_error="";
	
	if($_POST["eapb"]=="none")
	{
		$mensaje_error.="Seleccione la EAPB para la cual se subiran las facturas. <br>";
	}
	
	
	if($_POST["year_para_periodo"]=="")
	{
		$mensaje_error.="Escriba un a&ntildeo para el periodo de las facturas a subir. <br>";
	}
	
	if($_POST["periodo"]=="none")
	{
		$mensaje_error.="Seleccione un periodo. <br>";
	}	
	
	if ($_FILES["facturas_file"]["error"] > 0)
	{
		$mensaje_error.="No hay un archivo subido. <br>";
	}
	
	if($mensaje_error!="")
	{
		echo "<script>document.getElementById('div_mensaje_error').style.display='inline';</script>";
		echo "<script>document.getElementById('titulo_mensaje_error').innerHTML=\"<u>Error en la carga de las facturas:</u>\";</script>";
		echo "<script>document.getElementById('parrafo_mensaje_error').innerHTML=\"$mensaje_error\";</script>";
	}
}

?>