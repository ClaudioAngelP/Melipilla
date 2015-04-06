<?php

  require_once('../conectar_db.php');

  	$presta_id=$_GET['presta_id']*1;
  		
		list($oa)=cargar_registros_obj("
  		SELECT *, date_trunc('second', presta_fecha) AS presta_fecha, 
		p1.prev_desc AS prev_desc1, p2.prev_desc AS prev_desc2		
		  		
  		FROM prestacion 
		  JOIN pacientes USING (pac_id)
		  LEFT JOIN prevision AS p2 ON p2.prev_id=prestacion.prev_id
		  LEFT JOIN especialidades USING (esp_id)
		LEFT JOIN instituciones USING (inst_id)
		LEFT JOIN prestacion_origen USING (porigen_id)

  		WHERE presta_id=$presta_id
		",true);
	if($oa['inst_nombre']=='') $oa['inst_nombre']='<i>Indefinida.</i>';
	if($oa['ca_patologia']=='') $oa['ca_patologia']='<i>Eventos Sin Caso</i>';
	if($oa['prev_desc1']=='') $oa['prev_desc1']='<i>Indefinida.</i>';
	if($oa['prev_desc2']=='') $oa['prev_desc2']='<i>Indefinida.</i>';

	if($oa['id_sigges']==0) $oa['id_sigges']='<i>(n/a)</i>';

?>
	<html>
	<title>Registro de Prestaci&oacute;n</title>
   <?php  cabecera_popup('..'); ?>
	<script>
		abrir_ficha = function(id) {
			inter_ficha = window.open('visualizar_ic.php?tipo=inter_ficha&inter_id='+id,
			inter_ficha.focus();
		}
	</script>
	<style>
		body {
	</style>
	
	<body topmargin=0 leftmargin=0 rightmargin=0>
		<div class='sub-content'>
		<div class='sub-content'><img src='../iconos/script.png'> <b>Registro de Prestaci&oacute;n</b></div>
		<div class='sub-content2'>
		<center>
<?php
    print("		
		<table style='width:100%;'>
		<tr><td style='text-align:right;width:120px;'>Instituci&oacute;n:</td><td><b>".$oa['inst_nombre']."</b></td></tr>
		<tr><td style='text-align:right;'>N&uacute;mero de Folio:</td><td><b>".$oa['id_sigges']."</b></td></tr>
		</table>
		</center>
		</div>
		<div class='sub-content'><img src='../iconos/user_orange.png'> <b>Datos de Paciente</b></div>
		<div class='sub-content2'>
		<table>
		<tr><td style='text-align:right;'>RUT:</td>				<td><b>".$oa['pac_rut']."</b></td></tr>
		<tr><td style='text-align:right;'>Apellido Paterno:</td>
		<td><b><i>".$oa['pac_appat']."</i></b></td></tr>
		<tr><td style='text-align:right;'>Apellido Materno:</td>
		<td><b><i>".$oa['pac_apmat']."</i></b></td></tr>
		<tr><td style='text-align:right;'>Nombre(s):</td>			
		<td><b><i>".$oa['pac_nombres']."</i></b></td></tr>
		<tr><td style='text-align:right;'>Fecha de Nacimiento:</td>
    <td>".$oa['pac_fc_nac']."</td></tr>
		<tr><td style='text-align:right;'>Edad:</td>
    <td id='paciente_edad'>".$oa['pac_fc_nac']."</td></tr>
		<tr><td style='text-align:right;'>Direcci&oacute;n:</td>				
    <td>".$oa['pac_direccion']."</td></tr>
		<tr><td style='text-align:right;'>Comuna:</td>				
    <td><b>".$oa['ciud_desc']."</b>, ".$oa['prov_desc'].", <i>".$oa['reg_desc']."</i>.- </td></tr>
		<tr><td style='text-align:right;'>Sexo:</td>				
    <td>".$oa['sex_desc']."</td></tr>
		<tr><td style='text-align:right;'>Previsi&oacute;n Actual:</td>	
    <td>".$oa['prev_desc1']."</td></tr>
		<tr><td style='text-align:right;'>Grupo Sangu&iacute;neo:</td>			
    <td><b>".$oa['sang_desc']."</b></td></tr>
		<tr><td style='text-align:right;'>Grupo &Eacute;tnico:</td>
    <td>".$oa['getn_desc']."</td></tr>
		</table>
		</div>
		
		<div class='sub-content'><img src='../iconos/chart_organisation.png'> <b>Detalle de la Prestaci&oacute;n</b></div>
		<div class='sub-content2'>
		<table>
		<tr><td style='text-align:right;width:80px;'>Or&iacute;gen del Registro:</td>
    	<td><b>".$oa['porigen_nombre']."</b></td></tr>
		<tr><td style='text-align:right;'>Caso AUGE:</td>
    	<td><b>".$oa['ca_patologia']."</b></td></tr>
		<tr><td style='text-align:right;'>Prevision:</td>		
		<td width=60%><b>".$oa['prev_desc2']."</b></td></tr>
		<tr><td style='text-align:right;' valign='top'>Prestaci&oacute;n:</td>				
		<td style='font-weight:bold;'>".$oa['presta_codigo_v']." ( x ".$oa['presta_cant'].")</td></tr>
		<tr><td style='text-align:right;' valign='top'></td>				
		</table>
		print("</div>");
?>
		</div>
		</body>
		<script>
		$('paciente_edad').innerHTML = '<i>'+window.opener.calc_edad($('paciente_edad').innerHTML)+'</i>';
		</script>
		</html>