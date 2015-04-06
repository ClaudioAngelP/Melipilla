<?php
  require_once('../conectar_db.php');
  	$ipd_id=$_GET['ipd_id']*1;
		list($ipd)=cargar_registros_obj("
  		SELECT *, date_trunc('second', ipd_fecha) AS ipd_fecha FROM formulario_ipd 
      JOIN pacientes ON ipd_pac_id=pac_id
      LEFT JOIN comunas USING (ciud_id)
      LEFT JOIN provincias USING (prov_id)
      LEFT JOIN regiones USING (reg_id)
      LEFT JOIN sexo USING (sex_id)
      LEFT JOIN grupo_sanguineo USING (sang_id)
      LEFT JOIN grupos_etnicos USING (getn_id)
      LEFT JOIN prevision USING (prev_id)
      LEFT JOIN especialidades ON ipd_esp_id=esp_id
      LEFT JOIN diagnosticos ON ipd_diagnostico=diag_cod
		LEFT JOIN casos_auge ON formulario_ipd.id_caso=casos_auge.id_sigges

  		WHERE ipd_id=$ipd_id
		",true);

		if($ipd['ipd_folio']=='-1') $ipd['ipd_folio']='<i>(Sin Folio Asignado)</i>';
		
    if($ipd['ipd_confirma']=='t') 
      $ipd['ipd_confirma']='I.P.D. de Confirmaci&oacute;n <img src="../iconos/tick.png">';
    else
      $ipd['ipd_confirma']='I.P.D. de Descarte <img src="../iconos/cross.png">';
    
			
?>
		<html>
		
		<title>Informe de Proceso de Diagn&oacute;stico</title>
		
    <?php  cabecera_popup('..'); ?>
    
		<script>
		
		abrir_ficha = function(id) {
		
			inter_ficha = window.open('visualizar_ic.php?tipo=inter_ficha&inter_id='+id,
			'inter_ficha_ver', 'left='+(20)+',top='+(screen.height-470)+',width=480,height=400,status=0,scrollbars=1');
			
			inter_ficha.focus();
		
		}

		
		</script>
	
		<style>

		body {
			font-family: Arial, Helvetica, sans-serif;
			font-size: 10px;
		}

		</style>
	
		
		<body topmargin=0 leftmargin=0 rightmargin=0>
		
		<div class='sub-content'>
		<div class='sub-content'><img src='../iconos/script.png'> <b>Informe de Proceso de Diagn&oacute;stico</b></div>
		<div class='sub-content2'>
		<center>
		
<?php


    print("		
		<table style='width:100%;'>
		<tr><td style='text-align:right;width:120px;'>Fecha de Ingreso:</td><td><b>".$ipd['ipd_fecha']."</b></td></tr>
		<tr><td style='text-align:right;'>N&uacute;mero de Folio:</td><td><b>".$ipd['ipd_folio']."</b></td></tr>
		</table>
		</center>
		</div>
		
		<div class='sub-content'><img src='../iconos/user_orange.png'> <b>Datos de Paciente</b></div>
		<div class='sub-content2'>
		<table>
		<tr><td style='text-align:right;'>RUT:</td>				<td><b>".$ipd['pac_rut']."</b></td></tr>
		<tr><td style='text-align:right;'>Apellido Paterno:</td>
		<td><b><i>".$ipd['pac_appat']."</i></b></td></tr>
		<tr><td style='text-align:right;'>Apellido Materno:</td>
		<td><b><i>".$ipd['pac_apmat']."</i></b></td></tr>
		<tr><td style='text-align:right;'>Nombre(s):</td>			
		<td><b><i>".$ipd['pac_nombres']."</i></b></td></tr>
		<tr><td style='text-align:right;'>Fecha de Nacimiento:</td>
    <td>".$ipd['pac_fc_nac']."</td></tr>
		<tr><td style='text-align:right;'>Edad:</td>
    <td id='paciente_edad'>".$ipd['pac_fc_nac']."</td></tr>
		<tr><td style='text-align:right;'>Direcci&oacute;n:</td>				
    <td>".$ipd['pac_direccion']."</td></tr>
		<tr><td style='text-align:right;'>Comuna:</td>				
    <td><b>".$ipd['ciud_desc']."</b>, ".$ipd['prov_desc'].", <i>".$ipd['reg_desc']."</i>.- </td></tr>
		<tr><td style='text-align:right;'>Sexo:</td>				
    <td>".$ipd['sex_desc']."</td></tr>
		<tr><td style='text-align:right;'>Previsi&oacute;n:</td>	
    <td>".$ipd['prev_desc']."</td></tr>
		<tr><td style='text-align:right;'>Grupo Sangu&iacute;neo:</td>			
    <td><b>".$ipd['sang_desc']."</b></td></tr>
		<tr><td style='text-align:right;'>Grupo &Eacute;tnico:</td>
    <td>".$ipd['getn_desc']."</td></tr>
		</table>
		</div>
		
		<div class='sub-content'><img src='../iconos/chart_organisation.png'> <b>Informaci&oacute;n de I.P.D.</b></div>
		<div class='sub-content2'>
		<table>
		<tr><td style='text-align:right;'>Especialidad:</td>		
		<td width=60%><b>".$ipd['esp_desc']."</b></td></tr>
		<tr><td style='text-align:right;'>Patolog&iacute;a AUGE:</td>
    <td><b>".$ipd['ca_patologia']."</b></td></tr>

		<tr><td style='text-align:right;'>Resoluci&oacute;n AUGE:</td>
    <td><b>".$ipd['ipd_confirma']."</b></td></tr>

    <tr><td style='text-align:right;' valign='top'>Diagn&oacute;stico(s):</td>				
		<td style=''>".$ipd['ipd_diagnostico']."</td></tr>
		<tr><td style='text-align:right;' valign='top'>Fundamentos Cl&iacute;nicos:</td>				
		<td>".$ipd['ipd_fundamentos']."</td></tr>
		<tr><td style='text-align:right;' valign='top'>Tratamiento:</td>				
		<td>".$ipd['ipd_tratamiento']."</td></tr>
		</table>
		</div>");
		    
		/*    
		  
    print("
		<div class='sub-content'><img src='../iconos/user_comment.png'> <b>Datos del Profesional Solicitante</b></div>
		
    <div class='sub-content2'>
		
    <table style='width:100%;'>
		<tr><td style='text-align:right'>RUT:</td>
    <td style='font-weight:bold;'>".$inter[15]."</td></tr>
		<tr><td style='text-align:right'>Nombre:</td>
    <td>".htmlentities($inter[16])." ".htmlentities($inter[17])." ".htmlentities($inter[18])."</td></tr>
		</table>
		
    </div>
    ");
    
    */
		
?>
		
		</div>
		</body>
		<script>
		$('paciente_edad').innerHTML = '<i>'+window.opener.calc_edad($('paciente_edad').innerHTML)+'</i>';
		</script>
		</html>
