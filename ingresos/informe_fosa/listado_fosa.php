<?php 

	require_once('../../conectar_db.php');
	
	$clase=trim($_POST['sel_clases']);
	$sclase=trim($_POST['sel_clases']);
	$codigo=pg_escape_string(trim($_POST['filtro_codigo']));	
	
	$anios=$_POST['anios']*1;
	
	if(isset($_POST['cant'])) {
		$cant=$_POST['cant']*1;
		$cantidad='LIMIT '.$cant;
	} else {
		$cantidad='';
	}
	
	$traslado=isset($_POST['traslado']);
	
	if($clase!='-1') {
		$clase_w="ps_clase='$clase'";	
	} else {
		$clase_w="true";
	}

	if($codigo!='') {
		$codigo_w="ps_codigo ILIKE '$codigo'";	
	} else {
		$codigo_w="true";
	}

	if($sclase!='FOSA') {
	
		$l=cargar_registros_obj("
			SELECT *, COALESCE(us_vence, ps_vence) AS fecha_vence,
			( current_date - COALESCE(us_vence, ps_vence) ) AS dias_atraso 
			FROM uso_sepultura
			JOIN propiedad_sepultura ON
				ps_clase=sep_clase AND
				ps_codigo=sep_codigo AND
				ps_numero=sep_numero AND
				ps_letra=sep_letra AND
				ps_vigente 
			WHERE	$clase_w AND $codigo_w AND us_vigente AND NOT us_bloqueo AND (
				COALESCE(us_vence, ps_vence)::date < (current_date - interval '$anios year')		
			) AND (ps_clase || '|' || ps_codigo || '|' || ps_numero || '|' || ps_letra) NOT IN 
			(SELECT sep_clase || '|' || sep_codigo || '|' || sep_numero || '|' || sep_letra 
			FROM fosa_sepultura)
			ORDER BY ps_vence
			$cantidad
		");
	
		$f=cargar_registros_obj("
			SELECT * FROM (		
			SELECT *, 
			( SELECT COUNT(*) FROM uso_sepultura AS u1
				WHERE u1.sep_clase=f1.sep_clase AND
						u1.sep_codigo=f1.sep_codigo AND
						u1.sep_numero=f1.sep_numero AND 
						u1.sep_letra=f1.sep_letra AND 						
						us_vigente ) AS fosa_uso
			FROM fosa_sepultura AS f1) AS foo
			WHERE fosa_uso<fosa_capacidad
			ORDER BY (fosa_capacidad-fosa_uso::smallint) ASC
		");
		
		for($i=0;$i<sizeof($f);$i++) {
			
			$fosa[$i]=uso_sepultura($f[$i]['sep_clase'],
											$f[$i]['sep_codigo'],
											$f[$i]['sep_numero'],
											$f[$i]['sep_letra']);
												
		}

	} else {

		$l=cargar_registros_obj("
			SELECT *, us_vence AS fecha_vence,
			( current_date - us_vence ) AS dias_atraso 
			FROM uso_sepultura
			WHERE us_vigente AND 
					NOT us_bloqueo AND
					us_vence::date < (current_date) AND 
					(sep_clase || '|' || sep_codigo || '|' || sep_numero || '|' || sep_letra) IN 
					(SELECT 
					sep_clase || '|' || sep_codigo || '|' || sep_numero || '|' || sep_letra 
					FROM fosa_sepultura)
			ORDER BY us_vence
			$cantidad
		");
		
	}

?>

<table style='width:100%;font-size:11px;'>
<tr class='tabla_header'>
<td>Sepultura</td>
<td>R.U.T.</td>
<td>Nombre</td>
<td>Ubicaci&oacute;n</td>
<td>Atraso (D&iacute;as)</td>
<?php if($traslado) { ?>
<td>Fosa</td>
<td>Ubic.</td>
<td colspan=2>Acci&oacute;n</td>
<?php } ?>
</tr>

<?php 

	if($l)
		for($i=0;$i<sizeof($l);$i++) {
		
			$clase=($i%2==0)?'tabla_fila':'tabla_fila2';		
		
			if($traslado) {
				
				if($sclase!='FOSA') {
	
					$fnd=false;		
				
					for($k=0;$k<sizeof($f);$k++) {
					
						if($f[$k]['fosa_capacidad']>$f[$k]['fosa_uso']) {
							
							$fnd=true; 
							$f[$k]['fosa_uso']++;
							
							$ub=&$fosa[$k][0];
							$cant_r=$fosa[$k][2];
							
							$ubicacion='';							
							
							for($j=0;$j<sizeof($ub);$j++) {
								if($ub[$j][2]<$cant_r) {
									$ubicacion=$ub[$j][0];
									$ub[$j][2]++;
									break;
								}	
							}							
							
							break;
												
						}
										
					}
					
				}
			
			}
		
			print("<tr class='$clase'>
			<td style='text-align:center;font-weight:bold;' align='center'>
			".htmlentities($l[$i]['sep_clase'])." &gt;
			".htmlentities($l[$i]['sep_codigo'])." &gt;
			".htmlentities($l[$i]['sep_numero'])."
			".htmlentities($l[$i]['sep_letra'])."
			</td>
			<td align='right' style='text-align:right;'>".htmlentities($l[$i]['us_rut'])."</td>			
			<td style='text-align:left;font-weight:bold;'>".htmlentities($l[$i]['us_nombre'])."</td>			
			<td align='center' style='text-align:center;'>".htmlentities($l[$i]['us_ubicacion'])."</td>			
			<td style='text-align:right;' align='right'>".number_format($l[$i]['dias_atraso'],0,',','.')."</td>
			");
			
			
			if($traslado)
			
			if($sclase!='FOSA') {
			
				if($fnd) {
					
					print("
						<td style='text-align:center;font-weight:bold;' align='center'>
						".htmlentities($f[$k]['sep_clase'])." &gt;
						".htmlentities($f[$k]['sep_codigo'])." &gt;
						".htmlentities($f[$k]['sep_numero'])."
						".htmlentities($f[$k]['sep_letra'])."
						</td>	
					");	

					print('<td style="text-align:center;font-weight:bold;">'.$ubicacion.'</td>');	
	
					$dato_fosa=htmlentities($f[$k]['sep_clase'].'|'.
									$f[$k]['sep_codigo'].'|'.
									$f[$k]['sep_numero'].'|'.
									$f[$k]['sep_letra'].'|'.$ubicacion);
	
	
					print("
					<td>
					<center>
					<img src='iconos/user_go.png' style='cursor:pointer;' 
					onClick='mover(".$l[$i]['us_id'].", \"".$dato_fosa."\")' />
					</center>
					</td>
					<td>
					<center>
					<img src='iconos/cross.png' style='cursor:pointer;' 
					onClick='marcar(".$l[$i]['us_id'].")' />
					</center>
					</td>");		
	
				} else {
	
					print("
						<td colspan=3>(No hay disponibilidad...)</td>
						<td>&nbsp;</td>				
						<td>&nbsp;</td>				
						<td>&nbsp;</td>				
					");
					
				}
			
			} else {

				$dato_fosa='FOSA COMÚN|FOSA COMÚN|0||';

					print("
					<td style='text-align:center;font-weight:bold;'>FOSA COM&Uacute;N</td>
					<td>
					<center>
					<img src='iconos/user_go.png' style='cursor:pointer;' 
					onClick='mover(".$l[$i]['us_id'].", \"".$dato_fosa."\")' />
					</center>
					</td>
					<td>&nbsp;</td>");		
				
			}
			
			print("</tr>");
			
		}

?>

</table>

<?php //print_r($f); ?>