<?php

require_once('../../conectar_db.php');

$fap_id=$_POST['fap_id']*1;
$fecha_ingreso=$_POST['fecha1'];
$fecha_egreso=$_POST['fecha2'];
$hora_ingreso=$_POST['fap_pab_hora9'];
$hora_egreso=$_POST['fap_pab_hora10'];
$obs=pg_escape_string(utf8_decode($_POST['rec_observacion']));

if($fecha_egreso=='') $fecha_egreso="NULL"; else $fecha_egreso="'$fecha_egreso'"; 
if($hora_egreso=='') $hora_egreso="NULL"; else $hora_egreso="'$hora_egreso'"; 


pg_query("UPDATE fap_pabellon SET 
		fap_pab_hora9='$hora_ingreso',
		fap_pab_hora10=$hora_egreso,
		fap_fc_ingreso_recu='$fecha_ingreso',
		fap_fc_egreso_recu=$fecha_egreso,
		fap_observacion_recu='$obs'
		WHERE fap_id=$fap_id;");
		
		
print(json_encode(true));


?>
