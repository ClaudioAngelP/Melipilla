<?php

  require_once('../../conectar_db.php');

  $id=$_POST['equipo_id']*1;
  $eclase_id=pg_escape_string(utf8_decode($_POST['eclase_id']));
  $marca=pg_escape_string(utf8_decode($_POST['marca']));
  $modelo=pg_escape_string(utf8_decode($_POST['modelo']));
  $serie=pg_escape_string($_POST['serie']);
  $inventario=pg_escape_string($_POST['inventario']);
  $centro_ruta1=pg_escape_string($_POST['centro_ruta1']);
  $centro_ruta2=pg_escape_string($_POST['centro_ruta2']);
  $nuevo=isset($_POST['equipo_nuevo'])?'true':'false';
  $tiempo=($_POST['tiempo'])*1;
  $medida=($_POST['medida'])*1;
  $preventiva=($_POST['preventiva'])*1;
  $mant_prov=($_POST['equipo_mant_prov']*1);
  $accesorios=pg_escape_string(trim(utf8_decode($_POST['equipo_accesorios'])));
  $foto=pg_escape_string(utf8_decode($_POST['nombre_foto']));
  $prov_id=$_POST['prov_id']*1;
  
  
  $vida_estandar=$_POST['vida_estandar']*1;
  $vida_extendida=$_POST['vida_extendida']*1;
  $fecha_fabricacion=pg_escape_string($_POST['fecha_fabricacion']);
  $fecha_mantprev=pg_escape_string($_POST['fecha_mantprev']);

  $comentarios=pg_escape_string($_POST['comentarios']);

  if($id!=0 AND isset($_POST['eliminar'])) {

     pg_query("DELETE FROM equipos_medicos WHERE equipo_id=$id");
     
     exit(json_encode(true));
  
  }
  
  
  if($id==0) {
    pg_query("
    INSERT INTO equipos_medicos VALUES (
    default,
    $eclase_id,
    '$marca',
    '$modelo',
    '$serie',
    '$inventario',
    '$centro_ruta1',
    $tiempo, $medida,
    $preventiva, '$foto',
    0, now(), $prov_id, '$centro_ruta2', 
    $vida_estandar, $vida_extendida, '', '$fecha_fabricacion', 
    $nuevo, $mant_prov,'$accesorios', '$fecha_mantprev', '$comentarios'
    );
    ");
    
    list($eid)=cargar_registros_obj("SELECT CURRVAL('equipos_medicos_equipo_id_seq') AS id;");
    $id=$eid['id'];
    
  } else {
    pg_query("
    UPDATE equipos_medicos SET
    equipo_eclase_id=$eclase_id,
    equipo_marca='$marca',
    equipo_modelo='$modelo',
    equipo_serie='$serie',
    equipo_inventario='$inventario',
    equipo_centro_ruta='$centro_ruta1',
    equipo_garantia=$tiempo, equipo_garantia_medida=$medida,
    equipo_preventiva=$preventiva, equipo_foto='$foto',
    equipo_prov_id=$prov_id, equipo_centro_ruta2='$centro_ruta2',
    equipo_vida_estandar=$vida_estandar, equipo_vida_extendida=$vida_extendida,
    equipo_fecha_fabricacion='$fecha_fabricacion',
    equipo_nuevo=$nuevo, equipo_mant_prov=$mant_prov, 
    equipo_accesorios='$accesorios',
    equipo_fecha_mantprev='$fecha_mantprev',
    equipo_comentarios='$comentarios'
    WHERE equipo_id=$id
    ;
    ");
  
  }
  
  list($equipo) = cargar_registros_obj("
      SELECT 
      *, 
      equipo_fecha_ingreso::date AS equipo_fecha_ingreso, 
      extract(
        day from ((equipo_fecha_fabricacion + (equipo_vida_estandar || ' year')::interval)::date + (equipo_vida_extendida || ' year')::interval)::date - now()       
      ) AS vida_residual 
      
      FROM equipos_medicos 
      JOIN equipo_medico_clase ON equipo_eclase_id=eclase_id
      WHERE equipo_id=$id
  ");

  $fing=explode('/',$equipo['equipo_fecha_mantprev']);
  
  // timestamp ingreso
  $fi=mktime(0,0,0,$fing[1],$fing[0],$fing[2]);
  // timestamp termino vida util
  $fr=mktime(0,0,0,$fing[1],$fing[0]+$equipo['vida_residual'],$fing[2]);

  switch($equipo['equipo_preventiva']) {
      case 0: $intervalo=30; break;
      case 1: $intervalo=60; break;
      case 2: $intervalo=90; break;
      case 3: $intervalo=183; break;
      case 4: $intervalo=365; break;
  }  

  pg_query("DELETE FROM equipo_agenda_preventiva WHERE equipo_id=$id AND eot_id IS NULL;");

  $c=1;

  while(1) {
    $f=mktime(0,0,0,$fing[1],$fing[0]+($intervalo*$c),$fing[2]);

    if($f>=$fr) break;
    
    $fecha=date('d/m/Y',$f);    
    pg_query("INSERT INTO equipo_agenda_preventiva VALUES (
      DEFAULT, $id, '$fecha', NULL)
    ");
    
    $c++;
  }

  $car=cargar_registros_obj("
    SELECT 
    equipo_caracteristicas_tecnicas.*, 
    ctec_valor, ctec_id
    FROM equipo_caracteristicas_tecnicas
    LEFT JOIN equipos_medicos_ctec 
      ON  equipos_medicos_ctec.ecar_id=equipo_caracteristicas_tecnicas.ecar_id
      AND equipos_medicos_ctec.equipo_id=$id
    ORDER BY ecar_nombre;
    ");

  if($car)
  for($i=0;$i<count($car);$i++) {
  
    if(isset($_POST[('ecar_'.$car[$i]['ecar_id'])]) && 
        trim($_POST[('ecar_'.$car[$i]['ecar_id'])])!='') {
    
      $val=pg_escape_string(trim($_POST[('ecar_'.$car[$i]['ecar_id'])]));
    
      if($car[$i]['ecar_tipo']==2) 
        $val='t';
      
      if($car[$i]['ctec_id']=='')
        pg_query("INSERT INTO equipos_medicos_ctec VALUES (
          DEFAULT, ".$car[$i]['ecar_id'].", '$val', $id 
        )");
      else
        pg_query("UPDATE equipos_medicos_ctec SET
          ctec_valor='$val' WHERE ctec_id=".$car[$i]['ctec_id']);
    
    } else
      pg_query("DELETE FROM equipos_medicos_ctec 
                WHERE ecar_id=".$car[$i]['ecar_id']." 
                AND equipo_id=".$id);
     
  }  
  
  
  
  echo json_encode(true);
  
?>