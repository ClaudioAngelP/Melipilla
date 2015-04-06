<?php  require_once('../../conectar_db.php'); error_reporting(E_ALL);

	$tipo=$_POST['tipo_inf'];	$fecha = pg_escape_string($_POST['fecha1']); 
	
	$esp_id = $_POST['esp_id']*1;
	if($esp_id!=-1) $esp="especialidades.esp_id=$esp_id";
	else $esp="true";
	
	$doc_id= $_POST['doc_id']*1;
	if($doc_id!=-1) $doc="doctores.doc_id=$doc_id";
	else $doc="true";
	
?>

<?php
	
if($tipo==1){
	
	$nom_ant='';
	$doc_ant='';
	$esp_ant='';
  
	$salidas = cargar_registros_obj("SELECT nomina.nom_id,nom_fecha::date AS fecha,nomd_hora, upper(esp_desc)AS esp,doc_rut,pacientes.pac_ficha,
				upper(doc_nombres||' '||doc_paterno||' '||doc_materno) AS doc_nombre, 
				pac_rut,upper(pac_nombres||' '||pac_appat||' '||pac_apmat)AS pac_nombre,pacientes.pac_id, 
				date_trunc('second',COALESCE(nomd_fecha_asigna,nom_fecha))AS fecha_asigna,nomd_id,
				arc_id,arc_estado,
				especialidades.esp_id,doctores.doc_id,'' as anterior,'' as actual
				FROM nomina
				LEFT JOIN nomina_detalle USING (nom_id)
				LEFT JOIN especialidades ON nom_esp_id=esp_id
				LEFT JOIN doctores ON nom_doc_id=doc_id
				JOIN pacientes USING (pac_id)
				LEFT JOIN archivo_fichas ON pacientes.pac_ficha=archivo_fichas.pac_ficha AND arc_estado=1
				WHERE nom_fecha BETWEEN '$fecha 00:00:00' AND '$fecha 23:59:59' AND nomd_diag_cod NOT IN ('X','T','B')
				AND $esp AND $doc
				GROUP BY nomina.nom_id,nomd_id,nom_fecha,nom_esp_id,nom_doc_id,nomd_hora,esp_desc, doc_rut,doc_nombres,
				doc_paterno,doc_materno, pacientes.pac_ficha,pacientes.pac_id, pac_rut,pac_nombres,pac_appat,pac_apmat,nomd_fecha_asigna, especialidades.esp_id,doctores.doc_id,
				arc_id,arc_estado
				ORDER BY nom_fecha,esp_desc,doc_nombre,nomd_hora");
				
			

  if($salidas)    for($i=0;$i<count($salidas);$i++) {
    ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
	 
	 if($salidas[$i]['arc_estado']==1) {
		 $checked='DISABLED CHECKED';
	 	$color='color:#FF0000;';	
	 } else {
		 $checked='';
	 	$color='';
	 }

	if($nom_ant!=$salidas[$i]['nom_id']){
			$cont=1;
			
			print("<table style='width:100%;' class='lista_small'>
				<tr class='tabla_header'>
					<td style='text-align:left;font-size:14px;' colspan=10>Programa: <b>".htmlentities($salidas[$i]['esp'])."</b> &nbsp;&nbsp;Profesional/Servicio: <b>".htmlentities($salidas[$i]['doc_nombre'])."</b></td>
				</tr>
				<tr class='tabla_header'>
				<td style='width:3%;'>#</td>
				<td style='width:15%;'>Solicitado</td>
				<td style='width:5%;'>Hora</td>
				<td style='width:12%;'>Ficha</td>
				<td style='width:12%;'>RUN</td>
				<td style='width:40%;'>Nombre Completo</td>
				<td>Ubic. Anterior</td>
				<td>Ubic. Actual</td>
				<td>Estado</td>
				<td>Etiqueta</td>
				</tr>");
			
	}

	$opts='
	<option value="1">SOLICITADA</option>
	<option value="2">RETIRADA</option>
	<option value="3">DESPACHADA</option>
	<option value="3">RECIBIDA</option>
	<option value="4">EXTRAVIADA</option>
	';
	
    print("<tr class='$clase' style='$color'			onMouseOver='this.className=\"mouse_over\";'			onMouseOut='this.className=\"".$clase."\";'>			<td style='text-align:center;'>".$cont."</td>			<td style='text-align:center;'>".$salidas[$i]['fecha_asigna']."</td>
			<td style='text-align:center;'>".$salidas[$i]['nomd_hora']."</td>
			<td style='text-align:right;font-size:14px;font-weight:bold;'>".$salidas[$i]['pac_ficha']."</td>
			<td style='text-align:right;'>".$salidas[$i]['pac_rut']."</td>
			<td style='text-align:left;'>".htmlentities($salidas[$i]['pac_nombre'])."</td>
			<td style='text-align:left;'>".htmlentities($salidas[$i]['anterior'])."</td>
			<td style='text-align:left;'>".htmlentities($salidas[$i]['actual'])."</td>
			<td style='text-align:center;'>
			<select id='chk_".$salidas[$i]['pac_ficha']."' name='chk_".$salidas[$i]['pac_ficha']."'>
			$opts
			</select>
			</td><td>
			 <center><img src='iconos/printer.png'  style='cursor:pointer;'
                alt='Imprimir Etiqueta' title='Imprimir Etiqueta' onClick='imprimir_etiqueta(".$salidas[$i]['pac_id'].");' /></center>
			</td></tr>");
			    
	$nom_ant=$salidas[$i]['nom_id'];
	$cont++;
  	}


		print("</table>");	
}

if($tipo==2){
	$nom_ant='';
	$doc_ant='';
	$esp_ant='';
  
	$entradas = cargar_registros_obj("SELECT nom_id,arc_fecha_asigna,
			upper(esp_desc)AS esp,doc_rut,pacientes.pac_ficha,upper(doc_nombres||' '||doc_paterno||' '||doc_materno) AS doc_nombre,
			pac_rut,upper(pac_nombres||' '||pac_appat||' '||pac_apmat)AS pac_nombre,pacientes.pac_ficha,
			date_trunc('second',arc_fecha_asigna)AS fecha_asigna,esp_id,doc_id,
			pac_nombres,arc_fecha_salida,arc_estado,arc_id
			FROM archivo_fichas
			JOIN pacientes USING (pac_id)
			LEFT JOIN especialidades USING (esp_id)
			LEFT JOIN doctores USING (doc_id)
			WHERE arc_estado=1
			AND $esp AND $doc
			GROUP BY nom_id,arc_fecha_asigna,esp_id,doc_id,esp_desc,
			doc_rut,doc_nombres,doc_paterno,doc_materno,
			pacientes.pac_ficha,pac_rut,pac_nombres,pac_appat,pac_apmat,arc_fecha_salida,arc_estado,
			esp_id,doc_id,arc_id
			ORDER BY arc_fecha_salida,esp_desc,doc_nombre");
			

  if($entradas) { 
  for($i=0;$i<count($entradas);$i++) {

    ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';

	if($esp_ant!=$entradas[$i]['esp_id'] OR $doc_ant!=$entradas[$i]['doc_id']){
			$cont=1;
					
			print("<table style='width:100%;' class='lista_small'>
				<tr class='tabla_header'>
					<td style='text-align:left;' colspan=7>Programa: <b>".htmlentities($entradas[$i]['esp'])."</b> &nbsp;&nbsp;Profesional: <b>".htmlentities($entradas[$i]['doc_nombre'])."</b></td>
				</tr>
				<tr class='tabla_header'>
				<td style='width:3%;'>#</td>
				<td style='width:15%;'>Solicitado</td>
				<td style='width:12%;'>Ficha</td>
				<td style='width:12%;'>Rut</td>
				<td style='width:40%;'>Nombre</td>
				<td>Marcar</td>
				</tr>");
			
	}

    print("<tr class='$clase'
			onMouseOver='this.className=\"mouse_over\";'
			onMouseOut='this.className=\"".$clase."\";'>
			<td style='text-align:center;'>".$cont."</td>
			<td style='text-align:center;'>".$entradas[$i]['fecha_asigna']."</td>
			<td style='text-align:right;'>".$entradas[$i]['pac_ficha']."</td>
			<td style='text-align:right;'>".$entradas[$i]['pac_rut']."</td>
			<td style='text-align:left;'>".htmlentities($entradas[$i]['pac_nombre'])."</td>
			<td style='text-align:center;'>
			<input type='checkbox' id='chk_".$entradas[$i]['arc_id']."' name='chk_".$entradas[$i]['arc_id']."'
			onClick='recibir_ficha(".$entradas[$i]['arc_id'].");'>
			</td></tr>");
    
	$nom_ant=$entradas[$i]['nom_id'];
	$doc_ant=$entradas[$i]['doc_id'];
	$esp_ant=$entradas[$i]['esp_id'];
	
	$cont++;
  
	}
			print("</table>");	}

}



if($tipo==3){
	
	$doc_ant='';
	$esp_ant='';
  
	$lista = cargar_registros_obj("SELECT fesp_fecha::date AS fecha,upper(esp_desc)AS esp,doc_rut,
	pacientes.pac_ficha, upper(doc_nombres||' '||doc_paterno||' '||doc_materno) AS doc_nombre, 
	pac_rut,upper(pac_nombres||' '||pac_appat||' '||pac_apmat)AS pac_nombre, 
	fesp_estado, especialidades.esp_id,doctores.doc_id ,fesp_id,arc_estado
	FROM ficha_espontanea
	LEFT JOIN especialidades using(esp_id)
	LEFT JOIN doctores using (doc_id )
	JOIN pacientes USING (pac_id) 
	LEFT JOIN archivo_fichas ON pacientes.pac_ficha=archivo_fichas.pac_ficha AND arc_estado=1
	WHERE fesp_fecha BETWEEN '$fecha 00:00:00' AND '$fecha 23:59:59' AND fesp_estado=0 AND $esp AND $doc
	GROUP BY fesp_fecha,esp_desc, doc_rut,doc_nombres, doc_paterno,doc_materno, pacientes.pac_ficha,pac_rut,pac_nombres,
			pac_appat,pac_apmat, especialidades.esp_id,doctores.doc_id, arc_estado ,fesp_estado,fesp_id,arc_estado
	ORDER BY fesp_fecha,esp_desc,doc_nombre");
		

  if($lista)  
  for($i=0;$i<count($lista);$i++) {

    ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
	 
	 if($lista[$i]['arc_estado']==1) {
		 $checked='DISABLED CHECKED';
	 	$color='color:#FF0000;';	
	 } else {
		 $checked='';
	 	$color='';
	 }

	if($doc_ant!=$lista[$i]['doc_id'] AND $esp_ant!=$lista[$i]['esp_id']){
			$cont=1;
			
			print("<table style='width:100%;' class='lista_small'>
				<tr class='tabla_header'>
					<td style='text-align:left;' colspan=7>Programa: <b>".htmlentities($lista[$i]['esp'])."</b> &nbsp;&nbsp;Profesional: <b>".htmlentities($lista[$i]['doc_nombre'])."</b></td>
				</tr>
				<tr class='tabla_header'>
				<td style='width:3%;'>#</td>
				<td style='width:15%;'>Solicitado</td>
				<td style='width:12%;'>Ficha</td>
				<td style='width:12%;'>Rut</td>
				<td style='width:40%;'>Nombre</td>
				<td>Marcar</td>
				</tr>");
			
	}

    print("<tr class='$clase' style='$color'
			onMouseOver='this.className=\"mouse_over\";'
			onMouseOut='this.className=\"".$clase."\";'>
			<td style='text-align:center;'>".$cont."</td>
			<td style='text-align:center;'>".$lista[$i]['fecha']."</td>
			<td style='text-align:right;'>".$lista[$i]['pac_ficha']."</td>
			<td style='text-align:right;'>".$lista[$i]['pac_rut']."</td>
			<td style='text-align:left;'>".htmlentities($lista[$i]['pac_nombre'])."</td>
			<td style='text-align:center;'>
			<input type='checkbox' id='chk_".$lista[$i]['pac_ficha']."' name='chk_".$lista[$i]['pac_ficha']."' $checked 
					onClick='mover_espontanea(".$lista[$i]['fesp_id'].",".str_replace('PSI-','',$lista[$i]['pac_ficha']).");'>
			</td></tr>");
    
	$doc_ant=$lista[$i]['doc_id'];
	$esp_ant=$lista[$i]['esp_id'];
	$cont++;
  
	}
		print("</table>");	

}
?>
