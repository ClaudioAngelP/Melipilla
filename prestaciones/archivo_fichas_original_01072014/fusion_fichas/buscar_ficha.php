<?php
    require_once("../../../conectar_db.php");
    $ficha=trim(pg_escape_string($_POST['ficha']));
    //--------------------------------------------------------------------------
    if($ficha=='')
        exit('');
    
    if(strstr($ficha,'-'))
        $tmp=cargar_registros_obj("SELECT * FROM pacientes LEFT JOIN comunas USING (ciud_id) LEFT JOIN prevision USING (prev_id) LEFT JOIN sexo USING (sex_id) WHERE pac_rut='$ficha' LIMIT 1");
    else
        $tmp=cargar_registros_obj("SELECT * FROM pacientes LEFT JOIN comunas USING (ciud_id) LEFT JOIN prevision USING (prev_id) LEFT JOIN sexo USING (sex_id) WHERE pac_ficha='$ficha' LIMIT 1");
    if(!$tmp)
    {
        //$pac_id=$tmp[0]['pac_id']*1;
        //$pac_ficha=pg_escape_string($tmp[0]['pac_ficha']);
        $tmp=false;
    }
    echo json_encode(array($tmp));
?>