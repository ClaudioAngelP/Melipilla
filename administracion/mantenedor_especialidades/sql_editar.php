<?php 
    require_once('../../conectar_db.php');
    $esp_id = $_POST['esp_id']*1;
    $desc = pg_escape_string(utf8_decode($_POST['esp_desc']));
    $codint =  pg_escape_string(utf8_decode($_POST['esp_codigo_int']));
    pg_query("START TRANSACTION;");
    if($esp_id!=0) {
        pg_query("UPDATE especialidades SET esp_desc='$desc', esp_codigo_int='$codint' WHERE esp_id=$esp_id");
        $esp=cargar_registro("SELECT * FROM especialidades WHERE esp_id=$esp_id");
    }
    else {
        pg_query("INSERT INTO especialidades VALUES (DEFAULT,'$desc',0,	1,'','$codint');");
        $esp=cargar_registro("SELECT * FROM especialidades WHERE esp_id=CURRVAL('especialidades_esp_id_seq');");
        $esp_id=$esp['esp_id']*1;		
    }
	
    $proc = isset($_POST['proce'])?'true':'false';
    $info = isset($_POST['informe'])?'true':'false';
    $orden = isset($_POST['orden'])?'true':'false';
    $equipos = pg_escape_string(utf8_decode($_POST['equipos']));
    $campos = pg_escape_string(utf8_decode($_POST['campos']));
    $presta = json_decode($_POST['presta'], true);
    
    pg_query("DELETE FROM procedimiento WHERE esp_id=$esp_id");
    
    if($proc=='true'){
        pg_query("INSERT INTO procedimiento VALUES ($esp_id, '".$esp['esp_desc']."', '$equipos', '$campos', $info, $orden);");	
    }
    
    //$pr=cargar_registros_obj("SELECT * FROM procedimiento_codigo WHERE esp_id=$esp_id", true); 
    $pr=cargar_registros_obj("SELECT * FROM prestaciones_tipo_atencion WHERE esp_id=$esp_id", true); 
    
    
    if($presta)
		for($i=0;$i<sizeof($pr);$i++) {
			$fnd=false;
            
			for($j=0;$j<sizeof($presta);$j++) {
                
				if($pr[$i]['pta_id']==$presta[$j]['pta_id']) {
                    /*
                    pg_query("UPDATE procedimiento_codigo SET
                    pc_codigo='".pg_escape_string($presta[$j]['pc_codigo'])."', 
                    pc_desc='".pg_escape_string(utf8_decode($presta[$j]['pc_desc']))."'
                    WHERE pc_id=".$presta[$j]['pc_id']);
                    */
                    if($presta[$j]['doc_id']!="")
                    {
                        pg_query("UPDATE prestaciones_tipo_atencion SET
                        esp_desc='".pg_escape_string(utf8_decode($desc))."', 
                        doc_nombres='".pg_escape_string(utf8_decode($presta[$j]['doc_nombres']))."',
                        nom_motivo='".pg_escape_string(utf8_decode($presta[$j]['nom_motivo']))."',
                        presta_desc='".pg_escape_string(utf8_decode($presta[$j]['presta_desc']))."',
                        presta_codigo='".pg_escape_string($presta[$j]['presta_codigo'])."',
                        esp_id='".pg_escape_string($esp_id)."',
                        doc_id='".pg_escape_string($presta[$j]['doc_id'])."',
                        activado=true
                        WHERE pta_id=".$presta[$j]['pta_id']);
                    }
                    else
                    {
                        pg_query("UPDATE prestaciones_tipo_atencion SET
                        esp_desc='".pg_escape_string(utf8_decode($desc))."', 
                        doc_nombres='".pg_escape_string(utf8_decode($presta[$j]['doc_nombres']))."',
                        nom_motivo='".pg_escape_string(utf8_decode($presta[$j]['nom_motivo']))."',
                        presta_desc='".pg_escape_string(utf8_decode($presta[$j]['presta_desc']))."',
                        presta_codigo='".pg_escape_string($presta[$j]['presta_codigo'])."',
                        esp_id='".pg_escape_string($esp_id)."',
                        doc_id=null,
                        activado=true
                        WHERE pta_id=".$presta[$j]['pta_id']);
                    }
                    $fnd=true;
                    break;	
				}
            }
            if(!$fnd) {
                //pg_query("DELETE FROM prestaciones_tipo_atencion WHERE pta_id=".$pr[$i]['pta_id']); 
            }
        }
	if($presta)	
		for($i=0;$i<sizeof($presta);$i++) {
			if($presta[$i]['pta_id']==0) {
                    /*
                    pg_query("INSERT INTO procedimiento_codigo VALUES (DEFAULT, $esp_id, '".pg_escape_string(utf8_decode($presta[$i]['pc_desc']))."', 
                    '".pg_escape_string($presta[$i]['pc_codigo'])."');");	
                     * 
                     */
                     $pr=cargar_registro("SELECT * FROM prestaciones_tipo_atencion WHERE esp_id=$esp_id AND doc_id=".$presta[$i]['doc_id']." AND nom_motivo='".$presta[$i]['nom_motivo']."' AND presta_codigo='".$presta[$i]['presta_codigo']."'", true); 
                     if($pr) {
						 pg_query("UPDATE prestaciones_tipo_atencion set activado=true WHERE pta_id=".$pr['pta_id']); 
					 } else {
						pg_query("INSERT INTO prestaciones_tipo_atencion VALUES (
						'".pg_escape_string(utf8_decode($desc))."',
						'".pg_escape_string(utf8_decode($presta[$i]['doc_nombres']))."',
						'".pg_escape_string(utf8_decode($presta[$i]['nom_motivo']))."',
						'".pg_escape_string(utf8_decode($presta[$i]['presta_desc']))."',
						'".pg_escape_string($presta[$i]['presta_codigo'])."',
						$esp_id,
						".pg_escape_string($presta[$i]['doc_id']).",
						DEFAULT);");
					}
			}
		}
        pg_query("COMMIT;");
?>
