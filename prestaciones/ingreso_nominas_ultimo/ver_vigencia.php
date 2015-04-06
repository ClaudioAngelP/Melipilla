<?php
    require_once('../../conectar_db.php');
    $id=$_GET['recetad_id'];
    //print($_GET['recetad_id']);
    $det=cargar_registro("SELECT art_glosa,recetad_dias,receta_fecha_emision::DATE AS fecha_ini,
    ((receta_fecha_emision)+(recetad_dias||' days')::interval)::date AS fecha_fin,
    upper(doc_nombres||' '||doc_paterno||' '||doc_materno) AS doc_nombre
    FROM recetas_detalle 
    LEFT JOIN articulo ON art_id=recetad_art_id
    LEFT JOIN logs ON log_recetad_id=recetad_id
    LEFT JOIN receta ON receta_id=recetad_receta_id
    LEFT JOIN doctores ON doc_id=receta_doc_id
    WHERE recetad_id=".$id);
    $art=$det['art_glosa'];
    $fecha_ini=$det['fecha_ini'];
    $fecha_fin=$det['fecha_fin'];
    $doc=htmlentities($det['doc_nombre']);
?>
<html>
    <title>Vigencia de la Prescrici&oacute;n</title>
        <?php cabecera_popup('../..'); ?> 
    <body class='fuente_por_defecto popup_background'>
        <center>
            <br>
            <h2><u><?php print($art);?></u></h2>
            <br>
            <br>
            <div class='sub-content2'>
                <table style='width:100%;'>
                    <tr class='tabla_header'>
                        <td>M&eacute;dico</td>
                        <td>Fecha Inicio</td>
                        <td>Fecha T&eacute;rmino</td>
                    </tr>
                    <?php 
                    print("<tr>
                    <td>$doc</td>
                    <td style='text-align:center;'>$fecha_ini</td>
                    <td style='text-align:center;'>$fecha_fin</td>
                    </tr>");
                    ?>
                </table>
            </div>
        </center>
    </body>
</html>