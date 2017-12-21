<?php
//recorrido recursivo preorden padre,, hijo izquierdo, hijo derecho
function recorrer_arbol_y_adicionar(array &$arbol_menu, $opcion_menu)
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
			recorrer_arbol_y_adicionar($arbol_menu[$cont][1], $opcion_menu);
		}
		$cont++;
	}
}//fin funcion recorrer arbol y adicionar


function recorrer_arbol_y_dibujar(array $arbol_menu,$raiz)
{
	$html="";
	if($raiz==true)
	{
		$html.="<nav>";
		$html.="<ul id='menu'>";
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
		$html.="<li>";
		$html.="<a ";
		if($arbol_menu[$cont][0]['ruta_interfaz']=="" || $arbol_menu[$cont][0]['ruta_interfaz']=="null")
		{
			$html.=" href=\"#\" ";
		}
		else
		{
			$html.=" href=\"".str_replace("|","/",$arbol_menu[$cont][0]['ruta_interfaz'])."\" ";
		}
		$html.=" >";
		$html.=$arbol_menu[$cont][0]['nombre_opcion'];
		$html.="</a>";		
		if($arbol_menu[$cont][0]['tiene_submenus']=="t")
		{
			$html.="<ul>";
			$html.=recorrer_arbol_y_dibujar($arbol_menu[$cont][1],false);
			$html.="</ul>";
		}
		$html.="</li>";
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
		$html.="</ul>";
		$html.="</nav>";
	}
	
	return $html;
}

//devuelve el html del menu correspondiente
function crear_menu(array $resultados_query_menu)
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
			recorrer_arbol_y_adicionar($arbol_opciones_del_menu, $opcion_menu);
		}//fin else
	}//fin foreach
	
	$menu_html = recorrer_arbol_y_dibujar($arbol_opciones_del_menu,true);
	
	return $menu_html;
}



?>