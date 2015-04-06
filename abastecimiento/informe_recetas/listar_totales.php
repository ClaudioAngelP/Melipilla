<?php 
    require_once('../../conectar_db.php');
    $fecha1=pg_escape_string($_POST['fecha1']);
    $fecha2=pg_escape_string($_POST['fecha2']);
    $fecha3=pg_escape_string($_POST['fecha3']);
    $h1=pg_escape_string($_POST['hora1']);
    $h2=pg_escape_string($_POST['hora2']);
    $func_id=$_POST['func_id']*1;
    
    if($func_id!='')
        $func_w=" log_func_if=$func_id ";
    else
        $func_w="true";
		
    $bodega=$_POST['bodega']*1;
    $tipo=$_POST['tipo'];
    
    if($h1!='') $h1l_w=$h1;
    else $h1l_w='00:00:00';

    if($h2!='') $h2l_w=$h2;
    else $h2l_w='23:59:59';
    
    
    $xls=isset($_POST['xls']);
    
    function number_format2($num, $dig, $c, $p){
        GLOBAL $xls;
        if(!$xls)
            return number_format($num, $dig, $c, $p);
        else
            return number_format($num, $dig, '.', '');
    }

    function number_format3($num, $dig, $c, $p)
    {
        GLOBAL $xls;
        if(!$xls)
            return '$'.number_format($num, $dig, $c, $p).'.-';
	else
            return number_format($num, $dig, '.', '');
    }
   
    if($tipo==1){
        $prod_item=$_POST['prod_item']*1;
        $w_item="";
        if($prod_item!=0) {
            $w_item="AND art_item='$prod_item'";
        }
			
	$query="
		SELECT foo3.*, UPPER(centro_nombre) AS centro_nombre, func_rut, upper(func_nombre) AS fnombre FROM (
		(SELECT 'R' AS tipo, receta_id, recetad_id, centro_ruta, total, stock_cant, (total+stock_cant) AS saldo_despacho, art_val_ult, receta_func_id FROM (
		SELECT
		receta_id, recetad_id, receta_centro_ruta AS centro_ruta,
		(
			CASE WHEN NOT receta_cronica THEN ceil(((recetad_dias*24)/recetad_horas)*recetad_cant/COALESCE(art_unidad_cantidad,1)) 
		ELSE 
		ceil((((recetad_dias*24)/recetad_horas)*recetad_cant/COALESCE(art_unidad_cantidad,1))/(recetad_dias/30::float)) 
		END
		) AS total
		,
		COALESCE(SUM(stock_cant),0) AS stock_cant, art_val_ult, receta_func_id
		FROM receta
		JOIN recetas_detalle ON recetad_receta_id=receta_id
		JOIN articulo ON recetad_art_id=art_id  $w_item
		LEFT JOIN logs ON log_recetad_id=recetad_id --AND log_fecha BETWEEN '$fecha1 00:00:00' AND '$fecha2 23:59:59'
		LEFT JOIN stock ON stock_log_id=log_id AND stock_art_id=recetad_art_id
		WHERE receta_id IN (
			SELECT DISTINCT receta_id FROM logs
		JOIN recetas_detalle ON log_recetad_id=recetad_id
		JOIN receta ON recetad_receta_id=receta_id
		WHERE log_fecha BETWEEN '$fecha1 00:00:00' AND '$fecha2 23:59:59' AND log_tipo=9 AND receta_bod_id=$bodega
		)
		GROUP BY receta_id, recetad_id, centro_ruta,receta_cronica, recetad_cant,art_unidad_cantidad, recetad_horas, recetad_dias, art_val_ult, receta_func_id
		) AS foo)
		/*
		UNION
		(
			SELECT 'P' AS tipo, pedido_id AS receta_id, 
		pedidod_id AS recetad_id, origen_centro_ruta AS centro_ruta, 
		pedidod_cant AS total, SUM(stock_cant) AS stock_cant, 
		(pedidod_cant+COALESCE(SUM(stock_cant),0)) AS saldo_despacho,
		art_val_ult, log_func_if AS receta_func_id
		FROM pedido_detalle
		JOIN pedido USING (pedido_id)
		JOIN articulo USING (art_id)
		LEFT JOIN logs ON log_id_pedido=pedido_id AND log_fecha BETWEEN '$fecha1 00:00:00' AND '$fecha2 23:59:59'
		LEFT JOIN stock ON stock_log_id=log_id AND stock_art_id=art_id AND stock_cant<0 AND stock_bod_id=$bodega
		WHERE pedido_fecha BETWEEN '$fecha1 00:00:00' AND '$fecha2 23:59:59' AND destino_bod_id=$bodega AND pedidod_cant>0
		GROUP BY pedido_id, pedidod_id, origen_centro_ruta, pedidod_cant, art_val_ult, log_func_if
		)
		*/
		)
		AS foo3
		left JOIN centro_costo USING (centro_ruta)
		JOIN funcionario ON receta_func_id=func_id
		ORDER BY receta_id, recetad_id
		";
		//print($query);  
        $q=pg_query($query);
	if(isset($_POST['xls']))
	{
            $b=cargar_registro("SELECT * FROM bodega WHERE bod_id=$bodega");
            header("Content-type: application/vnd.ms-excel");
            header("Content-Disposition: filename=\"InformeRecetas_".$b['bod_glosa'].".xls\";");
            print("<table>
            <tr>
                <td colspan=4><b>Informe de Recetas y Preescripciones</b></td>
            </tr>
            <tr>
                <td colspan=2>Ubicaci&oacute;n:</td>
		<td>".$b['bod_glosa']."</td>
            </tr>
            <tr>
                <td colspan=2>Fecha Inicial:</td>
		<td>".$fecha1."</td>
            </tr>
            <tr>
                <td colspan=2>Fecha Final:</td>
		<td>".$fecha2."</td>
            </tr>
            </table>
            ");
        }
        $receta=array();
        $recetas_ok=0;
        $recetas_no=0;
        $preescrip_ok=0;
        $preescrip_no=0;
        $servicios=Array();
        $funcs=Array();
        if(pg_fetch_row($q)<=0) {
            print("No hay recetas ingresadas en la bodega y fecha indicada");
            exit();
	}
	pg_result_seek($q,0);
	while($r=pg_fetch_assoc($q)) {
            $r['centro_nombre']=htmlentities($r['centro_nombre']);
            if(!isset($funcs[$r['func_rut']])) {
                $funcs[$r['func_rut']]['recetas']=Array();
                $funcs[$r['func_rut']]['nombre']=htmlentities($r['fnombre']);			
                $funcs[$r['func_rut']]['preescrip_ok']=0;			
                $funcs[$r['func_rut']]['recetas_ok']=0;			
            }
            if(!isset($servicios[$r['centro_nombre']])) {
                $servicios[$r['centro_nombre']]['recetas']=Array();
                $servicios[$r['centro_nombre']]['preescrip_no']=0;
                $servicios[$r['centro_nombre']]['preescrip_ok']=0;			
                $servicios[$r['centro_nombre']]['recetas_no']=0;
                $servicios[$r['centro_nombre']]['recetas_ok']=0;			
                $servicios[$r['centro_nombre']]['sumatoria']=0;			
            }
            if($r['receta_fecha_cierre']!=null or $r['receta_fecha_cierre']!="") {
                if(!isset($receta[$r['tipo'].''.$r['receta_id']]) OR $receta[$r['tipo'].''.$r['receta_id']]==0)
                        $receta[$r['tipo'].''.$r['receta_id']]=Array(0,$r['centro_nombre'],$r['func_rut']);
                $servicios[$r['centro_nombre']]['preescrip_ok']++;
                $preescrip_ok++;
            } else {
                if($r['saldo_despacho']*1>0) {
                    $receta[$r['tipo'].''.$r['receta_id']]=Array(1,$r['centro_nombre'],$r['func_rut']);
                    $servicios[$r['centro_nombre']]['preescrip_no']++;
                    $preescrip_no++;
                } else {
                    if(!isset($receta[$r['tipo'].''.$r['receta_id']]) OR $receta[$r['tipo'].''.$r['receta_id']]==0)
                        $receta[$r['tipo'].''.$r['receta_id']]=Array(0,$r['centro_nombre'],$r['func_rut']);
                    $servicios[$r['centro_nombre']]['preescrip_ok']++;
                    $preescrip_ok++;
		}
            }
            $servicios[$r['centro_nombre']]['sumatoria']+=$r['art_val_ult']*$r['total'];
            $funcs[$r['func_rut']]['preescrip_ok']++;
	}
		
	foreach($receta AS $key=>$val) {
            if($val[0]==1)
                $recetas_no++; 
            else
                $recetas_ok++;
            
            if($val[0]==1)
                $servicios[$val[1]]['recetas_no']++;
            else
                $servicios[$val[1]]['recetas_ok']++;
            
            $funcs[$val[2]]['recetas_ok']++;
        }

        print("<center>
        <div class='sub-content' style='font-size:16px;text-align:center;'>CANTIDAD DE RECETAS/PREESCRIPCIONES</div>
        <br><br>
            <table style='width:100%;'>
                <tr>
                    <td style='text-align:right;width:50%;'>Recetas Completas:</td>
                    <td style='font-weight:bold;'>".number_format2($recetas_ok,0,',','.')."</td>
		</tr>
		<tr>
                    <td style='text-align:right;'>Recetas Incompletas:</td>
                    <td style='font-weight:bold;'>".number_format2($recetas_no,0,',','.')."</td>
		</tr>
                <tr>
                    <td style='text-align:right;'>Preescripciones Completas:</td>
                    <td style='font-weight:bold;'>".number_format2($preescrip_ok,0,',','.')."</td>
		</tr>
		<tr>
                    <td style='text-align:right;'>Preescripciones Incompletas:</td>
                    <td style='font-weight:bold;'>".number_format2($preescrip_no,0,',','.')."</td>
                </tr>
		<tr>
                    <td style='text-align:right;'>Promedio de Preescripciones por Receta:</td>
                    <td style='font-weight:bold;'>".number_format2(($preescrip_ok+$preescrip_no)/($recetas_ok+$recetas_no),2,',','.')."</td>
		</tr>
            </table>
	<br /><br />
	<table style='width:100%;'>
            <tr class='tabla_header'>
                <td>Servicio</td>
		<td>Rec. Completas</td>
		<td>Rec. Incompletas</td>
		<td>Total Recetas</td>
		<td>Pres. Completas</td>
		<td>Pres. Incompletas</td>
		<td>Total Pres.</td>
		<td>Prom. Rec. $</td>
		<td>Prom. P. x R.</td>
            </tr>
	");
	$i=0;
	foreach($servicios AS $key=>$val){
            $clase=($i%2==0)?'tabla_fila':'tabla_fila2';
            print("
            <tr class='$clase'>
                <td style='font-weight:bold;'>".$key."</td>
                <td style='text-align:right;'>".number_format2($val['recetas_ok'],0,',','.')."</td>
                <td style='text-align:right;'>".number_format2($val['recetas_no'],0,',','.')."</td>
                <td style='text-align:right;'>".number_format2(($val['recetas_ok']*1)+($val['recetas_no']*1),0,',','.')."</td>
                <td style='text-align:right;'>".number_format2($val['preescrip_ok'],0,',','.')."</td>
                <td style='text-align:right;'>".number_format2($val['preescrip_no'],0,',','.')."</td>		
                <td style='text-align:right;'>".number_format2(($val['preescrip_ok']*1)+($val['preescrip_no']*1),0,',','.')."</td>
                <td style='text-align:right;'>".number_format3($val['sumatoria']/($val['recetas_ok']+$val['recetas_no']),0,',','.')."</td>		
                <td style='text-align:right;'>".number_format2(($val['preescrip_ok']+$val['preescrip_no'])/($val['recetas_ok']+$val['recetas_no']),2,',','.')."</td>		
            </tr>
            ");
            $i++;
	}
	
        print("</table>
        <br /><br />
        <table style='width:100%;'>
            <tr class='tabla_header'>
                <td>#</td>
		<td>RUT</td>
		<td>Nombres</td>
		<td>Total Recetas</td>
		<td>Total Pres.</td>
            </tr>
	");
        
        $i=0;
        foreach($funcs AS $key=>$val) {
            $clase=($i%2==0)?'tabla_fila':'tabla_fila2';
            print("<tr class='$clase'>
            <td>".($i+1)."</td>
            <td style='font-weight:bold;text-align:right;'>".$key."</td>
            <td style='font-weight:bold;'>".$val['nombre']."</td>
            <td style='text-align:right;'>".number_format2($val['recetas_ok'],0,',','.')."</td>
            <td style='text-align:right;'>".number_format2($val['preescrip_ok'],0,',','.')."</td>
            </tr>
            ");
            $i++;
	}
	print("</table></center>");
    } else {
        $query="SELECT art_codigo,art_glosa,COALESCE(calcular_stock(stock_art_id,stock_bod_id),0 )AS stock,
	-SUM(stock_cant)AS cant, COUNT(DISTINCT receta_id)AS nro_recetas,stock_art_id,array_agg(distinct receta_id)
	FROM stock
	LEFT JOIN logs ON log_id=stock_log_id
	JOIN recetas_detalle ON recetad_id=log_recetad_id
	LEFT JOIN receta ON recetad_receta_id=receta_id
	LEFT JOIN articulo ON art_id=stock_art_id
	WHERE art_control=1 AND stock_bod_id=$bodega AND $func_w AND
	log_fecha BETWEEN '$fecha3 $h1l_w' AND '$fecha3 $h2l_w'
	GROUP BY art_codigo,art_glosa,stock_art_id,stock_bod_id,log_func_if
	ORDER BY art_glosa";
        //print($query);
	
        $q=pg_query($query);
	if(pg_fetch_row($q)<=0){
            print("No hay recetas ingresadas en la bodega y fecha indicada");
            exit();
	}
        
	pg_result_seek($q,0);
	print("<table width=100% >
        <tr class='tabla_header' ><td>#</td>
            <td>C&oacute;digo</td>
            <td>Descripci&oacute;n</td>
            <td>Stock</td>
            <td>Despachado</td>
            <td>Nro Recetas</td>
	</tr>");
		
	$i=1;
	while($r=pg_fetch_assoc($q)) {
            $clase=($i%2==0)?'tabla_fila':'tabla_fila2';
            print("<tr class='$clase'>
            <td style='font-weight:bold;'>".$i."</td>
            <td style='text-align:right;'>".$r['art_codigo']."</td>
            <td style='text-align:left;'>".htmlentities($r['art_glosa'])."</td>
            <td style='text-align:right;'>".$r['stock']."</td>
            <td style='text-align:right;'>".$r['cant']."</td>
            <td style='text-align:right;'>".$r['nro_recetas']."</td>
            </tr>");
            $i++;			
        }
	print("</table>");
    }
?>
