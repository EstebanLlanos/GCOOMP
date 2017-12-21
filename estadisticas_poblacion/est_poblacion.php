<?php
set_time_limit(936000);
ini_set('max_execution_time', 936000);
ini_set('memory_limit', '1900M');

require '../librerias_externas/Smarty-3.1.17/libs/Smarty.class.php';
include_once ('../utiles/clase_coneccion_bd.php');
include_once ('../utiles/funciones_crear_menu.php');

require_once '../utiles/crear_zip.php';

include ("../librerias_externas/PHPMailer/class.phpmailer.php");
include ("../librerias_externas/PHPMailer/class.smtp.php");
require_once ("../librerias_externas/PHPExcel/PHPExcel.php");
require_once ("../librerias_externas/PHPExcel/PHPExcel/Writer/Excel2007.php");

require_once '../utiles/configuracion_global_email.php';

$smarty = new Smarty;
$coneccionBD = new conexion();
$coneccionBD->crearConexion();
session_start();

if (!isset($_SESSION['usuario']) && !isset($_SESSION['tipo_perfil']))
{
	
	header ("Location: ../index.php");
}

if(
	isset($_SESSION['tipo_perfil'])
	&& $_SESSION['tipo_perfil']!='5'
	)
{
	header ("Location: ../index.php?no_tiene_permiso=true");
	
}//fin if

/*
//GUIA
sucursal	cadena	2		Sucursal del usuario	"
AA CALI,
BA BUGA,
CA POPAYAN,
CG CARTAGENA,
CU CUCUTA,
DA B/VENTURA,
EA MEDELLIN,
FA APARTADO,
GA BARRANQUILLA,
HA PALMIRA,
IA BOGOTA,
JA ARMENIA,
KA IBAGUE,
LA PEREIRA,
MA CARTAGO,
MS MUSHAISA,
MT MONTERIA,
NA TULUA,
NV NEIVA,
NY NUEVA YORK,
OA MANIZALES,
PS PASTO,
RN RIONEGRO,
SN SANTA MARTA,
UT UNION TEMPORAL,
VA VALLEDUPAR,
VI VILLAVICENCIO,
WA QUIBDO,
YA BUCARAMANGA"	primaria
tipocont	cadena	2		Plan del contrato	"A = Asociados
AS = Asesores
C = Colectivos
F = Familiar
EM = Empleados"	
nitcontr	numerico	11		Identificación del contratante		primaria
nombrect	cadena	30		Nombre del contratante		
plantari	cadena	4		Corresponde al código del programa que se le asigna originalmente como derivación de alguno de los que aparece en frente  ====>	"ADOR = Anos Dorados Oro
AND1 = Anos Dorados Plata
ANDO = Anos Dorados Clásico
AHCM = Anos Dorados HCM
ORO = ORO
PLAT = PLATA
CLAS = CLASICO
HCM = HCM
SAOR = Salud Oral
TRAD = TRADICIONAL
CEM = CEM"	primaria
numecont	numerico	8		Número de Contrato		primaria
anorenov	numerico	4		Ano de renovación del contrato		primaria
mesrenov	numerico	2		Mes de renovación del contrato		primaria
diarenov	numerico	2		Día de renovación del contrato		primaria
cuotacs	numerico	15		Tarifa que paga el contratante por todos sus afiliados dentro del contrato		
familias	numerico	5			Identicacion en AS-400	
cuotaus	numerico	15		Tarifa que paga el afiliado		
nitusuar	numerico	11		Identificación del usuario		primaria
nombreus	cadena	31		Nombre del usuario		
anonacim	numerico	4		Ano de nacimiento del usuario		
mesnacim	numerico	2		mes de nacimiento del usuario		
dianacim	numerico	2		Día de nacimiento del usuario		
sexo	numerico	1		Sexo del usuario	1 = Hombre y 2= Mujer	
ingsisaa	numerico	4		Ano de Ingreso con reconocimiento de antigüedad		primaria
ingsismm	numerico	2		Mes de Ingreso con reconocimiento de antiguedad		primaria
ingsisdd	numerico	2		Día de Ingreso con reconocimiento de antiguedad		primaria
ingcooaa	numerico	4		Ano de Ingreso a Coomeva Medicina Prepagada		primaria
ingcoomm	numerico	2		Mes de Ingreso a Coomeva Medicina Prepagada		primaria
ingcoodd	numerico	2		Día de Ingreso a Coomeva Medicina Prepagada		primaria
numeusua	numerico	2		Número de usuario para identificación dentro de la familia en un contrato		primaria
faminro	numerico	5		Número de familia para identificación dentro de un contrato		
parentes	cadena	2		Parentesco del usuario con respecto al contratante	"0T = Otros
AB = Abuelo 
CF = Cabeza de Flia.
CS = Casado (a)
CY = Conyuge
HJ = Hijo (a)
HM = Hermano (a)
MD = madre
ME = Menor de Edad
NT = Nieto
OP = Otra persona 
OT = otro
PD = Padre
PM= Primo (a)
SB = Sobrino (a)
SG = Suegro (a)
SL = Soltero (a)
TI = Tio (a)
UL = Union Libre
VD = Viudo (a)
"	
tipousua	cadena	1		Identifica la procedencia del usuario por tipo de contrato		
programa	cadena	4		Corresponde al código del programa según aparece en frente  ====>	"ADOR = Anos Dorados Oro
AND1 = Anos Dorados Plata
ANDO = Anos Dorados Clásico
AHCM = Anos Dorados HCM
ORO = ORO
PLAT = PLATA
CLAS = CLASICO
HCM = HCM
SAOR = Salud Oral
TRAD = TRADICIONAL
CEM = CEM"	primaria
planes	numerico	8		Planes	"1 = Familiar
2 = Colectivos
3 = Asociados"	
edad	numerico	8		Edad (anos y fracción)		
grupoed	numerico	8		Grupos de edad	"1= menores 1 ano
2 = 1 a 4 anos
3 = 5 a 9 anos
4 = 10 a 14 anos
5 = 15 a 19 anos
6  = 20 a 24 anos
7 = 25 a 29 anos
8 = 30 a 34 anos
9 = 35 a 39 anos
10 = 40 a 44 anos
11 = 45 a 49 anos
12 = 50 a 54 anos
13 = 55a 59 anos
14 = 60 a 64 anos
15 = 65 a 69 anos
16 = 70 a 74 anos
17 = 75 y más anos"	
grupoeta	numerico	8		Grupo etáreo	"1 = menores 1 ano
2 = 1 a 4 anos
3 = 5 a 14 anos
4 = 15 a 44 anos
5 = 45 a 59 anos
6 = Mayores de 60 anos"	
mesper	numerico	8		Mes contable		
anoper	numerico	8		Ano contable		
regional	numerico	8		Regional 	"1 = Cali
2 = Medellín
3 = Caribe
4 = Bogotá
5 = Eje Cafetero
7 = Exterior
8 = Unión Temporal"	
programe	cadena	4		Programa con extensión anos dorados; Es decir no diferencia la extensión anos dorados.	"ORO = ORO
PLAT = PLATA
CLAS = CLASICO
HCM = HCM
SAOR = Salud Oral
TRAD = TRADICIONAL
CEM = CEM"	primaria

faltan campos que no estan en la estructura pero si en los archivos
//FIN GUIA

*/

$query_verificar_tabla_existe="";
$query_verificar_tabla_existe.="
	SELECT * FROM pg_tables WHERE schemaname='public' and tablename='poblacion_para_analizar'
";

$error_bd_seq="";
$resultados_existe_tabla=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($query_verificar_tabla_existe, $error_bd_seq);		
if($error_bd_seq!="")
{
    echo "Error al consultar la existencia de la tabla.<br>";
}

if(count($resultados_existe_tabla)==0 || !is_array($resultados_existe_tabla) )
{

	//DROP TABLE poblacion_para_analizar;

	$query_crea_tabla="
		CREATE TABLE poblacion_para_analizar
		(
			c0_sucursal character varying(2),
			c1_tipocont character varying(2),
			c2_nitcontr numeric(11),
			c3_nombrect character varying(30),
			c4_plantari character varying(4),
			c5_numecont numeric(8),
			c6_anorenov numeric(4),
			c7_mesrenov numeric(2),
			c8_diarenov numeric(2),
			c9_cuotacs numeric(15),
			c10_familias numeric(6),
			c11_cuotaus numeric(15),
			c12_nitusuar numeric(11),
			c13_nombreus character varying(31),
			c14_anonacim numeric(4),
			c15_mesnacim numeric(2),
			c16_dianacim numeric(2),
			c17_sexo numeric(1),
			c18_ingsisaa numeric(4),
			c19_ingsismm numeric(2),
			c20_ingsisdd numeric(2),
			c21_ingcooaa numeric(4),
			c22_ingcoomm numeric(2),
			c23_ingcoodd numeric(2),
			c24_numeusua numeric(2),
			c25_faminro numeric(5),
			c26_parentes character varying(2),
			c27_tipousua character varying(1),
			c28_ocupaci character varying(320),
			c29_estadoci character varying(320),
			c30_programa character varying(4),			
			c31_mesper numeric(8),
			c32_anoper numeric(8),
			c33_fechacont character varying(320),
			c34_edad numeric(8),
			c35_grupoed numeric(8),
			c36_grupoeta numeric(8),
			c37_programe character varying(4),
			c38_regional numeric(8),
			c39_regionaleps character varying(320),
			c40_ciclovital character varying(320),
			c41_ngupc character varying(320),
			c42_program character varying(320),
			c43_un character varying(320),
			c44_planes numeric(8),
			c45_consecutivo character varying(320),
			c46_periodo character varying(320),
			c47_llave_coom character varying(320),
			c48_credencial character varying(320),	
			PRIMARY KEY(c0_sucursal, c1_tipocont, c2_nitcontr, c4_plantari, c5_numecont, c6_anorenov, c7_mesrenov, c8_diarenov, c12_nitusuar, c17_sexo, c18_ingsisaa, c19_ingsismm, c20_ingsisdd, c21_ingcooaa, c22_ingcoomm, c23_ingcoodd, c24_numeusua, c25_faminro, c26_parentes, c30_programa, c44_planes, c34_edad, c35_grupoed, c36_grupoeta, c31_mesper, c32_anoper, c38_regional, c37_programe )
		
		);
	";
	//ALTER TABLE poblacion_para_analizar ADD COLUMN c47_llave_coom character varying(320);
	//ALTER TABLE poblacion_para_analizar ADD COLUMN c48_credencial character varying(320);
	/*
	ALTER TABLE poblacion_para_analizar ADD COLUMN c48_credencial character varying(320);
	ALTER TABLE poblacion_para_analizar_2012 ADD COLUMN c48_credencial character varying(320);
	ALTER TABLE poblacion_para_analizar_2013 ADD COLUMN c48_credencial character varying(320);
	ALTER TABLE poblacion_para_analizar_2014 ADD COLUMN c48_credencial character varying(320);
	ALTER TABLE poblacion_para_analizar_2015 ADD COLUMN c48_credencial character varying(320);
	ALTER TABLE poblacion_para_analizar_2016 ADD COLUMN c48_credencial character varying(320);

	UPDATE poblacion_para_analizar  SET c48_credencial=c0_sucursal || '-' || c5_numecont  || '-' || c10_familias  || '-' || c12_nitusuar WHERE c10_familias IS NOT NULL;	
	UPDATE poblacion_para_analizar_2012  SET c48_credencial=c0_sucursal || '-' || c5_numecont  || '-' || c10_familias  || '-' || c12_nitusuar WHERE c10_familias IS NOT NULL;	
	UPDATE poblacion_para_analizar_2013  SET c48_credencial=c0_sucursal || '-' || c5_numecont  || '-' || c10_familias  || '-' || c12_nitusuar WHERE c10_familias IS NOT NULL;	
	UPDATE poblacion_para_analizar_2014  SET c48_credencial=c0_sucursal || '-' || c5_numecont  || '-' || c10_familias  || '-' || c12_nitusuar WHERE c10_familias IS NOT NULL;	
	UPDATE poblacion_para_analizar_2015  SET c48_credencial=c0_sucursal || '-' || c5_numecont  || '-' || c10_familias  || '-' || c12_nitusuar WHERE c10_familias IS NOT NULL;	
	UPDATE poblacion_para_analizar_2016  SET c48_credencial=c0_sucursal || '-' || c5_numecont  || '-' || c10_familias  || '-' || c12_nitusuar WHERE c10_familias IS NOT NULL;
	*/
	//ALTER TABLE poblacion_para_analizar ALTER COLUMN c10_familias type numeric(6); //se cambio a 6 porque algunos codigos exceden de 5
	//NOTA: las columnas c31_mesper, c32_anoper puede que sean claves
	/*
	c48_credencial esta compuesta por 
	Codigo Sucursal  : Campo 0
       Numero de Contrato..  Campo 5
       Numero de Familia: Campo 11 debe ser el campo 10 c10_familias, ya que el campo 11 es tarifa que paga el afiliado
       Numero Identificacion Usario : Campo 12 
	*/
      /*
      reporte 1

     */
	$error_bd_seq="";
	$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($query_crea_tabla, $error_bd_seq);
	if($error_bd_seq!="")
	{
		echo "error al crear la tabla debido a que no existia previamente ".$error_bd_seq."<br>";
	}//fin if
}//fin if




function alphanumericAndSpace( $string )
{
	$string = str_replace("á","a",$string);
	$string = str_replace("é","e",$string);
	$string = str_replace("í","i",$string);
	$string = str_replace("ó","o",$string);
	$string = str_replace("ú","u",$string);
	$string = str_replace("Á","A",$string);
	$string = str_replace("É","E",$string);
	$string = str_replace("Í","I",$string);
	$string = str_replace("Ó","O",$string);
	$string = str_replace("Ú","U",$string);
	
	$string = str_replace("n","n",$string);
	$string = str_replace("Ñ","N",$string);
    return trim(preg_replace('/[^a-zA-Z0-9\s, ;\-@.\/]/', '', $string));
}

function alphanumericAndSpace_include_br( $string )
{
	$string = str_replace("á","a",$string);
	$string = str_replace("é","e",$string);
	$string = str_replace("í","i",$string);
	$string = str_replace("ó","o",$string);
	$string = str_replace("ú","u",$string);
	$string = str_replace("Á","A",$string);
	$string = str_replace("É","E",$string);
	$string = str_replace("Í","I",$string);
	$string = str_replace("Ó","O",$string);
	$string = str_replace("Ú","U",$string);
	
	$string = str_replace("n","n",$string);
	$string = str_replace("Ñ","N",$string);
    return trim(preg_replace('/[^a-zA-Z0-9\s, ;\-@.\/\<\>\_\:]/', '', $string));
}

function alphanumericAndSpace4( $string )
{
    $string = str_replace("á","a",$string);
    $string = str_replace("é","e",$string);
    $string = str_replace("í","i",$string);
    $string = str_replace("ó","o",$string);
    $string = str_replace("ú","u",$string);
    $string = str_replace("Á","A",$string);
    $string = str_replace("É","E",$string);
    $string = str_replace("Í","I",$string);
    $string = str_replace("Ó","O",$string);
    $string = str_replace("Ú","U",$string);
    
    $string = str_replace("n","n",$string);
    $string = str_replace("Ñ","N",$string);
    $cadena = preg_replace('/[^a-zA-Z0-9\s,\-\/\.]/', '', $string);
    return $cadena;
}

function procesar_mensaje($mensaje)
{
	$mensaje_procesado = str_replace("á","a",$mensaje);
	$mensaje_procesado = str_replace("é","e",$mensaje_procesado);
	$mensaje_procesado = str_replace("í","i",$mensaje_procesado);
	$mensaje_procesado = str_replace("ó","o",$mensaje_procesado);
	$mensaje_procesado = str_replace("ú","u",$mensaje_procesado);
	$mensaje_procesado = str_replace("n","n",$mensaje_procesado);
	$mensaje_procesado = str_replace("Á","A",$mensaje_procesado);
	$mensaje_procesado = str_replace("É","E",$mensaje_procesado);
	$mensaje_procesado = str_replace("Í","I",$mensaje_procesado);
	$mensaje_procesado = str_replace("Ó","O",$mensaje_procesado);
	$mensaje_procesado = str_replace("Ú","U",$mensaje_procesado);
	$mensaje_procesado = str_replace("Ñ","N",$mensaje_procesado);
	$mensaje_procesado = str_replace(" "," ",$mensaje_procesado);
	$mensaje_procesado = str_replace("'"," ",$mensaje_procesado);
	$mensaje_procesado = str_replace("\n"," ",$mensaje_procesado);
	$mensaje_procesado = alphanumericAndSpace_include_br($mensaje_procesado);
	
	return $mensaje_procesado;
}

function contar_lineas_archivo($ruta_file)
{
	$linecount = 0;
	$handle = fopen($ruta_file, "r");
	while(!feof($handle))
	{
	  $line = fgets($handle);
	  $linecount++;
	}

	fclose($handle);

	return $linecount;
}//fin funcion

function RemoveBS($Str) {  
  $StrArr = str_split($Str); $NewStr = '';
  foreach ($StrArr as $Char) {    
    $CharNo = ord($Char);
    if ($CharNo == 163) { $NewStr .= $Char; continue; } // keep £ 
    if ($CharNo > 31 && $CharNo < 127) {
      $NewStr .= $Char;    
    }
  }  
  return $NewStr;
}//fin function

function xcantidad9($cantidad)
{
	$cont9es=0;
	$resultado="";
	while($cont9es<$cantidad)
	{
		$resultado.="9";
		$cont9es++;
	}
	return $resultado;
}//fin function

$menu= $_SESSION['menu_logueo_html'];
$nombre= $_SESSION['nombre_completo'];

$perfil_usuario_actual= $_SESSION['tipo_perfil'];
$entidad_salud_usuario_actual= $_SESSION['entidad_asociada_al_nick'];

$tipo_id=$_SESSION['tipo_id'];
$identificacion=$_SESSION['identificacion'];



$nick_user=$_SESSION['usuario'];

$correo_electronico=$_SESSION['correo'];

$perfil_usuario_actual= $_SESSION['tipo_perfil'];
$entidad_salud_usuario_actual= $_SESSION['entidad_asociada_al_nick'];

session_write_close();

$mensaje="";
$mostrarResultado="none";

$smarty->assign("mensaje_proceso", $mensaje, true);
$smarty->assign("mostrarResultado", $mostrarResultado, true);
$smarty->assign("nombre", $nombre, true);
$smarty->assign("menu", $menu, true);
$smarty->display('est_poblacion.html.tpl');

//INSERT INTO gios_menus_opciones_sistema(id_principal,id_padre,nombre_opcion,descripcion_ayuda,tiene_submenus,ruta_interfaz,prioridad_jerarquica) VALUES ('213','109','Analisis Est. Poblacion','',FALSE,'..|estadisticas_poblacion|est_poblacion.php','50');

//INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('213','5'); //admin sistema
//INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('213','4'); //admin eapb
//INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('213','3'); //usuario normal eapb
//INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('213','2'); //admin ips
//INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('213','1'); //usuario normal ips

/*
INSERT INTO gios_menus_opciones_sistema(id_principal,id_padre,nombre_opcion,descripcion_ayuda,tiene_submenus,ruta_interfaz,prioridad_jerarquica) VALUES ('213','109','Analisis Est. Poblacion','',FALSE,'..|estadisticas_poblacion|est_poblacion.php','50');

INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('213','5'); 

INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('213','4'); 

INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('213','3'); 

INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('213','2'); 

INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('213','1'); 

*/

date_default_timezone_set ("America/Bogota");
$fecha_actual = date('Y-m-d');
$tiempo_actual = date('H:i:s');
$tiempo_actual_string=str_replace(":","-",$tiempo_actual);

$fecha_para_archivo=str_replace("-", "", $fecha_actual ).str_replace(":", "", $tiempo_actual );

if( isset($_FILES["archivo_poblacion"]) 
	&& isset($_REQUEST["activa_generar"]) && $_REQUEST["activa_generar"]=="NO"
 )
{	
	
	//DIRECTORIO DE LOS ARCHIVOS
	$ruta_destino= '../TEMPORALES/';
	$nueva_carpeta=$ruta_destino."carga_archpobl".$fecha_para_archivo;
	if(!file_exists($nueva_carpeta))
	{
	    mkdir($nueva_carpeta, 0700, true);
	}
	else
	{
	    $files_to_erase = glob($nueva_carpeta."/*"); // get all file names
	    foreach($files_to_erase as $file_to_be_erased)
	    { // iterate files
	      if(is_file($file_to_be_erased))
	      {
			unlink($file_to_be_erased); // delete file
	      }
	    }
	}
	$ruta_destino=$nueva_carpeta."/";
	//FIN DIRECTORIO DE LOS ARCHIVOS

	$archivo_poblacion_a_cargar=$_FILES["archivo_poblacion"];
	$ruta_archivo_poblacion_a_cargar = $ruta_destino . $archivo_poblacion_a_cargar['name'];
	$ruta_archivo_poblacion_a_cargar=utf8_encode(str_replace("ñ", "n", utf8_decode($ruta_archivo_poblacion_a_cargar) )  );	
	$ruta_archivo_poblacion_a_cargar=utf8_encode(str_replace("Ñ", "N", utf8_decode($ruta_archivo_poblacion_a_cargar) )  );
	//move_uploaded_file($archivo_poblacion_a_cargar['tmp_name'],utf8_decode($ruta_archivo_poblacion_a_cargar) );
	move_uploaded_file($archivo_poblacion_a_cargar['tmp_name'],$ruta_archivo_poblacion_a_cargar );

	//echo "ruta sin decode ni encode :".$ruta_archivo_poblacion_a_cargar." , ruta con decode: ".utf8_decode($ruta_archivo_poblacion_a_cargar)." y ruta con encode: ".utf8_encode($ruta_archivo_poblacion_a_cargar);
		
	//archivo que se lee
	$lineas_del_archivo = contar_lineas_archivo($ruta_archivo_poblacion_a_cargar); 
	$archivo_cargar = fopen($ruta_archivo_poblacion_a_cargar, 'r') or exit("No se pudo abrir el archivo con los datos");
	
	//archivos que se crean
	$ruta_1=$ruta_destino."tabla_poblacion_para_analizar$fecha_para_archivo.sql.csv";
	$ruta_2=$ruta_destino."tabla_poblacion_para_analizar$fecha_para_archivo.error.csv";
	$ruta_3=$ruta_destino."registros_rechazados$fecha_para_archivo.csv";
	$ruta_4=$ruta_destino."tabla_poblacion_para_analizar$fecha_para_archivo.edupl.csv";
	$ruta_5=$ruta_destino."registros_rechazados_dupl$fecha_para_archivo.csv";
	$ruta_6=$ruta_destino."numero_reg_aciertos$fecha_para_archivo.csv";
	$ruta_7=$ruta_destino."numero_reg_camp_en_blanco$fecha_para_archivo.csv";
	$ruta_8=$ruta_destino."numero_reg_num_camp_diferentes$fecha_para_archivo.csv";

	$archivo_queries = fopen($ruta_1, "w") or die("fallo la creacion del archivo");
	$archivo_error= fopen($ruta_2, "w") or die("fallo la creacion del archivo");
	$archivo_rechazados= fopen($ruta_3, "w") or die("fallo la creacion del archivo");
	$archivo_error_dupl= fopen($ruta_4, "w") or die("fallo la creacion del archivo");
	$archivo_rechazados_dupl= fopen($ruta_5, "w") or die("fallo la creacion del archivo");
	$archivo_aciertos= fopen($ruta_6, "w") or die("fallo la creacion del archivo");
	$archivo_campos_en_blanco= fopen($ruta_7, "w") or die("fallo la creacion del archivo");
	$archivo_numero_campos_diferentes= fopen($ruta_8, "w") or die("fallo la creacion del archivo");
	
	$cont_linea=0;
	$aciertos=0;
	$errores=0;
	$mensaje="";
	$sql_carga="";

	//echo "codificacion del cliente: ".$coneccionBD->obtener_codificacion_cliente();
	$porcentaje=0;
	$muestra_primer_mensaje=true;
	while (!feof($archivo_cargar)) 
	{
		$numero_linea_actual_desde_uno=($cont_linea+1);

		$linea_org = fgets($archivo_cargar);
		$linea= utf8_encode($linea_org);//el encode arregla la linea leida a formato utf8 que tiene la base de datos
		$campos = explode("\t",$linea);
		if(count($campos)==1)
		{
			$campos = explode("|",$linea);
		}

		$campos=array_map("trim",$campos);

		$campos=str_replace("\"", "", $campos);
		$campos=str_replace("'", "", $campos);

		$string_valores="";
		
		$cont_campos=0;
		$tiene_campos_en_blanco=false;
		$mensaje_campo_esta_en_blanco="";
		$mensaje_campo_esta_en_blanco.="Los siguientes campos de la linea $numero_linea_actual_desde_uno que tiene ".count($campos)." campos, estan en blanco: ";
		while($cont_campos < count($campos))
		{
			if($string_valores!=""){$string_valores.=",";}	
			$campo_preparado=$campos[$cont_campos];	
			$campo_preparado = str_replace("á","a",$campo_preparado);
			$campo_preparado = str_replace("é","e",$campo_preparado);
			$campo_preparado = str_replace("í","i",$campo_preparado);
			$campo_preparado = str_replace("ó","o",$campo_preparado);
			$campo_preparado = str_replace("ú","u",$campo_preparado);
			$campo_preparado = str_replace("ñ","n",$campo_preparado);
			$campo_preparado = str_replace("Á","A",$campo_preparado);
			$campo_preparado = str_replace("É","E",$campo_preparado);
			$campo_preparado = str_replace("Í","I",$campo_preparado);
			$campo_preparado = str_replace("Ó","O",$campo_preparado);
			$campo_preparado = str_replace("Ú","U",$campo_preparado);			
			$campo_preparado = str_replace("ñ","n",$campo_preparado);			
			$campo_preparado = str_replace("Ñ","N",$campo_preparado);
			$campo_preparado=RemoveBS($campo_preparado);	
			$campo_preparado=preg_replace("/[^A-Za-z0-9: ;\-@.\/]/", "", $campo_preparado );	
			if(trim($campo_preparado)=="" )
			{
				$mensaje_campo_esta_en_blanco.="campo_".$cont_campos." ";
				$tiene_campos_en_blanco=true;
				

				

				//c2_nitcontr numeric(11),
				if($cont_campos==2)
				{
					$campo_preparado=xcantidad9(11);
				}//fin if

				
				//c5_numecont numeric(8),
				if($cont_campos==5)
				{
					$campo_preparado=xcantidad9(8);
				}//fin if

				//c6_anorenov numeric(4),
				if($cont_campos==6)
				{
					$campo_preparado=xcantidad9(4);
				}//fin if
				
				//c7_mesrenov numeric(2),
				if($cont_campos==7)
				{
					$campo_preparado=xcantidad9(2);
				}//fin if

				//c8_diarenov numeric(2),
				if($cont_campos==8)
				{
					$campo_preparado=xcantidad9(2);
				}//fin if

				//c9_cuotacs numeric(15),
				if($cont_campos==9)
				{
					$campo_preparado=xcantidad9(15);
				}//fin if

				//c10_familias numeric(5),
				if($cont_campos==10)
				{
					//$campo_preparado=xcantidad9(5);
					$campo_preparado=xcantidad9(6);
				}//fin if

				//c11_cuotaus numeric(15),
				if($cont_campos==11)
				{
					$campo_preparado=xcantidad9(15);
				}//fin if

				//c12_nitusuar numeric(11),
				if($cont_campos==12)
				{
					$campo_preparado=xcantidad9(11);
				}//fin if				
				
				//c14_anonacim numeric(4),
				if($cont_campos==14)
				{
					$campo_preparado=xcantidad9(4);
				}//fin if
				
				//c15_mesnacim numeric(2),
				if($cont_campos==15)
				{
					$campo_preparado=xcantidad9(2);
				}//fin if
				
				//c16_dianacim numeric(2),
				if($cont_campos==16)
				{
					$campo_preparado=xcantidad9(2);
				}//fin if
				
				//c17_sexo numeric(1),
				if($cont_campos==17)
				{
					$campo_preparado=xcantidad9(1);
				}//fin if
				
				//c18_ingsisaa numeric(4),
				if($cont_campos==18)
				{
					$campo_preparado=xcantidad9(4);
				}//fin if
				
				//c19_ingsismm numeric(2),
				if($cont_campos==19)
				{
					$campo_preparado=xcantidad9(2);
				}//fin if
				
				//c20_ingsisdd numeric(2),
				if($cont_campos==20)
				{
					$campo_preparado=xcantidad9(2);
				}//fin if
				
				//c21_ingcooaa numeric(4),
				if($cont_campos==21)
				{
					$campo_preparado=xcantidad9(4);
				}//fin if
				
				//c22_ingcoomm numeric(2),
				if($cont_campos==22)
				{
					$campo_preparado=xcantidad9(2);
				}//fin if
				
				//c23_ingcoodd numeric(2),
				if($cont_campos==23)
				{
					$campo_preparado=xcantidad9(2);
				}//fin if
				
				//c24_numeusua numeric(2),
				if($cont_campos==24)
				{
					$campo_preparado=xcantidad9(2);
				}//fin if
				
				//c25_faminro numeric(5),
				if($cont_campos==25)
				{
					$campo_preparado=xcantidad9(5);
				}//fin if				
						
				//c31_mesper numeric(8),
				if($cont_campos==31)
				{
					$campo_preparado=xcantidad9(8);
				}//fin if
				
				//c32_anoper numeric(8),
				if($cont_campos==32)
				{
					$campo_preparado=xcantidad9(8);
				}//fin if
								
				//c34_edad numeric(8),
				if($cont_campos==34)
				{
					$campo_preparado=xcantidad9(8);
				}//fin if
				
				//c35_grupoed numeric(8),
				if($cont_campos==35)
				{
					$campo_preparado=xcantidad9(8);
				}//fin if
				
				//c36_grupoeta numeric(8),
				if($cont_campos==36)
				{
					$campo_preparado=xcantidad9(8);
				}//fin if
				
				
				//c38_regional numeric(8),
				if($cont_campos==38)
				{
					$campo_preparado=xcantidad9(8);
				}//fin if
				
				//c44_planes numeric(8),
				if($cont_campos==44)
				{
					$campo_preparado=xcantidad9(8);
				}//fin if
				
				
			}//fin if campo esta en blanco		
			$string_valores.="'".$campo_preparado."'";
			$cont_campos++;
		}//fin while
		$mensaje_campo_esta_en_blanco.=" . \n";
		if($tiene_campos_en_blanco==true)
		{
			fwrite($archivo_campos_en_blanco, $mensaje_campo_esta_en_blanco);
		}//fin if

		$total_campos=count($campos);

		$lista_columnas="";

		if($total_campos==45)
		{
			$lista_columnas.="
			c0_sucursal,
			c1_tipocont,
			c2_nitcontr,
			c3_nombrect,
			c4_plantari,
			c5_numecont,
			c6_anorenov,
			c7_mesrenov,
			c8_diarenov,
			c9_cuotacs,
			c10_familias,
			c11_cuotaus,
			c12_nitusuar,
			c13_nombreus,
			c14_anonacim,
			c15_mesnacim,
			c16_dianacim,
			c17_sexo,
			c18_ingsisaa,
			c19_ingsismm,
			c20_ingsisdd,
			c21_ingcooaa,
			c22_ingcoomm,
			c23_ingcoodd,
			c24_numeusua,
			c25_faminro,
			c26_parentes,
			c27_tipousua,
			c28_ocupaci,
			c29_estadoci,
			c30_programa,			
			c31_mesper,
			c32_anoper,
			c33_fechacont,
			c34_edad,
			c35_grupoed,
			c36_grupoeta,
			c37_programe,
			c38_regional,
			c39_regionaleps,
			c40_ciclovital,
			c41_ngupc,
			c42_program,
			c43_un,
			c44_planes
			";

		}//fin if columnas para 45 campos
		else if($total_campos==46)
		{
			$lista_columnas.="
			c0_sucursal,
			c1_tipocont,
			c2_nitcontr,
			c3_nombrect,
			c4_plantari,
			c5_numecont,
			c6_anorenov,
			c7_mesrenov,
			c8_diarenov,
			c9_cuotacs,
			c10_familias,
			c11_cuotaus,
			c12_nitusuar,
			c13_nombreus,
			c14_anonacim,
			c15_mesnacim,
			c16_dianacim,
			c17_sexo,
			c18_ingsisaa,
			c19_ingsismm,
			c20_ingsisdd,
			c21_ingcooaa,
			c22_ingcoomm,
			c23_ingcoodd,
			c24_numeusua,
			c25_faminro,
			c26_parentes,
			c27_tipousua,
			c28_ocupaci,
			c29_estadoci,
			c30_programa,			
			c31_mesper,
			c32_anoper,
			c33_fechacont,
			c34_edad,
			c35_grupoed,
			c36_grupoeta,
			c37_programe,
			c38_regional,
			c39_regionaleps,
			c40_ciclovital,
			c41_ngupc,
			c42_program,
			c43_un,
			c44_planes,
			c47_llave_coom
			";

		}//fin if columnas para 46 campos
		else if($total_campos==47)
		{
			$lista_columnas.="
			c0_sucursal,
			c1_tipocont,
			c2_nitcontr,
			c3_nombrect,
			c4_plantari,
			c5_numecont,
			c6_anorenov,
			c7_mesrenov,
			c8_diarenov,
			c9_cuotacs,
			c10_familias,
			c11_cuotaus,
			c12_nitusuar,
			c13_nombreus,
			c14_anonacim,
			c15_mesnacim,
			c16_dianacim,
			c17_sexo,
			c18_ingsisaa,
			c19_ingsismm,
			c20_ingsisdd,
			c21_ingcooaa,
			c22_ingcoomm,
			c23_ingcoodd,
			c24_numeusua,
			c25_faminro,
			c26_parentes,
			c27_tipousua,
			c28_ocupaci,
			c29_estadoci,
			c30_programa,			
			c31_mesper,
			c32_anoper,
			c33_fechacont,
			c34_edad,
			c35_grupoed,
			c36_grupoeta,
			c37_programe,
			c38_regional,
			c39_regionaleps,
			c40_ciclovital,
			c41_ngupc,
			c42_program,
			c43_un,
			c44_planes,
			c45_consecutivo,
			c46_periodo
			";

		}//fin if columnas para 47 campos
		else
		{
			$lista_columnas.="
			c0_sucursal,
			c1_tipocont,
			c2_nitcontr,
			c3_nombrect,
			c4_plantari,
			c5_numecont,
			c6_anorenov,
			c7_mesrenov,
			c8_diarenov,
			c9_cuotacs,
			c10_familias,
			c11_cuotaus,
			c12_nitusuar,
			c13_nombreus,
			c14_anonacim,
			c15_mesnacim,
			c16_dianacim,
			c17_sexo,
			c18_ingsisaa,
			c19_ingsismm,
			c20_ingsisdd,
			c21_ingcooaa,
			c22_ingcoomm,
			c23_ingcoodd,
			c24_numeusua,
			c25_faminro,
			c26_parentes,
			c27_tipousua,
			c28_ocupaci,
			c29_estadoci,
			c30_programa,			
			c31_mesper,
			c32_anoper,
			c33_fechacont,
			c34_edad,
			c35_grupoed,
			c36_grupoeta,
			c37_programe,
			c38_regional,
			c39_regionaleps,
			c40_ciclovital,
			c41_ngupc,
			c42_program,
			c43_un,
			c44_planes,
			c45_consecutivo,
			c46_periodo
			";

			fwrite($archivo_numero_campos_diferentes, "La linea numero $numero_linea_actual_desde_uno, no posee una cantidad de campos aceptables $total_campos, \n");

		}//fin if columnas para 45 campos



		$sql_carga="";
		$sql_carga.="INSERT INTO  poblacion_para_analizar 
		(
		$lista_columnas
		)
		VALUES
		(
		$string_valores
		);
		";

		fwrite($archivo_queries, $sql_carga."\n");

		
		
		$error_bd="";
		try
		{
				$bool_funciono=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_carga, $error_bd);
				if($error_bd!="")
				{
					$mensaje_error="";
					//decode sirve para leer los mensajes de error de postgresql que esta en utf8
					$mensaje_error.="ERROR query, en la linea $numero_linea_actual_desde_uno , con un numero de campos $total_campos :\n".utf8_decode($error_bd)."\n";
					$mensaje_error.="Numero linea: $numero_linea_actual_desde_uno, array de campos de dicha linea separado por pipe |: ".implode("|", $campos)."\n";
					
					$errores++;

					$es_dupl="llave duplicada viola restricción de unicidad";
					if(strpos(utf8_decode($error_bd),$es_dupl)===false )
					{
						fwrite($archivo_error,$mensaje_error);
						fwrite($archivo_rechazados,$linea_org);
					}
					else
					{
						fwrite($archivo_error_dupl,$mensaje_error);
						fwrite($archivo_rechazados_dupl,$linea_org);
					}
				}
				else
				{
					fwrite($archivo_aciertos,$numero_linea_actual_desde_uno.",");
					$aciertos++;
				}
				
		}
		catch (Exception $e) 
		{
			fwrite($archivo_error,"ERROR excepcion, en la linea $numero_linea_actual_desde_uno , con un numero de campos $total_campos:\n".$e->getMessage()."\n");
			fwrite($archivo_error,"Numero linea: $numero_linea_actual_desde_uno, array de campos de dicha linea separado por pipe |: ".implode("|", $campos)."\n");
			$errores++;
		}

		$porcentaje_temp=0;
		if($lineas_del_archivo>0)
		{
			$porcentaje_temp=intval(($numero_linea_actual_desde_uno*100)/$lineas_del_archivo);
		}//fin if

		

		
		if($porcentaje_temp!=$porcentaje || ($numero_linea_actual_desde_uno>=$lineas_del_archivo) || $muestra_primer_mensaje==true)
		{
			$muestra_primer_mensaje=false;
			$porcentaje=$porcentaje_temp;
			$array_ruta=explode("/",$ruta_archivo_poblacion_a_cargar);
			$nombre_archivo_para_mostrar=$array_ruta[count($array_ruta)-1];
			$mensaje="";
			$mensaje.="Archivo: $nombre_archivo_para_mostrar<br>";
			$mensaje.="Linea $numero_linea_actual_desde_uno de $lineas_del_archivo , con un numero de campos $total_campos Porcentaje: $porcentaje %<br>";		
			$mensaje.="<span style='color:green;'>registros subidos $aciertos </span><br>";
			$mensaje.="<span style='color:red;'>registros no se pudieron subir $errores </span><br>";
			echo "<script>document.getElementById('mensaje_div').innerHTML=\"$mensaje\";</script>";
			ob_flush();
			flush();	
			
		}//fin if	
		
		$cont_linea++;
		

	}//fin while
	fclose($archivo_cargar);
	fclose($archivo_queries);
	fclose($archivo_error);
	fclose($archivo_rechazados);
	fclose($archivo_error_dupl);
	fclose($archivo_rechazados_dupl);
	fclose($archivo_aciertos);
	fclose($archivo_campos_en_blanco);
	fclose($archivo_numero_campos_diferentes);

	//CREAR ZIP
	/*
	$archivos_a_comprimir=array();
	$archivos_a_comprimir[]=$ruta_1;
	$archivos_a_comprimir[]=$ruta_2;
	$archivos_a_comprimir[]=$ruta_3;
	$ruta_zip=$ruta_destino."incons_carga_pobl_".$fecha_para_archivo.'.zip';
	if(file_exists($ruta_zip))
	{
		unlink($ruta_zip);
	}
	$result_zip = create_zip($archivos_a_comprimir,$ruta_zip);	
	*/
	//FIN CREAR ZIP

	
	//BOTONES DESCARGA
	$botones="";
	//$botones.=" <input type=\'button\' value=\'Descargar Queries, Inconsistencias, Registros Rechazados\'  class=\'btn btn-success color_boton\' onclick=\"download_file(\'$ruta_zip\');\"/> ";
	$botones.="<br><input type=\'button\' value=\'Descargar Queries\'  class=\'btn btn-success color_boton\' onclick=\"download_file(\'$ruta_1\');\"/> ";
	$botones.="<br><input type=\'button\' value=\'Descargar Log Errores\'  class=\'btn btn-success color_boton\' onclick=\"download_file(\'$ruta_2\');\"/> ";
	$botones.="<br><input type=\'button\' value=\'Descargar Registros Rechazados\'  class=\'btn btn-success color_boton\' onclick=\"download_file(\'$ruta_3\');\"/> ";
	$botones.="<br><input type=\'button\' value=\'Descargar Log Duplicados\'  class=\'btn btn-success color_boton\' onclick=\"download_file(\'$ruta_4\');\"/> ";
	$botones.="<br><input type=\'button\' value=\'Descargar Registros Duplicados\'  class=\'btn btn-success color_boton\' onclick=\"download_file(\'$ruta_5\');\"/> ";
	$botones.="<br><input type=\'button\' value=\'Descargar Numero Registros Exitosos\'  class=\'btn btn-success color_boton\' onclick=\"download_file(\'$ruta_6\');\"/> ";
	$botones.="<br><input type=\'button\' value=\'Descargar Registros con campos en blanco\'  class=\'btn btn-success color_boton\' onclick=\"download_file(\'$ruta_7\');\"/> ";
	$botones.="<br><input type=\'button\' value=\'Descargar Numero Registros con numero de campos diferentes\'  class=\'btn btn-success color_boton\' onclick=\"download_file(\'$ruta_8\');\"/> ";
	
	//FIN BOTONES DESCARGA

	if(connection_aborted()==false)
	{
		echo "<script>document.getElementById('div_mensaje_exito').style.display='inline';</script>";
		echo "<script>document.getElementById('parrafo_mensaje_exito').innerHTML='$botones';</script>";
		ob_flush();
		flush();
	}


}//fin if



if(isset($_REQUEST["activa_generar"]) && $_REQUEST["activa_generar"]=="SI")
{
	

	$mensaje_error="";

	$mensaje_error.= "entro a generar un  reporte<br>";

	$hubo_error_inicio=false;

	$escribio_info=false;
	//DIRECTORIO DE LOS ARCHIVOS
	$ruta_destino= '../TEMPORALES/';
	$nueva_carpeta=$ruta_destino."repopobl".$fecha_para_archivo;
	if(!file_exists($nueva_carpeta))
	{
	    mkdir($nueva_carpeta, 0700, true);
	}
	else
	{
	    $files_to_erase = glob($nueva_carpeta."/*"); // get all file names
	    foreach($files_to_erase as $file_to_be_erased)
	    { // iterate files
	      if(is_file($file_to_be_erased))
	      {
			unlink($file_to_be_erased); // delete file
	      }
	    }
	}
	$ruta_destino=$nueva_carpeta."/";
	//FIN DIRECTORIO DE LOS ARCHIVOS

	$ruta_repo1=$ruta_destino."reporte1$fecha_para_archivo.csv";
	$archivo_reporte_comprobacion_inicial = fopen($ruta_repo1, "w") or die("fallo la creacion del archivo");

	$ruta_repo2=$ruta_destino."reporte2$fecha_para_archivo.csv";
	$archivo_reporte_especificos = fopen($ruta_repo2, "w") or die("fallo la creacion del archivo");

	echo "Ruta Destino Server: ".$ruta_destino."<br>";

	

	$tipo_reporte="";
	if(isset($_REQUEST["tipo_reporte"]) && trim($_REQUEST["tipo_reporte"])!="" && trim($_REQUEST["tipo_reporte"])!="0")
	{
		$tipo_reporte=trim($_REQUEST["tipo_reporte"]);
	}//fin if tipo reporte
	else
	{
		$mensaje_error.= "Seleccione un reporte<br>";
		$hubo_error_inicio=true;
	}//fin else
	$tipo_programa="ORO";
	if(isset($_REQUEST["tipo_programa"]) && trim($_REQUEST["tipo_programa"])!="" && trim($_REQUEST["tipo_programa"])!="0")
	{
		$tipo_programa=trim($_REQUEST["tipo_programa"]);
	}//fin if tipo reporte
	else
	{
		$mensaje_error.= "Seleccione un Programa<br>";
		$hubo_error_inicio=true;
	}//fin else
	$year_seleccionado="";
	if(isset($_REQUEST["selector_years"]) && trim($_REQUEST["selector_years"])!="" && trim($_REQUEST["selector_years"])!="0")
	{
		$year_seleccionado=trim($_REQUEST["selector_years"]);
	}//fin if tipo reporte
	else
	{
		$mensaje_error.= "Seleccione una opcion para a&ntilde;os<br>";
		$hubo_error_inicio=true;
	}//fin else

	$array_tablas_por_years_pobl_coom=array();
	if($year_seleccionado=="" || $year_seleccionado=="todos")
	{
		$array_tablas_por_years_pobl_coom["2012"]="poblacion_para_analizar_2012";
		$array_tablas_por_years_pobl_coom["2013"]="poblacion_para_analizar_2013";
		$array_tablas_por_years_pobl_coom["2014"]="poblacion_para_analizar_2014";
		$array_tablas_por_years_pobl_coom["2015"]="poblacion_para_analizar_2015";
		$array_tablas_por_years_pobl_coom["2016"]="poblacion_para_analizar_2016";

		echo "Se selecciono todos los a&ntilde;os.<br>";
	}
	else if($year_seleccionado=="2012")
	{
		$array_tablas_por_years_pobl_coom["2012"]="poblacion_para_analizar_2012";
		echo "Se selecciono el a&ntilde;o 2012.<br>";
	}//fin else if selecciono solo un year
	else if($year_seleccionado=="2013")
	{
		$array_tablas_por_years_pobl_coom["2013"]="poblacion_para_analizar_2013";
		echo "Se selecciono el a&ntilde;o 2013.<br>";
	}//fin else if selecciono solo un year
	else if($year_seleccionado=="2014")
	{
		$array_tablas_por_years_pobl_coom["2014"]="poblacion_para_analizar_2014";
		echo "Se selecciono el a&ntilde;o 2014.<br>";
	}//fin else if selecciono solo un year
	else if($year_seleccionado=="2015")
	{
		$array_tablas_por_years_pobl_coom["2015"]="poblacion_para_analizar_2015";
		echo "Se selecciono el a&ntilde;o 2015.<br>";
	}//fin else if selecciono solo un year
	else if($year_seleccionado=="2016")
	{
		$array_tablas_por_years_pobl_coom["2016"]="poblacion_para_analizar_2016";
		echo "Se selecciono el a&ntilde;o 2016.<br>";
	}//fin else if selecciono solo un year

	if($hubo_error_inicio==false)
	{

		foreach ($array_tablas_por_years_pobl_coom as $key_years => $tabla_year_actual) 
		{
			

			if($tipo_reporte=="1")
			{
				$mensaje="";
				$mensaje.="\"Reporte Seleccionado Tipo:\";\"$tipo_reporte\";\"Comprobacion inicial, campo credencial\"";			
				echo "Tipo Reporte: ".str_replace(array("\"",";")," ", $mensaje)."<br>";
				ob_flush();
				flush();
				//fwrite($archivo_reporte_comprobacion_inicial, $mensaje."\n");

				$cont_meses=1;
				while($cont_meses<=12)
				{
					
					//c0_sucursal || '-' || c5_numecont  || '-' || c10_familias  || '-' || c12_nitusuar
					$query_reporte="";
					$query_reporte.="
						SELECT count(*) as contador_total, count(distinct c48_credencial) as contador_por_credencial FROM $tabla_year_actual 
						WHERE c31_mesper='$cont_meses' 
						AND c32_anoper='$key_years' 
						AND c0_sucursal IS NOT NULL
						AND c5_numecont IS NOT NULL
						AND c10_familias IS NOT NULL
						AND c12_nitusuar IS NOT NULL 
						AND c48_credencial IS NOT NULL 					
					";

					$error_bd_seq="";
					$resultados_reporte=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($query_reporte, $error_bd_seq);		
					if($error_bd_seq!="")
					{
					    $mensaje_error.=  "Error al realizar la consulta del reporte actual.<br>";
					}//fin if


					$contador_filas=0;
					foreach ($resultados_reporte as $key => $fila_actual) 
					{

						$contador_poblacion_mes_year=$fila_actual["contador_total"];

						$contador_poblacion_mes_year_credencial=$fila_actual["contador_por_credencial"];

						$mensaje="";
						$mensaje.="\"$cont_meses\";del anno\";\"$key_years\";\"hay\";\"$contador_poblacion_mes_year\";\"totales\";";
						$mensaje.="\"hay\";\"$contador_poblacion_mes_year_credencial\";\"por credencial\"";
						//echo str_replace(array("\"",";")," ", $mensaje)."<br>";
						ob_flush();
						flush();
						fwrite($archivo_reporte_comprobacion_inicial, $mensaje."\n");
						$escribio_info=true;
						$contador_filas++;
						
					}//fin foreach
					
					
					echo "Termino Mes $cont_meses del Year $key_years<br>";$cont_meses++;
				}//fin contador meses
				
			}//fin tipo reporte es 1

			if($tipo_reporte=="1.2")
			{
				$mensaje="";
				$mensaje.="\"Reporte Seleccionado Tipo:\";\"$tipo_reporte\";\"Comprobacion inicial, especifica todos los programas, campo credencial\"";			
				echo "Tipo Reporte: ".str_replace(array("\"",";")," ", $mensaje)."<br>";
				ob_flush();
				flush();
				//fwrite($archivo_reporte_comprobacion_inicial, $mensaje."\n");

				$array_tprograma=array();
				$array_tprograma[]=array("ORO","ORO");
				$array_tprograma[]=array("PLAT","PLATA");
				$array_tprograma[]=array("CLAS","CLASICO");
				$array_tprograma[]=array("HCM","HCM");
				$array_tprograma[]=array("SAOR","SALUD ORAL");
				$array_tprograma[]=array("TRAD","TRADICIONAL");
				$array_tprograma[]=array("CEM","CEM");

				$cont_meses=1;
				while($cont_meses<=12)
				{
					$cont_array_tprograma=0;
					while($cont_array_tprograma<count($array_tprograma) )
					{
						$tprograma=$array_tprograma[$cont_array_tprograma][0];
						$descripcion_tprograma=$array_tprograma[$cont_array_tprograma][1];

						//c0_sucursal || '-' || c5_numecont  || '-' || c10_familias  || '-' || c12_nitusuar
						$query_reporte="";
						$query_reporte.="
							SELECT count(*) as contador_total, count(distinct c48_credencial) as contador_por_credencial FROM $tabla_year_actual 
							WHERE c31_mesper='$cont_meses' 
							AND c32_anoper='$key_years'
							AND trim(c37_programe)='$tprograma' 
							AND c0_sucursal IS NOT NULL
							AND c5_numecont IS NOT NULL
							AND c10_familias IS NOT NULL
							AND c12_nitusuar IS NOT NULL 
							AND c48_credencial IS NOT NULL 					
						";

						$error_bd_seq="";
						$resultados_reporte=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($query_reporte, $error_bd_seq);		
						if($error_bd_seq!="")
						{
						    $mensaje_error.=  "Error al realizar la consulta del reporte actual.<br>";
						}//fin if


						$contador_filas=0;
						foreach ($resultados_reporte as $key => $fila_actual) 
						{

							$contador_poblacion_mes_year=$fila_actual["contador_total"];

							$contador_poblacion_mes_year_credencial=$fila_actual["contador_por_credencial"];

							$mensaje="";
							$mensaje.="\"$cont_meses\";del anno\";\"$key_years\";\"hay\";\"$contador_poblacion_mes_year\";\"totales\";";
							$mensaje.="\"hay\";\"$contador_poblacion_mes_year_credencial\";\"por credencial\";\"del programa \";\"$tprograma\";\"($descripcion_tprograma)\";";
							//echo str_replace(array("\"",";")," ", $mensaje)."<br>";
							ob_flush();
							flush();
							fwrite($archivo_reporte_comprobacion_inicial, $mensaje."\n");
							$escribio_info=true;
							$contador_filas++;
							
						}//fin foreach

						$cont_array_tprograma++;
					}//fin while
					
					
					echo "Termino Mes $cont_meses del Year $key_years<br>";$cont_meses++;
				}//fin contador meses
				
			}//fin tipo reporte es 1.2

			if($tipo_reporte=="1.5")
			{
				$mensaje="";
				$mensaje.="\"Reporte Seleccionado Tipo:\";\"$tipo_reporte\";\"Comprobacion inicial, campo credencial, Programa $tipo_programa\"";			
				echo "Tipo Reporte: ".str_replace(array("\"",";")," ", $mensaje)."<br>";
				ob_flush();
				flush();
				//fwrite($archivo_reporte_comprobacion_inicial, $mensaje."\n");

				$cont_meses=1;
				while($cont_meses<=12)
				{
					
					//c0_sucursal || '-' || c5_numecont  || '-' || c10_familias  || '-' || c12_nitusuar
					$query_reporte="";
					$query_reporte.="
						SELECT count(*) as contador_total, count(distinct c48_credencial) as contador_por_credencial FROM $tabla_year_actual 
						WHERE c31_mesper='$cont_meses' 
						AND c32_anoper='$key_years' 
						AND trim(c37_programe)='$tipo_programa'
						AND c0_sucursal IS NOT NULL
						AND c5_numecont IS NOT NULL
						AND c10_familias IS NOT NULL
						AND c12_nitusuar IS NOT NULL 
						AND c48_credencial IS NOT NULL 					
					";

					$error_bd_seq="";
					$resultados_reporte=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($query_reporte, $error_bd_seq);		
					if($error_bd_seq!="")
					{
					    $mensaje_error.=  "Error al realizar la consulta del reporte actual.<br>";
					}//fin if


					$contador_filas=0;
					foreach ($resultados_reporte as $key => $fila_actual) 
					{

						$contador_poblacion_mes_year=$fila_actual["contador_total"];

						$contador_poblacion_mes_year_credencial=$fila_actual["contador_por_credencial"];

						$mensaje="";
						$mensaje.="\"$cont_meses\";del anno\";\"$key_years\";\"hay\";\"$contador_poblacion_mes_year\";\"totales\";";
						$mensaje.="\"hay\";\"$contador_poblacion_mes_year_credencial\";\"por credencial y tipo programa\";\"$tipo_programa\"";
						//echo str_replace(array("\"",";")," ", $mensaje)."<br>";
						ob_flush();
						flush();
						fwrite($archivo_reporte_comprobacion_inicial, $mensaje."\n");
						$escribio_info=true;
						$contador_filas++;
						
					}//fin foreach
					
					
					echo "Termino Mes $cont_meses del Year $key_years<br>";$cont_meses++;
				}//fin contador meses
				
			}//fin tipo reporte es 1.5
			
			if($tipo_reporte=="2")
			{
				$mensaje="";
				$mensaje.="\"Reporte Seleccionado Tipo:\";\"$tipo_reporte\";\"Grupo Edad Y Sexo, Programa $tipo_programa\"";			
				echo "Tipo Reporte: ".str_replace(array("\"",";")," ", $mensaje)."<br>";
				ob_flush();
				flush();
				//fwrite($archivo_reporte_especificos, $mensaje."\n");

				$array_grupo_edad=array();
				$array_grupo_edad[]=array("1","menores 1 ano");
				$array_grupo_edad[]=array("2","1 a 4 anos");
				$array_grupo_edad[]=array("3","5 a 9 anos");
				$array_grupo_edad[]=array("4","10 a 14 anos");
				$array_grupo_edad[]=array("5","15 a 19 anos");
				$array_grupo_edad[]=array("6","20 a 24 anos");
				$array_grupo_edad[]=array("7","25 a 29 anos");
				$array_grupo_edad[]=array("8","30 a 34 anos");
				$array_grupo_edad[]=array("9","35 a 39 anos");
				$array_grupo_edad[]=array("10","40 a 44 anos");
				$array_grupo_edad[]=array("11","45 a 49 anos");
				$array_grupo_edad[]=array("12","50 a 54 anos");
				$array_grupo_edad[]=array("13","55 a 59 anos");
				$array_grupo_edad[]=array("14","60 a 64 anos");
				$array_grupo_edad[]=array("15","65 a 69 anos");
				$array_grupo_edad[]=array("16","70 a 74 anos");
				$array_grupo_edad[]=array("17","75 y mas anos");

				$cont_meses=1;
				while($cont_meses<=12)
				{
					$cont_array_custom=0;
					while($cont_array_custom<count($array_grupo_edad) )
					{
						$grupo_edad=$array_grupo_edad[$cont_array_custom][0];
						$descripcion_grupo_edad=$array_grupo_edad[$cont_array_custom][1];

						//c17_sexo='1' masculino
						$query_reporte="";
						$query_reporte.="
							SELECT count(distinct c48_credencial) as contador_por_credencial FROM $tabla_year_actual 
							WHERE c31_mesper='$cont_meses' 
							AND c32_anoper='$key_years'
							AND c17_sexo='1'
							AND c35_grupoed='$grupo_edad'
							AND trim(c37_programe)='$tipo_programa'
							AND c48_credencial IS NOT NULL 					
						";

						$error_bd_seq="";
						$resultados_reporte=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($query_reporte, $error_bd_seq);		
						if($error_bd_seq!="")
						{
						    $mensaje_error.=  "Error al realizar la consulta del reporte actual.<br>";
						}//fin if


						$contador_filas=0;
						foreach ($resultados_reporte as $key => $fila_actual) 
						{


							$contador_poblacion_mes_year_credencial=$fila_actual["contador_por_credencial"];

							$mensaje="";
							$mensaje.="\"$cont_meses\";\"$key_years\";\"Numero de registros\";\"$contador_poblacion_mes_year_credencial\";\" MASCULINO, para el grupo de edad\";\"$grupo_edad\";\"$descripcion_grupo_edad\"";
							
							//echo str_replace(array("\"",";")," ", $mensaje)."<br>";
							ob_flush();
							flush();

							fwrite($archivo_reporte_especificos, $mensaje."\n");
							
							$escribio_info=true;
							$contador_filas++;
							
						}//fin foreach

						//c17_sexo='2' femenino
						$query_reporte="";
						$query_reporte.="
							SELECT count(distinct c48_credencial) as contador_por_credencial FROM $tabla_year_actual 
							WHERE c31_mesper='$cont_meses' 
							AND c32_anoper='$key_years'
							AND c17_sexo='2'
							AND c35_grupoed='$grupo_edad'
							AND trim(c37_programe)='$tipo_programa'
							AND c48_credencial IS NOT NULL 					
						";

						$error_bd_seq="";
						$resultados_reporte=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($query_reporte, $error_bd_seq);		
						if($error_bd_seq!="")
						{
						    $mensaje_error.=  "Error al realizar la consulta del reporte actual.<br>";
						}//fin if


						$contador_filas=0;
						foreach ($resultados_reporte as $key => $fila_actual) 
						{


							$contador_poblacion_mes_year_credencial=$fila_actual["contador_por_credencial"];

							$mensaje="";
							$mensaje.="\"$cont_meses\";\"$key_years\";\"Numero de registros\";\"$contador_poblacion_mes_year_credencial\";\" FEMENINO, para el grupo de edad\";\"$grupo_edad\";\"$descripcion_grupo_edad\"";
							
							//echo str_replace(array("\"",";")," ", $mensaje)."<br>";
							ob_flush();
							flush();
							
							fwrite($archivo_reporte_especificos, $mensaje."\n");
							$escribio_info=true;
							$contador_filas++;
							
						}//fin foreach

						$cont_array_custom++;
					}//fin while grupo edad largo
					
					
					echo "Termino Mes $cont_meses del Year $key_years<br>";$cont_meses++;
				}//fin contador meses
				
			}//fin tipo reporte es 2

			if($tipo_reporte=="3")//parentesco
			{
				$mensaje="";
				$mensaje.="\"Reporte Seleccionado Tipo:\";\"$tipo_reporte\";\"Parentesco, Programa $tipo_programa\"";			
				echo "Tipo Reporte: ".str_replace(array("\"",";")," ", $mensaje)."<br>";
				ob_flush();
				flush();
				//fwrite($archivo_reporte_especificos, $mensaje."\n");
				
				$array_parentesco=array();
				$array_parentesco[]=array("0T","0tros(codigo con cero)");
				$array_parentesco[]=array("AB","Abuelo");
				$array_parentesco[]=array("CF","Cabeza de Familia");
				$array_parentesco[]=array("CS","Casado");
				$array_parentesco[]=array("CY","Conyugue");
				$array_parentesco[]=array("HJ","Hijo(a)");
				$array_parentesco[]=array("HM","Hermano(a)");
				$array_parentesco[]=array("MD","Madre");
				$array_parentesco[]=array("ME","Menor de Edad");
				$array_parentesco[]=array("NT","Nieto");
				$array_parentesco[]=array("OP","Otra Persona");
				$array_parentesco[]=array("OT","Otro");
				$array_parentesco[]=array("PD","Padre");
				$array_parentesco[]=array("PM","Primo");
				$array_parentesco[]=array("SB","Sobrino");
				$array_parentesco[]=array("SG","Suegro");
				$array_parentesco[]=array("SL","Soltero");
				$array_parentesco[]=array("TI","Tio");
				$array_parentesco[]=array("UL","Union Libre");
				$array_parentesco[]=array("VD","Viudo");

				$cont_meses=1;
				while($cont_meses<=12)
				{
					$cont_array_custom=0;
					while($cont_array_custom<count($array_parentesco) )
					{
						$parentesco=$array_parentesco[$cont_array_custom][0];
						$descripcion_parentesco=$array_parentesco[$cont_array_custom][1];
						
						$query_reporte="";
						$query_reporte.="
							SELECT count(distinct c48_credencial) as contador_por_credencial FROM $tabla_year_actual 
							WHERE c31_mesper='$cont_meses' 
							AND c32_anoper='$key_years'
							AND trim(c26_parentes)='$parentesco'
							AND trim(c37_programe)='$tipo_programa'
							AND c48_credencial IS NOT NULL 					
						";

						$error_bd_seq="";
						$resultados_reporte=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($query_reporte, $error_bd_seq);		
						if($error_bd_seq!="")
						{
						    $mensaje_error.=  "Error al realizar la consulta del reporte actual.<br>";
						}//fin if


						$contador_filas=0;
						foreach ($resultados_reporte as $key => $fila_actual) 
						{


							$contador_poblacion_mes_year_credencial=$fila_actual["contador_por_credencial"];

							$mensaje="";
							$mensaje.="\"$cont_meses\";\"$key_years\";\"Numero de registros\";\"$contador_poblacion_mes_year_credencial\";\"con el parentesco\";\"$parentesco\";\"( $descripcion_parentesco )\"";
							
							//echo str_replace(array("\"",";")," ", $mensaje)."<br>";
							ob_flush();
							flush();
							
							fwrite($archivo_reporte_especificos, $mensaje."\n");
							$escribio_info=true;
							$contador_filas++;
							
						}//fin foreach

						$cont_array_custom++;
					}//fin while grupo edad largo
					
					
					echo "Termino Mes $cont_meses del Year $key_years<br>";$cont_meses++;
				}//fin contador meses
				
			}//fin tipo reporte es 3


			if($tipo_reporte=="4")
			{
				$mensaje="";
				$mensaje.="\"Reporte Seleccionado Tipo:\";\"$tipo_reporte\";\"Distribucion poblacion sucursal y genero, Programa $tipo_programa\"";			
				echo "Tipo Reporte: ".str_replace(array("\"",";")," ", $mensaje)."<br>";
				ob_flush();
				flush();
				//fwrite($archivo_reporte_especificos, $mensaje."\n");


				$array_sucursales=array();
				$array_sucursales[]=array("AA","Cali");
				$array_sucursales[]=array("BA","Buga");
				$array_sucursales[]=array("CA","Popayan");
				$array_sucursales[]=array("CG","cartagena");
				$array_sucursales[]=array("CU","Cucuta");
				$array_sucursales[]=array("DA","Buenaventura");
				$array_sucursales[]=array("EA","Medellin");
				$array_sucursales[]=array("FA","Apartado");
				$array_sucursales[]=array("GA","Barranquilla");
				$array_sucursales[]=array("HA","Palmira");
				$array_sucursales[]=array("IA","Bogota");
				$array_sucursales[]=array("JA","Armenia");
				$array_sucursales[]=array("KA","Ibague");
				$array_sucursales[]=array("LA","Pereira");
				$array_sucursales[]=array("MA","Cartago");
				$array_sucursales[]=array("MS","Mushaisa");
				$array_sucursales[]=array("MT","Monteria");
				$array_sucursales[]=array("NA","Tulua");
				$array_sucursales[]=array("NV","Neiva");
				$array_sucursales[]=array("NY","Nueva York");
				$array_sucursales[]=array("OA","Manizales");
				$array_sucursales[]=array("PS","Pasto");
				$array_sucursales[]=array("RN","Rio Negro");
				$array_sucursales[]=array("SN","Santa Marta");
				$array_sucursales[]=array("UT","Union Temporal");
				$array_sucursales[]=array("VA","Valledupar");
				$array_sucursales[]=array("VI","Villavicencio");
				$array_sucursales[]=array("WA","Quibdo");
				$array_sucursales[]=array("YA","Bucaramanga");


				$cont_meses=1;
				while($cont_meses<=12)
				{
					$cont_array_custom=0;
					while($cont_array_custom<count($array_sucursales) )
					{
						$sucursal=$array_sucursales[$cont_array_custom][0];
						$descripcion_sucursal=$array_sucursales[$cont_array_custom][1];
						//c17_sexo='1' masculino
						$query_reporte="";
						$query_reporte.="
							SELECT count(distinct c48_credencial) as contador_por_credencial FROM $tabla_year_actual 
							WHERE c31_mesper='$cont_meses' 
							AND c32_anoper='$key_years'
							AND c17_sexo='1'
							AND trim(c0_sucursal)='$sucursal'
							AND trim(c37_programe)='$tipo_programa'
							AND c48_credencial IS NOT NULL 					
						";

						$error_bd_seq="";
						$resultados_reporte=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($query_reporte, $error_bd_seq);		
						if($error_bd_seq!="")
						{
						    $mensaje_error.=  "Error al realizar la consulta del reporte.<br>";
						}//fin if


						$contador_filas=0;
						foreach ($resultados_reporte as $key => $fila_actual) 
						{


							$contador_poblacion_mes_year_credencial=$fila_actual["contador_por_credencial"];

							$mensaje="";
							$mensaje.="\"$cont_meses\";\"$key_years\";\"Numero de registros\";\"$contador_poblacion_mes_year_credencial\";\" MASCULINO, para la sucursal\";\"$sucursal\";\"$descripcion_sucursal\"";
							
							//echo str_replace(array("\"",";")," ", $mensaje)."<br>";
							ob_flush();
							flush();

							fwrite($archivo_reporte_especificos, $mensaje."\n");
							
							$escribio_info=true;
							$contador_filas++;
							
						}//fin foreach

						//c17_sexo='2' femenino
						$query_reporte="";
						$query_reporte.="
							SELECT count(distinct c48_credencial) as contador_por_credencial FROM $tabla_year_actual 
							WHERE c31_mesper='$cont_meses' 
							AND c32_anoper='$key_years'
							AND c17_sexo='2'
							AND trim(c0_sucursal)='$sucursal'
							AND trim(c37_programe)='$tipo_programa'
							AND c48_credencial IS NOT NULL 					
						";

						$error_bd_seq="";
						$resultados_reporte=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($query_reporte, $error_bd_seq);		
						if($error_bd_seq!="")
						{
						    $mensaje_error.=  "Error al realizar la consulta del reporte.<br>";
						}//fin if


						$contador_filas=0;
						foreach ($resultados_reporte as $key => $fila_actual) 
						{


							$contador_poblacion_mes_year_credencial=$fila_actual["contador_por_credencial"];

							$mensaje="";
							$mensaje.="\"$cont_meses\";\"$key_years\";\"Numero de registros\";\"$contador_poblacion_mes_year_credencial\";\" FEMENINO, para la sucursal \";\"$sucursal\";\"$descripcion_sucursal\"";
							
							//echo str_replace(array("\"",";")," ", $mensaje)."<br>";
							ob_flush();
							flush();
							
							fwrite($archivo_reporte_especificos, $mensaje."\n");
							$escribio_info=true;
							$contador_filas++;
							
						}//fin foreach
						
						$cont_array_custom++;
					}//fin while grupo edad largo
					
					
					echo "Termino Mes $cont_meses del Year $key_years<br>";$cont_meses++;
				}//fin contador meses
				
			}//fin tipo reporte es 4

			if($tipo_reporte=="5")
			{
				$mensaje="";
				$mensaje.="\"Reporte Seleccionado Tipo:\";\"$tipo_reporte\";\"Distribucion poblacion sucursal y grupo edad, Programa $tipo_programa\"";			
				echo "Tipo Reporte: ".str_replace(array("\"",";")," ", $mensaje)."<br>";
				ob_flush();
				flush();
				//fwrite($archivo_reporte_especificos, $mensaje."\n");


				$array_sucursales=array();
				$array_sucursales[]=array("AA","Cali");
				$array_sucursales[]=array("BA","Buga");
				$array_sucursales[]=array("CA","Popayan");
				$array_sucursales[]=array("CG","cartagena");
				$array_sucursales[]=array("CU","Cucuta");
				$array_sucursales[]=array("DA","Buenaventura");
				$array_sucursales[]=array("EA","Medellin");
				$array_sucursales[]=array("FA","Apartado");
				$array_sucursales[]=array("GA","Barranquilla");
				$array_sucursales[]=array("HA","Palmira");
				$array_sucursales[]=array("IA","Bogota");
				$array_sucursales[]=array("JA","Armenia");
				$array_sucursales[]=array("KA","Ibague");
				$array_sucursales[]=array("LA","Pereira");
				$array_sucursales[]=array("MA","Cartago");
				$array_sucursales[]=array("MS","Mushaisa");
				$array_sucursales[]=array("MT","Monteria");
				$array_sucursales[]=array("NA","Tulua");
				$array_sucursales[]=array("NV","Neiva");
				$array_sucursales[]=array("NY","Nueva York");
				$array_sucursales[]=array("OA","Manizales");
				$array_sucursales[]=array("PS","Pasto");
				$array_sucursales[]=array("RN","Rio Negro");
				$array_sucursales[]=array("SN","Santa Marta");
				$array_sucursales[]=array("UT","Union Temporal");
				$array_sucursales[]=array("VA","Valledupar");
				$array_sucursales[]=array("VI","Villavicencio");
				$array_sucursales[]=array("WA","Quibdo");
				$array_sucursales[]=array("YA","Bucaramanga");

				$array_grupo_edad=array();
				$array_grupo_edad[]=array("1","menores 1 ano");
				$array_grupo_edad[]=array("2","1 a 4 anos");
				$array_grupo_edad[]=array("3","5 a 9 anos");
				$array_grupo_edad[]=array("4","10 a 14 anos");
				$array_grupo_edad[]=array("5","15 a 19 anos");
				$array_grupo_edad[]=array("6","20 a 24 anos");
				$array_grupo_edad[]=array("7","25 a 29 anos");
				$array_grupo_edad[]=array("8","30 a 34 anos");
				$array_grupo_edad[]=array("9","35 a 39 anos");
				$array_grupo_edad[]=array("10","40 a 44 anos");
				$array_grupo_edad[]=array("11","45 a 49 anos");
				$array_grupo_edad[]=array("12","50 a 54 anos");
				$array_grupo_edad[]=array("13","55 a 59 anos");
				$array_grupo_edad[]=array("14","60 a 64 anos");
				$array_grupo_edad[]=array("15","65 a 69 anos");
				$array_grupo_edad[]=array("16","70 a 74 anos");
				$array_grupo_edad[]=array("17","75 y mas anos");


				$cont_meses=1;
				while($cont_meses<=12)
				{
					$cont_array_custom=0;
					while($cont_array_custom<count($array_sucursales) )
					{
						$sucursal=$array_sucursales[$cont_array_custom][0];
						$descripcion_sucursal=$array_sucursales[$cont_array_custom][1];

						$cont_array_custom_2=0;
						while($cont_array_custom_2<count($array_grupo_edad) )
						{
							$grupo_edad=$array_grupo_edad[$cont_array_custom_2][0];
							$descripcion_grupo_edad=$array_grupo_edad[$cont_array_custom_2][1];


							$query_reporte="";
							$query_reporte.="
								SELECT count(distinct c48_credencial) as contador_por_credencial FROM $tabla_year_actual 
								WHERE c31_mesper='$cont_meses' 
								AND c32_anoper='$key_years'
								AND trim(c0_sucursal)='$sucursal'
								AND c35_grupoed='$grupo_edad'
								AND trim(c37_programe)='$tipo_programa'
								AND c48_credencial IS NOT NULL 					
							";

							$error_bd_seq="";
							$resultados_reporte=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($query_reporte, $error_bd_seq);		
							if($error_bd_seq!="")
							{
							    $mensaje_error.=  "Error al realizar la consulta del reporte.<br>";
							}//fin if


							$contador_filas=0;
							foreach ($resultados_reporte as $key => $fila_actual) 
							{


								$contador_poblacion_mes_year_credencial=$fila_actual["contador_por_credencial"];

								$mensaje="";
								$mensaje.="\"$cont_meses\";\"$key_years\";\"Numero de registros\";\"$contador_poblacion_mes_year_credencial\";\" para el grupo de edad\";\"$grupo_edad\";\"\"$descripcion_grupo_edad por sucursal\";\"$sucursal\";\"$descripcion_sucursal\"";
								
								//echo str_replace(array("\"",";")," ", $mensaje)."<br>";
								ob_flush();
								flush();

								fwrite($archivo_reporte_especificos, $mensaje."\n");
								
								$escribio_info=true;
								$contador_filas++;
								
							}//fin foreach

							

							$cont_array_custom_2++;

						}//fin while grupo edad

						$cont_array_custom++;

					}//fin while sucursales
					
					
					echo "Termino Mes $cont_meses del Year $key_years<br>";$cont_meses++;
				}//fin contador meses
				
			}//fin tipo reporte es 5


			if($tipo_reporte=="6")
			{
				$mensaje="";
				$mensaje.="\"Reporte Seleccionado Tipo:\";\"$tipo_reporte\";\"Distribucion poblacion regional y genero, Programa $tipo_programa\"";			
				echo "Tipo Reporte: ".str_replace(array("\"",";")," ", $mensaje)."<br>";
				ob_flush();
				flush();
				//fwrite($archivo_reporte_especificos, $mensaje."\n");


				$array_regional=array();
				$array_regional[]=array("1","Cali");
				$array_regional[]=array("2","Medellín");
				$array_regional[]=array("3","Caribe");
				$array_regional[]=array("4","Bogotá");
				$array_regional[]=array("5","Eje Cafetero");
				$array_regional[]=array("7","Exterior");
				$array_regional[]=array("8","Unión Temporal");


				$cont_meses=1;
				while($cont_meses<=12)
				{
					$cont_array_custom=0;
					while($cont_array_custom<count($array_regional) )
					{
						$regional=$array_regional[$cont_array_custom][0];
						$descripcion_regional=$array_regional[$cont_array_custom][1];
						//c17_sexo='1' masculino
						$query_reporte="";
						$query_reporte.="
							SELECT count(distinct c48_credencial) as contador_por_credencial FROM $tabla_year_actual 
							WHERE c31_mesper='$cont_meses' 
							AND c32_anoper='$key_years'
							AND c17_sexo='1'
							AND c38_regional='$regional'
							AND trim(c37_programe)='$tipo_programa'
							AND c48_credencial IS NOT NULL 					
						";

						$error_bd_seq="";
						$resultados_reporte=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($query_reporte, $error_bd_seq);		
						if($error_bd_seq!="")
						{
						    $mensaje_error.=  "Error al realizar la consulta del reporte.<br>";
						}//fin if


						$contador_filas=0;
						foreach ($resultados_reporte as $key => $fila_actual) 
						{


							$contador_poblacion_mes_year_credencial=$fila_actual["contador_por_credencial"];

							$mensaje="";
							$mensaje.="\"$cont_meses\";\"$key_years\";\"Numero de registros\";\"$contador_poblacion_mes_year_credencial\";\" MASCULINO, con regional \";\"$regional\";\"$descripcion_regional\"";
							
							//echo str_replace(array("\"",";")," ", $mensaje)."<br>";
							ob_flush();
							flush();

							fwrite($archivo_reporte_especificos, $mensaje."\n");
							
							$escribio_info=true;
							$contador_filas++;
							
						}//fin foreach

						//c17_sexo='2' femenino
						$query_reporte="";
						$query_reporte.="
							SELECT count(distinct c48_credencial) as contador_por_credencial FROM $tabla_year_actual 
							WHERE c31_mesper='$cont_meses' 
							AND c32_anoper='$key_years'
							AND c17_sexo='2'
							AND c38_regional='$regional'
							AND trim(c37_programe)='$tipo_programa'
							AND c48_credencial IS NOT NULL 					
						";

						$error_bd_seq="";
						$resultados_reporte=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($query_reporte, $error_bd_seq);		
						if($error_bd_seq!="")
						{
						    $mensaje_error.=  "Error al realizar la consulta del reporte.<br>";
						}//fin if


						$contador_filas=0;
						foreach ($resultados_reporte as $key => $fila_actual) 
						{


							$contador_poblacion_mes_year_credencial=$fila_actual["contador_por_credencial"];

							$mensaje="";
							$mensaje.="\"$cont_meses\";\"$key_years\";\"Numero de registros\";\"$contador_poblacion_mes_year_credencial\";\" FEMENINO, con regional \";\"$regional\";\"$descripcion_regional\"";
							
							//echo str_replace(array("\"",";")," ", $mensaje)."<br>";
							ob_flush();
							flush();
							
							fwrite($archivo_reporte_especificos, $mensaje."\n");
							$escribio_info=true;
							$contador_filas++;
							
						}//fin foreach
						
						$cont_array_custom++;
					}//fin while grupo edad largo
					
					
					echo "Termino Mes $cont_meses del Year $key_years<br>";$cont_meses++;
				}//fin contador meses
				
			}//fin tipo reporte es 6

			if($tipo_reporte=="7")
			{
				$mensaje="";
				$mensaje.="\"Reporte Seleccionado Tipo:\";\"$tipo_reporte\";\"Distribucion poblacion regional y grupo edad, Programa $tipo_programa\"";			
				echo "Tipo Reporte: ".str_replace(array("\"",";")," ", $mensaje)."<br>";
				ob_flush();
				flush();
				//fwrite($archivo_reporte_especificos, $mensaje."\n");


				$array_regional=array();
				$array_regional[]=array("1","Cali");
				$array_regional[]=array("2","Medellín");
				$array_regional[]=array("3","Caribe");
				$array_regional[]=array("4","Bogotá");
				$array_regional[]=array("5","Eje Cafetero");
				$array_regional[]=array("7","Exterior");
				$array_regional[]=array("8","Unión Temporal");

				$array_grupo_edad=array();
				$array_grupo_edad[]=array("1","menores 1 ano");
				$array_grupo_edad[]=array("2","1 a 4 anos");
				$array_grupo_edad[]=array("3","5 a 9 anos");
				$array_grupo_edad[]=array("4","10 a 14 anos");
				$array_grupo_edad[]=array("5","15 a 19 anos");
				$array_grupo_edad[]=array("6","20 a 24 anos");
				$array_grupo_edad[]=array("7","25 a 29 anos");
				$array_grupo_edad[]=array("8","30 a 34 anos");
				$array_grupo_edad[]=array("9","35 a 39 anos");
				$array_grupo_edad[]=array("10","40 a 44 anos");
				$array_grupo_edad[]=array("11","45 a 49 anos");
				$array_grupo_edad[]=array("12","50 a 54 anos");
				$array_grupo_edad[]=array("13","55 a 59 anos");
				$array_grupo_edad[]=array("14","60 a 64 anos");
				$array_grupo_edad[]=array("15","65 a 69 anos");
				$array_grupo_edad[]=array("16","70 a 74 anos");
				$array_grupo_edad[]=array("17","75 y mas anos");


				$cont_meses=1;
				while($cont_meses<=12)
				{
					$cont_array_custom=0;
					while($cont_array_custom<count($array_regional) )
					{
						$regional=$array_regional[$cont_array_custom][0];
						$descripcion_regional=$array_regional[$cont_array_custom][1];

						$cont_array_custom_2=0;
						while($cont_array_custom_2<count($array_grupo_edad) )
						{
							$grupo_edad=$array_grupo_edad[$cont_array_custom_2][0];
							$descripcion_grupo_edad=$array_grupo_edad[$cont_array_custom_2][1];


							$query_reporte="";
							$query_reporte.="
								SELECT count(distinct c48_credencial) as contador_por_credencial FROM $tabla_year_actual 
								WHERE c31_mesper='$cont_meses' 
								AND c32_anoper='$key_years'
								AND c38_regional='$regional'
								AND c35_grupoed='$grupo_edad'
								AND trim(c37_programe)='$tipo_programa'
								AND c48_credencial IS NOT NULL 					
							";

							$error_bd_seq="";
							$resultados_reporte=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($query_reporte, $error_bd_seq);		
							if($error_bd_seq!="")
							{
							    $mensaje_error.=  "Error al realizar la consulta del reporte.<br>";
							}//fin if


							$contador_filas=0;
							foreach ($resultados_reporte as $key => $fila_actual) 
							{


								$contador_poblacion_mes_year_credencial=$fila_actual["contador_por_credencial"];

								$mensaje="";
								$mensaje.="\"$cont_meses\";\"$key_years\";\"Numero de registros\";\"$contador_poblacion_mes_year_credencial\";\" para el grupo de edad\";\"$grupo_edad\";\"\"$descripcion_grupo_edad por regional\";\"$regional\";\"$descripcion_regional\"";
								
								//echo str_replace(array("\"",";")," ", $mensaje)."<br>";
								ob_flush();
								flush();

								fwrite($archivo_reporte_especificos, $mensaje."\n");
								
								$escribio_info=true;
								$contador_filas++;
								
							}//fin foreach

							

							$cont_array_custom_2++;

						}//fin while grupo edad

						$cont_array_custom++;

					}//fin while sucursales
					
					
					echo "Termino Mes $cont_meses del Year $key_years<br>";$cont_meses++;
				}//fin contador meses
				
			}//fin tipo reporte es 7

			if($tipo_reporte=="8")
			{
				$mensaje="";
				$mensaje.="\"Reporte Seleccionado Tipo:\";\"$tipo_reporte\";\"Distribucion poblacion regional, grupo edad, sexo, Programa $tipo_programa\"";			
				echo "Tipo Reporte: ".str_replace(array("\"",";")," ", $mensaje)."<br>";
				ob_flush();
				flush();
				//fwrite($archivo_reporte_especificos, $mensaje."\n");


				$array_regional=array();
				$array_regional[]=array("1","Cali");
				$array_regional[]=array("2","Medellín");
				$array_regional[]=array("3","Caribe");
				$array_regional[]=array("4","Bogotá");
				$array_regional[]=array("5","Eje Cafetero");
				$array_regional[]=array("7","Exterior");
				$array_regional[]=array("8","Unión Temporal");

				$array_grupo_edad=array();
				$array_grupo_edad[]=array("1","menores 1 ano");
				$array_grupo_edad[]=array("2","1 a 4 anos");
				$array_grupo_edad[]=array("3","5 a 9 anos");
				$array_grupo_edad[]=array("4","10 a 14 anos");
				$array_grupo_edad[]=array("5","15 a 19 anos");
				$array_grupo_edad[]=array("6","20 a 24 anos");
				$array_grupo_edad[]=array("7","25 a 29 anos");
				$array_grupo_edad[]=array("8","30 a 34 anos");
				$array_grupo_edad[]=array("9","35 a 39 anos");
				$array_grupo_edad[]=array("10","40 a 44 anos");
				$array_grupo_edad[]=array("11","45 a 49 anos");
				$array_grupo_edad[]=array("12","50 a 54 anos");
				$array_grupo_edad[]=array("13","55 a 59 anos");
				$array_grupo_edad[]=array("14","60 a 64 anos");
				$array_grupo_edad[]=array("15","65 a 69 anos");
				$array_grupo_edad[]=array("16","70 a 74 anos");
				$array_grupo_edad[]=array("17","75 y mas anos");


				$cont_meses=1;
				while($cont_meses<=12)
				{
					$cont_array_custom=0;
					while($cont_array_custom<count($array_regional) )
					{
						$regional=$array_regional[$cont_array_custom][0];
						$descripcion_regional=$array_regional[$cont_array_custom][1];

						$cont_array_custom_2=0;
						while($cont_array_custom_2<count($array_grupo_edad) )
						{
							$grupo_edad=$array_grupo_edad[$cont_array_custom_2][0];
							$descripcion_grupo_edad=$array_grupo_edad[$cont_array_custom_2][1];

							//masculino
							$query_reporte="";
							$query_reporte.="
								SELECT count(distinct c48_credencial) as contador_por_credencial FROM $tabla_year_actual 
								WHERE c31_mesper='$cont_meses' 
								AND c32_anoper='$key_years'
								AND c17_sexo='1'
								AND c38_regional='$regional'
								AND c35_grupoed='$grupo_edad'
								AND trim(c37_programe)='$tipo_programa'
								AND c48_credencial IS NOT NULL 					
							";

							$error_bd_seq="";
							$resultados_reporte=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($query_reporte, $error_bd_seq);		
							if($error_bd_seq!="")
							{
							    $mensaje_error.=  "Error al realizar la consulta del reporte.<br>";
							}//fin if


							$contador_filas=0;
							foreach ($resultados_reporte as $key => $fila_actual) 
							{


								$contador_poblacion_mes_year_credencial=$fila_actual["contador_por_credencial"];

								$mensaje="";
								$mensaje.="\"$cont_meses\";\"$key_years\";\"Numero de registros\";\"$contador_poblacion_mes_year_credencial\";\"\"MASCULINO\";\" para el grupo de edad\";\"$grupo_edad\";\"\"$descripcion_grupo_edad por regional\";\"$regional\";\"$descripcion_regional\"";
								
								//echo str_replace(array("\"",";")," ", $mensaje)."<br>";
								ob_flush();
								flush();

								fwrite($archivo_reporte_especificos, $mensaje."\n");
								
								$escribio_info=true;
								$contador_filas++;
								
							}//fin foreach masculino

							//femenino
							$query_reporte="";
							$query_reporte.="
								SELECT count(distinct c48_credencial) as contador_por_credencial FROM $tabla_year_actual 
								WHERE c31_mesper='$cont_meses' 
								AND c32_anoper='$key_years'
								AND c17_sexo='2'
								AND c38_regional='$regional'
								AND c35_grupoed='$grupo_edad'
								AND trim(c37_programe)='$tipo_programa'
								AND c48_credencial IS NOT NULL 					
							";

							$error_bd_seq="";
							$resultados_reporte=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($query_reporte, $error_bd_seq);		
							if($error_bd_seq!="")
							{
							    $mensaje_error.=  "Error al realizar la consulta del reporte.<br>";
							}//fin if


							$contador_filas=0;
							foreach ($resultados_reporte as $key => $fila_actual) 
							{


								$contador_poblacion_mes_year_credencial=$fila_actual["contador_por_credencial"];

								$mensaje="";
								$mensaje.="\"$cont_meses\";\"$key_years\";\"Numero de registros\";\"$contador_poblacion_mes_year_credencial\";\"FEMENINO\";\"para el grupo de edad\";\"$grupo_edad\";\"\"$descripcion_grupo_edad por regional\";\"$regional\";\"$descripcion_regional\"";
								
								//echo str_replace(array("\"",";")," ", $mensaje)."<br>";
								ob_flush();
								flush();

								fwrite($archivo_reporte_especificos, $mensaje."\n");
								
								$escribio_info=true;
								$contador_filas++;
								
							}//fin foreach femenino

							

							$cont_array_custom_2++;

						}//fin while grupo edad

						$cont_array_custom++;

					}//fin while sucursales
					
					
					echo "Termino Mes $cont_meses del Year $key_years<br>";$cont_meses++;
				}//fin contador meses
				
			}//fin tipo reporte es 8

			if($tipo_reporte=="9")//parentesco con regional
			{
				$mensaje="";
				$mensaje.="\"Reporte Seleccionado Tipo:\";\"$tipo_reporte\";\"Parentesco y regional, Programa $tipo_programa\"";			
				echo "Tipo Reporte: ".str_replace(array("\"",";")," ", $mensaje)."<br>";
				ob_flush();
				flush();
				//fwrite($archivo_reporte_especificos, $mensaje."\n");

				$array_regional=array();
				$array_regional[]=array("1","Cali");
				$array_regional[]=array("2","Medellín");
				$array_regional[]=array("3","Caribe");
				$array_regional[]=array("4","Bogotá");
				$array_regional[]=array("5","Eje Cafetero");
				$array_regional[]=array("7","Exterior");
				$array_regional[]=array("8","Unión Temporal");
				
				$array_parentesco=array();
				$array_parentesco[]=array("0T","0tros(codigo con cero)");
				$array_parentesco[]=array("AB","Abuelo");
				$array_parentesco[]=array("CF","Cabeza de Familia");
				$array_parentesco[]=array("CS","Casado");
				$array_parentesco[]=array("CY","Conyugue");
				$array_parentesco[]=array("HJ","Hijo(a)");
				$array_parentesco[]=array("HM","Hermano(a)");
				$array_parentesco[]=array("MD","Madre");
				$array_parentesco[]=array("ME","Menor de Edad");
				$array_parentesco[]=array("NT","Nieto");
				$array_parentesco[]=array("OP","Otra Persona");
				$array_parentesco[]=array("OT","Otro");
				$array_parentesco[]=array("PD","Padre");
				$array_parentesco[]=array("PM","Primo");
				$array_parentesco[]=array("SB","Sobrino");
				$array_parentesco[]=array("SG","Suegro");
				$array_parentesco[]=array("SL","Soltero");
				$array_parentesco[]=array("TI","Tio");
				$array_parentesco[]=array("UL","Union Libre");
				$array_parentesco[]=array("VD","Viudo");

				$cont_meses=1;
				while($cont_meses<=12)
				{
					$cont_array_reg=0;
					while($cont_array_reg<count($array_regional) )
					{
						$regional=$array_regional[$cont_array_reg][0];
						$descripcion_regional=$array_regional[$cont_array_reg][1];

						$cont_array_custom=0;
						while($cont_array_custom<count($array_parentesco) )
						{
							$parentesco=$array_parentesco[$cont_array_custom][0];
							$descripcion_parentesco=$array_parentesco[$cont_array_custom][1];
							
							$query_reporte="";
							$query_reporte.="
								SELECT count(distinct c48_credencial) as contador_por_credencial FROM $tabla_year_actual 
								WHERE c31_mesper='$cont_meses' 
								AND c32_anoper='$key_years'
								AND c38_regional='$regional'
								AND trim(c26_parentes)='$parentesco'
								AND trim(c37_programe)='$tipo_programa'
								AND c48_credencial IS NOT NULL 					
							";

							$error_bd_seq="";
							$resultados_reporte=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($query_reporte, $error_bd_seq);		
							if($error_bd_seq!="")
							{
							    $mensaje_error.=  "Error al realizar la consulta del reporte actual.<br>";
							}//fin if


							$contador_filas=0;
							foreach ($resultados_reporte as $key => $fila_actual) 
							{


								$contador_poblacion_mes_year_credencial=$fila_actual["contador_por_credencial"];

								$mensaje="";
								$mensaje.="\"$cont_meses\";\"$key_years\";\"Numero de registros\";\"$contador_poblacion_mes_year_credencial\";\"con el parentesco\";\"$parentesco\";\"( $descripcion_parentesco )\";\" regional\";\"$regional\";\"$descripcion_regional\"";
								
								//echo str_replace(array("\"",";")," ", $mensaje)."<br>";
								ob_flush();
								flush();
								
								fwrite($archivo_reporte_especificos, $mensaje."\n");
								$escribio_info=true;
								$contador_filas++;
								
							}//fin foreach

							$cont_array_custom++;
						}//fin while parentesco

						$cont_array_reg++;
					}//fin while regional
					
					
					echo "Termino Mes $cont_meses del Year $key_years<br>";$cont_meses++;
				}//fin contador meses
				
			}//fin tipo reporte es 9

			if($tipo_reporte=="10")
			{
				$mensaje="";
				$mensaje.="\"Reporte Seleccionado Tipo:\";\"$tipo_reporte\";\"Plan Tarifario Genero, Programa $tipo_programa\"";			
				echo "Tipo Reporte: ".str_replace(array("\"",";")," ", $mensaje)."<br>";
				ob_flush();
				flush();
				//fwrite($archivo_reporte_especificos, $mensaje."\n");

				$array_plan_tarifario=array();
				$array_plan_tarifario[]=array("1","Familiar");
				$array_plan_tarifario[]=array("2","Colectivos");
				$array_plan_tarifario[]=array("3","Asociados");

				

				$cont_meses=1;
				while($cont_meses<=12)
				{
					$cont_array_custom=0;
					while($cont_array_custom<count($array_plan_tarifario) )
					{
						$plan_tarifario=$array_plan_tarifario[$cont_array_custom][0];
						$descripcion_plan_tarifario=$array_plan_tarifario[$cont_array_custom][1];
						
						$query_reporte="";
						$query_reporte.="
							SELECT count(distinct c48_credencial) as contador_por_credencial FROM $tabla_year_actual 
							WHERE c31_mesper='$cont_meses' 
							AND c32_anoper='$key_years'
							AND c17_sexo='1'
							AND c44_planes='$plan_tarifario'
							AND trim(c37_programe)='$tipo_programa'
							AND c48_credencial IS NOT NULL 					
						";

						$error_bd_seq="";
						$resultados_reporte=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($query_reporte, $error_bd_seq);		
						if($error_bd_seq!="")
						{
						    $mensaje_error.=  "Error al realizar la consulta del reporte actual.<br>";
						}//fin if


						$contador_filas=0;
						foreach ($resultados_reporte as $key => $fila_actual) 
						{


							$contador_poblacion_mes_year_credencial=$fila_actual["contador_por_credencial"];

							$mensaje="";
							$mensaje.="\"$cont_meses\";\"$key_years\";\"Numero de registros\";\"$contador_poblacion_mes_year_credencial\";\"MASCULINO\";\"con el plan tarifario\";\"$plan_tarifario\";\"( $descripcion_plan_tarifario )\"";
							
							//echo str_replace(array("\"",";")," ", $mensaje)."<br>";
							ob_flush();
							flush();
							
							fwrite($archivo_reporte_especificos, $mensaje."\n");
							$escribio_info=true;
							$contador_filas++;
							
						}//fin foreach

						$query_reporte="";
						$query_reporte.="
							SELECT count(distinct c48_credencial) as contador_por_credencial FROM $tabla_year_actual 
							WHERE c31_mesper='$cont_meses' 
							AND c32_anoper='$key_years'
							AND c17_sexo='2'
							AND c44_planes='$plan_tarifario'
							AND trim(c37_programe)='$tipo_programa'
							AND c48_credencial IS NOT NULL 					
						";

						$error_bd_seq="";
						$resultados_reporte=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($query_reporte, $error_bd_seq);		
						if($error_bd_seq!="")
						{
						    $mensaje_error.=  "Error al realizar la consulta del reporte actual.<br>";
						}//fin if


						$contador_filas=0;
						foreach ($resultados_reporte as $key => $fila_actual) 
						{


							$contador_poblacion_mes_year_credencial=$fila_actual["contador_por_credencial"];

							$mensaje="";
							$mensaje.="\"$cont_meses\";\"$key_years\";\"Numero de registros\";\"$contador_poblacion_mes_year_credencial\";\"FEMENINO\";\"con el plan tarifario\";\"$plan_tarifario\";\"( $descripcion_plan_tarifario )\"";
							
							//echo str_replace(array("\"",";")," ", $mensaje)."<br>";
							ob_flush();
							flush();
							
							fwrite($archivo_reporte_especificos, $mensaje."\n");
							$escribio_info=true;
							$contador_filas++;
							
						}//fin foreach

						$cont_array_custom++;
					}//fin while plan tarifario
					
					
					echo "Termino Mes $cont_meses del Year $key_years<br>";$cont_meses++;
				}//fin contador meses
				
			}//fin tipo reporte es 10

			if($tipo_reporte=="11")
			{
				$mensaje="";
				$mensaje.="\"Reporte Seleccionado Tipo:\";\"$tipo_reporte\";\"Plan Tarifario, Genero, regional Programa $tipo_programa\"";			
				echo "Tipo Reporte: ".str_replace(array("\"",";")," ", $mensaje)."<br>";
				ob_flush();
				flush();
				//fwrite($archivo_reporte_especificos, $mensaje."\n");

				$array_regional=array();
				$array_regional[]=array("1","Cali");
				$array_regional[]=array("2","Medellín");
				$array_regional[]=array("3","Caribe");
				$array_regional[]=array("4","Bogotá");
				$array_regional[]=array("5","Eje Cafetero");
				$array_regional[]=array("7","Exterior");
				$array_regional[]=array("8","Unión Temporal");

				$array_plan_tarifario=array();
				$array_plan_tarifario[]=array("1","Familiar");
				$array_plan_tarifario[]=array("2","Colectivos");
				$array_plan_tarifario[]=array("3","Asociados");

				

				$cont_meses=1;
				while($cont_meses<=12)
				{
					$cont_array_reg=0;
					while($cont_array_reg<count($array_regional) )
					{
						$regional=$array_regional[$cont_array_reg][0];
						$descripcion_regional=$array_regional[$cont_array_reg][1];

						$cont_array_custom=0;
						while($cont_array_custom<count($array_plan_tarifario) )
						{
							$plan_tarifario=$array_plan_tarifario[$cont_array_custom][0];
							$descripcion_plan_tarifario=$array_plan_tarifario[$cont_array_custom][1];
							
							$query_reporte="";
							$query_reporte.="
								SELECT count(distinct c48_credencial) as contador_por_credencial FROM $tabla_year_actual 
								WHERE c31_mesper='$cont_meses' 
								AND c32_anoper='$key_years'
								AND c17_sexo='1'
								AND c38_regional='$regional'
								AND c44_planes='$plan_tarifario'
								AND trim(c37_programe)='$tipo_programa'
								AND c48_credencial IS NOT NULL 					
							";

							$error_bd_seq="";
							$resultados_reporte=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($query_reporte, $error_bd_seq);		
							if($error_bd_seq!="")
							{
							    $mensaje_error.=  "Error al realizar la consulta del reporte actual.<br>";
							}//fin if


							$contador_filas=0;
							foreach ($resultados_reporte as $key => $fila_actual) 
							{


								$contador_poblacion_mes_year_credencial=$fila_actual["contador_por_credencial"];

								$mensaje="";
								$mensaje.="\"$cont_meses\";\"$key_years\";\"Numero de registros\";\"$contador_poblacion_mes_year_credencial\";\"MASCULINO\";\"con el plan tarifario\";\"$plan_tarifario\";\"( $descripcion_plan_tarifario )\";\" regional\";\"$regional\";\"$descripcion_regional\"";
								
								//echo str_replace(array("\"",";")," ", $mensaje)."<br>";
								ob_flush();
								flush();
								
								fwrite($archivo_reporte_especificos, $mensaje."\n");
								$escribio_info=true;
								$contador_filas++;
								
							}//fin foreach

							$query_reporte="";
							$query_reporte.="
								SELECT count(distinct c48_credencial) as contador_por_credencial FROM $tabla_year_actual 
								WHERE c31_mesper='$cont_meses' 
								AND c32_anoper='$key_years'
								AND c17_sexo='2'
								AND c38_regional='$regional'
								AND c44_planes='$plan_tarifario'
								AND trim(c37_programe)='$tipo_programa'
								AND c48_credencial IS NOT NULL 					
							";

							$error_bd_seq="";
							$resultados_reporte=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($query_reporte, $error_bd_seq);		
							if($error_bd_seq!="")
							{
							    $mensaje_error.=  "Error al realizar la consulta del reporte actual.<br>";
							}//fin if


							$contador_filas=0;
							foreach ($resultados_reporte as $key => $fila_actual) 
							{


								$contador_poblacion_mes_year_credencial=$fila_actual["contador_por_credencial"];

								$mensaje="";
								$mensaje.="\"$cont_meses\";\"$key_years\";\"Numero de registros\";\"$contador_poblacion_mes_year_credencial\";\"FEMENINO\";\"con el plan tarifario\";\"$plan_tarifario\";\"( $descripcion_plan_tarifario )\";\" regional\";\"$regional\";\"$descripcion_regional\"";
								
								//echo str_replace(array("\"",";")," ", $mensaje)."<br>";
								ob_flush();
								flush();
								
								fwrite($archivo_reporte_especificos, $mensaje."\n");
								$escribio_info=true;
								$contador_filas++;
								
							}//fin foreach

							$cont_array_custom++;
						}//fin while plan tarifario
						$cont_array_reg++;
					}//fin while regional
					
					
					echo "Termino Mes $cont_meses del Year $key_years<br>";$cont_meses++;
				}//fin contador meses
				
			}//fin tipo reporte es 11

			if($tipo_reporte=="12")
			{
				$mensaje="";
				$mensaje.="\"Reporte Seleccionado Tipo:\";\"$tipo_reporte\";\"Plan Tarifario, grupo edad, Programa $tipo_programa\"";			
				echo "Tipo Reporte: ".str_replace(array("\"",";")," ", $mensaje)."<br>";
				ob_flush();
				flush();
				//fwrite($archivo_reporte_especificos, $mensaje."\n");

				$array_grupo_edad=array();
				$array_grupo_edad[]=array("1","menores 1 ano");
				$array_grupo_edad[]=array("2","1 a 4 anos");
				$array_grupo_edad[]=array("3","5 a 9 anos");
				$array_grupo_edad[]=array("4","10 a 14 anos");
				$array_grupo_edad[]=array("5","15 a 19 anos");
				$array_grupo_edad[]=array("6","20 a 24 anos");
				$array_grupo_edad[]=array("7","25 a 29 anos");
				$array_grupo_edad[]=array("8","30 a 34 anos");
				$array_grupo_edad[]=array("9","35 a 39 anos");
				$array_grupo_edad[]=array("10","40 a 44 anos");
				$array_grupo_edad[]=array("11","45 a 49 anos");
				$array_grupo_edad[]=array("12","50 a 54 anos");
				$array_grupo_edad[]=array("13","55 a 59 anos");
				$array_grupo_edad[]=array("14","60 a 64 anos");
				$array_grupo_edad[]=array("15","65 a 69 anos");
				$array_grupo_edad[]=array("16","70 a 74 anos");
				$array_grupo_edad[]=array("17","75 y mas anos");

				$array_plan_tarifario=array();
				$array_plan_tarifario[]=array("1","Familiar");
				$array_plan_tarifario[]=array("2","Colectivos");
				$array_plan_tarifario[]=array("3","Asociados");

				

				$cont_meses=1;
				while($cont_meses<=12)
				{
					$cont_array_grupo_edad=0;
					while($cont_array_grupo_edad<count($array_grupo_edad) )
					{
						$grupo_edad=$array_grupo_edad[$cont_array_grupo_edad][0];
						$descripcion_grupo_edad=$array_grupo_edad[$cont_array_grupo_edad][1];
						$cont_array_custom=0;
						while($cont_array_custom<count($array_plan_tarifario) )
						{
							$plan_tarifario=$array_plan_tarifario[$cont_array_custom][0];
							$descripcion_plan_tarifario=$array_plan_tarifario[$cont_array_custom][1];
													

							$query_reporte="";
							$query_reporte.="
								SELECT count(distinct c48_credencial) as contador_por_credencial FROM $tabla_year_actual 
								WHERE c31_mesper='$cont_meses' 
								AND c32_anoper='$key_years'
								AND c35_grupoed='$grupo_edad'
								AND c44_planes='$plan_tarifario'
								AND trim(c37_programe)='$tipo_programa'
								AND c48_credencial IS NOT NULL 					
							";

							$error_bd_seq="";
							$resultados_reporte=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($query_reporte, $error_bd_seq);		
							if($error_bd_seq!="")
							{
							    $mensaje_error.=  "Error al realizar la consulta del reporte actual.<br>";
							}//fin if


							$contador_filas=0;
							foreach ($resultados_reporte as $key => $fila_actual) 
							{


								$contador_poblacion_mes_year_credencial=$fila_actual["contador_por_credencial"];

								$mensaje="";
								$mensaje.="\"$cont_meses\";\"$key_years\";\"Numero de registros\";\"$contador_poblacion_mes_year_credencial\";\"con el plan tarifario\";\"$plan_tarifario\";\"( $descripcion_plan_tarifario )\"";
								
								//echo str_replace(array("\"",";")," ", $mensaje)."<br>";
								ob_flush();
								flush();
								
								fwrite($archivo_reporte_especificos, $mensaje."\n");
								$escribio_info=true;
								$contador_filas++;
								
							}//fin foreach

							$cont_array_custom++;
						}//fin while plan tarifario

						$cont_array_grupo_edad++;
					}//fin while grupo edad
					
					echo "Termino Mes $cont_meses del Year $key_years<br>";$cont_meses++;
				}//fin contador meses
				
			}//fin tipo reporte es 12

			if($tipo_reporte=="13")
			{
				$mensaje="";
				$mensaje.="\"Reporte Seleccionado Tipo:\";\"$tipo_reporte\";\"Plan Tarifario, grupo edad, regional Programa $tipo_programa\"";			
				echo "Tipo Reporte: ".str_replace(array("\"",";")," ", $mensaje)."<br>";
				ob_flush();
				flush();
				//fwrite($archivo_reporte_especificos, $mensaje."\n");

				$array_regional=array();
				$array_regional[]=array("1","Cali");
				$array_regional[]=array("2","Medellín");
				$array_regional[]=array("3","Caribe");
				$array_regional[]=array("4","Bogotá");
				$array_regional[]=array("5","Eje Cafetero");
				$array_regional[]=array("7","Exterior");
				$array_regional[]=array("8","Unión Temporal");

				$array_grupo_edad=array();
				$array_grupo_edad[]=array("1","menores 1 ano");
				$array_grupo_edad[]=array("2","1 a 4 anos");
				$array_grupo_edad[]=array("3","5 a 9 anos");
				$array_grupo_edad[]=array("4","10 a 14 anos");
				$array_grupo_edad[]=array("5","15 a 19 anos");
				$array_grupo_edad[]=array("6","20 a 24 anos");
				$array_grupo_edad[]=array("7","25 a 29 anos");
				$array_grupo_edad[]=array("8","30 a 34 anos");
				$array_grupo_edad[]=array("9","35 a 39 anos");
				$array_grupo_edad[]=array("10","40 a 44 anos");
				$array_grupo_edad[]=array("11","45 a 49 anos");
				$array_grupo_edad[]=array("12","50 a 54 anos");
				$array_grupo_edad[]=array("13","55 a 59 anos");
				$array_grupo_edad[]=array("14","60 a 64 anos");
				$array_grupo_edad[]=array("15","65 a 69 anos");
				$array_grupo_edad[]=array("16","70 a 74 anos");
				$array_grupo_edad[]=array("17","75 y mas anos");

				$array_plan_tarifario=array();
				$array_plan_tarifario[]=array("1","Familiar");
				$array_plan_tarifario[]=array("2","Colectivos");
				$array_plan_tarifario[]=array("3","Asociados");

				

				$cont_meses=1;
				while($cont_meses<=12)
				{
					$cont_array_grupo_edad=0;
					while($cont_array_grupo_edad<count($array_grupo_edad) )
					{
						$grupo_edad=$array_grupo_edad[$cont_array_grupo_edad][0];
						$descripcion_grupo_edad=$array_grupo_edad[$cont_array_grupo_edad][1];

						$cont_array_reg=0;
						while($cont_array_reg<count($array_regional) )
						{
							$regional=$array_regional[$cont_array_reg][0];
							$descripcion_regional=$array_regional[$cont_array_reg][1];

							$cont_array_custom=0;
							while($cont_array_custom<count($array_plan_tarifario) )
							{
								$plan_tarifario=$array_plan_tarifario[$cont_array_custom][0];
								$descripcion_plan_tarifario=$array_plan_tarifario[$cont_array_custom][1];
								
								$query_reporte="";
								$query_reporte.="
									SELECT count(distinct c48_credencial) as contador_por_credencial FROM $tabla_year_actual 
									WHERE c31_mesper='$cont_meses' 
									AND c32_anoper='$key_years'
									AND c35_grupoed='$grupo_edad'
									AND c38_regional='$regional'
									AND c44_planes='$plan_tarifario'
									AND trim(c37_programe)='$tipo_programa'
									AND c48_credencial IS NOT NULL 					
								";

								$error_bd_seq="";
								$resultados_reporte=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($query_reporte, $error_bd_seq);		
								if($error_bd_seq!="")
								{
								    $mensaje_error.=  "Error al realizar la consulta del reporte actual.<br>";
								}//fin if


								$contador_filas=0;
								foreach ($resultados_reporte as $key => $fila_actual) 
								{


									$contador_poblacion_mes_year_credencial=$fila_actual["contador_por_credencial"];

									$mensaje="";
									$mensaje.="\"$cont_meses\";\"$key_years\";\"Numero de registros\";\"$contador_poblacion_mes_year_credencial\";\"con el plan tarifario\";\"$plan_tarifario\";\"( $descripcion_plan_tarifario )\";\" regional\";\"$regional\";\"$descripcion_regional\"";
									
									//echo str_replace(array("\"",";")," ", $mensaje)."<br>";
									ob_flush();
									flush();
									
									fwrite($archivo_reporte_especificos, $mensaje."\n");
									$escribio_info=true;
									$contador_filas++;
									
								}//fin foreach

								

								$cont_array_custom++;
							}//fin while plan tarifario
							$cont_array_reg++;
						}//fin while regional
						$cont_array_grupo_edad++;
					}//fin while grupo edad
					
					echo "Termino Mes $cont_meses del Year $key_years<br>";$cont_meses++;
				}//fin contador meses
				
			}//fin tipo reporte es 13

			if($tipo_reporte=="14")
			{
				$mensaje="";
				$mensaje.="\"Reporte Seleccionado Tipo:\";\"$tipo_reporte\";\"Tipo Cotizante, Programa $tipo_programa\"";			
				echo "Tipo Reporte: ".str_replace(array("\"",";")," ", $mensaje)."<br>";
				ob_flush();
				flush();
				//fwrite($archivo_reporte_especificos, $mensaje."\n");

				$array_tipo_cotizante=array();
				$array_tipo_cotizante[]=array("A","Asociados");
				$array_tipo_cotizante[]=array("AS","Asesores");
				$array_tipo_cotizante[]=array("C","Colectivos");
				$array_tipo_cotizante[]=array("F","Familiar");
				$array_tipo_cotizante[]=array("EM","Empleados");

				

				$cont_meses=1;
				while($cont_meses<=12)
				{
					$cont_array_custom=0;
					while($cont_array_custom<count($array_plan_tarifario) )
					{
						$tipo_cotizante=$array_tipo_cotizante[$cont_array_custom][0];
						$descripcion_tipo_cotizante=$array_tipo_cotizante[$cont_array_custom][1];
						
						$query_reporte="";
						$query_reporte.="
							SELECT count(distinct c48_credencial) as contador_por_credencial FROM $tabla_year_actual 
							WHERE c31_mesper='$cont_meses' 
							AND c32_anoper='$key_years'
							AND c17_sexo='1'
							AND trim(c1_tipocont)='$tipo_cotizante'
							AND trim(c37_programe)='$tipo_programa'
							AND c48_credencial IS NOT NULL 					
						";

						$error_bd_seq="";
						$resultados_reporte=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($query_reporte, $error_bd_seq);		
						if($error_bd_seq!="")
						{
						    $mensaje_error.=  "Error al realizar la consulta del reporte actual.<br>";
						}//fin if


						$contador_filas=0;
						foreach ($resultados_reporte as $key => $fila_actual) 
						{


							$contador_poblacion_mes_year_credencial=$fila_actual["contador_por_credencial"];

							$mensaje="";
							$mensaje.="\"$cont_meses\";\"$key_years\";\"Numero de registros\";\"$contador_poblacion_mes_year_credencial\";\"MASCULINO\";\"con el tipo cotizante\";\"$tipo_cotizante\";\"( $descripcion_tipo_cotizante )\"";
							
							//echo str_replace(array("\"",";")," ", $mensaje)."<br>";
							ob_flush();
							flush();
							
							fwrite($archivo_reporte_especificos, $mensaje."\n");
							$escribio_info=true;
							$contador_filas++;
							
						}//fin foreach

						$query_reporte="";
						$query_reporte.="
							SELECT count(distinct c48_credencial) as contador_por_credencial FROM $tabla_year_actual 
							WHERE c31_mesper='$cont_meses' 
							AND c32_anoper='$key_years'
							AND c17_sexo='2'
							AND trim(c1_tipocont)='$tipo_cotizante'
							AND trim(c37_programe)='$tipo_programa'
							AND c48_credencial IS NOT NULL 					
						";

						$error_bd_seq="";
						$resultados_reporte=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($query_reporte, $error_bd_seq);		
						if($error_bd_seq!="")
						{
						    $mensaje_error.=  "Error al realizar la consulta del reporte actual.<br>";
						}//fin if


						$contador_filas=0;
						foreach ($resultados_reporte as $key => $fila_actual) 
						{


							$contador_poblacion_mes_year_credencial=$fila_actual["contador_por_credencial"];

							$mensaje="";
							$mensaje.="\"$cont_meses\";\"$key_years\";\"Numero de registros\";\"$contador_poblacion_mes_year_credencial\";\"FEMENINO\";\"con el tipo cotizante\";\"$tipo_cotizante\";\"( $descripcion_tipo_cotizante )\"";
							
							//echo str_replace(array("\"",";")," ", $mensaje)."<br>";
							ob_flush();
							flush();
							
							fwrite($archivo_reporte_especificos, $mensaje."\n");
							$escribio_info=true;
							$contador_filas++;
							
						}//fin foreach

						$cont_array_custom++;
					}//fin while plan tarifario
					
					
					echo "Termino Mes $cont_meses del Year $key_years<br>";$cont_meses++;
				}//fin contador meses
				
			}//fin tipo reporte es 14

			if($tipo_reporte=="15")
			{
				$mensaje="";
				$mensaje.="\"Reporte Seleccionado Tipo:\";\"$tipo_reporte\";\"Tipo Cotizante, Genero, regional Programa $tipo_programa\"";			
				echo "Tipo Reporte: ".str_replace(array("\"",";")," ", $mensaje)."<br>";
				ob_flush();
				flush();
				//fwrite($archivo_reporte_especificos, $mensaje."\n");

				$array_regional=array();
				$array_regional[]=array("1","Cali");
				$array_regional[]=array("2","Medellín");
				$array_regional[]=array("3","Caribe");
				$array_regional[]=array("4","Bogotá");
				$array_regional[]=array("5","Eje Cafetero");
				$array_regional[]=array("7","Exterior");
				$array_regional[]=array("8","Unión Temporal");

				$array_tipo_cotizante=array();
				$array_tipo_cotizante[]=array("A","Asociados");
				$array_tipo_cotizante[]=array("AS","Asesores");
				$array_tipo_cotizante[]=array("C","Colectivos");
				$array_tipo_cotizante[]=array("F","Familiar");
				$array_tipo_cotizante[]=array("EM","Empleados");

				

				$cont_meses=1;
				while($cont_meses<=12)
				{
					$cont_array_reg=0;
					while($cont_array_reg<count($array_regional) )
					{
						$regional=$array_regional[$cont_array_reg][0];
						$descripcion_regional=$array_regional[$cont_array_reg][1];

						$cont_array_custom=0;
						while($cont_array_custom<count($array_tipo_cotizante) )
						{
							$tipo_cotizante=$array_tipo_cotizante[$cont_array_custom][0];
							$descripcion_tipo_cotizante=$array_tipo_cotizante[$cont_array_custom][1];
							
							$query_reporte="";
							$query_reporte.="
								SELECT count(distinct c48_credencial) as contador_por_credencial FROM $tabla_year_actual 
								WHERE c31_mesper='$cont_meses' 
								AND c32_anoper='$key_years'
								AND c17_sexo='1'
								AND c38_regional='$regional'
								AND trim(c1_tipocont)='$tipo_cotizante'
								AND trim(c37_programe)='$tipo_programa'
								AND c48_credencial IS NOT NULL 					
							";

							$error_bd_seq="";
							$resultados_reporte=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($query_reporte, $error_bd_seq);		
							if($error_bd_seq!="")
							{
							    $mensaje_error.=  "Error al realizar la consulta del reporte actual.<br>";
							}//fin if


							$contador_filas=0;
							foreach ($resultados_reporte as $key => $fila_actual) 
							{


								$contador_poblacion_mes_year_credencial=$fila_actual["contador_por_credencial"];

								$mensaje="";
								$mensaje.="\"$cont_meses\";\"$key_years\";\"Numero de registros\";\"$contador_poblacion_mes_year_credencial\";\"MASCULINO\";\"con el tipo cotizante\";\"$tipo_cotizante\";\"( $descripcion_tipo_cotizante )\";\" regional\";\"$regional\";\"$descripcion_regional\"";
								
								//echo str_replace(array("\"",";")," ", $mensaje)."<br>";
								ob_flush();
								flush();
								
								fwrite($archivo_reporte_especificos, $mensaje."\n");
								$escribio_info=true;
								$contador_filas++;
								
							}//fin foreach

							$query_reporte="";
							$query_reporte.="
								SELECT count(distinct c48_credencial) as contador_por_credencial FROM $tabla_year_actual 
								WHERE c31_mesper='$cont_meses' 
								AND c32_anoper='$key_years'
								AND c17_sexo='2'
								AND c38_regional='$regional'
								AND trim(c1_tipocont)='$tipo_cotizante'
								AND trim(c37_programe)='$tipo_programa'
								AND c48_credencial IS NOT NULL 					
							";

							$error_bd_seq="";
							$resultados_reporte=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($query_reporte, $error_bd_seq);		
							if($error_bd_seq!="")
							{
							    $mensaje_error.=  "Error al realizar la consulta del reporte actual.<br>";
							}//fin if


							$contador_filas=0;
							foreach ($resultados_reporte as $key => $fila_actual) 
							{


								$contador_poblacion_mes_year_credencial=$fila_actual["contador_por_credencial"];

								$mensaje="";
								$mensaje.="\"$cont_meses\";\"$key_years\";\"Numero de registros\";\"$contador_poblacion_mes_year_credencial\";\"FEMENINO\";\"con el tipo cotizante\";\"$tipo_cotizante\";\"( $descripcion_tipo_cotizante )\";\" regional\";\"$regional\";\"$descripcion_regional\"";
								
								//echo str_replace(array("\"",";")," ", $mensaje)."<br>";
								ob_flush();
								flush();
								
								fwrite($archivo_reporte_especificos, $mensaje."\n");
								$escribio_info=true;
								$contador_filas++;
								
							}//fin foreach

							$cont_array_custom++;
						}//fin while plan tarifario
						$cont_array_reg++;
					}//fin while regional
					
					
					echo "Termino Mes $cont_meses del Year $key_years<br>";$cont_meses++;
				}//fin contador meses
				
			}//fin tipo reporte es 15

			if($tipo_reporte=="16")
			{
				$mensaje="";
				$mensaje.="\"Reporte Seleccionado Tipo:\";\"$tipo_reporte\";\"Tipo Cotizante, grupo edad, Programa $tipo_programa\"";			
				echo "Tipo Reporte: ".str_replace(array("\"",";")," ", $mensaje)."<br>";
				ob_flush();
				flush();
				//fwrite($archivo_reporte_especificos, $mensaje."\n");

				$array_grupo_edad=array();
				$array_grupo_edad[]=array("1","menores 1 ano");
				$array_grupo_edad[]=array("2","1 a 4 anos");
				$array_grupo_edad[]=array("3","5 a 9 anos");
				$array_grupo_edad[]=array("4","10 a 14 anos");
				$array_grupo_edad[]=array("5","15 a 19 anos");
				$array_grupo_edad[]=array("6","20 a 24 anos");
				$array_grupo_edad[]=array("7","25 a 29 anos");
				$array_grupo_edad[]=array("8","30 a 34 anos");
				$array_grupo_edad[]=array("9","35 a 39 anos");
				$array_grupo_edad[]=array("10","40 a 44 anos");
				$array_grupo_edad[]=array("11","45 a 49 anos");
				$array_grupo_edad[]=array("12","50 a 54 anos");
				$array_grupo_edad[]=array("13","55 a 59 anos");
				$array_grupo_edad[]=array("14","60 a 64 anos");
				$array_grupo_edad[]=array("15","65 a 69 anos");
				$array_grupo_edad[]=array("16","70 a 74 anos");
				$array_grupo_edad[]=array("17","75 y mas anos");

				$array_tipo_cotizante=array();
				$array_tipo_cotizante[]=array("A","Asociados");
				$array_tipo_cotizante[]=array("AS","Asesores");
				$array_tipo_cotizante[]=array("C","Colectivos");
				$array_tipo_cotizante[]=array("F","Familiar");
				$array_tipo_cotizante[]=array("EM","Empleados");

				

				$cont_meses=1;
				while($cont_meses<=12)
				{
					$cont_array_grupo_edad=0;
					while($cont_array_grupo_edad<count($array_grupo_edad) )
					{
						$grupo_edad=$array_grupo_edad[$cont_array_grupo_edad][0];
						$descripcion_grupo_edad=$array_grupo_edad[$cont_array_grupo_edad][1];
						$cont_array_custom=0;
						while($cont_array_custom<count($array_tipo_cotizante) )
						{
							$tipo_cotizante=$array_tipo_cotizante[$cont_array_custom][0];
							$descripcion_tipo_cotizante=$array_tipo_cotizante[$cont_array_custom][1];
													

							$query_reporte="";
							$query_reporte.="
								SELECT count(distinct c48_credencial) as contador_por_credencial FROM $tabla_year_actual 
								WHERE c31_mesper='$cont_meses' 
								AND c32_anoper='$key_years'
								AND c35_grupoed='$grupo_edad'
								AND trim(c1_tipocont)='$tipo_cotizante'
								AND trim(c37_programe)='$tipo_programa'
								AND c48_credencial IS NOT NULL 					
							";

							$error_bd_seq="";
							$resultados_reporte=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($query_reporte, $error_bd_seq);		
							if($error_bd_seq!="")
							{
							    $mensaje_error.=  "Error al realizar la consulta del reporte actual.<br>";
							}//fin if


							$contador_filas=0;
							foreach ($resultados_reporte as $key => $fila_actual) 
							{


								$contador_poblacion_mes_year_credencial=$fila_actual["contador_por_credencial"];

								$mensaje="";
								$mensaje.="\"$cont_meses\";\"$key_years\";\"Numero de registros\";\"$contador_poblacion_mes_year_credencial\";\"con el tipo cotizante\";\"$tipo_cotizante\";\"( $descripcion_tipo_cotizante )\";\"con el grupo edad grupo_edad\";\"$grupo_edad\"";
								
								//echo str_replace(array("\"",";")," ", $mensaje)."<br>";
								ob_flush();
								flush();
								
								fwrite($archivo_reporte_especificos, $mensaje."\n");
								$escribio_info=true;
								$contador_filas++;
								
							}//fin foreach
							

							$cont_array_custom++;
						}//fin while plan tarifario
						
						$cont_array_grupo_edad++;
					}//fin while grupo edad
					
					echo "Termino Mes $cont_meses del Year $key_years<br>";$cont_meses++;
				}//fin contador meses
				
			}//fin tipo reporte es 16

			if($tipo_reporte=="17")
			{
				$mensaje="";
				$mensaje.="\"Reporte Seleccionado Tipo:\";\"$tipo_reporte\";\"Tipo Cotizante, grupo edad, regional Programa $tipo_programa\"";			
				echo "Tipo Reporte: ".str_replace(array("\"",";")," ", $mensaje)."<br>";
				ob_flush();
				flush();
				//fwrite($archivo_reporte_especificos, $mensaje."\n");

				$array_regional=array();
				$array_regional[]=array("1","Cali");
				$array_regional[]=array("2","Medellín");
				$array_regional[]=array("3","Caribe");
				$array_regional[]=array("4","Bogotá");
				$array_regional[]=array("5","Eje Cafetero");
				$array_regional[]=array("7","Exterior");
				$array_regional[]=array("8","Unión Temporal");

				$array_grupo_edad=array();
				$array_grupo_edad[]=array("1","menores 1 ano");
				$array_grupo_edad[]=array("2","1 a 4 anos");
				$array_grupo_edad[]=array("3","5 a 9 anos");
				$array_grupo_edad[]=array("4","10 a 14 anos");
				$array_grupo_edad[]=array("5","15 a 19 anos");
				$array_grupo_edad[]=array("6","20 a 24 anos");
				$array_grupo_edad[]=array("7","25 a 29 anos");
				$array_grupo_edad[]=array("8","30 a 34 anos");
				$array_grupo_edad[]=array("9","35 a 39 anos");
				$array_grupo_edad[]=array("10","40 a 44 anos");
				$array_grupo_edad[]=array("11","45 a 49 anos");
				$array_grupo_edad[]=array("12","50 a 54 anos");
				$array_grupo_edad[]=array("13","55 a 59 anos");
				$array_grupo_edad[]=array("14","60 a 64 anos");
				$array_grupo_edad[]=array("15","65 a 69 anos");
				$array_grupo_edad[]=array("16","70 a 74 anos");
				$array_grupo_edad[]=array("17","75 y mas anos");

				$array_tipo_cotizante=array();
				$array_tipo_cotizante[]=array("A","Asociados");
				$array_tipo_cotizante[]=array("AS","Asesores");
				$array_tipo_cotizante[]=array("C","Colectivos");
				$array_tipo_cotizante[]=array("F","Familiar");
				$array_tipo_cotizante[]=array("EM","Empleados");

				

				$cont_meses=1;
				while($cont_meses<=12)
				{
					$cont_array_grupo_edad=0;
					while($cont_array_grupo_edad<count($array_grupo_edad) )
					{
						$grupo_edad=$array_grupo_edad[$cont_array_grupo_edad][0];
						$descripcion_grupo_edad=$array_grupo_edad[$cont_array_grupo_edad][1];

						$cont_array_reg=0;
						while($cont_array_reg<count($array_regional) )
						{
							$regional=$array_regional[$cont_array_reg][0];
							$descripcion_regional=$array_regional[$cont_array_reg][1];

							$cont_array_custom=0;
							while($cont_array_custom<count($array_tipo_cotizante) )
							{
								$tipo_cotizante=$array_tipo_cotizante[$cont_array_custom][0];
								$descripcion_tipo_cotizante=$array_tipo_cotizante[$cont_array_custom][1];
								
								$query_reporte="";
								$query_reporte.="
									SELECT count(distinct c48_credencial) as contador_por_credencial FROM $tabla_year_actual 
									WHERE c31_mesper='$cont_meses' 
									AND c32_anoper='$key_years'
									AND c35_grupoed='$grupo_edad'
									AND c38_regional='$regional'
									AND trim(c1_tipocont)='$tipo_cotizante'
									AND trim(c37_programe)='$tipo_programa'
									AND c48_credencial IS NOT NULL 					
								";

								$error_bd_seq="";
								$resultados_reporte=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($query_reporte, $error_bd_seq);		
								if($error_bd_seq!="")
								{
								    $mensaje_error.=  "Error al realizar la consulta del reporte actual.<br>";
								}//fin if


								$contador_filas=0;
								foreach ($resultados_reporte as $key => $fila_actual) 
								{


									$contador_poblacion_mes_year_credencial=$fila_actual["contador_por_credencial"];

									$mensaje="";
									$mensaje.="\"$cont_meses\";\"$key_years\";\"Numero de registros\";\"$contador_poblacion_mes_year_credencial\";\"con el tipo cotizante\";\"$tipo_cotizante\";\"( $descripcion_tipo_cotizante )\";\" con el grupo edad \";\"grupo_edad\";\"( $descripcion_grupo_edad )\";\" regional\";\"$regional\";\"$descripcion_regional\"";
									
									//echo str_replace(array("\"",";")," ", $mensaje)."<br>";
									ob_flush();
									flush();
									
									fwrite($archivo_reporte_especificos, $mensaje."\n");
									$escribio_info=true;
									$contador_filas++;
									
								}//fin foreach

								

								$cont_array_custom++;
							}//fin while plan tarifario
							$cont_array_reg++;
						}//fin while regional
						$cont_array_grupo_edad++;
					}//fin while grupo edad
					
					echo "Termino Mes $cont_meses del Year $key_years<br>";$cont_meses++;
				}//fin contador meses
				
			}//fin tipo reporte es 17

			if($tipo_reporte=="18")
			{
				$mensaje="";
				$mensaje.="\"Reporte Seleccionado Tipo:\";\"$tipo_reporte\";\"Antiguedad, teniendo en cuenta ingreso a CMP , Programa $tipo_programa\"";			
				echo "Tipo Reporte: ".str_replace(array("\"",";")," ", $mensaje)."<br>";
				ob_flush();
				flush();
				//fwrite($archivo_reporte_especificos, $mensaje."\n");



				$cont_meses=1;
				while($cont_meses<=12)
				{
					
					$accumulador_registros_consultados_por_mes=0;
					$total_registros_mes=0;

					//c0_sucursal || '-' || c5_numecont  || '-' || c10_familias  || '-' || c12_nitusuar
					$query_reporte="";
					$query_reporte.="
						SELECT  count(distinct c48_credencial) as contador_por_credencial FROM $tabla_year_actual 
						WHERE c31_mesper='$cont_meses' 
						AND c32_anoper='$key_years' 
						AND trim(c37_programe)='$tipo_programa'
						AND c0_sucursal IS NOT NULL
						AND c5_numecont IS NOT NULL
						AND c10_familias IS NOT NULL
						AND c12_nitusuar IS NOT NULL 
						AND c48_credencial IS NOT NULL 	
						AND c21_ingcooaa IS NOT NULL
						AND c22_ingcoomm IS NOT NULL				
					";

					$error_bd_seq="";
					$resultados_reporte=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($query_reporte, $error_bd_seq);		
					if($error_bd_seq!="")
					{
					    $mensaje_error.=  "Error al realizar la consulta del reporte actual.<br>";
					}//fin if


					$contador_filas=0;
					foreach ($resultados_reporte as $key => $fila_actual) 
					{
						$total_registros_mes=intval($fila_actual["contador_por_credencial"]);
						


						$cont_filtro_years_antiguedad=0;
						while($cont_filtro_years_antiguedad<=50
							&& $accumulador_registros_consultados_por_mes<$total_registros_mes
							)//50 years
						{
							echo "year: ".$key_years.", mes: ".$cont_meses.", verifica antiguedad para :".$cont_filtro_years_antiguedad." annos/years, total registros mes:".$total_registros_mes.", registros acumulados mes:".$accumulador_registros_consultados_por_mes."<br>";
							ob_flush();
							flush();
							

							$query_reporte="";
							$query_reporte.="
								SELECT  count(distinct c48_credencial) as contador_por_credencial FROM $tabla_year_actual 
								WHERE c31_mesper='$cont_meses' 
								AND c32_anoper='$key_years' 
								AND trim(c37_programe)='$tipo_programa'
								AND ( ( (c32_anoper::text ||'-'||c31_mesper::text||'-01')::date - (c21_ingcooaa::text ||'-'||c22_ingcoomm::text||'-'||(case when c23_ingcoodd='99'then '01' else c23_ingcoodd end) )::date ) / 365)='$cont_filtro_years_antiguedad'
								AND c0_sucursal IS NOT NULL
								AND c5_numecont IS NOT NULL
								AND c10_familias IS NOT NULL
								AND c12_nitusuar IS NOT NULL 
								AND c48_credencial IS NOT NULL
								AND c21_ingcooaa IS NOT NULL
								AND c22_ingcoomm IS NOT NULL 					
							";

							$error_bd_seq="";
							$resultados_reporte2=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($query_reporte, $error_bd_seq);		
							if($error_bd_seq!="")
							{
							    $mensaje_error.=  "Error al realizar la consulta del reporte actual.<br>";
							}//fin if

							$contador_filas_2=0;
							foreach ($resultados_reporte2 as $key2 => $fila_actual2) 
							{
								$contador_poblacion_mes_year_credencial=$fila_actual2["contador_por_credencial"];
								$accumulador_registros_consultados_por_mes+=$contador_poblacion_mes_year_credencial;

								$mensaje="";
								$mensaje.="\"$cont_meses\";del anno\";\"$key_years\";";
								$mensaje.="\"hay\";\"$contador_poblacion_mes_year_credencial\";\" con antiguedad en annos de ingreso a coomeva\";\"$cont_filtro_years_antiguedad\";\"  y tipo programa\";\"$tipo_programa\"";
								//echo str_replace(array("\"",";")," ", $mensaje)."<br>";
								ob_flush();
								flush();
								fwrite($archivo_reporte_especificos, $mensaje."\n");
								$escribio_info=true;
								$contador_filas_2++;
							}//fin foreach

							$cont_filtro_years_antiguedad++;
						}//fin while
						$contador_filas++;
						
					}//fin foreach
					
					
					
					echo "Termino Mes $cont_meses del Year $key_years<br>";$cont_meses++;
				}//fin contador meses
				
			}//fin tipo reporte es 18

			if($tipo_reporte=="19")
			{
				$mensaje="";
				$mensaje.="\"Reporte Seleccionado Tipo:\";\"$tipo_reporte\";\"Antiguedad, teniendo en cuenta ingreso a SISTEMA , Programa $tipo_programa\"";			
				echo "Tipo Reporte: ".str_replace(array("\"",";")," ", $mensaje)."<br>";
				ob_flush();
				flush();
				//fwrite($archivo_reporte_especificos, $mensaje."\n");



				$cont_meses=1;
				while($cont_meses<=12)
				{
					
					$accumulador_registros_consultados_por_mes=0;
					$total_registros_mes=0;

					//c0_sucursal || '-' || c5_numecont  || '-' || c10_familias  || '-' || c12_nitusuar
					$query_reporte="";
					$query_reporte.="
						SELECT  count(distinct c48_credencial) as contador_por_credencial FROM $tabla_year_actual 
						WHERE c31_mesper='$cont_meses' 
						AND c32_anoper='$key_years' 
						AND trim(c37_programe)='$tipo_programa'
						AND c0_sucursal IS NOT NULL
						AND c5_numecont IS NOT NULL
						AND c10_familias IS NOT NULL
						AND c12_nitusuar IS NOT NULL 
						AND c48_credencial IS NOT NULL 	
						AND c18_ingsisaa IS NOT NULL
						AND c19_ingsismm IS NOT NULL				
					";

					$error_bd_seq="";
					$resultados_reporte=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($query_reporte, $error_bd_seq);		
					if($error_bd_seq!="")
					{
					    $mensaje_error.=  "Error al realizar la consulta del reporte actual.<br>";
					}//fin if


					$contador_filas=0;
					foreach ($resultados_reporte as $key => $fila_actual) 
					{
						$total_registros_mes=intval($fila_actual["contador_por_credencial"]);


						


						$cont_filtro_years_antiguedad=0;
						while($cont_filtro_years_antiguedad<=50
							&& $accumulador_registros_consultados_por_mes<$total_registros_mes
							)//50 years
						{
							echo "year: ".$key_years.", mes: ".$cont_meses.", verifica antiguedad para :".$cont_filtro_years_antiguedad." annos/years, total registros mes:".$total_registros_mes.", registros acumulados mes:".$accumulador_registros_consultados_por_mes."<br>";
							ob_flush();
							flush();

							

							$query_reporte="";
							$query_reporte.="
								SELECT  count(distinct c48_credencial) as contador_por_credencial FROM $tabla_year_actual 
								WHERE c31_mesper='$cont_meses' 
								AND c32_anoper='$key_years' 
								AND trim(c37_programe)='$tipo_programa'
								AND ( ( (c32_anoper::text ||'-'||c31_mesper::text||'-01')::date - (c18_ingsisaa::text ||'-'||c19_ingsismm::text||'-'||(case when c20_ingsisdd='99'then '01' else c20_ingsisdd end) )::date ) / 365)='$cont_filtro_years_antiguedad'
								AND c0_sucursal IS NOT NULL
								AND c5_numecont IS NOT NULL
								AND c10_familias IS NOT NULL
								AND c12_nitusuar IS NOT NULL 
								AND c48_credencial IS NOT NULL
								AND c18_ingsisaa IS NOT NULL
								AND c19_ingsismm IS NOT NULL 					
							";

							$error_bd_seq="";
							$resultados_reporte2=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($query_reporte, $error_bd_seq);		
							if($error_bd_seq!="")
							{
							    $mensaje_error.=  "Error al realizar la consulta del reporte actual.<br>";
							}//fin if

							$contador_filas_2=0;
							foreach ($resultados_reporte2 as $key2 => $fila_actual2) 
							{
								$contador_poblacion_mes_year_credencial=$fila_actual2["contador_por_credencial"];
								$accumulador_registros_consultados_por_mes+=$contador_poblacion_mes_year_credencial;

								$mensaje="";
								$mensaje.="\"$cont_meses\";del anno\";\"$key_years\";";								
								$mensaje.="\"hay\";\"$contador_poblacion_mes_year_credencial\";\" con antiguedad en annos de ingreso a SISTEMA\";\"$cont_filtro_years_antiguedad\";\"  y tipo programa\";\"$tipo_programa\"";
								//echo str_replace(array("\"",";")," ", $mensaje)."<br>";
								ob_flush();
								flush();
								fwrite($archivo_reporte_especificos, $mensaje."\n");
								$escribio_info=true;
								$contador_filas_2++;
							}//fin foreach

							$cont_filtro_years_antiguedad++;
						}//fin while
						$contador_filas++;
						
					}//fin foreach
					
					
					
					echo "Termino Mes $cont_meses del Year $key_years<br>";$cont_meses++;
				}//fin contador meses
				
			}//fin tipo reporte es 19


			
		}//fin foreach tablas years poblacion

	}//fin hubo error inicio
	fclose($archivo_reporte_comprobacion_inicial);
	fclose($archivo_reporte_especificos);


	//BOTONES DESCARGA
	$botones="";
	//$botones.=" <input type=\'button\' value=\'Descargar Queries, Inconsistencias, Registros Rechazados\'  class=\'btn btn-success color_boton\' onclick=\"download_file(\'$ruta_zip\');\"/> ";
	try
	{
		if(filesize($ruta_repo1)>0 )
		{
			$botones.="<br><input type=\'button\' value=\'Descargar reporte verificacion inicial c48_credencial\'  class=\'btn btn-success color_boton\' onclick=\"download_file(\'$ruta_repo1\');\"/> ";
		}//fin if

		if(filesize($ruta_repo2)>0 )
		{
			$botones.="<br><input type=\'button\' value=\'Descargar reporte especifico\'  class=\'btn btn-success color_boton\' onclick=\"download_file(\'$ruta_repo2\');\"/> ";
		}//fin if
	}//fin try
	catch(Exception $e)
	{
		$botones.="<br><input type=\'button\' value=\'Descargar reporte verificacion inicial c48_credencial\'  class=\'btn btn-success color_boton\' onclick=\"download_file(\'$ruta_repo1\');\"/> ";
		$botones.="<br><input type=\'button\' value=\'Descargar reporte especifico\'  class=\'btn btn-success color_boton\' onclick=\"download_file(\'$ruta_repo2\');\"/> ";
	}//fin catch
	
	//FIN BOTONES DESCARGA

	if(connection_aborted()==false && $escribio_info==true)
	{
		echo "<script>document.getElementById('div_mensaje_exito').style.display='inline';</script>";
		echo "<script>document.getElementById('parrafo_mensaje_exito').innerHTML='$botones';</script>";
		ob_flush();
		flush();
	}
	else if(connection_aborted()==false  )
	{
		echo "<script>document.getElementById('div_mensaje_error').style.display='inline';</script>";
		echo "<script>document.getElementById('parrafo_mensaje_error').innerHTML='$mensaje_error';</script>";
		ob_flush();
		flush();
	}
	
}//fin if activa generar reportes


$coneccionBD->cerrar_conexion();
?>