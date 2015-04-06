<?php 

	require_once('../../conectar_db.php');
	
	$fecha1=pg_escape_string($_POST['fecha1']);
	$fecha2=pg_escape_string($_POST['fecha2']);
	$agrupa=$_POST['agrupa'];
	
	$esp_id=$_POST['esp_id']*1;

	$filtro=pg_escape_string(trim($_POST['filtro']));

   if($esp_id!=-1) {
  	 $w_esp='nom_esp_id='.$esp_id;	
   } else {
  	 $w_esp='true';
   }

   if($filtro!='') {
  	 $w_filtro="nomdp_codigo ILIKE '".$filtro."%'";	
   } else {
  	 $w_filtro='true';
   }
  
   if($_POST['xls']*1==1) {
   
    	header("Content-type: application/vnd.ms-excel");
    	header("Content-Disposition: filename=\"informe_nominas.xls\";");   
   	
	}
	
	
		   		
	$p=cargar_registros_obj("

		SELECT 
		
		pc_codigo AS nomdp_codigo, 1 AS cantidad, 
		pacientes.pac_id, pac_ficha,
		date_part('year',age('$fecha1'::date, pac_fc_nac)) as edad,
		sex_id,
		pac_rut, pac_appat, pac_apmat, pac_nombres, especialidades.esp_desc, prev_desc, pacientes.prev_id,
		nom_fecha::date, tipo, doc_rut, doc_paterno, doc_materno, doc_nombres, 
		nomina_detalle.nomd_diag_cod, nomd_destino, nomd_motivo

		FROM nomina_detalle
		JOIN nomina USING (nom_id)
		LEFT JOIN especialidades ON nom_esp_id=esp_id
		LEFT JOIN pacientes USING (pac_id)
		LEFT JOIN procedimiento ON nom_esp_id=procedimiento.esp_id
		JOIN procedimiento_codigo ON nom_esp_id=procedimiento_codigo.esp_id
		LEFT JOIN codigos_prestacion ON pc_codigo=codigo
 		LEFT JOIN doctores ON nom_doc_id=doc_id
		LEFT JOIN prevision on nomd_prev_id=prevision.prev_id
		WHERE 
		nom_fecha::date>='$fecha1' AND 
		nom_fecha::date<='$fecha2' AND
		$w_esp AND
		nomd_diag_cod NOT IN ('XX', 'R', '') AND
		procedimiento.esp_id IS NULL AND
		$w_filtro

		
		UNION
		
		
		SELECT 
		
		nomdp_codigo, nomdp_cantidad AS cantidad, 
		pac_id, pac_ficha,
		date_part('year',age('$fecha1'::date, pac_fc_nac)) as edad,
		sex_id,
		pac_rut, pac_appat, pac_apmat, pac_nombres, esp_desc, prev_desc, pacientes.prev_id,
		nom_fecha::date, tipo, doc_rut, doc_paterno, doc_materno, doc_nombres, 
		nomina_detalle.nomd_diag_cod, nomd_destino, nomd_motivo

		FROM (
			SELECT * FROM nomina WHERE 
 			nom_fecha::date>='$fecha1' AND 
			nom_fecha::date<='$fecha2' AND
			$w_esp
			) AS foo
		JOIN nomina_detalle USING (nom_id)
		JOIN nomina_detalle_prestaciones USING (nomd_id)
		LEFT JOIN codigos_prestacion ON nomdp_codigo=codigo
 		LEFT JOIN especialidades ON foo.nom_esp_id=esp_id
		LEFT JOIN pacientes USING (pac_id)
		LEFT JOIN doctores ON nom_doc_id=doc_id
		LEFT JOIN prevision on nomd_prev_id=prevision.prev_id
		WHERE 
		NOT nomina_detalle.pac_id=-1 AND
		nomina_detalle.nomd_diag_cod NOT IN ('XX','R','') AND
		$w_filtro
		ORDER BY nomdp_codigo, nom_fecha		

	", true);

?>

<table style='width:100%;font-size:10px;'>
	
	<tr class='tabla_header'>
	
		<td>#</td>
		
		<?php 
			if($agrupa==0)
				print("<td>Prestaci&oacute;n</td>");
			elseif($agrupa==1)
				print("<td>R.U.T. Prof.</td><td>Nombre Profesional</td>");
			else
				print("<td>Especialidad</td>");
		?>
		
		<td>Totales</td>
		<td>&lt;10</td>
		<td>10-14</td>
		<td>15-19</td>
		<td>20-24</td>
		<td>25-64</td>
		<td>&gt;65</td>
		<td>(Benef.) &lt; 15</td>
		<td>(Benef.) &gt; 15</td>
		<td>Masculino</td>
		<td>Femenino</td>	
		<td>(NSP) &lt; 15</td>
		<td>(NSP) &gt; 15</td>
		<td>Destino Alta</td>
		<td>Cons. Abreviada</td>
		
	</tr>

<?php

	$totales=array();
	
	if($p){
	
		for($i=0;$i<sizeof($p);$i++) {
			
			if($agrupa==0)
				$key='nomdp_codigo';
			elseif($agrupa==1)
				$key='doc_rut';
			else
				$key='esp_desc';
				
				
			if(!isset($totales[$p[$i][$key]])) {
				
				if($agrupa==1)
				$totales[$p[$i][$key]]['nombre']=trim($p[$i]['doc_paterno']." ".$p[$i]['doc_materno']." ".$p[$i]['doc_nombres']);
				
				$totales[$p[$i][$key]]['total']=0;	
				$totales[$p[$i][$key]]['masc']=0;	
				$totales[$p[$i][$key]]['feme']=0;	
				$totales[$p[$i][$key]]['geta']=array(0,0,0,0,0,0);	
				$totales[$p[$i][$key]]['benef']=array(0,0);	
				$totales[$p[$i][$key]]['nsp']=array(0,0);	
				$totales[$p[$i][$key]]['altas']=0;	
				$totales[$p[$i][$key]]['abrev']=0;	
				
			} 
			
			if(trim(strtoupper($p[$i]['nomd_diag_cod']))=='NSP') {
				
				if($p[$i]['edad']*1<=15)
					$totales[$p[$i][$key]]['nsp'][0]+=$p[$i]['cantidad']*1;			
				else
					$totales[$p[$i][$key]]['nsp'][1]+=$p[$i]['cantidad']*1;
				
				continue;
				
			}

			$totales[$p[$i][$key]]['total']+=$p[$i]['cantidad']*1;		
			
			if($p[$i]['sex_id']==0)			
				$totales[$p[$i][$key]]['masc']+=$p[$i]['cantidad']*1;
			else
				$totales[$p[$i][$key]]['feme']+=$p[$i]['cantidad']*1;

			if($p[$i]['prev_id']*1>=1 AND $p[$i]['prev_id']*1<=4) {
				if($p[$i]['edad']*1<=15)
					$totales[$p[$i][$key]]['benef'][0]+=$p[$i]['cantidad']*1;
				else
					$totales[$p[$i][$key]]['benef'][1]+=$p[$i]['cantidad']*1;
			}
			
			$val=$p[$i]['edad']*1;

			if($val<10) { $totales[$p[$i][$key]]['geta'][0]+=$p[$i]['cantidad']*1; }
			if($val>=10 AND $val<=14) { $totales[$p[$i][$key]]['geta'][1]+=$p[$i]['cantidad']*1; }
			if($val>=15 AND $val<=19) { $totales[$p[$i][$key]]['geta'][2]+=$p[$i]['cantidad']*1; }
			if($val>=20 AND $val<=24) { $totales[$p[$i][$key]]['geta'][3]+=$p[$i]['cantidad']*1; }
			if($val>=25 AND $val<=64) { $totales[$p[$i][$key]]['geta'][4]+=$p[$i]['cantidad']*1; }
			if($val>=65) { $totales[$p[$i][$key]]['geta'][5]+=$p[$i]['cantidad']*1; }
			
			if(trim($p[$i]['nomd_destino'])*1==6) 
				$totales[$p[$i][$key]]['altas']++;
				
			if(trim($p[$i]['nomd_motivo'])*1==9) 
				$totales[$p[$i][$key]]['abrev']++;
			
				
		}	
		
		foreach ($totales as $codigo => $pres){
		
		$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
		
		
			print("<tr class='$clase'>
						<td align='center' class='tabla_header'>".($n+1)."</td>");
						
			if($agrupa==0)
				print("<td style='text-align:center;'><b>".$codigo."</b></td>");

			if($agrupa==1)
				print("<td style='text-align:right;'><b>".$codigo."</b></td><td style='text-align:left;'>".$pres['nombre']."</td>");
			
			if($agrupa==2)
				print("<td style='text-align:left;'><b>".$codigo."</b></td>");

			print("
						<td style='text-align:right;'>".$pres['total']."</td>
						<td style='text-align:right;'>".$pres['geta'][0]."</td>
						<td style='text-align:right;'>".$pres['geta'][1]."</td>
						<td style='text-align:right;'>".$pres['geta'][2]."</td>
						<td style='text-align:right;'>".$pres['geta'][3]."</td>
						<td style='text-align:right;'>".$pres['geta'][4]."</td>
						<td style='text-align:right;'>".$pres['geta'][5]."</td>
						<td style='text-align:right;'>".$pres['benef'][0]."</td>
						<td style='text-align:right;'>".$pres['benef'][1]."</td>
						<td style='text-align:right;'>".$pres['masc']."</td>
						<td style='text-align:right;'>".$pres['feme']."</td>
						<td style='text-align:right;'>".$pres['nsp'][0]."</td>
						<td style='text-align:right;'>".$pres['nsp'][1]."</td>
						<td style='text-align:right;'>".$pres['altas']."</td>
						<td style='text-align:right;'>".$pres['abrev']."</td>
					</tr>");
		$i++;
		$n++;
		}
	}	
?>

</table>

<?php
	if(!isset($_POST['detalle']))
		exit();
 ?>

<table style='width:100%;font-size:10px;'>
<tr class='tabla_header'>

<td>#</td>

<?php if($esp_id==-1) { ?><td>Especialidad</td> <?php } ?>
<td>Prestaci&oacute;n</td>
<td>Cantidad</td>
<td>Tipo Prest.</td>
<td>Fecha</td>
<td>R.U.T.</td>
<td>Ficha</td>
<td>Paterno</td>
<td>Materno</td>
<td>Nombre</td>
<td>Previsi&oacute;n</td>
<td>Sexo</td>
<td>Edad</td>
<td>R.U.T. Prof.</td>
<td>Nombre Profesional</td>
</tr>

<?php 

	if($p) {
			
	
		for($i=0;$i<sizeof($p);$i++) {
		
			$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
			
			if($p[$i]['sex_id']==0)
		    	$sexo='M';
		    elseif($p[$i]['sex_id']==1)
		    	$sexo='F';
		    else 
		    	$sexo='I';

			print("
				<tr class='$clase'>
				<td align='center' class='tabla_header'>".($i+1)."</td>
			");

			if($esp_id==-1) print("<td align='center'><i>".$p[$i]['esp_desc']."</i></td>");

			$p[$i]['tipo']=strtoupper($p[$i]['tipo']);
			if($p[$i]['tipo']=='') $p[$i]['tipo']='<i>(Desconocido...)</i>';

			print("
				<td align='center'><b>".$p[$i]['nomdp_codigo']."</b></td>
				<td align='right'>".$p[$i]['cantidad']."</td>
				<td align='center'>".$p[$i]['tipo']."</td>
				<td align='center'>".$p[$i]['nom_fecha']."</td>
				<td align='right'><b>".$p[$i]['pac_rut']."</b></td>
				<td align='center'>".$p[$i]['pac_ficha']."</td>
				<td align='left'>".$p[$i]['pac_appat']."</td>
				<td align='left'>".$p[$i]['pac_apmat']."</td>
				<td align='left'>".$p[$i]['pac_nombres']."</td>
				
				<td align='center'>".$p[$i]['prev_desc']."</td>
				<td align='center'>".$sexo."</td>
				<td align='center'>".$p[$i]['edad']."</td>
				<td align='right'>".$p[$i]['doc_rut']."</td>
				<td align='left'>".trim($p[$i]['doc_paterno']." ".$p[$i]['doc_materno']." ".$p[$i]['doc_nombres'])."</td>
			");
						
			print("</tr>");		
			
			flush();			
			
		}

	}

?>

</table>
