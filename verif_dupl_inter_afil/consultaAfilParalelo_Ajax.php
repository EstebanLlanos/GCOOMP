<?php
//filtra un reporte de inconsistencias de una norma seleccionada
set_time_limit(2592000);
ini_set('max_execution_time', 2592000);
ini_set('memory_limit', '2000M');

error_reporting(E_ALL);
ini_set('display_errors', '0');
include_once ('../utiles/clase_coneccion_bd.php');
$coneccionBD = new conexion();
$coneccionBD->crearConexion();

$tablaAfiliados="gioss_afiliados_eapb_mp";
$order_by=" ORDER BY id_afiliado asc ";
$query_comun_afil="	select * FROM $tablaAfiliados  $order_by ";

$cantidad_registros_bloque_afil=50000;
$ultima_posicion_afil=0;
$IdProceso=0;
$pathArchivoAfiliadosActual="";

//lee archivo director proceso
$arrayRequest=$_REQUEST;
$director=$arrayRequest['director'];
$pathArchivoAfiliadosActual=$arrayRequest['resultfile'];
$ultima_posicion_afil=$arrayRequest['start'];
$cantidad_registros_bloque_afil=$arrayRequest['block'];
$numero_filas_afil=$arrayRequest['totalrows'];
//inicio lee archivo director proceso


$consecutivo_orden_bd=$ultima_posicion_afil;

$primera_linea=true;
$cont_registros=0;
if($numero_filas_afil>0)
{

	$query_a_extraer_resultados_afil="";

	$query_a_extraer_resultados_afil.=$query_comun_afil;

	$query_a_extraer_resultados_afil.=" LIMIT $cantidad_registros_bloque_afil OFFSET $ultima_posicion_afil; ";

	$error_bd_seq_afil="";
	$resultados_afil=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($query_a_extraer_resultados_afil, $error_bd_seq_afil);		
	if($error_bd_seq_afil!="")
	{
	    //echo "Error al consultar los resultados.<br>";

	}//fin if
	else if(is_array($resultados_afil) && count($resultados_afil)>0 )
	{
		foreach ($resultados_afil as $indiceFila => $filaAfil) 
		{
			# code...
			$arrayDataAfil=array();

			$arrayDataAfil[]=$numeroId=trim($filaAfil['id_afiliado']);						
			$arrayDataAfil[]=$tipoId=trim($filaAfil['tipo_id_afiliado']);
			$arrayDataAfil[]=$primerApellido=trim($filaAfil['primer_apellido']);
			$arrayDataAfil[]=$primerNombre=trim($filaAfil['primer_nombre']);
			$arrayDataAfil[]=$segundoApellido=trim($filaAfil['segundo_apellido']);
			$arrayDataAfil[]=$segundoNombre=trim($filaAfil['segundo_nombre']);
			$arrayDataAfil[]=$sexo=trim($filaAfil['sexo']);
			$arrayDataAfil[]=$fechaNacimiento=trim($filaAfil['fecha_nacimiento']);
			$arrayDataAfil[]=$consecutivo_orden_bd;


			$lineaDataAfil=implode('|', $arrayDataAfil);

			$archivoAfiliadosActual=fopen($pathArchivoAfiliadosActual, "a");
			if($primera_linea==true)
			{
				fwrite($archivoAfiliadosActual, $lineaDataAfil);
				$primera_linea=false;
			}//fin if
			else
			{
				fwrite($archivoAfiliadosActual, "\n".$lineaDataAfil);
			}//fin else
			fclose($archivoAfiliadosActual);

			$consecutivo_orden_bd++;
			
			$cont_registros++;
		}//fin foreach
	}//fin else if
	
	date_default_timezone_set('America/Bogota');
	$fecha_temp=date('Y-m-d');
	$tiempo_temp=date('H:i:s');

	$mensaje_director=$pathArchivoAfiliadosActual."|ultima_posicion_afil_".$ultima_posicion_afil."|cantidad_registros_bloque_afil_".$cantidad_registros_bloque_afil."|consecutivo_orden_bd_termina_en_".$consecutivo_orden_bd."|cont_registros_".$cont_registros."|tiempo_temp_".$tiempo_temp."|fecha_temp_".$fecha_temp;
	$archivoDirector=fopen($director, "a");
	fwrite($archivoDirector, $mensaje_director."\n");
	fclose($archivoDirector);
	
	$arrayRuta=explode("/", $pathArchivoAfiliadosActual);
	$nombre_archivo=$arrayRuta[count($arrayRuta)-1];
	echo "Termino Archivo $nombre_archivo en $consecutivo_orden_bd de un bloque maximo $cantidad_registros_bloque_afil <br>";

}//fin  if 



$coneccionBD->cerrar_conexion();
?>