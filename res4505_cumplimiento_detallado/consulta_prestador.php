<?php
set_time_limit(936000);
ini_set('max_execution_time', 936000);
ini_set('memory_limit', '900M');

include_once ('../utiles/clase_coneccion_bd.php');

if(isset($_REQUEST["eapb"]) )
{
	$eapb=$_REQUEST["eapb"];
	
	$coneccionBD = new conexion();
	
	//SELECTOR PRESTADORES ASOCIADOS EAPB
	$prestador="";
	$prestador.="<div id='div_prestador'>";
	$prestador.="<select id='prestador' name='prestador' class='campo_azul'>";
	$prestador.="<option value='none'>Seleccione un Prestador</option>";

	
	
	$sql_consulta_prestadores_asociados_eapb="SELECT ea.codigo_entidad,ea.nombre_de_la_entidad FROM ";
	$sql_consulta_prestadores_asociados_eapb.=" gioss_relacion_entre_entidades_salud re INNER JOIN gioss_entidades_sector_salud ea ON (re.entidad1 = ea.codigo_entidad) ";
	$sql_consulta_prestadores_asociados_eapb.=" WHERE re.entidad2='".$eapb."' ";
	$resultado_query_prestadores_asociados_eapb=$coneccionBD->consultar2($sql_consulta_prestadores_asociados_eapb);

	if(count($resultado_query_prestadores_asociados_eapb)>0)
	{
		foreach($resultado_query_prestadores_asociados_eapb as $prestador_asociado_eapb)
		{
			$prestador.="<option value='".$prestador_asociado_eapb['codigo_entidad']."'>".$prestador_asociado_eapb['nombre_de_la_entidad']."</option>";
		}
	}

	$prestador.="</select>";
	$prestador.="</div>";
	//FIN
	
	echo $prestador;
}
else if( (isset($_REQUEST["eapb"]) && $_REQUEST["eapb"]=="none") || !(isset($_REQUEST["eapb"])))
{
	//SELECTOR PRESTADORES ASOCIADOS EAPB
	$prestador="";
	$prestador.="<div id='div_prestador'>";
	$prestador.="<select id='prestador' name='prestador' class='campo_azul'>";
	$prestador.="<option value='none'>Seleccione un prestador</option>";
	$prestador.="</select>";
	$prestador.="</div>";
	//FIN

	echo $prestador;
}
?>