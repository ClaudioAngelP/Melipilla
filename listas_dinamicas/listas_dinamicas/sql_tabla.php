<?php 

	require_once('../conectar_db.php');
	
	$lista_id=$_POST['lista_id']*1;
	
	$li=cargar_registro("SELECT * FROM lista_dinamica WHERE lista_id=$lista_id");	

	$data=pg_query("SELECT * FROM (SELECT *, 
					(CURRENT_TIMESTAMP-in_fecha) AS dias
					FROM lista_dinamica_instancia 
					JOIN lista_dinamica_caso USING (caso_id)
					JOIN pacientes USING (pac_id)
					WHERE lista_id=$lista_id AND in_estado=0) AS foo ORDER BY dias DESC;");

	$j=0;
	
	while($r=pg_fetch_assoc($data)) {

		$in_id=$r['in_id'];
		$caso_id=$r['caso_id'];
		$lista_id=$_POST['sel_'.$in_id]*1;
		
		if($lista_id!=0) {
			pg_query("UPDATE lista_dinamica_instancia SET func_id=".$_SESSION['sgh_usuario_id'].", in_estado=1 WHERE in_id=$in_id");
			pg_query("INSERT INTO lista_dinamica_instancia 
			VALUES (DEFAULT, $caso_id, $lista_id, current_timestamp, 0, 0, '');");
		}
		
		$campos=explode('|', $li['lista_campos_tabla']);

		$valores='';
		
		for($i=0;$i<sizeof($campos);$i++) {

			if(strstr($campos[$i],'>>>')) {
				$cmp=explode('>>>',$campos[$i]);
				$nombre=htmlentities($cmp[0]); $tipo=$cmp[1]*1;
			} else {
				$cmp=$campos[$i]; $tipo=2;
			}

			if($tipo==0 OR $tipo==1) {
				$valores.=(isset($_POST['campo_'.$i.'_'.$in_id]))?'true':'false';	
			} else {
				$valores.=pg_escape_string(utf8_decode($_POST['campo_'.$i.'_'.$in_id]));
			}
			
			if($i<(sizeof($campos)-1)) $valores.='|';	
					
		}
		
		pg_query("UPDATE lista_dinamica_instancia SET in_valor_tabla='$valores' WHERE in_id=$in_id");

	}

?>
