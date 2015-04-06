<?php 

	require_once('../../conectar_db.php');

	$pac_id=$_POST['pac_id']*1;
	
	$tmp=cargar_registro("SELECT * FROM pacientes WHERE pac_id=$pac_id LIMIT 1");
	
	$pac_ficha=$tmp['pac_ficha'];
	
	if($pac_ficha!='')
        {
            if($pac_ficha!="0")
            {
                if($pac_ficha!=0)
                {
                    exit('Paciente ya tiene ficha asignada.');
                }
            }
        }
	if(isset($_POST['ficha'])) {
		$pac_ficha=pg_escape_string($_POST['ficha']);
		$tmp2=cargar_registro("SELECT * FROM pacientes WHERE pac_ficha='$pac_ficha' LIMIT 1");
		if($tmp2) {
			exit(htmlentities('Ficha '.$pac_ficha.' ya existe: ['.$tmp2['pac_rut'].'] '.$tmp2['pac_nombres'].' '.$tmp2['pac_appat'].' '.$tmp2['pac_apmat'].'.'));
		}
		pg_query("UPDATE pacientes SET pac_ficha='$pac_ficha' WHERE pac_id=$pac_id;");
                pg_query("update pacientes set pac_ficha=lpad(pac_ficha, 8, '00000000') WHERE pac_id=$pac_id;");
		exit(htmlentities('Ficha para ['.$tmp['pac_rut'].'] '.$tmp['pac_nombres'].' '.$tmp['pac_appat'].' '.$tmp['pac_apmat'].' asignada exitosamente.'));
	} else {
		pg_query("UPDATE pacientes SET pac_ficha=NEXTVAL('pacientes_pac_ficha_seq') WHERE pac_id=$pac_id;");
                pg_query("update pacientes set pac_ficha=lpad(pac_ficha, 8, '00000000') WHERE pac_id=$pac_id;");
		$tmp=cargar_registro("SELECT * FROM pacientes WHERE pac_id=$pac_id LIMIT 1");
		exit(htmlentities('Ficha para ['.$tmp['pac_rut'].'] '.$tmp['pac_nombres'].' '.$tmp['pac_appat'].' '.$tmp['pac_apmat'].' es [[['.$tmp['pac_ficha'].']]], asignada exitosamente.'));	
	}

?>