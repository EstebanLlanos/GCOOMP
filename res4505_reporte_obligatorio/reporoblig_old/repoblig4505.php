<?php
set_time_limit(936000);
ini_set('max_execution_time', 936000);
ini_set('memory_limit', '900M');

require '../librerias_externas/Smarty-3.1.17/libs/Smarty.class.php';
include_once ('../utiles/clase_coneccion_bd.php');
include_once ('../utiles/funciones_crear_menu.php');
require_once '../utiles/queries_utiles_bd.php';

require_once '../utiles/crear_zip.php';

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


$perfil_usuario_actual= $_SESSION['tipo_perfil'];
$entidad_salud_usuario_actual= $_SESSION['entidad_asociada_al_nick'];

$mostrarResultado = "none";
$mensaje="";
$resultadoDefinitivo="";
$utilidades = new Utilidades();
$rutaTemporal = '../TEMPORALES/';

$selector_fechas_corte="";
$selector_fechas_corte.="<select id='fechas_corte' name='fechas_corte' class='campo_azul' onchange='seleccionar_periodo();'>";
$selector_fechas_corte.="<option value='3-31'>3-31</option>";
$selector_fechas_corte.="<option value='6-30'>6-30</option>";
$selector_fechas_corte.="<option value='9-30'>9-30</option>";
$selector_fechas_corte.="<option value='12-31'>12-31</option>";
$selector_fechas_corte.="</select>";

$selector_periodo="";
$selector_periodo.="<select id='periodo' name='periodo' class='campo_azul' onchange='seleccionar_fecha_de_corte();'>";
$selector_periodo.="<option value='1'>Periodo 1</option>";
$selector_periodo.="<option value='2'>Periodo 2</option>";
$selector_periodo.="<option value='3'>Periodo 3</option>";
$selector_periodo.="<option value='4'>Periodo 4</option>";
$selector_periodo.="</select>";

//consultar el tipo de entidad
$sql_query_tipo_entidad_asociada="SELECT * FROM gioss_entidades_sector_salud WHERE codigo_entidad='$entidad_salud_usuario_actual'; ";
$resultado_query_tipo_entidad=$coneccionBD->consultar2($sql_query_tipo_entidad_asociada);
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

	$resultado_query_eapb_usuario=$coneccionBD->consultar2($sql_consulta_eapb_usuario_prestador);

	if(count($resultado_query_eapb_usuario)>0)
	{
		foreach($resultado_query_eapb_usuario as $eapb_prestador_usuario_res)
		{
			$eapb.="<option value='".$eapb_prestador_usuario_res['codigo_entidad']."'>".$eapb_prestador_usuario_res['nombre_de_la_entidad']."</option>";
		}
	}
}//fin if si el usuario es administrador y la entidad no es eapb, por lo tanto busca la eapb asociada a la entidad
else if(intval($perfil_usuario_actual)==3 && (intval($tipo_entidad)!=6 && intval($tipo_entidad)!=7 && intval($tipo_entidad)!=8 && intval($tipo_entidad)!=10) )
{
	$sql_query_tipo_entidad_asociada="SELECT * FROM gioss_entidades_sector_salud WHERE codigo_entidad='$entidad_salud_usuario_actual'; ";
	$resultado_query_tipo_entidad=$coneccionBD->consultar2($sql_query_tipo_entidad_asociada);

	if(count($resultado_query_tipo_entidad)>0)
	{
		foreach($resultado_query_tipo_entidad as $eapb_entidad)
		{
			$eapb.="<option value='".$eapb_entidad['codigo_entidad']."'>".$eapb_entidad['nombre_de_la_entidad']."</option>";
		}
	}
}//fin else si el usuario es de perfil 3 y la entidad es de tipo eapb


$eapb.="</select>";
$eapb.="</div>";
//FIN

if(isset($_POST["year_de_corte"]))
{

	$fecha_de_corte=$_POST['year_de_corte']."-".$_POST['fechas_corte'];
	$periodo=$_POST['periodo'];
	$accion=$_POST['selector_estado_info'];
	//echo "<script>alert(\"".$fecha_de_corte." ".$periodo." ".$accion."\");</script>";
	
	
	
	$secuencia_actual=$utilidades->obtenerSecuenciaActual("avs_cargue_archivo_obligatorio_seq_cargue_archivo");	
	//$cod_pss_IPS = $_POST['prestador'];
	$cod_eapb=$_POST['eapb'];
	//echo "<script>alert(\"".$secuencia_actual."\");</script>";
	//echo "<script>alert(\"".$cod_pss_IPS."\");</script>";
	
	
	
	
	date_default_timezone_set ("America/Bogota");
	
	$fecha_array= explode("-",$fecha_de_corte);
	$year=$fecha_array[0];
	
	$fecha_revisar = date('Y-m-d',strtotime($fecha_de_corte));
	
	$fecha_ini_p1= date('Y-m-d',strtotime("03/31/".$year));
    $fecha_fin_p1=date('Y-m-d', strtotime("04/25/".$year));
	
	$fecha_ini_p2= date('Y-m-d',strtotime("06/30/".$year));
    $fecha_fin_p2=date('Y-m-d', strtotime("07/25/".$year));
	
	$fecha_ini_p3= date('Y-m-d',strtotime("09/30/".$year));
    $fecha_fin_p3=date('Y-m-d', strtotime("10/25/".$year));
	
	$fecha_ini_p4_1= date('Y-m-d',strtotime("12/31/".$year));
    $fecha_fin_p4_1=date('Y-m-d', strtotime("12/31/".$year));
	
	$fecha_ini_p4_2= date('Y-m-d',strtotime("01/01/".$year));
    $fecha_fin_p4_2=date('Y-m-d', strtotime("01/25/".$year));
	
	$fecha_ini_bd ="";
	$fecha_fin_bd ="";
	$fecha_de_corte_periodo="";

    if (($fecha_revisar >= $fecha_ini_p1) && ($fecha_revisar <= $fecha_fin_p1))
    {
       $fecha_ini_bd="03/31/".$year;
	   $fecha_fin_bd="04/25/".$year;
	   $fecha_de_corte_periodo="03/30/".$year;
    }
	
	if (($fecha_revisar >= $fecha_ini_p2) && ($fecha_revisar <= $fecha_fin_p2))
    {
       $fecha_ini_bd="06/30/".$year;
	   $fecha_fin_bd="07/25/".$year;
	   $fecha_de_corte_periodo="06/30/".$year;	   
    }
	
	if (($fecha_revisar >= $fecha_ini_p3) && ($fecha_revisar <= $fecha_fin_p3))
    {
       $fecha_ini_bd="09/30/".$year;
	   $fecha_fin_bd="10/25/".$year;
	   $fecha_de_corte_periodo="09/30/".$year;
    }
	
	//en el periodo 4 se intercala ya que el rango pasa de un year al otro
	if (($fecha_revisar >= $fecha_ini_p4_1) && ($fecha_revisar <= $fecha_fin_p4_1))
    {
       $fecha_ini_bd="12/31/".$year;
	   $fecha_fin_bd="01/25/".$year;
	   $fecha_de_corte_periodo="12/31/".$year;
    }
	
	if (($fecha_revisar >= $fecha_ini_p4_2) && ($fecha_revisar <= $fecha_fin_p4_2))
    {
       $fecha_ini_bd="12/31/".$year;
	   $fecha_fin_bd="01/25/".$year;
	   $fecha_de_corte_periodo="12/31/".$year;
    }
    
	$array_fibd=explode("/",$fecha_ini_bd);
	$fecha_ini_bd=$array_fibd[2]."-".$array_fibd[0]."-".$array_fibd[1];
	
	$array_ffbd=explode("/",$fecha_fin_bd);
	$fecha_fin_bd=$array_ffbd[2]."-".$array_ffbd[0]."-".$array_ffbd[1];
	
	$array_fcbd=explode("/",$fecha_de_corte_periodo);
	$fecha_corte_bd=$array_fcbd[2]."-".$array_fcbd[0]."-".$array_fcbd[1];
	
	//echo "<script>alert(\"".$fecha_ini_bd." ".$fecha_fin_bd."\");</script>";
	
	
	$conexionbd = new conexion();
	
	//gios_datos_rechazados_r4505 gios_datos_validados_exito_r4505
	
	$sql_datos_reporte_obligatorio ="";
	if($accion=="validada")
	{
		$sql_datos_reporte_obligatorio .="SELECT * from gios_datos_validados_exito_r4505 WHERE ";
	}
	else if($accion=="rechazada")
	{
		$sql_datos_reporte_obligatorio .="SELECT * from gios_datos_rechazados_r4505 WHERE ";
	}
	$sql_datos_reporte_obligatorio .=" numero_de_identificacion_de_la_entidad_administradora='".$cod_eapb."' ";
	//$sql_datos_reporte_obligatorio .=" AND ";
	//$sql_datos_reporte_obligatorio .=" cod_registro_especial_pss='".$cod_pss_IPS."' ";
	$sql_datos_reporte_obligatorio .=" AND ";
	$sql_datos_reporte_obligatorio .=" fecha_de_corte = '".$fecha_de_corte."'  ";
	$sql_datos_reporte_obligatorio .=";";
	
	$resultados_query_existe=$conexionbd->consultar2($sql_datos_reporte_obligatorio);
	
	if(count($resultados_query_existe)>0)
	{
		$mensaje.="Reporte obligatorio para el periodo $periodo con la fecha de corte $fecha_de_corte  de la EAPB $cod_eapb. ";
		
		$ruta_escribir_archivo=$rutaTemporal."ReporteObligatorio4505_".$fecha_de_corte."_".$entidad_salud_usuario_actual."_".$cod_eapb.".csv";
		//echo "<script>alert(\"".count($resultados_query_existe)."\");</script>";
		$mensaje.="<br> Se obtuvieron ".count($resultados_query_existe)." filas.";
		
		$array_usuarios_duplicados=array();
		$array_numero_secuencia_usuario=array();
		$array_lineas_remover=array();
		$array_ultima_linea=array();
		$n_linea=0;
		foreach($resultados_query_existe as $key=>$linea_consulta)
		{
			//PARTE DUPLICADOS
			if(!array_key_exists($linea_consulta["campo3"]."_".$linea_consulta["campo4"],$array_usuarios_duplicados))
			{
				$array_usuarios_duplicados[$linea_consulta["campo3"]."_".$linea_consulta["campo4"]]=1;
				$array_numero_secuencia_usuario[$linea_consulta["campo3"]."_".$linea_consulta["campo4"]]=intval($linea_consulta["numero_de_secuencia"]);
				$array_ultima_linea[$linea_consulta["campo3"]."_".$linea_consulta["campo4"]]=$key;
				$array_lineas_remover[$linea_consulta["campo3"]."_".$linea_consulta["campo4"]]=array();
			}
			else
			{
				$array_usuarios_duplicados[$linea_consulta["campo3"]."_".$linea_consulta["campo4"]]++;
				if($array_numero_secuencia_usuario[$linea_consulta["campo3"]."_".$linea_consulta["campo4"]]<intval($linea_consulta["numero_de_secuencia"]))
				{
					$array_lineas_remover[$linea_consulta["campo3"]."_".$linea_consulta["campo4"]][]=$array_ultima_linea[$linea_consulta["campo3"]."_".$linea_consulta["campo4"]];
					$array_numero_secuencia_usuario[$linea_consulta["campo3"]."_".$linea_consulta["campo4"]]=intval($linea_consulta["numero_de_secuencia"]);
					
					$array_ultima_linea[$linea_consulta["campo3"]."_".$linea_consulta["campo4"]]=$key;
				}//fin if
			}//cuando ya esta la posicion en el arreglo(los arreglos de php se usan como diccionarios)
			$n_linea++;
		}//fin busca usuarios duplicados con numero de secuencia inferior dejndo el mas reciente
		
		foreach($array_lineas_remover as $usuario)
		{
			foreach($usuario as $indice_usuarios_repetidos_con_secuencia_inferior)
			{
			    unset($resultados_query_existe[$indice_usuarios_repetidos_con_secuencia_inferior]);
			}
		}
		//FIN PARTE DUPLICADOS
		
		//ESCRIBIENDO EN EL CSV
		$ReporteObligatorio = fopen($ruta_escribir_archivo, "w") or die("fallo la creacion del archivo");
		$encabezado_primera_linea="1|".$cod_eapb."|".$fecha_de_corte."|".count($resultados_query_existe);
		fwrite($ReporteObligatorio, $encabezado_primera_linea."\n");
		$cont_lineas=0;
		foreach($resultados_query_existe as $linea_consulta)
		{
			$cont_columnas=0;
			$cadena_escribir_linea="";
			foreach($linea_consulta as $columna)
			{
				if($cont_columnas<count($linea_consulta)-1)
				{
					$cadena_escribir_linea.=$columna."|";
				}
				else
				{
					$cadena_escribir_linea.=$columna;
				}
				$cont_columnas++;
			}
			
			if($cont_lineas<count($resultados_query_existe)-1)
			{
				$cadena_escribir_linea.="\n";
			}
			
			fwrite($ReporteObligatorio, $cadena_escribir_linea);
			
			$cont_lineas++;
		}//fin foreach
		fclose($ReporteObligatorio);
		//FIN ESCRIBIENDO EN EL CSV
		
		//CREAR ZIP
		$archivos_a_comprimir=array();
		$archivos_a_comprimir[0]=$ruta_escribir_archivo;
		$ruta_zip=$rutaTemporal."ReporteObligatorio4505_".$fecha_de_corte."_".$entidad_salud_usuario_actual."_".$cod_eapb.'.zip';
		$result_zip = create_zip($archivos_a_comprimir,$ruta_zip);		
		//FIN CREAR ZIP
		
		$mostrarResultado="inline";
		$resultadoDefinitivo.=$ruta_zip;
			
	}//fin if si hay resultados
	else
	{
		$mensaje.="<br>No se encontraron resultados.";
	}
	
	

}//fin if caundo se hizo submit

$smarty->assign("mensaje_proceso", $mensaje, true);
$smarty->assign("mostrarResultado",$mostrarResultado,true);
$smarty->assign("resultadoDefinitivo", $resultadoDefinitivo, true);
$smarty->assign("campo_eapb", $eapb, true);
//$smarty->assign("campo_prestador", $prestador, true);
$smarty->assign("campo_periodo", $selector_periodo, true);
$smarty->assign("campo_fechas_corte", $selector_fechas_corte, true);
$smarty->assign("nombre", $nombre, true);
$smarty->assign("menu", $menu, true);
$smarty->display('repoblig4505.html.tpl');

?>