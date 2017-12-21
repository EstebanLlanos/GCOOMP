<?php
set_time_limit(936000);
ini_set('max_execution_time', 936000);
ini_set('memory_limit', '900M');

include_once ('../utiles/clase_coneccion_bd.php');

if(isset($_REQUEST["prestador"]) && isset($_REQUEST["tipo_id"]) && isset($_REQUEST["identificacion"]))
{
	$prestador=$_REQUEST["prestador"];
	$tipo_id=$_REQUEST["tipo_id"];
	$identificacion=$_REQUEST["identificacion"];
	
	//SELECTOR EAPB-ASOCIADA_PRESTADOR_ASOCIADO_USUARIO
	$eapb="";
	$eapb.="<div id='div_eapb'>";
	$eapb.="<select id='eapb' name='eapb' class='campo_azul'>";
	$eapb.="<option value='none'>Seleccione un EAPB</option>";

	$coneccionBD = new conexion();
	/*
	$sql_consulta_eapb_usuario_prestador="SELECT gios_entidad_administradora.cod_entidad_administradora,gios_entidad_administradora.nom_entidad_administradora FROM ";
	$sql_consulta_eapb_usuario_prestador.=" gios_usuario_entidad_prestadora_eapb INNER JOIN gios_entidad_administradora ON (gios_usuario_entidad_prestadora_eapb.cod_entidad_administradora = gios_entidad_administradora.cod_entidad_administradora) ";
	$sql_consulta_eapb_usuario_prestador.=" WHERE tipo_identificacion_usuario='".$tipo_id."' AND  identificacion_usuario='".$identificacion."' AND gios_usuario_entidad_prestadora_eapb.cod_registro_especial_pss='".$prestador."'; ";
	*/
	$sql_consulta_eapb_usuario_prestador="SELECT ea.codigo_entidad,ea.nombre_de_la_entidad FROM ";
	$sql_consulta_eapb_usuario_prestador.=" gioss_relacion_entre_entidades_salud re INNER JOIN gioss_entidades_sector_salud ea ON (re.entidad2 = ea.codigo_entidad) ";
	$sql_consulta_eapb_usuario_prestador.=" WHERE re.entidad1='".$prestador."' ";
	$resultado_query_prestador_usuario=$coneccionBD->consultar2($sql_consulta_eapb_usuario_prestador);

	if(count($resultado_query_prestador_usuario)>0)
	{
		foreach($resultado_query_prestador_usuario as $eapb_prestador_usuario_res)
		{
			$eapb.="<option value='".$eapb_prestador_usuario_res['codigo_entidad']."'>".$eapb_prestador_usuario_res['nombre_de_la_entidad']."</option>";
		}
	}

	$eapb.="</select>";
	$eapb.="</div>";
	//FIN

	echo $eapb;
}
else
{
	//SELECTOR EAPB-ASOCIADA_PRESTADOR_ASOCIADO_USUARIO
	$eapb="";
	$eapb.="<div id='div_eapb'>";
	$eapb.="<select id='eapb' name='eapb' class='campo_azul'>";
	$eapb.="<option value='none'>Seleccione un EAPB</option>";
	$eapb.="</select>";
	$eapb.="</div>";
	//FIN

	echo $eapb;
}
?>