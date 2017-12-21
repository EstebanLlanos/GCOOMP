<?php

date_default_timezone_set ("America/Bogota");
$fecha_actual = date('Y-m-d');
$tiempo_actual = date('H:i:s');

$array_fecha_para_string=explode("-",$fecha_actual);
$array_tiempo_para_string=explode(":",$tiempo_actual);
$string_tiempo_fecha=$array_fecha_para_string[0].$array_fecha_para_string[1].$array_fecha_para_string[2].$array_tiempo_para_string[0].$array_tiempo_para_string[1].$array_tiempo_para_string[2];

if(isset($_POST["tipo_rips"]) && isset($_FILES["archivos_rips"]) && isset($_POST["nick"]) )
{
    
    $rutaTemporal = '../TEMPORALES/';
    $nueva_ruta_temporal=$rutaTemporal."rips".$_POST["nick"].$string_tiempo_fecha;
    
    //si el mismo usuario habia subido archivos en la misma interfaz sin
    //salirse se borran para evitar llenar el servidor de datos innecesarios
    if(isset($_POST["ruta_fecha_anterior"])
       && $_POST["ruta_fecha_anterior"]!=""
       && strlen($_POST["ruta_fecha_anterior"])>4
       && $_POST["nick"]!="")
    {
        $vieja_ruta_temporal=$rutaTemporal."rips".$_POST["nick"].$_POST["ruta_fecha_anterior"];
        if(file_exists($vieja_ruta_temporal))
        {
            $files_to_erase = glob($vieja_ruta_temporal."/*"); // get all file names
            foreach($files_to_erase as $file_to_be_erased)
            { // iterate files
              if(is_file($file_to_be_erased))
              {
                unlink($file_to_be_erased); // delete file
              }
            }
            rmdir($vieja_ruta_temporal);
        }//is la ruta existe
    }//fin if
    
    //borra lo que habia antes 
    if(!file_exists($nueva_ruta_temporal))
    {
            mkdir($nueva_ruta_temporal, 0700);
    }//fin if
    else
    {
            $files_to_erase = glob($nueva_ruta_temporal."/*"); // get all file names
            foreach($files_to_erase as $file_to_be_erased)
            { // iterate files
              if(is_file($file_to_be_erased))
              {
                unlink($file_to_be_erased); // delete file
              }
            }
    }//fin else
    
    $string_archivos_subidos_exito="";
    
    if($_POST["tipo_rips"]=="prestador_rips")
    {
        //Loop through each file
        for($i=0; $i<count($_FILES['archivos_rips']['name']); $i++)
        {
          //Get the temp file path
          $tmpFilePath = $_FILES['archivos_rips']['tmp_name'][$i];
        
          //Make sure we have a filepath
          if ($tmpFilePath != "")
          {
            //Setup our new file path
            $newFilePath = $nueva_ruta_temporal."/".$_FILES['archivos_rips']['name'][$i];
        
            //Upload the file into the temp dir
            if(move_uploaded_file($tmpFilePath, $newFilePath))
            {
        
              //Handle other code here
              if($string_archivos_subidos_exito!=""){$string_archivos_subidos_exito.="|";}
              $string_archivos_subidos_exito.=explode(".",$_FILES['archivos_rips']['name'][$i])[0];
            }
          }//fin if
        }//fin for
    }//if archivos es tipo prestador
    else if($_POST["tipo_rips"]=="prestador_rips_error")
    {
        
    }
    
    if($_POST["tipo_rips"]=="eapb_rips")
    {
        //Loop through each file
        for($i=0; $i<count($_FILES['archivos_rips']['name']); $i++)
        {
          //Get the temp file path
          $tmpFilePath = $_FILES['archivos_rips']['tmp_name'][$i];
        
          //Make sure we have a filepath
          if ($tmpFilePath != "")
          {
            //Setup our new file path
            $newFilePath = $nueva_ruta_temporal."/".$_FILES['archivos_rips']['name'][$i];
        
            //Upload the file into the temp dir
            if(move_uploaded_file($tmpFilePath, $newFilePath))
            {
        
              //Handle other code here
              /*
              if($string_archivos_subidos_exito!=""){$string_archivos_subidos_exito.="|";}
              $string_archivos_subidos_exito.=explode(".",$_FILES['archivos_rips']['name'][$i])[0];
              */
                $zip = new ZipArchive();
		$x = $zip->open($newFilePath);  // open the zip file to extract
		if ($x === true)
                {
			$zip->extractTo($nueva_ruta_temporal."/"); // place in the directory with same name  
			$zip->close();
                        
                        //unlink($newFilePath);
                        
                        $rutas_archivos_a_usar = glob($nueva_ruta_temporal."/*");
                        foreach($rutas_archivos_a_usar as $ruta_archivo_rips)
                        {
                            $array_pre_ruta=explode("/",$ruta_archivo_rips);
                            $temp_nombre_archivo=$array_pre_ruta[count($array_pre_ruta)-1];
                            
                            if($string_archivos_subidos_exito!=""){$string_archivos_subidos_exito.="|";}
                            $string_archivos_subidos_exito.=explode(".",$temp_nombre_archivo)[0];
                        }
 
			
		}
            }//fin if se pudo subir
          }//fin if
        }//fin for
    }
    else if($_POST["tipo_rips"]=="eapb_rips_error")
    {
        
    }
    
    
    echo $string_tiempo_fecha."|".$string_archivos_subidos_exito;
}//fin if isset
?>