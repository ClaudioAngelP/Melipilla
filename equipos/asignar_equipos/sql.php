<?php

  require_once('../../conectar_db.php');
  
  $eot_id=$_POST['eot_id']*1;
  
  $tec_id=0;
  
  $t = cargar_registros_obj("
    SELECT * FROM tecnico ORDER BY tec_nombre
  ");

    function asociar_tecnicos() { 
    
        GLOBAL $t, $eot_id;
        
        pg_query("DELETE FROM equipo_orden_tecnico WHERE eot_id=$eot_id");
        
        for($i=0;$i<count($t);$i++) {
        
            if(isset($_POST['t_'.($t[$i]['tec_id'])])) { 
            
                pg_query("INSERT INTO equipo_orden_tecnico VALUES (default, $eot_id, ".$t[$i]['tec_id'].");");
            
            }
        
        }

    
    }

  if($eot_id==-1) {
  
    $eagenda_id=$_POST['eagenda_id']*1;
    
    list($e)=cargar_registros_obj("
      SELECT * FROM equipo_agenda_preventiva 
      WHERE eagenda_id=".$eagenda_id);
      
    $equipo_id=$e['equipo_id'];
  
    pg_query("
    INSERT INTO equipo_orden_trabajo VALUES (
    DEFAULT,
    '".$e['eagenda_fecha']."',
    '', $equipo_id,
    current_timestamp, null, null, null, null, $tec_id, 0,
    -1, 1, ".($_SESSION['sgh_usuario_id']*1)."
    );
    ");
    
    pg_query("UPDATE equipos_medicos SET equipo_estado=2 WHERE equipo_id=".$equipo_id);
    
    list($eot) = cargar_registros_obj("SELECT CURRVAL('equipo_orden_trabajo_eot_id_seq') AS id;");

    pg_query("UPDATE equipo_agenda_preventiva SET eot_id=".$eot['id'].' WHERE eagenda_id='.$eagenda_id);
    
    $eot_id=$eot['id'];
    
    asociar_tecnicos();
    
    exit($eot_id);
   
  } else {
  
    pg_query("
      UPDATE equipo_orden_trabajo SET
      eot_tec_id=$tec_id,
      eot_estado=0,
      eot_func_id_asigna=".$_SESSION['sgh_usuario_id'].",
      eot_fecha_asigna=now()
      WHERE eot_id=$eot_id;
    ");
    
    asociar_tecnicos();
    
    exit('0');
    
  }


?>
