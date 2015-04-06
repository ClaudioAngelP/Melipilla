<?php
    require_once('../../conectar_db.php');
    $servs="'".str_replace(',','\',\'',_cav2(22))."'";
    //--------------------------------------------------------------------------
    if(_cav(22))
    {
        $bods=_cav(22);
    }
    else
    {
        $bods="null";
    }
    //--------------------------------------------------------------------------
    $origen   = pg_escape_string($_GET['bodega_origen']);
    $destino  = ($_GET['bodega_destino']*1);
    $estado   = ($_GET['estado']*1);
    $orden    = ($_GET['orden']*1);
    $fecha1   = pg_escape_string($_GET['fecha1']);
    $fecha2   = pg_escape_string($_GET['fecha2']);
    $pedido_nro = ($_GET['nro_pedido']*1);
    //--------------------------------------------------------------------------
    $origens=cargar_registros_obj("select * from(select bod_id::text as origen_id,bod_glosa as dorigen_desc from bodega where bod_id in ($bods) 
    union 
    select centro_ruta as origen_id,centro_nombre as origen_desc from centro_costo where centro_ruta in ($servs) 
    )AS foo");
    //--------------------------------------------------------------------------
    for($j=0;$j<sizeof($origens);$j++)
    {
        $origenes.="'".$origens[$j]['origen_id']."',";
    }
    //--------------------------------------------------------------------------
    if($pedido_nro!='')
    {
        $where=" pedido_nro=$pedido_nro ";
    }
    else
    {
        $where=" (date_trunc('day', pedido_fecha)>='$fecha1' AND date_trunc('day', pedido_fecha)<='$fecha2')";
        //--------------------------------------------------------------------------
	if($destino!=-1)
        {
            $where.=" AND destino_bod_id=$destino ";
        }
        //----------------------------------------------------------------------
	if($origen!=-1)
        {
            $where.=" AND origen_bod_id='$origen' ";
        }
        else
        {
            $where.=" AND origen_bod_id in (".trim($origenes,',').")";
        }
        //----------------------------------------------------------------------
	if($estado!=-1)
        {
            $where.=" AND pedido_estado=$estado ";
	}
        //----------------------------------------------------------------------
	if(isset($_GET['pds_propios']))
        {
            $where.=" AND pedido_func_id=".($_SESSION['sgh_usuario_id']*1)." ";
        }
        //----------------------------------------------------------------------
        if(isset($_GET['orientacion']))
        {
            $orienta='';
	}
        else
        {
            $orienta='DESC';
	}
        //----------------------------------------------------------------------
        if($orden==0)
        {
            $where.=" ORDER BY pedido_fecha $orienta ";
	}
        else
        {
            $where.=" ORDER BY pedido_nro $orienta ";
        }
    }
    //--------------------------------------------------------------------------
    $pedidos=cargar_registros_obj(
    "SELECT distinct * FROM(
		SELECT DISTINCT pedido_id, pedido_nro, date_trunc('second', pedido_fecha) AS pedido_fecha, 
		COALESCE(b1.bod_glosa, c2.centro_nombre, c1.centro_nombre) as ubicacion_origen, 
		COALESCE(b2.bod_glosa, 'Abastecimiento') as ubicacion_destino, pedido_estado, 
		CASE WHEN origen_bod_id=0 THEN COALESCE(c2.centro_ruta, c1.centro_ruta) 
		ELSE origen_bod_id::text END AS origen_bod_id, destino_bod_id, pedido_autorizado,
                pedido_func_id
		FROM pedido 
		LEFT JOIN bodega AS b1 ON b1.bod_id=origen_bod_id 
		LEFT JOIN logs ON log_id_pedido=pedido_id 
		LEFT JOIN cargo_centro_costo ON logs.log_id=cargo_centro_costo.log_id 
		LEFT JOIN centro_costo AS c1 ON cargo_centro_costo.centro_ruta=c1.centro_ruta 
		LEFT JOIN centro_costo AS c2 ON pedido.origen_centro_ruta=c2.centro_ruta 
		LEFT JOIN bodega AS b2 ON b2.bod_id=destino_bod_id 
		JOIN funcionario AS f1 ON f1.func_id=pedido_func_id 
		ORDER BY pedido_fecha DESC)AS foo
		WHERE $where ",true);
    
    if(isset($_GET['xls'])) {
	    header("Content-type: application/vnd.ms-excel");
      	header("Content-Disposition: filename=\"HistorialPedidos--.XLS\";");
    	$strip_html=true;
  	} else {
	$strip_html=false;
	}

    if($pedidos)
    {
       print('
            <table width=100%>
            <tr class="tabla_header" style="font-weight: bold;">
                <td>Nro. Pedido</td>
                <td>Fecha/Hora</td>
                <td>Ubicaci&oacute;n Or&iacute;gen</td>
                <td>&nbsp;</td>
                <td>Ubicaci&oacute;n Destino</td>
            ');
  
            //print("
            //<td>Ubicaci&oacute;n Or&iacute;gen</td>
            // <td>&nbsp;</td>
            // <td>Ubicaci&oacute;n Destino</td>");
  
        if($estado_q=='')
        {
            print('<td>Estado</td>');
		if(!$strip_html){print('<td>&nbsp;</td>');}
        }
  
        print('</tr>');
        
        for($i=0;$i<count($pedidos);$i++)
        {
            ($i%2==0)     ?   $clase='tabla_fila'   :  $clase='tabla_fila2';

            print(
                    '<tr class="'.$clase.'" onMouseOver="this.className=\'mouse_over\';" onMouseOut="this.className=\''.$clase.'\';">
                        <td style="text-align: center; font-weight: bold;">'.$pedidos[$i]['pedido_nro'].'</td>
                        <td style="text-align: center; font-weight: bold; font-size: 10px;">'.$pedidos[$i]['pedido_fecha'].'</td>'
                 );

            if($pedidos[$i]['origen_bod_id']!=0)
            {
                print('<td style="font-size: 10px;">'.$pedidos[$i]['ubicacion_origen'].'</td>');
            }
            else
            {
                print('<td style="font-size: 10px; color: green;">'.$pedidos[$i]['ubicacion_origen'].'</td>');
            }
            print('<td><center><b>&rarr;</b></center></td>');

            if($pedidos[$i]['destino_bod_id']!=0)
            {
                print('<td style="font-size: 10px;">'.$pedidos[$i]['ubicacion_destino'].'</td>');
            }
            else
            {
                print('<td style="font-size: 10px; color: blue;">'.$pedidos[$i]['ubicacion_destino'].'</td>');
            }
            if($estado_q=='')
            {
                if($pedidos[$i]['pedido_autorizado']=='t')
                {
                    switch($pedidos[$i]['pedido_estado'])
                    {
                        case 0: $pedidos[$i]['pedido_estado']='Enviado'; break;
                        case 1: $pedidos[$i]['pedido_estado']='Retornado'; break;
                        case 2: $pedidos[$i]['pedido_estado']='Terminado'; break;
                        case 3: $pedidos[$i]['pedido_estado']='Anulado'; break;
                    }
                }
                else
                {
                    $pedidos[$i]['pedido_estado']='Sin Autorizaci&oacute;n';
                }
                print('<td style="text-align: center;">'.$pedidos[$i]['pedido_estado'].'</td>');
            }
                    $pedido_id=$pedidos[$i]['pedido_nro'];
                    
                   if(!$strip_html){ 
			print('<td style="text-align: center;" class="no_printer">
                    <img src="iconos/magnifier.png" style="cursor: pointer;"
                    onClick="abrir_pedido1('.$pedido_id.');"
                    alt="Ver Detalle..."
                    title="Ver Detalle...">
                    </td>');
			}

                 
                



            print('</tr>');


    }
        print("</table>");
    }
else
{
    print('<div class=sub-content style="text-align:center;">NO HAY PEDIDOS EMITIDOS SEG&Uacute;N CRITERIO DE B&Uacute;SQUEDA.</div>');
}


?>

<script>

$("total_pds").value=<?php 
                        if($pedidos)
                        {
                            echo count($pedidos);
                        }
                        else
                        {
                            echo 0;
                        }
                        ?>;
</script>
