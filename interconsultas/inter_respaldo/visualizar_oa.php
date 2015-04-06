<?php
  require_once('../conectar_db.php');
  	$oa_id=$_GET['oa_id']*1;
		list($oa)=cargar_registros_obj("
  		SELECT *, date_trunc('second', oa_fecha) AS oa_fecha, 

  		i1.inst_nombre AS inst_nombre1, i2.inst_nombre AS inst_nombre2,
  		
  		e1.esp_desc AS esp_nombre,
  		
  		e2.esp_desc AS unidad_nombre
  		
  		FROM orden_atencion 
      JOIN pacientes ON oa_pac_id=pac_id
      LEFT JOIN comunas USING (ciud_id)
      LEFT JOIN provincias USING (prov_id)
      LEFT JOIN regiones USING (reg_id)
      LEFT JOIN sexo USING (sex_id)
      LEFT JOIN grupo_sanguineo USING (sang_id)
      LEFT JOIN grupos_etnicos USING (getn_id)
      LEFT JOIN prevision USING (prev_id)

      LEFT JOIN instituciones AS i1 ON oa_inst_id=i1.inst_id      LEFT JOIN instituciones AS i2 ON oa_inst_id2=i2.inst_id      
      
      LEFT JOIN codigos_prestacion ON oa_codigo=codigo
      LEFT JOIN especialidades AS e1 ON oa_especialidad=e1.esp_id      LEFT JOIN especialidades AS e2 ON oa_especialidad2=e2.esp_id
      LEFT JOIN diagnosticos ON oa_diag_cod=diag_cod
		LEFT JOIN casos_auge ON orden_atencion.id_caso=casos_auge.id_sigges

      LEFT JOIN profesionales_externos ON oa_prof_id=prof_id
      LEFT JOIN interconsulta_estado ON ice_id=oa_estado
      LEFT JOIN prioridad ON prior_id=oa_prioridad
  		WHERE oa_id=$oa_id
		",true);

		if($oa['oa_folio']=='0') $oa['oa_folio']='<i>(Sin Folio Asignado)</i>';			
?>
	<html>
	<title>Orden de Atenci&oacute;n</title>
   <?php  cabecera_popup('..'); ?>
	<script> 
	
	
		guardar_resolucion = function() {
	
			if($('estado').value==1 && $('esp_id').value=='') {
				alert('Debe seleccionar unidad de destino.');
				$('especialidad').focus();
				return;	
			}
									
			$('inter_diag_cod').disabled=false;

			var myAjax = new Ajax.Request(			'revision_inter/sql_resolucion.php', 			{
				method: 'post', 				parameters: 'accion=guardar_resolucion&'+$('resolucion').serialize()+'&'+$('inter_diag_cod').serialize(),				onComplete: function (pedido_datos) {
	         if(trim(pedido_datos.responseText)=='OK') {
					  alert('Resoluci&oacute;n guardada exitosamente.'.unescapeHTML());						window.opener.realizar_busqueda();						window.opener.focus();						window.close();
				} else {
						alert('ERROR:\\n'+pedido_datos.responseText);
				}
				}
			}
			);
		}
		
		abrir_ficha = function(id) {
			inter_ficha = window.open('visualizar_ic.php?tipo=inter_ficha&inter_id='+id,			'inter_ficha_ver', 'left='+(20)+',top='+(screen.height-470)+',width=480,height=400,status=0,scrollbars=1');
			inter_ficha.focus();
		}
		modificar_resolucion = function(id) {
			inter_ficha = window.open('visualizar_oa.php?tipo=revisar&oa_id='+id,			'_self');
		}

	</script>
	<style>
		body {			font-family: Arial, Helvetica, sans-serif;			font-size: 10px;		}
	</style>
	
	<body topmargin=0 leftmargin=0 rightmargin=0>
		<div class='sub-content'>
		<div class='sub-content'><img src='../iconos/script.png'> <b>Orden de Atenci&oacute;n</b></div>
		<div class='sub-content2'>
		<center>
<?php
    print("		
		<table style='width:100%;'>
		<tr><td style='text-align:right;width:100px;'>Procedencia:</td><td><b>".$oa['inst_nombre1']."</b></td></tr>		<tr><td style='text-align:right;width:100px;'>Destino:</td><td><b>".$oa['inst_nombre2']."</b></td></tr>
		<tr><td style='text-align:right;width:100px;'>Fecha de Ingreso:</td><td><b>".$oa['oa_fecha']."</b></td></tr>
		<tr><td style='text-align:right;'>N&uacute;mero de Folio:</td><td><b>".$oa['oa_folio']."</b></td></tr>
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
		<tr><td style='text-align:right;'>Previsi&oacute;n:</td>	
    <td>".$oa['prev_desc']."</td></tr>
		<tr><td style='text-align:right;'>Grupo Sangu&iacute;neo:</td>			
    <td><b>".$oa['sang_desc']."</b></td></tr>
		<tr><td style='text-align:right;'>Grupo &Eacute;tnico:</td>
    <td>".$oa['getn_desc']."</td></tr>
		</table>
		</div>
		
		<div class='sub-content'><img src='../iconos/chart_organisation.png'> <b>Detalle de la Orden</b></div>
		<div class='sub-content2'>
		<table>
		<tr><td style='text-align:right;'>Especialidad:</td>		
		<td width='60%'><b>".$oa['esp_nombre']."</b></td></tr>

		");		
		
		if($oa['unidad_nombre']!='') {
			
			print("
				<tr><td style='text-align:right;'>Unidad Receptora:</td>		
				<td width='60%'><b>".$oa['unidad_nombre']."</b></td></tr>
			");		
		
			
		}
		
		print("
		<tr><td style='text-align:right;' valign='top'>Prestaci&oacute;n:</td>				
		<td style='font-weight:bold;'>".$oa['oa_codigo']."</td></tr>
		<tr><td style='text-align:right;' valign='top'></td>						<td style='text-align:justify;'>".$oa['glosa']."</td></tr>		");


		if($oa['id_caso']!=0) {
			print("<tr><td style='text-align:right;'>Caso AUGE:</td>
    		<td><b>".$oa['ca_patologia']."</b></td></tr>");
    	}


		if($oa['oa_diag_desc']!='') {
			print("<tr><td style='text-align:right;' valign='top'>Diagn&oacute;stico CIE10:</td>				
			<td style='font-weight:bold;'>[".$oa['oa_diag_desc']."] ".$oa['diag_desc']."</td></tr>");
		}
		print("
		<tr><td style='text-align:right;' valign='top'>Hip&oacute;tesis Diagn&oacute;stica:</td>				
		<td>".$oa['oa_hipotesis']."</td></tr>		</table>
		</div>");
		    
	if($oa['prof_rut']!='') {
    print("	<div class='sub-content'><img src='../iconos/user_comment.png'> <b>Datos del Profesional Solicitante</b></div>    <div class='sub-content2'>    <table style='width:100%;'>		<tr><td style='text-align:right;width:100px;'>RUT:</td>    <td style='font-weight:bold;'>".$oa['prof_rut']."</td></tr>		<tr><td style='text-align:right'>Nombre:</td>    <td>".($oa['prof_paterno'])." ".($oa['prof_materno'])." ".($oa['prof_nombres'])."</td></tr>		</table>   </div>    ");
    } else {
    	$doc=cargar_registro("SELECT * FROM doctores WHERE doc_id=".$oa['oa_doc_id']);	
    print("	<div class='sub-content'><img src='../iconos/user_comment.png'> <b>Datos del Profesional Solicitante</b></div>    <div class='sub-content2'>    <table style='width:100%;'>		<tr><td style='text-align:right;width:100px;'>RUT:</td>    <td style='font-weight:bold;'>".$doc['doc_rut']."</td></tr>		<tr><td style='text-align:right'>Nombre:</td>    <td>".($doc['doc_paterno'])." ".($doc['doc_materno'])." ".($doc['doc_nombres'])."</td></tr>		</table>   </div>    ");
    }

    print("    <div class='sub-content'><img src='../iconos/page_edit.png'> 		<b>Resoluci&oacute;n</b></div>		<div class='sub-content2'>		<table style='width:100%;'>		<tr><td style='text-align:right;' width=150>Estado Actual:</td>						<td>		<img src='../iconos/".$oa['ice_icono'].".png'>
		<b>".htmlentities($oa['ice_desc'])."</b>		</td></tr>    	<tr><td style='text-align:right;' valign='top'>Prioridad:</td>		<td>".(($oa['prior_desc']!='')?$oa['prior_desc']:'Sin Prioridad')."</td></tr>
	");
	
		if(_cax(35))
		print("
			<tr>
			<td colspan=2>
			<center><input type='button' id='modifica' name='modifica' 
			onClick='modificar_resolucion(".$oa['oa_id'].");' value='--- Modificar Resoluci&oacute;n... ---' /></center>		
			</td>		
			</tr>
		");
	
	
	print("
		</table>	");



   
   if(isset($_GET['tipo']) AND $_GET['tipo']=='revisar') {

		$estadohtml=desplegar_opciones_sql(
			"SELECT ice_id, ice_desc FROM interconsulta_estado 
			WHERE ice_id>0
			ORDER BY ice_id", $oa['oa_estado']	
		);

		$prioridadhtml = desplegar_opciones("prioridad", 		"prior_id, prior_desc",$oa['oa_prioridad'],'true','ORDER BY prior_id');    	
  
   print("
		<div class='sub-content'><img src='../iconos/page_edit.png'> 
		<b>Resoluci&oacute;n</b></div>
		<div class='sub-content2'>
		<form name='resolucion' id='resolucion' onsubmit='return false;'>
		<table>
		<tr><td style='text-align:right;' width=150>Estado del Caso:</td>				
		<td>
		
		<input type='hidden' name='oa_id' id='oa_id' value='".$oa_id."'>
		<input type='hidden' name='institucion' id='institucion' value='".$institucion."'>
		<select id='estado' name='estado'
		onChange='if(this.value*1==1) {
			$(\"prioridad_tr\").style.display=\"\";
			$(\"unidad_tr\").style.display=\"\";
		} else { 		
			$(\"prioridad_tr\").style.display=\"none\";
			$(\"unidad_tr\").style.display=\"none\";
		}'>
			".$estadohtml."

		</select>
		</td></tr>

		<tr id='prioridad_tr' ".($oa['oa_estado']>1?'style="display:none;"':'')."><td style='text-align:right;' width=150>Prioridad:</td>				
		<td>
		<select id='prioridad' name='prioridad'>
		".$prioridadhtml."
	   </select>
		</td></tr>

		<tr id='unidad_tr' ".($oa['oa_estado']>1?'style="display:none;"':'').">
		<td style='text-align:right;'>Unidad Receptora:</td>
		<td>
		<input type='hidden' id='esp_id' name='esp_id' value='".$oa['oa_especialidad2']."'>
		<input type='text' id='especialidad' 
		value='".$oa['unidad_nombre']."' name='especialidad' size=35>
		</td>
		</tr>

		<tr id='unidad_tr'>
		<td style='text-align:right;'>Diag. CIE10:</td>
		<td>
		<input type='text' id='inter_diag_cod' name='inter_diag_cod' 
		value='".$oa['oa_diag_cod']."' DISABLED size=5 style='font-weight:bold;text-align:center;' />
		<input type='text' id='inter_diagnostico' 
		value='".$oa['oa_diagnostico']."' name='inter_diagnostico' size=25>
		</td>
		</tr>
    
		<tr><td style='text-align:right;' valign='top'>Observaciones:</td>
		<td>
		<textarea cols=30 rows=3 
    id='observaciones' name='observaciones'>".$oa['oa_rev_med']."</textarea>
		</td></tr>
		
		</table>
		</div>
		
		<center><div class='boton'><table><tr><td>
		<img src='../iconos/accept.png'>
		</td><td>
		<a href='#' onClick='guardar_resolucion();'>Guardar Resoluci&oacute;n...</a>
		</td></tr></table></div>
		</center>
		</form>
		
		<script>
      ingreso_especialidades=function(datos_esp) {
      	$('esp_id').value=datos_esp[0];
      	$('especialidad').value=datos_esp[2];
      }

      autocompletar_especialidades = new AutoComplete(
      	'especialidad', 
      	'../autocompletar_sql.php',
      function() {
        if($('especialidad').value.length<3) return false;
        
        return {
          method: 'get',
          parameters: 'tipo=subespecialidad&cadena='+encodeURIComponent($('especialidad').value)
        }
      }, 'autocomplete', 350, 200, 250, 1, 2, ingreso_especialidades);

      ingreso_diagnosticos=function(datos_diag) {
      	var cie10=datos_diag[0].charAt(0)+datos_diag[0].charAt(1)+datos_diag[0].charAt(2);
      	cie10+='.'+datos_diag[0].charAt(3);
      	
      	$('inter_diag_cod').value=cie10;
      	$('inter_diagnostico').value=datos_diag[2].unescapeHTML();
      }

      autocompletar_diagnosticos = new AutoComplete(
      	'inter_diagnostico', 
      	'../autocompletar_sql.php',
      function() {
        if($('inter_diagnostico').value.length<3) return false;
        
        return {
          method: 'get',
          parameters: 'tipo=diagnostico_tapsa&cadena='+encodeURIComponent($('inter_diagnostico').value)
        }
      }, 'autocomplete', 350, 200, 250, 1, 2, ingreso_diagnosticos);
		
		</script>
		
		");
		}		
?>
</div></body>
<script>
		$('paciente_edad').innerHTML = '<i>'+window.opener.calc_edad($('paciente_edad').innerHTML)+'</i>';
</script></html>