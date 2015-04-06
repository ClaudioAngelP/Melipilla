<?php
    require_once('../../conectar_db.php');
    $cad_id = $_GET['adjunto_id'];
    pg_query($conn, 'START TRANSACTION;');
    $ruta = cargar_registro("SELECT * FROM convenio_adjuntos WHERE cad_id = $cad_id");
    list($nombre,$tipo,$peso,$md5)=explode('|',$ruta['cad_adjunto']);
    if($ruta){
        $result = pg_query($conn,"DELETE FROM convenio_adjuntos WHERE cad_id = $cad_id");
	unlink("/var/www/produccion/administracion/convenios/adjuntos_convenio/".$md5);
    }
    pg_query($conn, 'COMMIT;');
?>