<?php 
	require_once('../../conectar_db.php');
	
	$convenio_id=$_GET['convenio_id']*1;
        
        $reg_convenio=cargar_registro("SELECT * FROM convenio WHERE convenio_id=$convenio_id", true);
        //print_r($reg_convenio);
        
        if($reg_convenio['convenio_categoria']!='servicios') {
            pg_query("
            UPDATE convenio_detalle AS c1 SET 	
            conveniod_monto_utilizado=(
			SELECT SUM(stock_subtotal) FROM convenio AS c2
			JOIN convenio_detalle on convenio_Detalle.convenio_id=c2.convenio_id AND convenio_detalle.art_id=c1.art_id
			join orden_compra on orden_licitacion=convenio_licitacion AND orden_prov_id=prov_id
			JOIN documento ON doc_orden_id=orden_id OR doc_orden_desc=orden_numero
			JOIN logs ON log_tipo=1 AND log_doc_id=doc_id 
			JOIN stock ON stock_log_id=log_id AND stock_art_id=c1.art_id
			WHERE c2.convenio_id=c1.convenio_id
            ), 
	
            conveniod_cantidad_recepcionada=(
			SELECT SUM(stock_cant) FROM convenio AS c4
			JOIN convenio_detalle on convenio_Detalle.convenio_id=c4.convenio_id AND convenio_detalle.art_id=c1.art_id
			join orden_compra on orden_licitacion=convenio_licitacion AND orden_prov_id=prov_id
			JOIN documento ON doc_orden_id=orden_id OR doc_orden_desc=orden_numero
			JOIN logs ON log_tipo=1 AND log_doc_id=doc_id
			JOIN stock ON stock_log_id=log_id AND stock_art_id=c1.art_id
			WHERE c4.convenio_id=c1.convenio_id
            ), 
	
            conveniod_monto_comprometido=(
			SELECT SUM(ordetalle_subtotal) FROM convenio AS c3
			JOIN convenio_detalle on convenio_Detalle.convenio_id=c3.convenio_id AND convenio_detalle.art_id=c1.art_id
			join orden_compra on orden_licitacion=convenio_licitacion AND orden_prov_id=prov_id
			JOIN orden_detalle ON ordetalle_orden_id=orden_id AND ordetalle_art_id=c1.art_id
			WHERE c3.convenio_id=c1.convenio_id and orden_estado!=3 and orden_estado_portal!='OC Removida'
            ),
            conveniod_cant_com=(
            SELECT SUM(ordetalle_cant) FROM convenio AS c5
			JOIN convenio_detalle on convenio_Detalle.convenio_id=c5.convenio_id AND c1.art_id=convenio_detalle.art_id
			join orden_compra on orden_licitacion=convenio_licitacion AND orden_prov_id=prov_id
			JOIN orden_detalle ON ordetalle_orden_id=orden_id AND ordetalle_art_id=c1.art_id
			WHERE c5.convenio_id=c1.convenio_id  and orden_estado!=3 and orden_estado_portal!='OC Removida'
			and orden_estado_portal!='OC Requerida para Cancelaci�n'
            )	
		
            WHERE convenio_id=$convenio_id;
            ");
        }
        else {
            
            pg_query("
            UPDATE convenio_detalle AS c1 SET 	
            conveniod_monto_utilizado=(
			SELECT SUM(serv_subtotal) FROM convenio AS c2
			JOIN convenio_detalle on convenio_detalle.convenio_id=c2.convenio_id AND convenio_detalle.art_id=c1.art_id
			JOIN orden_compra on orden_licitacion=convenio_licitacion AND orden_prov_id=prov_id
			JOIN documento ON doc_orden_id=orden_id OR doc_orden_desc=orden_numero
			JOIN logs ON log_tipo=50 AND log_doc_id=doc_id 
                        JOIN servicios ON serv_log_id=log_id AND serv_art_id=c1.art_id
			WHERE c2.convenio_id=c1.convenio_id
            ), 
	
            conveniod_cantidad_recepcionada=(
			SELECT SUM(serv_cant) FROM convenio AS c4
			JOIN convenio_detalle on convenio_detalle.convenio_id=c4.convenio_id AND convenio_detalle.art_id=c1.art_id
			JOIN orden_compra on orden_licitacion=convenio_licitacion AND orden_prov_id=prov_id
			JOIN documento ON doc_orden_id=orden_id OR doc_orden_desc=orden_numero
			JOIN logs ON log_tipo=50 AND log_doc_id=doc_id
                        JOIN servicios ON serv_log_id=log_id AND serv_art_id=c1.art_id
			WHERE c4.convenio_id=c1.convenio_id
            ), 
	
            conveniod_monto_comprometido=(
			SELECT SUM(ordetalle_subtotal) FROM convenio AS c3
			JOIN convenio_detalle on convenio_detalle.convenio_id=c3.convenio_id AND convenio_detalle.art_id=c1.art_id
			JOIN orden_compra on orden_licitacion=convenio_licitacion AND orden_prov_id=prov_id
			JOIN orden_detalle ON ordetalle_orden_id=orden_id AND ordetalle_art_id=c1.art_id
			WHERE c3.convenio_id=c1.convenio_id and orden_estado!=3 and orden_estado_portal!='OC Removida' and orden_estado_portal!='OC Requerida para Cancelaci�n'
            ),
            conveniod_cant_com=(
            SELECT SUM(ordetalle_cant) FROM convenio AS c5
			JOIN convenio_detalle on convenio_Detalle.convenio_id=c5.convenio_id AND c1.art_id=convenio_detalle.art_id
			join orden_compra on orden_licitacion=convenio_licitacion AND orden_prov_id=prov_id
			JOIN orden_detalle ON ordetalle_orden_id=orden_id AND ordetalle_art_id=c1.art_id
			WHERE c5.convenio_id=c1.convenio_id  and orden_estado!=3 and orden_estado_portal!='OC Removida' and orden_estado_portal!='OC Requerida para Cancelaci�n'
            )	
		
            WHERE convenio_id=$convenio_id and art_id!=0;
            ");
            
            $tmp=cargar_registro("
            SELECT *, 
            (SELECT COUNT(*) FROM convenio_detalle WHERE convenio_detalle.convenio_id=fooo.convenio_id) AS arts, 
            (SELECT COUNT(*) FROM orden_compra WHERE orden_licitacion=convenio_licitacion AND fooo.prov_id=orden_prov_id) AS ocs 
            FROM (
                SELECT DISTINCT c1.*, 
                (convenio_fecha_final-CURRENT_DATE) AS dias_vigencia,
                prov_glosa,
                upper(prov_rut)as prov_rut 
                FROM convenio AS c1
                JOIN proveedor USING (prov_id)
                WHERE convenio_id=$convenio_id
                ORDER BY c1.convenio_nombre
            ) AS fooo 
            ORDER BY convenio_nombre");
            if($tmp){
                if(($tmp['ocs']*1)>0){
                    $tmp1=cargar_registro("SELECT * from convenio_detalle WHERE convenio_detalle.convenio_id=$convenio_id AND art_id=0");
                    if(!$tmp1) {
                        pg_query($conn, "INSERT INTO convenio_detalle VALUES (".$convenio_id.", 0, DEFAULT, 0,null,null,null,null,0)");
                    }
                }
            }
            pg_query("UPDATE convenio_detalle AS c1 SET 	
            conveniod_monto_utilizado=(
                SELECT SUM(serv_subtotal) FROM convenio AS c2
		JOIN convenio_detalle on convenio_detalle.convenio_id=c2.convenio_id AND convenio_detalle.art_id=c1.art_id
		JOIN orden_compra on orden_licitacion=convenio_licitacion AND orden_prov_id=prov_id
		JOIN documento ON doc_orden_id=orden_id OR doc_orden_desc=orden_numero
		JOIN logs ON log_tipo=50 AND log_doc_id=doc_id 
		JOIN servicios ON serv_log_id=log_id AND serv_art_id=c1.art_id
		WHERE c2.convenio_id=c1.convenio_id
            ), 
            conveniod_cantidad_recepcionada=(
                SELECT SUM(serv_cant) FROM convenio AS c4
		JOIN convenio_detalle on convenio_detalle.convenio_id=c4.convenio_id AND convenio_detalle.art_id=c1.art_id
		JOIN orden_compra on orden_licitacion=convenio_licitacion AND orden_prov_id=prov_id
		JOIN documento ON doc_orden_id=orden_id OR doc_orden_desc=orden_numero
		JOIN logs ON log_tipo=50 AND log_doc_id=doc_id
		JOIN servicios ON serv_log_id=log_id AND serv_art_id=c1.art_id
		WHERE c4.convenio_id=c1.convenio_id 
            ), 
            conveniod_monto_comprometido=(
                SELECT SUM(orserv_subtotal) FROM convenio AS c3
		JOIN convenio_detalle on convenio_Detalle.convenio_id=c3.convenio_id AND convenio_detalle.art_id=c1.art_id
		JOIN orden_compra ON orden_licitacion=convenio_licitacion AND orden_prov_id=prov_id
		JOIN orden_servicios ON orserv_orden_id=orden_id 
		WHERE c3.convenio_id=c1.convenio_id  and orden_estado!=3 and orden_estado_portal!='OC Removida' and orden_estado_portal!='OC Requerida para Cancelaci�n'
            ),
            conveniod_cant_com=(
                SELECT SUM(orserv_cant) FROM convenio AS c5
		JOIN convenio_detalle on convenio_Detalle.convenio_id=c5.convenio_id AND convenio_detalle.art_id=c1.art_id
		JOIN orden_compra on orden_licitacion=convenio_licitacion
		JOIN orden_servicios ON orserv_orden_id=orden_id
		WHERE c5.convenio_id=c1.convenio_id  and orden_estado!=3 and orden_estado_portal!='OC Removida' and orden_estado_portal!='OC Requerida para Cancelaci�n'
            ),
            conveniod_punit=
            (
                case when 
                (
                    SELECT SUM(orserv_cant) FROM convenio AS c5
                    JOIN convenio_detalle on convenio_Detalle.convenio_id=c5.convenio_id AND convenio_detalle.art_id=c1.art_id
                    JOIN orden_compra on orden_licitacion=convenio_licitacion
                    JOIN orden_servicios ON orserv_orden_id=orden_id
                    WHERE c5.convenio_id=c1.convenio_id  and orden_estado!=3 and orden_estado_portal!='OC Removida' and orden_estado_portal!='OC Requerida para Cancelaci�n'
                )=0 then 0
                else
                (
                    (
                    SELECT SUM(orserv_subtotal) FROM convenio AS c3
                    JOIN convenio_detalle on convenio_Detalle.convenio_id=c3.convenio_id AND convenio_detalle.art_id=c1.art_id
                    JOIN orden_compra ON orden_licitacion=convenio_licitacion AND orden_prov_id=prov_id
                    JOIN orden_servicios ON orserv_orden_id=orden_id 
                    WHERE c3.convenio_id=c1.convenio_id  and orden_estado!=3 and orden_estado_portal!='OC Removida' and orden_estado_portal!='OC Requerida para Cancelaci�n'
                    )
                    /
                    (
                        SELECT SUM(orserv_cant) FROM convenio AS c5
                        JOIN convenio_detalle on convenio_Detalle.convenio_id=c5.convenio_id AND convenio_detalle.art_id=c1.art_id
                        JOIN orden_compra on orden_licitacion=convenio_licitacion
                        JOIN orden_servicios ON orserv_orden_id=orden_id
                        WHERE c5.convenio_id=c1.convenio_id  and orden_estado!=3 and orden_estado_portal!='OC Removida' and orden_estado_portal!='OC Requerida para Cancelaci�n'
                    )
                )
                end
            )
            WHERE convenio_id=$convenio_id and art_id=0;");
            
            $tmp=cargar_registro("
            SELECT SUM(doc_descuento)as total_descuento from (
                SELECT distinct doc_descuento FROM convenio AS c2
                JOIN convenio_detalle on convenio_detalle.convenio_id=c2.convenio_id AND convenio_detalle.art_id=0
                JOIN orden_compra on orden_licitacion=convenio_licitacion AND orden_prov_id=prov_id
                JOIN documento ON doc_orden_id=orden_id OR doc_orden_desc=orden_numero
                JOIN logs ON log_tipo=50 AND log_doc_id=doc_id 
                JOIN servicios ON serv_log_id=log_id AND serv_art_id=0
                WHERE c2.convenio_id=$convenio_id
            )as foo");
            if($tmp){
                pg_query("UPDATE convenio_detalle SET conveniod_monto_utilizado=(conveniod_monto_utilizado-".($tmp['total_descuento']*1).") WHERE convenio_id=$convenio_id and art_id=0;");
            }
        }
	$c=cargar_registro("
		SELECT * FROM convenio 
		JOIN proveedor USING (prov_id)
		LEFT JOIN funcionario USING (func_id)
		WHERE convenio_id=$convenio_id
	", true);
	
	$d=cargar_registros_obj("
		SELECT * FROM convenio_detalle 
                LEFT JOIN articulo USING(art_id)
		WHERE convenio_id=$convenio_id
		ORDER BY art_glosa;
	", true);


	$l=pg_query("SELECT * FROM convenio_adjuntos WHERE convenio_id=$convenio_id");
	$adj_con='';
        if($l) {
            $adj_con.="<table>";
            for($i=0;$i<pg_num_rows($l);$i++) {
                $adjunto = pg_fetch_assoc($l);
                list($nombre,$tipo,$peso,$md5)=explode('|',$adjunto['cad_adjunto']);
                $adj_con.="<tr class='$clase' onMouseOver='this.className=\"mouse_over\"' onMouseOut='this.className=\"$clase\"'>
                <table style='cursor:pointer;border:1px solid black;background-color:white;font-size:12px;' onClick='window.open(\"descargar_adjunto.php?adjunto_id=".$adjunto['cad_id']."\", \"_self\");'>
                    <tr>
                        <td><i>Archivo:</i></td>
                        <td><img src='../../iconos/application_put.png'></td>
                        <td><b><u>".$nombre."</u></b></td>
                        <td><i>(".number_format($peso/1024,1,',','.')." Kb)</i></td>
                        <td>.</td>
                    </tr>
                </table> 
                </tr>";
            }
            $adj_con.="</table>";
        }
        if(isset($_GET['xls'])) {
            header("Content-type: application/vnd.ms-excel");
            header("Content-Disposition: filename=\"HistorialPedidos--.XLS\";");
  	}
?>
<html>
    <title>Visualizar Convenio</title>
    <?php cabecera_popup('../..'); ?>
    <script>
        function ver_detalle(conveniod_id)  {
            window.open('ver_convenio_detalle.php?conveniod_id='+conveniod_id,'win_talonarios');	
        }
        function ver_multas(convenio_id)  {
            window.open('ver_multas_detalle.php?convenio_id='+convenio_id,'win_talonarios');
        }
        function ver_hitoricos(convenio_id)  {
            window.open('ver_historico_detalle.php?convenio_id='+convenio_id,'win_talonarios');
        }
    </script>
<body class='fuente_por_defecto popup_background'>
<?php
    if(!isset($_GET['xls'])){
        print("<center>");
    }?>
    <h1><?php echo $c['convenio_licitacion']; ?></h1>
    <h2><?php echo $c['convenio_nombre']; ?></h2>
    <h3>Proveedor: <?php echo '<b>'.$c['prov_rut'].'</b> '.$c['prov_glosa']; ?><br />
    Monto Total: $<?php echo number_format($c['convenio_monto']*1,0,',','.'); ?>.-
    </h3>
    <table style='width:100%;font-size:11px;'>
        <tr>
            <td style='text-align:right;' class='tabla_fila2'>ID Licitaci&oacute;n:</td>
            <td>
		<?php echo $c['convenio_licitacion']; ?>
            </td>
            <td style='text-align:right;' class='tabla_fila2'>Categoria:</td>
            <td>
		<?php echo utf8_decode(strtoupper($c['convenio_categoria'])); ?>
            </td>
        </tr>
        <tr>
            <td style='text-align:right;' class='tabla_fila2'>Tipo Convenio:</td>
            <td colspan="3">
		<?php 
			if($c['convenio_tipo_licitacion'] == 1) echo strtoupper("Apoyo Cl�nico");
			if($c['convenio_tipo_licitacion'] == 2) echo strtoupper("Recursos F�sicos");
			if($c['convenio_tipo_licitacion'] == 3) echo strtoupper("Prestaci�n de Servicios Cl�nicos"); 
		 ?>
            </td>
        </tr>
        <tr>
            <td style='text-align:right;' class='tabla_fila2'>Nombre Convenio:</td>
            <td colspan=3>
            	<?php echo $c['convenio_nombre']; ?>
            </td>
        </tr>
        <tr>
            <td style='text-align:right;' class='tabla_fila2'>Nro. Res. Aprueba Bases:</td>
            <td>
		<?php echo $c['convenio_nro_res_aprueba']; ?>
            </td>
            <td style='text-align:right;' class='tabla_fila2'>Fecha:</td>
            <td>
		<?php echo $c['convenio_fecha_aprueba']; ?>
            </td>
        </tr>
        <tr>
            <td style='text-align:right;' class='tabla_fila2'>Nro. Res. Adjudica:</td>
            <td>
		<?php echo $c['convenio_nro_res_adjudica']; ?>
            </td>
            <td style='text-align:right;' class='tabla_fila2'>Fecha:</td>
            <td>
                <?php echo $c['convenio_fecha_adjudica']; ?>
            </td>
        </tr>
        
        <?php if($c['convenio_aprueba']=='contrato'){ ?>
        <tr>
            <td style='text-align:right;' class='tabla_fila2'>Nro. Res. Aprueba Contrato:</td>
            <td>
                <?php echo $c['convenio_nro_res_prorroga']; ?>
            </td>
            <td style='text-align:right;' class='tabla_fila2'>Fecha:</td>
            <td>
                <?php echo $c['convenio_fecha_prorroga']; ?>
            </td> 
        </tr>
	 <?php } elseif($c['convenio_aprueba']=='prorroga'){ ?>
        <tr>
            <td style='text-align:right;' class='tabla_fila2'>Nro. Res. Aprueba Prorroga:</td>
            <td>
                <?php echo $c['convenio_nrores_prorroga']; ?>
            </td>
            <td style='text-align:right;' class='tabla_fila2'>Fecha:</td>
            <td>
                <?php echo $c['convenio_fecha_resprorroga']; ?>
            </td>
        </tr>
    <?php } elseif($c['convenio_aprueba']=='aumento') { ?>
        <tr  id='td_res_aumento' name='td_res_aumento'>
            <td style='text-align:right;' class='tabla_fila2'>Num. Res. Aprueba Aumento:</td>
            <td>
                <?php echo $c['convenio_aumento_aprueba']; ?>
            </td>
            <td style='text-align:right;' class='tabla_fila2'>Fecha:</td>
            <td>
                <?php echo $c['convenio_aumento_fecha']; ?>
            </td>
        </tr>
    <?php } ?>
        <tr>
            <td style='text-align:right;' class='tabla_fila2'>Nro. Res. Aprueba:</td>
            <td><b><?= strtoupper($c['convenio_aprueba'])."</b> ".$c['convenio_nro_res_prorroga'];?></td>
            <td style='text-align:right;' class='tabla_fila2'>Fecha:</td>
            <td><?= $c['convenio_fecha_prorroga']; ?></td>
        </tr>
        <tr id='td_res_apruebac' name='td_res_apruebac' style='display:none;'>
            <td style='text-align:right;' class='tabla_fila2'>Nro. Res. Aprueba Contrato:</td>
            <td>
                <?php echo $c['convenio_nro_res_contrato']; ?>
            </td>
            <td style='text-align:right;' class='tabla_fila2'>Fecha:</td>
            <td>
                <?php echo $c['convenio_fecha_resolucion']; ?>
            </td>
        </tr>
        <tr>
            <td style='text-align:right;' class='tabla_fila2'>Proveedor:</td>
            <td colspan=3>
                <b><?php echo $c['prov_rut']; ?></b>
                <?php echo $c['prov_glosa']; ?>
            </td>
        </tr>
        <tr>
            <td style='text-align:right;' class='tabla_fila2'>Administrador Contrato:</td>
            <td colspan=3>
                <b><?php echo $c['func_rut']; ?></b>
                <?php echo $c['func_nombre']; ?>
            </td>
        </tr>
        <tr>
            <td style='text-align:right;' class='tabla_fila2'>e-mail(s) Contacto:</td>
            <td colspan=3>
                <?php echo $c['convenio_mails']; ?>
            </td>
        </tr>
        <tr>
            <td style='text-align:right;' class='tabla_fila2'>Monto $:</td>
            <td>
                <?php echo $c['convenio_monto']; ?>
            </td>
            <td style='text-align:right;' class='tabla_fila2'>Plazo de Entrega (D&iacute;as):</td>
            <td>
                <?php echo $c['convenio_plazo']; ?>
            </td>
        </tr>
        <tr>
            <?php if($c['convenio_aprueba']=='aumento'){ ?>
            <td style='text-align:right;' class='tabla_fila2'>Monto Aumento $:</td>
            <td>
                <?php echo $c['convenio_monto_aumento']; ?>
            </td>
            <?php } else{ 
                    print("<td colspan=2></td>");
            }?>
            <td style='text-align:right;' class='tabla_fila2'>Duraci&oacute;n (Meses):</td>
            <td>
                <?php echo $c['convenio_plazo2']; ?>
            </td>
        </tr>
        <tr>
            <td style='text-align:right;' class='tabla_fila2'>Fecha Inicio:</td>
            <td>
                <?php echo $c['convenio_fecha_inicio']; ?>
            </td>
            <td style='text-align:right;' class='tabla_fila2'>Fecha T&eacute;rmino:</td>
            <td>
            <?php echo $c['convenio_fecha_final']; ?>
            </td>
        </tr>
        <tr>
            <td style='text-align:right;' class='tabla_fila2'>N&uacute;mero Boleta Garant&iacute;a:</td>
            <td>
                <?php echo $c['convenio_nro_boleta']; ?>
            </td>
            <td style='text-align:right;' class='tabla_fila2'>Fecha Venc. Boleta Garant&iacute;a:</td>
            <td>
                <?php echo $c['convenio_fecha_boleta']; ?>
            </td>
        </tr>
        <tr>
            <td style='text-align:right;' class='tabla_fila2'>Banco Boleta Garant&iacute;a:</td>
            <td>
                <?php echo $c['convenio_banco_boleta']; ?>
            </td>
            <td style='text-align:right;' class='tabla_fila2'>Tipo de Garant&iacute;a:</td>
            <td>
            <?php 
                if($c['convenio_tipo_garantia'] == 1) echo utf8_decode(strtoupper("Poliza de Seguro"));
                if($c['convenio_tipo_garantia'] == 2) echo utf8_decode(strtoupper("Certificado de Fianza"));
                if($c['convenio_tipo_garantia'] == 3) echo utf8_decode(strtoupper("Boleta Bancaria"));
                if($c['convenio_tipo_garantia'] == 4) echo utf8_decode(strtoupper("Vale Vista"));
                if($c['convenio_tipo_garantia'] == 5) echo utf8_decode(strtoupper("Deposito a la Vista"));
            ?>
            </td>
        </tr>
        <tr>
            <td style='text-align:right;' class='tabla_fila2'>Monto Boleta $:</td>
            <td>
                <?php echo $c['convenio_monto_boleta']; ?>
            </td>
            <td style='text-align:right;' class='tabla_fila2'>Multa (Descripci&oacute;n):</td>
            <td colspan=3>
                <?php echo $c['convenio_multa']; ?>
            </td>
        </tr>
        <tr>
            <td style='text-align:right;' class='tabla_fila2'>Comentarios:</td>
            <td colspan=3>
                <?php echo $c['convenio_comentarios']; ?>
            </td>
        </tr>
        <?php
        if(!isset($_GET['xls'])) {
    	?>
        <tr>
            <td style='text-align:right;'>Adjuntos:</td>
            <td>
                <?php echo $adj_con; ?>
            </td>
        </tr>
        <? } ?>
    </table>
    <br>
    <table style='width:100%;font-size:12px;'>
        <tr class='tabla_header'>
            <td>C&oacute;digo</td>
            <td style='width:50%;'>Art&iacute;culo</td>
            <td>Comprometido ($)</td>
            <td>Unidades Estimadas</td>	
            <td>Unidades Comprometidas</td>
            <td>Unidades Recepcionadas</td>
            <td>PU Conv.($)</td>
            <td>PU Recep.($)</td>
            <td>Devengado ($)</td>
            <td>&nbsp;</td>
	</tr>
        <?php 
        $mc=0;
        $md=0;
	if($d)
            for($i=0;$i<sizeof($d);$i++) {
                $clase=$i%2==0?'tabla_fila':'tabla_fila2';
                if($d[$i]['conveniod_cantidad_recepcionada']!=0)
                    $unit=$d[$i]['conveniod_monto_utilizado']/$d[$i]['conveniod_cantidad_recepcionada'];
		else
                    $unit=0;
		
		if($d[$i]['conveniod_cant_com']!=0) {
                    if($d[$i]['conveniod_cant']!=0) {
                        $unidUtil=$d[$i]['conveniod_cant_com']/$d[$i]['conveniod_cant'];
                    } else {
                        $unidUtil=0;
                    }		
                } else {
                    $unidUtil=0;
		}
                $mc+=$d[$i]['conveniod_monto_comprometido']*1.19;
		$md+=$d[$i]['conveniod_monto_utilizado']*1.19;
		
		if($unit==0) {
                    $color='blue';
		} elseif($unit==$d[$i]['conveniod_punit']) {
                    $color='green';
		} else {
                    $color='red';
		}
                if($unidUtil==0) {
                    $colorU='blue';
		} elseif($unidUtil==1) {
                    $colorU='green';
		} else {
                    $colorU='red';
		}
	
		print("<tr class='$clase'>");
                    if(($d[$i]['art_id']*1)==0){
                        print("<td style='text-align:right;font-weight:bold;color:red'>SIN ASIGNAR</td>");
                        print("<td style='text-align:left;font-weight:bold;color:red'>RECEPCION DE SERVICIOS</td>");
                    }else {
                        print("<td style='text-align:right;'>".$d[$i]['art_codigo']."</td>");
                        print("<td>".$d[$i]['art_glosa']."</td>");
                    }
                    print("<td style='text-align:right;'>$".number_format($d[$i]['conveniod_monto_comprometido']*1,0,',','.').".-</td>");
                    print("<td style='text-align:right;'>".number_format($d[$i]['conveniod_cant']*1,0,',','.')."</td>");
                    print("<td style='text-align:right;color:$colorU'>".number_format($d[$i]['conveniod_cant_com']*1,0,',','.')."</td>");
                    print("<td style='text-align:right;'>".number_format($d[$i]['conveniod_cantidad_recepcionada']*1,0,',','.')."</td>");
                    print("<td style='text-align:right;'>$".number_format($d[$i]['conveniod_punit']*1,0,',','.').".-</td>");
                    print("<td style='text-align:right;font-weight:bold;color:$color'>$".number_format($unit,2,',','.').".-</td>");
                    print("<td style='text-align:right;'>$".number_format($d[$i]['conveniod_monto_utilizado']*1,0,',','.').".-</td>");
                    print("<td>");
                        print("<center>");
                            print("<img src='../../iconos/magnifier.png' onClick='ver_detalle(".$d[$i]['conveniod_id'].");' style='cursor:pointer;' />");
                        print("</center>");
                    print("</td>");
                print("</tr>");
            }
        $pc=$mc*100/$c['convenio_monto']*1;
        $pd=$md*100/$c['convenio_monto']*1;
        print("
        <tr class='tabla_header'>
            <td colspan=2 style='text-align:right;'>Total Comprometido + iva:</td>
            <td style='text-align:right;font-weight:bold;'>$".number_format($mc,0,',','.').".-</td>
            <td colspan=5 style='text-align:right;'>Total Devengado  + iva:</td>
            <td style='text-align:right;font-weight:bold;'>$".number_format($md,0,',','.').".-</td>
            <td>&nbsp;</td>
        </tr>
        <tr class='tabla_header'>
            <td colspan=2 style='text-align:right;'>Comprometido:</td>
            <td style='text-align:right;font-weight:bold;'>".number_format($pc,2,',','.')."%</td>
            <td colspan=5 style='text-align:right;'>Devengado:</td>
            <td style='text-align:right;font-weight:bold;'>".number_format($pd,2,',','.')."%</td>
            <td>&nbsp;</td>
        </tr>
        ");
        ?>	
    </table>
    <center>
        <br/>
        <br/>
        <input type='button' value='[ IMPRIMIR CONVENIO ]' onClick='window.print();'>
        <!--
        <input type='button' value='[ EXPORTAR A EXCEL ]' onClick='exportar_excel();'>
        -->
        <input type='button' value='[ VER MULTAS ]' onClick='ver_multas(<?php echo $convenio_id ?>);'>
        <input type='button' value='[ VER HIST&Oacute;RICO ]' onClick='ver_hitoricos(<?php echo $convenio_id ?>);'>
        <br/>
        <br/>
    </center>
</body>
</html>
<script> 
    exportar_excel = function(){
        nuevaVentana=window.open('exportar_ver_convenio.php?convenio_id='+<?php echo $convenio_id;?>,'',"toolbar=0,location=0,status=0,width=600,height=400");
    }	
</script>