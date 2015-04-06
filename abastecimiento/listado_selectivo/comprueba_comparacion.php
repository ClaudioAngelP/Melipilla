<?php
    require_once('../../conectar_db.php');
    //COMPRUEBA SI LA COMPARACION YA FUE REALIZADA
    $ls_id=$_POST['correlativo'];
    $listado_existe=cargar_registro("SELECT ls_estado FROM listado_selectivo where ls_id=$ls_id");
    if($listado_existe['ls_estado']=='1')
    {
        print(json_encode(true));		
    }
    else
    {
        print(json_encode(false));
    }
?>
