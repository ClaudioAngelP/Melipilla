<?php 
    require_once('../../conectar_db.php');
    $nomd_id=$_POST['nomd_id']*1;
    $descripcion=pg_escape_string(utf8_decode($_POST['descripcion']));
    $nombre=$nomd_id.'_'.md5_file($_FILES['archivo']['tmp_name']);
    $error='';
    if(file_exists('../../ficha_clinica/adjuntos/'.$nombre)) {
        $error="Archivo id&eacute;ntico cargado previamente.<br>No se volver&aacute; a cargar.";
    }	
    if(!move_uploaded_file($_FILES['archivo']['tmp_name'], '../../ficha_clinica/adjuntos/'.$nombre)) {
        $error="Error al subir archivo.";						
    }
	
    if($error=='') {
        pg_query("INSERT INTO nomina_detalle_adjuntos VALUES (DEFAULT, $nomd_id, '$descripcion', '".pg_escape_string($nombre)."', 
        '".pg_escape_string($_FILES['archivo']['name'])."'	
        );");
    }
?>
<center>
<br /><br />
<?php 
    if($error!='') {
        echo '<img src="../../iconos/error.png" width=64 height=64 /><br />'.$error;
    } else { 
        echo '<img src="../../iconos/tick.png" width=64 height=64 /><br />Archivo cargado exitosamente.';
    }
?>
</center>
<script> 
    window.opener.listar_adjuntos();	
    setTimeout('window.close();',1500); 
</script>