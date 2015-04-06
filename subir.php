<?php
    require_once('conectar_db.php');
    //Como no sabemos cuantos archivos van a llegar, iteramos la variable $_FILES
    $ruta= $_GET['ruta'];
    $id = $_GET['id'];
    $tabla = $_GET['tabla'];
    $respuesta = ''; 
    foreach ($_FILES as $key)
    { 
        if ($key["error"] > 0)
        {
            //echo "Error: " . $_FILES["archivo"]["error"] . "<br />";
            //print("<script>window.alert('Error al enviar el archivo.');window.close();</script>");
            $respuesta.=$fname." [ERR][".$key['error']."] \n";
        }
        else
        {
            //Verificamos si se subio correctamente
            $nombre = $key['name'];//Obtenemos el nombre del archivo
            $fname=$key["name"];
            $ftype=$key["type"];
            $fsize=$key["size"];
            $md5=md5_file($key["tmp_name"]);
            //$comprobar = cargar_registro("SELECT * FROM $tabla WHERE md5 = '$md5'");
            $comprobar = cargar_registro("SELECT * 
            FROM 
            convenio_adjuntos 
            WHERE 
            convenio_id = $id 
            AND 
            cad_adjunto = '$md5'
            ");
            
            if($comprobar['cad_id'] == '')
            {
                $sql = "INSERT INTO $tabla VALUES (DEFAULT,$id,".$_SESSION['sgh_usuario_id'].",CURRENT_TIMESTAMP,'$fname|$ftype|$fsize|$md5','$ruta')";
                $result = pg_query($conn,$sql) or die(pg_last_error());
                if($result == TRUE)
                {
                    if(move_uploaded_file($key["tmp_name"],$ruta.$md5))
                    {
                        $respuesta.=$fname." [OK] \n";
                    }
                    else
                    {
                        $respuesta.=$fname." [ERR][".$key['error']."] \n";
                    }	
                    //echo "<h12><strong>Archivo: $fname</strong></h2>";
                }
                else
                {
                    $respuesta.=$fname." [ERR][".$key['error']."] \n";
                }
            }
            else
            {
                $respuesta.=$fname." [El archivo ya existe en nuestros registros para este convenio.] \n";
            }	 
	}
    }
    echo $respuesta;
?>