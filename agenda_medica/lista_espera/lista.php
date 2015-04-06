<?php
    require_once('../../conectar_db.php');
    $esp_id=$_POST['esp_id']*1;
    $carp_id=$_POST['carp_id']*1;
    $tipo=$_POST['tipo'];
    $filtro=pg_escape_string(trim(utf8_decode($_POST['filtro'])));
    $xls=(isset($_POST['xls']) AND $_POST['xls']*1==1);
    if($xls) {
        header("Content-type: application/vnd.ms-excel");
    	header("Content-Disposition: filename=\"Informe_LE.xls\";");
        print("
        <h2>Informe de Listas de Espera</h2>
        <table>
        <tr><td align='right'>Fecha Descarga:</td><td>".date('d/m/Y')."</td></tr>
        ");
        if($esp_id!=0) {
            $esp=cargar_registro("SELECT * FROM especialidades WHERE esp_id=".$esp_id);
            print("
            <tr>
                <td align='right'>Especialidad:</td>
                <td>".$esp['esp_desc']."</td>
            </tr>");
        }
        print("</table>");	
    }
    if($filtro!='') {
        $wfiltro = " AND (pac_rut ILIKE '%$filtro%' OR (pac_appat || ' ' || pac_apmat || ' ' || pac_nombres) ILIKE '%$filtro%')";
    } else {
        $wfiltro = "";
    }

    if(isset($_POST['asignados'])) {
        $asignados="JOIN cupos_asigna ON interconsulta.inter_id=cupos_asigna.inter_id
        $jcontrol
        JOIN cupos_atencion ON cupos_asigna.cupos_id=cupos_atencion.cupos_id";
        $where="";
        $a=true;
    } else {
        $asignados="LEFT JOIN cupos_asigna ON interconsulta.inter_id=cupos_asigna.inter_id
        $jcontrol
        LEFT JOIN cupos_atencion ON cupos_asigna.cupos_id=cupos_atencion.cupos_id";
        $where=" AND asigna_id IS NULL";
        $a=false;
    }
    if($a)
        $fcomp='cupos_fecha';
    else
        $fcomp='now()';

    if($tipo=='N') {
        $tabla="FROM interconsulta";
        $campo_inicio="inter_fecha";
        $campo_inicio2="oa_fecha::date AS inter_fecha";
        $jcontrol='AND cupos_asigna.control_id=0';
        $iestado=1;
        //$cwhere=" AND cupos_asigna.control_id=0";
        $ccontrol='0, 0';
        if($_POST['ordenar']=='F') {
            $orden="inter_fecha ASC, inter_prioridad DESC";
        } else {
            $orden="inter_prioridad DESC";
        }
        
        if($esp_id!=0){
            $w_inter_esp=" inter_unidad=$esp_id ";
            $w_inter_oa=" oa_especialidad2=$esp_id ";
        } else {
            $w_inter_esp=" true ";
            $w_inter_oa=" true ";
        }
        
        $consulta="
        SELECT *, (now()::date - inter_fecha) AS dias_espera
        FROM (
            SELECT 
            date_trunc('day', $campo_inicio)::date AS $campo_inicio,
	    pac_rut, pac_appat, pac_apmat, pac_nombres,
	    prior_desc, interconsulta.inter_id,
	    date_trunc('minute', cupos_fecha + asigna_hora) AS hora,
	    inter_prioridad, $ccontrol, 'IC' AS tipo
	    $tabla
	    JOIN pacientes ON inter_pac_id=pacientes.pac_id
	    JOIN prioridad ON prior_id=inter_prioridad
	    $asignados
	    WHERE inter_estado=$iestado 
            AND $w_inter_esp
            $where 
            $wfiltro 
            AND inter_inst_id2=$sgh_inst_id
            UNION
            SELECT 
	    $campo_inicio2,
	    pac_rut,
	    pac_appat, pac_apmat, pac_nombres,
	    prior_desc,
	    orden_atencion.oa_id AS inter_id,
	    null AS hora,
	    oa_prioridad AS inter_prioridad, $ccontrol, 'OA' as tipo
	    FROM orden_atencion
	    JOIN pacientes ON oa_pac_id=pacientes.pac_id
	    JOIN prioridad ON prior_id=oa_prioridad
	    WHERE oa_estado=$iestado 
            AND $w_inter_oa
            $wfiltro AND
	    oa_inst_id2=$sgh_inst_id
        ) AS foo	  
        ORDER BY $orden LIMIT 200
        ";
        
        
        $pacs = cargar_registros($consulta, false);
    } else if($tipo=='C') {
        $tabla="FROM orden_atencion";
        $campo_inicio="oa_fecha_aten";
        $jcontrol='AND true';
        $iestado=-1;
        //$cwhere=" AND NOT cupos_asigna.control_id=0";
        $cwhere='';
        if($_POST['ordenar']=='F') {
            $orden="$campo_inicio ASC , inter_prioridad DESC";
        } else {
            $orden="inter_prioridad DESC";
        }		
        $pacs = cargar_registros("
        SELECT *, (now()::date - $campo_inicio) AS dias_espera
        FROM (
            SELECT 
	    date_trunc('day', $campo_inicio)::date AS $campo_inicio,
	    pac_rut,
	    pac_appat, pac_apmat, pac_nombres,
	    prior_desc,
	    oa_id AS inter_id,
	    0 AS inter_prioridad, 0, 0, 'OA'
	    $tabla
	    JOIN pacientes ON oa_pac_id=pacientes.pac_id
	    JOIN prioridad ON prior_id=0
	    WHERE oa_estado=$iestado 
            AND oa_especialidad=$esp_id $wfiltro 
            AND oa_motivo=-1 AND
	    oa_inst_id2=$sgh_inst_id
        ) AS foo
	ORDER BY $orden 
        ", false);
    } else {
        $v=$_POST['ver'];
        if($v=='P') {
            $ver_w='oa_motivo_salida=0'; 
        } elseif($v=='R') {
            $ver_w='oa_motivo_salida>0';
	} else {
            $ver_w='true';		  
	}
        $v2=$_POST['ver_c']*1;
	if($v2!=-1) {
            $ver2_w='oa_tipo_aten='.$v2; 
	} else {
            $ver2_w='true';		  
	}
  	if($filtro!='') {
            $wfiltro = " AND (pac_rut ILIKE '%$filtro%' OR (pac_appat || ' ' || pac_apmat || ' ' || pac_nombres) ILIKE '%$filtro%')"; 
        } else {
            $wfiltro = "";
  	}
  	if($carp_id==0 AND $wfiltro=="") {
            $query="
            select carp_nombre, (COALESCE(oa_fecha_salida::date, current_date)-oa_fecha::date) AS dias_espera 
            from orden_carpeta 
            join orden_atencion on oa_carpeta_id=carp_id AND $ver_w AND $ver2_w
            order by carp_nombre;
            ";
            $resumen=cargar_registros_obj($query); 
            $r=array();
            for($i=0;$i<sizeof($resumen);$i++) {
                if(!isset($r[$resumen[$i]['carp_nombre']])) {
                    $r[$resumen[$i]['carp_nombre']]['total']=0;
                    $r[$resumen[$i]['carp_nombre']]['a']=0;
                    $r[$resumen[$i]['carp_nombre']]['b']=0;
                    $r[$resumen[$i]['carp_nombre']]['c']=0;
		}
		$r[$resumen[$i]['carp_nombre']]['total']++;
		if($resumen[$i]['dias_espera']*1<30)
                    $r[$resumen[$i]['carp_nombre']]['a']++;
                elseif($resumen[$i]['dias_espera']*1>=30 AND $resumen[$i]['dias_espera']*1<60)
                    $r[$resumen[$i]['carp_nombre']]['b']++;
                elseif($resumen[$i]['dias_espera']*1>=60)
                    $r[$resumen[$i]['carp_nombre']]['c']++;
            }
            print("<table style='width:100%;'>
            <tr class='tabla_header'>
                <td colspan=5 style='font-size:16px;font-weight:bold;'>Resumen de Lista Espera I.Q.</td>
            </tr>
            <tr class='tabla_header'>
                <td>Carpeta</td>
		<td>0-30</td>
		<td>30-60</td>
		<td>&gt;60</td>
		<td>Total</td>
            </tr>");
            $total_a=0;
            $total_b=0;
            $total_c=0;
            $total_t=0;		   
            foreach($r AS $key => $val) {
                $clase=($i%2==0)?'tabla_fila':'tabla_fila2';
		print("<tr class='$clase'>
                <td>".htmlentities($key)."</td>
                <td style='text-align:right;'>".$val['a']."</td>
                <td style='text-align:right;'>".$val['b']."</td>
                <td style='text-align:right;'>".$val['c']."</td>
                <td style='text-align:right;'>".$val['total']."</td>
                </tr>");
					
		$total_a+=($val['a']*1);
		$total_b+=($val['b']*1);
		$total_c+=($val['c']*1);
		$total_t+=($val['total']*1);
            }		   
	
            print("
            <tr class='tabla_header'><td style='text-align:right;'>Totales:</td>
            <td style='text-align:right;'>$total_a</td>
            <td style='text-align:right;'>$total_b</td>
            <td style='text-align:right;'>$total_c</td>
            <td style='text-align:right;'>$total_t</td>
            </tr>
            </table>
            ");
            exit();
	}
	
        $query="SELECT
        oa_fecha, 
        pac_rut, 
        pac_appat, 
        pac_apmat, 
        pac_nombres, 
        oa_prioridad,
        extract('days' from CURRENT_DATE-oa_fecha)  AS dias_espera,
        carp_nombre,
        oa_tipo_aten,
        oa_id 						 
        FROM orden_atencion
        JOIN pacientes ON oa_pac_id=pac_id
        JOIN orden_carpeta on oa_carpeta_id=carp_id
        ";
	  	
        if($carp_id>0)
            $query.=" WHERE oa_carpeta_id=$carp_id AND $ver_w AND $ver2_w";
        elseif($carp_id==-1)
            $query.=" WHERE oa_carpeta_id IS NOT NULL AND $ver_w AND $ver2_w";
        else
            $query.=" WHERE $ver_w AND $ver2_w";
	  		
        $query.=$wfiltro."ORDER BY oa_fecha";
        
        $pacs=cargar_registros($query);
    }
    if($tipo=='H' AND $carp_id!=0) {
        print("<div class='sub-content' style='font-size:16px;'>Total Carpeta: <b>".sizeof($pacs)."</b></div><br /><br />");
    }
?>
<table style='width:100%;' class='lista_small'>
    <tr class='tabla_header' style='text-align:center;font-weight:bold;'>
        <td>&nbsp;#&nbsp;</td>
        <td>Fecha Ing. Solicitud</td>
        <td>RUT</td>
        <td>Nombre Completo</td>
        <td>Prioridad</td>
        <td>D&iacute;as Espera</td>
        <?php if($tipo=='H') {?>
            <td>Carpeta HGF</td>
            <td>Tipo</td>
        <?php } ?>
        <?php if(!$xls) { ?>
            <td>Ficha <?php if($tipo=='N') echo 'I.C.'; else echo 'O.A.'; ?></td>
            <?php if($tipo!='H'){ ?>
                <td>Notificar</td>
            <?php } ?>
            <?php if(_cax(39)) { ?><td>Remover</td><?php } ?>
        <?php } else {  ?>
            <td>Documento</td>
            <td>Folio</td>
            <td>Instituci&oacute;n</td>
            <td>Especialidad</td>
            <td>Unidad Receptora</td>
            <td>Fecha Recepci&oacute;n</td>
            <td>RUT Profesional</td>
            <td>Nombre Profesional</td>
            <td>Motivo</td>
        <?php } ?>
    </tr>
    <?php 
    if($pacs){
        for($i=0;$i<count($pacs);$i++) {
            if(($i%2)==0) {
                $clase='tabla_fila';
            } else {
                $clase='tabla_fila2';
            }
            print('
            <tr class="'.$clase.'" onMouseOver="this.className=\'mouse_over\';" onMouseOut="this.className=\''.$clase.'\';">
            <td align="right">'.($i+1).'</td>
            <td align="center">'.$pacs[$i][0].'</td>
            <td align="right">'.$pacs[$i][1].'</td>
            <td>'.htmlentities(strtoupper($pacs[$i][2].' '.$pacs[$i][3].' '.$pacs[$i][4])).'</td>
            ');
            if($pacs[$i][5]==0){
                print('<td aligh="center">S/P</td>');
            } else {
                print('<td aligh="center">'.$pacs[$i][5].'</td>');
            }
            if($tipo=='H') {
                print("<td align='center'>".$pacs[$i][6]."</td>
                <td align='center'>".htmlentities($pacs[$i][7])."</td>
                ");
                if($pacs[$i][8]==0){
                    print("<td align='center'>Hosp. Quir&uacute;rgica</td>");
		} else if($pacs[$i][8]==1) {
                    print("<td align='center'>Hosp. M&eacute;dica</td>");
		} else {
                    print("<td align='center'>Procedimiento</td>");
		}
            } else {
                print("<td align='right'>".$pacs[$i][12]."</td>");
            }
            if(!$xls) {
                if($tipo=='H'){
                    print('<td><center>
                    <img src="iconos/magnifier.png" style="cursor:pointer;" onClick="abrir_oa('.$pacs[$i][9].');">
                    </center></td>
                    ');			
                } else {
                    print('
                    <td>
                    <center>
                        <img src="iconos/magnifier.png" style="cursor:pointer;" onClick="'.(($pacs[$i][11]=='IC')?'abrir_ic':'abrir_oa').'('.$pacs[$i][6].');">
                    </center>
                    </td>
                    <td>
                    <center>
                        <input type="checkbox" DISABLED id="notifica_'.$pacs[$i][6].'" name="notifica_'.$pacs[$i][6].'">
                        <img src="iconos/phone.png" style="cursor:pointer;" onClick="notificar('.$pacs[$i][6].', '.$pacs[$i][11].');">
                    </center>
                    </td>');
                }
                if(_cax(39))
                    if($tipo=='N' OR $tipo=='C') {
                        print('<td>
                        <center>      
                        <input type="button" id="remover_'.$pacs[$i][6].'" name="remover_'.$pacs[$i][6].'" value="Remover '.(($pacs[$i][11]=='IC')?'I.C.':'O.A.').'" onClick="'.(($pacs[$i][11]=='IC')?'remover_ic':'remover_oa').'('.$pacs[$i][6].');" />
                        </center>      
                        </td>
                        ');
		} else {
                    print('<td>
                    <center>      
                    <input type="button" id="remover_'.$pacs[$i][9].'" name="remover_'.$pacs[$i][9].'" value="Remover O.A." onClick="remover_oa('.$pacs[$i][9].');" />
                    </center>
                    </td>
                    ');
                }
            } else {
                $id = $pacs[$i][6];
                if($pacs[$i][11]=='IC') {
                    $doc=cargar_registro("SELECT *,
                    e1.esp_desc AS esp_desc,
                    e2.esp_desc AS unidad_receptora,
                    inter_fecha::date
                    FROM interconsulta
                    LEFT JOIN instituciones ON inter_inst_id1=inst_id
                    LEFT JOIN especialidades AS e1 ON inter_especialidad=e1.esp_id
                    LEFT JOIN especialidades AS e2 ON inter_unidad=e2.esp_id
                    LEFT JOIN prioridad ON inter_prioridad=prior_id
                    LEFT JOIN profesionales_externos ON inter_prof_id=prof_id 
                    WHERE inter_id=$id 
                    ORDER BY inter_ingreso DESC", true);
                    print("
                    <td align='center'>INTERCONSULTA</td>
                    <td align='center'><b>".$doc['inter_folio']."</b></td>
                    <td align='left'>".$doc['inst_nombre']."</td>
                    <td align='left'>".$doc['esp_desc']."</td>
                    <td align='left'>".$doc['unidad_receptora']."</td>
                    <td align='center'>".$doc['inter_fecha']."</td>
                    <td align='right'>".$doc['prof_rut']."</td>
                    <td align='left'>".trim($doc['prof_paterno'].' '.$doc['prof_materno'].' '.$doc['prof_nombres'])."</td>
                    <td align='center'>".$doc['inter_motivo']."</td>
                    ");	
                } else {
                    $doc=cargar_registro("SELECT *, 
                    oa_fecha::date AS oa_fecha, 
                    e1.esp_desc AS esp_desc,
                    e2.esp_desc AS unidad_receptora
                    FROM orden_atencion
                    LEFT JOIN instituciones ON oa_inst_id=inst_id 
                    LEFT JOIN especialidades AS e1 ON oa_especialidad=e1.esp_id
                    LEFT JOIN especialidades AS e2 ON oa_especialidad2=e2.esp_id
                    LEFT JOIN prioridad ON oa_prioridad=prior_id 
                    LEFT JOIN profesionales_externos ON oa_prof_id=prof_id 
                    WHERE oa_id=$id 
                    ORDER BY orden_atencion.oa_fecha DESC", true);
                    print("
                    <td align='center'>ORDEN DE ATENCI&Oacute;N</td>
                    <td align='center'><b>".$doc['oa_folio']."</b></td>
                    <td align='left'>".$doc['inst_nombre']."</td>
                    <td align='left'>".$doc['esp_desc']."</td>
                    <td align='left'>".$doc['unidad_receptora']."</td>
                    <td align='center'>".$doc['oa_fecha']."</td>
                    <td align='right'>".$doc['prof_rut']."</td>
                    <td align='left'>".trim($doc['prof_paterno'].' '.$doc['prof_materno'].' '.$doc['prof_nombres'])."</td>
                    <td align='center'>".$doc['oa_motivo']."</td>
                    ");	
                }
            }
            print('</tr>');
        }
        if($pacs) {
            for($i=0;$i<count($pacs);$i++) {
                $pacs[$i][2]=htmlentities(strtoupper($pacs[$i][2]));
                $pacs[$i][3]=htmlentities(strtoupper($pacs[$i][3]));
                $pacs[$i][4]=htmlentities(strtoupper($pacs[$i][4]));
            }
        }
    }
    ?>
</table>
<?php if (!isset($_GET['asignados'])) { ?>
    <script>  
        lista_espera=<?php echo json_encode($pacs);?>;
    </script>
<?php } ?>