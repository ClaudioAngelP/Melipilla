<?php
    require_once('../../conectar_db.php');
    $ordenc=0;
    if(isset($_POST['ordentxt']))
    {
        if($_POST['ordentxt']!='')
        {
            $nro_orden=pg_escape_string($_POST['ordentxt']);
            $nro_orden=str_replace('*', '%', $nro_orden);
            $ordenc=cargar_registros_obj("SELECT orden_id,COALESCE(orden_numero, orden_id::text) AS orden_numero,
            date_trunc('second', orden_fecha) AS orden_fecha, prov_rut, prov_glosa, array(SELECT pedido_nro FROM orden_pedido
            JOIN pedido ON orden_pedido.pedido_id=pedido.pedido_id WHERE orden_pedido.orden_id=orden_compra.orden_id) AS pedido_nro,
            orden_estado FROM orden_compra JOIN proveedor ON prov_id=orden_prov_id where orden_numero='$nro_orden'",false);
            if(!$ordenc)
            {
                $nro_orden2=$nro_orden*1;
                $ordenc=cargar_registros_obj("SELECT orden_id,COALESCE(orden_numero, orden_id::text) AS orden_numero,
                date_trunc('second', orden_fecha) AS orden_fecha, prov_rut, prov_glosa, array(SELECT pedido_nro FROM orden_pedido
                JOIN pedido ON orden_pedido.pedido_id=pedido.pedido_id WHERE orden_pedido.orden_id=orden_compra.orden_id) AS pedido_nro,
                orden_estado FROM orden_compra JOIN proveedor ON prov_id=orden_prov_id where orden_id=$nro_orden2",false);
                if(!$ordenc)
                    $ordenc=0;
            }
        }
    }
    if(isset($_POST['orden_prov_id']))
    {
        if($_POST['orden_prov_id']!='')
        {
            $prov_id=pg_escape_string($_POST['orden_prov_id']);
            $prov_id=str_replace('*', '%', $prov_id);
            $ordenc=cargar_registros_obj("SELECT orden_id,COALESCE(orden_numero, orden_id::text) AS orden_numero,
            date_trunc('second', orden_fecha) AS orden_fecha, prov_rut, prov_glosa, array(SELECT pedido_nro FROM orden_pedido
            JOIN pedido ON orden_pedido.pedido_id=pedido.pedido_id WHERE orden_pedido.orden_id=orden_compra.orden_id) AS pedido_nro,
            orden_estado FROM orden_compra JOIN proveedor ON prov_id=orden_prov_id where prov_id='$prov_id'",false);
        }
    }
    if($ordenc==0)
    {
        if($nro_orden!='')
        {
            if(file_exists('../../conectores/mercadopublico/xml/Orden_'.$nro_orden.'.xml'))
            {
                print('<div class=sub-content style="text-align:center;"><b>ATENCI&Oacute;N</b>: SE ENCONTR&Oacute; XML QUE NO FU&Eacute; CARGADO.</div>');
            }
            else
            {
                ob_start();
                $_GET['orden_numero']=strtoupper(trim($nro_orden));
                require_once('../../conectores/mercadopublico/mercadopublico.php');
                ob_end_clean();
                $ordenc=cargar_registros_obj("SELECT orden_id,COALESCE(orden_numero, orden_id::text) AS orden_numero,
                date_trunc('second', orden_fecha) AS orden_fecha, prov_rut, prov_glosa, array(SELECT pedido_nro FROM orden_pedido
                JOIN pedido ON orden_pedido.pedido_id=pedido.pedido_id WHERE orden_pedido.orden_id=orden_compra.orden_id) AS pedido_nro,
                orden_estado FROM orden_compra JOIN proveedor ON prov_id=orden_prov_id where orden_numero='$nro_orden'",false);
                if($ordenc==0 AND file_exists('../../conectores/mercadopublico/xml/Orden_'.$nro_orden.'.xml'))
                {
                    print('<div class=sub-content style="text-align:center;"><b>ATENCI&Oacute;N</b>: SE ENCONTR&Oacute; XML QUE NO FU&Eacute; CARGADO.</div>');
                }
            }
        }
        if($ordenc==0)
        {
            print('<div class=sub-content style="text-align:center;">EL NUMERO DE ORDEN DE COMPRA INGRESADO NO EXISTE</div>');
            exit();
        }
    }
    
    print('<table style="width:100%"><tr class="tabla_header"><td>Nro. Interno</td>
    <td>C&oacute;digo &Oacute;rden</td>
    <td>Fecha Ingreso</td>
    <td>RUT Proveedor</td>
    <td>Nombre Proveedor</td>
    <td>Nro(s). Pedido</td>
    <td>Estado</td>
    </tr>');
    
    for($i=0;$i<count($ordenc);$i++)
    {
        ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
        $orden=$ordenc[$i];
        switch($orden['orden_estado'])
        {
            case 0: $estado='Espera Recep.'; break;
            case 1: $estado='Recep. Parcial'; break;
            case 2: $estado='Recep. Completa'; break;
            case 3: $estado='Recep. Anulada'; break;
        }
        $pnro=pg_array_parse($orden['pedido_nro']);
        $pedidoshtml='';
        for($j=0;$j<count($pnro);$j++)
        {
            $pedidoshtml.=$pnro[$j];
            if($j<count($pnro)-1)
                $pedidoshtml.='<br>';
        }
        print('<tr class="'.$clase.'" style="cursor:pointer;" onMouseOver="this.className=\'mouse_over\';"
        onMouseOut="this.className=\''.$clase.'\'" onClick="abrir_orden(\''.$orden['orden_id'].'\');">
        <td style="text-align:center;">'.$orden['orden_id'].'</td>
        <td style="text-align:center;">'.$orden['orden_numero'].'</td>
        <td style="text-align:center;">'.$orden['orden_fecha'].'</td>
        <td style="text-align:center;">'.$orden['prov_rut'].'</td>
        <td>'.htmlentities($orden['prov_glosa']).'</td>
        <td style="text-align:center;">'.$pedidoshtml.'</td>
        <td style="text-align:center;">'.$estado.'</td></tr>');
    }
?>