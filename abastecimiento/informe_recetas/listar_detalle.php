<?php
    require_once('../../conectar_db.php');
    $fecha1=pg_escape_string($_POST['fecha1']);
    $fecha2=pg_escape_string($_POST['fecha2']);
    $fecha3=pg_escape_string($_POST['fecha3']);
    $bodega=$_POST['bodega']*1;
    $h1=pg_escape_string($_POST['hora1']);
    $h2=pg_escape_string($_POST['hora2']);
     
    if($h1!='') $h1l_w=$h1;
    else $h1l_w='00:00:00';

    if($h2!='') $h2l_w=$h2;
    else $h2l_w='23:59:59';
    
    $prod_item=$_POST['prod_item']*1;
    $func_id=$_POST['func_id'];
    
    if($func_id!='')
		$func_w=" log_func_if=$func_id ";
	else
		$func_w="true";
		
    $w_item="";
    $tipo=$_POST['tipo'];
    
    if($prod_item!=0)
        $w_item="AND art_item='$prod_item'";
    
    if($h1!='')
        $h1p_w="pedido_fecha::time>='$h1'";
    else
        $h1p_w='true';

    if($h2!='')
        $h2p_w="pedido_fecha::time<='$h2'";
    else
        $h2p_w='true';
		
    if($h1!='')
        $h1l_w="log_fecha::time>='$h1'";
    else
        $h1l_w='true';

    if($h2!='')
        $h2l_w="log_fecha::time<='$h2'";
    else
        $h2l_w='true';
	
	
    if($fecha1==$fecha2)
        {
            $camp_parcial="total_parcial,stock_cant_parcial,(total_parcial+stock_cant_parcial)AS saldo_despacho_parcial,";
            $w_parcial="
            ( 
                CASE WHEN recetad_dias<30 THEN 
                    ceil(((recetad_dias*24)/recetad_horas)*recetad_cant/COALESCE(art_unidad_cantidad,1)) 
                ELSE 
                    ceil((((recetad_dias*24)/recetad_horas)*recetad_cant/COALESCE(art_unidad_cantidad,1))/(recetad_dias/30)) 
                END 
            ) AS total_parcial,
            COALESCE
            (
                (
                select 
                sum(stock_cant) 
                from stock where stock_log_id in (
                                                    select 
                                                    log_id 
                                                    from logs 
                                                    where log_fecha BETWEEN '$fecha1 00:00:00' AND '$fecha2 23:59:59'
                                                    AND log_tipo=9 
                                                    and log_recetad_id=recetad_id 
                                                )
                and stock_art_id=recetad_art_id
                )
                ,
                0
            )
            as stock_cant_parcial,";
        }
        else
        {
            $camp_parcial="";
            $w_parcial="";
        }
        
	
        $query="SELECT foo3.*, 
        UPPER(COALESCE(centro_nombre,bod_glosa)) AS centro_nombre,
        func_rut,upper(func_nombre) AS fnombre 
        FROM
        (
            SELECT 
            'R' AS tipo,
            receta_id,
            recetad_id,
            centro_ruta,
            $camp_parcial
            total,stock_cant,(total+stock_cant) AS saldo_despacho,
            art_val_ult,receta_func_id,pac_rut,pac_nombre,art_codigo,art_glosa,art_item,item_glosa,forma_nombre,
            receta_func_id2,
            receta_fecha_cierre,
            receta_motivo_termino
            FROM
            (
		SELECT 
		receta_numero AS receta_id,
		receta_centro_ruta AS centro_ruta,
                $w_parcial
		recetad_id,
		(
			CASE WHEN recetad_dias<30 THEN 
				ceil(((recetad_dias*24)/recetad_horas)*recetad_cant/COALESCE(art_unidad_cantidad,1))
			ELSE
                                
                                ceil((((recetad_dias*24)/recetad_horas)*recetad_cant/COALESCE(art_unidad_cantidad,1)))
			END
		) AS total,
		COALESCE(SUM(stock_cant),0) AS stock_cant,
		art_val_ult,
		receta_func_id,
		pac_rut,
		pac_nombres||' '||pac_appat||' '||pac_apmat As pac_nombre,
		art_codigo,
		art_glosa,
                art_item,
                item_glosa,
                forma_nombre,
                receta_func_id2,
                receta_fecha_cierre,
                receta_motivo_termino
		FROM receta
		JOIN recetas_detalle ON recetad_receta_id=receta_id 
		JOIN articulo ON recetad_art_id=art_id $w_item
		LEFT JOIN logs ON log_recetad_id=recetad_id
		LEFT JOIN stock ON stock_log_id=log_id AND stock_art_id=recetad_art_id
		LEFT JOIN pacientes ON receta_paciente_id=pac_id
                LEFT JOIN bodega_forma on art_forma=forma_id
                LEFT JOIN item_presupuestario on item_codigo=art_item
		where receta_id in
		(
			SELECT DISTINCT receta_id ";
			
			if($tipo==1){ 
                $query.=" FROM logs 
				JOIN recetas_detalle ON log_recetad_id=recetad_id 
				JOIN receta ON recetad_receta_id=receta_id 
				WHERE (log_fecha BETWEEN '$fecha1 00:00:00' AND '$fecha2 23:59:59')
                AND ($h1l_w AND $h2l_w)
				AND log_tipo=9 AND receta_bod_id=$bodega";
			}else{
				if($h1!='') $h1l_w=$h1;
				else $h1l_w='00:00:00';

				if($h2!='') $h2l_w=$h2;
				else $h2l_w='23:59:59';
				$query.=" FROM stock
				LEFT JOIN logs ON log_id=stock_log_id
				 JOIN recetas_detalle ON recetad_id=log_recetad_id
				LEFT JOIN receta ON recetad_receta_id=receta_id
				LEFT JOIN articulo ON art_id=stock_art_id
				WHERE art_control=1 AND stock_bod_id=$bodega AND $func_w AND
				log_fecha BETWEEN '$fecha3 $h1l_w' AND '$fecha3 $h2l_w'";
			}
		$query.="
		)
		group by recetad_id,receta_numero,art_unidad_cantidad,centro_ruta,art_val_ult,receta_func_id,pac_rut,pac_nombres,pac_appat,pac_apmat,art_codigo,art_glosa,art_item,item_glosa,forma_nombre,receta_func_id2,receta_fecha_cierre,receta_motivo_termino
                )AS foo
        )AS foo3
        LEFT JOIN centro_costo USING (centro_ruta) 
        LEFT JOIN bodega ON bod_glosa=centro_ruta 
        JOIN funcionario ON receta_func_id=func_id 
        ORDER BY receta_id, recetad_id";
        
        //print($query);
        //die();
        
	$q=pg_query($query);
        
        if(isset($_POST['opcion'])){
            if(($_POST['opcion']*1)==1){
                $xls=false;
            } else {
                $xls=true;
            }
            
            
        } else {
            $xls=true;
        }
	
        

	function number_format2($num, $dig, $c, $p)
        {
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

        $b=cargar_registro("SELECT * FROM bodega WHERE bod_id=$bodega");
        if($xls){
            header("Content-type: application/vnd.ms-excel");
            header("Content-Disposition: filename=\"InformeRecetas_".$b['bod_glosa'].".xls\";");
        }
        if($xls){
            print("
            <table>
                <tr>
                    <td colspan=4><b>Informe de Recetas y Prescripciones</b></td>
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
        
        $preescrip_ok_dia=0;
	$preescrip_no_dia=0;
	
	$servicios=Array();
        
	$funcs=Array();
        
        if(pg_fetch_row($q)<=0)
        {
            print("No hay recetas ingresadas en la bodega y fecha indicada");
            exit();
        
        }
        
        pg_result_seek($q,0);
	while($r=pg_fetch_assoc($q))
        {
            $r['centro_nombre']=htmlentities($r['centro_nombre']);
            //------------------------------------------------------------------
            if(!isset($funcs[$r['func_rut']]))
            {
                $funcs[$r['func_rut']]['recetas']=Array();
		$funcs[$r['func_rut']]['nombre']=htmlentities($r['fnombre']);			
		$funcs[$r['func_rut']]['preescrip_ok']=0;			
		$funcs[$r['func_rut']]['recetas_ok']=0;			
            }
            //------------------------------------------------------------------
            if(!isset($servicios[$r['centro_nombre']]))
            {
                $servicios[$r['centro_nombre']]['recetas']=Array();
                $servicios[$r['centro_nombre']]['preescrip_no']=0;
                $servicios[$r['centro_nombre']]['preescrip_ok']=0;			
                $servicios[$r['centro_nombre']]['recetas_no']=0;
                $servicios[$r['centro_nombre']]['recetas_ok']=0;			
                $servicios[$r['centro_nombre']]['sumatoria']=0;			
            }
            //------------------------------------------------------------------
            if($r['receta_fecha_cierre']!=null or $r['receta_fecha_cierre']!="")
            {
                $terminada=0;
                if(!isset($receta[$r['tipo'].''.$r['receta_id']]) OR $receta[$r['tipo'].''.$r['receta_id']]==0)
                    $receta[$r['tipo'].''.$r['receta_id']]=Array(0,$r['centro_nombre'],$r['func_rut'],$terminada);
                $servicios[$r['centro_nombre']]['preescrip_ok']++;
                $preescrip_ok++;
            }
            else
            {
                if($r['saldo_despacho']*1>0)
                {
                    $terminada=1;
                
                    $receta[$r['tipo'].''.$r['receta_id']]=Array(1,$r['centro_nombre'],$r['func_rut'],$terminada);
                    $servicios[$r['centro_nombre']]['preescrip_no']++;
                    $preescrip_no++;
                }
                else
                {
                    $terminada=0;
                
                    if(!isset($receta[$r['tipo'].''.$r['receta_id']]) OR $receta[$r['tipo'].''.$r['receta_id']]==0)
                        $receta[$r['tipo'].''.$r['receta_id']]=Array(0,$r['centro_nombre'],$r['func_rut'],$terminada);
                
                    $servicios[$r['centro_nombre']]['preescrip_ok']++;
                    $preescrip_ok++;
                }
            }
            //------------------------------------------------------------------
            /*
            if($r['saldo_despacho']*1>0)
            {
                if($r['receta_fecha_cierre']!=null or $r['receta_fecha_cierre']!="")
                    $terminada=1;
                else
                    $terminada=0;
                
                $receta[$r['tipo'].''.$r['receta_id']]=Array(1,$r['centro_nombre'],$r['func_rut'],$terminada);
                $servicios[$r['centro_nombre']]['preescrip_no']++;
                $preescrip_no++;
            }
            else
            {
                if($r['receta_fecha_cierre']!=null or $r['receta_fecha_cierre']!="")
                    $terminada=1;
                else
                    $terminada=0;
                
                if(!isset($receta[$r['tipo'].''.$r['receta_id']]) OR $receta[$r['tipo'].''.$r['receta_id']]==0)
                    $receta[$r['tipo'].''.$r['receta_id']]=Array(0,$r['centro_nombre'],$r['func_rut'],$terminada);
                
                $servicios[$r['centro_nombre']]['preescrip_ok']++;
                $preescrip_ok++;
            }
            * 
            */
            //------------------------------------------------------------------
            if($fecha1==$fecha2)
            {
                if($r['receta_fecha_cierre']!=null or $r['receta_fecha_cierre']!="")
                {
                    $preescrip_ok_dia++;
                }
                else
                {
                    if($r['saldo_despacho_parcial']*1>0)
                    {
                        $preescrip_no_dia++;
                    }
                    else
                    {
                        $preescrip_ok_dia++;
                    }
                }
                
            }
            //------------------------------------------------------------------
            $servicios[$r['centro_nombre']]['sumatoria']+=$r['art_val_ult']*$r['total'];
            $funcs[$r['func_rut']]['preescrip_ok']++;
        
            
    }
    
    
    
    foreach($receta AS $key=>$val)
    {
        if($val[0]==1)
        {
            if($val[3]*1!=1)
            {
                $recetas_ok++;
            }
            else
            {
                $recetas_no++; 
            }
        }
        else
            $recetas_ok++;
        
            
        if($val[0]==1)
            $servicios[$val[1]]['recetas_no']++;
        else
            $servicios[$val[1]]['recetas_ok']++;
        
        $funcs[$val[2]]['recetas_ok']++;
    }

    print("<center>
        <div class='sub-content' style='font-size:16px;text-align:center;'>CANTIDAD DE RECETAS/PRESCRIPCIONES</div>
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
                    <td style='text-align:right;'>Prescripciones Completas:</td>
                    <td style='font-weight:bold;'>".number_format2($preescrip_ok,0,',','.')."</td>
		</tr>
		<tr>
                    <td style='text-align:right;'>Prescripciones Incompletas:</td>
                    <td style='font-weight:bold;'>".number_format2($preescrip_no,0,',','.')."</td>
		</tr>
		<tr>
                    <td style='text-align:right;'>Promedio de Prescripciones por Receta:</td>
                    <td style='font-weight:bold;text-align:right;'>".number_format2(($preescrip_ok+$preescrip_no)/($recetas_ok+$recetas_no),2,',','.')."</td>
		</tr>
                ");
                if($fecha1==$fecha2)
                {
                    print("<tr>");
                        print("<td style='text-align:right;'>Prescripciones Completas del Día:</td>");
                        print("<td style='font-weight:bold;'>".$preescrip_ok_dia."</td>");
                    print("</tr>");
                    print("<tr>");
                        print("<td style='text-align:right;'>Prescripciones Incompletas del Día:</td>");
                        print("<td style='font-weight:bold;'>".$preescrip_no_dia."</td>");
                    print("</tr>");
                }
            print("
            </table>
            <br /><br />
            <table style='width:100%;'>
                <tr class='tabla_header'>
                    <td style='text-align:center;'>CODINT. RECETA</td>
                    <!--<td style='text-align:center;'>SERVICIO</td>-->
                    <td style='text-align:center;'>RUT PACIENTE</td>
                    <td style='text-align:center;'>NOMBRE PACIENTE</td>
                    <!--<td style='text-align:center;'>ITEM PRESUPUETARIO</td>-->
                    <td style='text-align:center;'>CODIGO</td>
                    <td style='text-align:center;'>ARTICULO</td>
                    <td style='text-align:center;'>FORMA ARTICULO</td>
                    <td style='text-align:center;'>SERVICO</td>");
                    if($fecha1==$fecha2)
                    {
                    print("<td style='text-align:center;'>CANT. A ENTREGAR.</td>");
                    print("<td style='text-align:center;'>CANT. ENTREGADO.</td>");
                    print("<td style='text-align:center;'>ESTADO PRESCRIP DIA.</td>");
                    }
    
        print("
                    <td style='text-align:center;'>CANT. RECETADO.</td>
                    <td style='text-align:center;'>CANT. DESP.</td>
                    <td style='text-align:center;'>ESTADO PRESCRIP.</td>
                    <td style='text-align:center;'>ESTADO RECETA</td>
		</tr>
            ");
	
    $i=0;
    pg_result_seek($q,0);
    while($r=pg_fetch_assoc($q))
    {
        $clase=($i++%2==0)?'tabla_fila':'tabla_fila2';
        
        if($r['receta_fecha_cierre']!=null or $r['receta_fecha_cierre']!="")
        {
            if($r['saldo_despacho']*1>0)
                $estado='COMPLETA';
            else
                $estado='COMPLETA';
        }
        else
        {
            if($r['saldo_despacho']*1>0)
                $estado='INCOMPLETA';
            else
                $estado='COMPLETA';
        }
        if($fecha1==$fecha2)
        {
            if($r['receta_fecha_cierre']!=null or $r['receta_fecha_cierre']!="")
            {
                if($r['saldo_despacho_parcial']*1>0)
                    $estado_parcial='COMPLETA';
                else
                    $estado_parcial='COMPLETA';
            }
            else
            {
                if($r['saldo_despacho_parcial']*1>0)
                    $estado_parcial='INCOMPLETA';
                else
                    $estado_parcial='COMPLETA';
            }
        }
        print("<tr class='$clase'>");
            print("<td style='text-align:center;'>".$r['receta_id']."</td>");
            //print("<td style='text-align:left;'><b>".$r['centro_nombre']."</b></td>");
            print("<td style='text-align:center;'><b>".$r['pac_rut']."</b></td>");
            print("<td style='text-align:left;'><b>".$r['pac_nombre']."</b></td>");
            //print("<td style='text-align:center;'><b>".$r['item_glosa']."</b></td>");
            print("<td style='text-align:center;'><b>".$r['art_codigo']."</b></td>");
            print("<td style='font-weight:bold;text-align:left'>".$r['art_glosa']."</td>");
            print("<td style='font-weight:bold;text-align:center'>".$r['forma_nombre']."</td>");
            print("<td style='font-weight:bold;text-align:left'>".$r['centro_nombre']."</td>");
            if($fecha1==$fecha2)
            {
                print("<td style='text-align:center;'>".number_format($r['total_parcial']*1,0,',','.')."</td>");
                print("<td style='text-align:center;'>".number_format($r['stock_cant_parcial']*-1,0,',','.')."</td>");
                print("<td style='text-align:center;'>".$estado_parcial."</td>");
            }
            print("<td style='text-align:center;'>".number_format($r['total']*1,0,',','.')."</td>");
            print("<td style='text-align:center;'>".number_format($r['stock_cant']*-1,0,',','.')."</td>");
            print("<td style='text-align:center;'>".$estado."</td>");
            
            if($r['receta_fecha_cierre']!=null or $r['receta_fecha_cierre']!="")
            {
                if($receta[$r['tipo'].''.$r['receta_id']][0]!=='1')
                    print("<td style='text-align:center;'>COMPLETA</td>");
                else
                    print("<td style='text-align:center;'>COMPLETA</td>");
            }
            else
            {
                print("<td style='text-align:center;'>".($receta[$r['tipo'].''.$r['receta_id']][0]=='1'?'INCOMPLETA':'COMPLETA')."</td>");
            }
        print("</tr>");
    }
    print("</table><br /><br /></center>");
?>
