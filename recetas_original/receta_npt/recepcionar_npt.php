<?php require_once('../../conectar_db.php');

	$action=$_POST['action'];
	
	$ids=explode('|',trim($_POST['ids'],'| '));
	$idsno=explode('|',trim($_POST['ids2'],'| '));
	$tipo=$_POST['tipo'];
	$entrega=htmlentities($_POST['prov_rut'].'|'.$_POST['prov_nombre']);
	$func_id=$_POST['func_id'];
	$fnpt_id=$_POST['fnpt_id'];
	$fnpt_rut=$_POST['nomfuncionpt'];
	$fnpt_nombre=$_POST['fnpt_nombre'];
	$temperatura=$_POST['temperatura'];
	$hora=$_POST['hora'];
	$fecha_recep=pg_escape_string($_POST['fecha_recep']);
	$prov_id=4356;
	$tipo_doc=$_POST['doc_asociado'];
	$doc_num=$_POST['doc_num'];
	$comentarios=pg_escape_string(utf8_decode($_POST['comentarios']));
	
	//Inicio recepción NPT
	/*pg_query("INSERT INTO documento VALUES (DEFAULT,$prov_id,$tipo_doc,$doc_num,$_global_iva,null,
		      0, null,'$comentarios' )");
		      
 	pg_query("INSERT INTO logs VALUES (DEFAULT, $func_id2, 1, 
				current_timestamp,0,CURRVAL('documento_doc_id_seq'), NULL )");
	
	$log=cargar_registro("SELECT CURRVAL('logs_log_id_seq')AS id");*/
	
	$cant=0;
	$anterior=0;

	if($action==1){
		for($i=0;$i<sizeof($ids);$i++){
			$id=$ids[$i];		
			pg_query("UPDATE receta_npt SET	rnpt_entrega_nombre='$entrega',	rnpt_func_id2=$func_id,
						rnpt_fecha_recep='".$fecha_recep." ".$hora."', rnpt_temperatura='$temperatura',	rnpt_estado=1,
						rnpt_doc_tipo=$tipo_doc,rnpt_doc_num=$doc_num,rnpt_comentario='$comentarios'
					WHERE rnpt_id=$id;");
		}
		for($n=0;$n<sizeof($idsno);$n++){
			$id2=$idsno[$n];		
			pg_query("UPDATE receta_npt SET	rnpt_entrega_nombre='',	rnpt_func_id2=null,
						rnpt_fecha_recep=null, rnpt_temperatura='',	rnpt_estado=0,
						rnpt_doc_tipo=null,rnpt_doc_num=null,rnpt_comentario=''
					WHERE rnpt_id=$id2;");
		}
	
	//$doc=cargar_registro("SELECT CURRVAL('documento_doc_id_seq')AS id");
	if($tipo==0)
		print('Recepci&oacute;n Realizada Exitosamente.');
	else
		print('Recepci&oacute;n Modificada Exitosamente.');
	//Fin recepción NPT
	}
	
	if($action==2){	
		//Inicio cargo centro costos
	
	    $ids2=str_replace('|',',',trim($_POST['ids'],'| '));
	    
	    if($fnpt_id==0){
				pg_query("INSERT INTO func_npt VALUES(DEFAULT,'$fnpt_rut','$fnpt_nombre','');");
				$fnptid=cargar_registro("SELECT CURRVAL('func_npt_fnpt_id_seq') AS id;");
				$fnpt_id=$fnptid['id'];
		}
	    
	    pg_query("UPDATE receta_npt SET rnpt_estado=2,
	    				rnpt_fecha_desp='".$fecha_recep." ".$hora."',
	    				rnpt_func_id3=$fnpt_id 
	    			WHERE rnpt_id IN ($ids2)");
	   	    
	     $r=cargar_registros_obj("
	     	select centro_ruta, art_id, count(*) AS cantidad from (SELECT centro_ruta, 
			(CASE WHEN rnpt_detalle ilike '%SOLUCI%' THEN 
			(CASE WHEN rnpt_detalle ilike 'SOLUCION NPT MAGISTRAL TIPO 1%' THEN 11076
			WHEN rnpt_detalle ilike 'SOLUCION NPT MAGISTRAL TIPO 2%' THEN 11077
			WHEN rnpt_detalle ilike 'SOLUCION NPT MAGISTRAL TIPO 3%' THEN 11078
			WHEN rnpt_detalle ilike 'SOLUCION NPT MAGISTRAL TIPO 4%' THEN 11079
			WHEN rnpt_detalle ilike 'SOLUCION NPT MAGISTRAL TIPO 5%' THEN 15059
			END) 
		ELSE (CASE 
			WHEN rnpt_volumen_total<=250 THEN 11071
			WHEN rnpt_volumen_total<=500 THEN 11074
			WHEN rnpt_volumen_total<=1000 THEN 11075
			WHEN rnpt_volumen_total<=2000 THEN 11072
			ELSE 11073 END
			)
		END) AS art_id FROM receta_npt
			WHERE rnpt_id IN ($ids2)
			) AS foo
			group by centro_ruta, art_id
			order by centro_ruta
		");
			
	     $centro='';
	     
	     for($n=0;$n<sizeof($r);$n++){
			
			if($centro=='' OR $centro!=$r[$n]['centro_ruta']){
				$centro=$r[$n]['centro_ruta'];
				pg_query("INSERT INTO pedido VALUES(DEFAULT,nextval('global_numero_pedido'),current_timestamp,
	   					 ".($_SESSION['sgh_usuario_id']*1).",0,-1,3,
	    				'Creado junto con traslado de artículos asociado.',2,true,false,'$centro')");
	    		pg_query("INSERT INTO logs VALUES(DEFAULT,".($_SESSION['sgh_usuario_id']*1).",15,
	    		current_timestamp,0,NULL,CURRVAL('pedido_pedido_id_seq'),'')");
	    		pg_query("INSERT INTO cargo_centro_costo VALUES(CURRVAL('logs_log_id_seq'),'$centro')");
			}
					
	   		pg_query("INSERT INTO stock VALUES (DEFAULT,".$r[$n]['art_id'].",3,-".$r[$n]['cantidad'].",
			CURRVAL('logs_log_id_seq'),null,0)");
				
			pg_query("INSERT INTO pedido_detalle VALUES(DEFAULT,CURRVAL('pedido_pedido_id_seq'),
	        ".$r[$n]['art_id'].",".$r[$n]['cantidad'].",true)");
		
				
		} 
		
		//Fin cargo centro costos
		$pedido=cargar_registro("SELECT CURRVAL('pedido_pedido_id_seq')AS id");  
		
		print(json_encode(Array('2',$pedido['id'])));  
	}        		
	//print($doc['id']);
		
?>