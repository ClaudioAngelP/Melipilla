<?php 

	require_once('../../conectar_db.php');
	require_once('../../ficha_clinica/ficha_basica.php');

?>

<script>

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

		calc_dias();
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

	limpiar_formulario=function() {

		$('hosp_id').value=0;

		limpiar_ficha_basica();
		$('paciente_tipo_id').value=0;
		
		
		$('paciente_id').value='';
		$('paciente_rut').value='';
		$('doc_id').value='';
		
		$('hora1').value='12:00';								
		
		
		//$('nro_cama').value=reg.hosp_numero_cama;
		//$('disponible').value='1';

		$('prevision').value=0;
		$('prevision_clase').value=0;
		$('prevision_clase').style.display='';

		$('modalidad').value=0;
		$('ges').value=0;
                $("prog_social").style.display="none";
                $('motivo').value=0;
		$('procedencia').value=0;

		$('fecha0').value='';
		
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
	 
		if(bloquear_ingreso) return;	 
	 
      if($('prevision').value==3)
      {
           if($('motivo').value==0)
           {
               alert('Debe seleccionar ley o Programa Social para guardar los datos');
               return;
           }
       }


		try {
		
			var f1=$('fecha0').value.split('/');

			var d1=new Date( Date.parse(f1[1]+'/'+f1[0]+'/'+f1[2]) );

		} catch(err) {

			alert('Fecha de Ingreso incorrecta.');
			return;			

		}
		
		$('diag_cod').disabled=false;
		
		var params=$('paciente').serialize()+'&'+$('hosp').serialize();
		
		bloquear_ingreso=true;	 

		$('diag_cod').disabled=true;
	 
		var myAjax=new Ajax.Request(
			'prestaciones/ingreso_egreso_hospital/sql_solicitud.php',
			{
				method:'post',
				parameters: params,
				onComplete: function(r) {
				
					if(r.responseText=='N' || r.responseText=='E') {
										
						alert('Solicitud de ingreso hospitalario ingresada exitosamente.');
												
						limpiar_formulario();
												
						$('contenido').scrollTop=0;
						
					} else {
					
						alert(r.responseText);
						
					}
						
					bloquear_ingreso=false;						
																					
				}
				
			}		
			
		);	 
	 
	 }

</script>

<center>
<div class='sub-content' style='width:750px;'>
<div class='sub-content' 
style='background-color:#cccccc;font-weight:bold;'>
<table cellpadding=0 cellspacing=0 style='width:100%;'><tr><td>
<img src='iconos/building.png'></td><td style='width:90%;font-size:14px;'>
<b>Solicitud de Ingreso Hospitalario</b>
</td></tr></table>
</div>

<?php desplegar_ficha_basica(); ?>

<form id='hosp' name='hosp' onSubmit='return false;'>
<input type='hidden' id='hosp_id' name='hosp_id' value='0'>

<div class='sub-content'>
<img src='iconos/building.png'> <b>Datos de Ingreso</b>
</div>

<div class='sub-content'>

<table style='width:100%;'>

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
<option value='0'>Ley Salud (18.469)</option>
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


<tr><td style='text-align:right;'>
Modalidad de Atenci&oacute;n:
</td><td colspan=3>
<select id='modalidad' name='modalidad'>
<option value='0'>MAI</option>
<option value='1'>MLE</option>
</select>
</td></tr>

<tr><td style='text-align:right;'>
GES:
</td><td colspan=3>
<select id='ges' name='ges'>
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

<tr><td style='text-align:right;'>
Procedencia del Paciente:
</td><td colspan=3>
<select id='procedencia' name='procedencia'
onChange='
	if(this.value==1 || this.value==3) 
		$("inst_sel").style.display=""; 
	else
		$("inst_sel").style.display="none";	
'>
<option value='0'>Unidad de Emergencia</option>
<option value='1'>APS</option>
<option value='2'>Atenci&oacute;n de Especialidades</option>
<option value='3'>Otro Hospital</option>
<option value='4'>Otra Procedencia</option>
</select>
</td></tr>

    <tr id='inst_sel' style='display:none;'>
		<td style='text-align: right;'>Instituci&oacute;n de Procedencia:</td>
		<td style='text-align: left;' colspan=3>
		<input type='hidden' id='inst_id' name='inst_id' value=''>
		<input type='text' id='institucion' name='institucion' size=40>
	  </td>
    </tr>


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

<tr><td style='text-align:right;width:30%;'>
Hora Ingreso:</td><td>
<input type='text' id='hora1' name='hora1' size=10 value='12:00'>
</td></tr>

<tr><td style='text-align:right;width:30%;'>
Fecha Ingreso:</td><td>

  <input type='text' name='fecha0' id='fecha0' size=10
  style='text-align: center;'  onBlur="cfecha(this);" onFocus='this.select();'
  value=''>
  
  <img src='iconos/date_magnify.png' id='fecha0_boton' />
  
</td></tr>

<tr>
<td style='text-align:right;width:30%;'>
Servicio Ingreso:
</td>
<td>
<input type="hidden" id='centro_ruta0' name='centro_ruta0' value=''>
<input type="text" id='servicios0' name='servicios0'>
</td>
</tr>

<tr>
<td style='text-align:right;'>Criticidad del Paciente:</td>
<td>

<select id='criticidad' name='criticidad'>
<option value='A1'>A1</option>
<option value='A2'>A2</option>
<option value='A3'>A3</option>

<option value='B1'>B1</option>
<option value='B2'>B2</option>
<option value='B3'>B3</option>

<option value='C1' SELECTED>C1</option>
<option value='C2'>C2</option>
<option value='C3'>C3</option>

<option value='D1'>D1</option>
<option value='D2'>D2</option>
<option value='D3'>D3</option>
</select>

</td>
</tr>

<tr>
<td style='text-align:right;'>Diagn&oacute;stico CIE10:</td>
<td colspan=3>
<input type='text' id='diag_cod' name='diag_cod' 
value='' DISABLED size=5 style='font-weight:bold;text-align:center;' />
<input type='text' id='diagnostico' 
value='' name='diagnostico' size=35
onDblClick='$("diag_cod").value=""; $("diagnostico").value="";' />
</td>
</tr>


</table>
</div>

</form>

<center>
<br /><br />
<input type='button' id='ingresar' 
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
        button          :   'fecha0_boton'
    });

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
          parameters: 'tipo=servicios_hosp&cadena='+encodeURIComponent($('servicios0').value)
        }
      }, 'autocomplete', 350, 200, 150, 2, 3, seleccionar_serv2);


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



</script>