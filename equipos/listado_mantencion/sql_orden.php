<?php

  require_once('../../conectar_db.php');
  
  $eot_id=$_POST['eot_id']*1;
  $orden_id=$_POST['orden_id']*1;
    $accion=$_POST['accion'];

    if($accion=='agregar') {
    
        pg_query("INSERT INTO equipo_orden_compra VALUES (default, $eot_id, $orden_id)");
        
        print('<script>var fn=window.opener.listar_oc.bind(window.opener); fn(); window.close(); </script>');
        
        exit(0);
    
    }
    
    if($accion=='eliminar') {
    
        pg_query("DELETE FROM equipo_orden_compra WHERE eot_id=$eot_id AND orden_id=$orden_id");
    
    }


?>
