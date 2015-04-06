<?php

  require_once('../../conectar_db.php');
  
  if(!isset($_GET['submit']))
  {
  
  } 
  else
  {
  
    // El script puede ejecutarse por mas de 30 segundos.
    
    set_time_limit(0);
    $suma=0;
    $iva=0;
    $total=0;
  
    $tmp_inicio = microtime(true);

    $fecha1=pg_escape_string($_GET['fecha1']);
    $fecha2=pg_escape_string($_GET['fecha2']);
    $prov_id=pg_escape_string($_GET['id_prov']);
    $glosa_prov=pg_escape_string($_GET['glosa_pro']);
    $rut_prov=pg_escape_string($_GET['rut_prov']);
    
    if(isset($_GET['xls'])) 
    {
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: filename=\"Listado de ordenes de compra.XLS\";");
        $xlsborder="border=1";
        $clasetabla="";
    } 
    //else
    //{
    //    $xlsborder="";
    //    $clasetabla="class='tabla_header'";
    //    cabecera_popup('../..');

   //     ?>
   <!--     <title>Informe General de Gastos por Centro de Costo</title>
        <body class='fuente_por_defecto popup_background'>
    -->
     <?php
   // }
    
  
    // Crea tabla temporal con:
    // -- gastos de recetas
    // -- gastos fuera de bodega
    // -- despachos a servicios
    // -- gastos por hoja de cargo
     
    
    $query="select * from orden_compra
            where orden_prov_id=$prov_id
            and orden_fecha BETWEEN '".$fecha1." 00:00:00' AND '".$fecha2." 24:00:00'";

    $ordenes=pg_query($conn, $query);
    
    //$centros = pg_query($conn, "
    //SELECT DISTINCT centro_ruta, centro_nombre FROM gasto_centros
    //JO/IN centro_costo USING (centro_ruta);
    //");
    
   // $items = pg_query($conn, "
    //SELECT DISTINCT item_codigo, item_glosa FROM gasto_centros
    //JOIN item_presupuestario USING (item_codigo);
    //");
  
    // Cabecera del Informe
  
    print("<table $xlsborder style='font-size:11px;'>
    <tr ".$clasetabla." style='text-align: center; font-weight:bold;'>
    <td style='width:300px;'>&nbsp;</td></tr>
    <tr>
    <td style='width:300px;'>Rut Proveedor:</td>
    <td style='width:300px;'>".$rut_prov."</td>
    </tr>
    <tr>
    <td style='width:300px;'>Proveedor:</td>
    <td style='width:300px;'>".$glosa_prov."</td>
    </tr>
    <tr>
    <td style='width:300px;'>N° de ordenes:</td>
    <td style='width:300px;'>".pg_num_rows($ordenes)."</td>
    </tr>
    <tr>
    <td style='width:300px;'>&nbsp;</td>
    </tr>
    <tr>
    <td style='width:300px;'>&nbsp;</td>
    </tr>
    ");
    for($i=0;$i<pg_num_rows($ordenes);$i++)
    {
        $ordenes_arr = pg_fetch_row($ordenes, $i);
        print("<tr>
        <td>Orden:</td>
        <td>Fecha Emisión:</td>
        </tr>
        <tr>
        <td>".$ordenes_arr[1]."</td>
        <td>".$ordenes_arr[3]."</td>
        </tr>
        ");

        print("
        <td class='tabla_header' style='font-weight: bold;'>Detalle de Orden</td>
        </tr>
        ");

        print("<tr class='tabla_header' style='font-weight: bold;'>
        <td align='center' bgcolor='#848484'>Codigo</td>
        <td align='center' bgcolor='#848484'>Glosa</td>
        <td align='center' bgcolor='#848484'>Item-pres</td>
        <td align='center' bgcolor='#848484'>Cantidad</td>
        <td align='center' bgcolor='#848484'>P/U</td>
        <td align='center' bgcolor='#848484'>Subtotal</td>
        </tr>
        ");

        $query="select orden_detalle.*, articulo.art_codigo,art_glosa, item_glosa
        from orden_detalle
        left join articulo on ordetalle_art_id=art_id
        left join item_presupuestario on art_item=item_codigo
        where ordetalle_orden_id=$ordenes_arr[0]";

        $orden_detalle=pg_query($conn, $query);

        if(pg_num_rows($orden_detalle)!=0)
        {
            for($r=0;$r<pg_num_rows($orden_detalle);$r++)
            {
                $ordetalle_arr = pg_fetch_row($orden_detalle, $r);
                $cantidad=$ordetalle_arr[3]*1;
                print("<tr>
                    <td align='center'>".$ordetalle_arr[5]."</td>
                    <td>".$ordetalle_arr[6]."</td>
                    <td>".$ordetalle_arr[7]."</td>
                    <td align='center'>$cantidad</td>
                    <td>".number_formats($ordetalle_arr[4]/$ordetalle_arr[3])."</td>
                    <td align='right'>".number_formats(($ordetalle_arr[4]*1))."</td>
                    </tr>
                    ");
                    $suma=$suma+($ordetalle_arr[4]*1);

            }
        }

        $query="select orden_servicios.*, item_glosa from orden_servicios
        left join item_presupuestario on orserv_item=item_codigo
        where orserv_orden_id=$ordenes_arr[0]";

        $orden_servicio=pg_query($conn, $query);

        if(pg_num_rows($orden_servicio)!=0)
        {
            for($x=0;$x<pg_num_rows($orden_servicio);$x++)
            {
                $ordeserv_arr=pg_fetch_row($orden_servicio, $x);
                
                $cantidad=$ordeserv_arr[5];
                print("<tr>
                        <td align='center'>N/A</td>
                        <td>".$ordeserv_arr[2]."</td>
                        <td>".$ordeserv_arr[6]."</td>
                        <td align='center'>".$cantidad."</td>
                        <td>".number_formats($ordeserv_arr[3]/$ordeserv_arr[5])."</td>
                        <td align='right'>".number_formats(($ordeserv_arr[3]*1))."</td>
                        </tr>
                            ");
                $suma=$suma+($ordeserv_arr[3]*1);
            }
        }
        $total=$suma*1.19;
        $iva=number_formats($total-$suma);
        print("<tr class='tabla_header' style='font-weight: bold;'>
        <td align='right' colspan=5 bgcolor='#848484' >
        Neto:
        </td>
        <td align='right' bgcolor='#848484'>
        ".number_formats($suma)."
        </td>
        </tr>
        <tr class='tabla_header' style='font-weight: bold;'>
        <td align='right' colspan=5 bgcolor='#848484' >
        IVA:
        </td>
        <td align='right' bgcolor='#848484'>
        ".$iva."
        </td>
        </tr>
        <tr class='tabla_header' style='font-weight: bold;'>
        <td align='right' colspan=5 bgcolor='#848484' >
        Total:
        </td>
        <td align='right' bgcolor='#848484'>
        ".number_formats($total)."
        </td>
        </tr>
        ");
        

    print("<tr>
         <td style='width:300px;'>&nbsp;</td>
         </tr>
         <tr>
         <td style='width:300px;'>&nbsp;</td>
         </tr>
         <tr>
        </tr>
        ");
        $suma=0;
        $iva=0;
        $total=0;

    }
  
    print("</table>");
    
    $tmp_final = microtime(true);
    
    $tmp = $tmp_final-$tmp_inicio;
    
    print("<center>Obtenido en [".$tmp."] msecs.</center>");
  
  }

  if(!isset($_GET['xls'])) {
  
?>

</body>

<?php
  
  }

?>
