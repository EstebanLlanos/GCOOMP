<?php
include_once ('../utiles/clase_coneccion_bd.php');

$coneccionBD = new conexion();
$coneccionBD->crearConexion();

if(isset($_REQUEST['selector_campo_especifico']) 
	&& trim($_REQUEST['selector_campo_especifico'])!=""
	)
{
	$numero_campo=trim($_REQUEST['selector_campo_especifico']);
	$query_valor_permitido_campo="select * from valores_permitidos_2463 where trim(numero_campo_norma)=trim('$numero_campo') ; ";
	$resultado_query_valores_permitidos=$coneccionBD->consultar2_no_crea_cierra($query_valor_permitido_campo);

	$html_selector="<select id='selector_campo_valor_permitido_1' name='selector_campo_valor_permitido_1' class='campo_azul'>";
	$html_selector.="<option value=''>Seleccione un valor permitido para detallar</option>";
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
			

		}
	}//fin if
	$html_selector.="</select>";

	echo $html_selector;
}//fin if


$coneccionBD->cerrar_conexion();
?>