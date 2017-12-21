<?php
class criterios_reparacion_4505
{
	public function arreglo_fecha_corte_mensual_4505($fecha_corte_param)
	{
		$fecha_corte_actual=trim($fecha_corte_param);
		$fecha_corte_resultado=trim($fecha_corte_param);

		$array_fecha_corte_actual=explode("-", $fecha_corte_actual);
		if(count($array_fecha_corte_actual)==3)
		{
			$year_corte_actual=$array_fecha_corte_actual[0];
			$string_mes_dia_fecha_corte_actual=$array_fecha_corte_actual[1]."-".$array_fecha_corte_actual[2];
			if($string_mes_dia_fecha_corte_actual!="03-31"
				&& $string_mes_dia_fecha_corte_actual!="06-30"
				&& $string_mes_dia_fecha_corte_actual!="09-30"
				&& $string_mes_dia_fecha_corte_actual!="12-31"
				)
			{
				if($this->diferencia_dias_entre_fechas($fecha_corte_actual,$year_corte_actual."-03-31")>0 )
				{
					$fecha_corte_resultado=$year_corte_actual."-03-31";
				}
				else if($this->diferencia_dias_entre_fechas($fecha_corte_actual,$year_corte_actual."-03-31")<0
					&& $this->diferencia_dias_entre_fechas($fecha_corte_actual,$year_corte_actual."-06-30")>0 
				 )
				{
					$fecha_corte_resultado=$year_corte_actual."-06-30";
				}
				else if($this->diferencia_dias_entre_fechas($fecha_corte_actual,$year_corte_actual."-06-30")<0
					&& $this->diferencia_dias_entre_fechas($fecha_corte_actual,$year_corte_actual."-09-30")>0 
				 )
				{
					$fecha_corte_resultado=$year_corte_actual."-09-30";
				}
				else if($this->diferencia_dias_entre_fechas($fecha_corte_actual,$year_corte_actual."-09-30")<0
					&& $this->diferencia_dias_entre_fechas($fecha_corte_actual,$year_corte_actual."-12-31")>0 
				 )
				{
					$fecha_corte_resultado=$year_corte_actual."-12-31";
				}//fin else if
			}//fin if diferente fechas corte trimestres
		}//fin if

		$array_fecha_resultado =explode("-", $fecha_corte_resultado);
		if(count($array_fecha_resultado)==3)
		{
			return $fecha_corte_resultado;
		}//fin if
	}//fin function correccion fecha corte para archivos mensuales
	
	public function orden_para_correccion_campos_y_correccion(&$array_fields,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos)
	{
		$this->fecha_de_corte_periodo=$this->arreglo_fecha_corte_mensual_4505($this->fecha_de_corte_periodo);

		//FASE 0 Correcion de campo en blanco, formatos y valores permitidos para cada campo
	    //FASE 1 Correcion de los campos en relacion a el valor de los otros campos
	    $numero_campo_a_corregir=0;
	    $array_fields[9]=$this->correcion_campo($array_fields,9,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[10]=$this->correcion_campo($array_fields,10,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    
	    //gestacion
	    $array_fields[49]=$this->correcion_campo($array_fields,49,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[50]=$this->correcion_campo($array_fields,50,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[14]=$this->correcion_campo($array_fields,14,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[16]=$this->correcion_campo($array_fields,16,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[33]=$this->correcion_campo($array_fields,33,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[51]=$this->correcion_campo($array_fields,51,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[52]=$this->correcion_campo($array_fields,52,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[56]=$this->correcion_campo($array_fields,56,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[58]=$this->correcion_campo($array_fields,58,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);	    
	    $array_fields[57]=$this->correcion_campo($array_fields,57,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[59]=$this->correcion_campo($array_fields,59,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[60]=$this->correcion_campo($array_fields,60,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[61]=$this->correcion_campo($array_fields,61,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[79]=$this->correcion_campo($array_fields,79,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[78]=$this->correcion_campo($array_fields,78,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	        
	    //cancer cervix
	    $array_fields[87]=$this->correcion_campo($array_fields,87,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[88]=$this->correcion_campo($array_fields,88,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[86]=$this->correcion_campo($array_fields,86,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[89]=$this->correcion_campo($array_fields,89,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);		    
	    $array_fields[90]=$this->correcion_campo($array_fields,90,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[92]=$this->correcion_campo($array_fields,92,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[91]=$this->correcion_campo($array_fields,91,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[93]=$this->correcion_campo($array_fields,93,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[94]=$this->correcion_campo($array_fields,94,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[95]=$this->correcion_campo($array_fields,95,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[26]=$this->correcion_campo($array_fields,26,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);		    
	    
	    //cancer de seno
	    $array_fields[96]=$this->correcion_campo($array_fields,96,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[97]=$this->correcion_campo($array_fields,97,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[98]=$this->correcion_campo($array_fields,98,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[99]=$this->correcion_campo($array_fields,99,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[100]=$this->correcion_campo($array_fields,100,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[101]=$this->correcion_campo($array_fields,101,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[102]=$this->correcion_campo($array_fields,102,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[27]=$this->correcion_campo($array_fields,27,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    	    
	   	//ITS
	    $array_fields[82]=$this->correcion_campo($array_fields,82,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[83]=$this->correcion_campo($array_fields,83,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[75]=$this->correcion_campo($array_fields,75,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[76]=$this->correcion_campo($array_fields,76,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[80]=$this->correcion_campo($array_fields,80,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[81]=$this->correcion_campo($array_fields,81,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[15]=$this->correcion_campo($array_fields,15,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[115]=$this->correcion_campo($array_fields,115,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[116]=$this->correcion_campo($array_fields,116,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[24]=$this->correcion_campo($array_fields,24,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[74]=$this->correcion_campo($array_fields,74,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    	    
	   	//sintomatico respiratorio
	    $array_fields[113]=$this->correcion_campo($array_fields,113,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);	
	    $array_fields[112]=$this->correcion_campo($array_fields,112,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);	    
	    $array_fields[18]=$this->correcion_campo($array_fields,18,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);		    
	    $array_fields[19]=$this->correcion_campo($array_fields,19,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    
	    //obesidad y desnutricion
	    $array_fields[29]=$this->correcion_campo($array_fields,29,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[30]=$this->correcion_campo($array_fields,30,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[31]=$this->correcion_campo($array_fields,31,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[32]=$this->correcion_campo($array_fields,32,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[64]=$this->correcion_campo($array_fields,64,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[21]=$this->correcion_campo($array_fields,21,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[67]=$this->correcion_campo($array_fields,67,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    
	    //hipotiroidismo congenito
	    $array_fields[85]=$this->correcion_campo($array_fields,85,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[84]=$this->correcion_campo($array_fields,84,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[17]=$this->correcion_campo($array_fields,17,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[114]=$this->correcion_campo($array_fields,114,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    
	    //consulta crecimiento y desarrollo
	    $array_fields[69]=$this->correcion_campo($array_fields,69,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    
	    //victima violencia sexual
	    $array_fields[66]=$this->correcion_campo($array_fields,66,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[23]=$this->correcion_campo($array_fields,23,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    
	    //vicitma maltrato
	    $array_fields[65]=$this->correcion_campo($array_fields,65,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[22]=$this->correcion_campo($array_fields,22,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    
	    //consultas
	    $array_fields[34]=$this->correcion_campo($array_fields,34,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[62]=$this->correcion_campo($array_fields,62,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[63]=$this->correcion_campo($array_fields,63,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[68]=$this->correcion_campo($array_fields,68,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[72]=$this->correcion_campo($array_fields,72,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[73]=$this->correcion_campo($array_fields,73,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    
	    //examenes laboratorio
	    $array_fields[103]=$this->correcion_campo($array_fields,103,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[104]=$this->correcion_campo($array_fields,104,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[105]=$this->correcion_campo($array_fields,105,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[106]=$this->correcion_campo($array_fields,106,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);		    
	    $array_fields[107]=$this->correcion_campo($array_fields,107,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[109]=$this->correcion_campo($array_fields,109,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[108]=$this->correcion_campo($array_fields,108,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[110]=$this->correcion_campo($array_fields,110,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[111]=$this->correcion_campo($array_fields,111,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    
	    //planificacion familiar
	    $array_fields[53]=$this->correcion_campo($array_fields,53,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[54]=$this->correcion_campo($array_fields,54,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[55]=$this->correcion_campo($array_fields,55,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    
	    //lepra
	    $array_fields[117]=$this->correcion_campo($array_fields,117,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    $array_fields[20]=$this->correcion_campo($array_fields,20,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
	    	    
	    while($numero_campo_a_corregir<count($array_fields))
	    {
		if(
			 $numero_campo_a_corregir!=9
		   && $numero_campo_a_corregir!=10

		   && $numero_campo_a_corregir!=84
		   && $numero_campo_a_corregir!=85
		   && $numero_campo_a_corregir!=17
		   && $numero_campo_a_corregir!=114
		   && $numero_campo_a_corregir!=30
		   && $numero_campo_a_corregir!=29
		   && $numero_campo_a_corregir!=32
		   && $numero_campo_a_corregir!=31
		   && $numero_campo_a_corregir!=21
		   && $numero_campo_a_corregir!=64
		   && $numero_campo_a_corregir!=67
		   
		   && $numero_campo_a_corregir!=49
		   && $numero_campo_a_corregir!=50
		   && $numero_campo_a_corregir!=14
		   && $numero_campo_a_corregir!=16
		   && $numero_campo_a_corregir!=33
		   && $numero_campo_a_corregir!=51
		   && $numero_campo_a_corregir!=52
		   && $numero_campo_a_corregir!=56
		   && $numero_campo_a_corregir!=57
		   && $numero_campo_a_corregir!=58
		   && $numero_campo_a_corregir!=59
		   && $numero_campo_a_corregir!=60
		   && $numero_campo_a_corregir!=61
		   && $numero_campo_a_corregir!=69
		   && $numero_campo_a_corregir!=78
		   && $numero_campo_a_corregir!=79
		   && $numero_campo_a_corregir!=103
		   && $numero_campo_a_corregir!=104
		   
		   && $numero_campo_a_corregir!=112
		   && $numero_campo_a_corregir!=113
		   && $numero_campo_a_corregir!=18
		   && $numero_campo_a_corregir!=19
		   && $numero_campo_a_corregir!=80
		   && $numero_campo_a_corregir!=81
		   && $numero_campo_a_corregir!=82
		   && $numero_campo_a_corregir!=83
		   && $numero_campo_a_corregir!=24
		   && $numero_campo_a_corregir!=15
		   && $numero_campo_a_corregir!=74
		   && $numero_campo_a_corregir!=75
		   && $numero_campo_a_corregir!=76
		   && $numero_campo_a_corregir!=115
		   && $numero_campo_a_corregir!=116
		   && $numero_campo_a_corregir!=87
		   && $numero_campo_a_corregir!=88
		   && $numero_campo_a_corregir!=26
		   && $numero_campo_a_corregir!=86
		   && $numero_campo_a_corregir!=89
		   && $numero_campo_a_corregir!=90
		   && $numero_campo_a_corregir!=91
		   && $numero_campo_a_corregir!=92
		   && $numero_campo_a_corregir!=93
		   && $numero_campo_a_corregir!=94
		   && $numero_campo_a_corregir!=95
		   && $numero_campo_a_corregir!=104
		   && $numero_campo_a_corregir!=103
		   
		   && $numero_campo_a_corregir!=66
		   && $numero_campo_a_corregir!=23
		   
		   && $numero_campo_a_corregir!=65
		   && $numero_campo_a_corregir!=22
		   
		   && $numero_campo_a_corregir!=34			   
		   && $numero_campo_a_corregir!=62			   
		   && $numero_campo_a_corregir!=63
		   && $numero_campo_a_corregir!=68
		   && $numero_campo_a_corregir!=72
		   && $numero_campo_a_corregir!=73
		   
		   && $numero_campo_a_corregir!=104			   
		   && $numero_campo_a_corregir!=103			   
		   && $numero_campo_a_corregir!=105
		   && $numero_campo_a_corregir!=107
		   && $numero_campo_a_corregir!=106
		   && $numero_campo_a_corregir!=109
		   && $numero_campo_a_corregir!=108
		   && $numero_campo_a_corregir!=110
		   && $numero_campo_a_corregir!=111
		   
		   && $numero_campo_a_corregir!=53
		   && $numero_campo_a_corregir!=54
		   && $numero_campo_a_corregir!=55
		   
		   && $numero_campo_a_corregir!=27
		   && $numero_campo_a_corregir!=96
		   && $numero_campo_a_corregir!=97
		   && $numero_campo_a_corregir!=98
		   && $numero_campo_a_corregir!=99
		   && $numero_campo_a_corregir!=100
		   && $numero_campo_a_corregir!=101
		   && $numero_campo_a_corregir!=102


		   && $numero_campo_a_corregir!=117
		   && $numero_campo_a_corregir!=20
		   )
		{
		    $array_fields[$numero_campo_a_corregir]=$this->correcion_campo($array_fields,$numero_campo_a_corregir,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,$fase_correccion,$conexion_bd_validar_campos);
		}
	     $numero_campo_a_corregir++;
	    }//fin while
	    
	    
	    

	}//fin function orden_para_correccion_campos_y_correccion

	
	//CORRECCION DE LOS CAMPOS, LA FUNCION DEVUELVE EL CAMPO CORREGIDO
	public function correcion_campo(&$array_fields,$numero_campo_a_corregir,
				 $edad= -1,
				 $edad_dias =-1,
				 $edad_meses =-1,
				 $edad_semanas = -1,
				 $correccion_consecutivo=-1,
				 $fase_correccion=0,
				 &$conexion_bd_correccion
				 )//fin parametros funcion
	{
		require_once '../utiles/conf_personalizada.php';
		$NOMBRE_ENTIDAD_PERSONALIZADA=get_entidad_personalizada();

	    $campo_corregido="";
	    $campo_corregido = str_replace("á","a",$array_fields[$numero_campo_a_corregir]);
	    $campo_corregido = str_replace("é","e",$campo_corregido);
	    $campo_corregido = str_replace("í","i",$campo_corregido);
	    $campo_corregido = str_replace("ó","o",$campo_corregido);
	    $campo_corregido = str_replace("ú","u",$campo_corregido);
	    $campo_corregido = str_replace("ñ","n",$campo_corregido);
	    $campo_corregido = str_replace("Á","A",$campo_corregido);
	    $campo_corregido = str_replace("É","E",$campo_corregido);
	    $campo_corregido = str_replace("Í","I",$campo_corregido);
	    $campo_corregido = str_replace("Ó","O",$campo_corregido);
	    $campo_corregido = str_replace("Ú","U",$campo_corregido);
	    $campo_corregido = str_replace("Ñ","N",$campo_corregido);
	    $campo_corregido = str_replace("'"," ",$campo_corregido);
	    $campo_corregido=trim($this->alphanumericAndSpace4($campo_corregido));
	    
	    date_default_timezone_set ("America/Bogota");
	    
	    $fecha_corte=explode("-",$this->fecha_de_corte_periodo);
	    $date_de_corte=date($fecha_corte[0]."-".$fecha_corte[1]."-".$fecha_corte[2]);
	    $date_de_corte_posterior_year=date((intval($fecha_corte[0])+1)."-".$fecha_corte[1]."-".$fecha_corte[2]);	    
	    $date_de_corte_posterior_10_meses=date('Y-m-d',strtotime($fecha_corte[0]."-".$fecha_corte[1]."-".$fecha_corte[2] . ' +10 months'));
	    $date_de_corte_9_meses_menos=date('Y-m-d',strtotime($fecha_corte[0]."-".$fecha_corte[1]."-".$fecha_corte[2] . ' -9 months'));
	    $year_corte=$fecha_corte[0];
	    
	    //conexion base de datos
	    //$conexion_bd_correccion = new conexion();
	    
	    //CAMPOS ASOCIADOS AL RIESGO DE GESTACION
	    
	    //campo a corregir es 14
	    if($numero_campo_a_corregir==14)
	    {
	    	$c49_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[49]),"1900-12-31");
	    	$c56_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[56]),"1900-12-31");
	    	$c58_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[58]),"1900-12-31");
			if($fase_correccion==0)
			{
			    //campo en blanco
				if($campo_corregido=="")
				{

				    if($campo_corregido=="" && $array_fields[10]=="M")
				    {
						$campo_corregido="0";
				    }
				    else if($campo_corregido=="" && ($edad<10 || $edad>=60)
					    && $array_fields[10]=="F")
				    {
						$campo_corregido="0";
				    }
				    else if($campo_corregido=="" 
				    	&& ($c49_es_mayor_a_1900_12_31<0 || trim($array_fields[49])=="1800-01-01") 
				    	&& ($edad>=10 && $edad<60)
					    && $array_fields[10]=="F"
					    )
				    {
						$campo_corregido="2";
				    }
				    else if($campo_corregido=="" 
				    	&& $array_fields[10]=="F"			    	
				    	&& ($edad>=10 && $edad<60)
				    	&& ($array_fields[49]=="1845-01-01")
				    	&& ($c56_es_mayor_a_1900_12_31<0 || $c58_es_mayor_a_1900_12_31<0)
					    
					     )
				    {
						$campo_corregido="1";
				    }
				    else if($campo_corregido=="" 
				    	&& ($edad>=10 && $edad<60)
					    && $array_fields[10]=="F"
					    && ($array_fields[49]=="1845-01-01")
					    && ($array_fields[56]=="1845-01-01")
					    && ($array_fields[58]=="1845-01-01")
					    )
				    {
						$campo_corregido="21";
				    }
				    else if($campo_corregido=="")
				    {
						$campo_corregido="0";
				    }
				}//fin fi solo si campo esta en blanco
			    
			    
			    //valor permitido

			    if($campo_corregido!="0"
				 && $campo_corregido!="1"
				 && $campo_corregido!="2"
				 && $campo_corregido!="21")
			    {		    
			    
				    if($campo_corregido!="0"
					 && $campo_corregido!="1"
					 && $campo_corregido!="2"
					 && $campo_corregido!="21"
					  && $array_fields[10]=="M"
					  )
				    {
						$campo_corregido="0";
				    }
				    else if($campo_corregido!="0"
						 && $campo_corregido!="1"
						 && $campo_corregido!="2"
						 && $campo_corregido!="21"
						 && ($edad<10 || $edad>=60)
					    && $array_fields[10]=="F")
				    {
						$campo_corregido="0";
				    }
				    else if($campo_corregido!="0"
						 && $campo_corregido!="1"
						 && $campo_corregido!="2"
						 && $campo_corregido!="21"
				    	&& ($c49_es_mayor_a_1900_12_31<0 || trim($array_fields[49])=="1800-01-01") 
				    	&& ($edad>=10 && $edad<60)
					    && $array_fields[10]=="F"
					    )
				    {
						$campo_corregido="2";
				    }
				    else if($campo_corregido!="0"
						 && $campo_corregido!="1"
						 && $campo_corregido!="2"
						 && $campo_corregido!="21"
				    	&& $array_fields[10]=="F"			    	
				    	&& ($edad>=10 && $edad<60)
				    	&& ($array_fields[49]=="1845-01-01")
				    	&& ($c56_es_mayor_a_1900_12_31<0 || $c58_es_mayor_a_1900_12_31<0)
					    
					     )
				    {
						$campo_corregido="1";
				    }
				    else if($campo_corregido!="0"
						 && $campo_corregido!="1"
						 && $campo_corregido!="2"
						 && $campo_corregido!="21"
				    	&& ($edad>=10 && $edad<60)
					    && $array_fields[10]=="F"
					    && ($array_fields[49]=="1845-01-01")
					    && ($array_fields[56]=="1845-01-01")
					    && ($array_fields[58]=="1845-01-01")
					    )
				    {
						$campo_corregido="21";
				    }
				    else if($campo_corregido!="0"
					 && $campo_corregido!="1"
					 && $campo_corregido!="2"
					 && $campo_corregido!="21")
				    {
						$campo_corregido="0";
				    }
				}//fin if valor permitido
			    
			}//fin fase 0
			else if($fase_correccion==1)
			{
				$c56_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[56]),"1900-12-31");
				$c56_excede_fecha_corte=$this->diferencia_dias_entre_fechas(trim($array_fields[56]),$date_de_corte);

	    		$c58_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[58]),"1900-12-31");
	    		$c58_excede_fecha_corte=$this->diferencia_dias_entre_fechas(trim($array_fields[58]),$date_de_corte);

				//limite inferior fecha corte 1 years c56
				$campo56DiferenciaUnYearPorDebajoOigual=false;
				$fecha_corte_menos_1_years="";
				$comparacion_fecha_actual_vs_fecha_corte_menos_1_years=false;
				$array_fecha_corte=explode("-", $date_de_corte);
				if(count($array_fecha_corte)==3)
				{
					$fecha_corte_menos_1_years=(intval($array_fecha_corte[0])-1)."-".$array_fecha_corte[1]."-".$array_fecha_corte[2];
					$comparacion_fecha_actual_vs_fecha_corte_menos_1_years=$this->diferencia_dias_entre_fechas($array_fields[56],$fecha_corte_menos_1_years);
				}//fin if
				if($c56_es_mayor_a_1900_12_31<0 
				&& $c56_es_mayor_a_1900_12_31!==false
				&& $fecha_corte_menos_1_years!=""
				&& $comparacion_fecha_actual_vs_fecha_corte_menos_1_years!==false
				&& $comparacion_fecha_actual_vs_fecha_corte_menos_1_years>0
				)
				{
					$campo56DiferenciaUnYearPorDebajoOigual=true;
				}//fin if
				//fin limite inferior fecha corte 1 years

				//limite inferior fecha corte 1 years c58
				$campo58DiferenciaUnYearPorDebajoOigual=false;
				$fecha_corte_menos_1_years="";
				$comparacion_fecha_actual_vs_fecha_corte_menos_1_years=false;
				$array_fecha_corte=explode("-", $date_de_corte);
				if(count($array_fecha_corte)==3)
				{
					$fecha_corte_menos_1_years=(intval($array_fecha_corte[0])-1)."-".$array_fecha_corte[1]."-".$array_fecha_corte[2];
					$comparacion_fecha_actual_vs_fecha_corte_menos_1_years=$this->diferencia_dias_entre_fechas($array_fields[58],$fecha_corte_menos_1_years);
				}//fin if
				if($c58_es_mayor_a_1900_12_31<0 
				&& $c58_es_mayor_a_1900_12_31!==false
				&& $fecha_corte_menos_1_years!=""
				&& $comparacion_fecha_actual_vs_fecha_corte_menos_1_years!==false
				&& $comparacion_fecha_actual_vs_fecha_corte_menos_1_years>0
				)
				{
					$campo58DiferenciaUnYearPorDebajoOigual=true;
				}//fin if
				//fin limite inferior fecha corte 1 years

			    //calidad
				if($campo_corregido!="0" && $array_fields[10]=="M")
			    {
			    	//C1
					$campo_corregido="0";
			    }
			    else if($campo_corregido!="0" 
			    	&& ($edad<10 || $edad>=60)
				    && $array_fields[10]=="F")
			    {
			    	//c2
					$campo_corregido="0";
			    }
			    else if($campo_corregido!="2" 
			    	&& ($c49_es_mayor_a_1900_12_31<0 || trim($array_fields[49])=="1800-01-01") 
			    	&& ($edad>=10 && $edad<60)
				    && $array_fields[10]=="F"
				    )
			    {
			    	//c3
					$campo_corregido="2";
			    }
			    else if($campo_corregido!="1" 
			    	&& $array_fields[10]=="F"			    	
			    	&& ($edad>=10 && $edad<60)
			    	&& ($array_fields[49]=="1845-01-01")
			    	&& ($c56_es_mayor_a_1900_12_31<0 && $campo56DiferenciaUnYearPorDebajoOigual==false)
				     )
			    {
			    	//c4
					$campo_corregido="1";
			    }
			    else if($campo_corregido!="1" 
			    	&& $array_fields[10]=="F"			    	
			    	&& ($edad>=10 && $edad<60)
			    	&& ($array_fields[49]=="1845-01-01")
			    	&& $c56_es_mayor_a_1900_12_31>=0
			    	&& $c56_es_mayor_a_1900_12_31!==false
			    	&& ($c58_es_mayor_a_1900_12_31<0 && $campo58DiferenciaUnYearPorDebajoOigual==false)
				     )
			    {
			    	//c5
					$campo_corregido="1";
			    }
			    else if($campo_corregido=="1" 
			    	&& $array_fields[10]=="F"			    	
			    	&& ($edad>=10 && $edad<60)
			    	&& ($array_fields[49]=="1845-01-01")
			    	&& ($c56_es_mayor_a_1900_12_31<0 && $campo56DiferenciaUnYearPorDebajoOigual==true)
				     )
			    {
			    	//c6
					$campo_corregido="2";
			    }
			    else if($campo_corregido=="1" 
			    	&& $array_fields[10]=="F"			    	
			    	&& ($edad>=10 && $edad<60)
			    	&& ($array_fields[49]=="1845-01-01")
			    	&& $c56_es_mayor_a_1900_12_31>=0
			    	&& $c56_es_mayor_a_1900_12_31!==false
			    	&& ($c58_es_mayor_a_1900_12_31<0 && $campo58DiferenciaUnYearPorDebajoOigual==true)
				     )
			    {
			    	//c7
					$campo_corregido="2";
			    }
			    else if( ($campo_corregido!="21") 
			    	&& ($edad>=10 && $edad<60)
				    && $array_fields[10]=="F"
				    && ($array_fields[49]=="1845-01-01")
				    && ($array_fields[56]=="1845-01-01")
				    && ($array_fields[58]=="1845-01-01")
				    )
			    {
			    	//c6
					$campo_corregido="21";
			    }//fin calidad

			    
			   
			 
			 //echo "<script>alert('campo 14 despues $campo_corregido');</script>";
			}//fin fase 1
	    }//revisado campo 14
	    
	    //campo a corregir es 16
	    if($numero_campo_a_corregir==16)
	    {
	    	 $c49_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[49]),"1900-12-31");
			if($fase_correccion==0)
			{
			    //campo en blanco
			    if($campo_corregido=="")
			    {
				    if($campo_corregido=="" && $array_fields[10]=="M")
				    {
						$campo_corregido="0";
				    }
				    else if($campo_corregido=="" 
				    	&& ($edad<10 || $edad>=60)
					    && $array_fields[10]=="F"
					    )
				    {
						$campo_corregido="0";
				    }
				    else if($campo_corregido==""
				    	&& ($edad>=10 && $edad<60)
				    	&& $array_fields[10]=="F"
				     && ($c49_es_mayor_a_1900_12_31<0 || trim($array_fields[49])=="1800-01-01")  
				     )
				    {
						$campo_corregido="0";
				    }	
				    else if($campo_corregido=="" 
				    	&& ($edad>=10 && $edad<60)
					    && $array_fields[10]=="F" 
					    && $array_fields[49]=="1845-01-01"
					    && $array_fields[14]!="1"
					    )
				    {
						$campo_corregido="0";
				    }	    
				    else if($campo_corregido=="" 
				    	&& ($edad>=10 && $edad<60)
					    && $array_fields[10]=="F" 
					    && $array_fields[49]=="1845-01-01"
					    && $array_fields[14]=="1"
					    )
				    {
						$campo_corregido="21";
				    }
				    else if($campo_corregido=="")
				    {
						$campo_corregido="0";
				    }
				}//fin if esta en blanco
			    
			    //valor permitido

			    if($campo_corregido!="0"
			     && $campo_corregido!="1"
			     && $campo_corregido!="2"
			     && $campo_corregido!="21")
			    {
			    
				    if($campo_corregido!="0"
				     && $campo_corregido!="1"
				     && $campo_corregido!="2"
				     && $campo_corregido!="21"
				     && $array_fields[10]=="M")
				    {
						$campo_corregido="0";
				    }
				    else if($campo_corregido!="0"
					     && $campo_corregido!="1"
					     && $campo_corregido!="2"
					     && $campo_corregido!="21"
					      && ($edad<10 || $edad>=60)
					    && $array_fields[10]=="F"
					    )
				    {
						$campo_corregido="0";
				    }
				    else if($campo_corregido!="0"
					     && $campo_corregido!="1"
					     && $campo_corregido!="2"
					     && $campo_corregido!="21"
				    	&& ($edad>=10 && $edad<60)
				    	&& $array_fields[10]=="F"
				     && ($c49_es_mayor_a_1900_12_31<0 || trim($array_fields[49])=="1800-01-01")  
				     )
				    {
						$campo_corregido="0";
				    }	
				    else if($campo_corregido!="0"
					     && $campo_corregido!="1"
					     && $campo_corregido!="2"
					     && $campo_corregido!="21" 
				    	&& ($edad>=10 && $edad<60)
					    && $array_fields[10]=="F" 
					    && $array_fields[49]=="1845-01-01"
					    && $array_fields[14]!="1"
					    )
				    {
						$campo_corregido="0";
				    }	    
				    else if($campo_corregido!="0"
					     && $campo_corregido!="1"
					     && $campo_corregido!="2"
					     && $campo_corregido!="21" 
				    	&& ($edad>=10 && $edad<60)
					    && $array_fields[10]=="F" 
					    && $array_fields[49]=="1845-01-01"
					    && $array_fields[14]=="1"
					    )
				    {
						$campo_corregido="21";
				    }
				    else if($campo_corregido!="0"
				     && $campo_corregido!="1"
				     && $campo_corregido!="2"
				     && $campo_corregido!="21")
				    {
						$campo_corregido="0";
				    }
				}//fin if valor permitido
			    //$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_007($campo_corregido);
			}
			else if($fase_correccion==1)
			{
			    //calidad
			    if($campo_corregido!="0" 
			    	&& $array_fields[10]=="M"
			    	)
			    {
				$campo_corregido="0";
			    }
			    else if($campo_corregido!="0" 
			    	&& ($edad<10 || $edad>=60)
				    && $array_fields[10]=="F"
				    )
			    {
					$campo_corregido="0";
			    }
			    else if($campo_corregido!="0"
			    	&& ($edad>=10 && $edad<60)
			    	&& $array_fields[10]=="F"
			     && ($c49_es_mayor_a_1900_12_31<0 || trim($array_fields[49])=="1800-01-01")  
			     )
			    {
					$campo_corregido="0";
			    }	
			    else if($campo_corregido!="0" 
			    	&& ($edad>=10 && $edad<60)
				    && $array_fields[10]=="F" 
				    && $array_fields[49]=="1845-01-01"
				    && $array_fields[14]!="1"
				    )
			    {
					$campo_corregido="0";
			    }	    
			    else if($campo_corregido=="0" 
			    	&& ($edad>=10 && $edad<60)
				    && $array_fields[10]=="F" 
				    && $array_fields[49]=="1845-01-01"
				    && $array_fields[14]=="1"
				    )
			    {
					$campo_corregido="21";
			    }//fin calidad
			    
			}//fin fase 1
	    }//revisado c16
	    
	    //campo a corregir es 33
	    if($numero_campo_a_corregir==33)
	    {
	    	$c49_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[49]),"1900-12-31");
		     if($fase_correccion==0)
		     {
		     	if($campo_corregido=="")
				{
					//campo en blanco
					if($campo_corregido=="" && $array_fields[10]=="M")
					{
					    $campo_corregido="1845-01-01";
					}
					else if($campo_corregido=="" 
						&& ($edad<10 || $edad>=60)
						    && $array_fields[10]=="F")
					{
					    $campo_corregido="1845-01-01";
					}
					else if($campo_corregido=="" 
						&& ($edad>=10 && $edad<60)
						&& $array_fields[10]=="F" 
						&& ($array_fields[49]!="1845-01-01") 
						)
					{
					    $campo_corregido="1845-01-01";
					}
					else if($campo_corregido=="" 
						&& ($edad>=10 && $edad<60)
						&& $array_fields[10]=="F" 
						&& $array_fields[14]!="1"
						&& ($array_fields[49]=="1845-01-01") 
						)
					{
					    $campo_corregido="1845-01-01";
					}		
					else if($campo_corregido==""
						 && ($edad>=10 && $edad<60)
						&& $array_fields[10]=="F" 
						&& $array_fields[14]=="1"
						&& ($array_fields[49]=="1845-01-01") 
						)
					{
					    $campo_corregido="1800-01-01";
					}		
					else if($campo_corregido=="")
					{
					    $campo_corregido="1845-01-01";
					}

				}//fin if campo en blanco
				
				//formato
				$corrige_formato=false;
				$array_fecha_campo_actual=explode("-", $campo_corregido);
				if(count($array_fecha_campo_actual)==3)
			    {
					if(ctype_digit($array_fecha_campo_actual[0]) && ctype_digit($array_fecha_campo_actual[1]) && ctype_digit($array_fecha_campo_actual[2]))
					{
						//checkdate mm-dd-aaaa -> aaaa-mm-dd ?
						if(checkdate($array_fecha_campo_actual[1],$array_fecha_campo_actual[2],$array_fecha_campo_actual[0])
					       && intval($array_fecha_campo_actual[0])>=32)
					    {
					    	//no se corrige formato
					    }
					    else
					    {
					    	$corrige_formato=true;
					    }

					}
					else
					{
						$corrige_formato=true;
					}
				}
				else
				{
					$corrige_formato=true;
				}//fin else

				if($corrige_formato==true)
				{
					$campo_corregido=$this->corrector_formato_fecha($campo_corregido,false,33);
				}//fin if
				
				//valor permitido
				
				$excede_fecha_corte=$this->diferencia_dias_entre_fechas($campo_corregido,$date_de_corte);
				$es_menor_o_igual_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
				$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");

				//comparacion fecha campo actual es inferior a la fecha de nacimiento
				$fecha_nacimiento_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),"1900-12-31");
				$campo_actual_inferior_fecha_nacimiento=$this->diferencia_dias_entre_fechas($campo_corregido,trim($array_fields[9]));
				if($comparacion_a_1900_12_31<0 
				&& $comparacion_a_1900_12_31!==false
				&& $fecha_nacimiento_es_mayor_a_1900_12_31<0
				&& $fecha_nacimiento_es_mayor_a_1900_12_31!==false
				&& $campo_actual_inferior_fecha_nacimiento>0
				&& $campo_actual_inferior_fecha_nacimiento!==false
				)
				{
					$campo_corregido="1800-01-01";
				}//fin if
				//fin comparacion fecha campo actual es inferior a la fecha de nacimiento

				//limite inferior fecha corte 2 years
				$fecha_corte_menos_2_years="";
				$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=false;
				$array_fecha_corte=explode("-", $date_de_corte);
				if(count($array_fecha_corte)==3)
				{
					$fecha_corte_menos_2_years=(intval($array_fecha_corte[0])-2)."-".$array_fecha_corte[1]."-".$array_fecha_corte[2];
					$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=$this->diferencia_dias_entre_fechas($campo_corregido,$fecha_corte_menos_2_years);
				}//fin if
				if($comparacion_a_1900_12_31<0 
				&& $comparacion_a_1900_12_31!==false
				&& $fecha_corte_menos_2_years!=""
				&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years!==false
				&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years>0
				)
				{
					$campo_corregido="1845-01-01";
				}//fin if
				//fin limite inferior fecha corte 2 years
				
				//no cambia aqui solo calculo
				$corte_280="";
				$array_fcorte=explode("-",trim($date_de_corte));
				if(checkdate($array_fcorte[1],$array_fcorte[2],$array_fcorte[0]))
				{
				    $fecha = date_create(trim($date_de_corte));
				    //date sub resta, por lo cual al poner un dia negativo suma
				    date_sub($fecha, date_interval_create_from_date_string('-280 days'));
				    $corte_280="".date_format($fecha, 'Y-m-d');
				}
				
				$excede_fecha_corte_280=$this->diferencia_dias_entre_fechas($campo_corregido,$corte_280);
				
				//echo "<script>alert('corte280: $corte_280 , campo a corregir: $campo_corregido');</script>";
				//no cambia si excede fecha de corte
				
				if($comparacion_a_1900_12_31>=0
					&& $campo_corregido!="1800-01-01"
					&& $campo_corregido!="1845-01-01"
					)
				{
					//entra si no es un valor permitido
					 if($comparacion_a_1900_12_31>=0
						&& $campo_corregido!="1800-01-01"
						&& $campo_corregido!="1845-01-01"
						&& $array_fields[10]=="M")
					{
					    $campo_corregido="1845-01-01";
					}
					else if($comparacion_a_1900_12_31>=0
						&& $campo_corregido!="1800-01-01"
						&& $campo_corregido!="1845-01-01"
						&& ($edad<10 || $edad>=60)
						    && $array_fields[10]=="F"
						    )
					{
					    $campo_corregido="1845-01-01";
					}
					else if($comparacion_a_1900_12_31>=0
						&& $campo_corregido!="1800-01-01"
						&& $campo_corregido!="1845-01-01" 
						&& ($edad>=10 && $edad<60)
						&& $array_fields[10]=="F" 
						&& ($array_fields[49]!="1845-01-01") 
						)
					{
					    $campo_corregido="1845-01-01";
					}
					else if($comparacion_a_1900_12_31>=0
						&& $campo_corregido!="1800-01-01"
						&& $campo_corregido!="1845-01-01"
						&& ($edad>=10 && $edad<60)
						&& $array_fields[10]=="F" 
						&& $array_fields[14]!="1"
						&& ($array_fields[49]=="1845-01-01") 
						)
					{
					    $campo_corregido="1845-01-01";
					}		
					else if($comparacion_a_1900_12_31>=0
						&& $campo_corregido!="1800-01-01"
						&& $campo_corregido!="1845-01-01"
						 && ($edad>=10 && $edad<60)
						&& $array_fields[10]=="F" 
						&& $array_fields[14]=="1"
						&& ($array_fields[49]=="1845-01-01") 
						)
					{
					    $campo_corregido="1800-01-01";
					}	
					else if($comparacion_a_1900_12_31>=0
						&& $campo_corregido!="1800-01-01"
						&& $campo_corregido!="1845-01-01"
						)
					{
					    $campo_corregido="1845-01-01";
					}
				}//fin no es valor permitido
		     
		     }//fin fase 0
		     else if($fase_correccion==1)
		     {
				//echo "<script>alert('antes 33 fase 1 $campo_corregido');</script>";
				
				$es_fecha_33_valida=false;
				$verificacion_fecha_corte_pos_10_meses_33=-1;
				$verificacion_fecha_corte_33=-1;
				$array_fecha_correccion_33=explode("-",$campo_corregido);
				$diferencia_de_1900=-1;
				$verificacion_fecha_corte_33_9_meses_menos=-1;
				if(count($array_fecha_correccion_33)==3)
				{
				    //checkdate mm/dd/aaaa
				    if(checkdate($array_fecha_correccion_33[1],$array_fecha_correccion_33[2],$array_fecha_correccion_33[0]))
				    {
					$es_fecha_33_valida=true;
					//echo "<script>alert('".$campo_corregido." $date_de_corte_posterior_10_meses');</script>";
					$interval = date_diff(new DateTime($campo_corregido),new DateTime($date_de_corte_posterior_10_meses));
					$verificacion_fecha_corte_pos_10_meses_33 =(float)($interval->format("%r%a"));
					//->format("%r%a")
					//echo "<script>alert('$verificacion_fecha_corte_pos_year');</script>";
					$interval = date_diff(new DateTime($campo_corregido),new DateTime($date_de_corte));
					$verificacion_fecha_corte_33 =(float)($interval->format("%r%a"));
					$interval = date_diff(new DateTime($campo_corregido),new DateTime("1900-01-01"));
					$diferencia_de_1900 =(float)($interval->format("%r%a"));
					$interval = date_diff(new DateTime($campo_corregido),new DateTime($date_de_corte_9_meses_menos));
					$verificacion_fecha_corte_33_9_meses_menos=(float)($interval->format("%r%a"));
					
				    }
				}
				//calidad con fecha actividad
				$nueva_edad_year_actual=-1;
				$nueva_edad_meses_actual=-1;
				$nueva_edad_dias_actual=-1;
				    
				$array_fecha_nacimiento=explode("-",$array_fields[9]);
				$fecha_campo_actual=explode("-",$campo_corregido);
				if(count($fecha_campo_actual)==3 && checkdate($fecha_campo_actual[1],$fecha_campo_actual[2],$fecha_campo_actual[0]))
				{
				    //echo "<script>alert('entro');</script>";
				    
				    $array_calc_edad_actual=$this->edad_years_months_days($array_fecha_nacimiento[2]."-".$array_fecha_nacimiento[1]."-".$array_fecha_nacimiento[0],$fecha_campo_actual[2]."-".$fecha_campo_actual[1]."-".$fecha_campo_actual[0]);
				    $nueva_edad_year_actual=intval($array_calc_edad_actual['y']);
				    $nueva_edad_meses_actual=(intval($array_calc_edad_actual['y'])*12)+$array_calc_edad_actual['m'];
				    $nueva_edad_dias_actual=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),trim($array_fields[$numero_campo_a_corregir]));
				    
				    //echo "<script>alert(' EDAD DIAS $nueva_edad_dias_actual FN ".trim($array_fields[9])." FA ".trim($array_fields[$numero_campo_a_corregir])."  NCA $numero_campo_a_corregir');</script>";
				}
				$es_menor_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
				$es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");

				$fecha_c56_es_calendario=$this->diferencia_dias_entre_fechas(trim($array_fields[56]),"1900-12-31");
			    $fecha_c56_contra_campo_actual=$this->diferencia_dias_entre_fechas(trim($campo_corregido),trim($array_fields[56]));
				//calidad
				if($campo_corregido!="1845-01-01" && $array_fields[10]=="M")
				{
					//c1
				    $campo_corregido="1845-01-01";
				}
				else if($es_mayor_a_1900_12_31<0
					&& $array_fields[10]=="F"
					&& ($nueva_edad_year_actual<10 || $nueva_edad_year_actual>=60)
				)
				{
					//c2
				    $campo_corregido="1845-01-01";
				}
				else if($array_fields[10]=="F"
					&& ($edad<10 || $edad>=60)
				    && $campo_corregido=="1800-01-01"
				)
				{
					//c3
				    $campo_corregido="1845-01-01";
				}
				else if($es_mayor_a_1900_12_31<0
					&& ($nueva_edad_year_actual>=10 && $nueva_edad_year_actual<60)
					&& $array_fields[10]=="F" 
					&& ($array_fields[49]!="1845-01-01") 
					)
				{
					//c4
				    $campo_corregido="1845-01-01";
				}
				else if($campo_corregido=="1800-01-01"
					&& ($edad>=10 && $edad<60)
					&& $array_fields[10]=="F" 
					&& ($array_fields[49]!="1845-01-01") 
					)
				{
					//c5
				    $campo_corregido="1845-01-01";
				}
				else if($es_mayor_a_1900_12_31<0
					&& ($nueva_edad_year_actual>=10 && $nueva_edad_year_actual<60)
					&& $array_fields[10]=="F" 
					&& $array_fields[14]!="1"
					&& ($array_fields[49]=="1845-01-01") 
					)
				{
					//c6
				    $campo_corregido="1845-01-01";
				}	
				else if($campo_corregido=="1800-01-01" 
					&& ($edad>=10 && $edad<60)
					&& $array_fields[10]=="F" 
					&& $array_fields[14]!="1"
					&& ($array_fields[49]=="1845-01-01") 
					)
				{
					//c7
				    $campo_corregido="1845-01-01";
				}
				else if($campo_corregido=="1845-01-01" 
					 && ($edad>=10 && $edad<60)
					&& $array_fields[10]=="F" 
					&& $array_fields[14]=="1"
					&& ($array_fields[49]=="1845-01-01") 
					)
				{
					//c8
				    $campo_corregido="1800-01-01";
				}		
				else if($es_mayor_a_1900_12_31<0
					&& $fecha_c56_es_calendario<0
					&& ($fecha_c56_contra_campo_actual>0)
				)
				{
					//c9
				    $array_c56=explode("-",$array_fields[56]);
				    if(count($array_c56)==3 && checkdate($array_c56[1],$array_c56[2],$array_c56[0]) )
				    {
				    	$fecha = date_create(trim($array_fields[56]));
						//date sub resta, por lo cual al poner un dia negativo suma
					    date_sub($fecha, date_interval_create_from_date_string('-1 days'));
					    $c56_mas_un_dia="".date_format($fecha, 'Y-m-d');
					    $campo_corregido=$c56_mas_un_dia;

				    }//fin if verifica que sea una fecha
				}//fin calidad
				
				
				//echo "<script>alert('despues 33 fase 1 $campo_corregido');</script>";
		     }//fin fase 1
	    }//revisado c33 
	    
	    //campo a corregir es 49
	    if($numero_campo_a_corregir==49)
	    {
	     if($fase_correccion==0)
	     {
		//echo "<script>alert('antes 49 fase 0 $campo_corregido');</script>";
		//campo en blanco
		$c50_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[50]),"1900-12-31");

		if($campo_corregido=="")
		{
		
			if($campo_corregido==""
				&& $array_fields[10]=="M"
				)
			{
			    $campo_corregido="1845-01-01";
			}
			else if($campo_corregido==""
				&& $array_fields[10]=="F"
				&& ($edad<10 || $edad>=60)
				)
			{
			    $campo_corregido="1845-01-01";
			}
			else if($campo_corregido==""
				&& $array_fields[10]=="F"
				&& ($edad>=10 && $edad<60)
			   && $c50_es_mayor_a_1900_12_31<0
			   )
			{
			    

			    $array_c50=explode("-",$array_fields[50]);
			    if(count($array_c50)==3 && checkdate($array_c50[1],$array_c50[2],$array_c50[0]) )
			    {
			    	$fecha = date_create(trim($array_fields[50]));
					//date sub resta, por lo cual al poner un dia negativo suma
				    date_sub($fecha, date_interval_create_from_date_string('1 days'));
				    $c50_menos_un_dia="".date_format($fecha, 'Y-m-d');
				    $campo_corregido=$c50_menos_un_dia;

			    }//fin if verifica que sea una fecha

			    /*
			    $comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
			    $excede_fecha_corte=$this->diferencia_dias_entre_fechas($campo_corregido,$date_de_corte);
			    if($comparacion_a_1900_12_31<0 
			    	&& $comparacion_a_1900_12_31!==false
			    	&& $excede_fecha_corte<0
			    	&& $excede_fecha_corte!==false
			    	)
			    {
					$fecha = date_create(trim($date_de_corte));
					//date sub resta, por lo cual al poner un dia negativo suma
				    date_sub($fecha, date_interval_create_from_date_string('1 days'));
				    $date_corte_menos_un_dia="".date_format($fecha, 'Y-m-d');
				    $campo_corregido=$date_corte_menos_un_dia;
			    }//fin if
			    */
			}
			else if($campo_corregido=="")
			{
			    $campo_corregido="1845-01-01";
			}
		}//fin if entra campo en blanco si esta en blanco
		
		//formato
		$corrige_formato=false;
		$array_fecha_campo_actual=explode("-", $campo_corregido);
		if(count($array_fecha_campo_actual)==3)
	    {
			if(ctype_digit($array_fecha_campo_actual[0]) && ctype_digit($array_fecha_campo_actual[1]) && ctype_digit($array_fecha_campo_actual[2]))
			{
				//checkdate mm-dd-aaaa -> aaaa-mm-dd ?
				if(checkdate($array_fecha_campo_actual[1],$array_fecha_campo_actual[2],$array_fecha_campo_actual[0])
			       && intval($array_fecha_campo_actual[0])>=32)
			    {
			    	//no se corrige formato
			    }
			    else
			    {
			    	$corrige_formato=true;
			    }

			}
			else
			{
				$corrige_formato=true;
			}
		}
		else
		{
			$corrige_formato=true;
		}//fin else

		if($corrige_formato==true)
		{
			$campo_corregido=$this->corrector_formato_fecha($campo_corregido,false,-2);
		}
		
		//valor permitido
		//$date_de_corte
		$excede_fecha_corte=$this->diferencia_dias_entre_fechas($campo_corregido,$date_de_corte);
		$es_menor_o_igual_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
		
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");

		//comparacion fecha campo actual es inferior a la fecha de nacimiento
			$fecha_nacimiento_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),"1900-12-31");
			$campo_actual_inferior_fecha_nacimiento=$this->diferencia_dias_entre_fechas($campo_corregido,trim($array_fields[9]));
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_nacimiento_es_mayor_a_1900_12_31<0
			&& $fecha_nacimiento_es_mayor_a_1900_12_31!==false
			&& $campo_actual_inferior_fecha_nacimiento>0
			&& $campo_actual_inferior_fecha_nacimiento!==false
			)
			{
				$campo_corregido="1845-01-01";
			}//fin if
			//fin comparacion fecha campo actual es inferior a la fecha de nacimiento

			//limite inferior fecha corte 1 years
			$fecha_corte_menos_1_years="";
			$comparacion_fecha_actual_vs_fecha_corte_menos_1_years=false;
			$array_fecha_corte=explode("-", $date_de_corte);
			if(count($array_fecha_corte)==3)
			{
				$fecha_corte_menos_1_years=(intval($array_fecha_corte[0])-1)."-".$array_fecha_corte[1]."-".$array_fecha_corte[2];
				$comparacion_fecha_actual_vs_fecha_corte_menos_1_years=$this->diferencia_dias_entre_fechas($campo_corregido,$fecha_corte_menos_1_years);
			}//fin if
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_corte_menos_1_years!=""
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_1_years!==false
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_1_years>0
			)
			{
				$campo_corregido="1845-01-01";
			}//fin if
			//fin limite inferior fecha corte 1 years

		if($comparacion_a_1900_12_31<0 
			&& $excede_fecha_corte<0
			&& $comparacion_a_1900_12_31!==false
			&& $excede_fecha_corte!==false
			)
		{
			try
			{
				$fecha = date_create(trim($date_de_corte));
				//date sub resta, por lo cual al poner un dia negativo suma
			    date_sub($fecha, date_interval_create_from_date_string('1 days'));
			    $date_corte_menos_un_dia="".date_format($fecha, 'Y-m-d');
			    $campo_corregido=$date_corte_menos_un_dia;
			}//fin try
			catch(Exception $e)
			{
				echo $e->getMessage();
			}//fin catch
		}//fin comparacion excede la fecha de corte

		if($comparacion_a_1900_12_31>=0
			&& $campo_corregido!="1800-01-01"
			&& $campo_corregido!="1845-01-01"
			)
		{
			if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1845-01-01"
				&& $array_fields[10]=="M"
				)
			{
			    $campo_corregido="1845-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1845-01-01"
				&& $array_fields[10]=="F"
				&& ($edad<10 || $edad>=60)
				)
			{
			    $campo_corregido="1845-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1845-01-01"
				&& $array_fields[10]=="F"
				&& ($edad>=10 && $edad<60)
			   && $c50_es_mayor_a_1900_12_31<0
			   )
			{
			    

			    $array_c50=explode("-",$array_fields[50]);
			    if(count($array_c50)==3 && checkdate($array_c50[1],$array_c50[2],$array_c50[0]) )
			    {
			    	$fecha = date_create(trim($array_fields[50]));
					//date sub resta, por lo cual al poner un dia negativo suma
				    date_sub($fecha, date_interval_create_from_date_string('1 days'));
				    $c50_menos_un_dia="".date_format($fecha, 'Y-m-d');
				    $campo_corregido=$c50_menos_un_dia;

			    }//fin if verifica que sea una fecha

			    /*
			    $comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
			    $excede_fecha_corte=$this->diferencia_dias_entre_fechas($campo_corregido,$date_de_corte);
			    if($comparacion_a_1900_12_31<0 
			    	&& $comparacion_a_1900_12_31!==false
			    	&& $excede_fecha_corte<0
			    	&& $excede_fecha_corte!==false
			    	)
			    {
					$fecha = date_create(trim($date_de_corte));
					//date sub resta, por lo cual al poner un dia negativo suma
				    date_sub($fecha, date_interval_create_from_date_string('1 days'));
				    $date_corte_menos_un_dia="".date_format($fecha, 'Y-m-d');
				    $campo_corregido=$date_corte_menos_un_dia;
			    }//fin if
			    */
			    
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1845-01-01"
				)
			{
			    $campo_corregido="1845-01-01";
			}
		}//fin if si es diferente de los valores permitidos
		
		//echo "<script>alert('despues 49 fase 0 $campo_corregido');</script>";
		//$campo_corregido=$this->corrector_valor_permitido_fecha_criterio_003($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
		//echo "<script>alert('antes 49 fase 1 $campo_corregido');</script>";
		$c50_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[50]),"1900-12-31");
		$c50_es_menor_o_igual_1845_01_01=$this->diferencia_dias_entre_fechas(trim($array_fields[50]),"1845-01-01");
		$c50_es_mayor_a_1845_01_01=$this->diferencia_dias_entre_fechas(trim($array_fields[50]),"1800-01-01");
		
		$es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
		
		$c49_mayor_c50=$this->diferencia_dias_entre_fechas($campo_corregido,trim($array_fields[50]));
		
		$nueva_edad_year_actual=-1;
		$nueva_edad_meses_actual=-1;
		$nueva_edad_dias_actual=-1;
		
		$array_fecha_nacimiento=explode("-",$array_fields[9]);
		$fecha_campo_actual=explode("-",$campo_corregido);
		if(count($fecha_campo_actual)==3 && checkdate($fecha_campo_actual[1],$fecha_campo_actual[2],$fecha_campo_actual[0]))
		{
		    //echo "<script>alert('entro');</script>";
		    
		    $array_calc_edad_actual=$this->edad_years_months_days($array_fecha_nacimiento[2]."-".$array_fecha_nacimiento[1]."-".$array_fecha_nacimiento[0],$fecha_campo_actual[2]."-".$fecha_campo_actual[1]."-".$fecha_campo_actual[0]);
		    $nueva_edad_year_actual=intval($array_calc_edad_actual['y']);
		    $nueva_edad_meses_actual=(intval($array_calc_edad_actual['y'])*12)+$array_calc_edad_actual['m'];
		    $nueva_edad_dias_actual=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),trim($array_fields[$numero_campo_a_corregir]));
		    
		    //echo "<script>alert(' EDAD DIAS $nueva_edad_dias_actual FN ".trim($array_fields[9])." FA ".trim($array_fields[$numero_campo_a_corregir])."  NCA $numero_campo_a_corregir');</script>";
		}
		$es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");		
		
			//calidad c49
			
			
			
			if($campo_corregido!="1845-01-01"
				&& $array_fields[10]=="M"
				)
			{
				//c1
			    $campo_corregido="1845-01-01";
			}		
			else if($es_mayor_a_1900_12_31<0
				&& $array_fields[10]=="F"
				&& ($nueva_edad_year_actual<10 || $nueva_edad_year_actual>=60)
				)
			{
				//c2
			    $campo_corregido="1845-01-01";
			}		
			else if($campo_corregido=="1800-01-01"
				&& $array_fields[10]=="F"
				&& ($edad<10 || $edad>=60)
				)
			{
				//c3
			    $campo_corregido="1845-01-01";
			}
			
			else if($es_mayor_a_1900_12_31<0
				&& $array_fields[10]=="F"
			   	&& ($nueva_edad_year_actual>=10 && $nueva_edad_year_actual<60)
			   	&& $c50_es_mayor_a_1900_12_31<0
			   	&& $c49_mayor_c50<0
			   	)
			{
				

			    $array_c50=explode("-",$array_fields[50]);
			    if(checkdate($array_c50[1],$array_c50[2],$array_c50[0]))
			    {
			    	$campo_corregido=$array_fields[50];
			    }//fin if verifica que sea una fecha

			    /*
			    $comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
			    $excede_fecha_corte=$this->diferencia_dias_entre_fechas($campo_corregido,$date_de_corte);
			    if($comparacion_a_1900_12_31<0 
			    	&& $comparacion_a_1900_12_31!==false
			    	&& $excede_fecha_corte<0
			    	&& $excede_fecha_corte!==false
			    	)
			    {
					$fecha = date_create(trim($date_de_corte));
					//date sub resta, por lo cual al poner un dia negativo suma
				    date_sub($fecha, date_interval_create_from_date_string('1 days'));
				    $date_corte_menos_un_dia="".date_format($fecha, 'Y-m-d');
				    $campo_corregido=$date_corte_menos_un_dia;
			    }//fin if
			    */

			}
			else if($es_mayor_a_1900_12_31>=0
				&& $es_mayor_a_1900_12_31!==false
				&& $array_fields[10]=="F"
			   	&& ($edad>=10 && $edad<60)
			   	&& $c50_es_mayor_a_1900_12_31<0
			   	)
			{
				
			    $array_c50=explode("-",$array_fields[50]);
			    if(count($array_c50)==3 && checkdate($array_c50[1],$array_c50[2],$array_c50[0]) )
			    {
			    	$fecha = date_create(trim($array_fields[50]));
					//date sub resta, por lo cual al poner un dia negativo suma
				    date_sub($fecha, date_interval_create_from_date_string('1 days'));
				    $c50_menos_un_dia="".date_format($fecha, 'Y-m-d');
				    $campo_corregido=$c50_menos_un_dia;

			    }//fin if verifica que sea una fecha

			    /*
			    $comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
			    $excede_fecha_corte=$this->diferencia_dias_entre_fechas($campo_corregido,$date_de_corte);
			    if($comparacion_a_1900_12_31<0 
			    	&& $comparacion_a_1900_12_31!==false
			    	&& $excede_fecha_corte<0
			    	&& $excede_fecha_corte!==false
			    	)
			    {
					$fecha = date_create(trim($date_de_corte));
					//date sub resta, por lo cual al poner un dia negativo suma
				    date_sub($fecha, date_interval_create_from_date_string('1 days'));
				    $date_corte_menos_un_dia="".date_format($fecha, 'Y-m-d');
				    $campo_corregido=$date_corte_menos_un_dia;
			    }//fin if
			    */
			}//fin calidad		
			else if($campo_corregido=="1800-01-01"
				&& $array_fields[10]=="F"
				&& ($edad>=10 && $edad<60)
				)
			{
			    //nuevo 31 05 2016
			    $campo_corregido="1845-01-01";
			}//fin calidad
		
		//echo "<script>alert('despues 49 fase 1 $campo_corregido');</script>";
		
	     }//fin fase 1		
	    }//revisado c49
	    
	    //campo a corregir es 50
	    if($numero_campo_a_corregir==50)
	    {
			if($fase_correccion==0)
			{
			    //campo en blanco
			    $fecha_calendario_c49=$this->diferencia_dias_entre_fechas($array_fields[49],"1900-12-31");
			    
			    if($campo_corregido=="")
			    {
				    if($campo_corregido==""
					&& $array_fields[10]=="M"
					)
					{
					    $campo_corregido="1845-01-01";
					}
					else if($campo_corregido==""
						&& $array_fields[10]=="F"
						&& ($edad<10 || $edad>=60)
						)
					{
					    $campo_corregido="1845-01-01";
					}
				    else if($campo_corregido==""
				    	&& $array_fields[10]=="F"
				   		&& ($edad>=10 && $edad<60)
				   		&& $fecha_calendario_c49<0
				   	)
					{			    
						/*
					    $fecha = date_create(trim($array_fields[49]));
					    //date sub resta, por lo cual al poner un dia negativo suma
					    date_sub($fecha, date_interval_create_from_date_string('-1 days'));
					    $date_49="".date_format($fecha, 'Y-m-d');
					    $campo_corregido=$date_49;
					    */

					    $array_c49=explode("-",$array_fields[49]);
					    if(checkdate($array_c49[1],$array_c49[2],$array_c49[0]) )
					    {
					    	$campo_corregido=$array_fields[49];
					    }//fin if verifica que sea una fecha
					    
					    /*
					    $comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
					    $excede_fecha_corte=$this->diferencia_dias_entre_fechas($campo_corregido,$date_de_corte);
					    if($comparacion_a_1900_12_31<0 && $excede_fecha_corte<0)
					    {
							$campo_corregido=$date_de_corte;
					    }
					    */
					}
				    else if($campo_corregido=="")
				    {
					//criterio 4
						$campo_corregido="1845-01-01";
				    }
				}//fin if esta en blanco
			    
			    //formato
			    $corrige_formato=false;
				$array_fecha_campo_actual=explode("-", $campo_corregido);
				if(count($array_fecha_campo_actual)==3)
				{
					if(ctype_digit($array_fecha_campo_actual[0]) && ctype_digit($array_fecha_campo_actual[1]) && ctype_digit($array_fecha_campo_actual[2]))
					{
						//checkdate mm-dd-aaaa -> aaaa-mm-dd ?
						if(checkdate($array_fecha_campo_actual[1],$array_fecha_campo_actual[2],$array_fecha_campo_actual[0])
					       && intval($array_fecha_campo_actual[0])>=32)
					    {
					    	//no se corrige formato
					    }
					    else
					    {
					    	$corrige_formato=true;
					    }

					}
					else
					{
						$corrige_formato=true;
					}
				}
				else
				{
					$corrige_formato=true;
				}//fin else

				if($corrige_formato==true)
				{
			    	$campo_corregido=$this->corrector_formato_fecha($campo_corregido,false,-2);
				}
			    
			    //valor permitido
			    
			    $excede_fecha_corte=$this->diferencia_dias_entre_fechas($campo_corregido,$date_de_corte);
			    $es_menor_o_igual_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
			    $comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
			    
			    $comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");

			    //comparacion fecha campo actual es inferior a la fecha de nacimiento
			$fecha_nacimiento_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),"1900-12-31");
			$campo_actual_inferior_fecha_nacimiento=$this->diferencia_dias_entre_fechas($campo_corregido,trim($array_fields[9]));
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_nacimiento_es_mayor_a_1900_12_31<0
			&& $fecha_nacimiento_es_mayor_a_1900_12_31!==false
			&& $campo_actual_inferior_fecha_nacimiento>0
			&& $campo_actual_inferior_fecha_nacimiento!==false
			)
			{
				$campo_corregido="1845-01-01";
			}//fin if
			//fin comparacion fecha campo actual es inferior a la fecha de nacimiento

			//limite inferior fecha corte 1 years
			$fecha_corte_menos_1_years="";
			$comparacion_fecha_actual_vs_fecha_corte_menos_1_years=false;
			$array_fecha_corte=explode("-", $date_de_corte);
			if(count($array_fecha_corte)==3)
			{
				$fecha_corte_menos_1_years=(intval($array_fecha_corte[0])-1)."-".$array_fecha_corte[1]."-".$array_fecha_corte[2];
				$comparacion_fecha_actual_vs_fecha_corte_menos_1_years=$this->diferencia_dias_entre_fechas($campo_corregido,$fecha_corte_menos_1_years);
			}//fin if
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_corte_menos_1_years!=""
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_1_years!==false
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_1_years>0
			)
			{
				$campo_corregido="1845-01-01";
			}//fin if
			//fin limite inferior fecha corte 1 years

			    if($comparacion_a_1900_12_31<0 
					&& $excede_fecha_corte<0
					&& $comparacion_a_1900_12_31!==false
					&& $excede_fecha_corte!==false
					)
				{
					try
					{
						$fecha = date_create(trim($date_de_corte));
						//date sub resta, por lo cual al poner un dia negativo suma
					    date_sub($fecha, date_interval_create_from_date_string('1 days'));
					    $date_corte_menos_un_dia="".date_format($fecha, 'Y-m-d');
					    $campo_corregido=$date_corte_menos_un_dia;
					}//fin try
					catch(Exception $e)
					{
						echo $e->getMessage();
					}//fin catch
				}//fin comparacion excede la fecha de corte
			    
			    if($comparacion_a_1900_12_31>=0
				    && $campo_corregido!="1800-01-01"
				    && $campo_corregido!="1845-01-01")
			    {
				    if($comparacion_a_1900_12_31>=0
					    && $campo_corregido!="1800-01-01"
					    && $campo_corregido!="1845-01-01"
						&& $array_fields[10]=="M"
					)
					{
					    $campo_corregido="1845-01-01";
					}
					else if($comparacion_a_1900_12_31>=0
					    && $campo_corregido!="1800-01-01"
					    && $campo_corregido!="1845-01-01"
						&& $array_fields[10]=="F"
						&& ($edad<10 || $edad>=60)
						)
					{
					    $campo_corregido="1845-01-01";
					}
				    else if($comparacion_a_1900_12_31>=0
					    && $campo_corregido!="1800-01-01"
					    && $campo_corregido!="1845-01-01"
				    	&& $array_fields[10]=="F"
				   		&& ($edad>=10 && $edad<60)
				   		&& $fecha_calendario_c49<0
				   	)
					{			    

					    /*
					    $fecha = date_create(trim($array_fields[49]));
					    //date sub resta, por lo cual al poner un dia negativo suma
					    date_sub($fecha, date_interval_create_from_date_string('-1 days'));
					    $date_49="".date_format($fecha, 'Y-m-d');
					    $campo_corregido=$date_49;
					    */

					    $array_c49=explode("-",$array_fields[49]);
					    if(checkdate($array_c49[1],$array_c49[2],$array_c49[0]) )
					    {
					    	$campo_corregido=$array_fields[49];
					    }//fin if verifica que sea una fecha
					    
					    /*
					    $comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
					    if($comparacion_a_1900_12_31<0 && $excede_fecha_corte<0)
					    {
							$campo_corregido=$date_de_corte;
					    }
					    */
					}
				    else if($comparacion_a_1900_12_31>=0
					    && $campo_corregido!="1800-01-01"
					    && $campo_corregido!="1845-01-01")
				    {
						//criterio 4
						$campo_corregido="1845-01-01";
				    }
			    }//fin else if
			    
			
			    //$campo_corregido=$this->corrector_valor_permitido_fecha_criterio_003($campo_corregido);
			}
			else if($fase_correccion==1)
			{
			    
			    $comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
			    
			    $es_fecha_49_valida=false;
			    $array_fecha_correccion_49=explode("-",$array_fields[49]);
			    $verificacion_menor_que_49=-1;
			    if(count($array_fecha_correccion_49)==3)
			    {
					//checkdate mm/dd/aaaa
					if(checkdate($array_fecha_correccion_49[1],$array_fecha_correccion_49[2],$array_fecha_correccion_49[0]))
					{
					    $es_fecha_49_valida=true;
					    $interval = date_diff(new DateTime($campo_corregido),new DateTime($array_fields[49]));
					    $verificacion_menor_que_49=(float)($interval->format("%r%a"));
					}
			    }
			    
			    //echo "<script>alert(' campo  50 $campo_corregido  campo 49 ".$array_fields[49]." campo 50 pre ".$array_fields[50]."');</script>";
			    
			    
			    $es_fecha_es_valida=false;
			    $verificacion_fecha_corte_pos_year=-1;
			    $verificacion_fecha_corte=-1;
			    $verificacion_fecha_corte_50_9_meses_menos=-1;
			    $array_fecha_correccion_actual=explode("-",$campo_corregido);
			    $diferencia_de_1900=-1;
			    if(count($array_fecha_correccion_actual)==3)
			    {
				//checkdate mm/dd/aaaa
				if(checkdate($array_fecha_correccion_actual[1],$array_fecha_correccion_actual[2],$array_fecha_correccion_actual[0]))
				{
				    $es_fecha_es_valida=true;
				    $interval = date_diff(new DateTime($campo_corregido),new DateTime($date_de_corte_posterior_year));
				    $verificacion_fecha_corte_pos_year =(float)($interval->format("%r%a"));
				    $interval = date_diff(new DateTime($campo_corregido),new DateTime($date_de_corte));
				    $verificacion_fecha_corte =(float)($interval->format("%r%a"));
				    $interval = date_diff(new DateTime($campo_corregido),new DateTime($date_de_corte_9_meses_menos));
				    $verificacion_fecha_corte_50_9_meses_menos=(float)($interval->format("%r%a"));
				    $interval = date_diff(new DateTime($campo_corregido),new DateTime("1900-01-01"));
				    $diferencia_de_1900 =(float)($interval->format("%r%a"));
				    //echo "<script>alert('$verificacion_fecha_corte');</script>";
				}
			    }
			    
			    $nueva_edad_year_actual=-1;
			    $nueva_edad_meses_actual=-1;
			    $nueva_edad_dias_actual=-1;
			    
			    $array_fecha_nacimiento=explode("-",$array_fields[9]);
			    $fecha_campo_actual=explode("-",$campo_corregido);
			    if(count($fecha_campo_actual)==3 && checkdate($fecha_campo_actual[1],$fecha_campo_actual[2],$fecha_campo_actual[0]))
			    {
				//echo "<script>alert('entro');</script>";
				
				$array_calc_edad_actual=$this->edad_years_months_days($array_fecha_nacimiento[2]."-".$array_fecha_nacimiento[1]."-".$array_fecha_nacimiento[0],$fecha_campo_actual[2]."-".$fecha_campo_actual[1]."-".$fecha_campo_actual[0]);
				$nueva_edad_year_actual=intval($array_calc_edad_actual['y']);
				$nueva_edad_meses_actual=(intval($array_calc_edad_actual['y'])*12)+$array_calc_edad_actual['m'];
				$nueva_edad_dias_actual=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),trim($array_fields[$numero_campo_a_corregir]));
				
				//echo "<script>alert(' EDAD DIAS $nueva_edad_dias_actual FN ".trim($array_fields[9])." FA ".trim($array_fields[$numero_campo_a_corregir])."  NCA $numero_campo_a_corregir');</script>";
			    }
			    $es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
			    
			    $c49_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas($array_fields[49],"1900-12-31");
			    

			    
			    //calidad c50

			    if($array_fields[10]=="M"		   
				&& $campo_corregido!="1845-01-01"
			    )
			    {
					//criterio 1 
					$campo_corregido="1845-01-01";
			    }
			    else if($campo_corregido=="1800-01-01"
				&& $array_fields[10]=="F"
				&& ($edad<10 || $edad>=60)
				)
				{
				    $campo_corregido="1845-01-01";
				}
				else if($es_mayor_a_1900_12_31<0
					&& $array_fields[10]=="F"
					&& ($nueva_edad_year_actual<10 || $nueva_edad_year_actual>=60)
					)
				{
				    $campo_corregido="1845-01-01";
				}						
				else if($es_mayor_a_1900_12_31<0
					&& $array_fields[10]=="F"
					&& ($nueva_edad_year_actual>=10 && $nueva_edad_year_actual<60)
				    && $c49_es_mayor_a_1900_12_31<0	
				    && $verificacion_menor_que_49>0
				    && $comparacion_a_1900_12_31<0)
			    {
					/*
				    $fecha = date_create(trim($array_fields[49]));
				    //date sub resta, por lo cual al poner un dia negativo suma
				    date_sub($fecha, date_interval_create_from_date_string('-1 days'));
				    $date_49="".date_format($fecha, 'Y-m-d');
				    $campo_corregido=$date_49;
				    */

				    $array_c49=explode("-",$array_fields[49]);
				    if(checkdate($array_c49[1],$array_c49[2],$array_c49[0]) )
				    {
				    	$campo_corregido=$array_fields[49];
				    }//fin if verifica que sea una fecha
				    
				    /*
				    $comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
				    $excede_fecha_corte=$this->diferencia_dias_entre_fechas($campo_corregido,$date_de_corte);
				    if($comparacion_a_1900_12_31<0 && $excede_fecha_corte<0)
				    {
						$campo_corregido=$date_de_corte;
				    }
				    */
			    }
			    else if($es_mayor_a_1900_12_31>=0
			    	&& $es_mayor_a_1900_12_31!==false
					&& $array_fields[10]=="F"
					&& ($edad>=10 && $edad<60)
				    && $c49_es_mayor_a_1900_12_31<0
				    )
			    {
					/*
				    $fecha = date_create(trim($array_fields[49]));
				    //date sub resta, por lo cual al poner un dia negativo suma
				    date_sub($fecha, date_interval_create_from_date_string('-1 days'));
				    $date_49="".date_format($fecha, 'Y-m-d');
				    $campo_corregido=$date_49;
				    */
				    
				    $array_c49=explode("-",$array_fields[49]);
				    if(checkdate($array_c49[1],$array_c49[2],$array_c49[0]) )
				    {
				    	$campo_corregido=$array_fields[49];
				    }//fin if verifica que sea una fecha

				    /*
				    $comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
				    if($comparacion_a_1900_12_31<0 && $excede_fecha_corte<0)
				    {
						$campo_corregido=$date_de_corte;
				    }
				    */
			    }
			    else if($campo_corregido=="1800-01-01"
					&& $array_fields[10]=="F"
					&& ($edad>=10 && $edad<60)
					)
				{
				    //nuevo 31 05 2016
				    $campo_corregido="1845-01-01";
				}//fin calidad
				
			}//fin fase 1 
	    }//revisado c50
	    
	    //campo a corregir es 51
	    if($numero_campo_a_corregir==51)
	    {
	     if($fase_correccion==0)
	     {
			//campo en blanco
			if($campo_corregido=="")
			{		
				if($campo_corregido=="" && $array_fields[10]=="M")
				{
				    $campo_corregido="1845-01-01";
				}
				else if($campo_corregido=="" 
					&& ($edad<10 || $edad>=60)
					    && $array_fields[10]=="F"
					    )
				{
				    $campo_corregido="1845-01-01";
				}
				else if($campo_corregido=="" 
					&& ($edad>=10 && $edad<60)
					&& $array_fields[10]=="F" 
					)
				{
				    $campo_corregido="1835-01-01";
				}
				else if($campo_corregido=="")
				{
				    $campo_corregido="1845-01-01";
				}
			}//fin if
		
			//formato
			$corrige_formato=false;
			$array_fecha_campo_actual=explode("-", $campo_corregido);
			if(count($array_fecha_campo_actual)==3)
			{
				if(ctype_digit($array_fecha_campo_actual[0]) && ctype_digit($array_fecha_campo_actual[1]) && ctype_digit($array_fecha_campo_actual[2]))
				{
					//checkdate mm-dd-aaaa -> aaaa-mm-dd ?
					if(checkdate($array_fecha_campo_actual[1],$array_fecha_campo_actual[2],$array_fecha_campo_actual[0])
				       && intval($array_fecha_campo_actual[0])>=32)
				    {
				    	//no se corrige formato
				    }
				    else
				    {
				    	$corrige_formato=true;
				    }

				}
				else
				{
					$corrige_formato=true;
				}
			}
			else
			{
				$corrige_formato=true;
			}//fin else

			if($corrige_formato==true)
			{
				$campo_corregido=$this->corrector_formato_fecha($campo_corregido,false,-2);
			}
			
			//valor permitido
			
			$excede_fecha_corte=$this->diferencia_dias_entre_fechas($campo_corregido,$date_de_corte);
			$es_menor_o_igual_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
			$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
			
			//esta no aplica aca ni en el 33
			$corte_280="";
			$array_fcorte=explode("-",trim($date_de_corte));
			if(checkdate($array_fcorte[1],$array_fcorte[2],$array_fcorte[0]))
			{
			    $fecha = date_create(trim($date_de_corte));
			    //date sub resta, por lo cual al poner un dia negativo suma
			    date_sub($fecha, date_interval_create_from_date_string('-280 days'));
			    $corte_280="".date_format($fecha, 'Y-m-d');
			}
			
			$excede_fecha_corte_280=$this->diferencia_dias_entre_fechas($campo_corregido,$corte_280);//este no mira esto el 33 si

			//comparacion fecha campo actual es inferior a la fecha de nacimiento
			$fecha_nacimiento_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),"1900-12-31");
			$campo_actual_inferior_fecha_nacimiento=$this->diferencia_dias_entre_fechas($campo_corregido,trim($array_fields[9]));
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_nacimiento_es_mayor_a_1900_12_31<0
			&& $fecha_nacimiento_es_mayor_a_1900_12_31!==false
			&& $campo_actual_inferior_fecha_nacimiento>0
			&& $campo_actual_inferior_fecha_nacimiento!==false
			)
			{
				$campo_corregido="1800-01-01";
			}//fin if
			//fin comparacion fecha campo actual es inferior a la fecha de nacimiento

			//limite inferior fecha corte 2 years
			$fecha_corte_menos_2_years="";
			$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=false;
			$array_fecha_corte=explode("-", $date_de_corte);
			if(count($array_fecha_corte)==3)
			{
				$fecha_corte_menos_2_years=(intval($array_fecha_corte[0])-2)."-".$array_fecha_corte[1]."-".$array_fecha_corte[2];
				$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=$this->diferencia_dias_entre_fechas($campo_corregido,$fecha_corte_menos_2_years);
			}//fin if
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_corte_menos_2_years!=""
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years!==false
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years>0
			)
			{
				$campo_corregido="1845-01-01";
			}//fin if
			//fin limite inferior fecha corte 2 years

			//este no mira contra 280 dias
			if($comparacion_a_1900_12_31<0 
					&& $excede_fecha_corte<0
					&& $comparacion_a_1900_12_31!==false
					&& $excede_fecha_corte!==false
					)
				{
					$campo_corregido="1800-01-01";
				}//fin comparacion excede la fecha de corte

			
			
			$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
			if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01")
			{		
			  if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& $array_fields[10]=="M")
				{
				    $campo_corregido="1845-01-01";
				}
				else if($comparacion_a_1900_12_31>=0
					&& $campo_corregido!="1800-01-01"
					&& $campo_corregido!="1805-01-01"
					&& $campo_corregido!="1810-01-01"
					&& $campo_corregido!="1825-01-01"
					&& $campo_corregido!="1830-01-01"
					&& $campo_corregido!="1835-01-01"
					&& $campo_corregido!="1845-01-01"
					&& ($edad<10 || $edad>=60)
					    && $array_fields[10]=="F")
				{
				    $campo_corregido="1845-01-01";
				}
				else if($comparacion_a_1900_12_31>=0
					&& $campo_corregido!="1800-01-01"
					&& $campo_corregido!="1805-01-01"
					&& $campo_corregido!="1810-01-01"
					&& $campo_corregido!="1825-01-01"
					&& $campo_corregido!="1830-01-01"
					&& $campo_corregido!="1835-01-01"
					&& $campo_corregido!="1845-01-01"
					&& ($edad>=10 && $edad<60)
					&& $array_fields[10]=="F" 
					)
				{
				    $campo_corregido="1835-01-01";
				}
				else if($comparacion_a_1900_12_31>=0
					&& $campo_corregido!="1800-01-01"
					&& $campo_corregido!="1805-01-01"
					&& $campo_corregido!="1810-01-01"
					&& $campo_corregido!="1825-01-01"
					&& $campo_corregido!="1830-01-01"
					&& $campo_corregido!="1835-01-01"
					&& $campo_corregido!="1845-01-01")
				{
				    $campo_corregido="1845-01-01";
				}
			}//fin if diff valor permitido
		
	     
		//$campo_corregido=$this->corrector_valor_permitido_fecha_criterio_002($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
	       $es_fecha_es_valida=false;
			$verificacion_fecha_corte_pos_year=-1;
			$verificacion_fecha_corte=-1;
			$verificacion_fecha_corte_51_9_meses_menos=-1;
			$array_fecha_correccion_actual=explode("-",$campo_corregido);
			$diferencia_de_1900=-1;
			if(count($array_fecha_correccion_actual)==3)
			{
			    //checkdate mm/dd/aaaa
			    if(checkdate($array_fecha_correccion_actual[1],$array_fecha_correccion_actual[2],$array_fecha_correccion_actual[0]))
			    {
				$es_fecha_es_valida=true;
				$interval = date_diff(new DateTime($campo_corregido),new DateTime($date_de_corte_posterior_year));
				$verificacion_fecha_corte_pos_year =(float)($interval->format("%r%a"));
				$interval = date_diff(new DateTime($campo_corregido),new DateTime($date_de_corte));
				$verificacion_fecha_corte =(float)($interval->format("%r%a"));
				$interval = date_diff(new DateTime($campo_corregido),new DateTime($date_de_corte_9_meses_menos));
				$verificacion_fecha_corte_51_9_meses_menos=(float)($interval->format("%r%a"));
				//echo "<script>alert('$verificacion_fecha_corte');</script>";
				$interval = date_diff(new DateTime($campo_corregido),new DateTime("1900-01-01"));
				$diferencia_de_1900 =(float)($interval->format("%r%a"));
			    }
			}
			
			//calidad con fecha calendario
			$nueva_edad_year_actual=-1;
			$nueva_edad_meses_actual=-1;
			$nueva_edad_dias_actual=-1;
			    
			$array_fecha_nacimiento=explode("-",$array_fields[9]);
			$fecha_campo_actual=explode("-",$campo_corregido);
			if(count($fecha_campo_actual)==3 && checkdate($fecha_campo_actual[1],$fecha_campo_actual[2],$fecha_campo_actual[0]))
			{
			    //echo "<script>alert('entro');</script>";
			    
			    $array_calc_edad_actual=$this->edad_years_months_days($array_fecha_nacimiento[2]."-".$array_fecha_nacimiento[1]."-".$array_fecha_nacimiento[0],$fecha_campo_actual[2]."-".$fecha_campo_actual[1]."-".$fecha_campo_actual[0]);
			    $nueva_edad_year_actual=intval($array_calc_edad_actual['y']);
			    $nueva_edad_meses_actual=(intval($array_calc_edad_actual['y'])*12)+$array_calc_edad_actual['m'];
			    $nueva_edad_dias_actual=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),trim($array_fields[$numero_campo_a_corregir]));
			    
			    //echo "<script>alert(' EDAD DIAS $nueva_edad_dias_actual FN ".trim($array_fields[9])." FA ".trim($array_fields[$numero_campo_a_corregir])."  NCA $numero_campo_a_corregir');</script>";
			}
			$es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
			$es_menor_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
			
			//calidad c51

			if($campo_corregido!="1845-01-01"
			    && $array_fields[10]=="M"
			)
			{
			    $campo_corregido="1845-01-01";
			}
			else if($es_mayor_a_1900_12_31<0
				&& $array_fields[10]=="F"
				&& ($nueva_edad_year_actual<10 || $nueva_edad_year_actual>=60)
			)
			{
			    $campo_corregido="1845-01-01";
			}
			else if($array_fields[10]=="F"
				&& ($edad<10 || $edad>=60)
				&& (trim($array_fields[51])=="1800-01-01"
				    || trim($array_fields[51])=="1805-01-01"
				    || trim($array_fields[51])=="1810-01-01"
				    || trim($array_fields[51])=="1825-01-01"
				    || trim($array_fields[51])=="1830-01-01"
				    || trim($array_fields[51])=="1835-01-01"
				    )
			)
			{
			    $campo_corregido="1845-01-01";
			}
			else if(
				($campo_corregido=="1845-01-01" || $campo_corregido=="1800-01-01")
			    && ($edad>=10 && $edad<60)
				&& $array_fields[10]=="F"
			    )
			{
			    $campo_corregido="1835-01-01";
			}
		
	     }//fin fase 1
	    }//revisado c51
	    
	    //campo a corregir es 56
	    if($numero_campo_a_corregir==56)
	    {

	    	$c58_comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[58]),"1900-12-31");
		     if($fase_correccion==0)
		     {
		     		if($campo_corregido=="")
					{
						//campo en blanco
						if($campo_corregido=="" 
							&& $array_fields[10]=="M"
							)
						{
							//c1
						    $campo_corregido="1845-01-01";
						}
						else if($campo_corregido=="" 
							&& ($edad<10 || $edad>=60)
							    && $array_fields[10]=="F"
							    )
						{
							//c2
						    $campo_corregido="1845-01-01";
						}					
						else if($campo_corregido=="" 
							&& ($edad>=10 && $edad<60)
							&& $array_fields[10]=="F" 
							&& $array_fields[49]!="1845-01-01"
							)
						{
							//c3
						    $campo_corregido="1845-01-01";
						}
						else if($campo_corregido=="" 
							 && $array_fields[10]=="F"
							 && ($edad>=10 && $edad<60)
							 && $array_fields[14]!="1"
							 && $array_fields[49]=="1845-01-01"
							)
						{
							//c4
						    $campo_corregido="1845-01-01";
						}	
						else if($campo_corregido=="" 
							&& ($edad>=10 && $edad<60)
							&& $array_fields[10]=="F" 
							&& $array_fields[14]=="1"
							&& $array_fields[49]=="1845-01-01"
							&& $c58_comparacion_a_1900_12_31<0
							)
						{
							//c5
						    $campo_corregido=trim($array_fields[58]);
						}	
						else if($campo_corregido=="" 
							&& ($edad>=10 && $edad<60)
							&& $array_fields[10]=="F" 
							&& $array_fields[14]=="1"
							&& $array_fields[49]=="1845-01-01"
							)
						{
							//c6
						    $campo_corregido="1800-01-01";
						}	
						else if($campo_corregido=="")
						{
							//c8
						    $campo_corregido="1845-01-01";
						}
					}//fin if campo en blanco

					//formato
					$corrige_formato=false;
					$array_fecha_campo_actual=explode("-", $campo_corregido);
					if(count($array_fecha_campo_actual)==3)
					{
						if(ctype_digit($array_fecha_campo_actual[0]) && ctype_digit($array_fecha_campo_actual[1]) && ctype_digit($array_fecha_campo_actual[2]))
						{
							//checkdate mm-dd-aaaa -> aaaa-mm-dd ?
							if(checkdate($array_fecha_campo_actual[1],$array_fecha_campo_actual[2],$array_fecha_campo_actual[0])
						       && intval($array_fecha_campo_actual[0])>=32)
						    {
						    	//no se corrige formato
						    }
						    else
						    {
						    	$corrige_formato=true;
						    }

						}
						else
						{
							$corrige_formato=true;
						}
					}
					else
					{
						$corrige_formato=true;
					}//fin else

					if($corrige_formato==true)
					{
						$campo_corregido=$this->corrector_formato_fecha($campo_corregido,false,-2);
					}
					
					
					//valor permitido
					$excede_fecha_corte=$this->diferencia_dias_entre_fechas($campo_corregido,$date_de_corte);
					$es_menor_o_igual_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
					$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
					
					
					$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");

					//comparacion fecha campo actual es inferior a la fecha de nacimiento
			$fecha_nacimiento_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),"1900-12-31");
			$campo_actual_inferior_fecha_nacimiento=$this->diferencia_dias_entre_fechas($campo_corregido,trim($array_fields[9]));
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_nacimiento_es_mayor_a_1900_12_31<0
			&& $fecha_nacimiento_es_mayor_a_1900_12_31!==false
			&& $campo_actual_inferior_fecha_nacimiento>0
			&& $campo_actual_inferior_fecha_nacimiento!==false
			)
			{
				$campo_corregido="1800-01-01";
			}//fin if
			//fin comparacion fecha campo actual es inferior a la fecha de nacimiento

			//limite inferior fecha corte 1 years
			$fecha_corte_menos_1_years="";
			$comparacion_fecha_actual_vs_fecha_corte_menos_1_years=false;
			$array_fecha_corte=explode("-", $date_de_corte);
			if(count($array_fecha_corte)==3)
			{
				$fecha_corte_menos_1_years=(intval($array_fecha_corte[0])-1)."-".$array_fecha_corte[1]."-".$array_fecha_corte[2];
				$comparacion_fecha_actual_vs_fecha_corte_menos_1_years=$this->diferencia_dias_entre_fechas($campo_corregido,$fecha_corte_menos_1_years);
			}//fin if
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_corte_menos_1_years!=""
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_1_years!==false
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_1_years>0
			)
			{
				$campo_corregido="1845-01-01";
			}//fin if
			//fin limite inferior fecha corte 1 years

					if($comparacion_a_1900_12_31<0 
						&& $excede_fecha_corte<0
						&& $comparacion_a_1900_12_31!==false
						&& $excede_fecha_corte!==false
						)
					{
						$campo_corregido="1800-01-01";
					}//fin comparacion excede la fecha de corte

					if($comparacion_a_1900_12_31>=0
						&& $campo_corregido!="1800-01-01"
						&& $campo_corregido!="1805-01-01"
						&& $campo_corregido!="1810-01-01"
						&& $campo_corregido!="1825-01-01"
						&& $campo_corregido!="1830-01-01"
						&& $campo_corregido!="1835-01-01"
						&& $campo_corregido!="1845-01-01")
					{
						if($comparacion_a_1900_12_31>=0
							&& $campo_corregido!="1800-01-01"
							&& $campo_corregido!="1805-01-01"
							&& $campo_corregido!="1810-01-01"
							&& $campo_corregido!="1825-01-01"
							&& $campo_corregido!="1830-01-01"
							&& $campo_corregido!="1835-01-01"
							&& $campo_corregido!="1845-01-01" 
							&& $array_fields[10]=="M"
							)
						{
							//c2
						    $campo_corregido="1845-01-01";
						}					
						else if($comparacion_a_1900_12_31>=0
							&& $campo_corregido!="1800-01-01"
							&& $campo_corregido!="1805-01-01"
							&& $campo_corregido!="1810-01-01"
							&& $campo_corregido!="1825-01-01"
							&& $campo_corregido!="1830-01-01"
							&& $campo_corregido!="1835-01-01"
							&& $campo_corregido!="1845-01-01" 
							&& ($edad<10 || $edad>=60)
							    && $array_fields[10]=="F"
							    )
						{
							//c3
						    $campo_corregido="1845-01-01";
						}					
						else if($comparacion_a_1900_12_31>=0
							&& $campo_corregido!="1800-01-01"
							&& $campo_corregido!="1805-01-01"
							&& $campo_corregido!="1810-01-01"
							&& $campo_corregido!="1825-01-01"
							&& $campo_corregido!="1830-01-01"
							&& $campo_corregido!="1835-01-01"
							&& $campo_corregido!="1845-01-01" 
							&& ($edad>=10 && $edad<60)
							&& $array_fields[10]=="F" 
							&& $array_fields[49]!="1845-01-01"
							)
						{
							//c4
						    $campo_corregido="1845-01-01";
						}
						else if($comparacion_a_1900_12_31>=0
							&& $campo_corregido!="1800-01-01"
							&& $campo_corregido!="1805-01-01"
							&& $campo_corregido!="1810-01-01"
							&& $campo_corregido!="1825-01-01"
							&& $campo_corregido!="1830-01-01"
							&& $campo_corregido!="1835-01-01"
							&& $campo_corregido!="1845-01-01" 
							 && $array_fields[10]=="F"
							 && ($edad>=10 && $edad<60)
							 && $array_fields[14]!="1"
							 && $array_fields[49]=="1845-01-01"
							)
						{
							//c5
						    $campo_corregido="1845-01-01";
						}
						else if($comparacion_a_1900_12_31>=0
							&& $campo_corregido!="1800-01-01"
							&& $campo_corregido!="1805-01-01"
							&& $campo_corregido!="1810-01-01"
							&& $campo_corregido!="1825-01-01"
							&& $campo_corregido!="1830-01-01"
							&& $campo_corregido!="1835-01-01"
							&& $campo_corregido!="1845-01-01" 
							 && $array_fields[10]=="F"
							 && ($edad>=10 && $edad<60)
							 && $array_fields[14]=="1"
							 && $array_fields[49]=="1845-01-01"
							)
						{
							//c6
						    $campo_corregido="1800-01-01";
						}							
						else if($comparacion_a_1900_12_31>=0
							&& $campo_corregido!="1800-01-01"
							&& $campo_corregido!="1805-01-01"
							&& $campo_corregido!="1810-01-01"
							&& $campo_corregido!="1825-01-01"
							&& $campo_corregido!="1830-01-01"
							&& $campo_corregido!="1835-01-01"
							&& $campo_corregido!="1845-01-01")
						{
							//c6
						    $campo_corregido="1845-01-01";
						}
					}//fin if
				     
					//$campo_corregido=$this->corrector_valor_permitido_fecha_criterio_002($campo_corregido);
		     }
		     else if($fase_correccion==1)
		     {
		      
		      	//calidad con fecha actividad
				$nueva_edad_year_actual=-1;
				$nueva_edad_meses_actual=-1;
				$nueva_edad_dias_actual=-1;
				    
				$array_fecha_nacimiento=explode("-",$array_fields[9]);
				$fecha_campo_actual=explode("-",$campo_corregido);
				if(count($fecha_campo_actual)==3 && checkdate($fecha_campo_actual[1],$fecha_campo_actual[2],$fecha_campo_actual[0]))
				{
				    //echo "<script>alert('entro');</script>";
				    
				    $array_calc_edad_actual=$this->edad_years_months_days($array_fecha_nacimiento[2]."-".$array_fecha_nacimiento[1]."-".$array_fecha_nacimiento[0],$fecha_campo_actual[2]."-".$fecha_campo_actual[1]."-".$fecha_campo_actual[0]);
				    $nueva_edad_year_actual=intval($array_calc_edad_actual['y']);
				    $nueva_edad_meses_actual=(intval($array_calc_edad_actual['y'])*12)+$array_calc_edad_actual['m'];
				    $nueva_edad_dias_actual=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),trim($array_fields[$numero_campo_a_corregir]));
				    
				    //echo "<script>alert(' EDAD DIAS $nueva_edad_dias_actual FN ".trim($array_fields[9])." FA ".trim($array_fields[$numero_campo_a_corregir])."  NCA $numero_campo_a_corregir');</script>";
				}
				$es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
				$es_menor_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");


			    	$c58_comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[58]),"1900-12-31");

			    	$c56_mayor_c58=$this->diferencia_dias_entre_fechas(trim($array_fields[58]),trim($array_fields[$numero_campo_a_corregir]));

				//calidad c56
				if($campo_corregido!="1845-01-01"
				    && $array_fields[10]=="M"
				)
				{
					//criterio 1
				    $campo_corregido="1845-01-01";
				}
				else if($es_mayor_a_1900_12_31<0
					&& $array_fields[10]=="F"
					&& ($nueva_edad_year_actual<10 || $nueva_edad_year_actual>=60)
				)
				{
					//criterio 2
				    $campo_corregido="1845-01-01";
				}
				else if($array_fields[10]=="F"
					&& ($edad<10 || $edad>=60)
					&& (trim($array_fields[56])=="1800-01-01"
						    || trim($array_fields[56])=="1805-01-01"
						    || trim($array_fields[56])=="1810-01-01"
						    || trim($array_fields[56])=="1825-01-01"
						    || trim($array_fields[56])=="1830-01-01"
						    || trim($array_fields[56])=="1835-01-01"
						    )
				)
				{
					//criterio 3
				    $campo_corregido="1845-01-01";
				}
				else if($es_mayor_a_1900_12_31<0
					&& ($nueva_edad_year_actual>=10 && $nueva_edad_year_actual<60)
					&& $array_fields[10]=="F" 
					&& $array_fields[49]!="1845-01-01"
					)
				{
					//c4
				    $campo_corregido="1845-01-01";
				}					
				else if(
					(trim($array_fields[56])=="1800-01-01"
				    || trim($array_fields[56])=="1805-01-01"
				    || trim($array_fields[56])=="1810-01-01"
				    || trim($array_fields[56])=="1825-01-01"
				    || trim($array_fields[56])=="1830-01-01"
				    || trim($array_fields[56])=="1835-01-01"
				    )
					&& ($edad>=10 && $edad<60)
					&& $array_fields[10]=="F" 
					&& $array_fields[49]!="1845-01-01"
					)
				{
					//c5
				    $campo_corregido="1845-01-01";
				}				
				else if($es_mayor_a_1900_12_31<0
					&& ($nueva_edad_year_actual>=10 && $nueva_edad_year_actual<60)
					 && $array_fields[10]=="F"
					 && $array_fields[14]!="1"
					 && $array_fields[49]=="1845-01-01"
					)
				{
					//c6
				    $campo_corregido="1845-01-01";
				}	
				else if(
					(trim($array_fields[56])=="1800-01-01"
				    || trim($array_fields[56])=="1805-01-01"
				    || trim($array_fields[56])=="1810-01-01"
				    || trim($array_fields[56])=="1825-01-01"
				    || trim($array_fields[56])=="1830-01-01"
				    || trim($array_fields[56])=="1835-01-01"
				    )
					 && $array_fields[10]=="F"
					 && ($edad>=10 && $edad<60)
					 && $array_fields[14]!="1"
					 && $array_fields[49]=="1845-01-01"
					)
				{
					//c7
				    $campo_corregido="1845-01-01";
				}					
				else if($es_mayor_a_1900_12_31>=0
					&& $es_mayor_a_1900_12_31!==false
					&& ($edad>=10 && $edad<60)
					&& $array_fields[10]=="F" 
					&& $array_fields[14]=="1"
					&& $array_fields[49]=="1845-01-01"
					&& $c58_comparacion_a_1900_12_31<0
					)
				{
					//c8
				    $campo_corregido=trim($array_fields[58]);
				}
				else if($es_mayor_a_1900_12_31>=0
					&& $es_mayor_a_1900_12_31!==false
					&& $campo_corregido!="1800-01-01"
					&& ($edad>=10 && $edad<60)
					&& $array_fields[10]=="F" 
					&& $array_fields[14]=="1"
					&& $array_fields[49]=="1845-01-01"
					)
				{
					//c9
				    $campo_corregido="1800-01-01";
				}		
					
							
				//fin calidad
				
			
		     }//fin fase 1
	    }//revisado c56
	    
	    //campo a corregir es 57
	    if($numero_campo_a_corregir==57)
	    {
	    	$c56_comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[56]),"1900-12-31");
		     if($fase_correccion==0)
		     {
		     	

				//campo en blanco
				if($campo_corregido=="")
				{
					if($campo_corregido=="" 
						&& $array_fields[10]=="M")
					{
						//criterio 1
					    $campo_corregido="0";
					}
					else if($campo_corregido=="" 
						&& ($edad<10 || $edad>=60)
						    && $array_fields[10]=="F"
						    )
					{
						//criterio 2
					    $campo_corregido="0";
					}
					else if($campo_corregido==""
						&& ($edad>=10 && $edad<60)
						    && $array_fields[10]=="F"
					 && ($array_fields[49]!="1845-01-01") 
					 )
					{
						//criterio 3
					    $campo_corregido="0";
					}	
					else if($campo_corregido==""
						&& ($edad>=10 && $edad<60)
					    && $array_fields[10]=="F"
					 	&& $array_fields[14]!="1"
					 	&& ($array_fields[49]=="1845-01-01") 
					 )
					{
						//criterio 4
					    $campo_corregido="0";
					}	
					else if($campo_corregido==""
						&& ($edad>=10 && $edad<60)
						    && $array_fields[10]=="F"
						    && $array_fields[14]=="1"
						    && ($array_fields[49]=="1845-01-01") 
					 )
					{
						//criterio 5
					    $campo_corregido="999";
					}						
					else if($campo_corregido=="")
					{
						//criterio 6
					    $campo_corregido="0";
					}
				}//fin campo esta en blanco
				
				//valor permitido
				if((intval($campo_corregido)<1 || intval($campo_corregido)>25 || is_numeric($campo_corregido)==false)
					&& intval($campo_corregido)!=999
					&& intval($campo_corregido)!=0
					)
				{

					if((intval($campo_corregido)<1 || intval($campo_corregido)>25 || is_numeric($campo_corregido)==false)
						&& intval($campo_corregido)!=999
						&& intval($campo_corregido)!=0
						&& $array_fields[10]=="M")
					{
						//criterio 1
					    $campo_corregido="0";
					}
					else if((intval($campo_corregido)<1 || intval($campo_corregido)>25 || is_numeric($campo_corregido)==false)
						&& intval($campo_corregido)!=999
						&& intval($campo_corregido)!=0
						&& ($edad<10 || $edad>=60)
						    && $array_fields[10]=="F"
						    )
					{
						//criterio 2
					    $campo_corregido="0";
					}
					else if((intval($campo_corregido)<1 || intval($campo_corregido)>25 || is_numeric($campo_corregido)==false)
						&& intval($campo_corregido)!=999
						&& intval($campo_corregido)!=0
						&& ($edad>=10 && $edad<60)
						    && $array_fields[10]=="F"
					 && ($array_fields[49]!="1845-01-01") 
					 )
					{
						//criterio 3
					    $campo_corregido="0";
					}	
					else if((intval($campo_corregido)<1 || intval($campo_corregido)>25 || is_numeric($campo_corregido)==false)
						&& intval($campo_corregido)!=999
						&& intval($campo_corregido)!=0
						&& ($edad>=10 && $edad<60)
					    && $array_fields[10]=="F"
					 	&& $array_fields[14]!="1"
					 	&& ($array_fields[49]=="1845-01-01") 
					 )
					{
						//criterio 4
					    $campo_corregido="0";
					}	
					else if((intval($campo_corregido)<1 || intval($campo_corregido)>25 || is_numeric($campo_corregido)==false)
						&& intval($campo_corregido)!=999
						&& intval($campo_corregido)!=0
						&& ($edad>=10 && $edad<60)
						    && $array_fields[10]=="F"
						    && $array_fields[14]=="1"
						    && ($array_fields[49]=="1845-01-01") 
					 )
					{
						//criterio 5
					    $campo_corregido="999";
					}	
					else if((intval($campo_corregido)<1 || intval($campo_corregido)>25 || is_numeric($campo_corregido)==false)
						&& intval($campo_corregido)!=999
						&& intval($campo_corregido)!=0
						)
					{
						//criterio 6
					    $campo_corregido="0";
					}
				}//si es diferente de los valores permitidos
				
				//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_039($campo_corregido);
		     }
		     else if($fase_correccion==1)
		     {
					//calidad c57

			     	if($campo_corregido!="0" 
					&& $array_fields[10]=="M")
					{
						//criterio 1
					    $campo_corregido="0";
					}
					else if($campo_corregido!="0" 
					&& ($edad<10 || $edad>=60)
					    && $array_fields[10]=="F"
					    )
					{
						//criterio 2
					    $campo_corregido="0";
					}
					else if($campo_corregido!="0"
						&& ($edad>=10 && $edad<60)
						    && $array_fields[10]=="F"
					 && ($array_fields[49]!="1845-01-01") 
					 )
					{
						//criterio 3
					    $campo_corregido="0";
					}	
					else if($campo_corregido!="0"
						&& ($edad>=10 && $edad<60)
						    && $array_fields[10]=="F"
						    && $array_fields[14]!="1"
						    && ($array_fields[49]=="1845-01-01") 
					 )
					{
						//criterio 4
					    $campo_corregido="0";
					}
					else if($campo_corregido=="0"
						&& ($edad>=10 && $edad<60)
						    && $array_fields[10]=="F"
						    && $array_fields[14]=="1"
						    && ($array_fields[49]=="1845-01-01") 
					 )
					{
						//criterio 5
					    $campo_corregido="999";
					}
					
			     }//fin if
	    }//revisado c57
	    
	    //campo a corregir es 58
	    if($numero_campo_a_corregir==58)
	    {
	    	$c56_comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[56]),"1900-12-31");
	     if($fase_correccion==0)
	     {
				//campo en blanco
				if($campo_corregido=="")
				{
					if($campo_corregido=="" 
						&& $array_fields[10]=="M"
						)
					{
						//c1
					    $campo_corregido="1845-01-01";
					}
					else if($campo_corregido=="" 
							&& ($edad<10 || $edad>=60)
						    && $array_fields[10]=="F"
						    )
					{
						//c2
					    $campo_corregido="1845-01-01";
					}
					else if($campo_corregido=="" 
							&& ($edad>=10 && $edad<60)
							&& $array_fields[10]=="F" 
							&& $array_fields[49]!="1845-01-01"
						    )
					{
						//c3
					    $campo_corregido="1845-01-01";
					}
					else if($campo_corregido=="" 
						&& ($edad>=10 && $edad<60)
							&& $array_fields[10]=="F" 
							&& $array_fields[49]=="1845-01-01"
							&& $array_fields[14]!="1"
							 )
					{
						//c4
					    $campo_corregido="1845-01-01";
					}
					else if($campo_corregido=="" 
						&& ($edad>=10 && $edad<60)
							&& $array_fields[10]=="F" 
							&& $array_fields[49]=="1845-01-01"
							&& $array_fields[14]=="1"
							&& $c56_comparacion_a_1900_12_31<0
						)
					{
						//c5
					    $campo_corregido=trim($array_fields[56]);
					}
					else if($campo_corregido=="" 
						&& ($edad>=10 && $edad<60)
							&& $array_fields[10]=="F" 
							&& $array_fields[49]=="1845-01-01"
							&& $array_fields[14]=="1"
						)
					{
						//c5
					    $campo_corregido="1800-01-01";
					}
					else if($campo_corregido=="")
					{
						//c6
					    $campo_corregido="1845-01-01";
					}
				}//fin campo en blanco
				
				//formato
				$corrige_formato=false;
				$array_fecha_campo_actual=explode("-", $campo_corregido);
				if(count($array_fecha_campo_actual)==3)
				{
					if(ctype_digit($array_fecha_campo_actual[0]) && ctype_digit($array_fecha_campo_actual[1]) && ctype_digit($array_fecha_campo_actual[2]))
					{
						//checkdate mm-dd-aaaa -> aaaa-mm-dd ?
						if(checkdate($array_fecha_campo_actual[1],$array_fecha_campo_actual[2],$array_fecha_campo_actual[0])
					       && intval($array_fecha_campo_actual[0])>=32)
					    {
					    	//no se corrige formato
					    }
					    else
					    {
					    	$corrige_formato=true;
					    }

					}
					else
					{
						$corrige_formato=true;
					}
				}
				else
				{
					$corrige_formato=true;
				}//fin else

				if($corrige_formato==true)
				{
					$campo_corregido=$this->corrector_formato_fecha($campo_corregido,false,-2);
				}
				
				
				//valor permitido
				$excede_fecha_corte=$this->diferencia_dias_entre_fechas($campo_corregido,$date_de_corte);
				$es_menor_o_igual_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
				$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
				
				
				$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");

				//comparacion fecha campo actual es inferior a la fecha de nacimiento
			$fecha_nacimiento_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),"1900-12-31");
			$campo_actual_inferior_fecha_nacimiento=$this->diferencia_dias_entre_fechas($campo_corregido,trim($array_fields[9]));
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_nacimiento_es_mayor_a_1900_12_31<0
			&& $fecha_nacimiento_es_mayor_a_1900_12_31!==false
			&& $campo_actual_inferior_fecha_nacimiento>0
			&& $campo_actual_inferior_fecha_nacimiento!==false
			)
			{
				$campo_corregido="1800-01-01";
			}//fin if
			//fin comparacion fecha campo actual es inferior a la fecha de nacimiento


			//limite inferior fecha corte 1 years
			$fecha_corte_menos_1_years="";
			$comparacion_fecha_actual_vs_fecha_corte_menos_1_years=false;
			$array_fecha_corte=explode("-", $date_de_corte);
			if(count($array_fecha_corte)==3)
			{
				$fecha_corte_menos_1_years=(intval($array_fecha_corte[0])-1)."-".$array_fecha_corte[1]."-".$array_fecha_corte[2];
				$comparacion_fecha_actual_vs_fecha_corte_menos_1_years=$this->diferencia_dias_entre_fechas($campo_corregido,$fecha_corte_menos_1_years);
			}//fin if
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_corte_menos_1_years!=""
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_1_years!==false
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_1_years>0
			)
			{
				$campo_corregido="1845-01-01";
			}//fin if
			//fin limite inferior fecha corte 1 years

				if($comparacion_a_1900_12_31<0 
					&& $excede_fecha_corte<0
					&& $comparacion_a_1900_12_31!==false
					&& $excede_fecha_corte!==false
					)
				{
					$campo_corregido="1800-01-01";
				}//fin comparacion excede la fecha de corte

				if($comparacion_a_1900_12_31>=0
					&& $campo_corregido!="1800-01-01"
					&& $campo_corregido!="1845-01-01"
					)
				{
					if($comparacion_a_1900_12_31>=0
						&& $campo_corregido!="1800-01-01"
						&& $campo_corregido!="1845-01-01"
						&& $array_fields[10]=="M"
						)
					{
						//c1
					    $campo_corregido="1845-01-01";
					}
					else if($comparacion_a_1900_12_31>=0
						&& $campo_corregido!="1800-01-01"
						&& $campo_corregido!="1845-01-01"
							&& ($edad<10 || $edad>=60)
						    && $array_fields[10]=="F"
						    )
					{
						//c2
					    $campo_corregido="1845-01-01";
					}
					else if($comparacion_a_1900_12_31>=0
						&& $campo_corregido!="1800-01-01"
						&& $campo_corregido!="1845-01-01"
							&& ($edad>=10 && $edad<60)
							&& $array_fields[10]=="F" 
							&& $array_fields[49]!="1845-01-01"
						    )
					{
						//c3
					    $campo_corregido="1845-01-01";
					}
					else if($comparacion_a_1900_12_31>=0
						&& $campo_corregido!="1800-01-01"
						&& $campo_corregido!="1845-01-01"
						&& ($edad>=10 && $edad<60)
							&& $array_fields[10]=="F" 
							&& $array_fields[49]=="1845-01-01"
							&& $array_fields[14]!="1"
							 )
					{
						//c4
					    $campo_corregido="1845-01-01";
					}
					else if($comparacion_a_1900_12_31>=0
						&& $campo_corregido!="1800-01-01"
						&& $campo_corregido!="1845-01-01"
						&& ($edad>=10 && $edad<60)
							&& $array_fields[10]=="F" 
							&& $array_fields[49]=="1845-01-01"
							&& $array_fields[14]=="1"
						)
					{
						//c5
					    $campo_corregido="1800-01-01";
					}
					else if($comparacion_a_1900_12_31>=0
						&& $campo_corregido!="1800-01-01"
						&& $campo_corregido!="1845-01-01"
						)
					{
					    $campo_corregido="1845-01-01";
					}
				}//fin vp
				
				//$campo_corregido=$this->corrector_valor_permitido_fecha_criterio_003($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
		
				//calidad
				$es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
				$es_menor_al_campo56=$this->diferencia_dias_entre_fechas($campo_corregido,trim($array_fields[56]));
				
				//calidad con fecha actividad
				$nueva_edad_year_actual=-1;
				$nueva_edad_meses_actual=-1;
				$nueva_edad_dias_actual=-1;
				    
				$array_fecha_nacimiento=explode("-",$array_fields[9]);
				$fecha_campo_actual=explode("-",$campo_corregido);
				if(count($fecha_campo_actual)==3 && checkdate($fecha_campo_actual[1],$fecha_campo_actual[2],$fecha_campo_actual[0]))
				{
				    //echo "<script>alert('entro');</script>";
				    
				    $array_calc_edad_actual=$this->edad_years_months_days($array_fecha_nacimiento[2]."-".$array_fecha_nacimiento[1]."-".$array_fecha_nacimiento[0],$fecha_campo_actual[2]."-".$fecha_campo_actual[1]."-".$fecha_campo_actual[0]);
				    $nueva_edad_year_actual=intval($array_calc_edad_actual['y']);
				    $nueva_edad_meses_actual=(intval($array_calc_edad_actual['y'])*12)+$array_calc_edad_actual['m'];
				    $nueva_edad_dias_actual=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),trim($array_fields[$numero_campo_a_corregir]));
				    
				    //echo "<script>alert(' EDAD DIAS $nueva_edad_dias_actual FN ".trim($array_fields[9])." FA ".trim($array_fields[$numero_campo_a_corregir])."  NCA $numero_campo_a_corregir');</script>";
				}
				$es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
				$es_menor_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");

				$c56_comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[56]),"1900-12-31");
				
				//calidad c58
				if($campo_corregido!="1845-01-01" 
					&& $array_fields[10]=="M"
					)
				{
					//c1
				    $campo_corregido="1845-01-01";
				}
				else if($es_mayor_a_1900_12_31<0
						&& ($nueva_edad_year_actual<10 || $nueva_edad_year_actual>=60)
					    && $array_fields[10]=="F"
					    )
				{
					//c2
				    $campo_corregido="1845-01-01";
				}
				else if($campo_corregido=="1800-01-01" 
						&& ($edad<10 || $edad>=60)
					    && $array_fields[10]=="F"
					    )
				{
					//c3
				    $campo_corregido="1845-01-01";
				}
				else if($es_mayor_a_1900_12_31<0
						&& ($nueva_edad_year_actual>=10 && $nueva_edad_year_actual<60)
						&& $array_fields[10]=="F" 
						&& $array_fields[49]!="1845-01-01"
					    )
				{
					//c4
				    $campo_corregido="1845-01-01";
				}
				else if($campo_corregido=="1800-01-01" 
						&& ($edad>=10 && $edad<60)
						&& $array_fields[10]=="F" 
						&& $array_fields[49]!="1845-01-01"
					    )
				{
					//c5
				    $campo_corregido="1845-01-01";
				}
				else if($es_mayor_a_1900_12_31<0
						&& ($nueva_edad_year_actual>=10 && $nueva_edad_year_actual<60)
						&& $array_fields[10]=="F" 
						&& $array_fields[49]=="1845-01-01"
						&& $array_fields[14]!="1" 
						)
				{
					//c6
				    $campo_corregido="1845-01-01";
				}
				else if($campo_corregido=="1800-01-01" 
					&& ($edad>=10 && $edad<60)
						&& $array_fields[10]=="F" 
						&& $array_fields[49]=="1845-01-01"
						&& $array_fields[14]!="1" 
						)
				{
					//c7
				    $campo_corregido="1845-01-01";
				}
				else if($es_mayor_a_1900_12_31>=0
					&& $es_mayor_a_1900_12_31!==false
					&& ($edad>=10 && $edad<60)
					&& $array_fields[10]=="F" 
					&& $array_fields[14]=="1"
					&& $array_fields[49]=="1845-01-01"
					&& $c56_comparacion_a_1900_12_31<0
					)
				{
					//c8
				    $campo_corregido=trim($array_fields[56]);
				}	
				else if($es_mayor_a_1900_12_31>=0
					&& $es_mayor_a_1900_12_31!==false
					&& $campo_corregido!="1800-01-01" 
					&& ($edad>=10 && $edad<60)
						&& $array_fields[10]=="F" 
						&& $array_fields[49]=="1845-01-01"
						&& $array_fields[14]=="1" 
						)
				{
					//c9
				    $campo_corregido="1800-01-01";
				}
				
				
		
	     }//fin fase 1
	    }//revisado c58
	    
	    //campo a corregir es 59
	    if($numero_campo_a_corregir==59)
	    {
	     if($fase_correccion==0)
	     {
			//campo en blanco
			if($campo_corregido=="")
			{

				if($campo_corregido=="" 
					&& $array_fields[10]=="M"
					)
				{
					//c1
				    $campo_corregido="0";
				}
				else if($campo_corregido=="" 
					&& ($array_fields[14]=="0" || $array_fields[14]=="2" || $array_fields[14]=="21")
				    && $array_fields[10]=="F"
					)
				{
					//c2
				    $campo_corregido="0";
				}
				else if($campo_corregido==""
					&& $array_fields[14]=="1"
					&& $array_fields[10]=="F"					
					)
				{
					//c3
				    $campo_corregido="21";
				}
				else if($campo_corregido=="")
				{
					//c6
				    $campo_corregido="0";
				}
			}//fin campo en blanco
			
			
			//valor permitido
			if($campo_corregido!="0"
			    && $campo_corregido!="1"
			    && $campo_corregido!="16"
			    && $campo_corregido!="17"
			    && $campo_corregido!="18"
			    && $campo_corregido!="20"
			    && $campo_corregido!="21"
			    )
			{
				if($campo_corregido!="0"
				    && $campo_corregido!="1"
				    && $campo_corregido!="16"
				    && $campo_corregido!="17"
				    && $campo_corregido!="18"
				    && $campo_corregido!="20"
				    && $campo_corregido!="21"
					&& $array_fields[10]=="M"
					)
				{
					//c1
				    $campo_corregido="0";
				}
				else if($campo_corregido!="0"
				    && $campo_corregido!="1"
				    && $campo_corregido!="16"
				    && $campo_corregido!="17"
				    && $campo_corregido!="18"
				    && $campo_corregido!="20"
				    && $campo_corregido!="21"
					&& ($array_fields[14]=="0" || $array_fields[14]=="2" || $array_fields[14]=="21")
				    && $array_fields[10]=="F"
					)
				{
					//c2
				    $campo_corregido="0";
				}
				else if($campo_corregido!="0"
				    && $campo_corregido!="1"
				    && $campo_corregido!="16"
				    && $campo_corregido!="17"
				    && $campo_corregido!="18"
				    && $campo_corregido!="20"
				    && $campo_corregido!="21"
					&& $array_fields[14]=="1"
					&& $array_fields[10]=="F"					
					)
				{
					//c3
				    $campo_corregido="21";
				}
				else if($campo_corregido!="0"
				    && $campo_corregido!="1"
				    && $campo_corregido!="16"
				    && $campo_corregido!="17"
				    && $campo_corregido!="18"
				    && $campo_corregido!="20"
				    && $campo_corregido!="21"
				    )
				{
					//c6
				    $campo_corregido="0";
				}
			}//fin vp
			//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_016($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
			//calidad c59
			if($campo_corregido!="0" 
				&& $array_fields[10]=="M"
				)
			{
				//c1
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0" 
				&& ($array_fields[14]=="0" || $array_fields[14]=="2" || $array_fields[14]=="21")
			    && $array_fields[10]=="F"
				)
			{
				//c2
			    $campo_corregido="0";
			}
			else if($campo_corregido!="21"
				&& $array_fields[14]=="1"
				&& $array_fields[10]=="F"					
				)
			{
				//c3
			    $campo_corregido="21";
			}//fin calidad
	        
		
	     }//fin fase 1
	    }//fin if campo c59
	    
	    //campo a corregir es 60
	    if($numero_campo_a_corregir==60)
	    {
	     if($fase_correccion==0)
	     {
			//campo en blanco
			if($campo_corregido=="")
			{
				if($campo_corregido=="" 
					&& $array_fields[10]=="M"
					)
				{
					//c1
				    $campo_corregido="0";
				}
				else if($campo_corregido=="" 
					&& ($array_fields[14]=="0" || $array_fields[14]=="2" || $array_fields[14]=="21")
				    && $array_fields[10]=="F"
					)
				{
					//c2
				    $campo_corregido="0";
				}
				else if($campo_corregido==""
					&& $array_fields[14]=="1"
					&& $array_fields[10]=="F"					
					)
				{
					//c3
				    $campo_corregido="21";
				}
				else if($campo_corregido=="")
				{
					//c6
				    $campo_corregido="0";
				}
			}//fin campo en blanco
			
			
			//valor permitido
			if($campo_corregido!="0"
			    && $campo_corregido!="1"
			    && $campo_corregido!="16"
			    && $campo_corregido!="17"
			    && $campo_corregido!="18"
			    && $campo_corregido!="20"
			    && $campo_corregido!="21"
			    )
			{
				if($campo_corregido!="0"
				    && $campo_corregido!="1"
				    && $campo_corregido!="16"
				    && $campo_corregido!="17"
				    && $campo_corregido!="18"
				    && $campo_corregido!="20"
				    && $campo_corregido!="21"
					&& $array_fields[10]=="M"
					)
				{
					//c1
				    $campo_corregido="0";
				}
				else if($campo_corregido!="0"
				    && $campo_corregido!="1"
				    && $campo_corregido!="16"
				    && $campo_corregido!="17"
				    && $campo_corregido!="18"
				    && $campo_corregido!="20"
				    && $campo_corregido!="21"
					&& ($array_fields[14]=="0" || $array_fields[14]=="2" || $array_fields[14]=="21")
				    && $array_fields[10]=="F"
					)
				{
					//c2
				    $campo_corregido="0";
				}
				else if($campo_corregido!="0"
				    && $campo_corregido!="1"
				    && $campo_corregido!="16"
				    && $campo_corregido!="17"
				    && $campo_corregido!="18"
				    && $campo_corregido!="20"
				    && $campo_corregido!="21"
					&& $array_fields[14]=="1"
					&& $array_fields[10]=="F"					
					)
				{
					//c3
				    $campo_corregido="21";
				}
				else if($campo_corregido!="0"
				    && $campo_corregido!="1"
				    && $campo_corregido!="16"
				    && $campo_corregido!="17"
				    && $campo_corregido!="18"
				    && $campo_corregido!="20"
				    && $campo_corregido!="21"
				    )
				{
					//c6
				    $campo_corregido="0";
				}
			}//fin vp
			//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_016($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
	        //calidad c60

	     	if($campo_corregido!="0" 
				&& $array_fields[10]=="M"
				)
			{
				//c1
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0" 
				&& ($array_fields[14]=="0" || $array_fields[14]=="2" || $array_fields[14]=="21")
			    && $array_fields[10]=="F"
				)
			{
				//c2
			    $campo_corregido="0";
			}
			else if($campo_corregido!="21"
				&& $array_fields[14]=="1"
				&& $array_fields[10]=="F"					
				)
			{
				//c3
			    $campo_corregido="21";
			}//fin calidad

	     }//fin fase 1
	    }//revisado
	    
	    //campo a corregir es 61
	    if($numero_campo_a_corregir==61)
	    {
	     if($fase_correccion==0)
	     {
			//campo en blanco
			if($campo_corregido=="")
			{
			
				if($campo_corregido=="" 
					&& $array_fields[10]=="M"
					)
				{
					//c1
				    $campo_corregido="0";
				}
				else if($campo_corregido=="" 
					&& ($array_fields[14]=="0" || $array_fields[14]=="2" || $array_fields[14]=="21")
				    && $array_fields[10]=="F"
					)
				{
					//c2
				    $campo_corregido="0";
				}
				else if($campo_corregido==""
					&& $array_fields[14]=="1"
					&& $array_fields[10]=="F"					
					)
				{
					//c3
				    $campo_corregido="21";
				}
				else if($campo_corregido=="")
				{
					//c6
				    $campo_corregido="0";
				}
			}//fin campo en blanco
			
			
			//valor permitido
			if($campo_corregido!="0"
			    && $campo_corregido!="1"
			    && $campo_corregido!="16"
			    && $campo_corregido!="17"
			    && $campo_corregido!="18"
			    && $campo_corregido!="20"
			    && $campo_corregido!="21"
			    )
			{
				if($campo_corregido!="0"
				    && $campo_corregido!="1"
				    && $campo_corregido!="16"
				    && $campo_corregido!="17"
				    && $campo_corregido!="18"
				    && $campo_corregido!="20"
				    && $campo_corregido!="21"
					&& $array_fields[10]=="M"
					)
				{
					//c1
				    $campo_corregido="0";
				}
				else if($campo_corregido!="0"
				    && $campo_corregido!="1"
				    && $campo_corregido!="16"
				    && $campo_corregido!="17"
				    && $campo_corregido!="18"
				    && $campo_corregido!="20"
				    && $campo_corregido!="21"
					&& ($array_fields[14]=="0" || $array_fields[14]=="2" || $array_fields[14]=="21")
				    && $array_fields[10]=="F"
					)
				{
					//c2
				    $campo_corregido="0";
				}
				else if($campo_corregido!="0"
				    && $campo_corregido!="1"
				    && $campo_corregido!="16"
				    && $campo_corregido!="17"
				    && $campo_corregido!="18"
				    && $campo_corregido!="20"
				    && $campo_corregido!="21"
					&& $array_fields[14]=="1"
					&& $array_fields[10]=="F"					
					)
				{
					//c3
				    $campo_corregido="21";
				}
				else if($campo_corregido!="0"
				    && $campo_corregido!="1"
				    && $campo_corregido!="16"
				    && $campo_corregido!="17"
				    && $campo_corregido!="18"
				    && $campo_corregido!="20"
				    && $campo_corregido!="21"
				    )
				{
					//c6
				    $campo_corregido="0";
				}
			}//fin vp
			//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_016($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
			if($campo_corregido!="0" 
				&& $array_fields[10]=="M"
				)
			{
				//c1
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0" 
				&& ($array_fields[14]=="0" || $array_fields[14]=="2" || $array_fields[14]=="21")
			    && $array_fields[10]=="F"
				)
			{
				//c2
			    $campo_corregido="0";
			}
			else if($campo_corregido!="21"
				&& $array_fields[14]=="1"
				&& $array_fields[10]=="F"					
				)
			{
				//c3
			    $campo_corregido="21";
			}//fin calidad
			
	     }//fin fase 1
	    }//revisado c61
	    
	    //campo a corregir es 78
	    if($numero_campo_a_corregir==78)
	    {
	     if($fase_correccion==0)
	     {
			//campo en blanco
			if($campo_corregido=="")
			{
			
				if($campo_corregido=="" 
					&& $array_fields[10]=="M"
					)
				{
					//c1
				    $campo_corregido="1845-01-01";
				}
				else if($campo_corregido=="" 
					&& ($edad<10 || $edad>=60)
					    && $array_fields[10]=="F")
				{
					//c2
				    $campo_corregido="1845-01-01";
				}
				else if($campo_corregido=="" 
					&& ($edad>=10 && $edad<60)
					&& $array_fields[10]=="F" 
					&& ($array_fields[49]!="1845-01-01")
					)
				{
					//c3
				    $campo_corregido="1845-01-01";
				}		
				else if($campo_corregido=="" 
					&& ($edad>=10 && $edad<60)
					&& $array_fields[10]=="F" 
					&& $array_fields[14]!="1"
					&& ($array_fields[49]=="1845-01-01")
					)
				{
					//c4
				    $campo_corregido="1845-01-01";
				}
				else if($campo_corregido=="" 
					&& ($edad>=10 && $edad<60)
					&& $array_fields[10]=="F" 
					&& $array_fields[14]=="1"
					&& ( $array_fields[79]=="1" || $array_fields[79]=="2" ||$array_fields[79]=="22")
					&& ($array_fields[49]=="1845-01-01")
					)
				{
					//c5
				    $campo_corregido="1800-01-01";
				}	
				else if($campo_corregido=="" 
					&& ($edad>=10 && $edad<60)
					&& $array_fields[10]=="F" 
					&& $array_fields[14]=="1"
					&& ( $array_fields[79]=="0")
					&& ($array_fields[49]=="1845-01-01")
					)
				{
					//c6
				    $campo_corregido="1835-01-01";
				}
				else if($campo_corregido=="")
				{
					//c7
				    $campo_corregido="1845-01-01";
				}
			}//fin if
			
			//formato
			$corrige_formato=false;
			$array_fecha_campo_actual=explode("-", $campo_corregido);
			if(count($array_fecha_campo_actual)==3)
			{
				if(ctype_digit($array_fecha_campo_actual[0]) && ctype_digit($array_fecha_campo_actual[1]) && ctype_digit($array_fecha_campo_actual[2]))
				{
					//checkdate mm-dd-aaaa -> aaaa-mm-dd ?
					if(checkdate($array_fecha_campo_actual[1],$array_fecha_campo_actual[2],$array_fecha_campo_actual[0])
				       && intval($array_fecha_campo_actual[0])>=32)
				    {
				    	//no se corrige formato
				    }
				    else
				    {
				    	$corrige_formato=true;
				    }

				}
				else
				{
					$corrige_formato=true;
				}
			}
			else
			{
				$corrige_formato=true;
			}//fin else

			if($corrige_formato==true)
			{
			$campo_corregido=$this->corrector_formato_fecha($campo_corregido,false,-2);
			}
			
			
			//valor permitido
			$excede_fecha_corte=$this->diferencia_dias_entre_fechas($campo_corregido,$date_de_corte);
			$es_menor_o_igual_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
			$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
			
			
			$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");

			//comparacion fecha campo actual es inferior a la fecha de nacimiento
			$fecha_nacimiento_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),"1900-12-31");
			$campo_actual_inferior_fecha_nacimiento=$this->diferencia_dias_entre_fechas($campo_corregido,trim($array_fields[9]));
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_nacimiento_es_mayor_a_1900_12_31<0
			&& $fecha_nacimiento_es_mayor_a_1900_12_31!==false
			&& $campo_actual_inferior_fecha_nacimiento>0
			&& $campo_actual_inferior_fecha_nacimiento!==false
			)
			{
				$campo_corregido="1800-01-01";
			}//fin if
			//fin comparacion fecha campo actual es inferior a la fecha de nacimiento

			//limite inferior fecha corte 2 years
			$fecha_corte_menos_2_years="";
			$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=false;
			$array_fecha_corte=explode("-", $date_de_corte);
			if(count($array_fecha_corte)==3)
			{
				$fecha_corte_menos_2_years=(intval($array_fecha_corte[0])-2)."-".$array_fecha_corte[1]."-".$array_fecha_corte[2];
				$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=$this->diferencia_dias_entre_fechas($campo_corregido,$fecha_corte_menos_2_years);
			}//fin if
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_corte_menos_2_years!=""
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years!==false
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years>0
			)
			{
				$campo_corregido="1845-01-01";
			}//fin if
			//fin limite inferior fecha corte 2 years

			if($comparacion_a_1900_12_31<0 
					&& $excede_fecha_corte<0
					&& $comparacion_a_1900_12_31!==false
					&& $excede_fecha_corte!==false
					)
				{
					$campo_corregido="1800-01-01";
				}//fin comparacion excede la fecha de corte

			if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				)
			{
				if($comparacion_a_1900_12_31>=0
					&& $campo_corregido!="1800-01-01"
					&& $campo_corregido!="1805-01-01"
					&& $campo_corregido!="1810-01-01"
					&& $campo_corregido!="1825-01-01"
					&& $campo_corregido!="1830-01-01"
					&& $campo_corregido!="1835-01-01"
					&& $campo_corregido!="1845-01-01"
					&& $array_fields[10]=="M"
					)
				{
					//c2
				    $campo_corregido="1845-01-01";
				}
				else if($comparacion_a_1900_12_31>=0
					&& $campo_corregido!="1800-01-01"
					&& $campo_corregido!="1805-01-01"
					&& $campo_corregido!="1810-01-01"
					&& $campo_corregido!="1825-01-01"
					&& $campo_corregido!="1830-01-01"
					&& $campo_corregido!="1835-01-01"
					&& $campo_corregido!="1845-01-01"
					&& ($edad<10 || $edad>=60)
					    && $array_fields[10]=="F")
				{
					//c3
				    $campo_corregido="1845-01-01";
				}
				else if($comparacion_a_1900_12_31>=0
					&& $campo_corregido!="1800-01-01"
					&& $campo_corregido!="1805-01-01"
					&& $campo_corregido!="1810-01-01"
					&& $campo_corregido!="1825-01-01"
					&& $campo_corregido!="1830-01-01"
					&& $campo_corregido!="1835-01-01"
					&& $campo_corregido!="1845-01-01"
					&& ($edad>=10 && $edad<60)
					&& $array_fields[10]=="F" 
					&& ($array_fields[49]!="1845-01-01")
					)
				{
					//c4
				    $campo_corregido="1845-01-01";
				}		
				else if($comparacion_a_1900_12_31>=0
					&& $campo_corregido!="1800-01-01"
					&& $campo_corregido!="1805-01-01"
					&& $campo_corregido!="1810-01-01"
					&& $campo_corregido!="1825-01-01"
					&& $campo_corregido!="1830-01-01"
					&& $campo_corregido!="1835-01-01"
					&& $campo_corregido!="1845-01-01"
					&& ($edad>=10 && $edad<60)
					&& $array_fields[10]=="F" 
					&& $array_fields[14]!="1"
					&& ($array_fields[49]=="1845-01-01")
					)
				{
					//c5
				    $campo_corregido="1845-01-01";
				}
				else if($comparacion_a_1900_12_31>=0
					&& $campo_corregido!="1800-01-01"
					&& $campo_corregido!="1805-01-01"
					&& $campo_corregido!="1810-01-01"
					&& $campo_corregido!="1825-01-01"
					&& $campo_corregido!="1830-01-01"
					&& $campo_corregido!="1835-01-01"
					&& $campo_corregido!="1845-01-01"
					&& ($edad>=10 && $edad<60)
					&& $array_fields[10]=="F" 
					&& $array_fields[14]=="1"
					&& ( $array_fields[79]=="1" || $array_fields[79]=="2" ||$array_fields[79]=="22")
					&& ($array_fields[49]=="1845-01-01")
					)
				{
					//c6
				    $campo_corregido="1800-01-01";
				}
				else if($comparacion_a_1900_12_31>=0
					&& $campo_corregido!="1800-01-01"
					&& $campo_corregido!="1805-01-01"
					&& $campo_corregido!="1810-01-01"
					&& $campo_corregido!="1825-01-01"
					&& $campo_corregido!="1830-01-01"
					&& $campo_corregido!="1835-01-01"
					&& $campo_corregido!="1845-01-01"
					&& ($edad>=10 && $edad<60)
					&& $array_fields[10]=="F" 
					&& $array_fields[14]=="1"
					&& ( $array_fields[79]=="0")
					&& ($array_fields[49]=="1845-01-01")
					)
				{
					//c6
				    $campo_corregido="1835-01-01";
				}
				else if($comparacion_a_1900_12_31>=0
					&& $campo_corregido!="1800-01-01"
					&& $campo_corregido!="1805-01-01"
					&& $campo_corregido!="1810-01-01"
					&& $campo_corregido!="1825-01-01"
					&& $campo_corregido!="1830-01-01"
					&& $campo_corregido!="1835-01-01"
					&& $campo_corregido!="1845-01-01"
					)
				{
					//c8
				    $campo_corregido="1845-01-01";
				}
			}//fin else
			//$campo_corregido=$this->corrector_valor_permitido_fecha_criterio_002($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
			//calidad con fecha actividad
			$nueva_edad_year_actual=-1;
			$nueva_edad_meses_actual=-1;
			$nueva_edad_dias_actual=-1;
			    
			$array_fecha_nacimiento=explode("-",$array_fields[9]);
			$fecha_campo_actual=explode("-",$campo_corregido);
			if(count($fecha_campo_actual)==3 && checkdate($fecha_campo_actual[1],$fecha_campo_actual[2],$fecha_campo_actual[0]))
			{
			    //echo "<script>alert('entro');</script>";
			    
			    $array_calc_edad_actual=$this->edad_years_months_days($array_fecha_nacimiento[2]."-".$array_fecha_nacimiento[1]."-".$array_fecha_nacimiento[0],$fecha_campo_actual[2]."-".$fecha_campo_actual[1]."-".$fecha_campo_actual[0]);
			    $nueva_edad_year_actual=intval($array_calc_edad_actual['y']);
			    $nueva_edad_meses_actual=(intval($array_calc_edad_actual['y'])*12)+$array_calc_edad_actual['m'];
			    $nueva_edad_dias_actual=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),trim($array_fields[$numero_campo_a_corregir]));
			    
			    //echo "<script>alert(' EDAD DIAS $nueva_edad_dias_actual FN ".trim($array_fields[9])." FA ".trim($array_fields[$numero_campo_a_corregir])."  NCA $numero_campo_a_corregir');</script>";
			}
			$es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
			$es_menor_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");

			$c49es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas($array_fields[49],"1900-12-31");
			//calidad
			if($campo_corregido!="1845-01-01"
			    && $array_fields[10]=="M"
			)
			{
				//c1
			    $campo_corregido="1845-01-01";
			}
			else if($es_mayor_a_1900_12_31<0
				&& $array_fields[10]=="F"
				&& ($nueva_edad_year_actual<10 || $nueva_edad_year_actual>=60)
			)
			{
				//c2
			    $campo_corregido="1845-01-01";
			}
			else if($array_fields[10]=="F"
				&& ($edad<10 || $edad>=60)
				&& (trim($array_fields[78])=="1800-01-01"
						|| trim($array_fields[78])=="1805-01-01"
						|| trim($array_fields[78])=="1810-01-01"
						|| trim($array_fields[78])=="1825-01-01"
						|| trim($array_fields[78])=="1830-01-01"
						|| trim($array_fields[78])=="1835-01-01"
						)
			)
			{
				//c3
			    $campo_corregido="1845-01-01";
			}
			else if($es_mayor_a_1900_12_31<0
				&& ($nueva_edad_year_actual>=10 && $nueva_edad_year_actual<60)
				&& $array_fields[10]=="F" 
				&& ($c49es_mayor_a_1900_12_31<0 || $array_fields[49]=="1800-01-01")
				)
			{
				//c4
			    $campo_corregido="1845-01-01";
			}		
			else if(
				(trim($array_fields[78])=="1800-01-01"
						|| trim($array_fields[78])=="1805-01-01"
						|| trim($array_fields[78])=="1810-01-01"
						|| trim($array_fields[78])=="1825-01-01"
						|| trim($array_fields[78])=="1830-01-01"
						|| trim($array_fields[78])=="1835-01-01"
						)
				&& ($edad>=10 && $edad<60)
				&& $array_fields[10]=="F" 
				&& ($c49es_mayor_a_1900_12_31<0 || $array_fields[49]=="1800-01-01")
				)
			{
				//c5
			    $campo_corregido="1845-01-01";
			}
			else if($es_mayor_a_1900_12_31<0
				&& ($nueva_edad_year_actual>=10 && $nueva_edad_year_actual<60)
				&& $array_fields[10]=="F" 
				&& $array_fields[14]!="1"
				&& ($array_fields[49]=="1845-01-01")
				)
			{
				//c6
			    $campo_corregido="1845-01-01";
			}
			else if(
				(trim($array_fields[78])=="1800-01-01"
						|| trim($array_fields[78])=="1805-01-01"
						|| trim($array_fields[78])=="1810-01-01"
						|| trim($array_fields[78])=="1825-01-01"
						|| trim($array_fields[78])=="1830-01-01"
						|| trim($array_fields[78])=="1835-01-01"
						)
				&& ($edad>=10 && $edad<60)
				&& $array_fields[10]=="F" 
				&& $array_fields[14]!="1"
				&& ($array_fields[49]=="1845-01-01")
				)
			{
				//c7
			    $campo_corregido="1845-01-01";
			}
			else if(
				$es_mayor_a_1900_12_31>=0
				&& $es_mayor_a_1900_12_31!==false
				&& trim($array_fields[78])!="1800-01-01"
				&& ($edad>=10 && $edad<60)
				&& $array_fields[10]=="F" 
				&& $array_fields[14]=="1"
				&& ($array_fields[79]=="1" || $array_fields[79]=="2" || $array_fields[79]=="22")
				&& ($array_fields[49]=="1845-01-01")
				)
			{
				//c8
			    $campo_corregido="1800-01-01";
			}
			else if(
				$es_mayor_a_1900_12_31<0
				&& ($nueva_edad_year_actual>=10 && $nueva_edad_year_actual<60)
				&& $array_fields[10]=="F" 
				&& $array_fields[14]=="1"
				&& ($array_fields[79]=="0")
				&& ($array_fields[49]=="1845-01-01")
				)
			{
				//c9
			    $campo_corregido="1835-01-01";
			}
			else if(
				trim($array_fields[78])=="1800-01-01"
				&& ($edad>=10 && $edad<60)
				&& $array_fields[10]=="F" 
				&& $array_fields[14]=="1"
				&& ($array_fields[79]=="0")
				&& ($array_fields[49]=="1845-01-01")
				)
			{
				//c10
			    $campo_corregido="1835-01-01";
			}

			
			
			

		
	     }//fin fase 1
	    }//revisado c78
	    
	    //campo a corregir es 79
	    if($numero_campo_a_corregir==79)
	    {
	     if($fase_correccion==0)
	     {
			//campo en blanco
			$fecha_calendario_c78=$this->diferencia_dias_entre_fechas(trim($array_fields[78]),"1900-12-31");
			$c49es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[49]),"1900-12-31");
			
			if($campo_corregido=="")
			{
				if($campo_corregido=="" && $array_fields[10]=="M")
				{
					//c1
				    $campo_corregido="0";
				}
				else if($campo_corregido=="" && ($edad<10 || $edad>=60)
					    && $array_fields[10]=="F")
				{
					//c2
				    $campo_corregido="0";
				}
				else if($campo_corregido=="" 
					&& ($edad>=10 && $edad<60) //en el excel dice menor de 10 o mayor igual de 60 para las siguientes lo cual es un error
					    && $array_fields[10]=="F"
					&& $c49es_mayor_a_1900_12_31<0
					)
				{
					//c3
				    $campo_corregido="0";
				}
				else if($campo_corregido=="" 
					&& ($edad>=10 && $edad<60) //en el excel dice menor de 10 o mayor igual de 60 para las siguientes lo cual es un error
					    && $array_fields[10]=="F"
					&& ($array_fields[49]=="1800-01-01")
					)
				{
					//c4
				    $campo_corregido="0";
				}
				else if($campo_corregido=="" 
					&& ($edad>=10 && $edad<60)
					    && $array_fields[10]=="F"
					    && $array_fields[14]!="1"
					    && ($array_fields[49]=="1845-01-01")
					    )
				{
					//c5
				    $campo_corregido="0";
				}
				else if($campo_corregido==""
					&& ($edad>=10 && $edad<60)
					    && $array_fields[10]=="F"
					    && $array_fields[14]=="1"
					&& ($array_fields[49]=="1845-01-01")
					)
				{
					//c6
				    $campo_corregido="22";
				}					
				else if($campo_corregido=="")
				{
					//c7
				    $campo_corregido="0";
				}
			}//fin if
			
			
			//valor permitido
			if($campo_corregido!="0"
			    && $campo_corregido!="1"
			    && $campo_corregido!="2"
			    && $campo_corregido!="22"
			    )
			{
			
				if($campo_corregido!="0"
				    && $campo_corregido!="1"
				    && $campo_corregido!="2"
				    && $campo_corregido!="22"
					&& $array_fields[10]=="M"
					)
				{
					//c1
				    $campo_corregido="0";
				}
				else if($campo_corregido!="0"
				    && $campo_corregido!="1"
				    && $campo_corregido!="2"
				    && $campo_corregido!="22"
					&& ($edad<10 || $edad>=60)
					    && $array_fields[10]=="F")
				{
					//c2
				    $campo_corregido="0";
				}
				else if($campo_corregido!="0"
				    && $campo_corregido!="1"
				    && $campo_corregido!="2"
				    && $campo_corregido!="22"
					&& ($edad>=10 && $edad<60) //en el excel dice menor de 10 o mayor igual de 60 para las siguientes lo cual es un error, lo mismo para el valor permitido y calidad
					    && $array_fields[10]=="F"
					&& $c49es_mayor_a_1900_12_31<0
					)
				{
					//c3
				    $campo_corregido="0";
				}
				else if($campo_corregido!="0"
				    && $campo_corregido!="1"
				    && $campo_corregido!="2"
				    && $campo_corregido!="22"
					&& ($edad>=10 && $edad<60) //en el excel dice menor de 10 o mayor igual de 60 para las siguientes lo cual es un error, lo mismo para el valor permitido y calidad
					    && $array_fields[10]=="F"
					&& ($array_fields[49]=="1800-01-01")
					)
				{
					//c4
				    $campo_corregido="0";
				}
				else if($campo_corregido!="0"
				    && $campo_corregido!="1"
				    && $campo_corregido!="2"
				    && $campo_corregido!="22"
					&& ($edad>=10 && $edad<60)
					    && $array_fields[10]=="F"
					    && $array_fields[14]!="1"
					    && ($array_fields[49]=="1845-01-01")
					    )
				{
					//c5
				    $campo_corregido="0";
				}
				else if($campo_corregido!="0"
				    && $campo_corregido!="1"
				    && $campo_corregido!="2"
				    && $campo_corregido!="22"
					&& ($edad>=10 && $edad<60)
					    && $array_fields[10]=="F"
					    && $array_fields[14]=="1"
					&& ($array_fields[49]=="1845-01-01")
					)
				{
					//c6
				    $campo_corregido="22";
				}
				else if($campo_corregido!="0"
				    && $campo_corregido!="1"
				    && $campo_corregido!="2"
				    && $campo_corregido!="22"
				    )
				{
				    $campo_corregido="0";
				}
			}//fin if
			//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_018($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
	        //calidad c79
	        $fecha_calendario_c78=$this->diferencia_dias_entre_fechas(trim($array_fields[78]),"1900-12-31");
			$c49es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[49]),"1900-12-31");

			$fecha_calendario_c78=$this->diferencia_dias_entre_fechas(trim($array_fields[78]),"1900-12-31");
		        if($array_fields[10]=="M"
			    && $campo_corregido!="0"
			)
			{
				//c1
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0" 
				&& ($edad<10 || $edad>=60)
				    && $array_fields[10]=="F"
				    )
			{
				//c2
			    $campo_corregido="0";
			}
			else if(
				$campo_corregido!="0"
				&& ($edad>=10 && $edad<60) //en el excel dice menor de 10 o mayor igual de 60 para las siguientes lo cual es un error
				    && $array_fields[10]=="F"
				&& $c49es_mayor_a_1900_12_31<0
			)
			{
				//c3
			    $campo_corregido="0";
			}	
			else if(
				$campo_corregido!="0"
				&& ($edad>=10 && $edad<60) //en el excel dice menor de 10 o mayor igual de 60 para las siguientes lo cual es un error
				    && $array_fields[10]=="F"
				&& ($array_fields[49]=="1800-01-01")
			)
			{
				//c4
			    $campo_corregido="0";
			}	
			else if($campo_corregido!="0"
				&& ($edad>=10 && $edad<60)
				    && $array_fields[10]=="F"
				    && $array_fields[14]!="1"
				    && ($array_fields[49]=="1845-01-01")
				    )
			{
				//c5
			    $campo_corregido="0";
			}
			else if($campo_corregido!="1"
				&& $campo_corregido!="2"
				&& $campo_corregido!="22"				
				&& ($edad>=10 && $edad<60)
				    && $array_fields[10]=="F"
				    && $array_fields[14]=="1"
				    && ($array_fields[49]=="1845-01-01")
				)
			{
				//c6
			    $campo_corregido="22";
			}
			
	     }//fin fase 1
	    }//fin campo 79
	    
	    
	    //campo a corregir es 103
	    if($numero_campo_a_corregir==103)
	    {
	    	$fecha_calendario_contra_campo_actual=$this->diferencia_dias_entre_fechas(trim($array_fields[103]),"1900-12-31");
		     if($fase_correccion==0)
		     {
		     	if($campo_corregido=="")
				{
					//campo en blanco
					if($campo_corregido==""	
						&& $array_fields[104]=="0" 
						&& $array_fields[10]=="M"
						)
					{
					    //criterio 1
					    $campo_corregido="1845-01-01";
					}						
					else if($campo_corregido==""
						&& $array_fields[104]=="0"
						&& intval($array_fields[14])!=1
						&& trim($array_fields[10])=="F"
						&& ($edad!=10
						   && $edad!=11
						   && $edad!=12
						   && $edad!=13
						   )
						)
					{
					    //criterio 2
					    $campo_corregido="1845-01-01";
					}
					else if($campo_corregido==""	
						&& $array_fields[104]!="0"	
						)
					{
					    //criterio 3
					    $campo_corregido="1800-01-01";
					}
					else if($campo_corregido==""	
						&& $array_fields[104]=="0" 
						&& $array_fields[14]=="1"
						&& $array_fields[10]=="F"
						)
					{
					    //criterio 4
					    $campo_corregido="1835-01-01";
					}					
					else if($campo_corregido==""
						&& $array_fields[104]=="0" 
						&& $array_fields[14]!="1" 
						&& $array_fields[10]=="F"
						
						&& ($edad==10
						    || $edad==11
						    || $edad==12
						    || $edad==13
						    )
						)
					{
					    //criterio 5
					    $campo_corregido="1835-01-01";
					}	
					else if($campo_corregido=="")
					{
					    //criterio 9
					    $campo_corregido="1845-01-01";
					}
				}//fin else if
			
			
			
				//formato
				$corrige_formato=false;
				$array_fecha_campo_actual=explode("-", $campo_corregido);
				if(count($array_fecha_campo_actual)==3)
				{
					if(ctype_digit($array_fecha_campo_actual[0]) && ctype_digit($array_fecha_campo_actual[1]) && ctype_digit($array_fecha_campo_actual[2]))
					{
						//checkdate mm-dd-aaaa -> aaaa-mm-dd ?
						if(checkdate($array_fecha_campo_actual[1],$array_fecha_campo_actual[2],$array_fecha_campo_actual[0])
					       && intval($array_fecha_campo_actual[0])>=32)
					    {
					    	//no se corrige formato
					    }
					    else
					    {
					    	$corrige_formato=true;
					    }

					}
					else
					{
						$corrige_formato=true;
					}
				}
				else
				{
					$corrige_formato=true;
				}//fin else

				if($corrige_formato==true)
				{
					$campo_corregido=$this->corrector_formato_fecha($campo_corregido,false,-2);
				}
				
				
				//valor permitido
				$excede_fecha_corte=$this->diferencia_dias_entre_fechas($campo_corregido,$date_de_corte);
				$es_menor_o_igual_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
				$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
				
				
				$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");

				//comparacion fecha campo actual es inferior a la fecha de nacimiento
			$fecha_nacimiento_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),"1900-12-31");
			$campo_actual_inferior_fecha_nacimiento=$this->diferencia_dias_entre_fechas($campo_corregido,trim($array_fields[9]));
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_nacimiento_es_mayor_a_1900_12_31<0
			&& $fecha_nacimiento_es_mayor_a_1900_12_31!==false
			&& $campo_actual_inferior_fecha_nacimiento>0
			&& $campo_actual_inferior_fecha_nacimiento!==false
			)
			{
				$campo_corregido="1800-01-01";
			}//fin if
			//fin comparacion fecha campo actual es inferior a la fecha de nacimiento

			//limite inferior fecha corte 2 years
			$fecha_corte_menos_2_years="";
			$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=false;
			$array_fecha_corte=explode("-", $date_de_corte);
			if(count($array_fecha_corte)==3)
			{
				$fecha_corte_menos_2_years=(intval($array_fecha_corte[0])-2)."-".$array_fecha_corte[1]."-".$array_fecha_corte[2];
				$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=$this->diferencia_dias_entre_fechas($campo_corregido,$fecha_corte_menos_2_years);
			}//fin if
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_corte_menos_2_years!=""
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years!==false
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years>0
			)
			{
				$campo_corregido="1845-01-01";
			}//fin if
			//fin limite inferior fecha corte 2 years

				if($comparacion_a_1900_12_31<0 
					&& $excede_fecha_corte<0
					&& $comparacion_a_1900_12_31!==false
					&& $excede_fecha_corte!==false
					)
				{
					$campo_corregido="1800-01-01";
				}//fin comparacion excede la fecha de corte
				
				if($comparacion_a_1900_12_31>=0
					&& $campo_corregido!="1800-01-01"
					&& $campo_corregido!="1805-01-01"
					&& $campo_corregido!="1810-01-01"
					&& $campo_corregido!="1825-01-01"
					&& $campo_corregido!="1830-01-01"
					&& $campo_corregido!="1835-01-01"
					&& $campo_corregido!="1845-01-01")
				{
					if($comparacion_a_1900_12_31>=0
						&& $campo_corregido!="1800-01-01"
						&& $campo_corregido!="1805-01-01"
						&& $campo_corregido!="1810-01-01"
						&& $campo_corregido!="1825-01-01"
						&& $campo_corregido!="1830-01-01"
						&& $campo_corregido!="1835-01-01"
						&& $campo_corregido!="1845-01-01"
						&& $array_fields[104]=="0" 
						&& $array_fields[10]=="M"
						)
					{
					    //criterio 1
					    $campo_corregido="1845-01-01";
					}						
					else if($comparacion_a_1900_12_31>=0
						&& $campo_corregido!="1800-01-01"
						&& $campo_corregido!="1805-01-01"
						&& $campo_corregido!="1810-01-01"
						&& $campo_corregido!="1825-01-01"
						&& $campo_corregido!="1830-01-01"
						&& $campo_corregido!="1835-01-01"
						&& $campo_corregido!="1845-01-01"
						&& $array_fields[104]=="0"
						&& intval($array_fields[14])!=1
						&& trim($array_fields[10])=="F"
						&& ($edad!=10
						   && $edad!=11
						   && $edad!=12
						   && $edad!=13
						   )
						)
					{
					    //criterio 2
					    $campo_corregido="1845-01-01";
					}
					else if($comparacion_a_1900_12_31>=0
						&& $campo_corregido!="1800-01-01"
						&& $campo_corregido!="1805-01-01"
						&& $campo_corregido!="1810-01-01"
						&& $campo_corregido!="1825-01-01"
						&& $campo_corregido!="1830-01-01"
						&& $campo_corregido!="1835-01-01"
						&& $campo_corregido!="1845-01-01"
						&& $array_fields[104]!="0"	
						)
					{
					    //criterio 3
					    $campo_corregido="1800-01-01";
					}
					else if($comparacion_a_1900_12_31>=0
						&& $campo_corregido!="1800-01-01"
						&& $campo_corregido!="1805-01-01"
						&& $campo_corregido!="1810-01-01"
						&& $campo_corregido!="1825-01-01"
						&& $campo_corregido!="1830-01-01"
						&& $campo_corregido!="1835-01-01"
						&& $campo_corregido!="1845-01-01"
						&& $array_fields[104]=="0" 
						&& $array_fields[14]=="1"
						&& $array_fields[10]=="F"
						)
					{
					    //criterio 4
					    $campo_corregido="1835-01-01";
					}					
					else if($comparacion_a_1900_12_31>=0
						&& $campo_corregido!="1800-01-01"
						&& $campo_corregido!="1805-01-01"
						&& $campo_corregido!="1810-01-01"
						&& $campo_corregido!="1825-01-01"
						&& $campo_corregido!="1830-01-01"
						&& $campo_corregido!="1835-01-01"
						&& $campo_corregido!="1845-01-01"
						&& $array_fields[104]=="0" 
						&& $array_fields[14]!="1" 
						&& $array_fields[10]=="F"
						
						&& ($edad==10
						    || $edad==11
						    || $edad==12
						    || $edad==13
						    )
						)
					{
					    //criterio 5
					    $campo_corregido="1835-01-01";
					}	
					else if($comparacion_a_1900_12_31>=0
						&& $campo_corregido!="1800-01-01"
						&& $campo_corregido!="1805-01-01"
						&& $campo_corregido!="1810-01-01"
						&& $campo_corregido!="1825-01-01"
						&& $campo_corregido!="1830-01-01"
						&& $campo_corregido!="1835-01-01"
						&& $campo_corregido!="1845-01-01")
					{
					    //criterio 9
					    $campo_corregido="1845-01-01";
					}
				}//fin else if	
		     
			//$campo_corregido=$this->corrector_valor_permitido_fecha_criterio_002($campo_corregido);
		     }
		     else if($fase_correccion==1)
		     {
			//calidad con fecha actividad
			$nueva_edad_year_actual=-1;
			$nueva_edad_meses_actual=-1;
			$nueva_edad_dias_actual=-1;
			    
			$array_fecha_nacimiento=explode("-",$array_fields[9]);
			$fecha_campo_actual=explode("-",$campo_corregido);
			if(count($fecha_campo_actual)==3 && checkdate($fecha_campo_actual[1],$fecha_campo_actual[2],$fecha_campo_actual[0]))
			{
			    //echo "<script>alert('entro');</script>";
			    
			    $array_calc_edad_actual=$this->edad_years_months_days($array_fecha_nacimiento[2]."-".$array_fecha_nacimiento[1]."-".$array_fecha_nacimiento[0],$fecha_campo_actual[2]."-".$fecha_campo_actual[1]."-".$fecha_campo_actual[0]);
			    $nueva_edad_year_actual=intval($array_calc_edad_actual['y']);
			    $nueva_edad_meses_actual=(intval($array_calc_edad_actual['y'])*12)+$array_calc_edad_actual['m'];
			    $nueva_edad_dias_actual=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),trim($array_fields[$numero_campo_a_corregir]));
			    
			    //echo "<script>alert(' EDAD DIAS $nueva_edad_dias_actual FN ".trim($array_fields[9])." FA ".trim($array_fields[$numero_campo_a_corregir])."  NCA $numero_campo_a_corregir');</script>";
			}
			$es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
			$es_menor_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
			
			
			//calidad c103
			if($campo_corregido!="1845-01-01"	
				&& $array_fields[104]=="0" 
				&& $array_fields[10]=="M"
				)
			{
			    //criterio 1
			    $campo_corregido="1845-01-01";
			}						
			else if($es_mayor_a_1900_12_31<0
				&& $array_fields[104]=="0"
				&& intval($array_fields[14])!=1
				&& trim($array_fields[10])=="F"
				&& ($nueva_edad_year_actual!=10
				   && $nueva_edad_year_actual!=11
				   && $nueva_edad_year_actual!=12
				   && $nueva_edad_year_actual!=13
				   )
				)
			{
			    //criterio 2
			    $campo_corregido="1845-01-01";
			}
			else if(

				(
					$campo_corregido=="1800-01-01"
					|| $campo_corregido=="1805-01-01"
				    || $campo_corregido=="1810-01-01"
				    || $campo_corregido=="1825-01-01"
				    || $campo_corregido=="1830-01-01"
				    || $campo_corregido=="1835-01-01"
			    
			    )
				&& $array_fields[104]=="0"
				&& intval($array_fields[14])!=1
				&& trim($array_fields[10])=="F"
				&& ($edad!=10
				   && $edad!=11
				   && $edad!=12
				   && $edad!=13
				   )
				)
			{
			    //criterio 3
			    $campo_corregido="1845-01-01";
			}
			else if(
				($campo_corregido=="1805-01-01"
			    || $campo_corregido=="1810-01-01"
			    || $campo_corregido=="1825-01-01"
			    || $campo_corregido=="1830-01-01"
			    || $campo_corregido=="1835-01-01"
			    || $campo_corregido=="1845-01-01"
			    )		
				&& $array_fields[104]!="0"	
			)//fin condicion
			{
			    //criterio 4
			    $campo_corregido="1800-01-01";
			}
			else if(
				($es_mayor_a_1900_12_31<0 || $campo_corregido=="1800-01-01")
				&& $array_fields[104]=="0" 
				&& $array_fields[14]=="1"
				&& $array_fields[10]=="F"
				)
			{
			    //criterio 5
			    $campo_corregido="1835-01-01";
			}					
			else if($es_mayor_a_1900_12_31<0
				&& $array_fields[104]=="0" 
				&& $array_fields[14]!="1" 
				&& $array_fields[10]=="F"
				
				&& ($nueva_edad_year_actual==10
				    || $nueva_edad_year_actual==11
				    || $nueva_edad_year_actual==12
				    || $nueva_edad_year_actual==13
				    )
				)
			{
			    //criterio 6
			    $campo_corregido="1835-01-01";
			}	
			else if(
				($campo_corregido=="1800-01-01" 
					|| $campo_corregido=="1805-01-01"
					|| $campo_corregido=="1810-01-01"
					|| $campo_corregido=="1825-01-01"
					|| $campo_corregido=="1830-01-01"
					|| $campo_corregido=="1845-01-01")
				&& $array_fields[104]=="0" 
				&& $array_fields[14]!="1" 
				&& $array_fields[10]=="F"
				
				&& ($edad==10
				    || $edad==11
				    || $edad==12
				    || $edad==13
				    )
				)//fin condicion
			{
			    //criterio 7
			    $campo_corregido="1835-01-01";
			}//fin calidad
			else if(
				($campo_corregido=="1800-01-01" 
					|| $campo_corregido=="1805-01-01"
					|| $campo_corregido=="1810-01-01"
					|| $campo_corregido=="1825-01-01"
					|| $campo_corregido=="1830-01-01"
					|| $campo_corregido=="1845-01-01")
				&& $array_fields[104]=="0" 
				&& $array_fields[14]=="1" 
				&& $array_fields[10]=="F"
				)//fin condicion
			{
			    //criterio 8
			    $campo_corregido="1835-01-01";
			}//fin calidad

			
			
			
		     }//fin fase 1
	    }//fin campo 103
	    
	    //campo a corregir es 104
	    if($numero_campo_a_corregir==104)
	    {
		    if($fase_correccion==0)
		    {
				//echo "<script>alert('antes 104 fase 0 $campo_corregido');</script>";
				//campo en blanco
				$fecha_calendario_c103=$this->diferencia_dias_entre_fechas(trim($array_fields[103]),"1900-12-31");
				if($campo_corregido=="")
				{
				    $campo_corregido="0";
				}
				
				//formato mayor de 10 entero menor de diez hata un decimal peude mostrar
				$campo_corregido=trim($campo_corregido);
				$campo_corregido=str_replace(",",".",$campo_corregido);
				$campo_corregido=floatval($campo_corregido);
				if(!ctype_digit($campo_corregido) && is_numeric($campo_corregido))
				{
				    $array_num_decimal=explode(".",trim($campo_corregido));
				    if(count($array_num_decimal)>1)
				    {
					if(strlen($array_num_decimal[0])>1 && strlen($array_num_decimal[1])>1)
					{
					    //$campo_corregido="".round(floatval($campo_corregido),0,PHP_ROUND_HALF_UP);
					    $campo_corregido="".intval($campo_corregido);
					}
					else if(strlen($array_num_decimal[0])==1 && strlen($array_num_decimal[1])>2)
					{
					    $campo_corregido="".round(floatval($campo_corregido),1,PHP_ROUND_HALF_UP);
					}
				    }//fin if hay posiciones decimales
				}//fin formato
				
				
				//valor permitido
				$fecha_calendario_c103=$this->diferencia_dias_entre_fechas(trim($array_fields[103]),"1900-12-31");

				if($campo_corregido!="0"
				    && (floatval($campo_corregido)<1.5 || floatval($campo_corregido)>25 || is_numeric($campo_corregido)==false)
				    && ( trim($array_fields[103])=="1800-01-01"
				    	|| trim($array_fields[103])=="1805-01-01"
		    			|| trim($array_fields[103])=="1810-01-01"
		    			|| trim($array_fields[103])=="1825-01-01"
		    			|| trim($array_fields[103])=="1830-01-01"
		    			|| trim($array_fields[103])=="1835-01-01"
		    			|| trim($array_fields[103])=="1845-01-01"
		    		 )
				)
				{
				    $campo_corregido="0";
				}
				else if($campo_corregido!="0"
				    && ( (floatval($campo_corregido)>0 && floatval($campo_corregido)<1.5) || is_numeric($campo_corregido)==false)
				    && $fecha_calendario_c103<0
				)
				{
				    $campo_corregido="1.5";
				}
				else if($campo_corregido!="0"
				    && ( (floatval($campo_corregido)>25 && floatval($campo_corregido)<30) || is_numeric($campo_corregido)==false)
				    && $fecha_calendario_c103<0
				)
				{
				    $campo_corregido="25";
				}
				else if($campo_corregido!="0"
				    && ( (floatval($campo_corregido)<1.5 || floatval($campo_corregido)>25) || is_numeric($campo_corregido)==false)
				)
				{
				    $campo_corregido="0";
				}
				//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_033($campo_corregido);
				
				//echo "<script>alert('despues 104 fase 0 $campo_corregido');</script>";
		    }//fin fase 0
		    else if($fase_correccion==1)
		    {
				//echo "<script>alert('antes 104 fase 1 $campo_corregido');</script>";
				//calidad no aplica
				$fecha_calendario_c103=$this->diferencia_dias_entre_fechas(trim($array_fields[103]),"1900-12-31");
				
				if($campo_corregido!="0"
				    && (trim($array_fields[103])=="1805-01-01"
		    			|| trim($array_fields[103])=="1810-01-01"
		    			|| trim($array_fields[103])=="1825-01-01"
		    			|| trim($array_fields[103])=="1830-01-01"
		    			|| trim($array_fields[103])=="1835-01-01"
		    			|| trim($array_fields[103])=="1845-01-01"
		    		 )
				)
				{
				    $campo_corregido="0";
				}
				
				//echo "<script>alert('despues 104 fase 1 $campo_corregido');</script>";
	     	}//fin fase
	    }//fin campo 104
	    
	    //FIN CAMPOS ASOCIADOS AL RIESGO DE GESTACION
	    
	    //CAMPOS CANCER DE CERVIX
	    
	    //campo a corregir es 26
	    if($numero_campo_a_corregir==26)
	    {
	    	//echo "valores asociados al campo 26 ($campo_corregido), campo 10 sexo  ".$array_fields[10]." <br>";
	     if($fase_correccion==0)
	     {
			//campo en blanco
			if($campo_corregido=="")
			{
				if($campo_corregido==""  
					&& $array_fields[10]=="M"
					)
				{
					//c1
				    $campo_corregido="0";
				}
				else if($campo_corregido=="")
				{
					//c6
				    $campo_corregido="21";
				}
				/*
				//anulada para coomeva
				else if($campo_corregido==""
					&& $array_fields[10]=="F"
					&& ($array_fields[88]=="1"
						|| $array_fields[88]=="2"
						|| $array_fields[88]=="3"
						|| $array_fields[88]=="4"
						|| $array_fields[88]=="5"
						|| $array_fields[88]=="6"
						|| trim($array_fields[88])=="7"
			    		|| trim($array_fields[88])=="8"
			    		|| trim($array_fields[88])=="9"
						|| $array_fields[88]=="10"
						|| $array_fields[88]=="11"
						|| $array_fields[88]=="12"
						|| $array_fields[88]=="13"
						|| $array_fields[88]=="14"
						|| $array_fields[88]=="15"
						|| $array_fields[88]=="16"
						)
					)
				{
					//c2
				    $campo_corregido="1";
				}
				else if($campo_corregido==""
					&& $array_fields[10]=="F"
					&& (
						 $array_fields[94]=="4"
						|| $array_fields[94]=="5"
						|| $array_fields[94]=="6"
						)
					)
				{
					//c3
				    $campo_corregido="1";
				}
				else if($campo_corregido==""
					&& $array_fields[10]=="F"
					&& ($array_fields[88]=="17")
					)
				{
					//c4
				    $campo_corregido="2";
				}
				else if($campo_corregido==""
					&& $array_fields[10]=="F"
					
					)
				{
					//c5
				    $campo_corregido="21";
				}
				else if($campo_corregido=="")
				{
					//c6
				    $campo_corregido="21";
				}
				else if($campo_corregido=="")
				{
					//c6
				    $campo_corregido="21";
				}
				*/
			}//fin if

		
			//valor permitido
		
			if($campo_corregido!="0"
				&& $campo_corregido!="1"
				&& $campo_corregido!="2"
				&& $campo_corregido!="21"
				)
			{
			
				if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="21" 
					&& $array_fields[10]=="M"
					)
				{
					//c1
				    $campo_corregido="0";
				}
				else if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="21"
					)
				{
					//c6
				    $campo_corregido="21";
				}
				/*
				//anulada para coomeva
				else if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="21"
					&& $array_fields[10]=="F"
					&& ($array_fields[88]=="1"
						|| $array_fields[88]=="2"
						|| $array_fields[88]=="3"
						|| $array_fields[88]=="4"
						|| $array_fields[88]=="5"
						|| $array_fields[88]=="6"
						|| trim($array_fields[88])=="7"
			    		|| trim($array_fields[88])=="8"
			    		|| trim($array_fields[88])=="9"
						|| $array_fields[88]=="10"
						|| $array_fields[88]=="11"
						|| $array_fields[88]=="12"
						|| $array_fields[88]=="13"
						|| $array_fields[88]=="14"
						|| $array_fields[88]=="15"
						|| $array_fields[88]=="16"
						)
					)
				{
					//c2
				    $campo_corregido="1";
				}
				else if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="21"
					&& $array_fields[10]=="F"
					&& (
						 $array_fields[94]=="4"
						|| $array_fields[94]=="5"
						|| $array_fields[94]=="6"
						)
					)
				{
					//c3
				    $campo_corregido="1";
				}
				else if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="21"
					&& $array_fields[10]=="F"
					&& ($array_fields[88]=="17")
					)
				{
					//c4
				    $campo_corregido="2";
				}
				else if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="21"
					&& $array_fields[10]=="F"
					
					)
				{
					//c5
				    $campo_corregido="21";
				}
				else if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="21"
					)
				{
					//c6
				    $campo_corregido="21";
				}
				*/
			}//fin if
		
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_007($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
			//calidad
			if($campo_corregido!="0"  
					&& $array_fields[10]=="M"
					)
				{
					//c1
				    $campo_corregido="0";
				}
				else if($campo_corregido=="0"
					&& $array_fields[10]=="F"
					
					)
				{
					//c5
				    $campo_corregido="21";
				}//fin calidad
				/*
				anulada para coomeva
				else if($campo_corregido!="1"
					&& $array_fields[10]=="F"
					&& ($array_fields[88]=="1"
						|| $array_fields[88]=="2"
						|| $array_fields[88]=="3"
						|| $array_fields[88]=="4"
						|| $array_fields[88]=="5"
						|| $array_fields[88]=="6"
						|| trim($array_fields[88])=="7"
			    		|| trim($array_fields[88])=="8"
			    		|| trim($array_fields[88])=="9"
						|| $array_fields[88]=="10"
						|| $array_fields[88]=="11"
						|| $array_fields[88]=="12"
						|| $array_fields[88]=="13"
						|| $array_fields[88]=="14"
						|| $array_fields[88]=="15"
						|| $array_fields[88]=="16"
						)
					)
				{
					//c2
				    $campo_corregido="1";
				}
				else if($campo_corregido!="1"
					&& $array_fields[10]=="F"
					&& (
						 $array_fields[94]=="4"
						|| $array_fields[94]=="5"
						|| $array_fields[94]=="6"
						)
					)
				{
					//c3
				    $campo_corregido="1";
				}
				else if($campo_corregido!="2"
					&& $array_fields[10]=="F"
					&& ($array_fields[88]=="17")
					)
				{
					//c4
				    $campo_corregido="2";
				}
				else if($campo_corregido=="0"
					&& $array_fields[10]=="F"
					
					)
				{
					//c5
				    $campo_corregido="21";
				}//fin calidad
				*/

	     	
		
		
	     }//fin fase 1
	    }//revisado
	    
	    //campo a corregir es 86
	    if($numero_campo_a_corregir==86)
	    {
	     if($fase_correccion==0)
	     {
			//campo en blanco
	     	if($campo_corregido=="")
			{
				if($campo_corregido==""  
					&& $array_fields[10]=="M"
					)
				{
				    $campo_corregido="0";
				}
				else if(
					$campo_corregido==""
					&& $edad<=10
					&&  $array_fields[10]=="F"
					)
				{
				    $campo_corregido="0";
				}
				else if(
					$campo_corregido==""
					&& $edad>10
					&& (trim($array_fields[88])!="0")
					&&  $array_fields[10]=="F"

					)
				{
				    $campo_corregido="1";
				}
				else if(
					$campo_corregido==""
					&& $edad>10
					&& (trim($array_fields[88])=="0")
					&&  $array_fields[10]=="F"

					)
				{
				    $campo_corregido="0";
				}
				else if($campo_corregido=="")
				{
				    $campo_corregido="0";
				}
			}//fin if
		
		
		//valor permitido

			if($campo_corregido!="0"
			&& $campo_corregido!="1"
			&& $campo_corregido!="2"
			&& $campo_corregido!="3"
			&& $campo_corregido!="16"
			&& $campo_corregido!="17"
			&& $campo_corregido!="18"
			&& $campo_corregido!="19"
			&& $campo_corregido!="20"
			&& $campo_corregido!="22"
			)
		{
		
			if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="3"
					&& $campo_corregido!="16"
					&& $campo_corregido!="17"
					&& $campo_corregido!="18"
					&& $campo_corregido!="19"
					&& $campo_corregido!="20"
					&& $campo_corregido!="22"  
					&& $array_fields[10]=="M"
					)
				{
				    $campo_corregido="0";
				}
				else if(
					$campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="3"
					&& $campo_corregido!="16"
					&& $campo_corregido!="17"
					&& $campo_corregido!="18"
					&& $campo_corregido!="19"
					&& $campo_corregido!="20"
					&& $campo_corregido!="22"
					&& $edad<=10
					&&  $array_fields[10]=="F"
					)
				{
				    $campo_corregido="0";
				}
				else if(
					$campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="3"
					&& $campo_corregido!="16"
					&& $campo_corregido!="17"
					&& $campo_corregido!="18"
					&& $campo_corregido!="19"
					&& $campo_corregido!="20"
					&& $campo_corregido!="22"
					&& $edad>10
					&& (trim($array_fields[88])!="0")
					&&  $array_fields[10]=="F"

					)
				{
				    $campo_corregido="1";
				}
				else if(
					$campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="3"
					&& $campo_corregido!="16"
					&& $campo_corregido!="17"
					&& $campo_corregido!="18"
					&& $campo_corregido!="19"
					&& $campo_corregido!="20"
					&& $campo_corregido!="22"
					&& $edad>10
					&& (trim($array_fields[88])=="0")
					&&  $array_fields[10]=="F"

					)
				{
				    $campo_corregido="0";
				}
				else if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="3"
					&& $campo_corregido!="16"
					&& $campo_corregido!="17"
					&& $campo_corregido!="18"
					&& $campo_corregido!="19"
					&& $campo_corregido!="20"
					&& $campo_corregido!="22"
					)
				{
				    $campo_corregido="0";
				}
		}//fin if valor permitido
		
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_012($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
			//calidad
			if($campo_corregido!="0"  
					&& $array_fields[10]=="M"
					)
				{
				    $campo_corregido="0";
				}
				else if(
					$campo_corregido!="0"
					&& $edad<=10
					&&  $array_fields[10]=="F"
					)
				{
				    $campo_corregido="0";
				}
				else if(
					$campo_corregido!="1"
					&& $edad>10
					&& (trim($array_fields[88])!="0")
					&&  $array_fields[10]=="F"

					)
				{
				    $campo_corregido="1";
				}
				else if(
					($campo_corregido=="1" || $campo_corregido=="22")
					&& $edad>10
					&& (trim($array_fields[88])=="0")
					&&  $array_fields[10]=="F"

					)
				{
				    $campo_corregido="0";
				}//fin calidad
			
	     }//fin if
	    }//revisado c86
	    
	    //campo a corregir es 87
	    if($numero_campo_a_corregir==87)
	    {
	     if($fase_correccion==0)
	     {
			//campo en blanco
	     	if($campo_corregido=="")
			{
				if($campo_corregido=="" && $array_fields[10]=="M")
				{
				    $campo_corregido="1845-01-01";
				}
				else if($campo_corregido==""
					&& $array_fields[10]=="F"
					&& $edad<10)
				{
				    $campo_corregido="1845-01-01";
				}
				else if($campo_corregido==""
					&& $array_fields[10]=="F"
					&& $edad>=10
					&& $array_fields[88]=="0")
				{
				    $campo_corregido="1845-01-01";
				}
				else if($campo_corregido==""
					&& $array_fields[10]=="F"
					&& $edad>=10
					&& $array_fields[88]!="0" && $array_fields[88]!="999"
					)
				{
				    $campo_corregido="1800-01-01";
				}
				else if($campo_corregido==""
					&& $array_fields[10]=="F"
					&& $edad>=10
					&& $array_fields[88]=="999")
				{
				    $campo_corregido="1845-01-01";
				}
				else if($campo_corregido=="")
				{
				    $campo_corregido="1845-01-01";
				}
			}//fin if
		
		//formato
		$corrige_formato=false;
			$array_fecha_campo_actual=explode("-", $campo_corregido);
			if(count($array_fecha_campo_actual)==3)
			{
				if(ctype_digit($array_fecha_campo_actual[0]) && ctype_digit($array_fecha_campo_actual[1]) && ctype_digit($array_fecha_campo_actual[2]))
				{
					//checkdate mm-dd-aaaa -> aaaa-mm-dd ?
					if(checkdate($array_fecha_campo_actual[1],$array_fecha_campo_actual[2],$array_fecha_campo_actual[0])
				       && intval($array_fecha_campo_actual[0])>=32)
				    {
				    	//no se corrige formato
				    }
				    else
				    {
				    	$corrige_formato=true;
				    }

				}
				else
				{
					$corrige_formato=true;
				}
			}
			else
			{
				$corrige_formato=true;
			}//fin else

			if($corrige_formato==true)
			{
				$campo_corregido=$this->corrector_formato_fecha($campo_corregido,false,-2);
			}
		
		//valor permitido
		$excede_fecha_corte=$this->diferencia_dias_entre_fechas($campo_corregido,$date_de_corte);
		$es_menor_o_igual_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
		
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");

		//comparacion fecha campo actual es inferior a la fecha de nacimiento
			$fecha_nacimiento_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),"1900-12-31");
			$campo_actual_inferior_fecha_nacimiento=$this->diferencia_dias_entre_fechas($campo_corregido,trim($array_fields[9]));
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_nacimiento_es_mayor_a_1900_12_31<0
			&& $fecha_nacimiento_es_mayor_a_1900_12_31!==false
			&& $campo_actual_inferior_fecha_nacimiento>0
			&& $campo_actual_inferior_fecha_nacimiento!==false
			)
			{
				$campo_corregido="1800-01-01";
			}//fin if
			//fin comparacion fecha campo actual es inferior a la fecha de nacimiento

			//limite inferior fecha corte 2 years
			$fecha_corte_menos_2_years="";
			$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=false;
			$array_fecha_corte=explode("-", $date_de_corte);
			if(count($array_fecha_corte)==3)
			{
				$fecha_corte_menos_2_years=(intval($array_fecha_corte[0])-2)."-".$array_fecha_corte[1]."-".$array_fecha_corte[2];
				$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=$this->diferencia_dias_entre_fechas($campo_corregido,$fecha_corte_menos_2_years);
			}//fin if
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_corte_menos_2_years!=""
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years!==false
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years>0
			)
			{
				$campo_corregido="1845-01-01";
			}//fin if
			//fin limite inferior fecha corte 2 years

		if($comparacion_a_1900_12_31<0 
					&& $excede_fecha_corte<0
					&& $comparacion_a_1900_12_31!==false
					&& $excede_fecha_corte!==false
					)
				{
					$campo_corregido="1800-01-01";
				}//fin comparacion excede la fecha de corte


		if($comparacion_a_1900_12_31>=0
			&& $campo_corregido!="1800-01-01"
			&& $campo_corregido!="1805-01-01"
			&& $campo_corregido!="1810-01-01"
			&& $campo_corregido!="1825-01-01"
			&& $campo_corregido!="1830-01-01"
			&& $campo_corregido!="1835-01-01"
			&& $campo_corregido!="1845-01-01"
			)
		{
			if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& $array_fields[10]=="M"
				)
			{
			    $campo_corregido="1845-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& $array_fields[10]=="F"
				&& $edad<10
				)
			{
			    $campo_corregido="1845-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& $array_fields[10]=="F"
				&& $edad>=10
				&& $array_fields[88]=="0"
				)
			{
			    $campo_corregido="1845-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& $array_fields[10]=="F"
				&& $edad>=10
				&& $array_fields[88]!="0" && $array_fields[88]!="999"
				)
			{
			    $campo_corregido="1800-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
					&& $array_fields[10]=="F"
					&& $edad>=10
					&& $array_fields[88]=="999")
				{
				    $campo_corregido="1845-01-01";
				}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				)
			{
			    $campo_corregido="1845-01-01";
			}
		}//fin if
	     
		//$campo_corregido=$this->corrector_valor_permitido_fecha_criterio_002($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
		//calidad con fecha actividad
		$nueva_edad_year_actual=-1;
		$nueva_edad_meses_actual=-1;
		$nueva_edad_dias_actual=-1;
		    
		$array_fecha_nacimiento=explode("-",$array_fields[9]);
		$fecha_campo_actual=explode("-",$campo_corregido);
		if(count($fecha_campo_actual)==3 && checkdate($fecha_campo_actual[1],$fecha_campo_actual[2],$fecha_campo_actual[0]))
		{
		    //echo "<script>alert('entro');</script>";
		    
		    $array_calc_edad_actual=$this->edad_years_months_days($array_fecha_nacimiento[2]."-".$array_fecha_nacimiento[1]."-".$array_fecha_nacimiento[0],$fecha_campo_actual[2]."-".$fecha_campo_actual[1]."-".$fecha_campo_actual[0]);
		    $nueva_edad_year_actual=intval($array_calc_edad_actual['y']);
		    $nueva_edad_meses_actual=(intval($array_calc_edad_actual['y'])*12)+$array_calc_edad_actual['m'];
		    $nueva_edad_dias_actual=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),trim($array_fields[$numero_campo_a_corregir]));
		    
		    //echo "<script>alert(' EDAD DIAS $nueva_edad_dias_actual FN ".trim($array_fields[9])." FA ".trim($array_fields[$numero_campo_a_corregir])."  NCA $numero_campo_a_corregir');</script>";
		}
		$es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
		$es_menor_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
		
			//calidad
			if($array_fields[10]=="M" && $campo_corregido!="1845-01-01")
			{
			    $campo_corregido="1845-01-01";
			}
			else if($array_fields[10]=="F"
				&& $nueva_edad_year_actual<10
				&& $es_mayor_a_1900_12_31<0)
			{
			    $campo_corregido="1845-01-01";
			}
			else if($array_fields[10]=="F"
				&& $edad<10
				//&& $es_menor_1845_01_01>0
				&& (trim($array_fields[87])=="1835-01-01"
					  || trim($array_fields[87])=="1830-01-01"
					  || trim($array_fields[87])=="1825-01-01"
					  || trim($array_fields[87])=="1810-01-01"
					  || trim($array_fields[87])=="1805-01-01"
					  || trim($array_fields[87])=="1800-01-01"
					  )
				)
			{
			    $campo_corregido="1845-01-01";
			}
			else if($array_fields[10]=="F"
				&& $nueva_edad_year_actual>=10
				&& $es_mayor_a_1900_12_31<0
				&& $array_fields[88]=="0")
			{
			    $campo_corregido="1845-01-01";
			}
			else if($array_fields[10]=="F"
				&& $edad>=10
				&& (trim($array_fields[87])=="1835-01-01"
					  || trim($array_fields[87])=="1830-01-01"
					  || trim($array_fields[87])=="1825-01-01"
					  || trim($array_fields[87])=="1810-01-01"
					  || trim($array_fields[87])=="1805-01-01"
					  || trim($array_fields[87])=="1800-01-01"
					  )
				&& $array_fields[88]=="0")
			{
			    $campo_corregido="1845-01-01";
			}
			else if($array_fields[10]=="F"
				&& $edad>=10
				&& (trim($array_fields[87])=="1835-01-01"
					  || trim($array_fields[87])=="1830-01-01"
					  || trim($array_fields[87])=="1825-01-01"
					  || trim($array_fields[87])=="1810-01-01"
					  || trim($array_fields[87])=="1805-01-01"
					  || trim($array_fields[87])=="1845-01-01"
					  )
				&& $array_fields[88]!="0" && $array_fields[88]!="999"
				)
			{
			    $campo_corregido="1800-01-01";
			}
			else if($array_fields[10]=="F"
				&& $edad>=10
				&& trim($array_fields[87])=="1800-01-01"
				&& $array_fields[88]=="999")
			{
			    $campo_corregido="1845-01-01";
			}//fin calidad
		
	     }//fin fase 1
	    }//revisado c87
	    
	    //campo a corregir es 88
	    if($numero_campo_a_corregir==88)
	    {
	     if($fase_correccion==0)
	     {

	     	$c87es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[87]),"1900-12-31");
		//campo en blanco
		if($campo_corregido=="" )
		{
			if($campo_corregido=="" && $array_fields[10]=="M")
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido=="" && $array_fields[10]=="F"
				&& $edad<=10)
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido=="" && $array_fields[10]=="F"
				&& $edad>10
				&& ($array_fields[87]=="1845-01-01"
				|| $array_fields[87]=="1835-01-01"
				|| $array_fields[87]=="1830-01-01"
				|| $array_fields[87]=="1825-01-01"
				|| $array_fields[87]=="1810-01-01"
				|| $array_fields[87]=="1805-01-01")
				)
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido=="" && $array_fields[10]=="F"
				&& $edad>10
				&& ($c87es_mayor_a_1900_12_31<0)
				)
			{
			    $campo_corregido="999";
			}
			else if($campo_corregido=="" && $array_fields[10]=="F"
				&& $edad>10
				&& ($array_fields[87]=="1800-01-01")
				)
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido=="" )
			{
			    $campo_corregido="0";
			}
		}//fin if
		
		//valor permitido
		if($campo_corregido!="0"
			&& $campo_corregido!="1"
			&& $campo_corregido!="2"
			&& $campo_corregido!="3"
			&& $campo_corregido!="4"
			&& $campo_corregido!="5"
			&& $campo_corregido!="6"
			&& $campo_corregido!="7"
			&& $campo_corregido!="8"
			&& $campo_corregido!="9"
			&& $campo_corregido!="10"
			&& $campo_corregido!="11"
			&& $campo_corregido!="12"
			&& $campo_corregido!="13"
			&& $campo_corregido!="14"
			&& $campo_corregido!="15"
			&& $campo_corregido!="16"
			&& $campo_corregido!="17"
			&& $campo_corregido!="18"
			&& $campo_corregido!="999"
		    )
		{

			if($campo_corregido!="0"
				&& $campo_corregido!="1"
				&& $campo_corregido!="2"
				&& $campo_corregido!="3"
				&& $campo_corregido!="4"
				&& $campo_corregido!="5"
				&& $campo_corregido!="6"
				&& $campo_corregido!="7"
				&& $campo_corregido!="8"
				&& $campo_corregido!="9"
				&& $campo_corregido!="10"
				&& $campo_corregido!="11"
				&& $campo_corregido!="12"
				&& $campo_corregido!="13"
				&& $campo_corregido!="14"
				&& $campo_corregido!="15"
				&& $campo_corregido!="16"
				&& $campo_corregido!="17"
				&& $campo_corregido!="18"
				&& $campo_corregido!="999"
				 && $array_fields[10]=="M")
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0"
				&& $campo_corregido!="1"
				&& $campo_corregido!="2"
				&& $campo_corregido!="3"
				&& $campo_corregido!="4"
				&& $campo_corregido!="5"
				&& $campo_corregido!="6"
				&& $campo_corregido!="7"
				&& $campo_corregido!="8"
				&& $campo_corregido!="9"
				&& $campo_corregido!="10"
				&& $campo_corregido!="11"
				&& $campo_corregido!="12"
				&& $campo_corregido!="13"
				&& $campo_corregido!="14"
				&& $campo_corregido!="15"
				&& $campo_corregido!="16"
				&& $campo_corregido!="17"
				&& $campo_corregido!="18"
				&& $campo_corregido!="999"
				 && $array_fields[10]=="F"
				&& $edad<=10)
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0"
				&& $campo_corregido!="1"
				&& $campo_corregido!="2"
				&& $campo_corregido!="3"
				&& $campo_corregido!="4"
				&& $campo_corregido!="5"
				&& $campo_corregido!="6"
				&& $campo_corregido!="7"
				&& $campo_corregido!="8"
				&& $campo_corregido!="9"
				&& $campo_corregido!="10"
				&& $campo_corregido!="11"
				&& $campo_corregido!="12"
				&& $campo_corregido!="13"
				&& $campo_corregido!="14"
				&& $campo_corregido!="15"
				&& $campo_corregido!="16"
				&& $campo_corregido!="17"
				&& $campo_corregido!="18"
				&& $campo_corregido!="999"
				 && $array_fields[10]=="F"
				&& $edad>10
				&& ($array_fields[87]=="1845-01-01"
				|| $array_fields[87]=="1835-01-01"
				|| $array_fields[87]=="1830-01-01"
				|| $array_fields[87]=="1825-01-01"
				|| $array_fields[87]=="1810-01-01"
				|| $array_fields[87]=="1805-01-01")
				)
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0"
				&& $campo_corregido!="1"
				&& $campo_corregido!="2"
				&& $campo_corregido!="3"
				&& $campo_corregido!="4"
				&& $campo_corregido!="5"
				&& $campo_corregido!="6"
				&& $campo_corregido!="7"
				&& $campo_corregido!="8"
				&& $campo_corregido!="9"
				&& $campo_corregido!="10"
				&& $campo_corregido!="11"
				&& $campo_corregido!="12"
				&& $campo_corregido!="13"
				&& $campo_corregido!="14"
				&& $campo_corregido!="15"
				&& $campo_corregido!="16"
				&& $campo_corregido!="17"
				&& $campo_corregido!="18"
				&& $campo_corregido!="999"
				 && $array_fields[10]=="F"
				&& $edad>10
				&& ($c87es_mayor_a_1900_12_31<0)
				)
			{
			    $campo_corregido="999";
			}
			else if($campo_corregido!="0"
				&& $campo_corregido!="1"
				&& $campo_corregido!="2"
				&& $campo_corregido!="3"
				&& $campo_corregido!="4"
				&& $campo_corregido!="5"
				&& $campo_corregido!="6"
				&& $campo_corregido!="7"
				&& $campo_corregido!="8"
				&& $campo_corregido!="9"
				&& $campo_corregido!="10"
				&& $campo_corregido!="11"
				&& $campo_corregido!="12"
				&& $campo_corregido!="13"
				&& $campo_corregido!="14"
				&& $campo_corregido!="15"
				&& $campo_corregido!="16"
				&& $campo_corregido!="17"
				&& $campo_corregido!="18"
				&& $campo_corregido!="999"
				 && $array_fields[10]=="F"
				&& $edad>10
				&& ($array_fields[87]=="1800-01-01")
				)
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0"
				&& $campo_corregido!="1"
				&& $campo_corregido!="2"
				&& $campo_corregido!="3"
				&& $campo_corregido!="4"
				&& $campo_corregido!="5"
				&& $campo_corregido!="6"
				&& $campo_corregido!="7"
				&& $campo_corregido!="8"
				&& $campo_corregido!="9"
				&& $campo_corregido!="10"
				&& $campo_corregido!="11"
				&& $campo_corregido!="12"
				&& $campo_corregido!="13"
				&& $campo_corregido!="14"
				&& $campo_corregido!="15"
				&& $campo_corregido!="16"
				&& $campo_corregido!="17"
				&& $campo_corregido!="18"
				&& $campo_corregido!="999"
			    )
			{
			    $campo_corregido="0";
			}
		}//fin vp
		
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_027($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
	     	$c87es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[87]),"1900-12-31");
	      	//calidad
			if($array_fields[10]=="M" && $campo_corregido!="0")
			{
			    $campo_corregido="0";
			}
			else if($array_fields[10]=="F"
				&& $edad<=10
				&& $campo_corregido!="0")
			{
			    $campo_corregido="0";
			}
			else if($array_fields[10]=="F"
				&& ( $array_fields[87]=="1805-01-01"
				|| $array_fields[87]=="1810-01-01"
				|| $array_fields[87]=="1825-01-01"
				|| $array_fields[87]=="1830-01-01"
				|| $array_fields[87]=="1835-01-01"
				|| $array_fields[87]=="1845-01-01")
				&& $campo_corregido!="0"
				&& $edad>10
				)
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido=="0"
				&& $edad>10
				&& $array_fields[10]=="F"
				&& ($c87es_mayor_a_1900_12_31<0)
				)
			{
			    $campo_corregido="999";
			}
			else if($campo_corregido=="999"
				&& $edad>10
				&& $array_fields[10]=="F"
				&& ($array_fields[87]=="1800-01-01")
				)
			{
			    $campo_corregido="0";
			}

	     }//fin fase 1
	    }//revisado c88
	    
	    //campo a corregir es 89
	    if($numero_campo_a_corregir==89)
	    {
	    	$c87es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[87]),"1900-12-31");
	     if($fase_correccion==0)
	     {
		//campo en blanco
	    if($campo_corregido=="")
		{
		
			if($campo_corregido=="" && $array_fields[10]=="M")
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido==""
				&& $edad<10
				&& $array_fields[10]=="F"
				)
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido==""
				&& $edad>=10
				&& $array_fields[10]=="F"
				&& $array_fields[88]=="0")
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido==""
				&& $edad>=10
				&& $array_fields[10]=="F"
				&& ($array_fields[88]=="1"
					 || $array_fields[88]=="2"
					 || $array_fields[88]=="3"
					 || $array_fields[88]=="4"
					 || $array_fields[88]=="5"
					 || $array_fields[88]=="6"
					 || $array_fields[88]=="7"
					 || $array_fields[88]=="8"
					 || $array_fields[88]=="9"
					 || $array_fields[88]=="10"
					 || $array_fields[88]=="11"
					 || $array_fields[88]=="12"
					 || $array_fields[88]=="13"
					 || $array_fields[88]=="14"
					 || $array_fields[88]=="15"
					 || $array_fields[88]=="16"
					 || $array_fields[88]=="17"
					)
				)
			{
			    $campo_corregido="1";
			}
			else if($campo_corregido==""
				&& $edad>=10
				&& $array_fields[10]=="F"
				&& $array_fields[88]=="18")
			{
			    $campo_corregido="4";
			}
			else if($campo_corregido==""
				&& $edad>=10 
				&& $array_fields[10]=="F"
				&& $array_fields[88]=="999"
				&& $c87es_mayor_a_1900_12_31>=0
				&& $c87es_mayor_a_1900_12_31!==false
				)
			{
			    $campo_corregido="0";
			}		
			else if($campo_corregido=="")
			{
			    $campo_corregido="0";
			}
		}//fin campo esta en blanco
		
		//valor permitido
		if($campo_corregido!="0"
			&& $campo_corregido!="1"
			&& $campo_corregido!="2"
			&& $campo_corregido!="3"
			&& $campo_corregido!="4"
			&& $campo_corregido!="999"
			)
		{
			if($campo_corregido!="0"
				&& $campo_corregido!="1"
				&& $campo_corregido!="2"
				&& $campo_corregido!="3"
				&& $campo_corregido!="4"
				&& $campo_corregido!="999"
				 && $array_fields[10]=="M")
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0"
				&& $campo_corregido!="1"
				&& $campo_corregido!="2"
				&& $campo_corregido!="3"
				&& $campo_corregido!="4"
				&& $campo_corregido!="999"
				&& $edad<10
				&& $array_fields[10]=="F"
				)
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0"
				&& $campo_corregido!="1"
				&& $campo_corregido!="2"
				&& $campo_corregido!="3"
				&& $campo_corregido!="4"
				&& $campo_corregido!="999"
				&& $edad>=10
				&& $array_fields[10]=="F"
				&& $array_fields[88]=="0")
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0"
				&& $campo_corregido!="1"
				&& $campo_corregido!="2"
				&& $campo_corregido!="3"
				&& $campo_corregido!="4"
				&& $campo_corregido!="999"
				&& $edad>=10
				&& $array_fields[10]=="F"
				&& ($array_fields[88]=="1"
					 || $array_fields[88]=="2"
					 || $array_fields[88]=="3"
					 || $array_fields[88]=="4"
					 || $array_fields[88]=="5"
					 || $array_fields[88]=="6"
					 || $array_fields[88]=="7"
					 || $array_fields[88]=="8"
					 || $array_fields[88]=="9"
					 || $array_fields[88]=="10"
					 || $array_fields[88]=="11"
					 || $array_fields[88]=="12"
					 || $array_fields[88]=="13"
					 || $array_fields[88]=="14"
					 || $array_fields[88]=="15"
					 || $array_fields[88]=="16"
					 || $array_fields[88]=="17"
					)
				)
			{
			    $campo_corregido="1";
			}
			else if($campo_corregido!="0"
				&& $campo_corregido!="1"
				&& $campo_corregido!="2"
				&& $campo_corregido!="3"
				&& $campo_corregido!="4"
				&& $campo_corregido!="999"
				&& $edad>=10
				&& $array_fields[10]=="F"
				&& $array_fields[88]=="18")
			{
			    $campo_corregido="4";
			}
			else if($campo_corregido!="0"
				&& $campo_corregido!="1"
				&& $campo_corregido!="2"
				&& $campo_corregido!="3"
				&& $campo_corregido!="4"
				&& $campo_corregido!="999"
				&& $edad>=10 
				&& $array_fields[10]=="F"
				&& $array_fields[88]=="999"
				&& $c87es_mayor_a_1900_12_31>=0
				&& $c87es_mayor_a_1900_12_31!==false
				)
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0"
				&& $campo_corregido!="1"
				&& $campo_corregido!="2"
				&& $campo_corregido!="3"
				&& $campo_corregido!="4"
				&& $campo_corregido!="999"
				)
			{
			    $campo_corregido="0";
			}
		}//fin if
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_028($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
	     	//calidad
	     	if($campo_corregido!="0" && $array_fields[10]=="M")
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0"
				&& $edad<10
				&& $array_fields[10]=="F"
				)
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0"
				&& $edad>=10
				&& $array_fields[10]=="F"
				&& $array_fields[88]=="0")
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido!="1"
				&& $campo_corregido!="2"
				&& $edad>=10
				&& $array_fields[10]=="F"
				&& ($array_fields[88]=="1"
					 || $array_fields[88]=="2"
					 || $array_fields[88]=="3"
					 || $array_fields[88]=="4"
					 || $array_fields[88]=="5"
					 || $array_fields[88]=="6"
					 || $array_fields[88]=="7"
					 || $array_fields[88]=="8"
					 || $array_fields[88]=="9"
					 || $array_fields[88]=="10"
					 || $array_fields[88]=="11"
					 || $array_fields[88]=="12"
					 || $array_fields[88]=="13"
					 || $array_fields[88]=="14"
					 || $array_fields[88]=="15"
					 || $array_fields[88]=="16"
					 || $array_fields[88]=="17"
					)
				)
			{
			    $campo_corregido="1";
			}
			else if($campo_corregido!="3"
				&& $campo_corregido!="4"
				&& $edad>=10
				&& $array_fields[10]=="F"
				&& $array_fields[88]=="18")
			{
			    $campo_corregido="4";
			}
			else if($campo_corregido=="999"
				&& $edad>=10 
				&& $array_fields[10]=="F"
				&& $array_fields[88]=="999"
				&& $c87es_mayor_a_1900_12_31>=0
				&& $c87es_mayor_a_1900_12_31!==false
				)
			{
			    $campo_corregido="0";
			}//fin calidad

			

	     }//fin fase 1
	    }//revisado c89
	    
	    //campo a corregir es 90
	    if($numero_campo_a_corregir==90)
	    {
	     if($fase_correccion==0)
	     {
		
		//campo en blanco
		if($campo_corregido=="")
		{
			if($campo_corregido=="" && $array_fields[10]=="M")
			{
			    $campo_corregido="0";
			}
			else if($array_fields[10]=="F"
				&& $edad<10
				&& $campo_corregido=="")
			{
			    $campo_corregido="0";
			}
			else if($array_fields[10]=="F"
				&& $edad>=10
				&& $campo_corregido==""
				&& $array_fields[88]=="0")
			{
			    $campo_corregido="0";
			}
			else if($array_fields[10]=="F"
				&& $edad>=10
				&& $campo_corregido==""
				&& $array_fields[88]!="0")
			{
			    $campo_corregido="999";
			}			
			else if($campo_corregido=="")
			{
			    $campo_corregido="0";
			}
		}//fin if
		
		
		//formato
	      if(strlen($campo_corregido)==11 &&
	       (substr($campo_corregido,0,1)=="5" || substr($campo_corregido,0,1)=="8")
	       )
		{
		    $campo_corregido="0".$campo_corregido;
		}
		else if(strlen($campo_corregido)==10)
		{
		    $campo_corregido=$campo_corregido."01";
		}
		
		
		//valor permitido
		$query_bd="";
		$query_bd.="SELECT * FROM gios_prestador_servicios_salud WHERE cod_registro_especial_pss = '".$campo_corregido."' ";		
		$query_bd.=";";
		$resultados_query=$conexion_bd_correccion->consultar2_no_crea_cierra($query_bd);
		

		if($campo_corregido!="0" && $campo_corregido!="999"
			&& (!is_array($resultados_query) || count($resultados_query)==0)
			
			)
		{
			if($campo_corregido!="0" && $campo_corregido!="999"
				&& (!is_array($resultados_query) || count($resultados_query)==0)
				&& $array_fields[10]=="M"
				)
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0" && $campo_corregido!="999"
				&& (!is_array($resultados_query) || count($resultados_query)==0)
				&& $array_fields[10]=="F"
				&& $edad<10
				)
			{
			    $campo_corregido="0";
			}			
			else if($campo_corregido!="0" && $campo_corregido!="999"
				&& (!is_array($resultados_query) || count($resultados_query)==0)
				&& $array_fields[10]=="F"
				&& $edad>=10
				&& $array_fields[88]=="0"
				)
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0" && $campo_corregido!="999"
				&& (!is_array($resultados_query) || count($resultados_query)==0)
				&& $array_fields[10]=="F"
				&& $edad>=10
				&& $array_fields[88]!="0"
				)
			{
			    $campo_corregido="999";
			}
			else if($campo_corregido!="0" && $campo_corregido!="999"
				&& (!is_array($resultados_query) || count($resultados_query)==0)
				
				)
			{
			    $campo_corregido="0";
			}
		}//fin if
		
		
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_020($campo_corregido,$conexion_bd_correccion);
	     }
	     else if($fase_correccion==1)
	     {
			$query_bd="";
			$query_bd.="SELECT * FROM gios_prestador_servicios_salud WHERE cod_registro_especial_pss = '".$campo_corregido."' ";		
			$query_bd.=";";
			$resultados_query=$conexion_bd_correccion->consultar2_no_crea_cierra($query_bd);
			
			if($array_fields[10]=="M" && $campo_corregido!="0")
			{
			    $campo_corregido="0";
			}
			else if($array_fields[10]=="F"
				&& $edad<10
				&& $campo_corregido!="0")
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0"
				//&& $campo_corregido!="999"
				//&& (!is_array($resultados_query) || count($resultados_query)==0)
				&& ($array_fields[88]=="0")
				&& $array_fields[10]=="F"
				&& $edad>=10
				)
			{
			    $campo_corregido="0";
			}
			else if($array_fields[88]!="0"
				&& $campo_corregido=="0"
				&& $array_fields[10]=="F"
				&& $edad>=10)
			{
			    $campo_corregido="999";
			}//fin calidad
	     }//fin fase 1
	    }//revisado
	    
	    //campo a corregir es 91
	    if($numero_campo_a_corregir==91)
	    {
	     
	     if($fase_correccion==0)
	     {
		//campo en blanco
     	 if($campo_corregido=="")
		{
			if($campo_corregido=="" && $array_fields[10]=="M")
			{
			    $campo_corregido="1845-01-01";
			}
			else if($campo_corregido=="" && $array_fields[10]=="F"
				&& $edad<10)
			{
			    $campo_corregido="1845-01-01";
			}
			else if($campo_corregido=="" && $array_fields[10]=="F"
				&& $edad>=10
				&& ( $array_fields[88]=="17"  )
				)
			{
			    $campo_corregido="1845-01-01";
			}
			else if($campo_corregido=="" && $array_fields[10]=="F"
				&& $edad>=10
				&& ($array_fields[92]=="0" || $array_fields[92]=="999")
				&& ( $array_fields[88]!="17" )
				)
			{
			    $campo_corregido="1845-01-01";
			}
			else if($campo_corregido=="" && $array_fields[10]=="F"
				&& $edad>=10
				&& ( $array_fields[88]!="17" )
				&& $array_fields[92]!="0" && $array_fields[92]!="999"
				)
			{
			    $campo_corregido="1800-01-01";
			}			
			else if($campo_corregido=="")
			{
			    $campo_corregido="1845-01-01";
			}
		}//fin if 
		
		//formato
		$corrige_formato=false;
		$array_fecha_campo_actual=explode("-", $campo_corregido);
		if(count($array_fecha_campo_actual)==3)
		{
			if(ctype_digit($array_fecha_campo_actual[0]) && ctype_digit($array_fecha_campo_actual[1]) && ctype_digit($array_fecha_campo_actual[2]))
			{
				//checkdate mm-dd-aaaa -> aaaa-mm-dd ?
				if(checkdate($array_fecha_campo_actual[1],$array_fecha_campo_actual[2],$array_fecha_campo_actual[0])
			       && intval($array_fecha_campo_actual[0])>=32)
			    {
			    	//no se corrige formato
			    }
			    else
			    {
			    	$corrige_formato=true;
			    }

			}
			else
			{
				$corrige_formato=true;
			}
		}
		else
		{
			$corrige_formato=true;
		}//fin else

		if($corrige_formato==true)
		{
		$campo_corregido=$this->corrector_formato_fecha($campo_corregido,false,-2);
		}
		
		//valor permitido
		$excede_fecha_corte=$this->diferencia_dias_entre_fechas($campo_corregido,$date_de_corte);
		$es_menor_o_igual_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
		
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");

		//comparacion fecha campo actual es inferior a la fecha de nacimiento
			$fecha_nacimiento_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),"1900-12-31");
			$campo_actual_inferior_fecha_nacimiento=$this->diferencia_dias_entre_fechas($campo_corregido,trim($array_fields[9]));
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_nacimiento_es_mayor_a_1900_12_31<0
			&& $fecha_nacimiento_es_mayor_a_1900_12_31!==false
			&& $campo_actual_inferior_fecha_nacimiento>0
			&& $campo_actual_inferior_fecha_nacimiento!==false
			)
			{
				$campo_corregido="1800-01-01";
			}//fin if
			//fin comparacion fecha campo actual es inferior a la fecha de nacimiento

			//limite inferior fecha corte 2 years
			$fecha_corte_menos_2_years="";
			$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=false;
			$array_fecha_corte=explode("-", $date_de_corte);
			if(count($array_fecha_corte)==3)
			{
				$fecha_corte_menos_2_years=(intval($array_fecha_corte[0])-2)."-".$array_fecha_corte[1]."-".$array_fecha_corte[2];
				$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=$this->diferencia_dias_entre_fechas($campo_corregido,$fecha_corte_menos_2_years);
			}//fin if
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_corte_menos_2_years!=""
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years!==false
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years>0
			)
			{
				$campo_corregido="1845-01-01";
			}//fin if
			//fin limite inferior fecha corte 2 years

		if($comparacion_a_1900_12_31<0 
					&& $excede_fecha_corte<0
					&& $comparacion_a_1900_12_31!==false
					&& $excede_fecha_corte!==false
					)
				{
					$campo_corregido="1800-01-01";
				}//fin comparacion excede la fecha de corte
		
		if($comparacion_a_1900_12_31>=0
			&& $campo_corregido!="1800-01-01"
			&& $campo_corregido!="1805-01-01"
			&& $campo_corregido!="1810-01-01"
			&& $campo_corregido!="1825-01-01"
			&& $campo_corregido!="1830-01-01"
			&& $campo_corregido!="1835-01-01"
			&& $campo_corregido!="1845-01-01"
			)
		{
			 if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& $array_fields[10]=="M")
			{
			    $campo_corregido="1845-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& $array_fields[10]=="F"
				&& $edad<10)
			{
			    $campo_corregido="1845-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& $array_fields[10]=="F"
				&& $edad>=10
				&& ( $array_fields[88]=="17"  )
				)
			{
			    $campo_corregido="1845-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& $array_fields[10]=="F"
				&& $edad>=10
				&& ($array_fields[92]=="0" || $array_fields[92]=="999")
				&& ( $array_fields[88]!="17" )
				)
			{
			    $campo_corregido="1845-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& $array_fields[10]=="F"
				&& $edad>=10
				&& ( $array_fields[88]!="17" )
				&& $array_fields[92]!="0" && $array_fields[92]!="999"
				)
			{
			    $campo_corregido="1800-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				)
			{
			    $campo_corregido="1845-01-01";
			}
		}//fin else if
	     
	     
		//$campo_corregido=$this->corrector_valor_permitido_fecha_criterio_002($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
			//calidad con fecha actividad
			$nueva_edad_year_actual=-1;
			$nueva_edad_meses_actual=-1;
			$nueva_edad_dias_actual=-1;
			    
			$array_fecha_nacimiento=explode("-",$array_fields[9]);
			$fecha_campo_actual=explode("-",$campo_corregido);
			if(count($fecha_campo_actual)==3 && checkdate($fecha_campo_actual[1],$fecha_campo_actual[2],$fecha_campo_actual[0]))
			{
			    //echo "<script>alert('entro');</script>";
			    
			    $array_calc_edad_actual=$this->edad_years_months_days($array_fecha_nacimiento[2]."-".$array_fecha_nacimiento[1]."-".$array_fecha_nacimiento[0],$fecha_campo_actual[2]."-".$fecha_campo_actual[1]."-".$fecha_campo_actual[0]);
			    $nueva_edad_year_actual=intval($array_calc_edad_actual['y']);
			    $nueva_edad_meses_actual=(intval($array_calc_edad_actual['y'])*12)+$array_calc_edad_actual['m'];
			    $nueva_edad_dias_actual=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),trim($array_fields[$numero_campo_a_corregir]));
			    
			    //echo "<script>alert(' EDAD DIAS $nueva_edad_dias_actual FN ".trim($array_fields[9])." FA ".trim($array_fields[$numero_campo_a_corregir])."  NCA $numero_campo_a_corregir');</script>";
			}
			$es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
			$es_menor_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");

			if($campo_corregido!="1845-01-01" && $array_fields[10]=="M")
			{
				//c1
			    $campo_corregido="1845-01-01";
			}
			else if($es_mayor_a_1900_12_31<0 && $array_fields[10]=="F"
				&& $nueva_edad_year_actual<10)
			{
				//c2
			    $campo_corregido="1845-01-01";
			}
			else if(
				(trim($array_fields[91])=="1800-01-01"
				  ||  trim($array_fields[91])=="1805-01-01"
				  || trim($array_fields[91])=="1810-01-01"
				  || trim($array_fields[91])=="1825-01-01"
				  || trim($array_fields[91])=="1830-01-01"
				  || trim($array_fields[91])=="1835-01-01"
				  )
				  && $array_fields[10]=="F"
				&& $edad<10)
			{
				//c3
			    $campo_corregido="1845-01-01";
			}
			else if($es_mayor_a_1900_12_31<0
				&& $array_fields[10]=="F"
				&& $nueva_edad_year_actual>=10
				&& ( $array_fields[88]=="17"  )
				)
			{
				//c4
			    $campo_corregido="1845-01-01";
			}
			else if(
				(trim($array_fields[91])=="1800-01-01"
				  ||  trim($array_fields[91])=="1805-01-01"
				  || trim($array_fields[91])=="1810-01-01"
				  || trim($array_fields[91])=="1825-01-01"
				  || trim($array_fields[91])=="1830-01-01"
				  || trim($array_fields[91])=="1835-01-01"
				  )
				   && $array_fields[10]=="F"
				&& $edad>=10
				&& ( $array_fields[88]=="17" )
				)
			{
				//c5
			    $campo_corregido="1845-01-01";
			}
			else if(
					$es_mayor_a_1900_12_31<0
				   && $array_fields[10]=="F"
				&& $nueva_edad_year_actual>=10
				&& ($array_fields[92]=="0" )
				&& ( $array_fields[88]!="17" )
				)
			{
				//c6
			    $campo_corregido="1845-01-01";
			}
			else if(
				(trim($array_fields[91])=="1800-01-01"
				  ||  trim($array_fields[91])=="1805-01-01"
				  || trim($array_fields[91])=="1810-01-01"
				  || trim($array_fields[91])=="1825-01-01"
				  || trim($array_fields[91])=="1830-01-01"
				  || trim($array_fields[91])=="1835-01-01"
				  )
				   && $array_fields[10]=="F"
				&& $edad>=10
				&& ($array_fields[92]=="0" )
				&& ( $array_fields[88]!="17")
				)
			{
				//c7
			    $campo_corregido="1845-01-01";
			}
			else if(
				(trim($array_fields[91])=="1845-01-01"
				  ||  trim($array_fields[91])=="1805-01-01"
				  || trim($array_fields[91])=="1810-01-01"
				  || trim($array_fields[91])=="1825-01-01"
				  || trim($array_fields[91])=="1830-01-01"
				  || trim($array_fields[91])=="1835-01-01"
				  )
				   && $array_fields[10]=="F"
				&& $edad>=10
				&& ( $array_fields[88]!="17")
				&& $array_fields[92]!="0" && $array_fields[92]!="999"
				)
			{
				//c8
			    $campo_corregido="1800-01-01";
			}//fin calidad		
		
		
		
	     }//fin fase 1
	    }//revisado c91
	    
	    //campo a corregir es 92
	    if($numero_campo_a_corregir==92)
	    {
	     if($fase_correccion==0)
	     {
		//campo en blanco
		$es_c91_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas($array_fields[91],"1900-12-31");

		if($campo_corregido=="")
		{
			if($campo_corregido=="" && $array_fields[10]=="M")
			{
			    $campo_corregido="0";
			}
			else if($array_fields[10]=="F"
				&& $edad<10
				&& $campo_corregido=="")
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido==""
				&& $array_fields[10]=="F"
				&& $edad>=10
				&& ($array_fields[88]=="17" )
				)
			{
			    $campo_corregido="0";
			}
			else if($array_fields[10]=="F"
				&& $edad>=10
				&& $campo_corregido==""
				&& ($array_fields[88]!="17" )
				&& ($array_fields[91]=="1805-01-01"
				|| $array_fields[91]=="1810-01-01"
				|| $array_fields[91]=="1825-01-01"
				|| $array_fields[91]=="1830-01-01"
				|| $array_fields[91]=="1835-01-01"
				|| $array_fields[91]=="1845-01-01")
				)
			{
			    $campo_corregido="0";
			}
			else if($array_fields[10]=="F"
				&& $edad>=10
				&& ($array_fields[88]!="17" )
				&& $campo_corregido==""
				&& $es_c91_mayor_a_1900_12_31<0
				)
			{
			    $campo_corregido="999";
			}
			else if($array_fields[10]=="F"
				&& $edad>=10
				&& ($array_fields[88]!="17" )
				&& $campo_corregido==""
				&& $array_fields[91]=="1800-01-01"
				)
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido=="")
			{
			    $campo_corregido="0";
			}
		}//fin if
		
		
		//formato
	      if(strlen($campo_corregido)==11 &&
	       (substr($campo_corregido,0,1)=="5" || substr($campo_corregido,0,1)=="8")
	       )
		{
		    $campo_corregido="0".$campo_corregido;
		}
		else if(strlen($campo_corregido)==10)
		{
		    $campo_corregido=$campo_corregido."01";
		}
		
		
		//valor permitido
		$query_bd="";
		$query_bd.="SELECT * FROM gios_prestador_servicios_salud WHERE cod_registro_especial_pss = '".$campo_corregido."' ";		
		$query_bd.=";";
		$resultados_query=$conexion_bd_correccion->consultar2_no_crea_cierra($query_bd);
		
		 if($campo_corregido!="0" && $campo_corregido!="999"
			&& (!is_array($resultados_query) || count($resultados_query)==0)
			
			)
		 {
			if($campo_corregido!="0" && $campo_corregido!="999"
				&& (!is_array($resultados_query) || count($resultados_query)==0)
				&& $array_fields[10]=="M"
				)
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0" && $campo_corregido!="999"
				&& (!is_array($resultados_query) || count($resultados_query)==0)
				&& $array_fields[10]=="F"
				&& $edad<10
				)
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0" && $campo_corregido!="999"
				&& (!is_array($resultados_query) || count($resultados_query)==0)
				&& $array_fields[10]=="F"
				&& $edad>=10
				&& ($array_fields[88]=="17" )
				)
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0" && $campo_corregido!="999"
				&& (!is_array($resultados_query) || count($resultados_query)==0)
				&& $array_fields[10]=="F"
				&& $edad>=10
				&& ($array_fields[88]!="17" )
				&& ($array_fields[91]=="1805-01-01"
				|| $array_fields[91]=="1810-01-01"
				|| $array_fields[91]=="1825-01-01"
				|| $array_fields[91]=="1830-01-01"
				|| $array_fields[91]=="1835-01-01"
				|| $array_fields[91]=="1845-01-01")
				)
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0" && $campo_corregido!="999"
				&& (!is_array($resultados_query) || count($resultados_query)==0)
				&& $array_fields[10]=="F"
				&& $edad>=10
				&& ($array_fields[88]!="17"  )
				&& $es_c91_mayor_a_1900_12_31<0
				)
			{
			    $campo_corregido="999";
			}
			else if($campo_corregido!="0" && $campo_corregido!="999"
				&& (!is_array($resultados_query) || count($resultados_query)==0)
				&& $array_fields[10]=="F"
				&& $edad>=10
				&& ($array_fields[88]!="17" )
				&& $array_fields[91]=="1800-01-01"
				)
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0" && $campo_corregido!="999"
				&& (!is_array($resultados_query) || count($resultados_query)==0)
				
				)
			{
			    $campo_corregido="0";
			}
		}//fin if
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_020($campo_corregido,$conexion_bd_correccion);
	     }
	     else if($fase_correccion==1)
	     {
			$query_bd="";
			$query_bd.="SELECT * FROM gios_prestador_servicios_salud WHERE cod_registro_especial_pss = '".$campo_corregido."' ";		
			$query_bd.=";";
			$resultados_query=$conexion_bd_correccion->consultar2_no_crea_cierra($query_bd);
			//calidad c92

			$es_c91_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas($array_fields[91],"1900-12-31");

			if($array_fields[10]=="M" && $campo_corregido!="0")
			{
			    $campo_corregido="0";
			}
			else if($array_fields[10]=="F"
				&& $edad<10
				&& $campo_corregido!="0")
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0"
				&& $array_fields[10]=="F"
				&& $edad>=10
				&& ($array_fields[88]=="17" )
				)
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0"
				&& ($array_fields[91]=="1805-01-01"
				|| $array_fields[91]=="1810-01-01"
				|| $array_fields[91]=="1825-01-01"
				|| $array_fields[91]=="1830-01-01"
				|| $array_fields[91]=="1835-01-01"
				|| $array_fields[91]=="1845-01-01")
				&& $array_fields[10]=="F"
				&& $edad>=10
				&& ($array_fields[88]!="17"  )
				)
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido=="0" 
				&& $es_c91_mayor_a_1900_12_31<0
				&& $array_fields[10]=="F"
				&& $edad>=10
				&& ($array_fields[88]!="17"  )
				)
			{
			    $campo_corregido="999";
			}
			else if($campo_corregido=="999" 
				&& $array_fields[91]=="1800-01-01"
				&& $array_fields[10]=="F"
				&& $edad>=10
				&& ($array_fields[88]!="17"  )
				)
			{
			    $campo_corregido="0";
			}
		
		
	     }//fin fase 1
	    }//revisado c92
	    
	    //campo a corregir es 93
	    if($numero_campo_a_corregir==93)
	    {
	     if($fase_correccion==0)
	     {
		//campo en blanco
     	if($campo_corregido=="")
		{
			if($campo_corregido=="" && $array_fields[10]=="M")
			{
			    $campo_corregido="1845-01-01";
			}
			else if($campo_corregido=="" && $array_fields[10]=="F"
				&& $edad<10)
			{
			    $campo_corregido="1845-01-01";
			}
			else if($campo_corregido==""
				&& $array_fields[10]=="F"
				&& $edad>=10
				&& ($array_fields[88]=="17" )
				)
			{
			    $campo_corregido="1845-01-01";
			}			
			else if($campo_corregido==""
				&& $array_fields[10]=="F"
				&& $edad>=10
				&& ($array_fields[88]!="17" )
				&& ($array_fields[94]=="0" || $array_fields[94]=="999")
				)
			{
			    $campo_corregido="1845-01-01";
			}
			else if($campo_corregido==""
				&& $array_fields[10]=="F"
				&& $edad>=10
				&& ($array_fields[88]!="17" )
				&& $array_fields[94]!="0" && $array_fields[94]!="999"
				)
			{
			    $campo_corregido="1800-01-01";
			}
			else if($campo_corregido=="")
			{
			    $campo_corregido="1845-01-01";
			}
		}//fin if campo en blanco
		
		//formato
		$corrige_formato=false;
		$array_fecha_campo_actual=explode("-", $campo_corregido);
		if(count($array_fecha_campo_actual)==3)
		{
			if(ctype_digit($array_fecha_campo_actual[0]) && ctype_digit($array_fecha_campo_actual[1]) && ctype_digit($array_fecha_campo_actual[2]))
			{
				//checkdate mm-dd-aaaa -> aaaa-mm-dd ?
				if(checkdate($array_fecha_campo_actual[1],$array_fecha_campo_actual[2],$array_fecha_campo_actual[0])
			       && intval($array_fecha_campo_actual[0])>=32)
			    {
			    	//no se corrige formato
			    }
			    else
			    {
			    	$corrige_formato=true;
			    }

			}
			else
			{
				$corrige_formato=true;
			}
		}
		else
		{
			$corrige_formato=true;
		}//fin else

		if($corrige_formato==true)
		{
		$campo_corregido=$this->corrector_formato_fecha($campo_corregido,false,-2);
		}
		
		//valor permitido
		$excede_fecha_corte=$this->diferencia_dias_entre_fechas($campo_corregido,$date_de_corte);
		$es_menor_o_igual_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
		
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");

		//comparacion fecha campo actual es inferior a la fecha de nacimiento
			$fecha_nacimiento_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),"1900-12-31");
			$campo_actual_inferior_fecha_nacimiento=$this->diferencia_dias_entre_fechas($campo_corregido,trim($array_fields[9]));
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_nacimiento_es_mayor_a_1900_12_31<0
			&& $fecha_nacimiento_es_mayor_a_1900_12_31!==false
			&& $campo_actual_inferior_fecha_nacimiento>0
			&& $campo_actual_inferior_fecha_nacimiento!==false
			)
			{
				$campo_corregido="1800-01-01";
			}//fin if
			//fin comparacion fecha campo actual es inferior a la fecha de nacimiento

			//limite inferior fecha corte 2 years
			$fecha_corte_menos_2_years="";
			$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=false;
			$array_fecha_corte=explode("-", $date_de_corte);
			if(count($array_fecha_corte)==3)
			{
				$fecha_corte_menos_2_years=(intval($array_fecha_corte[0])-2)."-".$array_fecha_corte[1]."-".$array_fecha_corte[2];
				$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=$this->diferencia_dias_entre_fechas($campo_corregido,$fecha_corte_menos_2_years);
			}//fin if
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_corte_menos_2_years!=""
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years!==false
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years>0
			)
			{
				$campo_corregido="1845-01-01";
			}//fin if
			//fin limite inferior fecha corte 2 years

		if($comparacion_a_1900_12_31<0 
					&& $excede_fecha_corte<0
					&& $comparacion_a_1900_12_31!==false
					&& $excede_fecha_corte!==false
					)
				{
					$campo_corregido="1800-01-01";
				}//fin comparacion excede la fecha de corte

		if($comparacion_a_1900_12_31>=0
			&& $campo_corregido!="1800-01-01"
			&& $campo_corregido!="1805-01-01"
			&& $campo_corregido!="1810-01-01"
			&& $campo_corregido!="1825-01-01"
			&& $campo_corregido!="1830-01-01"
			&& $campo_corregido!="1835-01-01"
			&& $campo_corregido!="1845-01-01")
		{
			if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& $array_fields[10]=="M")
			{
			    $campo_corregido="1845-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& $array_fields[10]=="F"
				&& $edad<10)
			{
			    $campo_corregido="1845-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& $array_fields[10]=="F"
				&& $edad>=10
				&& ($array_fields[88]=="17"  )
				)
			{
			    $campo_corregido="1845-01-01";
			}
			else if(
				$comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& $array_fields[10]=="F"
				&& $edad>=10
				&& ($array_fields[88]!="17" )
				&&  ($array_fields[94]=="0" || $array_fields[94]=="999")
				)
			{
			    $campo_corregido="1845-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& $array_fields[10]=="F"
				&& $edad>=10
				&& ($array_fields[88]!="17" )
				&& $array_fields[94]!="0" && $array_fields[94]!="999"
				)
			{
			    $campo_corregido="1800-01-01";
			}			
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01")
			{
			    $campo_corregido="1845-01-01";
			}
		}//fin else if
		
		//$campo_corregido=$this->corrector_valor_permitido_fecha_criterio_002($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
		//calidad con fecha actividad
		$nueva_edad_year_actual=-1;
		$nueva_edad_meses_actual=-1;
		$nueva_edad_dias_actual=-1;
		    
		$array_fecha_nacimiento=explode("-",$array_fields[9]);
		$fecha_campo_actual=explode("-",$campo_corregido);
		if(count($fecha_campo_actual)==3 && checkdate($fecha_campo_actual[1],$fecha_campo_actual[2],$fecha_campo_actual[0]))
		{
		    //echo "<script>alert('entro');</script>";
		    
		    $array_calc_edad_actual=$this->edad_years_months_days($array_fecha_nacimiento[2]."-".$array_fecha_nacimiento[1]."-".$array_fecha_nacimiento[0],$fecha_campo_actual[2]."-".$fecha_campo_actual[1]."-".$fecha_campo_actual[0]);
		    $nueva_edad_year_actual=intval($array_calc_edad_actual['y']);
		    $nueva_edad_meses_actual=(intval($array_calc_edad_actual['y'])*12)+$array_calc_edad_actual['m'];
		    $nueva_edad_dias_actual=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),trim($array_fields[$numero_campo_a_corregir]));
		    
		    //echo "<script>alert(' EDAD DIAS $nueva_edad_dias_actual FN ".trim($array_fields[9])." FA ".trim($array_fields[$numero_campo_a_corregir])."  NCA $numero_campo_a_corregir');</script>";
		}
		$es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
		$es_menor_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
		
		//calidad
		if($array_fields[10]=="M" && $campo_corregido!="1845-01-01")
		{
		    $campo_corregido="1845-01-01";
		}
		else if($array_fields[10]=="F"
			&& $nueva_edad_year_actual<10
			&& $es_mayor_a_1900_12_31<0)
		{
		    $campo_corregido="1845-01-01";
		}
		else if($array_fields[10]=="F"
			&& $edad<10
			&& (trim($array_fields[93])=="1800-01-01"
			    || trim($array_fields[93])=="1805-01-01"
				  || trim($array_fields[93])=="1810-01-01"
				  || trim($array_fields[93])=="1825-01-01"
				  || trim($array_fields[93])=="1830-01-01"
				  || trim($array_fields[93])=="1835-01-01")
			)
		{
		    $campo_corregido="1845-01-01";
		}
		else if( $es_mayor_a_1900_12_31<0
			&& $array_fields[10]=="F"
			&& $nueva_edad_year_actual>=10
			&& ($array_fields[88]=="17" )
			)
		{
		    $campo_corregido="1845-01-01";
		}
		else if (
			 (trim($array_fields[93])=="1800-01-01"
			    || trim($array_fields[93])=="1805-01-01"
				  || trim($array_fields[93])=="1810-01-01"
				  || trim($array_fields[93])=="1825-01-01"
				  || trim($array_fields[93])=="1830-01-01"
				  || trim($array_fields[93])=="1835-01-01")
			&& $array_fields[10]=="F"
			&& $edad>=10
			&& ($array_fields[88]=="17")
			)
		{
		    $campo_corregido="1845-01-01";
		}
		else if(
			$es_mayor_a_1900_12_31<0
			&& $array_fields[10]=="F"
			&& $nueva_edad_year_actual>=10
			&& ($array_fields[88]!="17")//debe ser diferente como en las validaciones y otros criterios no igual a 17
			&& $array_fields[94]=="0"
			)
		{
		    $campo_corregido="1845-01-01";
		}
		else if (
			 (trim($array_fields[93])=="1800-01-01"
			    || trim($array_fields[93])=="1805-01-01"
				  || trim($array_fields[93])=="1810-01-01"
				  || trim($array_fields[93])=="1825-01-01"
				  || trim($array_fields[93])=="1830-01-01"
				  || trim($array_fields[93])=="1835-01-01")
			&& $array_fields[10]=="F"
			&& $edad>=10
			&& ($array_fields[88]!="17" )
			&& ($array_fields[94]=="0" || $array_fields[94]=="999" )
			)
		{
		    $campo_corregido="1845-01-01";
		}
		else if(
			(trim($array_fields[93])=="1845-01-01"
				|| trim($array_fields[93])=="1805-01-01"
				  || trim($array_fields[93])=="1810-01-01"
				  || trim($array_fields[93])=="1825-01-01"
				  || trim($array_fields[93])=="1830-01-01"
				  || trim($array_fields[93])=="1835-01-01")
			&& $array_fields[10]=="F"
			&& $edad>=10
			&& ($array_fields[88]!="17" )
			&& $array_fields[94]!="0" && $array_fields[94]!="999"
			)
		{
		    $campo_corregido="1800-01-01";
		}
		
		
		
	     }//fin fase 1
	    }//revisado c93
	    
	    //campo a corregir es 94
	    if($numero_campo_a_corregir==94)
	    {
	    	$es_c93_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas($array_fields[93],"1900-12-31");
	     if($fase_correccion==0)
	     {
		
			//campo en blanco
			

			if($campo_corregido=="")
			{
				if($campo_corregido=="" && $array_fields[10]=="M")
				{
				    $campo_corregido="0";
				}
				else if($campo_corregido==""
					&& $edad<10
					&& $array_fields[10]=="F" )
				{
				    $campo_corregido="0";
				}
				else if($campo_corregido==""
					&& $array_fields[10]=="F"
					&& $edad>=10
					&& ($array_fields[88]=="17")
					)
				{
				    $campo_corregido="0";
				}			
				else if($campo_corregido==""
					&& $edad>=10
					&& ($array_fields[88]!="17" )
					&& $array_fields[10]=="F"
					&& ($array_fields[93]=="1845-01-01"
				       || $array_fields[93]=="1835-01-01"
				       || $array_fields[93]=="1830-01-01"
				       || $array_fields[93]=="1825-01-01"
				       || $array_fields[93]=="1810-01-01"
				       || $array_fields[93]=="1805-01-01"
				       )
					)
				{
				    $campo_corregido="0";
				}
				else if($campo_corregido==""
					&& $edad>=10
					&& ($array_fields[88]!="17" )
					&& $array_fields[10]=="F"
					&& $es_c93_mayor_a_1900_12_31<0
					)
				{
				    $campo_corregido="999";
				}
				else if($campo_corregido==""
					&& $edad>=10
					&& ($array_fields[88]!="17" )
					&& $array_fields[10]=="F"
					&& $array_fields[93]=="1800-01-01"
					)
				{
				    $campo_corregido="0";
				}
				else if($campo_corregido=="")
				{
				    $campo_corregido="0";
				}
			}//fin campo en blanco
			
			
			//valor permitido
			if($campo_corregido!="0"
				&& $campo_corregido!="1"
				&& $campo_corregido!="2"
				&& $campo_corregido!="3"
				&& $campo_corregido!="4"
				&& $campo_corregido!="5"
				&& $campo_corregido!="6"
				&& $campo_corregido!="999"
				)
			{
				if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="3"
					&& $campo_corregido!="4"
					&& $campo_corregido!="5"
					&& $campo_corregido!="6"
					&& $campo_corregido!="999"
					 && $array_fields[10]=="M")
				{
				    $campo_corregido="0";
				}
				else if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="3"
					&& $campo_corregido!="4"
					&& $campo_corregido!="5"
					&& $campo_corregido!="6"
					&& $campo_corregido!="999"
					&& $edad<10
					&& $array_fields[10]=="F" )
				{
				    $campo_corregido="0";
				}
				else if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="3"
					&& $campo_corregido!="4"
					&& $campo_corregido!="5"
					&& $campo_corregido!="6"
					&& $campo_corregido!="999"
					&& $array_fields[10]=="F"
					&& $edad>=10
					&& ($array_fields[88]=="17" )
					)
				{
				    $campo_corregido="0";
				}			
				else if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="3"
					&& $campo_corregido!="4"
					&& $campo_corregido!="5"
					&& $campo_corregido!="6"
					&& $campo_corregido!="999"
					&& $edad>=10
					&& ($array_fields[88]!="17" )
					&& $array_fields[10]=="F"
					&& ($array_fields[93]=="1845-01-01"
				       || $array_fields[93]=="1835-01-01"
				       || $array_fields[93]=="1830-01-01"
				       || $array_fields[93]=="1825-01-01"
				       || $array_fields[93]=="1810-01-01"
				       || $array_fields[93]=="1805-01-01"
				       )
					)
				{
				    $campo_corregido="0";
				}
				else if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="3"
					&& $campo_corregido!="4"
					&& $campo_corregido!="5"
					&& $campo_corregido!="6"
					&& $campo_corregido!="999"
					&& $edad>=10
					&& ($array_fields[88]!="17" )
					&& $array_fields[10]=="F"
					&& $es_c93_mayor_a_1900_12_31<0
					)
				{
				    $campo_corregido="999";
				}
				else if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="3"
					&& $campo_corregido!="4"
					&& $campo_corregido!="5"
					&& $campo_corregido!="6"
					&& $campo_corregido!="999"
					&& $edad>=10
					&& ($array_fields[88]!="17")
					&& $array_fields[10]=="F"
					&& $array_fields[93]=="1800-01-01"
					)
				{
				    $campo_corregido="0";
				}
				else if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="3"
					&& $campo_corregido!="4"
					&& $campo_corregido!="5"
					&& $campo_corregido!="6"
					&& $campo_corregido!="999"
					)
				{
				    $campo_corregido="0";
				}
			}//fin valor permitido
		
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_029($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
			//calidad
	     	if($campo_corregido!="0" && $array_fields[10]=="M")
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0"
				&& $edad<10
				&& $array_fields[10]=="F" )
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0"
				&& $array_fields[10]=="F"
				&& $edad>=10
				&& ($array_fields[88]=="17")
				)
			{
			    $campo_corregido="0";
			}			
			else if($campo_corregido!="0"
				&& $edad>=10
				&& ($array_fields[88]!="17" )
				&& $array_fields[10]=="F"
				&& ($array_fields[93]=="1845-01-01"
			       || $array_fields[93]=="1835-01-01"
			       || $array_fields[93]=="1830-01-01"
			       || $array_fields[93]=="1825-01-01"
			       || $array_fields[93]=="1810-01-01"
			       || $array_fields[93]=="1805-01-01"
			       )
				)
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido=="0"
				&& $edad>=10
				&& ($array_fields[88]!="17" )
				&& $array_fields[10]=="F"
				&& $es_c93_mayor_a_1900_12_31<0
				)
			{
			    $campo_corregido="999";
			}
			else if($campo_corregido=="999"
				&& $edad>=10
				&& ($array_fields[88]!="17" )
				&& $array_fields[10]=="F"
				&& $array_fields[93]=="1800-01-01"
				)
			{
			    $campo_corregido="0";
			}//fin calidad
		
		
		
	     }//fin fase 1
	    }//revisado c94
	    
	    //campo a corregir es 95
	    if($numero_campo_a_corregir==95)
	    {
	     if($fase_correccion==0)
	     {
	      
	      //campo en blanco
		if($campo_corregido=="")
		{
			if($campo_corregido=="" && $array_fields[10]=="M")
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido==""
				&& $edad<10
				&& $array_fields[10]=="F")
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido==""
				&& $array_fields[10]=="F"
				&& $edad>=10
				&& ($array_fields[88]=="17")
				)
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido==""
				&& $edad>=10
				&& $array_fields[10]=="F"
				&& ($array_fields[88]!="17" )
				&& $array_fields[94]=="0"
				)
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido==""
				&& $edad>=10
				&& $array_fields[10]=="F"
				&& ($array_fields[88]!="17")
				&& $array_fields[93]=="1800-01-01"
				&& $array_fields[94]=="999"
				)
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido==""
				&& $edad>=10
				&& $array_fields[10]=="F"
				&& ($array_fields[88]!="17"  )
				&& $array_fields[94]!="0" 
				)
			{
			    $campo_corregido="999";
			}			
			else if($campo_corregido=="")
			{
			    $campo_corregido="0";
			}
		}//fin if
		
		
		//formato
	      if(strlen($campo_corregido)==11 &&
	       (substr($campo_corregido,0,1)=="5" || substr($campo_corregido,0,1)=="8")
	       )
		{
		    $campo_corregido="0".$campo_corregido;
		}
		else if(strlen($campo_corregido)==10)
		{
		    $campo_corregido=$campo_corregido."01";
		}
		
		
		//valor permitido
		$query_bd="";
		$query_bd.="SELECT * FROM gios_prestador_servicios_salud WHERE cod_registro_especial_pss = '".$campo_corregido."' ";		
		$query_bd.=";";
		$resultados_query=$conexion_bd_correccion->consultar2_no_crea_cierra($query_bd);

		if($campo_corregido!="0" && $campo_corregido!="999"
			&& (!is_array($resultados_query) || count($resultados_query)==0)
			)
		{
		
			if($campo_corregido!="0" && $campo_corregido!="999"
				&& (!is_array($resultados_query) || count($resultados_query)==0)
				 && $array_fields[10]=="M")
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0" && $campo_corregido!="999"
				&& (!is_array($resultados_query) || count($resultados_query)==0)
				&& $edad<10
				&& $array_fields[10]=="F")
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0" && $campo_corregido!="999"
				&& (!is_array($resultados_query) || count($resultados_query)==0)
				&& $array_fields[10]=="F"
				&& $edad>=10
				&& ($array_fields[88]=="17"  )
				)
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0" && $campo_corregido!="999"
				&& (!is_array($resultados_query) || count($resultados_query)==0)
				&& $edad>=10
				&& $array_fields[10]=="F"
				&& ($array_fields[88]!="17" )
				&& $array_fields[94]=="0"
				)
			{
			    $campo_corregido="0";
			}
			
			else if($campo_corregido!="0" && $campo_corregido!="999"
				&& (!is_array($resultados_query) || count($resultados_query)==0)
				&& $edad>=10
				&& $array_fields[10]=="F"
				&& ($array_fields[88]!="17" )
				&& $array_fields[93]=="1800-01-01"
				&& $array_fields[94]=="999"
				)
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0" && $campo_corregido!="999"
				&& (!is_array($resultados_query) || count($resultados_query)==0)
				&& $edad>=10
				&& $array_fields[10]=="F"
				&& ($array_fields[88]!="17"  )
				&& $array_fields[94]!="0" 
				)
			{
			    $campo_corregido="999";
			}
			else if($campo_corregido!="0" && $campo_corregido!="999"
				&& (!is_array($resultados_query) || count($resultados_query)==0)
				)
			{
			    $campo_corregido="0";
			}
		}//fin if
	      
		
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_020($campo_corregido,$conexion_bd_correccion);
	     }
	     else if($fase_correccion==1)
	     {
			$query_bd="";
			$query_bd.="SELECT * FROM gios_prestador_servicios_salud WHERE cod_registro_especial_pss = '".$campo_corregido."' ";		
			$query_bd.=";";
			$resultados_query=$conexion_bd_correccion->consultar2_no_crea_cierra($query_bd);
			
			//calidad

			if($campo_corregido!="0" && $array_fields[10]=="M")
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0"
				&& $edad<10
				&& $array_fields[10]=="F")
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0"
				&& $array_fields[10]=="F"
				&& $edad>=10
				&& ($array_fields[88]=="17")
				)
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0"
				&& $edad>=10
				&& $array_fields[10]=="F"
				&& ($array_fields[88]!="17" )
				&& $array_fields[94]=="0"
				)
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido=="999"
				&& $edad>=10
				&& $array_fields[10]=="F"
				&& ($array_fields[88]!="17"  )
				&& $array_fields[93]=="1800-01-01"
				&& $array_fields[94]=="999"
				)
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido=="0"
				&& $edad>=10
				&& $array_fields[10]=="F"
				&& ($array_fields[88]!="17" )
				&& $array_fields[94]!="0" 
				)
			{
			    $campo_corregido="999";
			}//fin calidad
			
			
			
		
	     }//fin fase 1
	    }//revisado c95
	    
	    //FIN CAMPOS CANCER DE CERVIX
	    
	    
	    //CAMPOS CANCER DE SENO
	    
	    //campo a corregir es 27
	    if($numero_campo_a_corregir==27)
	    {
	      if($fase_correccion==0)
	      {
			//campo en blanco
			if($campo_corregido=="")
			{
				/*
		    	//anulada para coomeva
			    if($campo_corregido==""
			    	&& (trim($array_fields[97])=="4"
		    				|| trim($array_fields[97])=="5"
		    				|| trim($array_fields[97])=="6"
		    				|| trim($array_fields[97])=="7"
				    		)
			    	&& $edad>=35
					&& $array_fields[10]=="F"
			    	)
				{
				    $campo_corregido="1";
				}
				else if($campo_corregido==""
    				&& (trim($array_fields[101])=="3"
	    				|| trim($array_fields[101])=="4"
	    				|| trim($array_fields[101])=="5"
			    		)
			    	&& $edad>=35
					&& $array_fields[10]=="F"
			    	)
				{
				    $campo_corregido="1";
				}
				else if($campo_corregido==""
			    	&& (trim($array_fields[97])=="0"
		    				|| trim($array_fields[97])=="1"
		    				|| trim($array_fields[97])=="2"
		    				|| trim($array_fields[97])=="3"
		    				|| trim($array_fields[97])=="999"
				    		)
			    	&& (trim($array_fields[101])=="0"
	    				|| trim($array_fields[101])=="1"
	    				|| trim($array_fields[101])=="2"
	    				|| trim($array_fields[101])=="999"
			    		)
			    	&& $edad>=35
					&& $array_fields[10]=="F"
			    	)
				{
				    $campo_corregido="2";
				}
				*/
				if($campo_corregido==""
			    	&& (trim($array_fields[97])=="1"
			    		|| trim($array_fields[97])=="2"
			    		|| trim($array_fields[97])=="3"
			    		|| trim($array_fields[97])=="999"
			    	)
			    	&& $edad>=35
					&& $array_fields[10]=="F"
			    	)
				{
				    $campo_corregido="2";
				}//fin if
				else if($campo_corregido==""
			    	&& (trim($array_fields[101])=="1"
			    		|| trim($array_fields[101])=="5"
			    		|| trim($array_fields[101])=="999"
			    	)
			    	&& $edad>=35
					&& $array_fields[10]=="F"
			    	)
				{
				    $campo_corregido="2";
				}//fin calidad
				else if($campo_corregido=="" )
				{
				    $campo_corregido="21";
				}
			}//fin campo en blanco
			
			//valor permitido
			if($campo_corregido!="1"
			   && $campo_corregido!="2"
			   && $campo_corregido!="21" )
			{
				/*
		    	//anulada para coomeva
			    if($campo_corregido!="1"
				   && $campo_corregido!="2"
				   && $campo_corregido!="21"
			    	&& (trim($array_fields[97])=="4"
		    				|| trim($array_fields[97])=="5"
		    				|| trim($array_fields[97])=="6"
		    				|| trim($array_fields[97])=="7"
				    		)
			    	&& $edad>=35
					&& $array_fields[10]=="F"
			    	)
				{
				    $campo_corregido="1";
				}
				else if($campo_corregido!="1"
				   && $campo_corregido!="2"
				   && $campo_corregido!="21"
    				&& (trim($array_fields[101])=="3"
	    				|| trim($array_fields[101])=="4"
	    				|| trim($array_fields[101])=="5"
			    		)
			    	&& $edad>=35
					&& $array_fields[10]=="F"
			    	)
				{
				    $campo_corregido="1";
				}
				else if($campo_corregido!="1"
				   && $campo_corregido!="2"
				   && $campo_corregido!="21"
			    	&& (trim($array_fields[97])=="0"
		    				|| trim($array_fields[97])=="1"
		    				|| trim($array_fields[97])=="2"
		    				|| trim($array_fields[97])=="3"
		    				|| trim($array_fields[97])=="999"
				    		)
			    	&& (trim($array_fields[101])=="0"
	    				|| trim($array_fields[101])=="1"
	    				|| trim($array_fields[101])=="2"
	    				|| trim($array_fields[101])=="999"
			    		)
			    	&& $edad>=35
					&& $array_fields[10]=="F"
			    	)
				{
				    $campo_corregido="2";
				}
				*/
				if($campo_corregido!="1"
				   && $campo_corregido!="2"
				   && $campo_corregido!="21"
			    	&& (trim($array_fields[97])=="1"
			    		|| trim($array_fields[97])=="2"
			    		|| trim($array_fields[97])=="3"
			    		|| trim($array_fields[97])=="999"
			    	)
			    	&& $edad>=35
					&& $array_fields[10]=="F"
			    	)
				{
				    $campo_corregido="2";
				}//fin if
				else if($campo_corregido!="1"
				   && $campo_corregido!="2"
				   && $campo_corregido!="21"
			    	&& (trim($array_fields[101])=="1"
			    		|| trim($array_fields[101])=="5"
			    		|| trim($array_fields[101])=="999"
			    	)
			    	&& $edad>=35
					&& $array_fields[10]=="F"
			    	)
				{
				    $campo_corregido="2";
				}//fin calidad
				else if($campo_corregido!="1"
				   && $campo_corregido!="2"
				   && $campo_corregido!="21"
			    )
				{
				    $campo_corregido="21";
				}
			}//fin valor permitido
		
		
			//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_009($campo_corregido);
	      }//fin fase 0
	      else if($fase_correccion==1)
	      {
	      		if($campo_corregido=="21"
			    	&& (trim($array_fields[97])=="1"
			    		|| trim($array_fields[97])=="2"
			    		|| trim($array_fields[97])=="3"
			    		|| trim($array_fields[97])=="999"
			    	)
			    	&& $edad>=35
					&& $array_fields[10]=="F"
			    	)
				{
				    $campo_corregido="2";
				}//fin if
				else if($campo_corregido=="21"
			    	&& (trim($array_fields[101])=="1"
			    		|| trim($array_fields[101])=="5"
			    		|| trim($array_fields[101])=="999"
			    	)
			    	&& $edad>=35
					&& $array_fields[10]=="F"
			    	)
				{
				    $campo_corregido="2";
				}//fin calidad
	      		/*
		    	//anulada para coomeva
				if($campo_corregido!="1"
			    	&& (trim($array_fields[97])=="4"
		    				|| trim($array_fields[97])=="5"
		    				|| trim($array_fields[97])=="6"
		    				|| trim($array_fields[97])=="7"
				    		)
			    	&& $edad>=35
					&& $array_fields[10]=="F"
			    	)
				{
				    $campo_corregido="1";
				}
				else if($campo_corregido!="1"
    				&& (trim($array_fields[101])=="3"
	    				|| trim($array_fields[101])=="4"
	    				|| trim($array_fields[101])=="5"
			    		)
			    	&& $edad>=35
					&& $array_fields[10]=="F"
			    	)
				{
				    $campo_corregido="1";
				}
				else if($campo_corregido!="2"
			    	&& (trim($array_fields[97])=="0"
		    				|| trim($array_fields[97])=="1"
		    				|| trim($array_fields[97])=="2"
		    				|| trim($array_fields[97])=="3"
		    				|| trim($array_fields[97])=="999"
				    		)
			    	&& (trim($array_fields[101])=="0"
	    				|| trim($array_fields[101])=="1"
	    				|| trim($array_fields[101])=="2"
	    				|| trim($array_fields[101])=="999"
			    		)
			    	&& $edad>=35
					&& $array_fields[10]=="F"
			    	)
				{
				    $campo_corregido="2";
				}//fin calidad
				*/
	      }//fin fase 1
	    }//revisado c27
	    
	    //campo a corregir es 96
	    if($numero_campo_a_corregir==96)
	    {
	     
	     if($fase_correccion==0)
	     {
		//campo en blanco
	     	if($campo_corregido=="")
			{
				if($campo_corregido=="" && $array_fields[10]=="M")
				{
				    $campo_corregido="1845-01-01";
				}
				else if($campo_corregido==""
					&& $array_fields[10]=="F"
					&& $edad<35
					)
				{
				    $campo_corregido="1845-01-01";
				}
				else if($campo_corregido==""
					&& $array_fields[10]=="F"
					&& $edad>=35
					&& ($array_fields[97]=="0" || $array_fields[97]=="999")
					)
				{
				    $campo_corregido="1845-01-01";
				}
				else if($campo_corregido==""
					&& $array_fields[10]=="F"
					&& $edad>=35
					&& $array_fields[97]!="0" && $array_fields[97]!="999"
					)
				{
				    $campo_corregido="1800-01-01";
				}
				else if($campo_corregido=="")
				{
				    $campo_corregido="1845-01-01";
				}
			}//fin if
		
		//formato
		$corrige_formato=false;
		$array_fecha_campo_actual=explode("-", $campo_corregido);
		if(count($array_fecha_campo_actual)==3)
		{
			if(ctype_digit($array_fecha_campo_actual[0]) && ctype_digit($array_fecha_campo_actual[1]) && ctype_digit($array_fecha_campo_actual[2]))
			{
				//checkdate mm-dd-aaaa -> aaaa-mm-dd ?
				if(checkdate($array_fecha_campo_actual[1],$array_fecha_campo_actual[2],$array_fecha_campo_actual[0])
			       && intval($array_fecha_campo_actual[0])>=32)
			    {
			    	//no se corrige formato
			    }
			    else
			    {
			    	$corrige_formato=true;
			    }

			}
			else
			{
				$corrige_formato=true;
			}
		}
		else
		{
			$corrige_formato=true;
		}//fin else

		if($corrige_formato==true)
		{
		$campo_corregido=$this->corrector_formato_fecha($campo_corregido,false,-2);
		}
		
		//valor permitido
		$excede_fecha_corte=$this->diferencia_dias_entre_fechas($campo_corregido,$date_de_corte);
		$es_menor_o_igual_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
		
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");

		//comparacion fecha campo actual es inferior a la fecha de nacimiento
			$fecha_nacimiento_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),"1900-12-31");
			$campo_actual_inferior_fecha_nacimiento=$this->diferencia_dias_entre_fechas($campo_corregido,trim($array_fields[9]));
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_nacimiento_es_mayor_a_1900_12_31<0
			&& $fecha_nacimiento_es_mayor_a_1900_12_31!==false
			&& $campo_actual_inferior_fecha_nacimiento>0
			&& $campo_actual_inferior_fecha_nacimiento!==false
			)
			{
				$campo_corregido="1800-01-01";
			}//fin if
			//fin comparacion fecha campo actual es inferior a la fecha de nacimiento

			//limite inferior fecha corte 2 years
			$fecha_corte_menos_2_years="";
			$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=false;
			$array_fecha_corte=explode("-", $date_de_corte);
			if(count($array_fecha_corte)==3)
			{
				$fecha_corte_menos_2_years=(intval($array_fecha_corte[0])-2)."-".$array_fecha_corte[1]."-".$array_fecha_corte[2];
				$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=$this->diferencia_dias_entre_fechas($campo_corregido,$fecha_corte_menos_2_years);
			}//fin if
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_corte_menos_2_years!=""
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years!==false
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years>0
			)
			{
				$campo_corregido="1845-01-01";
			}//fin if
			//fin limite inferior fecha corte 2 years

		if($comparacion_a_1900_12_31<0 
					&& $excede_fecha_corte<0
					&& $comparacion_a_1900_12_31!==false
					&& $excede_fecha_corte!==false
					)
				{
					$campo_corregido="1800-01-01";
				}//fin comparacion excede la fecha de corte

		if($comparacion_a_1900_12_31>=0
			&& $campo_corregido!="1800-01-01"
			&& $campo_corregido!="1805-01-01"
			&& $campo_corregido!="1810-01-01"
			&& $campo_corregido!="1825-01-01"
			&& $campo_corregido!="1830-01-01"
			&& $campo_corregido!="1835-01-01"
			&& $campo_corregido!="1845-01-01"
			)
		{
			if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& $array_fields[10]=="M"
				)
			{
			    $campo_corregido="1845-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& $array_fields[10]=="F"
				&& $edad<35
				)
			{
			    $campo_corregido="1845-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& $array_fields[10]=="F"
				&& $edad>=35
				&& ($array_fields[97]=="0" || $array_fields[97]=="999")
				)
			{
			    $campo_corregido="1845-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& $array_fields[10]=="F"
				&& $edad>=35
				&& $array_fields[97]!="0" && $array_fields[97]!="999"
				)
			{
			    $campo_corregido="1800-01-01";
			}
			else if($array_fields[10]=="F"
			
				&& (
					trim($campo_corregido)=="1800-01-01"
				    || trim($campo_corregido)=="1805-01-01"
				    || trim($campo_corregido)=="1810-01-01"
				    || trim($campo_corregido)=="1825-01-01"
				    || trim($campo_corregido)=="1830-01-01"
				    || trim($campo_corregido)=="1835-01-01"
				    )	
				&& $edad>=35
				& ( $array_fields[97]=="0" || $array_fields[97]=="999")
				)
			{
				//c7
			    $campo_corregido="1845-01-01";
			}
		}//fin if
	     
		//$campo_corregido=$this->corrector_valor_permitido_fecha_criterio_002($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
		//calidad con fecha actividad
		$nueva_edad_year_actual=-1;
		$nueva_edad_meses_actual=-1;
		$nueva_edad_dias_actual=-1;
		    
		$array_fecha_nacimiento=explode("-",$array_fields[9]);
		$fecha_campo_actual=explode("-",$campo_corregido);
		if(count($fecha_campo_actual)==3 && checkdate($fecha_campo_actual[1],$fecha_campo_actual[2],$fecha_campo_actual[0]))
		{
		    //echo "<script>alert('entro');</script>";
		    
		    $array_calc_edad_actual=$this->edad_years_months_days($array_fecha_nacimiento[2]."-".$array_fecha_nacimiento[1]."-".$array_fecha_nacimiento[0],$fecha_campo_actual[2]."-".$fecha_campo_actual[1]."-".$fecha_campo_actual[0]);
		    $nueva_edad_year_actual=intval($array_calc_edad_actual['y']);
		    $nueva_edad_meses_actual=(intval($array_calc_edad_actual['y'])*12)+$array_calc_edad_actual['m'];
		    $nueva_edad_dias_actual=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),trim($array_fields[$numero_campo_a_corregir]));
		    
		    //echo "<script>alert(' EDAD DIAS $nueva_edad_dias_actual FN ".trim($array_fields[9])." FA ".trim($array_fields[$numero_campo_a_corregir])."  NCA $numero_campo_a_corregir');</script>";
		}
		$es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
		$es_menor_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
		//calidad
		if($array_fields[10]=="M" && $campo_corregido!="1845-01-01")
		{
			//c1
		    $campo_corregido="1845-01-01";
		}
		else if($array_fields[10]=="F"
			&& $nueva_edad_year_actual<35
			&& $es_mayor_a_1900_12_31<0)
		{
			//c2
		    $campo_corregido="1845-01-01";
		}
		else if($array_fields[10]=="F"
			&& $edad<35
			&& ( trim($campo_corregido)=="1800-01-01"
			    || trim($campo_corregido)=="1805-01-01"
			    || trim($campo_corregido)=="1810-01-01"
			    || trim($campo_corregido)=="1825-01-01"
			    || trim($campo_corregido)=="1830-01-01"
			    || trim($campo_corregido)=="1835-01-01")
			)
		{
			//c3
		    $campo_corregido="1845-01-01";
		}
		else if($array_fields[10]=="F"
			&& trim($campo_corregido)=="1800-01-01"
			&& $edad>=35
			& ( $array_fields[97]=="999")
			)
		{
			//c4
		    $campo_corregido="1845-01-01";
		}
		else if($array_fields[10]=="F"
			&& $edad>=35
			&& (
			    trim($campo_corregido)=="1805-01-01"
			    || trim($campo_corregido)=="1810-01-01"
			    || trim($campo_corregido)=="1825-01-01"
			    || trim($campo_corregido)=="1830-01-01"
			    || trim($campo_corregido)=="1835-01-01"
			    || trim($campo_corregido)=="1845-01-01"
			    )			
			&& $array_fields[97]!="0" && $array_fields[97]!="999"
			)
		{
			//c5
		    $campo_corregido="1800-01-01";
		}
		else if($array_fields[10]=="F"
			&& $es_mayor_a_1900_12_31<0
			&& $nueva_edad_year_actual>=35
			&& ($array_fields[97]=="0" )
			)
		{
			//c6
		    $campo_corregido="1845-01-01";
		}
		else if($array_fields[10]=="F"
			&& trim($campo_corregido)=="1800-01-01"
			&& $edad>=35
			& ( $array_fields[97]=="0")
			)
		{
			//c7
		    $campo_corregido="1845-01-01";
		}
		

	     }//fin if
	    }//revisado 96
	    
	    //campo a corregir es 97
	    if($numero_campo_a_corregir==97)
	    {

	    	
	     if($fase_correccion==0)
	     {
	     	$c96es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[96]),"1900-12-31");
			//campo en blanco

			if($campo_corregido=="")
			{
				if($campo_corregido=="" && $array_fields[10]=="M")
				{
					//c1
				    $campo_corregido="0";
				}
				else if($campo_corregido==""
					&& $array_fields[10]=="F"
					&& $edad<35
					)
				{
					//c2
				    $campo_corregido="0";
				}
				else if($campo_corregido==""
					&& $array_fields[10]=="F"
					&& $edad>=35
					&& ($c96es_mayor_a_1900_12_31<0  )
					)
				{
					//c3
				    $campo_corregido="999";
				}
				else if($campo_corregido==""
					&& $array_fields[10]=="F"
					&& $edad>=35
					&& ( $array_fields[96]=="1800-01-01" )
					)
				{
					//c4
				    $campo_corregido="0";
				}				
				else if($campo_corregido=="")
				{
					//c5
				    $campo_corregido="0";
				}
			}//fin if
		
		//valor permitido
			if($campo_corregido!="0"
			&& $campo_corregido!="1"
			&& $campo_corregido!="2"
			&& $campo_corregido!="3"
			&& $campo_corregido!="4"
			&& $campo_corregido!="5"
			&& $campo_corregido!="6"
			&& $campo_corregido!="7"
			&& $campo_corregido!="999"
		  	)
			{
				if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="3"
					&& $campo_corregido!="4"
					&& $campo_corregido!="5"
					&& $campo_corregido!="6"
					&& $campo_corregido!="7"
					&& $campo_corregido!="999"
					&& $array_fields[10]=="M"
				  )
				{
				    $campo_corregido="0";
				}
				else if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="3"
					&& $campo_corregido!="4"
					&& $campo_corregido!="5"
					&& $campo_corregido!="6"
					&& $campo_corregido!="7"
					&& $campo_corregido!="999"
					&& $array_fields[10]=="F"
					&& $edad<35
				  )
				{
				    $campo_corregido="0";
				}
				else if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="3"
					&& $campo_corregido!="4"
					&& $campo_corregido!="5"
					&& $campo_corregido!="6"
					&& $campo_corregido!="7"
					&& $campo_corregido!="999"
					&& $array_fields[10]=="F"
					&& $edad>=35
					&& ($c96es_mayor_a_1900_12_31<0 )
				  )
				{
				    $campo_corregido="999";
				}
				else if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="3"
					&& $campo_corregido!="4"
					&& $campo_corregido!="5"
					&& $campo_corregido!="6"
					&& $campo_corregido!="7"
					&& $campo_corregido!="999"
					&& $array_fields[10]=="F"
					&& $edad>=35
					&& ( $array_fields[96]=="1800-01-01" )
				  )
				{
				    $campo_corregido="0";
				}				
				else if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="3"
					&& $campo_corregido!="4"
					&& $campo_corregido!="5"
					&& $campo_corregido!="6"
					&& $campo_corregido!="7"
					&& $campo_corregido!="999"
				  )
				{
				    $campo_corregido="0";
				}
			}//fin if

		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_030($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
	     	$c96es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[96]),"1900-12-31");

			if($array_fields[10]=="M"
			   && $campo_corregido!="0")
			{
			    $campo_corregido="0";
			}		
			else if($array_fields[10]=="F"
				&& $edad<35
				&& $campo_corregido!="0")
			{
			    $campo_corregido="0";
			}	
			else if( ($c96es_mayor_a_1900_12_31<0 )
				&& $campo_corregido=="0"
				&& $array_fields[10]=="F"
				&& $edad>=35)
			{
			    $campo_corregido="999";
			}
			else if( ( $array_fields[96]=="1800-01-01"
				|| $array_fields[96]=="1805-01-01" 
				|| $array_fields[96]=="1810-01-01" 
				|| $array_fields[96]=="1825-01-01" 
				|| $array_fields[96]=="1830-01-01" 
				|| $array_fields[96]=="1835-01-01"
				|| $array_fields[96]=="1845-01-01"  
			 )
				&& $campo_corregido=="999"
				&& $array_fields[10]=="F"
				&& $edad>=35)
			{
			    $campo_corregido="0";
			}//finc alidad 97
			
	     }//fin fase 1
	    }//revisado 97
	    
	    //campo a corregir es 98
	    if($numero_campo_a_corregir==98)
	    {
	     if($fase_correccion==0)
	     {
			//campo en blanco
			if($campo_corregido=="")
			{		
				if($campo_corregido==""
				   && $array_fields[10]=="M"
				   )
				{
				    $campo_corregido="0";
				}
				else if($array_fields[10]=="F"
					&& $edad<35
					&& $campo_corregido==""
					)
				{
				    $campo_corregido="0";
				}
				else if($array_fields[10]=="F"
					&& $edad>=35
					&& $campo_corregido==""
					&& $array_fields[97]=="0"
					)
				{
				    $campo_corregido="0";
				}
				else if($array_fields[10]=="F"
					&& $edad>=35
					&& $campo_corregido==""
					&& $array_fields[97]!="0"
					)
				{
				    $campo_corregido="999";
				}
				else if($campo_corregido=="")
				{
				    $campo_corregido="0";
				}
			}//fin if
		
		//formato
	      if(strlen($campo_corregido)==11 &&
	       (substr($campo_corregido,0,1)=="5" || substr($campo_corregido,0,1)=="8")
	       )
		{
		    $campo_corregido="0".$campo_corregido;
		}
		else if(strlen($campo_corregido)==10)
		{
		    $campo_corregido=$campo_corregido."01";
		}
		
		
		//valor permitido
		$query_bd="";
		$query_bd.="SELECT * FROM gios_prestador_servicios_salud WHERE cod_registro_especial_pss = '".$campo_corregido."' ";		
		$query_bd.=";";
		$resultados_query=$conexion_bd_correccion->consultar2_no_crea_cierra($query_bd);
		
		if($campo_corregido!="0" && $campo_corregido!="999"
			&& (!is_array($resultados_query) || count($resultados_query)==0)
			)
		{
			if($campo_corregido!="0" && $campo_corregido!="999"
				&& (!is_array($resultados_query) || count($resultados_query)==0)
				&& $array_fields[10]=="M"
				)
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0" && $campo_corregido!="999"
				&& (!is_array($resultados_query) || count($resultados_query)==0)
				&& $array_fields[10]=="F"
				&& $edad<35
				)
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0" && $campo_corregido!="999"
				&& (!is_array($resultados_query) || count($resultados_query)==0)
				&& $array_fields[10]=="F"
				&& $edad>=35
				&& $array_fields[97]=="0"
				)
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0" && $campo_corregido!="999"
				&& (!is_array($resultados_query) || count($resultados_query)==0)
				&& $array_fields[10]=="F"
				&& $edad>=35
				&& $array_fields[97]!="0"
				)
			{
			    $campo_corregido="999";
			}
			else if($campo_corregido!="0" && $campo_corregido!="999"
				&& (!is_array($resultados_query) || count($resultados_query)==0)
				)
			{
			    $campo_corregido="0";
			}
		}//fin if
		
		
		
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_020($campo_corregido,$conexion_bd_correccion);
	     }
	     else if($fase_correccion==1)
	     {
	        $query_bd="";
		$query_bd.="SELECT * FROM gios_prestador_servicios_salud WHERE cod_registro_especial_pss = '".$campo_corregido."' ";		
		$query_bd.=";";
		$resultados_query=$conexion_bd_correccion->consultar2_no_crea_cierra($query_bd);
		
		if($array_fields[10]=="M" && $campo_corregido!="0")
		{
		    $campo_corregido="0";
		}
		else if($array_fields[10]=="F"
			&& $edad<35
			&& $campo_corregido!="0")
		{
		    $campo_corregido="0";
		}
		else if($array_fields[97]=="0"
			&& $campo_corregido!="0"
			&& $array_fields[10]=="F"
			&& $edad>=35)
		{
		    $campo_corregido="0";
		}
		else if($array_fields[10]=="F"
			&& $edad>=35
			&& $campo_corregido=="0"
			&& $array_fields[97]!="0")
		{
		    $campo_corregido="999";
		}
		
	     }//fin fase 1
	    }//revisado c98
	    
	     //campo a corregir es 99
	    if($numero_campo_a_corregir==99)
	    {
	     if($fase_correccion==0)
	     {
		//campo en blanco
		if($campo_corregido=="")
		{
			if($campo_corregido==""
				&& $array_fields[10]=="M"
			    )
			{
			    //criterio 1
			    $campo_corregido="1845-01-01";
			}
			else if($campo_corregido==""
				&& $array_fields[10]=="F"
				&& $edad<35
			    )
			{
			    //criterio 2
			    $campo_corregido="1845-01-01";
			}
			else if($campo_corregido==""
				&& $array_fields[10]=="F"
				&& $edad>=35
				&& ( trim($array_fields[97])=="2" || trim($array_fields[97])=="3")
			    )
			{
			    //criterio 3
			    $campo_corregido="1845-01-01";
			}
			else if($campo_corregido==""
			       && $array_fields[10]=="F"
			       && $edad>=35			       
			       && ($array_fields[97]!="2" &&  $array_fields[97]!="3")
			       && (trim($array_fields[101])=="0" || trim($array_fields[101])=="999" )
			    )
			{
			    //criterio 4
			    $campo_corregido="1845-01-01";
			}
			else if($campo_corregido==""
				&& $array_fields[10]=="F"
				&& ($array_fields[97]!="2" && $array_fields[97]!="3")
				&& $edad>=35
				&& trim($array_fields[101])!="0" 
				&& trim($array_fields[101])!="999" 
			    )
			{
			    //criterio 5
			    $campo_corregido="1800-01-01";
			}			
			else if($campo_corregido=="")
			{
			    //criterio 6
			    $campo_corregido="1845-01-01";
			}
		}//fin campo esta en blanco
		
		//formato
		$corrige_formato=false;
		$array_fecha_campo_actual=explode("-", $campo_corregido);
		if(count($array_fecha_campo_actual)==3)
		{
			if(ctype_digit($array_fecha_campo_actual[0]) && ctype_digit($array_fecha_campo_actual[1]) && ctype_digit($array_fecha_campo_actual[2]))
			{
				//checkdate mm-dd-aaaa -> aaaa-mm-dd ?
				if(checkdate($array_fecha_campo_actual[1],$array_fecha_campo_actual[2],$array_fecha_campo_actual[0])
			       && intval($array_fecha_campo_actual[0])>=32)
			    {
			    	//no se corrige formato
			    }
			    else
			    {
			    	$corrige_formato=true;
			    }

			}
			else
			{
				$corrige_formato=true;
			}
		}
		else
		{
			$corrige_formato=true;
		}//fin else

		if($corrige_formato==true)
		{
		$campo_corregido=$this->corrector_formato_fecha($campo_corregido,false,-2);
		}
		
		//valor permitido
		$excede_fecha_corte=$this->diferencia_dias_entre_fechas($campo_corregido,$date_de_corte);
		$es_menor_o_igual_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
		
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");

		//comparacion fecha campo actual es inferior a la fecha de nacimiento
			$fecha_nacimiento_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),"1900-12-31");
			$campo_actual_inferior_fecha_nacimiento=$this->diferencia_dias_entre_fechas($campo_corregido,trim($array_fields[9]));
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_nacimiento_es_mayor_a_1900_12_31<0
			&& $fecha_nacimiento_es_mayor_a_1900_12_31!==false
			&& $campo_actual_inferior_fecha_nacimiento>0
			&& $campo_actual_inferior_fecha_nacimiento!==false
			)
			{
				$campo_corregido="1800-01-01";
			}//fin if
			//fin comparacion fecha campo actual es inferior a la fecha de nacimiento

			//limite inferior fecha corte 2 years
			$fecha_corte_menos_2_years="";
			$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=false;
			$array_fecha_corte=explode("-", $date_de_corte);
			if(count($array_fecha_corte)==3)
			{
				$fecha_corte_menos_2_years=(intval($array_fecha_corte[0])-2)."-".$array_fecha_corte[1]."-".$array_fecha_corte[2];
				$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=$this->diferencia_dias_entre_fechas($campo_corregido,$fecha_corte_menos_2_years);
			}//fin if
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_corte_menos_2_years!=""
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years!==false
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years>0
			)
			{
				$campo_corregido="1845-01-01";
			}//fin if
			//fin limite inferior fecha corte 2 years

		if($comparacion_a_1900_12_31<0 
					&& $excede_fecha_corte<0
					&& $comparacion_a_1900_12_31!==false
					&& $excede_fecha_corte!==false
					)
				{
					$campo_corregido="1800-01-01";
				}//fin comparacion excede la fecha de corte

		if($comparacion_a_1900_12_31>=0
			&& $campo_corregido!="1800-01-01"
			&& $campo_corregido!="1805-01-01"
			&& $campo_corregido!="1810-01-01"
			&& $campo_corregido!="1825-01-01"
			&& $campo_corregido!="1830-01-01"
			&& $campo_corregido!="1835-01-01"
			&& $campo_corregido!="1845-01-01"
			)
		{
			if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& $array_fields[10]=="M"
			    )
			{
			    //criterio 1
			    $campo_corregido="1845-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& $array_fields[10]=="F"
				&& $edad<35
			    )
			{
			    //criterio 2
			    $campo_corregido="1845-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& $array_fields[10]=="F"
				&& $edad>=35
				&& (trim($array_fields[97])=="2"  || trim($array_fields[97])=="3")
			    )
			{
			    //criterio 3
			    $campo_corregido="1845-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
			       && $array_fields[10]=="F"
			       && $edad>=35			       
			       && ($array_fields[97]!="2" &&  $array_fields[97]!="3")
			       && (trim($array_fields[101])=="0" || trim($array_fields[101])=="999" )
			    )
			{
			    //criterio 4
			    $campo_corregido="1845-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& $array_fields[10]=="F"
				&& ($array_fields[97]!="2" &&  $array_fields[97]!="3")
				&& $edad>=35
				&& trim($array_fields[101])!="0" 
				&& trim($array_fields[101])!="999" 
			    )
			{
			    //criterio 5
			    $campo_corregido="1800-01-01";
			}			
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				)
			{
			    //criterio 6
			    $campo_corregido="1845-01-01";
			}
		}//fin else if
	     
		//$campo_corregido=$this->corrector_valor_permitido_fecha_criterio_002($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
		//calidad con fecha actividad
		$nueva_edad_year_actual=-1;
		$nueva_edad_meses_actual=-1;
		$nueva_edad_dias_actual=-1;
		    
		$array_fecha_nacimiento=explode("-",$array_fields[9]);
		$fecha_campo_actual=explode("-",$campo_corregido);
		if(count($fecha_campo_actual)==3 && checkdate($fecha_campo_actual[1],$fecha_campo_actual[2],$fecha_campo_actual[0]))
		{
		    //echo "<script>alert('entro');</script>";
		    
		    $array_calc_edad_actual=$this->edad_years_months_days($array_fecha_nacimiento[2]."-".$array_fecha_nacimiento[1]."-".$array_fecha_nacimiento[0],$fecha_campo_actual[2]."-".$fecha_campo_actual[1]."-".$fecha_campo_actual[0]);
		    $nueva_edad_year_actual=intval($array_calc_edad_actual['y']);
		    $nueva_edad_meses_actual=(intval($array_calc_edad_actual['y'])*12)+$array_calc_edad_actual['m'];
		    $nueva_edad_dias_actual=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),trim($array_fields[$numero_campo_a_corregir]));
		    
		    //echo "<script>alert(' EDAD DIAS $nueva_edad_dias_actual FN ".trim($array_fields[9])." FA ".trim($array_fields[$numero_campo_a_corregir])."  NCA $numero_campo_a_corregir');</script>";
		}
		$es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
		$es_menor_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");

		$c100es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[100]),"1900-12-31");
		$c99es_mayor_igual_a_c100=$this->diferencia_dias_entre_fechas($campo_corregido,trim($array_fields[100]));
		
		//calidad c99
		if($campo_corregido!="1845-01-01"
			&& $array_fields[10]=="M"
		    )
		{
		    //criterio 1
		    $campo_corregido="1845-01-01";
		}
		else if($es_mayor_a_1900_12_31<0
			&& $array_fields[10]=="F"
			&& $nueva_edad_year_actual<35
		    )
		{
		    //criterio 2
		    $campo_corregido="1845-01-01";
		}
		else if((trim($array_fields[99])=="1800-01-01"
			|| trim($array_fields[99])=="1805-01-01"
			|| trim($array_fields[99])=="1810-01-01"
			|| trim($array_fields[99])=="1825-01-01"
			|| trim($array_fields[99])=="1830-01-01"
			|| trim($array_fields[99])=="1835-01-01"
			)
			&& $array_fields[10]=="F"
			&& $edad<35			
		    )
		{
		    //criterio 3
		    $campo_corregido="1845-01-01";
		}
		else if((trim($array_fields[99])=="1800-01-01"
			|| trim($array_fields[99])=="1805-01-01"
			|| trim($array_fields[99])=="1810-01-01"
			|| trim($array_fields[99])=="1825-01-01"
			|| trim($array_fields[99])=="1830-01-01"
			|| trim($array_fields[99])=="1835-01-01"
			)
			&& $array_fields[10]=="F"
			&& $edad>=35
			&& (trim($array_fields[97])=="2" || trim($array_fields[97])=="3")
		    )
		{
		    //criterio 4
		    $campo_corregido="1845-01-01";
		}
		else if($es_mayor_a_1900_12_31<0
			&& $array_fields[10]=="F"
			&& $nueva_edad_year_actual>=35
			&& (trim($array_fields[97])=="2" || trim($array_fields[97])=="3")
		    )
		{
		    //criterio 5
		    $campo_corregido="1845-01-01";
		}
		else if($es_mayor_a_1900_12_31<0
		       && $array_fields[10]=="F"
		       && $nueva_edad_year_actual>=35			       
		       && ($array_fields[97]!="2" &&  $array_fields[97]!="3")
		       && (trim($array_fields[101])=="0" )
		    )
		{
		    //criterio 6
		    $campo_corregido="1845-01-01";
		}
		else if((trim($array_fields[99])=="1800-01-01"
			|| trim($array_fields[99])=="1805-01-01"
			|| trim($array_fields[99])=="1810-01-01"
			|| trim($array_fields[99])=="1825-01-01"
			|| trim($array_fields[99])=="1830-01-01"
			|| trim($array_fields[99])=="1835-01-01"
			)
			&& $array_fields[10]=="F"
			&& ( $array_fields[97]!="2" &&  $array_fields[97]!="3")
			&& $edad>=35
			&& (trim($array_fields[101])=="0" || trim($array_fields[101])=="999")
		    )
		{
		    //criterio 7
		    $campo_corregido="1845-01-01";
		}
		else if(
			(
			 trim($array_fields[99])=="1805-01-01"
			|| trim($array_fields[99])=="1810-01-01"
			|| trim($array_fields[99])=="1825-01-01"
			|| trim($array_fields[99])=="1830-01-01"
			|| trim($array_fields[99])=="1835-01-01"
			|| trim($array_fields[99])=="1845-01-01"
			)
			&& $array_fields[10]=="F"
			&& ($array_fields[97]!="2" &&  $array_fields[97]!="3")
			&& $edad>=35
			&& trim($array_fields[101])!="0" && trim($array_fields[101])!="999"
		    )
		{
		    //criterio 8
		    $campo_corregido="1800-01-01";
		}
		else if($es_mayor_a_1900_12_31<0
			&& $c100es_mayor_a_1900_12_31<0 
			&& $c99es_mayor_igual_a_c100<=0 && $c99es_mayor_igual_a_c100!==false
			)
		{
			//criterio 9
		    $campo_corregido="1800-01-01";
		}//fin if
		
		
	     }//fin fase 1
	    }//revisado c99
	    
	    //campo a corregir es 100
	    if($numero_campo_a_corregir==100)
	    {
	     if($fase_correccion==0)
	     {
		//campo en blanco
	     	if($campo_corregido=="")
			{
				if($campo_corregido==""
				   && $array_fields[10]=="M"
				   )
				{
				    //criterio 1
				    $campo_corregido="1845-01-01";
				}
				else if($campo_corregido==""
				   && $array_fields[10]=="F"
				   && $edad<35
				   )
				{
				    //criterio 2
				    $campo_corregido="1845-01-01";
				}
				else if($campo_corregido==""
					&& $array_fields[10]=="F"
					&& $edad>=35
					&& ( $array_fields[97]=="2" || $array_fields[97]=="3")
					)
				{
				    //criterio 3
				    $campo_corregido="1845-01-01";
				}
				else if($campo_corregido==""			
					&& $array_fields[10]=="F"
					&& $edad>=35
					&& $array_fields[97]!="2"
					&& $array_fields[97]!="3"
					&& ($array_fields[101]=="0" || $array_fields[101]=="999" )
				   )
				{
				    //criterio 4
				    $campo_corregido="1845-01-01";
				}
				else if($campo_corregido==""		
					&& $array_fields[10]=="F"
					&& $edad>=35
					&& $array_fields[97]!="2"
					&& $array_fields[97]!="3"
					&& $array_fields[101]!="0" 
					&& $array_fields[101]!="999" 
				   )
				{
				    //criterio 5
				    $campo_corregido="1800-01-01";
				}				
				else if($campo_corregido=="")
				{
				    //criterio 6
				    $campo_corregido="1845-01-01";
				}
			}//fin if
		
		//formato
		$corrige_formato=false;
		$array_fecha_campo_actual=explode("-", $campo_corregido);
		if(count($array_fecha_campo_actual)==3)
		{
			if(ctype_digit($array_fecha_campo_actual[0]) && ctype_digit($array_fecha_campo_actual[1]) && ctype_digit($array_fecha_campo_actual[2]))
			{
				//checkdate mm-dd-aaaa -> aaaa-mm-dd ?
				if(checkdate($array_fecha_campo_actual[1],$array_fecha_campo_actual[2],$array_fecha_campo_actual[0])
			       && intval($array_fecha_campo_actual[0])>=32)
			    {
			    	//no se corrige formato
			    }
			    else
			    {
			    	$corrige_formato=true;
			    }

			}
			else
			{
				$corrige_formato=true;
			}
		}
		else
		{
			$corrige_formato=true;
		}//fin else

		if($corrige_formato==true)
		{
		$campo_corregido=$this->corrector_formato_fecha($campo_corregido,false,-2);
		}
		
		//valor permitido
		$excede_fecha_corte=$this->diferencia_dias_entre_fechas($campo_corregido,$date_de_corte);
		$es_menor_o_igual_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
		
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");

		//comparacion fecha campo actual es inferior a la fecha de nacimiento
			$fecha_nacimiento_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),"1900-12-31");
			$campo_actual_inferior_fecha_nacimiento=$this->diferencia_dias_entre_fechas($campo_corregido,trim($array_fields[9]));
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_nacimiento_es_mayor_a_1900_12_31<0
			&& $fecha_nacimiento_es_mayor_a_1900_12_31!==false
			&& $campo_actual_inferior_fecha_nacimiento>0
			&& $campo_actual_inferior_fecha_nacimiento!==false
			)
			{
				$campo_corregido="1800-01-01";
			}//fin if
			//fin comparacion fecha campo actual es inferior a la fecha de nacimiento

			//limite inferior fecha corte 2 years
			$fecha_corte_menos_2_years="";
			$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=false;
			$array_fecha_corte=explode("-", $date_de_corte);
			if(count($array_fecha_corte)==3)
			{
				$fecha_corte_menos_2_years=(intval($array_fecha_corte[0])-2)."-".$array_fecha_corte[1]."-".$array_fecha_corte[2];
				$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=$this->diferencia_dias_entre_fechas($campo_corregido,$fecha_corte_menos_2_years);
			}//fin if
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_corte_menos_2_years!=""
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years!==false
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years>0
			)
			{
				$campo_corregido="1845-01-01";
			}//fin if
			//fin limite inferior fecha corte 2 years

		if($comparacion_a_1900_12_31<0 
					&& $excede_fecha_corte<0
					&& $comparacion_a_1900_12_31!==false
					&& $excede_fecha_corte!==false
					)
				{
					$campo_corregido="1800-01-01";
				}//fin comparacion excede la fecha de corte

		if($comparacion_a_1900_12_31>=0
			&& $campo_corregido!="1800-01-01"
			&& $campo_corregido!="1845-01-01"
			)
		{
			if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1845-01-01"
			   && $array_fields[10]=="M"
			   )
			{
			    //criterio 1
			    $campo_corregido="1845-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1845-01-01"
			   && $array_fields[10]=="F"
			   && $edad<35
			   )
			{
			    //criterio 2
			    $campo_corregido="1845-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1845-01-01"
				&& $array_fields[10]=="F"
				&& $edad>=35
				&& ($array_fields[97]=="2"  || $array_fields[97]=="3")
				)
			{
			    //criterio 3
			    $campo_corregido="1845-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1845-01-01"			
				&& $array_fields[10]=="F"
				&& $edad>=35
				&& $array_fields[97]!="2"
				&& $array_fields[97]!="3"
				&& ($array_fields[101]=="0" || $array_fields[101]=="999" )
			   )
			{
			    //criterio 4
			    $campo_corregido="1845-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1845-01-01"		
				&& $array_fields[10]=="F"
				&& $edad>=35
				&& $array_fields[97]!="2"
				&& $array_fields[97]!="3"
				&& $array_fields[101]!="0" 
				&& $array_fields[101]!="999" 
			   )
			{
			    //criterio 5
			    $campo_corregido="1800-01-01";
			}			
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1845-01-01"
				)
			{
			    //criterio 6
			    $campo_corregido="1845-01-01";
			}
		}//fin else if
	     
		//$campo_corregido=$this->corrector_valor_permitido_fecha_criterio_003($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
		$c99_es_menor_a_1900_12_31=$this->diferencia_dias_entre_fechas($array_fields[99],"1900-12-31");
		$c100_es_menor_a_c99=$this->diferencia_dias_entre_fechas($campo_corregido,$array_fields[99]);
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
		
		//calidad con fecha actividad
		$nueva_edad_year_actual=-1;
		$nueva_edad_meses_actual=-1;
		$nueva_edad_dias_actual=-1;
		    
		$array_fecha_nacimiento=explode("-",$array_fields[9]);
		$fecha_campo_actual=explode("-",$campo_corregido);
		if(count($fecha_campo_actual)==3 && checkdate($fecha_campo_actual[1],$fecha_campo_actual[2],$fecha_campo_actual[0]))
		{
		    //echo "<script>alert('entro');</script>";
		    
		    $array_calc_edad_actual=$this->edad_years_months_days($array_fecha_nacimiento[2]."-".$array_fecha_nacimiento[1]."-".$array_fecha_nacimiento[0],$fecha_campo_actual[2]."-".$fecha_campo_actual[1]."-".$fecha_campo_actual[0]);
		    $nueva_edad_year_actual=intval($array_calc_edad_actual['y']);
		    $nueva_edad_meses_actual=(intval($array_calc_edad_actual['y'])*12)+$array_calc_edad_actual['m'];
		    $nueva_edad_dias_actual=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),trim($array_fields[$numero_campo_a_corregir]));
		    
		    //echo "<script>alert(' EDAD DIAS $nueva_edad_dias_actual FN ".trim($array_fields[9])." FA ".trim($array_fields[$numero_campo_a_corregir])."  NCA $numero_campo_a_corregir');</script>";
		}
		$es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
		$es_menor_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
		//calidad c100
		if($campo_corregido!="1845-01-01"
		   && $array_fields[10]=="M"
		   )
		{
		    //criterio 1
		    $campo_corregido="1845-01-01";
		}
		else if($es_mayor_a_1900_12_31<0
			&& $array_fields[10]=="F"
			&& $nueva_edad_year_actual<35
		    )
		{
		    //criterio 2
		    $campo_corregido="1845-01-01";
		}
		else if(
			trim($array_fields[100])=="1800-01-01"
			&& $array_fields[10]=="F"
			&& $edad<35
			)
		{
		    //criterio 3
		    $campo_corregido="1845-01-01";
		}
		else if(
			trim($array_fields[100])=="1800-01-01"
			&& $array_fields[10]=="F"
			&& $edad>=35
			&& ( $array_fields[97]=="2" || $array_fields[97]=="3")
			)
		{
		    //criterio 4
		    $campo_corregido="1845-01-01";
		}
		else if($es_mayor_a_1900_12_31<0
			&& $array_fields[10]=="F"
			&& $nueva_edad_year_actual>=35
			&& ( $array_fields[97]=="2" || $array_fields[97]=="3")
			)
		
		{
		    //criterio 5
		    $campo_corregido="1845-01-01";
		}
		else if($es_mayor_a_1900_12_31<0		
			&& $array_fields[10]=="F"
			&& $nueva_edad_year_actual>=35
			&& $array_fields[97]!="2"
			&& $array_fields[97]!="3"
			&& ($array_fields[101]=="0"  )
		   )
		{
		    //criterio 6
		    $campo_corregido="1845-01-01";
		}
		else if(trim($array_fields[100])=="1800-01-01"		
			&& $array_fields[10]=="F"
			&& $edad>=35
			&& $array_fields[97]!="2"
			&& $array_fields[97]!="3"
			&& ($array_fields[101]=="0" || $array_fields[101]=="999" )
		   )
		{
		    //criterio 7
		    $campo_corregido="1845-01-01";
		}
		else if($campo_corregido=="1845-01-01"			
			&& $array_fields[10]=="F"
			&& $edad>=35
			&& $array_fields[97]!="2"
			&& $array_fields[97]!="3"
			&& $array_fields[101]!="0" 
			&& $array_fields[101]!="999" 
		   )
		{
		    //criterio 8
		    $campo_corregido="1800-01-01";
		}						
		else if($c100_es_menor_a_c99>=0 //o igual
			&& $c100_es_menor_a_c99!==false
			&& $comparacion_a_1900_12_31<0
			)
		{
			//criterio 9
		    $campo_corregido="1800-01-01";
		}
		
		
		
		
	     }//fin fase 1
	    }//revisado 100
	    
	    //campo a corregir es 101
	    if($numero_campo_a_corregir==101)
	    {
	    	$c100_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[100]),"1900-12-31");
	     if($fase_correccion==0)
	     {
			//campo en blanco
	     	 if($campo_corregido=="")
			{
				if($campo_corregido==""
				   && $array_fields[10]=="M"
				   )
				{
				    //criterio 1
				    $campo_corregido="0";
				}
				else if($campo_corregido==""
					&& $array_fields[10]=="F"
					&& $edad<35			
					)
				{
				    //criterio 2
				    $campo_corregido="0";
				}
				else if($campo_corregido==""		
					&& $array_fields[10]=="F"
					&& $edad>=35
					&& ($array_fields[97]=="2" || $array_fields[97]=="3")
					)
				{
				    //criterio 3
				    $campo_corregido="0";
				}
				else if($campo_corregido==""
					&& $array_fields[10]=="F"
					&& $edad>=35
					&& ($array_fields[97]!="2" && $array_fields[97]!="3")
					&& $array_fields[100]=="1845-01-01"		
					)
				{
				    //criterio 4
				    $campo_corregido="0";
				}
				else if($campo_corregido==""
					&& $array_fields[10]=="F"
					&& $edad>=35
					&& ($array_fields[97]!="2" && $array_fields[97]!="3")
					&& $c100_es_mayor_a_1900_12_31<0		
					)
				{
				    //criterio 5
				    $campo_corregido="999";
				}
				else if($campo_corregido=="")
				{
				    $campo_corregido="0";
				}
			}//fin if
		
		//valor permitido
			if($campo_corregido!="0"
			&& $campo_corregido!="1"
			&& $campo_corregido!="2"
			&& $campo_corregido!="3"
			&& $campo_corregido!="4"
			&& $campo_corregido!="5"
			&& $campo_corregido!="999"
		  	)
			{
				if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="3"
					&& $campo_corregido!="4"
					&& $campo_corregido!="5"
					&& $campo_corregido!="999"
				   && $array_fields[10]=="M"
				   )
				{
				    //criterio 1
				    $campo_corregido="0";
				}
				else if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="3"
					&& $campo_corregido!="4"
					&& $campo_corregido!="5"
					&& $campo_corregido!="999"
					&& $array_fields[10]=="F"
					&& $edad<35			
					)
				{
				    //criterio 2
				    $campo_corregido="0";
				}
				else if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="3"
					&& $campo_corregido!="4"
					&& $campo_corregido!="5"
					&& $campo_corregido!="999"	
					&& $array_fields[10]=="F"
					&& $edad>=35
					&& ($array_fields[97]=="2" || $array_fields[97]=="3")
					)
				{
				    //criterio 3
				    $campo_corregido="0";
				}
				else if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="3"
					&& $campo_corregido!="4"
					&& $campo_corregido!="5"
					&& $campo_corregido!="999"
					&& $array_fields[10]=="F"
					&& $edad>=35
					&& ($array_fields[97]!="2" && $array_fields[97]!="3")
					&& $array_fields[100]=="1845-01-01"		
					)
				{
				    //criterio 4
				    $campo_corregido="0";
				}
				else if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="3"
					&& $campo_corregido!="4"
					&& $campo_corregido!="5"
					&& $campo_corregido!="999"
					&& $array_fields[10]=="F"
					&& $edad>=35
					&& ($array_fields[97]!="2" && $array_fields[97]!="3")
					&& $c100_es_mayor_a_1900_12_31<0		
					)
				{
				    //criterio 4
				    $campo_corregido="999";
				}
				else if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="3"
					&& $campo_corregido!="4"
					&& $campo_corregido!="5"
					&& $campo_corregido!="999"
				  )
				{
				    $campo_corregido="0";
				}
			}//fin if 
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_031($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
			//calidad c101
			if($campo_corregido!="0"
			   && $array_fields[10]=="M"
			   )
			{
			    //criterio 1
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0"
				&& $array_fields[10]=="F"
				&& $edad<35			
				)
			{
			    //criterio 2
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0"		
				&& $array_fields[10]=="F"
				&& $edad>=35
				&& ($array_fields[97]=="2" || $array_fields[97]=="3")
				)
			{
			    //criterio 3
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0"
				&& $array_fields[10]=="F"
				&& $edad>=35
				&& ($array_fields[97]!="2" && $array_fields[97]!="3")
				&& $array_fields[100]=="1845-01-01"		
				)
			{
			    //criterio 4
			    $campo_corregido="0";
			}
			else if($campo_corregido=="0"
				&& $array_fields[10]=="F"
				&& $edad>=35
				&& ($array_fields[97]!="2"  && $array_fields[97]!="3")
				&& $c100_es_mayor_a_1900_12_31<0		
				)
			{
			    //criterio 5
			    $campo_corregido="999";
			}
			else if($campo_corregido=="999"
				&& $array_fields[10]=="F"
				&& $edad>=35
				&& ($array_fields[97]!="2"  && $array_fields[97]!="3")
				&& ($array_fields[100]=="1800-01-01")
				)//fin condicion
			{
			    //criterio 5
			    $campo_corregido="0";
			}//fin calidad
		
		
	     }//fin fase 1
	    }//revisado c101
	    
	    //campo a corregir es 102
	    if($numero_campo_a_corregir==102)
	    {
	       if($fase_correccion==0)
	       {
		
		//campo en blanco
		$c99es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[99]),"1900-12-31");
		

		if($campo_corregido=="")
		{
			if($campo_corregido==""
			   && $array_fields[10]=="M"
			   )
			{
			    //criterio 1
			    $campo_corregido="0";
			}
			else if($campo_corregido==""
				&& $array_fields[10]=="F"
				&& $edad<35
			    )
			{
			    //criterio 2
			    $campo_corregido="0";
			}
			else if($campo_corregido==""
				&& $array_fields[10]=="F"
				&& $edad>=35
				&& ($array_fields[97]=="2" || $array_fields[97]=="3")
				)
			{
			    //criterio 3
			    $campo_corregido="0";
			}
			else if($campo_corregido==""
				&& $array_fields[10]=="F"
				&& $edad>=35
				&& (trim($array_fields[97])!="2" && trim($array_fields[97])!="3")	
				&& $array_fields[101]=="0"			
				)
			{
			    //criterio 4
			    $campo_corregido="0";
			}
			else if($campo_corregido==""
				&& $array_fields[10]=="F"
				&& $edad>=35
				&& (trim($array_fields[97])!="2" && trim($array_fields[97])!="3") 
				&& $array_fields[101]!="0"	
				)
			{
			    //criterio 5
			    $campo_corregido="999";
			}
			else if($campo_corregido=="")
			{
			    $campo_corregido="0";
			}
		}//fin if
		
		
		//formato
	      if(strlen($campo_corregido)==11 &&
	       (substr($campo_corregido,0,1)=="5" || substr($campo_corregido,0,1)=="8")
	       )
		{
		    $campo_corregido="0".$campo_corregido;
		}
		else if(strlen($campo_corregido)==10)
		{
		    $campo_corregido=$campo_corregido."01";
		}
		
		
		//valor permitido
		$query_bd="";
		$query_bd.="SELECT * FROM gios_prestador_servicios_salud WHERE cod_registro_especial_pss = '".$campo_corregido."' ";		
		$query_bd.=";";
		$resultados_query=$conexion_bd_correccion->consultar2_no_crea_cierra($query_bd);
		
		$c99es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[99]),"1900-12-31");

		if($campo_corregido!="0" && $campo_corregido!="999"
			&& (!is_array($resultados_query) || count($resultados_query)==0) 
			)
		{		
			if($campo_corregido!="0" && $campo_corregido!="999"
				&& (!is_array($resultados_query) || count($resultados_query)==0)
			   && $array_fields[10]=="M"
			   )
			{
			    //criterio 1
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0" && $campo_corregido!="999"
				&& (!is_array($resultados_query) || count($resultados_query)==0)
				&& $array_fields[10]=="F"
				&& $edad<35
			    )
			{
			    //criterio 2
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0" && $campo_corregido!="999"
				&& (!is_array($resultados_query) || count($resultados_query)==0)
				&& $array_fields[10]=="F"
				&& $edad>=35
				&& ($array_fields[97]=="2" || $array_fields[97]=="3")
				)
			{
			    //criterio 3
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0" && $campo_corregido!="999"
				&& (!is_array($resultados_query) || count($resultados_query)==0)
				&& $array_fields[10]=="F"
				&& $edad>=35
				&& (trim($array_fields[97])!="2" && trim($array_fields[97])!="3") 
				&& $array_fields[101]=="0"			
				)
			{
			    //criterio 4
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0" && $campo_corregido!="999"
				&& (!is_array($resultados_query) || count($resultados_query)==0)
				&& $array_fields[10]=="F"
				&& $edad>=35
				&& (trim($array_fields[97])!="2" && trim($array_fields[97])!="3") 
				&& $array_fields[101]!="0"	

				)
			{
			    //criterio 5
			    $campo_corregido="999";
			}
			else if($campo_corregido!="0" && $campo_corregido!="999"
				&& (!is_array($resultados_query) || count($resultados_query)==0) 
				)
			{
			    $campo_corregido="0";
			}
		}//fin else
		
		
		
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_020($campo_corregido,$conexion_bd_correccion);
	       }
	       else if($fase_correccion==1)
	       {
				$query_bd="";
				$query_bd.="SELECT * FROM gios_prestador_servicios_salud WHERE cod_registro_especial_pss = '".$campo_corregido."' ";		
				$query_bd.=";";
				$resultados_query=$conexion_bd_correccion->consultar2_no_crea_cierra($query_bd);
				
				$c99es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[99]),"1900-12-31");
				
				//calidad c102
				if($campo_corregido!="0"
				   && $array_fields[10]=="M"
				   )
				{
				    //criterio 1
				    $campo_corregido="0";
				}
				else if($campo_corregido!="0"
					&& $array_fields[10]=="F"
					&& $edad<35
				    )
				{
				    //criterio 2
				    $campo_corregido="0";
				}
				else if($campo_corregido!="0"
					&& $array_fields[10]=="F"
					&& $edad>=35
					&& ($array_fields[97]=="2" || $array_fields[97]=="3")
					)
				{
				    //criterio 3
				    $campo_corregido="0";
				}
				else if($campo_corregido!="0"
					&& $array_fields[10]=="F"
					&& $edad>=35
					&& (trim($array_fields[97])!="2"  && trim($array_fields[97])!="3") 
					&& $array_fields[101]=="0"			
					)
				{
				    //criterio 4
				    $campo_corregido="0";
				}
				else if($campo_corregido=="0"
					&& $array_fields[10]=="F"
					&& $edad>=35
					&& (trim($array_fields[97])!="2" && trim($array_fields[97])!="3") 	
					&& $array_fields[101]!="0"		
					)
				{
				    //criterio 5
				    $campo_corregido="999";
				}
		
		
		
	       }//fin fase 1
	    }//revisado c102
	    //FIN CAMPOS CANCER DE SENO
	    
	    //CAMPOS ASOCIADOS A VACUNACION
	    
	    //campo a corregir es 35
	    if($numero_campo_a_corregir==35)
	    {
	     if($fase_correccion==0)
	     {
		//campo en blanco
	     	if($campo_corregido=="")
	     	{
				if($campo_corregido=="" && $edad>=6)
				{
				    $campo_corregido="0";
				}
				else if($edad<6 && $campo_corregido=="")
				{
				    $campo_corregido="22";
				}
			}//fin if esta en blanco
		
		//valor permitido
			if($campo_corregido!="0"
			&& $campo_corregido!="1"
			&& $campo_corregido!="16"
			&& $campo_corregido!="17"
			&& $campo_corregido!="18"
			&& $campo_corregido!="19"
			&& $campo_corregido!="20"
			&& $campo_corregido!="22")
			{
				if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="16"
					&& $campo_corregido!="17"
					&& $campo_corregido!="18"
					&& $campo_corregido!="19"
					&& $campo_corregido!="20"
					&& $campo_corregido!="22"
					&& $edad>=6
					)
				{
				    $campo_corregido="0";
				}
				else if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="16"
					&& $campo_corregido!="17"
					&& $campo_corregido!="18"
					&& $campo_corregido!="19"
					&& $campo_corregido!="20"
					&& $campo_corregido!="22"
					&& $edad<6
					)
				{
				    $campo_corregido="22";
				}
				else if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="16"
					&& $campo_corregido!="17"
					&& $campo_corregido!="18"
					&& $campo_corregido!="19"
					&& $campo_corregido!="20"
					&& $campo_corregido!="22")
				{
				    $campo_corregido="0";
				}
			}//fin if
		
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_011($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
			//calidad
			if($edad>=6 && $campo_corregido!="0")
			{
			    $campo_corregido="0";
			}
			else if($edad<6 && $campo_corregido!="22")
			{
			    $campo_corregido="22";
			}//fin else
			
	     }//fin fase 1
	    }//fin campo 35 
	    
	    //campo a corregir es 36
	    if($numero_campo_a_corregir==36)
	    {
	     if($fase_correccion==0)
	     {
			//campo en blanco
	     	if($campo_corregido=="")
	     	{
				if($campo_corregido=="" && $edad>=6)
				{
				    $campo_corregido="0";
				}
				else if($edad<6 && $campo_corregido=="")
				{
				    $campo_corregido="22";
				}
			}//fin if
		
		//valor permitido
			if($campo_corregido!="0"
			&& $campo_corregido!="1"
			&& $campo_corregido!="16"
			&& $campo_corregido!="17"
			&& $campo_corregido!="18"
			&& $campo_corregido!="19"
			&& $campo_corregido!="20"
			&& $campo_corregido!="22")
		{
			if($campo_corregido!="0"
				&& $campo_corregido!="1"
				&& $campo_corregido!="16"
				&& $campo_corregido!="17"
				&& $campo_corregido!="18"
				&& $campo_corregido!="19"
				&& $campo_corregido!="20"
				&& $campo_corregido!="22"
				&& $edad>=6
				)
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0"
				&& $campo_corregido!="1"
				&& $campo_corregido!="16"
				&& $campo_corregido!="17"
				&& $campo_corregido!="18"
				&& $campo_corregido!="19"
				&& $campo_corregido!="20"
				&& $campo_corregido!="22"
				&& $edad<6 )
			{
			    $campo_corregido="22";
			}
			else if($campo_corregido!="0"
				&& $campo_corregido!="1"
				&& $campo_corregido!="16"
				&& $campo_corregido!="17"
				&& $campo_corregido!="18"
				&& $campo_corregido!="19"
				&& $campo_corregido!="20"
				&& $campo_corregido!="22")
			{
			    $campo_corregido="0";
			}

		}//fin if
		
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_011($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
		//calidad
		if($edad>=6 && $campo_corregido!="0")
		{
		    $campo_corregido="0";
		}
		else if($edad<6 && $campo_corregido!="22")
		{
		    $campo_corregido="22";
		}
	     }//fin fase 1
	    }//fin campo 36
	    
	    //campo a corregir es 37
	    if($numero_campo_a_corregir==37)
	    {
	     if($fase_correccion==0)
	     {
		//campo en blanco
	     	if($campo_corregido=="")
	     	{
				if($campo_corregido=="" && $edad>=6)
				{
				    $campo_corregido="0";
				}
				else if($edad<6 && $campo_corregido=="")
				{
				    $campo_corregido="22";
				}
				else if($campo_corregido=="")
				{
				    $campo_corregido="0";
				}
			}//fin campo en blanco
		
		//valor permitido

			 if($campo_corregido!="0"
			&& $campo_corregido!="1"
			&& $campo_corregido!="2"
			&& $campo_corregido!="3"
			&& $campo_corregido!="16"
			&& $campo_corregido!="17"
			&& $campo_corregido!="18"
			&& $campo_corregido!="19"
			&& $campo_corregido!="20"
			&& $campo_corregido!="22"
			)
			 {
				if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="3"
					&& $campo_corregido!="16"
					&& $campo_corregido!="17"
					&& $campo_corregido!="18"
					&& $campo_corregido!="19"
					&& $campo_corregido!="20"
					&& $campo_corregido!="22"
					&& $edad>=6
					)
				{
				    $campo_corregido="0";
				}
				else if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="3"
					&& $campo_corregido!="16"
					&& $campo_corregido!="17"
					&& $campo_corregido!="18"
					&& $campo_corregido!="19"
					&& $campo_corregido!="20"
					&& $campo_corregido!="22"
					&& $edad<6
					)
				{
				    $campo_corregido="22";
				}
				else if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="3"
					&& $campo_corregido!="16"
					&& $campo_corregido!="17"
					&& $campo_corregido!="18"
					&& $campo_corregido!="19"
					&& $campo_corregido!="20"
					&& $campo_corregido!="22"
					)
				{
				    $campo_corregido="0";
				}
			}//fin if
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_012($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
		//calidad
		if($edad>=6 && $campo_corregido!="0")
		{
		    $campo_corregido="0";
		}
		else if($edad<6 
			&& $campo_corregido!="22"
			)
		{
		    $campo_corregido="22";
		}
		/*
		else if(
			($campo_corregido=="3") 
			&& $edad_meses<6
			)
		{
		    $campo_corregido="2";
		}	
		else if(
			($campo_corregido=="2") 
			&& $edad_meses<4
			)
		{
		    $campo_corregido="1";
		}	
		*/

		/*
		else if($edad<6 
			&& $campo_corregido=="0"
			&& ($NOMBRE_ENTIDAD_PERSONALIZADA=="PrepagadaCoomeva")
			)
		{
		    $campo_corregido="22";
		}
		else if($edad<6 && $campo_corregido=="0" 
			&& ($NOMBRE_ENTIDAD_PERSONALIZADA!="PrepagadaCoomeva")
			 )
		{
		    $campo_corregido="22";
		}
		else if(($campo_corregido=="2" || $campo_corregido=="3")
			&& $edad_meses<4
			&& ($NOMBRE_ENTIDAD_PERSONALIZADA!="PrepagadaCoomeva")
		    )
		{
		    $campo_corregido="1";
		}
		else if(
			($campo_corregido=="3") 
			&& $edad_meses>=4 && $edad_meses<6
			&& ($NOMBRE_ENTIDAD_PERSONALIZADA!="PrepagadaCoomeva")
			)
		{
		    $campo_corregido="2";
		}		
		else if(
			($campo_corregido=="1") 
			&& $edad_meses>=4 && $edad_meses<6
			&& ($NOMBRE_ENTIDAD_PERSONALIZADA!="PrepagadaCoomeva")
			 )
		{
		    $campo_corregido="2";
		}
		else if(
			($campo_corregido=="1") 
			&& $edad_meses>=6 && $edad<6
			&& ($NOMBRE_ENTIDAD_PERSONALIZADA!="PrepagadaCoomeva")
			)
		{
		    $campo_corregido="3";
		}
		*/
		
		
	     }//fin fase 1
	    }//fin campo 
	    
	    //campo a corregir es 38
	    if($numero_campo_a_corregir==38)
	    {
	     if($fase_correccion==0)
	     {
		//campo en blanco
	     	if($campo_corregido=="")
	     	{
				if($campo_corregido=="" && $edad>=6)
				{
				    $campo_corregido="0";
				}
				else if($edad<6 && $campo_corregido=="")
				{
				    $campo_corregido="22";
				}
				else if($campo_corregido=="")
				{
				    $campo_corregido="0";
				}
			}//fin if
		
		//valor permitido
			if($campo_corregido!="0"
			&& $campo_corregido!="1"
			&& $campo_corregido!="2"
			&& $campo_corregido!="3"
			&& $campo_corregido!="4"
			&& $campo_corregido!="5"
			&& $campo_corregido!="16"
			&& $campo_corregido!="17"
			&& $campo_corregido!="18"
			&& $campo_corregido!="19"
			&& $campo_corregido!="20"
			&& $campo_corregido!="22")
		{
			if($campo_corregido!="0"
				&& $campo_corregido!="1"
				&& $campo_corregido!="2"
				&& $campo_corregido!="3"
				&& $campo_corregido!="4"
				&& $campo_corregido!="5"
				&& $campo_corregido!="16"
				&& $campo_corregido!="17"
				&& $campo_corregido!="18"
				&& $campo_corregido!="19"
				&& $campo_corregido!="20"
				&& $campo_corregido!="22"
				&& $edad>=6
				)
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0"
				&& $campo_corregido!="1"
				&& $campo_corregido!="2"
				&& $campo_corregido!="3"
				&& $campo_corregido!="4"
				&& $campo_corregido!="5"
				&& $campo_corregido!="16"
				&& $campo_corregido!="17"
				&& $campo_corregido!="18"
				&& $campo_corregido!="19"
				&& $campo_corregido!="20"
				&& $campo_corregido!="22"
				&& $edad<6
				)
			{
			    $campo_corregido="22";
			}
			else if($campo_corregido!="0"
				&& $campo_corregido!="1"
				&& $campo_corregido!="2"
				&& $campo_corregido!="3"
				&& $campo_corregido!="4"
				&& $campo_corregido!="5"
				&& $campo_corregido!="16"
				&& $campo_corregido!="17"
				&& $campo_corregido!="18"
				&& $campo_corregido!="19"
				&& $campo_corregido!="20"
				&& $campo_corregido!="22")
			{
			    $campo_corregido="0";
			}
		}//fin if valor permitido
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_014($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
		//calidad
		if($edad>=6 && $campo_corregido!="0")
		{
		    $campo_corregido="0";
		}
		else if($edad<6 
			&& $campo_corregido!="22"
			)
		{
		    $campo_corregido="22";
		}
		/*
		else if( ($campo_corregido=="2"
			  || $campo_corregido=="3"
			  || $campo_corregido=="4"
			  || $campo_corregido=="5"
			  )
			&& $edad_meses<4
			)
		{
		    $campo_corregido="1";
		}
		else if( ($campo_corregido=="3"
			  || $campo_corregido=="4"
			  || $campo_corregido=="5"
			  )
			&& $edad_meses<6 
			)
		{
		    $campo_corregido="2";
		}
		else if( 
			(
				 $campo_corregido=="4"
			  || $campo_corregido=="5"
			  )
			&& $edad<1 
			)
		{
		    $campo_corregido="3";
		}
		else if( 
			 $campo_corregido=="5"
			 
			&& $edad_meses<18 
			)
		{
		    $campo_corregido="4";
		}
		*/

		/*
		else if($edad<6 
			&& $campo_corregido!="22"
			&& ($NOMBRE_ENTIDAD_PERSONALIZADA=="PrepagadaCoomeva")
			)
		{
		    $campo_corregido="22";
		}
		else if($edad<6 && $campo_corregido=="0" 
			&& ($NOMBRE_ENTIDAD_PERSONALIZADA!="PrepagadaCoomeva")
			 )
		{
		    $campo_corregido="22";
		}
		else if( ($campo_corregido=="2"
			  || $campo_corregido=="3"
			  || $campo_corregido=="4"
			  || $campo_corregido=="5"
			  )
			&& $edad_meses<4
			&& ($NOMBRE_ENTIDAD_PERSONALIZADA!="PrepagadaCoomeva")
			)
		{
		    $campo_corregido="1";
		}
		else if( ($campo_corregido=="3"
			  || $campo_corregido=="4"
			  || $campo_corregido=="5"
			  )
			&& $edad_meses<4 
			&& ($NOMBRE_ENTIDAD_PERSONALIZADA!="PrepagadaCoomeva")
			)
		{
		    $campo_corregido="1";
		}
		else if( ($campo_corregido=="3"
			  || $campo_corregido=="4"
			  || $campo_corregido=="5"
			  )
			&& $edad_meses>=4 && $edad_meses<6
			&& ($NOMBRE_ENTIDAD_PERSONALIZADA!="PrepagadaCoomeva")
			)
		{
		    $campo_corregido="2";
		}
		else if( ($campo_corregido=="4"
			  || $campo_corregido=="5"
			  )
			&& $edad_meses<4
			&& ($NOMBRE_ENTIDAD_PERSONALIZADA!="PrepagadaCoomeva")
			)
		{
		    $campo_corregido="1";
		}
		else if( ($campo_corregido=="4"
			  || $campo_corregido=="5"
			  )
			&& $edad_meses>=4 && $edad_meses<6
			&& ($NOMBRE_ENTIDAD_PERSONALIZADA!="PrepagadaCoomeva")
			)
		{
		    $campo_corregido="2";
		}
		else if( ($campo_corregido=="4"
			  || $campo_corregido=="5"
			  )
			&& $edad_meses>=6 && $edad_meses<12
			&& ($NOMBRE_ENTIDAD_PERSONALIZADA!="PrepagadaCoomeva")
			)
		{
		    $campo_corregido="3";
		}
		else if( $campo_corregido=="5"			  
			&& $edad_meses<4
			&& ($NOMBRE_ENTIDAD_PERSONALIZADA!="PrepagadaCoomeva")
			)
		{
		    $campo_corregido="1";
		}
		else if( $campo_corregido=="5"			  
			&& $edad_meses>=4 && $edad_meses<6
			&& ($NOMBRE_ENTIDAD_PERSONALIZADA!="PrepagadaCoomeva")
			)
		{
		    $campo_corregido="2";
		}
		else if( $campo_corregido=="5"			  
			&& $edad_meses>=6 && $edad_meses<12
			&& ($NOMBRE_ENTIDAD_PERSONALIZADA!="PrepagadaCoomeva")
			)
		{
		    $campo_corregido="3";
		}
		else if( $campo_corregido=="5"			  
			&& $edad_meses>=12 && $edad_meses<18
			&& ($NOMBRE_ENTIDAD_PERSONALIZADA!="PrepagadaCoomeva")
			)
		{
		    $campo_corregido="4";
		}
		*/
		
	     }//fin fase 1
	    }//fin campo 38
	    
	    //campo a corregir es 39
	    if($numero_campo_a_corregir==39)
	    {
	     if($fase_correccion==0)
	     {
			//campo en blanco
	     	if($campo_corregido=="")
	     	{
				if($campo_corregido==""   && ($edad>=6 || $edad_meses<18) )
				{
				    $campo_corregido="0";
				}
				else if($edad<6 && $edad_meses>=18 && $campo_corregido=="")
				{
				    $campo_corregido="22";
				}
				else if($campo_corregido=="" )
				{
				    $campo_corregido="0";
				}
			}//fin if
		
			//valor permitido
				if($campo_corregido!="0"
				&& $campo_corregido!="4"
				&& $campo_corregido!="5"
				&& $campo_corregido!="16"
				&& $campo_corregido!="17"
				&& $campo_corregido!="18"
				&& $campo_corregido!="19"
				&& $campo_corregido!="20"
				&& $campo_corregido!="22"
				)
			{
				if($campo_corregido!="0"
					&& $campo_corregido!="4"
					&& $campo_corregido!="5"
					&& $campo_corregido!="16"
					&& $campo_corregido!="17"
					&& $campo_corregido!="18"
					&& $campo_corregido!="19"
					&& $campo_corregido!="20"
					&& $campo_corregido!="22"
					&& ($edad>=6 || $edad_meses<18)
					)
				{
				    $campo_corregido="0";
				}
				else if($campo_corregido!="0"
					&& $campo_corregido!="4"
					&& $campo_corregido!="5"
					&& $campo_corregido!="16"
					&& $campo_corregido!="17"
					&& $campo_corregido!="18"
					&& $campo_corregido!="19"
					&& $campo_corregido!="20"
					&& $campo_corregido!="22"
					&& $edad<6 && $edad_meses>=18
					)
				{
				    $campo_corregido="22";
				}
				else if($campo_corregido!="0"
					&& $campo_corregido!="4"
					&& $campo_corregido!="5"
					&& $campo_corregido!="16"
					&& $campo_corregido!="17"
					&& $campo_corregido!="18"
					&& $campo_corregido!="19"
					&& $campo_corregido!="20"
					&& $campo_corregido!="22"
					)
				{
				    $campo_corregido="0";
				}
			}//fin if valor permitido
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_015($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
			//calidad
			if( ($edad>=6 || $edad_meses<18) && $campo_corregido!="0")
			{
			    $campo_corregido="0";
			}
			else if($edad<6 
				&& $edad_meses>=18 
				&& $campo_corregido!="22" 
				 )
			{
			    $campo_corregido="22";
			}
			/*
			else if($edad<5 
				&& $edad_meses>18 
				&& $campo_corregido=="5" 
				 )
			{
			    $campo_corregido="4";
			}
			*/

		/*
		else if($edad<6 
			&& $edad_meses>=18 
			&& $campo_corregido!="22"
			&& ($NOMBRE_ENTIDAD_PERSONALIZADA=="PrepagadaCoomeva")
			)
		{
		    $campo_corregido="22";
		}
		else if($edad<6 
			&& $edad_meses>=18 
			&& $campo_corregido=="0" 
			&& ($NOMBRE_ENTIDAD_PERSONALIZADA!="PrepagadaCoomeva")
			 )
		{
		    $campo_corregido="22";
		}
		else if($campo_corregido=="4"
			&& $edad_meses>=60 && $edad<6
			&& ($NOMBRE_ENTIDAD_PERSONALIZADA!="PrepagadaCoomeva")
			)
		{
		    $campo_corregido="5";
		}		
		else if($campo_corregido=="5"
			&& $edad_meses<60 && $edad_meses>=18
			&& ($NOMBRE_ENTIDAD_PERSONALIZADA!="PrepagadaCoomeva")
			)
		{
		    $campo_corregido="4";
		}
		*/
		
		
	     }//fin fase 1
	    }//fin campo 39
	    
	    //campo a corregir es 40
	    if($numero_campo_a_corregir==40)
	    {
	     if($fase_correccion==0)
	     {
		//campo en blanco
	     	if($campo_corregido=="")
	     	{
				if($campo_corregido=="" && $edad>=6)
				{
				    $campo_corregido="0";
				}
				else if($edad<6 && $campo_corregido=="")
				{
				    $campo_corregido="22";
				}
				else if($campo_corregido=="")
				{
				    $campo_corregido="0";
				}
			}//fin if
		
		//valor permitido
			if($campo_corregido!="0"
			&& $campo_corregido!="1"
			&& $campo_corregido!="2"
			&& $campo_corregido!="16"
			&& $campo_corregido!="17"
			&& $campo_corregido!="18"
			&& $campo_corregido!="19"
			&& $campo_corregido!="20"
			&& $campo_corregido!="22")
			{
				if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="16"
					&& $campo_corregido!="17"
					&& $campo_corregido!="18"
					&& $campo_corregido!="19"
					&& $campo_corregido!="20"
					&& $campo_corregido!="22"
					&& $edad<6
					)
				{
				    $campo_corregido="22";
				}
				else if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="16"
					&& $campo_corregido!="17"
					&& $campo_corregido!="18"
					&& $campo_corregido!="19"
					&& $campo_corregido!="20"
					&& $campo_corregido!="22"
					&& $edad>=6
					)
				{
				    $campo_corregido="0";
				}
				else if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="16"
					&& $campo_corregido!="17"
					&& $campo_corregido!="18"
					&& $campo_corregido!="19"
					&& $campo_corregido!="20"
					&& $campo_corregido!="22")
				{
				    $campo_corregido="0";
				}
			}//fin if vp
		
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_013($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
				//calidad c40
				if($edad>=6 && $campo_corregido!="0")
				{
				    $campo_corregido="0";
				}
				else if($edad<6 && $campo_corregido!="22" 
				 )
				{
				    $campo_corregido="22";
				}
				/*
				else if($campo_corregido=="2"
					&& $edad_meses<4
					)
				{
				    $campo_corregido="1";
				}
				*/
				
				/*
				else if($edad<6 
					&& $campo_corregido!="22"
					&& ($NOMBRE_ENTIDAD_PERSONALIZADA=="PrepagadaCoomeva")
					)
				{
				    $campo_corregido="22";
				}
				else if($edad<6 && $campo_corregido=="0" 
					&& ($NOMBRE_ENTIDAD_PERSONALIZADA!="PrepagadaCoomeva")
					 )
				{
				    $campo_corregido="22";
				}
				else if($campo_corregido=="2"
					&& $edad_meses<4
					&& ($NOMBRE_ENTIDAD_PERSONALIZADA!="PrepagadaCoomeva")
					)
				{
				    $campo_corregido="1";
				}
				else if($campo_corregido=="1"
					&& $edad_meses>=4
					&& ($NOMBRE_ENTIDAD_PERSONALIZADA!="PrepagadaCoomeva")
					)
				{
				    $campo_corregido="2";
				}
				*/
	     }//fin fase 1
	    }//campo 40
	    
	    //campo a corregir es 41
	    if($numero_campo_a_corregir==41)
	    {
	     if($fase_correccion==0)
	     {
		//campo en blanco
	     	if($campo_corregido=="")
	     	{
				if($campo_corregido=="" && $edad>=6)
				{
				    $campo_corregido="0";
				}
				else if($edad<6 && $campo_corregido=="")
				{
				    $campo_corregido="22";
				}
				else if($campo_corregido=="")
				{
				    $campo_corregido="0";
				}
			}//fin if
		
		//valor permitido

			if($campo_corregido!="0"
			&& $campo_corregido!="1"
			&& $campo_corregido!="2"
			&& $campo_corregido!="3"
			&& $campo_corregido!="16"
			&& $campo_corregido!="17"
			&& $campo_corregido!="18"
			&& $campo_corregido!="19"
			&& $campo_corregido!="20"
			&& $campo_corregido!="22")
			{
				 if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="3"
					&& $campo_corregido!="16"
					&& $campo_corregido!="17"
					&& $campo_corregido!="18"
					&& $campo_corregido!="19"
					&& $campo_corregido!="20"
					&& $campo_corregido!="22"
					&& $edad>=6
					)
				{
				    $campo_corregido="0";
				}
				else if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="3"
					&& $campo_corregido!="16"
					&& $campo_corregido!="17"
					&& $campo_corregido!="18"
					&& $campo_corregido!="19"
					&& $campo_corregido!="20"
					&& $campo_corregido!="22"
					&& $edad<6
					)
				{
				    $campo_corregido="22";
				}
				
				else if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="3"
					&& $campo_corregido!="16"
					&& $campo_corregido!="17"
					&& $campo_corregido!="18"
					&& $campo_corregido!="19"
					&& $campo_corregido!="20"
					&& $campo_corregido!="22")
				{
				    $campo_corregido="0";
				}
			}//fin if
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_012($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
		//calidad c41
		if($edad>=6 && $campo_corregido!="0")
		{
		    $campo_corregido="0";
		}
		else if($edad<6 && $campo_corregido!="22" 
			
			 )
		{
		    $campo_corregido="22";
		}
		/*
		else if(($campo_corregido=="3")
			&& $edad_meses<6
		    )
		{
		    $campo_corregido="2";
		}
		else if(($campo_corregido=="2")
			&& $edad_meses<4
		    )
		{
		    $campo_corregido="1";
		}
		*/
		/*
		else if($edad<6 
			&& $campo_corregido!="22"
			&& ($NOMBRE_ENTIDAD_PERSONALIZADA=="PrepagadaCoomeva")
			)
		{
		    $campo_corregido="22";
		}
		else if($edad<6 && $campo_corregido=="0" 
			&& ($NOMBRE_ENTIDAD_PERSONALIZADA!="PrepagadaCoomeva")
			 )
		{
		    $campo_corregido="22";
		}
		else if(($campo_corregido=="2" || $campo_corregido=="3")
			&& $edad_meses<4
			&& ($NOMBRE_ENTIDAD_PERSONALIZADA!="PrepagadaCoomeva")
		    )
		{
		    $campo_corregido="1";
		}
		else if(
			($campo_corregido=="3") && $edad_meses>=4 && $edad_meses<6
			&& ($NOMBRE_ENTIDAD_PERSONALIZADA!="PrepagadaCoomeva")
			)
		{
		    $campo_corregido="2";
		}		
		else if(
			($campo_corregido=="1") 
			&& $edad_meses>=4 && $edad_meses<6
			&& ($NOMBRE_ENTIDAD_PERSONALIZADA!="PrepagadaCoomeva")
			 )
		{
		    $campo_corregido="2";
		}
		else if(($campo_corregido=="1") 
			&& $edad_meses>=6 && $edad<6
			&& ($NOMBRE_ENTIDAD_PERSONALIZADA!="PrepagadaCoomeva")
			)
		{
		    $campo_corregido="3";
		}
		*/
		
		
		
	     }//fin fase 1
	    }//fin campo 41
	    
	    //campo a corregir es 42
	    if($numero_campo_a_corregir==42)
	    {
	     if($fase_correccion==0)
	     {
		//campo en blanco
	     	if($campo_corregido=="")
	     	{
				if($campo_corregido=="" && $edad>=6)
				{
				    $campo_corregido="0";
				}
				else if($edad<6 && $campo_corregido=="")
				{
				    $campo_corregido="22";
				}
				else if($campo_corregido=="")
				{
				    $campo_corregido="0";
				}
			}//fin if
		
		//valor permitido
			if($campo_corregido!="0"
			&& $campo_corregido!="1"
			&& $campo_corregido!="2"
			&& $campo_corregido!="3"
			&& $campo_corregido!="16"
			&& $campo_corregido!="17"
			&& $campo_corregido!="18"
			&& $campo_corregido!="19"
			&& $campo_corregido!="20"
			&& $campo_corregido!="22")
			{
				if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="3"
					&& $campo_corregido!="16"
					&& $campo_corregido!="17"
					&& $campo_corregido!="18"
					&& $campo_corregido!="19"
					&& $campo_corregido!="20"
					&& $campo_corregido!="22"
					&& $edad>=6
					)
				{
				    $campo_corregido="0";
				}				
				else if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="3"
					&& $campo_corregido!="16"
					&& $campo_corregido!="17"
					&& $campo_corregido!="18"
					&& $campo_corregido!="19"
					&& $campo_corregido!="20"
					&& $campo_corregido!="22"
					&& $edad<6
					)
				{
				    $campo_corregido="22";
				}
				else if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="3"
					&& $campo_corregido!="16"
					&& $campo_corregido!="17"
					&& $campo_corregido!="18"
					&& $campo_corregido!="19"
					&& $campo_corregido!="20"
					&& $campo_corregido!="22")
				{
				    $campo_corregido="0";
				}
			}//fin if
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_012($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
		//calidad c42
		if($edad>=6 && $campo_corregido!="0")
		{
		    $campo_corregido="0";
		}
		else if($edad<6 && $campo_corregido!="22" 
			 )
		{
		    $campo_corregido="22";
		}
		/*
		else if(
			($campo_corregido=="3")
			&& $edad_meses<6
		    )
		{
		    $campo_corregido="2";
		}
		else if(
			($campo_corregido=="2" )
			&& $edad_meses<4
		    )
		{
		    $campo_corregido="1";
		}
		*/

		/*
		else if($edad<6 
			&& $campo_corregido!="22"
			&& ($NOMBRE_ENTIDAD_PERSONALIZADA=="PrepagadaCoomeva")
			)
		{
		    $campo_corregido="22";
		}
		else if($edad<6 && $campo_corregido=="0" 
			&& ($NOMBRE_ENTIDAD_PERSONALIZADA!="PrepagadaCoomeva")
			 )
		{
		    $campo_corregido="22";
		}
		else if(
			($campo_corregido=="2" || $campo_corregido=="3")
			&& $edad_meses<4
			&& ($NOMBRE_ENTIDAD_PERSONALIZADA!="PrepagadaCoomeva")
		    )
		{
		    $campo_corregido="1";
		}
		else if(($campo_corregido=="3" || $campo_corregido=="1") 
			&& $edad_meses>=4 && $edad_meses<6
			&& ($NOMBRE_ENTIDAD_PERSONALIZADA!="PrepagadaCoomeva")
			)
		{
		    $campo_corregido="2";
		}
		else if(($campo_corregido=="1") 
			&& $edad_meses>=6 && $edad<6
			&& ($NOMBRE_ENTIDAD_PERSONALIZADA!="PrepagadaCoomeva")
			)
		{
		    $campo_corregido="3";
		}
		*/
		
	     }//fin if 
	    }//fin campo 42
	    
	    //campo a corregir es 43
	    if($numero_campo_a_corregir==43)
	    {
	     if($fase_correccion==0)
	     {
		//campo en blanco
	     	if($campo_corregido=="")
	     	{
				if($campo_corregido=="" && ($edad>=6 || $edad<1) )
				{
				    $campo_corregido="0";
				}
				else if($edad<6 && $edad>=1 && $campo_corregido=="")
				{
				    $campo_corregido="22";
				}
				else if($campo_corregido=="")
				{
					$campo_corregido="0";
				}//fin else if
			}//fin esta en blanco
		
		//valor permitido
		if($campo_corregido!="0"
			&& $campo_corregido!="1"
			&& $campo_corregido!="16"
			&& $campo_corregido!="17"
			&& $campo_corregido!="18"
			&& $campo_corregido!="19"
			&& $campo_corregido!="20"
			&& $campo_corregido!="22")
		{
			if($campo_corregido!="0"
				&& $campo_corregido!="1"
				&& $campo_corregido!="16"
				&& $campo_corregido!="17"
				&& $campo_corregido!="18"
				&& $campo_corregido!="19"
				&& $campo_corregido!="20"
				&& $campo_corregido!="22"
				&& ($edad>=6 || $edad<1)
				)
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0"
				&& $campo_corregido!="1"
				&& $campo_corregido!="16"
				&& $campo_corregido!="17"
				&& $campo_corregido!="18"
				&& $campo_corregido!="19"
				&& $campo_corregido!="20"
				&& $campo_corregido!="22"
				&& $edad<6 && $edad>=1
				)
			{
			    $campo_corregido="22";
			}
			else if($campo_corregido!="0"
				&& $campo_corregido!="1"
				&& $campo_corregido!="16"
				&& $campo_corregido!="17"
				&& $campo_corregido!="18"
				&& $campo_corregido!="19"
				&& $campo_corregido!="20"
				&& $campo_corregido!="22")
			{
			    $campo_corregido="0";
			}
		}//fin if 
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_011($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
			//calidad c43
			if( ($edad>=6 || $edad<1) && $campo_corregido!="0")
			{
			    $campo_corregido="0";
			}
			else if($edad<6 
				&& $edad>=1
				&& $campo_corregido!="22" 
				 )
			{
			    $campo_corregido="22";
			}//fin else if

		/*
		else if($edad<6 
			&& $edad_meses>=12
			&& $campo_corregido!="22"
			&& ($NOMBRE_ENTIDAD_PERSONALIZADA=="PrepagadaCoomeva")
			)
		{
		    $campo_corregido="22";
		}
		else if($edad<6 
			&& $edad_meses>=12
			&& $campo_corregido=="0" 
			&& ($NOMBRE_ENTIDAD_PERSONALIZADA!="PrepagadaCoomeva")
			 )
		{
		    $campo_corregido="22";
		}
		*/
		
	     }//fin fase 1
	    }//fin campo 43
	    
	    //campo a corregir es 44
	    if($numero_campo_a_corregir==44)
	    {
	     if($fase_correccion==0)
	     {
		//campo en blanco
	     	if($campo_corregido=="")
	     	{
				if($campo_corregido=="" && ($edad>=6 || $edad<1) )
				{
				    $campo_corregido="0";
				}
				else if($edad<6 && $edad>=1 && $campo_corregido=="")
				{
				    $campo_corregido="22";
				}
				else if($campo_corregido=="")
				{
				    $campo_corregido="0";
				}
			}//fin if
		
		//valor permitido
				if($campo_corregido!="0"
				&& $campo_corregido!="1"
				&& $campo_corregido!="16"
				&& $campo_corregido!="17"
				&& $campo_corregido!="18"
				&& $campo_corregido!="19"
				&& $campo_corregido!="20"
				&& $campo_corregido!="22")
			{

				if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="16"
					&& $campo_corregido!="17"
					&& $campo_corregido!="18"
					&& $campo_corregido!="19"
					&& $campo_corregido!="20"
					&& $campo_corregido!="22"
					&& ($edad>=6 || $edad<1)
					)
				{
				    $campo_corregido="0";
				}
				else if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="16"
					&& $campo_corregido!="17"
					&& $campo_corregido!="18"
					&& $campo_corregido!="19"
					&& $campo_corregido!="20"
					&& $campo_corregido!="22"
					&& $edad<6 && $edad>=1
					)
				{
				    $campo_corregido="22";
				}
				else if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="16"
					&& $campo_corregido!="17"
					&& $campo_corregido!="18"
					&& $campo_corregido!="19"
					&& $campo_corregido!="20"
					&& $campo_corregido!="22")
				{
				    $campo_corregido="0";
				}
			}//fin if
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_011($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
	     	//calidad c44
			if( ($edad>=6 || $edad<1) && $campo_corregido!="0")
			{
			    $campo_corregido="0";
			}
			else if($edad<6 
				&& $edad>=1
				&& $campo_corregido!="22" 
				 )
			{
			    $campo_corregido="22";
			}
			/*
			else if($edad<6 
			&& $edad_meses>=12
			&& $campo_corregido!="22"
			&& ($NOMBRE_ENTIDAD_PERSONALIZADA=="PrepagadaCoomeva")
			)
			{
			    $campo_corregido="22";
			}
			else if($edad<6 
				&& $edad_meses>=12
				&& $campo_corregido=="0" 
				&& ($NOMBRE_ENTIDAD_PERSONALIZADA!="PrepagadaCoomeva")
				 )
			{
			    $campo_corregido="22";
			}
			*/
	     }//fin fase 1
	    }//fin campo 44
	    
	    //campo a corregir es 45
	    if($numero_campo_a_corregir==45)
	    {
	     if($fase_correccion==0)
	     {
			//campo en blanco
	     	if($campo_corregido=="")
	     	{
				if($campo_corregido=="" && ($edad>=6 || $edad<1) )
				{
				    $campo_corregido="0";
				}
				else if($edad<6 && $edad>=1 && $campo_corregido=="")
				{
				    $campo_corregido="22";
				}
				else if($campo_corregido=="")
				{
				    $campo_corregido="0";
				}
			}//fin if
		
		//valor permitido
			if($campo_corregido!="0"
			&& $campo_corregido!="1"
			&& $campo_corregido!="16"
			&& $campo_corregido!="17"
			&& $campo_corregido!="18"
			&& $campo_corregido!="19"
			&& $campo_corregido!="20"
			&& $campo_corregido!="22")
		{
			if($campo_corregido!="0"
				&& $campo_corregido!="1"
				&& $campo_corregido!="16"
				&& $campo_corregido!="17"
				&& $campo_corregido!="18"
				&& $campo_corregido!="19"
				&& $campo_corregido!="20"
				&& $campo_corregido!="22"
				&& ($edad>=6 || $edad<1) 
				)
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0"
				&& $campo_corregido!="1"
				&& $campo_corregido!="16"
				&& $campo_corregido!="17"
				&& $campo_corregido!="18"
				&& $campo_corregido!="19"
				&& $campo_corregido!="20"
				&& $campo_corregido!="22"
				&& $edad<6 && $edad>=1
				)
			{
			    $campo_corregido="22";
			}
			else if($campo_corregido!="0"
				&& $campo_corregido!="1"
				&& $campo_corregido!="16"
				&& $campo_corregido!="17"
				&& $campo_corregido!="18"
				&& $campo_corregido!="19"
				&& $campo_corregido!="20"
				&& $campo_corregido!="22")
			{
			    $campo_corregido="0";
			}
		}//fin if
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_013($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
			//calidad c45
			if(($edad>=6 || $edad<1) && $campo_corregido!="0")
			{
			    $campo_corregido="0";
			}
			else if($edad<6 
				&& $edad>=1
				&& $campo_corregido!="22" 
				 )
			{
			    $campo_corregido="22";
			}
			/*
			else if($edad<5 
				&& $edad>=1
				&& $campo_corregido=="2" 
				 )
			{
			    $campo_corregido="1";
			}
			*/
			/*
			else if($edad<6 
			&& $edad_meses>=12
			&& $campo_corregido!="22"
			&& ($NOMBRE_ENTIDAD_PERSONALIZADA=="PrepagadaCoomeva")
			)
			{
			    $campo_corregido="22";
			}
			else if($edad<6 
				&& $edad_meses>=12
				&& $campo_corregido=="0" 
				&& ($NOMBRE_ENTIDAD_PERSONALIZADA!="PrepagadaCoomeva")
				 )
			{
			    $campo_corregido="22";
			}
			else if($edad_meses<60 
				&& $edad_meses>=12 
				&& $campo_corregido=="2"
				&& ($NOMBRE_ENTIDAD_PERSONALIZADA!="PrepagadaCoomeva")
				)
			{
			    $campo_corregido="1";
			}
			else if($edad<6 && $edad_meses>=60 
				&& $campo_corregido=="1"
				&& ($NOMBRE_ENTIDAD_PERSONALIZADA!="PrepagadaCoomeva")
			)
			{
			    $campo_corregido="2";
			}
			*/
		/*
		 *no aplica aun
		else if($edad<6 && $edad_meses>=60 && $campo_corregido=="1")
		{
		    $campo_corregido="2";
		}
		*/
	     }//fin if
	    }//fin campo 45
	    
	    //campo a corregir es 46
	    if($numero_campo_a_corregir==46)
	    {
	    	//echo "valores asociados al campo 46 ($campo_corregido), campo 10 sexo  ".$array_fields[10].", edad $edad, fecha nacimiento(".$array_fields[9].")<br>";
	     if($fase_correccion==0)
	     {
		//campo en blanco
	     	if($campo_corregido=="")
	     	{
				if($campo_corregido==""
				   && $array_fields[10]=="M"
				   )
				{
				    $campo_corregido="0";
				}
				else if($campo_corregido==""
				   && $array_fields[10]=="F"
				   && $edad<9
				   )
				{
				    $campo_corregido="0";
				}
				else if($campo_corregido==""
				   && $array_fields[10]=="F"
				   && $edad>=9
				   )
				{
				    $campo_corregido="22";
				}
				else if($campo_corregido=="")
				{
				    $campo_corregido="0";
				}
			}//fin if
		
		//valor permitido
			if($campo_corregido!="0"
			&& $campo_corregido!="1"
			&& $campo_corregido!="2"
			&& $campo_corregido!="3"
			&& $campo_corregido!="16"
			&& $campo_corregido!="17"
			&& $campo_corregido!="18"
			&& $campo_corregido!="19"
			&& $campo_corregido!="20"
			&& $campo_corregido!="22")
		{
			if($campo_corregido!="0"
				&& $campo_corregido!="1"
				&& $campo_corregido!="2"
				&& $campo_corregido!="3"
				&& $campo_corregido!="16"
				&& $campo_corregido!="17"
				&& $campo_corregido!="18"
				&& $campo_corregido!="19"
				&& $campo_corregido!="20"
				&& $campo_corregido!="22"
				&& $array_fields[10]=="M"
				)
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0"
				&& $campo_corregido!="1"
				&& $campo_corregido!="2"
				&& $campo_corregido!="3"
				&& $campo_corregido!="16"
				&& $campo_corregido!="17"
				&& $campo_corregido!="18"
				&& $campo_corregido!="19"
				&& $campo_corregido!="20"
				&& $campo_corregido!="22"
				&& $array_fields[10]=="F"
				&& $edad<9
				)
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0"
				&& $campo_corregido!="1"
				&& $campo_corregido!="2"
				&& $campo_corregido!="3"
				&& $campo_corregido!="16"
				&& $campo_corregido!="17"
				&& $campo_corregido!="18"
				&& $campo_corregido!="19"
				&& $campo_corregido!="20"
				&& $campo_corregido!="22"
				&& $array_fields[10]=="F"
				&& $edad>=9
				)
			{
			    $campo_corregido="22";
			}
			else if($campo_corregido!="0"
				&& $campo_corregido!="1"
				&& $campo_corregido!="2"
				&& $campo_corregido!="3"
				&& $campo_corregido!="16"
				&& $campo_corregido!="17"
				&& $campo_corregido!="18"
				&& $campo_corregido!="19"
				&& $campo_corregido!="20"
				&& $campo_corregido!="22")
			{
			    $campo_corregido="0";
			}
		}//fin if vp
		
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_012($campo_corregido);
		
	     }
	     else if($fase_correccion==1)
	     {
			//calidad
			if($array_fields[10]=="M" && $campo_corregido!="0")
			{
			    $campo_corregido="0";
			}
			else if( $array_fields[10]=="F" && $campo_corregido!="0" && $edad<9)
			{
			    $campo_corregido="0";
			}
			else if( $array_fields[10]=="F" 
				&& $campo_corregido!="22" 
				&& $edad>=9
				)
			{
			    $campo_corregido="22";
			}
			/*
			else if( $array_fields[10]=="F" 
				&& $campo_corregido!="22" 
				&& $edad>=9
				&& ($NOMBRE_ENTIDAD_PERSONALIZADA=="PrepagadaCoomeva")
				)
			{
			    $campo_corregido="22";
			}
			else if( $array_fields[10]=="F" 
				&& $campo_corregido=="0" 
				&& $edad>=9
				&& ($NOMBRE_ENTIDAD_PERSONALIZADA!="PrepagadaCoomeva")
				)
			{
			    $campo_corregido="22";
			}
			*/
			
	     }//fin fase 1
	     
	    }//fin if campo 46
	    
	    //campo a corregir es 47
	    if($numero_campo_a_corregir==47)
	    {
	     if($fase_correccion==0)
	     {
		//campo en blanco
	     	if($campo_corregido=="")
	     	{
				if($campo_corregido==""
				   && $array_fields[10]=="M"
				   )
				{
				    $campo_corregido="0";
				}
				else if($campo_corregido==""
				   && $array_fields[10]=="F"
				   && ($edad<10 || $edad>=50)
				   )
				{
				    $campo_corregido="0";
				}
				else if($campo_corregido==""
				   && $array_fields[10]=="F"
				   && $edad>=10 && $edad<50
				   )
				{
				    $campo_corregido="22";
				}
				else if($campo_corregido=="")
				{
				    $campo_corregido="0";
				}
			}//fin if
		
		//valor permitido
		if( $campo_corregido!="0"
		&& $campo_corregido!="1"
		&& $campo_corregido!="2"
		&& $campo_corregido!="3"
		&& $campo_corregido!="4"
		&& $campo_corregido!="5"
		&& $campo_corregido!="16"
		&& $campo_corregido!="17"
		&& $campo_corregido!="18"
		&& $campo_corregido!="19"
		&& $campo_corregido!="20"
		&& $campo_corregido!="22")
		{
			if( $campo_corregido!="0"
				&& $campo_corregido!="1"
				&& $campo_corregido!="2"
				&& $campo_corregido!="3"
				&& $campo_corregido!="4"
				&& $campo_corregido!="5"
				&& $campo_corregido!="16"
				&& $campo_corregido!="17"
				&& $campo_corregido!="18"
				&& $campo_corregido!="19"
				&& $campo_corregido!="20"
				&& $campo_corregido!="22"
				&& $array_fields[10]=="M"
				)
			{
			    $campo_corregido="0";
			}
			else if( $campo_corregido!="0"
				&& $campo_corregido!="1"
				&& $campo_corregido!="2"
				&& $campo_corregido!="3"
				&& $campo_corregido!="4"
				&& $campo_corregido!="5"
				&& $campo_corregido!="16"
				&& $campo_corregido!="17"
				&& $campo_corregido!="18"
				&& $campo_corregido!="19"
				&& $campo_corregido!="20"
				&& $campo_corregido!="22"
				&& $array_fields[10]=="F"
				&& ($edad<10 || $edad>=50)
				)
			{
			    $campo_corregido="0";
			}
			else if( $campo_corregido!="0"
				&& $campo_corregido!="1"
				&& $campo_corregido!="2"
				&& $campo_corregido!="3"
				&& $campo_corregido!="4"
				&& $campo_corregido!="5"
				&& $campo_corregido!="16"
				&& $campo_corregido!="17"
				&& $campo_corregido!="18"
				&& $campo_corregido!="19"
				&& $campo_corregido!="20"
				&& $campo_corregido!="22"
				&& $array_fields[10]=="F"
				&& $edad>=10 && $edad<50
				)
			{
			    $campo_corregido="22";
			}
			else if( $campo_corregido!="0"
				&& $campo_corregido!="1"
				&& $campo_corregido!="2"
				&& $campo_corregido!="3"
				&& $campo_corregido!="4"
				&& $campo_corregido!="5"
				&& $campo_corregido!="16"
				&& $campo_corregido!="17"
				&& $campo_corregido!="18"
				&& $campo_corregido!="19"
				&& $campo_corregido!="20"
				&& $campo_corregido!="22")
			{
			    $campo_corregido="0";
			}
		}//fin if
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_014($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
			//calidad
			if($array_fields[10]=="M" && $campo_corregido!="0")
			{
			    $campo_corregido="0";
			}
			else if( $array_fields[10]=="F" && $campo_corregido!="0" && ($edad<10 || $edad>=50))
			{
			    $campo_corregido="0";
			}
			else if( $array_fields[10]=="F" 
				&& $campo_corregido!="22" 
				&& $edad>=10 && $edad<50
				)
			{
			    $campo_corregido="22";
			}
			/*
			else if( $array_fields[10]=="F" 
				&& $campo_corregido!="22" 
				&& $edad>=10 && $edad<50
				&& ($NOMBRE_ENTIDAD_PERSONALIZADA=="PrepagadaCoomeva")
				)
			{
			    $campo_corregido="22";
			}
			else if( $array_fields[10]=="F" 
				&& $campo_corregido=="0" 
				&& $edad>=10 && $edad<50
				&& ($NOMBRE_ENTIDAD_PERSONALIZADA!="PrepagadaCoomeva")
				)
			{
			    $campo_corregido="22";
			}
			*/

	     }//fin fase 1
	    }//fin campo 47
	    
	    //campo a corregir es 48
	    if($numero_campo_a_corregir==48)
	    {
	     if($fase_correccion==0)
	     {
			//esta en blanco
	     	if($campo_corregido=="")
	     	{
				if($campo_corregido=="" && $edad<2)
				{
				    $campo_corregido="0";
				}
				else if($campo_corregido=="" && $edad>=2)
				{
					$campo_corregido="22";
				}
				else if($campo_corregido=="")
				{
					$campo_corregido="0";
				}
			}//fin if
		
		//valor permitido
			if($campo_corregido!="0"
		    && $campo_corregido!="1"
		    && $campo_corregido!="2"
		    && $campo_corregido!="16"
		    && $campo_corregido!="17"
		    && $campo_corregido!="18"
		    && $campo_corregido!="19"
		    && $campo_corregido!="20"
		    && $campo_corregido!="22"
		    )
		{
			if($campo_corregido!="0"
			    && $campo_corregido!="1"
			    && $campo_corregido!="2"
			    && $campo_corregido!="16"
			    && $campo_corregido!="17"
			    && $campo_corregido!="18"
			    && $campo_corregido!="19"
			    && $campo_corregido!="20"
			    && $campo_corregido!="22"
			    && $edad<2
			    )
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0"
			    && $campo_corregido!="1"
			    && $campo_corregido!="2"
			    && $campo_corregido!="16"
			    && $campo_corregido!="17"
			    && $campo_corregido!="18"
			    && $campo_corregido!="19"
			    && $campo_corregido!="20"
			    && $campo_corregido!="22"
			    && $edad>=2
			    )
			{
			    $campo_corregido="22";
			}
			else if($campo_corregido!="0"
			    && $campo_corregido!="1"
			    && $campo_corregido!="2"
			    && $campo_corregido!="16"
			    && $campo_corregido!="17"
			    && $campo_corregido!="18"
			    && $campo_corregido!="19"
			    && $campo_corregido!="20"
			    && $campo_corregido!="22"
			    )
			{
			    $campo_corregido="0";
			}
		}//fin if
		
		
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_013($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
		//echo "<script>alert(' ANTES campo_corregir $campo_corregido edad years $edad');</script>";
		//calidad
		if($campo_corregido!="0" && $edad<2)
		{
			$campo_corregido="0";
		}
		else if($campo_corregido=="0" && $edad>=2)
		{
			$campo_corregido="22";
		}
		else if($campo_corregido=="20" && $edad>=2)
		{
			$campo_corregido="22";
		}
			
			//echo "<script>alert(' DESPUES campo_corregir $campo_corregido edad years $edad');</script>";
	     }//fin fase 1
	    }//c48   
	    //FIN CAMPOS ASOCIADOS A VACUNACION
	    
	    //CAMPOS CONSULTAS
	    
	    //campo a corregir es 34
	    if($numero_campo_a_corregir==34)
	    {
	     if($fase_correccion==0)
	     {
		
		
		//campo en blanco
	     	if($campo_corregido=="")
	     	{
				if($campo_corregido=="" && $edad>=6)
				{
				    $campo_corregido="0";
				}
				else if($campo_corregido=="" && $edad<6)
				{
				    $campo_corregido="999";
				}
			}//fin if esta en blanco
		
		//deja solo entero
		$campo_corregido=trim($campo_corregido);
		$campo_corregido=str_replace(",",".",$campo_corregido);
		if(!ctype_digit($campo_corregido) && is_numeric($campo_corregido))
		{
		    $campo_corregido="".intval(round(floatval($campo_corregido),0,PHP_ROUND_HALF_UP));		    
		}
		
		//valor permitido
		if($campo_corregido!="0"
		   && $campo_corregido!="999"
		   && (intval($campo_corregido)<20 || is_numeric($campo_corregido)==false)
		   )
		{
		    $campo_corregido="999";
		}
		else if($campo_corregido!="0"
		   && $campo_corregido!="999"
		   && (intval($campo_corregido)>43 || is_numeric($campo_corregido)==false)
		   )
		{
		    $campo_corregido="999";
		}
		
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_021($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
		//calidad
		if($edad>=6 && $campo_corregido!="0")
		{
		    $campo_corregido="0";
		}
		else if($edad<6 && $campo_corregido=="0")
		{
		    $campo_corregido="999";
		}
		else if($campo_corregido!="999"
			&& $campo_corregido!="0"
			&& intval($campo_corregido)>43
			&& $edad<6
			)
		{
		    $campo_corregido="999";
		}
		else if($campo_corregido!="999"
			&& $campo_corregido!="0"
			&& intval($campo_corregido)<20
			&& $edad<6
			)
		{
		    $campo_corregido="999";
		}
		
		
	     }//fin fase 1
	    }//fin campo 34
	    
	    //campo a corregir es 52
	    if($numero_campo_a_corregir==52)
	    {
	     if($fase_correccion==0)
	     {
			//campo en blanco
	     	if($campo_corregido=="")
			{
				if($campo_corregido=="" && $edad_dias>90)
				{
				    $campo_corregido="1845-01-01";
				}
				else if($campo_corregido=="" && $edad_dias<90)
				{
				    $campo_corregido="1835-01-01";
				}
				else if($campo_corregido=="")
				{
				    $campo_corregido="1845-01-01";
				}
			}//fin if
		
			//formato
			$corrige_formato=false;
			$array_fecha_campo_actual=explode("-", $campo_corregido);
			if(count($array_fecha_campo_actual)==3)
			{
				if(ctype_digit($array_fecha_campo_actual[0]) && ctype_digit($array_fecha_campo_actual[1]) && ctype_digit($array_fecha_campo_actual[2]))
				{
					//checkdate mm-dd-aaaa -> aaaa-mm-dd ?
					if(checkdate($array_fecha_campo_actual[1],$array_fecha_campo_actual[2],$array_fecha_campo_actual[0])
				       && intval($array_fecha_campo_actual[0])>=32)
				    {
				    	//no se corrige formato
				    }
				    else
				    {
				    	$corrige_formato=true;
				    }

				}
				else
				{
					$corrige_formato=true;
				}
			}
			else
			{
				$corrige_formato=true;
			}//fin else

			if($corrige_formato==true)
			{
			$campo_corregido=$this->corrector_formato_fecha($campo_corregido,false,-2);
			}
			
			//valor permitido
			$excede_fecha_corte=$this->diferencia_dias_entre_fechas($campo_corregido,$date_de_corte);
			$es_menor_o_igual_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
			$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
			
			
			$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");

			//comparacion fecha campo actual es inferior a la fecha de nacimiento
			$fecha_nacimiento_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),"1900-12-31");
			$campo_actual_inferior_fecha_nacimiento=$this->diferencia_dias_entre_fechas($campo_corregido,trim($array_fields[9]));
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_nacimiento_es_mayor_a_1900_12_31<0
			&& $fecha_nacimiento_es_mayor_a_1900_12_31!==false
			&& $campo_actual_inferior_fecha_nacimiento>0
			&& $campo_actual_inferior_fecha_nacimiento!==false
			)
			{
				$campo_corregido="1800-01-01";
			}//fin if
			//fin comparacion fecha campo actual es inferior a la fecha de nacimiento

			//limite inferior fecha corte 2 years
			$fecha_corte_menos_2_years="";
			$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=false;
			$array_fecha_corte=explode("-", $date_de_corte);
			if(count($array_fecha_corte)==3)
			{
				$fecha_corte_menos_2_years=(intval($array_fecha_corte[0])-2)."-".$array_fecha_corte[1]."-".$array_fecha_corte[2];
				$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=$this->diferencia_dias_entre_fechas($campo_corregido,$fecha_corte_menos_2_years);
			}//fin if
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_corte_menos_2_years!=""
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years!==false
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years>0
			)
			{
				$campo_corregido="1845-01-01";
			}//fin if
			//fin limite inferior fecha corte 2 years

			if($comparacion_a_1900_12_31<0 
				&& $excede_fecha_corte<0
				&& $comparacion_a_1900_12_31!==false
				&& $excede_fecha_corte!==false
				)
			{
				$campo_corregido="1800-01-01";
			}//fin comparacion excede la fecha de corte

			if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01")
			{
				if($comparacion_a_1900_12_31>=0
					&& $campo_corregido!="1800-01-01"
					&& $campo_corregido!="1805-01-01"
					&& $campo_corregido!="1810-01-01"
					&& $campo_corregido!="1825-01-01"
					&& $campo_corregido!="1830-01-01"
					&& $campo_corregido!="1835-01-01"
					&& $campo_corregido!="1845-01-01"
					&& $edad_dias>90)
				{
				    $campo_corregido="1845-01-01";
				}
				else if($comparacion_a_1900_12_31>=0
					&& $campo_corregido!="1800-01-01"
					&& $campo_corregido!="1805-01-01"
					&& $campo_corregido!="1810-01-01"
					&& $campo_corregido!="1825-01-01"
					&& $campo_corregido!="1830-01-01"
					&& $campo_corregido!="1835-01-01"
					&& $campo_corregido!="1845-01-01"
					&& $edad_dias<=90)
				{
				    $campo_corregido="1835-01-01";
				}
				else if($comparacion_a_1900_12_31>=0
					&& $campo_corregido!="1800-01-01"
					&& $campo_corregido!="1805-01-01"
					&& $campo_corregido!="1810-01-01"
					&& $campo_corregido!="1825-01-01"
					&& $campo_corregido!="1830-01-01"
					&& $campo_corregido!="1835-01-01"
					&& $campo_corregido!="1845-01-01")
				{
				    $campo_corregido="1845-01-01";
				}
			}//fin else if vp
	     
		//$campo_corregido=$this->corrector_valor_permitido_fecha_criterio_002($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
			$nueva_edad_year_actual=-1;
			$nueva_edad_meses_actual=-1;
			$nueva_edad_dias_actual=-1;
			
			$array_fecha_nacimiento=explode("-",$array_fields[9]);
			$fecha_campo_actual=explode("-",$campo_corregido);
			if(count($fecha_campo_actual)==3 && checkdate($fecha_campo_actual[1],$fecha_campo_actual[2],$fecha_campo_actual[0]))
			{
			    //echo "<script>alert('entro');</script>";
			    
			    $array_calc_edad_actual=$this->edad_years_months_days($array_fecha_nacimiento[2]."-".$array_fecha_nacimiento[1]."-".$array_fecha_nacimiento[0],$fecha_campo_actual[2]."-".$fecha_campo_actual[1]."-".$fecha_campo_actual[0]);
			    $nueva_edad_year_actual=intval($array_calc_edad_actual['y']);
			    $nueva_edad_meses_actual=(intval($array_calc_edad_actual['y'])*12)+$array_calc_edad_actual['m'];
			    $nueva_edad_dias_actual=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),trim($array_fields[$numero_campo_a_corregir]));
			    
			    //echo "<script>alert(' EDAD DIAS $nueva_edad_dias_actual FN ".trim($array_fields[9])." FA ".trim($array_fields[$numero_campo_a_corregir])."  NCA $numero_campo_a_corregir');</script>";
			}
			
			$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
			
			
			$es_fecha_es_valida=false;
			$verificacion_fecha_corte_pos_year=-1;
			$verificacion_fecha_corte=-1;
			$diferencia_de_1900=-1;
			$array_fecha_correccion_actual=explode("-",$campo_corregido);
			if(count($array_fecha_correccion_actual)==3)
			{
			    //checkdate mm/dd/aaaa
			    if(checkdate($array_fecha_correccion_actual[1],$array_fecha_correccion_actual[2],$array_fecha_correccion_actual[0]))
			    {
				$es_fecha_es_valida=true;
				$interval = date_diff(new DateTime($campo_corregido),new DateTime($date_de_corte_posterior_year));
				$verificacion_fecha_corte_pos_year =(float)($interval->format("%r%a"));
				$interval = date_diff(new DateTime($campo_corregido),new DateTime($date_de_corte));
				$verificacion_fecha_corte =(float)($interval->format("%r%a"));
				$interval = date_diff(new DateTime($campo_corregido),new DateTime("1900-01-01"));
				$diferencia_de_1900 =(float)($interval->format("%r%a"));
			    }
			}
			
			$es_menor_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");

			$fecha_nacimiento_contra_campo_actual=$this->diferencia_dias_entre_fechas($campo_corregido,trim($array_fields[9]));
			
			//echo "<script>alert('edad_dias_fecha_corte $edad_dias');</script>";
			
			//calidad
			if($nueva_edad_dias_actual>30
				&& $edad_dias<=90
			   && $comparacion_a_1900_12_31<0
			)
			{
			 
			  $campo_corregido="1835-01-01";
			  
			}
			else if($nueva_edad_dias_actual>30
				&& $edad_dias>90
			   && $comparacion_a_1900_12_31<0
			)
			{
			 
			  $campo_corregido="1845-01-01";
			  
			}
			else if($edad_dias>90
				&& (trim($array_fields[52])=="1800-01-01"
				    || trim($array_fields[52])=="1805-01-01"
				    || trim($array_fields[52])=="1810-01-01"
				    || trim($array_fields[52])=="1825-01-01"
				    || trim($array_fields[52])=="1830-01-01"
				    || trim($array_fields[52])=="1835-01-01"
				    )
			)
			{
			    $campo_corregido="1845-01-01";
			}
			else if(
				$edad_dias<=90
				&& ($campo_corregido=="1845-01-01"
					|| $campo_corregido=="1800-01-01"
					)
			)
			{
			    $campo_corregido="1835-01-01";
			}//fin calidad
			
		
		
		
		
	     }//fin fase
	    }//fin campo 52
	    
	    //campo a corregir es 62
	    if($numero_campo_a_corregir==62)
	    {
	     if($fase_correccion==0)
	     {
		
		//campo en blanco
	     	if($campo_corregido=="")
	     	{
				if($campo_corregido==""
				   && ($edad==4 || $edad==11 || $edad==16 || $edad==45)
				   )
				{
				    $campo_corregido="1835-01-01";
				}
				else if($campo_corregido==""
				   && ($edad!=4 && $edad!=11 && $edad!=16 && $edad!=45)
				   )
				{
				    $campo_corregido="1845-01-01";
				}
				else if($campo_corregido=="")
				{
				    $campo_corregido="1845-01-01";
				}
			}//fin if
		
		//formato
		$corrige_formato=false;
		$array_fecha_campo_actual=explode("-", $campo_corregido);
		if(count($array_fecha_campo_actual)==3)
		{
			if(ctype_digit($array_fecha_campo_actual[0]) && ctype_digit($array_fecha_campo_actual[1]) && ctype_digit($array_fecha_campo_actual[2]))
			{
				//checkdate mm-dd-aaaa -> aaaa-mm-dd ?
				if(checkdate($array_fecha_campo_actual[1],$array_fecha_campo_actual[2],$array_fecha_campo_actual[0])
			       && intval($array_fecha_campo_actual[0])>=32)
			    {
			    	//no se corrige formato
			    }
			    else
			    {
			    	$corrige_formato=true;
			    }

			}
			else
			{
				$corrige_formato=true;
			}
		}
		else
		{
			$corrige_formato=true;
		}//fin else

		if($corrige_formato==true)
		{
		$campo_corregido=$this->corrector_formato_fecha($campo_corregido);
		}
		
		//valor permitido
		
		$excede_fecha_corte=$this->diferencia_dias_entre_fechas($campo_corregido,$date_de_corte);
		$es_menor_o_igual_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
		
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");

		//comparacion fecha campo actual es inferior a la fecha de nacimiento
			$fecha_nacimiento_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),"1900-12-31");
			$campo_actual_inferior_fecha_nacimiento=$this->diferencia_dias_entre_fechas($campo_corregido,trim($array_fields[9]));
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_nacimiento_es_mayor_a_1900_12_31<0
			&& $fecha_nacimiento_es_mayor_a_1900_12_31!==false
			&& $campo_actual_inferior_fecha_nacimiento>0
			&& $campo_actual_inferior_fecha_nacimiento!==false
			)
			{
				$campo_corregido="1800-01-01";
			}//fin if
			//fin comparacion fecha campo actual es inferior a la fecha de nacimiento

			//limite inferior fecha corte 2 years
			$fecha_corte_menos_2_years="";
			$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=false;
			$array_fecha_corte=explode("-", $date_de_corte);
			if(count($array_fecha_corte)==3)
			{
				$fecha_corte_menos_2_years=(intval($array_fecha_corte[0])-2)."-".$array_fecha_corte[1]."-".$array_fecha_corte[2];
				$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=$this->diferencia_dias_entre_fechas($campo_corregido,$fecha_corte_menos_2_years);
			}//fin if
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_corte_menos_2_years!=""
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years!==false
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years>0
			)
			{
				$campo_corregido="1845-01-01";
			}//fin if
			//fin limite inferior fecha corte 2 years

		if($comparacion_a_1900_12_31<0 
			&& $excede_fecha_corte<0
			&& $comparacion_a_1900_12_31!==false
			&& $excede_fecha_corte!==false
			)
		{
			$campo_corregido="1800-01-01";
		}//fin comparacion excede la fecha de corte

		if($comparacion_a_1900_12_31>=0
			&& $campo_corregido!="1800-01-01"
			&& $campo_corregido!="1805-01-01"
			&& $campo_corregido!="1810-01-01"
			&& $campo_corregido!="1825-01-01"
			&& $campo_corregido!="1830-01-01"
			&& $campo_corregido!="1835-01-01"
			&& $campo_corregido!="1845-01-01"
			)
		{
			if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& ($edad!=4 && $edad!=11 && $edad!=16 && $edad!=45)
				)
			{
			    $campo_corregido="1845-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& ($edad==4 || $edad==11 || $edad==16 || $edad==45)
				)
			{
			    $campo_corregido="1835-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				)
			{
			    $campo_corregido="1845-01-01";
			}
		}//fin if
		
		
		//$campo_corregido=$this->corrector_valor_permitido_fecha_criterio_002($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
			if(
				($campo_corregido=="1845-01-01" || $campo_corregido=="1800-01-01")
				&& ($edad==4 || $edad==11 || $edad==16 || $edad==45)
				)
			{
			    $campo_corregido="1835-01-01";
			}
			else if(
				(
					$campo_corregido=="1800-01-01"
					|| $campo_corregido=="1805-01-01"
					|| $campo_corregido=="1810-01-01"
					|| $campo_corregido=="1825-01-01"
					|| $campo_corregido=="1830-01-01"
					|| $campo_corregido=="1835-01-01"
					)
				&& ($edad!=4 && $edad!=11 && $edad!=16 && $edad!=45)
				)
			{
			    $campo_corregido="1845-01-01";
			}
		
	     }//fin fase 1
	    }//fin campo 62
	    
	    //campo a corregir es 63
	    if($numero_campo_a_corregir==63)
	    {
	     if($fase_correccion==0)
	     {
		//campo en blanco
	     	if($campo_corregido=="")
	     	{
				if($campo_corregido==""
				   && ($edad==55
				       || $edad==60
				       || $edad==65
				       || $edad==70
				       || $edad==75
				       || $edad==80
				       || $edad==85
				       || $edad==90
				       || $edad==95
				       || $edad==100
				       || $edad==105
				       || $edad==110
				       || $edad==115
				       || $edad==120
				       )
				   )
				{
				    $campo_corregido="1835-01-01";
				}
				else if($campo_corregido==""
				   && ($edad!=55
				       && $edad!=60
				       && $edad!=65
				       && $edad!=70
				       && $edad!=75
				       && $edad!=80
				       && $edad!=85
				       && $edad!=90
				       && $edad!=95
				       && $edad!=100
				       && $edad!=105
				       && $edad!=110
				       && $edad!=115
				       && $edad!=120
				       )
				   )
				{
				    $campo_corregido="1845-01-01";
				}
				else if($campo_corregido=="")
				{
				    $campo_corregido="1845-01-01";
				}
			}//fin if
		
		//formato
		$corrige_formato=false;
		$array_fecha_campo_actual=explode("-", $campo_corregido);
		if(count($array_fecha_campo_actual)==3)
		{
			if(ctype_digit($array_fecha_campo_actual[0]) && ctype_digit($array_fecha_campo_actual[1]) && ctype_digit($array_fecha_campo_actual[2]))
			{
				//checkdate mm-dd-aaaa -> aaaa-mm-dd ?
				if(checkdate($array_fecha_campo_actual[1],$array_fecha_campo_actual[2],$array_fecha_campo_actual[0])
			       && intval($array_fecha_campo_actual[0])>=32)
			    {
			    	//no se corrige formato
			    }
			    else
			    {
			    	$corrige_formato=true;
			    }

			}
			else
			{
				$corrige_formato=true;
			}
		}
		else
		{
			$corrige_formato=true;
		}//fin else

		if($corrige_formato==true)
		{
		$campo_corregido=$this->corrector_formato_fecha($campo_corregido);
		}
		
		//valor permitido
		
		$excede_fecha_corte=$this->diferencia_dias_entre_fechas($campo_corregido,$date_de_corte);
		$es_menor_o_igual_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
		
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");

		//comparacion fecha campo actual es inferior a la fecha de nacimiento
			$fecha_nacimiento_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),"1900-12-31");
			$campo_actual_inferior_fecha_nacimiento=$this->diferencia_dias_entre_fechas($campo_corregido,trim($array_fields[9]));
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_nacimiento_es_mayor_a_1900_12_31<0
			&& $fecha_nacimiento_es_mayor_a_1900_12_31!==false
			&& $campo_actual_inferior_fecha_nacimiento>0
			&& $campo_actual_inferior_fecha_nacimiento!==false
			)
			{
				$campo_corregido="1800-01-01";
			}//fin if
			//fin comparacion fecha campo actual es inferior a la fecha de nacimiento

			//limite inferior fecha corte 2 years
			$fecha_corte_menos_2_years="";
			$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=false;
			$array_fecha_corte=explode("-", $date_de_corte);
			if(count($array_fecha_corte)==3)
			{
				$fecha_corte_menos_2_years=(intval($array_fecha_corte[0])-2)."-".$array_fecha_corte[1]."-".$array_fecha_corte[2];
				$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=$this->diferencia_dias_entre_fechas($campo_corregido,$fecha_corte_menos_2_years);
			}//fin if
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_corte_menos_2_years!=""
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years!==false
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years>0
			)
			{
				$campo_corregido="1845-01-01";
			}//fin if
			//fin limite inferior fecha corte 2 years

		if($comparacion_a_1900_12_31<0 
			&& $excede_fecha_corte<0
			&& $comparacion_a_1900_12_31!==false
			&& $excede_fecha_corte!==false
			)
		{
			$campo_corregido="1800-01-01";
		}//fin comparacion excede la fecha de corte

		if($comparacion_a_1900_12_31>=0
			&& $campo_corregido!="1800-01-01"
			&& $campo_corregido!="1805-01-01"
			&& $campo_corregido!="1810-01-01"
			&& $campo_corregido!="1825-01-01"
			&& $campo_corregido!="1830-01-01"
			&& $campo_corregido!="1835-01-01"
			&& $campo_corregido!="1845-01-01"
			)
		{
			if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& ($edad==55
			       || $edad==60
			       || $edad==65
			       || $edad==70
			       || $edad==75
			       || $edad==80
			       || $edad==85
			       || $edad==90
			       || $edad==95
			       || $edad==100
			       || $edad==105
			       || $edad==110
			       || $edad==115
			       || $edad==120
			       )
				)
			{
			    $campo_corregido="1835-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& ($edad!=55
			       && $edad!=60
			       && $edad!=65
			       && $edad!=70
			       && $edad!=75
			       && $edad!=80
			       && $edad!=85
			       && $edad!=90
			       && $edad!=95
			       && $edad!=100
			       && $edad!=105
			       && $edad!=110
			       && $edad!=115
			       && $edad!=120
			       )
				)
			{
			    $campo_corregido="1845-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				)
			{
			    $campo_corregido="1845-01-01";
			}
		}//fin if
	     
		//$campo_corregido=$this->corrector_valor_permitido_fecha_criterio_002($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
			if(
				(
					$campo_corregido=="1845-01-01"
					|| $campo_corregido=="1800-01-01"
					)
				&& ($edad==55
			       || $edad==60
			       || $edad==65
			       || $edad==70
			       || $edad==75
			       || $edad==80
			       || $edad==85
			       || $edad==90
			       || $edad==95
			       || $edad==100
			       || $edad==105
			       || $edad==110
			       || $edad==115
			       || $edad==120
			       )
				
				)
			{
			    $campo_corregido="1835-01-01";
			}
			else if(
				(
					$campo_corregido=="1800-01-01"
					|| $campo_corregido=="1805-01-01"
					|| $campo_corregido=="1810-01-01"
					|| $campo_corregido=="1825-01-01"
					|| $campo_corregido=="1830-01-01"
					|| $campo_corregido=="1835-01-01"
					)
				&& ($edad!=55
			       && $edad!=60
			       && $edad!=65
			       && $edad!=70
			       && $edad!=75
			       && $edad!=80
			       && $edad!=85
			       && $edad!=90
			       && $edad!=95
			       && $edad!=100
			       && $edad!=105
			       && $edad!=110
			       && $edad!=115
			       && $edad!=120
			       )
				
				)
			{
			    $campo_corregido="1845-01-01";
			}
		
	     }//fin fase 1
	    }//fin campo 63
	    
	    //campo a corregir es 67
	    if($numero_campo_a_corregir==67)
	    {
	     if($fase_correccion==0)
	     {
		//campo en blanco
		if($campo_corregido=="")
		{
		    $campo_corregido="1845-01-01";
		}
		
		//formato
		$corrige_formato=false;
		$array_fecha_campo_actual=explode("-", $campo_corregido);
		if(count($array_fecha_campo_actual)==3)
		{
			if(ctype_digit($array_fecha_campo_actual[0]) && ctype_digit($array_fecha_campo_actual[1]) && ctype_digit($array_fecha_campo_actual[2]))
			{
				//checkdate mm-dd-aaaa -> aaaa-mm-dd ?
				if(checkdate($array_fecha_campo_actual[1],$array_fecha_campo_actual[2],$array_fecha_campo_actual[0])
			       && intval($array_fecha_campo_actual[0])>=32)
			    {
			    	//no se corrige formato
			    }
			    else
			    {
			    	$corrige_formato=true;
			    }

			}
			else
			{
				$corrige_formato=true;
			}
		}
		else
		{
			$corrige_formato=true;
		}//fin else

		if($corrige_formato==true)
		{
		$campo_corregido=$this->corrector_formato_fecha($campo_corregido,false,-2);
		}
		
		//valor permitido
		$excede_fecha_corte=$this->diferencia_dias_entre_fechas($campo_corregido,$date_de_corte);
		$es_menor_o_igual_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
		
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");

		//comparacion fecha campo actual es inferior a la fecha de nacimiento
			$fecha_nacimiento_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),"1900-12-31");
			$campo_actual_inferior_fecha_nacimiento=$this->diferencia_dias_entre_fechas($campo_corregido,trim($array_fields[9]));
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_nacimiento_es_mayor_a_1900_12_31<0
			&& $fecha_nacimiento_es_mayor_a_1900_12_31!==false
			&& $campo_actual_inferior_fecha_nacimiento>0
			&& $campo_actual_inferior_fecha_nacimiento!==false
			)
			{
				$campo_corregido="1800-01-01";
			}//fin if
			//fin comparacion fecha campo actual es inferior a la fecha de nacimiento

			//limite inferior fecha corte 2 years
			$fecha_corte_menos_2_years="";
			$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=false;
			$array_fecha_corte=explode("-", $date_de_corte);
			if(count($array_fecha_corte)==3)
			{
				$fecha_corte_menos_2_years=(intval($array_fecha_corte[0])-2)."-".$array_fecha_corte[1]."-".$array_fecha_corte[2];
				$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=$this->diferencia_dias_entre_fechas($campo_corregido,$fecha_corte_menos_2_years);
			}//fin if
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_corte_menos_2_years!=""
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years!==false
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years>0
			)
			{
				$campo_corregido="1845-01-01";
			}//fin if
			//fin limite inferior fecha corte 2 years

		if($comparacion_a_1900_12_31<0 
			&& $excede_fecha_corte<0
			&& $comparacion_a_1900_12_31!==false
			&& $excede_fecha_corte!==false
			)
		{
			$campo_corregido="1800-01-01";
		}//fin comparacion excede la fecha de corte

		if($comparacion_a_1900_12_31>=0
			&& $campo_corregido!="1800-01-01"
			&& $campo_corregido!="1805-01-01"
			&& $campo_corregido!="1810-01-01"
			&& $campo_corregido!="1825-01-01"
			&& $campo_corregido!="1830-01-01"
			&& $campo_corregido!="1835-01-01"
			&& $campo_corregido!="1845-01-01"
			)
		{
		    $campo_corregido="1845-01-01";
		}
	     
		//$campo_corregido=$this->corrector_valor_permitido_fecha_criterio_002($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
	     	//calidad 67
	     	$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");

			if($comparacion_a_1900_12_31>=0
				&& $comparacion_a_1900_12_31!==false 
				&& $campo_corregido!="1845-01-01")
			{
			    $campo_corregido="1845-01-01";
			}//fin calidad
		
	     }//fin fase 1
	    }//fin campo 67
	    
	    //campo a corregir es 68
	    if($numero_campo_a_corregir==68)
	    {
	     if($fase_correccion==0)
	     {
		//campo en blanco
		if($campo_corregido=="")
		{
		    $campo_corregido="1845-01-01";
		}
		
		//formato
		$corrige_formato=false;
		$array_fecha_campo_actual=explode("-", $campo_corregido);
		if(count($array_fecha_campo_actual)==3)
		{
			if(ctype_digit($array_fecha_campo_actual[0]) && ctype_digit($array_fecha_campo_actual[1]) && ctype_digit($array_fecha_campo_actual[2]))
			{
				//checkdate mm-dd-aaaa -> aaaa-mm-dd ?
				if(checkdate($array_fecha_campo_actual[1],$array_fecha_campo_actual[2],$array_fecha_campo_actual[0])
			       && intval($array_fecha_campo_actual[0])>=32)
			    {
			    	//no se corrige formato
			    }
			    else
			    {
			    	$corrige_formato=true;
			    }

			}
			else
			{
				$corrige_formato=true;
			}
		}
		else
		{
			$corrige_formato=true;
		}//fin else

		if($corrige_formato==true)
		{
		$campo_corregido=$this->corrector_formato_fecha($campo_corregido,false,-2);
		}
		
		//valor permitido
		
		$excede_fecha_corte=$this->diferencia_dias_entre_fechas($campo_corregido,$date_de_corte);
		$es_menor_o_igual_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
		
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");

		//comparacion fecha campo actual es inferior a la fecha de nacimiento
			$fecha_nacimiento_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),"1900-12-31");
			$campo_actual_inferior_fecha_nacimiento=$this->diferencia_dias_entre_fechas($campo_corregido,trim($array_fields[9]));
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_nacimiento_es_mayor_a_1900_12_31<0
			&& $fecha_nacimiento_es_mayor_a_1900_12_31!==false
			&& $campo_actual_inferior_fecha_nacimiento>0
			&& $campo_actual_inferior_fecha_nacimiento!==false
			)
			{
				$campo_corregido="1800-01-01";
			}//fin if
			//fin comparacion fecha campo actual es inferior a la fecha de nacimiento

			//limite inferior fecha corte 2 years
			$fecha_corte_menos_2_years="";
			$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=false;
			$array_fecha_corte=explode("-", $date_de_corte);
			if(count($array_fecha_corte)==3)
			{
				$fecha_corte_menos_2_years=(intval($array_fecha_corte[0])-2)."-".$array_fecha_corte[1]."-".$array_fecha_corte[2];
				$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=$this->diferencia_dias_entre_fechas($campo_corregido,$fecha_corte_menos_2_years);
			}//fin if
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_corte_menos_2_years!=""
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years!==false
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years>0
			)
			{
				$campo_corregido="1845-01-01";
			}//fin if
			//fin limite inferior fecha corte 2 years

		if($comparacion_a_1900_12_31<0 
			&& $excede_fecha_corte<0
			&& $comparacion_a_1900_12_31!==false
			&& $excede_fecha_corte!==false
			)
		{
			$campo_corregido="1800-01-01";
		}//fin comparacion excede la fecha de corte

		if($comparacion_a_1900_12_31>=0
			&& $campo_corregido!="1800-01-01"
			&& $campo_corregido!="1805-01-01"
			&& $campo_corregido!="1810-01-01"
			&& $campo_corregido!="1825-01-01"
			&& $campo_corregido!="1830-01-01"
			&& $campo_corregido!="1835-01-01"
			&& $campo_corregido!="1845-01-01"
			)
		{
		    $campo_corregido="1845-01-01";
		}
	     
		//$campo_corregido=$this->corrector_valor_permitido_fecha_criterio_002($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
			/*
			$es_fecha_es_valida=false;
			$verificacion_fecha_corte_pos_year=-1;
			$verificacion_fecha_corte=-1;
			$diferencia_de_1900=-1;
			$array_fecha_correccion_actual=explode("-",$campo_corregido);
			if(count($array_fecha_correccion_actual)==3)
			{
			    //checkdate mm/dd/aaaa
			    if(checkdate($array_fecha_correccion_actual[1],$array_fecha_correccion_actual[2],$array_fecha_correccion_actual[0]))
			    {
				$es_fecha_es_valida=true;
				$interval = date_diff(new DateTime($campo_corregido),new DateTime($date_de_corte_posterior_year));
				$verificacion_fecha_corte_pos_year =(float)($interval->format("%r%a"));
				$interval = date_diff(new DateTime($campo_corregido),new DateTime($date_de_corte));
				$verificacion_fecha_corte =(float)($interval->format("%r%a"));
				$interval = date_diff(new DateTime($campo_corregido),new DateTime("1900-01-01"));
				$diferencia_de_1900 =(float)($interval->format("%r%a"));
			    }
			}
			
			
			*/

			if( 
				($campo_corregido=="1800-01-01"
					|| $campo_corregido=="1805-01-01"
					|| $campo_corregido=="1810-01-01"
					|| $campo_corregido=="1825-01-01"
					|| $campo_corregido=="1830-01-01"
					|| $campo_corregido=="1835-01-01"
					)

				)//fin condicion
			{
			    $campo_corregido="1845-01-01";
			}//fin calidad
	     }//fin fase 1
	    }
	    
	    //campo a corregir es 72
	    if($numero_campo_a_corregir==72)
	    {
	     if($fase_correccion==0)
	     {
			//campo en blanco
	     	if($campo_corregido=="")
			{
				if($campo_corregido==""
				   && ($edad>=10 && $edad<30)
				   )
				{
				    $campo_corregido="1835-01-01";
				}
				else if($campo_corregido==""
				   && ($edad<10 || $edad>=30)
				   )
				{
				    $campo_corregido="1845-01-01";
				}
				else if($campo_corregido=="")
				{
				    $campo_corregido="1845-01-01";
				}
			}//fin if
		
		//formato
		$corrige_formato=false;
		$array_fecha_campo_actual=explode("-", $campo_corregido);
		if(count($array_fecha_campo_actual)==3)
		{
			if(ctype_digit($array_fecha_campo_actual[0]) && ctype_digit($array_fecha_campo_actual[1]) && ctype_digit($array_fecha_campo_actual[2]))
			{
				//checkdate mm-dd-aaaa -> aaaa-mm-dd ?
				if(checkdate($array_fecha_campo_actual[1],$array_fecha_campo_actual[2],$array_fecha_campo_actual[0])
			       && intval($array_fecha_campo_actual[0])>=32)
			    {
			    	//no se corrige formato
			    }
			    else
			    {
			    	$corrige_formato=true;
			    }

			}
			else
			{
				$corrige_formato=true;
			}
		}
		else
		{
			$corrige_formato=true;
		}//fin else

		if($corrige_formato==true)
		{
		$campo_corregido=$this->corrector_formato_fecha($campo_corregido,false,-2);
		}
		
		//valor permitido
		
		$excede_fecha_corte=$this->diferencia_dias_entre_fechas($campo_corregido,$date_de_corte);
		$es_menor_o_igual_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
		
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");

		//comparacion fecha campo actual es inferior a la fecha de nacimiento
			$fecha_nacimiento_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),"1900-12-31");
			$campo_actual_inferior_fecha_nacimiento=$this->diferencia_dias_entre_fechas($campo_corregido,trim($array_fields[9]));
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_nacimiento_es_mayor_a_1900_12_31<0
			&& $fecha_nacimiento_es_mayor_a_1900_12_31!==false
			&& $campo_actual_inferior_fecha_nacimiento>0
			&& $campo_actual_inferior_fecha_nacimiento!==false
			)
			{
				$campo_corregido="1800-01-01";
			}//fin if
			//fin comparacion fecha campo actual es inferior a la fecha de nacimiento

			//limite inferior fecha corte 2 years
			$fecha_corte_menos_2_years="";
			$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=false;
			$array_fecha_corte=explode("-", $date_de_corte);
			if(count($array_fecha_corte)==3)
			{
				$fecha_corte_menos_2_years=(intval($array_fecha_corte[0])-2)."-".$array_fecha_corte[1]."-".$array_fecha_corte[2];
				$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=$this->diferencia_dias_entre_fechas($campo_corregido,$fecha_corte_menos_2_years);
			}//fin if
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_corte_menos_2_years!=""
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years!==false
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years>0
			)
			{
				$campo_corregido="1845-01-01";
			}//fin if
			//fin limite inferior fecha corte 2 years

		if($comparacion_a_1900_12_31<0 
					&& $excede_fecha_corte<0
					&& $comparacion_a_1900_12_31!==false
					&& $excede_fecha_corte!==false
					)
				{
					$campo_corregido="1800-01-01";
				}//fin comparacion excede la fecha de corte

		if($comparacion_a_1900_12_31>=0
			&& $campo_corregido!="1800-01-01"
			&& $campo_corregido!="1805-01-01"
			&& $campo_corregido!="1810-01-01"
			&& $campo_corregido!="1825-01-01"
			&& $campo_corregido!="1830-01-01"
			&& $campo_corregido!="1835-01-01"
			&& $campo_corregido!="1845-01-01"
			)
		{
			 if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& ($edad>=10 && $edad<30)
				)
			{
			    $campo_corregido="1835-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& ($edad<10 || $edad>=30)
				)
			{
			    $campo_corregido="1845-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				)
			{
			    $campo_corregido="1845-01-01";
			}
		}//fin else if
	     
		//$campo_corregido=$this->corrector_valor_permitido_fecha_criterio_002($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
		//calidad con fecha actividad
		$nueva_edad_year_actual=-1;
		$nueva_edad_meses_actual=-1;
		$nueva_edad_dias_actual=-1;
		    
		$array_fecha_nacimiento=explode("-",$array_fields[9]);
		$fecha_campo_actual=explode("-",$campo_corregido);
		if(count($fecha_campo_actual)==3 && checkdate($fecha_campo_actual[1],$fecha_campo_actual[2],$fecha_campo_actual[0]))
		{
		    //echo "<script>alert('entro');</script>";
		    
		    $array_calc_edad_actual=$this->edad_years_months_days($array_fecha_nacimiento[2]."-".$array_fecha_nacimiento[1]."-".$array_fecha_nacimiento[0],$fecha_campo_actual[2]."-".$fecha_campo_actual[1]."-".$fecha_campo_actual[0]);
		    $nueva_edad_year_actual=intval($array_calc_edad_actual['y']);
		    $nueva_edad_meses_actual=(intval($array_calc_edad_actual['y'])*12)+$array_calc_edad_actual['m'];
		    $nueva_edad_dias_actual=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),trim($array_fields[$numero_campo_a_corregir]));
		    
		    //echo "<script>alert(' EDAD DIAS $nueva_edad_dias_actual FN ".trim($array_fields[9])." FA ".trim($array_fields[$numero_campo_a_corregir])."  NCA $numero_campo_a_corregir');</script>";
		}
		$es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
		$es_menor_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
		if(($edad>=10 && $edad<30)
			&& ($campo_corregido=="1845-01-01" || $campo_corregido=="1800-01-01")
			)
		{
		    $campo_corregido="1835-01-01";
		}
		else if(($nueva_edad_year_actual<10 || $nueva_edad_year_actual>=30)
			&& $es_mayor_a_1900_12_31<0
			)
		{
		    $campo_corregido="1845-01-01";
		}
		else if(($edad<10 || $edad>=30)
			&& ($campo_corregido=="1800-01-01"
				|| $campo_corregido=="1805-01-01"
				|| $campo_corregido=="1810-01-01"
				|| $campo_corregido=="1825-01-01"
				|| $campo_corregido=="1830-01-01"
				|| $campo_corregido=="1835-01-01"
				)
			)
		{
		    $campo_corregido="1845-01-01";
		}
		else if(
			 ($campo_corregido=="1800-01-01"
				|| $campo_corregido=="1805-01-01"
				|| $campo_corregido=="1810-01-01"
				|| $campo_corregido=="1825-01-01"
				|| $campo_corregido=="1830-01-01"
				|| $campo_corregido=="1845-01-01"
				)
			 && $edad>=10
			 && $edad<30
			)
		{
		    $campo_corregido="1835-01-01";
		}
	     }//fin else
	    }//fin campo 72
	    
	    //campo a corregir es 73
	    if($numero_campo_a_corregir==73)
	    {
	     if($fase_correccion==0)
	     {
				//campo en blanco
		     	 if($campo_corregido=="")
				{
					if($campo_corregido==""
					   && ($edad<=45)
					   )
					{
					    $campo_corregido="1845-01-01";
					}
					else if($campo_corregido==""
					   && ($edad>45)
					   )
					{
					    $campo_corregido="1835-01-01";
					}
					else if($campo_corregido=="")
					{
					    $campo_corregido="1845-01-01";
					}
				}//fin if
				
				//formato
				$corrige_formato=false;
				$array_fecha_campo_actual=explode("-", $campo_corregido);
				if(count($array_fecha_campo_actual)==3)
				{
					if(ctype_digit($array_fecha_campo_actual[0]) && ctype_digit($array_fecha_campo_actual[1]) && ctype_digit($array_fecha_campo_actual[2]))
					{
						//checkdate mm-dd-aaaa -> aaaa-mm-dd ?
						if(checkdate($array_fecha_campo_actual[1],$array_fecha_campo_actual[2],$array_fecha_campo_actual[0])
					       && intval($array_fecha_campo_actual[0])>=32)
					    {
					    	//no se corrige formato
					    }
					    else
					    {
					    	$corrige_formato=true;
					    }

					}
					else
					{
						$corrige_formato=true;
					}
				}
				else
				{
					$corrige_formato=true;
				}//fin else

				if($corrige_formato==true)
				{
					$campo_corregido=$this->corrector_formato_fecha($campo_corregido,false,-2);
				}
				
				//valor permitido
				
				$excede_fecha_corte=$this->diferencia_dias_entre_fechas($campo_corregido,$date_de_corte);
				$es_menor_o_igual_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
				$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
				
				$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");

				//comparacion fecha campo actual es inferior a la fecha de nacimiento
			$fecha_nacimiento_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),"1900-12-31");
			$campo_actual_inferior_fecha_nacimiento=$this->diferencia_dias_entre_fechas($campo_corregido,trim($array_fields[9]));
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_nacimiento_es_mayor_a_1900_12_31<0
			&& $fecha_nacimiento_es_mayor_a_1900_12_31!==false
			&& $campo_actual_inferior_fecha_nacimiento>0
			&& $campo_actual_inferior_fecha_nacimiento!==false
			)
			{
				$campo_corregido="1800-01-01";
			}//fin if
			//fin comparacion fecha campo actual es inferior a la fecha de nacimiento

			//limite inferior fecha corte 2 years
			$fecha_corte_menos_2_years="";
			$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=false;
			$array_fecha_corte=explode("-", $date_de_corte);
			if(count($array_fecha_corte)==3)
			{
				$fecha_corte_menos_2_years=(intval($array_fecha_corte[0])-2)."-".$array_fecha_corte[1]."-".$array_fecha_corte[2];
				$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=$this->diferencia_dias_entre_fechas($campo_corregido,$fecha_corte_menos_2_years);
			}//fin if
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_corte_menos_2_years!=""
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years!==false
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years>0
			)
			{
				$campo_corregido="1845-01-01";
			}//fin if
			//fin limite inferior fecha corte 2 years

				if($comparacion_a_1900_12_31<0 
					&& $excede_fecha_corte<0
					&& $comparacion_a_1900_12_31!==false
					&& $excede_fecha_corte!==false
					)
				{
					$campo_corregido="1800-01-01";
				}//fin comparacion excede la fecha de corte

				if(
					$comparacion_a_1900_12_31>=0
					&& $campo_corregido!="1800-01-01"
					&& $campo_corregido!="1805-01-01"
					&& $campo_corregido!="1810-01-01"
					&& $campo_corregido!="1825-01-01"
					&& $campo_corregido!="1830-01-01"
					&& $campo_corregido!="1835-01-01"
					&& $campo_corregido!="1845-01-01"
					)
				{
					if($comparacion_a_1900_12_31>=0
						&& $campo_corregido!="1800-01-01"
						&& $campo_corregido!="1805-01-01"
						&& $campo_corregido!="1810-01-01"
						&& $campo_corregido!="1825-01-01"
						&& $campo_corregido!="1830-01-01"
						&& $campo_corregido!="1835-01-01"
						&& $campo_corregido!="1845-01-01"
						&& ($edad<=45)
						)
					{
					    $campo_corregido="1845-01-01";
					}
					else if($comparacion_a_1900_12_31>=0
						&& $campo_corregido!="1800-01-01"
						&& $campo_corregido!="1805-01-01"
						&& $campo_corregido!="1810-01-01"
						&& $campo_corregido!="1825-01-01"
						&& $campo_corregido!="1830-01-01"
						&& $campo_corregido!="1835-01-01"
						&& $campo_corregido!="1845-01-01"
						&& ($edad>45)
						)
					{
					    $campo_corregido="1835-01-01";
					}
					else if($comparacion_a_1900_12_31>=0
						&& $campo_corregido!="1800-01-01"
						&& $campo_corregido!="1805-01-01"
						&& $campo_corregido!="1810-01-01"
						&& $campo_corregido!="1825-01-01"
						&& $campo_corregido!="1830-01-01"
						&& $campo_corregido!="1835-01-01"
						&& $campo_corregido!="1845-01-01"
						)
					{
					    $campo_corregido="1845-01-01";
					}
				}//fin if
			     
				//$campo_corregido=$this->corrector_valor_permitido_fecha_criterio_002($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
				//calidad con fecha actividad
				$nueva_edad_year_actual=-1;
				$nueva_edad_meses_actual=-1;
				$nueva_edad_dias_actual=-1;
				    
				$array_fecha_nacimiento=explode("-",$array_fields[9]);
				$fecha_campo_actual=explode("-",$campo_corregido);
				if(count($fecha_campo_actual)==3 && checkdate($fecha_campo_actual[1],$fecha_campo_actual[2],$fecha_campo_actual[0]))
				{
				    //echo "<script>alert('entro');</script>";
				    
				    $array_calc_edad_actual=$this->edad_years_months_days($array_fecha_nacimiento[2]."-".$array_fecha_nacimiento[1]."-".$array_fecha_nacimiento[0],$fecha_campo_actual[2]."-".$fecha_campo_actual[1]."-".$fecha_campo_actual[0]);
				    $nueva_edad_year_actual=intval($array_calc_edad_actual['y']);
				    $nueva_edad_meses_actual=(intval($array_calc_edad_actual['y'])*12)+$array_calc_edad_actual['m'];
				    $nueva_edad_dias_actual=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),trim($array_fields[$numero_campo_a_corregir]));
				    
				    //echo "<script>alert(' EDAD DIAS $nueva_edad_dias_actual FN ".trim($array_fields[9])." FA ".trim($array_fields[$numero_campo_a_corregir])."  NCA $numero_campo_a_corregir');</script>";
				}
				$es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
				$es_menor_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
				if(($edad>45)
					&& ($campo_corregido=="1845-01-01" || $campo_corregido=="1800-01-01")
					)
				{
				    $campo_corregido="1835-01-01";
				}
				else if(($nueva_edad_year_actual<=45)
					&& $es_mayor_a_1900_12_31<0
					)
				{
				    $campo_corregido="1845-01-01";
				    if(trim($campo_corregido)=="1845-01-01"
					&& $edad<=45
					)
					{
					    $campo_corregido="1845-01-01";
					    
					}
					else if(trim($campo_corregido)=="1845-01-01"
					&& $edad>45
					)
					{
					    $campo_corregido="1835-01-01";
					    
					}

				}
				else if(
					(trim($campo_corregido)=="1800-01-01"
				   	|| trim($campo_corregido)=="1805-01-01"
				    || trim($campo_corregido)=="1810-01-01"
				    || trim($campo_corregido)=="1825-01-01"
				    || trim($campo_corregido)=="1830-01-01"
				    || trim($campo_corregido)=="1835-01-01"
				   	)
					&& $edad<=45
					
					)
				{
				    $campo_corregido="1845-01-01";
				}
				else if(
					(trim($campo_corregido)=="1800-01-01"
				   	|| trim($campo_corregido)=="1805-01-01"
				    || trim($campo_corregido)=="1810-01-01"
				    || trim($campo_corregido)=="1825-01-01"
				    || trim($campo_corregido)=="1830-01-01"
				    || trim($campo_corregido)=="1845-01-01"
				   	)
					&& $edad>45
					
					)
				{
				    $campo_corregido="1835-01-01";
				}
			
	     }//fin fase
	    }//fin campo73
	    
	    //campo a corregir es 118
	    if($numero_campo_a_corregir==118)
	    {
	     
	     if($fase_correccion==0)
	     {
		//campo en blanco
		if($campo_corregido=="")
		{
		    $campo_corregido="1845-01-01";
		}
		
		//formato
		$corrige_formato=false;
		$array_fecha_campo_actual=explode("-", $campo_corregido);
		if(count($array_fecha_campo_actual)==3)
		{
			if(ctype_digit($array_fecha_campo_actual[0]) && ctype_digit($array_fecha_campo_actual[1]) && ctype_digit($array_fecha_campo_actual[2]))
			{
				//checkdate mm-dd-aaaa -> aaaa-mm-dd ?
				if(checkdate($array_fecha_campo_actual[1],$array_fecha_campo_actual[2],$array_fecha_campo_actual[0])
			       && intval($array_fecha_campo_actual[0])>=32)
			    {
			    	//no se corrige formato
			    }
			    else
			    {
			    	$corrige_formato=true;
			    }

			}
			else
			{
				$corrige_formato=true;
			}
		}
		else
		{
			$corrige_formato=true;
		}//fin else

		if($corrige_formato==true)
		{
		$campo_corregido=$this->corrector_formato_fecha($campo_corregido,false,-2);
		}
		
		//valor permitido
		$excede_fecha_corte=$this->diferencia_dias_entre_fechas($campo_corregido,$date_de_corte);
		$es_menor_o_igual_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
		
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");

		//comparacion fecha campo actual es inferior a la fecha de nacimiento
			$fecha_nacimiento_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),"1900-12-31");
			$campo_actual_inferior_fecha_nacimiento=$this->diferencia_dias_entre_fechas($campo_corregido,trim($array_fields[9]));
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_nacimiento_es_mayor_a_1900_12_31<0
			&& $fecha_nacimiento_es_mayor_a_1900_12_31!==false
			&& $campo_actual_inferior_fecha_nacimiento>0
			&& $campo_actual_inferior_fecha_nacimiento!==false
			)
			{
				$campo_corregido="1800-01-01";
			}//fin if
			//fin comparacion fecha campo actual es inferior a la fecha de nacimiento

			//limite inferior fecha corte 2 years
			$fecha_corte_menos_2_years="";
			$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=false;
			$array_fecha_corte=explode("-", $date_de_corte);
			if(count($array_fecha_corte)==3)
			{
				$fecha_corte_menos_2_years=(intval($array_fecha_corte[0])-2)."-".$array_fecha_corte[1]."-".$array_fecha_corte[2];
				$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=$this->diferencia_dias_entre_fechas($campo_corregido,$fecha_corte_menos_2_years);
			}//fin if
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_corte_menos_2_years!=""
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years!==false
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years>0
			)
			{
				$campo_corregido="1845-01-01";
			}//fin if
			//fin limite inferior fecha corte 2 years
		
		if($comparacion_a_1900_12_31<0 
					&& $excede_fecha_corte<0
					&& $comparacion_a_1900_12_31!==false
					&& $excede_fecha_corte!==false
					)
				{
					$campo_corregido="1800-01-01";
				}//fin comparacion excede la fecha de corte

		if($comparacion_a_1900_12_31>=0
			&& $campo_corregido!="1800-01-01"
			&& $campo_corregido!="1805-01-01"
			&& $campo_corregido!="1810-01-01"
			&& $campo_corregido!="1825-01-01"
			&& $campo_corregido!="1830-01-01"
			&& $campo_corregido!="1835-01-01"
			&& $campo_corregido!="1845-01-01"
			)
		{
		    $campo_corregido="1845-01-01";
		}
	     
		//$campo_corregido=$this->corrector_valor_permitido_fecha_criterio_002($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
		/*
		$es_fecha_es_valida=false;
		$verificacion_fecha_corte_pos_year=-1;
		$verificacion_fecha_corte=-1;
		$diferencia_de_1900=-1;
		$array_fecha_correccion_actual=explode("-",$campo_corregido);
		if(count($array_fecha_correccion_actual)==3)
		{
		    //checkdate mm/dd/aaaa
		    if(checkdate($array_fecha_correccion_actual[1],$array_fecha_correccion_actual[2],$array_fecha_correccion_actual[0]))
		    {
			$es_fecha_es_valida=true;
			$interval = date_diff(new DateTime($campo_corregido),new DateTime($date_de_corte_posterior_year));
			$verificacion_fecha_corte_pos_year =(float)($interval->format("%r%a"));
			$interval = date_diff(new DateTime($campo_corregido),new DateTime($date_de_corte));
			$verificacion_fecha_corte =(float)($interval->format("%r%a"));
			$interval = date_diff(new DateTime($campo_corregido),new DateTime("1900-01-01"));
			$diferencia_de_1900 =(float)($interval->format("%r%a"));
		    }
		}
		
		if(!($diferencia_de_1900<0 && $verificacion_fecha_corte>=0)
			&& $campo_corregido!="1845-01-01"
			&& $campo_corregido!="1835-01-01"
			&& $campo_corregido!="1830-01-01"
			&& $campo_corregido!="1825-01-01"
			&& $campo_corregido!="1810-01-01"
			&& $campo_corregido!="1805-01-01"
			&& $campo_corregido!="1800-01-01"
		)
		{
		    $campo_corregido="1800-01-01";
		}
		*/

			if(
				 $campo_corregido=="1800-01-01"
				)
			{
			    $campo_corregido="1845-01-01";
			}
	     }//fin fase 1
	    }
	    //FIN CAMPOS CONSULTAS
	    
	    //CAMPOS MALTRATO Y VIOLENCIA SEXUAL
	    
	    //campo a corregir es 22
	    if($numero_campo_a_corregir==22)
	    {
	     if($fase_correccion==0)
	     {
	     	$c65_es_mayor_1999_12_31=$this->diferencia_dias_entre_fechas($array_fields[65],"1900-12-31");
			//campo en blanco

	     	if($campo_corregido=="")
			{
			
				if($campo_corregido==""
				   && $array_fields[10]=="M"
				   && $edad>=18
				   )
				{
					//c1
				    $campo_corregido="0";
				}
				else if($campo_corregido==""
					&& $array_fields[10]=="F"
					&& $edad>=18
					&& $c65_es_mayor_1999_12_31<0	   
				   )
				{
					//c2
				    $campo_corregido="1";
				}
				else if($campo_corregido==""
					&& $edad<18
					&& $c65_es_mayor_1999_12_31<0	   
				   )
				{
					//c3
				    $campo_corregido="2";
				}
				else if($campo_corregido==""
				   	&& $array_fields[10]=="F"
				    && $array_fields[65]=="1845-01-01"
				    && $edad>=18
				   )
				{
					//c4
				    $campo_corregido="21";
				}
				else if($campo_corregido==""
					&& $edad<18
					&& $array_fields[65]=="1845-01-01"		   
				   )
				{
					//c5
				    $campo_corregido="21";
				}
				else if($campo_corregido=="")
				{
					//c6
				    $campo_corregido="0";
				}
			}//fin if
		
		
		
		//valor permitido

			if($campo_corregido!="0"
		    && $campo_corregido!="1"
		    && $campo_corregido!="2"
		    && $campo_corregido!="3"
		    && $campo_corregido!="21"
		    )
		{
		
			if($campo_corregido!="0"
				    && $campo_corregido!="1"
				    && $campo_corregido!="2"
				    && $campo_corregido!="3"
				    && $campo_corregido!="21"
				   && $array_fields[10]=="M"
				   && $edad>=18
				   )
				{
					//c1
				    $campo_corregido="0";
				}
				else if($campo_corregido!="0"
				    && $campo_corregido!="1"
				    && $campo_corregido!="2"
				    && $campo_corregido!="3"
				    && $campo_corregido!="21"
					&& $array_fields[10]=="F"
					&& $edad>=18
					&& $c65_es_mayor_1999_12_31<0	   
				   )
				{
					//c2
				    $campo_corregido="1";
				}
				else if($campo_corregido!="0"
				    && $campo_corregido!="1"
				    && $campo_corregido!="2"
				    && $campo_corregido!="3"
				    && $campo_corregido!="21"
					&& $edad<18
					&& $c65_es_mayor_1999_12_31<0	   
				   )
				{
					//c3
				    $campo_corregido="2";
				}
				else if($campo_corregido!="0"
				    && $campo_corregido!="1"
				    && $campo_corregido!="2"
				    && $campo_corregido!="3"
				    && $campo_corregido!="21"
				   	&& $array_fields[10]=="F"
				    && $array_fields[65]=="1845-01-01"
				    && $edad>=18
				   )
				{
					//c4
				    $campo_corregido="21";
				}
				else if($campo_corregido!="0"
				    && $campo_corregido!="1"
				    && $campo_corregido!="2"
				    && $campo_corregido!="3"
				    && $campo_corregido!="21"
					&& $edad<18
					&& $array_fields[65]=="1845-01-01"		   
				   )
				{
					//c5
				    $campo_corregido="21";
				}
				else if($campo_corregido!="0"
				    && $campo_corregido!="1"
				    && $campo_corregido!="2"
				    && $campo_corregido!="3"
				    && $campo_corregido!="21"
				    )
				{
					//c6
				    $campo_corregido="0";
				}
		}//fin if
		
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_008($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
	     	$c65_es_mayor_1999_12_31=$this->diferencia_dias_entre_fechas($array_fields[65],"1900-12-31");
			//calidad
			if($campo_corregido!="0"
				   && $array_fields[10]=="M"
				   && $edad>=18
				   )
				{
					//c1
				    $campo_corregido="0";
				}
				else if($campo_corregido!="1"
					&& $array_fields[10]=="F"
					&& $edad>=18
					&& $c65_es_mayor_1999_12_31<0	   
				   )
				{
					//c2
				    $campo_corregido="1";
				}
				else if($campo_corregido!="2"
					&& $edad<18
					&& $c65_es_mayor_1999_12_31<0	   
				   )
				{
					//c3
				    $campo_corregido="2";
				}
				else if($campo_corregido!="3"
					&& $campo_corregido!="21"
				   	&& $array_fields[10]=="F"
				    && $array_fields[65]=="1845-01-01"
				    && $edad>=18
				   )
				{
					//c4
				    $campo_corregido="21";
				}
				else if($campo_corregido!="3"
					&& $campo_corregido!="21"
					&& $edad<18
					&& $array_fields[65]=="1845-01-01"		   
				   )
				{
					//c5
				    $campo_corregido="21";
				}//fin calidad

			
		
		
		
	     }//fin fase 1
	    }//fin c22
	    
	    //campo a corregir es 23
	    if($numero_campo_a_corregir==23)
	    {
	     if($fase_correccion==0)
	     {
		//campo en blanco		
		$c66_es_mayor_1999_12_31=$this->diferencia_dias_entre_fechas($array_fields[66],"1900-12-31");
		
		if($campo_corregido=="")
		{
			if($campo_corregido==""
			   //&& trim($array_fields[66])!="1845-01-01"
			   && $c66_es_mayor_1999_12_31<0 
			   && $c66_es_mayor_1999_12_31!==false
			   
			   )
			{
			    $campo_corregido="1";
			}
			else if($campo_corregido==""
			   && trim($array_fields[66])=="1845-01-01"
			   
			   )
			{
			    $campo_corregido="21";
			}
			else if($campo_corregido=="")
			{
			    $campo_corregido="21";
			}
		}//fin if
		
		//valor permitido
		$c66_es_mayor_1999_12_31=$this->diferencia_dias_entre_fechas($array_fields[66],"1900-12-31");
		
		if($campo_corregido!="1"
		   && $campo_corregido!="2"
		   && $campo_corregido!="21"
		   )
		{
			if($campo_corregido!="1"
			   && $campo_corregido!="2"
			   && $campo_corregido!="21"
			    //&& trim($array_fields[66])!="1845-01-01"
			   && $c66_es_mayor_1999_12_31<0 
			   && $c66_es_mayor_1999_12_31!==false
			   
			   )
			{
			    $campo_corregido="1";
			}
			else if($campo_corregido!="1"
			   && $campo_corregido!="2"
			   && $campo_corregido!="21"
			    && trim($array_fields[66])=="1845-01-01"
			   
			   )
			{
			    $campo_corregido="21";
			}
			else if($campo_corregido!="1"
			   && $campo_corregido!="2"
			   && $campo_corregido!="21"
			   )
			{
			    $campo_corregido="21";
			}
		}//fin if
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_009($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
			$c66_es_mayor_1999_12_31=$this->diferencia_dias_entre_fechas($array_fields[66],"1900-12-31");
			
			//calidad c23
			if($campo_corregido!="1"
			   //&& trim($array_fields[66])!="1845-01-01"
			   && $c66_es_mayor_1999_12_31<0 
			   && $c66_es_mayor_1999_12_31!==false
			   
			   )
			{
			    $campo_corregido="1";
			}
			else if($campo_corregido=="1"
			   && trim($array_fields[66])=="1845-01-01"
			   
			   )
			{
			    $campo_corregido="21";
			}
		
	     }//fin fase 1
	    }//fin campo 23
	    
	    //campo a corregir es 65
	    if($numero_campo_a_corregir==65)
	    {
	     
	     if($fase_correccion==0)
	     {
		//campo en blanco	
		if($campo_corregido=="")
		{	
			if($campo_corregido==""
				&& $array_fields[10]=="M"
				&& $edad>=18
			    )
			{
				//c1
			    $campo_corregido="1845-01-01";
			}
			else if($campo_corregido==""
				&& $edad<18				
				&& ( $array_fields[22]=="3" || $array_fields[22]=="21")
			    )
			{
				//c2
			    $campo_corregido="1845-01-01";
			}
			else if($campo_corregido==""
				&& $array_fields[10]=="F"	
				&& ($array_fields[22]=="3" || $array_fields[22]=="21")
				&& $edad>=18
			    )
			{
				//c3
			    $campo_corregido="1845-01-01";
			}
			else if($campo_corregido==""
				&& $edad<18	
				&& ($array_fields[22]=="2")
			    )
			{
				//c4
			    $campo_corregido="1800-01-01";
			}
			else if($campo_corregido==""
				&& $array_fields[10]=="F"	
				&& $edad>=18
				&& ($array_fields[22]=="1")
			    )
			{
				//c5
			    $campo_corregido="1800-01-01";
			}
			else if($campo_corregido=="")
			{
				//c6
			    $campo_corregido="1845-01-01";
			}
		}//fin if
		
		
		//formato
		$corrige_formato=false;
		$array_fecha_campo_actual=explode("-", $campo_corregido);
		if(count($array_fecha_campo_actual)==3)
		{
			if(ctype_digit($array_fecha_campo_actual[0]) && ctype_digit($array_fecha_campo_actual[1]) && ctype_digit($array_fecha_campo_actual[2]))
			{
				//checkdate mm-dd-aaaa -> aaaa-mm-dd ?
				if(checkdate($array_fecha_campo_actual[1],$array_fecha_campo_actual[2],$array_fecha_campo_actual[0])
			       && intval($array_fecha_campo_actual[0])>=32)
			    {
			    	//no se corrige formato
			    }
			    else
			    {
			    	$corrige_formato=true;
			    }

			}
			else
			{
				$corrige_formato=true;
			}
		}
		else
		{
			$corrige_formato=true;
		}//fin else

		if($corrige_formato==true)
		{
		$campo_corregido=$this->corrector_formato_fecha($campo_corregido,false,-2);
		}
		
		//valor permitido
		$excede_fecha_corte=$this->diferencia_dias_entre_fechas($campo_corregido,$date_de_corte);
		$es_menor_o_igual_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
		
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");

		//comparacion fecha campo actual es inferior a la fecha de nacimiento
			$fecha_nacimiento_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),"1900-12-31");
			$campo_actual_inferior_fecha_nacimiento=$this->diferencia_dias_entre_fechas($campo_corregido,trim($array_fields[9]));
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_nacimiento_es_mayor_a_1900_12_31<0
			&& $fecha_nacimiento_es_mayor_a_1900_12_31!==false
			&& $campo_actual_inferior_fecha_nacimiento>0
			&& $campo_actual_inferior_fecha_nacimiento!==false
			)
			{
				$campo_corregido="1800-01-01";
			}//fin if
			//fin comparacion fecha campo actual es inferior a la fecha de nacimiento

			//limite inferior fecha corte 2 years
			$fecha_corte_menos_2_years="";
			$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=false;
			$array_fecha_corte=explode("-", $date_de_corte);
			if(count($array_fecha_corte)==3)
			{
				$fecha_corte_menos_2_years=(intval($array_fecha_corte[0])-2)."-".$array_fecha_corte[1]."-".$array_fecha_corte[2];
				$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=$this->diferencia_dias_entre_fechas($campo_corregido,$fecha_corte_menos_2_years);
			}//fin if
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_corte_menos_2_years!=""
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years!==false
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years>0
			)
			{
				$campo_corregido="1845-01-01";
			}//fin if
			//fin limite inferior fecha corte 2 years

		if($comparacion_a_1900_12_31<0 
			&& $excede_fecha_corte<0
			&& $comparacion_a_1900_12_31!==false
			&& $excede_fecha_corte!==false
			)
		{
			$campo_corregido="1800-01-01";
		}//fin comparacion excede la fecha de corte

		if($comparacion_a_1900_12_31>=0
			&& $campo_corregido!="1800-01-01"
			&& $campo_corregido!="1805-01-01"
			&& $campo_corregido!="1810-01-01"
			&& $campo_corregido!="1825-01-01"
			&& $campo_corregido!="1830-01-01"
			&& $campo_corregido!="1835-01-01"
			&& $campo_corregido!="1845-01-01"
			)
		{
			if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& $array_fields[10]=="M"
				&& $edad>=18
			    )
			{
				//c1
			    $campo_corregido="1845-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& $edad<18				
				&& ( $array_fields[22]=="3" || $array_fields[22]=="21")
			    )
			{
				//c2
			    $campo_corregido="1845-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& $array_fields[10]=="F"	
				&& ($array_fields[22]=="3" || $array_fields[22]=="21")
				&& $edad>=18
			    )
			{
				//c3
			    $campo_corregido="1845-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& $edad<18	
				&& ($array_fields[22]=="2")
			    )
			{
				//c4
			    $campo_corregido="1800-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& $array_fields[10]=="F"	
				&& $edad>=18
				&& ($array_fields[22]=="1")
			    )
			{
				//c5
			    $campo_corregido="1800-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				)
			{
			    $campo_corregido="1845-01-01";
			}
		}//fin else if
	     
		//$campo_corregido=$this->corrector_valor_permitido_fecha_criterio_002($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
		//calidad con fecha actividad
		$nueva_edad_year_actual=-1;
		$nueva_edad_meses_actual=-1;
		$nueva_edad_dias_actual=-1;
		    
		$array_fecha_nacimiento=explode("-",$array_fields[9]);
		$fecha_campo_actual=explode("-",$campo_corregido);
		if(count($fecha_campo_actual)==3 && checkdate($fecha_campo_actual[1],$fecha_campo_actual[2],$fecha_campo_actual[0]))
		{
		    //echo "<script>alert('entro');</script>";
		    
		    $array_calc_edad_actual=$this->edad_years_months_days($array_fecha_nacimiento[2]."-".$array_fecha_nacimiento[1]."-".$array_fecha_nacimiento[0],$fecha_campo_actual[2]."-".$fecha_campo_actual[1]."-".$fecha_campo_actual[0]);
		    $nueva_edad_year_actual=intval($array_calc_edad_actual['y']);
		    $nueva_edad_meses_actual=(intval($array_calc_edad_actual['y'])*12)+$array_calc_edad_actual['m'];
		    $nueva_edad_dias_actual=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),trim($array_fields[$numero_campo_a_corregir]));
		    
		    //echo "<script>alert(' EDAD DIAS $nueva_edad_dias_actual FN ".trim($array_fields[9])." FA ".trim($array_fields[$numero_campo_a_corregir])."  NCA $numero_campo_a_corregir');</script>";
		}
		$es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
		$es_menor_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
		
			//calidad
		

			if($es_mayor_a_1900_12_31<0
				&& $array_fields[10]=="M"
				&& $nueva_edad_year_actual>=18
			    )
			{
				//c1
			    $campo_corregido="1845-01-01";
			}
			else if(
				(	$campo_corregido=="1800-01-01"
					|| $campo_corregido=="1805-01-01"
					|| $campo_corregido=="1810-01-01"
					|| $campo_corregido=="1825-01-01"
					|| $campo_corregido=="1830-01-01"
					|| $campo_corregido=="1835-01-01"
					
					)
				&& $array_fields[10]=="M"
				&& $edad>=18
			    )
			{
				//c2
			    $campo_corregido="1845-01-01";
			}
			else if($es_mayor_a_1900_12_31<0
				
				&& $nueva_edad_year_actual<18				
				&& ($array_fields[22]=="3" || $array_fields[22]=="21")
			    )
			{
				//c3
			    $campo_corregido="1845-01-01";
			}
			else if(
				(	$campo_corregido=="1800-01-01"
					|| $campo_corregido=="1805-01-01"
					|| $campo_corregido=="1810-01-01"
					|| $campo_corregido=="1825-01-01"
					|| $campo_corregido=="1830-01-01"
					|| $campo_corregido=="1835-01-01"
					
					)
				
				&& $edad<18				
				&& ($array_fields[22]=="3" || $array_fields[22]=="21")
			    )
			{
				//c4
			    $campo_corregido="1845-01-01";
			}
			else if($es_mayor_a_1900_12_31<0
				&& $array_fields[10]=="F"	
				&& ($array_fields[22]=="3" || $array_fields[22]=="21")
				&& $nueva_edad_year_actual>=18
			    )
			{
				//c5
			    $campo_corregido="1845-01-01";
			}	
			else if(
				(	$campo_corregido=="1800-01-01"
					|| $campo_corregido=="1805-01-01"
					|| $campo_corregido=="1810-01-01"
					|| $campo_corregido=="1825-01-01"
					|| $campo_corregido=="1830-01-01"
					|| $campo_corregido=="1835-01-01"
					
					)
				&& $array_fields[10]=="F"	
				&& ($array_fields[22]=="3" || $array_fields[22]=="21")
				&& $edad>=18	
			    )
			{
				//c6
			    $campo_corregido="1845-01-01";
			}	
			else if($campo_corregido=="1845-01-01"
				&& $edad<18	
				&& ($array_fields[22]=="2")
			    )
			{
				//c7
			    $campo_corregido="1800-01-01";
			}
			else if($campo_corregido=="1845-01-01"
				&& $array_fields[10]=="F"	
				&& $edad>=18
				&& ($array_fields[22]=="1")
			    )
			{
				//c8
			    $campo_corregido="1800-01-01";
			}//fin calidad
		
	     }//fin fase 1
	    }//fin c65
	    
	    //campo a corregir es 66
	    if($numero_campo_a_corregir==66)
	    {
	     if($fase_correccion==0)
	     {
			//campo en blanco
			if($campo_corregido=="")
			{
				if($campo_corregido==""
					&& $array_fields[23]!="1"
					)
				{
				    $campo_corregido="1845-01-01";
				}
				else if($campo_corregido=="")
				{
				    $campo_corregido="1845-01-01";
				}
			}//fin if
		
		//formato
		$corrige_formato=false;
		$array_fecha_campo_actual=explode("-", $campo_corregido);
		if(count($array_fecha_campo_actual)==3)
		{
			if(ctype_digit($array_fecha_campo_actual[0]) && ctype_digit($array_fecha_campo_actual[1]) && ctype_digit($array_fecha_campo_actual[2]))
			{
				//checkdate mm-dd-aaaa -> aaaa-mm-dd ?
				if(checkdate($array_fecha_campo_actual[1],$array_fecha_campo_actual[2],$array_fecha_campo_actual[0])
			       && intval($array_fecha_campo_actual[0])>=32)
			    {
			    	//no se corrige formato
			    }
			    else
			    {
			    	$corrige_formato=true;
			    }

			}
			else
			{
				$corrige_formato=true;
			}
		}
		else
		{
			$corrige_formato=true;
		}//fin else

		if($corrige_formato==true)
		{
		$campo_corregido=$this->corrector_formato_fecha($campo_corregido,false,-2);
		}
		
		//valor permitido
		$excede_fecha_corte=$this->diferencia_dias_entre_fechas($campo_corregido,$date_de_corte);
		$es_menor_o_igual_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
		
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");

		//comparacion fecha campo actual es inferior a la fecha de nacimiento
			$fecha_nacimiento_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),"1900-12-31");
			$campo_actual_inferior_fecha_nacimiento=$this->diferencia_dias_entre_fechas($campo_corregido,trim($array_fields[9]));
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_nacimiento_es_mayor_a_1900_12_31<0
			&& $fecha_nacimiento_es_mayor_a_1900_12_31!==false
			&& $campo_actual_inferior_fecha_nacimiento>0
			&& $campo_actual_inferior_fecha_nacimiento!==false
			)
			{
				$campo_corregido="1800-01-01";
			}//fin if
			//fin comparacion fecha campo actual es inferior a la fecha de nacimiento

			//limite inferior fecha corte 2 years
			$fecha_corte_menos_2_years="";
			$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=false;
			$array_fecha_corte=explode("-", $date_de_corte);
			if(count($array_fecha_corte)==3)
			{
				$fecha_corte_menos_2_years=(intval($array_fecha_corte[0])-2)."-".$array_fecha_corte[1]."-".$array_fecha_corte[2];
				$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=$this->diferencia_dias_entre_fechas($campo_corregido,$fecha_corte_menos_2_years);
			}//fin if
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_corte_menos_2_years!=""
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years!==false
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years>0
			)
			{
				$campo_corregido="1845-01-01";
			}//fin if
			//fin limite inferior fecha corte 2 years

		if($comparacion_a_1900_12_31<0 
			&& $excede_fecha_corte<0
			&& $comparacion_a_1900_12_31!==false
			&& $excede_fecha_corte!==false
			)
		{
			$campo_corregido="1800-01-01";
		}//fin comparacion excede la fecha de corte
		
		if($comparacion_a_1900_12_31>=0
			&& $campo_corregido!="1800-01-01"
			&& $campo_corregido!="1805-01-01"
			&& $campo_corregido!="1810-01-01"
			&& $campo_corregido!="1825-01-01"
			&& $campo_corregido!="1830-01-01"
			&& $campo_corregido!="1835-01-01"
			&& $campo_corregido!="1845-01-01"
		    )
		{
			if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& $array_fields[23]!="1"
				)
			{
			    $campo_corregido="1845-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
			    )
			{
			    $campo_corregido="1845-01-01";
			}
		}//fin if
	     
		//$campo_corregido=$this->corrector_valor_permitido_fecha_criterio_002($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
		//calidad c66
		if(
			$comparacion_a_1900_12_31>=0
			&& $comparacion_a_1900_12_31!==false
			&& trim($array_fields[66])!="1845-01-01"
		)
		{
		    $campo_corregido="1845-01-01";
		}
		
	     }//fin fase 1
	    }//c66
	    //FIN CAMPOS MALTRATO Y VIOLENCIA SEXUAL
	    
	    //CAMPOS CONSULTA MENOR 10 YEARS
	    
	    //campo a corregir es 69
	    if($numero_campo_a_corregir==69)
	    {
	     
	     if($fase_correccion==0)
	     {
			//campo en blanco
	     	if($campo_corregido=="")
	     	{
				if($campo_corregido=="" && $edad<10)
				{
				    $campo_corregido="1835-01-01";
				}
				else if($campo_corregido=="" && $edad>=10)
				{
				    $campo_corregido="1845-01-01";
				}
			}//fin if esta en blanco
		
		//formato
		$corrige_formato=false;
		$array_fecha_campo_actual=explode("-", $campo_corregido);
		if(count($array_fecha_campo_actual)==3)
		{
			if(ctype_digit($array_fecha_campo_actual[0]) && ctype_digit($array_fecha_campo_actual[1]) && ctype_digit($array_fecha_campo_actual[2]))
			{
				//checkdate mm-dd-aaaa -> aaaa-mm-dd ?
				if(checkdate($array_fecha_campo_actual[1],$array_fecha_campo_actual[2],$array_fecha_campo_actual[0])
			       && intval($array_fecha_campo_actual[0])>=32)
			    {
			    	//no se corrige formato
			    }
			    else
			    {
			    	$corrige_formato=true;
			    }

			}
			else
			{
				$corrige_formato=true;
			}
		}
		else
		{
			$corrige_formato=true;
		}//fin else

		if($corrige_formato==true)
		{
		$campo_corregido=$this->corrector_formato_fecha($campo_corregido,false,-2);
		}
		
		//valor permitido
		
		$excede_fecha_corte=$this->diferencia_dias_entre_fechas($campo_corregido,$date_de_corte);
		$es_menor_o_igual_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
		
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");

		//comparacion fecha campo actual es inferior a la fecha de nacimiento
			$fecha_nacimiento_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),"1900-12-31");
			$campo_actual_inferior_fecha_nacimiento=$this->diferencia_dias_entre_fechas($campo_corregido,trim($array_fields[9]));
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_nacimiento_es_mayor_a_1900_12_31<0
			&& $fecha_nacimiento_es_mayor_a_1900_12_31!==false
			&& $campo_actual_inferior_fecha_nacimiento>0
			&& $campo_actual_inferior_fecha_nacimiento!==false
			)
			{
				$campo_corregido="1800-01-01";
			}//fin if
			//fin comparacion fecha campo actual es inferior a la fecha de nacimiento

			//limite inferior fecha corte 2 years
			$fecha_corte_menos_2_years="";
			$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=false;
			$array_fecha_corte=explode("-", $date_de_corte);
			if(count($array_fecha_corte)==3)
			{
				$fecha_corte_menos_2_years=(intval($array_fecha_corte[0])-2)."-".$array_fecha_corte[1]."-".$array_fecha_corte[2];
				$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=$this->diferencia_dias_entre_fechas($campo_corregido,$fecha_corte_menos_2_years);
			}//fin if
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_corte_menos_2_years!=""
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years!==false
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years>0
			)
			{
				$campo_corregido="1845-01-01";
			}//fin if
			//fin limite inferior fecha corte 2 years

		if($comparacion_a_1900_12_31<0 
					&& $excede_fecha_corte<0
					&& $comparacion_a_1900_12_31!==false
					&& $excede_fecha_corte!==false
					)
				{
					$campo_corregido="1800-01-01";
				}//fin comparacion excede la fecha de corte

		if(
			$comparacion_a_1900_12_31>=0
			&& $campo_corregido!="1800-01-01"
			&& $campo_corregido!="1805-01-01"
			&& $campo_corregido!="1810-01-01"
			&& $campo_corregido!="1825-01-01"
			&& $campo_corregido!="1830-01-01"
			&& $campo_corregido!="1835-01-01"
			&& $campo_corregido!="1845-01-01"
			)
		{
			if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& $edad<10
				)
			{
			    $campo_corregido="1835-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& $edad>=10
				)
			{
			    $campo_corregido="1845-01-01";
			}
		}//fin else if
	     
		//$campo_corregido=$this->corrector_valor_permitido_fecha_criterio_002($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
		//calidad con fecha actividad
		$nueva_edad_year_actual=-1;
		$nueva_edad_meses_actual=-1;
		$nueva_edad_dias_actual=-1;
		    
		$array_fecha_nacimiento=explode("-",$array_fields[9]);
		$fecha_campo_actual=explode("-",$campo_corregido);
		if(count($fecha_campo_actual)==3 && checkdate($fecha_campo_actual[1],$fecha_campo_actual[2],$fecha_campo_actual[0]))
		{
		    //echo "<script>alert('entro');</script>";
		    
		    $array_calc_edad_actual=$this->edad_years_months_days($array_fecha_nacimiento[2]."-".$array_fecha_nacimiento[1]."-".$array_fecha_nacimiento[0],$fecha_campo_actual[2]."-".$fecha_campo_actual[1]."-".$fecha_campo_actual[0]);
		    $nueva_edad_year_actual=intval($array_calc_edad_actual['y']);
		    $nueva_edad_meses_actual=(intval($array_calc_edad_actual['y'])*12)+$array_calc_edad_actual['m'];
		    $nueva_edad_dias_actual=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),trim($array_fields[$numero_campo_a_corregir]));
		    
		    //echo "<script>alert(' EDAD DIAS $nueva_edad_dias_actual FN ".trim($array_fields[9])." FA ".trim($array_fields[$numero_campo_a_corregir])."  NCA $numero_campo_a_corregir');</script>";
		}
		$es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
		$es_menor_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
		
		$cc_antes=$campo_corregido;
				
		//calidad
		if($edad<10
			&& ($campo_corregido=="1845-01-01" || $campo_corregido=="1800-01-01")
			)
		{
		    $campo_corregido="1835-01-01";
		}
		else if($nueva_edad_year_actual>=10
			&& $es_mayor_a_1900_12_31<0
			)
		{
		    $campo_corregido="1845-01-01";
		}
		else if($edad>=10		
			&& ($campo_corregido=="1800-01-01"
			    || $campo_corregido=="1805-01-01"
			    || $campo_corregido=="1810-01-01"
			    || $campo_corregido=="1825-01-01"
			    || $campo_corregido=="1830-01-01"
			    || $campo_corregido=="1835-01-01"
			    )
			)
		{
		    $campo_corregido="1845-01-01";
		}
		else if($edad<10		
			&& ($campo_corregido=="1800-01-01"
			    || $campo_corregido=="1805-01-01"
			    || $campo_corregido=="1810-01-01"
			    || $campo_corregido=="1825-01-01"
			    || $campo_corregido=="1830-01-01"
			    || $campo_corregido=="1845-01-01"
			    )
			)
		{
		    $campo_corregido="1835-01-01";
		}
		
		
	     }//fin fase 1
	    }//fin campo 69
	    
	    //campo a corregir es 70
	    if($numero_campo_a_corregir==70)
	    {
	     if($fase_correccion==0)
	     {
		//campo en blanco
	     	if($campo_corregido=="")
	     	{				
				if($campo_corregido=="" && $edad>=10)
				{
				    $campo_corregido="0";
				}
				else if($campo_corregido=="" && $edad<10)
				{
				    $campo_corregido="21";
				}
			}//fin if
		
		//valor permitido
			if($campo_corregido!="0"
			&& $campo_corregido!="1"
			&& $campo_corregido!="16"
			&& $campo_corregido!="17"
			&& $campo_corregido!="18"
			&& $campo_corregido!="20"
			&& $campo_corregido!="21"
			)
			{
				if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="16"
					&& $campo_corregido!="17"
					&& $campo_corregido!="18"
					&& $campo_corregido!="20"
					&& $campo_corregido!="21"
					&& $edad>=10
					)
				{
				    $campo_corregido="0";
				}
				else if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="16"
					&& $campo_corregido!="17"
					&& $campo_corregido!="18"
					&& $campo_corregido!="20"
					&& $campo_corregido!="21"
					&& $edad<10
					)
				{
				    $campo_corregido="21";
				}
			}//fin  if
		
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_016($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
			//calidad c70
			
			if( $campo_corregido!="0" && $edad>=10 )
			{
			    $campo_corregido="0";
			}
			else if( $campo_corregido!="21" && $edad<10 )
			{
			    $campo_corregido="21";
			}//fin calidad
		
	     }//fin fase 1
	    }//fin campo 70
	    
	    //campo a corregir es 71
	    if($numero_campo_a_corregir==71)
	    {
	     if($fase_correccion==0)
	     {
		//campo en blanco
	     	if($campo_corregido=="")
	     	{
				if($campo_corregido=="" && $edad>=10)
				{
				    $campo_corregido="0";
				}
				else if($campo_corregido=="" && $edad<10)
				{
				    $campo_corregido="21";
				}
			}//fin if
		
			//valor permitido
			if(
				$campo_corregido!="0"
			&& $campo_corregido!="1"
			&& $campo_corregido!="16"
			&& $campo_corregido!="17"
			&& $campo_corregido!="18"
			&& $campo_corregido!="20"
			&& $campo_corregido!="21"
				)
			{
				if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="16"
					&& $campo_corregido!="17"
					&& $campo_corregido!="18"
					&& $campo_corregido!="20"
					&& $campo_corregido!="21"
					&& $edad>=10
					)
				{
				    $campo_corregido="0";
				}
				else if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="16"
					&& $campo_corregido!="17"
					&& $campo_corregido!="18"
					&& $campo_corregido!="20"
					&& $campo_corregido!="21"
					&& $edad<10
					)
				{
				    $campo_corregido="21";
				}
			}//fin if
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_016($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
			//calidad c71
			if( $campo_corregido!="0" && $edad>=10 )
			{
			    $campo_corregido="0";
			}
			else if( $campo_corregido!="21" && $edad<10 )
			{
			    $campo_corregido="21";
			}//fin calidad
	     }//fin fase1
	    }//fin campo 71
	    //FIN CAMPOS CONSULTA MENOR 10 YEARS
	    
	    //CAMPOS GRUPO ITS (infecciones de transmision sexual)
	    //campo a corregir es 15
	    if($numero_campo_a_corregir==15)
	    {
	     if($fase_correccion==0)
	     {
		//campo en blanco

	     	if($campo_corregido=="")
	     	{
		
				if($campo_corregido==""
					&& $array_fields[10]=="M"
					&& $edad_dias>=90
				   )
				{
				    //echo "<script>alert('criterio 1 campo_en_blanco 15 vr $campo_corregido');</script>";
				    $campo_corregido="0";
				}
				else if($campo_corregido==""
				    && $array_fields[10]=="F"
				    && $edad_dias>=90
				    && $edad<10
				   )
				{
				    //echo "<script>alert('criterio 2 campo_en_blanco 15 vr $campo_corregido');</script>";
				    $campo_corregido="0";
				}
				else if($campo_corregido==""
				    && $array_fields[10]=="F"
				    && $edad>=60
				   )
				{
				    //echo "<script>alert('criterio 3 campo_en_blanco 15 vr $campo_corregido');</script>";
				    $campo_corregido="0";
				}
				else if($campo_corregido==""
					&& trim($array_fields[49])!="1845-01-01"
					&& $array_fields[10]=="F"
					&& $edad>=10
					&& $edad<60
				   )
				{
				    //echo "<script>alert('criterio 4 campo_en_blanco 15 vr $campo_corregido');</script>";
				    $campo_corregido="0";
				}
				else if($campo_corregido==""
					&& trim($array_fields[50])!="1845-01-01"
					&& $array_fields[10]=="F"
					&& $edad>=10
					&& $edad<60
				   )
				{
				    //echo "<script>alert('criterio 5 campo_en_blanco 15 vr $campo_corregido');</script>";
				    $campo_corregido="0";
				}
				else if($campo_corregido==""
					&& trim($array_fields[14])!="1"
					&& trim($array_fields[49])=="1845-01-01"
					&& $array_fields[10]=="F"
					&& $edad>=10
					&& $edad<60
				   )
				{
				    //echo "<script>alert('criterio 6 campo_en_blanco 15 vr $campo_corregido');</script>";
				    $campo_corregido="0";
				}
				else if($campo_corregido==""
					&& trim($array_fields[14])=="1"			
					&& trim($array_fields[81])=="2"
					&& trim($array_fields[49])=="1845-01-01"
					&& $array_fields[10]=="F"
					&& $edad>=10
					&& $edad<60
				   )
				{
				    //echo "<script>alert('criterio 7 campo_en_blanco 15 vr $campo_corregido');</script>";
				    $campo_corregido="1";
				}
				else if($campo_corregido==""		
					&& trim($array_fields[81])=="2"
					&& $edad_dias<90
				   )
				{
				    //echo "<script>alert('criterio 8 campo_en_blanco 15 vr $campo_corregido');</script>";
				    $campo_corregido="2";
				}
				else if($campo_corregido==""
					&& trim($array_fields[14])=="1"			
					&& (trim($array_fields[81])=="1" || trim($array_fields[81])=="22")
					&& trim($array_fields[49])=="1845-01-01"
					&& $array_fields[10]=="F"
					&& $edad>=10
					&& $edad<60
				   )
				{
				    //echo "<script>alert('criterio 9 campo_en_blanco 15 vr $campo_corregido');</script>";
				    $campo_corregido="3";
				}
				else if($campo_corregido==""	
					&& (trim($array_fields[81])=="1" || trim($array_fields[81])=="22")
					&& $edad_dias<90
				   )
				{
				    //echo "<script>alert('criterio 10 campo_en_blanco 15 vr $campo_corregido');</script>";
				    $campo_corregido="3";
				}
				else if($campo_corregido==""
					&& trim($array_fields[14])=="1"			
					&& (trim($array_fields[81])=="0")
					&& trim($array_fields[49])=="1845-01-01"
					&& $array_fields[10]=="F"
					&& $edad>=10
					&& $edad<60
				   )
				{
				    //echo "<script>alert('criterio 11 campo_en_blanco 15 vr $campo_corregido');</script>";
				    $campo_corregido="21";
				}
				else if($campo_corregido==""	
					&& (trim($array_fields[81])=="0")
					&& $edad_dias<90
				   )
				{
				    //echo "<script>alert('criterio 12 campo_en_blanco 15 vr $campo_corregido');</script>";
				    $campo_corregido="21";
				}
				else if($campo_corregido=="")
				{
				    //echo "<script>alert('criterio 13 campo_en_blanco 15 vr $campo_corregido');</script>";
				    $campo_corregido="0";
				}	
			}//fin if
		
		//valor permitido

		if($campo_corregido!="0"
		&& $campo_corregido!="1"
		&& $campo_corregido!="2"
		&& $campo_corregido!="3"
		&& $campo_corregido!="21"
		)
		{
		
			if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="3"
					&& $campo_corregido!="21"
					&& $array_fields[10]=="M"
					&& $edad_dias>=90
				   )
				{
				    //echo "<script>alert('criterio 1 vp 15 vr $campo_corregido');</script>";
				    $campo_corregido="0";
				}
				else if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="3"
					&& $campo_corregido!="21"
				    && $array_fields[10]=="F"
				    && $edad_dias>=90
				    && $edad<10
				   )
				{
				    //echo "<script>alert('criterio 2 vp 15 vr $campo_corregido');</script>";
				    $campo_corregido="0";
				}
				else if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="3"
					&& $campo_corregido!="21"
				    && $array_fields[10]=="F"
				    && $edad>=60
				   )
				{
				    //echo "<script>alert('criterio 3 vp 15 vr $campo_corregido');</script>";
				    $campo_corregido="0";
				}
				else if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="3"
					&& $campo_corregido!="21"
					&& trim($array_fields[49])!="1845-01-01"
					&& $array_fields[10]=="F"
					&& $edad>=10
					&& $edad<60
				   )
				{
				    //echo "<script>alert('criterio 4 vp 15 vr $campo_corregido');</script>";
				    $campo_corregido="0";
				}
				else if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="3"
					&& $campo_corregido!="21"
					&& trim($array_fields[50])!="1845-01-01"
					&& $array_fields[10]=="F"
					&& $edad>=10
					&& $edad<60
				   )
				{
				    //echo "<script>alert('criterio 5 vp 15 vr $campo_corregido');</script>";
				    $campo_corregido="0";
				}
				else if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="3"
					&& $campo_corregido!="21"
					&& trim($array_fields[14])!="1"
					&& trim($array_fields[49])=="1845-01-01"
					&& $array_fields[10]=="F"
					&& $edad>=10
					&& $edad<60
				   )
				{
				    //echo "<script>alert('criterio 6 vp 15 vr $campo_corregido');</script>";
				    $campo_corregido="0";
				}
				else if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="3"
					&& $campo_corregido!="21"
					&& trim($array_fields[14])=="1"			
					&& trim($array_fields[81])=="2"
					&& trim($array_fields[49])=="1845-01-01"
					&& $array_fields[10]=="F"
					&& $edad>=10
					&& $edad<60
				   )
				{
				    //echo "<script>alert('criterio 7 vp 15 vr $campo_corregido');</script>";
				    $campo_corregido="1";
				}
				else if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="3"
					&& $campo_corregido!="21"	
					&& trim($array_fields[81])=="2"
					&& $edad_dias<90
				   )
				{
				    //echo "<script>alert('criterio 8 vp 15 vr $campo_corregido');</script>";
				    $campo_corregido="2";
				}
				else if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="3"
					&& $campo_corregido!="21"
					&& trim($array_fields[14])=="1"			
					&& (trim($array_fields[81])=="1" || trim($array_fields[81])=="22")
					&& trim($array_fields[49])=="1845-01-01"
					&& $array_fields[10]=="F"
					&& $edad>=10
					&& $edad<60
				   )
				{
				    //echo "<script>alert('criterio 9 vp 15 vr $campo_corregido');</script>";
				    $campo_corregido="3";
				}
				else if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="3"
					&& $campo_corregido!="21"
					&& (trim($array_fields[81])=="1" || trim($array_fields[81])=="22")
					&& $edad_dias<90
				   )
				{
				    //echo "<script>alert('criterio 10 vp 15 vr $campo_corregido');</script>";
				    $campo_corregido="3";
				}
				else if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="3"
					&& $campo_corregido!="21"
					&& trim($array_fields[14])=="1"			
					&& (trim($array_fields[81])=="0")
					&& trim($array_fields[49])=="1845-01-01"
					&& $array_fields[10]=="F"
					&& $edad>=10
					&& $edad<60
				   )
				{
				    //echo "<script>alert('criterio 11 vp 15 vr $campo_corregido');</script>";
				    $campo_corregido="21";
				}
				else if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="3"
					&& $campo_corregido!="21"	
					&& (trim($array_fields[81])=="0")
					&& $edad_dias<90
				   )
				{
				    //echo "<script>alert('criterio 12 vp 15 vr $campo_corregido');</script>";
				    $campo_corregido="21";
				}
				else if($campo_corregido!="0"
					&& $campo_corregido!="1"
					&& $campo_corregido!="2"
					&& $campo_corregido!="3"
					&& $campo_corregido!="21"
					)
				{
				    //echo "<script>alert('criterio 13 valor_permitido 15 vr $campo_corregido');</script>";
				    $campo_corregido="0";
				}
		}//fin if
		
		
		
		
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_008($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
		//echo "<script>alert('entro fase 1 15');</script>";
		
			//calidad c15
				if($campo_corregido!="0"
					&& $array_fields[10]=="M"
					&& $edad_dias>=90
				   )
				{
				    //echo "<script>alert('criterio 1 calidad 15 vr $campo_corregido');</script>";
				    $campo_corregido="0";
				}
				else if($campo_corregido!="0"
				    && $array_fields[10]=="F"
				    && $edad_dias>=90
				    && $edad<10
				   )
				{
				    //echo "<script>alert('criterio 2 calidad 15 vr $campo_corregido');</script>";
				    $campo_corregido="0";
				}
				else if($campo_corregido!="0"
				    && $array_fields[10]=="F"
				    && $edad>=60
				   )
				{
				    //echo "<script>alert('criterio 3 calidad 15 vr $campo_corregido');</script>";
				    $campo_corregido="0";
				}
				else if($campo_corregido!="0"
					&& trim($array_fields[49])!="1845-01-01"
					&& $array_fields[10]=="F"
					&& $edad>=10
					&& $edad<60
				   )
				{
				    //echo "<script>alert('criterio 4 calidad 15 vr $campo_corregido');</script>";
				    $campo_corregido="0";
				}
				else if($campo_corregido!="0"
					&& trim($array_fields[50])!="1845-01-01"
					&& $array_fields[10]=="F"
					&& $edad>=10
					&& $edad<60
				   )
				{
				    //echo "<script>alert('criterio 5 campo_en_blanco 15 vr $campo_corregido');</script>";
				    $campo_corregido="0";
				}
				else if($campo_corregido!="0"
					&& trim($array_fields[14])!="1"
					&& trim($array_fields[49])=="1845-01-01"
					&& $array_fields[10]=="F"
					&& $edad>=10
					&& $edad<60
				   )
				{
				    //echo "<script>alert('criterio 6 campo_en_blanco 15 vr $campo_corregido');</script>";
				    $campo_corregido="0";
				}
				else if($campo_corregido!="1"
					&& trim($array_fields[14])=="1"			
					&& trim($array_fields[81])=="2"
					&& trim($array_fields[49])=="1845-01-01"
					&& $array_fields[10]=="F"
					&& $edad>=10
					&& $edad<60
				   )
				{
				    //echo "<script>alert('criterio 7 campo_en_blanco 15 vr $campo_corregido');</script>";
				    $campo_corregido="1";
				}
				else if($campo_corregido!="2"		
					&& trim($array_fields[81])=="2"
					&& $edad_dias<90
				   )
				{
				    //echo "<script>alert('criterio 8 campo_en_blanco 15 vr $campo_corregido');</script>";
				    $campo_corregido="2";
				}
				else if($campo_corregido!="3"
					&& trim($array_fields[14])=="1"			
					&&  (trim($array_fields[81])=="1" || trim($array_fields[81])=="22")
					&& trim($array_fields[49])=="1845-01-01"
					&& $array_fields[10]=="F"
					&& $edad>=10
					&& $edad<60
				   )
				{
				    //echo "<script>alert('criterio 9 campo_en_blanco 15 vr $campo_corregido');</script>";
				    $campo_corregido="3";
				}
				else if($campo_corregido!="3"	
					&&  (trim($array_fields[81])=="1" || trim($array_fields[81])=="22")
					&& $edad_dias<90
				   )
				{
				    //echo "<script>alert('criterio 10 campo_en_blanco 15 vr $campo_corregido');</script>";
				    $campo_corregido="3";
				}
				else if($campo_corregido!="21"
					&& trim($array_fields[14])=="1"			
					&& (trim($array_fields[81])=="0" )
					&& trim($array_fields[49])=="1845-01-01"
					&& $array_fields[10]=="F"
					&& $edad>=10
					&& $edad<60
				   )
				{
				    //echo "<script>alert('criterio 11 campo_en_blanco 15 vr $campo_corregido');</script>";
				    $campo_corregido="21";
				}
				else if($campo_corregido!="21"	
					&& (trim($array_fields[81])=="0" )
					&& $edad_dias<90
				   )
				{
				    //echo "<script>alert('criterio 12 campo_en_blanco 15 vr $campo_corregido');</script>";
				    $campo_corregido="21";
				}//fin calidad
	       
		
	     }//fin fase
	    }//fin c15
	    
	    //campo a corregir es 24
	    if($numero_campo_a_corregir==24)
	    {
	     if($fase_correccion==0)
	     {
			//campo en blanco
	     	if($campo_corregido=="")
			{
				if($campo_corregido==""
				   && ($array_fields[15]=="1")
				   )
				{
				    $campo_corregido="1";
				}
				else if($campo_corregido==""
				   && $array_fields[81]=="2"
				   )
				{
				    $campo_corregido="1";
				}
				else if($campo_corregido==""
					&& $array_fields[83]=="2"
				   )
				{
				    $campo_corregido="1";
				}
				else if($campo_corregido=="")
				{
				    $campo_corregido="21";
				}
			}//fin if
			
			//valor permitido
			if(	$campo_corregido!="1"
			   && $campo_corregido!="2"
			   && $campo_corregido!="21"
			   )
			{
				if($campo_corregido!="1"
				   && $campo_corregido!="2"
				   && $campo_corregido!="21"
				   && ($array_fields[15]=="1" )
				   )
				{
				    $campo_corregido="1";
				}
				else if($campo_corregido!="1"
				   && $campo_corregido!="2"
				   && $campo_corregido!="21"
				   && $array_fields[81]=="2"
				   )
				{
				    $campo_corregido="1";
				}
				else if($campo_corregido!="1"
				   && $campo_corregido!="2"
				   && $campo_corregido!="21"
				   && $array_fields[83]=="2"
				   )
				{
				    $campo_corregido="1";
				}
				else if($campo_corregido!="1"
				   && $campo_corregido!="2"
				   && $campo_corregido!="21"
				   )
				{
				    $campo_corregido="21";
				}
			}//fin if
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_009($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
		//calidad
		if($campo_corregido!="1"
			&& ($array_fields[15]=="1" )
		   )
		{
		    $campo_corregido="1";
		}	
		else if($campo_corregido!="1"
			&& $array_fields[81]=="2"
		   )
		{
		    $campo_corregido="1";
		}
		//campo 83 igual 2 significa es vih positivo
		else if($campo_corregido!="1"
			&& $array_fields[83]=="2"
		   )
		{
		    $campo_corregido="1";
		}	
	     }//fin fase 1
	    }//fin campo 24
	    
	    //campo a corregir es 74
	    if($numero_campo_a_corregir==74)
	    {
	     if($fase_correccion==0)
	     {
			//campo en blanco
	     	if($campo_corregido=="")
			{
				if($campo_corregido==""
				   && $array_fields[24]=="1"
				   )
				{
				    $campo_corregido="999";//se cambio apra prepagada pero duda si tmabien es asi para los demas
				}
				else if($campo_corregido==""
				   && ($array_fields[24]=="2" || $array_fields[24]=="21")
				   )
				{
				    $campo_corregido="0";
				}
				else if($campo_corregido=="")
				{
				    $campo_corregido="0";
				}
			}//fin if
		
		//valor permitido
		if(($campo_corregido!="0"
		&& $campo_corregido!="993"
		&& $campo_corregido!="994"
		&& $campo_corregido!="995"
		&& $campo_corregido!="996"
		&& $campo_corregido!="997"
		&& $campo_corregido!="999")
		&& (intval($campo_corregido)<1 || intval($campo_corregido)>=150)
		)
		{

			if(($campo_corregido!="0"
				&& $campo_corregido!="993"
				&& $campo_corregido!="994"
				&& $campo_corregido!="995"
				&& $campo_corregido!="996"
				&& $campo_corregido!="997"
				&& $campo_corregido!="999")
				&& (intval($campo_corregido)<1 || intval($campo_corregido)>=150)
				&& $array_fields[24]=="1"
			)
			{
			    $campo_corregido="999";
			}
			else if(($campo_corregido!="0"
				&& $campo_corregido!="993"
				&& $campo_corregido!="994"
				&& $campo_corregido!="995"
				&& $campo_corregido!="996"
				&& $campo_corregido!="997"
				&& $campo_corregido!="999")
				&& (intval($campo_corregido)<1 || intval($campo_corregido)>=150)
				&& ($array_fields[24]=="2" || $array_fields[24]=="21")
			)
			{
			    $campo_corregido="0";
			}
			else if(($campo_corregido!="0"
				&& $campo_corregido!="993"
				&& $campo_corregido!="994"
				&& $campo_corregido!="995"
				&& $campo_corregido!="996"
				&& $campo_corregido!="997"
				&& $campo_corregido!="999")
				&& (intval($campo_corregido)<1 || intval($campo_corregido)>=150)
			)
			{
			    $campo_corregido="0";
			}
		}//fin if
		
		
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_019($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
		
		
			//validacion de calidad
			if($campo_corregido!="999" && $array_fields[24]=="1")
			{
			    $campo_corregido="999";
			}//fin 
			else if($campo_corregido!="0" 
				&& ($array_fields[24]=="2" || $array_fields[24]=="21") 
				)
			{
			    $campo_corregido="0";
			}//fin calidad

	     }//fin fase 1
	    }//fin campo 74
	    
	    //campo a corregir es 75
	    if($numero_campo_a_corregir==75)
	    {
	     if($fase_correccion==0)
	     {
		//campo en blanco
     	if($campo_corregido=="")
		{
			if($campo_corregido==""
			  &&  trim($array_fields[83])=="0"
			  && $array_fields[14]!="1"
			)
			{
			    $campo_corregido="1845-01-01";
			}
			else if($campo_corregido==""
			  &&  trim($array_fields[83])=="0"
			  && $array_fields[14]=="1"
			)//fin condicion
			{
			    $campo_corregido="1835-01-01";
			}
			else if($campo_corregido==""
			  &&  (trim($array_fields[83])=="1"
			    || trim($array_fields[83])=="2"
			    || trim($array_fields[83])=="22"
			    )
			)
			{
			    $campo_corregido="1800-01-01";
			}
			else if($campo_corregido=="")
			{
			    $campo_corregido="1845-01-01";
			}
		}//fin if
		
		//formato
		$corrige_formato=false;
		$array_fecha_campo_actual=explode("-", $campo_corregido);
		if(count($array_fecha_campo_actual)==3)
		{
			if(ctype_digit($array_fecha_campo_actual[0]) && ctype_digit($array_fecha_campo_actual[1]) && ctype_digit($array_fecha_campo_actual[2]))
			{
				//checkdate mm-dd-aaaa -> aaaa-mm-dd ?
				if(checkdate($array_fecha_campo_actual[1],$array_fecha_campo_actual[2],$array_fecha_campo_actual[0])
			       && intval($array_fecha_campo_actual[0])>=32)
			    {
			    	//no se corrige formato
			    }
			    else
			    {
			    	$corrige_formato=true;
			    }

			}
			else
			{
				$corrige_formato=true;
			}
		}
		else
		{
			$corrige_formato=true;
		}//fin else

		if($corrige_formato==true)
		{
		$campo_corregido=$this->corrector_formato_fecha($campo_corregido,false,-2);
		}
		
		//valor permitido
		
		$excede_fecha_corte=$this->diferencia_dias_entre_fechas($campo_corregido,$date_de_corte);
		$es_menor_o_igual_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
		
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");

		//comparacion fecha campo actual es inferior a la fecha de nacimiento
			$fecha_nacimiento_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),"1900-12-31");
			$campo_actual_inferior_fecha_nacimiento=$this->diferencia_dias_entre_fechas($campo_corregido,trim($array_fields[9]));
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_nacimiento_es_mayor_a_1900_12_31<0
			&& $fecha_nacimiento_es_mayor_a_1900_12_31!==false
			&& $campo_actual_inferior_fecha_nacimiento>0
			&& $campo_actual_inferior_fecha_nacimiento!==false
			)
			{
				$campo_corregido="1800-01-01";
			}//fin if
			//fin comparacion fecha campo actual es inferior a la fecha de nacimiento

			//limite inferior fecha corte 2 years
			$fecha_corte_menos_2_years="";
			$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=false;
			$array_fecha_corte=explode("-", $date_de_corte);
			if(count($array_fecha_corte)==3)
			{
				$fecha_corte_menos_2_years=(intval($array_fecha_corte[0])-2)."-".$array_fecha_corte[1]."-".$array_fecha_corte[2];
				$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=$this->diferencia_dias_entre_fechas($campo_corregido,$fecha_corte_menos_2_years);
			}//fin if
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_corte_menos_2_years!=""
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years!==false
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years>0
			)
			{
				$campo_corregido="1845-01-01";
			}//fin if
			//fin limite inferior fecha corte 2 years

		if($comparacion_a_1900_12_31<0 
					&& $excede_fecha_corte<0
					&& $comparacion_a_1900_12_31!==false
					&& $excede_fecha_corte!==false
					)
				{
					$campo_corregido="1800-01-01";
				}//fin comparacion excede la fecha de corte

		if($comparacion_a_1900_12_31>=0
			&& $campo_corregido!="1800-01-01"
			&& $campo_corregido!="1805-01-01"
			&& $campo_corregido!="1810-01-01"
			&& $campo_corregido!="1825-01-01"
			&& $campo_corregido!="1830-01-01"
			&& $campo_corregido!="1835-01-01"
			&& $campo_corregido!="1845-01-01"
			)
		{
			if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
			  &&  trim($array_fields[83])=="0"
			  && $array_fields[14]!="1"
			)
			{
			    $campo_corregido="1845-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
			  &&  trim($array_fields[83])=="0"
			  && $array_fields[14]=="1"
			)//fin condicion
			{
			    $campo_corregido="1835-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
			  &&  (trim($array_fields[83])=="1"
			    || trim($array_fields[83])=="2"
			    || trim($array_fields[83])=="22"
			    )
			)
			{
			    $campo_corregido="1800-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				)
			{
			    $campo_corregido="1845-01-01";
			}
		}//fin else if
		//$campo_corregido=$this->corrector_valor_permitido_fecha_criterio_002($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
		
		//calidad
		$es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
		$c76_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[76]),"1900-12-31");
		$es_mayor_al_campo76=$this->diferencia_dias_entre_fechas($campo_corregido,trim($array_fields[76]));
		
		$c82_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[82]),"1900-12-31");
		$es_mayor_al_campo82=$this->diferencia_dias_entre_fechas($campo_corregido,trim($array_fields[82]));
	        

	    	if($campo_corregido!="1845-01-01"
			  &&  trim($array_fields[83])=="0"
			  && $array_fields[14]!="1"
			)
			{
			    $campo_corregido="1845-01-01";
			}
			else if($campo_corregido!="1805-01-01"
			&& $campo_corregido!="1810-01-01"
			&& $campo_corregido!="1825-01-01"
			&& $campo_corregido!="1830-01-01"
			&& $campo_corregido!="1835-01-01"
			  &&  trim($array_fields[83])=="0"
			  && $array_fields[14]=="1"
			)//fin condicion
			{
			    $campo_corregido="1835-01-01";
			}
			else if(
				($campo_corregido=="1805-01-01"
		    || $campo_corregido=="1810-01-01"
		    || $campo_corregido=="1825-01-01"
		    || $campo_corregido=="1830-01-01"
		    || $campo_corregido=="1835-01-01"
		    || $campo_corregido=="1845-01-01"
		    )
			  &&  (trim($array_fields[83])=="1"
			    || trim($array_fields[83])=="2"
			    || trim($array_fields[83])=="22"
			    )
			)
			{
			    $campo_corregido="1800-01-01";
			}
			else if($es_mayor_a_1900_12_31<0
				&& trim($array_fields[83])!="0"
				&& $c76_es_mayor_a_1900_12_31<0
				&& $es_mayor_al_campo76<=0//o igual
				&& $es_mayor_al_campo76!==false
				)
			{
				$campo_corregido="1800-01-01";
			}
			else if($es_mayor_a_1900_12_31<0
				&& trim($array_fields[83])!="0"
				&& $c82_es_mayor_a_1900_12_31<0
				&& $es_mayor_al_campo82<=0//o igual
				&& $es_mayor_al_campo82!==false
				)
			{
				$campo_corregido="1800-01-01";
			}

		
	       
	     }//fin fase 1
	    }//fin campo 75
	    
	    //campo a corregir es 76
	    if($numero_campo_a_corregir==76)
	    {
	     
	     if($fase_correccion==0)
	     {
		$c75_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[75]),"1900-12-31");
		$c75_es_menor_o_igual_1845_01_01=$this->diferencia_dias_entre_fechas(trim($array_fields[75]),"1845-01-01");
		$c75_es_mayor_a_1845_01_01=$this->diferencia_dias_entre_fechas(trim($array_fields[75]),"1800-01-01");
		
		//campo en blanco
		if($campo_corregido=="")
		{
			if($campo_corregido==""
			  &&  trim($array_fields[83])=="0"
			  && $array_fields[14]!="1"
			)
			{
			    $campo_corregido="1845-01-01";
			}
			else if($campo_corregido==""
			  &&  trim($array_fields[83])=="0"
			  && $array_fields[14]=="1"
			)//fin condicion
			{
			    $campo_corregido="1835-01-01";
			}
			else if($campo_corregido==""
			  &&  (trim($array_fields[83])=="1"
			    || trim($array_fields[83])=="2"
			    || trim($array_fields[83])=="22"
			    )
			)
			{
			    $campo_corregido="1800-01-01";
			}
			else if($campo_corregido=="")
			{
			    $campo_corregido="1845-01-01";
			}
			
		}//fin if
		
		//formato
		$corrige_formato=false;
		$array_fecha_campo_actual=explode("-", $campo_corregido);
		if(count($array_fecha_campo_actual)==3)
		{
			if(ctype_digit($array_fecha_campo_actual[0]) && ctype_digit($array_fecha_campo_actual[1]) && ctype_digit($array_fecha_campo_actual[2]))
			{
				//checkdate mm-dd-aaaa -> aaaa-mm-dd ?
				if(checkdate($array_fecha_campo_actual[1],$array_fecha_campo_actual[2],$array_fecha_campo_actual[0])
			       && intval($array_fecha_campo_actual[0])>=32)
			    {
			    	//no se corrige formato
			    }
			    else
			    {
			    	$corrige_formato=true;
			    }

			}
			else
			{
				$corrige_formato=true;
			}
		}
		else
		{
			$corrige_formato=true;
		}//fin else

		if($corrige_formato==true)
		{
		$campo_corregido=$this->corrector_formato_fecha($campo_corregido,false,-2);
		}
		
		//valor permitido
		
		$excede_fecha_corte=$this->diferencia_dias_entre_fechas($campo_corregido,$date_de_corte);
		$es_menor_o_igual_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
		
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");

		//comparacion fecha campo actual es inferior a la fecha de nacimiento
			$fecha_nacimiento_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),"1900-12-31");
			$campo_actual_inferior_fecha_nacimiento=$this->diferencia_dias_entre_fechas($campo_corregido,trim($array_fields[9]));
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_nacimiento_es_mayor_a_1900_12_31<0
			&& $fecha_nacimiento_es_mayor_a_1900_12_31!==false
			&& $campo_actual_inferior_fecha_nacimiento>0
			&& $campo_actual_inferior_fecha_nacimiento!==false
			)
			{
				$campo_corregido="1800-01-01";
			}//fin if
			//fin comparacion fecha campo actual es inferior a la fecha de nacimiento

			//limite inferior fecha corte 2 years
			$fecha_corte_menos_2_years="";
			$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=false;
			$array_fecha_corte=explode("-", $date_de_corte);
			if(count($array_fecha_corte)==3)
			{
				$fecha_corte_menos_2_years=(intval($array_fecha_corte[0])-2)."-".$array_fecha_corte[1]."-".$array_fecha_corte[2];
				$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=$this->diferencia_dias_entre_fechas($campo_corregido,$fecha_corte_menos_2_years);
			}//fin if
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_corte_menos_2_years!=""
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years!==false
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years>0
			)
			{
				$campo_corregido="1845-01-01";
			}//fin if
			//fin limite inferior fecha corte 2 years

		if($comparacion_a_1900_12_31<0 
					&& $excede_fecha_corte<0
					&& $comparacion_a_1900_12_31!==false
					&& $excede_fecha_corte!==false
					)
				{
					$campo_corregido="1800-01-01";
				}//fin comparacion excede la fecha de corte
		
		if($comparacion_a_1900_12_31>=0
			&& $campo_corregido!="1800-01-01"
			&& $campo_corregido!="1805-01-01"
			&& $campo_corregido!="1810-01-01"
			&& $campo_corregido!="1825-01-01"
			&& $campo_corregido!="1830-01-01"
			&& $campo_corregido!="1835-01-01"
			&& $campo_corregido!="1845-01-01"
			)
		{
			 if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
			  &&  trim($array_fields[83])=="0"
			  && $array_fields[14]!="1"
			)
			{
			    $campo_corregido="1845-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
			  &&  trim($array_fields[83])=="0"
			  && $array_fields[14]=="1"
			)//fin condicion
			{
			    $campo_corregido="1835-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
			  &&  (trim($array_fields[83])=="1"
			    || trim($array_fields[83])=="2"
			    || trim($array_fields[83])=="22"
			    )
			)
			{
			    $campo_corregido="1800-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				)
			{
			    $campo_corregido="1845-01-01";
			}
		}//fin else if
		
		//$campo_corregido=$this->corrector_valor_permitido_fecha_criterio_002($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
		$es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
		$es_menor_al_campo75=$this->diferencia_dias_entre_fechas($campo_corregido,trim($array_fields[75]));
		$es_menor_al_campo82=$this->diferencia_dias_entre_fechas($campo_corregido,trim($array_fields[82]));
		$c82_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[82]),"1900-12-31");
		$c75_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[75]),"1900-12-31");
		
		//calidad
		if($campo_corregido!="1845-01-01"
			  &&  trim($array_fields[83])=="0"
			  && $array_fields[14]!="1"
			)
			{
			    $campo_corregido="1845-01-01";
			}
			else if($campo_corregido!="1805-01-01"
			&& $campo_corregido!="1810-01-01"
			&& $campo_corregido!="1825-01-01"
			&& $campo_corregido!="1830-01-01"
			&& $campo_corregido!="1835-01-01"
			  &&  trim($array_fields[83])=="0"
			  && $array_fields[14]=="1"
			)//fin condicion
			{
			    $campo_corregido="1835-01-01";
			}
			else if(
				($campo_corregido=="1805-01-01"
		    || $campo_corregido=="1810-01-01"
		    || $campo_corregido=="1825-01-01"
		    || $campo_corregido=="1830-01-01"
		    || $campo_corregido=="1835-01-01"
		    || $campo_corregido=="1845-01-01"
		    )
			  &&  (trim($array_fields[83])=="1"
			    || trim($array_fields[83])=="2"
			    || trim($array_fields[83])=="22"
			    )
			)
			{
			    $campo_corregido="1800-01-01";
			}
			else if($es_mayor_a_1900_12_31<0
				&& trim($array_fields[83])!="0"
				&& $c75_es_mayor_a_1900_12_31<0
				&& $es_menor_al_campo75>=0//o igual
				&& $es_menor_al_campo75!==false
				)
			{
				$campo_corregido="1800-01-01";
			}
			else if($es_mayor_a_1900_12_31<0
				&& trim($array_fields[83])!="0"
				&& $c82_es_mayor_a_1900_12_31<0
				&& $es_menor_al_campo82>=0//o igual
				&& $es_menor_al_campo82!==false
				)
			{
				$campo_corregido="1800-01-01";
			}
		
		
		
	     }//fin fase 1
	    }//fin campo 76
	    
	    //campo a corregir es 80
	    if($numero_campo_a_corregir==80)
	    {
	     if($fase_correccion==0)
	     {
			//campo en blanco
	     	if($campo_corregido=="")
			{
				if($campo_corregido=="" 
					&& ($array_fields[81]=="0" || $array_fields[81]=="22")
					)
				{
				    $campo_corregido="1845-01-01";
				}
				else if($campo_corregido=="" 
					&& ($array_fields[81]=="1" || $array_fields[81]=="2")
					 )
				{
				    $campo_corregido="1800-01-01";
				}
				else if($campo_corregido=="")
				{
				    $campo_corregido="1845-01-01";
				}
			}//fin if
		
		//formato
		$corrige_formato=false;
		$array_fecha_campo_actual=explode("-", $campo_corregido);
		if(count($array_fecha_campo_actual)==3)
		{
			if(ctype_digit($array_fecha_campo_actual[0]) && ctype_digit($array_fecha_campo_actual[1]) && ctype_digit($array_fecha_campo_actual[2]))
			{
				//checkdate mm-dd-aaaa -> aaaa-mm-dd ?
				if(checkdate($array_fecha_campo_actual[1],$array_fecha_campo_actual[2],$array_fecha_campo_actual[0])
			       && intval($array_fecha_campo_actual[0])>=32)
			    {
			    	//no se corrige formato
			    }
			    else
			    {
			    	$corrige_formato=true;
			    }

			}
			else
			{
				$corrige_formato=true;
			}
		}
		else
		{
			$corrige_formato=true;
		}//fin else

		if($corrige_formato==true)
		{
		$campo_corregido=$this->corrector_formato_fecha($campo_corregido,false,-2);
		}
		
		//valor permitido
		
		$excede_fecha_corte=$this->diferencia_dias_entre_fechas($campo_corregido,$date_de_corte);
		$es_menor_o_igual_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
		
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");

		//comparacion fecha campo actual es inferior a la fecha de nacimiento
			$fecha_nacimiento_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),"1900-12-31");
			$campo_actual_inferior_fecha_nacimiento=$this->diferencia_dias_entre_fechas($campo_corregido,trim($array_fields[9]));
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_nacimiento_es_mayor_a_1900_12_31<0
			&& $fecha_nacimiento_es_mayor_a_1900_12_31!==false
			&& $campo_actual_inferior_fecha_nacimiento>0
			&& $campo_actual_inferior_fecha_nacimiento!==false
			)
			{
				$campo_corregido="1800-01-01";
			}//fin if
			//fin comparacion fecha campo actual es inferior a la fecha de nacimiento

			//limite inferior fecha corte 2 years
			$fecha_corte_menos_2_years="";
			$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=false;
			$array_fecha_corte=explode("-", $date_de_corte);
			if(count($array_fecha_corte)==3)
			{
				$fecha_corte_menos_2_years=(intval($array_fecha_corte[0])-2)."-".$array_fecha_corte[1]."-".$array_fecha_corte[2];
				$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=$this->diferencia_dias_entre_fechas($campo_corregido,$fecha_corte_menos_2_years);
			}//fin if
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_corte_menos_2_years!=""
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years!==false
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years>0
			)
			{
				$campo_corregido="1845-01-01";
			}//fin if
			//fin limite inferior fecha corte 2 years

		if($comparacion_a_1900_12_31<0 
					&& $excede_fecha_corte<0
					&& $comparacion_a_1900_12_31!==false
					&& $excede_fecha_corte!==false
					)
				{
					$campo_corregido="1800-01-01";
				}//fin comparacion excede la fecha de corte

		if($comparacion_a_1900_12_31>=0
			&& $campo_corregido!="1800-01-01"
			&& $campo_corregido!="1805-01-01"
			&& $campo_corregido!="1810-01-01"
			&& $campo_corregido!="1825-01-01"
			&& $campo_corregido!="1830-01-01"
			&& $campo_corregido!="1835-01-01"
			&& $campo_corregido!="1845-01-01"
			)
		{
			if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& ($array_fields[81]=="0" || $array_fields[81]=="22")
				)
			{
			    $campo_corregido="1845-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& ($array_fields[81]=="1" || $array_fields[81]=="2")
				)
			{
			    $campo_corregido="1800-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				)
			{
			    $campo_corregido="1845-01-01";
			}
		}//fin else if
	     
		//$campo_corregido=$this->corrector_valor_permitido_fecha_criterio_002($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
			//calidad
			if(
				$campo_corregido!="1845-01-01"
			   && ($array_fields[81]=="0" )
			   )
			{
			    $campo_corregido="1845-01-01";
			}
			else if(($campo_corregido=="1805-01-01"
				|| $campo_corregido=="1810-01-01"
				|| $campo_corregido=="1825-01-01"
				|| $campo_corregido=="1830-01-01"
				|| $campo_corregido=="1835-01-01"
				|| $campo_corregido=="1845-01-01")
				&&  ($array_fields[81]=="1" || $array_fields[81]=="2")
				)
			{
			    $campo_corregido="1800-01-01";
			}
			else if(
				($campo_corregido=="1800-01-01")
				&&  ($array_fields[81]=="22")
				)
			{
			    $campo_corregido="1845-01-01";
			}
	     }//fin fase 1
	    }//fin campo 80
	    
	    //campo a corregir es 81
	    if($numero_campo_a_corregir==81)
	    {
	     if($fase_correccion==0)
	     {
		$c80_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[80]),"1900-12-31");
		$c80_es_menor_o_igual_1845_01_01=$this->diferencia_dias_entre_fechas(trim($array_fields[80]),"1845-01-01");
		$c80_es_mayor_a_1845_01_01=$this->diferencia_dias_entre_fechas(trim($array_fields[80]),"1800-01-01");
		
		//campo en blanco
		if($campo_corregido=="")
		{
			if($campo_corregido==""
			   && $c80_es_mayor_a_1900_12_31<0
			   )
			{
			    $campo_corregido="22";
			}
			else if($campo_corregido==""
				&& (trim($array_fields[80])=="1805-01-01"
			       || trim($array_fields[80])=="1810-01-01"
			       || trim($array_fields[80])=="1825-01-01"
			       || trim($array_fields[80])=="1830-01-01"
			       || trim($array_fields[80])=="1835-01-01"
			       || trim($array_fields[80])=="1845-01-01"
			       )
				)
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido=="")
			{
			    $campo_corregido="0";
			}
		}//fin if
		
		//valor permitido
		if($campo_corregido!="0"
		   && $campo_corregido!="1"
		   && $campo_corregido!="2"
		   && $campo_corregido!="22"
		   )
		{
			if($campo_corregido!="0"
			   && $campo_corregido!="1"
			   && $campo_corregido!="2"
			   && $campo_corregido!="22"
			   && $c80_es_mayor_a_1900_12_31<0
			   )
			{
			    $campo_corregido="22";
			}
			else if($campo_corregido!="0"
			   && $campo_corregido!="1"
			   && $campo_corregido!="2"
			   && $campo_corregido!="22"
			   && (trim($array_fields[80])=="1805-01-01"
			       || trim($array_fields[80])=="1810-01-01"
			       || trim($array_fields[80])=="1825-01-01"
			       || trim($array_fields[80])=="1830-01-01"
			       || trim($array_fields[80])=="1835-01-01"
			       || trim($array_fields[80])=="1845-01-01"
			       )
			   )
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0"
			   && $campo_corregido!="1"
			   && $campo_corregido!="2"
			   && $campo_corregido!="22"
			   )
			{
			    $campo_corregido="0";
			}
		}//fin if
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_018($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
		$c80_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[80]),"1900-12-31");
		$c80_es_menor_o_igual_1845_01_01=$this->diferencia_dias_entre_fechas(trim($array_fields[80]),"1845-01-01");
		$c80_es_mayor_a_1845_01_01=$this->diferencia_dias_entre_fechas(trim($array_fields[80]),"1800-01-01");
		
		//calidad 81
		if($campo_corregido=="0"
		   && $c80_es_mayor_a_1900_12_31<0
		   )
		{
			//c1
		    $campo_corregido="22";
		}
		else if($campo_corregido!="0"
			&& (trim($array_fields[80])=="1805-01-01"
		       || trim($array_fields[80])=="1810-01-01"
		       || trim($array_fields[80])=="1825-01-01"
		       || trim($array_fields[80])=="1830-01-01"
		       || trim($array_fields[80])=="1835-01-01"
		       || trim($array_fields[80])=="1845-01-01"
		       )
			)
		{
			//c2
		    $campo_corregido="0";
		}
		
	     }//fin fase 1
	    }//c81
	    
	    //campo a corregir es 82
	    if($numero_campo_a_corregir==82)
	    {
	     
	     if($fase_correccion==0)
	     {
			//campo en blanco
	     	if($campo_corregido=="")
			{
				if($campo_corregido==""
					&& $array_fields[83]=="0"
					&& trim($array_fields[14])!="1"
					)
				{
				    $campo_corregido="1845-01-01";
				}
				else if($campo_corregido==""
				   && $array_fields[83]=="0"
				   && trim($array_fields[14])=="1"
				   )
				{
				    $campo_corregido="1835-01-01";
				}
				else if($campo_corregido==""
					&& $array_fields[83]=="22"
					&& trim($array_fields[14])=="1"
					)
				{
				    $campo_corregido="1835-01-01";
				}
				else if($campo_corregido==""
					&& $array_fields[83]=="22"
					&& trim($array_fields[14])!="1"
					)
				{
				    $campo_corregido="1845-01-01";
				}
				else if($campo_corregido==""
					&& ($array_fields[83]=="1" || $array_fields[83]=="2")
					)
				{
				    $campo_corregido="1800-01-01";
				}
				else if($campo_corregido=="")
				{
				    $campo_corregido="1845-01-01";
				}
			}//fin if
		
		//formato
		$corrige_formato=false;
		$array_fecha_campo_actual=explode("-", $campo_corregido);
		if(count($array_fecha_campo_actual)==3)
		{
			if(ctype_digit($array_fecha_campo_actual[0]) && ctype_digit($array_fecha_campo_actual[1]) && ctype_digit($array_fecha_campo_actual[2]))
			{
				//checkdate mm-dd-aaaa -> aaaa-mm-dd ?
				if(checkdate($array_fecha_campo_actual[1],$array_fecha_campo_actual[2],$array_fecha_campo_actual[0])
			       && intval($array_fecha_campo_actual[0])>=32)
			    {
			    	//no se corrige formato
			    }
			    else
			    {
			    	$corrige_formato=true;
			    }

			}
			else
			{
				$corrige_formato=true;
			}
		}
		else
		{
			$corrige_formato=true;
		}//fin else

		if($corrige_formato==true)
		{
		$campo_corregido=$this->corrector_formato_fecha($campo_corregido,false,-2);
		}
		
		//valor permitido
		
		$excede_fecha_corte=$this->diferencia_dias_entre_fechas($campo_corregido,$date_de_corte);
		$es_menor_o_igual_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
		
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");

		//comparacion fecha campo actual es inferior a la fecha de nacimiento
			$fecha_nacimiento_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),"1900-12-31");
			$campo_actual_inferior_fecha_nacimiento=$this->diferencia_dias_entre_fechas($campo_corregido,trim($array_fields[9]));
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_nacimiento_es_mayor_a_1900_12_31<0
			&& $fecha_nacimiento_es_mayor_a_1900_12_31!==false
			&& $campo_actual_inferior_fecha_nacimiento>0
			&& $campo_actual_inferior_fecha_nacimiento!==false
			)
			{
				$campo_corregido="1800-01-01";
			}//fin if
			//fin comparacion fecha campo actual es inferior a la fecha de nacimiento

			//limite inferior fecha corte 2 years
			$fecha_corte_menos_2_years="";
			$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=false;
			$array_fecha_corte=explode("-", $date_de_corte);
			if(count($array_fecha_corte)==3)
			{
				$fecha_corte_menos_2_years=(intval($array_fecha_corte[0])-2)."-".$array_fecha_corte[1]."-".$array_fecha_corte[2];
				$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=$this->diferencia_dias_entre_fechas($campo_corregido,$fecha_corte_menos_2_years);
			}//fin if
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_corte_menos_2_years!=""
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years!==false
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years>0
			)
			{
				$campo_corregido="1845-01-01";
			}//fin if
			//fin limite inferior fecha corte 2 years

		if($comparacion_a_1900_12_31<0 
					&& $excede_fecha_corte<0
					&& $comparacion_a_1900_12_31!==false
					&& $excede_fecha_corte!==false
					)
				{
					$campo_corregido="1800-01-01";
				}//fin comparacion excede la fecha de corte

		if($comparacion_a_1900_12_31>=0
			&& $campo_corregido!="1800-01-01"
			&& $campo_corregido!="1805-01-01"
			&& $campo_corregido!="1810-01-01"
			&& $campo_corregido!="1825-01-01"
			&& $campo_corregido!="1830-01-01"
			&& $campo_corregido!="1835-01-01"
			&& $campo_corregido!="1845-01-01"
			)
		{
			if($comparacion_a_1900_12_31>=0
					&& $campo_corregido!="1800-01-01"
					&& $campo_corregido!="1805-01-01"
					&& $campo_corregido!="1810-01-01"
					&& $campo_corregido!="1825-01-01"
					&& $campo_corregido!="1830-01-01"
					&& $campo_corregido!="1835-01-01"
					&& $campo_corregido!="1845-01-01"
					&& $array_fields[83]=="0"
					&& trim($array_fields[14])!="1"
					)
				{
				    $campo_corregido="1845-01-01";
				}
				else if($comparacion_a_1900_12_31>=0
					&& $campo_corregido!="1800-01-01"
					&& $campo_corregido!="1805-01-01"
					&& $campo_corregido!="1810-01-01"
					&& $campo_corregido!="1825-01-01"
					&& $campo_corregido!="1830-01-01"
					&& $campo_corregido!="1835-01-01"
					&& $campo_corregido!="1845-01-01"
				   && $array_fields[83]=="0"
				   && trim($array_fields[14])=="1"
				   )
				{
				    $campo_corregido="1835-01-01";
				}
				else if($comparacion_a_1900_12_31>=0
					&& $campo_corregido!="1800-01-01"
					&& $campo_corregido!="1805-01-01"
					&& $campo_corregido!="1810-01-01"
					&& $campo_corregido!="1825-01-01"
					&& $campo_corregido!="1830-01-01"
					&& $campo_corregido!="1835-01-01"
					&& $campo_corregido!="1845-01-01"
					&& $array_fields[83]=="22"
					&& trim($array_fields[14])=="1"
					)
				{
				    $campo_corregido="1835-01-01";
				}
				else if($comparacion_a_1900_12_31>=0
					&& $campo_corregido!="1800-01-01"
					&& $campo_corregido!="1805-01-01"
					&& $campo_corregido!="1810-01-01"
					&& $campo_corregido!="1825-01-01"
					&& $campo_corregido!="1830-01-01"
					&& $campo_corregido!="1835-01-01"
					&& $campo_corregido!="1845-01-01"
					&& $array_fields[83]=="22"
					&& trim($array_fields[14])!="1"
					)
				{
				    $campo_corregido="1845-01-01";
				}
				else if($comparacion_a_1900_12_31>=0
					&& $campo_corregido!="1800-01-01"
					&& $campo_corregido!="1805-01-01"
					&& $campo_corregido!="1810-01-01"
					&& $campo_corregido!="1825-01-01"
					&& $campo_corregido!="1830-01-01"
					&& $campo_corregido!="1835-01-01"
					&& $campo_corregido!="1845-01-01"
					&& ($array_fields[83]=="1" || $array_fields[83]=="2")
					)
				{
				    $campo_corregido="1800-01-01";
				}
				else if($comparacion_a_1900_12_31>=0
					&& $campo_corregido!="1800-01-01"
					&& $campo_corregido!="1805-01-01"
					&& $campo_corregido!="1810-01-01"
					&& $campo_corregido!="1825-01-01"
					&& $campo_corregido!="1830-01-01"
					&& $campo_corregido!="1835-01-01"
					&& $campo_corregido!="1845-01-01"
					)
				{
				    $campo_corregido="1845-01-01";
				}
		}//fin else if
	     
		//$campo_corregido=$this->corrector_valor_permitido_fecha_criterio_002($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
			//calidad
			$es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
			$es_menor_solo_al_campo75=$this->diferencia_dias_entre_fechas($campo_corregido,trim($array_fields[75]));
			$es_mayor_al_campo76=$this->diferencia_dias_entre_fechas($campo_corregido,trim($array_fields[76]));
			$es_mayor_75_a_1900_12_31=$this->diferencia_dias_entre_fechas($array_fields[75],"1900-12-31");
			$es_mayor_76_a_1900_12_31=$this->diferencia_dias_entre_fechas($array_fields[76],"1900-12-31");
			
			if($campo_corregido!="1845-01-01"
				&& $array_fields[83]=="0"
				&& trim($array_fields[14])!="1"
				)
			{
			    $campo_corregido="1845-01-01";
			}
			else if($campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $array_fields[83]=="0"
				&& trim($array_fields[14])=="1"
				)
			{
			    $campo_corregido="1835-01-01";
			}
			else if(
				($campo_corregido=="1800-01-01")
			   && $array_fields[83]=="22"
			   && trim($array_fields[14])!="1"
			   )
			{
			    $campo_corregido="1845-01-01";
			}
			else if(
				($campo_corregido=="1800-01-01")
			   && $array_fields[83]=="22"
			   && trim($array_fields[14])=="1"
			   )
			{
			    $campo_corregido="1835-01-01";
			}			
			else if(($campo_corregido=="1805-01-01"
			    || $campo_corregido=="1810-01-01"
			    || $campo_corregido=="1825-01-01"
			    || $campo_corregido=="1830-01-01"
			    || $campo_corregido=="1835-01-01"
			    || $campo_corregido=="1845-01-01"
			    )
			   && ($array_fields[83]=="1" || $array_fields[83]=="2")
			   )
			{
			    $campo_corregido="1800-01-01";
			}
			else if(
				$es_mayor_a_1900_12_31<0
				&& $es_mayor_75_a_1900_12_31<0
				&& $es_menor_solo_al_campo75>0
				&& $es_menor_solo_al_campo75!==false
				)
			{
				$array_c75=explode("-",$array_fields[75]);
			    if(count($array_c75)==3 && checkdate($array_c75[1],$array_c75[2],$array_c75[0]) )
			    {
			    	$fecha = date_create(trim($array_fields[75]));
					//date sub resta, por lo cual al poner un dia negativo suma
				    date_sub($fecha, date_interval_create_from_date_string('-1 days'));
				    $c75_plus_1="".date_format($fecha, 'Y-m-d');
				    $campo_corregido=$c75_plus_1;
			    }//fin if verifica que sea una fecha

			    
			    $comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
			    $excede_fecha_corte=$this->diferencia_dias_entre_fechas($campo_corregido,$date_de_corte);
			    if($comparacion_a_1900_12_31<0 
			    	&& $comparacion_a_1900_12_31!==false
			    	&& $excede_fecha_corte<0
			    	&& $excede_fecha_corte!==false
			    	)
			    {
					$fecha = date_create(trim($array_fields[75]));
					$c75="".date_format($fecha, 'Y-m-d');
				    $campo_corregido=$c75;
			    }//fin if
			    
			    
			}//fin calidad
			else if(
				$es_mayor_a_1900_12_31<0
				&& $es_mayor_76_a_1900_12_31<0
				&& $es_mayor_al_campo76<0
				&& $es_mayor_al_campo76!==false
				)
			{
				$array_c76=explode("-",$array_fields[76]);
			    if(count($array_c76)==3 && checkdate($array_c76[1],$array_c76[2],$array_c76[0]) )
			    {
			    	$fecha = date_create(trim($array_fields[76]));
					//date sub resta, por lo cual al poner un dia negativo suma
				    date_sub($fecha, date_interval_create_from_date_string('1 days'));
				    $c76_minus_1="".date_format($fecha, 'Y-m-d');
				    $campo_corregido=$c76_minus_1;
			    }//fin if verifica que sea una fecha

			}//fin calidad
		
		
	     }//fin fase 1
	    }//fin campo 82
	    
	    //campo a corregir es 83
	    if($numero_campo_a_corregir==83)
	    {
	     if($fase_correccion==0)
	     {
		$c82_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[82]),"1900-12-31");
		$c82_es_menor_o_igual_1845_01_01=$this->diferencia_dias_entre_fechas(trim($array_fields[82]),"1845-01-01");
		$c82_es_mayor_a_1800_01_01=$this->diferencia_dias_entre_fechas(trim($array_fields[82]),"1800-01-01");
		
		//campo en blanco
		if($campo_corregido=="")
		{
			if($campo_corregido==""
			   && ($c82_es_mayor_a_1900_12_31<0 || trim($array_fields[82])=="1800-01-01")
			   )
			{
			    $campo_corregido="22";
			}
			else if($campo_corregido==""
				&&  (trim($array_fields[82])=="1805-01-01"
				    || trim($array_fields[82])=="1810-01-01"
				    || trim($array_fields[82])=="1825-01-01"
				    || trim($array_fields[82])=="1830-01-01"
				    || trim($array_fields[82])=="1835-01-01"
				    || trim($array_fields[82])=="1845-01-01"
				    )
				)
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido==""
			      &&  trim($array_fields[82])==""		       
			    )
			{
			    $campo_corregido="0";
			}
		}//fin if
		
		//valor permitido
		 if($campo_corregido!="0"
		   && $campo_corregido!="1"
		   && $campo_corregido!="2"
		   && $campo_corregido!="22"
		   )
		{
			if($campo_corregido!="0"
			   && $campo_corregido!="1"
			   && $campo_corregido!="2"
			   && $campo_corregido!="22"
			   && ($c82_es_mayor_a_1900_12_31<0 || trim($array_fields[82])=="1800-01-01")
			   )
			{
			    $campo_corregido="22";
			}
			else if($campo_corregido!="0"
			   && $campo_corregido!="1"
			   && $campo_corregido!="2"
			   && $campo_corregido!="22"
				&&  (trim($array_fields[82])=="1805-01-01"
				    || trim($array_fields[82])=="1810-01-01"
				    || trim($array_fields[82])=="1825-01-01"
				    || trim($array_fields[82])=="1830-01-01"
				    || trim($array_fields[82])=="1835-01-01"
				    || trim($array_fields[82])=="1845-01-01"
				    )
				)
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0"
			   && $campo_corregido!="1"
			   && $campo_corregido!="2"
			   && $campo_corregido!="22"
			   )
			{
			    $campo_corregido="0";
			}
		}//fin else if
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_018($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
		$c82_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[82]),"1900-12-31");
		$c82_es_menor_o_igual_1845_01_01=$this->diferencia_dias_entre_fechas(trim($array_fields[82]),"1845-01-01");
		$c82_es_mayor_a_1800_01_01=$this->diferencia_dias_entre_fechas(trim($array_fields[82]),"1800-01-01");
		
		if($campo_corregido=="0"		   
		   && ($c82_es_mayor_a_1900_12_31<0 || trim($array_fields[82])=="1800-01-01")
		   )
		{
		    $campo_corregido="22";
		}
		else if($campo_corregido!="0"
		      &&  (trim($array_fields[82])=="1805-01-01"
		       || trim($array_fields[82])=="1810-01-01"
		       || trim($array_fields[82])=="1825-01-01"
		       || trim($array_fields[82])=="1830-01-01"
		       || trim($array_fields[82])=="1835-01-01"
		       || trim($array_fields[82])=="1845-01-01"
		       )
			)
		{
		    $campo_corregido="0";
		}
		
	     }//fin fase 1
	    }//fin campo 83
	    
	    //campo a corregir es 115
	    if($numero_campo_a_corregir==115)
	    {
	     if($fase_correccion==0)
	     {
			//campo en blanco
			if($campo_corregido=="")
			{
		     	
				if( $campo_corregido==""
				   && $array_fields[10]=="M"
				)
				{
					//ct1
				    $campo_corregido="0";
				}
				else if($campo_corregido==""
					&& $array_fields[10]=="F"
					&& ($edad<10 || $edad>=60 ) 
					)
				{
					//ct2
				    $campo_corregido="0";
				}
				else if( $campo_corregido==""
					&& $array_fields[10]=="F"
					&& ($edad>=10 && $edad<60 )
		     		&& $array_fields[49]!="1845-01-01"
				)
				{
					//ct3
				    $campo_corregido="0";
				}
				else if( $campo_corregido==""
					&& $array_fields[10]=="F"
					&& ($edad>=10 && $edad<60 )
		     		&& $array_fields[49]=="1845-01-01"
		     		&& $array_fields[14]!="1"
				)
				{
					//ct4
				    $campo_corregido="0";
				}
				else if( $campo_corregido==""
					&& $array_fields[10]=="F"
					&& ($edad>=10 && $edad<60 )
		     		&& $array_fields[14]=="1"
		     		&& $array_fields[15]!="1"
		     		&& $array_fields[49]=="1845-01-01"
				)
				{
					//ct5
				    $campo_corregido="0";
				}
				else if( $campo_corregido==""
					&& $array_fields[10]=="F"
					&& ($edad>=10 && $edad<60 )
		     		&& $array_fields[14]=="1"
		     		&& $array_fields[15]=="1"
		     		&& $array_fields[49]=="1845-01-01"
				)
				{
					//ct6
				    $campo_corregido="22";
				}				
				else if($campo_corregido=="")
				{
					//ct7
				    $campo_corregido="0";
				}
			}//fin if
		
			//valor permitido
			if($campo_corregido!="0"
			    && $campo_corregido!="1"
			    && $campo_corregido!="2"
			    && $campo_corregido!="16"
			    && $campo_corregido!="17"
			    && $campo_corregido!="18"
			    && $campo_corregido!="19"
			    && $campo_corregido!="20"
			    && $campo_corregido!="22"
			    )
			{

		     	if( $campo_corregido!="0"
				    && $campo_corregido!="1"
				    && $campo_corregido!="2"
				    && $campo_corregido!="16"
				    && $campo_corregido!="17"
				    && $campo_corregido!="18"
				    && $campo_corregido!="19"
				    && $campo_corregido!="20"
				    && $campo_corregido!="22"
				   && $array_fields[10]=="M"
				)
				{
					//ct1
				    $campo_corregido="0";
				}
				else if($campo_corregido!="0"
				    && $campo_corregido!="1"
				    && $campo_corregido!="2"
				    && $campo_corregido!="16"
				    && $campo_corregido!="17"
				    && $campo_corregido!="18"
				    && $campo_corregido!="19"
				    && $campo_corregido!="20"
				    && $campo_corregido!="22"
					&& $array_fields[10]=="F"
					&& ($edad<10 || $edad>=60 ) 
					)
				{
					//ct2
				    $campo_corregido="0";
				}
				else if( $campo_corregido!="0"
				    && $campo_corregido!="1"
				    && $campo_corregido!="2"
				    && $campo_corregido!="16"
				    && $campo_corregido!="17"
				    && $campo_corregido!="18"
				    && $campo_corregido!="19"
				    && $campo_corregido!="20"
				    && $campo_corregido!="22"
					&& $array_fields[10]=="F"
					&& ($edad>=10 && $edad<60 )
		     		&& $array_fields[49]!="1845-01-01"
				)
				{
					//ct3
				    $campo_corregido="0";
				}
				else if( $campo_corregido!="0"
				    && $campo_corregido!="1"
				    && $campo_corregido!="2"
				    && $campo_corregido!="16"
				    && $campo_corregido!="17"
				    && $campo_corregido!="18"
				    && $campo_corregido!="19"
				    && $campo_corregido!="20"
				    && $campo_corregido!="22"
					&& $array_fields[10]=="F"
					&& ($edad>=10 && $edad<60 )
		     		&& $array_fields[49]=="1845-01-01"
		     		&& $array_fields[14]!="1"
				)
				{
					//ct4
				    $campo_corregido="0";
				}
				else if( $campo_corregido!="0"
				    && $campo_corregido!="1"
				    && $campo_corregido!="2"
				    && $campo_corregido!="16"
				    && $campo_corregido!="17"
				    && $campo_corregido!="18"
				    && $campo_corregido!="19"
				    && $campo_corregido!="20"
				    && $campo_corregido!="22"
					&& $array_fields[10]=="F"
					&& ($edad>=10 && $edad<60 )
		     		&& $array_fields[14]=="1"
		     		&& $array_fields[15]!="1"
		     		&& $array_fields[49]=="1845-01-01"
				)
				{
					//ct5
				    $campo_corregido="0";
				}
				else if( $campo_corregido!="0"
				    && $campo_corregido!="1"
				    && $campo_corregido!="2"
				    && $campo_corregido!="16"
				    && $campo_corregido!="17"
				    && $campo_corregido!="18"
				    && $campo_corregido!="19"
				    && $campo_corregido!="20"
				    && $campo_corregido!="22"
					&& $array_fields[10]=="F"
					&& ($edad>=10 && $edad<60 )
		     		&& $array_fields[14]=="1"
		     		&& $array_fields[15]=="1"
		     		&& $array_fields[49]=="1845-01-01"
				)
				{
					//ct6
				    $campo_corregido="22";
				}	
				else if($campo_corregido!="0"
				    && $campo_corregido!="1"
				    && $campo_corregido!="2"
				    && $campo_corregido!="16"
				    && $campo_corregido!="17"
				    && $campo_corregido!="18"
				    && $campo_corregido!="19"
				    && $campo_corregido!="20"
				    && $campo_corregido!="22"
				    )
				{
					//c7
				    $campo_corregido="0";
				}

			}//fin else if
		
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_017($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
				//calidad
	     		if( $campo_corregido!="0"
				   && $array_fields[10]=="M"
				)
				{
					//ct1
				    $campo_corregido="0";
				}
				else if($campo_corregido!="0"
					&& $array_fields[10]=="F"
					&& ($edad<10 || $edad>=60 ) 
					)
				{
					//ct2
				    $campo_corregido="0";
				}
				else if( $campo_corregido!="0"
					&& $array_fields[10]=="F"
					&& ($edad>=10 && $edad<60 )
		     		&& $array_fields[49]!="1845-01-01"
				)
				{
					//ct3
				    $campo_corregido="0";
				}
				else if( $campo_corregido!="0"
					&& $array_fields[10]=="F"
					&& ($edad>=10 && $edad<60 )
		     		&& $array_fields[49]=="1845-01-01"
		     		&& $array_fields[14]!="1"
				)
				{
					//ct4
				    $campo_corregido="0";
				}
				else if( $campo_corregido!="0"
					&& $array_fields[10]=="F"
					&& ($edad>=10 && $edad<60 )
		     		&& $array_fields[14]=="1"
		     		&& $array_fields[15]!="1"
		     		&& $array_fields[49]=="1845-01-01"
				)
				{
					//ct5
				    $campo_corregido="0";
				}
				else if( $campo_corregido=="0"
					&& $array_fields[10]=="F"
					&& ($edad>=10 && $edad<60 )
		     		&& $array_fields[14]=="1"
		     		&& $array_fields[15]=="1"
		     		&& $array_fields[49]=="1845-01-01"
				)
				{
					//ct6
				    $campo_corregido="22";
				}//fin calidad	

				

	     }//fin fase 1
	    }//fin c115
	    
	    //campo a corregir es 116
	    if($numero_campo_a_corregir==116)
	    {
	     if($fase_correccion==0)
	     {
		//campo en blanco

	     	if($campo_corregido=="")
			{
				if($campo_corregido==""
					&& $edad_dias<90
				   && $array_fields[15]=="2"
				   )
				{
				    $campo_corregido="22";
				}
				else if($campo_corregido==""
					&& $edad_dias>=90
				   )
				{
				    $campo_corregido="0";
				}
				else if($campo_corregido==""
					&& $edad_dias<90
					&& $array_fields[15]!="2"
					)
				{
				    $campo_corregido="0";
				}
				else if($campo_corregido=="")
				{
				    $campo_corregido="0";
				}
			}//fin if
		
		//valor permitido
			if( $campo_corregido!="0"
		    && $campo_corregido!="1"
		    && $campo_corregido!="2"
		    && $campo_corregido!="16"
		    && $campo_corregido!="17"
		    && $campo_corregido!="18"
		    && $campo_corregido!="19"
		    && $campo_corregido!="20"
		    && $campo_corregido!="22"
		    )
		{

			if($campo_corregido!="0"
			    && $campo_corregido!="1"
			    && $campo_corregido!="2"
			    && $campo_corregido!="16"
			    && $campo_corregido!="17"
			    && $campo_corregido!="18"
			    && $campo_corregido!="19"
			    && $campo_corregido!="20"
			    && $campo_corregido!="22"
				&& $edad_dias<90
			   && $array_fields[15]=="2"
			   )
			{
			    $campo_corregido="22";
			}
			else if($campo_corregido!="0"
			    && $campo_corregido!="1"
			    && $campo_corregido!="2"
			    && $campo_corregido!="16"
			    && $campo_corregido!="17"
			    && $campo_corregido!="18"
			    && $campo_corregido!="19"
			    && $campo_corregido!="20"
			    && $campo_corregido!="22"
				&& $edad_dias>=90
			   )
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0"
			    && $campo_corregido!="1"
			    && $campo_corregido!="2"
			    && $campo_corregido!="16"
			    && $campo_corregido!="17"
			    && $campo_corregido!="18"
			    && $campo_corregido!="19"
			    && $campo_corregido!="20"
			    && $campo_corregido!="22"
				&& $edad_dias<90
				&& $array_fields[15]!="2"
				)
			{
			    $campo_corregido="0";
			}
			else if( $campo_corregido!="0"
			    && $campo_corregido!="1"
			    && $campo_corregido!="2"
			    && $campo_corregido!="16"
			    && $campo_corregido!="17"
			    && $campo_corregido!="18"
			    && $campo_corregido!="19"
			    && $campo_corregido!="20"
			    && $campo_corregido!="22"
			    )
			{
			    $campo_corregido="0";
			}
		}//fin if
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_017($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
			//calidad
			if($campo_corregido=="0"
				&& $edad_dias<90
			   && $array_fields[15]=="2"
			   )
			{
			    $campo_corregido="22";
			}
			else if($campo_corregido!="0"
				&& $edad_dias>=90
			   )
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0"
				&& $edad_dias<90
				&& $array_fields[15]!="2"
				)
			{
			    $campo_corregido="0";
			}//fin calidad
	     }//fin fase 1
	    }//fin c116
	    
	    //FIN CAMPOS GRUPO ITS
	    
	    //CAMPOS OTROS RIESGOS
	    //campo a corregir es 18
	    if($numero_campo_a_corregir==18)
	    {
	     if($fase_correccion==0)
	     {
		//campo en blanco
     	if($campo_corregido=="")
		{
			if($campo_corregido==""
			   && $array_fields[113]=="4")
			{
			    $campo_corregido="21";
			}
			else if($campo_corregido==""
				&& $array_fields[113]!="4"
				)
			{
			    $campo_corregido="1";
			}
			else if($campo_corregido=="")
			{
			    $campo_corregido="21";
			}
		}//fin if
		
		//valor permitido
		if($campo_corregido!="1"
			&& $campo_corregido!="2"
			&& $campo_corregido!="21"		   
		   )
		{
			if($campo_corregido!="1"
			    && $campo_corregido!="2"
			    && $campo_corregido!="21"
			   && $array_fields[113]=="4"		   
			   )
			{
			    $campo_corregido="21";
			}
			else if($campo_corregido!="1"
				&& $campo_corregido!="2"
				&& $campo_corregido!="21"
				&& $array_fields[113]!="4"
			   )
			{
			    $campo_corregido="1";
			}
			else if($campo_corregido!="1"
				&& $campo_corregido!="2"
				&& $campo_corregido!="21"		   
			   )
			{
			    $campo_corregido="21";
			}
		}//fin if
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_009($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
			//calidad c18
			if($campo_corregido!="21"
				&& $array_fields[113]=="4"
				)
			{
			    $campo_corregido="21";
			}
			else if($campo_corregido=="21"
			   && ($array_fields[113]!="4")
			   )
			{
			    $campo_corregido="1";
			}
			 
		
	     }//fin fase
	    }//revisado c18
	    
	    //campo a corregir es 19
	    if($numero_campo_a_corregir==19)
	    {
	     if($fase_correccion==0)
	     {
		//campo en blanco
		if($campo_corregido=="")
		{
		    $campo_corregido="21";
		}
		
		//valor permitido
		if($campo_corregido!="0"
		   && $campo_corregido!="1"
		   && $campo_corregido!="2"
		   && $campo_corregido!="21")
		{
		    $campo_corregido="21";
		}
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_007($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
		//no  hay de calidad
	     }//fin fase
	    }
	    
	    //campo a corregir es 25
	    if($numero_campo_a_corregir==25)
	    {
	     if($fase_correccion==0)
	     {
		//campo en blanco
		if($campo_corregido=="")
		{
		    $campo_corregido="21";
		}
		
		//valor permitido
		if($campo_corregido!="1"
		   && $campo_corregido!="2"
		   && $campo_corregido!="3"
		   && $campo_corregido!="4"
		   && $campo_corregido!="5"
		   && $campo_corregido!="6"
		   && $campo_corregido!="7"
		   && $campo_corregido!="21"
		)
		{
		    $campo_corregido="21";
		}
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_011_a($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
		//no hay de calidad
	     }//fin fase 1
	    }
	    
	    //campo a corregir es 77
	    if($numero_campo_a_corregir==77)
	    {
	     if($fase_correccion==0)
	     {
		//campo en blanco
	     	if($campo_corregido=="")
			{
				if($campo_corregido==""
				   && ($array_fields[25]=="7" || $array_fields[25]=="21")
				   )
				{
				    $campo_corregido="0";
				}
				else if($campo_corregido==""
				   && ($array_fields[25]=="1"
				       || $array_fields[25]=="2"
				       || $array_fields[25]=="3"
				       || $array_fields[25]=="4"
				       || $array_fields[25]=="5"
				       || $array_fields[25]=="6"
				       )
				   )
				{
				    $campo_corregido="22";
				}
				else if($campo_corregido=="")
				{
				    $campo_corregido="0";
				}
			}//fin if
		
		//valor permitido
		if($campo_corregido!="0"
		   && $campo_corregido!="1"
		    && $campo_corregido!="2"
		    && $campo_corregido!="16"
		    && $campo_corregido!="17"
		    && $campo_corregido!="18"
		    && $campo_corregido!="19"
		    && $campo_corregido!="20"
		    && $campo_corregido!="22"
		)
		{
			if($campo_corregido!="0"
			   && $campo_corregido!="1"
			    && $campo_corregido!="2"
			    && $campo_corregido!="16"
			    && $campo_corregido!="17"
			    && $campo_corregido!="18"
			    && $campo_corregido!="19"
			    && $campo_corregido!="20"
			    && $campo_corregido!="22"
			    && ($array_fields[25]=="7" || $array_fields[25]=="21")
			)
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0"
			   && $campo_corregido!="1"
			    && $campo_corregido!="2"
			    && $campo_corregido!="16"
			    && $campo_corregido!="17"
			    && $campo_corregido!="18"
			    && $campo_corregido!="19"
			    && $campo_corregido!="20"
			    && $campo_corregido!="22"
			    && ($array_fields[25]=="1"
			       || $array_fields[25]=="2"
			       || $array_fields[25]=="3"
			       || $array_fields[25]=="4"
			       || $array_fields[25]=="5"
			       || $array_fields[25]=="6"
			       )
			)
			{
			    $campo_corregido="22";
			}
			else if($campo_corregido!="0"
			   && $campo_corregido!="1"
			    && $campo_corregido!="2"
			    && $campo_corregido!="16"
			    && $campo_corregido!="17"
			    && $campo_corregido!="18"
			    && $campo_corregido!="19"
			    && $campo_corregido!="20"
			    && $campo_corregido!="22"
			)
			{
			    $campo_corregido="0";
			}
		}//fin if
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_017($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
		//validacion de calidad
		if($campo_corregido=="0"
		   && ($array_fields[25]!="7" && $array_fields[25]!="21")
		)
		{
		    $campo_corregido="22";
		}
		else if($campo_corregido!="0"
		   && ($array_fields[25]=="7" || $array_fields[25]=="21")
		)
		{
		    $campo_corregido="0";
		}
	     }//fin fase
	    }
	    
	    //campo a corregir es 28
	    if($numero_campo_a_corregir==28)
	    {
	     if($fase_correccion==0)
	     {
		//en blanco
		if($campo_corregido=="")
		{
		    $campo_corregido="21";
		}
		
		//valor permititdo
		if($campo_corregido!="1"
		   && $campo_corregido!="2"
		   && $campo_corregido!="21"
		)
		{
		    $campo_corregido="21";
		}
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_009($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
		//no hay de calidad
	     }//fin fase
	    }
	    
	    //campo a corregir es 20
	    if($numero_campo_a_corregir==20)
	    {
	     if($fase_correccion==0)
	     {
		//campo en blanco
	     	if($campo_corregido=="")
	     	{
				if($campo_corregido==""
					&& ($array_fields[117]!="1" && $array_fields[117]!="2")
					)
				{
				    $campo_corregido="21";
				}
				else if($campo_corregido==""
					&& ($array_fields[117]=="1" || $array_fields[117]=="2")
					)
				{
				    $campo_corregido="1";
				}
				else if($campo_corregido=="" )
				{
				    $campo_corregido="21";
				}
			}//fin if
		
		//valor permitido
		if($campo_corregido!="1"
		   && $campo_corregido!="2"
		   && $campo_corregido!="3"
		   && $campo_corregido!="21"
		   )
		{
			if($campo_corregido!="1"
			   	&& $campo_corregido!="2"
			   	&& $campo_corregido!="3"
			   	&& $campo_corregido!="21"
				&& ($array_fields[117]!="1" && $array_fields[117]!="2")
			)
			{
			    $campo_corregido="21";
			}//fin else if
			else if($campo_corregido!="1"
			   	&& $campo_corregido!="2"
			   	&& $campo_corregido!="3"
			   	&& $campo_corregido!="21"
				&& ($array_fields[117]=="1" || $array_fields[117]=="2")
				)
			{
			    $campo_corregido="1";
			}//fin else if
			else if($campo_corregido!="1"
			   && $campo_corregido!="2"
			   && $campo_corregido!="3"
			   && $campo_corregido!="21"
			   )
			{
			    $campo_corregido="21";
			}//fin else if
		}//fin if
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_010($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
			//calidad c20
			if($campo_corregido!="1"
			   && $campo_corregido!="2"
			   && $array_fields[117]!="0")
			{
			    $campo_corregido="1";
			}
			else if(
				($campo_corregido=="1"
			   || $campo_corregido=="2"
			   || $campo_corregido=="3"
			   )
			   && $array_fields[117]=="0"
			   )
			{
			    $campo_corregido="21";
			}
	     }//fin fase
	    }//fin campo 20
	    
	    //campo a corregir es 117
	    if($numero_campo_a_corregir==117)
	    {
	     if($fase_correccion==0)
	     {
		//campo en blanco

	     	if($campo_corregido=="")
	     	{
				if($campo_corregido==""
				   && ($array_fields[20]=="1" || $array_fields[20]=="2")
				   )
				{
				    $campo_corregido="22";
				}
				else if($campo_corregido==""
				   && ($array_fields[20]=="3" || $array_fields[20]=="21")
				   )
				{
				    $campo_corregido="0";
				}
				else if($campo_corregido=="")
				{
				    $campo_corregido="0";
				}
			}//fin if
		
		
		//valor permitido
		if($campo_corregido!="0"
		&& $campo_corregido!="1"
		&& $campo_corregido!="2"
		&& $campo_corregido!="16"
		&& $campo_corregido!="17"
		&& $campo_corregido!="18"
		&& $campo_corregido!="19"
		&& $campo_corregido!="20"
		&& $campo_corregido!="22"
		)
		{

			if($campo_corregido!="0"
			&& $campo_corregido!="1"
			&& $campo_corregido!="2"
			&& $campo_corregido!="16"
			&& $campo_corregido!="17"
			&& $campo_corregido!="18"
			&& $campo_corregido!="19"
			&& $campo_corregido!="20"
			&& $campo_corregido!="22"
			
			 && ($array_fields[20]=="1" || $array_fields[20]=="2")
			)
			{
			    $campo_corregido="22";
			}
			else if($campo_corregido!="0"
			&& $campo_corregido!="1"
			&& $campo_corregido!="2"
			&& $campo_corregido!="16"
			&& $campo_corregido!="17"
			&& $campo_corregido!="18"
			&& $campo_corregido!="19"
			&& $campo_corregido!="20"
			&& $campo_corregido!="22"
			
			 && ($array_fields[20]=="3" || $array_fields[20]=="21")
			)
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0"
			&& $campo_corregido!="1"
			&& $campo_corregido!="2"
			&& $campo_corregido!="16"
			&& $campo_corregido!="17"
			&& $campo_corregido!="18"
			&& $campo_corregido!="19"
			&& $campo_corregido!="20"
			&& $campo_corregido!="22"
			)
			{
			    $campo_corregido="0";
			}

		}//fin if
		
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_017($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
			//calidad
			if($campo_corregido=="0"
			   && ($array_fields[20]=="1" || $array_fields[20]=="2")
			)
			{
			    $campo_corregido="22";
			}
			else if($campo_corregido!="0"
			   && ($array_fields[20]!="1" && $array_fields[20]!="2")
			)
			{
			    $campo_corregido="0";
			}
	     }//fin fase 1
	    }
	    
	    //campo a corregir es 112
	    if($numero_campo_a_corregir==112)
	    {
	     if($fase_correccion==0)
	     {

	     	
			//campo en blanco
			if($campo_corregido=="")
			{
				if($campo_corregido==""
				   && ($array_fields[113]=="4" || $array_fields[113]=="22")
				   && ($edad==50
					 || $edad==55
					 || $edad==60
					 || $edad==65
					 || $edad==70
					 || $edad==75
					 || $edad==80
					 || $edad==85
					 || $edad==90
					 || $edad==95
					 || $edad==100
					 || $edad==105
					 || $edad==110
					 || $edad==115
					 || $edad==120
					 )
				   )
				{
					//c1
				    $campo_corregido="1835-01-01";
				}
				else if($campo_corregido==""
				   && ($array_fields[113]=="4" || $array_fields[113]=="22")
				   && ($edad!=50
					 && $edad!=55
					 && $edad!=60
					 && $edad!=65
					 && $edad!=70
					 && $edad!=75
					 && $edad!=80
					 && $edad!=85
					 && $edad!=90
					 && $edad!=95
					 && $edad!=100
					 && $edad!=105
					 && $edad!=110
					 && $edad!=115
					 && $edad!=120
					 )
				   )
				{
					//c2
				    $campo_corregido="1845-01-01";
				}
				else if($campo_corregido==""
				   && ($array_fields[113]=="1" || $array_fields[113]=="2" || $array_fields[113]=="3")
				   )
				{
					//c3
				    $campo_corregido="1800-01-01";
				}
				else if($campo_corregido=="")
				{
					//c4
				    $campo_corregido="1845-01-01";
				}
			}//fin if
		
		
		
		//formato
		$corrige_formato=false;
		$array_fecha_campo_actual=explode("-", $campo_corregido);
		if(count($array_fecha_campo_actual)==3)
		{
			if(ctype_digit($array_fecha_campo_actual[0]) && ctype_digit($array_fecha_campo_actual[1]) && ctype_digit($array_fecha_campo_actual[2]))
			{
				//checkdate mm-dd-aaaa -> aaaa-mm-dd ?
				if(checkdate($array_fecha_campo_actual[1],$array_fecha_campo_actual[2],$array_fecha_campo_actual[0])
			       && intval($array_fecha_campo_actual[0])>=32)
			    {
			    	//no se corrige formato
			    }
			    else
			    {
			    	$corrige_formato=true;
			    }

			}
			else
			{
				$corrige_formato=true;
			}
		}
		else
		{
			$corrige_formato=true;
		}//fin else

		if($corrige_formato==true)
		{
		$campo_corregido=$this->corrector_formato_fecha($campo_corregido,false,-2);
		}
		
		$excede_fecha_corte=$this->diferencia_dias_entre_fechas($campo_corregido,$date_de_corte);
		$es_menor_o_igual_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
		
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");

		//comparacion fecha campo actual es inferior a la fecha de nacimiento
			$fecha_nacimiento_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),"1900-12-31");
			$campo_actual_inferior_fecha_nacimiento=$this->diferencia_dias_entre_fechas($campo_corregido,trim($array_fields[9]));
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_nacimiento_es_mayor_a_1900_12_31<0
			&& $fecha_nacimiento_es_mayor_a_1900_12_31!==false
			&& $campo_actual_inferior_fecha_nacimiento>0
			&& $campo_actual_inferior_fecha_nacimiento!==false
			)
			{
				$campo_corregido="1800-01-01";
			}//fin if
			//fin comparacion fecha campo actual es inferior a la fecha de nacimiento

			//limite inferior fecha corte 2 years
			$fecha_corte_menos_2_years="";
			$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=false;
			$array_fecha_corte=explode("-", $date_de_corte);
			if(count($array_fecha_corte)==3)
			{
				$fecha_corte_menos_2_years=(intval($array_fecha_corte[0])-2)."-".$array_fecha_corte[1]."-".$array_fecha_corte[2];
				$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=$this->diferencia_dias_entre_fechas($campo_corregido,$fecha_corte_menos_2_years);
			}//fin if
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_corte_menos_2_years!=""
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years!==false
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years>0
			)
			{
				$campo_corregido="1845-01-01";
			}//fin if
			//fin limite inferior fecha corte 2 years

		if($comparacion_a_1900_12_31<0 
					&& $excede_fecha_corte<0
					&& $comparacion_a_1900_12_31!==false
					&& $excede_fecha_corte!==false
					)
				{
					$campo_corregido="1800-01-01";
				}//fin comparacion excede la fecha de corte

		if($comparacion_a_1900_12_31>=0
			&& $campo_corregido!="1800-01-01"
			&& $campo_corregido!="1805-01-01"
			&& $campo_corregido!="1810-01-01"
			&& $campo_corregido!="1825-01-01"
			&& $campo_corregido!="1830-01-01"
			&& $campo_corregido!="1835-01-01"
			&& $campo_corregido!="1845-01-01"
			      
			)
		{
			if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& ($array_fields[113]=="4" || $array_fields[113]=="22")
				&& ($edad==50
					 || $edad==55
					 || $edad==60
					 || $edad==65
					 || $edad==70
					 || $edad==75
					 || $edad==80
					 || $edad==85
					 || $edad==90
					 || $edad==95
					 || $edad==100
					 || $edad==105
					 || $edad==110
					 || $edad==115
					 || $edad==120
					 )
				)
			{
				//c2
			    $campo_corregido="1835-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				   && ($array_fields[113]=="4" || $array_fields[113]=="22")
				   && ($edad!=50
					 && $edad!=55
					 && $edad!=60
					 && $edad!=65
					 && $edad!=70
					 && $edad!=75
					 && $edad!=80
					 && $edad!=85
					 && $edad!=90
					 && $edad!=95
					 && $edad!=100
					 && $edad!=105
					 && $edad!=110
					 && $edad!=115
					 && $edad!=120
					 )
				   )
				{
					//c2
				    $campo_corregido="1845-01-01";
				}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& ($array_fields[113]=="1" || $array_fields[113]=="2" || $array_fields[113]=="3")
				)
			{
				//c3
			    $campo_corregido="1800-01-01";
			}		
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				      
				)
			{
				//c5
			    $campo_corregido="1845-01-01";
			}
		}//fin else if
		
		//$campo_corregido=$this->corrector_valor_permitido_fecha_criterio_002($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {

	     	//calculo edad con fecha actividad campo relacionado
			$nueva_edad_year_relacionado=-1;
			$nueva_edad_meses_relacionado=-1;
			$nueva_edad_dias_relacionado=-1;
			    
			$numero_campo_relacionado=112;
			$array_fecha_nacimiento=explode("-",trim($array_fields[9]) );
			$fecha_campo_relacionado=explode("-",trim($array_fields[$numero_campo_relacionado]) );
			if(count($fecha_campo_relacionado)==3 && checkdate($fecha_campo_relacionado[1],$fecha_campo_relacionado[2],$fecha_campo_relacionado[0]))
			{
			    
			    $array_calc_edad_relacionado=$this->edad_years_months_days($array_fecha_nacimiento[2]."-".$array_fecha_nacimiento[1]."-".$array_fecha_nacimiento[0],$fecha_campo_relacionado[2]."-".$fecha_campo_relacionado[1]."-".$fecha_campo_relacionado[0]);
			    $nueva_edad_year_relacionado=intval($array_calc_edad_relacionado['y']);
			    $nueva_edad_meses_relacionado=(intval($array_calc_edad_relacionado['y'])*12)+$array_calc_edad_relacionado['m'];
			    $nueva_edad_dias_relacionado=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),trim($array_fields[$numero_campo_relacionado]));

		    }//fin if 
		    //fin calculo edad fecha con actividad campo relacionado
		    
		    $fecha_calendario_c_relacionado=$this->diferencia_dias_entre_fechas(trim($array_fields[$numero_campo_relacionado]),"1900-12-31");
		    
		//calidad
		$es_fecha_calendario=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
		
		if( 

			(
				$campo_corregido!="1845-01-01"
		    )
		    && ($array_fields[113]=="4")
		    && ($edad!=50
		       && $edad!=55
		       && $edad!=60
		       && $edad!=65
		       && $edad!=70
		       && $edad!=75
		       && $edad!=80
		       && $edad!=85
		       && $edad!=90
		       && $edad!=95
		       && $edad!=100
		       && $edad!=105
		       && $edad!=110
		       && $edad!=115
		       && $edad!=120
		       )      
		)
		{
			//c3
		    $campo_corregido="1845-01-01";
		}
		else if( 
			(
		     $campo_corregido!="1835-01-01"
		    && $campo_corregido!="1830-01-01"
		    && $campo_corregido!="1825-01-01"
		    && $campo_corregido!="1810-01-01"
		    && $campo_corregido!="1805-01-01"
			)
		    && ($array_fields[113]=="4")
		    && ($edad==50
		       || $edad==55
		       || $edad==60
		       || $edad==65
		       || $edad==70
		       || $edad==75
		       || $edad==80
		       || $edad==85
		       || $edad==90
		       || $edad==95
		       || $edad==100
		       || $edad==105
		       || $edad==110
		       || $edad==115
		       || $edad==120
		       ) 
		    
		)
		{
			//c2
		    $campo_corregido="1835-01-01";
		}	
		else if( ($campo_corregido=="1845-01-01"
		    || $campo_corregido=="1835-01-01"
		    || $campo_corregido=="1830-01-01"
		    || $campo_corregido=="1825-01-01"
		    || $campo_corregido=="1810-01-01"
		    || $campo_corregido=="1805-01-01")
		    && ($array_fields[113]=="1" || $array_fields[113]=="2" || $array_fields[113]=="3")
		    
		)
		{
			//c1
		    $campo_corregido="1800-01-01";
		}	
		else if( 

			(
				$campo_corregido=="1800-01-01"
		    )
		    && ($array_fields[113]=="22")
		    && ($edad!=50
		       && $edad!=55
		       && $edad!=60
		       && $edad!=65
		       && $edad!=70
		       && $edad!=75
		       && $edad!=80
		       && $edad!=85
		       && $edad!=90
		       && $edad!=95
		       && $edad!=100
		       && $edad!=105
		       && $edad!=110
		       && $edad!=115
		       && $edad!=120
		       )    
		)
		{
			//c2
		    $campo_corregido="1845-01-01";
		}
		else if( 

			(
				$campo_corregido=="1800-01-01"
		    )
		    && ($array_fields[113]=="22")
		    && ($edad==50
		       || $edad==55
		       || $edad==60
		       || $edad==65
		       || $edad==70
		       || $edad==75
		       || $edad==80
		       || $edad==85
		       || $edad==90
		       || $edad==95
		       || $edad==100
		       || $edad==105
		       || $edad==110
		       || $edad==115
		       || $edad==120
		       )      
		)
		{
			//c3
		    $campo_corregido="1835-01-01";
		}
		
	     }//fin fase
	    }//c112
	    
	    //campo a corregir es 113
	    if($numero_campo_a_corregir==113)
	    {
	      if($fase_correccion==0)
	      {
		//campo en blanco
		$es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas("1900-12-31",trim($array_fields[112]));
		
		$fecha_calendario_112=$this->diferencia_dias_entre_fechas(trim($array_fields[112]),"1900-12-31");
		
		if($campo_corregido=="")
		{
			if($campo_corregido==""
			   && ($array_fields[112]=="1800-01-01"
			   		|| $array_fields[112]=="1805-01-01"
			       || $array_fields[112]=="1810-01-01"
			       || $array_fields[112]=="1825-01-01"
			       || $array_fields[112]=="1830-01-01"
			       || $array_fields[112]=="1835-01-01"
			       || $array_fields[112]=="1845-01-01"
			       )
			   )
			{
				//c1
			    $campo_corregido="4";
			}
			else if($campo_corregido==""
				&& $fecha_calendario_112<0
			   )
			{
				//c2
			    $campo_corregido="22";
			}			
			else if($campo_corregido=="")
			{
				//c3
			    $campo_corregido="4";
			}//fin condiciones
		}//fin if
		
		
		
		//valor permitido

		if($campo_corregido!="1"
		   && $campo_corregido!="2"
		   && $campo_corregido!="3"
		   && $campo_corregido!="4"
		   && $campo_corregido!="22"
		   )
		{

			if($campo_corregido!="1"
			   && $campo_corregido!="2"
			   && $campo_corregido!="3"
			   && $campo_corregido!="4"
			   && $campo_corregido!="22"
			   && ($array_fields[112]=="1800-01-01"
			   		|| $array_fields[112]=="1805-01-01"
			       || $array_fields[112]=="1810-01-01"
			       || $array_fields[112]=="1825-01-01"
			       || $array_fields[112]=="1830-01-01"
			       || $array_fields[112]=="1835-01-01"
			       || $array_fields[112]=="1845-01-01"
			       )
			   )
			{
				//c1
			    $campo_corregido="4";
			}
			else if($campo_corregido!="1"
			   && $campo_corregido!="2"
			   && $campo_corregido!="3"
			   && $campo_corregido!="4"
			   && $campo_corregido!="22"
				&& $fecha_calendario_112<0
			   )
			{
				//c2
			    $campo_corregido="22";
			}
			else if($campo_corregido!="1"
			   && $campo_corregido!="2"
			   && $campo_corregido!="3"
			   && $campo_corregido!="4"
			   && $campo_corregido!="22"
			   )
			{
				//c3
			    $campo_corregido="4";
			}
		}//fin if
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_032($campo_corregido);
	      }
	      else if($fase_correccion==1)
	      {
		$es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas("1900-12-31",trim($array_fields[112]));
		
		$fecha_calendario_112=$this->diferencia_dias_entre_fechas(trim($array_fields[112]),"1900-12-31");
		
		//calidad c113
		if($campo_corregido!="4"
		   && ($array_fields[112]=="1805-01-01"
		       || $array_fields[112]=="1810-01-01"
		       || $array_fields[112]=="1825-01-01"
		       || $array_fields[112]=="1830-01-01"
		       || $array_fields[112]=="1835-01-01"
		       || $array_fields[112]=="1845-01-01"
		       )
		   )
		{
			//c1
		    $campo_corregido="4";
		}
		else if($campo_corregido=="4"
			&& $fecha_calendario_112<0
		   )
		{
			//c2
		    $campo_corregido="22";
		}
		else if($campo_corregido=="22"
			&& $array_fields[112]=="1800-01-01"
		   )
		{
			//c3
		    $campo_corregido="4";
		}

		


		
		
	      }//fin fase
	    }//revisado c113
	    
	    //campo a corregir es 17
	    if($numero_campo_a_corregir==17)
	    {
	    	$fecha_calendario_c84=$this->diferencia_dias_entre_fechas(trim($array_fields[84]),"1900-12-31");

	    	 //parte edad campo inv
		    $nueva_edad_year_actual_inv=-1;
			$nueva_edad_meses_actual_inv=-1;
			$nueva_edad_dias_actual_inv=-1;
			
			$campo_fech_nac=trim($array_fields[9]);
			$array_fecha_nacimiento=explode("-",$campo_fech_nac);
			$campo_invitado=trim($array_fields[84]);
			$fecha_campo_inv=explode("-",$campo_invitado);
			if(count($fecha_campo_inv)==3 && checkdate($fecha_campo_inv[1],$fecha_campo_inv[2],$fecha_campo_inv[0]))
			{
			    $array_calc_edad_actual=$this->edad_years_months_days($array_fecha_nacimiento[2]."-".$array_fecha_nacimiento[1]."-".$array_fecha_nacimiento[0],$fecha_campo_inv[2]."-".$fecha_campo_inv[1]."-".$fecha_campo_inv[0]);
			    $nueva_edad_year_actual_inv=intval($array_calc_edad_actual['y']);
			    $nueva_edad_meses_actual_inv=(intval($array_calc_edad_actual['y'])*12)+$array_calc_edad_actual['m'];
			    $nueva_edad_dias_actual_inv=$this->diferencia_dias_entre_fechas($campo_fech_nac, $campo_invitado);
			}//fin if
			//fin parte edad campo inv
	     if($fase_correccion==0)
	     {
		//campo en blanco	
		if($campo_corregido=="")
		{	
			if($campo_corregido==""
			   && $edad_meses>36
			   )
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido==""
			   && $edad_meses<=36
			   && ($array_fields[85]=="2")
			   )
			{
			    $campo_corregido="1";
			}
			else if($campo_corregido==""
			   && $edad_meses<=36
			   	 && ($array_fields[85]=="1" || $array_fields[85]=="22")
			   	)
			   
			{
			    $campo_corregido="2";
			}
			else if($campo_corregido==""
			   && $edad_meses<=36
			   && $array_fields[85]=="0"
			   	)
			   
			{
			    $campo_corregido="21";
			}
			else if($campo_corregido=="")
			{
			    $campo_corregido="0";
			}
		}//fin if
		
		//valor permitido
		if($campo_corregido!="0"
			&& $campo_corregido!="1"
			&& $campo_corregido!="2"
			&& $campo_corregido!="21"
			)
		{
			if($campo_corregido!="0"
				&& $campo_corregido!="1"
				&& $campo_corregido!="2"
				&& $campo_corregido!="21"
			   && $edad_meses>36
			   )
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0"
				&& $campo_corregido!="1"
				&& $campo_corregido!="2"
				&& $campo_corregido!="21"
			   && $edad_meses<=36
			   && ($array_fields[85]=="2")
			   )
			{
			    $campo_corregido="1";
			}
			else if($campo_corregido!="0"
				&& $campo_corregido!="1"
				&& $campo_corregido!="2"
				&& $campo_corregido!="21"
			   && $edad_meses<=36
			   	 && ($array_fields[85]=="1" || $array_fields[85]=="22")
			   	)
			   
			{
			    $campo_corregido="2";
			}
			else if($campo_corregido!="0"
				&& $campo_corregido!="1"
				&& $campo_corregido!="2"
				&& $campo_corregido!="21"
			   && $edad_meses<=36
			   && $array_fields[85]=="0"
			   	)
			   
			{
			    $campo_corregido="21";
			}
			else if($campo_corregido!="0"
				&& $campo_corregido!="1"
				&& $campo_corregido!="2"
				&& $campo_corregido!="21"
			  )
			{
			    $campo_corregido="0";
			}
		}//fin if
		
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_007($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
			//calidad
	     	if($campo_corregido!="0"
			   && $edad_meses>36
			   )
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido!="1"
			   && $edad_meses<=36
			   && ($array_fields[85]=="2")
			   )
			{
			    $campo_corregido="1";
			}
			else if($campo_corregido!="2"
			   && $edad_meses<=36
			   	 && ($array_fields[85]=="1" || $array_fields[85]=="22")
			   	)
			   
			{
			    $campo_corregido="2";
			}
			else if($campo_corregido!="21"
			   && $edad_meses<=36
			   && $array_fields[85]=="0" 
			   	)
			   
			{
			    $campo_corregido="21";
			}//fin calidad

			
		
		
	     }//fin fase 1
	    }//fin campo 17
	    
	    //campo a corregir es 114
	    if($numero_campo_a_corregir==114)
	    {
	     if($fase_correccion==0)
	     {
			//campo en blanco	
			if($campo_corregido=="")
			{	
			if($campo_corregido==""
			   && (trim($array_fields[17])!="1")
			   )
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido==""
			   && $array_fields[17]=="1"
			   )
			{
			    $campo_corregido="22";
			}
			else if($campo_corregido=="")
			{
			    $campo_corregido="0";
			}
		}//fin if
		
		//valor permitido
		 if($campo_corregido!="0"
			&& $campo_corregido!="1"
			&& $campo_corregido!="2"
			&& $campo_corregido!="16"
			&& $campo_corregido!="17"
			&& $campo_corregido!="18"
			&& $campo_corregido!="19"
			&& $campo_corregido!="20"
			&& $campo_corregido!="22"
		  )
		{
			if($campo_corregido!="0"
				&& $campo_corregido!="1"
				&& $campo_corregido!="2"
				&& $campo_corregido!="16"
				&& $campo_corregido!="17"
				&& $campo_corregido!="18"
				&& $campo_corregido!="19"
				&& $campo_corregido!="20"
				&& $campo_corregido!="22"
				&& (trim($array_fields[17])!="1")
			  )
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0"
				&& $campo_corregido!="1"
				&& $campo_corregido!="2"
				&& $campo_corregido!="16"
				&& $campo_corregido!="17"
				&& $campo_corregido!="18"
				&& $campo_corregido!="19"
				&& $campo_corregido!="20"
				&& $campo_corregido!="22"
				&& $array_fields[17]=="1"
			  )
			{
			    $campo_corregido="22";
			}
			else if($campo_corregido!="0"
				&& $campo_corregido!="1"
				&& $campo_corregido!="2"
				&& $campo_corregido!="16"
				&& $campo_corregido!="17"
				&& $campo_corregido!="18"
				&& $campo_corregido!="19"
				&& $campo_corregido!="20"
				&& $campo_corregido!="22"
			  )
			{
			    $campo_corregido="0";
			}
		}//fin if
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_017($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
			//calidad 114
			if($campo_corregido!="0"
				&& (trim($array_fields[17])!="1")
				)
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido=="0"
				&& $array_fields[17]=="1"
				)
			{
			    $campo_corregido="22";
			}//fin calidad
	     }//fin fase
	    }//fin campo 114
	    //FIN CAMPOS OTROS RIESGOS
	    
	    //CAMPOS RIESGO OBESIDAD
	    //campo a corregir es 21
	    if($numero_campo_a_corregir==21)
	    {
	    	$c64_fecha_calendario=$this->diferencia_dias_entre_fechas(trim($array_fields[64]),"1900-12-31");
	     if($fase_correccion==0)
	     {
			//calculo IMC
			$estatura_metros=floatval($array_fields[32])/100;
			$masa_kilogramos=floatval($array_fields[30]);
			$val_imc=0;
			if($estatura_metros>0)
			{
			    $val_imc=$masa_kilogramos/($estatura_metros*$estatura_metros);
			}
			
			//campo en blanco
			if($campo_corregido=="" )
			{
				if($campo_corregido==""
					&& ($array_fields[30]=="999" )
				   )
				{
					//c1
				    $campo_corregido="21";
				}
				else if($campo_corregido==""
					&& ( $array_fields[32]=="999")
				   )
				{
					//c2
				    $campo_corregido="21";
				}		
				else if($campo_corregido==""				   
				   && ($array_fields[30]!="999" && $array_fields[32]!="999")
				   && $val_imc>30
				   && $edad>18
				   )
				{
					//c3
				    $campo_corregido="1";
				}			
				else if($campo_corregido==""
				   
				   && ($array_fields[30]!="999" && $array_fields[32]!="999")		   
				   && ($c64_fecha_calendario<0 )
				   )
				{
					//c4
				    $campo_corregido="2";
				}
				else if($campo_corregido==""
				   
				   && ($array_fields[30]!="999" && $array_fields[32]!="999")		   
				   && $val_imc<30
				   && $edad>18
				   && $c64_fecha_calendario>=0 && $c64_fecha_calendario!==false 
				       && trim($array_fields[64])!="1800-01-01"
				   )
				{
					//c5
				    $campo_corregido="3";
				}
				else if($campo_corregido==""
				   
				   && ($array_fields[30]!="999" && $array_fields[32]!="999")		   
				   && $c64_fecha_calendario>=0 && $c64_fecha_calendario!==false
				   && trim($array_fields[64])!="1800-01-01"
				   && $edad<=18
				   )
				{
					//c6
				    $campo_corregido="3";
				}
				else if($campo_corregido=="" )
				{
					//c7
				    $campo_corregido="21";
				}
			}//fin if
			
			//valor permitido
			if($campo_corregido!="1"
			   && $campo_corregido!="2"
			   && $campo_corregido!="3"
			   && $campo_corregido!="21"
			   )
			{
				
				if($campo_corregido!="1"
				   && $campo_corregido!="2"
				   && $campo_corregido!="3"
				   && $campo_corregido!="21"
					&& ($array_fields[30]=="999" )
				   )
				{
					//c1
				    $campo_corregido="21";
				}
				else if($campo_corregido!="1"
				   && $campo_corregido!="2"
				   && $campo_corregido!="3"
				   && $campo_corregido!="21"
					&& ( $array_fields[32]=="999")
				   )
				{
					//c2
				    $campo_corregido="21";
				}		
				else if($campo_corregido!="1"
				   && $campo_corregido!="2"
				   && $campo_corregido!="3"
				   && $campo_corregido!="21"				   
				   && ($array_fields[30]!="999" && $array_fields[32]!="999")
				   && $val_imc>30
				   && $edad>18
				   )
				{
					//c3
				    $campo_corregido="1";
				}			
				else if($campo_corregido!="1"
				   && $campo_corregido!="2"
				   && $campo_corregido!="3"
				   && $campo_corregido!="21"
				   
				   && ($array_fields[30]!="999" && $array_fields[32]!="999")		   
				   && ($c64_fecha_calendario<0 )
				   )
				{
					//c4
				    $campo_corregido="2";
				}
				else if($campo_corregido!="1"
				   && $campo_corregido!="2"
				   && $campo_corregido!="3"
				   && $campo_corregido!="21"
				   
				   && ($array_fields[30]!="999" && $array_fields[32]!="999")		   
				   && $val_imc<30
				   && $edad>18
				   && $c64_fecha_calendario>=0 && $c64_fecha_calendario!==false 
				       && trim($array_fields[64])!="1800-01-01"
				   )
				{
					//c5
				    $campo_corregido="3";
				}
				else if($campo_corregido!="1"
				   && $campo_corregido!="2"
				   && $campo_corregido!="3"
				   && $campo_corregido!="21"
				   
				   && ($array_fields[30]!="999" && $array_fields[32]!="999")		   
				   && $c64_fecha_calendario>=0 && $c64_fecha_calendario!==false
				   && trim($array_fields[64])!="1800-01-01"
				   && $edad<=18
				   )
				{
					//c6
				    $campo_corregido="3";
				}
				else if($campo_corregido!="1"
				   && $campo_corregido!="2"
				   && $campo_corregido!="3"
				   && $campo_corregido!="21"
				   )
				{
					//c7
				    $campo_corregido="21";
				}

			}//fin if
			
			//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_010($campo_corregido);
	     }//fin fase 0
	     else if($fase_correccion==1)
	     {
				//calculo IMC
				$estatura_metros=floatval($array_fields[32])/100;
				$masa_kilogramos=floatval($array_fields[30]);
				$val_imc=0;
				if($estatura_metros>0)
				{
				    $val_imc=$masa_kilogramos/($estatura_metros*$estatura_metros);
				}
				
				//calidad c21
				if($campo_corregido!="21"
					&& ($array_fields[30]=="999" )
				   )
				{
					//c1
				    $campo_corregido="21";
				}
				else if($campo_corregido!="21"
					&& ( $array_fields[32]=="999")
				   )
				{
					//c2
				    $campo_corregido="21";
				}		
				else if($campo_corregido!="1"				   
				   && ($array_fields[30]!="999" && $array_fields[32]!="999")
				   && $val_imc>30
				   && $edad>18
				   )
				{
					//c3
				    $campo_corregido="1";
				}			
				else if($campo_corregido!="2"
				   
				   && ($array_fields[30]!="999" && $array_fields[32]!="999")		   
				   && ($c64_fecha_calendario<0 )
				   )
				{
					//c4
				    $campo_corregido="2";
				}
				else if($campo_corregido!="3"
				   
				   && ($array_fields[30]!="999" && $array_fields[32]!="999")		   
				   && $val_imc<30
				   && $edad>18
				   && $c64_fecha_calendario>=0 && $c64_fecha_calendario!==false 
				       && trim($array_fields[64])!="1800-01-01"
				   )
				{
					//c5
				    $campo_corregido="3";
				}
				else if($campo_corregido!="3"
				   
				   && ($array_fields[30]!="999" && $array_fields[32]!="999")		   
				   && $c64_fecha_calendario>=0 && $c64_fecha_calendario!==false
				   && trim($array_fields[64])!="1800-01-01"
				   && $edad<=18
				   )
				{
					//c6
				    $campo_corregido="3";
				}//fin calidad


				

				
				
		
	     }//fin fase
	    }//fin campo 21
	    
	    //campo a corregir es 29
	    if($numero_campo_a_corregir==29)
	    {
	     if($fase_correccion==0)
	     {
			//campo en blanco
			if($campo_corregido=="")
			{
				if($campo_corregido=="" && $array_fields[30]=="999"  )
				{
				    $campo_corregido="1800-01-01";
				}
				else if($campo_corregido==""  && floatval($array_fields[30])<0.20)
				{
				    $campo_corregido="1800-01-01";
				}
				else if($campo_corregido==""  && floatval($array_fields[30])>250 && $array_fields[30]!="999" )
				{
				    $campo_corregido="1800-01-01";
				}
			    else if($campo_corregido=="")
				{
				    $campo_corregido="1800-01-01";
				}
			}//fin campo en blanco
		
			//formato
			$corrige_formato=false;
			$array_fecha_campo_actual=explode("-", $campo_corregido);
			if(count($array_fecha_campo_actual)==3)
			{
				if(ctype_digit($array_fecha_campo_actual[0]) && ctype_digit($array_fecha_campo_actual[1]) && ctype_digit($array_fecha_campo_actual[2]))
				{
					//checkdate mm-dd-aaaa -> aaaa-mm-dd ?
					if(checkdate($array_fecha_campo_actual[1],$array_fecha_campo_actual[2],$array_fecha_campo_actual[0])
				       && intval($array_fecha_campo_actual[0])>=32)
				    {
				    	//no se corrige formato
				    }
				    else
				    {
				    	$corrige_formato=true;
				    }

				}
				else
				{
					$corrige_formato=true;
				}
			}
			else
			{
				$corrige_formato=true;
			}//fin else

			if($corrige_formato==true)
			{
			$campo_corregido=$this->corrector_formato_fecha($campo_corregido);
			}
			
			//valor permitido
			$excede_fecha_corte=$this->diferencia_dias_entre_fechas($campo_corregido,$date_de_corte);
			$es_menor_o_igual_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
			$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
			
			$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");

			//comparacion fecha campo actual es inferior a la fecha de nacimiento
			$fecha_nacimiento_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),"1900-12-31");
			$campo_actual_inferior_fecha_nacimiento=$this->diferencia_dias_entre_fechas($campo_corregido,trim($array_fields[9]));
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_nacimiento_es_mayor_a_1900_12_31<0
			&& $fecha_nacimiento_es_mayor_a_1900_12_31!==false
			&& $campo_actual_inferior_fecha_nacimiento>0
			&& $campo_actual_inferior_fecha_nacimiento!==false
			)
			{
				$campo_corregido="1800-01-01";
			}//fin if
			//fin comparacion fecha campo actual es inferior a la fecha de nacimiento

			//limite inferior fecha corte 2 years
			$fecha_corte_menos_2_years="";
			$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=false;
			$array_fecha_corte=explode("-", $date_de_corte);
			if(count($array_fecha_corte)==3)
			{
				$fecha_corte_menos_2_years=(intval($array_fecha_corte[0])-2)."-".$array_fecha_corte[1]."-".$array_fecha_corte[2];
				$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=$this->diferencia_dias_entre_fechas($campo_corregido,$fecha_corte_menos_2_years);
			}//fin if
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_corte_menos_2_years!=""
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years!==false
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years>0
			)
			{
				$campo_corregido="1800-01-01";//porque no existe 1845-01-01
			}//fin if
			//fin limite inferior fecha corte 2 years


			if($comparacion_a_1900_12_31<0 
			&& $excede_fecha_corte<0
			&& $comparacion_a_1900_12_31!==false
			&& $excede_fecha_corte!==false
			)
			{
				$campo_corregido="1800-01-01";
			}//fin comparacion excede la fecha de corte

			if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				)
			{
			    if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				 && $array_fields[30]=="999"  )
				{
				    $campo_corregido="1800-01-01";
				}
				else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				  && floatval($array_fields[30])<0.20)
				{
				    $campo_corregido="1800-01-01";
				}
				else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				  && floatval($array_fields[30])>250 && $array_fields[30]!="999" )
				{
				    $campo_corregido="1800-01-01";
				}
			    else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01")
				{
				    $campo_corregido="1800-01-01";
				}
			}//fin valor permitido
			
			//$campo_corregido=$this->corrector_valor_permitido_fecha_criterio_004($campo_corregido);
		     }
		     else if($fase_correccion==1)
		     {
				//calidad
				if($campo_corregido!="1800-01-01"
				&& $array_fields[30]=="999"
				)
				{
				    $campo_corregido="1800-01-01";
				}
				else if($campo_corregido!="1800-01-01"  && floatval($array_fields[30])<0.20)
				{
				    $campo_corregido="1800-01-01";
				}
				else if($campo_corregido!="1800-01-01"  && floatval($array_fields[30])>250 && $array_fields[30]!="999" )
				{
				    $campo_corregido="1800-01-01";
				}

		 	}//fin fase
	    }//fin campo 29
	    
	    //campo a corregir es 30
	    if($numero_campo_a_corregir==30)
	    {
	     if($fase_correccion==0)
	     {
			//campo en blanco
			if($campo_corregido=="")
			{
			    if($campo_corregido=="" && $array_fields[29]=="1800-01-01" )
				{
				    $campo_corregido="999";
				}
				else if($campo_corregido=="")
				{
				    $campo_corregido="999";
				}
			}//fin campo en blanco
		
			//formato
			//echo "<script>alert('antes r30');</script>";
			$campo_corregido=trim($campo_corregido);
			$campo_corregido=str_replace(",",".",$campo_corregido);
			$longitud_decimal=3;//debido a que la longitud aumento a 5
			if(!ctype_digit($campo_corregido) && is_numeric($campo_corregido))
			{
			    $array_num_decimal=explode(".",trim($campo_corregido));
			    if($array_num_decimal[0]==0)
			    {
					$campo_corregido="".round(floatval($campo_corregido),$longitud_decimal,PHP_ROUND_HALF_UP);
					$array_num_decimal_2=explode(".",trim($campo_corregido));
					while(strlen($array_num_decimal_2[1])<$longitud_decimal)
					{
					    $array_num_decimal_2[1]=$array_num_decimal_2[1]."0";
					}
					$campo_corregido=$array_num_decimal_2[0].".".$array_num_decimal_2[1];
				
			    }
			    else if($array_num_decimal[0]!=0)
			    {
				$campo_corregido="".intval(round(floatval($campo_corregido),0,PHP_ROUND_HALF_UP));
				//echo "<script>alert('entro r30');</script>";
			    }
			}
			else
			{
			   //echo "<script>alert('fallo r formato  r30 $campo_corregido');</script>"; 
			}
			
			//valor permitido
			if( (floatval($campo_corregido)<0.20 || floatval($campo_corregido)>250 || is_numeric($campo_corregido)==false)
				&& $campo_corregido!="999"
				&& $array_fields[29]=="1800-01-01"
				)
			{
			    $campo_corregido="999";
			}
			else if(
				(floatval($campo_corregido)<0.20 || floatval($campo_corregido)>250 || is_numeric($campo_corregido)==false)
				&& $campo_corregido!="999"
				&& floatval($campo_corregido)<0.20 
				)
			{
			    $campo_corregido="999";
			}
			else if(
				(floatval($campo_corregido)<0.20 || floatval($campo_corregido)>250 || is_numeric($campo_corregido)==false)
				&& $campo_corregido!="999"
				&& floatval($campo_corregido)>250
				)
			{
			    $campo_corregido="999";
			}
			else if(
				(floatval($campo_corregido)<0.20 || floatval($campo_corregido)>250 || is_numeric($campo_corregido)==false)
				&& $campo_corregido!="999"
				)
			{
			    $campo_corregido="999";
			}
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_035($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
			//calidad
			if($campo_corregido!="999"
				&& $array_fields[29]=="1800-01-01"
			)
			{
			    $campo_corregido="999";
			}
			else if(floatval($campo_corregido)<0.20)
			{
			    $campo_corregido="999";
			}
			else if(floatval($campo_corregido)>250 && $campo_corregido!="999")
			{
			    $campo_corregido="999";
			}
	     }//fin fase
	    }//fin campo 30
	    
	    //campo a corregir es 31
	    if($numero_campo_a_corregir==31)
	    {
	     if($fase_correccion==0)
	     {
			//campo en blanco
			
			if($campo_corregido=="")
			{
				if($campo_corregido=="" && $array_fields[32]=="999"  )
				{
				    $campo_corregido="1800-01-01";
				}
				else if($campo_corregido==""  && intval($array_fields[32])<20)
				{
				    $campo_corregido="1800-01-01";
				}
				else if($campo_corregido==""  && intval($array_fields[32])>225 && $array_fields[32]!="999" )
				{
				    $campo_corregido="1800-01-01";
				}
			    else if($campo_corregido=="")
				{
				    $campo_corregido="1800-01-01";
				}
			}//fin campo en blanco
		
			//formato
			$corrige_formato=false;
			$array_fecha_campo_actual=explode("-", $campo_corregido);
			if(count($array_fecha_campo_actual)==3)
			{
				if(ctype_digit($array_fecha_campo_actual[0]) && ctype_digit($array_fecha_campo_actual[1]) && ctype_digit($array_fecha_campo_actual[2]))
				{
					//checkdate mm-dd-aaaa -> aaaa-mm-dd ?
					if(checkdate($array_fecha_campo_actual[1],$array_fecha_campo_actual[2],$array_fecha_campo_actual[0])
				       && intval($array_fecha_campo_actual[0])>=32)
				    {
				    	//no se corrige formato
				    }
				    else
				    {
				    	$corrige_formato=true;
				    }

				}
				else
				{
					$corrige_formato=true;
				}
			}
			else
			{
				$corrige_formato=true;
			}//fin else

			if($corrige_formato==true)
			{
			$campo_corregido=$this->corrector_formato_fecha($campo_corregido);
			}
			
			//valor permitido
			$excede_fecha_corte=$this->diferencia_dias_entre_fechas($campo_corregido,$date_de_corte);
			$es_menor_o_igual_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
			$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
			
			$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");

			//comparacion fecha campo actual es inferior a la fecha de nacimiento
			$fecha_nacimiento_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),"1900-12-31");
			$campo_actual_inferior_fecha_nacimiento=$this->diferencia_dias_entre_fechas($campo_corregido,trim($array_fields[9]));
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_nacimiento_es_mayor_a_1900_12_31<0
			&& $fecha_nacimiento_es_mayor_a_1900_12_31!==false
			&& $campo_actual_inferior_fecha_nacimiento>0
			&& $campo_actual_inferior_fecha_nacimiento!==false
			)
			{
				$campo_corregido="1800-01-01";
			}//fin if
			//fin comparacion fecha campo actual es inferior a la fecha de nacimiento

			//limite inferior fecha corte 2 years
			$fecha_corte_menos_2_years="";
			$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=false;
			$array_fecha_corte=explode("-", $date_de_corte);
			if(count($array_fecha_corte)==3)
			{
				$fecha_corte_menos_2_years=(intval($array_fecha_corte[0])-2)."-".$array_fecha_corte[1]."-".$array_fecha_corte[2];
				$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=$this->diferencia_dias_entre_fechas($campo_corregido,$fecha_corte_menos_2_years);
			}//fin if
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_corte_menos_2_years!=""
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years!==false
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years>0
			)
			{
				$campo_corregido="1800-01-01";
			}//fin if
			//fin limite inferior fecha corte 2 years

			if($comparacion_a_1900_12_31<0 
			&& $excede_fecha_corte<0
			&& $comparacion_a_1900_12_31!==false
			&& $excede_fecha_corte!==false
			)
			{
				$campo_corregido="1800-01-01";
			}//fin comparacion excede la fecha de corte
			

			if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				)
			{
			    if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				 && $array_fields[32]=="999"  )
				{
				    $campo_corregido="1800-01-01";
				}
				else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				  && intval($array_fields[32])<20
				  )
				{
				    $campo_corregido="1800-01-01";
				}
				else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				  && intval($array_fields[32])>225 && $array_fields[32]!="999" )
				{
				    $campo_corregido="1800-01-01";
				}
			    else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01")
				{
				    $campo_corregido="1800-01-01";
				}
			}//fin valor permitido
	     
		//$campo_corregido=$this->corrector_valor_permitido_fecha_criterio_004($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
			//calidad
			if($campo_corregido!="1800-01-01"
				&& $array_fields[32]=="999"
			)
			{
			    $campo_corregido="1800-01-01";
			}
			else if($campo_corregido!="1800-01-01"  && intval($array_fields[32])<20)
			{
			    $campo_corregido="1800-01-01";
			}
			else if($campo_corregido!="1800-01-01"  && intval($array_fields[32])>225 && $array_fields[30]!="999" )
			{
			    $campo_corregido="1800-01-01";
			}
		
	     }//fin fase
	    }//fin campo 31
	    
	    //campo a corregir es 32
	    if($numero_campo_a_corregir==32)
	    {
	     if($fase_correccion==0)
	     {
			//campo en blanco
			if($campo_corregido=="")
			{
			    if($campo_corregido=="" && $array_fields[31]=="1800-01-01" )
				{
				    $campo_corregido="999";
				}
				else if($campo_corregido=="")
				{
				    $campo_corregido="999";
				}
			}//fin campo en blanco
			
			//formato
			$campo_corregido=trim($campo_corregido);
			
			//$campo_corregido=str_replace(",",".",$campo_corregido);
			$campo_corregido=str_replace(",","",$campo_corregido);//aca se quita solo el separador solo c32
			$campo_corregido=str_replace(".","",$campo_corregido);//aca se quita solo el separador
			
			$campo_corregido=floatval($campo_corregido);
			if(!ctype_digit($campo_corregido) && is_numeric($campo_corregido))
			{
			    $campo_corregido="".intval(round(floatval($campo_corregido),0,PHP_ROUND_HALF_UP));		    
			}
			
			//valor permitido
			if( (intval($campo_corregido)<20 || intval($campo_corregido)>225 || is_numeric($campo_corregido)==false)
				&& $campo_corregido!="999"
				&& $array_fields[31]=="1800-01-01"
				)
			{
			    $campo_corregido="999";
			}
			else if(
				(intval($campo_corregido)<20 || intval($campo_corregido)>225 || is_numeric($campo_corregido)==false)
				&& $campo_corregido!="999"
				&& intval($campo_corregido)<20 
				)
			{
			    $campo_corregido="999";
			}
			else if(
				(intval($campo_corregido)<20 || intval($campo_corregido)>225 || is_numeric($campo_corregido)==false)
				&& $campo_corregido!="999"
				&& intval($campo_corregido)>225
				)
			{
			    $campo_corregido="999";
			}
			else if(
				(intval($campo_corregido)<20 || intval($campo_corregido)>225 || is_numeric($campo_corregido)==false)
				&& $campo_corregido!="999"
				)
			{
			    $campo_corregido="999";
			}
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_036($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
		
			//calidad
			if($campo_corregido!="999"
				&& $array_fields[31]=="1800-01-01"
			)
			{
			    $campo_corregido="999";
			}
			else if(intval($campo_corregido)<20)
			{
			    $campo_corregido="999";
			}
			else if(intval($campo_corregido)>225 && $campo_corregido!="999")
			{
			    $campo_corregido="999";
			}
	     }//fin fase
	    }//fin campo 32
	    
	    //campo a corregir es 64
	    if($numero_campo_a_corregir==64)
	    {
	     if($fase_correccion==0)
	     {
			//campo en blanco
			 if($campo_corregido=="" )
			{
		     	if($campo_corregido==""
				   && $array_fields[30]=="999"
				   )
				{
					//c1
				    $campo_corregido="1845-01-01";
				}
				else if($campo_corregido==""
				   && $array_fields[32]=="999"
				   )
				{
					//c2
				    $campo_corregido="1845-01-01";
				}
				else if($campo_corregido==""
				   && $array_fields[21]!="2"
				   )
				{
					//c3
				    $campo_corregido="1845-01-01";
				}
				else if($campo_corregido==""
					&& $array_fields[30]!="999"
					&& $array_fields[32]!="999"
				   && $array_fields[21]=="2"
				   )
				{
					//c4
				    $campo_corregido="1800-01-01";
				}
				else if($campo_corregido=="" )
				{
				    $campo_corregido="1845-01-01";
				}
			}//fin if
			
			//formato
			$corrige_formato=false;
			$array_fecha_campo_actual=explode("-", $campo_corregido);
			if(count($array_fecha_campo_actual)==3)
			{
				if(ctype_digit($array_fecha_campo_actual[0]) && ctype_digit($array_fecha_campo_actual[1]) && ctype_digit($array_fecha_campo_actual[2]))
				{
					//checkdate mm-dd-aaaa -> aaaa-mm-dd ?
					if(checkdate($array_fecha_campo_actual[1],$array_fecha_campo_actual[2],$array_fecha_campo_actual[0])
				       && intval($array_fecha_campo_actual[0])>=32)
				    {
				    	//no se corrige formato
				    }
				    else
				    {
				    	$corrige_formato=true;
				    }

				}
				else
				{
					$corrige_formato=true;
				}
			}
			else
			{
				$corrige_formato=true;
			}//fin else

			if($corrige_formato==true)
			{
			$campo_corregido=$this->corrector_formato_fecha($campo_corregido,false,-2);
			}
			
			//valor permitido
			$excede_fecha_corte=$this->diferencia_dias_entre_fechas($campo_corregido,$date_de_corte);
			$es_menor_o_igual_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
			$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
			
			$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");

			//comparacion fecha campo actual es inferior a la fecha de nacimiento
			$fecha_nacimiento_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),"1900-12-31");
			$campo_actual_inferior_fecha_nacimiento=$this->diferencia_dias_entre_fechas($campo_corregido,trim($array_fields[9]));
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_nacimiento_es_mayor_a_1900_12_31<0
			&& $fecha_nacimiento_es_mayor_a_1900_12_31!==false
			&& $campo_actual_inferior_fecha_nacimiento>0
			&& $campo_actual_inferior_fecha_nacimiento!==false
			)
			{
				$campo_corregido="1800-01-01";
			}//fin if
			//fin comparacion fecha campo actual es inferior a la fecha de nacimiento

			//limite inferior fecha corte 2 years
			$fecha_corte_menos_2_years="";
			$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=false;
			$array_fecha_corte=explode("-", $date_de_corte);
			if(count($array_fecha_corte)==3)
			{
				$fecha_corte_menos_2_years=(intval($array_fecha_corte[0])-2)."-".$array_fecha_corte[1]."-".$array_fecha_corte[2];
				$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=$this->diferencia_dias_entre_fechas($campo_corregido,$fecha_corte_menos_2_years);
			}//fin if
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_corte_menos_2_years!=""
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years!==false
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years>0
			)
			{
				$campo_corregido="1845-01-01";
			}//fin if
			//fin limite inferior fecha corte 2 years

			if($comparacion_a_1900_12_31<0 
			&& $excede_fecha_corte<0
			&& $comparacion_a_1900_12_31!==false
			&& $excede_fecha_corte!==false
			)
		{
			$campo_corregido="1800-01-01";
		}//fin comparacion excede la fecha de corte

			if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1845-01-01"
				)
			{
				if($comparacion_a_1900_12_31>=0
					&& $campo_corregido!="1800-01-01"
					&& $campo_corregido!="1845-01-01"
				   && $array_fields[30]=="999"
				   )
				{
					//c1
				    $campo_corregido="1845-01-01";
				}
				else if($comparacion_a_1900_12_31>=0
					&& $campo_corregido!="1800-01-01"
					&& $campo_corregido!="1845-01-01"
				   && $array_fields[32]=="999"
				   )
				{
					//c2
				    $campo_corregido="1845-01-01";
				}
				else if($comparacion_a_1900_12_31>=0
					&& $campo_corregido!="1800-01-01"
					&& $campo_corregido!="1845-01-01"
				   && $array_fields[21]!="2"
				   )
				{
					//c3
				    $campo_corregido="1845-01-01";
				}
				else if($comparacion_a_1900_12_31>=0
					&& $campo_corregido!="1800-01-01"
					&& $campo_corregido!="1845-01-01"
					&& $array_fields[30]!="999"
					&& $array_fields[32]!="999"
				   && $array_fields[21]=="2"
				   )
				{
					//c4
				    $campo_corregido="1800-01-01";
				}
				else if($comparacion_a_1900_12_31>=0
					&& $campo_corregido!="1800-01-01"
					&& $campo_corregido!="1845-01-01"
					)
				{
				    $campo_corregido="1845-01-01";
				}
			}//fin else if
		     
			//$campo_corregido=$this->corrector_valor_permitido_fecha_criterio_003($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
			//calidad
	     	if($campo_corregido!="1845-01-01"
			   && $array_fields[30]=="999"
			   )
			{
				//c1
			    $campo_corregido="1845-01-01";
			}
			else if($campo_corregido!="1845-01-01"
			   && $array_fields[32]=="999"
			   )
			{
				//c2
			    $campo_corregido="1845-01-01";
			}
			else if($campo_corregido!="1845-01-01"
			   && $array_fields[21]!="2"
			   )
			{
				//c3
			    $campo_corregido="1845-01-01";
			}
			else if($campo_corregido=="1845-01-01"
				&& $array_fields[30]!="999"
				&& $array_fields[32]!="999"
			   && $array_fields[21]=="2"
			   )
			{
				//c4
			    $campo_corregido="1800-01-01";
			}
			
	     }//fin fase
		
	    }//fin campo 64
	    //FIN CAMPOS RIESGO OBESIDAD
	    
	    //CAMPOS  PLANIFICACION FAMILIAR
	    //campo a corregir es 53
	    if($numero_campo_a_corregir==53)
	    {
	     if($fase_correccion==0)
	     {
		//echo "<script>alert('campo 53 a $campo_corregido edad fc $edad ');</script>";
		//campo en blanco
	     	if($campo_corregido=="")
	     	{
				if($campo_corregido=="" && ($edad>=10 && $edad<60))
				{
				    $campo_corregido="1835-01-01";
				}
				else if($campo_corregido=="" && ($edad<10 || $edad>=60))
				{
				    $campo_corregido="1845-01-01";
				}
				else if($campo_corregido=="")
				{
				    $campo_corregido="1845-01-01";
				}
			}//fin if
		
		//formato
		$corrige_formato=false;
		$array_fecha_campo_actual=explode("-", $campo_corregido);
		if(count($array_fecha_campo_actual)==3)
		{
			if(ctype_digit($array_fecha_campo_actual[0]) && ctype_digit($array_fecha_campo_actual[1]) && ctype_digit($array_fecha_campo_actual[2]))
			{
				//checkdate mm-dd-aaaa -> aaaa-mm-dd ?
				if(checkdate($array_fecha_campo_actual[1],$array_fecha_campo_actual[2],$array_fecha_campo_actual[0])
			       && intval($array_fecha_campo_actual[0])>=32)
			    {
			    	//no se corrige formato
			    }
			    else
			    {
			    	$corrige_formato=true;
			    }

			}
			else
			{
				$corrige_formato=true;
			}
		}
		else
		{
			$corrige_formato=true;
		}//fin else

		if($corrige_formato==true)
		{
		$campo_corregido=$this->corrector_formato_fecha($campo_corregido,false,-2);
		}
		
		//valor permitido
		
		$excede_fecha_corte=$this->diferencia_dias_entre_fechas($campo_corregido,$date_de_corte);
		$es_menor_o_igual_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
		
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");

		//comparacion fecha campo actual es inferior a la fecha de nacimiento
			$fecha_nacimiento_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),"1900-12-31");
			$campo_actual_inferior_fecha_nacimiento=$this->diferencia_dias_entre_fechas($campo_corregido,trim($array_fields[9]));
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_nacimiento_es_mayor_a_1900_12_31<0
			&& $fecha_nacimiento_es_mayor_a_1900_12_31!==false
			&& $campo_actual_inferior_fecha_nacimiento>0
			&& $campo_actual_inferior_fecha_nacimiento!==false
			)
			{
				$campo_corregido="1800-01-01";
			}//fin if
			//fin comparacion fecha campo actual es inferior a la fecha de nacimiento

			//limite inferior fecha corte 2 years
			$fecha_corte_menos_2_years="";
			$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=false;
			$array_fecha_corte=explode("-", $date_de_corte);
			if(count($array_fecha_corte)==3)
			{
				$fecha_corte_menos_2_years=(intval($array_fecha_corte[0])-2)."-".$array_fecha_corte[1]."-".$array_fecha_corte[2];
				$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=$this->diferencia_dias_entre_fechas($campo_corregido,$fecha_corte_menos_2_years);
			}//fin if
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_corte_menos_2_years!=""
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years!==false
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years>0
			)
			{
				$campo_corregido="1845-01-01";
			}//fin if
			//fin limite inferior fecha corte 2 years

		if($comparacion_a_1900_12_31<0 
			&& $excede_fecha_corte<0
			&& $comparacion_a_1900_12_31!==false
			&& $excede_fecha_corte!==false
			)
		{
			$campo_corregido="1800-01-01";
		}//fin comparacion excede la fecha de corte

		if($comparacion_a_1900_12_31>=0
			&& $campo_corregido!="1800-01-01"
			&& $campo_corregido!="1805-01-01"
			&& $campo_corregido!="1810-01-01"
			&& $campo_corregido!="1825-01-01"
			&& $campo_corregido!="1830-01-01"
			&& $campo_corregido!="1835-01-01"
			&& $campo_corregido!="1845-01-01"
			)
		{
			if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"			
				&& ($edad>=10 && $edad<60)
				)
			{
			    $campo_corregido="1835-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& ($edad<10 || $edad>=60)
				)
			{
			    $campo_corregido="1845-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				)
			{
			    $campo_corregido="1845-01-01";
			}
		}//fin else if
		//echo "<script>alert('campo 53 a2 $campo_corregido edad fc $edad ');</script>";
	     
		//$campo_corregido=$this->corrector_valor_permitido_fecha_criterio_002($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
		//calidad con fecha actividad
		$nueva_edad_year_actual=-1;
		$nueva_edad_meses_actual=-1;
		$nueva_edad_dias_actual=-1;
		    
		$array_fecha_nacimiento=explode("-",$array_fields[9]);
		$fecha_campo_actual=explode("-",$campo_corregido);
		if(count($fecha_campo_actual)==3 && checkdate($fecha_campo_actual[1],$fecha_campo_actual[2],$fecha_campo_actual[0]))
		{
		    //echo "<script>alert('entro');</script>";
		    
		    $array_calc_edad_actual=$this->edad_years_months_days($array_fecha_nacimiento[2]."-".$array_fecha_nacimiento[1]."-".$array_fecha_nacimiento[0],$fecha_campo_actual[2]."-".$fecha_campo_actual[1]."-".$fecha_campo_actual[0]);
		    $nueva_edad_year_actual=intval($array_calc_edad_actual['y']);
		    $nueva_edad_meses_actual=(intval($array_calc_edad_actual['y'])*12)+$array_calc_edad_actual['m'];
		    $nueva_edad_dias_actual=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),trim($array_fields[$numero_campo_a_corregir]));
		    
		    //echo "<script>alert(' EDAD DIAS $nueva_edad_dias_actual FN ".trim($array_fields[9])." FA ".trim($array_fields[$numero_campo_a_corregir])."  NCA $numero_campo_a_corregir');</script>";
		}
		$es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
		$es_menor_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
		
		//echo "<script>alert('campo 53 a3 $campo_corregido edad fc $edad ');</script>";
		
		if(
			($campo_corregido=="1845-01-01" || $campo_corregido=="1800-01-01") 
			&& ($edad>=10 && $edad<60)
		)
		{
		    $campo_corregido="1835-01-01";
		}
		else if($es_mayor_a_1900_12_31<0 && ($nueva_edad_year_actual<10 || $nueva_edad_year_actual>=60))
		{
		    $campo_corregido="1845-01-01";
		}
		else if(
			(trim($array_fields[53])=="1800-01-01"
				    || trim($array_fields[53])=="1805-01-01"
				    || trim($array_fields[53])=="1810-01-01"
				    || trim($array_fields[53])=="1825-01-01"
				    || trim($array_fields[53])=="1830-01-01"
				    || trim($array_fields[53])=="1835-01-01"
		       )
			&& ($edad<10 || $edad>=60)
			)
		{
		    $campo_corregido="1845-01-01";
		}
		
		//echo "<script>alert('campo 53 a4 $campo_corregido edad fc $edad edad fa $nueva_edad_year_actual');</script>";
	     }//fin fase
	    }//c53
	    
	    //campo a corregir es 54
	    if($numero_campo_a_corregir==54)
	    {
	     if($fase_correccion==0)
	     {
				//campo en blanco
				$campo_55_es_fecha_calendario=$this->diferencia_dias_entre_fechas(trim($array_fields[55]),"1900-12-31");
				if($campo_corregido=="")
				{

					if($campo_corregido==""
					   && ($edad<10 || $edad>=60)
					   )
					{
					    $campo_corregido="0";
					}
					else if($campo_corregido==""
					&& ($edad>=10 && $edad<60)
					)
					{
					    $campo_corregido="21";
					}
					else if($campo_corregido=="")
					{
					    $campo_corregido="0";
					}
					//else if($NOMBRE_ENTIDAD_PERSONALIZADA=="PrepagadaCoomeva")
					
				}//fin if
				
				
				
				//valor permitido
				$campo_55_es_fecha_calendario=$this->diferencia_dias_entre_fechas(trim($array_fields[55]),"1900-12-31");

				$es_diferente_valores_permitidos=(
						$campo_corregido!="0"
					   	&& $campo_corregido!="1"
						&& $campo_corregido!="2"
						&& $campo_corregido!="3"
						&& $campo_corregido!="4"
						&& $campo_corregido!="5"
						&& $campo_corregido!="6"
						&& $campo_corregido!="7"
						&& $campo_corregido!="8"
						&& $campo_corregido!="9"
						&& $campo_corregido!="10"
						&& $campo_corregido!="11"
						&& $campo_corregido!="12"
						&& $campo_corregido!="13"
						&& $campo_corregido!="14"
						&& $campo_corregido!="15"
						&& $campo_corregido!="16"
						&& $campo_corregido!="17"
						&& $campo_corregido!="18"
						&& $campo_corregido!="20"
						&& $campo_corregido!="21"
					);

				if($es_diferente_valores_permitidos==true )
				{

					if($es_diferente_valores_permitidos==true 
					   && ($edad<10 || $edad>=60)
					   )
					{
					    $campo_corregido="0";
					}
					else if($es_diferente_valores_permitidos==true 
					&& ($edad>=10 && $edad<60)
					)
					{
					    $campo_corregido="21";
					}
					else if($es_diferente_valores_permitidos==true )
					{
					    $campo_corregido="0";
					}
					

				}//fin if valor permitido

		
		
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_026($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
				//calidad c54
				$campo_55_es_fecha_calendario=$this->diferencia_dias_entre_fechas(trim($array_fields[55]),"1900-12-31");
				
				if($campo_corregido!="0"
				   && ($edad<10 || $edad>=60)
				   )
				{
				    $campo_corregido="0";
				}
				else if($campo_corregido!="21"
					&& ($edad>=10 && $edad<60)
					)
				{
				    $campo_corregido="21";
				}//fin calidad
				

				
				
	     }//fin fase 1 
	    }//fin campo 54
	    
	    //campo a corregir es 55
	    if($numero_campo_a_corregir==55)
	    {
	     if($fase_correccion==0)
	     {
			//campo en blanco
			if($campo_corregido=="")
			{
				/*
				if($campo_corregido==""
				    && ($edad<10 || $edad>=60)
				    )
				{
				    $campo_corregido="1845-01-01";
				}
				else if($campo_corregido==""
					&& ($edad>=10 && $edad<60)
					    && ($array_fields[54]=="1"
						|| $array_fields[54]=="2"
						|| $array_fields[54]=="3"
						|| $array_fields[54]=="4"
						|| $array_fields[54]=="5"
						|| $array_fields[54]=="6"
						|| $array_fields[54]=="7"
						|| $array_fields[54]=="8"
						|| $array_fields[54]=="9"
						|| $array_fields[54]=="10"
						|| $array_fields[54]=="11"
						|| $array_fields[54]=="12"
						|| $array_fields[54]=="13"
						|| $array_fields[54]=="14"
						|| $array_fields[54]=="15"
						|| $array_fields[54]=="21"
						)
					)
				{
				    $campo_corregido="1800-01-01";
				}
				else if($campo_corregido==""
				&& ($edad>=10 && $edad<60)
				    && ($array_fields[54]=="0"
					)
				)
				{
				    $campo_corregido="1835-01-01";
				}//fin else
				else if($campo_corregido==""
				&& ($edad>=10 && $edad<60)
				    && ($array_fields[54]=="16"
					)
				)
				{
				    $campo_corregido="1805-01-01";
				}//fin else
				else if($campo_corregido==""
				&& ($edad>=10 && $edad<60)
				    && ($array_fields[54]=="17"
					)
				)
				{
				    $campo_corregido="1810-01-01";
				}//fin else
				else if($campo_corregido==""
				&& ($edad>=10 && $edad<60)
				    && ($array_fields[54]=="18"
					)
				)
				{
				    $campo_corregido="1825-01-01";
				}//fin else
				else if($campo_corregido==""
				&& ($edad>=10 && $edad<60)
				    && ($array_fields[54]=="20"
					)
				)
				{
				    $campo_corregido="1835-01-01";
				}//fin else
				*/
				if(
					$campo_corregido==""
			    && ($edad<10 || $edad>=60)
			    )
				{
				    $campo_corregido="1845-01-01";
				}
				else if(
					$campo_corregido==""
				&& ($edad>=10 && $edad<60)
					)//fin condicion
				{
				    $campo_corregido="1835-01-01";
				}
				else if($campo_corregido=="")
				{
				    $campo_corregido="1845-01-01";
				}
			}//fin if
		
		
		//formato
		$corrige_formato=false;
		$array_fecha_campo_actual=explode("-", $campo_corregido);
		if(count($array_fecha_campo_actual)==3)
		{
			if(ctype_digit($array_fecha_campo_actual[0]) && ctype_digit($array_fecha_campo_actual[1]) && ctype_digit($array_fecha_campo_actual[2]))
			{
				//checkdate mm-dd-aaaa -> aaaa-mm-dd ?
				if(checkdate($array_fecha_campo_actual[1],$array_fecha_campo_actual[2],$array_fecha_campo_actual[0])
			       && intval($array_fecha_campo_actual[0])>=32)
			    {
			    	//no se corrige formato
			    }
			    else
			    {
			    	$corrige_formato=true;
			    }

			}
			else
			{
				$corrige_formato=true;
			}
		}
		else
		{
			$corrige_formato=true;
		}//fin else

		if($corrige_formato==true)
		{
		$campo_corregido=$this->corrector_formato_fecha($campo_corregido,false,-2);
		}
		
		//valor permitido
		
		$excede_fecha_corte=$this->diferencia_dias_entre_fechas($campo_corregido,$date_de_corte);
		$es_menor_o_igual_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
		
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");

		//comparacion fecha campo actual es inferior a la fecha de nacimiento
			$fecha_nacimiento_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),"1900-12-31");
			$campo_actual_inferior_fecha_nacimiento=$this->diferencia_dias_entre_fechas($campo_corregido,trim($array_fields[9]));
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_nacimiento_es_mayor_a_1900_12_31<0
			&& $fecha_nacimiento_es_mayor_a_1900_12_31!==false
			&& $campo_actual_inferior_fecha_nacimiento>0
			&& $campo_actual_inferior_fecha_nacimiento!==false
			)
			{
				$campo_corregido="1800-01-01";
			}//fin if
			//fin comparacion fecha campo actual es inferior a la fecha de nacimiento

			//limite inferior fecha corte 2 years
			$fecha_corte_menos_2_years="";
			$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=false;
			$array_fecha_corte=explode("-", $date_de_corte);
			if(count($array_fecha_corte)==3)
			{
				$fecha_corte_menos_2_years=(intval($array_fecha_corte[0])-2)."-".$array_fecha_corte[1]."-".$array_fecha_corte[2];
				$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=$this->diferencia_dias_entre_fechas($campo_corregido,$fecha_corte_menos_2_years);
			}//fin if
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_corte_menos_2_years!=""
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years!==false
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years>0
			)
			{
				$campo_corregido="1845-01-01";
			}//fin if
			//fin limite inferior fecha corte 2 years

		if($comparacion_a_1900_12_31<0 
			&& $excede_fecha_corte<0
			&& $comparacion_a_1900_12_31!==false
			&& $excede_fecha_corte!==false
			)
		{
			$campo_corregido="1800-01-01";
		}//fin comparacion excede la fecha de corte

		if($comparacion_a_1900_12_31>=0
			&& $campo_corregido!="1800-01-01"
			&& $campo_corregido!="1805-01-01"
			&& $campo_corregido!="1810-01-01"
			&& $campo_corregido!="1825-01-01"
			&& $campo_corregido!="1830-01-01"
			&& $campo_corregido!="1835-01-01"
			&& $campo_corregido!="1845-01-01"
			)
		{
				/*
				if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				    && ($edad<10 || $edad>=60)
				    )
				{
				    $campo_corregido="1845-01-01";
				}
				else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
					&& ($edad>=10 && $edad<60)
					    && ($array_fields[54]=="1"
						|| $array_fields[54]=="2"
						|| $array_fields[54]=="3"
						|| $array_fields[54]=="4"
						|| $array_fields[54]=="5"
						|| $array_fields[54]=="6"
						|| $array_fields[54]=="7"
						|| $array_fields[54]=="8"
						|| $array_fields[54]=="9"
						|| $array_fields[54]=="10"
						|| $array_fields[54]=="11"
						|| $array_fields[54]=="12"
						|| $array_fields[54]=="13"
						|| $array_fields[54]=="14"
						|| $array_fields[54]=="15"
						|| $array_fields[54]=="21"
						)
					)
				{
				    $campo_corregido="1800-01-01";
				}
				else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& ($edad>=10 && $edad<60)
				    && ($array_fields[54]=="0"
					)
				)
				{
				    $campo_corregido="1835-01-01";
				}//fin else
				else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& ($edad>=10 && $edad<60)
				    && ($array_fields[54]=="16"
					)
				)
				{
				    $campo_corregido="1805-01-01";
				}//fin else
				else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& ($edad>=10 && $edad<60)
				    && ($array_fields[54]=="17"
					)
				)
				{
				    $campo_corregido="1810-01-01";
				}//fin else
				else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& ($edad>=10 && $edad<60)
				    && ($array_fields[54]=="18"
					)
				)
				{
				    $campo_corregido="1825-01-01";
				}//fin else
				else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& ($edad>=10 && $edad<60)
				    && ($array_fields[54]=="20"
					)
				)
				{
				    $campo_corregido="1835-01-01";
				}//fin else
				*/
				if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
			    && ($edad<10 || $edad>=60)
			    )
				{
				    $campo_corregido="1845-01-01";
				}
				else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& ($edad>=10 && $edad<60)
				)//fin condicion
				{
				    $campo_corregido="1835-01-01";
				}
				else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				)
				{
				    $campo_corregido="1845-01-01";
				}
			
		}//fin else if

		
		
	     
		//$campo_corregido=$this->corrector_valor_permitido_fecha_criterio_003($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
			//calidad con fecha actividad
			$nueva_edad_year_actual=-1;
			$nueva_edad_meses_actual=-1;
			$nueva_edad_dias_actual=-1;
			    
			$array_fecha_nacimiento=explode("-",$array_fields[9]);
			$fecha_campo_actual=explode("-",$campo_corregido);
			if(count($fecha_campo_actual)==3 && checkdate($fecha_campo_actual[1],$fecha_campo_actual[2],$fecha_campo_actual[0]))
			{
			    //echo "<script>alert('entro');</script>";
			    
			    $array_calc_edad_actual=$this->edad_years_months_days($array_fecha_nacimiento[2]."-".$array_fecha_nacimiento[1]."-".$array_fecha_nacimiento[0],$fecha_campo_actual[2]."-".$fecha_campo_actual[1]."-".$fecha_campo_actual[0]);
			    $nueva_edad_year_actual=intval($array_calc_edad_actual['y']);
			    $nueva_edad_meses_actual=(intval($array_calc_edad_actual['y'])*12)+$array_calc_edad_actual['m'];
			    $nueva_edad_dias_actual=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),trim($array_fields[$numero_campo_a_corregir]));
			    
			    //echo "<script>alert(' EDAD DIAS $nueva_edad_dias_actual FN ".trim($array_fields[9])." FA ".trim($array_fields[$numero_campo_a_corregir])."  NCA $numero_campo_a_corregir');</script>";
			}
			$es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
			$es_menor_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
			
			//calidad c55
			if($es_mayor_a_1900_12_31<0
		    && ($nueva_edad_year_actual<10 || $nueva_edad_year_actual>=60)
		    )
			{
			    $campo_corregido="1845-01-01";
			}
			else if(
				(trim($campo_corregido)=="1800-01-01"
					|| $campo_corregido=="1805-01-01"
					|| $campo_corregido=="1810-01-01"
					|| $campo_corregido=="1825-01-01"
					|| $campo_corregido=="1830-01-01"
					|| $campo_corregido=="1835-01-01"
					)
				&& ($edad<10 || $edad>=60)
			    )//fin if
			{
			    $campo_corregido="1845-01-01";
			}
			else if($campo_corregido!="1835-01-01"
			&& ($edad>=10 && $edad<60)
			)//fin condicion
			{
			    $campo_corregido="1835-01-01";
			}//fin else fin calidad
			/*
			else if(
					(
					$campo_corregido=="1805-01-01"
					|| $campo_corregido=="1810-01-01"
					|| $campo_corregido=="1825-01-01"
					|| $campo_corregido=="1830-01-01"
					|| $campo_corregido=="1835-01-01"
					|| $campo_corregido=="1845-01-01"
					)
				&& ($edad>=10 && $edad<60)
				    && ($array_fields[54]=="1"
					|| $array_fields[54]=="2"
					|| $array_fields[54]=="3"
					|| $array_fields[54]=="4"
					|| $array_fields[54]=="5"
					|| $array_fields[54]=="6"
					|| $array_fields[54]=="7"
					|| $array_fields[54]=="8"
					|| $array_fields[54]=="9"
					|| $array_fields[54]=="10"
					|| $array_fields[54]=="11"
					|| $array_fields[54]=="12"
					|| $array_fields[54]=="13"
					|| $array_fields[54]=="14"
					|| $array_fields[54]=="15"
					|| $array_fields[54]=="21"
					)
				)
			{
			    $campo_corregido="1800-01-01";
			}
			else if($campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
			&& ($edad>=10 && $edad<60)
			    && ($array_fields[54]=="0"
				)
			)
			{
			    $campo_corregido="1835-01-01";
			}//fin else
			else if($campo_corregido!="1805-01-01"
			&& ($edad>=10 && $edad<60)
			    && ($array_fields[54]=="16"
				)
			)
			{
			    $campo_corregido="1805-01-01";
			}//fin else
			else if($campo_corregido!="1810-01-01"
			&& ($edad>=10 && $edad<60)
			    && ($array_fields[54]=="17"
				)
			)
			{
			    $campo_corregido="1810-01-01";
			}//fin else
			else if($campo_corregido!="1825-01-01"
			&& ($edad>=10 && $edad<60)
			    && ($array_fields[54]=="18"
				)
			)
			{
			    $campo_corregido="1825-01-01";
			}//fin else
			else if($campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
			&& ($edad>=10 && $edad<60)
			    && ($array_fields[54]=="20"
				)
			)
			{
			    $campo_corregido="1835-01-01";
			}//fin else fin calidad
			*/
		
		
		

		
		
		
	     }//fin fase 1
	    }//fin campo 55
	    //FIN CAMPOS  PLANIFICACION FAMILIAR
	    
	    //CAMPOS EXAMENES DE LABORATORIO
	    
	    //campo a corregir es 84
	    if($numero_campo_a_corregir==84)
	    {
	     if($fase_correccion==0)
	     {
		//campo en blanco
     	if($campo_corregido=="")
		{
			if($campo_corregido==""
			   && $edad_dias>90
			   )
			{
			    $campo_corregido="1845-01-01";
			}
			else if($campo_corregido==""
			   && $edad_dias<=90
			   && ($array_fields[85]=="1" || $array_fields[85]=="2")
			   )
			{
			    $array_c9=explode("-",$array_fields[9]);
			    if(count($array_c9)==3 && checkdate($array_c9[1],$array_c9[2],$array_c9[0]) )
			    {
			    	$fecha = date_create(trim($array_fields[9]));
					//date sub resta, por lo cual al poner un dia negativo suma
				    date_sub($fecha, date_interval_create_from_date_string('-1 days'));
				    $c9_mas_un_dia="".date_format($fecha, 'Y-m-d');
				    $campo_corregido=$c9_mas_un_dia;

			    }//fin if verifica que sea una fecha
			}
			else if($campo_corregido==""
				&& (trim($array_fields[85])=="0")
			   && $edad_dias<=90
			   )
			{
			    $campo_corregido="1845-01-01";
			}
			else if($campo_corregido=="")
			{
			    $campo_corregido="1845-01-01";
			}
		}//fin if
		
		//formato
		$corrige_formato=false;
		$array_fecha_campo_actual=explode("-", $campo_corregido);
		if(count($array_fecha_campo_actual)==3)
		{
			if(ctype_digit($array_fecha_campo_actual[0]) && ctype_digit($array_fecha_campo_actual[1]) && ctype_digit($array_fecha_campo_actual[2]))
			{
				//checkdate mm-dd-aaaa -> aaaa-mm-dd ?
				if(checkdate($array_fecha_campo_actual[1],$array_fecha_campo_actual[2],$array_fecha_campo_actual[0])
			       && intval($array_fecha_campo_actual[0])>=32)
			    {
			    	//no se corrige formato
			    }
			    else
			    {
			    	$corrige_formato=true;
			    }

			}
			else
			{
				$corrige_formato=true;
			}
		}
		else
		{
			$corrige_formato=true;
		}//fin else

		if($corrige_formato==true)
		{
		$campo_corregido=$this->corrector_formato_fecha($campo_corregido,false,-2);
		}
		
		//valor permitido
		$excede_fecha_corte=$this->diferencia_dias_entre_fechas($campo_corregido,$date_de_corte);
		$es_menor_o_igual_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
		
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");

		//comparacion fecha campo actual es inferior a la fecha de nacimiento
			$fecha_nacimiento_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),"1900-12-31");
			$campo_actual_inferior_fecha_nacimiento=$this->diferencia_dias_entre_fechas($campo_corregido,trim($array_fields[9]));
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_nacimiento_es_mayor_a_1900_12_31<0
			&& $fecha_nacimiento_es_mayor_a_1900_12_31!==false
			&& $campo_actual_inferior_fecha_nacimiento>0
			&& $campo_actual_inferior_fecha_nacimiento!==false
			)
			{
				$campo_corregido="1800-01-01";
			}//fin if
			//fin comparacion fecha campo actual es inferior a la fecha de nacimiento

			//limite inferior fecha corte 2 years
			$fecha_corte_menos_2_years="";
			$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=false;
			$array_fecha_corte=explode("-", $date_de_corte);
			if(count($array_fecha_corte)==3)
			{
				$fecha_corte_menos_2_years=(intval($array_fecha_corte[0])-2)."-".$array_fecha_corte[1]."-".$array_fecha_corte[2];
				$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=$this->diferencia_dias_entre_fechas($campo_corregido,$fecha_corte_menos_2_years);
			}//fin if
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_corte_menos_2_years!=""
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years!==false
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years>0
			)
			{
				$campo_corregido="1845-01-01";
			}//fin if
			//fin limite inferior fecha corte 2 years

		if($comparacion_a_1900_12_31<0 
					&& $excede_fecha_corte<0
					&& $comparacion_a_1900_12_31!==false
					&& $excede_fecha_corte!==false
					)
				{
					$campo_corregido="1800-01-01";
				}//fin comparacion excede la fecha de corte

		if($comparacion_a_1900_12_31>=0
			&& $campo_corregido!="1800-01-01"
			&& $campo_corregido!="1805-01-01"
			&& $campo_corregido!="1810-01-01"
			&& $campo_corregido!="1825-01-01"
			&& $campo_corregido!="1830-01-01"
			&& $campo_corregido!="1835-01-01"
			&& $campo_corregido!="1845-01-01"
			)
		{
			if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& $edad_dias>90
				)
			{
			    $campo_corregido="1845-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
			   && $edad_dias<=90
			   && ($array_fields[85]=="1" || $array_fields[85]=="2")
			   )
			{
			    $array_c9=explode("-",$array_fields[9]);
			    if(count($array_c9)==3 && checkdate($array_c9[1],$array_c9[2],$array_c9[0]) )
			    {
			    	$fecha = date_create(trim($array_fields[9]));
					//date sub resta, por lo cual al poner un dia negativo suma
				    date_sub($fecha, date_interval_create_from_date_string('-1 days'));
				    $c9_mas_un_dia="".date_format($fecha, 'Y-m-d');
				    $campo_corregido=$c9_mas_un_dia;

			    }//fin if verifica que sea una fecha
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& (trim($array_fields[85])=="0")
				&& $edad_dias<=90
				)
			{
			    $campo_corregido="1845-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				)
			{
			    $campo_corregido="1845-01-01";
			}
		}//fin else if
	     
		//$campo_corregido=$this->corrector_valor_permitido_fecha_criterio_002($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
			$nueva_edad_year_actual=-1;
			$nueva_edad_meses_actual=-1;
			$nueva_edad_dias_actual=-1;
			
			$array_fecha_nacimiento=explode("-",$array_fields[9]);
			$fecha_campo_actual=explode("-",$campo_corregido);
			if(count($fecha_campo_actual)==3 && checkdate($fecha_campo_actual[1],$fecha_campo_actual[2],$fecha_campo_actual[0]))
			{
			    $array_calc_edad_actual=$this->edad_years_months_days($array_fecha_nacimiento[2]."-".$array_fecha_nacimiento[1]."-".$array_fecha_nacimiento[0],$fecha_campo_actual[2]."-".$fecha_campo_actual[1]."-".$fecha_campo_actual[0]);
			    $nueva_edad_year_actual=intval($array_calc_edad_actual['y']);
			    $nueva_edad_meses_actual=(intval($array_calc_edad_actual['y'])*12)+$array_calc_edad_actual['m'];
			    $nueva_edad_dias_actual=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),trim($array_fields[$numero_campo_a_corregir]));
			}
			
			$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
			$es_menor_o_igual_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
			
			//calidad
			if($edad_dias>90
				&& $comparacion_a_1900_12_31<0
			)
			{
				//c1
			    $campo_corregido="1845-01-01";
			}
			else if($edad_dias>90
				
				&& ($campo_corregido=="1800-01-01"
				|| $campo_corregido=="1805-01-01"
				|| $campo_corregido=="1810-01-01"
				|| $campo_corregido=="1825-01-01"
				|| $campo_corregido=="1830-01-01"
				|| $campo_corregido=="1835-01-01")
			)//fin condicion
			{
			    $campo_corregido="1845-01-01";
			}
			else if($nueva_edad_dias_actual>2
				&& $edad_dias<=90
			   && $comparacion_a_1900_12_31<0
			   && $array_fields[85]!="0"
			)
			{
			    $array_c9=explode("-",$array_fields[9]);
			    if(count($array_c9)==3 && checkdate($array_c9[1],$array_c9[2],$array_c9[0]) )
			    {
			    	$fecha = date_create(trim($array_fields[9]));
					//date sub resta, por lo cual al poner un dia negativo suma
				    date_sub($fecha, date_interval_create_from_date_string('-1 days'));
				    $c9_mas_un_dia="".date_format($fecha, 'Y-m-d');
				    $campo_corregido=$c9_mas_un_dia;

			    }//fin if verifica que sea una fecha
			}//nueva 24/10/2017
			else if($comparacion_a_1900_12_31<0//NO MIRA CONTRA LA FECHA DE ACTIVIDAD LA EDAD
			   && $edad_dias<=90
			   && $array_fields[85]=="0"
			)
			{
			    $campo_corregido="1845-01-01";
			}
			else if($edad_dias<=90
				&& $campo_corregido=="1800-01-01"
				&& ($array_fields[85]=="0" || $array_fields[85]=="22")
			)
			{
			    $campo_corregido="1845-01-01";
			}
			else if($edad_dias<=90
				&& ($campo_corregido=="1805-01-01"
				|| $campo_corregido=="1810-01-01"
				|| $campo_corregido=="1825-01-01"
				|| $campo_corregido=="1830-01-01"
				|| $campo_corregido=="1835-01-01"
				|| $campo_corregido=="1845-01-01")
				&& (trim($array_fields[85])=="1" || trim($array_fields[85])=="2")
			)
			{
			    $campo_corregido="1800-01-01";
			}//fin calidad
			
		
		
	     }//fin fase
	    }//c84
	    
	    //campo a corregir es 85
	    if($numero_campo_a_corregir==85)
	    {
	     if($fase_correccion==0)
	     {
	     	//calculo edad con fecha actividad campo relacionado
			$nueva_edad_year_relacionado=-1;
			$nueva_edad_meses_relacionado=-1;
			$nueva_edad_dias_relacionado=-1;
			    
			$numero_campo_relacionado=84;
			$array_fecha_nacimiento=explode("-",trim($array_fields[9]) );
			$fecha_campo_relacionado=explode("-",trim($array_fields[$numero_campo_relacionado]) );
			if(count($fecha_campo_relacionado)==3 && checkdate($fecha_campo_relacionado[1],$fecha_campo_relacionado[2],$fecha_campo_relacionado[0]))
			{
			    
			    $array_calc_edad_relacionado=$this->edad_years_months_days($array_fecha_nacimiento[2]."-".$array_fecha_nacimiento[1]."-".$array_fecha_nacimiento[0],$fecha_campo_relacionado[2]."-".$fecha_campo_relacionado[1]."-".$fecha_campo_relacionado[0]);
			    $nueva_edad_year_relacionado=intval($array_calc_edad_relacionado['y']);
			    $nueva_edad_meses_relacionado=(intval($array_calc_edad_relacionado['y'])*12)+$array_calc_edad_relacionado['m'];
			    $nueva_edad_dias_relacionado=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),trim($array_fields[$numero_campo_relacionado]));

		    }//fin if 
		    //fin calculo edad fecha con actividad campo relacionado
		    
		    $fecha_calendario_c84=$this->diferencia_dias_entre_fechas(trim($array_fields[84]),"1900-12-31");
		    
		//campo en blanco
	     if($campo_corregido=="")
		{
			if($campo_corregido==""
				&& $edad_dias>90
			   )
			{
				//c1
			    $campo_corregido="0";
			}
			else if($campo_corregido==""
				&& ($array_fields[84]=="1800-01-01")
				&& $edad_dias<=90
			   )
			{
				//c2
			    $campo_corregido="0";
			}	
			else if($campo_corregido==""
				&& ($array_fields[84]=="1845-01-01"
			       || $array_fields[84]=="1835-01-01"
			       || $array_fields[84]=="1830-01-01"
			       || $array_fields[84]=="1825-01-01"
			       || $array_fields[84]=="1810-01-01"
			       || $array_fields[84]=="1805-01-01"
			       )
				&& $edad_dias<=90
			   )
			{
				//c3
			    $campo_corregido="0";
			}	
			else if($campo_corregido==""
			   && $fecha_calendario_c84<0
	    		&& $edad_dias<=90
			   )
			{
				//c4
			    $campo_corregido="22";
			}
			else if($campo_corregido=="")
			{
				//c5
			    $campo_corregido="0";
			}
			
		}//fin if
		
		//valor permitido
		if($campo_corregido!="0"
			&& $campo_corregido!="1"
			&& $campo_corregido!="2"
			&& $campo_corregido!="22"
		  )
		{
			if($campo_corregido!="0"
				&& $campo_corregido!="1"
				&& $campo_corregido!="2"
				&& $campo_corregido!="22"
				&& $edad_dias>90
			   )
			{
				//c1
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0"
				&& $campo_corregido!="1"
				&& $campo_corregido!="2"
				&& $campo_corregido!="22"
				&& ($array_fields[84]=="1800-01-01")
				&& $edad_dias<=90
			   )
			{
				//c2
			    $campo_corregido="0";
			}	
			else if($campo_corregido!="0"
				&& $campo_corregido!="1"
				&& $campo_corregido!="2"
				&& $campo_corregido!="22"
				&& ($array_fields[84]=="1845-01-01"
			       || $array_fields[84]=="1835-01-01"
			       || $array_fields[84]=="1830-01-01"
			       || $array_fields[84]=="1825-01-01"
			       || $array_fields[84]=="1810-01-01"
			       || $array_fields[84]=="1805-01-01"
			       )
				&& $edad_dias<=90
			   )
			{
				//c3
			    $campo_corregido="0";
			}	
			else if($campo_corregido!="0"
				&& $campo_corregido!="1"
				&& $campo_corregido!="2"
				&& $campo_corregido!="22"
			   && $fecha_calendario_c84<0
	    		&& $edad_dias<=90
			   )
			{
				//c4
			    $campo_corregido="22";
			}
			else if($campo_corregido!="0"
				&& $campo_corregido!="1"
				&& $campo_corregido!="2"
				&& $campo_corregido!="22"
			  )
			{
				//c6
			    $campo_corregido="0";
			}
		}//fin if
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_018($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {

	     	//calculo edad con fecha actividad campo relacionado
			$nueva_edad_year_relacionado=-1;
			$nueva_edad_meses_relacionado=-1;
			$nueva_edad_dias_relacionado=-1;
			    
			$numero_campo_relacionado=84;
			$array_fecha_nacimiento=explode("-",trim($array_fields[9]) );
			$fecha_campo_relacionado=explode("-",trim($array_fields[$numero_campo_relacionado]) );
			if(count($fecha_campo_relacionado)==3 && checkdate($fecha_campo_relacionado[1],$fecha_campo_relacionado[2],$fecha_campo_relacionado[0]))
			{
			    
			    $array_calc_edad_relacionado=$this->edad_years_months_days($array_fecha_nacimiento[2]."-".$array_fecha_nacimiento[1]."-".$array_fecha_nacimiento[0],$fecha_campo_relacionado[2]."-".$fecha_campo_relacionado[1]."-".$fecha_campo_relacionado[0]);
			    $nueva_edad_year_relacionado=intval($array_calc_edad_relacionado['y']);
			    $nueva_edad_meses_relacionado=(intval($array_calc_edad_relacionado['y'])*12)+$array_calc_edad_relacionado['m'];
			    $nueva_edad_dias_relacionado=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),trim($array_fields[$numero_campo_relacionado]));

		    }//fin if 
		    //fin calculo edad fecha con actividad campo relacionado
		    
		    $fecha_calendario_c84=$this->diferencia_dias_entre_fechas(trim($array_fields[84]),"1900-12-31");
		    

			//calidad c85

			if($campo_corregido!="0"
				&& $edad_dias>90
			   )
			{
				//c1
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0"
				&& ($array_fields[84]=="1845-01-01"
			       || $array_fields[84]=="1835-01-01"
			       || $array_fields[84]=="1830-01-01"
			       || $array_fields[84]=="1825-01-01"
			       || $array_fields[84]=="1810-01-01"
			       || $array_fields[84]=="1805-01-01"
			       )
				&& $edad_dias<=90
			   )
			{
				//c3
			    $campo_corregido="0";
			}
			else if($campo_corregido=="22" 
				&& ($array_fields[84]=="1800-01-01")
				&& $edad_dias<=90
			   )
			{
				//c2
			    $campo_corregido="0";
			}				
			else if($campo_corregido=="0"
			   && $fecha_calendario_c84<0
	    		&& $edad_dias<=90
			   )
			{
				//c4
			    $campo_corregido="22";
			}//fin calidad

	     	
			
	     }//fin fase
	    }//c85
	    
	    
	    //campo a corregir es 105
	    if($numero_campo_a_corregir==105)
	    {
	     
     	
	     if($fase_correccion==0)
	     {
			//campo en blanco
			if($campo_corregido=="")
			{
		     	if($campo_corregido==""
				   && $array_fields[14]=="1"
				   )
				{
				    $campo_corregido="1835-01-01";
				}
				else if($campo_corregido==""
					&& $array_fields[14]!="1"
				   && ($edad==50
				       || $edad==55
				       || $edad==60
				       || $edad==65
				       || $edad==70
				       || $edad==75
				       || $edad==80
				       || $edad==85
				       || $edad==90
				       || $edad==95
				       || $edad==100
				       || $edad==105
				       || $edad==110
				       || $edad==115
				       || $edad==120
				       )
				   )
				{
				    $campo_corregido="1835-01-01";
				}
				else if($campo_corregido=="")
				{
				    $campo_corregido="1845-01-01";
				}
			}//fin if
		
			//formato
			$corrige_formato=false;
			$array_fecha_campo_actual=explode("-", $campo_corregido);
			if(count($array_fecha_campo_actual)==3)
			{
				if(ctype_digit($array_fecha_campo_actual[0]) && ctype_digit($array_fecha_campo_actual[1]) && ctype_digit($array_fecha_campo_actual[2]))
				{
					//checkdate mm-dd-aaaa -> aaaa-mm-dd ?
					if(checkdate($array_fecha_campo_actual[1],$array_fecha_campo_actual[2],$array_fecha_campo_actual[0])
				       && intval($array_fecha_campo_actual[0])>=32)
				    {
				    	//no se corrige formato
				    }
				    else
				    {
				    	$corrige_formato=true;
				    }

				}
				else
				{
					$corrige_formato=true;
				}
			}
			else
			{
				$corrige_formato=true;
			}//fin else

			if($corrige_formato==true)
			{
			$campo_corregido=$this->corrector_formato_fecha($campo_corregido);
			}
		
			//valor permitido
			$excede_fecha_corte=$this->diferencia_dias_entre_fechas($campo_corregido,$date_de_corte);
			$es_menor_o_igual_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
			$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
			
			$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");

			//comparacion fecha campo actual es inferior a la fecha de nacimiento
			$fecha_nacimiento_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),"1900-12-31");
			$campo_actual_inferior_fecha_nacimiento=$this->diferencia_dias_entre_fechas($campo_corregido,trim($array_fields[9]));
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_nacimiento_es_mayor_a_1900_12_31<0
			&& $fecha_nacimiento_es_mayor_a_1900_12_31!==false
			&& $campo_actual_inferior_fecha_nacimiento>0
			&& $campo_actual_inferior_fecha_nacimiento!==false
			)
			{
				$campo_corregido="1800-01-01";
			}//fin if
			//fin comparacion fecha campo actual es inferior a la fecha de nacimiento

			//limite inferior fecha corte 2 years
			$fecha_corte_menos_2_years="";
			$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=false;
			$array_fecha_corte=explode("-", $date_de_corte);
			if(count($array_fecha_corte)==3)
			{
				$fecha_corte_menos_2_years=(intval($array_fecha_corte[0])-2)."-".$array_fecha_corte[1]."-".$array_fecha_corte[2];
				$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=$this->diferencia_dias_entre_fechas($campo_corregido,$fecha_corte_menos_2_years);
			}//fin if
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_corte_menos_2_years!=""
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years!==false
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years>0
			)
			{
				$campo_corregido="1845-01-01";
			}//fin if
			//fin limite inferior fecha corte 2 years

			if($comparacion_a_1900_12_31<0 
					&& $excede_fecha_corte<0
					&& $comparacion_a_1900_12_31!==false
					&& $excede_fecha_corte!==false
					)
				{
					$campo_corregido="1800-01-01";
				}//fin comparacion excede la fecha de corte
			
			if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				)
			{
				if($comparacion_a_1900_12_31>=0
					&& $campo_corregido!="1800-01-01"
					&& $campo_corregido!="1805-01-01"
					&& $campo_corregido!="1810-01-01"
					&& $campo_corregido!="1825-01-01"
					&& $campo_corregido!="1830-01-01"
					&& $campo_corregido!="1835-01-01"
					&& $campo_corregido!="1845-01-01"
				   && $array_fields[14]=="1"
				   )
				{
				    $campo_corregido="1835-01-01";
				}
				else if($comparacion_a_1900_12_31>=0
					&& $campo_corregido!="1800-01-01"
					&& $campo_corregido!="1805-01-01"
					&& $campo_corregido!="1810-01-01"
					&& $campo_corregido!="1825-01-01"
					&& $campo_corregido!="1830-01-01"
					&& $campo_corregido!="1835-01-01"
					&& $campo_corregido!="1845-01-01"
					&& $array_fields[14]!="1"
				   && ($edad==50
				       || $edad==55
				       || $edad==60
				       || $edad==65
				       || $edad==70
				       || $edad==75
				       || $edad==80
				       || $edad==85
				       || $edad==90
				       || $edad==95
				       || $edad==100
				       || $edad==105
				       || $edad==110
				       || $edad==115
				       || $edad==120
				       )
				   )
				{
				    $campo_corregido="1835-01-01";
				}
				else if($comparacion_a_1900_12_31>=0
					&& $campo_corregido!="1800-01-01"
					&& $campo_corregido!="1805-01-01"
					&& $campo_corregido!="1810-01-01"
					&& $campo_corregido!="1825-01-01"
					&& $campo_corregido!="1830-01-01"
					&& $campo_corregido!="1835-01-01"
					&& $campo_corregido!="1845-01-01"
					)
				{
				    $campo_corregido="1845-01-01";
				}
			}//fin else if
		
		//$campo_corregido=$this->corrector_valor_permitido_fecha_criterio_002($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
	     	if(
	     		($campo_corregido=="1845-01-01"
     	 			|| $campo_corregido=="1800-01-01"
     	 			)
				 && $array_fields[10]=="M"
			   && ($edad==50
			       || $edad==55
			       || $edad==60
			       || $edad==65
			       || $edad==70
			       || $edad==75
			       || $edad==80
			       || $edad==85
			       || $edad==90
			       || $edad==95
			       || $edad==100
			       || $edad==105
			       || $edad==110
			       || $edad==115
			       || $edad==120
			       )
			   )
			{
			    $campo_corregido="1835-01-01";
			}
			else if(
	     		(trim($array_fields[105])=="1800-01-01"
	     			|| trim($array_fields[105])=="1805-01-01"
			    		|| trim($array_fields[105])=="1810-01-01"
			    		|| trim($array_fields[105])=="1825-01-01"
			    		|| trim($array_fields[105])=="1830-01-01"
			    		|| trim($array_fields[105])=="1835-01-01"
			    		)
				    	&& trim($array_fields[10])=="M"
				    && (
					    	(intval($edad)!=50 )
					     && (intval($edad)!=55 ) 
					     && (intval($edad)!=60 ) 
					     && (intval($edad)!=65 ) 
					    && (intval($edad)!=70 ) 
					    && (intval($edad)!=75 ) 
					    && (intval($edad)!=80 ) 
					    && (intval($edad)!=85 )
					    && (intval($edad)!=90 ) 
					    && (intval($edad)!=95 ) 
					    && (intval($edad)!=100 )
					    && (intval($edad)!=105 )
					    && (intval($edad)!=110 )
					    && (intval($edad)!=115 ) 
					    && (intval($edad)!=120 )
					    )
			   )
			{
			    $campo_corregido="1845-01-01";
			}
     	 	else if(
     	 		($campo_corregido=="1845-01-01"
     	 			|| $campo_corregido=="1800-01-01"
     	 			)
     	 		&& $array_fields[10]=="F"
			   && $array_fields[14]=="1"
			   )
			{
			    $campo_corregido="1835-01-01";
			}
			else if(

				($campo_corregido=="1845-01-01"
     	 			|| $campo_corregido=="1800-01-01"
     	 			)
				 && $array_fields[14]!="1"
				 && $array_fields[10]=="F"
			   && ($edad==50
			       || $edad==55
			       || $edad==60
			       || $edad==65
			       || $edad==70
			       || $edad==75
			       || $edad==80
			       || $edad==85
			       || $edad==90
			       || $edad==95
			       || $edad==100
			       || $edad==105
			       || $edad==110
			       || $edad==115
			       || $edad==120
			       )
			   )
			{
			    $campo_corregido="1835-01-01";
			}
			else  if(

				(trim($array_fields[105])=="1800-01-01"
					|| trim($array_fields[105])=="1805-01-01"
			    		|| trim($array_fields[105])=="1810-01-01"
			    		|| trim($array_fields[105])=="1825-01-01"
			    		|| trim($array_fields[105])=="1830-01-01"
			    		|| trim($array_fields[105])=="1835-01-01"
			    		)
				    	&& trim($array_fields[10])=="F"
				    	&& trim($array_fields[14])!="1"
				    && (
					    	(intval($edad)!=50 )
					     && (intval($edad)!=55 ) 
					     && (intval($edad)!=60 ) 
					     && (intval($edad)!=65 ) 
					    && (intval($edad)!=70 ) 
					    && (intval($edad)!=75 ) 
					    && (intval($edad)!=80 ) 
					    && (intval($edad)!=85 )
					    && (intval($edad)!=90 ) 
					    && (intval($edad)!=95 ) 
					    && (intval($edad)!=100 )
					    && (intval($edad)!=105 )
					    && (intval($edad)!=110 )
					    && (intval($edad)!=115 ) 
					    && (intval($edad)!=120 )
					    )
			   )
			{
			    $campo_corregido="1845-01-01";
			}
		
	     }//fin fase 1
	    }//fin campo 105
	    
	    //campo a corregir es 106
	    if($numero_campo_a_corregir==106)
	    {
	     
	     if($fase_correccion==0)
	     {
		//campo en blanco
     	if($campo_corregido=="")
		{
			if($campo_corregido==""
			   && floatval($array_fields[107])>=0.15
			   && floatval($array_fields[107])<=37
			   )
			{
			    $campo_corregido="1800-01-01";
			}
			else if($campo_corregido==""			
			   && (floatval($array_fields[107])<0.15
			   || floatval($array_fields[107])>37)
			   && ($edad==50
			       || $edad==55
			       || $edad==60
			       || $edad==65
			       || $edad==70
			       || $edad==75
			       || $edad==80
			       || $edad==85
			       || $edad==90
			       || $edad==95
			       || $edad==100
			       || $edad==105
			       || $edad==110
			       || $edad==115
			       || $edad==120
			       )
			   )
			{
			    $campo_corregido="1835-01-01";
			}
			else if($campo_corregido==""			
			   && (floatval($array_fields[107])<0.15
			   || floatval($array_fields[107])>37)
			   && ( $edad!=50
			       && $edad!=55
			       && $edad!=60
			       && $edad!=65
			       && $edad!=70
			       && $edad!=75
			       && $edad!=80
			       && $edad!=85
			       && $edad!=90
			       && $edad!=95
			       && $edad!=100
			       && $edad!=105
			       && $edad!=110
			       && $edad!=115
			       && $edad!=120
			       )
			   )
			{
			    $campo_corregido="1845-01-01";
			}
			else if($campo_corregido=="")
			{
			    $campo_corregido="1845-01-01";
			}
		}//fin if
		
		//formato
		$corrige_formato=false;
		$array_fecha_campo_actual=explode("-", $campo_corregido);
		if(count($array_fecha_campo_actual)==3)
		{
			if(ctype_digit($array_fecha_campo_actual[0]) && ctype_digit($array_fecha_campo_actual[1]) && ctype_digit($array_fecha_campo_actual[2]))
			{
				//checkdate mm-dd-aaaa -> aaaa-mm-dd ?
				if(checkdate($array_fecha_campo_actual[1],$array_fecha_campo_actual[2],$array_fecha_campo_actual[0])
			       && intval($array_fecha_campo_actual[0])>=32)
			    {
			    	//no se corrige formato
			    }
			    else
			    {
			    	$corrige_formato=true;
			    }

			}
			else
			{
				$corrige_formato=true;
			}
		}
		else
		{
			$corrige_formato=true;
		}//fin else

		if($corrige_formato==true)
		{
		$campo_corregido=$this->corrector_formato_fecha($campo_corregido);
		}
		
		//valor permitido
		$excede_fecha_corte=$this->diferencia_dias_entre_fechas($campo_corregido,$date_de_corte);
		$es_menor_o_igual_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
		
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");

		//comparacion fecha campo actual es inferior a la fecha de nacimiento
			$fecha_nacimiento_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),"1900-12-31");
			$campo_actual_inferior_fecha_nacimiento=$this->diferencia_dias_entre_fechas($campo_corregido,trim($array_fields[9]));
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_nacimiento_es_mayor_a_1900_12_31<0
			&& $fecha_nacimiento_es_mayor_a_1900_12_31!==false
			&& $campo_actual_inferior_fecha_nacimiento>0
			&& $campo_actual_inferior_fecha_nacimiento!==false
			)
			{
				$campo_corregido="1800-01-01";
			}//fin if
			//fin comparacion fecha campo actual es inferior a la fecha de nacimiento

			//limite inferior fecha corte 2 years
			$fecha_corte_menos_2_years="";
			$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=false;
			$array_fecha_corte=explode("-", $date_de_corte);
			if(count($array_fecha_corte)==3)
			{
				$fecha_corte_menos_2_years=(intval($array_fecha_corte[0])-2)."-".$array_fecha_corte[1]."-".$array_fecha_corte[2];
				$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=$this->diferencia_dias_entre_fechas($campo_corregido,$fecha_corte_menos_2_years);
			}//fin if
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_corte_menos_2_years!=""
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years!==false
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years>0
			)
			{
				$campo_corregido="1845-01-01";
			}//fin if
			//fin limite inferior fecha corte 2 years

		if($comparacion_a_1900_12_31<0 
					&& $excede_fecha_corte<0
					&& $comparacion_a_1900_12_31!==false
					&& $excede_fecha_corte!==false
					)
				{
					$campo_corregido="1800-01-01";
				}//fin comparacion excede la fecha de corte

		if($comparacion_a_1900_12_31>=0
			&& $campo_corregido!="1800-01-01"
			&& $campo_corregido!="1805-01-01"
			&& $campo_corregido!="1810-01-01"
			&& $campo_corregido!="1825-01-01"
			&& $campo_corregido!="1830-01-01"
			&& $campo_corregido!="1835-01-01"
			&& $campo_corregido!="1845-01-01"
			)
		{
			if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& floatval($array_fields[107])>=0.15
			    && floatval($array_fields[107])<=37
				)
			{
			    $campo_corregido="1800-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& ($edad==50
			       || $edad==55
			       || $edad==60
			       || $edad==65
			       || $edad==70
			       || $edad==75
			       || $edad==80
			       || $edad==85
			       || $edad==90
			       || $edad==95
			       || $edad==100
			       || $edad==105
			       || $edad==110
			       || $edad==115
			       || $edad==120
			       )
				&& (floatval($array_fields[107])<0.15
			   || floatval($array_fields[107])>37)
				
				)
			{
			    $campo_corregido="1835-01-01";
			}
			else if(
				$comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& ( $edad!=50
			       && $edad!=55
			       && $edad!=60
			       && $edad!=65
			       && $edad!=70
			       && $edad!=75
			       && $edad!=80
			       && $edad!=85
			       && $edad!=90
			       && $edad!=95
			       && $edad!=100
			       && $edad!=105
			       && $edad!=110
			       && $edad!=115
			       && $edad!=120
			       )
				&& (floatval($array_fields[107])<0.15
			   || floatval($array_fields[107])>37)
				
				)
			{
			    $campo_corregido="1845-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& $array_fields[107]==""
				)
			{
			    $campo_corregido="1845-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				)
			{
			    $campo_corregido="1845-01-01";
			}
		}//fin else if
		
	     
		//$campo_corregido=$this->corrector_valor_permitido_fecha_criterio_002($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
			$fecha_calendario_contra_campo_actual=$this->diferencia_dias_entre_fechas(trim($array_fields[106]),"1900-12-31");
				    
			//calidad
			if(
						(
						$campo_corregido=="1800-01-01"
				       )

						&& (floatval($array_fields[107])<0.15
				   || floatval($array_fields[107])>37)
						
					&& ($edad==50
			       || $edad==55
			       || $edad==60
			       || $edad==65
			       || $edad==70
			       || $edad==75
			       || $edad==80
			       || $edad==85
			       || $edad==90
			       || $edad==95
			       || $edad==100
			       || $edad==105
			       || $edad==110
			       || $edad==115
			       || $edad==120
			       )
			   
			   
			   )
			{
				//c1
			    $campo_corregido="1835-01-01";
			}
			else if($campo_corregido=="1800-01-01"
				&& ( $edad!=50
			       && $edad!=55
			       && $edad!=60
			       && $edad!=65
			       && $edad!=70
			       && $edad!=75
			       && $edad!=80
			       && $edad!=85
			       && $edad!=90
			       && $edad!=95
			       && $edad!=100
			       && $edad!=105
			       && $edad!=110
			       && $edad!=115
			       && $edad!=120
			       )
			   	&& (floatval($array_fields[107])<0.15
				   || floatval($array_fields[107])>37)
			   
			   )
			{
				//c2
			    $campo_corregido="1845-01-01";
			}
			else if(($campo_corregido=="1805-01-01"
			    || $campo_corregido=="1810-01-01"
			    || $campo_corregido=="1825-01-01"
			    || $campo_corregido=="1830-01-01"
			    || $campo_corregido=="1835-01-01"
			    || $campo_corregido=="1845-01-01"
			    )
			  && floatval($array_fields[107])>=0.15
				    && floatval($array_fields[107])<=37
			   )
			{
				//c3
			    $campo_corregido="1800-01-01";
			}
			else if(
						(
						$campo_corregido!="1805-01-01"
						&& $campo_corregido!="1810-01-01"
						&& $campo_corregido!="1825-01-01"
						&& $campo_corregido!="1830-01-01"
						&& $campo_corregido!="1835-01-01"
				       )

						&& (floatval($array_fields[107])<0.15
				   || floatval($array_fields[107])>37)
						
					&& ($edad==50
			       || $edad==55
			       || $edad==60
			       || $edad==65
			       || $edad==70
			       || $edad==75
			       || $edad==80
			       || $edad==85
			       || $edad==90
			       || $edad==95
			       || $edad==100
			       || $edad==105
			       || $edad==110
			       || $edad==115
			       || $edad==120
			       )
			   
			   
			   )
			{
				//c4
			    $campo_corregido="1835-01-01";
			}
			else if($campo_corregido!="1845-01-01"
				&& ( $edad!=50
			       && $edad!=55
			       && $edad!=60
			       && $edad!=65
			       && $edad!=70
			       && $edad!=75
			       && $edad!=80
			       && $edad!=85
			       && $edad!=90
			       && $edad!=95
			       && $edad!=100
			       && $edad!=105
			       && $edad!=110
			       && $edad!=115
			       && $edad!=120
			       )
			   	&& (floatval($array_fields[107])<0.15
				   || floatval($array_fields[107])>37)
			   
			   )
			{
				//c5
			    $campo_corregido="1845-01-01";
			}
		
	     }//fin fase
	    }//fin campo 106
	    
	    //campo a corregir es 107
	    if($numero_campo_a_corregir==107)
	    {
	     if($fase_correccion==0)
	     {
		$fecha_calendario_c106=$this->diferencia_dias_entre_fechas(trim($array_fields[106]),"1900-12-31");
		
		//campo en blanco	
		if( $campo_corregido==""  )
		{	
			if( $campo_corregido==""
			    &&(
						trim($array_fields[106])!="1805-01-01"
						&& trim($array_fields[106])!="1810-01-01"
						&& trim($array_fields[106])!="1825-01-01"
						&& trim($array_fields[106])!="1830-01-01"
						&& trim($array_fields[106])!="1835-01-01"
						&& trim($array_fields[106])!="1845-01-01"
				       )
			    )//fin if
			{
			    $campo_corregido="999";
			}
			else if( $campo_corregido==""
			    &&($array_fields[106]=="1845-01-01"
			    || $array_fields[106]=="1835-01-01"
			    || $array_fields[106]=="1830-01-01"
			    || $array_fields[106]=="1825-01-01"
			    || $array_fields[106]=="1810-01-01"
			    || $array_fields[106]=="1805-01-01")
			    
			    )
			{
			    $campo_corregido="0";
			}
			else if( $campo_corregido==""  )
			{
			    $campo_corregido="0";
			}
		}//fin if
		
		//formato
		$campo_corregido=trim($campo_corregido);
		$campo_corregido=str_replace(",",".",$campo_corregido);
		$campo_corregido=floatval($campo_corregido);
		if(!ctype_digit($campo_corregido) && is_numeric($campo_corregido))
		{
		    $array_num_decimal=explode(".",trim($campo_corregido));
		    if(count($array_num_decimal)>1)
		    {
			if(strlen($array_num_decimal[0])>1 && strlen($array_num_decimal[1])>1)
			{
			    $campo_corregido="".round(floatval($campo_corregido),1,PHP_ROUND_HALF_UP);
			}
			else if(strlen($array_num_decimal[0])==1 && strlen($array_num_decimal[1])>2)
			{
			    $campo_corregido="".round(floatval($campo_corregido),2,PHP_ROUND_HALF_UP);
			}
		    }//fin if hya posiciones decimales
		}
		
		//valor permitido
		if($campo_corregido!="0" && $campo_corregido!="999"
		   && (floatval($campo_corregido)<0.15 || floatval($campo_corregido)>37 || is_numeric($campo_corregido)==false) 
		   &&(
						trim($array_fields[106])!="1805-01-01"
						&& trim($array_fields[106])!="1810-01-01"
						&& trim($array_fields[106])!="1825-01-01"
						&& trim($array_fields[106])!="1830-01-01"
						&& trim($array_fields[106])!="1835-01-01"
						&& trim($array_fields[106])!="1845-01-01"
				       )
		   )
		{
		    $campo_corregido="999";
		}
		else if($campo_corregido!="0" && $campo_corregido!="999"
			&& (floatval($campo_corregido)<0.15 || floatval($campo_corregido)>37 || is_numeric($campo_corregido)==false) 
			&&($array_fields[106]=="1845-01-01"
			    || $array_fields[106]=="1835-01-01"
			    || $array_fields[106]=="1830-01-01"
			    || $array_fields[106]=="1825-01-01"
			    || $array_fields[106]=="1810-01-01"
			    || $array_fields[106]=="1805-01-01")
		 )
		{
		    $campo_corregido="0";
		}
		else if($campo_corregido!="0" && $campo_corregido!="999"
			&& (floatval($campo_corregido)<0.15 || floatval($campo_corregido)>37 || is_numeric($campo_corregido)==false)

		 )
		{
		    $campo_corregido="0";
		}
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_040($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
		$fecha_calendario_c106=$this->diferencia_dias_entre_fechas(trim($array_fields[106]),"1900-12-31");
			//calidad
			if( $campo_corregido=="0"
			    &&(
					trim($array_fields[106])!="1805-01-01"
					&& trim($array_fields[106])!="1810-01-01"
					&& trim($array_fields[106])!="1825-01-01"
					&& trim($array_fields[106])!="1830-01-01"
					&& trim($array_fields[106])!="1835-01-01"
					&& trim($array_fields[106])!="1845-01-01"
			       )
			    )//fin if
			{
			    $campo_corregido="999";
			}
			else if( $campo_corregido!="0"
			    &&($array_fields[106]=="1845-01-01"
			    || $array_fields[106]=="1835-01-01"
			    || $array_fields[106]=="1830-01-01"
			    || $array_fields[106]=="1825-01-01"
			    || $array_fields[106]=="1810-01-01"
			    || $array_fields[106]=="1805-01-01")
			    
			    )
			{
			    $campo_corregido="0";
			}
		
	     }//fin fase 1
	    }//fin campo 107
	    
	    //campo a corregir es 108
	    if($numero_campo_a_corregir==108)
	    {
	     if($fase_correccion==0)
	     {
		//campo en blanco
     	if($campo_corregido=="" )
		{
			if($campo_corregido==""
				&& ($array_fields[109]!="0")
				
				)
			{
			    $campo_corregido="1800-01-01";
			}
			else if($campo_corregido==""			
				&& ($array_fields[109]=="0")
				&& ($edad!=50
			       && $edad!=55
			       && $edad!=60
			       && $edad!=65
			       && $edad!=70
			       && $edad!=75
			       && $edad!=80
			       && $edad!=85
			       && $edad!=90
			       && $edad!=95
			       && $edad!=100
			       && $edad!=105
			       && $edad!=110
			       && $edad!=115
			       && $edad!=120
			       )
				)
			{
			    $campo_corregido="1845-01-01";
			}
			else if($campo_corregido==""			
				&& ($array_fields[109]=="0")
				&& ($edad==50
			       || $edad==55
			       || $edad==60
			       || $edad==65
			       || $edad==70
			       || $edad==75
			       || $edad==80
			       || $edad==85
			       || $edad==90
			       || $edad==95
			       || $edad==100
			       || $edad==105
			       || $edad==110
			       || $edad==115
			       || $edad==120
			       )
				)
			{
			    $campo_corregido="1835-01-01";
			}
			else if($campo_corregido=="")
			{
			    $campo_corregido="1845-01-01";
			}
			
			
		}//fin if
		
		//formato
		$corrige_formato=false;
		$array_fecha_campo_actual=explode("-", $campo_corregido);
		if(count($array_fecha_campo_actual)==3)
		{
			if(ctype_digit($array_fecha_campo_actual[0]) && ctype_digit($array_fecha_campo_actual[1]) && ctype_digit($array_fecha_campo_actual[2]))
			{
				//checkdate mm-dd-aaaa -> aaaa-mm-dd ?
				if(checkdate($array_fecha_campo_actual[1],$array_fecha_campo_actual[2],$array_fecha_campo_actual[0])
			       && intval($array_fecha_campo_actual[0])>=32)
			    {
			    	//no se corrige formato
			    }
			    else
			    {
			    	$corrige_formato=true;
			    }

			}
			else
			{
				$corrige_formato=true;
			}
		}
		else
		{
			$corrige_formato=true;
		}//fin else

		if($corrige_formato==true)
		{
		$campo_corregido=$this->corrector_formato_fecha($campo_corregido,false,-2);
		}
		
		//valor permitido
		$excede_fecha_corte=$this->diferencia_dias_entre_fechas($campo_corregido,$date_de_corte);
		$es_menor_o_igual_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");

		//comparacion fecha campo actual es inferior a la fecha de nacimiento
			$fecha_nacimiento_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),"1900-12-31");
			$campo_actual_inferior_fecha_nacimiento=$this->diferencia_dias_entre_fechas($campo_corregido,trim($array_fields[9]));
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_nacimiento_es_mayor_a_1900_12_31<0
			&& $fecha_nacimiento_es_mayor_a_1900_12_31!==false
			&& $campo_actual_inferior_fecha_nacimiento>0
			&& $campo_actual_inferior_fecha_nacimiento!==false
			)
			{
				$campo_corregido="1800-01-01";
			}//fin if
			//fin comparacion fecha campo actual es inferior a la fecha de nacimiento

			//limite inferior fecha corte 2 years
			$fecha_corte_menos_2_years="";
			$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=false;
			$array_fecha_corte=explode("-", $date_de_corte);
			if(count($array_fecha_corte)==3)
			{
				$fecha_corte_menos_2_years=(intval($array_fecha_corte[0])-2)."-".$array_fecha_corte[1]."-".$array_fecha_corte[2];
				$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=$this->diferencia_dias_entre_fechas($campo_corregido,$fecha_corte_menos_2_years);
			}//fin if
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_corte_menos_2_years!=""
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years!==false
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years>0
			)
			{
				$campo_corregido="1845-01-01";
			}//fin if
			//fin limite inferior fecha corte 2 years

		if($comparacion_a_1900_12_31<0 
					&& $excede_fecha_corte<0
					&& $comparacion_a_1900_12_31!==false
					&& $excede_fecha_corte!==false
					)
				{
					$campo_corregido="1800-01-01";
				}//fin comparacion excede la fecha de corte
		
		if($comparacion_a_1900_12_31>=0
			&& $campo_corregido!="1800-01-01"
			&& $campo_corregido!="1805-01-01"
			&& $campo_corregido!="1810-01-01"
			&& $campo_corregido!="1825-01-01"
			&& $campo_corregido!="1830-01-01"
			&& $campo_corregido!="1835-01-01"
			&& $campo_corregido!="1845-01-01"			
			)
		{
			if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& ($array_fields[109]!="0")
				
				)
			{
			    $campo_corregido="1800-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"		
				&& ($array_fields[109]=="0")
				&& ($edad!=50
			       && $edad!=55
			       && $edad!=60
			       && $edad!=65
			       && $edad!=70
			       && $edad!=75
			       && $edad!=80
			       && $edad!=85
			       && $edad!=90
			       && $edad!=95
			       && $edad!=100
			       && $edad!=105
			       && $edad!=110
			       && $edad!=115
			       && $edad!=120
			       )
				)
			{
			    $campo_corregido="1845-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"		
				&& ($array_fields[109]=="0")
				&& ($edad==50
			       || $edad==55
			       || $edad==60
			       || $edad==65
			       || $edad==70
			       || $edad==75
			       || $edad==80
			       || $edad==85
			       || $edad==90
			       || $edad==95
			       || $edad==100
			       || $edad==105
			       || $edad==110
			       || $edad==115
			       || $edad==120
			       )
				)
			{
			    $campo_corregido="1835-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"			
				)
			{
			    $campo_corregido="1845-01-01";
			}
		}//fin else if
	     
		//$campo_corregido=$this->corrector_valor_permitido_fecha_criterio_002($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
	     	$fecha_calendario_c108=$this->diferencia_dias_entre_fechas(trim($array_fields[108]),"1900-12-31");


			//calidad
			if(
				($campo_corregido=="1805-01-01"
			    || $campo_corregido=="1810-01-01"
			    || $campo_corregido=="1825-01-01"
			    || $campo_corregido=="1830-01-01"
			    || $campo_corregido=="1835-01-01"
			    || $campo_corregido=="1845-01-01"
			    )	   
				&& ($array_fields[109]!="0")
				
				)
			{
			    $campo_corregido="1800-01-01";
			}
			else if(
			($campo_corregido=="1805-01-01"
			    || $campo_corregido=="1810-01-01"
			    || $campo_corregido=="1825-01-01"
			    || $campo_corregido=="1830-01-01"
			    || $campo_corregido=="1835-01-01"
			    || $campo_corregido=="1800-01-01"
			    )	   			
				&& ($array_fields[109]=="0")
				&& ($edad!=50
			       && $edad!=55
			       && $edad!=60
			       && $edad!=65
			       && $edad!=70
			       && $edad!=75
			       && $edad!=80
			       && $edad!=85
			       && $edad!=90
			       && $edad!=95
			       && $edad!=100
			       && $edad!=105
			       && $edad!=110
			       && $edad!=115
			       && $edad!=120
			       )
				)
			{
			    $campo_corregido="1845-01-01";
			}
			else if(
			$fecha_calendario_c108<0	
				&& ($array_fields[109]=="0")
				&& ($edad!=50
			       && $edad!=55
			       && $edad!=60
			       && $edad!=65
			       && $edad!=70
			       && $edad!=75
			       && $edad!=80
			       && $edad!=85
			       && $edad!=90
			       && $edad!=95
			       && $edad!=100
			       && $edad!=105
			       && $edad!=110
			       && $edad!=115
			       && $edad!=120
			       )
				)
			{
			    $campo_corregido="1845-01-01";
			}
			else if($fecha_calendario_c108<0			
				&& ($array_fields[109]=="0")
				&& ($edad==50
			       || $edad==55
			       || $edad==60
			       || $edad==65
			       || $edad==70
			       || $edad==75
			       || $edad==80
			       || $edad==85
			       || $edad==90
			       || $edad==95
			       || $edad==100
			       || $edad==105
			       || $edad==110
			       || $edad==115
			       || $edad==120
			       )
				)
			{
			    $campo_corregido="1835-01-01";
			}
			else if(
					($campo_corregido!="1805-01-01"
			    && $campo_corregido!="1810-01-01"
			    && $campo_corregido!="1825-01-01"
			    && $campo_corregido!="1830-01-01"
			    && $campo_corregido!="1835-01-01"
			    
			    )	   
				&& ($array_fields[109]=="0")
				&& ($edad==50
			       || $edad==55
			       || $edad==60
			       || $edad==65
			       || $edad==70
			       || $edad==75
			       || $edad==80
			       || $edad==85
			       || $edad==90
			       || $edad==95
			       || $edad==100
			       || $edad==105
			       || $edad==110
			       || $edad==115
			       || $edad==120
			       )
				)
			{
			    $campo_corregido="1835-01-01";
			}//fin calidad

			

			
				
		
	     }//fin fase
	    }//fin campo 108
	    
	    //campo a corregir es 109
	    if($numero_campo_a_corregir==109)
	    {
	     if($fase_correccion==0)
	     {
	     	$fecha_calendario_c108=$this->diferencia_dias_entre_fechas(trim($array_fields[108]),"1900-12-31");
			
		//campo en blanco	
		if( $campo_corregido=="" )
		{	
			if( $campo_corregido==""
			    && (

					 trim($array_fields[108])!="1805-01-01"
					&& trim($array_fields[108])!="1810-01-01"
					&& trim($array_fields[108])!="1825-01-01"
					&& trim($array_fields[108])!="1830-01-01"
					&& trim($array_fields[108])!="1835-01-01"
					&& trim($array_fields[108])!="1845-01-01"
		    		)
			    )
			{
			    $campo_corregido="999";
			}
			else if( $campo_corregido==""
			    &&($array_fields[108]=="1845-01-01"
			    || $array_fields[108]=="1835-01-01"
			    || $array_fields[108]=="1830-01-01"
			    || $array_fields[108]=="1825-01-01"
			    || $array_fields[108]=="1810-01-01"
			    || $array_fields[108]=="1805-01-01")
			    
			    )
			{
			    $campo_corregido="0";
			}
			else if( $campo_corregido=="" )
			{
			    $campo_corregido="0";
			}
		}//fin if
		
		//formato
		$campo_corregido=trim($campo_corregido);
		$campo_corregido=str_replace(",",".",$campo_corregido);
		$campo_corregido=floatval($campo_corregido);
		if(!ctype_digit($campo_corregido) && is_numeric($campo_corregido))
		{
		    $array_num_decimal=explode(".",trim($campo_corregido));
		    if(count($array_num_decimal)>1)
		    {
			if(strlen($array_num_decimal[0])>1 && strlen($array_num_decimal[1])>1)
			{
			    $campo_corregido="".round(floatval($campo_corregido),1,PHP_ROUND_HALF_UP);
			}
			else if(strlen($array_num_decimal[0])==1 && strlen($array_num_decimal[1])>2)
			{
			    $campo_corregido="".round(floatval($campo_corregido),2,PHP_ROUND_HALF_UP);
			}
		    }//fin if
		}
		
			//valor permitido
			if($campo_corregido!="0"
				&& $campo_corregido!="999"
			   		&& ( floatval($campo_corregido)<3.5 || floatval($campo_corregido)>20 || is_numeric($campo_corregido)==false)
			   		&& (

						 trim($array_fields[108])!="1805-01-01"
						&& trim($array_fields[108])!="1810-01-01"
						&& trim($array_fields[108])!="1825-01-01"
						&& trim($array_fields[108])!="1830-01-01"
						&& trim($array_fields[108])!="1835-01-01"
						&& trim($array_fields[108])!="1845-01-01"
			    		)
			    )
			{
			    $campo_corregido="999";
			}
			else if( $campo_corregido!="0"
				&& $campo_corregido!="999"
			   		&& ( floatval($campo_corregido)<3.5 || floatval($campo_corregido)>20 || is_numeric($campo_corregido)==false)
				    &&($array_fields[108]=="1845-01-01"
				    || $array_fields[108]=="1835-01-01"
				    || $array_fields[108]=="1830-01-01"
				    || $array_fields[108]=="1825-01-01"
				    || $array_fields[108]=="1810-01-01"
				    || $array_fields[108]=="1805-01-01")
				    
				    )
			{
			    $campo_corregido="0";
			}
			else if($campo_corregido!="0"
				&& $campo_corregido!="999"
			   		&& ( floatval($campo_corregido)<3.5 || floatval($campo_corregido)>20 || is_numeric($campo_corregido)==false)
		   		)
			{
			    $campo_corregido="0";
			}	
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_041($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {

	     	$fecha_calendario_c108=$this->diferencia_dias_entre_fechas(trim($array_fields[108]),"1900-12-31");
			
				//calidad
				if( $campo_corregido=="0"
				     && ( $fecha_calendario_c108<0)
				    )
				{
				    $campo_corregido="999";
				}
				else if( $campo_corregido=="999"
				    && ($fecha_calendario_c108>=0 && $fecha_calendario_c108!==false)
				    )
				{
				    $campo_corregido="0";
				}
	     }//fin fase
	    }//c109
	    
	    //campo a corregir es 110
	    if($numero_campo_a_corregir==110)
	    {
	     
	     if($fase_correccion==0)
	     {
		//campo en blanco
     	if($campo_corregido=="")
		{
			if($campo_corregido==""
			   && ($edad==50
			       || $edad==55
			       || $edad==60
			       || $edad==65
			       || $edad==70
			       || $edad==75
			       || $edad==80
			       || $edad==85
			       || $edad==90
			       || $edad==95
			       || $edad==100
			       || $edad==105
			       || $edad==110
			       || $edad==115
			       || $edad==120
			       )
			   )
			{
			    $campo_corregido="1835-01-01";
			}
			else if($campo_corregido==""
			  && (
					    	(intval($edad)!=50 )
					     && (intval($edad)!=55 ) 
					     && (intval($edad)!=60 ) 
					     && (intval($edad)!=65 ) 
					    && (intval($edad)!=70 ) 
					    && (intval($edad)!=75 ) 
					    && (intval($edad)!=80 ) 
					    && (intval($edad)!=85 )
					    && (intval($edad)!=90 ) 
					    && (intval($edad)!=95 ) 
					    && (intval($edad)!=100 )
					    && (intval($edad)!=105 )
					    && (intval($edad)!=110 )
					    && (intval($edad)!=115 ) 
					    && (intval($edad)!=120 )
					    )
			   )
			{
			    $campo_corregido="1845-01-01";
			}
			else if($campo_corregido=="")
			{
			    $campo_corregido="1845-01-01";
			}
		}//fin if
		
		//formato
		$corrige_formato=false;
		$array_fecha_campo_actual=explode("-", $campo_corregido);
		if(count($array_fecha_campo_actual)==3)
		{
			if(ctype_digit($array_fecha_campo_actual[0]) && ctype_digit($array_fecha_campo_actual[1]) && ctype_digit($array_fecha_campo_actual[2]))
			{
				//checkdate mm-dd-aaaa -> aaaa-mm-dd ?
				if(checkdate($array_fecha_campo_actual[1],$array_fecha_campo_actual[2],$array_fecha_campo_actual[0])
			       && intval($array_fecha_campo_actual[0])>=32)
			    {
			    	//no se corrige formato
			    }
			    else
			    {
			    	$corrige_formato=true;
			    }

			}
			else
			{
				$corrige_formato=true;
			}
		}
		else
		{
			$corrige_formato=true;
		}//fin else

		if($corrige_formato==true)
		{
		$campo_corregido=$this->corrector_formato_fecha($campo_corregido,false,-2);
		}
		
		//valor permitido
		$excede_fecha_corte=$this->diferencia_dias_entre_fechas($campo_corregido,$date_de_corte);
		$es_menor_o_igual_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
		
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");

		//comparacion fecha campo actual es inferior a la fecha de nacimiento
			$fecha_nacimiento_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),"1900-12-31");
			$campo_actual_inferior_fecha_nacimiento=$this->diferencia_dias_entre_fechas($campo_corregido,trim($array_fields[9]));
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_nacimiento_es_mayor_a_1900_12_31<0
			&& $fecha_nacimiento_es_mayor_a_1900_12_31!==false
			&& $campo_actual_inferior_fecha_nacimiento>0
			&& $campo_actual_inferior_fecha_nacimiento!==false
			)
			{
				$campo_corregido="1800-01-01";
			}//fin if
			//fin comparacion fecha campo actual es inferior a la fecha de nacimiento

			//limite inferior fecha corte 2 years
			$fecha_corte_menos_2_years="";
			$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=false;
			$array_fecha_corte=explode("-", $date_de_corte);
			if(count($array_fecha_corte)==3)
			{
				$fecha_corte_menos_2_years=(intval($array_fecha_corte[0])-2)."-".$array_fecha_corte[1]."-".$array_fecha_corte[2];
				$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=$this->diferencia_dias_entre_fechas($campo_corregido,$fecha_corte_menos_2_years);
			}//fin if
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_corte_menos_2_years!=""
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years!==false
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years>0
			)
			{
				$campo_corregido="1845-01-01";
			}//fin if
			//fin limite inferior fecha corte 2 years

		if($comparacion_a_1900_12_31<0 
					&& $excede_fecha_corte<0
					&& $comparacion_a_1900_12_31!==false
					&& $excede_fecha_corte!==false
					)
				{
					$campo_corregido="1800-01-01";
				}//fin comparacion excede la fecha de corte
		
		if($comparacion_a_1900_12_31>=0
			&& $campo_corregido!="1800-01-01"
			&& $campo_corregido!="1805-01-01"
			&& $campo_corregido!="1810-01-01"
			&& $campo_corregido!="1825-01-01"
			&& $campo_corregido!="1830-01-01"
			&& $campo_corregido!="1835-01-01"
			&& $campo_corregido!="1845-01-01"
			)
		{
			if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& ($edad==50
				    || $edad==55
				    || $edad==60
				    || $edad==65
				    || $edad==70
				    || $edad==75
				    || $edad==80
				    || $edad==85
				    || $edad==90
				    || $edad==95
				    || $edad==100
				    || $edad==105
				    || $edad==110
				    || $edad==115
				    || $edad==120
				    )
				)
			{
			    $campo_corregido="1835-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
			  && (
					    	(intval($edad)!=50 )
					     && (intval($edad)!=55 ) 
					     && (intval($edad)!=60 ) 
					     && (intval($edad)!=65 ) 
					    && (intval($edad)!=70 ) 
					    && (intval($edad)!=75 ) 
					    && (intval($edad)!=80 ) 
					    && (intval($edad)!=85 )
					    && (intval($edad)!=90 ) 
					    && (intval($edad)!=95 ) 
					    && (intval($edad)!=100 )
					    && (intval($edad)!=105 )
					    && (intval($edad)!=110 )
					    && (intval($edad)!=115 ) 
					    && (intval($edad)!=120 )
					    )
			   )
			{
			    $campo_corregido="1845-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				)
			{
			    $campo_corregido="1845-01-01";
			}
		}//fin else if
		
	     
		//$campo_corregido=$this->corrector_valor_permitido_fecha_criterio_002($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
		if(
			($campo_corregido=="1845-01-01"
				|| $campo_corregido=="1800-01-01"
				)
		   && ($edad==50
		       || $edad==55
		       || $edad==60
		       || $edad==65
		       || $edad==70
		       || $edad==75
		       || $edad==80
		       || $edad==85
		       || $edad==90
		       || $edad==95
		       || $edad==100
		       || $edad==105
		       || $edad==110
		       || $edad==115
		       || $edad==120
		       )
		   )
		{
		    $campo_corregido="1835-01-01";
		}
		else if(
			($campo_corregido=="1800-01-01"
				|| $campo_corregido=="1805-01-01"
				|| $campo_corregido=="1810-01-01"
				|| $campo_corregido=="1825-01-01"
				|| $campo_corregido=="1830-01-01"
				|| $campo_corregido=="1835-01-01"
				)
			  && (
					    	(intval($edad)!=50 )
					     && (intval($edad)!=55 ) 
					     && (intval($edad)!=60 ) 
					     && (intval($edad)!=65 ) 
					    && (intval($edad)!=70 ) 
					    && (intval($edad)!=75 ) 
					    && (intval($edad)!=80 ) 
					    && (intval($edad)!=85 )
					    && (intval($edad)!=90 ) 
					    && (intval($edad)!=95 ) 
					    && (intval($edad)!=100 )
					    && (intval($edad)!=105 )
					    && (intval($edad)!=110 )
					    && (intval($edad)!=115 ) 
					    && (intval($edad)!=120 )
					    )
			   )
			{
			    $campo_corregido="1845-01-01";
			}
		
	     }//fin fase
	    }//fin campo 110
	    
	    //campo a corregir es 111
	    if($numero_campo_a_corregir==111)
	    {
	     if($fase_correccion==0)
	     {
		//campo en blanco
	     	if($campo_corregido=="")
			{
				if($campo_corregido==""
				   && ($edad==50
				       || $edad==55
				       || $edad==60
				       || $edad==65
				       || $edad==70
				       || $edad==75
				       || $edad==80
				       || $edad==85
				       || $edad==90
				       || $edad==95
				       || $edad==100
				       || $edad==105
				       || $edad==110
				       || $edad==115
				       || $edad==120
				       )
				   )
				{
				    $campo_corregido="1835-01-01";
				}
				else if($campo_corregido==""
				  && (
						    	(intval($edad)!=50 )
						     && (intval($edad)!=55 ) 
						     && (intval($edad)!=60 ) 
						     && (intval($edad)!=65 ) 
						    && (intval($edad)!=70 ) 
						    && (intval($edad)!=75 ) 
						    && (intval($edad)!=80 ) 
						    && (intval($edad)!=85 )
						    && (intval($edad)!=90 ) 
						    && (intval($edad)!=95 ) 
						    && (intval($edad)!=100 )
						    && (intval($edad)!=105 )
						    && (intval($edad)!=110 )
						    && (intval($edad)!=115 ) 
						    && (intval($edad)!=120 )
						    )
				   )
				{
				    $campo_corregido="1845-01-01";
				}
				else if($campo_corregido=="")
				{
				    $campo_corregido="1845-01-01";
				}
			}//fin if
		
		//formato
		$corrige_formato=false;
		$array_fecha_campo_actual=explode("-", $campo_corregido);
		if(count($array_fecha_campo_actual)==3)
		{
			if(ctype_digit($array_fecha_campo_actual[0]) && ctype_digit($array_fecha_campo_actual[1]) && ctype_digit($array_fecha_campo_actual[2]))
			{
				//checkdate mm-dd-aaaa -> aaaa-mm-dd ?
				if(checkdate($array_fecha_campo_actual[1],$array_fecha_campo_actual[2],$array_fecha_campo_actual[0])
			       && intval($array_fecha_campo_actual[0])>=32)
			    {
			    	//no se corrige formato
			    }
			    else
			    {
			    	$corrige_formato=true;
			    }

			}
			else
			{
				$corrige_formato=true;
			}
		}
		else
		{
			$corrige_formato=true;
		}//fin else

		if($corrige_formato==true)
		{
		$campo_corregido=$this->corrector_formato_fecha($campo_corregido,false,-2);
		}
		
		//valor permitido
		$excede_fecha_corte=$this->diferencia_dias_entre_fechas($campo_corregido,$date_de_corte);
		$es_menor_o_igual_1845_01_01=$this->diferencia_dias_entre_fechas($campo_corregido,"1845-01-01");
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");
		
		$comparacion_a_1900_12_31=$this->diferencia_dias_entre_fechas($campo_corregido,"1900-12-31");

		//comparacion fecha campo actual es inferior a la fecha de nacimiento
			$fecha_nacimiento_es_mayor_a_1900_12_31=$this->diferencia_dias_entre_fechas(trim($array_fields[9]),"1900-12-31");
			$campo_actual_inferior_fecha_nacimiento=$this->diferencia_dias_entre_fechas($campo_corregido,trim($array_fields[9]));
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_nacimiento_es_mayor_a_1900_12_31<0
			&& $fecha_nacimiento_es_mayor_a_1900_12_31!==false
			&& $campo_actual_inferior_fecha_nacimiento>0
			&& $campo_actual_inferior_fecha_nacimiento!==false
			)
			{
				$campo_corregido="1800-01-01";
			}//fin if
			//fin comparacion fecha campo actual es inferior a la fecha de nacimiento

			//limite inferior fecha corte 2 years
			$fecha_corte_menos_2_years="";
			$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=false;
			$array_fecha_corte=explode("-", $date_de_corte);
			if(count($array_fecha_corte)==3)
			{
				$fecha_corte_menos_2_years=(intval($array_fecha_corte[0])-2)."-".$array_fecha_corte[1]."-".$array_fecha_corte[2];
				$comparacion_fecha_actual_vs_fecha_corte_menos_2_years=$this->diferencia_dias_entre_fechas($campo_corregido,$fecha_corte_menos_2_years);
			}//fin if
			if($comparacion_a_1900_12_31<0 
			&& $comparacion_a_1900_12_31!==false
			&& $fecha_corte_menos_2_years!=""
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years!==false
			&& $comparacion_fecha_actual_vs_fecha_corte_menos_2_years>0
			)
			{
				$campo_corregido="1845-01-01";
			}//fin if
			//fin limite inferior fecha corte 2 years

		if($comparacion_a_1900_12_31<0 
					&& $excede_fecha_corte<0
					&& $comparacion_a_1900_12_31!==false
					&& $excede_fecha_corte!==false
					)
				{
					$campo_corregido="1800-01-01";
				}//fin comparacion excede la fecha de corte
		
		if($comparacion_a_1900_12_31>=0
			&& $campo_corregido!="1800-01-01"
			&& $campo_corregido!="1805-01-01"
			&& $campo_corregido!="1810-01-01"
			&& $campo_corregido!="1825-01-01"
			&& $campo_corregido!="1830-01-01"
			&& $campo_corregido!="1835-01-01"
			&& $campo_corregido!="1845-01-01"
			)
		{
			if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				&& ($edad==50
				    || $edad==55
				    || $edad==60
				    || $edad==65
				    || $edad==70
				    || $edad==75
				    || $edad==80
				    || $edad==85
				    || $edad==90
				    || $edad==95
				    || $edad==100
				    || $edad==105
				    || $edad==110
				    || $edad==115
				    || $edad==120
				    )
				)
			{
			    $campo_corregido="1835-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
			  && (
					    	(intval($edad)!=50 )
					     && (intval($edad)!=55 ) 
					     && (intval($edad)!=60 ) 
					     && (intval($edad)!=65 ) 
					    && (intval($edad)!=70 ) 
					    && (intval($edad)!=75 ) 
					    && (intval($edad)!=80 ) 
					    && (intval($edad)!=85 )
					    && (intval($edad)!=90 ) 
					    && (intval($edad)!=95 ) 
					    && (intval($edad)!=100 )
					    && (intval($edad)!=105 )
					    && (intval($edad)!=110 )
					    && (intval($edad)!=115 ) 
					    && (intval($edad)!=120 )
					    )
			   )
			{
			    $campo_corregido="1845-01-01";
			}
			else if($comparacion_a_1900_12_31>=0
				&& $campo_corregido!="1800-01-01"
				&& $campo_corregido!="1805-01-01"
				&& $campo_corregido!="1810-01-01"
				&& $campo_corregido!="1825-01-01"
				&& $campo_corregido!="1830-01-01"
				&& $campo_corregido!="1835-01-01"
				&& $campo_corregido!="1845-01-01"
				
				)
			{
			    $campo_corregido="1845-01-01";
			}
		}//fin else if
		
	     
	     
		//$campo_corregido=$this->corrector_valor_permitido_fecha_criterio_002($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
			if(
			($campo_corregido=="1845-01-01"
				|| $campo_corregido=="1800-01-01"
				)
		   && ($edad==50
		       || $edad==55
		       || $edad==60
		       || $edad==65
		       || $edad==70
		       || $edad==75
		       || $edad==80
		       || $edad==85
		       || $edad==90
		       || $edad==95
		       || $edad==100
		       || $edad==105
		       || $edad==110
		       || $edad==115
		       || $edad==120
		       )
		   )
		{
		    $campo_corregido="1835-01-01";
		}
		else if(
			($campo_corregido=="1800-01-01"
				|| $campo_corregido=="1805-01-01"
				|| $campo_corregido=="1810-01-01"
				|| $campo_corregido=="1825-01-01"
				|| $campo_corregido=="1830-01-01"
				|| $campo_corregido=="1835-01-01"
				)
			  && (
					    	(intval($edad)!=50 )
					     && (intval($edad)!=55 ) 
					     && (intval($edad)!=60 ) 
					     && (intval($edad)!=65 ) 
					    && (intval($edad)!=70 ) 
					    && (intval($edad)!=75 ) 
					    && (intval($edad)!=80 ) 
					    && (intval($edad)!=85 )
					    && (intval($edad)!=90 ) 
					    && (intval($edad)!=95 ) 
					    && (intval($edad)!=100 )
					    && (intval($edad)!=105 )
					    && (intval($edad)!=110 )
					    && (intval($edad)!=115 ) 
					    && (intval($edad)!=120 )
					    )
			   )
			{
			    $campo_corregido="1845-01-01";
			}
		
	     }//fin fase
	    }//fin campo 111
	    
	    //FIN CAMPOS EXAMENES DE LABORATORIO
	    
	    //CAMPOS DATOS IDENTIFICACION
	    
	    
	    //campo a corregir es 0
	    if($numero_campo_a_corregir==0)
	    {
		//criterio valor permitido 022
		if($fase_correccion==0)
		{
		 if($campo_corregido!="2")
		 {
		     $campo_corregido="2";
		 }
		}
		else if($fase_correccion==1)
		{}
	    }
	    
	    //campo a corregir es 1
	    if($numero_campo_a_corregir==1)
	    {
		//criterio 038
		if($fase_correccion==0)
		{
		//echo "<script>alert('$correccion_consecutivo');</script>";
		//se asigna consecutivo a todos enves de revisar
		$campo_corregido=$this->consecutivo_fixer;
		}
		else if($fase_correccion==1)
		{}
	    }
	    
	    //campo a corregir es 2
	    if($numero_campo_a_corregir==2)
	    {
	     if($fase_correccion==0)
	     {
	       if(strlen($campo_corregido)==11 &&
	       (substr($campo_corregido,0,1)=="5" || substr($campo_corregido,0,1)=="8")
	       )
		{
		    $campo_corregido="0".$campo_corregido;
		}
		else if(strlen($campo_corregido)==10)
		{
		    $campo_corregido=$campo_corregido."01";
		}
	      
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_034($campo_corregido,$conexion_bd_correccion);
	     }
	     else if($fase_correccion==1)
	     {
		$query_bd="";
		$query_bd.="SELECT * FROM gios_prestador_servicios_salud WHERE cod_registro_especial_pss = '".$campo_corregido."' ";		
		$query_bd.=";";
		$resultados_query=$conexion_bd_correccion->consultar2_no_crea_cierra($query_bd);
		
		//echo "<script>alert('$campo_corregido ".count($resultados_query)." ".$array_fields[2]."');</script>";
		
		if((!is_array($resultados_query) || count($resultados_query)==0) )
		{
		    $campo_corregido="999";
		}
	     }//fin else
	    }
	    
	    //campo a corregir es 3
	    if($numero_campo_a_corregir==3)
	    {
		//no aplicar corrector?
		if($fase_correccion==0)
		{
			  $campo_corregido=strtoupper( trim($campo_corregido) );
			  if($campo_corregido=="NU")
			  {
			  	$campo_corregido="NV";
			  }//fin if
			}//fin if
		else if($fase_correccion==1)
		{}
	    }
	    
	    //campo a corregir es 4
	    if($numero_campo_a_corregir==4)
	    {
			//no aplicar corrector?
			if($fase_correccion==0)
			{
			    $campo_corregido=trim($campo_corregido);

			    $campo_corregido=preg_replace('/[^0-9]+/', '', $campo_corregido);
			}//fin if
			else if($fase_correccion==1)
			{}
	    }//fin if
	    
	    //campo a corregir es 5
	    if($numero_campo_a_corregir==5)
	    {
		//criterio 001
		if($fase_correccion==0)
		{
		 if($campo_corregido=="")
		 {
		     $campo_corregido="NONE";
		 }
		 
		 $campo_corregido=strtoupper($campo_corregido);
		 $campo_corregido=str_replace(array(".",",",";",":","-","_"), "", $campo_corregido);
		}
		else if($fase_correccion==1)
		{}
	    }
	    
	    //campo a corregir es 6
	    if($numero_campo_a_corregir==6)
	    {
		//criterio 001
		if($fase_correccion==0)
		{
		 if($campo_corregido=="")
		 {
		     $campo_corregido="NONE";
		 }
		 
		 $campo_corregido=strtoupper($campo_corregido);
		 $campo_corregido=str_replace(array(".",",",";",":","-","_"), "", $campo_corregido);
		}
		else if($fase_correccion==1)
		{}
	    }
	    
	    //campo a corregir es 7
	    if($numero_campo_a_corregir==7)
	    {
		//criterio 001
		if($fase_correccion==0)
		{
		 if($campo_corregido=="")
		 {
		     $campo_corregido="NONE";
		 }
		 $campo_corregido=strtoupper($campo_corregido);
		 $campo_corregido=str_replace(array(".",",",";",":","-","_"), "", $campo_corregido);
		}
		else if($fase_correccion==1)
		{}
	    }
	    
	    //campo a corregir es 8
	    if($numero_campo_a_corregir==8)
	    {
		//criterio 001
		if($fase_correccion==0)
		{
		 if($campo_corregido=="")
		 {
		     $campo_corregido="NONE";
		 }
		 
		 $campo_corregido=strtoupper($campo_corregido);
		 $campo_corregido=str_replace(array(".",",",";",":","-","_"), "", $campo_corregido);
		}
		else if($fase_correccion==1)
		{}
	    }
	    
	    //campo a corregir es 9
	    if($numero_campo_a_corregir==9)
	    {

			//la fecha de nacimiento ya se corrige antes
			//$campo_corregido=$this->corrector_formato_fecha($campo_corregido,true);
			if($fase_correccion==0)
			{
				/*
				if($NOMBRE_ENTIDAD_PERSONALIZADA=="PrepagadaCoomeva")
				{
					//echo "entro 1 ".$NOMBRE_ENTIDAD_PERSONALIZADA." test campo 9 antes $campo_corregido <br>";
					//PARTE PRE CORRECCION SEXO Y FECHA NACIMIENTO DE ACUERDO A TABLAS DE REGIMEN
					//gioss_afiliados_eapb_rc, id_afiliado, tipo_id_afiliado,$this->tipo_de_regimen_de_la_informacion_reportada
					$query_bd_existe_afiliado_en_tabla_regimen="";
					$resultados_query_existe_afiliado_tablas_regimen=array();
					$nombre_tabla_afiliado_hallado="";
					$numero_id_c4=$array_fields[4];
					$tipo_id_c3=$array_fields[3];

					if($this->tipo_de_regimen_de_la_informacion_reportada=="C")
					{
						$nombre_tabla_afiliado_hallado="gioss_afiliados_eapb_rc";

						$query_bd_existe_afiliado_en_tabla_regimen="SELECT * FROM ".$nombre_tabla_afiliado_hallado." WHERE id_afiliado = '".$numero_id_c4."' AND tipo_id_afiliado = '".$tipo_id_c3."' AND codigo_eapb='".$this->cod_eapb_global."' ;";
						
					}
					if($this->tipo_de_regimen_de_la_informacion_reportada=="S")
					{
						$nombre_tabla_afiliado_hallado="gioss_afiliados_regimen_subsidiado";

						$query_bd_existe_afiliado_en_tabla_regimen="SELECT * FROM ".$nombre_tabla_afiliado_hallado." WHERE id_afiliado = '".$numero_id_c4."' AND tipo_id_afiliado = '".$tipo_id_c3."' AND codigo_eapb='".$this->cod_eapb_global."' ;";

					}
					if($this->tipo_de_regimen_de_la_informacion_reportada=="E" || $this->tipo_de_regimen_de_la_informacion_reportada=="O")
					{
						$nombre_tabla_afiliado_hallado="gioss_afiliados_eapb_mp";

						$query_bd_existe_afiliado_en_tabla_regimen="SELECT * FROM ".$nombre_tabla_afiliado_hallado." WHERE id_afiliado = '".$numero_id_c4."' AND tipo_id_afiliado = '".$tipo_id_c3."' AND codigo_eapb='".$this->cod_eapb_global."' ;";

					}
					if($this->tipo_de_regimen_de_la_informacion_reportada=="P")
					{
						$nombre_tabla_afiliado_hallado="gioss_afiliados_eapb_rp";

						$query_bd_existe_afiliado_en_tabla_regimen="SELECT * FROM ".$nombre_tabla_afiliado_hallado." WHERE id_afiliado = '".$numero_id_c4."' AND tipo_id_afiliado = '".$tipo_id_c3."' AND codigo_eapb='".$this->cod_eapb_global."' ;";

					}
					if($this->tipo_de_regimen_de_la_informacion_reportada=="N")
					{
						$nombre_tabla_afiliado_hallado="gioss_afiliados_eapb_nv";

						$query_bd_existe_afiliado_en_tabla_regimen="SELECT * FROM ".$nombre_tabla_afiliado_hallado." WHERE id_afiliado = '".$numero_id_c4."' AND tipo_id_afiliado = '".$tipo_id_c3."' AND codigo_eapb='".$this->cod_eapb_global."' ;";

					}//fin if
					$error_bd_afiliados="";
					$resultados_query_existe_afiliado_tablas_regimen=$conexion_bd_correccion->consultar_no_warning_get_error_no_crea_cierra($query_bd_existe_afiliado_en_tabla_regimen,$error_bd_afiliados);


					$num_filas_resultado_existe_tablas_regimen=count($resultados_query_existe_afiliado_tablas_regimen);

					$se_modifico_sexo_o_fecha_de_acuerdo_a_tabla_regimen=false;
					$si_existe=false;

					$sexo_anterior="";
					$fecha_anterior="";

					$sexo_posterior="";
					$fecha_posterior="";

					if($num_filas_resultado_existe_tablas_regimen>0 
						&& is_array($resultados_query_existe_afiliado_tablas_regimen)
						)
					{
						$si_existe=true;
						//verifica el sexo y fecha registrado en bd
						$numero_campo_sexo=10;
						$sexo_en_registro_archivo=strtoupper(trim($array_fields[$numero_campo_sexo]) );
						$sexo_anterior=$sexo_en_registro_archivo;
						$numero_campo_fecha_nacimiento=9;
						$fecha_nacimiento_en_registro_archivo=trim($array_fields[$numero_campo_fecha_nacimiento]);
						$fecha_anterior=$fecha_nacimiento_en_registro_archivo;
						$sexo_en_bd=strtoupper(trim($resultados_query_existe_afiliado_tablas_regimen[0]['sexo']) );
						$fecha_nacimiento_en_bd=trim($resultados_query_existe_afiliado_tablas_regimen[0]['fecha_nacimiento']);

						if($sexo_en_bd!="" && $fecha_nacimiento_en_bd!="")
						{
							

							$fecha_nacimiento_en_registro_archivo=$this->corrector_formato_fecha($fecha_nacimiento_en_registro_archivo,true);
							if($this->formato_fecha_valida_quick($fecha_nacimiento_en_bd) )
							{
								$campo_corregido=$fecha_nacimiento_en_bd;
								$se_modifico_sexo_o_fecha_de_acuerdo_a_tabla_regimen=true;
								
							}//fin if fecha nacimeinto es valida
							else
							{
								$fecha_nacimiento_en_bd=$this->corrector_formato_fecha($fecha_nacimiento_en_bd,true);
								if($this->formato_fecha_valida_quick($fecha_nacimiento_en_bd) )
								{
									if($fecha_nacimiento_en_bd!=$fecha_nacimiento_en_registro_archivo)
									{
										$campo_corregido=$fecha_nacimiento_en_bd;
										$se_modifico_sexo_o_fecha_de_acuerdo_a_tabla_regimen=true;
									}//fin if
								}//fin if fecha nacimeinto es valida

							}//fin else
						}//fin if datos de bd no estan vacios

					}//fin if hay concidencia en bd
					unset($resultados_query_existe_afiliado_tablas_regimen);
				    //FIN PARTE LLENA ARCHIVO NUEVO ORIGINAL CON POSIBLES REGISTROS AFILIADOS MODIFICADOS SEXO FECHA NACIMIENTO
				    
				}//fin if es coomeva prepagada
				*/
			}
			else if($fase_correccion==1)
			{		 
				
				
			}//fin fase
	    }//fin campo 9
	    
	    //campo a corregir es 10
	    if($numero_campo_a_corregir==10)
	    {
			//no aplicar debe venir correcto?
			if($fase_correccion==0)
			{
				/*
				if($NOMBRE_ENTIDAD_PERSONALIZADA=="PrepagadaCoomeva")
				{
					//PARTE PRE CORRECCION SEXO Y FECHA NACIMIENTO DE ACUERDO A TABLAS DE REGIMEN
					//gioss_afiliados_eapb_rc, id_afiliado, tipo_id_afiliado,$this->tipo_de_regimen_de_la_informacion_reportada
					$query_bd_existe_afiliado_en_tabla_regimen="";
					$resultados_query_existe_afiliado_tablas_regimen=array();
					$nombre_tabla_afiliado_hallado="";
					$numero_id_c4=$array_fields[4];
					$tipo_id_c3=$array_fields[3];

					if($this->tipo_de_regimen_de_la_informacion_reportada=="C")
					{
						$nombre_tabla_afiliado_hallado="gioss_afiliados_eapb_rc";

						$query_bd_existe_afiliado_en_tabla_regimen="SELECT * FROM ".$nombre_tabla_afiliado_hallado." WHERE id_afiliado = '".$numero_id_c4."' AND tipo_id_afiliado = '".$tipo_id_c3."' AND codigo_eapb='".$this->cod_eapb_global."' ;";
						
					}
					if($this->tipo_de_regimen_de_la_informacion_reportada=="S")
					{
						$nombre_tabla_afiliado_hallado="gioss_afiliados_regimen_subsidiado";

						$query_bd_existe_afiliado_en_tabla_regimen="SELECT * FROM ".$nombre_tabla_afiliado_hallado." WHERE id_afiliado = '".$numero_id_c4."' AND tipo_id_afiliado = '".$tipo_id_c3."' AND codigo_eapb='".$this->cod_eapb_global."' ;";

					}
					if($this->tipo_de_regimen_de_la_informacion_reportada=="E" || $this->tipo_de_regimen_de_la_informacion_reportada=="O")
					{
						$nombre_tabla_afiliado_hallado="gioss_afiliados_eapb_mp";

						$query_bd_existe_afiliado_en_tabla_regimen="SELECT * FROM ".$nombre_tabla_afiliado_hallado." WHERE id_afiliado = '".$numero_id_c4."' AND tipo_id_afiliado = '".$tipo_id_c3."' AND codigo_eapb='".$this->cod_eapb_global."' ;";

					}
					if($this->tipo_de_regimen_de_la_informacion_reportada=="P")
					{
						$nombre_tabla_afiliado_hallado="gioss_afiliados_eapb_rp";

						$query_bd_existe_afiliado_en_tabla_regimen="SELECT * FROM ".$nombre_tabla_afiliado_hallado." WHERE id_afiliado = '".$numero_id_c4."' AND tipo_id_afiliado = '".$tipo_id_c3."' AND codigo_eapb='".$this->cod_eapb_global."' ;";

					}
					if($this->tipo_de_regimen_de_la_informacion_reportada=="N")
					{
						$nombre_tabla_afiliado_hallado="gioss_afiliados_eapb_nv";

						$query_bd_existe_afiliado_en_tabla_regimen="SELECT * FROM ".$nombre_tabla_afiliado_hallado." WHERE id_afiliado = '".$numero_id_c4."' AND tipo_id_afiliado = '".$tipo_id_c3."' AND codigo_eapb='".$this->cod_eapb_global."' ;";

					}//fin if
					$error_bd_afiliados="";
					$resultados_query_existe_afiliado_tablas_regimen=$conexion_bd_correccion->consultar_no_warning_get_error_no_crea_cierra($query_bd_existe_afiliado_en_tabla_regimen,$error_bd_afiliados);


					$num_filas_resultado_existe_tablas_regimen=count($resultados_query_existe_afiliado_tablas_regimen);

					$se_modifico_sexo_o_fecha_de_acuerdo_a_tabla_regimen=false;
					$si_existe=false;

					$sexo_anterior="";
					$fecha_anterior="";

					$sexo_posterior="";
					$fecha_posterior="";

					if($num_filas_resultado_existe_tablas_regimen>0 
						&& is_array($resultados_query_existe_afiliado_tablas_regimen)
						)
					{
						$si_existe=true;
						//verifica el sexo y fecha registrado en bd
						$numero_campo_sexo=10;
						$sexo_en_registro_archivo=strtoupper(trim($array_fields[$numero_campo_sexo]) );
						$sexo_anterior=$sexo_en_registro_archivo;
						$numero_campo_fecha_nacimiento=9;
						$fecha_nacimiento_en_registro_archivo=trim($array_fields[$numero_campo_fecha_nacimiento]);
						$fecha_anterior=$fecha_nacimiento_en_registro_archivo;
						$sexo_en_bd=strtoupper(trim($resultados_query_existe_afiliado_tablas_regimen[0]['sexo']) );
						$fecha_nacimiento_en_bd=trim($resultados_query_existe_afiliado_tablas_regimen[0]['fecha_nacimiento']);

						if($sexo_en_bd!="" && $fecha_nacimiento_en_bd!="")
						{
							//si el sexo registrado en la tabla regimen
							//esta bien escrito y es diferente de lo registrado en 
							//el archivo(independiente si este esta bien escrito o no)
							//lo remplaza por el nuevo sexo
							if($sexo_en_bd=="F" 
								|| $sexo_en_bd=="M"
								)
							{
								if($sexo_en_bd!=$sexo_en_registro_archivo)
								{
									$campo_corregido=$sexo_en_bd;
									$se_modifico_sexo_o_fecha_de_acuerdo_a_tabla_regimen=true;
								}//fin if
							}//fin if
							
						}//fin if datos de bd no estan vacios

					}//fin if hay concidencia en bd
					unset($resultados_query_existe_afiliado_tablas_regimen);
				    //FIN PARTE LLENA ARCHIVO NUEVO ORIGINAL CON POSIBLES REGISTROS AFILIADOS MODIFICADOS SEXO FECHA NACIMIENTO
				}//fin if es coomeva prepagada
				*/
			 	$campo_corregido=strtoupper($campo_corregido);
			}
			else if($fase_correccion==1)
			{
				
			}//fin else if
	    }//fin if
	    
	    //campo a corregir es 11
	    if($numero_campo_a_corregir==11)
	    {
	     if($fase_correccion==0)
	     {
	     	if($campo_corregido!="1"
		       && $campo_corregido!="2"
		       && $campo_corregido!="3"
		       && $campo_corregido!="4"
		       && $campo_corregido!="5"
		       && $campo_corregido!="6"
		       )
		    {
				$campo_corregido="6";
		    }
			//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_025($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
		if($campo_corregido!="1"
		   && $campo_corregido!="2"
		   && $campo_corregido!="3"
		   && $campo_corregido!="4"
		   && $campo_corregido!="5"
		   && $campo_corregido!="6"
		   )
		{
		    $campo_corregido="6";
		}
	     }//fin fase
	    }
	    
	    //campo a corregir es 12
	    if($numero_campo_a_corregir==12)
	    {
	     if($fase_correccion==0)
	     {
		//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_037($campo_corregido,$conexion_bd_correccion);
	     }
	     else if($fase_correccion==1)
	     {
		$query_ciou="";
		$query_ciou.="SELECT * FROM gioss_ciou WHERE codigo_ciou_08='".$campo_corregido."'";
		$resultados_query=$conexion_bd_correccion->consultar2_no_crea_cierra($query_ciou);
		if($campo_corregido!="9999" && $campo_corregido!="9998" && (!is_array($resultados_query) || count($resultados_query)==0)  )
		{
		    //echo "<script>alert('antes de corregir 12: $campo_corregido');</script>";
		    $campo_corregido="9999";
		}
		//echo "<script>alert('campo_corregido 12: $campo_corregido');</script>";
	     }//fin fase
	    }
	    
	    //campo a corregir es 13
	    if($numero_campo_a_corregir==13)
	    {
	     if($fase_correccion==0)
	     {
	     	if($campo_corregido!="1"
		       && $campo_corregido!="2"
		       && $campo_corregido!="3"
		       && $campo_corregido!="4"
		       && $campo_corregido!="5"
		       && $campo_corregido!="6"
		       && $campo_corregido!="7"
		       && $campo_corregido!="8"
		       && $campo_corregido!="9"
		       && $campo_corregido!="10"
		       && $campo_corregido!="11"
		       && $campo_corregido!="12"
		       && $campo_corregido!="13"
		       )
		    {
				$campo_corregido="13";
		    }
			//$campo_corregido=$this->corrector_valor_permitido_numerico_criterio_006($campo_corregido);
	     }
	     else if($fase_correccion==1)
	     {
		if(intval($campo_corregido)<1 || intval($campo_corregido)>13)
		{
		    $campo_corregido="13";
		}
	     }//fin fase
	    }
	    
	    //FIN CAMPOS DATOS IDENTIFICACION

	    if(isset($resultados_query)==true)
	    {
	    	unset($resultados_query);
	    }
	    
	    $array_fields[$numero_campo_a_corregir]=$campo_corregido;
	    return $campo_corregido;
	}
	//FIN CRITERIOS CORRECCION FUNCION DEVUELVE EL CAMPO CORREGIDO

	public function formato_fecha_valida_quick($fecha_a_verificar,$separador="-")
	{
		$es_fecha_valida=true;

		$fecha_a_verificar_array= explode($separador,$fecha_a_verificar);

		if(count($fecha_a_verificar_array)!=3)
		{			
			$es_fecha_valida=false;
		}//fin if
		else if( !ctype_digit($fecha_a_verificar_array[0]) 
			|| !ctype_digit($fecha_a_verificar_array[1]) 
			|| !ctype_digit($fecha_a_verificar_array[2])  
			)
		{			
			$es_fecha_valida=false;
		}//fin if
		else if( 
			!checkdate($fecha_a_verificar_array[1],$fecha_a_verificar_array[2],$fecha_a_verificar_array[0])
			)
		{			
			$es_fecha_valida=false;
		}//fin if

		return $es_fecha_valida;
	}//fin function
}//fin clase
?>