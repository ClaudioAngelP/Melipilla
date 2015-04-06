<?php 

	require_once('../../conectar_db.php');
	
	$accion = $_POST['accion'];
	$lista_id =	$_POST['lista_id']*1;
	$nombre =	pg_escape_string(utf8_decode($_POST['lista_nombre']));
	$campos_tabla	=	pg_escape_string(utf8_decode($_POST['campos_tabla']));
	$campos_formulario	=	pg_escape_string(utf8_decode($_POST['campos_formulario']));
	$lista_reporte	=	pg_escape_string(utf8_decode($_POST['lista_reporte']));
	$lista_condiciones	=	pg_escape_string(utf8_decode($_POST['lista_condiciones']));
	$alerta_amarilla	=	($_POST['aler_amarilla']*1);
	$alerta_roja	=	($_POST['aler_roja']*1);
	$correo_alerta	=	pg_escape_string(utf8_decode($_POST['correo_alerta']));
	$destino_arr = json_decode($_POST['destino'], true);
	$destino_ids="NULL";
	
	
	
	
	if(sizeof($destino_arr)>0){
		
		$destino_ids="'{";

		for($d=0;$d<sizeof($destino_arr);$d++) {
	
			if($d+1<sizeof($destino_arr)){
				$destino_ids.=$destino_arr[$d].",";
			}else{
				$destino_ids.=$destino_arr[$d]."}'";
			}
	
		}
	}
	
	pg_query("START TRANSACTION;");

	if($lista_id!=0) {

		pg_query("UPDATE lista_dinamica SET 
				lista_nombre='$nombre', 
				lista_campos_tabla='$campos_tabla',
				lista_campos_formulario='$campos_formulario',
				lista_reporte='$lista_reporte',
				lista_condiciones='$lista_condiciones',
				lista_plazo_alerta_amarilla=$alerta_amarilla,
				lista_plazo_alerta_roja=$alerta_roja,
				lista_correo_alerta='$correo_alerta',
				lista_id_destino=$destino_ids
				WHERE lista_id=$lista_id");
				
		print("UPDATE lista_dinamica SET 
				lista_nombre='$nombre', 
				lista_campos_tabla='$campos_tabla',
				lista_campos_formulario='$campos_formulario',
				lista_reporte='$lista_reporte',
				lista_condiciones='$lista_condiciones',
				lista_plazo_alerta_amarilla=$alerta_amarilla,
				lista_plazo_alerta_roja=$alerta_roja,
				lista_correo_alerta='$correo_alerta',
				lista_id_destino=$destino_ids
				WHERE lista_id=$lista_id");
				
			
	} else {
		
		pg_query("INSERT INTO lista_dinamica VALUES (
			DEFAULT,
			'$nombre',
			'$campos_tabla',
			'$campos_formulario',
			'$lista_reporte',
			'$lista_condiciones',
			$destino_ids,
			null,
			$alerta_amarilla,
			$alrta_roja,
			'$correo_alerta'
		);");
		
		
		print("INSERT INTO lista_dinamica VALUES (
			DEFAULT,
			'$nombre',
			'$campos_tabla',
			'$campos_formulario',
			'$lista_reporte',
			'$lista_condiciones',
			$destino_ids,
			null,
			$alerta_amarilla,
			$alrta_roja,
			'$correo_alerta'
		);");
	}	

	pg_query("COMMIT;");
	

?>