<?php

	require_once('../../conectar_db.php');
	    
	list($mes, $anio) = explode('/', $_POST['mesanio']);
	
	$f1=date('d/m/Y', mktime(0,0,0,$mes*1,1,$anio*1));
	$f2=date('d/m/Y', mktime(0,0,0,($mes*1)+1,1,$anio*1));
	    
    
?>

<table style='width: 100%;' cellspacing=0>
    <tr class='tabla_header' style='font-weight: bold;'>
        <td style='width: 15%;'>
            Folio
        </td>
        <td style='width: 15%;'>
            Fecha
        </td>
        <td style='width:45%;'>
            Descripci&oacute;n
        </td>
        <td>
            Total
        </td>
        <td>
            Detalle
        </td>
    </tr>
    <?php

        $certs = cargar_registros_obj("
        				SELECT *
        				FROM item_presupuestario_certificados
        				WHERE cert_fecha>='$f1' AND cert_fecha<'$f2'
						ORDER BY cert_fecha
						");
             
		if($certs)
        for($i=0;$i<sizeof($certs);$i++)
        {
            ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
            print("
            
            <tr class='".$clase."' style='height:25px;'
            onMouseOver='this.className=\"mouse_over\";'
            onMouseOut='this.className=\"".$clase."\";'>
				
				<td style='text-align:right;font-weight:bold;'>".$certs[$i]['cert_folio']."</td>
				<td style='text-align:center;'>".htmlentities(substr($certs[$i]['cert_fecha'],0,16))."</td>
				<td style='font-size:10px;'>".htmlentities($certs[$i]['cert_descripcion'])."</td>
				<td style='text-align:right;font-size:14px;'>$".number_format($certs[$i]['cert_monto'],0,',','.').".-</td>
							
				<td>
					<center>
						<img src='iconos/layout_edit.png' onClick='certificado(\"".$certs[$i]['cert_id']."\");' style='cursor:pointer;' />
						<img src='iconos/printer.png' onClick='imprimir_certificado(\"".$certs[$i]['cert_id']."\");' style='cursor:pointer;' />
					</center>
				</td>

            </tr>
            
            ");
        }

    ?>

</table>
