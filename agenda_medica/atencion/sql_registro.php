<?php

  require_once('../../conectar_db.php');
  
  $asigna_id=($_POST['asigna_id'])*1;
  $destino=isset($_POST['destino'])?($_POST['destino'])*1:4;
  $fecha=isset($_POST['fecha'])?pg_escape_string($_POST['fecha']):'';
  $hora=isset($_POST['horas'])?pg_escape_string($_POST['horas']):'';
  $asiste=($_POST['asiste']*1);
  
  pg_query("START TRANSACTION;");

  list($a)=cargar_registros_obj("
    SELECT *, cupos_fecha::date AS cupos_fecha FROM cupos_asigna 
    JOIN cupos_atencion USING (cupos_id)
    JOIN interconsulta USING (inter_id)
    JOIN pacientes ON inter_pac_id=pac_id
    JOIN especialidades ON esp_id=cupos_esp_id
    WHERE asigna_id=$asigna_id
  ");

  $inter_id=$a['inter_id'];
  $esp_id=$a['esp_id'];
  $pac_id=$a['pac_id'];
  $doc_id=$a['cupos_doc_id'];
  $control_id=$a['control_id']*1;
  
  if(!$asiste) $destino=4;
  
  pg_query("
    UPDATE cupos_asigna SET 
    asigna_destino=$destino, 
    asigna_asiste=$asiste
    WHERE asigna_id=$asigna_id  
  ");

  $chk=cargar_registros_obj("
            SELECT * FROM controles 
            WHERE inter_id=$inter_id AND control_id_anterior=$control_id
            ");

  if($chk) {
    
    pg_query("DELETE FROM cupos_asigna 
              WHERE inter_id=$inter_id AND control_id=".$chk[0]['control_id']);

    pg_query("DELETE FROM controles 
              WHERE inter_id=$inter_id AND control_id_anterior=$control_id");

  }
  
  if($asiste) {
    
  
  if($destino==1) { 
    pg_query("
      UPDATE interconsulta SET inter_estado=3 
      WHERE inter_id=$inter_id
    ");

    pg_query("
       INSERT INTO controles VALUES (
        DEFAULT, $inter_id, $esp_id, $pac_id,  
        '$fecha', $doc_id , $control_id
       );
    ");
  }

  if($destino==2)
    pg_query("
      UPDATE interconsulta SET inter_estado=4 
      WHERE inter_id=$inter_id
    ");
    
  if($destino==3)
    pg_query("
      UPDATE interconsulta SET inter_estado=5 
      WHERE inter_id=$inter_id
    ");
    
  }
  
  if($destino==1 AND $hora!='') {

    list($c)=cargar_registros_obj("
      SELECT * FROM cupos_atencion 
      WHERE cupos_doc_id=$doc_id AND cupos_fecha='$fecha' AND 
      '$hora' BETWEEN cupos_horainicio AND cupos_horafinal
    ");

    pg_query($conn,
    "
    INSERT INTO cupos_asigna VALUES (
    DEFAULT,
    $inter_id,
    ".$c['cupos_id'].",
    '$hora', CURRVAL('controles_control_id_seq'), $doc_id, $control_id
    )
    "); 
    
    list(list($asigna_id))=
        cargar_registros("SELECT CURRVAL('cupos_asigna_asigna_id_seq'); ");   
  
  }

  pg_query("COMMIT;");

  if($destino==1 AND $hora!='') {

?>


<script>

  var conf=confirm('¿Imprimir la citación generada?');
  
  if(!conf) window.close();
  
  window.open('../generar_documentos/citaciones.php?asigna_id=<?php echo $asigna_id; ?>',
              '_self');

</script>



<?php 

  } else {

?>

<script> 

  window.close(); 
  
</script>

<?php } ?>
