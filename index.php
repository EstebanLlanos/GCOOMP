
<html>
<head><title>GIOSS</title></head>
<body>
<?php
/*
echo "<h1>index</h1>";
include_once ('utiles/clase_coneccion_bd.php');
$coneccionBD = new conexion();
$sql_consulta_usuario="SELECT * FROM gios_perfiles_sistema ";
$resultado_query=$coneccionBD->consultar2($sql_consulta_usuario);
if(is_array($resultado_query) && count($resultado_query))
{
    $resultado_texto_bd="";
    foreach($resultado_query as $key_one=>$fila)
    {
        $fila_obtenida="";
        foreach($fila as $key_two=>$columna)
        {
            if($fila_obtenida!=""){$fila_obtenida.=",";}
            $fila_obtenida.=$columna;
        }
        if($resultado_texto_bd!=""){$resultado_texto_bd.="<br>";}
        $resultado_texto_bd.=$fila_obtenida;
    }
    echo $resultado_texto_bd;
}
session_start();
session_write_close();

NOTA: las sesiones causan bloqueos si no se cierran despues de tomar o escribir informacion de estas
por lo tanto cerrar la sesion es lo recomendado, en especial cuando se usan scripts muy largos
*/
if(isset($_REQUEST["se_cerro_session"]))
{
    echo "<script>window.location = 'pantalla_de_logueo/interfaz_de_logueo.php?se_cerro_session=SI'</script>";
}
else
{
    if(isset($_REQUEST["no_tiene_permiso"]) )
    {
        echo "<script>window.location = 'pantalla_de_logueo/interfaz_de_logueo.php?no_tiene_permiso=true'</script>";
    }//fin if
    else
    {
        echo "<script>window.location = 'pantalla_de_logueo/interfaz_de_logueo.php'</script>";
    }//fin else
}//fin else

?>

</body>
</html>