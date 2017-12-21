<html>
<head>
<title>Subir tabla_administradora_eapb_rips</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body>

<div id='resultado_conversion'></div>

<?php
set_time_limit(936000);
ini_set('max_execution_time', 936000);
ini_set('memory_limit', '900M');

 
include_once ('../utiles/clase_coneccion_bd.php');

function alphanumericAndSpace( $string )
{
    return preg_replace('/[^a-zA-Z0-9\s,@]/', '', $string);
}

$coneccionBD = new conexion();

$archivo_prestadores = fopen("tabla_administradora_eapb_rips.csv", 'r') or exit("No se pudo abrir el archivo con las entidades prestadoras");
$archivo_queries = fopen("tabla_administradora_eapb_rips.sql", "w") or die("fallo la creacion del archivo");
$archivo_error= fopen("tabla_administradora_eapb_rips.error", "w") or die("fallo la creacion del archivo");
$cont_reset_div=0;
$cont_linea=0;
$error="";
$error_para_txt="";
while (!feof($archivo_prestadores)) 
{
	$linea = fgets($archivo_prestadores);
	$linea_res = str_replace("\n","",$linea);
	
	$linea_res = str_replace("�","a",$linea_res);
	$linea_res = str_replace("�","e",$linea_res);
	$linea_res = str_replace("�","i",$linea_res);
	$linea_res = str_replace("�","o",$linea_res);
	$linea_res = str_replace("�","u",$linea_res);
	$linea_res = str_replace("�","n",$linea_res);
	$linea_res = str_replace("�","A",$linea_res);
	$linea_res = str_replace("�","E",$linea_res);
	$linea_res = str_replace("�","I",$linea_res);
	$linea_res = str_replace("�","O",$linea_res);
	$linea_res = str_replace("�","U",$linea_res);
	$linea_res = str_replace("�","N",$linea_res);
	$linea_res = str_replace(" "," ",$linea_res);
	$linea_res= alphanumericAndSpace($linea_res);	
	$campos = explode(",",$linea_res);
	
	/*
	codigo_entidad_eapb character varying(320),
	descripcion_de_la_entidad_administradora_de_plan_de_beneficios character varying(320),
	Regimen character varying(320),
	codigo_regimen character varying(320),
	*/
	if($linea_res!="")
	{
		$sql_insert="";
		//INICIA QUERY INSERT gioss_administradora_eapb_rips
		$sql_insert.="insert into gioss_administradora_eapb_rips ";
		$sql_insert.="(";
		$sql_insert.="codigo_entidad_eapb,";
		$sql_insert.="descripcion_de_la_entidad_administradora_de_plan_de_beneficios,";
		$sql_insert.="Regimen,";
		$sql_insert.="codigo_regimen";
		$sql_insert.=")";
		$sql_insert.="values";
		$sql_insert.="(";
		$cont_campos=0;
		while($cont_campos < count($campos))
		{
			$campo_procesado="";
					
			if($cont_campos == count($campos)-1)
			{
				$campo_procesado = substr($campos[$cont_campos], 0, strlen($campos[$cont_campos])-1);
				$campo_procesado = str_replace("\n","",$campo_procesado);
				$campo_procesado = str_replace("\"","",$campo_procesado);
				$campo_procesado = str_replace("'","",$campo_procesado);
				//$campo_procesado = intval($campo_procesado);
			}
			else
			{
				$campo_procesado = str_replace("\n","",$campos[$cont_campos]);
				$campo_procesado = str_replace("\"","",$campo_procesado);
				$campo_procesado = str_replace("'","",$campo_procesado);
			}
			
			if($cont_campos < count($campos)-1)
			{
				$sql_insert.="'".utf8_decode($campo_procesado)."',";
			}
			else
			{
				$sql_insert.="'".utf8_decode($campo_procesado)."'";
			}
			$cont_campos++;
		}
		$sql_insert.=");";
		//FIN QUERY INSERT gioss_administradora_eapb_rips
		
		
		fwrite($archivo_queries, $sql_insert."\n");
		
		
		$x=0;
		$bool_funciono=false;
		try
		{
		$bool_funciono=$coneccionBD->insertar2($sql_insert, $x);
		
		}
		catch (Exception $e) {}

		$mensaje="";
		if($bool_funciono==false)
		{
			$mensaje="<p style='color:green;'> tabla medicamentos insertada en la base de datos. Linea(cuenta desde 0): ".$cont_linea.". num columnas ".count($campos)."  </p>".$error;
		}
		else
		{
			$error.="<p style='color:red'>error al insertar. Linea(cuenta desde 0): ".$cont_linea.". num columnas ".count($campos)."   </p>";
			$error_para_txt.="error al insertar. Linea(cuenta desde 0): ".$cont_linea.". num columnas ".count($campos)."\n";
			$mensaje=$error;
		}

		
		echo "<script>document.getElementById('resultado_conversion').innerHTML=\"".$mensaje."\";</script>";
			
	}//fin if linea no vacia
	
	$cont_linea++;
}//fin while
fclose($archivo_queries);
fclose($archivo_prestadores);

fwrite($archivo_error, $error_para_txt);
fclose($archivo_error);
?>

</body>
</html>

