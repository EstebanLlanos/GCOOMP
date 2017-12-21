<html>
<head>
<title>Subir diagnostico ciex</title>
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

$archivo_prestadores = fopen("tabla_diagnostico_ciex.csv", 'r') or exit("No se pudo abrir el archivo con las entidades prestadoras");
$archivo_queries = fopen("tabla_diagnostico_ciex.sql", "w") or die("fallo la creacion del archivo");
$cont_reset_div=0;
$cont_linea=0;
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
	codigo_ciex character varying(320),
	descripcion_diagnosticos_ciex character varying(320),
	Codigo_dx_agrupado character varying(320),
	Cod_capitulo_dx character varying(320),
	descripcio_subgrupo_diagnostico character varying(320),
	cod_sexo character varying(320),
	cod_unidad_edad_minima character varying(320),
	edad_minima character varying(320),
	Cod_unidad_edad_maxima character varying(320),
	edad_maxima character varying(320),
	*/
	if($linea_res!="")
	{
		$sql_insert="";
		//INICIA QUERY INSERT gioss_diagnostico_ciex
		$sql_insert.="insert into gioss_diagnostico_ciex ";
		$sql_insert.="(";
		$sql_insert.="codigo_ciex,descripcion_diagnosticos_ciex,Codigo_dx_agrupado,";
		$sql_insert.="Cod_capitulo_dx,descripcio_subgrupo_diagnostico,cod_sexo,";
		$sql_insert.="cod_unidad_edad_minima,edad_minima,Cod_unidad_edad_maxima,";
		$sql_insert.="edad_maxima ";
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
		//FIN QUERY INSERT gioss_diagnostico_ciex
		
		
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
			$mensaje="<p style='color:green;'> tabla gioss_diagnostico_ciex insertada en la base de datos. Linea: ".$cont_linea.".   </p>";
		}
		else
		{
			$mensaje="<p style='color:red'>error al insertar. Linea: ".$cont_linea.".   </p>";
		}

		
		echo "<script>document.getElementById('resultado_conversion').innerHTML=\"".$mensaje."\";</script>";
			
	}//fin if linea no vacia
	
	$cont_linea++;
}//fin while
fclose($archivo_queries);
fclose($archivo_prestadores);

?>

</body>
</html>

