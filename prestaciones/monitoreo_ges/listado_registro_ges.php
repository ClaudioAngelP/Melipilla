<?php 

	require_once('../../conectar_db.php');
	
	$mon_id=$_POST['mon_id']*1;
	
	/*

		SELECT *, date_trunc('second', COALESCE(in_fecha, monr_fecha)) AS fecha, COALESCE(in_comentarios, monr_observaciones) AS observa
		FROM monitoreo_ges_registro AS mgr
		LEFT JOIN lista_dinamica_caso USING (monr_id)
		LEFT JOIN lista_dinamica_instancia AS ldi USING (caso_id)
		LEFT JOIN funcionario ON COALESCE(ldi.func_id, monr_func_id)=funcionario.func_id 
		LEFT JOIN lista_dinamica_condiciones AS ldc2 ON COALESCE(ldi.id_condicion, monr_clase::integer)=ldc2.id_condicion
		LEFT JOIN lista_dinamica_bandejas AS ldb ON COALESCE(ldi.codigo_bandeja, monr_subclase)=ldb.codigo_bandeja
		WHERE mgr.mon_id=$mon_id AND NOT (ldi.func_id=0 AND monr_estado>0) ORDER BY fecha DESC, monr_estado ASC;


	 */
	
	$m=cargar_registros_obj("
		SELECT *, date_trunc('second', (monr_fecha)) AS fecha, (monr_observaciones) AS observa
		FROM monitoreo_ges_registro AS mgr
		LEFT JOIN lista_dinamica_caso USING (monr_id)
		LEFT JOIN funcionario ON (monr_func_id)=funcionario.func_id 
		LEFT JOIN lista_dinamica_condiciones AS ldc2 ON (monr_clase::integer)=ldc2.id_condicion
		LEFT JOIN lista_dinamica_bandejas AS ldb ON (monr_subclase)=ldb.codigo_bandeja
		WHERE mgr.mon_id=$mon_id ORDER BY fecha DESC, monr_estado ASC;", true);
	
	if($m)
	for($i=0;$i<sizeof($m);$i++) {
		
		if($m[$i]['monr_subcondicion']!='') {
			$subcond='<i>('.$m[$i]['monr_subcondicion'].')</i>';
		} else {
			$subcond='';
		}
		
		switch($m[$i]['monr_estado']*1) {
			case 0: $estado='Activo'; $color='green'; $style=''; break;
			case 1: $estado='Terminado'; $color='blue'; $style=''; break;
			case 2: $estado='Anulado'; $color='red'; $style='text-decoration:line-through;'; break;
		} 
		
		print("
		<table style='width:100%;font-size:13px;' cellspacing=0 cellpadding=3>
		<tr><td class='tabla_header' style='text-align:right;width:20%;'>Fecha Registro:</td><td class='tabla_fila' colspan=2>".$m[$i]['fecha']."</td></tr>
		<tr><td class='tabla_header' style='text-align:right;'>Condici&oacute;n:</td><td class='tabla_fila' style='font-size:16px;color:$color;$style' colspan=2><b>".$m[$i]['nombre_condicion']." $subcond</b>  [<u>Estado: <b>$estado</b></u>]</td></tr>
		");
		
		if($m[$i]['monr_descripcion']!='')
			print("<tr><td class='tabla_header' style='text-align:right;'>Detalle:</td><td class='tabla_fila' style='color:yellowgreen;' colspan=2><b>".$m[$i]['monr_descripcion']."</b></td></tr>");
		
		print("
		<tr><td class='tabla_header' style='text-align:right;'>Funcionario:</td><td class='tabla_fila' colspan=2>".$m[$i]['func_nombre']."</td></tr>
		");
		
		if($m[$i]['monr_fecha_proxmon']!='')
		print("
		<tr><td class='tabla_header' style='text-align:right;'>Fecha Prox. Monitoreo:</td><td class='tabla_fila' colspan=2><b>".$m[$i]['monr_fecha_proxmon']."</b></td></tr>
		");
		
		if($m[$i]['monr_fecha_evento']!='')
		print("
		<tr><td class='tabla_header' style='text-align:right;'>Fecha Evento:</td><td class='tabla_fila' colspan=2><b>".$m[$i]['monr_fecha_evento']."</b></td></tr>
		");
		
		if($m[$i]['nombre_bandeja']=='') $m[$i]['nombre_bandeja']='<i>(Sin Bandeja...)</i>';
		
		print("<tr><td class='tabla_header' style='text-align:right;'>Proceso Actual:</td><td class='tabla_fila' style='color:blue;' colspan=2><span style='cursor:pointer;' onClick='ver_caso(".$m[$i]['caso_id'].");'><b>".$m[$i]['nombre_bandeja']."</b> <img src='../../iconos/magnifier.png' style='cursor:pointer;width:12px;height:12px;' /></span></td></tr>");
		
		if($m[$i]['observa']!='')
			print("<tr><td class='tabla_header' style='text-align:right;'>Observaciones:</td><td class='tabla_fila' colspan=2><i>".$m[$i]['observa']."</i></td></tr>");
		
		
		print("</table>");
	}

?>
