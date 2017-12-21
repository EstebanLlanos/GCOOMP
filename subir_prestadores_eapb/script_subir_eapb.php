<html>
<head>
<title>Subir EAPB</title>
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

/*
echo $coneccionBD->obtenerCodificacion()."<br>";
$coneccionBD->ponerCodificacion("LATIN1");
echo $coneccionBD->obtenerCodificacion()."<br>";
*/

$archivo_eapb = fopen("eapb.csv", 'r') or exit("No se pudo abrir el archivo csv con las entidades administradoras");
$archivo_queries = fopen("queries_insert_eapb.sql", "w") or die("fallo la creacion del archivo");
$cont_reset_div=0;
$cont_linea=0;
$mensaje="";
while (!feof($archivo_eapb)) 
{
	$linea = fgets($archivo_eapb);
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
	$linea_res= alphanumericAndSpace($linea_res);
	
	$campos = explode(",",$linea_res);
	
	//echo $linea_res."<br>";
	
	/*
	 cod_entidad_administradora character varying(6) NOT NULL,
	  cod_tipo_identificacion character varying(2) NOT NULL,
	  num_tipo_identificacion character varying(15) NOT NULL,
	  nom_entidad_administradora character varying(100) NOT NULL,
	  des_representante_legal character varying(60),
	  des_direccion character varying(60),
	  des_telefono character varying(30),
	  des_nombre_contacto character varying(30),
	  txt_correo_contacto character varying(60) NOT NULL,
	  cod_tipo_regimen character varying(1) NOT NULL,
	  logo character varying(50)
	*/
	if($linea_res!="")
	{
		$sql_insert="";
		//INICIA QUERY INSERT gios_entidad_administradora
		$sql_insert.="insert into gios_entidad_administradora";
		$sql_insert.="(";
		$sql_insert.="cod_entidad_administradora,codigo_tipo_entidad_eapb,nom_entidad_administradora,";
		$sql_insert.="des_representante_legal,des_direccion,des_telefono,";
		$sql_insert.="txt_correo_contacto,sigla,mpio,";
		$sql_insert.="dpto";
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
		//FIN QUERY INSERT gios_entidad_administradora
		
		fwrite($archivo_queries, $sql_insert."\n");

		$x=0;
		$bool_funciono=false;
		try
		{
		$bool_funciono=$coneccionBD->insertar2($sql_insert, $x);
		}
		catch (Exception $e) {}

		
		if($bool_funciono==false)
		{
			$mensaje="<p style='color:green;'>entidad administradora eapb insertada en la base de datos. Linea: ".$cont_linea." ".$sql_insert." ".utf8_decode($campos[2])."</p><br></br>";
		}
		else
		{
			$mensaje="<p style='color:red'>error al insertar. Linea: ".$cont_linea." ".$sql_insert."</p><br></br>";
		}

		
		echo "<script>document.getElementById('resultado_conversion').innerHTML=\"".$mensaje."\";</script>";
			
		
	}//fin if linea res
	$cont_linea++;
}
fclose($archivo_queries);
fclose($archivo_eapb);
?>

</body>
</html>

