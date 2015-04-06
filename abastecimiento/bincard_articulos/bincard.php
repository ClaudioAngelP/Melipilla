<?php
    $temp_5_1=microtime();
    require_once('../../conectar_db.php');
    $codigo = ($_GET['prod_id']);
    $bodega = ($_GET['bodega']);
    $fecha1 = pg_escape_string($_GET['fecha1']);
    $fecha2 = pg_escape_string($_GET['fecha2']);
      
    if(strstr($bodega,'.'))
    {
        $tabla_stock='stock_servicios';
        $centro_ruta=pg_escape_string($bodega);
        $bodega='stock_centro_ruta=\''.(pg_escape_string($bodega)).'\'';
        $mt=18;      
        $ubica_join='LEFT JOIN centro_costo AS c2 
                      ON stock_centro_ruta=c2.centro_ruta';
        $campo="centro_origen";
        $campofoo="c2.centro_nombre AS centro_origen";
      
        $centro_reg = cargar_registro("SELECT * FROM centro_costo WHERE
                                      centro_ruta='".$centro_ruta."'");
        
        $bodega_nombre = $centro_reg['centro_nombre'];
      
    }
    else
    {
        $tabla_stock='stock';
        $bod_id=($bodega*1);
        $bodega='stock_bod_id='.($bod_id);
        $mt=2;  
        $ubica_join='LEFT JOIN bodega ON stock_bod_id=bod_id';
        $campo='b1.bod_glosa';
        $campofoo='bod_glosa';

        $bodega_reg = pg_query($conn, "
        SELECT bod_glosa FROM bodega WHERE bod_id=".$bod_id."
        ");
        
        $bodega_row = pg_fetch_row($bodega_reg);
        
        $bodega_nombre = $bodega_row[0];
    }
      
    if(isset($_GET['strip']))
    {
        $strip_html = true;
        $codigo = pg_escape_string($_GET['prod_ids']);
        $codigos = str_replace('!', ',', $codigo);
    }
    else
    {
        $strip_html = false;
        $codigos = $codigo;
    }
      
    if(isset($_GET['tipomov'])) 
        $mostrar_tipo=true;
    else
        $mostrar_tipo=false;
      
    if(isset($_GET['saldo']))
        $mostrar_saldo=true;
    else
        $mostrar_saldo=false;
              
    if(isset($_GET['datosreceta']))
        $mostrar_receta=true;
    else
        $mostrar_receta=false;
      
    if(isset($_GET['origendestino']))
        $mostrar_lugar=true;
    else 
        $mostrar_lugar=false;
      
    if(isset($_GET['valor']))
        $mostrar_valor=true;
    else
        $mostrar_valor=false;
      
    if(isset($_GET['datosmedico']))
        $mostrar_medico=true;
    else
        $mostrar_medico=false;
      
    if(isset($_GET['datospaciente']))
        $mostrar_paciente=true;
    else
        $mostrar_paciente=false;
	
	 if(isset($_GET['datosadquiriente']))
        $mostrar_adquiriente=true;
    else
        $mostrar_adquiriente=false;
    
    $temp_4_1=microtime();
      
    pg_query($conn, "
        CREATE TEMP TABLE logs_periodo AS
        SELECT DISTINCT logs.* FROM logs
        JOIN $tabla_stock ON
        stock_log_id=log_id 
        AND stock_art_id IN ($codigos) 
        AND $bodega
        WHERE
        date_trunc('day',log_fecha)>='$fecha1'
        AND
        date_trunc('day',log_fecha)<='$fecha2'
        ");

    pg_query($conn, "
        CREATE TEMP TABLE stock_periodo AS
        SELECT * FROM $tabla_stock
        JOIN logs_periodo ON stock_log_id=logs_periodo.log_id
        WHERE stock_art_id IN ($codigos) AND $bodega
        ");
      
    $temp_4_2=microtime();

    if(isset($_GET['xls'])) {
	    header("Content-type: application/vnd.ms-excel");
    if(!$fref) {
      header("Content-Disposition: filename=\"InformeBincard".$ubica."---".date('d-m-Y').".XLS\";");
    } else {
      header("Content-Disposition: filename=\"InformeBincard".$ubica."---".str_replace('/','-',$fecha_ref).".XLS\";");
      
    }
    $strip_html=true;
  	} else {
  
  }
  
    if($fecha1!=$fecha2)
    {
        $rango_fechas = $fecha1.' <i>al</i> '.$fecha2;
    }
    else
    {
        $rango_fechas = $fecha1;
    }
      
    $codigo = pg_escape_string($codigo);
    $fecha1 = pg_escape_string($fecha1);
    $fecha2 = pg_escape_string($fecha2);
      
    if($bodega==-1)
    {
        $bodega_where='';
    }
    else
    {
        $bodega_where="
        AND
        $bodega
        ";
    }
  
    if(!$strip_html)
    {
        print("
                <html>
                    <title>Bincard de Art&iacute;culos</title>");
                    cabecera_popup('../..');
?>
        <script>
            imprimir_bincard = function()
            {
                informe = $('datos_prod').innerHTML+'<hr><div class="bincard">'+$('tabla_bincard').innerHTML+'</div>';
                imprimirHTML(informe);
            }
            ajustar_div = function()
            {
                $('tabla_bincard').style.height=window.innerHeight-260;
            }
        </script>
  
<?php
        print("
            <body class='fuente_por_defecto popup_background'
            onload='ajustar_div();'
            onresize='ajustar_div();'>
            <div class='sub-content2' id='datos_prod'>
            ");
    }
      
    $art_ids = explode('!', $codigo);
    for($x=0;$x<count($art_ids);$x++)
    {
        $art_id=$art_ids[$x];
        $temp_1_1=microtime();
        $cantidadinicial = pg_query($conn, "
            SELECT sum(stock_cant) FROM $tabla_stock
            JOIN logs ON stock_log_id = logs.log_id
            LEFT JOIN pedido ON logs.log_id_pedido = pedido.pedido_id
            LEFT JOIN pedido_detalle ON
            pedido_detalle.pedido_id = pedido.pedido_id AND
            pedido_detalle.art_id = stock_art_id
            WHERE
            ((NOT log_tipo=$mt) OR
            (log_tipo=$mt AND pedidod_estado))
            AND
            stock_art_id=$art_id AND log_fecha<'$fecha1' $bodega_where; ");
            $temp_1_2=microtime();
            /*"
            SELECT
            COALESCE(SUM(stock_cant),0)
            FROM stock
            LEFT JOIN articulo ON art_id=stock_art_id
            LEFT JOIN logs ON log_id=stock_log_id
            LEFT JOIN pedido ON log_id_pedido=pedido_id
            LEFT JOIN pedido_detalle ON
            pedido_detalle.pedido_id=pedido.pedido_id AND
            pedido_detalle.art_id=stock_art_id
            WHERE
            articulo.art_id=$art_id
            AND
            (COALESCE(pedido_estado,2)=2 OR pedidod_estado)
            AND
            log_fecha<'$fecha1'
            $bodega_where
            ");*/
        $cantinit = pg_fetch_row($cantidadinicial);
        $temp_2_1=microtime();
        $movimientos = pg_query($conn, "
            SELECT
            foo.*,
            $campo,
            c1.centro_nombre,
            prov_glosa,
            pedido_nro,
            pedidod_estado,
            pedidod_id
            FROM
            (
            SELECT
            date_trunc('second', logs_periodo.log_fecha) AS log_fecha,
            logs_periodo.log_tipo,
            stock_cant,
            c0.centro_nombre,
            $campofoo,
            pedido_estado,
            doc_rut,
            doc_paterno || ' ' || doc_materno || ' ' || doc_nombres,
            COALESCE(p1.pac_rut,p2.pac_rut),
            CASE WHEN p1.pac_id IS NOT NULL THEN p1.pac_appat || ' ' || p1.pac_apmat || ' ' || p1.pac_nombres 
            ELSE p2.pac_appat || ' ' || p2.pac_apmat || ' ' || p2.pac_nombres END AS pac_nombre,
            adq_rut,
            adq_appat || ' ' || adq_apmat || ' ' || adq_nombres,
            tipotalonario_nombre_corto,
            receta_numero,
            bod_destino_movimiento(logs_periodo.log_id) AS bod_destino,
            centro_destino_movimiento(logs_periodo.log_id) AS centro_destino,
            documento.doc_id,
            pedido_id,
            (stock_subtotal) AS valor,
            logs_periodo.log_id,
            institucion_solicita.instsol_desc
            FROM stock_periodo
            LEFT JOIN articulo ON art_id=stock_art_id
            LEFT JOIN logs_periodo ON logs_periodo.log_id=stock_log_id
            LEFT JOIN documento ON logs_periodo.log_doc_id=documento.doc_id
            LEFT JOIN pedido ON logs_periodo.log_id_pedido=pedido_id
            LEFT JOIN recetas_detalle ON logs_periodo.log_recetad_id=recetad_id
            LEFT JOIN receta ON recetad_receta_id=receta_id
            LEFT JOIN receta_tipo_talonario
            ON receta_tipotalonario_id=tipotalonario_id
            LEFT JOIN centro_costo AS c0 ON receta_centro_ruta=c0.centro_ruta
            $ubica_join
            LEFT JOIN pacientes AS p1 ON receta.receta_paciente_id=p1.pac_id
            LEFT JOIN cargo_hoja ON cargo_hoja.log_id=logs_periodo.log_id
            LEFT JOIN pacientes AS p2 ON p2.pac_id=cargo_hoja.pac_id  
            LEFT JOIN receta_adquiriente ON receta.receta_id=receta_adquiriente.receta_id
            LEFT JOIN doctores ON receta.receta_doc_id=doctores.doc_id
            LEFT JOIN cargo_instsol ON logs_periodo.log_id=cargo_instsol.log_id
            LEFT JOIN institucion_solicita ON institucion_solicita.instsol_id=cargo_instsol.instsol_id
            WHERE
            articulo.art_id=$art_id
            $bodega_where
            ) AS foo
            LEFT JOIN bodega AS b1 ON bod_destino=bod_id
            LEFT JOIN centro_costo AS c1 ON centro_destino=c1.centro_ruta
            LEFT JOIN documento ON documento.doc_id=foo.doc_id
            LEFT JOIN proveedor ON prov_id=documento.doc_prov_id
            LEFT JOIN pedido ON pedido.pedido_id=foo.pedido_id
            LEFT JOIN pedido_detalle ON
            pedido_detalle.pedido_id=pedido.pedido_id
            AND pedido_detalle.art_id=$art_id
            
            WHERE
            NOT
            ( log_tipo=$mt AND
            (pedido.pedido_estado IN (2,3)) AND
            (NOT pedido_detalle.pedidod_estado)
            )
            ORDER BY log_fecha
            ");
            

        $temp_2_2=microtime();
        $temp_3_1=microtime();
      
        
        $consulta_cant_final="SELECT sum(stock_cant) FROM $tabla_stock
        JOIN logs ON stock_log_id = logs.log_id
        LEFT JOIN pedido ON logs.log_id_pedido = pedido.pedido_id
        LEFT JOIN pedido_detalle ON
        pedido_detalle.pedido_id = pedido.pedido_id AND
        pedido_detalle.art_id = stock_art_id
        WHERE
        ((NOT log_tipo=$mt) OR
        (log_tipo=$mt AND pedidod_estado))
        AND
        stock_art_id=$art_id and
        date_trunc('day', log_fecha)<='$fecha2' $bodega_where; ";
        
        //print($consulta_cant_final);
      
        $cantidadfinal = pg_query($conn, $consulta_cant_final);
        
        
     
        $temp_3_2=microtime();

        $articulo = pg_query("
		SELECT
		art_id,
		art_codigo,
		art_glosa,  
		art_nombre,
		item_glosa,
		clasifica_nombre,
		forma_nombre,
		art_vence,
		art_control
        FROM articulo forma_nombre
        LEFT JOIN item_presupuestario ON item_codigo=art_item
		LEFT JOIN bodega_clasificacion ON clasifica_id=art_clasifica_id
		LEFT JOIN bodega_forma ON forma_id=art_id
        WHERE
		art_id=$art_id 
		LIMIT 1
        ");
    
        $art=pg_fetch_row($articulo);
      
        if($art[7]==1)  $art[7]='iconos/tick.png';//perecible
        else            $art[7]='iconos/cross.png';
      
        if($art[8]>0)     $art[8]='iconos/tick.png'; //controlado
        else              $art[8]='iconos/cross.png';
      
        print("
        <table style='font-size:12px;'>
        <tr>
        <td colspan=3 style='font-weight:bold;text-align:center;'>
        <font size=+1>
        ".htmlentities($sghservicio)."<br>
        ".htmlentities($sghinstitucion)."
        </font>
        </td>
        </tr><br>
        <tr><td style='text-align: right;'>Ubicaci&oacute;n:</td>
        <td><b>".htmlentities($bodega_nombre)."</b></td></tr>
        <tr><td style='text-align: right;'>Rango de Fechas:</td>
        <td><b>".$rango_fechas."</b></td></tr>
        <tr><td style='text-align: right;'>C&oacute;digo Int.:</td>
        <td id='prod_codigo' style='font-weight: bold; font-style: italic;'>
        ".$art[1]."
        </td></tr>
        <tr><td style='text-align: right;'>Glosa:</td>
        <td id='prod_glosa' style='font-weight: bold; font-style: italic;'>
        ".htmlentities($art[2])."</td></tr>
        <tr><td style='text-align: right;'>Nombre:</td>
        <td id='prod_nombre' style='font-weight: bold;'>
        ".htmlentities($art[3])."</td></tr>
        <tr><td style='text-align: right;'>Item Presupuestario:</td>
        <td id='prod_item' style='font-weight: bold; font-style: italic;'>
        ".htmlentities($art[4])."</td></tr>
        <tr><td style='text-align: right;'>Clasificaci&oacute;n:</td>
        <td id='prod_clasifica'>
        ".htmlentities($art[5])."</td></tr>
        ");
  
        /*
        print("
        <tr><td style='text-align: right;'>Forma Farmac&eacute;utica:</td>
        <td id='prod_forma'>
        ".htmlentities($art[6])."</td></tr>
        <tr><td id='prod_vence' style='text-align: right;'>Perecible:</td>
        <td>
        <img src='".$art[7]."'>
        </td></tr>
        <tr><td id='prod_controlado' style='text-align: right;'>Controlado:</td>
        <td>
        <img src='".$art[8]."'>
        </td></tr>
        ");
        */
  
        print("</table>");
  
        if(!$strip_html)
        print("
            </div>
            <div class='bincard' id='tabla_bincard'
            style='height:215px;
            overflow:auto;
            '>
            ");
        else
            print("
            <hr>
            <div class='bincard' style='page-break-after: always;'>
            ");
  
        $temp_5_2=microtime();
  
        print("
            <table width='100%'>
            <tr class='tabla_header' style='font-weight:bold;'>
            <td width='15%'>Fecha</td>
            <td>Tipo de Movimiento</td>
            ");
      
        if($mostrar_receta)
        print("<td>Tipo Receta</td><td>Nro. Receta</td>");
      
        if($mostrar_medico)
        print('<td>RUT M&eacute;dico</td><td>Nombre M&eacute;dico</td>');
      
        if($mostrar_paciente)
        print('<td>RUT Paciente</td><td>Nombre Paciente</td>');
        
        if($mostrar_adquiriente)
        print('<td>RUT Adquiriente</td><td>Nombre Adquiriente</td>');
      
        if($mostrar_lugar)
        print("<td>Corr./Nro. Pedido</td><td>Or&iacute;gen/Destino</td>");
      
        print("<td>Cant.</td>");
      
        if($mostrar_valor)
        print("<td>P. Unit. $</td>");
      
        if($mostrar_saldo)
        {
            print("<td>Saldo</td>");
            $saldo = $cantinit[0];
        }
      
        print("<td class='no_printer'>
        <img src='../../iconos/zoom.png'>
        </td></tr>");
      
        print("
        <tr class='tabla_fila_m'>
        <td style='text-align: center; font-weight: bold;'>$fecha1 00:00:00</td>
        <td>Cantidad Inicial.</td>
        ");

        if($mostrar_receta)
        print("<td>&nbsp;</td><td>&nbsp;</td>");
      
        if($mostrar_medico)
        print("<td>&nbsp;</td><td>&nbsp;</td>");
      
        if($mostrar_paciente)
        print("<td>&nbsp;</td><td>&nbsp;</td>");
        
        if($mostrar_adquiriente)
        print("<td>&nbsp;</td><td>&nbsp;</td>");
      
        if($mostrar_lugar)
        print("<td>&nbsp;</td><td>&nbsp;</td>");
      
        print("
        <td style='text-align: right;'>
        ".number_format($cantinit[0], 2, ',', '.')."</td>");
      
        if($mostrar_valor)
        print("<td>&nbsp;</td>");
      
        if($mostrar_saldo)
        {
            print("<td style='text-align: right;'>
            ".number_format($saldo, 2,',','.')."</td>");
        }
      
        print("<td
        style='text-align: center;' class='no_printer'>
        <img src='../../iconos/control_play.png'>
        </td></tr>
        ");
        $num_pedido=0;
        for($i=0;$i<pg_num_rows($movimientos);$i++)
        {
            $estilo='';
            $msg='';
            $fila = pg_fetch_row($movimientos);
            ($i%2==1)   ?   $clase='tabla_fila'   : $clase='tabla_fila2';
            $tipomov = $fila[1];

            switch($tipomov)
            {
                case 0:
                case 1: $fila[1]='Ingreso desde Proveedor.'; break;
                case 2: $fila[1]='Traslado de Productos.'; break;
                case 4: $fila[1]='Ingreso por Excedente.'; break;
                case 5: $fila[1]='Ingreso por Donaci&oacute;n.'; break;
                case 6: $fila[1]='Pr&eacute;stamo/Devoluci&oacute;n de Art&iacute;culos.'; break;
                case 7: $fila[1]='Baja por Vencimiento.'; break;
                case 8: $fila[1]='Dado de Baja.'; break;
                case 9: $fila[1]='Gasto por Receta.'; break;
                case 10: $fila[1]='Utilizado en Farmacia Magistral.'; break;
                case 15: $fila[1]='Cargo a Servicio.'; break;
                case 16: $fila[1]='Devoluci&oacute;n desde Servicio.'; break;
                case 17: $fila[1]='Salida por Hoja de Cargo en Servicio.'; break;
                case 18: $fila[1]='Despacho a Servicio.'; break;
                case 20: $fila[1]='Inicio de Control por Sistema.'; break;
                case 30: $fila[1]='Ajuste de Saldos.'; break;
                case 31: $fila[1]='Ajuste de Saldos por Merma.'; break;
                case 32: $fila[1]='Ajuste de Saldos por Vencimiento.'; break;
                case 33: $fila[1]='Ingreso por anulaci&oacute;n de Receta.'; break;
            }
            switch($tipomov)
            {
                //ingreso desde proveedor
                case 1:
                $ubicacion = "
                <td style='text-align: center;'>
                [".htmlentities($fila[16])."]</td>
                <td>
                ".htmlentities($fila[23])."
                </td>
                ";
                $accion="window.opener.visualizador_documentos(\"Visualizar Recepción\",
                \"doc_id=\"+\"".htmlentities($fila[16])."\");
                 window.opener.focus();
                ";
                break;
            
                //traslado de producto
                case 2:
                if(($fila[5]==0 or $fila[5]==1) and $fila[25]=='f')
                {
                    $estilo = 'style="font-style: italic;"';
                    $msg = '<br><b>[Requiere Confirmaci&oacute;n]</b>';
                }
                $ubicacion="
                <td style='text-align: center;'>(".htmlentities($fila[24]).")</td>
                <td>
                ".htmlentities($fila[21])."
                </td>";
             
                $accion="window.opener.visualizador_documentos(\"Visualizar Movimiento\",
                    \"id_pedido=\"+encodeURIComponent(\"".htmlentities($fila[17])."\"));
                    window.opener.focus();
                    ";
                break;


                case 5:
                if(($fila[5]==0 or $fila[5]==1) and $fila[25]=='f')
                {
                    $estilo = 'style="font-style: italic;"';
                    $msg = '<br><b>[Requiere Confirmaci&oacute;n]</b>';
                }

                $ubicacion='<td>&nbsp;</td><td>&nbsp;</td>';


                //$ubicacion="
                //<td style='text-align: center;'>(".htmlentities($fila[24]).")</td>
                ///<td>
                //".htmlentities($fila[21])."
                //</td>";

                $accion="
                window.opener.visualizador_documentos(\"Visualizar Movimiento\",
                \"log_id=\"+encodeURIComponent(\"".htmlentities($fila[19])."\"));
                window.opener.focus();
                ";
                break;


                //prestamo o devolución
                case 6:
                $ubicacion="
                <td style='text-align: center;'>{".htmlentities($fila[19])."}</td>
                <td>
                ".htmlentities($fila[20])."
                </td>";
                $accion="
                window.opener.visualizador_documentos(\"Visualizar Movimiento\", 
                \"log_id=\"+encodeURIComponent(\"".htmlentities($fila[19])."\"));
                window.opener.focus();
                ";
                break;

                //gasto por receta
                case 9:
                $ubicacion="
                <td>&nbsp;</td>
                <td>
                ".htmlentities($fila[3])."
                </td>";
                $accion="";
                break;

                //cargo a servicio
                case 15:
                $ubicacion="
                <td style='text-align: center;'>(".htmlentities($fila[24]).")</td>
                <td>
                ".htmlentities($fila[22])."
                </td>";
              
                $accion="window.opener.visualizador_documentos(\"Visualizar Cargo a Servicio\",
                \"id_pedido=\"+encodeURIComponent(\"".htmlentities($fila[17])."\"));
                window.opener.focus();
                ";
                break;

                //devolución desde servicio
                case 16:
                $ubicacion="
                <td style='text-align: center;'>{".htmlentities($fila[19])."}</td>
                <td>
                ".htmlentities($fila[22])."
                </td>";
                $accion="";
                break;

                default:
                $ubicacion='<td>&nbsp;</td><td>&nbsp;</td>';
                $accion="";
                break;






                //despacho a servicio
                case 18:
                if($fila[24]!='')
                {
                    $ubicacion="
                    <td style='text-align: center;'>(".htmlentities($fila[24]).")</td>
                    <td>
                    ".htmlentities($fila[22])."
                    </td>";
                }
                else
                {
                     $ubicacion="
                    <td style='text-align: center;'>{".htmlentities($fila[19])."}</td>
                    <td>
                    ".htmlentities($fila[22])."
                    </td>";
                }
                $accion="window.opener.visualizador_documentos(\"Visualizar Despacho a Servicio\",
                    \"id_pedido=\"+encodeURIComponent(\"".htmlentities($fila[17])."\"));window.opener.focus();
                    ";

                break;
                //default:
                //$ubicacion='<td>&nbsp;</td><td>&nbsp;</td>';
                //$accion="window.opener.visualizador_documentos(\"Visualizar Despacho a Servicio\",
                //\"id_pedido=\"+ecodeURIComponent(\"".htmlentities($fila[24])."\"))";
                //break;



                case 20:
                //$ubicacion="
                //<td style='text-align: center;'>{".htmlentities($fila[19])."}</td>
                //<td>
                //".htmlentities($fila[22])."
                //</td>";
                $ubicacion='<td>&nbsp;</td><td>&nbsp;</td>';


                $accion="window.opener.visualizador_documentos(\"Visualizar Ajuste de Saldo\",
                \"log_id=\"+\"".htmlentities($fila[19])."\");
                 window.opener.focus();";
                break;





                 case 30:
                $ubicacion="
                <td style='text-align: center;'>{".htmlentities($fila[19])."}</td>
                <td>
                ".htmlentities($fila[22])."
                </td>";
                $accion="window.opener.visualizador_documentos(\"Visualizar Ajuste de Saldo\",
                \"log_id=\"+\"".htmlentities($fila[19])."\");
                 window.opener.focus();
                ";
                break;
                
                case 33:
                $ubicacion="
                <td style='text-align: center;'>{".htmlentities($fila[19])."}</td>
                <td>
                ".htmlentities($fila[22])."
                </td>";
                $accion="window.opener.visualizador_documentos(\"Visualizar Entrada	\",
                \"log_id=\"+\"".htmlentities($fila[19])."\");
                 window.opener.focus();
                ";
                break;


              default:
                $ubicacion='<td>&nbsp;</td><td>&nbsp;</td>';
                $accion="";
                break;



            }
            print("
            <tr class='$clase' $estilo>
            <td style='text-align: center; font-weight: bold;'>".$fila[0]."</td>
            <td>".$fila[1].' '.$msg."</td>");

            if($mostrar_receta)
            {
                if($tipomov==9)
                {
                    if($fila[12]!=null)
                    {
                        print("
                        <td style='text-align: center;'>".htmlentities($fila[12])."</td>
                        <td style='text-align: center;'>".htmlentities($fila[13])."</td>
                        ");
                    }
                    else
                    {
                        print("
                        <td style='text-align: center;'>Aguda</td>
                        <td style='text-align: center;'>N/A</td>
                        ");
                    }
                }
                else
                {
                    print("<td>&nbsp;</td><td>&nbsp;</td>");
                }
            }
            if($mostrar_medico)
                print('<td style="text-align:center;">'.htmlentities($fila[6]).'</td>
                <td>'.htmlentities($fila[7]).'</td>');
          
            if($mostrar_paciente)
                print('<td style="text-align:center;">'.htmlentities($fila[8]).'             </td>
                <td>'.htmlentities($fila[9]).'</td>');
                
            if($mostrar_adquiriente)
            	print('<td style="text-align:center;">'.htmlentities($fila[10]).'</td>
            	<td>'.htmlentities($fila[11]).'</td>');
          
            if($mostrar_lugar)
                print($ubicacion);
          
            if($fila[2]>0) $prefix='+'; else $prefix='';
          
            print("
            <td style='text-align: right;'>
            ".$prefix.''.number_format($fila[2], 2, ',', '.')."</td>
            ");
          
            if($mostrar_valor)
                print("<td style='text-align: right;'>
                $".number_format(($fila[18]/$fila[2]),0,',','.').".-</td>");
      
          
            if($mostrar_saldo)
            {
                if(!($tipomov==2 AND $fila[5]!=2))
                $saldo+=$fila[2];
                else if($tipomov==2 AND $fila[25]=='t')
                $saldo+=$fila[2];
            
                print("
                <td style='text-align: right;'>
                ".number_format($saldo, 2, ',', '.')."</td>");
            }
          
        if(!isset($_GET['xls'])) 
            	print("
		            <td style='text-align: center;' class='no_printer'>
		            <img src='../../iconos/zoom_in.png' style='cursor: pointer;'
		            onClick='$accion'
		            alt='Ver Detalle...'
		            title='Ver Detalle...'>
		            </td>
	            ");
      		  	
      		  	print("</tr>");
        }
      
        $cantfinit = pg_fetch_row($cantidadfinal);
        print("
        <tr class='tabla_fila_m'>
        <td style='text-align: center; font-weight: bold;'
        >$fecha2 23:59:59</td><td>Cantidad Final.</td>");
      
        if($mostrar_receta)
        print("<td>&nbsp;</td><td>&nbsp;</td>");
      
        if($mostrar_medico)
        print("<td>&nbsp;</td><td>&nbsp;</td>");
      
        if($mostrar_paciente)
        print("<td>&nbsp;</td><td>&nbsp;</td>");
        
         if($mostrar_adquiriente)
        print("<td>&nbsp;</td><td>&nbsp;</td>");
      
        if($mostrar_lugar)
        print("<td>&nbsp;</td><td>&nbsp;</td>");
      
        print("
        <td style='text-align: right;'>
        ".number_format($cantfinit[0], 2, ',', '.')."</td>
        ");
      
        if($mostrar_valor)
            print("<td>&nbsp;</td>");
      
        if($mostrar_saldo)
        {
            print("<td style='text-align: right;'>
            ".number_format($saldo, 2, ',', '.')."</td>");
        }
      
        print("
        <td style='text-align: center;' class='no_printer'>
        <img src='../../iconos/control_stop.png'>
        </td></tr>
        ");
      
        print("
        </table>
        </div>
        ");
    }
  
    if(!$strip_html)
    {
?>
        <center>
        <div class='boton'>
            <table>
            <tr>
                <td>
                    <img src='../../iconos/printer.png'>
                </td>
                <td>
                    <a href='#' onClick='imprimir_bincard();'>
                        Imprimir Bincard...
                    </a>
                </td>
            </tr>
            </table>
		</div>
        </center>
        </body>
        </html>

<?php 
    }
die();
?>
