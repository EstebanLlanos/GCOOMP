<?php
include_once ('../utiles/clase_coneccion_bd.php');

$coneccionBD = new conexion();
$coneccionBD->crearConexion();

if(isset($_REQUEST['selector_campo_cruzado']) 
	&& trim($_REQUEST['selector_campo_cruzado'])!=""
	&& isset($_REQUEST['id_para_selector'])
	&& trim($_REQUEST['id_para_selector'])!=""
	)
{
	$numero_campo=trim($_REQUEST['selector_campo_cruzado']);
	$id_para_selector=trim($_REQUEST['id_para_selector']);
	$array_id_para_selector=explode("_", $id_para_selector);
	$numero_id="0";
	if(isset($array_id_para_selector[1]) && trim($array_id_para_selector[1])!="" )
	{
		$numero_id=trim($array_id_para_selector[1]);
	}//fin if


	$query_valor_permitido_campo="select * from valores_permitidos_0247 where trim(numero_campo_norma)=trim('$numero_campo') ; ";
	$resultado_query_valores_permitidos=$coneccionBD->consultar2_no_crea_cierra($query_valor_permitido_campo);

	$html_selector="";
	$html_selector.="<h6  style='color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;'>Valor Permitido del Campo $numero_id:</h6>";
	$html_selector.="<select id='$id_para_selector' name='$id_para_selector' class='campo_azul'>";
	$html_selector.="<option value='none'>Seleccione un valor permitido para detallar</option>";
	if(is_array($resultado_query_valores_permitidos) && count($resultado_query_valores_permitidos)>0 )
	{
		foreach ($resultado_query_valores_permitidos as $key => $campo_actual) 
		{
			$lista_valores_permitidos=$campo_actual["lista_valores_permitidos"];
			$array_valores_permitidos=explode(";", $lista_valores_permitidos);


			$cont_pos=0;
			while ($cont_pos<count($array_valores_permitidos)) 
			{	
				$valor_permitido="";
				$descripcion_valor_permitido="";
				if(($cont_pos%2)==0)
				{
					$valor_permitido=$array_valores_permitidos[$cont_pos];
					$pos_descripcion=$cont_pos+1;
					if($pos_descripcion<count($array_valores_permitidos) )
					{
						$descripcion_valor_permitido=$array_valores_permitidos[$pos_descripcion];
					}//fin if

					//se agrega aqui porque ya se verificaron dos posiciones y entra al condicional cuando es par
					$html_selector.="<option value='$valor_permitido'>($valor_permitido) $descripcion_valor_permitido</option>";
				}//fin if

				

				$cont_pos++;
				
			}//fin while
			

		}//fin foreach
	}//fin if

	if($id_para_selector!="campocrossvp_1")
	{
		$html_selector.="<option value='TODOS' selected>TODOS LOS VALORES PERMITIDOS</option>";
	}

	$html_selector.="</select>";

	echo $html_selector;
}//fin if


$coneccionBD->cerrar_conexion();
?>