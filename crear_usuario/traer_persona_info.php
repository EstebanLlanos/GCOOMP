<?php

include_once ('../utiles/clase_coneccion_bd.php');

$coneccionBD = new conexion();

$html_poner_info="";

if(isset($_REQUEST["tipo_id"]) && isset($_REQUEST["id"]))
{
	$tipo_id=$_REQUEST["tipo_id"];
	$id=$_REQUEST["id"];
	//parte verifica si existe el usuario
	$sql_consulta_existe_persona="SELECT * FROM gios_usuarios_sistema WHERE tipo_identificacion_usuario='$tipo_id' AND identificacion_usuario='$id';";
	$resultado_query_existe_persona=$coneccionBD->consultar2($sql_consulta_existe_persona);

	$existe_persona=count($resultado_query_existe_persona)>0;
	
	
	//fin parte verifica existe el usuario
	
	if($existe_persona)
	{
		foreach($resultado_query_existe_persona as $info_persona)
		{
			$html_poner_info.="document.getElementById('primer_nombre').value='".$info_persona["primer_nombre_usuario"]."';\n";
			$html_poner_info.="document.getElementById('segundo_nombre').value='".$info_persona["segundo_nombre_usuario"]."';\n";
			$html_poner_info.="document.getElementById('primer_apellido').value='".$info_persona["primer_apellido_usuario"]."';\n";
			$html_poner_info.="document.getElementById('segundo_apellido').value='".$info_persona["segundo_apellido_usuario"]."';\n";
			
			
			$html_poner_info.="document.getElementById('direccion').value='".$info_persona["direccion_usuario"]."';\n";
			$html_poner_info.="document.getElementById('telefono').value='".$info_persona["telefono_fijo"]."';\n";
			$html_poner_info.="document.getElementById('celular').value='".$info_persona["telefono_celular"]."';\n";
			
			$fecha_nacimiento=explode("-",$info_persona["fecha_nacimiento"]);
			
			
			$html_poner_info.="document.getElementById('fecha_cumple').value='".$fecha_nacimiento[1]."/".$fecha_nacimiento[2]."/".$fecha_nacimiento[0]."';\n";
			
			$html_poner_info.="document.getElementById('primer_nombre').readOnly=true;\n";
			$html_poner_info.="document.getElementById('segundo_nombre').readOnly=true;\n";
			$html_poner_info.="document.getElementById('primer_apellido').readOnly=true;\n";
			$html_poner_info.="document.getElementById('segundo_apellido').readOnly=true;\n";
			
			$sql_consulta_nicklogueo="SELECT * FROM gioss_entidad_nicklogueo_perfil_estado_persona WHERE tipo_id='$tipo_id' AND identificacion_usuario='$id' ORDER BY nicklogueo asc;";
			$resultado_query_nicklogueo=$coneccionBD->consultar2($sql_consulta_nicklogueo);
			
			$existe_asociado_prestadora=count($resultado_query_nicklogueo)>0;
			if($existe_asociado_prestadora)
			{
				$ultimo_nick_logueo=$resultado_query_nicklogueo[count($resultado_query_nicklogueo)-1]["nicklogueo"];
				$ultimo_correo=$resultado_query_nicklogueo[count($resultado_query_nicklogueo)-1]["correo_usuario"];
				
				$html_poner_info.="document.getElementById('nick_logueo').value='".explode("_",$ultimo_nick_logueo)[0]."_".(intval(explode("_",$ultimo_nick_logueo)[1])+1)."';\n";
				$html_poner_info.="document.getElementById('email').value='".$ultimo_correo."';\n";
				
			}
			else
			{
				$html_poner_info.="document.getElementById('nick_logueo').value='".str_replace(" ","",$info_persona["primer_nombre_usuario"].$info_persona["primer_apellido_usuario"]."_1")."';\n";
			}
			$html_poner_info.="se_trajo_info=true;";
		}//fin foreach		
		
	}//fin if existe persona
	else
	{
		$html_poner_info.="document.getElementById('primer_nombre').readOnly=false;\n";
		$html_poner_info.="document.getElementById('segundo_nombre').readOnly=false;\n";
		$html_poner_info.="document.getElementById('primer_apellido').readOnly=false;\n";
		$html_poner_info.="document.getElementById('segundo_apellido').readOnly=false;\n";
		$html_poner_info.="se_trajo_info=false;";
	}
	
	echo $html_poner_info;
}

?>