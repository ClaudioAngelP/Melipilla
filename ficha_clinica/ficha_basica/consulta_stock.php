<?php
    require_once('../../conectar_db.php'); 
    $str=pg_escape_string(utf8_decode($_POST['str'])); 
    $bods=cargar_registros_obj("SELECT bod_id,bod_glosa FROM bodega WHERE bod_despacho OR bod_id=1 ORDER BY bod_glosa;");
    for($b=0;$b<sizeof($bods);$b++)
    {
        if(isset($_POST['chk_'.$b]))
        {
            $bod=cargar_registro("SELECT bod_id,bod_glosa FROM bodega WHERE bod_id=".$_POST['chk_'.$b].";");
            $b_titulos.="<td>".htmlentities($bod['bod_glosa'])."</td>";
            $b_stock.="COALESCE(calcular_stock(art_id, ".$bod['bod_id']."),0) AS stock_".$bod['bod_id'].",";
	}
    }
?>
<table style='width:100%;'>
    <tr class='tabla_header'>
        <td style='width:10%;'>C&oacute;digo</td>
        <td style='width:60%;'>Descripci&oacute;n</td>
        <?php echo $b_titulos; ?>
        <td>TOTAL</td>
    </tr>
    <?php 
    $s=cargar_registros_obj("SELECT *, ".trim($b_stock,",")." FROM (
    SELECT art_id, art_codigo, art_glosa FROM articulo
    LEFT JOIN articulo_bodega ON artb_art_id=art_id AND artb_bod_id=3
    WHERE art_codigo ILIKE '%$str%' OR art_glosa ILIKE '%$str%'
    LIMIT 10
    ) AS foo ORDER BY art_glosa;", true);
    if($s)
        for($i=0;$i<sizeof($s);$i++)
        {
            $clase=($i%2==0)?'tabla_fila':'tabla_fila2';
            print("<tr class='$clase' style='cursor:pointer;' onMouseOver='this.className=\"mouse_over\"' onMouseOut='this.className=\"$clase\";'>
            <td style='text-align:right;'>".$s[$i]['art_codigo']."</td>
            <td style='text-align:left;font-size:12px;'>".$s[$i]['art_glosa']."</td>
            <!--<td style='text-align:right;font-weight:bold;'>".number_format($s[$i]['stock'],0,',','.')."</td>-->");
            $stotal=0;
            for($b=0;$b<sizeof($bods);$b++)
            {
                if(isset($_POST['chk_'.$b]))
                {
                    print("<td style='text-align:right;font-weight:bold;'>".number_format($s[$i]['stock_'.$_POST['chk_'.$b]],0,',','.')."</td>");
                    $stotal+=$s[$i]['stock_'.$_POST['chk_'.$b]];
                }
            }
            print("<td style='text-align:right;font-weight:bold;'>".number_format($stotal,0,',','.')."</td>");
            print("</tr>");
        }
    ?>
</table>