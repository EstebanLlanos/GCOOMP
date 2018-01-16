<?php
set_time_limit(936000);
ini_set('max_execution_time', 936000);
ini_set('memory_limit', '900M');

require '../librerias_externas/Smarty-3.1.17/libs/Smarty.class.php';
include_once ('../utiles/clase_coneccion_bd.php');
include_once ('../utiles/funciones_crear_menu.php');

$smarty = new Smarty;
$coneccionBD = new conexion();
$coneccionBD->crearConexion();

//CREA CARPETA TEMPORALES SI NO EXISTE
//en esta carpeta se almacenaran los archivos subidos a gioss 
//y generados por gioss en su mayoria
$html_se_creo_temporales="";
$ruta_temporales="../TEMPORALES";
if(!file_exists($ruta_temporales))
{
	mkdir($ruta_temporales, 0777);
	$html_se_creo_temporales.="Se Creo La Carpeta TEMPORALES.";
}
//FIN CREA CARPETA TEMPORALES SI NO EXISTE


//cuando vuelve a la pantalla de logueo cierra la sesion anterior



//session_id("SESSIONGIOSS");
session_start();
if(isset($_SESSION['tipo_id']) && isset($_SESSION['identificacion']))
{
	//echo "<script>alert('".$_SESSION['tipo_id'].",".$_SESSION['identificacion']."');</script>";
}
//session_write_close();



//echo "<script>alert('antes de destruir ".session_id()."');</script>";

//VERIFICA SI SE CERRO SESSION
$se_cerro_session="NO";
if(isset($_REQUEST["se_cerro_session"]))
{
	$se_cerro_session=$_REQUEST["se_cerro_session"];
	unset($_REQUEST["se_cerro_session"]);
	//direccion para bd  menu 68 en gios_menus_opciones_sistema ..|index.php?se_cerro_session=SI
	//echo "<script>alert('Se cerro la sesion');</script>";
}
//FIN VERIFICA SI SE CERRO SESSION
if (session_id()!=""
    //strpos(session_id(),"SESSIONGIOSS") !== false
    && isset($_SESSION)
    && isset($_SESSION['identificacion'])
    && $_SESSION['identificacion']!=""
    )
{
	if($se_cerro_session=="NO")
	{
		if(isset($_REQUEST["no_tiene_permiso"]) )
    	{
			echo "<script>window.location = '../pantalla_inicial/pantalla_inicial.php?no_tiene_permiso=true'</script>";
		}//fin if
		else
		{
			echo "<script>window.location = '../pantalla_inicial/pantalla_inicial.php'</script>";
		}//fin else
	}//fin if no cerro sesion
	
	if($se_cerro_session=="SI")
	{
		session_unset();
		session_destroy();
		session_write_close();
		setcookie(session_name(),'',0,'/');
		session_regenerate_id(true);
		//echo "<script>alert('destruyo session ".session_id()." ');</script>";
	}
	
}

//fin cuando vuelve a la pantalla de logueo cierra la sesion anterior


function diferencia_dias_entre_fechas($fecha_1,$fecha_2)
{
    //las fechas deben ser cadenas de 10 caracteres en el sigueinte formato AAAA-MM-DD, ejemplo: 1989-03-03
    //si la fecha 1 es inferior a la fecha 2, obtendra un valor mayor a 0
    //si la fecha uno excede o es igual a la fecha 2, tendra un valor resultado menor o igual a 0
    date_default_timezone_set("America/Bogota");
    
    $array_fecha_1=explode("-",$fecha_1);
    
    $verificar_fecha_para_date_diff=true;
    
    if(count($array_fecha_1)==3)
    {
	    if(!ctype_digit($array_fecha_1[0])
	       || !ctype_digit($array_fecha_1[1]) || !ctype_digit($array_fecha_1[2])
	       || !checkdate(intval($array_fecha_1[1]),intval($array_fecha_1[2]),intval($array_fecha_1[0])) )
	    {
		    $verificar_fecha_para_date_diff=false;
	    }
    }
    else
    {
	    $verificar_fecha_para_date_diff=false;	
    }
    
    $array_fecha_2=explode("-",$fecha_2);			
    if(count($array_fecha_2)==3)
    {
	    if(!ctype_digit($array_fecha_2[0])
	       || !ctype_digit($array_fecha_2[1]) || !ctype_digit($array_fecha_2[2])
	       || !checkdate(intval($array_fecha_2[1]),intval($array_fecha_2[2]),intval($array_fecha_2[0])) )
	    {
		    $verificar_fecha_para_date_diff=false;
	    }
    }
    else
    {
	    $verificar_fecha_para_date_diff=false;
    }

    if($verificar_fecha_para_date_diff==true)
    {
	    $year1=intval($array_fecha_1[0])."";
	    $mes1=intval($array_fecha_1[1])."";
	    $dia1=intval($array_fecha_1[2])."";

	    $year2=intval($array_fecha_2[0])."";
	    $mes2=intval($array_fecha_2[1])."";
	    $dia2=intval($array_fecha_2[2])."";

	    if(strlen($dia1)==1)
	    {
	    	$dia1="0".$dia1;
	    }//fin if

	    if(strlen($mes1)==1)
	    {
	    	$mes1="0".$mes1;
	    }//fin if

	    if(strlen($dia2)==1)
	    {
	    	$dia2="0".$dia2;
	    }//fin if

	    if(strlen($mes2)==1)
	    {
	    	$mes2="0".$mes2;
	    }//fin if

	    $fecha_1=$year1."-".$mes1."-".$dia1;

	    $fecha_2=$year2."-".$mes2."-".$dia2;
	}//fin if
    
    $diferencia_dias_entre_fechas=0;
    if($verificar_fecha_para_date_diff==true)
    {
	    $date_fecha_1=date($fecha_1);
	    $date_fecha_2=date($fecha_2);
	    $fecha_1_format=new DateTime($date_fecha_1);
	    $fecha_2_format=new DateTime($date_fecha_2);		
	    try
	    {
	    $interval = date_diff($fecha_1_format,$fecha_2_format);
	    $diferencia_dias_entre_fechas= (float)$interval->format("%r%a");
	    }
	    catch(Exception $e)
	    {}
    }//fin if funcion date diff
    else
    {
	    $diferencia_dias_entre_fechas=false;
    }
    
    return $diferencia_dias_entre_fechas;
    
}//fin calculo diferencia entre fechas


$menu="";
$menu_logueo_html="";
$menu_sin_logueo=array();

$sql_consulta_id_menus_sin_perfil ="SELECT * FROM gios_menus_perfiles  INNER JOIN gios_menus_opciones_sistema ON (gios_menus_perfiles.id_menu = gios_menus_opciones_sistema.id_principal) WHERE id_perfil = '6' ORDER BY prioridad_jerarquica ASC;";
$resultado_query_menus_sin_perfil=$coneccionBD->consultar2_no_crea_cierra($sql_consulta_id_menus_sin_perfil);
$menu=crear_menu($resultado_query_menus_sin_perfil);


$error="";



//parte logueo usuario
if(isset($_POST['login']) && isset($_POST['password']))
{
	
	$usuario="";
	$contrasena="";
	
	if(preg_match("/select|drop|table|from|where/i",$_POST['login'])==false
	   && preg_match("/select|drop|table|from|where/i",$_POST['password'])==false)
	{
	
		$usuario=$_POST['login'];
		$contrasena=$_POST['password'];
	}
	else
	{
		$error.="Hubo intento de SQL Injection. <br>";
	}
	
	
	$sql_consulta_usuario="SELECT * FROM gios_usuarios_sistema p INNER JOIN gioss_entidad_nicklogueo_perfil_estado_persona nu ON (p.tipo_identificacion_usuario=nu.tipo_id AND p.identificacion_usuario=nu.identificacion_usuario)";
	$sql_consulta_usuario.=" WHERE nu.nicklogueo = '".$usuario."' AND nu.password = '".$contrasena."' AND nu.estado_nicklogueo='1';";
	$resultado_query=$coneccionBD->consultar2_no_crea_cierra($sql_consulta_usuario);
	
	$tipo_id="";
	$identificacion="";
	$nombre_completo="";
	$tipo_perfil="";
	$entidad_asociada_al_nick="";
	$nombre_entidad="";
	
	date_default_timezone_set ("America/Bogota");
	$fecha_actual = date('Y-m-d');
	$tiempo_actual = date('h:i:s');
	
	//verificacion usuario expirado
	$usuario_expirado=false;
	if(count($resultado_query)>0 && is_array($resultado_query))
	{
		$fecha_expiracion=$resultado_query[0]['fecha_expiracion'];
		if(diferencia_dias_entre_fechas($fecha_actual,$fecha_expiracion)<=0)
		{
			$usuario_expirado=true;
			$error.="Usuario expirado $fecha_expiracion.";
			
			$query_update_fecha_ultimo_acceso="";
			$query_update_fecha_ultimo_acceso.=" UPDATE gioss_entidad_nicklogueo_perfil_estado_persona ";
			$query_update_fecha_ultimo_acceso.=" SET ";
			$query_update_fecha_ultimo_acceso.=" estado_nicklogueo='2' ";
			$query_update_fecha_ultimo_acceso.=" WHERE ";
			$query_update_fecha_ultimo_acceso.=" nicklogueo = '".$usuario."' AND password = '".$contrasena."' AND estado_nicklogueo='1';";
			$error_bd_seq="";		
			$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($query_update_fecha_ultimo_acceso, $error_bd_seq);
			if($error_bd_seq!="")
			{
				echo "<script>alert('error al actualizar el estado del usuario');</script>";
			}
		}
		else
		{
			/*
			echo "<script>alert('Bienvenido');</script>";
			ob_flush();
			flush();
			*/
		}
	}
	//fin verificacion usuario expirado
		
	if(count($resultado_query)>0 && is_array($resultado_query) && $usuario_expirado==false)
	{
		
	
		$query_update_fecha_ultimo_acceso="";
		$query_update_fecha_ultimo_acceso.=" UPDATE gioss_entidad_nicklogueo_perfil_estado_persona ";
		$query_update_fecha_ultimo_acceso.=" SET ";
		$query_update_fecha_ultimo_acceso.=" fecha_ultimo_acceso='$fecha_actual', ";
		$query_update_fecha_ultimo_acceso.=" hora_ultimo_acceso='$tiempo_actual' ";
		$query_update_fecha_ultimo_acceso.=" WHERE ";
		$query_update_fecha_ultimo_acceso.=" nicklogueo = '".$usuario."' AND password = '".$contrasena."' AND estado_nicklogueo='1';";
		$error_bd_seq="";		
		$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($query_update_fecha_ultimo_acceso, $error_bd_seq);
		if($error_bd_seq!="")
		{
			echo "<script>alert('error al actualizar la ultima fecha de acceso');</script>";
		}
		
		$ultima_fecha_acceso=$resultado_query[0]['fecha_ultimo_acceso'];
		$ultima_hora_acceso=$resultado_query[0]['hora_ultimo_acceso'];
		//print_r($resultado_query);
		$tipo_id=$resultado_query[0]['tipo_identificacion_usuario'];
		$identificacion=$resultado_query[0]['identificacion_usuario'];
		//perfil asociado de la tabla gioss_entidad_nicklogueo_perfil_estado_persona
		$tipo_perfil=$resultado_query[0]['perfil_asociado'];
		$nombre_completo=$resultado_query[0]['primer_nombre_usuario']." ".$resultado_query[0]['segundo_nombre_usuario']." ".$resultado_query[0]['primer_apellido_usuario']." ".$resultado_query[0]['segundo_apellido_usuario'];
		$primer_nombre=$resultado_query[0]['primer_nombre_usuario'];
		$segundo_nombre=$resultado_query[0]['segundo_nombre_usuario'];
		$primer_apellido=$resultado_query[0]['primer_apellido_usuario'];
		$segundo_apellido=$resultado_query[0]['segundo_apellido_usuario'];
		$correo_usuario=$resultado_query[0]['correo_usuario'];
		
		$entidad_asociada_al_nick=$resultado_query[0]['entidad'];
		
		$query_entidad="SELECT * FROM gioss_entidades_sector_salud WHERE codigo_entidad='$entidad_asociada_al_nick'; ";
		$res_entidad=$coneccionBD->consultar2_no_crea_cierra($query_entidad);
		
		$nombre_entidad=$res_entidad[0]['nombre_de_la_entidad'];
		
		$sql_consulta_id_menus_con_perfil ="SELECT * FROM gios_menus_perfiles  INNER JOIN gios_menus_opciones_sistema ON (gios_menus_perfiles.id_menu = gios_menus_opciones_sistema.id_principal) WHERE id_perfil = '".$tipo_perfil."' ORDER BY prioridad_jerarquica ASC;";
		$resultado_query_menus_con_perfil=$coneccionBD->consultar2_no_crea_cierra($sql_consulta_id_menus_con_perfil);
		$menu_logueo_html=crear_menu($resultado_query_menus_con_perfil);
		
		/* Empezamos la sesion */
		if (session_status() == PHP_SESSION_NONE) 
		{
			session_start();
		}//fin if session ha iniciado
		/* Creamos la sesion */
		$_SESSION['usuario'] = $usuario;
		$_SESSION['tipo_id'] = $tipo_id;
		$_SESSION['identificacion'] = $identificacion;
		$_SESSION['tipo_perfil'] = $tipo_perfil;
		$_SESSION['nombre_completo'] = $nombre_completo;
		$_SESSION['primer_nombre'] = $primer_nombre;
		$_SESSION['segundo_nombre'] = $segundo_nombre;
		$_SESSION['primer_apellido'] = $primer_apellido;
		$_SESSION['segundo_apellido'] = $segundo_apellido;
		$_SESSION['menu_logueo_html'] = $menu_logueo_html;
		$_SESSION['correo'] = $correo_usuario;
		
		$_SESSION['ultima_fecha_acceso'] = $ultima_fecha_acceso;
		$_SESSION['ultima_hora_acceso'] = $ultima_hora_acceso;
		
		$_SESSION['entidad_asociada_al_nick'] = $entidad_asociada_al_nick;
		$_SESSION['nombre_entidad']=$nombre_entidad;

		//echo print_r($_SESSION,true);
		
		session_write_close();
		
		
		header ("Location: ../pantalla_inicial/pantalla_inicial.php");
	}
	else
	{
		if($usuario_expirado!=true)
		{
			$error.="Usuario o contrase&ntildea invalidos.";
		}
	}
}//fin if recibe usuario y contrasena


$smarty->assign("error", $error, true);
$smarty->assign("menu", $menu, true);
$smarty->display('interfaz_de_logueo.html.tpl');

$ram_usada_MB=(memory_get_usage(true)/1048576.2);
echo "<script>document.getElementById('medidor_ram').innerHTML='".$ram_usada_MB." $html_se_creo_temporales';</script>";
$coneccionBD->cerrar_conexion();

if($se_cerro_session!="NO")
{
	//direccion para bd  menu 68 en gios_menus_opciones_sistema ..|index.php?se_cerro_session=SI
	echo "<script>alert('Se cerro la sesion');</script>";
}

if(isset($_POST['login']) && isset($_POST['password']) )//fin condicion
{
	$login=trim($_POST['login']);
	$password=trim($_POST['password']);
	$html_javascript="<script>
	document.getElementById('login').value='$login';
	document.getElementById('password').value='$password';
	</script>";
	echo $html_javascript;
}
?>