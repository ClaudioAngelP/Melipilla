<?php

  require_once('../../conectar_db.php');
  
  $fecha = pg_escape_string($_POST['fecha1']);
  $centro_ruta = pg_escape_string($_POST['centro_ruta']);
  $pac_id = pg_escape_string($_POST['pac_id_0']);
  $prestacion = pg_escape_string($_POST['codigo_prestacion']);
  $diag_cod = pg_escape_string($_POST['diag_cod']);
  $cant=$_POST['cantidad']*1;
  $compra=(isset($_POST['compra']))?$compra='true':$compra='false';
  $auge=(isset($_POST['auge']))?$auge='true':$auge='false';
  
  if(isset($_POST['pat_id']))
    $pat_id=$_POST['pat_id']*1;
  else
    $pat_id=0;
  
  if(isset($_POST['garantia']))  
    $garantia=$_POST['garantia']*1;
  else
    $garantia=0;

  $hora=date('H:i:s');

  pg_query($conn,"
    INSERT INTO prestacion VALUES (
    DEFAULT,
    '$fecha $hora',
    $pac_id, 
    '$centro_ruta',
    '$prestacion',
    $cant,
    $compra,
    $auge, $pat_id, $garantia, 0, '$diag_cod'
    )
  ");
  
  if($auge) {
  
    // Actualiza información del episodio clínico...

    $ep=cargar_registros_obj("
          SELECT * FROM episodio_clinico  
          WHERE ep_pac_id=$pac_id AND ep_pat_id=$pat_id AND ep_estado<=1");

    $ep_id=$ep[0]['ep_id']; $etapa_actual=$ep[0]['ep_etapa']*1;
  
    $dpat=cargar_registros_obj("
      SELECT * FROM detalle_patauge 
        WHERE pat_id=$pat_id AND presta_codigo='$prestacion' 
        AND detpat_etapa>=$etapa_actual;
    ");
  
    $detpat_id=$dpat[0]['detpat_id']; $etapa=$dpat[0]['detpat_etapa'];
  
    if(!$dpat)
    pg_query("
          UPDATE episodio_clinico 
          SET ep_detpat_id=$detpat_id, ep_etapa=$etapa
          WHERE ep_id=$ep_id" 
          );
                    
  }

  print(json_encode(true));

?>
