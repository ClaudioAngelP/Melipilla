<?php

  require_once('../conectar_db.php');

  	$ccaso_id=$_GET['ccaso_id']*1;
  		
		list($cc)=cargar_registros_obj("
  		SELECT *, date_trunc('second', ccaso_fecha) AS ccaso_fecha 
      FROM formulario_ccaso 
      JOIN pacientes ON ccaso_pac_id=pac_id
      LEFT JOIN comunas USING (ciud_id)
      LEFT JOIN provincias USING (prov_id)
      LEFT JOIN regiones USING (reg_id)
      LEFT JOIN sexo USING (sex_id)
      LEFT JOIN grupo_sanguineo USING (sang_id)
      LEFT JOIN grupos_etnicos USING (getn_id)
      LEFT JOIN prevision USING (prev_id)
      
      LEFT JOIN especialidades ON ccaso_esp_id=esp_id
      LEFT JOIN diagnosticos ON ccaso_diag_cod=diag_cod
      
      LEFT JOIN patologias_auge ON ccaso_pat_id=pat_id
      LEFT JOIN patologias_auge_ramas ON ccaso_patrama_id=patrama_id
      
      LEFT JOIN form_ccaso_causal ON ccaso_causal=causal_codigo_ifl
      LEFT JOIN form_ccaso_subcausal ON ccaso_subcausal=subcausal_codigo_ifl
      
  		WHERE ccaso_id=$ccaso_id
		",true);

		if($cc['ccaso_folio']=='-1') $cc['ccaso_folio']='<i>(Sin Folio Asignado)</i>';
		    
			
?>
		<html>
		
		<title>Cierre de Caso AUGE</title>
		
    <?php  cabecera_popup('..'); ?>
    	
		<style>

		body {
			font-family: Arial, Helvetica, sans-serif;
			font-size: 10px;
		}

		</style>
	
		
		<body topmargin=0 leftmargin=0 rightmargin=0>
		
		<div class='sub-content'>
		<div class='sub-content'><img src='../iconos/script.png'> <b>Cierre de Caso AUGE</b></div>
		<div class='sub-content2'>
		<center>
		
<?php


    print("		
		<table>
		<tr><td style='text-align:right;'>Fecha de Ingreso:</td><td><b>".$cc['ccaso_fecha']."</b></td></tr>
		<tr><td style='text-align:right;'>N&uacute;mero de Folio:</td><td><b>".$cc['ccaso_folio']."</b></td></tr>
		</table>
		</center>
		</div>
		
		<div class='sub-content'><img src='../iconos/user_orange.png'> <b>Datos de Paciente</b></div>
		<div class='sub-content2'>
		<table>
		<tr><td style='text-align:right;'>RUT:</td>
    <td><b>".$cc['pac_rut']."</b></td></tr>
		<tr><td style='text-align:right;'>Apellido Paterno:</td>
		<td><b><i>".$cc['pac_appat']."</i></b></td></tr>
		<tr><td style='text-align:right;'>Apellido Materno:</td>
		<td><b><i>".$cc['pac_apmat']."</i></b></td></tr>
		<tr><td style='text-align:right;'>Nombre(s):</td>			
		<td><b><i>".$cc['pac_nombres']."</i></b></td></tr>
		<tr><td style='text-align:right;'>Fecha de Nacimiento:</td>
    <td>".$cc['pac_fc_nac']."</td></tr>
		<tr><td style='text-align:right;'>Edad:</td>
    <td id='paciente_edad'>".$cc['pac_fc_nac']."</td></tr>
		<tr><td style='text-align:right;'>Direcci&oacute;n:</td>				
    <td>".$cc['pac_direccion']."</td></tr>
		<tr><td style='text-align:right;'>Comuna:</td>				
    <td><b>".$cc['ciud_desc']."</b>, ".$cc['prov_desc'].", <i>".$cc['reg_desc']."</i>.- </td></tr>
		<tr><td style='text-align:right;'>Sexo:</td>				
    <td>".$cc['sex_desc']."</td></tr>
		<tr><td style='text-align:right;'>Previsi&oacute;n:</td>	
    <td>".$cc['prev_desc']."</td></tr>
		<tr><td style='text-align:right;'>Grupo Sangu&iacute;neo:</td>			
    <td><b>".$cc['sang_desc']."</b></td></tr>
		<tr><td style='text-align:right;'>Grupo &Eacute;tnico:</td>
    <td>".$cc['getn_desc']."</td></tr>
		</table>
		</div>
		
		<div class='sub-content'><img src='../iconos/chart_organisation.png'> <b>Detalle Cierre de Caso</b></div>
		<div class='sub-content2'>
		<table>
		<tr><td style='text-align:right;'>Especialidad:</td>		
		<td width=60%><b>".$cc['esp_desc']."</b></td></tr>
		<tr><td style='text-align:right;'>Patolog&iacute;a AUGE:</td>
    <td><b>".$cc['pat_glosa']."</b></td></tr>
    <tr><td style='text-align:right;'>Rama Patolog&iacute;a AUGE:</td>
    <td><b>".$cc['rama_nombre']."</b></td></tr>
    <tr><td style='text-align:right;'>Causal:</td>
    <td><b>".$cc['causal_nombre']."</b></td></tr>");
    
    if($cc['subcausal_nombre']!='')
      print("
      <tr><td style='text-align:right;'>Subcausal:</td>
      <td><b>".$cc['subcausal_nombre']."</b></td></tr>
      ");

    if($cc['ccaso_causal']=='9')
      print("
      <tr><td style='text-align:right;'>Fecha de Parto/Aborto:</td>
      <td><b>".$cc['ccaso_fecha_pa']."</b></td></tr>
      <tr><td style='text-align:right;'>Semanas de Gestaci&oacute;n:</td>
      <td><b>".$cc['ccaso_semanas_gestacion']."</b></td></tr>
      ");

    if($cc['ccaso_causal']=='1')
      print("
      <tr><td style='text-align:right;'>Fecha de Defunci&oacute;n:</td>
      <td><b>".$cc['ccaso_fecha_defuncion']."</b></td></tr>
      ");

    
    print("
    <tr><td style='text-align:right;' valign='top'>Diagn&oacute;stico(s):</td>				
		<td style='font-weight:bold;'>[".$cc['ccaso_diag_cod']."] ".$cc['diag_desc']."</td></tr>
		<tr><td style='text-align:right;' valign='top'>Observaciones:</td>				
		<td>".$cc['ccaso_observaciones']."</td></tr>
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
