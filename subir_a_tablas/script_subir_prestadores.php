<html>
<head>
<title>Subir Prestadores</title>
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

$archivo_prestadores = fopen("prestadores_sin_comas.csv", 'r') or exit("No se pudo abrir el archivo con las entidades prestadoras");
$archivo_queries = fopen("queries_insert_prestadores.sql", "w") or die("fallo la creacion del archivo");
$cont_reset_div=0;
$cont_linea=0;
while (!feof($archivo_prestadores)) 
{
	$linea = fgets($archivo_prestadores);
	$linea_res = str_replace("\n","",$linea);
	
	$linea_res = str_replace("á","a",$linea_res);
	$linea_res = str_replace("é","e",$linea_res);
	$linea_res = str_replace("í","i",$linea_res);
	$linea_res = str_replace("ó","o",$linea_res);
	$linea_res = str_replace("ú","u",$linea_res);
	$linea_res = str_replace("ñ","n",$linea_res);
	$linea_res = str_replace("Á","A",$linea_res);
	$linea_res = str_replace("É","E",$linea_res);
	$linea_res = str_replace("Í","I",$linea_res);
	$linea_res = str_replace("Ó","O",$linea_res);
	$linea_res = str_replace("Ú","U",$linea_res);
	$linea_res = str_replace("Ñ","N",$linea_res);
	$linea_res = str_replace(" "," ",$linea_res);
	$linea_res= alphanumericAndSpace($linea_res);	
	$campos = explode(",",$linea_res);
	
	/*
	 cod_tipo_identificacion character varying(2) NOT NULL,
	  num_tipo_identificacion character varying(90) NOT NULL,
	  cod_registro_especial_pss character varying(12) NOT NULL,
	  nom_entidad_prestadora character varying(130),
	  des_representante_legal character varying(60),
	  cod_municipio character varying(5),
	  des_direccion character varying(200),
	  des_telefono character varying(60),
	  txt_correo_contacto character varying(60),
	  clase_prestador character varying(2),
	  cod_tipo_entidad character varying(2),
	  cod_naturaleza_juridica character varying(2),
	  cod_tipo_cobertura character varying(2),
	  num_sede_ips character varying(2) NOT NULL,
	  digito_verificacion character varying(2) NOT NULL,
	  nombre_comercial_prestador character varying(130) NOT NULL,
	  zona character varying(1) NOT NULL,
	  cod_nivel_atencion character varying(2) NOT NULL,
	*/
	if($linea_res!="")
	{
		$sql_insert="";
		//INICIA QUERY INSERT gios_prestador_servicios_salud
		$sql_insert.="insert into gios_prestador_servicios_salud";
		$sql_insert.="(";
		$sql_insert.="cod_tipo_identificacion,num_tipo_identificacion,cod_registro_especial_pss,";
		$sql_insert.="nom_entidad_prestadora,des_representante_legal,cod_municipio,";
		$sql_insert.="des_direccion,des_telefono,txt_correo_contacto,";
		$sql_insert.="clase_prestador,cod_tipo_entidad,cod_naturaleza_juridica,";
		$sql_insert.="cod_tipo_cobertura,num_sede_ips,digito_verificacion,";
		$sql_insert.="nombre_comercial_prestador,zona,cod_nivel_atencion, cod_depto";
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
				$campo_procesado = intval($campo_procesado);
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
		//FIN QUERY INSERT gios_prestador_servicios_salud
		
		
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
			$mensaje="<p style='color:green;'>prestador insertado en la base de datos. Linea: ".$cont_linea.".   </p>";
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

