<?php 
    require_once('../../conectar_db.php');
    $art_id=$_POST['art_id']*1;
    $art_glosa=pg_escape_string(utf8_decode($_POST['art_glosa']));
    $art_via=pg_escape_string(utf8_decode($_POST['art_via']));
    $art_indicacion=pg_escape_string(utf8_decode($_POST['art_indicacion']));
    $art_clasifica_id=$_POST['art_clasifica_id']*1;
    $art_forma=$_POST['art_forma']*1;
    $art_control=$_POST['art_control']*1;
    $art_arsenal=isset($_POST['art_arsenal'])?'true':'false';
    pg_query("UPDATE articulo SET art_glosa='$art_glosa',
        art_via='$art_via',
        art_indicacion='$art_indicacion',
	art_clasifica_id=$art_clasifica_id,
	art_forma=$art_forma,
	art_control=$art_control,
	art_arsenal=$art_arsenal
	WHERE art_id=$art_id;");

    $g=cargar_registros_obj("SELECT *, (SELECT autfd_id FROM autorizacion_farmacos_detalle WHERE autorizacion_farmacos_detalle.autf_id=autorizacion_farmacos.autf_id AND autorizacion_farmacos_detalle.art_id=$art_id LIMIT 1) AS chk FROM autorizacion_farmacos ORDER BY autf_id=1 DESC,autf_nombre;", true);
    if($g)
        for($i=0;$i<sizeof($g);$i++) {
            if(isset($_POST['autf_'.$g[$i]['autf_id']])) {
                if($g[$i]['chk']=='') {
                    pg_query("INSERT INTO autorizacion_farmacos_detalle VALUES (DEFAULT, ".$g[$i]['autf_id'].", $art_id, '');");
		}
            } else {
                if($g[$i]['chk']!='') {
                    pg_query("DELETE FROM autorizacion_farmacos_detalle WHERE autf_id=".$g[$i]['autf_id']." AND art_id=$art_id;");
                }
            }
        }
    
    print("PRODUCTO MODIFICADO EXITOSAMENTE.");
?>