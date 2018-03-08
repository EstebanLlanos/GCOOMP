<?php
set_time_limit(936000);
ini_set('max_execution_time', 936000);
ini_set('memory_limit', '900M');

require '../librerias_externas/Smarty-3.1.17/libs/Smarty.class.php';
include_once ('../utiles/clase_coneccion_bd.php');
include_once ('../utiles/funciones_crear_menu.php');

$coneccionBD = new conexion();
$coneccionBD->crearConexion();

$valor_actual="";
if(isset($_REQUEST['valor_actual'])==true)
{
	$valor_actual=trim($_REQUEST['valor_actual']);
}//fin if


$id_principal="";
if(isset($_REQUEST['id_principal'])==true)
{
	$id_principal=trim($_REQUEST['id_principal']);
}//fin if

$id_padre="";
if(isset($_REQUEST['id_padre'])==true)
{
	$id_padre=trim($_REQUEST['id_padre']);
}//fin if

if($valor_actual!="" && $id_principal!="")
{
	$prioridad_hijo_menor_igual_padre=false;
	if(strtolower($id_padre)!="null"  && $id_padre!="" )
	{
		$query_prioridad_padre="SELECT * FROM gios_menus_opciones_sistema WHERE id_principal='$id_padre' ; ";
		$resultados_prioridad_padre=$coneccionBD->consultar2_no_crea_cierra($query_prioridad_padre);
		if(count($resultados_prioridad_padre)>0 && is_array($resultados_prioridad_padre)==true)
		{
			$valor_padre=$resultados_prioridad_padre[0]['prioridad_jerarquica'];
			if(floatval($valor_actual)<=floatval($valor_padre))
			{
				$prioridad_hijo_menor_igual_padre=true;
			}//fin if
		}//fin if
	}//fin if

	$prioridad_actual_mayor_igual_prioridad_hijos=false;
	
	$query_hijo_menor_prioridad="SELECT * FROM gios_menus_opciones_sistema WHERE id_padre='$id_principal'AND prioridad_jerarquica=(SELECT min(prioridad_jerarquica::numeric) FROM gios_menus_opciones_sistema WHERE id_padre='$id_principal'); ";
	$resultados_hijo_menor_prioridad=$coneccionBD->consultar2_no_crea_cierra($query_hijo_menor_prioridad);
	if(count($resultados_hijo_menor_prioridad)>0 && is_array($resultados_hijo_menor_prioridad)==true)
	{
		$valor_hijo=$resultados_hijo_menor_prioridad[0]['prioridad_jerarquica'];
		if(floatval($valor_actual)>=floatval($valor_hijo))
		{
			$prioridad_actual_mayor_igual_prioridad_hijos=true;
		}//fin if
	}//fin if hay resultados

	

	if($prioridad_hijo_menor_igual_padre==false && $prioridad_actual_mayor_igual_prioridad_hijos==false)
	{
		$query_update_prioridad="UPDATE gios_menus_opciones_sistema SET prioridad_jerarquica='$valor_actual' WHERE id_principal='$id_principal' ; ";
		$error_bd="";
		$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($query_update_prioridad,$error_bd);
		if($error_bd!="")
		{
			echo "ERROR";
		}//fin if
		else
		{
			echo "OK";
		}//fin else
	}
	else if($prioridad_hijo_menor_igual_padre==true)
	{
		echo "ERROR:<br>PJPH";
	}//fin else prioridad hijo menor igual padre 	
	else if($prioridad_actual_mayor_igual_prioridad_hijos==true)
	{
		echo "ERROR:<br>PJAH";
	}//fin else prioridad actual mayor igual hijos 
}//fin if
$coneccionBD->cerrar_conexion();
?>