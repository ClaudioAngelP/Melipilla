<?php 

	require_once('../conectar_db.php');
	
	$in_id=$_POST['in_id']*1;
	$in_comentarios=pg_escape_string(utf8_decode($_POST['in_comentarios']));

	$in=cargar_registro("SELECT *,
						 (CURRENT_TIMESTAMP::date-in_fecha::date)::integer AS dias
						 FROM lista_dinamica_instancia 
						 JOIN lista_dinamica_caso USING (caso_id)
						 JOIN pacientes USING (pac_id)
						 WHERE in_id=$in_id", true);

	$lista_id=$in['lista_id']*1;
		
	$li=cargar_registro("SELECT * FROM lista_dinamica WHERE lista_id=$lista_id");	
	
	$campos=explode('|', $li['lista_campos_formulario']);

	$valores='';
	
	for($i=0;$i<sizeof($campos);$i++) {

		if(strstr($campos[$i],'>>>')) {
			$cmp=explode('>>>',$campos[$i]);
			$nombre=htmlentities($cmp[0]); $tipo=$cmp[1]*1;
		} else {
			$cmp=$campos[$i]; $tipo=2;
		}

		if($tipo==0 OR $tipo==1) {
			$valores.=(isset($_POST['campo_'.$i]))?'true':'false';	
		} else {
			$valores.=pg_escape_string(utf8_decode($_POST['campo_'.$i]));
		}
		
		if($i<(sizeof($campos)-1)) $valores.='|';	
				
	}
	
	pg_query("UPDATE lista_dinamica_instancia SET
		func_id=".$_SESSION['sgh_usuario_id'].",
		in_valor='".$valores."',
		in_comentarios='".$in_comentarios."'
	WHERE in_id=$in_id");	

?>
