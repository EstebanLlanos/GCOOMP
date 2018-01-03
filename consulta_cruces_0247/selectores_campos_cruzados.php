<?php
include_once ('../utiles/clase_coneccion_bd.php');
require_once ("funcion_array_valores_permitidos_campo.php");

$coneccionBD = new conexion();
$coneccionBD->crearConexion();

//PARTE CONSULTA NOMBRES CAMPOS P1
$sql_query_valores_permitidos="SELECT * FROM valores_permitidos_0247 order by numero_campo_norma::numeric asc; ";
$resultado_query_valores_permitidos=$coneccionBD->consultar2_no_crea_cierra($sql_query_valores_permitidos);


//FIN PARTE CONSULTA NOMBRES CAMPOS P1

if(isset($_REQUEST['numero_campocross_actual'])
	&& trim($_REQUEST['numero_campocross_actual'])!=""
	&& ctype_digit($_REQUEST['numero_campocross_actual'])
	&& isset($_REQUEST['str_array_campos'])
	&& trim($_REQUEST['str_array_campos'])!=""
	&& isset($_REQUEST['str_array_vpcampos'])
	&& trim($_REQUEST['str_array_vpcampos'])!=""
	)//fin condicion if
{
	$numero_campocross_actual=intval($_REQUEST['numero_campocross_actual']);
	$array_campos_valores_seleccionados=explode("ZsepZ", trim($_REQUEST['str_array_campos']) );
	$array_campos_valores_permitidos_seleccionados=explode("ZsepZ", trim($_REQUEST['str_array_vpcampos']) );

	//$numero_campocross_actual==(count($array_campos_valores_seleccionados)-1) ; ya que $numero_campocross_actual debe ser mayor por el 
	//campo que se adicionara

	//echo $numero_campocross_actual."<br>".print_r($array_campos_valores_seleccionados,true)."<br>".print_r($array_campos_valores_permitidos_seleccionados,true)."<br>";

	$html_tabla_selectores="";
	$html_tabla_selectores.="<table style='width: 50%;'>";
	$html_tabla_selectores.="
		<tr>
			<td colspan='2' style='text-align:left;'>
				<h6  style='color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;'>Seleccione si se comparara entre valores especificos seleccionados o se mostrara una tabla comparativa entre los valores permitidos de los campos cruzados</h6>
				<select id='selector_all_or_one_vp' name='selector_all_or_one_vp' class='campo_azul' onchange='all_or_one_vp();'>
					<option value='allvp' >Todos los Valores Permitidos</option>
					<option value='specificvp' selected>Valores Permitido Especifico</option>
				</select>
			</td>
		</tr>
	";
	$cont_old_campo=1;
	$opciones_selector_campos_sin_elegir="";
	while($cont_old_campo<$numero_campocross_actual)
	{
		$array_valores_permitidos_del_campo=lista_valores_permitidos_campo($array_campos_valores_seleccionados[$cont_old_campo-1],$coneccionBD);
		$html_tabla_selectores.="
		<tr>
			<td style='text-align:left;'>
				<div  id='divcampocross_$cont_old_campo'>
					<h6  style='color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;''>Campo $cont_old_campo para consulta cruzada:</h6>
					<select id='campocross_$cont_old_campo' name='campocross_$cont_old_campo' class='campo_azul' onchange=\"consultar_valor_permitido_campo_cruzado('divcampocrossvp_$cont_old_campo','$cont_old_campo');\">
						<option value='none' selected>Seleccione Campo</option>
		";

		$opciones_selector_campos="";
		$opciones_selector_campos_sin_elegir="";
		//PARTE SELECTORES
		if(is_array($resultado_query_valores_permitidos) && count($resultado_query_valores_permitidos)>0 )
		{
			$cont_c1=0;
			while($cont_c1<count($resultado_query_valores_permitidos) )
			{
				$nombre_campo=trim($resultado_query_valores_permitidos[$cont_c1]['nombre_campo']);
				$numero_campo_norma=intval(trim($resultado_query_valores_permitidos[$cont_c1]['numero_campo_norma']) );
				if($array_campos_valores_seleccionados[$cont_old_campo-1]==$numero_campo_norma && $array_campos_valores_seleccionados[$cont_old_campo-1]!="none")
				{
					$opciones_selector_campos.="<option value='$numero_campo_norma' selected>Campo Numero $numero_campo_norma $nombre_campo </option>";
				}
				else
				{
					$opciones_selector_campos.="<option value='$numero_campo_norma'>Campo Numero $numero_campo_norma $nombre_campo </option>";
				}//fin else
				$opciones_selector_campos_sin_elegir.="<option value='$numero_campo_norma'>Campo Numero $numero_campo_norma $nombre_campo </option>";
				
				$cont_c1++;

			}//fin while
		}//fin if
		else
		{
			$cont_c1=0;
			while($cont_c1<210)
			{
				$opciones_selector_campos.="<option value='$cont_c1'>Campo Numero $cont_c1 </option>";
				$cont_c1++;

			}//fin while
		}//else
		//FIN PARTE SELECTORES

		$html_tabla_selectores.=$opciones_selector_campos;

		$html_tabla_selectores.="
						
					</select>
					<input type='hidden' id='copycampocross_$cont_old_campo' name='copycampocross_$cont_old_campo'>
				</div>
			</td>
			";

		$array_descripcion_vp=$array_valores_permitidos_del_campo['valores_permitidos'];
		$array_valor_permitido=$array_valores_permitidos_del_campo['valores_permitidos_puros'];

		$html_tabla_selectores.="
			<td style='text-align:left;'>
			    <div id='divcampocrossvp_$cont_old_campo' >
			    	<h6  style='color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;''>Valor Permitido del Campo $cont_old_campo:</h6>
					<select id='campocrossvp_$cont_old_campo' name='campocrossvp_$cont_old_campo' class='campo_azul'>
						<option value='none' >Seleccione el valor permitido para el campo $cont_old_campo</option>
						";
		$cont_vp=0;
		$opciones_valores_permitidos="";
		foreach ($array_valor_permitido as $key => $valor_permitido_actual) 
		{
			$descripcion_valor_permitido=print_r($array_descripcion_vp[$valor_permitido_actual],true);
			if($array_campos_valores_permitidos_seleccionados[$cont_old_campo-1]==$valor_permitido_actual)
			{
				$opciones_valores_permitidos.="<option value='$valor_permitido_actual' selected>$descripcion_valor_permitido</option>";
			}//fin if
			else
			{
				$opciones_valores_permitidos.="<option value='$valor_permitido_actual'>$descripcion_valor_permitido</option>";
			}//fin else
			$cont_vp++;
		}//fin foreach

		$html_tabla_selectores.=$opciones_valores_permitidos;

		$html_tabla_selectores.="
					</select>
			    </div>
			</td>
		</tr>
		";
		$cont_old_campo++;
	}//fin while

	$html_tabla_selectores.="
		<tr>
			<td style='text-align:left;'>
				<div  id='divcampocross_$numero_campocross_actual'>
					<h6  style='color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;'>Campo $numero_campocross_actual para consulta cruzada:</h6>
					<select id='campocross_$numero_campocross_actual' name='campocross_$numero_campocross_actual' class='campo_azul' onchange=\"consultar_valor_permitido_campo_cruzado('divcampocrossvp_$numero_campocross_actual','$numero_campocross_actual');\">
						<option value='none' selected>Seleccione Campo</option>
						$opciones_selector_campos_sin_elegir
						</select>
					<input type='hidden' id='copycampocross_$numero_campocross_actual' name='copycampocross_$numero_campocross_actual'>
				</div>
			</td>
			<td style='text-align:left;'>
			    <div id='divcampocrossvp_$numero_campocross_actual' >
			    	<h6  style='color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;''>Valor Permitido del Campo $numero_campocross_actual:</h6>
					<select id='campocrossvp_$numero_campocross_actual' name='campocrossvp_$numero_campocross_actual' class='campo_azul'>
						<option value='none' selected>Seleccione el valor permitido para el campo $numero_campocross_actual</option>
					</select>
			    </div>
			</td>
		</tr>

		<tr>
			<td colspan='2' style='text-align:left;'>
				<h6  id='sub_titulo_conteo_detallado_cross' style='color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;'>Seleccione si se mostrara el detallado del campo seleccionado,<br>(registros con el valor permitido seleccionado para dicho campo, datos de identificacion y valor del campo en cuesti&oacute;n )<br> o solo su conteo:</h6><br>
				<select id='selector_general_o_detallado_cross' name='selector_general_o_detallado_cross' class='campo_azul' >
					<option value='conteo' selected>Conteo Agrupado</option>
					<option value='detallado' >Detallado Por Registros</option>
				</select>
			</td>
		</tr>

		
		";
		

	$html_tabla_selectores.="</table>";

	$html_tabla_selectores.="<input type='button' id='boton_adicionar_campo' name='boton_adicionar_campo' class='btn btn-success color_boton' value='Cruzar Con campo Adicional (+)' onclick='adicionar_campo_para_cruce();'/>	";

	echo $html_tabla_selectores;
}//fin if

$coneccionBD->cerrar_conexion();
?>