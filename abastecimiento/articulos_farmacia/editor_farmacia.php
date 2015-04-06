<?php
    require_once('../../conectar_db.php');
    $art_id=$_POST['art_id']*1;
    $a=cargar_registro("SELECT * FROM articulo LEFT JOIN bodega_forma ON art_forma=forma_id WHERE art_id=$art_id", true);
    $clasificahtml = desplegar_opciones("bodega_clasificacion","clasifica_id, clasifica_nombre",$a['art_clasifica_id'],'true','ORDER BY clasifica_nombre');
    $formahtml = desplegar_opciones("bodega_forma","forma_id, forma_nombre",$a['art_forma'],'true','ORDER BY forma_nombre');
    $controlhtml = desplegar_opciones("receta_tipo_talonario", "tipotalonario_id, tipotalonario_medicamento_clase",$a['art_control'],'true','ORDER BY tipotalonario_id');
?>
<form id='datos_art' name='datos_art' onSubmit='return false;'>
    <input type='hidden' id='art_id' name='art_id' value='<?php echo $art_id; ?>' />
    <center>
        <table style='width:100%;font-size:16px;'>
            <tr>
                <td style='text-align:right;width:30%;' class='tabla_fila2'>C&oacute;digo:</td>
                <td style='font-size:24px;'><?php echo $a['art_codigo']; ?></td>
            </tr>
            <tr>
                <td style='text-align:right;width:30%;' class='tabla_fila2'>Descripci&oacute;n:</td>
                <td><input type='text' id='art_glosa' name='art_glosa' value='<?php echo $a['art_glosa']; ?>' size=60 style='font-size:18px;' /></td>
            </tr>
            <?php
            if(($_SESSION['sgh_usuario_id']*1)==7)
                $visible="";
            else
                $visible="none";
            ?>
            <tr style="display: <?php echo $visible;?>">
                <td style='text-align:right;width:30%;' class='tabla_fila2'>
                    Vademecum&copy; (VMP):
                </td>
                <td>
                    <input type='text' id='id_vademecum' name='id_vademecum' value='<?php echo $a['id_vademecum']; ?>' size=20 style='text-align:center;font-weight:bold;color:blue;' READONLY />
                    <img src='iconos/layout.png' onClick='ver_vademecum();' style='cursor:pointer;' />
                    <img src='iconos/magnifier.png' onClick='asignar_vademecum();' style='cursor:pointer;' />
                </td>
            </tr>
            <tr>
                <td style='text-align:right;width:30%;' class='tabla_fila2'>Forma:</td>
                <td><select id='art_forma' name='art_forma' style='width:350px;font-size:16px;'>
                    <?php echo $formahtml; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td style='text-align:right;width:30%;' class='tabla_fila2'>Clasificaci&oacute;n:</td>
                <td><select id='art_clasifica_id' name='art_clasifica_id' style='width:350px;font-size:16px;'>
                    <?php echo $clasificahtml; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td style='text-align:right;width:30%;' class='tabla_fila2'>Controlado:</td>
                <td>
                    <select id='art_control' name='art_control' style='font-size:18px;'>
                        <option value='0'>No</option>
                            <?php echo $controlhtml; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td style='text-align:right;' class='tabla_fila2'>Multidosis (Cantidad/Ud. Adm.):</td>
                <td>
                    <?php if(($a['art_unidad_cantidad']*1)==0) $multi_cantidad=""; else $multi_cantidad=$a['art_unidad_cantidad']*1; ?>
                    <input type='text' id='art_unidad_cantidad' name='art_unidad_cantidad' value='<?php echo $multi_cantidad; ?>' size=10 style='text-align:right;' />
                    <input type='text' id='art_unidad_adm' name='art_unidad_adm' value='<?php echo $a['art_unidad_adm']; ?>' size=20 style='text-align:left;' />
                </td>
            </tr>
            <tr>
                <td style='text-align:right;' class='tabla_fila2'>Visible en Receta:</td>
                <td>
                    <input type='checkbox' id='art_arsenal' name='art_arsenal' <?php echo ($a['art_arsenal']=='t'?'CHECKED':''); ?> />
                </td>
            </tr>
            <tr>
                <td style='text-align:right;' class='tabla_fila2'>V&iacute;a Administraci&oacute;n:</td>
                <td><input type='text' id='art_via' name='art_via' value='<?php echo $a['art_via']; ?>' size=40 style='font-size:16px;' /></td>
            </tr>
            <tr>
                <td style='text-align:right;' class='tabla_fila2'>Indicaciones:</td>
                <td><textarea id='art_indicacion' name='art_indicacion' style='width:350px;height:100px;font-size:16px;'><?php echo $a['art_indicacion']; ?></textarea></td>
            </tr>
            <tr>
                <td style='text-align:right;' class='tabla_fila2'>Grupos:</td>
                <td>
                    <div id='grupos' style='width:75%;height:200px;overflow:auto;border:1px solid black;'>
                        <table style='width:100%;'>
                        <?php
                            $g=cargar_registros_obj("SELECT *, (
                            SELECT autfd_id FROM autorizacion_farmacos_detalle 
                            WHERE autorizacion_farmacos_detalle.autf_id=autorizacion_farmacos.autf_id 
                            AND autorizacion_farmacos_detalle.art_id=$art_id LIMIT 1
                            ) AS chk 
                            FROM autorizacion_farmacos 
                            ORDER BY autf_id=1 DESC,autf_nombre;", true);
                            if($g)
                                for($i=0;$i<sizeof($g);$i++) {
                                    $clase=($i%2==0)?'tabla_fila':'tabla_fila2';
                                    $chk=$g[$i]['chk']!=''?'CHECKED':'';
                                    if($i==0) $stl='font-weight:bold;color:green;'; else $stl='';
                                    print("<tr class='$clase'><td><input type='checkbox' id='autf_".$g[$i]['autf_id']."' name='autf_".$g[$i]['autf_id']."' $chk /><td style='$stl'>".$g[$i]['autf_nombre']."</td></tr>");
                                }
                        ?>
                        </table>
                    </div>
                </td>
            </tr>
        </table>
    </center>
</form>
<script> $('guardar').show(); </script>