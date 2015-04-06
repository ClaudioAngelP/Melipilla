<?php

  require_once('../../conectar_db.php');
  
  $pac_id=$_POST['pac_id']*1;
  
  $c=cargar_registros_obj("
    SELECT DISTINCT patologias_auge.* FROM episodio_clinico
    JOIN patologias_auge ON ep_pat_id=pat_id
    LEFT JOIN patologias_auge_ramas ON ep_patrama_id=patrama_id
    WHERE ep_pac_id=$pac_id
    ORDER BY pat_glosa
  ", true);
  
  print(json_encode($c));

?>
