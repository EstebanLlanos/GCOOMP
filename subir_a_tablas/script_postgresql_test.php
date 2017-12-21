<?php
set_time_limit(936000);
ini_set('max_execution_time', 936000);
ini_set('memory_limit', '2000M');

error_reporting(E_ALL);
ini_set('display_errors', '1');

include_once ('../utiles/clase_coneccion_bd.php');
$conexionbd = new conexion();
$conexionbd->crearConexion();
$conexionbd->cerrar_conexion();
?>