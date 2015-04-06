<?php

    require_once('../../conectar_db.php');
    $bod_id=$_POST['bodega_id2'];
    $pac_rut=$_POST['paciente_rut2'];
    //$fecha1=pg_escape_string($_POST['fecha1']);
    //$fecha2=pg_escape_string($_POST['fecha2']);
  
    if(strstr($bod_id,'.'))
    {
        //$tabla_stock='stock_servicios';
        $ubica="stock_centro_ruta='".pg_escape_string($bod_id)."'";
    }
    else
    {
        //$tabla_stock='stock';
        $ubica="stock_bod_id=".($bod_id*1);
    }
  
    //$fecha="AND (log_fecha BETWEEN '$fecha1 00:00:00' AND '$fecha2 23:59:59')";
  
    //if($pac_id!=0)
    //    $pac=' AND cargo_hoja.pac_id='.($pac_id*1);
    //else
    //    $pac='';
    
    $lista = cargar_registros_obj("
        select * from cargo_hoja
        join pacientes on cargo_hoja.pac_id=pacientes.pac_id
        join stock on log_id=stock_log_id
        join articulo on stock_art_id=art_id
        join bodega_forma on art_forma=forma_id
        join logs on cargo_hoja.log_id=logs.log_id
        where pac_rut='$pac_rut'
        and $ubica
        and logs.log_tipo=17
        order by log_fecha
        ");

if($lista)
{
    $neto=0;
    $total=0;
    $iva=0;
    print('<table style="width:100%;">
    <tr class="tabla_header">
    <td>Fecha</td>
    <td>Codigo</td>
    <td>Glosa</td>
    <td>Cant</td>
    <td>P/Unit</td>
    <td>Subtotal</td>
    </tr>');
    for($i=0;$i<count($lista);$i++)
    {
        if($i%2==0) $clase='tabla_fila'; else $clase='tabla_fila2';
        print('<tr class="'.$clase.'">');
        print('<td style="text-align:center;"><font size=1>'.$lista[$i]['log_fecha'].'</font></td>');
        print('<td style="text-align:center;"><font size=1>'.$lista[$i]['art_codigo'].'</font></td>');
        print('<td style="text-align:left;"><font size=1>'.$lista[$i]['art_glosa'].'</font></td>');
        //print('<td style="text-align:left;"><font size=1>'.$lista[$i]['forma_nombre'].'</font></td>');
        print('<td style="text-align:center;">'.number_format(-($lista[$i]['stock_cant']*1),2,',','.').'</td>');
        print('<td style="text-align:center;">$'.number_format(($lista[$i]['art_val_ult'])*1,2,',','.').'</td>');
        $subtotal=($lista[$i]['art_val_ult']*1)*(-($lista[$i]['stock_cant'])*1);
        print('<td style="text-align:center;">$'.number_format($subtotal,2,',','.').'</td>');
        $neto=$neto+$subtotal;
    }
    Print('</tr>');
    print('<tr class="tabla_header" style="font-weight:bold;">');
    print('<td colspan=5 style="text-align:right;">Neto:</td>');
    print('<td style="text-align:right;">$'.number_format($neto,2,',','.').'.-</td></tr>');
    print('<tr class="tabla_header" style="font-weight:bold;">');
    $total=$neto*1.19;
    $iva=$total-$neto;
    print('<td colspan=5 style="text-align:right;">I.V.A.:</td>');
    print('<td style="text-align:right;">$'.number_format($iva,2,',','.').'.-</td></tr>');
    print('<tr class="tabla_header" style="font-weight:bold;">');
    print('<td colspan=5 style="text-align:right;">Total:</td>');
    print('<td style="text-align:right;">$'.number_format($total,2,',','.').'.-</td></tr>');


    

}
print('</table>');

?>


