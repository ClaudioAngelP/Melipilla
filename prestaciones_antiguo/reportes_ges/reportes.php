<?php 
	
	require_once('../../conectar_db.php');
	$fecha1=pg_escape_string($_POST['fecha1']);
	$fecha2=pg_escape_string($_POST['fecha2']);
	$tipo_informe=pg_escape_string($_POST['tipo_informe']*1);

	if($tipo_informe==3 OR $tipo_informe==4) {
		
		header("Content-type: application/vnd.ms-excel");
		header("Content-Disposition: filename=\"Reporte_GES.csv\";");			

		if($tipo_informe==3) {
			$estado_w='mon_estado';
		} elseif($tipo_informe==4) {
			$estado_w="mon_estado AND mon_estado_sigges='Exceptuada'";
		}
		
		$q=pg_query("

				SELECT 
				
				mon_fecha_ingreso::date, mon_rut, pac_ficha, mon_nombre, _pst_patologia_interna, _pst_garantia_interna, _pst_rama_interna, mon_fecha_inicio, mon_fecha_limite, nombre_condicion, monr_subcondicion, monr_fecha_evento, mon_estado_sigges, mon_fecha_sigges, monr_observaciones
				
				FROM (
			
				SELECT 

				*, 

					(CURRENT_DATE)-mon_fecha_limite AS dias,
					
					trim(pst_patologia_interna) AS _pst_patologia_interna,
			
					trim(pst_garantia_interna) AS _pst_garantia_interna,

					trim(pst_rama_interna) AS _pst_rama_interna,
					
					mg.mon_id AS real_mon_id,
					
				(SELECT pac_id FROM pacientes WHERE pac_rut=mon_rut ORDER BY pac_id DESC LIMIT 1) AS pac_id,
				
				(monr_fecha_evento IS NOT NULL AND monr_fecha_evento<CURRENT_DATE) AS reclasificar

				FROM monitoreo_ges AS mg

				JOIN patologias_sigges_traductor ON mon_pst_id=pst_id
				
				LEFT JOIN monitoreo_ges_registro AS mgr ON mgr.mon_id=mg.mon_id AND mgr.monr_estado=0
				LEFT JOIN lista_dinamica_condiciones ON id_condicion=COALESCE(monr_clase,'0')::integer
				LEFT JOIN lista_dinamica_bandejas ON codigo_bandeja=monr_subclase

				WHERE $estado_w
				
				) AS foo
				JOIN pacientes USING (pac_id)
				
				ORDER BY dias DESC
			
		");
		
		print(utf8_decode('"Fecha Ingreso";"RUT";"Ficha";"Nombre";"Patología";"Garantía";"Rama";"Fecha Inicio";"Fecha Límite";"Condición";"Cual";"Fecha de Evento";"Estado SIGGES";"Fecha SIGGES";"Ultima Historia"')."\r\n");
		
		while($r=pg_fetch_array($q)) {
			
			for($i=0;$i<sizeof($r);$i++) {
				print('"'.str_replace('"','\"',$r[$i]).'";');
			}
			
			print("\r\n");
			
		}

		exit();
		
	}
	
	$xls=false;
	
	if(isset($_POST['xls']) AND $_POST['xls']=='1') {
		
		$xls=true;
	
  	   header("Content-type: application/vnd.ms-excel");
       header("Content-Disposition: filename=\"Reporte_GES.xls\";");			
		
	}


	if($tipo_informe==1 OR $tipo_informe==7) {

		if($tipo_informe==1)
			$filtro='';
		else
			$filtro="WHERE m2.monr_clase NOT IN ('', '49', '50')";
		
		$q=cargar_registros_obj("
		
		select mon_fecha_ingreso, mon_rut, pac_ficha, mon_nombre, pst_patologia_interna, mon_garantia, pst_rama_interna, mon_fecha_inicio, mon_fecha_limite, cc0,ff0,cc1,fecha_evento,cc2,ff2,cc3,ff3,mon_estado_sigges,mon_fecha_sigges FROM (
		select mges.*, patologias_sigges_traductor.*, foo.monr_id, c3.nombre_condicion AS cc0, m3.monr_fecha_evento AS ff0, c1.nombre_condicion AS cc1, c2.nombre_condicion AS cc2, m2.monr_fecha_evento AS ff2, c4.nombre_condicion AS cc3, m4.monr_fecha_evento AS ff3, m1.monr_fecha_evento as fecha_evento, (select pac_id FROM pacientes WHERE mon_rut=pac_rut LIMIT 1) AS pac_id from (
		select monr_id, mon_id, monr_clase,
		(select monr_id from monitoreo_ges_registro AS mgr2 where NOT mgr2.monr_clase='11' AND mgr1.mon_id=mgr2.mon_id AND mgr2.monr_fecha>mgr1.monr_fecha ORDER BY monr_fecha ASC LIMIT 1) AS monr_id2,
		(select monr_id from monitoreo_ges_registro AS mgr3 where NOT mgr3.monr_clase='11' AND  mgr1.mon_id=mgr3.mon_id AND mgr3.monr_fecha<mgr1.monr_fecha ORDER BY monr_fecha DESC LIMIT 1) AS monr_id3
		from monitoreo_Ges_registro AS mgr1
		where monr_clase='11' AND monr_fecha_evento BETWEEN '$fecha1' AND '$fecha2'
		) AS foo
		join monitoreo_ges AS mges ON foo.mon_id=mges.mon_id
		join patologias_sigges_traductor ON mon_pst_id=pst_id
		left join monitoreo_ges_registro AS m1 on foo.monr_id=m1.monr_id
		left join monitoreo_ges_registro AS m2 on monr_id2=m2.monr_id
		left join monitoreo_ges_registro AS m3 on monr_id3=m3.monr_id
		left join monitoreo_ges_registro AS m4 on m4.mon_id=mges.mon_id AND m4.monr_estado=0
		left join lista_dinamica_condiciones AS c1 on c1.id_condicion::text=foo.monr_clase
		left join lista_dinamica_condiciones AS c2 on c2.id_condicion::text=m2.monr_clase
		left join lista_dinamica_condiciones AS c3 on c3.id_condicion::text=m3.monr_clase
		left join lista_dinamica_condiciones AS c4 on c4.id_condicion::text=m4.monr_clase
		$filtro ) AS foooo
		left join pacientes using (pac_id);
		
		", true);


    $totales=array();
    $resultados=array();

		for($i=0;$i<sizeof($q);$i++){

      $causa=$q[$i]['cc0'];
      $resultado=$q[$i]['cc2'];
      
      if(!isset($totales[$causa])) {
        $totales[$causa]=array();
      }

      $resultados[]=$resultado;
      
      if(!isset($totales[$causa][$resultado])) {
        $totales[$causa][$resultado]=0;
      }

      $totales[$causa][$resultado]++;

    }
    
    $resultados=array_values(array_unique($resultados));
    
    
    print("<table style='width:100%;'><tr class='tabla_header'><td>Causal</td>");
    
    for($j=0;$j<sizeof($resultados);$j++) {
      if($resultados[$j]!='')
        print("<td style='text-align:center;'>".$resultados[$j]."</td>");
      else
        print("<td style='text-align:center;'>(Esperando...)</td>");
    }
    
    print("<td>Subtotal</td></tr>");
    
    $cc=0;
    
    foreach($totales AS $key => $vals) {
    
      $clase=(($cc++)%2==0)?'tabla_fila':'tabla_fila2';
    
      if($key=='') $key='(Sin Informaci&oacute;n...)';
    
      print("<tr class='$clase'><td class='tabla_header' style='text-align:right;'>".$key."</td>");
      
      $subtotal=0;
      
      for($j=0;$j<sizeof($resultados);$j++) {
        print("<td style='text-align:right;'>".$vals[$resultados[$j]]."</td>");
        $subtotal+=$vals[$resultados[$j]]*1;
      }
      
      print("<td style='text-align:right;font-weight:bold;'>$subtotal</td></tr>");
    
    }    

		
		print("
    </table><br/><br/>
		<table style='width:100%;'>
		<tr class='tabla_header'>
		<td>Fecha Ingreso</td>
		<td>RUT</td>
		<td>Ficha</td>
		<td>Nombre Completo</td>
		<td>Patolog&iacute;a</td>
		<td>Garant&iacute;a</td>
		<td>Rama</td>
		<td>Fecha Inicio</td>
		<td>Fecha L&iacute;mite</td>
		<td>Causal Carta</td>
		<td>Fecha</td>
		<td>Condici&oacute;n</td>
		<td>Fecha</td>
		<td>Resultado Carta</td>
		<td>Fecha</td>
		<td>Condici&oacute;n Actual</td>
		<td>Fecha Evento Actual</td>
		</tr>
		");
		
		for($i=0;$i<sizeof($q);$i++){
			$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
			print("<tr class='$clase'>
			<td style='text-align:center;'>".substr($q[$i]['mon_fecha_ingreso'],0,16)."</td>
			<td style='text-align:right;'>".$q[$i]['mon_rut']."</td>
			<td style='text-align:center;'>".$q[$i]['pac_ficha']."</td>
			<td style='text-align:left;'>".$q[$i]['mon_nombre']."</td>
			<td style='text-align:left;'>".$q[$i]['pst_patologia_interna']."</td>
			<td style='text-align:left;'>".$q[$i]['mon_garantia']."</td>
			<td style='text-align:left;'>".$q[$i]['pst_rama_interna']."</td>
			<td style='text-align:center;'>".$q[$i]['mon_fecha_inicio']."</td>
			<td style='text-align:center;'>".$q[$i]['mon_fecha_limite']."</td>
			<td style='text-align:left;'>".$q[$i]['cc0']."</td>
			<td style='text-align:center;'>".$q[$i]['ff0']."</td>
			<td style='text-align:left;'>".$q[$i]['cc1']."</td>
			<td style='text-align:center;'>".$q[$i]['fecha_evento']."</td>
			<td style='text-align:left;'>".$q[$i]['cc2']."</td>
			<td style='text-align:center;'>".$q[$i]['ff2']."</td>
			<td style='text-align:left;'>".$q[$i]['cc3']."</td>
			<td style='text-align:center;'>".$q[$i]['ff3']."</td>
			</tr>");
		}
		
	} 
  
	if($tipo_informe==2) {
		
		$q=cargar_registros_obj("
	SELECT * FROM (
    SELECT mon_fecha_ingreso, mon_rut, mon_nombre, pst_patologia_interna AS mon_patologia, mon_garantia, mon_fecha_inicio, mon_fecha_limite, mgr.monr_fecha, monr_subcondicion,
    (SELECT monr_valor FROM monitoreo_ges_registro AS mgr2 WHERE mgr.mon_id=mgr2.mon_id AND monr_subclase='E' ORDER BY monr_fecha DESC LIMIT 1) AS campos_e,
    (SELECT monr_valor FROM monitoreo_ges_registro AS mgr2 WHERE mgr.mon_id=mgr2.mon_id AND monr_subclase='N' ORDER BY monr_fecha DESC LIMIT 1) AS campos_n,
	(SELECT monr_id FROM monitoreo_ges_registro AS mgr3 WHERE mgr.mon_id=mgr3.mon_id ORDER BY monr_fecha DESC LIMIT 1) AS monr_id
    FROM monitoreo_ges_registro AS mgr
    JOIN monitoreo_ges using (mon_id)
	join patologias_sigges_traductor ON mon_pst_id=pst_id
    WHERE monr_clase='20' AND monr_subclase='N' AND monr_fecha BETWEEN '$fecha1' AND '$fecha2'
	) AS foo
	JOIN monitoreo_ges_registro USING (monr_id)
	LEFT JOIN lista_dinamica_condiciones ON COALESCE(monr_clase,'0')::bigint=id_condicion
	;
		", true);

    print("
		<table style='width:100%;'>
		<tr class='tabla_header'>
		<td>Fecha Ingreso</td>
		<td>RUT</td>
		<td>Ficha</td>
		<td>Nombre Completo</td>
		<td>Patolog&iacute;a</td>
		<td>Garant&iacute;a</td>
		<td>Fecha Inicio</td>
		<td>Fecha L&iacute;mite</td>
		<td>Fecha Env&iacute;o</td>
		<td>Tipo de Compra</td>
		<td>Proveedor</td>
		<td>Fecha O.C.</td>
		<td>Nro. O.C.</td>
		<td>Condici&oacute;n</td>
		<td>Cual</td>
		<td>Fecha Evento</td>
		<td>Ultima Historia</td>
		</tr>
		");
		
		for($i=0;$i<sizeof($q);$i++){
			
      $clase=($i%2==0)?'tabla_fila':'tabla_fila2';
      
      $tmp=explode('|',$q[$i]['campos_e']);
      $tmp1=explode('|',$q[$i]['campos_n']);
      
			print("<tr class='$clase'>
			<td style='text-align:center;'>".substr($q[$i]['mon_fecha_ingreso'],0,16)."</td>
			<td style='text-align:right;'>".$q[$i]['mon_rut']."</td>
			<td style='text-align:center;'>".$q[$i]['pac_ficha']."</td>
			<td style='text-align:left;'>".$q[$i]['mon_nombre']."</td>
			<td style='text-align:left;'>".$q[$i]['mon_patologia']."</td>
			<td style='text-align:left;'>".$q[$i]['mon_garantia']."</td>
			<td style='text-align:center;'>".$q[$i]['mon_fecha_inicio']."</td>
			<td style='text-align:center;'>".$q[$i]['mon_fecha_limite']."</td>
			<td style='text-align:center;'>".substr($q[$i]['monr_fecha'],0,16)."</td>
			<td style='text-align:left;'>".$tmp[0]."</td>
			<td style='text-align:left;'>".$tmp[1]."</td>
			<td style='text-align:left;'>".$tmp1[0]."</td>
			<td style='text-align:left;'>".$tmp1[1]."</td>
			<td style='text-align:left;'>".$q[$i]['nombre_condicion']."</td>
                        <td style='text-align:left;'>".$q[$i]['monr_subcondicion']."</td>
                        <td style='text-align:left;'>".$q[$i]['monr_fecha_evento']."</td>
                        <td style='text-align:left;'>".$q[$i]['monr_observaciones']."</td>
                        
			</tr>");
      
		}


  }  

	if($tipo_informe==5) {
		
		$q=cargar_registros_obj("
			select * from (
select *,
(select pac_id from pacientes where mon_rut=pac_rut order by pac_id DESC LIMIT 1) AS pac_id
from monitoreo_ges where NOT mon_estado) AS foo where pac_id IS NULL;
		", true);

		print("
                <table style='width:100%;'>
                <tr class='tabla_header'>
                <td>Fecha Ingreso</td>
                <td>RUT</td>
                <td>Nombre Completo</td>
                <td>Patolog&iacute;a</td>
                <td>Garant&iacute;a</td>
                <td>Fecha Inicio</td>
                <td>Fecha L&iacute;mite</td>
                </tr>
                ");
                
                for($i=0;$i<sizeof($q);$i++){
                        $clase=($i%2==0)?'tabla_fila':'tabla_fila2';
                        print("<tr class='$clase'>
                        <td style='text-align:center;'>".substr($q[$i]['mon_fecha_ingreso'],0,16)."</td>
                        <td style='text-align:right;'>".$q[$i]['mon_rut']."</td>
                        <td style='text-align:left;'>".$q[$i]['mon_nombre']."</td>
                        <td style='text-align:left;'>".$q[$i]['mon_patologia']."</td>
                        <td style='text-align:left;'>".$q[$i]['mon_garantia']."</td>
                        <td style='text-align:center;'>".$q[$i]['mon_fecha_inicio']."</td>
                        <td style='text-align:center;'>".$q[$i]['mon_fecha_limite']."</td>
                       </tr> 
              			");
              		}
	}
  
  
  if($tipo_informe==6) {
  
    $q=cargar_registros_obj("
      SELECT * FROM (
      select mon_id, count(*) AS total from monitoreo_ges_registro where monr_subclase='E'
      group by mon_id) AS foo
      join monitoreo_ges using (mon_id)
	join patologias_sigges_traductor ON mon_pst_id=pst_id
      ORDER BY total DESC;    
    ", true);

		print("
                <table style='width:100%;'>
                <tr class='tabla_header'>
                <td>Fecha Ingreso</td>
                <td>RUT</td>
                <td>Nombre Completo</td>
                <td>Patolog&iacute;a</td>
                <td>Garant&iacute;a</td>
                <td>Fecha Inicio</td>
                <td>Fecha L&iacute;mite</td>
                <td>En SDA</td>
                </tr>
                ");
                
                for($i=0;$i<sizeof($q);$i++){
                        $clase=($i%2==0)?'tabla_fila':'tabla_fila2';
                        print("<tr class='$clase'>
                        <td style='text-align:center;'>".substr($q[$i]['mon_fecha_ingreso'],0,16)."</td>
                        <td style='text-align:right;'>".$q[$i]['mon_rut']."</td>
                        <td style='text-align:left;'>".$q[$i]['mon_nombre']."</td>
                        <td style='text-align:left;'>".$q[$i]['pst_patologia_interna']."</td>
                        <td style='text-align:left;'>".$q[$i]['mon_garantia']."</td>
                        <td style='text-align:center;'>".$q[$i]['mon_fecha_inicio']."</td>
                        <td style='text-align:center;'>".$q[$i]['mon_fecha_limite']."</td>
                        <td style='text-align:center;'>".$q[$i]['total']."</td>
                       </tr> 
              			");
              		}


  
  }

	if($tipo_informe==8) {

                $q=pg_query("
                select func_rut, func_nombre,
                extract('month' from monr_fecha) AS mes,
                extract('year' from monr_fecha) AS anio,
                count(*) as historias
                from monitoreo_ges_registro
                join funcionario on monr_func_id=func_id
                WHERE monr_fecha BETWEEN '$fecha1' AND '$fecha2'
                group by func_rut, func_nombre, mes, anio;");

                print("<table style='width:100%;'><tr class='tabla_header'><td>RUT</td><td>Nombre Completo</td><td>Mes</td><td>A&ntilde;o</td><td>Cantidad</td></tr>");

                $cc=0;

                while($r=pg_fetch_assoc($q)) {

                        $clase=(($cc++)%2==0)?'tabla_fila':'tabla_fila2';

                        print("<tr class='$clase'>
                                <td style='text-align:right;'>".$r['func_rut']."</td>
                                <td>".htmlentities(strtoupper($r['func_nombre']))."</td>
                                <td style='text-align:right;'>".$r['mes']."</td>
                                <td style='text-align:right;'>".$r['anio']."</td>
                                <td style='text-align:right;'>".$r['historias']."</td>
                                </tr>");

                }

                print("</table>");



        }

	if($tipo_informe==9) {

		$q=cargar_registros_obj("
		select *, func_rut, func_nombre,
                extract('month' from monr_fecha) AS mes,
                extract('year' from monr_fecha) AS anio
                from monitoreo_ges_registro
		join monitoreo_ges using (mon_id)
		left join lista_dinamica_condiciones on COALESCE(monr_clase,'0')::bigint=id_condicion
		left join lista_dinamica_especialidades on mon_cod_especialidad=esp_codigo
                join funcionario on monr_func_id=func_id
                WHERE monr_fecha BETWEEN '$fecha1' AND '$fecha2';
		", true);

		print("
                <table style='width:100%;'>
                <tr class='tabla_header'>
                <td>Fecha Ingreso</td>
                <td>RUT</td>
                <td>Ficha</td>
                <td>Nombre Completo</td>
                <td>Patolog&iacute;a</td>
                <td>Garant&iacute;a</td>
                <td>Fecha Inicio</td>
                <td>Fecha L&iacute;mite</td>
		<td>Fecha Evento</td>
                <td>Condici&oacute;n</td>
		<td>Cual</td>
		<td>Observaci&oacute;n Monitor</td>
                <td>Fecha Monitoreo</td>
                <td>Nombre Monitor</td>
                </tr>
                ");

                for($i=0;$i<sizeof($q);$i++) {

                $clase=($i%2==0)?'tabla_fila':'tabla_fila2';

                print("<tr class='$clase'>
                        <td style='text-align:center;'>".substr($q[$i]['mon_fecha_ingreso'],0,16)."</td>
                        <td style='text-align:right;'>".$q[$i]['mon_rut']."</td>
                        <td style='text-align:center;'>".$q[$i]['pac_ficha']."</td>
                        <td style='text-align:left;'>".$q[$i]['mon_nombre']."</td>
                        <td style='text-align:left;'>".$q[$i]['mon_patologia']."</td>
                        <td style='text-align:left;'>".$q[$i]['mon_garantia']."</td>
                        <td style='text-align:center;'>".$q[$i]['mon_fecha_inicio']."</td>
                        <td style='text-align:center;'>".$q[$i]['mon_fecha_limite']."</td>
			<td style='text-align:left;'>".$q[$i]['monr_fecha_evento']."</td>
                        <td style='text-align:left;'>".$q[$i]['nombre_condicion']."</td>
                        <td style='text-align:left;'>".$q[$i]['monr_subcondicion']."</td>
                        <td style='text-align:left;'>".$q[$i]['monr_observaciones']."</td>
                        <td style='text-align:center;'>".substr($q[$i]['monr_fecha'],0,16)."</td>
                        <td style='text-align:left;'>".$q[$i]['func_nombre']."</td>

                        </tr>");

		}

		print("</table>");

	}

	

?>

</table>


<?php if($xls) exit(); ?>

<script>


$('cant_registros').innerHTML="<?php 

	if($q)
		print(''.sizeof($q).'');
	else
		print('0');
		
?>";

</script>
