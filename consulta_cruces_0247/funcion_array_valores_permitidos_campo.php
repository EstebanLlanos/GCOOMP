<?php

function lista_valores_permitidos_campo($numero_campo_param,$coneccionBD_param)

{
	$array_valores_permitidos_campo=array();
	$array_valores_permitidos_puros_campo=array();
	$descripcion_campo="";

	$numero_campo=trim($numero_campo_param);
	$query_valor_permitido_campo="select * from valores_permitidos_0247 where trim(numero_campo_norma)=trim('$numero_campo') ; ";
	$resultado_query_valores_permitidos=$coneccionBD_param->consultar2_no_crea_cierra($query_valor_permitido_campo);

	
	if(is_array($resultado_query_valores_permitidos) && count($resultado_query_valores_permitidos)>0 )
	{
		foreach ($resultado_query_valores_permitidos as $key => $campo_actual) 
		{
			$lista_valores_permitidos=$campo_actual["lista_valores_permitidos"];
			$descripcion_campo=$campo_actual["nombre_campo"];
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
					
					$array_valores_permitidos_campo[trim($valor_permitido)]="($valor_permitido) $descripcion_valor_permitido";
					$array_valores_permitidos_puros_campo[]=$valor_permitido;
				}//fin if

				

				$cont_pos++;
				
			}//fin while
			

		}//fin foreach
	}//fin if

	$array_resultado['descripcion_campo']=$descripcion_campo;
	$array_resultado['valores_permitidos']=$array_valores_permitidos_campo;
	$array_resultado['valores_permitidos_puros']=$array_valores_permitidos_puros_campo;

	return $array_resultado;


}//fin function
?>