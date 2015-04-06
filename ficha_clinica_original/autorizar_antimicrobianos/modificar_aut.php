<?php 

	require_once('../../conectar_db.php');
	
	$hospam_id=$_GET['hospam_id']*1;
	$fundamento=$_GET['fundamento'];
	
	$h=cargar_registro("
		SELECT *
		FROM hospitalizacion_autorizacion_meds
		JOIN hospitalizacion USING (hosp_id)
		JOIN pacientes ON hosp_pac_id=pac_id
		JOIN articulo USING (art_id)
		JOIN funcionario ON hospam_func_id=func_id
		JOIN doctores ON hospam_doc_id=doc_id
		LEFT JOIN bodega_forma ON art_forma=forma_id
		WHERE hospam_id=$hospam_id
	", true);

?>

<html>
<title>Modificar Autorizaci&oacute;n de Antimicrobianos</title>

<?php cabecera_popup('../..'); ?>

<script>

	guardar_autoriza_meds=function() {
		
		if($('art_id2').value=='') {
			alert('Debe seleccionar el medicamento a solicitar al paciente.'.unescapeHTML());
			return;
		}
		
		var myAjax=new Ajax.Request(
			'sql_modificar.php',
			{
				method:'post',
				parameters:'hospam_id=<?php echo $hospam_id; ?>&fundamento=<?php echo $fundamento; ?>&'+
					$('doc_id2').serialize()+'&'+
					$('art_id2').serialize()+'&'+
					$('art_cantidad2').serialize()+'&'+
					$('art_horas').serialize()+'&'+
					$('art_dias').serialize()+'&'+
					$('art_observa').serialize()+'&'+
					$('art_terapia').serialize()+'&'+
					$('art_cultivo').serialize()+'&'+
					$('otro_diag').serialize()+'&'+
					$('art_motivo').serialize(),
				onComplete:function(r) {
					 try {
					        resultado=r.responseText;
					        
					        if(resultado) {
					          
					          alert( ('Autorizaci&oacute;n modificada exitosamente.'.unescapeHTML()) );
							  
							  
					        } else {
								
					          alert('ERROR:\n\n'+r.responseText);
					          
					        }
					        
					  } catch(err) {
					          alert('ERROR:\n\n'+err.responseText);
					        
					  }

				}
			}
		);
		
	}


</script>

<body class='popup_background fuente_por_defecto'>

<div class='sub-content'>
<img src='../../iconos/pill.png'>
<b>Modificar Autorizaci&oacute;n de Antimicrobianos</b>
</div>

<div class='sub-content'>

<table style='width:100%;'>
	<tr>
		<td rowspan=6> 
		<center><img src='../../iconos/add.png' style='width:32px;height:32px;'></center>
		</td>
		<td style='text-align:right;'>M&eacute;dico:</td>
		<td>
		<input type='text' id='rut_medico2' name='rut_medico2' size=10
		style='text-align: center;' value='<?php echo $h['doc_rut']; ?>' disabled>
		
		
		<input type='hidden' id='doc_id2' name='doc_id2' value='<?php echo $h['doc_id']; ?>'>
		<input type='text' id='nombre_medico2' name='nombre_medico2' size=35
		   value='<?php echo trim($h['doc_paterno'].' '.$h['doc_materno'].' '.$h['doc_nombres']); ?>' onDblClick='$("doc_id").value=""; $("nombre_medico").value="";' />
		</td>
		<td rowspan=6>
		<input type='button' value='-- Modificar --' onClick='guardar_autoriza_meds();' />
		</td>
		</tr>
				
		<tr>
		
		<td style='text-align:right;'>Art&iacute;culo:</td>
		<td>
		<input type='hidden' id='art_id2' name='art_id2' value='<?php echo $h['art_id']; ?>' />
		<input type='text' size=10 id='art_codigo2' name='art_codigo2' value='<?php echo $h['art_codigo']; ?>' />
		
		<input type='text' size=45 id='art_glosa2' name='art_glosa2' value='<?php echo $h['art_glosa']; ?>' READONLY />
		</td>
		
		</tr>
		
		<tr>
		<td style='text-align:right;'>Dosis:</td>
		<td colspan=2>
		<input type='text' size=3 id='art_cantidad2' name='art_cantidad2' style='text-align:right;' value='<?php echo $h['hospam_cant']; ?>' />
		
		&nbsp;
		<span id='art_forma2' style='font-weight:bold;'></span>
		&nbsp;
		cada
		&nbsp;
		<input type='text' size=3 id='art_horas' name='art_horas' style='text-align:right;' value='<?php echo $h['hospam_horas']; ?>' />
		&nbsp;horas por&nbsp;
		<input type='text' size=3 id='art_dias' name='art_dias' style='text-align:right;' value='<?php echo $h['hospam_dias']; ?>' />
		&nbsp;d&iacute;as.</td></tr><tr>
		<td style='text-align:right;'>Tipo Terapia:</td>
		<td>
		<select id='art_motivo' name='art_motivo'>
		<option value='Inicio Tratamiento' <?php if($h['hospam_motivo']=='Inicio Tratamiento') echo 'SELECTED'; ?> >Inicio Tratamiento</option>
		<option value='Modificaci&oacute;n' <?php if($h['hospam_motivo']=='Modificaci&oacute;n') echo 'SELECTED'; ?> >Modificaci&oacute;n</option>
		<option value='Continuaci&oacute;n' <?php if($h['hospam_motivo']=='Continuaci&oacute;n') echo 'SELECTED'; ?> >Continuaci&oacute;n</option>
		</select>&nbsp;
		<select id='art_terapia' name='art_terapia' onChange='if(this.value=="Terapia Espec&iacute;fica".unescapeHTML()){ $("art_cultivo").show(); $("cultivo_lbl").show();}else{ $("art_cultivo").hide(); $("cultivo_lbl").hide();}'>
		<option value='Terapia Emp&iacute;rica' <?php if($h['hospam_terapia']=='Terapia Emp&iacute;rica') echo 'SELECTED'; ?> >Terapia Emp&iacute;rica</option>
		<option value='Terapia Espec&iacute;fica' <?php if($h['hospam_terapia']=='Terapia Espec&iacute;fica') echo 'SELECTED'; ?> >Terapia Espec&iacute;fica</option>
		</select>
		&nbsp;
		<span style='text-align:right;' name='cultivo_lbl' id='cultivo_lbl'>Cultivo:</span>
		<input type='text' id='art_cultivo' name='art_cultivo' size=30  value='<?php echo $h['hospam_cultivo']; ?>'>
		</td>
		</tr>
		
		<tr>
		<td style='text-align:right;'>Diagn&oacute;stico:</td>
		<td><input type='text' id='otro_diag' name='otro_diag' style='' value='<?php echo $h['hospam_diagnostico']; ?>' size=30 /></td>
		</tr><tr>
		<td style='text-align:right;'>Observaciones:</td>
		<td colspan=7>
		<input type='text' size=45 id='art_observa' name='art_observa' value='<?php echo $h['hospam_observaciones']; ?>' />
		
		</td>
		
		
		</tr>
		

	
</table>


</div>


</body>

</html>


<script>


      ingreso_rut2=function(datos_medico) {
      	$('doc_id2').value=datos_medico[3];
      	$('rut_medico2').value=datos_medico[1];
      }

      autocompletar_medicos = new AutoComplete(
      'nombre_medico2', 
      '../../autocompletar_sql.php',
      function() {
        if($('nombre_medico2').value.length<3) return false;
        
        return {
          method: 'get',
          parameters: 'tipo=medicos&nombre_medico='+encodeURIComponent($('nombre_medico2').value)
        }
      }, 'autocomplete', 350, 200, 250, 1, 2, ingreso_rut2);


      ingreso_ab=function(datos_art) {
      	      	
      	$('art_id2').value=datos_art[0];
      	$('art_codigo2').value=datos_art[1];
      	$('art_glosa2').value=datos_art[2].unescapeHTML();
      	$('art_forma2').innerHTML=datos_art[3];
      	$('art_cantidad2').focus();
      	
      }

      autocompletar_medicamentos = new AutoComplete(
      	'art_codigo2', 
      	'../../prestaciones/asignar_camas/autocompletar_sql.php',
      function() {
        if($('art_codigo2').value.length<3) return false;
        
        return {
          method: 'get',
          parameters: 'tipo=medicamento_restringido&art_codigo='+encodeURIComponent($('art_codigo2').value)
        }
      }, 'autocomplete', 450, 300, 250, 1, 2, ingreso_ab);



</script>
