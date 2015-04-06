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
    
    <?php

        $gastos = cargar_registros_obj("
        				
        			SELECT foo.centro_ruta, centro_nombre, art_item, item_glosa, gasto, ppto_monto FROM (
					select centro_ruta, art_item, sum(art_val_ult*-(stock_cant)) AS gasto
					FROM stock
					JOIN logs ON stock_log_id=log_id AND log_fecha::date>='$f1' AND log_fecha::date<='$f2' AND log_tipo IN (2,15,18)
					JOIN cargo_centro_costo USING (log_id)
					JOIN articulo ON stock_art_id=art_id
					WHERE stock_cant<0
					GROUP BY centro_ruta, art_item)
					AS foo
					JOIN centro_costo USING (centro_ruta)
					JOIN item_presupuestario ON art_item=item_codigo
					LEFT JOIN centro_costo_presupuesto AS ppto ON foo.centro_ruta=ppto.centro_ruta AND foo.art_item=ppto_item AND ppto_fecha='$f1'
					ORDER BY centro_nombre, art_item;
        				
        ", true);
        
   
        $tmp='';
   
		if(!$gastos) {
			print("<center><br /><br /><br /><h1>Sin Movimientos en el Periodo...</h1></center>");
		}
             
		if($gastos)
        for($i=0;$i<sizeof($gastos);$i++)
        {
            ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
            
            
            if($tmp!=$gastos[$i]['centro_nombre']) {
				
				print("<tr>
				<td colspan=8 class='tabla_header' style='font-size:20px;'>".$gastos[$i]['centro_nombre']."</td>
				</tr>
				
				<tr class='tabla_header' style='font-weight: bold;font-size:10px;'>
					<td style='width: 15%;font-size:10px;'>
						C&oacute;digo
					</td>
					<td style='width: 30%;font-size:10px;'>
						Item Presupuestario
					</td>
					<td style='width:5%;font-size:10px;'>
						Ppto. Mensual
					</td>
					<td style='width:5%;font-size:10px;'>
						Editar
					</td>
					<td style='width:5%;font-size:10px;'>
						Gasto
					</td>
					<td style='width:5%;font-size:10px;'>
						Saldo
					</td>
					<td>
						Gr&aacute;fico
					</td>
					<td>
						&nbsp;
					</td>
				</tr>
				
				");
				
				$tmp=$gastos[$i]['centro_nombre'];
			}
            
            $saldo=$gastos[$i]['ppto_monto']*1-$gastos[$i]['gasto'];
            
            if($saldo>0)
            $grafico=graficar_item(
					array(	$gastos[$i]['ppto_monto'],
							$gastos[$i]['gasto'],
							0,
							$gastos[$i]['ppto_monto']*1-$gastos[$i]['gasto']));
							
			else
			
			$grafico=graficar_item(
					array(	$gastos[$i]['ppto_monto'],
							0,
							$gastos[$i]['gasto'],
							$gastos[$i]['ppto_monto']*1-$gastos[$i]['gasto']));
			
            
            print("
            
            <tr class='".$clase."' style='height:25px;'
            onMouseOver='this.className=\"mouse_over\";'
            onMouseOut='this.className=\"".$clase."\";'>
				
				<td style='text-align:right;font-weight:bold;'>".$gastos[$i]['art_item']."</td>
				<td style='font-size:10px;'>".($gastos[$i]['item_glosa'])."</td>
				<td style='text-align:right;'>$".number_format($gastos[$i]['ppto_monto']*1,0,',','.').".-</td>
				<td>
					<center>
						<img src='iconos/pencil.png' onClick='modificar_ppto(\"".$gastos[$i]['centro_ruta']."\", \"".$gastos[$i]['centro_nombre']."\", \"".$gastos[$i]['art_item']."\");' style='cursor:pointer;' />
					</center>
				</td>
				<td style='text-align:right;'>$".number_format($gastos[$i]['gasto']*1,0,',','.').".-</td>
				<td style='text-align:right;'>$".number_format($saldo,0,',','.').".-</td>
				
				<td style='text-align:center;'>
				<center>".$grafico."</center></td>
				
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
