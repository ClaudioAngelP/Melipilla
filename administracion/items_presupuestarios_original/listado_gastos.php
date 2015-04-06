<?php

	require_once('../../conectar_db.php');
	    
	list($mes, $anio) = explode('/', $_POST['mesanio']);
	
	$f1=date('d/m/Y', mktime(0,0,0,$mes*1,1,$anio*1));
	$f2=date('d/m/Y', mktime(0,0,0,($mes*1)+1,1,$anio*1));
	    
	function graficar_item($datos) {
	
		$t=$datos[0]*1;
		
    if($t>0) {
  		$v=($datos[1]*100/$t);
  		$a=($datos[2]*100/$t);
  		$b=($datos[3]*100/$t);
    } else {
		$v=0;
  		$a=0;
  		$b=0;    

		$html='<table style="width:200px;border:1px solid black;height:20px;" cellpadding=0 cellspacing=0>';
		$html.='<tr>';
		$html.='<td style="width:100%;text-align:right;background-color:#eeeeee;overflow:hidden;">&nbsp;</td>';
		$html.="</tr></table>";
		
		return $html;	

    }
    
		$r=100-($v+$a+$b);
	
		$html='<table style="width:200px;border:1px solid black;height:20px;" cellpadding=0 cellspacing=0>';
		$html.='<tr>';
		if($v>0) $html.='<td style="width:'.$v.'%;background-color:green;text-align:center;overflow:hidden;color:white;">'.(($v>10)?number_format($v,0,',','.').'%':'').'</td>';
		if($a>0) $html.='<td style="width:'.$a.'%;background-color:yellow;text-align:center;">'.(($a>10)?number_format($a,0,',','.').'%':'').'</td>';
		if($b>0) $html.='<td style="width:'.$b.'%;background-color:skyblue;text-align:center;color:blue;">'.(($b>10)?number_format($b,0,',','.').'%':'').'</td>';
		$html.='<td style="width:'.$r.'%;text-align:right;background-color:skyblue;overflow:hidden;color:green;">'.(($r>10)?number_format($r,0,',','.').'%':'').'</td>';
		$html.="</tr></table>";
		
		return $html;	
		
	}
    
?>

<table style='width: 100%;' cellspacing=0>
    <tr class='tabla_header' style='font-weight: bold;'>
        <td style='width: 15%;'>
            C&oacute;digo
        </td>
        <td style='width:45%;'>
            Descripci&oacute;n
        </td>
        <td>
            Requerimiento Anual
        </td>
        <td>
            Requerimiento Mensual
        </td>
        <td>
            Compromiso Mensual
        </td>
        <td>
            Disponibilidad
        </td>
        <td>
            Detalle
        </td>
    </tr>
    <?php

        $gastos = cargar_registros_obj("
        				SELECT *,
        				(
							select sum(ordetalle_subtotal) from orden_detalle
							join articulo on ordetalle_art_id=art_id AND art_item ILIKE item_codigo || '%'
							join orden_compra on ordetalle_orden_id=orden_id AND 
							orden_fecha::date>='$f1' AND orden_fecha::date<'$f2'
        				) AS total_oc
        				FROM item_presupuestario_sigfe
              ORDER BY item_codigo
						");
             
		if($gastos)
        for($i=0;$i<sizeof($gastos);$i++)
        {
            ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
            print("
            
            <tr class='".$clase."' style='height:25px;'
            onMouseOver='this.className=\"mouse_over\";'
            onMouseOut='this.className=\"".$clase."\";'
            onClick='cargar_gasto(".$gasto[0].");'>
				
				<td style='text-align:right;font-weight:bold;'>".$gastos[$i]['item_codigo']."</td>
				<td style='font-size:10px;'>".htmlentities($gastos[$i]['item_nombre'])."</td>
				<td style='text-align:right;'>$".number_format($gastos[$i]['item_requerimiento'],0,',','.').".-</td>
				<td style='text-align:right;'>$".number_format($gastos[$i]['item_requerimiento']/12,0,',','.').".-</td>
				<td style='text-align:right;'>$".number_format($gastos[$i]['total_oc'],0,',','.').".-</td>
				
				<td style='text-align:center;'>
				".graficar_item(
					array(	($gastos[$i]['item_requerimiento']/12),
							$gastos[$i]['total_oc'],
							0,
							($gastos[$i]['item_requerimiento']/12)-$gastos[$i]['total_oc']))."</td>
				
				<td>
					<center>
						<img src='iconos/magnifier.png' onClick='detalle(\"".$gastos[$i]['item_codigo']."\");' style='cursor:pointer;' />
					</center>
				</td>

            </tr>
            
            ");
        }

    ?>

</table>
