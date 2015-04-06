<?php
require_once('../../conectar_db.php');

$fap_id=$_POST['fap_id']*1;

$chk=cargar_registro("SELECT * 
					  FROM fap_pabellon 
					  LEFT JOIN fap_prestacion USING(fap_id)
					  WHERE fap_id=$fap_id LIMIT 1;");
  
$estado='';
if($chk['fap_suspension']=='')
if($chk){	
	if($chk['fap_diag_cod']=='')
		$estado.='- Diag. Pre ;';
	if($chk['fap_diagnostico_1']=='')
		$estado.='- Diag. Post ;';
	if($chk['fappr_codigo']=='')
		$estado.='- Prestaciones ;';
	if($chk['fap_pab_hora3']=='' OR $chk['fap_pab_hora4']=='' OR $chk['fap_pab_hora5']=='' OR $chk['fap_pab_hora7']=='')
		$estado.='- Flujo de Horas.';
}else{
	$estado='Sin Reg.';
}

print($estado);
?>
