<?php

  require_once('../../conectar_db.php');
  
  $ep_id=$_POST['ep_id']*1;
  
  $lista = cargar_registro("
    SELECT * FROM episodio_clinico 
    JOIN pacientes ON pac_id=ep_pac_id
    JOIN patologias_auge ON ep_pat_id=pat_id
    LEFT JOIN patologias_auge_ramas ON ep_patrama_id=patrama_id
    JOIN interconsulta ON ep_inter_id=inter_id
    LEFT JOIN detalle_patauge ON ep_detpat_id=detpat_id
    LEFT JOIN codigos_prestacion ON codigo=presta_codigo
    WHERE ep_id=$ep_id
  ",true);
  
  list($lista['ep_fecha_inicio'])=explode(' ', $lista['ep_fecha_inicio']);
  list($d,$m,$y)=explode('/', $lista['ep_fecha_inicio']);
  
  $lista['ep_fecha_inicio']=date('d/m/Y', mktime(0,0,0,$m,$d,$y));
  
  print(json_encode($lista));
  
?>
