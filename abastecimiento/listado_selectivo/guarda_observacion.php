<?php
    require_once('../../conectar_db.php');
    //COMPRUEBA SI LA COMPARACION YA FUE REALIZADA
    $lsd_id=$_POST['lsd_id']*1;
    $comentario=$_POST['comentario'];
    if($lsd_id!='' or $lsd_id!=null)
    {
        pg_query("UPDATE listado_selectivo_detalle SET lsd_comentario='$comentario' WHERE lsd_id=$lsd_id");
    }			
    print(json_encode(true));		
?>