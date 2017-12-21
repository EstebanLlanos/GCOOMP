<?php
set_time_limit(936000);
ini_set('max_execution_time', 936000);
ini_set('memory_limit', '900M');

require '../librerias_externas/Smarty-3.1.17/libs/Smarty.class.php';
include_once ('../utiles/clase_coneccion_bd.php');
include_once ('../utiles/funciones_crear_menu.php');

$coneccionBD = new conexion();
$coneccionBD->crearConexion();

if(isset($_REQUEST["perfil_id"]))
{
	$perfil_id=$_REQUEST["perfil_id"];
	
	
	$query_all_menus="";
	$query_all_menus.="SELECT * FROM gios_menus_opciones_sistema 
	ORDER BY id_padre asc , prioridad_jerarquica asc, id_principal asc
	;
	";
	$resultados_all_menus=$coneccionBD->consultar2_no_crea_cierra($query_all_menus);
	/*
	$query_menus_del_perfil="";
	$query_menus_del_perfil.="SELECT * FROM gios_menus_opciones_sistema ms
	LEFT JOIN gios_menus_perfiles gmp ON (ms.id_principal=gmp.id_menu)
	WHERE id_perfil='$perfil_id' ORDER BY prioridad_jerarquica
	";
	$resultados_menus_del_perfil=$coneccionBD->consultar2_no_crea_cierra($query_menus_del_perfil);
	*/
	$html="";
	if(is_array($resultados_all_menus) && count($resultados_all_menus)>0)
	{
		$html.="<table border='1'>";
		
		//titulos
		$html.="<tr>
		<th style='text-align:center;'>ID Interfaz</th>
		<th style='text-align:center;'>Nombre Interfaz</th>
		<th style='text-align:center;'>Prioridad</th>
		<th style='text-align:center;'>Posee sub-menus</th>
		<th style='text-align:center;'>
		Menu Padre(Prioridad)
		
		<div id='cuadro_verde' style='height:20px;width:20px;background-color:green;margin:0px 0px;position:relative;top:-15px;left:70%;color:white;'>G</div>
		<div id='cuadro_naranja' style='height:20px;width:20px;background-color:orange;margin:0px 0px;position:relative;top:-35px;left:80%;color:white;'>R</div>
		<div id='cuadro_rojo' style='height:20px;width:20px;background-color:red;margin:0px 0px;position:relative;top:-55px;left:90%;color:white;'>B</div>
		</th>
		<th style='text-align:center;'>Activo en el perfil</th>
		</tr>
		";
		//fin titulos		
		
		foreach($resultados_all_menus as $menu)
		{
			$html.="<tr class='pasar_mouse'>";
			
			$html.="<td style='text-align:left;'>";		
			$html.=$menu["id_principal"];	
			$html.="</td>";

			$html.="<td style='text-align:left;'>";		
			$html.=$menu["nombre_opcion"];
			$html.="<input type='hidden' id='nombre_menu_".$menu["id_principal"]."'
			name='nombre_menu_".$menu["id_principal"]."'
			value='".$menu["nombre_opcion"]."'>";	
			$html.="</td>";
			
			$html.="<td style='text-align:left;'>";		
			$html.=$menu["prioridad_jerarquica"];
			$prioridad_hijo=$menu["prioridad_jerarquica"];
			$html.="</td>";
			
			$html.="<td style='text-align:left;'>";
			if($menu["tiene_submenus"]=="t")
			{
				$html.="<b>Si</b> tiene sub-menus";
			}
			else
			{
				$html.="No tiene sub-menus";
			}
			$html.="</td>";
			
			$html.="<td style='text-align:left;'>";
			$id_padre=$menu["id_padre"];
			
			$jerarquia_esta_bien="G";
			$color_jerarquia="green";
			
			while(ctype_digit($id_padre))
			{
				$query_menu_padre="";
				$query_menu_padre.="SELECT * FROM gios_menus_opciones_sistema 
				 WHERE id_principal='".$id_padre."'
				;
				";
				$resultados_menu_padre=$coneccionBD->consultar2_no_crea_cierra($query_menu_padre);
				
				
				
				if(is_array($resultados_menu_padre) && count($resultados_menu_padre)>0)
				{
					$nombre_menu_padre=$resultados_menu_padre[0]["nombre_opcion"];
					$prioridad_padre=$resultados_menu_padre[0]["prioridad_jerarquica"];
					
					$string_para_prioridad_color="";
					if(floatval($prioridad_hijo)>floatval($prioridad_padre))
					{
						$string_para_prioridad_color.="<b style='color:green;'>$prioridad_padre</b>";
						if($jerarquia_esta_bien!="R" && $jerarquia_esta_bien!="B")
						{
							$jerarquia_esta_bien="G";
							$color_jerarquia="green";
						}
					}
					else if(floatval($prioridad_hijo)==floatval($prioridad_padre))
					{
						$string_para_prioridad_color.="<b style='color:orange;'>$prioridad_padre</b>";
						if($jerarquia_esta_bien!="R" && $jerarquia_esta_bien!="B")
						{
							$jerarquia_esta_bien="R";
							$color_jerarquia="orange";
						}
					}
					else if(floatval($prioridad_hijo)<floatval($prioridad_padre))
					{
						$string_para_prioridad_color.="<b style='color:red;'>$prioridad_padre</b>";
						if($jerarquia_esta_bien!="R" && $jerarquia_esta_bien!="B")
						{
							$jerarquia_esta_bien="B";
							$color_jerarquia="red";
						}
					}
					
					
					
					
					$html.="<span class='nobr'>".$nombre_menu_padre."($string_para_prioridad_color)</span>";
					$id_padre=$resultados_menu_padre[0]["id_padre"];
					if(ctype_digit($id_padre))
					{
						$html.="<span class='nobr'>=></span>";
					}
				}
				else
				{
					break;
				}
			}//fin while
			
			$cuadro_color_res="
			<div id='cuadro_color_res_".$menu["id_principal"]."'
			style='height:20px;width:20px;
			background-color:$color_jerarquia;
			margin:0px 0px;position:relative;
			top:0px;left:95%;color:white;
			text-align:center;'>
			<b>$jerarquia_esta_bien</b>
			</div>
			";
			$html.=$cuadro_color_res;
			$html.="</td>";
			
			$html.="<td style='text-align:center;'>";
			$query_menus_del_perfil="";
			$query_menus_del_perfil.="SELECT * FROM gios_menus_opciones_sistema ms
			LEFT JOIN gios_menus_perfiles gmp ON (ms.id_principal=gmp.id_menu)
			WHERE id_perfil='$perfil_id' AND id_principal='".$menu["id_principal"]."' ORDER BY prioridad_jerarquica
			";
			$resultados_menus_del_perfil=$coneccionBD->consultar2_no_crea_cierra($query_menus_del_perfil);
			if(is_array($resultados_menus_del_perfil) && count($resultados_menus_del_perfil)>0)
			{
				$html.="<input type='checkbox' id='checkbox_menu_id_".$menu["id_principal"]."'
				name='checkbox_menu_id_".$menu["id_principal"]."' checked='true' onchange='activar_para_perfil(this.value);' value='".$menu["id_principal"]."'
				/>";
			}
			else
			{
				$html.="<input type='checkbox' id='checkbox_menu_id_".$menu["id_principal"]."'
				name='checkbox_menu_id_".$menu["id_principal"]."' onchange='activar_para_perfil(this.value);' value='".$menu["id_principal"]."'
				/>";
			}
			$html.="</td>";
			
			$html.="</tr>";
		}
		$html.="</table>";
	}
	
	echo $html;
}

$coneccionBD->cerrar_conexion();
?>