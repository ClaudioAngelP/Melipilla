<?php 

	require_once('../../conectar_db.php');
	require_once('../../ficha_clinica/ficha_basica.php');
	
	$especialidadhtml = desplegar_opciones("especialidades", 
	"esp_id, esp_desc",'','true','ORDER BY esp_desc');
	
	
	

?>

<script>

	listado = function() {

  var params=$('esp_id').serialize()+'&'+$('tipo').serialize()+'&'+$('carp_id').serialize();
  params+='&'+$('filtro').serialize()+'&'+$('ordenar').serialize()+'&'+$('nombre_medico').serialize();
  
  $('tab_listado_content').innerHTML='<br><br><img src="imagenes/ajax-loader2.gif"><br><br>Cargando'
  
  var myAjax=new Ajax.Updater(
  'tab_listado_content',
  'agenda_medica/lista_espera/lista.php',
  {
    method:'post', evalScripts: true, 
    parameters: params
  }
  );
  
}
	
	 bloquear_ingreso=false;
	cfecha=function(obj) {
		var val=obj.value;
		val=trim(val);
		if(val.length==6) {
			obj.value=''+val[0]+val[1]+'/'+val[2]+val[3]+'/20'+val[4]+val[5];
		} else if(val.length==8) {
			obj.value=''+val[0]+val[1]+'/'+val[2]+val[3]+'/'+val[4]+val[5]+val[6]+val[7];
		} else {
			obj.value=''+val;
		}

	}
	
	calc_dias=function() {
	
		try {
		
			if($('fecha0').value=='' ||
				$('fecha2').value=='') {
			
				$('dias_estadia').innerHTML='?';			
				return;			
				
			}		
		
			var f1=$('fecha0').value.split('/');
			var f2=$('fecha2').value.split('/');

			var d1=new Date( Date.parse(f1[1]+'/'+f1[0]+'/'+f1[2]) );
			var d2=new Date( Date.parse(f2[1]+'/'+f2[0]+'/'+f2[2]) );
			var one_day=1000*60*60*24;
	
			$('dias_estadia').innerHTML=Math.ceil((d2.getTime()-d1.getTime())/(one_day))+' d&iacute;as.';

		} catch(err) {
			
			$('dias_estadia').innerHTML='?';

		}
		
	}
	
	limpia = function(){
		
		cambiar_pagina("prestaciones/ingreso_egreso_hospital/solicitud_hospitalizacion.php");
	}

	limpiar_formulario=function() {

		$('hosp_id').value=0;

		limpiar_ficha_basica();
		$('paciente_tipo_id').value=0;
		
		
		$('paciente_id').value='';
		$('paciente_rut').value='';	
		$('hora1').value='<?php echo date("H:m"); ?>';								

		//$('nro_cama').value=reg.hosp_numero_cama;
		//$('disponible').value='1';
	   $('inst_id').value='';
	   $('inst_id').style.display='none';
	   $('inst_sel').value='';
	   $('inst_sel').style.display='none';
	   $('servicios0').value='';
		$('prevision').value=0;
		$('prevision_clase').value=0;
		$('prevision_clase').style.display='';
		
		
		$('esp_id').value='';
		$('especialidad').value='';

		$('modalidad').value=0;
		$('ges').value=0;
      $("prog_social").style.display="none";
      $("inst_id").style.display="none";
      $('motivo').value=0;
		$('procedencia').value=0;
		$('criticidad').value='C1';
		$('diag_cod').value='';
		$('diagnostico').value='';
		$('fecha0').value='<?php echo date("d/m/Y");?>';
		$('hosp').style.display='none';
		
	}

	cargar_hosp=function() {
	
		var myAjax=new Ajax.Request(
			'prestaciones/ingreso_egreso_hospital/info_hospitalizacion.php',
			{
				method:'post',
				parameters:$('nro_folio').serialize(),
				onComplete:function(r) {
					try {

						reg=r.responseText.evalJSON(true);
						
						if(reg) {

							$('hosp_id').value=reg.hosp_id;
					
							$('paciente_id').value=reg.hosp_pac_id;
							$('paciente_rut').value=reg.pac_rut;
							$('doc_id').value=reg.hosp_doc_id;
							
							var fec=reg.hosp_fecha_ing.split(' ');				
							$('fecha0').value=fec[0];								
							$('hora1').value=fec[1];								
							
							if(reg.doc_rut!=null) {
								$('rut_medico').value=reg.doc_rut;
								$('nombre_medico').value=(reg.doc_paterno+' '+reg.doc_materno+' '+reg.doc_nombres).unescapeHTML();
							} else {
								$('rut_medico').value='';
								$('nombre_medico').value='';							
							}
							
							//$('nro_cama').value=reg.hosp_numero_cama;
							//$('disponible').value='1';

							$('prevision').value=reg.hosp_prevision;
							if(reg.hosp_prevision=='0')
                                                        {
								$('prevision_clase').value=reg.hosp_prevision_clase;
								$('prevision_clase').style.display='';
                                                                $('prog_social').style.display='none';
                                                                $("motivo").value=0;
							}
                                                        else
                                                        {
                                                                $("prevision_clase").value=0;
                                                                $('prevision_clase').style.display='none';
                                                                if(reg.hosp_prevision=='3')
                                                                {

                                                                    $('prog_social').style.display='';
                                                                }
                                                                else
                                                                {

                                                                    $('prog_social').style.display='none';
                                                                    $("motivo").value=0;
                                                                }
							}

							$('modalidad').value=reg.hosp_modalidad;
							$('ges').value=reg.hosp_ges;
							$('motivo').value=reg.hosp_motivo;
							$('procedencia').value=reg.hosp_procedencia;

							$('centro_ruta0').value=reg.hosp_centro_ruta;
							$('servicios0').value=reg.centro_nombre.unescapeHTML();

							if(reg.hosp_procedencia=='3') {
								$('inst_sel').style.display='';
								$('inst_id').value=reg.hosp_inst_id;
								$('institucion').value=reg.inst_nombre.unescapeHTML();
							} else {
								$('inst_sel').style.display='none';
								$('inst_id').value='';
								$('institucion').value='';
							}
							
							if(reg.hosp_fecha_egr!=null) {

								<?php if($ingreso) { ?>
								$('egreso').checked=true;
								$('panel_egreso').style.display='';
								<?php } ?>
								
								var fec=reg.hosp_fecha_egr.split(' ');
								
								$('fecha2').value=fec[0];
								$('hora2').value=fec[1];								
								$('condicion').value=reg.hosp_condicion_egr;
								
								if(reg.hosp_parto=='t')
									$('parto').value=0; 
								else if(reg.hosp_parto=='f')
									$('parto').value=1;
								else
									$('parto').value=-1;
																

								if(reg.hosp_nacimiento=='t')
									$('nacimiento').value=0; 
								else if(reg.hosp_nacimiento=='f') 
									$('nacimiento').value=1;		
								else
									$('nacimiento').value=-1;		
														
								
							} else {
							
								<?php if($ingreso) { ?>
								$('egreso').checked=false;
								$('panel_egreso').style.display='none';
								<?php } ?>
								
								$('fecha2').value='';
								$('hora2').value='12:00';
								$('condicion').value=1;
								
								$('parto').value=-1;								

								$('nacimiento').value=-1;								

							}

							diagnosticos=reg.diag1;
							diagnosticos_egreso=reg.diag2;
							traslados_int=reg.traslados;
							prestaciones=reg.prestaciones;
							
							calc_dias();

							buscar_paciente();

							redibujar('diag');
							redibujar('diagf');
							redibujar('pres');
							redibujar('tras');
							
							<?php if($ingreso) { ?>
								cargar_cama();
							<?php } ?>
							
						} else {
						
							limpiar_formulario();

						} 		
					
					} catch(err) {
					
						alert(err);
					
					}							
				}
			}		
		);	
	
	}

	 bloquear_ingreso=false;
	 
	guardar_hosp = function() {
		if(!validacion_hora($('hora1'))) {
			alert('Debe ingresar hora correctamente.');
			return;	
		} 
	 
		if(bloquear_ingreso) 
		{
			alert(bloquear_ingreso);	
			return;	 
		}
		//Validacion de la previcion
		/*
		if($('prevision').value==3)
		{
           if($('motivo').value==0)
           {
               alert('Debe seleccionar ley o Programa Social para guardar los datos');
               return;
           }
		}
		*/

		//Validacion de la fecha
		try {
			var f1=$('fecha0').value.split('/');
			var d1=new Date( Date.parse(f1[1]+'/'+f1[0]+'/'+f1[2]) );
		} catch(err) {
			alert('Fecha de ingreso incorrecta.');
			return;			
		}
		//valida diagnostico 
		/* 
		if( trim($('diagnostico').value)=='') {
			alert('Debe ingresar un diagnostico.');
			return;
		}
		*/
	
		if($('modalidad').value==-1){
			alert('Debe indicar la Modalidad de Atenci&oacute;n.'.unescapeHTML()); return;
		}
	
		/*
		if($('ges').value==-1){
			alert('Debe indicar si es GES o no.'); return;
		}
		*/

		if($('esp_id').value==''){
			alert('Debe seleccionar la Especialidad.'); return;
		}
	
		if($('centro_ruta0').value==''){
			alert('Debe seleccionar Servicio de Ingreso'); return;
		}

		//if($('criticidad').value==-1){
			//alert('Debe seleccionar la Categorizaci&oacute;n del paciente.'.unescapeHTML()); return;
		//}

		if($('procedencia').value==-1){
			alert('Debe indicar la procedencia.'); return;
		}

		if($('procedencia').value==1 && $('diagnostico').value==''){
			alert('Debe ingresar el Diagn&oacute;stico'.unescapeHTML());
			return;
		}
	
		if($('procedencia').value==1 && $('dau').value==''){
			alert('Debe ingresar el DAU del paciente'.unescapeHTML());
			return;
		}

		$('diag_cod').disabled=false;
		
		var params=$('paciente').serialize()+'&'+$('hosp').serialize();
				
		bloquear_ingreso=true;	 
		
	 
		var myAjax=new Ajax.Request('prestaciones/ingreso_egreso_hospital/sql_solicitud.php',
		{
			method:'post',
			parameters: params,
			onComplete: function(r) {
				try{
					var hosp=r.responseText.split("|");
					var id_hosp=hosp[1];
					var response=hosp[0];
					if(response=='N' || response=='E') {
						alert('Solicitud de ingreso hospitalario ingresada exitosamente.');
						limpia();
						bloquear_ingreso=false;			
						$('contenido').scrollTop=0;						
						//$('pac_rut').focus();						
						window.open('prestaciones/ingreso_egreso_hospital/imprimir_egreso.php?hosp_id='+id_hosp, '_blank');
						//alert(bloquear_ingreso);
					} else {
						alert(response);
						window.open('prestaciones/ingreso_egreso_hospital/imprimir_egreso.php?hosp_id='+id_hosp, '_blank');
					}
					bloquear_ingreso=false;	
				}catch(err){
					alert(err);		
				}
			}
		});	 
	}
	 
	 comprueba_hosp = function(){
		var myAjax=new Ajax.Request(
			'prestaciones/ingreso_egreso_hospital/comprueba_hospitalizacion.php',
			{
				method:'post',
				parameters: 'paciente_id='+$('paciente_id').value,
				onComplete:function(resp) {
				try {
        
					resultado=resp.responseText.evalJSON(true);
			
					if(resultado) {
						alert( ('El paciente tiene una Cta. Corriente Abierta: '+ resultado) );
						limpia();
						return;
					}
				}catch(err){
					alert("ERROR");
				};
				}	
			}		
		);
	 }

pac_verifica_datos=[];

paciente_seleccionado=function() {

	 $('hosp').style.display='';
         $('ingresar').style.display='';


	if(pac_verifica_datos[$('paciente_id').value*1]!=undefined || $('paciente_id').value*1==0) return;

	 var nombre='=====================================================================\n'+$('paciente_nombre').value+' '+$('paciente_paterno').value+' '+$('paciente_materno').value+'\n\n';

	 var dire=prompt(('Verifique la DIRECCI&Oacute;N del paciente:\n'+nombre).unescapeHTML(), $('paciente_dire').value);
	 var fono=prompt(('Verifique el TEL&Eacute;FONO del paciente:\n'+nombre).unescapeHTML(), $('paciente_fono').value);

	 params='';

	 if(dire!=null && dire!=$('paciente_dire').value.unescapeHTML()) {
		$('paciente_dire').value=dire;
		params='paciente_dire='+encodeURIComponent($('paciente_dire').value);
	 }

	 if(fono!=null && fono!=$('paciente_fono').value) {
                $('paciente_fono').value=fono;
		params=params+'&paciente_fono='+encodeURIComponent($('paciente_fono').value);
         }

	 pac_verifica_datos[$('paciente_id').value*1]=1;

	 if(params!='') {
		params=params+'&'+$('paciente_id').serialize();
		//alert(params);

		var myAjax=new Ajax.Request(
			'prestaciones/ingreso_egreso_hospital/sql_actualizar_direccion_fono.php',
			{method:'post',parameters:params}
		);
	}



}

</script>

<center>
<div class='sub-content' style='width:750px;'>
<div class='sub-content' 
style='background-color:#cccccc;font-weight:bold;'>
<table cellpadding=0 cellspacing=0 style='width:100%;'><tr>
<img src='iconos/building.png'>
<b>Ingreso Hospitalario</b>
</td></tr></table>
</div>

<?php desplegar_ficha_basica('paciente_seleccionado();'); ?>

<form id='hosp' name='hosp' onSubmit='return false;' style='display: none;'>
<input type='hidden' id='hosp_id' name='hosp_id' value='0'>

<div class='sub-content'>
<img src='iconos/building.png'> <b>Datos de Ingreso</b>
</div>

<div class='sub-content'>

<table style='width:100%;'>

<!---
<tr><td style='text-align:right;'>
Previsi&oacute;n de Salud:
</td><td colspan=3>
<select id='prevision' name='prevision' onChange='
	if(this.value=="0") 
	 {
		$("prevision_clase").style.display="";
                $("prog_social").style.display="none";
                $("motivo").value="0";

     }
     else
     {
		$("prevision_clase").style.display="none";
                $("prevision_clase").value="0";
                if(this.value==3)
                {
                    $("prog_social").style.display="";
                }
                else
                {
                    $("prog_social").style.display="none";
                    $("motivo").value="0";
                }
     }
'>
<option value='-1'>(Seleccionar...)</option>
<option value='0'>FONASA</option>
<option value='1'>ISAPRE</option>
<option value='2'>Particular</option>
<option value='3'>Otra</option>
</select>
<select id='prevision_clase' name='prevision_clase'>
<option value='0'>A</option>
<option value='1'>B</option>
<option value='2'>C</option>
<option value='3'>D</option>
</select>
</td></tr>
-->

<tr>
	<td style='text-align:right;'>Tipo de Atenci&oacute;n Hospitalario</td>
	<td colspan=3>
		<select id='tipo_atencion' name='tipo_atencion'>
			<option value='-1'>(Seleccionar...)</option>
			<option value='0'>Hospitalizaci&oacute;n</option>
			<option value='1'>Cirug&iacute;a Mayor Ambulatoria</option>
			<option value='2'>Procedimientos Ambulatorios</option>
			<option value='3'>Hospital Psiqui&aacute;trico Diurno</option>
		</select>
	</td>
</tr>

<tr><td style='text-align:right;'>
Modalidad de Atenci&oacute;n:
</td><td colspan=3>
<select id='modalidad' name='modalidad'>
<option value='-1'>(Seleccionar...)</option>
<option value='0'>MAI</option>
<option value='1'>MLE</option>
</select>
</td></tr>

<tr><td style='text-align:right;'>
GES:
</td><td colspan=3>
<select id='ges' name='ges'>
<option value='-1'>(Seleccionar...)</option>
<option value='0'>No</option>
<option value='1'>S&iacute;</option>
</select>
</td></tr>

<tr id='prog_social' name='prog_social' style='display:none;'><td style='text-align:right;'>
Ley/Programa Social:
</td><td colspan=3>
<select id='motivo' name='motivo'>
<option value='0'></option>
<option value='1'>Ley 18.490 Accidentes del Transporte</option>
<option value='2'>Ley 16.744 Accidentes del Trabajo y Enfermedades Profesionales</option>
<option value='3'>Ley 16.744 Accidente Escolar</option>
<option value='4'>PRAIS</option>
<option value='5'>Chile Solidario</option>
<option value='6'>Chile Crece Contigo</option>
<option value='7'>Otro Programa Social</option>
</select>
</td></tr>

<tr>
<td id='tag_esp' style='text-align:right;'>
Especialidad:</td><td>
<input type='hidden' id='esp_id' name='esp_id' value=''>
<input type='text' id='especialidad'  name='especialidad' value='' 
onDblClick='$("esp_id").value=""; $("especialidad").value="";' size=35>
</td>
</tr>

<tr hidden='true'>
<td id='tag_esp' style='text-align:right;'>
Subespecialidad:</td><td>
<input type='hidden' id='esp_id2' name='esp_id2' value=''>
<input type='text' id='especialidad2'  name='especialidad2' value='' 
onDblClick='$("esp_id2").value=""; $("especialidad2").value="";' size=35>
</td>
</tr>

<tr id='rut_medico_tr'>
<td style='text-align:right;'>R.U.T. M&eacute;dico:</td>
<td>
<input type='text' id='rut_medico' name='rut_medico' size=10
style='text-align: center;' value='<?php echo $r[0]['doc_rut']; ?>' disabled></td></tr>
<tr id='nombre_medico_tr'>
<td style='text-align:right;'>M&eacute;dico Tratante:</td>
<td>
<input type='hidden' id='doc_id' name='doc_id' value='<?php echo $r[0]['hosp_doc_id']; ?>'>
<input type='text' id='nombre_medico' name='nombre_medico' size=35 onDblClick='$("rut_medico").value="";$("doc_id").value="";$("nombre_medico").value="";'
value='<?php echo trim($r[0]['doc_paterno'].' '.$r[0]['doc_materno'].' '.$r[0]['doc_nombres']); ?>' />
</td>
</tr>

<tr><td style='text-align:right;'>
Procedencia del Paciente:
</td><td colspan=3>
<select id='procedencia' name='procedencia'
onChange='
	if(this.value==2 || this.value==4){ 
		$("tr_dau").style.display="none";
		$("inst_sel").style.display=""; 
	}else if(this.value==1){
		$("inst_sel").style.display="none";	
		$("tr_dau").style.display="";
	}else{
		$("inst_sel").style.display="none";	
		$("tr_dau").style.display="none";
	}
'>
<option value='-1'>(Seleccionar...)</option>
<!--<option value='0'>U. Emergencia Adulto (UEA)</option>
<option value='1'>U. Emergencia Infantil (UEI)</option>
<option value='2'>U. Emergencia Maternal (UEGO)</option>
<option value='4'>Obstetricia y Ginecolog&iacute;a</option>
<option value='5'>Hospitalizaci&oacute;n</option>
<option value='6'>Atenci&oacute;n Ambulatoria</option>
<option value='3'>Otro Hospital</option>-->
<option value='1'>Unidad de Emergencia (del mismo establecimiento)</option>
<option value='2'>Atenci&oacute;n Primaria de Salud (APS)</option>
<option value='3'>Atenci&oacute;n Especialidades (CAE)</option>
<option value='4'>Otro Establecimiento de la RED</option>
<option value='5'>Otra Procedencia (RN proveniente de la maternidad)</option>
</select>
</td></tr>

    <tr id='inst_sel' style='display:none;'>
		<td style='text-align: right;'>Instituci&oacute;n de Procedencia:</td>
		<td style='text-align: left;' colspan=3>
		<input type='hidden' id='inst_id' name='inst_id' value=''>
		<input type='text' id='institucion' name='institucion' size=40>
	  </td>
    </tr>

<tr id='tr_dau' style='display:none;'>
<td style='text-align:right;'>
DAU:
</td><td colspan=3>
<input type='text' id='dau'  name='dau' value='' 
size=10>
</td></tr>
<!---

<tr><td style='text-align:right;'>
N&uacute;mero Cama:
</td><td colspan=3>
<input type='hidden' id='disponible' name='disponible' value='0'>
<input type='text' style='text-align:center;font-weight:bold;' 
id='nro_cama' name='nro_cama' size=10 onKeyUp='cargar_cama();'>
<input type='button' onClick='ver_camas();' 
value='- Disponibilidad Camas -'>
</td></tr>

<tr><td style='text-align:right;'>
Tipo Cama:
</td><td id='desc_cama' style='height:30px;' colspan=3>
<i>(Debe seleccionar cama...)</i>
</td></tr>

--->

<tr><td style='text-align:right;'>
Fecha Ingreso:
</td><td colspan=3>
		<input type='text' name='fecha0' id='fecha0' value="<?php echo date('d/m/Y'); ?>">
		<img src='iconos/date_magnify.png' name='fecha_boton' id='fecha_boton'>
</td></tr>

<tr><td style='text-align:right;width:30%;'>
Hora Ingreso:</td><td>
<input type='text' id='hora1' name='hora1' style='text-align: center;' size=10 value='<?php echo date("H:i:s");?>' onBlur='validacion_hora(this);'>
</td></tr>

<tr>
<td style='text-align:right;width:30%;'>
Servicio Ingreso:
</td>
<td>
<?php
//$ccamas = cargar_registros_obj("SELECT * FROM clasifica_camas  WHERE tcama_id>58 ORDER BY tcama_num_ini", true);
$ccamas = cargar_registros_obj("SELECT * FROM clasifica_camas where tcama_id>58 ORDER BY tcama_num_ini", true);
?>
<select id='centro_ruta0' name='centro_ruta0'>
<option value='' SELECTED>(Seleccione servicio de ingreso...)</option>
<?php 
for($i=0;$i<sizeof($ccamas);$i++) {
	print("<option value='".$ccamas[$i]['tcama_id']."'>".$ccamas[$i]['tcama_tipo']."</option>");
}
?>
</select>
</td>
</tr>

<tr style='display:none;'>
<td style='text-align:right;'>Categorizaci&oacute;n del Paciente:</td>
<td>

<select id='criticidad' name='criticidad'>
<option value='-1' SELECTED>(Seleccionar...)</option>
<option value='A1'>A1</option>
<option value='A2'>A2</option>
<option value='A3'>A3</option>

<option value='B1'>B1</option>
<option value='B2'>B2</option>
<option value='B3'>B3</option>

<option value='C1'>C1</option>
<option value='C2'>C2</option>
<option value='C3'>C3</option>

<option value='D1'>D1</option>
<option value='D2'>D2</option>
<option value='D3'>D3</option>
</select>

</td>
</tr>

<tr>
<td style='text-align:right;'>Diagn&oacute;stico Ingreso CIE10:</td>
<td colspan=3>
<input type='text' id='diag_cod' name='diag_cod' value='' DISABLED size=5 style='font-weight:bold;text-align:center;' />
<input type='text' id='diagnostico' value='' name='diagnostico' size=35 onDblClick='$("diag_cod").value=""; $("diagnostico").value="";' />
</td>
</tr>


</table>
</div>

</form>

<center>
<br /><br />
<input type='button' id='ingresar' style='display: none'
value='- Guardar datos de Hospitalizaci&oacute;n -' onClick='guardar_hosp();'>
<br /><br />
</center>

</div>

</center>

<script>

    Calendar.setup({
        inputField     :    'fecha0',         // id of the input field
        ifFormat       :    '%d/%m/%Y',       // format of the input field
        showsTime      :    false,
        button          :   'fecha_boton'
    });
    
      ingreso_rut=function(datos_medico) {
      	$('doc_id').value=datos_medico[3];
      	$('rut_medico').value=datos_medico[1];
      }

       autocompletar_medicos = new AutoComplete(
      'nombre_medico', 
      'autocompletar_sql.php',
      function() {
        if($('nombre_medico').value.length<3) return false;
        
        return {
          method: 'get',
          parameters: 'tipo=medicos&'+$('nombre_medico').serialize()
        }
      }, 'autocomplete', 350, 200, 250, 1, 2, ingreso_rut);

    
    
     ingreso_especialidades=function(datos_esp) {
      	$('esp_id').value=datos_esp[0];
      	$('especialidad').value=datos_esp[2].unescapeHTML();
      }
      
       autocompletar_especialidades = new AutoComplete(
      'especialidad', 
      'autocompletar_gcamas.php',
      function() {
        if($('especialidad').value.length<3) return false;
        
        return {
          method: 'get',
          parameters: 'tipo=especialidad&esp_desc='+encodeURIComponent($('especialidad').value)
        }
      }, 'autocomplete', 350, 200, 250, 1, 2, ingreso_especialidades);
      
     ingreso_especialidades2=function(datos_esp) {
      	$('esp_id2').value=datos_esp[0];
      	$('especialidad2').value=datos_esp[2].unescapeHTML();
      }
      
       autocompletar_especialidades2 = new AutoComplete(
      'especialidad2', 
      'autocompletar_gcamas.php',
      function() {
        if($('especialidad2').value.length<3) return false;
        
        return {
          method: 'get',
          parameters: 'tipo=subespecialidad&esp_desc='+encodeURIComponent($('especialidad2').value)
        }
      }, 'autocomplete', 350, 200, 250, 1, 2, ingreso_especialidades2);


/*
    seleccionar_serv2 = function(d) {

		$('centro_ruta0').value=d[0].unescapeHTML();
		$('servicios0').value=d[2].unescapeHTML(); 

    }

    autocompletar_servicios2 = new AutoComplete(
      'servicios0', 
      'autocompletar_sql.php',
      function() {
        if($('servicios0').value.length<3) return false;

        return {
          method: 'get',
          parameters: 'tipo=centros_pabellon&cadena='+encodeURIComponent($('servicios0').value)
        }
      }, 'autocomplete', 350, 200, 150, 2, 3, seleccionar_serv2);
*/

    /*seleccionar_serv2 = function(d) {

		$('centro_ruta0').value=d[0].unescapeHTML();
		$('servicios0').value=d[2].unescapeHTML(); 

    }

    autocompletar_servicios2 = new AutoComplete(
      'servicios0', 
      'autocompletar_sql.php',
      function() {
        if($('servicios0').value.length<3) return false;

        return {
          method: 'get',
          parameters: 'tipo=servicios_hospitalizacion&cadena='+encodeURIComponent($('servicios0').value)
        }
      }, 'autocomplete', 350, 200, 150, 2, 3, seleccionar_serv2);*/


      ingreso_diagnosticos=function(datos_diag) {
      	
      	var cie10=datos_diag[0].charAt(0)+datos_diag[0].charAt(1)+datos_diag[0].charAt(2);
      	cie10+='.'+datos_diag[0].charAt(3);
      	
      	$('diag_cod').value=cie10;
      	$('diagnostico').value=datos_diag[2].unescapeHTML();
      	
      }

      autocompletar_diagnosticos = new AutoComplete(
      	'diagnostico', 
      	'autocompletar_sql.php',
      function() {
        if($('diagnostico').value.length<3) return false;
        
        return {
          method: 'get',
          parameters: 'tipo=diagnostico_tapsa&cadena='+encodeURIComponent($('diagnostico').value)
        }
      }, 'autocomplete', 350, 200, 250, 1, 2, ingreso_diagnosticos);


	var inputs=$('contenido').getElementsByTagName('input');

	for(var i=0;i<inputs.length;i++) {

		Event.observe(inputs[i], 'focus', function() {
			this.setStyle({border: '2px solid red'});
		});

		Event.observe(inputs[i], 'blur', function() {
			this.setStyle({border: ''});
		});

	}

	var inputs=$('contenido').getElementsByTagName('select');

	for(var i=0;i<inputs.length;i++) {

		Event.observe(inputs[i], 'focus', function() {
			this.setStyle({border: '3px solid red'});
		});

		Event.observe(inputs[i], 'blur', function() {
			this.setStyle({border: ''});
		});

	}
seleccionar_inst = function(d) {
    
      $('inst_id').value=d[0];
      $('institucion').value=d[1].unescapeHTML();
    
    }
autocompletar_institucion = new AutoComplete(
      'institucion', 
      'autocompletar_sql.php',
      function() {
        if($('institucion').value.length<3) return false;

        return {
          method: 'get',
          parameters: 'tipo=hospitales&cadena='+encodeURIComponent($('institucion').value)
        }
      }, 'autocomplete', 350, 200, 150, 0, 3, seleccionar_inst);

validacion_hora($('hora1'));

</script>