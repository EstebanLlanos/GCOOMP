<?php
set_time_limit(936000);
ini_set('max_execution_time', 936000);
ini_set('memory_limit', '900M');

require '../librerias_externas/Smarty-3.1.17/libs/Smarty.class.php';
include_once ('../utiles/clase_coneccion_bd.php');
include_once ('../utiles/funciones_crear_menu.php');

//recorrido recursivo preorden padre,, hijo izquierdo, hijo derecho
function recorrer_arbol_y_adicionar_gestion(array &$arbol_menu, $opcion_menu)
{
	$cont=0;
	while($cont<count($arbol_menu))
	{
		if($arbol_menu[$cont][0]['id_principal']==$opcion_menu['id_padre'])
		{			
			$arbol_menu[$cont][1][]=array();
			$arbol_menu[$cont][1][count($arbol_menu[$cont][1])-1][]=$opcion_menu;
			$arbol_menu[$cont][1][count($arbol_menu[$cont][1])-1][]=array();
		}
		else
		{
			recorrer_arbol_y_adicionar_gestion($arbol_menu[$cont][1], $opcion_menu);
		}
		$cont++;
	}
}//fin funcion recorrer arbol y adicionar


function recorrer_arbol_y_dibujar_gestion(array $arbol_menu,$raiz,$perfil_id,&$coneccionBD)
{
	$html="";
	if($raiz==true)
	{
		//$html.="<tr class='pasar_mouse'>";
		//$html.="<ul id='menu'>";
	}
	$cont=0;
	while($cont<count($arbol_menu))
	{
		/*
		if($arbol_menu[$cont][0]['id_padre']=="")
		{
			$html.="<nav>";
			$html.="<ul>";
		}
		*/
		$menu_actual=$arbol_menu[$cont][0];

		if($menu_actual["tiene_submenus"]=="t")
		{
			$html.="<tr class='pasar_mouse_tiene_sub_menus'>";
		}
		else
		{
			$html.="<tr class='pasar_mouse'>";
		}

		$html.="<td style='text-align:left;'>";		
		$html.="--".$menu_actual["id_principal"]."--";	
		$html.="</td>";

		$html.="<td style='text-align:left;'>";		
		$html.=$menu_actual["nombre_opcion"];
		$html.="<input type='hidden' id='nombre_menu_".$menu_actual["id_principal"]."'
		name='nombre_menu_".$menu_actual["id_principal"]."'
		value='".$menu_actual["nombre_opcion"]."'>";	
		$html.="</td>";
		
		$html.="<td style='text-align:left;'>";	
		$html.="<table style='text-align:left;'><tr style='vertical-align:middle;'><td>";
		$html.="<div id='botones_prioridad'><table><tr><td><input type='button' onclick=\"cambiar_valor('valor_prio_".$menu_actual["id_principal"]."','0.01','suma')\" id='subir_prio_".$menu_actual["id_principal"]."' name='subir_prio_".$menu_actual["id_principal"]."' value='&#x25B2;'/></td></tr><tr><td><input type='button' onclick=\"cambiar_valor('valor_prio_".$menu_actual["id_principal"]."','0.01','resta')\" id='bajar_prio_".$menu_actual["id_principal"]."' name='bajar_prio_".$menu_actual["id_principal"]."' value='&#x25BC;'/></td></tr></table></div>";		
		$html.="</td><td>";	
		$html.="<b><div id='div_prio".$menu_actual["id_principal"]."'><input type='text' style='width:30px;' class='campo_azul' id='valor_prio_".$menu_actual["id_principal"]."' name='valor_prio_".$menu_actual["id_principal"]."' value='".$menu_actual["prioridad_jerarquica"]."' onkeypress='return isNumberKey(event)' /></div><div id='res_prio".$menu_actual["id_principal"]."'></div></b>";
		$html.="</td><td><input type='button' class='btn btn-success color_boton' id='cambiar_prioridad".$menu_actual["id_principal"]."' name='cambiar_prioridad".$menu_actual["id_principal"]."' value='OK' onclick=\"cambiar_prioridad_bd('".$menu_actual["id_principal"]."');\" ></td></tr></table>";	
		$prioridad_hijo=$menu_actual["prioridad_jerarquica"];
		$html.="</td>";
		
		$html.="<td style='text-align:left;'>";
		if($menu_actual["tiene_submenus"]=="t")
		{
			$html.="<b>Si</b> tiene sub-menus";
		}
		else
		{
			$html.="No tiene sub-menus";
		}
		$html.="</td>";
		
		$html.="<td style='text-align:left;'>";
		$id_padre=$menu_actual["id_padre"];
		
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
				$id_padre=$resultados_menu_padre[0]["id_principal"];
				$prioridad_padre=$resultados_menu_padre[0]["prioridad_jerarquica"];
				
				$string_para_prioridad_color="";
				if(floatval($prioridad_hijo)>floatval($prioridad_padre))
				{
					$string_para_prioridad_color.="<b style='color:green;'>$prioridad_padre</b> Id: <b> $id_padre </b> ";
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
		<div id='cuadro_color_res_".$menu_actual["id_principal"]."'
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
		WHERE id_perfil='$perfil_id' AND id_principal='".$menu_actual["id_principal"]."' ORDER BY prioridad_jerarquica
		";
		$resultados_menus_del_perfil=$coneccionBD->consultar2_no_crea_cierra($query_menus_del_perfil);
		if(is_array($resultados_menus_del_perfil) && count($resultados_menus_del_perfil)>0)
		{
			$html.="<input type='checkbox' id='checkbox_menu_id_".$menu_actual["id_principal"]."'
			name='checkbox_menu_id_".$menu_actual["id_principal"]."' checked='true' onchange='activar_para_perfil(this.value);' value='".$menu_actual["id_principal"]."'
			/>";
		}
		else
		{
			$html.="<input type='checkbox' id='checkbox_menu_id_".$menu_actual["id_principal"]."'
			name='checkbox_menu_id_".$menu_actual["id_principal"]."' onchange='activar_para_perfil(this.value);' value='".$menu_actual["id_principal"]."'
			/>";
		}
		$html.="</td>";

		$html.="</tr>";
		/*		
		$html.="<td >";
		$html.="<a ";
		if($menu_actual['ruta_interfaz']=="" || $menu_actual['ruta_interfaz']=="null")
		{
			$html.=" href=\"#\" ";
		}
		else
		{
			$html.=" href=\"".str_replace("|","/",$menu_actual['ruta_interfaz'])."\" ";
		}
		$html.=" >";
		$html.=$menu_actual['nombre_opcion'];
		$html.="</a>";
		$html.="</td>";
		*/
			
		if($menu_actual['tiene_submenus']=="t")
		{
			/*
			$html.="<tr class='pasar_mouse'>";
			$html.="<td colspan=100>";
			$html.="<div id='padre".$menu_actual["id_padre"]."'>";
			$html.="<table border='1'>";
			*/
			$html.=recorrer_arbol_y_dibujar_gestion($arbol_menu[$cont][1],false,$perfil_id,$coneccionBD);
			/*
			$html.="</tr>";
			$html.="</table>";
			$html.="</div>";
			$html.="</td>";
			$html.="</tr>";
			*/
		}

		/*
		if($arbol_menu[$cont][0]['id_padre']=="")
		{
			$html.="</ul>";
			$html.="</nav>";
		}
		*/
		$cont++;
	}//fin while recorre arbol y ramas
	if($raiz==true)
	{
		//$html.="</ul>";
		//$html.="</tr>";
		
	}
	
	return $html;
}

//devuelve el html del menu correspondiente
function crear_menu_gestion(array $resultados_query_menu,$perfil_id,&$coneccionBD)
{
	$menu_html="";
	$arbol_opciones_del_menu=array();	
	foreach($resultados_query_menu as $key=>$opcion_menu)
	{
		if($opcion_menu['id_padre']=="" || $opcion_menu['id_padre']=="null")
		{
			$arbol_opciones_del_menu[]=array();
			$arbol_opciones_del_menu[count($arbol_opciones_del_menu)-1][]=$opcion_menu;
			$arbol_opciones_del_menu[count($arbol_opciones_del_menu)-1][]=array();
		}
		else
		{
			recorrer_arbol_y_adicionar_gestion($arbol_opciones_del_menu, $opcion_menu);
		}//fin else
	}//fin foreach
	
	$menu_html = recorrer_arbol_y_dibujar_gestion($arbol_opciones_del_menu,true,$perfil_id,$coneccionBD);
	
	return $menu_html;
}

$coneccionBD = new conexion();
$coneccionBD->crearConexion();

if(isset($_REQUEST["perfil_id"]))
{
	$perfil_id=$_REQUEST["perfil_id"];
	
	//echo $perfil_id."<br>";

	$query_all_menus="";
	$query_all_menus.="SELECT * FROM gios_menus_opciones_sistema 
	ORDER BY prioridad_jerarquica asc
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

		//PARTE REFORMA GESTION INTERFACES ARBOL
		$html.=crear_menu_gestion($resultados_all_menus,$perfil_id,$coneccionBD);
		//FIN PARTE REFORMA GESTION INTERFACES ARBOL
		$html.="</table>";
	}//fin if
	
	echo $html;
}

$coneccionBD->cerrar_conexion();
?>