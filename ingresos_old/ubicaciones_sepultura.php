<?php 

	require_once('../conectar_db.php');

	if(!isset($_POST['ps_id'])) {

		$clase=pg_escape_string(utf8_decode($_POST['clase']));
		$codigo=pg_escape_string(utf8_decode($_POST['codigo']));
		$numero=($_POST['numero']*1);
		$letra=pg_escape_string(utf8_decode($_POST['letra']));
	
	} else {
		
		$ps_id=($_POST['ps_id']*1);
		
		$ps=cargar_registro("SELECT * FROM propiedad_sepultura WHERE ps_id=$ps_id");		

		$clase=pg_escape_string($ps['ps_clase']);
		$codigo=pg_escape_string($ps['ps_codigo']);
		$numero=pg_escape_string($ps['ps_numero']);
		$letra=pg_escape_string($ps['ps_letra']);
		
	}

	$estado=$_POST['estado']*1;

	list($ub,$cant_e,$cant_r)=uso_sepultura($clase,$codigo,$numero,$letra);	
	
	echo '<select id="sel_ubicaciones" name="sel_ubicaciones">';

	$opt=false;

	for($i=0;$i<sizeof($ub);$i++) {
		
		$fail=false;
		
		if(	($ub[$i][1]*1>=$cant_e AND $estado==0) OR
				($ub[$i][2]*1>=$cant_r AND $estado!=0)	) 
			$fail=true;
		
		if(!$fail) {

			if($cant_e>0)
				$ent='Ent:'.$ub[$i][1].'/'.$cant_e;

			if($cant_r>0)
				$red='Red:'.$ub[$i][2].'/'.$cant_r;

			if($cant_e>0 AND $cant_r>0)
				$esp='&nbsp;';
			else 
				$esp='';
							
			echo '<option value="'.htmlentities($ub[$i][0]).'"><b>'.htmlentities($ub[$i][0]).'</b> ['.$ent.''.$esp.''.$red.']</option>';
			$opt=true;
		}	
	}
	
	if(!$opt)
		echo '<option value="-1">(No se puede sepultar en el estado seleccionado...)</option>';

	echo '</select>';

?>