<?php

  require_once('../../conectar_db.php');
  
  $pat_id=$_POST['pat_id']*1;

  if(!isset($_POST['eliminar'])) {
  
    $nombre_pat=pg_escape_string(utf8_decode($_POST['nombre_patologia']));
    $ingreso = $_POST['via']*1;
    $fecha1=pg_escape_string($_POST['fecha1']);
    $indef=isset($_POST['indef']);
    
    if(!$indef) {
      $fecha2="'".pg_escape_string($_POST['fecha2'])."'";
    } else {
      $fecha2='null';
    }
        
    if($pat_id==0) {
    
      pg_query($conn, "
        INSERT INTO patologias_auge VALUES (
        DEFAULT,
        '$nombre_pat',
        '$fecha1', $fecha2, $ingreso
        )
      ");
      
      $id = cargar_registro("SELECT CURRVAL('patologias_auge_pat_id_seq') AS id");
      
      exit(json_encode($id['id']));
    
    } else {
    
      pg_query($conn, "
        UPDATE patologias_auge SET
        pat_glosa='$nombre_pat',
        pat_fecha_inicio='$fecha1',
        pat_fecha_final=$fecha2,
        pat_ingreso=$ingreso
        WHERE pat_id=$pat_id
      ");
      
      exit(json_encode($pat_id));
    
    }
  
  } else {

      pg_query($conn, "
        DELETE FROM patologias_auge WHERE pat_id=$pat_id;
      ");
      
      pg_query($conn, "
        DELETE FROM detalle_patauge WHERE pat_id=$pat_id;
      ");
      
      exit(json_encode(true));
  
  }

?>
