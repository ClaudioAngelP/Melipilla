<?php
    require_once('../../conectar_db.php');
    $examenes_sol=$_POST['presta_examen'];
    $pac_id=$_POST['pac_id'];
    $nomd_original=$_POST['nomd_id'];
    
    $nom=cargar_registro("
	SELECT nomina.*,esp_centro_ruta from nomina 
	JOIN nomina_detalle USING (nom_id) 
	LEFT JOIN especialidades on esp_id=nom_esp_id 
	where nomd_id=".$nomd_original."");
	
    if($nom) {
		$doc_id=$nom['nom_doc_id'];
		$esp_id=$nom['nom_esp_id'];
		$esp_centro_ruta=$nom['esp_centro_ruta'];
	}
    
    if($examenes_sol)
    {
        if($examenes_sol!='')
        {
            //------------------------------------------------------------------
            $examenes = array();
            $tipo_examen='null';
            $cont=0;
            for($i=0;$i<count($examenes_sol);$i++)
            {
                if($tipo_examen!=$examenes_sol[$i]['tipo_examen'])
                {
                    $examenes[$cont][0]=$examenes_sol[$i]['tipo_examen'];
                    $examenes[$cont][1]=$examenes_sol[$i]['esp'];
                    $tipo_examen=$examenes_sol[$i]['tipo_examen'];
                    $cont=$cont+1;
                }
            }
            pg_query($conn, 'START TRANSACTION;');
            for($i=0;$i<count($examenes);$i++)
            {
                $tipo_examen=$examenes[$i][0];
                $esp=$examenes[$i][1];
                
                       
                pg_query($conn, "INSERT INTO solicitud_examen VALUES 
                (DEFAULT, $esp,'$tipo_examen',$pac_id,".($_SESSION['sgh_usuario_id']*1).", current_timestamp, $nomd_original,0,false,$doc_id,$esp_id,null,'$esp_centro_ruta');");
                
                for($x=0;$x<count($examenes_sol);$x++)
                {
                    if($tipo_examen==$examenes_sol[$x]['tipo_examen'])
                    {
                        if(strstr($examenes_sol[$x]['desc'],'['))
                        {
                            $array_organo=explode("[", $examenes_sol[$x]['desc']);
                            $organo=trim($array_organo[1], ']');
                            //$organo=trim($myString, ',');
                        }
                        else
                        {
                            $organo='';
                        }
                        //print("INSERT INTO solicitud_examen_detalle VALUES (DEFAULT, CURRVAL('solicitud_examen_sol_exam_id_seq'),'".$examenes_sol[$x]['pc_id']."','$organo','".$examenes_sol[$x]['cantidad']."',0,0,null,'".$examenes_sol[$x]['obs_examen']."');");

                        pg_query($conn, "INSERT INTO solicitud_examen_detalle VALUES (DEFAULT,CURRVAL('solicitud_examen_sol_exam_id_seq'),'".$examenes_sol[$x]['pc_id']."' ,'$organo','".$examenes_sol[$x]['cantidad']."',0,0,null,null,null,null,null,null,'".$examenes_sol[$x]['obs_examen']."');");


                    }
                }
            }
            pg_query($conn, 'COMMIT;');
            //------------------------------------------------------------------
        }
    }
    print(json_encode($solicitudes));
?>
