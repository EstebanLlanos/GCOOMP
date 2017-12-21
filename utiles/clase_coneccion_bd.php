<?php
set_time_limit(936000);
ini_set('max_execution_time', 936000);
ini_set('memory_limit', '900M');

include_once('cadena_para_coneccion.php');
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of clase_coneccion_bd
 *
 * funciones basicas hacia una base de datos postgresql 
 */

class conexion 
{

    //put your code here
    var $servidor;
    var $usuario;
    var $pass;
    var $bd;
    var $conexion;
    var $puerto;

    var $gestor_de_errores;

    var $filas_afectadas_update;

	function __construct() 
	{
		global $SERVIDOR;
		global $USUARIO;
		global $CONTRASENA;
		global $BASE_DE_DATOS;
		global $PUERTO;
		
		$this->servidor = $SERVIDOR;
		$this->usuario = $USUARIO;
		$this->pass = $CONTRASENA;
		$this->bd = $BASE_DE_DATOS;
		$this->puerto = $PUERTO;
	}

	public function get_filas_afectadas_update()
    {
    	return $this->filas_afectadas_update;
    }//fin function

	public function consultar($sql)
	{
    
	    $this->crearConexion();

	    $exec_query=pg_query($this->conexion, $sql);
    
	    $query = pg_fetch_array($exec_query);

	    pg_free_result($exec_query);
		
		$this->cerrar_conexion();    
    
	    if (!$query) 
		    {
		
	    } 
		    else 
		    {
		return $query;
	    }
	}
	
	public function consultar_no_crea_cierra($sql)
	{
    
	    //$this->crearConexion();
    
	    //$query = pg_fetch_array(pg_query($this->conexion, $sql));

	    $exec_query=pg_query($this->conexion, $sql);
    
	    $query = pg_fetch_array($exec_query);

	    pg_free_result($exec_query);
		
		//$this->cerrar_conexion();    
    
	    if (!$query) 
		    {
		
	    } 
		    else 
		    {
		return $query;
	    }
	}
    
	public function consultar2($sql)
	{
    
	    $this->crearConexion();
    
	    //$query = pg_fetch_all(pg_query($this->conexion, $sql));

	    $exec_query=pg_query($this->conexion, $sql);
    
	    $query = pg_fetch_all($exec_query);

	    
	    
	    if($exec_query==false)
	    {
		$query_con_error=preg_replace("/[^A-Za-z0-9:.\-\s+\_\'\=\,\*]/", "", trim($sql) );
		//echo "<script>alert(\"ERROR consultar2 $query_con_error\");</script>";
		
		$this->escribir_en_log_errores($sql,"consultar2");
	    }
	    pg_free_result($exec_query);
	    $this->cerrar_conexion();
	    if (!$query) 
	    {
		    
	    } 
	    else 
	    {
		//$query_sin_error=preg_replace("/[^A-Za-z0-9:.\-\s+\_\'\=\,\*]/", "", trim($sql) );
		//echo "<script>alert(\"query usada existosamente $query_sin_error con ".count($query)." de resultados \");</script>";
		return $query;
	    }
	}
	
	// función de gestión de errores
	public function miGestorDeErrores($errno, $errstr, $errfile, $errline)
	{
		if (!(error_reporting() & $errno))
		{
			// Este código de error no está incluido en error_reporting
			return;
		}//fin if
		
		/* Según el típo de error, lo procesamos */
		switch ($errno)
		{
			case E_WARNING:
				$mensaje_gestor_errores="";
				$mensaje_gestor_errores.="Hay un WARNING.<br />\n";
				$mensaje_gestor_errores.="El warning es: ". $errstr ."<br />\n";
				$mensaje_gestor_errores.="El fichero donde se ha producido el warning es: ". $errfile ."<br />\n";
				$mensaje_gestor_errores.="La l&iacutenea donde se ha producido el warning es: ". $errline ."<br />\n";
				if(connection_aborted()==false)
				{
					echo $mensaje_gestor_errores;
					$mensaje_gestor_errores_para_log=str_replace(array("<br />","\n")," ",$mensaje_gestor_errores);
					$mensaje_gestor_errores_para_log=str_replace("&iacute","i",$mensaje_gestor_errores_para_log);
					$this->escribir_en_log_errores($mensaje_gestor_errores_para_log,"mensaje_gestor_errores");
				}
				else
				{
					$mensaje_gestor_errores_para_log=str_replace(array("<br />","\n")," ",$mensaje_gestor_errores);
					$mensaje_gestor_errores_para_log=str_replace("&iacute","i",$mensaje_gestor_errores_para_log);
					$this->escribir_en_log_errores($mensaje_gestor_errores_para_log,"mensaje_gestor_errores");
				}
				/* No ejecutar el gestor de errores interno de PHP, hacemos que lo pueda procesar un try catch */
				
				break;
			     
			case E_NOTICE:
				$mensaje_gestor_errores="";
				$mensaje_gestor_errores.= "Hay un NOTICE:<br />\n";
				$mensaje_gestor_errores.= "El notice es: ". $errstr ."<br />\n";
				$mensaje_gestor_errores.= "El fichero donde se ha producido el notice es: ". $errfile ."<br />\n";
				$mensaje_gestor_errores.= "La l&iacutenea donde se ha producido el notice es: ". $errline ."<br />\n";
				if(connection_aborted()==false)
				{
					echo $mensaje_gestor_errores;
					$mensaje_gestor_errores_para_log=str_replace(array("<br />","\n")," ",$mensaje_gestor_errores);
					$mensaje_gestor_errores_para_log=str_replace("&iacute","i",$mensaje_gestor_errores_para_log);
					$this->escribir_en_log_errores($mensaje_gestor_errores_para_log,"mensaje_gestor_errores");
				}
				else
				{
					$mensaje_gestor_errores_para_log=str_replace(array("<br />","\n")," ",$mensaje_gestor_errores);
					$mensaje_gestor_errores_para_log=str_replace("&iacute","i",$mensaje_gestor_errores_para_log);
					$this->escribir_en_log_errores($mensaje_gestor_errores_para_log,"mensaje_gestor_errores");
				}
				/* No ejecutar el gestor de errores interno de PHP, hacemos que lo pueda procesar un try catch */
				
				break;
			
			default:
				/* Ejecuta el gestor de errores interno de PHP */
				return false;
				break;
		}
		
		/* No ejecutar el gestor de errores interno de PHP */
		return true;
	}//fin funcion gestor de errores
	
	public function escribir_en_log_errores($cadena_texto_a_imprimir_en_log_errores,$sitio_del_error="no definido")
	{
		//PARTE LOG EN TXT
		//crea directorio log
		$ruta_temporales="../TEMPORALES/";
		$ruta_carpeta_del_log_errores_bd=$ruta_temporales."logs_errores_bd";
		if(!file_exists($ruta_carpeta_del_log_errores_bd))
		{
			mkdir($ruta_carpeta_del_log_errores_bd, 0700);
		}
		//fin crea directorio log
		$cadena_preparada_para_log_bd=preg_replace("/[^A-Za-z0-9:.\-\s+\_\'\=\,\*\(\)]/", "", trim($cadena_texto_a_imprimir_en_log_errores) );
		date_default_timezone_set ("America/Bogota");
		$fecha_para_archivo= date('Y-m-d-H');
		$tiempo_actual = date('H:i:s');
		$ruta_archivo_log_bd=$ruta_carpeta_del_log_errores_bd."/"."log_error_bd_".$fecha_para_archivo.".txt";		    
		//se sobreescribe con el modo a		
		$file_archivo_log_error_bd = fopen($ruta_archivo_log_bd, "a") or die("fallo la creacion del archivo");
		fwrite($file_archivo_log_error_bd, "\n"."tiempo error: $tiempo_actual query problematica en $sitio_del_error: ".$cadena_preparada_para_log_bd);
		fclose($file_archivo_log_error_bd);
		//FIN PARTE LOG EN TXT
	}//fin funcion $this->escribir_en_log_errores
	
	
	public function consultar2_no_crea_cierra($sql)
	{
    
	    //$this->crearConexion();
	    //$this->gestor_de_errores = set_error_handler(array($this,"miGestorDeErrores"));
		
	    try
	    {
		//$sql = trim(preg_replace('/\s+/', ' ', $sql));
		$cuenta_remplazos=0;
		$antes=$sql;
		$sql=str_replace("\xA0", ' ', $sql,$cuenta_remplazos); //esta es la que remueve el 0xa0
		
		if($cuenta_remplazos>0)
		{
			$this->escribir_en_log_errores($sql." ANTES ".$antes,"consultar2_no_crea_cierra remplazo caracter no utf8");
		}
		
		if($sql=="")
		{
			$this->escribir_en_log_errores($sql." ANTES ".$antes,"consultar2_no_crea_cierra esta en blanco");
		}
    
		//$query = pg_fetch_all(pg_query($this->conexion, $sql));

		$exec_query=pg_query($this->conexion, $sql);
    
	    $query = pg_fetch_all($exec_query);
		
		if($exec_query==false)
		{
			$query_con_error=preg_replace("/[^A-Za-z0-9:.\-\s+\_\'\=\,\*]/", "", trim($sql) );
			//echo "<script>alert(\"ERROR consultar2_no_crea_cierra $query_con_error\");</script>";
			
			$this->escribir_en_log_errores($sql,"consultar2_no_crea_cierra");
		}
		pg_free_result($exec_query);
		//$this->cerrar_conexion();
		
		if (!$query) 
		{
			
		} 
		else 
		{
		    //$query_sin_error=preg_replace("/[^A-Za-z0-9:.\-\s+\_\'\=\,\*]/", "", trim($sql) );
		    //echo "<script>alert(\"query usada existosamente $query_sin_error con ".count($query)." de resultados \");</script>";
		    return $query;
		}//fin else
	    }//fin try
	    catch(Exception $ex)
	    {
		$msg_try_catch = $ex->getMessage() . $ex->getTraceAsString();
		
		$this->escribir_en_log_errores($msg_try_catch,"consultar2_no_crea_cierra");
		$this->escribir_en_log_errores($sql,"consultar2_no_crea_cierra");
		
	    }//fin catch
	    
	    restore_error_handler();
	}//fin if function
    
	public function consultar_no_warning_get_error($sql,&$error) 
	{
		$error = "";
		$this->crearConexion();		
		//$query= pg_fetch_all(pg_query($this->conexion, $sql));
		$exec_query=pg_query($this->conexion, $sql);
    
	    $query = pg_fetch_all($exec_query);
		if($query)
		{}
		else
		{
			//$this->escribir_en_log_errores($sql,"consultar_no_warning_get_error p1");
			$error = "".pg_last_error($this->conexion); 
		}
		pg_free_result($exec_query);
		$this->cerrar_conexion();
		//parte para que sirva count()==0 si no hay resultados
		if (!$query) 
		{
			//$this->escribir_en_log_errores($sql,"consultar_no_warning_get_error p2");
		} 
		else 
		{
			return $query;
		}
        }
	
	public function consultar_no_warning_get_error_no_crea_cierra($sql,&$error) 
	{
		$error = "";
		//$this->crearConexion();		
		//$query= pg_fetch_all(pg_query($this->conexion, $sql));
		$exec_query=pg_query($this->conexion, $sql);
    
	    $query = pg_fetch_all($exec_query);
		if($query)
		{}
		else
		{
			//$this->escribir_en_log_errores($sql,"consultar_no_warning_get_error_no_crea_cierra p1");
			$error = "".pg_last_error($this->conexion); 
			if($error!="")
			{
				$this->escribir_en_log_errores($sql,"consultar_no_warning_get_error_no_crea_cierra p1 ".$error);
			}
		}
		pg_free_result($exec_query);
		//$this->cerrar_conexion();
		//parte para que sirva count()==0 si no hay resultados
		if (!$query) 
		{
			//$this->escribir_en_log_errores($sql,"consultar_no_warning_get_error_no_crea_cierra p2");
		} 
		else 
		{
			return $query;
		}
        }

	public function borrar($sql)
	{
    
	    $this->crearConexion();
	    pg_exec($this->conexion, $sql);
	    $this->cerrar_conexion();
		    
	}
    
	public function insertar($sql, $x = 0)
	{
    
	    $this->crearConexion();
	    pg_exec($this->conexion, $sql) or print("fallo insert en linea " . $x);
		    $this->cerrar_conexion();
	}
    
	public function insertar2($sql, $x = 0)
	{
	    $bandera = false;
	    $this->crearConexion();
	    pg_exec($this->conexion, $sql) or $bandera = true;
	    $this->cerrar_conexion();
	    return $bandera;
	}
	    
	public function insertar3($sql)
	{
	    $error = false;
	    $this->crearConexion();
	    pg_exec($this->conexion, $sql) or $error = "".pg_last_error($this->conexion); 
		    $this->cerrar_conexion();
	    return $error;
	}
	
	public function insertar_no_warning_get_error($sql,&$error) 
	{
		$error = "";
		$bandera = false;
		$this->crearConexion();
		if(@pg_exec($this->conexion, $sql))
		{}
		else
		{
		 $error = "".pg_last_error($this->conexion); 
		 $bandera = true;
		}
		$this->cerrar_conexion();
		return $bandera;
        }
	
	public function insertar_no_warning_get_error_no_crea_cierra($sql,&$error) 
	{
		$error = "";
		$bandera = false;
		
		if((strstr($sql,"VIEW")!=false && (strstr($sql,"CREATE")!=false || strstr($sql,"REPLACE")!=false))
		   || (strstr($sql,"view")!=false && (strstr($sql,"create")!=false || strstr($sql,"replace")!=false))
		   )
		{
			//$this->cerrar_conexion();
			$this->crearConexion();
		}
		
		$cuenta_remplazos=0;
		$antes=$sql;
		$sql=str_replace("\xA0", ' ', $sql,$cuenta_remplazos); //esta es la que remueve el 0xa0
		
		if($cuenta_remplazos>0)
		{
			$this->escribir_en_log_errores($sql." ANTES ".$antes,"consultar2_no_crea_cierra remplazo caracter no utf8");
		}
		
		if($sql=="")
		{
			$this->escribir_en_log_errores($sql." ANTES ".$antes,"consultar2_no_crea_cierra esta en blanco");
		}
		
		$resultado_insert=@pg_exec($this->conexion, $sql);
		//echo "no entro";
		if($resultado_insert)
		{
			$this->filas_afectadas_update=pg_affected_rows($resultado_insert);
			//echo "entro filas afectadas";
			//$this->escribir_en_log_errores($sql." ANTES ".$antes," entro a actualizar filas afectadas");
		}
		else
		{
			$error = "".pg_last_error($this->conexion);
			
			//error query como tal
			$error_sin_error=preg_replace("/[^A-Za-z0-9:.\-\s+\_\'\=\,\*]/", "", trim($error) );
			//echo "<script>alert(\" error query $error_sin_error \");</script>";
			//fin error query como tal
			
			//PARTE MUESTRA QUERY
			/*
			$query_sin_error=preg_replace("/[^A-Za-z0-9:.\-\s+\_\'\=\,\*]/", "", trim($sql) );
			//parte 1
			
			if(strlen($query_sin_error)>150)
			{
			       $parte_query=substr($query_sin_error,0,150);
			       echo "<script>alert(\" error p1 $parte_query \");</script>";
			}
			else if(strlen($query_sin_error)>0)
			{
			       $parte_query=substr($query_sin_error,0,strlen($query_sin_error));
			       echo "<script>alert(\" error p1 $parte_query \");</script>";
			}
			//parte 2
			if(strlen($query_sin_error)>300)
			{
			       $parte_query=substr($query_sin_error,150,300);
			       echo "<script>alert(\" error p2 $parte_query \");</script>";
			}
			else if(strlen($query_sin_error)>150)
			{
			       $parte_query=substr($query_sin_error,150,strlen($query_sin_error));
			       echo "<script>alert(\" error p2 $parte_query \");</script>";
			}
			//parte 3
			if(strlen($query_sin_error)>450)
			{
			       $parte_query=substr($query_sin_error,300,450);
			       echo "<script>alert(\" error p3 $parte_query \");</script>";
			}
			else if(strlen($query_sin_error)>300)
			{
			       $parte_query=substr($query_sin_error,300,strlen($query_sin_error));
			       echo "<script>alert(\" error p3 $parte_query \");</script>";
			}
			*/
			//FIN PARTE MUESTRA QUERY
			
			
			$this->escribir_en_log_errores($sql,"insertar_no_warning_get_error_no_crea_cierra");
			
			//echo "<script>alert(\" error query, debio escribir query en el log \");</script>";
			
			$bandera = true;
		}//fin else
		if((strstr($sql,"VIEW")!=false && (strstr($sql,"CREATE")!=false || strstr($sql,"REPLACE")!=false))
		   || (strstr($sql,"view")!=false && (strstr($sql,"create")!=false || strstr($sql,"replace")!=false))
		   )
		{
			//echo "<script>alert(\" era vista \");</script>";
			//$this->cerrar_conexion();
			$this->crearConexion();
		}
		return $bandera;
        }
	
	public function insertar_no_warning_get_error_no_crea_cierra_2($sql,&$error) 
	{
		$error = "";
		$bandera = false;
		
		
		if(@pg_exec($this->conexion, $sql))
		{
			
		}
		else
		{
		 $error = "".pg_last_error($this->conexion); 
		 $bandera = true;
		}
		
		return $bandera;
        }
	
	public function obtenerCodificacion()
	{
		$this->crearConexion();
		return pg_client_encoding($this->conexion);
		$this->cerrar_conexion();
		
	}
	
	public function ponerCodificacion($codificacion)
	{
		$this->crearConexion();
		pg_set_client_encoding($this->conexion, $codificacion);
		$this->cerrar_conexion();
		
	}

	public function crearConexion()
	{	
	    if(connection_aborted()==false)
	    {
		//echo "<script>alert(\" antes de crear la conexion \");</script>";
	    }
	    $this->gestor_de_errores = set_error_handler(array($this,"miGestorDeErrores"));
	    
	    $cadena_de_coneccion="host=".$this->servidor." port=".$this->puerto." dbname=".$this->bd." user=".$this->usuario." password=".$this->pass;	    
	    $this->conexion = pg_connect($cadena_de_coneccion,PGSQL_CONNECT_FORCE_NEW) or die("Fallo la conexion a la Base de datos!" . pg_last_error());
	    if(connection_aborted()==false)
	    {
		//echo "<script>alert(\" Se creo la conexion \");</script>";
	    }
	}

	public function cerrar_conexion()
	{	
		pg_close($this->conexion);
	}

	public function obtener_codificacion_cliente()
	{	
		return pg_client_encoding($this->conexion);
	}

	public function cambiar_codificacion_cliente($codificacion)
	{	
		return pg_set_client_encoding($this->conexion, $codificacion);
	}
	
	function __destruct()
	{
	    //pg_close($this->conexion);
	}

}

?>
