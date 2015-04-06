<?php
    require_once('../../conectar_db.php');

        $id_usuario=$_SESSION['sgh_usuario_id'];
        $clave_actual = pg_escape_string ($_GET['txt_clave_ant']);
        $clave_nueva = pg_escape_string ($_GET['txt_clave_nuev']);
        
        $clave = cargar_registro("SELECT func_clave FROM funcionario WHERE func_id=".$id_usuario.";");

        if(md5($clave_actual)==$clave['func_clave']){
			
            pg_query("UPDATE funcionario SET func_clave='".md5($clave_nueva)."' where func_id='$id_usuario'");
            print(1);      
        }else{
            print(0);
        }
        
?>
