<?php

	  require_once('../../conectar_db.php');


		$bolnum = $_POST['bolnum']*1;
		$func_id = $_POST['func_id']*1;
		$prestaciones=json_decode($_POST['prestaciones']);	
		$tipo= $_POST['tipo']*1;
		$fecha='current_timestamp';	
	
	if($tipo==0){
		for($i=0;$i<sizeof($prestaciones);$i++)
		{
			if($prestaciones[$i]->r1t){
			$doc_id=$prestaciones[$i]->r1t*1;		
			$beq_tipo=1;
			pg_query($conn, "
				INSERT INTO boletin_equipo
				VALUES (
				DEFAULT,
				$bolnum,
				$doc_id,			
				$beq_tipo
				)
			");
			}
			
			if($prestaciones[$i]->r2t){
			$doc_id=$prestaciones[$i]->r2t*1;		
			$beq_tipo=2;
			pg_query($conn, "
				INSERT INTO boletin_equipo
				VALUES (
				DEFAULT,
				$bolnum,
				$doc_id,			
				$beq_tipo
				)
			");
			}
			if($prestaciones[$i]->r3t){
			$doc_id=$prestaciones[$i]->r3t*1;		
			$beq_tipo=3;
			pg_query($conn, "
				INSERT INTO boletin_equipo
				VALUES (
				DEFAULT,
				$bolnum,
				$doc_id,			
				$beq_tipo
				)
			");
			}
			if($prestaciones[$i]->r4t){
			$doc_id=$prestaciones[$i]->r4t*1;		
			$beq_tipo=4;
			pg_query($conn, "
				INSERT INTO boletin_equipo
				VALUES (
				DEFAULT,
				$bolnum,
				$doc_id,			
				$beq_tipo
				)
			");
			}
			if($prestaciones[$i]->r5t){
			$doc_id=$prestaciones[$i]->r5t*1;		
			$beq_tipo=5;
			pg_query($conn, "
				INSERT INTO boletin_equipo
				VALUES (
				DEFAULT,
				$bolnum,
				$doc_id,			
				$beq_tipo
				)
			");
			}
			if($prestaciones[$i]->r6t){
			$doc_id=$prestaciones[$i]->r6t*1;		
			$beq_tipo=6;
			pg_query($conn, "
				INSERT INTO boletin_equipo
				VALUES (
				DEFAULT,
				$bolnum,
				$doc_id,			
				$beq_tipo
				)
			");
			}
			if($prestaciones[$i]->r7t){
			$doc_id=$prestaciones[$i]->r7t*1;		
			$beq_tipo=7;
			pg_query($conn, "
				INSERT INTO boletin_equipo
				VALUES (
				DEFAULT,
				$bolnum,
				$doc_id,			
				$beq_tipo
				)
			");
			}
			
			
		}
		}else{
			for($i=0;$i<sizeof($prestaciones);$i++)
		{
			if($prestaciones[$i]->r1t){
			$doc_id=$prestaciones[$i]->r1t*1;		
			$beq_tipo=1;
			pg_query($conn, "
				UPDATE boletin_equipo set beq_doc_id=$doc_id WHERE bolnum=$bolnum AND beq_tipo=$beq_tipo ");
			}
			
			if($prestaciones[$i]->r2t){
			$doc_id=$prestaciones[$i]->r2t*1;		
			$beq_tipo=2;
			pg_query($conn, "
				UPDATE boletin_equipo set beq_doc_id=$doc_id WHERE bolnum=$bolnum AND beq_tipo=$beq_tipo ");
			}
			
			if($prestaciones[$i]->r3t){
			$doc_id=$prestaciones[$i]->r3t*1;		
			$beq_tipo=3;
			pg_query($conn, "
				UPDATE boletin_equipo set beq_doc_id=$doc_id WHERE bolnum=$bolnum AND beq_tipo=$beq_tipo ");
			}
			
			if($prestaciones[$i]->r4t){
			$doc_id=$prestaciones[$i]->r4t*1;		
			$beq_tipo=4;
			pg_query($conn, "
				UPDATE boletin_equipo set beq_doc_id=$doc_id WHERE bolnum=$bolnum AND beq_tipo=$beq_tipo ");
			}
			
			if($prestaciones[$i]->r5t){
			$doc_id=$prestaciones[$i]->r5t*1;		
			$beq_tipo=5;
			pg_query($conn, "
				UPDATE boletin_equipo set beq_doc_id=$doc_id WHERE bolnum=$bolnum AND beq_tipo=$beq_tipo ");
			}
			
			if($prestaciones[$i]->r6t){
			$doc_id=$prestaciones[$i]->r6t*1;		
			$beq_tipo=6;
			pg_query($conn, "
				UPDATE boletin_equipo set beq_doc_id=$doc_id WHERE bolnum=$bolnum AND beq_tipo=$beq_tipo ");
			}
			
			if($prestaciones[$i]->r7t){
			$doc_id=$prestaciones[$i]->r7t*1;		
			$beq_tipo=7;
			pg_query($conn, "
				UPDATE boletin_equipo set beq_doc_id=$doc_id WHERE bolnum=$bolnum AND beq_tipo=$beq_tipo ");
			}
			
			
			
		}
		}
		
			
	print(json_encode(array(true,$bolnum,$tipo)));
?>
