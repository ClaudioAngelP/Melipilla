<?php
    require_once('../../conectar_db.php');
    $bod_id = $_POST['bodega_id']*1;
    $arts = json_decode($_POST['arts']);
    $comentarios = pg_escape_string($_POST['comentarios']);
    $tipo_mov = $_POST['tipo_mov']*1;
    if($tipo_mov==3)
    {
        $prov_id=$_POST['prov_id']*1;  
	$nombre_responsable=$_POST['nombre_responsable'];
	$rut_responsable=$_POST['rut_responsable'];
    }
    else
    {
        $prov_id='null';
  	$nombre_responsable='';
  	$rut_responsable='';
    }
    $tipo_mov+=30;  
    pg_query("START TRANSACTION");
    pg_query("INSERT INTO logs VALUES (DEFAULT, ".($_SESSION['sgh_usuario_id']*1).", $tipo_mov, now(), 0, 0, 0, '$comentarios')");
    $log = "CURRVAL('logs_log_id_seq')";
    pg_query("INSERT INTO devolucion_proveedor VALUES (DEFAULT,$log,$prov_id,'$nombre_responsable','$rut_responsable');");	
    $num=0;
    for($i=0;$i<count($arts);$i++)
    {
        $lotes = $arts[$i]->lotes;
        $art_id = $arts[$i]->id;
        for($n=0;$n<count($lotes);$n++)
        {
            $lote = $lotes[$n];
            if($lote[2]!=null)
                $fec = "'".$lote[2]."'";
            else
                $fec = 'null';
      
            if($_POST['tipo_mov']*1==3)
            {
                $dif = -($lote[1]);
            }
            else
            {
                $dif = -($lote[0]-$lote[1]);
            }
            
            if($dif!=0)
            {
                pg_query("INSERT INTO stock VALUES (DEFAULT, $art_id, $bod_id, $dif, $log, $fec, 0 )");
                $num++;
            }
        }
    }
    list(list($log_id)) = cargar_registros("SELECT CURRVAL('logs_log_id_seq');",false);
    pg_query("COMMIT;");
    print(json_encode($log_id));
?>