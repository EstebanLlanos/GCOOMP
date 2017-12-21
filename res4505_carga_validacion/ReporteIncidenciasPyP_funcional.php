<html>
<head>
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<title>Reportes</title>
<script type="text/javascript" src="ReporteIncidenciasPyP_funcional.js"></script>
<link type="text/css" href="ReporteIncidenciasPyP_funcional.css" rel="stylesheet" />
<link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap.css" rel="stylesheet" />
</head>

<body>
<?php
set_time_limit(936000);
ini_set('max_execution_time', 936000);
ini_set('memory_limit', '900M');

session_start();


if (!isset($_SESSION['usuario']) && !isset($_SESSION['tipo_perfil']))
{
	header ("Location: ../index.html");
}

include_once ('../utiles/clase_coneccion_bd.php');

$html1="";

$html1.="<h1 align='center' style='color:black;font-family:Helvetica Neue;text-decoration: underline;'>Reporte de inconsistencias PyP ";

function contar_lineas_archivo($ruta)
{
	$lineas=0;
	//CONTADOR DE LINEAS EFICIENTE
		
	$handle = fopen($ruta, "r");
	while(!feof($handle)){
	  $line = fgets($handle);
	  $lineas++;
	}

	fclose($handle);
	//FIN CONTADOR DE LINEAS EFICIENTE
	
	return $lineas;
}


if(isset($_POST['consecutivo']))
{
	$ruta_reportes="../TEMPORALES/";
	$nombre_reporte_caracteres="ReporteCaracteresEspeciales".$_POST['consecutivo'].".txt";
	$nombre_reporte_errores_campos="ErroresCampos".$_POST['consecutivo'].".csv";
	$nombre_reporte_usuarios_duplicados="UsuariosDuplicados".$_POST['consecutivo'].".csv";
	$html1.=$_POST['consecutivo']."</h1>";
	echo $html1;
	$ruta_error_char=$ruta_reportes.$nombre_reporte_caracteres;
	$ruta_error_fields=$ruta_reportes.$nombre_reporte_errores_campos;
	$ruta_usuarios_duplicados=$ruta_reportes.$nombre_reporte_usuarios_duplicados;
	
	$descargas_archivos_html="";
	$descargas_archivos_html.="<table align='center' >";
	
	$descargas_archivos_html.="<tr>";
	
	$size_duplicados=0;
	$size_campos_error=0;
	$size_char_error=0;
	
	if(file_exists($ruta_usuarios_duplicados) && false)
	{
	$size_duplicados=contar_lineas_archivo($ruta_usuarios_duplicados);
	
	$descargas_archivos_html.="<td>";
	$descargas_archivos_html.="<input type='button' class='btn btn-success color_boton' value='Descargar Reporte Usuarios Duplicados. No. lineas: ".$size_duplicados."' onclick='download_duplicados(\"".$ruta_usuarios_duplicados."\");' />";
	$descargas_archivos_html.="</td>";
	}
	
	if(file_exists($ruta_error_fields))
	{
	$size_campos_error=contar_lineas_archivo($ruta_error_fields);
	
	$descargas_archivos_html.="<td>";
	$descargas_archivos_html.="<input type='button' class='btn btn-success color_boton' value='Descargar Reporte Inconsistencias Campos. No. lineas: ".$size_campos_error."' onclick='download_inconsistencias_campos(\"".$ruta_error_fields."\");'/>";
	$descargas_archivos_html.="</td>";
	}
	
	if(file_exists($ruta_error_char))
	{
	$size_char_error=contar_lineas_archivo($ruta_error_char);
	
	$descargas_archivos_html.="<td>";
	$descargas_archivos_html.="<input type='button' class='btn btn-success color_boton' value='Descargar Reporte Errores Sobre Caracteres Especiales. No. lineas: ".$size_char_error."' onclick='download_errores_caracteres_especiales(\"".$ruta_error_char."\");'/>";
	$descargas_archivos_html.="</td>";
	}
	
	$descargas_archivos_html.="</tr>";
	
	$descargas_archivos_html.="<tr>";
	$descargas_archivos_html.="<td colspan='100' align='center'>";
	$descargas_archivos_html.="<div id='estado_conversion'></div>";
	$descargas_archivos_html.="</td>";
	$descargas_archivos_html.="</tr>";
	
	$descargas_archivos_html.="</table>";
	echo $descargas_archivos_html;
	
	//DIVS PARA RESULTADOS PAGINADOS
	$html_divs_separadores="";
	if(file_exists($ruta_usuarios_duplicados) && false)
	{
	$html_divs_separadores.="<h1 align='center' style='color:black;font-family:Helvetica Neue;'>Usuarios Duplicados:<h1>";
	}
	$html_divs_separadores.="<div id='duplicados_div'></div>";
	if(file_exists($ruta_error_fields))
	{
	$html_divs_separadores.="<h1 align='center' style='color:black;font-family:Helvetica Neue;'>Errores En Los Campos:<h1>";
	}
	$html_divs_separadores.="<div id='error_campos4505_div'></div>";
	
	$html_divs_separadores.="<div id='caracteres_error'></div>";
	
	//las funciones van adelante para poder asignar en los divs
	$html_divs_separadores.="<script>";
	if(file_exists($ruta_usuarios_duplicados) && false)
	{
		$html_divs_separadores.="traer_seccion_texto(0,10,'".$ruta_usuarios_duplicados."',".$size_duplicados.",'duplicados_div',0);";
	}
	if(file_exists($ruta_error_fields))
	{
		$html_divs_separadores.="traer_seccion_texto(0,10,'".$ruta_error_fields."',".$size_campos_error.",'error_campos4505_div',1);";
	}
	if(false)
	{
		$html_divs_separadores.="traer_seccion_texto(0,10,'".$ruta_error_char."',".$size_char_error.",'caracteres_error',2);";
	}
	$html_divs_separadores.="</script>";
	echo $html_divs_separadores;
	
	//FIN DIVS PARA RESULTADOS PAGINADOS
	
	
}
else
{
	$html1.="</h1>";
	echo $html1;
}


?>
</body>
</html>