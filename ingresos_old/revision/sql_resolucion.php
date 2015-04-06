<?php



  require_once('../../conectar_db.php');



		$id=($_GET['inter_id']*1);

		$institucion=($_GET['institucion']*1);

		$estado=$_GET['estado'];

		

    $prioridad=$_GET['prioridad']*1;

		$observa=iconv("UTF-8", "ISO-8859-1",$_GET['observaciones']);

		

		pg_query($conn,"

		UPDATE interconsulta 

    SET inter_estado=$estado, 

        inter_rev_med='$observa', 

        inter_prioridad=$prioridad

		WHERE inter_id=$id; 

		");

		

		if($estado==1) {

    

    $datos = pg_query($conn,"

    SELECT 

    inter_especialidad,

    inter_pac_id, inter_diag_cod, inter_inst_id1

    FROM 

    interconsulta

    WHERE 

    inter_id=$id;

    ");

    

    $data = pg_fetch_row($datos);

    

    $especialidad = $data[0];

    $paciente = $data[1];

    $diag = pg_escape_string($data[2]);

    $institucion = $data[3];

    

    if(file_exists('../../agenda_medica')) {

      pg_query($conn,"

        INSERT INTO lista_espera 

        (esp_id,pac_id,list_auge,list_fc_ingreso,list_tipo_pac,

        diag_cod,id_origen,prior_id) VALUES (

        $especialidad,$paciente,0::bit,now(),0::bit,'$diag',$institucion,$prioridad

        )

      ");

    

    }

    

    if($estado==1) {

    

      // Inicia Episodio Clínico que agrupará el tratamiento del paciente...

      

      list($ic)=cargar_registros_obj("SELECT * FROM interconsulta WHERE inter_id=".$id);

      

      $pac_id=$ic['inter_pac_id'];

      $pat_id=$ic['inter_pat_id'];

      $patrama_id=$ic['inter_patrama_id'];

      

      /*
      pg_query($conn, "

        INSERT INTO episodio_clinico VALUES (

        DEFAULT, $pac_id, now(), now(), null, 

        $pat_id, -1, 0, $patrama_id, $id

        );

      ");
      */

    

    }



    

    }

	

		print("OK");



?>