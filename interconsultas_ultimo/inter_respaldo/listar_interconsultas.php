<?php

  require_once('../conectar_db.php');
  
    if($_GET['tipo']=='estado_interconsultas' or $_GET['tipo']=='revisar_interconsultas') {
		if($_GET['tipo']=='estado_interconsultas') {
			
			if(isset($_GET['inst_id']))
				$institucion = $_GET['inst_id']*1;
			else 				$institucion = $_GET['inst_id1']*1;
			
			$buscar = $_GET['buscar'];
			$orden = $_GET['orden'];
			if(isset($_GET['ascendente'])) {
				$ascen = '';
			} else {
				$ascen='DESC';
			}
			switch ($orden) {
				case 0: $orden='inter_ingreso'; break;				case 1: $orden='pac_rut'; break;				case 2: $orden='pac_appat, pac_apmat, pac_nombres'; break;				case 3: $orden='esp_desc'; break;				case 4: $orden='inter_folio'; break;
			}
		
			if(trim($buscar)!='') {
				$condicion="					WHERE inter_inst_id2=$sgh_inst_id AND inter_estado>=0 AND (					inter_folio || ' ' || pac_rut || ' ' ||	pac_appat || ' ' || pac_apmat || ' ' || pac_nombres ILIKE '%$buscar%' 					)				";
				$condicion2="					WHERE oa_inst_id2=$sgh_inst_id AND oa_estado>=0 AND (					oa_folio || ' ' || pac_rut || ' ' ||	pac_appat || ' ' || pac_apmat || ' ' || pac_nombres ILIKE '%$buscar%' 					)				";
			} else {
				$condicion="WHERE inter_inst_id2=$sgh_inst_id AND inter_estado>=0";
				$condicion2="WHERE oa_inst_id2=$sgh_inst_id AND oa_estado>=0";
			}
					$query="
			SELECT * FROM (

			SELECT 
			
			inter_folio, inter_ingreso, pac_rut, pac_appat, pac_apmat, pac_nombres, 
			esp_desc, inter_estado, inter_id, ice_desc, ice_icono, 'IC'
			FROM interconsulta 
			LEFT JOIN pacientes ON inter_pac_id=pac_id
			LEFT JOIN especialidades ON inter_especialidad=esp_id
			LEFT JOIN interconsulta_estado ON inter_estado=ice_id
			$condicion
			
			UNION

			SELECT 
			
			oa_folio AS inter_folio, oa_fecha::date AS inter_ingreso, pac_rut, pac_appat, pac_apmat, pac_nombres, 
			esp_desc, oa_estado AS inter_estado, oa_id, ice_desc, ice_icono, 'OA'
			FROM orden_atencion 
			LEFT JOIN pacientes ON oa_pac_id=pac_id
			LEFT JOIN especialidades ON oa_especialidad=esp_id
			LEFT JOIN interconsulta_estado ON oa_estado=ice_id
			$condicion2
			
			) AS foo
			ORDER BY $orden
			$ascen
			";


			$resultado = pg_query($conn, $query);
			print("<table width=100%>
			<tr class='tabla_header' style='font-weight: bold;'>
			<td>Fecha Ing.</td>
			<td>Documento</td>
			<td>Rut Paciente</td>
			<td>Paterno</td>
			<td>Materno</td>
			<td>Nombre</td>
			<td>Especialidad</td>
			<td>Estado</td>
			</tr>
			");
		
		} else {
			
			$filtro=trim(pg_escape_string(utf8_decode($_GET['filtro'])));			
			
			$institucion=($_GET['inst_id1']*1);
			$especialidad=($_GET['especialidad']*1);

			if($filtro!='') {
				$w_filtro="inter_folio || ' ' || pac_rut || ' ' || pac_appat || ' ' || pac_apmat || ' ' || pac_nombres ILIKE '%$filtro%'";
				$w_filtro2="oa_folio || ' ' || pac_rut || ' ' || pac_appat || ' ' || pac_apmat || ' ' || pac_nombres ILIKE '%$filtro%'";
			} else { 
				$w_filtro='true';
				$w_filtro2='true';
			}
				
			if($institucion==0) {
				$w_inst='true';
				$w_inst2='true';
			} else {
				$w_inst='inter_inst_id1='.$institucion;
				$w_inst2='oa_inst_id='.$institucion;
			}			
			if($especialidad==-1) {
				$w_esp='true';
				$w_esp2='true';
			} else { 
				$w_esp='inter_especialidad='.$especialidad;
				$w_esp2='oa_especialidad='.$especialidad;
			}
			$resultado = pg_query($conn, "
		SELECT * FROM (

		SELECT 
        inter_folio,         inter_ingreso,         pac_rut, pac_appat, pac_apmat, pac_nombres, 			esp_desc,         inter_estado,        inst_nombre,        inter_inst_id1,        inter_id, ice_icono, ice_desc, 'IC'
      FROM interconsulta 
			LEFT JOIN pacientes ON inter_pac_id=pac_id			LEFT JOIN especialidades ON inter_especialidad=esp_id			LEFT JOIN instituciones ON inter_inst_id1=inst_id
			LEFT JOIN interconsulta_estado ON inter_estado=ice_id
		WHERE 
         $w_esp AND $w_inst AND $w_filtro AND 			inter_inst_id2=".($sgh_inst_id)." AND        inter_estado=0
		UNION 
		
		SELECT 
        oa_folio AS inter_folio,         oa_fecha::date AS inter_ingreso,         pac_rut, pac_appat, pac_apmat, pac_nombres, 		  esp_desc,         oa_estado AS inter_estado,        inst_nombre,        oa_inst_id AS inter_inst_id1,        oa_id, ice_icono, ice_desc, 'OA'
      FROM orden_atencion 
			LEFT JOIN pacientes ON oa_pac_id=pac_id			LEFT JOIN especialidades ON oa_especialidad=esp_id			LEFT JOIN instituciones ON oa_inst_id=inst_id
			LEFT JOIN interconsulta_estado ON oa_estado=ice_id
		WHERE 
         $w_esp2 AND $w_inst2 AND $w_filtro2 AND 			oa_inst_id2=".($sgh_inst_id)." AND         oa_estado=0 AND NOT oa_motivo=-1
		) AS foo

		ORDER BY inter_folio		
		");
		
			print("<table width=100%>
			<tr class='tabla_header' style='font-weight: bold;'>
			<td>Fecha Ing.</td>
			<td>Procedencia</td>
			<td>Documento</td>
			<td>R.U.T. Paciente</td>
			<td>Paterno</td>
			<td>Materno</td>
			<td>Nombre</td>
			<td>Estado</td>
			</tr>
			");
		}
		
		
		
		for($i=0;$i<pg_num_rows($resultado);$i++) {
			
			$fila = pg_fetch_row($resultado);
			
			for($a=0;$a<count($fila);$a++) $fila[$a] = htmlentities($fila[$a]);
			($i%2)==1	?	$clase='tabla_fila'	: $clase='tabla_fila2';
			if($fila[0]=='-1') $fila[0]='(s/n)';
			if($_GET['tipo']=='estado_interconsultas') {
			if($fila[11]=='IC') $tipo='ficha'; else $tipo='oa';

			print("
			<tr class='".$clase."'
			onMouseOver='this.className=\"mouse_over\";'
			onMouseOut='this.className=\"".$clase."\";'
			onClick='abrir_".$tipo."(".$fila[8].");'
			>
			<td style='text-align: center;'><i>".$fila[1]."</i></td>
			<td style='text-align: center;'>".$fila[11]."#<b>".$fila[0]."</b></td>
			<td style='text-align: center;'><b>".$fila[2]."</b></td>
			<td><b>".$fila[3]."</b></td>
			<td><b>".$fila[4]."</b></td>
			<td><b>".$fila[5]."</b></td>
			<td>".$fila[6]."</td>
			<td><center>
			<img src='iconos/".$fila[10].".png' alt='".$fila[9]."' title='".$fila[9]."'>
			</center></td>
			</tr>
			");
		
			} else {
					if($fila[13]=='IC') $tipo='ficha'; else $tipo='oa';

			print("
			<tr class='".$clase."'
			onMouseOver='this.className=\"mouse_over\";'
			onMouseOut='this.className=\"".$clase."\";'
			onClick='abrir_".$tipo."(".$fila[10].",".$fila[9].");'>
			<td style='text-align: center;'><i>".$fila[1]."</i></td>
			<td style='text-align: center;font-size:9px;'><i>".$fila[8]."</i></td>
			<td style='text-align: center;'>".$fila[13]."#<b>".$fila[0]."</b></td>
			<td style='text-align: right;'><b>".$fila[2]."</b></td>
			<td><b>".$fila[3]."</b></td>
			<td><b>".$fila[4]."</b></td>
			<td><b>".$fila[5]."</b></td>
			<td><center>
			<img src='iconos/".$fila[11].".png' alt='".$fila[12]."' title='".$fila[12]."'>
			</center></td>
			</tr>
			");
			
			}	
		}
		
		print("</table>");
		
  }


?>
