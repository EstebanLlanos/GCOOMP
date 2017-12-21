<?php

function contar_lineas_archivo($ruta)
{
	$lineas=0;
	//CONTADOR DE LINEAS EFICIENTE
		
	$handle = fopen($ruta, "r");
	while(!feof($handle)){
	  $line = fgets($handle);
	  $lineas++;
	}

	fclose($handle);
	//FIN CONTADOR DE LINEAS EFICIENTE
	
	return $lineas;
}

function remplazar_caracteres_especiales_por_equivalente_html($cadena)
{
	$linea_de_texto_nueva = str_replace("á","&aacute",$cadena);
	$linea_de_texto_nueva = str_replace("é","&eacute",$linea_de_texto_nueva);
	$linea_de_texto_nueva = str_replace("í","&iacute",$linea_de_texto_nueva);
	$linea_de_texto_nueva = str_replace("ó","&oacute",$linea_de_texto_nueva);
	$linea_de_texto_nueva = str_replace("ú","&uacute",$linea_de_texto_nueva);
	$linea_de_texto_nueva = str_replace("ñ","&ntilde",$linea_de_texto_nueva);
	$linea_de_texto_nueva = str_replace("\n","",$linea_de_texto_nueva);
	return $linea_de_texto_nueva;
}

if(isset($_GET["ruta"]))
{
	$ruta_archivo=str_replace("|","/",$_GET["ruta"]);
	$file = new SplFileObject($ruta_archivo);
	
	$inicio=intval($_GET["ini"]);
	$fin=intval($_GET["fin"]);
	$total_lineas=intval($_GET["nlineas"]);
	
	$modo=intval($_GET["modo"]);
	
	$divisor_destino=$_GET["divisor_destino"];
	
	if(file_exists($ruta_archivo))
	{		
		$contador=$inicio;
		$fin_linea=$fin;
		$cadena_linea="";
		if($total_lineas<$fin)
		{
			$fin_linea = $total_lineas;
		}
		
		$fila_html="";
		
		if($modo==0)
		{
			$html_columnas_duplicados="";
			$html_columnas_duplicados.="<table align='center' style='border: 1px solid black;'>";
			//titulos columnas
			$html_columnas_duplicados.="<tr>";
			$html_columnas_duplicados.="<td align='center' style='border: 1px solid black;font-family:Helvetica Neue;background-color:#D3D3D3;color:black;'>Numero Consecutivo</td>";
			$html_columnas_duplicados.="<td align='center' style='border: 1px solid black;font-family:Helvetica Neue;background-color:#D3D3D3;color:black;'>Tipo Validaci&oacuten</td>";
			$html_columnas_duplicados.="<td align='center' style='border: 1px solid black;font-family:Helvetica Neue;background-color:#D3D3D3;color:black;'>Concepto Validaci&oacuten</td>";
			$html_columnas_duplicados.="<td align='center' style='border: 1px solid black;font-family:Helvetica Neue;background-color:#D3D3D3;color:black;'>Detallado Inconsistencia</td>";
			$html_columnas_duplicados.="<td align='center' style='border: 1px solid black;font-family:Helvetica Neue;background-color:#D3D3D3;color:black;'>Numero de lineas donde se encontraron los datos duplicados</td>";
			$html_columnas_duplicados.="<td align='center' style='border: 1px solid black;font-family:Helvetica Neue;background-color:#D3D3D3;color:black;'>Identificaci&oacuten Duplicada</td>";
			$html_columnas_duplicados.="<td align='center' style='border: 1px solid black;font-family:Helvetica Neue;background-color:#D3D3D3;color:black;'>Numero de Campo(s)</td>";
			$html_columnas_duplicados.="</tr>";
			$fila_html.=$html_columnas_duplicados;
			//fin titulos columnas
		}
		
		if($modo==1)
		{
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					
			$error_campos_titulos="";
			$error_campos_titulos.="<table align='center' style='border: 1px solid black;'>";
			//titulos columnas
			$error_campos_titulos.="<tr>";
			$error_campos_titulos.="<td align='center' style='border: 1px solid black;font-family:Helvetica Neue;background-color:#D3D3D3;color:black;'>Numero Consecutivo</td>";
			$error_campos_titulos.="<td align='center' style='border: 1px solid black;font-family:Helvetica Neue;background-color:#D3D3D3;color:black;'>Nombre archivo 4505</td>";
			$error_campos_titulos.="<td align='center' style='border: 1px solid black;font-family:Helvetica Neue;background-color:#D3D3D3;color:black;'>Codigo tipo inconsistencia</td>";
			$error_campos_titulos.="<td align='center' style='border: 1px solid black;font-family:Helvetica Neue;background-color:#D3D3D3;color:black;'>Descripci&oacuten tipo inconsistencia</td>";
			$error_campos_titulos.="<td align='center' style='border: 1px solid black;font-family:Helvetica Neue;background-color:#D3D3D3;color:black;'>Codigo grupo inconsistencia</td>";
			$error_campos_titulos.="<td align='center' style='border: 1px solid black;font-family:Helvetica Neue;background-color:#D3D3D3;color:black;'>Descripci&oacuten tipo inconsistencia</td>";
			$error_campos_titulos.="<td align='center' style='border: 1px solid black;font-family:Helvetica Neue;background-color:#D3D3D3;color:black;'>Codigo detalle inconsistencia</td>";
			$error_campos_titulos.="<td align='center' style='border: 1px solid black;font-family:Helvetica Neue;background-color:#D3D3D3;color:black;'>Descripci&oacuten detalle inconsistencia</td>";
			$error_campos_titulos.="<td align='center' style='border: 1px solid black;font-family:Helvetica Neue;background-color:#D3D3D3;color:black;'>Numero linea</td>";
			$error_campos_titulos.="<td align='center' style='border: 1px solid black;font-family:Helvetica Neue;background-color:#D3D3D3;color:black;'>Numero de Campo</td>";
			$error_campos_titulos.="</tr>";
			//fin titulos columnas
			$fila_html.=$error_campos_titulos;
		}
		
		if($modo==2)
		{}
		
		while($contador< $fin_linea)
		{
			$file = new SplFileObject($ruta_archivo);
			$file->seek($contador);
			$cadena_linea=remplazar_caracteres_especiales_por_equivalente_html($file->current());
			
			if($cadena_linea!="" )
			{
				if($modo==0)
				{
					
					if(substr($cadena_linea,0,2)=="NO")
					{
						$fila_html.="<tr><td align='center' colspan='100' style='border: 1px solid black;font-family:Helvetica Neue;background-color:#D3D3D3;color:black;font-size: 12px;'>".$cadena_linea."</td></tr>";
					}
					else
					{
						$columnas_duplicados = explode(",",$cadena_linea);
						$fila_html.="<tr>";
						if(($contador % 2) ==0)
						{
							$cont_columnas_duplicados=0;
							while($cont_columnas_duplicados<count($columnas_duplicados))
							{
								if(($cont_columnas_duplicados%2)==0 )
								{
									$fila_html.="<td align='left' style='border: 1px solid black;font-family:Helvetica Neue;background-color:white;color:black;font-size: 12px;'  >".$columnas_duplicados[$cont_columnas_duplicados]."</td>";	
								}
								else 
								{
									$fila_html.="<td align='left' style='border: 1px solid black;font-family:Helvetica Neue;background-color:white;color:black;font-size: 12px;'  >".$columnas_duplicados[$cont_columnas_duplicados]."</td>";
								}
								$cont_columnas_duplicados++;							
							}						
						}
						else
						{
							$cont_columnas_duplicados=0;
							while($cont_columnas_duplicados<count($columnas_duplicados))
							{
								if(($cont_columnas_duplicados%2)==0)
								{
									$fila_html.="<td align='left' style='border: 1px solid black;font-family:Helvetica Neue;background-color:#D3D3D3;color:black;font-size: 12px;'>".$columnas_duplicados[$cont_columnas_duplicados]."</td>";									
								}
								else
								{
									$fila_html.="<td align='left' style='border: 1px solid black;font-family:Helvetica Neue;background-color:#D3D3D3;color:black;font-size: 12px;'>".$columnas_duplicados[$cont_columnas_duplicados]."</td>";
								}
								$cont_columnas_duplicados++;							
							}
						}
						$fila_html.="</tr>";
						
					
					}//fin else
				}//fin modo duplicados
				
				if($modo==1)
				{
					if(substr($cadena_linea,0,15)=="ERROR CABEZOTE;")
					{
						$fila_html.="<tr><td align='left' colspan='100' style='border: 1px solid black;font-family:Helvetica Neue;background-color:#D3D3D3;color:black;font-size: 12px;'>".$cadena_linea."</td></tr>";
						
					}
					else
					{
						$columnas = explode(",",$cadena_linea);
						$fila_html.="<tr>";
						if(($contador % 2) ==0)
						{						
							$cont_columnas=0;
							while($cont_columnas<count($columnas))
							{
								if(($cont_columnas%2)==0 )
								{
									$fila_html.="<td align='left' style='border: 1px solid black;font-family:Helvetica Neue;background-color:white;color:black;font-size: 12px;' onmouseover='cambiar_amarillo(this);' onmouseout='cambiar_blanco(this);'>".$columnas[$cont_columnas]."</td>";	
								}
								else 
								{
									$fila_html.="<td align='left' style='border: 1px solid black;font-family:Helvetica Neue;background-color:white;color:black;font-size: 12px;' onmouseover='cambiar_amarillo(this);' onmouseout='cambiar_blanco(this);'>".$columnas[$cont_columnas]."</td>";
								}
								$cont_columnas++;							
							}						
						}//fin if modulo == 0 lineas osea par
						else
						{
							$cont_columnas=0;
							while($cont_columnas<count($columnas))
							{
								if(($cont_columnas%2)==0)
								{
									$fila_html.="<td align='left' style='border: 1px solid black;font-family:Helvetica Neue;background-color:#D3D3D3;color:black;font-size: 12px;' onmouseover='cambiar_amarillo(this);' onmouseout='cambiar_gris(this);'>".$columnas[$cont_columnas]."</td>";									
								}
								else
								{
									$fila_html.="<td align='left' style='border: 1px solid black;font-family:Helvetica Neue;background-color:#D3D3D3;color:black;font-size: 12px;' onmouseover='cambiar_amarillo(this);' onmouseout='cambiar_gris(this);'>".$columnas[$cont_columnas]."</td>";
								}
								$cont_columnas++;							
							}
						}// fin lineas impares
						$fila_html.="</tr>";
					}//fin else
				}//fin modo error campos
				
				if($modo==2)
				{
					if($cadena_linea!="" && $contador>0)
					{
					
						$fila_html.="<p align='center' style='font-family:Helvetica Neue;font-size: 10px;'>".$cadena_linea."</p>";
					
					}
					else if($contador==0)
					{
						$fila_html.="<p align='center' style='color:black;font-family:Helvetica Neue;'>".$cadena_linea."</p>";
					}
				}//fin if modo 3
			}//fin if cadena no es vacia
			
			//AVANZA DE LINEA
			$contador++;
		}//FIN WHILE
		$fila_html.="</table>";
		
		$fila_html.="<table align='center'>";
		$fila_html.="<tr>";
		
		$fila_html.="<td align='center'>";
		$fila_html.="<input class='btn btn-success color_boton' type='button' value='<' onclick=\"retroceder_seccion_texto($inicio,$fin_linea,'$ruta_archivo',$total_lineas,'$divisor_destino',$modo);\" />";
		$fila_html.="</td>";
		
		$fila_html.="<td align='center'>";
		$fila_html.="<h4>Lineas desde la $inicio hasta la $fin_linea, de un total de $total_lineas </h4>";
		$fila_html.="</td>";
		
		$fila_html.="<td align='center'>";
		$fila_html.="<input class='btn btn-success color_boton' type='button' value='>'  onclick=\"avanzar_seccion_texto($inicio,$fin_linea,'$ruta_archivo',$total_lineas,'$divisor_destino',$modo);\" />";
		$fila_html.="</td>";
		
		$fila_html.="</tr>";
		$fila_html.="</table>";
		echo $fila_html;
	}//fin if archivo existe
	
}
?>