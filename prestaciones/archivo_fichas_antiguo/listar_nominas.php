<?php  

require_once('../../conectar_db.php'); error_reporting(E_ALL);

	$tipo=$_POST['tipo_inf'];	$fecha = pg_escape_string($_POST['fecha1']); 
	
	$esp_id = $_POST['esp_id']*1;
	if($esp_id!=-1) $esp="especialidades.esp_id=$esp_id";
	else $esp="true";
	
	$doc_id= $_POST['doc_id']*1;
	if($doc_id!=-1) $doc="doctores.doc_id=$doc_id";
	else $doc="true";
	
	
if($tipo==1){
	
	$nom_ant='';
	$doc_ant='';
	$esp_ant='';
  
	$salidas = cargar_registros_obj("SELECT nomina.nom_id,nom_fecha::date AS fecha,nomd_hora, upper(esp_desc)AS esp,doc_rut,pacientes.pac_ficha,
				upper(doc_nombres||' '||doc_paterno||' '||doc_materno) AS doc_nombre, 
				pac_rut,upper(pac_nombres||' '||pac_appat||' '||pac_apmat)AS pac_nombre,pacientes.pac_id, 
				date_trunc('second',COALESCE(nomd_fecha_asigna,nom_fecha))AS fecha_asigna,nomd_id,
				especialidades.esp_id,doctores.doc_id,
				COALESCE((SELECT COALESCE(esp_desc,'ARCHIVO') FROM archivo_movimientos LEFT JOIN especialidades ON origen_esp_id=esp_id WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha ORDER BY am_fecha DESC LIMIT 1),'ARCHIVO') as ubic_anterior,
				COALESCE((SELECT COALESCE(esp_desc,'ARCHIVO') FROM archivo_movimientos LEFT JOIN especialidades ON destino_esp_id=esp_id WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha ORDER BY am_fecha DESC LIMIT 1),'ARCHIVO') as ubic_actual,
				COALESCE((SELECT COALESCE(am_estado,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha ORDER BY am_fecha DESC LIMIT 1),0) as am_estado
				FROM nomina
				LEFT JOIN nomina_detalle USING (nom_id)
				LEFT JOIN especialidades ON nom_esp_id=esp_id
				LEFT JOIN doctores ON nom_doc_id=doc_id
				JOIN pacientes USING (pac_id)
				WHERE nom_fecha BETWEEN '$fecha 00:00:00' AND '$fecha 23:59:59' AND nomd_diag_cod NOT IN ('X','T','B')
				AND $esp AND $doc
				ORDER BY nom_fecha,esp_desc,doc_nombre,pac_ficha");
				
		// LEFT JOIN archivo_fichas ON pacientes.pac_ficha=archivo_fichas.pac_ficha AND arc_estado=1
				
} elseif($tipo==2){
	
	$nom_ant='';
	$doc_ant='';
	$esp_ant='';
  
	$salidas = cargar_registros_obj("SELECT am_fecha::date AS fecha,am_fecha::time AS nomd_hora, upper(esp_desc)AS esp,doc_rut,pacientes.pac_ficha,
				upper(doc_nombres||' '||doc_paterno||' '||doc_materno) AS doc_nombre, 
				pac_rut,upper(pac_nombres||' '||pac_appat||' '||pac_apmat)AS pac_nombre,pacientes.pac_id, 
				date_trunc('second',am_fecha)AS fecha_asigna,
				especialidades.esp_id,doctores.doc_id,
				COALESCE((SELECT COALESCE(esp_desc,'ARCHIVO') FROM archivo_movimientos LEFT JOIN especialidades ON origen_esp_id=esp_id WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha ORDER BY am_fecha DESC LIMIT 1),'ARCHIVO') as ubic_anterior,
				COALESCE((SELECT COALESCE(esp_desc,'ARCHIVO') FROM archivo_movimientos LEFT JOIN especialidades ON destino_esp_id=esp_id WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha ORDER BY am_fecha DESC LIMIT 1),'ARCHIVO') as ubic_actual,
				COALESCE((SELECT COALESCE(am_estado,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha ORDER BY am_fecha DESC LIMIT 1),0) as am_estado
				FROM archivo_movimientos
				LEFT JOIN especialidades ON destino_esp_id=esp_id
				LEFT JOIN doctores ON destino_doc_id=doc_id
				JOIN pacientes USING (pac_id)
				WHERE 
				am_final AND am_estado=2
				AND $esp AND $doc
				ORDER BY esp_desc,doc_nombre,pac_ficha");
				
		// nom_fecha BETWEEN '$fecha 00:00:00' AND '$fecha 23:59:59' AND nomd_diag_cod NOT IN ('X','T','B')
		// LEFT JOIN archivo_fichas ON pacientes.pac_ficha=archivo_fichas.pac_ficha AND arc_estado=1
				
}


				
			

  $opts=Array('Solicitada', 'Retirada', 'Enviada', 'Recepcionada', 'Devuelta', 'Extraviada');
  $opts_color=Array('black','yellowgreen','yellowgreen','purple','green','red');
	
  if($tipo==2 AND !$salidas)	 {
	print("<center><h1>(No tiene fichas pendientes por recepcionar...)</h1></center>");
  }
	
  if($salidas)    for($i=0;$i<count($salidas);$i++) {
    ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
	 
	$checked='';
	$color='';
	 
	if($doc_ant!=$salidas[$i]['doc_id'] OR $esp_ant!=$salidas[$i]['esp_id']){
	
			$doc_ant=$salidas[$i]['doc_id'];
			$esp_ant=$salidas[$i]['esp_id'];
			
			$cont=1;
			
			print("<table style='width:100%;' class='lista_small'>
				<tr class='tabla_header'>
					<td style='text-align:left;font-size:16px;' colspan=11>Programa: <b>".htmlentities($salidas[$i]['esp'])."</b><br/>Profesional/Servicio: <b>".htmlentities($salidas[$i]['doc_nombre'])."</b></td>
				</tr>
				<tr class='tabla_header'>
				<td style='width:3%;'>#</td>
				<td style='width:15%;'>Solicitado</td>
				<td style='width:12%;'>Ficha</td>
				<td style='width:12%;'>RUN</td>
				<td style='width:40%;'>Nombre Completo</td>
			");
			
			if($tipo!=2)
			print("
				<td>Ubic. Anterior</td>
				<td>Ubic. Actual</td>
			");
			
			print("
				<td>Estado</td>
				<td>Etiqueta</td>
				<td>Historial</td>
				</tr>");
			
	}

	$options='';
	
	for($l=0;$l<sizeof($opts);$l++) {
		if($salidas[$i]['am_estado']*1==$l) $sel='SELECTED'; else $sel='';
		$options.='<option value="'.$l.'" '.$sel.'>'.$opts[$l].'</option>';
	}
	
    print("<tr class='$clase' style='color:".$opts_color[$salidas[$i]['am_estado']*1].";$color'			onMouseOver='this.className=\"mouse_over\";'			onMouseOut='this.className=\"".$clase."\";'>			<td style='text-align:center;'>".$cont."</td>			<td style='text-align:center;'>".$salidas[$i]['fecha_asigna']."</td>
			<td style='text-align:right;font-size:14px;font-weight:bold;'>".$salidas[$i]['pac_ficha']."</td>
			<td style='text-align:right;'>".$salidas[$i]['pac_rut']."</td>
			<td style='text-align:left;'>".htmlentities($salidas[$i]['pac_nombre'])."</td>
		");
		
		if($tipo!=2) {
		print("
			<td style='text-align:left;'>".htmlentities($salidas[$i]['ubic_anterior'])."</td>
			<td style='text-align:left;'>".htmlentities($salidas[$i]['ubic_actual'])."</td>
		");
		}
		
		print("
			<td style='text-align:center;'>
			<select id='chk_".$salidas[$i]['pac_ficha']."' name='chk_".$salidas[$i]['pac_ficha']."'>
			".$options."
			</select>
			</td><td>
			 <center><img src='iconos/printer.png'  style='cursor:pointer;'
                alt='Imprimir Etiqueta' title='Imprimir Etiqueta' onClick='imprimir_etiqueta(".$salidas[$i]['pac_id'].");' /></center>
			</td><td>
			 <center><img src='iconos/magnifier.png'  style='cursor:pointer;'
                alt='Ver Historial' title='Ver Historial' onClick='historial_ficha(".$salidas[$i]['pac_id'].");' /></center>
			</td></tr>");
			    
		//$nom_ant=$salidas[$i]['nom_id'];
		$cont++;
  	}


	print("</table>");




?>
